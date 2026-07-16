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

namespace Borlabs\Cookie\Controller\Admin\ContentBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerLanguageStringCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerLocationCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerOverviewLocalizationStrings;
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Provider\ProviderEditLocalizationStrings;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerDefaultSettingsFieldManager;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerService;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;
use Borlabs\Cookie\System\Provider\ProviderService;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

/**
 * The **ContentBlockerController** class takes care of displaying the Content Blocker section in the backend.
 * It also processes all requests that can be executed in the Content Blocker section.
 */
final class ContentBlockerController implements ControllerInterface
{
    use SettingsFieldListTrait;

    public const CONTROLLER_ID = 'borlabs-cookie-content-blocker';

    private ContentBlockerCreateEditLocalizationStrings $contentBlockerCreateEditLocalizationStrings;

    private ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFieldManager;

    private ContentBlockerRepository $contentBlockerRepository;

    private ContentBlockerService $contentBlockerService;

    private DefaultLocalizationStrings $defaultLocalizationStrings;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager  $messageManager;

    private ProviderRepository $providerRepository;

    private ProviderService $providerService;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ServiceRepository $serviceRepository;

    private StyleBuilder $styleBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        ContentBlockerCreateEditLocalizationStrings $contentBlockerCreateEditLocalizationStrings,
        ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFieldManager,
        ContentBlockerRepository $contentBlockerRepository,
        ContentBlockerService $contentBlockerService,
        DefaultLocalizationStrings $defaultLocalizationStrings,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        ProviderRepository $providerRepository,
        ProviderService $providerService,
        ScriptConfigBuilder $scriptConfigBuilder,
        ServiceRepository $serviceRepository,
        StyleBuilder $styleBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerCreateEditLocalizationStrings = $contentBlockerCreateEditLocalizationStrings;
        $this->contentBlockerDefaultSettingsFieldManager = $contentBlockerDefaultSettingsFieldManager;

        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->contentBlockerService = $contentBlockerService;
        $this->defaultLocalizationStrings = $defaultLocalizationStrings;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->providerRepository = $providerRepository;
        $this->providerService = $providerService;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->serviceRepository = $serviceRepository;
        $this->styleBuilder = $styleBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function delete(int $id): string
    {
        $contentBlocker = $this->contentBlockerRepository->findById($id);

        if ($contentBlocker === null) {
            // Note: no error message to prevent reload after delete from showing an error
            return $this->viewOverview();
        }

        if ($contentBlocker->undeletable) {
            $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['deleteNotAllowed']);

            return $this->viewOverview();
        }

