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

namespace Borlabs\Cookie\Controller\Admin\Service;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Enum\Service\CookiePurposeEnum;
use Borlabs\Cookie\Enum\Service\CookieTypeEnum;
use Borlabs\Cookie\Enum\Service\ServiceOptionEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Provider\ProviderEditLocalizationStrings;
use Borlabs\Cookie\Localization\Service\ServiceCookieCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\Service\ServiceCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\Service\ServiceLocationCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\Service\ServiceOptionCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\Service\ServiceOverviewLocalizationStrings;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceLocationRepository;
use Borlabs\Cookie\Repository\Service\ServiceOptionRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;
use Borlabs\Cookie\System\Provider\ProviderService;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Service\ServiceCookieService;
use Borlabs\Cookie\System\Service\ServiceDefaultSettingsFieldManager;
use Borlabs\Cookie\System\Service\ServiceLocationService;
use Borlabs\Cookie\System\Service\ServiceOptionService;
use Borlabs\Cookie\System\Service\ServiceService;
use Borlabs\Cookie\System\ServiceGroup\ServiceGroupService;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

final class ServiceController implements ControllerInterface
{
    use SettingsFieldListTrait;

    public const CONTROLLER_ID = 'borlabs-cookie-services';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private ProviderRepository $providerRepository;

    private ProviderService $providerService;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ServiceCookieRepository $serviceCookieRepository;

    private ServiceCookieService $serviceCookieService;

    private ServiceCreateEditLocalizationStrings $serviceCreateEditLocalizationStrings;

    private ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceGroupService $serviceGroupService;

    private ServiceLocationRepository $serviceLocationRepository;

    private ServiceLocationService $serviceLocationService;

    private ServiceOptionRepository $serviceOptionRepository;

    private ServiceOptionService $serviceOptionService;

    private ServiceRepository $serviceRepository;

    private ServiceService $serviceService;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        ProviderRepository $providerRepository,
        ProviderService $providerService,
        ScriptConfigBuilder $scriptConfigBuilder,
        ServiceCookieRepository $serviceCookieRepository,
        ServiceCookieService $serviceCookieService,
        ServiceCreateEditLocalizationStrings $serviceCreateEditLocalizationStrings,
        ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceGroupService $serviceGroupService,
        ServiceLocationRepository $serviceLocationRepository,
        ServiceLocationService $serviceLocationService,
        ServiceOptionRepository $serviceOptionRepository,
        ServiceOptionService $serviceOptionService,
        ServiceRepository $serviceRepository,
        ServiceService $serviceService,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->providerRepository = $providerRepository;
        $this->providerService = $providerService;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->serviceCookieRepository = $serviceCookieRepository;
        $this->serviceCookieService = $serviceCookieService;
        $this->serviceCreateEditLocalizationStrings = $serviceCreateEditLocalizationStrings;
        $this->serviceDefaultSettingsFieldManager = $serviceDefaultSettingsFieldManager;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceGroupService = $serviceGroupService;
        $this->serviceLocationRepository = $serviceLocationRepository;
        $this->serviceLocationService = $serviceLocationService;
        $this->serviceOptionRepository = $serviceOptionRepository;
        $this->serviceOptionService = $serviceOptionService;
        $this->serviceRepository = $serviceRepository;
        $this->serviceService = $serviceService;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function delete(int $id): string
    {
        $service = $this->serviceRepository->findById($id);

        if ($service === null) {
            // Note: no error message to prevent reload after delete from showing an error
            return $this->viewOverview();
        }

        if ($service->undeletable) {
            $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['deleteNotAllowed']);

            return $this->viewOverview();
        }

