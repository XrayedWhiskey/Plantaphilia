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

namespace Borlabs\Cookie\System\StyleBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Model\StyleBlocker\StyleBlockerModel;
use Borlabs\Cookie\Repository\StyleBlocker\StyleBlockerRepository;
use Borlabs\Cookie\System\LocalScanner\ScanRequestService;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\WordPressFrontendDriver\OutputBufferManager;

final class StyleBlockerManager
{
    private Container $container;

    private Log $log;

    private OutputBufferManager $outputBufferManager;

    private ScanRequestService $scanRequestService;

    private StyleBlockerRepository $styleBlockerRepository;

    /**
     * @var StyleBlockerModel[]
     */
    private array $styleBlockers = [];

    private WpFunction $wpFunction;

    public function __construct(
        Container $container,
        Log $log,
        ScanRequestService $scanRequestService,
        StyleBlockerRepository $styleBlockerRepository,
        WpFunction $wpFunction
    ) {
        $this->container = $container;
        $this->log = $log;
        $this->scanRequestService = $scanRequestService;
        $this->styleBlockerRepository = $styleBlockerRepository;
        $this->wpFunction = $wpFunction;
    }

    public function blockHandle(string $tag, string $handle, string $src): string
    {
        if ($this->hasStyleBlockers() === false) {
            return $tag;
        }

        if ($handle === 'borlabs-cookie-origin' || $handle === 'borlabs-cookie-custom') {
            return $tag;
        }

        $search = [
            '<link ',
            ' href=',
        ];

        foreach ($this->styleBlockers as $styleBlocker) {
            if (in_array($handle, array_column($styleBlocker->handles->list, 'key', 'key'), true) === false) {
                continue;
            }

            $replace = [
                '<link data-borlabs-cookie-style-blocker-handle="' . $handle . '" data-borlabs-cookie-style-blocker-id="' . $styleBlocker->key . '" ',
                ' data-borlabs-cookie-style-blocker-href=',
            ];
            $tag = str_replace($search, $replace, $tag);
        }

        return $tag;
    }

    public function blockUnregisteredStyleTags(): void
    {
        $buffer = &$this->outputBufferManager->getBuffer();
        $modifiedBuffer = preg_replace_callback('/<style([^>]*)>(.*)<\/style>/Us', [$this, 'blockStyleTag'], $buffer);

        if ($modifiedBuffer === null) {
            ini_set('pcre.backtrack_limit', '5000000');

            $modifiedBuffer = preg_replace_callback('/<style([^>]*)>(.*)<\/style>/Us', [$this, 'blockStyleTag'], $buffer);
        }

        if ($modifiedBuffer === null) {
            $this->log->critical(
                'Your inline CSS appears to be excessively lengthy and would benefit from relocation to a separate file. This adjustment is advisable because inline CSS lacks the capability for caching, potentially leading to suboptimal performance.',
                [
                    'pregLastError' => preg_last_error(),
                    'pregLastErrorMessage' => preg_last_error_msg(),
                ],
            );
        }

        $buffer = preg_replace_callback('/<link([^>]*)>/Us', [$this, 'blockLinkTag'], $modifiedBuffer);
    }

    public function hasStyleBlockers(): bool
    {
        return (bool) count($this->styleBlockers);
    }

    public function init(): void
    {
        $this->outputBufferManager = $this->container->get(OutputBufferManager::class);

        if ($this->scanRequestService->noStyleBlockers()
            || $this->wpFunction->applyFilter('borlabsCookie/styleBlocker/skipInitialization', null) === true
        ) {
            return;
        }

        $this->styleBlockers = $this->styleBlockerRepository->getAllActive();
    }

    public function setOutputBufferManager(OutputBufferManager $outputBufferManager): void
    {
        $this->outputBufferManager = $outputBufferManager;
    }

    /**
     * @param StyleBlockerModel[] $styleBlockers
     */
    public function setStyleBlockers(array $styleBlockers): void
    {
        $this->styleBlockers = $styleBlockers;
    }

    private function blockLinkTag(array $matches): string
    {
        if ($this->hasStyleBlockers() === false) {
            return $matches[0];
        }

        if (strpos($matches[0], 'borlabs-cookie-origin-inline-css') !== false) {
            return $matches[0];
        }

        $search = [
            '<link ',
            ' href=',
        ];

        foreach ($this->styleBlockers as $styleBlocker) {
            if (count($styleBlocker->phrases->list) === 0) {
                continue;
            }

            foreach ($styleBlocker->phrases->list as $phrase) {
                if ($this->matchesPhrase($matches[0], $phrase->value) === false) {
                    continue;
                }

                $replace = [
                    '<link data-borlabs-cookie-style-blocker-id="' . $styleBlocker->key . '" ',
                    ' data-borlabs-cookie-style-blocker-href=',
                ];

                return str_replace($search, $replace, $matches[0]);
            }
        }

        return $matches[0];
    }

    private function blockStyleTag(array $matches): string
    {
        if ($this->hasStyleBlockers() === false) {
            return $matches[0];
        }

        /** @var string $wholeStyleTag */
        $wholeStyleTag = $matches[0];

        /** @var string $styleTagSignature */
        $styleTagSignature = $matches[1];

        /** @var string $styleTagContent */
        $styleTagContent = $matches[2];

        foreach ($this->styleBlockers as $styleBlocker) {
            if (count($styleBlocker->phrases->list) === 0) {
                continue;
            }

            foreach ($styleBlocker->phrases->list as $phrase) {
                if ($this->matchesPhrase($wholeStyleTag, $phrase->value) === false) {
                    continue;
                }

                // Switch type attribute and add data attribute
                $styleTagSignature = ' data-borlabs-cookie-style-blocker-id=\'' . $styleBlocker->key . '\'' . $styleTagSignature;

                return '<script type="text/template" ' . $styleTagSignature . '>' . $styleTagContent . '</script>';
            }
        }

        return $wholeStyleTag;
    }

    private function matchesPhrase(string $styleTagContent, string $phrase): bool
    {
        if (strpos($styleTagContent, $phrase) !== false) {
            return true;
        }

        // Verify if the phrase is a URL in the format //hostname.tld/path/ to prevent incorrect regex matches.
        if (substr($phrase, 0, 2) === '//' && substr($phrase, -1) === '/') {
            return false;
        }

        if (substr($phrase, 0, 1) === '/' && substr($phrase, -1) === '/' && @preg_match($phrase, $styleTagContent)) {
            return true;
        }

        if (preg_last_error() !== PREG_NO_ERROR) {
            $this->log->error(
                'An error occurred while processing a regular expression of a Style Blocker.',
                [
                    'error' => preg_last_error(),
                    'errorMessage' => preg_last_error_msg(),
                    'phrase' => $phrase,
                ],
            );
        }

        return false;
    }
}
