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

use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Log\Log;

final class DialogStyleConfigImporter
{
    private DialogStyleConfig $dialogStyleConfig;

    private Log $log;

    public function __construct(DialogStyleConfig $dialogStyleConfig, Log $log)
    {
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->log = $log;
    }

    public function import($legacyConfigData, string $languageCode)
    {
        $dialogStyleDto = $this->dialogStyleConfig->defaultConfig();
        $dialogStyleDto->customCss = (string) ($legacyConfigData['cookieBoxCustomCSS'] ?? $dialogStyleDto->customCss);
        $dialogStyleDto->dialogBackdropBackgroundColor = (string) ($legacyConfigData['contentBlockerBgColor'] ?? $dialogStyleDto->dialogBackdropBackgroundColor);
        $dialogStyleDto->dialogBackgroundColor = (string) ($legacyConfigData['cookieBoxBgColor'] ?? $dialogStyleDto->dialogBackgroundColor);
        $dialogStyleDto->dialogBorderRadiusBottomLeft = (int) ($legacyConfigData['cookieBoxBorderRadius'] ?? $dialogStyleDto->dialogBorderRadiusBottomLeft);
        $dialogStyleDto->dialogBorderRadiusBottomRight = (int) ($legacyConfigData['cookieBoxBorderRadius'] ?? $dialogStyleDto->dialogBorderRadiusBottomRight);
        $dialogStyleDto->dialogBorderRadiusTopLeft = (int) ($legacyConfigData['cookieBoxBorderRadius'] ?? $dialogStyleDto->dialogBorderRadiusTopLeft);
        $dialogStyleDto->dialogBorderRadiusTopRight = (int) ($legacyConfigData['cookieBoxBorderRadius'] ?? $dialogStyleDto->dialogBorderRadiusTopRight);
        $dialogStyleDto->dialogButtonAcceptAllColor = (string) ($legacyConfigData['cookieBoxAcceptAllBtnColor'] ?? $dialogStyleDto->dialogButtonAcceptAllColor);
        $dialogStyleDto->dialogButtonAcceptAllColorHover = (string) ($legacyConfigData['cookieBoxAcceptAllBtnHoverColor'] ?? $dialogStyleDto->dialogButtonAcceptAllColorHover);
        $dialogStyleDto->dialogButtonAcceptAllTextColor = (string) ($legacyConfigData['cookieBoxAcceptAllBtnTxtColor'] ?? $dialogStyleDto->dialogButtonAcceptAllTextColor);
        $dialogStyleDto->dialogButtonAcceptAllTextColorHover = (string) ($legacyConfigData['cookieBoxAcceptAllBtnHoverTxtColor'] ?? $dialogStyleDto->dialogButtonAcceptAllTextColorHover);
        $dialogStyleDto->dialogButtonAcceptOnlyEssentialColor = (string) ($legacyConfigData['cookieBoxRefuseBtnColor'] ?? $dialogStyleDto->dialogButtonAcceptOnlyEssentialColor);
        $dialogStyleDto->dialogButtonAcceptOnlyEssentialColorHover = (string) ($legacyConfigData['cookieBoxRefuseBtnHoverColor'] ?? $dialogStyleDto->dialogButtonAcceptOnlyEssentialColorHover);
        $dialogStyleDto->dialogButtonAcceptOnlyEssentialTextColor = (string) ($legacyConfigData['cookieBoxRefuseBtnTxtColor'] ?? $dialogStyleDto->dialogButtonAcceptOnlyEssentialTextColor);
        $dialogStyleDto->dialogButtonAcceptOnlyEssentialTextColorHover = (string) ($legacyConfigData['cookieBoxRefuseBtnHoverTxtColor'] ?? $dialogStyleDto->dialogButtonAcceptOnlyEssentialTextColorHover);
        $dialogStyleDto->dialogButtonBorderRadiusBottomLeft = (int) ($legacyConfigData['cookieBoxBtnBorderRadius'] ?? $dialogStyleDto->dialogButtonBorderRadiusBottomLeft);
        $dialogStyleDto->dialogButtonBorderRadiusBottomRight = (int) ($legacyConfigData['cookieBoxBtnBorderRadius'] ?? $dialogStyleDto->dialogButtonBorderRadiusBottomRight);
        $dialogStyleDto->dialogButtonBorderRadiusTopLeft = (int) ($legacyConfigData['cookieBoxBtnBorderRadius'] ?? $dialogStyleDto->dialogButtonBorderRadiusTopLeft);
        $dialogStyleDto->dialogButtonBorderRadiusTopRight = (int) ($legacyConfigData['cookieBoxBtnBorderRadius'] ?? $dialogStyleDto->dialogButtonBorderRadiusTopRight);
        $dialogStyleDto->dialogButtonCloseColor = (string) ($legacyConfigData['cookieBoxBtnColor'] ?? $dialogStyleDto->dialogButtonCloseColor);
        $dialogStyleDto->dialogButtonCloseColorHover = (string) ($legacyConfigData['cookieBoxBtnHoverColor'] ?? $dialogStyleDto->dialogButtonCloseColorHover);
        $dialogStyleDto->dialogButtonCloseTextColor = (string) ($legacyConfigData['cookieBoxBtnTxtColor'] ?? $dialogStyleDto->dialogButtonCloseTextColor);
        $dialogStyleDto->dialogButtonCloseTextColorHover = (string) ($legacyConfigData['cookieBoxBtnHoverTxtColor'] ?? $dialogStyleDto->dialogButtonCloseTextColorHover);
        $dialogStyleDto->dialogButtonPreferencesColor = (string) ($legacyConfigData['cookieBoxIndividualSettingsBtnColor'] ?? $dialogStyleDto->dialogButtonPreferencesColor);
        $dialogStyleDto->dialogButtonPreferencesColorHover = (string) ($legacyConfigData['cookieBoxIndividualSettingsBtnHoverColor'] ?? $dialogStyleDto->dialogButtonPreferencesColorHover);
        $dialogStyleDto->dialogButtonPreferencesTextColor = (string) ($legacyConfigData['cookieBoxIndividualSettingsBtnTxtColor'] ?? $dialogStyleDto->dialogButtonPreferencesTextColor);
        $dialogStyleDto->dialogButtonPreferencesTextColorHover = (string) ($legacyConfigData['cookieBoxIndividualSettingsBtnHoverTxtColor'] ?? $dialogStyleDto->dialogButtonPreferencesTextColorHover);
        $dialogStyleDto->dialogButtonSaveConsentColor = (string) ($legacyConfigData['cookieBoxBtnColor'] ?? $dialogStyleDto->dialogButtonSaveConsentColor);
        $dialogStyleDto->dialogButtonSaveConsentColorHover = (string) ($legacyConfigData['cookieBoxBtnHoverColor'] ?? $dialogStyleDto->dialogButtonSaveConsentHoverColordialogButtonSaveConsentColorHover);
        $dialogStyleDto->dialogButtonSaveConsentTextColor = (string) ($legacyConfigData['cookieBoxBtnTxtColor'] ?? $dialogStyleDto->dialogButtonSaveConsentTextColor);
        $dialogStyleDto->dialogButtonSaveConsentTextColorHover = (string) ($legacyConfigData['cookieBoxBtnHoverTxtColor'] ?? $dialogStyleDto->dialogButtonSaveConsentTextColorHover);
        $dialogStyleDto->dialogButtonSelectionColor = (string) ($legacyConfigData['cookieBoxBtnColor'] ?? $dialogStyleDto->dialogButtonSelectionColor);
        $dialogStyleDto->dialogButtonSelectionColorHover = (string) ($legacyConfigData['cookieBoxBtnHoverColor'] ?? $dialogStyleDto->dialogButtonSelectionColorHover);
        $dialogStyleDto->dialogButtonSelectionTextColor = (string) ($legacyConfigData['cookieBoxBtnTxtColor'] ?? $dialogStyleDto->dialogButtonSelectionTextColor);
        $dialogStyleDto->dialogButtonSelectionTextColorHover = (string) ($legacyConfigData['cookieBoxBtnHoverTxtColor'] ?? $dialogStyleDto->dialogButtonSelectionTextColorHover);
        $dialogStyleDto->dialogCardBackgroundColor = (string) ($legacyConfigData['cookieBoxAccordionBgColor'] ?? $dialogStyleDto->dialogCardBackgroundColor);
        $dialogStyleDto->dialogCardBorderRadiusBottomLeft = (int) ($legacyConfigData['cookieBoxAccordionBorderRadius'] ?? $dialogStyleDto->dialogCardBorderRadiusBottomLeft);
        $dialogStyleDto->dialogCardBorderRadiusBottomRight = (int) ($legacyConfigData['cookieBoxAccordionBorderRadius'] ?? $dialogStyleDto->dialogCardBorderRadiusBottomRight);
        $dialogStyleDto->dialogCardBorderRadiusTopLeft = (int) ($legacyConfigData['cookieBoxAccordionBorderRadius'] ?? $dialogStyleDto->dialogCardBorderRadiusTopLeft);
        $dialogStyleDto->dialogCardBorderRadiusTopRight = (int) ($legacyConfigData['cookieBoxAccordionBorderRadius'] ?? $dialogStyleDto->dialogCardBorderRadiusTopRight);
        $dialogStyleDto->dialogCardControlElementColor = (string) ($legacyConfigData['cookieBoxPrimaryLinkColor'] ?? $dialogStyleDto->dialogCardControlElementColor);
        $dialogStyleDto->dialogCardControlElementColorHover = (string) ($legacyConfigData['cookieBoxPrimaryLinkHoverColor'] ?? $dialogStyleDto->dialogCardControlElementColorHover);
        $dialogStyleDto->dialogCardSeparatorColor = (string) ($legacyConfigData['cookieBoxPrimaryLinkColor'] ?? $dialogStyleDto->dialogCardSeparatorColor);
        $dialogStyleDto->dialogCardTextColor = (string) ($legacyConfigData['cookieBoxAccordionTxtColor'] ?? $dialogStyleDto->dialogCardTextColor);
        $dialogStyleDto->dialogCheckboxBackgroundColorActive = (string) ($legacyConfigData['cookieBoxCheckboxActiveBgColor'] ?? $dialogStyleDto->dialogCheckboxBackgroundColorActive);
        $dialogStyleDto->dialogCheckboxBackgroundColorDisabled = (string) ($legacyConfigData['cookieBoxCheckboxDisabledBgColor'] ?? $dialogStyleDto->dialogCheckboxBackgroundColorDisabled);
        $dialogStyleDto->dialogCheckboxBackgroundColorInactive = (string) ($legacyConfigData['cookieBoxCheckboxInactiveBgColor'] ?? $dialogStyleDto->dialogCheckboxBackgroundColorInactive);
        $dialogStyleDto->dialogCheckboxBorderColorActive = (string) ($legacyConfigData['cookieBoxCheckboxActiveBorderColor'] ?? $dialogStyleDto->dialogCheckboxBorderColorActive);
        $dialogStyleDto->dialogCheckboxBorderColorDisabled = (string) ($legacyConfigData['cookieBoxCheckboxDisabledBorderColor'] ?? $dialogStyleDto->dialogCheckboxBorderColorDisabled);
        $dialogStyleDto->dialogCheckboxBorderColorInactive = (string) ($legacyConfigData['cookieBoxCheckboxInactiveBorderColor'] ?? $dialogStyleDto->dialogCheckboxBorderColorInactive);
        $dialogStyleDto->dialogCheckboxBorderRadiusBottomLeft = (int) ($legacyConfigData['cookieBoxCheckboxBorderRadius'] ?? $dialogStyleDto->dialogCheckboxBorderRadiusBottomLeft);
        $dialogStyleDto->dialogCheckboxBorderRadiusBottomRight = (int) ($legacyConfigData['cookieBoxCheckboxBorderRadius'] ?? $dialogStyleDto->dialogCheckboxBorderRadiusBottomRight);
        $dialogStyleDto->dialogCheckboxBorderRadiusTopLeft = (int) ($legacyConfigData['cookieBoxCheckboxBorderRadius'] ?? $dialogStyleDto->dialogCheckboxBorderRadiusTopLeft);
        $dialogStyleDto->dialogCheckboxBorderRadiusTopRight = (int) ($legacyConfigData['cookieBoxCheckboxBorderRadius'] ?? $dialogStyleDto->dialogCheckboxBorderRadiusTopRight);
        $dialogStyleDto->dialogCheckboxCheckMarkColorActive = (string) ($legacyConfigData['cookieBoxCheckboxCheckMarkActiveColor'] ?? $dialogStyleDto->dialogCheckboxCheckMarkColorActive);
        $dialogStyleDto->dialogCheckboxCheckMarkColorDisabled = (string) ($legacyConfigData['cookieBoxCheckboxCheckMarkDisabledColor'] ?? $dialogStyleDto->dialogCheckboxCheckMarkColorDisabled);
        $dialogStyleDto->dialogControlElementColor = (string) ($legacyConfigData['cookieBoxBtnSwitchActiveBgColor'] ?? $dialogStyleDto->dialogControlElementColor);
        $dialogStyleDto->dialogControlElementColorHover = (string) ($legacyConfigData['cookieBoxBtnSwitchActiveBgColor'] ?? $dialogStyleDto->dialogControlElementColorHover);
        $dialogStyleDto->dialogFontFamily = (string) ($legacyConfigData['cookieBoxFontFamily'] ?? $dialogStyleDto->dialogFontFamily);
        $dialogStyleDto->dialogFontFamilyStatus = $dialogStyleDto->dialogFontFamily !== 'inherit';
        $dialogStyleDto->dialogFontSize = (int) ($legacyConfigData['cookieBoxFontSize'] ?? $dialogStyleDto->dialogFontSize);
        $dialogStyleDto->dialogFooterBackgroundColor = (string) ($legacyConfigData['cookieBoxBgColor'] ?? $dialogStyleDto->dialogFooterBackgroundColor);
        $dialogStyleDto->dialogFooterTextColor = (string) ($legacyConfigData['cookieBoxTxtColor'] ?? $dialogStyleDto->dialogFooterTextColor);
        $dialogStyleDto->dialogLinkPrimaryColor = (string) ($legacyConfigData['cookieBoxPrimaryLinkColor'] ?? $dialogStyleDto->dialogLinkPrimaryColor);
        $dialogStyleDto->dialogLinkPrimaryColorHover = (string) ($legacyConfigData['cookieBoxPrimaryLinkHoverColor'] ?? $dialogStyleDto->dialogLinkPrimaryColorHover);
        $dialogStyleDto->dialogLinkSecondaryColor = (string) ($legacyConfigData['cookieBoxSecondaryLinkColor'] ?? $dialogStyleDto->dialogLinkSecondaryColor);
        $dialogStyleDto->dialogLinkSecondaryColorHover = (string) ($legacyConfigData['cookieBoxSecondaryLinkHoverColor'] ?? $dialogStyleDto->dialogLinkSecondaryColorHover);
        $dialogStyleDto->dialogListBorderRadiusBottomLeft = (int) ($legacyConfigData['cookieBoxAccordionBorderRadius'] ?? $dialogStyleDto->dialogListBorderRadiusBottomLeft);
        $dialogStyleDto->dialogListBorderRadiusBottomRight = (int) ($legacyConfigData['cookieBoxAccordionBorderRadius'] ?? $dialogStyleDto->dialogListBorderRadiusBottomRight);
        $dialogStyleDto->dialogListBorderRadiusTopLeft = (int) ($legacyConfigData['cookieBoxAccordionBorderRadius'] ?? $dialogStyleDto->dialogListBorderRadiusTopLeft);
        $dialogStyleDto->dialogListBorderRadiusTopRight = (int) ($legacyConfigData['cookieBoxAccordionBorderRadius'] ?? $dialogStyleDto->dialogListBorderRadiusTopRight);
        $dialogStyleDto->dialogListItemBackgroundColorEven = (string) ($legacyConfigData['cookieBoxAccordionBgColor'] ?? $dialogStyleDto->dialogListItemBackgroundColorEven);
        $dialogStyleDto->dialogListItemBackgroundColorOdd = (string) ($legacyConfigData['cookieBoxAccordionBgColor'] ?? $dialogStyleDto->dialogListItemBackgroundColorOdd);
        $dialogStyleDto->dialogListItemControlElementColor = (string) ($legacyConfigData['cookieBoxPrimaryLinkColor'] ?? $dialogStyleDto->dialogListItemControlElementColor);
        $dialogStyleDto->dialogListItemControlElementColorHover = (string) ($legacyConfigData['cookieBoxPrimaryLinkHoverColor'] ?? $dialogStyleDto->dialogListItemControlElementColorHover);
        $dialogStyleDto->dialogListItemSeparatorColor = (string) ($legacyConfigData['cookieBoxPrimaryLinkColor'] ?? $dialogStyleDto->dialogListItemSeparatorColor);
        $dialogStyleDto->dialogListItemTextColorEven = (string) ($legacyConfigData['cookieBoxAccordionTxtColor'] ?? $dialogStyleDto->dialogListItemTextColorEven);
        $dialogStyleDto->dialogListItemTextColorOdd = (string) ($legacyConfigData['cookieBoxAccordionTxtColor'] ?? $dialogStyleDto->dialogListItemTextColorOdd);
        $dialogStyleDto->dialogSearchBarInputBackgroundColor = (string) ($legacyConfigData['cookieBoxBgColor'] ?? $dialogStyleDto->dialogSearchBarInputBackgroundColor);
        $dialogStyleDto->dialogSearchBarInputBorderColorDefault = (string) ($legacyConfigData['cookieBoxPrimaryLinkColor'] ?? $dialogStyleDto->dialogSearchBarInputBorderColorDefault);
        $dialogStyleDto->dialogSearchBarInputBorderColorFocus = (string) ($legacyConfigData['cookieBoxPrimaryLinkColor'] ?? $dialogStyleDto->dialogSearchBarInputBorderColorFocus);
        $dialogStyleDto->dialogSearchBarInputBorderRadiusBottomLeft = (int) ($legacyConfigData['cookieBoxBtnBorderRadius'] ?? $dialogStyleDto->dialogSearchBarInputBorderRadiusBottomLeft);
        $dialogStyleDto->dialogSearchBarInputBorderRadiusBottomRight = (int) ($legacyConfigData['cookieBoxBtnBorderRadius'] ?? $dialogStyleDto->dialogSearchBarInputBorderRadiusBottomRight);
        $dialogStyleDto->dialogSearchBarInputBorderRadiusTopLeft = (int) ($legacyConfigData['cookieBoxBtnBorderRadius'] ?? $dialogStyleDto->dialogSearchBarInputBorderRadiusTopLeft);
        $dialogStyleDto->dialogSearchBarInputBorderRadiusTopRight = (int) ($legacyConfigData['cookieBoxBtnBorderRadius'] ?? $dialogStyleDto->dialogSearchBarInputBorderRadiusTopRight);
        $dialogStyleDto->dialogSearchBarInputTextColor = (string) ($legacyConfigData['cookieBoxTxtColor'] ?? $dialogStyleDto->dialogSearchBarInputTextColor);
        $dialogStyleDto->dialogSeparatorColor = (string) ($legacyConfigData['cookieBoxPrimaryLinkColor'] ?? $dialogStyleDto->dialogSeparatorColor);
        $dialogStyleDto->dialogSwitchButtonBackgroundColorActive = (string) ($legacyConfigData['cookieBoxBtnSwitchActiveBgColor'] ?? $dialogStyleDto->dialogSwitchButtonBackgroundColorActive);
        $dialogStyleDto->dialogSwitchButtonBackgroundColorInactive = (string) ($legacyConfigData['cookieBoxBtnSwitchInactiveBgColor'] ?? $dialogStyleDto->dialogSwitchButtonBackgroundColorInactive);
        $dialogStyleDto->dialogSwitchButtonColorActive = (string) ($legacyConfigData['cookieBoxBtnSwitchActiveColor'] ?? $dialogStyleDto->dialogSwitchButtonColorActive);
        $dialogStyleDto->dialogSwitchButtonColorInactive = (string) ($legacyConfigData['cookieBoxBtnSwitchInactiveColor'] ?? $dialogStyleDto->dialogSwitchButtonColorInactive);
        $dialogStyleDto->dialogTabBarTabBackgroundColorActive = (string) ($legacyConfigData['cookieBoxAcceptAllBtnColor'] ?? $dialogStyleDto->dialogTabBarTabBackgroundColorActive);
        $dialogStyleDto->dialogTabBarTabBackgroundColorInactive = (string) ($legacyConfigData['cookieBoxBgColor'] ?? $dialogStyleDto->dialogTabBarTabBackgroundColorInactive);
        $dialogStyleDto->dialogTabBarTabBorderColorBottomActive = (string) ($legacyConfigData['cookieBoxAcceptAllBtnColor'] ?? $dialogStyleDto->dialogTabBarTabBorderColorBottomActive);
        $dialogStyleDto->dialogTabBarTabBorderColorBottomInactive = (string) ($legacyConfigData['cookieBoxAcceptAllBtnColor'] ?? $dialogStyleDto->dialogTabBarTabBorderColorBottomInactive);
        $dialogStyleDto->dialogTabBarTabBorderColorLeftActive = (string) ($legacyConfigData['cookieBoxAcceptAllBtnColor'] ?? $dialogStyleDto->dialogTabBarTabBorderColorLeftActive);
        $dialogStyleDto->dialogTabBarTabBorderColorLeftInactive = (string) ($legacyConfigData['cookieBoxBgColor'] ?? $dialogStyleDto->dialogTabBarTabBorderColorLeftInactive);
        $dialogStyleDto->dialogTabBarTabBorderColorRightActive = (string) ($legacyConfigData['cookieBoxAcceptAllBtnColor'] ?? $dialogStyleDto->dialogTabBarTabBorderColorRightActive);
        $dialogStyleDto->dialogTabBarTabBorderColorRightInactive = (string) ($legacyConfigData['cookieBoxBgColor'] ?? $dialogStyleDto->dialogTabBarTabBorderColorRightInactive);
        $dialogStyleDto->dialogTabBarTabBorderColorTopActive = (string) ($legacyConfigData['cookieBoxAcceptAllBtnColor'] ?? $dialogStyleDto->dialogTabBarTabBorderColorTopActive);
        $dialogStyleDto->dialogTabBarTabBorderColorTopInactive = (string) ($legacyConfigData['cookieBoxBgColor'] ?? $dialogStyleDto->dialogTabBarTabBorderColorTopInactive);
        $dialogStyleDto->dialogTabBarTabTextColorActive = (string) ($legacyConfigData['cookieBoxAcceptAllBtnTxtColor'] ?? $dialogStyleDto->dialogTabBarTabTextColorActive);
        $dialogStyleDto->dialogTabBarTabTextColorInactive = (string) ($legacyConfigData['cookieBoxTxtColor'] ?? $dialogStyleDto->dialogTabBarTabTextColorInactive);
        $dialogStyleDto->dialogTableBorderRadiusBottomLeft = (int) ($legacyConfigData['cookieBoxTableBorderRadius'] ?? $dialogStyleDto->dialogTableBorderRadiusBottomLeft);
        $dialogStyleDto->dialogTableBorderRadiusBottomRight = (int) ($legacyConfigData['cookieBoxTableBorderRadius'] ?? $dialogStyleDto->dialogTableBorderRadiusBottomRight);
        $dialogStyleDto->dialogTableBorderRadiusTopLeft = (int) ($legacyConfigData['cookieBoxTableBorderRadius'] ?? $dialogStyleDto->dialogTableBorderRadiusTopLeft);
        $dialogStyleDto->dialogTableBorderRadiusTopRight = (int) ($legacyConfigData['cookieBoxTableBorderRadius'] ?? $dialogStyleDto->dialogTableBorderRadiusTopRight);
        $dialogStyleDto->dialogTableRowBackgroundColorEven = (string) ($legacyConfigData['cookieBoxTableBgColor'] ?? $dialogStyleDto->dialogTableRowBackgroundColorEven);
        $dialogStyleDto->dialogTableRowBackgroundColorOdd = (string) ($legacyConfigData['cookieBoxTableBgColor'] ?? $dialogStyleDto->dialogTableRowBackgroundColorOdd);
        $dialogStyleDto->dialogTableRowBorderColor = (string) ($legacyConfigData['cookieBoxTableBorderColor'] ?? $dialogStyleDto->dialogTableRowBorderColor);
        $dialogStyleDto->dialogTableRowTextColorEven = (string) ($legacyConfigData['cookieBoxTableTxtColor'] ?? $dialogStyleDto->dialogTableRowTextColorEven);
        $dialogStyleDto->dialogTableRowTextColorOdd = (string) ($legacyConfigData['cookieBoxTableTxtColor'] ?? $dialogStyleDto->dialogTableRowTextColorOdd);
        $dialogStyleDto->dialogTextColor = (string) ($legacyConfigData['cookieBoxTxtColor'] ?? $dialogStyleDto->dialogTextColor);
        $dialogStyleSaveStatus = $this->dialogStyleConfig->save($dialogStyleDto, $languageCode);

        $this->log->info(
            '[Import] Dialog style config ({{ languageCode }}) imported: {{ status }}',
            [
                'languageCode' => $languageCode,
                'status' => $dialogStyleSaveStatus ? 'Yes' : 'No',
            ],
        );
    }
}
