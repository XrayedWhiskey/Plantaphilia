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

namespace Borlabs\Cookie\System\WordPressAdminDriver;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Localization\WordPressAdminInitLocalizationStrings;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\CompatibilityPatch\CompatibilityPatchManager;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerManager;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\License\LicenseStatusMessage;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\MetaBox\MetaBoxService;
use Borlabs\Cookie\System\SafeMode\SafeMode;
use Borlabs\Cookie\System\Shortcode\ShortcodeHandler;
use Borlabs\Cookie\System\SystemCheck\SystemCheck;

final class WordPressAdminInit
{
    private CompatibilityPatchManager $compatibilityPatchManager;

    private Container $container;

    private ContentBlockerManager $contentBlockerManager;

    private GeneralConfig $generalConfig;

    private Language $language;

    private License $license;

    private LicenseStatusMessage $licenseStatusMessage;

    private MessageManager $messageManager;

    private MetaBoxService $metaBoxService;

    private SafeMode $safeMode;

    private ShortcodeHandler $shortcodeHandler;

    private SystemCheck $systemCheck;

    private WordPressAdminInitLocalizationStrings $wordPressAdminInitLocalizationStrings;

    private WordPressAdminResources $wordPressAdminResources;

    private WordPressPageManager $wordPressPageManager;

    private WordPressSidebarMenuModeManager $wordPressSidebarMenuModeManager;

    private WpFunction $wpFunction;

    public function __construct(
        CompatibilityPatchManager $compatibilityPatchManager,
        Container $container,
        ContentBlockerManager $contentBlockerManager,
        GeneralConfig $generalConfig,
        Language $language,
        License $license,
        LicenseStatusMessage $licenseStatusMessage,
        MessageManager $messageManager,
        MetaBoxService $metaBoxService,
        SafeMode $safeMode,
        ShortcodeHandler $shortcodeHandler,
        SystemCheck $systemCheck,
        WordPressAdminInitLocalizationStrings $wordPressAdminInitLocalizationStrings,
        WordPressAdminResources $wordPressAdminResources,
        WordPressPageManager $wordPressPageManager,
        WordPressSidebarMenuModeManager $wordPressSidebarMenuModeManager,
        WpFunction $wpFunction
    ) {
        $this->compatibilityPatchManager = $compatibilityPatchManager;
        $this->container = $container;
        $this->contentBlockerManager = $contentBlockerManager;
        $this->generalConfig = $generalConfig;
        $this->language = $language;
        $this->license = $license;
        $this->licenseStatusMessage = $licenseStatusMessage;
        $this->messageManager = $messageManager;
        $this->metaBoxService = $metaBoxService;
        $this->safeMode = $safeMode;
        $this->shortcodeHandler = $shortcodeHandler;
        $this->systemCheck = $systemCheck;
        $this->wordPressAdminInitLocalizationStrings = $wordPressAdminInitLocalizationStrings;
        $this->wordPressAdminResources = $wordPressAdminResources;
        $this->wordPressPageManager = $wordPressPageManager;
        $this->wordPressSidebarMenuModeManager = $wordPressSidebarMenuModeManager;
        $this->wpFunction = $wpFunction;
    }

    /**
     * addActionLinks function.
     *
     * @param mixed $links
     */
    public function addActionLinks($links)
    {
        if (is_array($links)) {
            array_unshift(
                $links,
                '<a href="' . $this->wpFunction->escUrl($this->wpFunction->getAdminUrl('admin.php?page=borlabs-cookie')) . '">'
                . $this->wordPressAdminInitLocalizationStrings::get()['pluginLinks']['dashboard']
                . '</a>',
                '<a href="' . $this->wpFunction->escUrl($this->wpFunction->getAdminUrl('admin.php?page=borlabs-cookie-setup-assistant')) . '">'
                . $this->wordPressAdminInitLocalizationStrings::get()['pluginLinks']['setupAssistant']
                . '</a>',
                '<a href="' . $this->wpFunction->escUrl($this->wpFunction->getAdminUrl('admin.php?page=borlabs-cookie-license')) . '">'
                . $this->wordPressAdminInitLocalizationStrings::get()['pluginLinks']['license']
                . '</a>',
            );
        }

        return $links;
    }

