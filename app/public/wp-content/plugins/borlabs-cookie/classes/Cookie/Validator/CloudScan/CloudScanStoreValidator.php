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

namespace Borlabs\Cookie\Validator\CloudScan;

use Borlabs\Cookie\Enum\CloudScan\CloudScanTypeEnum;
use Borlabs\Cookie\Enum\CloudScan\PageTypeEnum;
use Borlabs\Cookie\Localization\CloudScan\CloudScanCreateLocalizationStrings;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

final class CloudScanStoreValidator
{
    private Validator $validator;

    public function __construct(
        MessageManager $messageManager
    ) {
        $this->validator = new Validator($messageManager, true);
    }

    public function isValid(array $postData): bool
    {
        $localization = CloudScanCreateLocalizationStrings::get();

        $selectPageType = $postData['selectPageType'] ?? '';
        $this->validator->isEnumValue('selectPageType', $selectPageType, PageTypeEnum::class);
        $this->validator->isEnumValue('selectScanType', $postData['selectScanType'] ?? '', CloudScanTypeEnum::class);

        if ($selectPageType === PageTypeEnum::CUSTOM) {
            $this->validator->isBoolean($localization['field']['enableCustomScanUrls'], $postData['enableCustomScanUrls'] ?? '');

            if (Sanitizer::booleanString($postData['enableCustomScanUrls'] ?? '')) {
                $urls = Sanitizer::hostList($postData['customScanUrls'] ?? '', true);
                $this->validator->isNotEmptyString($localization['field']['customScanUrls'], count($urls) <= 0 ? '' : $urls[0]);
            } else {
                $this->validator->isUrl($localization['field']['scanPageUrl'], $postData['scanPageUrl'] ?? '');
            }
        }

        return $this->validator->isValid();
    }
}
