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

namespace Borlabs\Cookie\Controller\Admin\Dialog;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\Adapter\WpGetPagesArgumentDto;
use Borlabs\Cookie\Dto\Config\DialogSettingsDto;
use Borlabs\Cookie\Dto\Config\LanguageOptionDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\Config\LanguageOptionDtoList;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\Dialog\DialogSettingsLocalizationStrings;
use Borlabs\Cookie\Localization\GeoIp\CountryLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Repository\Country\CountryRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\Support\Validator;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\GeoIp\GeoIp;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

final class DialogSettingsController implements ControllerInterface, ExtendedRouteValidationInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-dialog-settings';

    private CountryRepository $countryRepository;

    private DialogSettingsConfig $dialogSettingsConfig;

    private DialogSettingsLocalizationStrings $dialogSettingsLocalizationStrings;

    private DialogStyleConfig $dialogStyleConfig;

    private GeoIp $geoIp;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private IabTcfConfig $iabTcfConfig;

    private Language $language;

    private MessageManager $messageManager;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private StyleBuilder $styleBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        CountryRepository $countryRepository,
        DialogSettingsConfig $dialogSettingsConfig,
        DialogSettingsLocalizationStrings $dialogSettingsLocalizationStrings,
        DialogStyleConfig $dialogStyleConfig,
        GeoIp $geoIp,
        GlobalLocalizationStrings $globalLocalizationStrings,
        IabTcfConfig $iabTcfConfig,
        Language $language,
        MessageManager $messageManager,
        ScriptConfigBuilder $scriptConfigBuilder,
        StyleBuilder $styleBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->countryRepository = $countryRepository;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->dialogSettingsLocalizationStrings = $dialogSettingsLocalizationStrings;
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->geoIp = $geoIp;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->styleBuilder = $styleBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function reset(): string
    {
        // Save config
        $this->dialogSettingsConfig->save(
            $this->dialogSettingsConfig->defaultConfig(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return $this->viewOverview();
    }

    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        try {
            if ($action === 'reset') {
                return $this->reset();
            }

            if ($action === 'save') {
                return $this->save($request->postData);
            }
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewOverview();
    }

    /**
     * Updates the configuration.
     *
     * @param array<string> $postData
     */
    public function save(array $postData): string
    {
        $defaultDialogSettingsConfig = $this->dialogSettingsConfig->defaultConfig();
        $iabTcfConfig = $this->iabTcfConfig->get();
        $dialogSettingsConfig = $this->dialogSettingsConfig->get();
        $previousGeoIpActiveStatus = $dialogSettingsConfig->geoIpActive;
        $dialogSettingsConfig = $this->dialogSettingsConfig->mapPostDataToProperties($postData, $dialogSettingsConfig);
        $dialogSettingsConfig->showBorlabsCookieBranding = (bool) ($iabTcfConfig->iabTcfStatus ?: $postData['showBorlabsCookieBranding']);
        $dialogSettingsConfig->buttonDetailsOrder = Validator::isStringJSON($postData['buttonDetailsOrder'] ?? '')
            ? Sanitizer::filterAllowedValues(json_decode($postData['buttonDetailsOrder']), $defaultDialogSettingsConfig->buttonDetailsOrder)
            : $defaultDialogSettingsConfig->buttonDetailsOrder;
        $dialogSettingsConfig->buttonEntranceOrder = Validator::isStringJSON($postData['buttonEntranceOrder'] ?? '')
            ? Sanitizer::filterAllowedValues(json_decode($postData['buttonEntranceOrder']), $defaultDialogSettingsConfig->buttonEntranceOrder)
            : $defaultDialogSettingsConfig->buttonEntranceOrder;
        // The settings for `showAcceptAllButton`, `showAcceptOnlyEssentialButton` and `showSaveButton` must be set before calling this method
        $dialogSettingsConfig->buttonDetailsOrder = $this->placeDisabledButtonsAtTheEnd(
            $dialogSettingsConfig->buttonDetailsOrder,
            $dialogSettingsConfig,
        );
        $dialogSettingsConfig->buttonEntranceOrder = $this->placeDisabledButtonsAtTheEnd(
            $dialogSettingsConfig->buttonEntranceOrder,
            $dialogSettingsConfig,
            true,
        );

        // Privacy URL
        $dialogSettingsConfig->privacyPageId = 0;
        $dialogSettingsConfig->privacyPageCustomUrl = '';
        $dialogSettingsConfig->privacyPageUrl = '';

        if ((int) ($postData['privacyPageId'] ?? 0) > 0) {
            $dialogSettingsConfig->privacyPageId = (int) $postData['privacyPageId'];
            $dialogSettingsConfig->privacyPageUrl = $this->wpFunction->getPermalink($dialogSettingsConfig->privacyPageId) ?? '';
        }

        if (($postData['enablePrivacyPageCustomUrl'] ?? null) === '1' && $postData['privacyPageCustomUrl'] ?? null !== '') {
            if (filter_var($postData['privacyPageCustomUrl'], FILTER_VALIDATE_URL)) {
                $dialogSettingsConfig->privacyPageId = 0;
                $dialogSettingsConfig->privacyPageUrl = $postData['privacyPageCustomUrl'];
                $dialogSettingsConfig->privacyPageCustomUrl = $postData['privacyPageCustomUrl'];
            }
        }

        // Imprint URL
        $dialogSettingsConfig->imprintPageId = 0;
        $dialogSettingsConfig->imprintPageCustomUrl = '';
        $dialogSettingsConfig->imprintPageUrl = '';

        if ((int) ($postData['imprintPageId'] ?? 0) > 0) {
            $dialogSettingsConfig->imprintPageId = (int) $postData['imprintPageId'];
            $dialogSettingsConfig->imprintPageUrl = $this->wpFunction->getPermalink($dialogSettingsConfig->imprintPageId) ?? '';
        }

        if (($postData['enableImprintPageCustomUrl'] ?? null) === '1' && $postData['imprintPageCustomUrl'] ?? null !== '') {
            if (filter_var($postData['imprintPageCustomUrl'], FILTER_VALIDATE_URL)) {
                $dialogSettingsConfig->imprintPageId = 0;
                $dialogSettingsConfig->imprintPageUrl = $postData['imprintPageCustomUrl'];
                $dialogSettingsConfig->imprintPageCustomUrl = $postData['imprintPageCustomUrl'];
            }
        }

        // Hide dialog on page
        $dialogSettingsConfig->hideDialogOnPages = [];

        if (!empty($postData['hideDialogOnPages'])) {
            $postData['hideDialogOnPages'] = stripslashes($postData['hideDialogOnPages']);
            $postData['hideDialogOnPages'] = preg_split('/\r\n|[\r\n]/', $postData['hideDialogOnPages']);

            if (!empty($postData['hideDialogOnPages'])) {
                foreach ($postData['hideDialogOnPages'] as $path) {
                    $path = trim(stripslashes($path));

                    if (!empty($path)) {
                        $dialogSettingsConfig->hideDialogOnPages[] = $path;
                    }
                }
            }
        }

        // GeoIp
        $dialogSettingsConfig->geoIpCountriesWithHiddenDialog = [];

        if (isset($postData['geoIpCountriesWithHiddenDialog']) && is_array($postData['geoIpCountriesWithHiddenDialog'])) {
            $dialogSettingsConfig->geoIpCountriesWithHiddenDialog = Sanitizer::filterAllowedValues(
                $postData['geoIpCountriesWithHiddenDialog'],
                $this->countryRepository->getAllCountryCodes(),
            );
        }

        // On GeoIp activation
        if ($previousGeoIpActiveStatus !== $dialogSettingsConfig->geoIpActive && $dialogSettingsConfig->geoIpActive) {
            try {
                $this->geoIp->downloadGeoIpDatabase(true);
            } catch (TranslatedException $e) {
                $this->messageManager->error($e->getTranslatedMessage());
            } catch (GenericException $e) {
                $this->messageManager->error($e->getMessage());
            }
        }

        // Language Switcher
        $dialogSettingsConfig->languageOptions = new LanguageOptionDtoList();

        if (isset($postData['languageOptions']) && is_array($postData['languageOptions'])) {
            foreach ($postData['languageOptions'] as $languageOption) {
                $dialogSettingsConfig->languageOptions->add(
                    new LanguageOptionDto(
                        $languageOption['code'] ?? 'missing',
                        $languageOption['name'] ?? 'missing',
                        $languageOption['url'] ?? 'missing',
                    ),
                );
            }
        }

        // Save config for other languages
        $languages = array_keys(
            array_filter(
                $postData['languages']['configuration'] ?? [],
                fn ($checked) => $checked === '1',
            ),
        );

        foreach ($languages as $languageCode) {
            $this->dialogSettingsConfig->save($dialogSettingsConfig, $languageCode);
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $languageCode,
            );
            $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
                $this->wpFunction->getCurrentBlogId(),
                $languageCode,
            );
        }

        // Save config for this language. The save routine also updates the current language object.
        $this->dialogSettingsConfig->save($dialogSettingsConfig, $this->language->getSelectedLanguageCode());
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return $this->viewOverview();
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        /*
         * The button to download the GeoIP database is in the same form as the settings,
         * so it shares the nonce of the "save" action.
         */
        if (in_array($request->postData['action'] ?? '', ['downloadGeoIpDatabase'], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-save', $nonce)
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = $this->dialogSettingsLocalizationStrings::get();
        $templateData['localized']['countries'] = CountryLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = (array) $this->dialogSettingsConfig->get();
        $templateData['languages'] = $this->language->getLanguageList();

        // Get all pages
        $pages = $this->wpFunction->getPages(new WpGetPagesArgumentDto());
        $templateData['options']['pages'] = Transformer::toKeyValueDtoList($pages, 'ID', 'post_title');

        // Add default select option
        $templateData['options']['pages']->add(
            new KeyValueDto('0', $this->globalLocalizationStrings::get()['option']['defaultSelectOption']),
            true,
        );

        $buttonDetailsOrderList = new KeyValueDtoList();

        foreach ($this->dialogSettingsConfig->get()->buttonDetailsOrder as $buttonKey) {
            $buttonDetailsOrderList->add(
                new KeyValueDto(
                    $buttonKey,
                    $this->dialogSettingsLocalizationStrings::get()['option']['buttonDetailsOrder' . ucfirst($buttonKey)] ?? $buttonKey,
                ),
            );
        }
        $templateData['options']['buttonDetails'] = $buttonDetailsOrderList;

        $buttonEntranceOrderList = new KeyValueDtoList();

        foreach ($this->dialogSettingsConfig->get()->buttonEntranceOrder as $buttonKey) {
            $buttonEntranceOrderList->add(
                new KeyValueDto(
                    $buttonKey,
                    $this->dialogSettingsLocalizationStrings::get()['option']['buttonEntranceOrder' . ucfirst($buttonKey)] ?? $buttonKey,
                ),
            );
        }
        $templateData['options']['buttonEntrance'] = $buttonEntranceOrderList;

        // GeoIp
        $templateData['options']['countriesGroupedByUnion'] = $this->geoIp->getAllCountriesGroupedByUnionsLocalized();
        $templateData['data']['geoIpDatabaseImported'] = $this->geoIp->isGeoIpDatabaseDownloaded();
        $lastSuccessfulCheckWithApiTimestamp = $this->geoIp->getLastSuccessfulCheckWithApiTimestamp();
        $templateData['data']['geoIpLastSuccessfulCheckWithApiFormattedTime']
            = $lastSuccessfulCheckWithApiTimestamp === null
            ? '-' : Formatter::timestamp($lastSuccessfulCheckWithApiTimestamp);

        $templateData['localized']['hint']['useBaseFontSizeForDescriptions'] = Formatter::interpolate(
            $templateData['localized']['hint']['useBaseFontSizeForDescriptions'],
            [
                'baseFontSize' => $this->dialogStyleConfig->get()->dialogFontSize . ' px',
                'calculatedFontSize' => round($this->dialogStyleConfig->get()->dialogFontSize * .875, 2) . ' px',
            ],
        );
        $templateData['localized']['thingsToKnow']['howToDisplayUserIdA'] = Formatter::interpolate(
            $templateData['localized']['thingsToKnow']['howToDisplayUserIdA'],
            [
                'shortcode' => '<span class="brlbs-cmpnt-code-example">[borlabs-cookie type="uid"/]</span>',
            ],
        );
        $templateData['localized']['thingsToKnow']['howToDisplayUserIdB'] = Formatter::interpolate(
            $templateData['localized']['thingsToKnow']['howToDisplayUserIdB'],
            [
                'shortcode' => '<span class="brlbs-cmpnt-code-example">[borlabs-cookie type="consent-history"/]</span>',
            ],
        );
        $templateData['localized']['thingsToKnow']['howToDisplayUserIdC'] = Formatter::interpolate(
            $templateData['localized']['thingsToKnow']['howToDisplayUserIdC'],
            [
                'shortcode' => '<span class="brlbs-cmpnt-code-example">[borlabs-cookie type="service-list"/]</span>',
            ],
        );
        $templateData['localized']['thingsToKnow']['howToOptOutA'] = Formatter::interpolate(
            $templateData['localized']['thingsToKnow']['howToOptOutA'],
            [
                'shortcode' => '<span class="brlbs-cmpnt-code-example">[borlabs-cookie type="btn-switch-consent" id="ID of the Service"/]</span>',
            ],
        );
        $templateData['data']['iabTcf'] = $this->iabTcfConfig->get();

        return $this->template->getEngine()->render(
            'dialog/dialog-general/dialog-general.html.twig',
            $templateData,
        );
    }

    private function placeDisabledButtonsAtTheEnd(
        array $buttonOrder,
        DialogSettingsDto $dialogSettingsConfig,
        bool $isButtonEntranceOrder = false
    ): array {
        $newOrder = [];
        $disabledButtons = [];

        foreach ($buttonOrder as $button) {
            if ($button === 'all' && $dialogSettingsConfig->showAcceptAllButton === false) {
                $disabledButtons[] = $button;
            } elseif ($button === 'essential' && $dialogSettingsConfig->showAcceptOnlyEssentialButton === false) {
                $disabledButtons[] = $button;
            } elseif ($button === 'save' && $dialogSettingsConfig->showSaveButton === false && $isButtonEntranceOrder === true) {
                $disabledButtons[] = $button;
            } else {
                $newOrder[] = $button;
            }
        }

        return array_merge($newOrder, $disabledButtons);
    }
}
