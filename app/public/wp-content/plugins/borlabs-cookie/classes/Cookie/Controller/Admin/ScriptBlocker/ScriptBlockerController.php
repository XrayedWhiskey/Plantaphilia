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

namespace Borlabs\Cookie\Controller\Admin\ScriptBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\Adapter\WpGetPagesArgumentDto;
use Borlabs\Cookie\Dto\LocalScanner\ScanRequestOptionDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\LocalScanner\HandleTypeEnum;
use Borlabs\Cookie\Enum\LocalScanner\ScanModeEnum;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\LocalScanner\LocalScannerLocalizationStrings;
use Borlabs\Cookie\Localization\LocalScanner\ScanResultLocalizationStrings;
use Borlabs\Cookie\Localization\ScriptBlocker\ScriptBlockerCreateEditLocalizationStrings;
use Borlabs\Cookie\Localization\ScriptBlocker\ScriptBlockerOverviewLocalizationStrings;
use Borlabs\Cookie\Model\ScriptBlocker\ScriptBlockerModel;
use Borlabs\Cookie\Repository\ScriptBlocker\ScriptBlockerRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\BorlabsCookieGlobalsService;
use Borlabs\Cookie\System\ScriptBlocker\ScriptBlockerService;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

class ScriptBlockerController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-script-blocker';

    private BorlabsCookieGlobalsService $borlabsCookieGlobalsService;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private MessageManager $messageManager;

    private ScriptBlockerCreateEditLocalizationStrings $scriptBlockerCreateEditLocalizationStrings;

    private ScriptBlockerRepository $scriptBlockerRepository;

    private ScriptBlockerService $scriptBlockerService;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        BorlabsCookieGlobalsService $borlabsCookieGlobalsService,
        GlobalLocalizationStrings $globalLocalizationStrings,
        MessageManager $messageManager,
        ScriptBlockerCreateEditLocalizationStrings $scriptBlockerCreateEditLocalizationStrings,
        ScriptBlockerRepository $scriptBlockerRepository,
        ScriptBlockerService $scriptBlockerService,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->borlabsCookieGlobalsService = $borlabsCookieGlobalsService;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->messageManager = $messageManager;
        $this->scriptBlockerCreateEditLocalizationStrings = $scriptBlockerCreateEditLocalizationStrings;
        $this->scriptBlockerRepository = $scriptBlockerRepository;
        $this->scriptBlockerService = $scriptBlockerService;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function delete(int $id): string
    {
        $scriptBlocker = $this->scriptBlockerRepository->findByIdOrFail($id);
        $this->scriptBlockerRepository->delete($scriptBlocker);
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['deletedSuccessfully']);

        return $this->viewOverview();
    }

    public function route(RequestDto $request): ?string
    {
        $id = (int) ($request->postData['id'] ?? $request->getData['id'] ?? -1);
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        // Edit Script Blocker
        if ($action === 'edit') {
            return $this->viewEdit($id, $request->postData, $request->getData);
        }

        // Delete Script Blocker
        if ($action === 'delete') {
            return $this->delete($id);
        }

        // Switch status of Script Blocker
        if ($action === 'switch-status') {
            return $this->switchStatus($id);
        }

        // Create new or update existing Script Blocker
        if ($action === 'save') {
            return $this->save($id, $request->postData);
        }

        return $this->viewOverview();
    }

    public function save(int $id, array $postData): string
    {
        $scriptBlockerId = $this->scriptBlockerService->save($id, $postData);

        if ($scriptBlockerId !== null) {
            $this->thirdPartyCacheClearerManager->clearCache();

            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);
        }

        return $this->viewEdit($scriptBlockerId ?? $id, $postData, []);
    }

    public function switchStatus(int $id): string
    {
        $this->scriptBlockerRepository->switchStatus($id);
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['changedStatusSuccessfully']);

        return $this->viewOverview();
    }

    public function viewEdit(int $id, array $postData, array $getData): string
    {
        if ($id !== -1) {
            $scriptBlocker = $this->scriptBlockerRepository->findByIdOrFail($id);
        } else {
            $scriptBlocker = new ScriptBlockerModel();
        }

        $this->borlabsCookieGlobalsService->addProperty('handleTypes', array_column(HandleTypeEnum::getAll(), 'description', 'value'));
        $scanRequestOption = new ScanRequestOptionDto();
        $scanRequestOption->noScriptBlockers = true;
        $scanRequestOption->scriptScanRequest = true;
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
        $templateData['data'] = (array) $scriptBlocker;
        $templateData['data'] = array_merge($templateData['data'], $postData, ['id' => $scriptBlocker->id]);
        $templateData['data']['contentBlockerGlobalJavaScript'] = $scriptBlocker->id !== -1 ? $this->scriptBlockerService->getGlobalJavaScriptForContentBlocker($scriptBlocker) : '';
        $templateData['data']['serviceOptInScriptTag'] = $scriptBlocker->id !== -1 ? $this->scriptBlockerService->getOptInScriptTagForService($scriptBlocker) : '';

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
        $templateData['localized'] = $this->scriptBlockerCreateEditLocalizationStrings::get();
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
                    'name' => $scriptBlocker->name,
                ],
            );
        }

        return $this->template->getEngine()->render(
            'script-blocker/create-edit-script-blocker.html.twig',
            $templateData,
        );
    }

    public function viewOverview(): string
    {
        $scriptBlockers = $this->scriptBlockerRepository->getAll();

        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = ScriptBlockerOverviewLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['scriptBlockers'] = $scriptBlockers;

        return $this->template->getEngine()->render(
            'script-blocker/overview-script-blocker.html.twig',
            $templateData,
        );
    }
}
