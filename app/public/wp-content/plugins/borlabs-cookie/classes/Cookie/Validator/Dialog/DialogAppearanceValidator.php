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

namespace Borlabs\Cookie\Validator\Dialog;

use Borlabs\Cookie\Localization\Dialog\DialogAppearanceLocalizationStrings;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

/**
 * Class DialogAppearanceValidator.
 */
final class DialogAppearanceValidator
{
    /**
     * @var \Borlabs\Cookie\Validator\Validator
     */
    private $validator;

    public function __construct(MessageManager $message)
    {
        $this->validator = new Validator($message, true);
    }

    public function isValid(array $postData): bool
    {
        $localization = DialogAppearanceLocalizationStrings::get();
        $l = $localization['field'];

        if (isset($postData['dialogFontFamilyStatus'])) {
            $this->validator->isNotEmptyString(
                $l['fontFamily'],
                $postData['dialogFontFamily'],
            );
        }
        $this->validator->isMinLengthCertainCharacters(
            $l['fontSize'],
            $postData['dialogFontSize'],
            1,
            '0-9',
        );
        $this->validator->isHexColor($l['dialog'] . ': ' . $l['backgroundColor'], $postData['dialogBackgroundColor']);
        $this->validator->isHexColor($l['dialog'] . ': ' . $l['textColor'], $postData['dialogTextColor']);
        $this->validator->isHexColor(
            $l['backdrop'] . ': ' . $l['backgroundColor'],
            $postData['dialogBackdropBackgroundColor'],
        );
        $this->validator->isMinLengthCertainCharacters(
            $l['backdrop'] . ': ' . $l['opacity'],
            $postData['dialogBackdropBackgroundOpacity'],
            1,
            '0-9',
        );
        // Buttons
        $this->validator->isHexColor(
            $l['dialogButtonSaveConsentColor'] . ': ' . $l['default'],
            $postData['dialogButtonSaveConsentColor'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonSaveConsentColor'] . ': ' . $l['hover'],
            $postData['dialogButtonSaveConsentColorHover'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonSaveConsentTextColor'] . ': ' . $l['default'],
            $postData['dialogButtonSaveConsentTextColor'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonSaveConsentTextColor'] . ': ' . $l['hover'],
            $postData['dialogButtonSaveConsentTextColorHover'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonAcceptOnlyEssentialColor'] . ': ' . $l['default'],
            $postData['dialogButtonAcceptOnlyEssentialColor'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonAcceptOnlyEssentialColor'] . ': ' . $l['hover'],
            $postData['dialogButtonAcceptOnlyEssentialColorHover'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonAcceptOnlyEssentialTextColor'] . ': ' . $l['default'],
            $postData['dialogButtonAcceptOnlyEssentialTextColor'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonAcceptOnlyEssentialTextColor'] . ': ' . $l['hover'],
            $postData['dialogButtonAcceptOnlyEssentialTextColorHover'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonAcceptAllColor'] . ': ' . $l['default'],
            $postData['dialogButtonAcceptAllColor'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonAcceptAllColor'] . ': ' . $l['hover'],
            $postData['dialogButtonAcceptAllColorHover'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonAcceptAllTextColor'] . ': ' . $l['default'],
            $postData['dialogButtonAcceptAllTextColor'],
        );
        $this->validator->isHexColor(
            $l['dialogButtonAcceptAllTextColor'] . ': ' . $l['hover'],
            $postData['dialogButtonAcceptAllTextColorHover'],
        );
        $this->validator->isHexColor(
            $l['dialogSwitchButtonBackgroundColor'] . ': ' . $l['active'],
            $postData['dialogSwitchButtonBackgroundColorActive'],
        );
        $this->validator->isHexColor(
            $l['dialogSwitchButtonBackgroundColor'] . ': ' . $l['inactive'],
            $postData['dialogSwitchButtonBackgroundColorInactive'],
        );
        $this->validator->isHexColor(
            $l['dialogSwitchButtonColor'] . ': ' . $l['active'],
            $postData['dialogSwitchButtonColorActive'],
        );
        $this->validator->isHexColor(
            $l['dialogSwitchButtonColor'] . ': ' . $l['inactive'],
            $postData['dialogSwitchButtonColorInactive'],
        );
        // Checkbox
        $this->validator->isHexColor(
            $l['dialogCheckboxActive'] . ': ' . $l['backgroundColor'],
            $postData['dialogCheckboxBackgroundColorActive'],
        );
        $this->validator->isHexColor(
            $l['dialogCheckboxActive'] . ': ' . $l['borderColor'],
            $postData['dialogCheckboxBorderColorActive'],
        );
        $this->validator->isHexColor(
            $l['dialogCheckboxInactive'] . ': ' . $l['backgroundColor'],
            $postData['dialogCheckboxBackgroundColorInactive'],
        );
        $this->validator->isHexColor(
            $l['dialogCheckboxInactive'] . ': ' . $l['borderColor'],
            $postData['dialogCheckboxBorderColorInactive'],
        );
        $this->validator->isHexColor(
            $l['dialogCheckboxDisabled'] . ': ' . $l['backgroundColor'],
            $postData['dialogCheckboxBackgroundColorDisabled'],
        );
        $this->validator->isHexColor(
            $l['dialogCheckboxDisabled'] . ': ' . $l['borderColor'],
            $postData['dialogCheckboxBorderColorDisabled'],
        );
        $this->validator->isHexColor(
            $l['dialogCheckMark'] . ': ' . $l['active'],
            $postData['dialogCheckboxCheckMarkColorActive'],
        );
        $this->validator->isHexColor(
            $l['dialogCheckMark'] . ': ' . $l['inactive'],
            $postData['dialogCheckboxCheckMarkColorDisabled'],
        );
        // Links
        $this->validator->isHexColor(
            $l['dialogLinkPrimaryColor'] . ': ' . $l['default'],
            $postData['dialogLinkPrimaryColor'],
        );
        $this->validator->isHexColor(
            $l['dialogLinkPrimaryColor'] . ': ' . $l['hover'],
            $postData['dialogLinkPrimaryColorHover'],
        );
        $this->validator->isHexColor(
            $l['dialogLinkSecondaryColor'] . ': ' . $l['default'],
            $postData['dialogLinkSecondaryColor'],
        );
        $this->validator->isHexColor(
            $l['dialogLinkSecondaryColor'] . ': ' . $l['hover'],
            $postData['dialogLinkSecondaryColorHover'],
        );

        return $this->validator->isValid();
    }
}
