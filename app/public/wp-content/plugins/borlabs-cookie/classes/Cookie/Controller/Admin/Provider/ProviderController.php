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

namespace Borlabs\Cookie\Controller\Admin\Provider;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Provider\ProviderEditLocalizationStrings;
use Borlabs\Cookie\Localization\Provider\ProviderOverviewLocalizationStrings;
use Borlabs\Cookie\Localization\ValidatorLocalizationStrings;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Provider\ProviderService;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

final class ProviderController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-provider';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private ProviderRepository $providerRepository;

    private ProviderService $providerService;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        ProviderRepository $providerRepository,
        ProviderService $providerService,
        ScriptConfigBuilder $scriptConfigBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager
    ) {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->providerRepository = $providerRepository;
        $this->providerService = $providerService;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
    }

    public function delete(int $id): string
    {
        $provider = $this->providerRepository->findById(
            $id,
            [
                'contentBlockers',
                'services',
            ],
        );

        if ($provider === null) {
            // Note: no error message to prevent reload after delete from showing an error
            return $this->viewOverview();
        }

        if ($provider->undeletable) {
            $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['deleteNotAllowed']);

            return $this->viewOverview();
        }

        try {
            $this->providerRepository->deleteWithRelationChecks($provider);
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $this->language->getSelectedLanguageCode(),
            );
            $this->thirdPartyCacheClearerManager->clearCache();

            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['deletedSuccessfully']);
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        }

        return $this->viewOverview();
    }

    public function reset(): string
    {
        $this->providerService->reset();
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return $this->viewOverview();
    }

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        // Edit Provider
        if ($action === 'edit') {
            return $this->viewEdit($id, $request->postData);
        }

        if ($action === 'reset') {
            return $this->reset();
        }

        if ($action === 'delete') {
            return $this->delete($id);
        }

        // Create new or update existing provider
        if ($action === 'save') {
            return $this->save($id, $request->postData);
        }

        return $this->viewOverview();
    }

    public function save(int $id, array $postData): string
    {
        $providerId = $this->providerService->save($id, $this->language->getSelectedLanguageCode(), $postData);

        if ($providerId !== null) {
            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);
        }

        if ($providerId !== null
            && (
                isset($postData['languages']['configuration'])
                || isset($postData['languages']['translation'])
            )
        ) {
            $this->providerService->handleAdditionalLanguages(
                $providerId,
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

        return $this->viewEdit($providerId ?? $id, $postData);
    }

    public function viewEdit(int $id, array $postData): ?string
    {
        if ($id !== -1) {
            $provider = $this->providerRepository->findById($id);

            // Check if service is of the current language
            if ($provider->language !== $this->language->getSelectedLanguageCode()) {
                $this->messageManager->error(ProviderEditLocalizationStrings::get()['alert']['providerNotOfCurrentLanguage']);

                return $this->viewOverview();
            }
        } else {
            $provider = new ProviderModel();
            $provider->language = $this->language->getSelectedLanguageCode();
        }

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['data'] = (array) $provider;
        $templateData['data'] = array_merge($templateData['data'], $postData, ['id' => $provider->id]);
        $templateData['isCreateAction'] = $id === -1;
        $templateData['isEditAction'] = $id !== -1;
        $templateData['languages'] = $this->language->getLanguageList();
        $templateData['localized'] = ProviderEditLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();

        if ($templateData['isEditAction']) {
            $templateData['localized']['breadcrumb']['edit'] = Formatter::interpolate(
                $templateData['localized']['breadcrumb']['edit'],
                [
                    'name' => $provider->name,
                ],
            );
        }

        // Prepare error messages for the validator
        $validationLocalization = ValidatorLocalizationStrings::get();
        $templateData['localized']['validation']['address'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            [
                'fieldName' => $templateData['localized']['field']['address'],
            ],
        );
        $templateData['localized']['validation']['cookieUrl'] = Formatter::interpolate(
            $validationLocalization['isUrl'],
            [
                'fieldName' => $templateData['localized']['field']['cookieUrl'],
            ],
        );
        $templateData['localized']['validation']['description'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            [
                'fieldName' => $templateData['localized']['field']['description'],
            ],
        );
        $templateData['localized']['validation']['name'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            [
                'fieldName' => $templateData['localized']['field']['name'],
            ],
        );
        $templateData['localized']['validation']['optOutUrl'] = Formatter::interpolate(
            $validationLocalization['isUrl'],
            [
                'fieldName' => $templateData['localized']['field']['cookieUrl'],
            ],
        );
        $templateData['localized']['validation']['privacyUrl'] = Formatter::interpolate(
            $validationLocalization['isUrl'],
            [
                'fieldName' => $templateData['localized']['field']['privacyUrl'],
            ],
        );

        return $this->template->getEngine()->render(
            'provider/edit-provider.html.twig',
            $templateData,
        );
    }

    public function viewOverview(): string
    {
        $provider = $this->providerRepository->getAllOfSelectedLanguage();

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = ProviderOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['provider'] = $provider;

        return $this->template->getEngine()->render(
            'provider/overview-provider.html.twig',
            $templateData,
        );
    }
}
