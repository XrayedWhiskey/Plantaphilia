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

namespace Borlabs\Cookie\Dto\Config;

/**
 * The **DialogStyleDto** class is used as a typed object that is passed within the system.
 *
 * The object contains styling properties to change the visual appearance of the dialog and other front-end parts of
 * Borlabs Cookie.
 *
 * @see \Borlabs\Cookie\System\Config\DialogStyleConfig
 */
final class DialogStyleDto extends AbstractConfigDto
{
    public string $customCss = '';

    /**
     * @var string The animation delay of the dialog in seconds. This value cannot be changed in the UI.
     */
    public string $dialogAnimationDelay = '2s';

    /**
     * @var string The animation duration of the dialog in seconds. This value cannot be changed in the UI.
     */
    public string $dialogAnimationDuration = '1s';

    public string $dialogBackdropBackgroundColor = '#000';

    public int $dialogBackdropBackgroundOpacity = 80;

    public string $dialogBackgroundColor = '#fff';

    public int $dialogBorderRadiusBottomLeft = 4;

    public int $dialogBorderRadiusBottomRight = 4;

    public int $dialogBorderRadiusTopLeft = 4;

    public int $dialogBorderRadiusTopRight = 4;

    public string $dialogButtonAcceptAllColor = '#2563eb';

    public string $dialogButtonAcceptAllColorHover = '#1e40af';

    public string $dialogButtonAcceptAllTextColor = '#fff';

    public string $dialogButtonAcceptAllTextColorHover = '#fff';

    public string $dialogButtonAcceptOnlyEssentialColor = '#2563eb';

    public string $dialogButtonAcceptOnlyEssentialColorHover = '#1e40af';

    public string $dialogButtonAcceptOnlyEssentialTextColor = '#fff';

    public string $dialogButtonAcceptOnlyEssentialTextColorHover = '#fff';

    public int $dialogButtonBorderRadiusBottomLeft = 4;

    public int $dialogButtonBorderRadiusBottomRight = 4;

    public int $dialogButtonBorderRadiusTopLeft = 4;

    public int $dialogButtonBorderRadiusTopRight = 4;

    public string $dialogButtonCloseColor = '#2563eb';

    public string $dialogButtonCloseColorHover = '#1e40af';

    public string $dialogButtonCloseTextColor = '#fff';

    public string $dialogButtonCloseTextColorHover = '#fff';

    public string $dialogButtonPreferencesColor = '#2563eb';

    public string $dialogButtonPreferencesColorHover = '#1e40af';

    public string $dialogButtonPreferencesTextColor = '#fff';

    public string $dialogButtonPreferencesTextColorHover = '#fff';

    public string $dialogButtonSaveConsentColor = '#2563eb';

    public string $dialogButtonSaveConsentColorHover = '#1e40af';

    public string $dialogButtonSaveConsentTextColor = '#fff';

    public string $dialogButtonSaveConsentTextColorHover = '#fff';

    public string $dialogButtonSelectionColor = '#000';

    public string $dialogButtonSelectionColorHover = '#262626';

    public string $dialogButtonSelectionTextColor = '#fff';

    public string $dialogButtonSelectionTextColorHover = '#fff';

    public string $dialogCardBackgroundColor = '#f7f7f7';

    public int $dialogCardBorderRadiusBottomLeft = 4;

    public int $dialogCardBorderRadiusBottomRight = 4;

    public int $dialogCardBorderRadiusTopLeft = 4;

    public int $dialogCardBorderRadiusTopRight = 4;

    public string $dialogCardControlElementColor = '#2563eb';

    public string $dialogCardControlElementColorHover = '#1e40af';

    public int $dialogCardListPaddingMediumScreenBottom = 24;

    public int $dialogCardListPaddingMediumScreenLeft = 24;

    public int $dialogCardListPaddingMediumScreenRight = 24;

    public int $dialogCardListPaddingMediumScreenTop = 0;

