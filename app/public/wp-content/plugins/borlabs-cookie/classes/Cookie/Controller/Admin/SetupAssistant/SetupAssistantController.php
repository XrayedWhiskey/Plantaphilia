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

namespace Borlabs\Cookie\Controller\Admin\SetupAssistant;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\Adapter\WpGetPagesArgumentDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Enum\SetupAssistant\SetupTypeEnum;
use Borlabs\Cookie\Enum\System\DisplayModeSettingsEnum;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\License\LicenseLocalizationStrings;
use Borlabs\Cookie\Localization\RestClient\RestClientLocalizationStrings;
use Borlabs\Cookie\Localization\SetupAssistant\SetupAssistantLocalizationStrings;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\Support\Validator;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\BorlabsCookieGlobalsService;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\Validator\License\LicenseValidator;
use DateTimeImmutable;

final class SetupAssistantController implements ControllerInterface, ExtendedRouteValidationInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-setup-assistant';

    private BorlabsCookieGlobalsService $borlabsCookieGlobalsService;

    private DialogSettingsConfig $dialogSettingsConfig;

    private DialogStyleConfig $dialogStyleConfig;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private License $license;

    private LicenseLocalizationStrings $licenseLocalizationStrings;

    private LicenseValidator $licenseValidator;

    private MessageManager $messageManager;

    private SetupAssistantLocalizationStrings $setupAssistantLocalizationStrings;

    private Template $template;

    private WpFunction $wpFunction;

    public function __construct(
        BorlabsCookieGlobalsService $borlabsCookieGlobalsService,
        DialogSettingsConfig $dialogSettingsConfig,
        DialogStyleConfig $dialogStyleConfig,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        License $license,
        LicenseLocalizationStrings $licenseLocalizationStrings,
        LicenseValidator $licenseValidator,
        MessageManager $messageManager,
        SetupAssistantLocalizationStrings $setupAssistantLocalizationStrings,
        Template $template,
        WpFunction $wpFunction
    ) {
        $this->borlabsCookieGlobalsService = $borlabsCookieGlobalsService;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->license = $license;
        $this->licenseLocalizationStrings = $licenseLocalizationStrings;
        $this->licenseValidator = $licenseValidator;
        $this->messageManager = $messageManager;
        $this->setupAssistantLocalizationStrings = $setupAssistantLocalizationStrings;
        $this->template = $template;
        $this->wpFunction = $wpFunction;
    }

    /**
     * Registers the license key for the current site.
     *
     * @param array<string> $postData only key 'licenseKey' is required by this method
     *
     * @throws \Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function register(array $postData): string
    {
        if (!$this->licenseValidator->isValid($postData)) {
            return $this->viewOverview();
        }

        $this->license->register($postData['licenseKey']);

        return $this->viewFlow($postData);
    }

    public function route(RequestDto $request): string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        try {
            if ($action === 'register-and-start') {
                return $this->register($request->postData);
            }
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        if ($action === 'start') {
            return $this->viewFlow($request->postData);
        }

        return $this->viewOverview($request->postData, $request->getData);
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        if (isset($request->postData['action'])
            && in_array($request->postData['action'], ['register-and-start', 'start'], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->postData['action'], $nonce)
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    public function viewFlow(array $postData = [])
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = $this->setupAssistantLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['localized']['license'] = $this->licenseLocalizationStrings::get();
        $templateData['localized']['restClient'] = RestClientLocalizationStrings::get();

        $setupType = SetupTypeEnum::QUICK();
        $setupSteps = 1;

        if (isset($postData['setupType']) && SetupTypeEnum::hasValue($postData['setupType'])) {
            $setupType = SetupTypeEnum::fromValue($postData['setupType']);

            if ($setupType->is(SetupTypeEnum::CUSTOM())) {
                $setupSteps += 7;
            } elseif ($setupType->is(SetupTypeEnum::GUIDED())) {
                $setupSteps += 12;
            }

            $setupSteps += ($this->language->isMultilanguagePluginActive() ? 0 : -1);
        }

        $this->borlabsCookieGlobalsService->addProperty('restClientLocalizationStrings', RestClientLocalizationStrings::get());
        $this->borlabsCookieGlobalsService->addProperty('setupAssistant', [
            'multilanguagePluginActive' => $this->language->isMultilanguagePluginActive(),
            'selectedLanguageCode' => $this->language->getSelectedLanguageCode(),
            'startedAt' => (new DateTimeImmutable())->getTimestamp(),
            'type' => $setupType,
        ]);
        $this->borlabsCookieGlobalsService->addProperty('colorAssistant', array_filter(
            Transformer::objectToArray($this->dialogStyleConfig->defaultConfig()) ?? [],
            fn ($value) => Validator::isHexColor($value) || is_int($value),
        ));

        $templateData['enum']['displayModeSettings'] = DisplayModeSettingsEnum::getLocalizedKeyValueList();
        $templateData['enum']['displayModeSettings']->sortListByPropertiesNaturally(['value']);
        $templateData['enum']['wordPressAdminSidebarMenuMode'] = WordPressAdminSidebarMenuModeEnum::getLocalizedKeyValueList();
        $templateData['enum']['wordPressAdminSidebarMenuMode']->sortListByPropertiesNaturally(['value']);

        $templateData['data']['dialogBackgroundColor'] = $this->dialogStyleConfig->defaultConfig()->dialogBackgroundColor;
        $templateData['data']['dialogButtonSaveConsentColor'] = $this->dialogStyleConfig->defaultConfig()->dialogButtonSaveConsentColor;
        $templateData['data']['dialogButtonSaveConsentTextColor'] = $this->dialogStyleConfig->defaultConfig()->dialogButtonSaveConsentTextColor;
        $templateData['data']['languageOptions'] = $this->language->getLanguageListWithUrls();
        $templateData['data']['languages'] = $this->language->getLanguageList();
        $templateData['data']['logo'] = $this->dialogSettingsConfig->defaultConfig()->logo;
        $templateData['data']['multilanguagePluginActive'] = $this->language->isMultilanguagePluginActive();
        $templateData['data']['setupSteps'] = $setupSteps;
        $templateData['data']['setupType'] = $setupType;

        // Get all pages
        $pages = $this->wpFunction->getPages(new WpGetPagesArgumentDto());
        $templateData['options']['pages'] = Transformer::toKeyValueDtoList($pages, 'ID', 'post_title');

        // Add default select option
        $templateData['options']['pages']->add(
            new KeyValueDto('0', $this->globalLocalizationStrings::get()['option']['defaultSelectOption']),
            true,
        );

        $templateData['localized']['text']['setupCompleteC'] = Formatter::interpolate(
            $templateData['localized']['text']['setupCompleteC'],
            [
                'mailRecipient' => $this->wpFunction->wpGetCurrentUser()->user_email,
            ],
        );

        return $this->template->getEngine()->render(
            'setup-assistant/setup-flow.html.twig',
            $templateData,
        );
    }

    public function viewOverview(array $postData = [], array $getData = []): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['enum']['setupType'] = SetupTypeEnum::getLocalizedKeyValueList();
        $templateData['localized'] = $this->setupAssistantLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['localized']['license'] = LicenseLocalizationStrings::get();
        $templateData['localized']['restClient'] = RestClientLocalizationStrings::get();
        $this->borlabsCookieGlobalsService->addProperty('restClientLocalizationStrings', RestClientLocalizationStrings::get());

        return $this->template->getEngine()->render(
            'setup-assistant/setup-assistant.html.twig',
            $templateData,
        );
    }
}
