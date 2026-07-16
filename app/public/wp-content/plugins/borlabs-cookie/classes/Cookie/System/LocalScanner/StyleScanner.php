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

namespace Borlabs\Cookie\System\LocalScanner;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Transient\Transient;
use Borlabs\Cookie\System\WordPressFrontendDriver\OutputBufferManager;

class StyleScanner implements ScannerInterface
{
    private OutputBufferManager $outputBufferManager;

    private KeyValueDtoList $phrases;

    private ScanRequestService $scanRequestService;

    private ScanResultService $scanResultService;

    private Transient $transient;

    private WpFunction $wpFunction;

    public function __construct(
        OutputBufferManager $outputBufferManager,
        ScanRequestService $scanRequestService,
        ScanResultService $scanResultService,
        Transient $transient,
        WpFunction $wpFunction
    ) {
        $this->outputBufferManager = $outputBufferManager;
        $this->scanRequestService = $scanRequestService;
        $this->scanResultService = $scanResultService;
        $this->transient = $transient;
        $this->wpFunction = $wpFunction;
    }

    public function detectHandles(string $tag, string $handle, string $src)
    {
        $matchedPhrase = Searcher::findFirstMatchingObjectValue($tag, $this->phrases->list, 'key');

        if ($matchedPhrase !== null) {
            $this->scanResultService->addMatchedHandle($handle, $matchedPhrase, $src);
        } else {
            $this->scanResultService->addUnmatchedHandle($handle, $src);
        }

        return $tag;
    }

    public function detectTags()
    {
        $buffer = &$this->outputBufferManager->getBuffer();
        $linkTags = [];
        preg_match_all('/<link\s.*?\brel=(?:"stylesheet"|\'stylesheet\').*?>/', $buffer, $linkTags);

        foreach ($linkTags[0] as $linkTag) {
            $matchedPhrase = Searcher::findFirstMatchingObjectValue($linkTag, $this->phrases->list, 'key');

            if ($matchedPhrase !== null) {
                $this->scanResultService->addMatchedTag($matchedPhrase, $linkTag);
            } else {
                $this->scanResultService->addUnmatchedTag($linkTag);
            }
        }

        $styleTags = [];
        preg_match_all('/<style([^>]*)>(.*)<\/style>/Us', $buffer, $styleTags);

        foreach ($styleTags[0] as $index => $styleTag) {
            $matchedPhrase = Searcher::findFirstMatchingObjectValue($styleTag, $this->phrases->list, 'key');

            if ($matchedPhrase !== null) {
                $this->scanResultService->addMatchedTag($matchedPhrase, $styleTag);
            } else {
                $this->scanResultService->addUnmatchedTag($styleTag);
            }
        }
    }

    public function init()
    {
        $this->wpFunction->addFilter('style_loader_tag', [$this, 'detectHandles'], 999, 3);
        $this->phrases = $this->transient->get('ScanPhrases' . $this->scanRequestService->getScanRequestId())->value;
    }
}
