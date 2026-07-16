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

use Borlabs\Cookie\DtoList\Config\LanguageOptionDtoList;

/**
 * The **DialogSettingsDto** class is used as a typed object that is passed within the system.
 *
 * The object contains content and behavior configuration properties related to the Borlabs Cookie dialog.
 *
 * @see \Borlabs\Cookie\System\Config\DialogSettingsConfig
 */
final class DialogSettingsDto extends AbstractConfigDto
{
    /**
     * @var bool default: `true`; `true`: The appearance and disappearance of the dialog is animated
     */
    public bool $animation = true;

    /**
     * @var bool default: `false`; `true`: The animation is delayed by 1 second
     */
    public bool $animationDelay = false;

    /**
     * @var string default: `fadeInDown`; The appearance animation of the dialog
     */
    public string $animationIn = 'fadeInDown';

    /**
     * @var string default: `flipOutX`; The disappearance animation of the dialog
     */
    public string $animationOut = 'flipOutX';

    /**
     * @var array|string[] The order of button types that are displayed in the cookie details dialog.
     *                     `all`: Accept all button,
     *                     `essential`: Accept only essential button
     *                     `save`: Save button,
     */
    public array $buttonDetailsOrder = [
        'save',
        'all',
        'essential',
    ];

    /**
     * @var array|string[] The order of button types that are displayed in the cookie entrance dialog.
     *                     `all`: Accept all button,
     *                     `essential`: Accept only essential button
     *                     `preferences`: Preferences button that opens the cookie details dialog
     *                     `save`: Save button,
     */
    public array $buttonEntranceOrder = [
        'save',
        'all',
        'essential',
        'preferences',
    ];

    /**
     * @var bool default: `true`; `true`: Switch buttons are displayed with round borders
     */
    public bool $buttonSwitchRound = true;

    /**
     * @var bool default: `true`; `true`: A transparent div container below the dialog blocks website accessibility
     *           until consent is granted
     */
    public bool $enableBackdrop = true;

    /**
     * @var bool default: `false`; `true`: The geo ip feature is activated
     */
    public bool $geoIpActive = false;

    /**
     * @var bool Default: `false`; `true`: The caching mode of the geo ip feature is activated.
     *           If this is activated, the information whether the dialog is displayed is not stored in the HTML response
     *           but is fetched from the REST API.
     */
    public bool $geoIpCachingMode = false;

    /**
     * @var array Countries (two-letter code) for which the dialog is not displayed (e.g. US).
     */
    public array $geoIpCountriesWithHiddenDialog = [];

    /**
     * @var array list of URLs where the dialog is not displayed to visitors without consent
     */
    public array $hideDialogOnPages = [];

    /**
     * @var string a custom URL to the imprint page
     */
    public string $imprintPageCustomUrl = '';

    /**
     * @var int the page id of the imprint page
     */
    public int $imprintPageId = 0;

    /**
     * @var string The URL to the imprint page. If {@see \Borlabs\Cookie\Dto\Config\Dialog::$imprintedPageId} is `0`
     *             the value of {@see \Borlabs\Cookie\Dto\Config\Dialog::$imprintPageCustomUrl} is used.
     */
    public string $imprintPageUrl = '';

    public ?LanguageOptionDtoList $languageOptions = null;

    /**
     * @var string default: `box-compact`; The name of the layout used for the dialog
     */
    public string $layout = 'box-compact';

    /**
     * @var bool default: `true`; `true`: The text of DialogLocalizationDto::$legalInformationDescriptionConfirmAge is displayed
     *
     * @see DialogLocalizationDto::$legalInformationDescriptionConfirmAge
     */
    public bool $legalInformationDescriptionConfirmAgeStatus = true;

    /**
     * @var bool default: `true`; `true`: The text of DialogLocalizationDto::$legalInformationDescriptionIndividualSettings is displayed
     *
     * @see DialogLocalizationDto::$legalInformationDescriptionIndividualSettings
     */
    public bool $legalInformationDescriptionIndividualSettingsStatus = true;

    /**
     * @var bool default: `true`; `true`: The text of DialogLocalizationDto::$legalInformationDescriptionMoreInformation is displayed
     *
     * @see DialogLocalizationDto::$legalInformationDescriptionMoreInformation
     */
    public bool $legalInformationDescriptionMoreInformationStatus = true;