    public int $dialogCardListPaddingSmallScreenBottom = 16;

    public int $dialogCardListPaddingSmallScreenLeft = 16;

    public int $dialogCardListPaddingSmallScreenRight = 16;

    public int $dialogCardListPaddingSmallScreenTop = 0;

    public string $dialogCardSeparatorColor = '#e5e5e5';

    public string $dialogCardTextColor = '#555';

    public string $dialogCheckboxBackgroundColorActive = '#0063e3';

    public string $dialogCheckboxBackgroundColorDisabled = '#e6e6e6';

    public string $dialogCheckboxBackgroundColorInactive = '#fff';

    public string $dialogCheckboxBorderColorActive = '#0063e3';

    public string $dialogCheckboxBorderColorDisabled = '#e6e6e6';

    public string $dialogCheckboxBorderColorInactive = '#a72828';

    public int $dialogCheckboxBorderRadiusBottomLeft = 4;

    public int $dialogCheckboxBorderRadiusBottomRight = 4;

    public int $dialogCheckboxBorderRadiusTopLeft = 4;

    public int $dialogCheckboxBorderRadiusTopRight = 4;

    public string $dialogCheckboxCheckMarkColorActive = '#fff';

    public string $dialogCheckboxCheckMarkColorDisabled = '#999';

    public string $dialogControlElementColor = '#2563eb';

    public string $dialogControlElementColorHover = '#1e40af';

    /**
     * @deprecated 3.1.1 This property is deprecated and will be removed in a future version
     */
    public string $dialogCookieGroupJustification = 'space-between';

    public string $dialogFontFamily = 'inherit';

    public bool $dialogFontFamilyStatus = false;

    public int $dialogFontSize = 14;

    public string $dialogFooterBackgroundColor = '#f5f5f5';

    public string $dialogFooterTextColor = '#404040';

    public string $dialogLinkPrimaryColor = '#2563eb';

    public string $dialogLinkPrimaryColorHover = '#1e40af';

    public string $dialogLinkSecondaryColor = '#404040';

    public string $dialogLinkSecondaryColorHover = '#3b82f6';

    public int $dialogListBorderRadiusBottomLeft = 4;

    public int $dialogListBorderRadiusBottomRight = 4;

    public int $dialogListBorderRadiusTopLeft = 4;

    public int $dialogListBorderRadiusTopRight = 4;

    public string $dialogListItemBackgroundColorEven = '#fff';

    public string $dialogListItemBackgroundColorOdd = '#fff';

    public string $dialogListItemControlElementColor = '#262626';

    public string $dialogListItemControlElementColorHover = '#262626';

    public string $dialogListItemControlElementSeparatorColor = '#262626';

    public string $dialogListItemSeparatorColor = '#e5e5e5';

    public int $dialogListItemSeparatorWidth = 1;

    public string $dialogListItemTextColorEven = '#555';

    public string $dialogListItemTextColorOdd = '#555';

    public int $dialogListPaddingMediumScreenBottom = 12;

    public int $dialogListPaddingMediumScreenLeft = 12;

    public int $dialogListPaddingMediumScreenRight = 12;

    public int $dialogListPaddingMediumScreenTop = 12;

    public int $dialogListPaddingSmallScreenBottom = 8;

    public int $dialogListPaddingSmallScreenLeft = 8;

    public int $dialogListPaddingSmallScreenRight = 8;

    public int $dialogListPaddingSmallScreenTop = 8;

    public string $dialogSearchBarInputBackgroundColor = '#fff';

    public string $dialogSearchBarInputBorderColorDefault = '#ccc';

    public string $dialogSearchBarInputBorderColorFocus = '#2563eb';

    public int $dialogSearchBarInputBorderRadiusBottomLeft = 4;

    public int $dialogSearchBarInputBorderRadiusBottomRight = 4;