        try {
            $this->contentBlockerRepository->deleteWithRelations($id);
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $this->language->getSelectedLanguageCode(),
            );
            $this->thirdPartyCacheClearerManager->clearCache();

            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['deletedSuccessfully']);

            return $this->viewOverview();
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }
        $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['deleteFailed']);

        return $this->viewOverview();
    }

    public function reset(): string
    {
        $this->contentBlockerService->reset();
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return $this->viewOverview();
    }

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        // Edit Content Blocker
        if ($action === 'edit') {
            return $this->viewEdit($id, $request->postData, $request->getData);
        }

        // Delete Content Blocker
        if ($action === 'delete') {
            return $this->delete($id);
        }

        // Reset default Content Blocker
        if ($action === 'reset') {
            return $this->reset();
        }

        // Switch status of Content Blocker
        if ($action === 'switch-status') {
            return $this->switchStatus($id);
        }

        // Create new or update existing Content Blocker
        if ($action === 'save') {
            return $this->save($id, $request->postData);
        }

        return $this->viewOverview();
    }

    public function save(int $id, array $postData): string
    {
        // Check Service
        $serviceId = null;

        if ($postData['serviceId'] !== '0') {
            $service = $this->serviceRepository->findById((int) $postData['serviceId']);
            $serviceId = $service->id ?? null;

            if ($serviceId === null) {
                $this->messageManager->error($this->contentBlockerCreateEditLocalizationStrings::get()['alert']['selectedServiceDoesNotExist']);
            }

            if ($service !== null && $service->language !== $this->language->getSelectedLanguageCode()) {
                $this->messageManager->error($this->contentBlockerCreateEditLocalizationStrings::get()['alert']['serviceNotOfCurrentLanguage']);
                $serviceId = null;
            }
        }

        // Check or handle Provider
        if (!isset($postData['providerId'])) {
            $providerId = $this->providerService->save(-1, $this->language->getSelectedLanguageCode(), $postData['provider']);
        } else {
            $provider = $this->providerRepository->findById((int) $postData['providerId']);
            $providerId = $provider->id ?? null;

            if ($providerId === null) {
                $this->messageManager->error($this->contentBlockerCreateEditLocalizationStrings::get()['alert']['selectedProviderDoesNotExist']);
            }

            // Check if Provider is of the current language
            if ($provider->language !== $this->language->getSelectedLanguageCode()) {
                $this->messageManager->error($this->contentBlockerCreateEditLocalizationStrings::get()['alert']['providerNotOfCurrentLanguage']);
                $providerId = null;
            }
        }

        if (!isset($providerId) || ($postData['serviceId'] !== '0' && $serviceId === null)) {
            return $this->viewEdit($id, $postData, []);
        }

        $postData['serviceId'] = (string) $serviceId;
        $postData['providerId'] = (string) $providerId;
        $contentBlockerId = $this->contentBlockerService->saveWithRelatedData($id, $this->language->getSelectedLanguageCode(), $postData);

        if ($contentBlockerId !== null) {
            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);
        }

        // Add service to additional languages
        if ($contentBlockerId !== null
            && (
                isset($postData['languages']['configuration'])
                || isset($postData['languages']['translation'])
            )
        ) {
            $this->contentBlockerService->createOrUpdateContentBlockerPerLanguage(
                $contentBlockerId,
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

        return $this->viewEdit($contentBlockerId ?? $id, $postData, []);
    }

    public function switchStatus(int $id): string
    {
        $this->contentBlockerRepository->switchStatus($id);
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['changedStatusSuccessfully']);

        return $this->viewOverview();
    }

    public function viewEdit(int $id, array $postData, array $getData): string
    {
        if ($id !== -1) {
            $contentBlocker = $this->contentBlockerRepository->findByIdOrFail($id, [
                'provider',
                'contentBlockerLocations',
            ]);

            // Check if content blocker is of the current language
            if ($contentBlocker->language !== $this->language->getSelectedLanguageCode()) {
                $this->messageManager->error($this->contentBlockerCreateEditLocalizationStrings::get()['alert']['contentBlockerNotOfCurrentLanguage']);

                return $this->viewOverview();
            }
        } else {
            $contentBlocker = new ContentBlockerModel();
            $contentBlocker->language = $this->language->getSelectedLanguageCode();
        }

        if (!empty($contentBlocker->description)) {
            $this->messageManager->info($contentBlocker->description);
        }

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['data'] = (array) $contentBlocker;
        $templateData['data'] = array_merge($templateData['data'], $postData, ['id' => $contentBlocker->id]);
        $unknownProvider = $this->providerRepository->getByKey('unknown');
        $templateData['data']['providerId'] = $contentBlocker->provider->id ?? $unknownProvider->id ?? null;
        $languageStrings = $contentBlocker->languageStrings ?? new KeyValueDtoList();

        if (isset($postData['languageStrings'])) {
            foreach ($postData['languageStrings'] as $languageStringPostData) {
                $languageStrings->add(new KeyValueDto($languageStringPostData['key'], $languageStringPostData['value']));

                foreach ($languageStrings->list as $index => $languageString) {
                    if ($languageString->key === $languageStringPostData['key']) {
                        // Update list item
                        $languageStrings->list[$index]->value = $languageStringPostData['value'];
                    }
                }
            }
        }

        $templateData['data']['languageStrings'] = $languageStrings;
        $repeatableSettingsFields = $contentBlocker->repeatableSettingsFields ?? null;

        if (isset($postData['repeatableSettingsFields'], $repeatableSettingsFields)) {
            foreach ($postData['repeatableSettingsFields'] as $repeatableSettingsFieldsPostData) {
                $repeatableSettingsFields = $this->updateRepeatableSettingsValuesFromFormFields(
                    $repeatableSettingsFields,
                    $repeatableSettingsFieldsPostData,
                );
            }
        }

        $templateData['data']['repeatableSettingsFields'] = $repeatableSettingsFields;
        $settingsFields = $contentBlocker->settingsFields ?? new SettingsFieldDtoList();
        $defaultSettingsFields = $this->contentBlockerDefaultSettingsFieldManager->get($this->language->getSelectedLanguageCode());

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
        $templateData['isCreateAction'] = $id === -1;
        $templateData['isEditAction'] = $id !== -1;
        $templateData['isProviderMismatch'] = $id !== -1 && isset($contentBlocker->providerId, $contentBlocker->serviceId) ? $this->isProviderMismatchWithService($contentBlocker->providerId, (int) $contentBlocker->serviceId) : false;
        $templateData['localized'] = $this->contentBlockerCreateEditLocalizationStrings::get();
        $templateData['localized']['contentBlockerLocation'] = ContentBlockerLocationCreateEditLocalizationStrings::get();
        $templateData['localized']['default'] = $this->defaultLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['localized']['languageString'] = ContentBlockerLanguageStringCreateEditLocalizationStrings::get();
        $templateData['localized']['provider'] = ProviderEditLocalizationStrings::get();
        $templateData['providers'] = Transformer::toKeyValueDtoList(
            $this->providerRepository->getAllOfSelectedLanguage(),
            'id',
            'name',
        );
        $templateData['services'] = Transformer::toKeyValueDtoList(
            $this->serviceRepository->getAllOfSelectedLanguage(),
            'id',
            'name',
        );
        $templateData['services']->add(
            new KeyValueDto('0', $this->contentBlockerCreateEditLocalizationStrings::get()['option']['noService']),
            true,
        );

        $templateData['localized']['thingsToKnow']['shortcodeExplained'] = Formatter::interpolate(
            $templateData['localized']['thingsToKnow']['shortcodeExplained'],
            [
                'shortcode' => '<span class="brlbs-cmpnt-code-example">[borlabs-cookie id="' . ($contentBlocker->key ?? 'abc-id') . '" type="content-blocker"]URL[/borlabs-cookie]</span>',
            ],
        );

        // Only edit:
        if ($templateData['isEditAction']) {
            $templateData['localized']['breadcrumb']['edit'] = Formatter::interpolate(
                $templateData['localized']['breadcrumb']['edit'],
                [
                    'name' => $contentBlocker->name,
                ],
            );
        }

        $templateData = $this->wpFunction->applyFilter('borlabsCookie/contentBlocker/view/edit/modifyTemplateData', $templateData);

        return $this->template->getEngine()->render(
            'content-blocker/content-blocker-manage/edit-content-blocker.html.twig',
            $templateData,
        );
    }

    public function viewOverview(): string
    {
        $contentBlockers = $this->contentBlockerRepository->getAllOfSelectedLanguage(true);

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = ContentBlockerOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['contentBlockers'] = $contentBlockers;

        return $this->template->getEngine()->render(
            'content-blocker/content-blocker-manage/overview-content-blocker.html.twig',
            $templateData,
        );
    }

    private function isProviderMismatchWithService(int $providerId, int $serviceId): bool
    {
        $service = $this->serviceRepository->findById($serviceId, ['provider']);

        if ($service === null) {
            return false;
        }

        return $service->provider->id !== $providerId;
    }
}
