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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent;

use Borlabs\Cookie\System\Config\ContentBlockerStyleConfig;
use Borlabs\Cookie\System\Log\Log;

final class ContentBlockerStyleConfigImporter
{
    private ContentBlockerStyleConfig $contentBlockerStyleConfig;

    private Log $log;

    public function __construct(
        ContentBlockerStyleConfig $contentBlockerStyleConfig,
        Log $log
    ) {
        $this->contentBlockerStyleConfig = $contentBlockerStyleConfig;
        $this->log = $log;
    }

    public function import($legacyConfigData, string $languageCode)
    {
        $contentBlockerStyleDto = $this->contentBlockerStyleConfig->defaultConfig();
        $contentBlockerStyleDto->backgroundColor = (string) ($legacyConfigData['contentBlockerBgColor'] ?? $contentBlockerStyleDto->backgroundColor);
        $contentBlockerStyleDto->backgroundOpacity = (int) ($legacyConfigData['contentBlockerBgOpacity'] ?? $contentBlockerStyleDto->backgroundOpacity);
        $contentBlockerStyleDto->buttonBorderRadiusBottomLeft = (int) ($legacyConfigData['contentBlockerBtnBorderRadius'] ?? $contentBlockerStyleDto->buttonBorderRadiusBottomLeft);
        $contentBlockerStyleDto->buttonBorderRadiusBottomRight = (int) ($legacyConfigData['contentBlockerBtnBorderRadius'] ?? $contentBlockerStyleDto->buttonBorderRadiusBottomRight);
        $contentBlockerStyleDto->buttonBorderRadiusTopLeft = (int) ($legacyConfigData['contentBlockerBtnBorderRadius'] ?? $contentBlockerStyleDto->buttonBorderRadiusTopLeft);
        $contentBlockerStyleDto->buttonBorderRadiusTopRight = (int) ($legacyConfigData['contentBlockerBtnBorderRadius'] ?? $contentBlockerStyleDto->buttonBorderRadiusTopRight);
        $contentBlockerStyleDto->buttonColor = (string) ($legacyConfigData['contentBlockerBtnColor'] ?? $contentBlockerStyleDto->buttonColor);
        $contentBlockerStyleDto->buttonColorHover = (string) ($legacyConfigData['contentBlockerBtnHoverColor'] ?? $contentBlockerStyleDto->buttonColorHover);
        $contentBlockerStyleDto->buttonTextColor = (string) ($legacyConfigData['contentBlockerBtnTxtColor'] ?? $contentBlockerStyleDto->buttonTextColor);
        $contentBlockerStyleDto->buttonTextColorHover = (string) ($legacyConfigData['contentBlockerBtnHoverTxtColor'] ?? $contentBlockerStyleDto->buttonTextColorHover);
        $contentBlockerStyleDto->fontFamily = (string) ($legacyConfigData['contentBlockerFontFamily'] ?? $contentBlockerStyleDto->fontFamily);
        $contentBlockerStyleDto->fontFamilyStatus = $contentBlockerStyleDto->fontFamily !== 'inherit';
        $contentBlockerStyleDto->fontSize = (int) ($legacyConfigData['contentBlockerFontSize'] ?? $contentBlockerStyleDto->fontSize);
        $contentBlockerStyleDto->linkColor = (string) ($legacyConfigData['contentBlockerLinkColor'] ?? $contentBlockerStyleDto->linkColor);
        $contentBlockerStyleDto->linkColorHover = (string) ($legacyConfigData['contentBlockerLinkHoverColor'] ?? $contentBlockerStyleDto->linkColorHover);
        $contentBlockerStyleDto->separatorColor = (string) ($legacyConfigData['contentBlockerLinkColor'] ?? $contentBlockerStyleDto->separatorColor);
        $contentBlockerStyleDto->textColor = (string) ($legacyConfigData['contentBlockerTxtColor'] ?? $contentBlockerStyleDto->textColor);
        $contentBlockerStyleSaveStatus = $this->contentBlockerStyleConfig->save($contentBlockerStyleDto, $languageCode);

        $this->log->info(
            '[Import] Content Blocker style config ({{ languageCode }}) imported: {{ status }}',
            [
                'languageCode' => $languageCode,
                'status' => $contentBlockerStyleSaveStatus ? 'Yes' : 'No',
            ],
        );
    }
}