        try {
            $this->serviceRepository->deleteWithRelations($service->id);
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $this->language->getSelectedLanguageCode(),
            );
            $this->thirdPartyCacheClearerManager->clearCache();

            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['deletedSuccessfully']);
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewOverview();
    }

    public function reset(): string
    {
        $success = $this->serviceService->reset();

        if ($success) {
            $this->thirdPartyCacheClearerManager->clearCache();

            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);
        } else {
            $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['actionFailed']);
        }

        return $this->viewOverview();
    }

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        // Edit Service
        if ($action === 'edit') {
            return $this->viewEdit($id, $request->postData, $request->getData);
        }

        // Delete Service
        if ($action === 'delete') {
            return $this->delete($id);
        }

        // Reset default Services
        if ($action === 'reset') {
            return $this->reset();
        }

        // Switch status of Service
        if ($action === 'switch-status') {
            return $this->switchStatus($id);
        }

        // Create new or update existing service
        if ($action === 'save') {
            return $this->save($id, $request->postData);
        }

        return $this->viewOverview();
    }

    public function save(int $id, array $postData): string
    {
        // Check Service Group
        $serviceGroupId = null;

        if (!isset($postData['serviceGroupId'])) {
            $this->messageManager->error($this->serviceCreateEditLocalizationStrings::get()['alert']['noServiceGroupSelected']);
        } else {
            $serviceGroup = $this->serviceGroupRepository->findById((int) $postData['serviceGroupId']);
            $serviceGroupId = $serviceGroup->id ?? null;

            if ($serviceGroupId === null) {
                $this->messageManager->error($this->serviceCreateEditLocalizationStrings::get()['alert']['selectedServiceGroupDoesNotExist']);
            }

            if ($serviceGroup !== null && $serviceGroup->language !== $this->language->getSelectedLanguageCode()) {
                $this->messageManager->error($this->serviceCreateEditLocalizationStrings::get()['alert']['serviceGroupNotOfCurrentLanguage']);
                $serviceGroupId = null;
            }
        }

        // Check or handle Provider
        if (!isset($postData['providerId'])) {
            $providerId = $this->providerService->save(-1, $this->language->getSelectedLanguageCode(), $postData['provider']);
        } else {
            $provider = $this->providerRepository->findById((int) $postData['providerId']);
            $providerId = $provider->id ?? null;

            if ($providerId === null) {
                $this->messageManager->error($this->serviceCreateEditLocalizationStrings::get()['alert']['selectedProviderDoesNotExist']);
            }

            // Check if Provider is of the current language
            if ($provider->language !== $this->language->getSelectedLanguageCode()) {
                $this->messageManager->error($this->serviceCreateEditLocalizationStrings::get()['alert']['providerNotOfCurrentLanguage']);
                $providerId = null;
            }
        }

        if (!isset($serviceGroupId, $providerId)) {
            return $this->viewEdit($id, $postData, []);
        }

        $postData['serviceGroupId'] = (string) $serviceGroupId;
        $postData['providerId'] = (string) $providerId;
        $serviceId = $this->serviceService->saveWithRelatedData($id, $this->language->getSelectedLanguageCode(), $postData);

        if ($serviceId !== null) {
            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);
        }

        // Add service to additional languages
        if ($serviceId !== null
            && (
                isset($postData['languages']['configuration'])
                || isset($postData['languages']['translation'])
            )
        ) {
            $this->serviceService->createOrUpdateServicePerLanguage(
                $serviceId,
                $postData,
                array_keys(
                    array_filter(
                        $postData['languages']['configuration'] ?? [],
                        fn ($checked) => $checked === '1',
                    ),
                ),
                array_keys(
                    array_filter(
                        $postData['languages']['translation'] ?? [],
                        fn ($checked) => $checked === '1',
                    ),
                ),
            );
        }

        $this->thirdPartyCacheClearerManager->clearCache();

        return $this->viewEdit($serviceId ?? $id, $postData, []);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     */
    public function switchStatus(int $id): string
    {
        $this->serviceRepository->switchStatus($id);
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['changedStatusSuccessfully']);

        return $this->viewOverview();
    }

    /**
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewEdit(int $id, array $postData, array $getData): string
    {
        if ($id !== -1) {
            $service = $this->serviceRepository->findByIdOrFail($id, [
                'provider',
                'serviceCookies',
                'serviceGroup',
                'serviceLocations',
                'serviceOptions',
            ]);

            // Check if service is of the current language
            if ($service->language !== $this->language->getSelectedLanguageCode()) {
                $this->messageManager->error($this->serviceCreateEditLocalizationStrings::get()['alert']['serviceNotOfCurrentLanguage']);

                return $this->viewOverview();
            }
        } else {
            $service = new ServiceModel();
            $service->language = $this->language->getSelectedLanguageCode();
            $service->serviceGroupId = (int) ($postData['serviceGroupId'] ?? $getData['serviceGroupId']);
        }

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['data'] = (array) $service;
        $templateData['data'] = array_merge($templateData['data'], $postData, ['id' => $service->id]);
        $unknownProvider = $this->providerRepository->getByKey('unknown');
        $templateData['data']['providerId'] = $service->provider->id ?? $unknownProvider->id ?? null;
        $repeatableSettingsFields = $service->repeatableSettingsFields ?? null;

        if (isset($postData['repeatableSettingsFields'], $repeatableSettingsFields)) {
            foreach ($postData['repeatableSettingsFields'] as $repeatableSettingsFieldsPostData) {
                $repeatableSettingsFields = $this->updateRepeatableSettingsValuesFromFormFields(
                    $repeatableSettingsFields,
                    $repeatableSettingsFieldsPostData,
                );
            }
        }

        $templateData['data']['repeatableSettingsFields'] = $repeatableSettingsFields;
        $settingsFields = $service->settingsFields ?? new SettingsFieldDtoList();
        $defaultSettingsFields = $this->serviceDefaultSettingsFieldManager->get($this->language->getSelectedLanguageCode());

        foreach ($defaultSettingsFields->list as $defaultSettingsField) {
            $settingsFields->add($defaultSettingsField, true);
        }

        if (isset($postData['settingsFields'])) {
            foreach ($postData['settingsFields'] as $settingsFieldsPostData) {
                $settingsFields = $this->updateSettingsValuesFromFormFields($settingsFields, $settingsFieldsPostData);
            }
        }

        $templateData['data']['settingsFields'] = $settingsFields;
        $templateData['language'] = $this->language->getSelectedLanguageCode();
        $templateData['languages'] = $this->language->getLanguageList();
        $templateData['enum']['cookiePurposes'] = CookiePurposeEnum::getLocalizedKeyValueList();
        $templateData['enum']['cookieTypes'] = CookieTypeEnum::getLocalizedKeyValueList();
        $templateData['enum']['serviceOptions'] = ServiceOptionEnum::getLocalizedKeyValueList();
        $templateData['isCreateAction'] = $id === -1;
        $templateData['isEditAction'] = $id !== -1;
        $templateData['localized'] = $this->serviceCreateEditLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['localized']['provider'] = ProviderEditLocalizationStrings::get();
        $templateData['localized']['serviceCookie'] = ServiceCookieCreateEditLocalizationStrings::get();
        $templateData['localized']['serviceLocation'] = ServiceLocationCreateEditLocalizationStrings::get();
        $templateData['localized']['serviceOption'] = ServiceOptionCreateEditLocalizationStrings::get();
        $templateData['providers'] = Transformer::toKeyValueDtoList(
            $this->providerRepository->getAllOfSelectedLanguage(),
            'id',
            'name',
        );
        $templateData['serviceGroups'] = Transformer::toKeyValueDtoList(
            $this->serviceGroupRepository->getAllOfSelectedLanguage(),
            'id',
            'name',
        );

        // Only edit:
        if ($templateData['isEditAction']) {
            $templateData['localized']['breadcrumb']['edit'] = Formatter::interpolate(
                $templateData['localized']['breadcrumb']['edit'],
                [
                    'name' => $service->name,
                ],
            );
        }

        /*
        // Only create:
        if ($templateData['isCreateAction']) {
        }

        $validationLocalization = ValidatorLocalizationStrings::get();
        $templateData['localized']['validation']['serviceId'] = Formatter::interpolate(
            $validationLocalization['isCertainCharacters'],
            [
                'fieldName' => $templateData['localized']['field']['serviceId'],
                'characterPool' => 'a-z - _',
            ],
        );
        $templateData['localized']['validation']['name'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            [
                'fieldName' => $templateData['localized']['field']['name'],
            ],
        );
        $templateData['localized']['validation']['partners'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            [
                'fieldName' => $templateData['localized']['field']['partners'],
            ],
        );
        $templateData['localized']['validation']['address'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            [
                'fieldName' => $templateData['localized']['field']['address'],
            ],
        );
        $templateData['localized']['validation']['providerName'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            [
                'fieldName' => $templateData['localized']['field']['providerName'],
            ],
        );
        */

        $templateData = $this->wpFunction->applyFilter('borlabsCookie/service/view/edit/modifyTemplateData', $templateData);

        return $this->template->getEngine()->render(
            'service/edit-create-service.html.twig',
            $templateData,
        );
    }

    public function viewOverview(): string
    {
        $serviceGroupsWithServices = $this->serviceGroupRepository->getAllOfSelectedLanguage(true);

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = ServiceOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['serviceGroupsWithServices'] = $serviceGroupsWithServices;

        return $this->template->getEngine()->render(
            'service/overview-service.html.twig',
            $templateData,
        );
    }
}