    /**
     * extendPluginUpdateMessage function.
     *
     * @param mixed $pluginData
     * @param mixed $response
     */
    public function extendPluginUpdateMessage($pluginData, $response)
    {
        // Check license
        $licenseData = $this->license->get();

        if (empty($licenseData)) {
            echo '<br>';
            echo $this->licenseStatusMessage->getMessageEnterLicenseKey();
        } elseif (!empty($licenseData->validUntil) && strtotime($licenseData->validUntil) < strtotime(date('Y-m-d'))) {
            echo '<br>';
            echo $this->licenseStatusMessage->getLicenseMessageKeyExpired();
        }
    }

    /**
     * handleSystemCheck function.
     */
    public function handleSystemCheck()
    {
        $currentScreenData = $this->wpFunction->getCurrentScreen();

        if (is_string($currentScreenData->id) && strpos($currentScreenData->id, 'borlabs-cookie') !== false) {
            // Check if license is expired
            if ($currentScreenData->id !== 'borlabs-cookie_page_borlabs-cookie-license'
                && $currentScreenData->id !== 'borlabs-cookie_page_borlabs-cookie-legacy-importer'
                && $currentScreenData->id !== 'borlabs-cookie_page_borlabs-cookie-setup-assistant') {
                $this->licenseStatusMessage->handleMessageActivateLicenseKey();
                $this->licenseStatusMessage->handleMessageLicenseExpired();
                $this->licenseStatusMessage->handleMessageLicenseNotValidForCurrentBuild();
            }

            $systemCheckReport = $this->systemCheck->report();

            if (!empty($systemCheckReport)) {
                foreach ($systemCheckReport as $reportType) {
                    foreach ($reportType as $auditOrAuditEntries) {
                        if ($auditOrAuditEntries instanceof AuditDto && $auditOrAuditEntries->success === false) {
                            $this->messageManager->error($auditOrAuditEntries->message);
                        } elseif (is_array($auditOrAuditEntries)) {
                            foreach ($auditOrAuditEntries as $audit) {
                                if ($audit instanceof AuditDto && $audit->success === false) {
                                    $this->messageManager->error($audit->message);
                                }
                            }
                        }
                    }
                }
            }

            // Check if Borlabs Cookie is active but only if plugin is unlocked
            if ($this->license->isPluginUnlocked()) {
                if (
                    $this->generalConfig->get()->borlabsCookieStatus === false
                    && empty($_POST['borlabsCookieStatus'])
                    && $currentScreenData->id !== 'borlabs-cookie_page_borlabs-cookie-setup-assistant'
                ) {
                    $this->messageManager->warning($this->wordPressAdminInitLocalizationStrings::get()['alert']['borlabsCookieNotActive']);
                }
            }
        }
    }

    public function register()
    {
        // Set current request
        $request = new RequestDto(
            Sanitizer::requestData($_POST),
            Sanitizer::requestData($_GET),
            Sanitizer::requestData($_SERVER),
        );
        $this->container->add('currentRequest', $request);

        // Detect language and load text domain.
        $this->language->setInitializationSignal();
        $this->language->init();
        $this->language->loadTextDomain();
        $this->language->handleLanguageSwitchRequest();

        // Load compatibility patches
        $this->safeMode->handle($request);
        $this->compatibilityPatchManager->loadPatches();
        $this->compatibilityPatchManager->initPatches();

        // Add menu
        $this->wpFunction->addAction('admin_menu', [$this->wordPressPageManager, 'register']);

        // Load JavaScript & CSS
        $this->wpFunction->addAction('admin_enqueue_scripts', [$this->wordPressAdminResources, 'register']);
        $this->wpFunction->addAction('admin_enqueue_scripts', [$this->wordPressSidebarMenuModeManager, 'register']);

        // System Check
        $this->wpFunction->addAction('current_screen', [$this, 'handleSystemCheck']);

        // Extend update plugin message
        $this->wpFunction->addAction('in_plugin_update_message-' . BORLABS_COOKIE_BASENAME, [$this, 'extendPluginUpdateMessage'], 10, 2);

        // Add action links to plugin page
        $this->wpFunction->addFilter('plugin_action_links_' . BORLABS_COOKIE_BASENAME, [$this, 'addActionLinks']);
        $this->wpFunction->addFilter('script_loader_tag', [$this->wordPressAdminResources, 'transformScriptTagsToModules'], 100, 2);

        // Meta Box
        $this->wpFunction->addAction('wp_loaded', [$this->metaBoxService, 'register']);

        // Register shortcodes
        if ($this->wpFunction->wpDoingAjax() === true) {
            $this->contentBlockerManager->init();
            $this->wpFunction->addShortcode('borlabs-cookie', [$this->shortcodeHandler, 'handle']);
        }
    }
}
