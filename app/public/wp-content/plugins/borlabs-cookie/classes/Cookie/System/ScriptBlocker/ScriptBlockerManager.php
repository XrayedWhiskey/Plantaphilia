<?php
/*
 *  Copyright (c) 2026 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\System\ScriptBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Repository\ScriptBlocker\ScriptBlockerRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\LocalScanner\ScanRequestService;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\WordPressFrontendDriver\OutputBufferManager;

final class ScriptBlockerManager
{
    private Container $container;

    private Log $log;

    private OutputBufferManager $outputBufferManager;

    private ScanRequestService $scanRequestService;

    private ScriptBlockerRepository $scriptBlockerRepository;

    /**
     * @var \Borlabs\Cookie\Model\ScriptBlocker\ScriptBlockerModel[]
     */
    private array $scriptBlockers = [];

    private WpFunction $wpFunction;

    public function __construct(
        Container $container,
        Log $log,
        ScanRequestService $scanRequestService,
        ScriptBlockerRepository $scriptBlockerRepository,
        WpFunction $wpFunction
    ) {
        $this->container = $container;
        $this->log = $log;
        $this->scanRequestService = $scanRequestService;
        $this->scriptBlockerRepository = $scriptBlockerRepository;
        $this->wpFunction = $wpFunction;
    }

    public function blockHandle(string $tag, string $handle, string $src): string
    {
        if ($this->hasScriptBlockers() === false) {
            return $tag;
        }

        if (
            $handle === 'borlabs-cookie-config'
            || $handle === 'borlabs-cookie-core'
            || $handle === 'borlabs-cookie-debug-console'
            || $handle === 'borlabs-cookie-legacy-backward-compatibility'
            || $handle === 'borlabs-cookie-prioritize'
            || $handle === 'borlabs-cookie-stub'
        ) {
            return $tag;
        }

        $searchPatterns = [
            // Match type="text/javascript" or type='text/javascript'
            '/type\s*=\s*([\'"])text\/javascript\1/i',
            // Match type="module" with single or double quotes
            '/type\s*=\s*([\'"])module\1/i',
            // Match type="application/javascript" or type='application/javascript'
            '/type\s*=\s*([\'"])application\/javascript\1/i',
            // Match <script (opening tag, capture the whole start of tag)
            '/<script\s+/i',
            // Match src= with optional quotes and optional spaces
            '/\s+src\s*=/i',
        ];
        $scriptTagsMatches = [];
        preg_match_all('/<script([^>]*)>(.*)<\/script>/Us', $tag, $scriptTagsMatches);

        $wholeScriptTags = $scriptTagsMatches[0];
        $scriptTagsSignature = $scriptTagsMatches[1];
        $scriptTagsContent = $scriptTagsMatches[2];
        $modifiedTag = '';

        foreach ($this->scriptBlockers as $scriptBlocker) {
            foreach ($wholeScriptTags as $index => $scriptTag) {
                if (in_array($handle, array_column($scriptBlocker->handles->list, 'key', 'key'), true) === false) {
                    continue;
                }

                if (isset($scriptTagsSignature[$index])) {
                    $scriptTagsSignature[$index] = $this->handleScriptType($scriptTagsSignature[$index]);
                }

                if (!isset($scriptTagsSignature[$index])) {
                    continue;
                }

                $scriptTagsContent[$index] = $this->wpFunction->applyFilter(
                    'borlabsCookie/scriptBlocker/blocking/modifyScriptTagContent/' . $scriptBlocker->key,
                    $scriptTagsContent[$index],
                );

                $scriptTag = '<script' . $scriptTagsSignature[$index] . '>' . $scriptTagsContent[$index] . '</script>';
                $replacements = [
                    'type="text/template"',
                    'type="text/template-module"',
                    'type="text/template"',
                    '<script data-borlabs-cookie-script-blocker-handle="' . $handle
                    . '" data-borlabs-cookie-script-blocker-id="' . $scriptBlocker->key . '" ',
                    ' data-borlabs-cookie-script-blocker-src=',
                ];

                foreach ($searchPatterns as $patternIndex => $pattern) {
                    $scriptTag = preg_replace($pattern, $replacements[$patternIndex], $scriptTag);
                }

                // Remove async or defer attribute
                if (strpos($scriptTag, 'data-borlabs-cookie-script-blocker-src=') !== false) {
                    $scriptTag = preg_replace('/\s(defer|async)=[\'"](defer|async)[\'"]/', '', $scriptTag);
                    $scriptTag = preg_replace('/((\s+)(?:defer|async)([\s>]))/', '$3', $scriptTag);
                }

                $modifiedTag .= $scriptTag;
            }
        }

        return $modifiedTag === '' ? $tag : $modifiedTag;
    }

    public function blockUnregisteredScriptTags(): void
    {
        $buffer = &$this->outputBufferManager->getBuffer();
        $modifiedBuffer = preg_replace_callback('/<script([^>]*)>(.*)<\/script>/Us', [$this, 'blockScriptTag'], $buffer);

        if ($modifiedBuffer === null) {
            ini_set('pcre.backtrack_limit', '5000000');

            $modifiedBuffer = preg_replace_callback('/<script([^>]*)>(.*)<\/script>/Us', [$this, 'blockScriptTag'], $buffer);
        }

        if ($modifiedBuffer === null) {
            $this->log->critical(
                'Your inline JavaScript appears to be excessively lengthy and would benefit from relocation to a separate file. This adjustment is advisable because inline JavaScript lacks the capability for caching, potentially leading to suboptimal performance.',
                [
                    'pregLastError' => preg_last_error(),
                    'pregLastErrorMessage' => preg_last_error_msg(),
                ],
            );
        }

        $buffer = $modifiedBuffer;
    }

    public function hasScriptBlockers(): bool
    {
        return (bool) count($this->scriptBlockers);
    }

    public function init(): void
    {
        $this->outputBufferManager = $this->container->get(OutputBufferManager::class);

        if ($this->scanRequestService->noScriptBlockers()
            || $this->wpFunction->applyFilter('borlabsCookie/scriptBlocker/skipInitialization', null) === true
        ) {
            return;
        }

        $this->scriptBlockers = $this->scriptBlockerRepository->getAllActive();
    }

    public function setOutputBufferManager(OutputBufferManager $outputBufferManager): void
    {
        $this->outputBufferManager = $outputBufferManager;
    }

    public function setScriptBlockers(array $scriptBlockers): void
    {
        $this->scriptBlockers = $scriptBlockers;
    }

    private function blockScriptTag(array $matches): string
    {
        if ($this->hasScriptBlockers() === false) {
            return $matches[0];
        }

        /** @var string $wholeScriptTag */
        $wholeScriptTag = $matches[0];

        /** @var string $scriptTagSignature */
        $scriptTagSignature = $matches[1];

        /** @var string $scriptTagContent */
        $scriptTagContent = $matches[2];

        foreach ($this->scriptBlockers as $scriptBlocker) {
            if (count($scriptBlocker->phrases->list) === 0) {
                continue;
            }

            foreach ($scriptBlocker->phrases->list as $phrase) {
                if ($this->matchesPhrase($wholeScriptTag, $phrase->value) === false
                    || (
                        strpos($wholeScriptTag, 'borlabs-cookie-config') !== false
                        || strpos($wholeScriptTag, 'borlabs-cookie-core') !== false
                        || strpos($wholeScriptTag, 'borlabs-cookie-debug-console') !== false
                        || strpos($wholeScriptTag, 'borlabs-cookie-legacy-backward-compatibility') !== false
                        || strpos($wholeScriptTag, 'borlabs-cookie-prioritize') !== false
                        || strpos($wholeScriptTag, 'borlabs-cookie-stub') !== false
                        || strpos($wholeScriptTag, 'data-borlabs-cookie-script-blocker-ignore') !== false
                    )
                ) {
                    continue;
                }

                $modifiedScriptTagSignature = $this->handleScriptType($scriptTagSignature);

                if ($modifiedScriptTagSignature === null) {
                    continue;
                }

                // Switch type attribute and add data attribute
                $modifiedScriptTagSignature = ' data-borlabs-cookie-script-blocker-id=\'' . $scriptBlocker->key . '\'' . $modifiedScriptTagSignature;

                // Handle script tags with src attribute (externally loaded)
                $modifiedScriptTagSignature = preg_replace(
                    '/(\s)src=(["\']|[^\s]*)/',
                    '$1data-borlabs-cookie-script-blocker-src=$2',
                    $modifiedScriptTagSignature,
                    1,
                );

                if (strpos($modifiedScriptTagSignature, 'data-borlabs-cookie-script-blocker-src=') !== false) {
                    // Remove async or defer attribute
                    $modifiedScriptTagSignature = preg_replace(
                        '/(\s+)(defer|async)=[\'"](defer|async|true|)[\'"]/',
                        '',
                        $modifiedScriptTagSignature,
                    );
                    $modifiedScriptTagSignature = preg_replace(
                        '/(\s+)(?:defer|async)/',
                        '',
                        $modifiedScriptTagSignature,
                    );
                }

                // Handle onExist
                if ($scriptBlocker->onExist !== null && !$scriptBlocker->onExist->isEmpty()) {
                    $onExist = Searcher::findObject($scriptBlocker->onExist->list, 'key', $phrase->key);

                    if ($onExist !== null) {
                        return $this->handleOnExistOnScriptTag($modifiedScriptTagSignature, $scriptTagContent, $onExist->value);
                    }
                }

                $scriptTagContent = $this->wpFunction->applyFilter(
                    'borlabsCookie/scriptBlocker/blocking/modifyScriptTagContent/' . $scriptBlocker->key,
                    $scriptTagContent,
                );

                return $this->wpFunction->applyFilter(
                    'borlabsCookie/scriptBlocker/blocking/afterBlocking/' . $scriptBlocker->key,
                    '<script' . $modifiedScriptTagSignature . '>' . $scriptTagContent . '</script>',
                );
            }
        }

        return $wholeScriptTag;
    }

    private function handleOnExistOnScriptTag(string $scriptTagSignature, string $scriptTagContent, string $onExist): string
    {
        if (strstr($scriptTagSignature, 'data-borlabs-cookie-script-blocker-src') !== false) {
            $srcMatches = [];
            preg_match('/(\s)data-borlabs-cookie-script-blocker-src=((["\']([^"\']*)["\'])|[^\s>]*)/', $scriptTagSignature, $srcMatches);
            $srcValue = count($srcMatches) === 5 ? $srcMatches[4] : $srcMatches[2];
            $scriptTagSignature = preg_replace('/(\s)data-borlabs-cookie-script-blocker-src=((["\']([^"\']*)["\'])|[^\s>]*)/', '', $scriptTagSignature);
            $scriptTagContent = <<<EOT
const newScript = document.createElement('script');
for (let i = 0; i < currentScript.attributes.length; i++) {
  const attribute = currentScript.attributes[i];
  if (attribute.name.indexOf('data-borlabs-cookie') !== -1) {
    continue;
  }
  newScript.setAttribute(attribute.name, attribute.value);
}
newScript.setAttribute('src', '{$srcValue}');
currentScript.parentNode.insertBefore(newScript, currentScript.nextSibling);
currentScript.remove();
EOT;
        }

        $scriptTagContent = 'var currentScript = document.currentScript; window.BorlabsCookie.Tools.onExist(\'' . $onExist . '\', () => { '
            . $scriptTagContent
            . ' });';

        $scriptBlockerId = [];
        preg_match('/data-borlabs-cookie-script-blocker-id=\'([a-z\-_]+)\'/', $scriptTagSignature, $scriptBlockerId);
        $scriptTagContent = $this->wpFunction->applyFilter(
            'borlabsCookie/scriptBlocker/blocking/modifyScriptTagContent/' . $scriptBlockerId[1],
            $scriptTagContent,
        );

        return $this->wpFunction->applyFilter(
            'borlabsCookie/scriptBlocker/blocking/afterBlocking/' . $scriptBlockerId[1],
            '<script' . $scriptTagSignature . '>' . $scriptTagContent . '</script>',
        );
    }

    private function handleScriptType(string $scriptTagSignature): ?string
    {
        // Detect if script is of type javascript
        $typeAttributeMatches = [];
        preg_match('/type\s*=\s*([\'"])(text\/javascript|module|application\/javascript)\1/i', $scriptTagSignature, $typeAttributeMatches);
        $detectedType = $typeAttributeMatches[2] ?? null;

        // Mapping of original to new types
        $typeMap = [
            'text/javascript' => 'text/template',
            'application/javascript' => 'text/template',
            'module' => 'text/template-module',
        ];

        // Only <script>-tags without type attribute or with type attribute text/javascript, module or application/javascript are JavaScript
        if ($detectedType !== null && !in_array(strtolower($detectedType), array_keys($typeMap), true)) {
            return null;
        }

        // Add type attribute if missing
        if ($detectedType === null) {
            return ' type=\'text/template\'' . $scriptTagSignature;
        }

        $newType = $typeMap[strtolower($detectedType)] ?? null;

        if ($newType === null) {
            return null;
        }

        return $this->replaceTypeAttribute(
            $scriptTagSignature,
            $typeAttributeMatches[0],
            $detectedType,
            $newType,
        );
    }

    private function matchesPhrase(string $scriptTagContent, string $phrase): bool
    {
        if (strpos($scriptTagContent, $phrase) !== false) {
            return true;
        }

        // Verify if the phrase is a URL in the format //hostname.tld/path/ to prevent incorrect regex matches.
        if (substr($phrase, 0, 2) === '//' && substr($phrase, -1) === '/') {
            return false;
        }

        if (substr($phrase, 0, 1) === '/' && substr($phrase, -1) === '/' && @preg_match($phrase, $scriptTagContent)) {
            return true;
        }

        if (preg_last_error() !== PREG_NO_ERROR) {
            $this->log->error(
                'An error occurred while processing a regular expression of a Script Blocker.',
                [
                    'error' => preg_last_error(),
                    'errorMessage' => preg_last_error_msg(),
                    'phrase' => $phrase,
                ],
            );
        }

        return false;
    }

    private function replaceTypeAttribute(
        string $scriptTagSignature,
        string $originalAttribute,
        string $currentType,
        string $newType
    ): string {
        $newAttribute = str_replace($currentType, $newType, $originalAttribute);

        return str_replace(
            $originalAttribute,
            $newAttribute,
            $scriptTagSignature,
        );
    }
}
