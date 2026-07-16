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

use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Log\Log;

final class DialogSettingsConfigImporter
{
    private DialogSettingsConfig $dialogSettingsConfig;

    private Log $log;

    public function __construct(DialogSettingsConfig $dialogSettingsConfig, Log $log)
    {
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->log = $log;
    }

    public function import($legacyConfigData, string $languageCode)
    {
        $dialogSettingsDto = $this->dialogSettingsConfig->defaultConfig();
        $dialogSettingsDto->animation = (bool) ($legacyConfigData['cookieBoxAnimation'] ?? $dialogSettingsDto->animation);
        $dialogSettingsDto->animationDelay = (bool) ($legacyConfigData['cookieBoxAnimationDelay'] ?? $dialogSettingsDto->animationDelay);
        $dialogSettingsDto->animationIn = (string) ($legacyConfigData['cookieBoxAnimationIn'] ?? $dialogSettingsDto->animationIn);
        $dialogSettingsDto->animationOut = (string) ($legacyConfigData['cookieBoxAnimationOut'] ?? $dialogSettingsDto->animationOut);
        $dialogSettingsDto->buttonSwitchRound = (bool) ($legacyConfigData['cookieBoxBtnSwitchRound'] ?? $dialogSettingsDto->buttonSwitchRound);
        $dialogSettingsDto->enableBackdrop = (bool) ($legacyConfigData['cookieBoxBlocksContent'] ?? $dialogSettingsDto->enableBackdrop);
        $dialogSettingsDto->hideDialogOnPages = (array) ($legacyConfigData['hideCookieBoxOnPages'] ?? $dialogSettingsDto->hideDialogOnPages);
        $dialogSettingsDto->imprintPageCustomUrl = (string) ($legacyConfigData['imprintPageCustomURL'] ?? $dialogSettingsDto->imprintPageCustomUrl);
        $dialogSettingsDto->imprintPageId = (int) (isset($legacyConfigData['imprintPageId']) && $legacyConfigData['imprintPageId'] !== -1 ? $legacyConfigData['imprintPageId'] : $dialogSettingsDto->imprintPageId);
        $dialogSettingsDto->imprintPageUrl = (string) ($legacyConfigData['imprintPageURL'] ?? $dialogSettingsDto->imprintPageUrl);
        $dialogSettingsDto->layout = (string) ($legacyConfigData['cookieBoxLayout'] ?? $dialogSettingsDto->layout);
        $dialogSettingsDto->legalInformationDescriptionConfirmAgeStatus = (bool) ($legacyConfigData['cookieBoxShowTextDescriptionConfirmAge'] ?? $dialogSettingsDto->legalInformationDescriptionConfirmAgeStatus);
        $dialogSettingsDto->legalInformationDescriptionIndividualSettingsStatus = (bool) ($legacyConfigData['cookieBoxShowTextDescriptionIndividualSettings'] ?? $dialogSettingsDto->legalInformationDescriptionIndividualSettingsStatus);
        $dialogSettingsDto->legalInformationDescriptionMoreInformationStatus = (bool) ($legacyConfigData['cookieBoxShowDescriptionMoreInformation'] ?? $dialogSettingsDto->legalInformationDescriptionMoreInformationStatus);
        $dialogSettingsDto->legalInformationDescriptionNonEuDataTransferStatus = (bool) ($legacyConfigData['cookieBoxShowTextDescriptionNonEUDataTransfer'] ?? $dialogSettingsDto->legalInformationDescriptionNonEuDataTransferStatus);
        $dialogSettingsDto->legalInformationDescriptionNoObligationStatus = (bool) ($legacyConfigData['cookieBoxShowTextDescriptionNoObligation'] ?? $dialogSettingsDto->legalInformationDescriptionNoObligationStatus);
        $dialogSettingsDto->legalInformationDescriptionPersonalDataStatus = (bool) ($legacyConfigData['cookieBoxShowTextDescriptionPersonalData'] ?? $dialogSettingsDto->legalInformationDescriptionPersonalDataStatus);
        $dialogSettingsDto->legalInformationDescriptionRevokeStatus = (bool) ($legacyConfigData['cookieBoxShowTextDescriptionRevoke'] ?? $dialogSettingsDto->legalInformationDescriptionRevokeStatus);
        $dialogSettingsDto->legalInformationDescriptionTechnologyStatus = (bool) ($legacyConfigData['cookieBoxShowTextDescriptionTechnology'] ?? $dialogSettingsDto->legalInformationDescriptionTechnologyStatus);
        $dialogSettingsDto->logo = (string) ($legacyConfigData['cookieBoxLogo'] ?? $dialogSettingsDto->logo);
        $dialogSettingsDto->logoHd = (string) ($legacyConfigData['cookieBoxLogoHD'] ?? $dialogSettingsDto->logoHd);
        $dialogSettingsDto->position = (string) ($legacyConfigData['cookieBoxPosition'] ?? $dialogSettingsDto->position);
        $dialogSettingsDto->privacyPageCustomUrl = (string) ($legacyConfigData['privacyPageCustomURL'] ?? $dialogSettingsDto->privacyPageCustomUrl);
        $dialogSettingsDto->privacyPageId = (int) (isset($legacyConfigData['privacyPageId']) && $legacyConfigData['privacyPageId'] !== -1 ? $legacyConfigData['privacyPageId'] : $dialogSettingsDto->privacyPageId);
        $dialogSettingsDto->privacyPageUrl = (string) ($legacyConfigData['privacyPageURL'] ?? $dialogSettingsDto->privacyPageUrl);
        $legacyServiceGroupJustification = (string) ($legacyConfigData['cookieBoxCookieGroupJustification'] ?? $dialogSettingsDto->serviceGroupJustification);

        if ($legacyServiceGroupJustification === 'space-between') {
            $legacyServiceGroupJustification = 'between';
        } elseif ($legacyServiceGroupJustification === 'space-around') {
            $legacyServiceGroupJustification = 'around';
        } else {
            $legacyServiceGroupJustification = $dialogSettingsDto->serviceGroupJustification;
        }

        $dialogSettingsDto->serviceGroupJustification = $legacyServiceGroupJustification;
        $dialogSettingsDto->showAcceptAllButton = (bool) ($legacyConfigData['cookieBoxShowAcceptAllButton'] ?? $dialogSettingsDto->showAcceptAllButton);
        $dialogSettingsDto->showAcceptOnlyEssentialButton = !($legacyConfigData['cookieBoxHideRefuseOption'] ?? !$dialogSettingsDto->showAcceptOnlyEssentialButton);
        $dialogSettingsDto->showBorlabsCookieBranding = (bool) ($legacyConfigData['supportBorlabsCookie'] ?? $dialogSettingsDto->showBorlabsCookieBranding);
        $dialogSettingsDto->showDialog = (bool) ($legacyConfigData['showCookieBox'] ?? $dialogSettingsDto->showDialog);
        $dialogSettingsDto->showDialogOnLoginPage = (bool) ($legacyConfigData['showCookieBoxOnLoginPage'] ?? $dialogSettingsDto->showDialogOnLoginPage);
        $dialogSettingsDto->showLogo = (bool) ($legacyConfigData['cookieBoxShowLogo'] ?? $dialogSettingsDto->showLogo);
        $dialogSettingsSaveStatus = $this->dialogSettingsConfig->save($dialogSettingsDto, $languageCode);

        $this->log->info(
            '[Import] Dialog settings config ({{ languageCode }}) imported: {{ status }}',
            [
                'languageCode' => $languageCode,
                'status' => $dialogSettingsSaveStatus ? 'Yes' : 'No',
            ],
        );
    }
}
