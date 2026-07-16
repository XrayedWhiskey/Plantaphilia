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

namespace Borlabs\Cookie\Controller\Admin\ServiceGroup;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\ServiceGroup\ServiceGroupCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\ServiceGroup\ServiceGroupGeneralLocalizationStrings;
use Borlabs\Cookie\Localization\ServiceGroup\ServiceGroupOverviewLocalizationStrings;
use Borlabs\Cookie\Localization\ValidatorLocalizationStrings;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\ServiceGroup\ServiceGroupService;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;
use Borlabs\Cookie\Validator\ServiceGroup\ServiceGroupValidator;

/**
 * Class ServiceGroups.
 */
final class ServiceGroupController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-service-groups';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ServiceGroupGeneralLocalizationStrings $serviceGroupGeneralLocalizationStrings;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceGroupService $serviceGroupService;

    private ServiceGroupValidator $serviceGroupValidator;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    public function __construct(
        Language $language,
        GlobalLocalizationStrings $globalLocalizationStrings,
        MessageManager $messageManager,
        ScriptConfigBuilder $scriptConfigBuilder,
        ServiceGroupGeneralLocalizationStrings $serviceGroupGeneralLocalizationStrings,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceGroupService $serviceGroupService,
        ServiceGroupValidator $serviceGroupValidator,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager
    ) {
        $this->language = $language;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->messageManager = $messageManager;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->serviceGroupGeneralLocalizationStrings = $serviceGroupGeneralLocalizationStrings;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceGroupService = $serviceGroupService;
        $this->serviceGroupValidator = $serviceGroupValidator;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
    }

    /**
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function delete(int $id): string
    {
        $serviceGroup = $this->serviceGroupRepository->findById($id);

        if ($serviceGroup === null) {
            // Note: no error message to prevent reload after delete from showing an error
            return $this->viewOverview();
        }

        // Check if no service is linked to this service group
        if ($this->serviceGroupRepository->hasService($serviceGroup)) {
            $this->messageManager->error($this->serviceGroupGeneralLocalizationStrings::get()['alert']['cannotDeleteServiceGroupWithService']);

            return $this->viewOverview();
        }

        try {
            $this->serviceGroupRepository->delete($serviceGroup);
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

    /**
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function reset(): string
    {
        $success = $this->serviceGroupService->reset();

        if ($success) {
            $this->thirdPartyCacheClearerManager->clearCache();

            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);
        } else {
            $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['actionFailed']);
        }

        return $this->viewOverview();
    }

    /**
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     *
     * @return string
     */
    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        // Create new or update existing service group
        if ($action === 'save') {
            return $this->save($id, $request->postData);
        }

        // Edit Service Group
        if ($action === 'edit') {
            return $this->viewEdit($id, []);
        }

        // Switch status of Service Group
        if ($action === 'switch-status') {
            return $this->switchStatus($id);
        }

        // Delete Service Group
        if ($action === 'delete') {
            return $this->delete($id);
        }

        // Reset default Service Groups
        if ($action === 'reset') {
            return $this->reset();
        }

        return $this->viewOverview();
    }

    public function save(int $id, array $postData): string
    {
        $serviceGroupId = $this->serviceGroupService->save($id, $this->language->getSelectedLanguageCode(), $postData);

        if ($serviceGroupId !== null) {
            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);
        }

        if ($serviceGroupId !== null
            && (
                isset($postData['languages']['configuration'])
                || isset($postData['languages']['translation'])
            )
        ) {
            $this->serviceGroupService->handleAdditionalLanguages(
                $serviceGroupId,
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

        return $this->viewEdit($serviceGroupId ?? -1, $postData);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function switchStatus(int $id): string
    {
        $this->serviceGroupRepository->switchStatus($id);
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['changedStatusSuccessfully']);

        return $this->viewOverview();
    }

    /**
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewEdit(int $id, array $postData): string
    {
        if ($id !== -1) {
            $serviceGroup = $this->serviceGroupRepository->findById($id);
        } else {
            $serviceGroup = new ServiceGroupModel();
            $serviceGroup->language = $this->language->getSelectedLanguageCode();
        }

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['data'] = (array) $serviceGroup;
        $templateData['data'] = array_merge($templateData['data'], $postData, ['id' => $serviceGroup->id]);
        $templateData['isCreateAction'] = $id === -1;
        $templateData['isEditAction'] = $id !== -1;
        $templateData['languages'] = $this->language->getLanguageList();
        $templateData['localized'] = ServiceGroupCreateEditLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();

        $validationLocalization = ValidatorLocalizationStrings::get();
        $templateData['localized']['validation']['name'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            ['fieldName' => $templateData['localized']['field']['name']],
        );
        $templateData['localized']['validation']['position'] = Formatter::interpolate(
            $validationLocalization['isIntegerGreaterThan'],
            [
                'fieldName' => $templateData['localized']['field']['position'],
                'limit' => 0,
            ],
        );

        // Only edit:
        if ($templateData['isEditAction']) {
            $templateData['localized']['breadcrumb']['edit'] = Formatter::interpolate(
                $templateData['localized']['breadcrumb']['edit'],
                [
                    'name' => $serviceGroup->name,
                ],
            );
        }

        // Only create:
        if ($templateData['isCreateAction']) {
            $templateData['localized']['validation']['key'] = Formatter::interpolate(
                $validationLocalization['isMinLengthCertainCharacters'],
                [
                    'fieldName' => $templateData['localized']['field']['key'],
                    'minLength' => 3,
                    'characterPool' => 'a-z-_',
                ],
            );
        }

        return $this->template->getEngine()->render(
            'service-group/edit-create-service-group.html.twig',
            $templateData,
        );
    }

    /**
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewOverview(): string
    {
        $serviceGroups = $this->serviceGroupRepository->getAllOfSelectedLanguage();

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = ServiceGroupOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['serviceGroups'] = $serviceGroups;

        return $this->template->getEngine()->render(
            'service-group/overview-service-group.html.twig',
            $templateData,
        );
    }
}
