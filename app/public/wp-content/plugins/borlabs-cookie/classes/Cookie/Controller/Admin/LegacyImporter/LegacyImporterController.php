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

namespace Borlabs\Cookie\Controller\Admin\LegacyImporter;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\LegacyImporter\LegacyImporterLocalizationStrings;
use Borlabs\Cookie\System\Config\BackwardsCompatibilityConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Package\Traits\InstallationStatusAnalyzerTrait;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\BorlabsCookieLegacyImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ContentBlockerImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ScriptBlockerImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ServiceGroupImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ServiceImporter;

final class LegacyImporterController implements ControllerInterface, ExtendedRouteValidationInterface
{
    use InstallationStatusAnalyzerTrait;

    public const CONTROLLER_ID = 'borlabs-cookie-legacy-importer';

    private BackwardsCompatibilityConfig $backwardsCompatibilityConfig;

    private BorlabsCookieLegacyImporter $borlabsCookieLegacyImporter;

    private ContentBlockerImporter $contentBlockerImporter;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private LegacyImporterLocalizationStrings $legacyImporterLocalizationStrings;

    private MessageManager $messageManager;

    private ScriptBlockerImporter $scriptBlockerImporter;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ServiceGroupImporter $serviceGroupImporter;

    private ServiceImporter $serviceImporter;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        BackwardsCompatibilityConfig $backwardsCompatibilityConfig,
        BorlabsCookieLegacyImporter $borlabsCookieLegacyImporter,
        ContentBlockerImporter $contentBlockerImporter,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        LegacyImporterLocalizationStrings $legacyImporterLocalizationStrings,
        MessageManager $messageManager,
        ScriptBlockerImporter $scriptBlockerImporter,
        ScriptConfigBuilder $scriptConfigBuilder,
        ServiceGroupImporter $serviceGroupImporter,
        ServiceImporter $serviceImporter,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->backwardsCompatibilityConfig = $backwardsCompatibilityConfig;
        $this->borlabsCookieLegacyImporter = $borlabsCookieLegacyImporter;
        $this->contentBlockerImporter = $contentBlockerImporter;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->legacyImporterLocalizationStrings = $legacyImporterLocalizationStrings;
        $this->messageManager = $messageManager;
        $this->scriptBlockerImporter = $scriptBlockerImporter;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->serviceGroupImporter = $serviceGroupImporter;
        $this->serviceImporter = $serviceImporter;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function import(): void
    {
        $importReport = $this->borlabsCookieLegacyImporter->import();

        if ($importReport !== null && $this->isInstallationCompletelySuccessful($importReport->customContentBlockersImported)
            && $this->isInstallationCompletelySuccessful($importReport->customScriptBlockersImported)
            && $this->isInstallationCompletelySuccessful($importReport->customServicesImported)
            && $this->isInstallationCompletelySuccessful($importReport->presetContentBlockersImported)
            && $this->isInstallationCompletelySuccessful($importReport->presetServicesImported)
            && $importReport->presetServiceGroupsImported
            && ($importReport->customServiceGroupsImported ?? true)) {
            $this->messageManager->success($this->legacyImporterLocalizationStrings::get()['alert']['importedSuccessfully']);
        } else {
            $this->messageManager->error($this->legacyImporterLocalizationStrings::get()['alert']['importedUnsuccessfully']);
        }
    }

    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        try {
            if ($action === 'import') {
                $this->import();
            }

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

    /**
     * Updates the configuration.
     *
     * @see \Borlabs\Cookie\System\Config\AbstractConfigManager
     *
     * @param array<string> $postData
     */
    public function save(array $postData): bool
    {
        $backwardsCompatibilityConfig = $this->backwardsCompatibilityConfig->get();
        $backwardsCompatibilityConfig->loadBackwardsCompatibilityJavaScript = (bool) $postData['loadBackwardsCompatibilityJavaScript'];
        $this->backwardsCompatibilityConfig->save($backwardsCompatibilityConfig);
        $languages = array_column($this->language->getLanguageList()->list, 'key');

        foreach ($languages as $languageCode) {
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $languageCode,
            );
        }

        $this->thirdPartyCacheClearerManager->clearCache();
        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return true;
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        if (isset($request->postData['action'])
            && in_array($request->postData['action'], ['import',], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->postData['action'], $nonce)
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = $this->legacyImporterLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['backwardsCompatibilityConfig'] = $this->backwardsCompatibilityConfig->get();
        $templateData['data']['isImportCompleted'] = $this->borlabsCookieLegacyImporter->isImportCompleted();
        $templateData['data']['isImportDataAvailable'] = $this->borlabsCookieLegacyImporter->isImportDataAvailable();
        $templateData['data']['preImportMetadata']['contentBlockers'] = $this->contentBlockerImporter->getPreImportMetadataList();
        $templateData['data']['preImportMetadata']['contentBlockers']->sortListByPropertiesNaturally(['language', 'name',]);
        $templateData['data']['preImportMetadata']['scriptBlockers'] = $this->scriptBlockerImporter->getPreImportMetadataList();
        $templateData['data']['preImportMetadata']['scriptBlockers']->sortListByPropertyNaturally('name');
        $templateData['data']['preImportMetadata']['serviceGroups'] = $this->serviceGroupImporter->getPreImportMetadataList();
        $templateData['data']['preImportMetadata']['serviceGroups']->sortListByPropertiesNaturally(['language', 'name']);
        $templateData['data']['preImportMetadata']['services'] = $this->serviceImporter->getPreImportMetadataList();
        $templateData['data']['preImportMetadata']['services']->sortListByPropertiesNaturally(['language', 'name',]);

        return $this->template->getEngine()->render(
            'legacy-importer/legacy-importer.html.twig',
            $templateData,
        );
    }
}