    public int $dialogSearchBarInputBorderRadiusTopLeft = 4;

    public int $dialogSearchBarInputBorderRadiusTopRight = 4;

    public int $dialogSearchBarInputBorderWidthBottom = 1;

    public int $dialogSearchBarInputBorderWidthLeft = 1;

    public int $dialogSearchBarInputBorderWidthRight = 1;

    public int $dialogSearchBarInputBorderWidthTop = 1;

    public string $dialogSearchBarInputTextColor = '#555';

    public string $dialogSeparatorColor = '#e5e5e5';

    public string $dialogSwitchButtonBackgroundColorActive = '#2563eb';

    public string $dialogSwitchButtonBackgroundColorInactive = '#bdc1c8';

    public string $dialogSwitchButtonColorActive = '#fff';

    public string $dialogSwitchButtonColorInactive = '#fff';

    public string $dialogTabBarTabBackgroundColorActive = '#2563eb';

    public string $dialogTabBarTabBackgroundColorInactive = '#fff';

    public string $dialogTabBarTabBorderColorBottomActive = '#0063e3';

    public string $dialogTabBarTabBorderColorBottomInactive = '#e6e6e6';

    public string $dialogTabBarTabBorderColorLeftActive = '#0063e3';

    public string $dialogTabBarTabBorderColorLeftInactive = '#e6e6e6';

    public string $dialogTabBarTabBorderColorRightActive = '#0063e3';

    public string $dialogTabBarTabBorderColorRightInactive = '#e6e6e6';

    public string $dialogTabBarTabBorderColorTopActive = '#0063e3';

    public string $dialogTabBarTabBorderColorTopInactive = '#e6e6e6';

    public int $dialogTabBarTabBorderRadiusBottomLeftActive = 0;

    public int $dialogTabBarTabBorderRadiusBottomLeftInactive = 0;

    public int $dialogTabBarTabBorderRadiusBottomRightActive = 0;

    public int $dialogTabBarTabBorderRadiusBottomRightInactive = 0;

    public int $dialogTabBarTabBorderRadiusTopLeftActive = 4;

    public int $dialogTabBarTabBorderRadiusTopLeftInactive = 4;

    public int $dialogTabBarTabBorderRadiusTopRightActive = 4;

    public int $dialogTabBarTabBorderRadiusTopRightInactive = 4;

    public int $dialogTabBarTabBorderWidthBottomActive = 2;

    public int $dialogTabBarTabBorderWidthBottomInactive = 2;

    public int $dialogTabBarTabBorderWidthLeftActive = 0;

    public int $dialogTabBarTabBorderWidthLeftInactive = 0;

    public int $dialogTabBarTabBorderWidthRightActive = 0;

    public int $dialogTabBarTabBorderWidthRightInactive = 0;

    public int $dialogTabBarTabBorderWidthTopActive = 0;

    public int $dialogTabBarTabBorderWidthTopInactive = 0;

    public string $dialogTabBarTabTextColorActive = '#fff';

    public string $dialogTabBarTabTextColorInactive = '#555';

    public int $dialogTableBorderRadiusBottomLeft = 0;

    public int $dialogTableBorderRadiusBottomRight = 0;

    public int $dialogTableBorderRadiusTopLeft = 0;

    public int $dialogTableBorderRadiusTopRight = 0;

    public int $dialogTableCellPaddingBottom = 8;

    public int $dialogTableCellPaddingLeft = 8;

    public int $dialogTableCellPaddingRight = 8;

    public int $dialogTableCellPaddingTop = 8;

    public string $dialogTableRowBackgroundColorEven = '#fcfcfc';

    public string $dialogTableRowBackgroundColorOdd = '#fafafa';

    public string $dialogTableRowBorderColor = '#e5e5e5';

    public string $dialogTableRowTextColorEven = '#555';

    public string $dialogTableRowTextColorOdd = '#555';

    public string $dialogTextColor = '#555';
}
