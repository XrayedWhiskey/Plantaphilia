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

namespace Borlabs\Cookie\Controller\Admin\ImportExport;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\ImportExport\ImportExportLocalizationStrings;
use Borlabs\Cookie\System\ImportExport\ExportService;
use Borlabs\Cookie\System\ImportExport\ImportService;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;
use Borlabs\Cookie\Validator\Import\ImportValidator;

/**
 * Jerry: Well, what does *he* do?
 * George: He's an importer.
 * Jerry: Just imports, no exports?
 * George: He's an importer/exporter, okay?
 *
 * ...
 *
 * Vanessa: That's right! What're you doing here?
 * Jerry: Oh, were meeting a friend of ours for lunch. He works here in the building.
 * George: Yeah, Art *Vandelay*.
 * Vanessa: Really? Which company?
 * Jerry: I don't know. He's an importer.
 * Vanessa: Importer?
 * George: ...And exporter.
 * Jerry: He's an importer/exporter.
 * George: I'm, uh, I'm an architect.
 * Vanessa: Really. What do you design?
 * George: Uh, railroads, uh...
 * Vanessa: I thought engineers do that.
 * George: They can...
 */
final class ImportExportController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-import-export';

    private ExportService $exportService;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private ImportService $importService;

    private ImportValidator $importValidator;

    private Language $language;

    private MessageManager $messageManager;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private StyleBuilder $styleBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        ExportService $exportService,
        GlobalLocalizationStrings $globalLocalizationStrings,
        ImportService $importService,
        ImportValidator $importValidator,
        Language $language,
        MessageManager $messageManager,
        ScriptConfigBuilder $scriptConfigBuilder,
        StyleBuilder $styleBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->exportService = $exportService;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->importService = $importService;
        $this->importValidator = $importValidator;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->styleBuilder = $styleBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        try {
            if ($action === 'save') {
                $this->save($request->postData);
            }
        } catch (TranslatedException $exception) {
            $this->messageManager->error($exception->getTranslatedMessage());
        } catch (GenericException $exception) {
            $this->messageManager->error($exception->getMessage());
        }

        return $this->viewOverview();
    }

    public function save(array $postData): bool
    {
        if (!$this->importValidator->isValid($postData)) {
            return false;
        }

        $importData = json_decode($postData['importData'], true);

        foreach ($importData as $key => $base64EncodedConfig) {
            if ($key === 'contentBlockerStyleConfigData') {
                $this->importService->importContentBlockerStyleConfig(
                    $base64EncodedConfig,
                    $this->language->getSelectedLanguageCode(),
                );
            } elseif ($key === 'dialogLocalizationData') {
                $this->importService->importDialogLocalization(
                    $base64EncodedConfig,
                    $this->language->getSelectedLanguageCode(),
                );
            } elseif ($key === 'dialogSettingsConfigData') {
                $this->importService->importDialogSettingsConfig(
                    $base64EncodedConfig,
                    $this->language->getSelectedLanguageCode(),
                );
            } elseif ($key === 'dialogStyleConfigData') {
                $this->importService->importDialogStyleConfig(
                    $base64EncodedConfig,
                    $this->language->getSelectedLanguageCode(),
                );
            } elseif ($key === 'widgetConfigData') {
                $this->importService->importWidgetConfig(
                    $base64EncodedConfig,
                    $this->language->getSelectedLanguageCode(),
                );
            }
        }

        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success(ImportExportLocalizationStrings::get()['alert']['importedSuccessfully']);

        return true;
    }

    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = ImportExportLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['contentBlockerStyleConfig'] = $this->exportService->getContentBlockerStyleConfigExportData();
        $templateData['data']['dialogLocalization'] = $this->exportService->getDialogLocalizationExportData();
        $templateData['data']['dialogSettingsConfig'] = $this->exportService->getDialogSettingsConfigExportData();
        $templateData['data']['dialogStyleConfig'] = $this->exportService->getDialogStyleConfigExportData();
        $templateData['data']['widgetConfig'] = $this->exportService->getWidgetConfigExportData();

        return $this->template->getEngine()->render(
            'import-export/import-export.html.twig',
            $templateData,
        );
    }
}