    /**
     * @var bool default: `true`; `true`: The text of DialogLocalizationDto::$legalInformationDescriptionNonEuDataTransfer is displayed
     *
     * @see DialogLocalizationDto::$legalInformationDescriptionNonEuDataTransfer
     */
    public bool $legalInformationDescriptionNonEuDataTransferStatus = true;

    /**
     * @var bool default: `true`; `true`: The text of DialogLocalizationDto::$legalInformationDescriptionNoObligation is displayed
     *
     * @see DialogLocalizationDto::$legalInformationDescriptionNoObligation
     */
    public bool $legalInformationDescriptionNoObligationStatus = true;

    /**
     * @var bool default: `true`; `true`: The text of DialogLocalizationDto::$legalInformationDescriptionPersonalData is displayed
     *
     * @see DialogLocalizationDto::$legalInformationDescriptionPersonalData
     */
    public bool $legalInformationDescriptionPersonalDataStatus = true;

    /**
     * @var bool default: `true`; `true`: The text of DialogLocalizationDto::$legalInformationDescriptionRevoke is displayed
     *
     * @see DialogLocalizationDto::$legalInformationDescriptionRevoke
     */
    public bool $legalInformationDescriptionRevokeStatus = true;

    /**
     * @var bool default: `true`; `true`: The text of DialogLocalizationDto::$legalInformationDescriptionTechnology is displayed
     *
     * @see DialogLocalizationDto::$legalInformationDescriptionTechnology
     */
    public bool $legalInformationDescriptionTechnologyStatus = true;

    /**
     * @var string Default: `/borlabs-cookie-logo.svg`; The URL to the logo that will be displayed in the dialog.
     */
    public string $logo = '/borlabs-cookie-logo.svg';

    /**
     * @var string Default: `/borlabs-cookie-logo.svg`; The URL to the logo in high quality that will be displayed in
     *             the dialog.
     */
    public string $logoHd = '/borlabs-cookie-logo.svg';

    /**
     * @var string default: `top-center`; The position of the dialog
     */
    public string $position = 'top-center';

    /**
     * @var string a custom URL to the privacy policy page
     */
    public string $privacyPageCustomUrl = '';

    /**
     * @var int the page id of the privacy policy page
     */
    public int $privacyPageId = 0;

    /**
     * @var string The URL to the privacy policy page. If {@see \Borlabs\Cookie\Dto\Config\Dialog::$privacyPageId} is
     *             `0` the value of {@see \Borlabs\Cookie\Dto\Config\Dialog::$privacyPageCustomUrl} is used.
     */
    public string $privacyPageUrl = '';

    /**
     * @var string default: `between`; Available options: `left`, `center`, `right`, `between`, `around`
     */
    public string $serviceGroupJustification = 'between';

    /**
     * @var bool default: `true`; `true`: A button giving consent to all services is displayed
     */
    public bool $showAcceptAllButton = true;

    /**
     * @var bool default: `true`; `false`: The button to accept only essential cookies/services is not available
     */
    public bool $showAcceptOnlyEssentialButton = true;

    /**
     * @var bool default: `true`; `true`: An icon and the text `Borlabs Cookie` is displayed in the dialog
     */
    public bool $showBorlabsCookieBranding = true;

    /**
     * @var bool default: `true`; `true`: The button to close the dialog is displayed. Only available for Dialog Entrance.
     */
    public bool $showCloseButton = true;

    /**
     * @var bool default: `true`; `true`: The dialog is displayed to visitors without consent
     */
    public bool $showDialog = true;

    /**
     * @var bool default: `true`; `true`: The dialog is displayed to visitors after an interaction like clicking, scrolling, or pressing a key
     */
    public bool $showDialogAfterUserInteraction = true;

    /**
     * @var bool default: `true`; `true`: The dialog is displayed to visitors without consent on the login page
     */
    public bool $showDialogOnLoginPage = false;

    /**
     * @var bool default: `true`; `true`: A separator below the heading is displayed
     */
    public bool $showHeadlineSeparator = true;

    /**
     * @var bool default: `true`; `true`: The {@see \Borlabs\Cookie\Dto\Config\DialogSettingsDto::$logo} is displayed
     */
    public bool $showLogo = true;

    /**
     * @var bool default: `true`; `true`: A button giving consent to selected services is displayed. Only available for Dialog Entrance.
     */
    public bool $showSaveButton = true;

    /**
     * @var bool default: `false`; `true`: Descriptions will use the base font size rather than the reduced variant
     */
    public bool $useBaseFontSizeForDescriptions = false;
}
