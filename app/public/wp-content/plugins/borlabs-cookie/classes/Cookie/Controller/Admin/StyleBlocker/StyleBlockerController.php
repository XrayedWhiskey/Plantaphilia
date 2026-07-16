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

namespace Borlabs\Cookie\Controller\Admin\StyleBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\Adapter\WpGetPagesArgumentDto;
use Borlabs\Cookie\Dto\LocalScanner\ScanRequestOptionDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\LocalScanner\HandleTypeEnum;
use Borlabs\Cookie\Enum\LocalScanner\ScanModeEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\LocalScanner\LocalScannerLocalizationStrings;
use Borlabs\Cookie\Localization\LocalScanner\ScanResultLocalizationStrings;
use Borlabs\Cookie\Localization\StyleBlocker\StyleBlockerCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\StyleBlocker\StyleBlockerOverviewLocalizationStrings;
use Borlabs\Cookie\Model\StyleBlocker\StyleBlockerModel;
use Borlabs\Cookie\Repository\StyleBlocker\StyleBlockerRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\BorlabsCookieGlobalsService;
use Borlabs\Cookie\System\StyleBlocker\StyleBlockerService;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

class StyleBlockerController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-style-blocker';

    private BorlabsCookieGlobalsService $borlabsCookieGlobalsService;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private MessageManager $messageManager;

    private StyleBlockerCreateEditLocalizationStrings $styleBlockerCreateEditLocalizationStrings;

    private StyleBlockerRepository  $styleBlockerRepository;

    private StyleBlockerService $styleBlockerService;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        BorlabsCookieGlobalsService $borlabsCookieGlobalsService,
        GlobalLocalizationStrings $globalLocalizationStrings,
        MessageManager $messageManager,
        StyleBlockerCreateEditLocalizationStrings $styleBlockerCreateEditLocalizationStrings,
        StyleBlockerRepository $styleBlockerRepository,
        StyleBlockerService $styleBlockerService,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->borlabsCookieGlobalsService = $borlabsCookieGlobalsService;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->messageManager = $messageManager;
        $this->styleBlockerCreateEditLocalizationStrings = $styleBlockerCreateEditLocalizationStrings;
        $this->styleBlockerRepository = $styleBlockerRepository;
        $this->styleBlockerService = $styleBlockerService;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function delete(int $id): string
    {
        $styleBlocker = $this->styleBlockerRepository->findById($id);

        if ($styleBlocker === null) {
            return $this->viewOverview();
        }

        if ($styleBlocker->undeletable) {
            $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['deleteNotAllowed']);

            return $this->viewOverview();
        }

        try {
            $this->styleBlockerRepository->delete($styleBlocker);
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

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        // Edit Style Blocker
        if ($action === 'edit') {
            return $this->viewEdit($id, $request->postData, $request->getData);
        }

        // Delete Style Blocker
        if ($action === 'delete') {
            return $this->delete($id);
        }

        // Switch status of Style Blocker
        if ($action === 'switch-status') {
            return $this->switchStatus($id);
        }

        // Create new or update existing Style Blocker
        if ($action === 'save') {
            return $this->save($id, $request->postData);
        }

        return $this->viewOverview();
    }

    public function save(int $id, array $postData): string
    {
        $styleBlockerId = $this->styleBlockerService->save($id, $postData);

        if ($styleBlockerId !== null) {
            $this->thirdPartyCacheClearerManager->clearCache();

            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);
        }

        return $this->viewEdit($styleBlockerId ?? $id, $postData, []);
    }

    public function switchStatus(int $id): string
    {
        $this->styleBlockerRepository->switchStatus($id);
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['changedStatusSuccessfully']);

        return $this->viewOverview();
    }

    public function viewEdit(int $id, array $postData, array $getData): string
    {
        if ($id !== -1) {
            $styleBlocker = $this->styleBlockerRepository->findByIdOrFail($id);
        } else {
            $styleBlocker = new StyleBlockerModel();
        }

        $this->borlabsCookieGlobalsService->addProperty('handleTypes', array_column(HandleTypeEnum::getAll(), 'description', 'value'));
        $scanRequestOption = new ScanRequestOptionDto();
        $scanRequestOption->noStyleBlockers = true;
        $scanRequestOption->styleScanRequest = true;
        $this->borlabsCookieGlobalsService->addProperty('scanRequestOption', (array) $scanRequestOption);
        $this->borlabsCookieGlobalsService->addProperty('scanResultTables', [
            'matchedHandles' => 'matchedHandles',
            'matchedTags' => 'matchedTags',
            'unmatchedHandles' => 'unmatchedHandles',
            'unmatchedTags' => 'unmatchedTags',
        ]);
        $this->borlabsCookieGlobalsService->addProperty('scanResultValidationMessages', ScanResultLocalizationStrings::get()['validation']);

        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['data'] = (array) $styleBlocker;
        $templateData['data'] = array_merge($templateData['data'], $postData, ['id' => $styleBlocker->id]);
        $templateData['data']['contentBlockerGlobalJavaScript'] = $styleBlocker->id !== -1 ? $this->styleBlockerService->getGlobalJavaScriptForContentBlocker($styleBlocker) : '';
        $templateData['data']['serviceOptInScriptTag'] = $styleBlocker->id !== -1 ? $this->styleBlockerService->getOptInScriptTagForService($styleBlocker) : '';

        if (!isset($templateData['data']['localScanner']['scanMode'])) {
            $templateData['data']['localScanner']['scanMode'] = ScanModeEnum::GUEST();
        }

        // Get all pages
        $pages = $this->wpFunction->getPages(new WpGetPagesArgumentDto());
        $pagesOptions = new KeyValueDtoList(array_map(function ($page) {
            return new KeyValueDto((string) $this->wpFunction->getPermalink($page->ID), $page->post_title);
        }, $pages));
        $pagesOptions->add(
            new KeyValueDto(
                '0',
                $this->globalLocalizationStrings::get()['option']['defaultSelectOption'],
            ),
            true,
        );
        $templateData['options']['localScanner']['pages'] = $pagesOptions;
        $templateData['enum']['localScanner']['scanModes'] = ScanModeEnum::getLocalizedKeyValueList();
        $templateData['isCreateAction'] = $id === -1;
        $templateData['isEditAction'] = $id !== -1;
        $templateData['localized'] = $this->styleBlockerCreateEditLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['localized']['localScanner'] = LocalScannerLocalizationStrings::get();
        $templateData['localized']['localScanner']['global'] = $this->globalLocalizationStrings::get();
        $templateData['localized']['scanResult'] = ScanResultLocalizationStrings::get();
        $templateData['localized']['scanResult']['global'] = $this->globalLocalizationStrings::get();

        // Only edit:
        if ($templateData['isEditAction']) {
            $templateData['localized']['breadcrumb']['edit'] = Formatter::interpolate(
                $templateData['localized']['breadcrumb']['edit'],
                [
                    'name' => $styleBlocker->name,
                ],
            );
        }

        return $this->template->getEngine()->render(
            'style-blocker/create-edit-style-blocker.html.twig',
            $templateData,
        );
    }

    public function viewOverview(): string
    {
        $styleBlockers = $this->styleBlockerRepository->getAll();

        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = StyleBlockerOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['styleBlockers'] = $styleBlockers;

        return $this->template->getEngine()->render(
            'style-blocker/overview-style-blocker.html.twig',
            $templateData,
        );
    }
}
