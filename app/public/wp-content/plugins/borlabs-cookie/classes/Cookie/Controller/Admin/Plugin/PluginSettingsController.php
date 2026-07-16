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

namespace Borlabs\Cookie\Controller\Admin\Plugin;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Controller\Admin\ExtendedRouteValidationInterface;
use Borlabs\Cookie\Dto\Adapter\WpGetPostTypeArgumentDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\System\AutomaticUpdateEnum;
use Borlabs\Cookie\Enum\System\DisplayModeSettingsEnum;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Plugin\PluginSettingsLocalizationStrings;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\Updater\Updater;
use Exception;

final class PluginSettingsController implements ControllerInterface, ExtendedRouteValidationInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-plugin-settings';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private MessageManager $messageManager;

    private PluginConfig $pluginConfig;

    private Template $template;

    private Updater $updater;

    private WpFunction $wpFunction;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        MessageManager $messageManager,
        PluginConfig $pluginConfig,
        Template $template,
        Updater $updater,
        WpFunction $wpFunction
    ) {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->messageManager = $messageManager;
        $this->pluginConfig = $pluginConfig;
        $this->template = $template;
        $this->updater = $updater;
        $this->wpFunction = $wpFunction;
    }

    public function reset(): bool
    {
        $defaultConfig = $this->pluginConfig->defaultConfig();
        $this->pluginConfig->save($defaultConfig);
        $this->updater->handleAutomaticUpdateStatus();
        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return true;
    }

    /**
     * Is loaded by {@see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()} and gets information
     * what about to do.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     * @throws Exception
     */
    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'reset') {
            $this->reset();
        }

        if ($action === 'save') {
            $this->save($request->postData);
        }

        if ($action === 'saved') {
            $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);
        }

        return $this->viewOverview();
    }

    /**
     * Updates the plugin configuration.
     *
     * @param array<string,mixed> $postData
     */
    public function save(array $postData)
    {
        $config = $this->pluginConfig->get();

        if (isset($postData['automaticUpdate']) && AutomaticUpdateEnum::hasValue($postData['automaticUpdate'])) {
            $config->automaticUpdate = AutomaticUpdateEnum::fromValue($postData['automaticUpdate']);
        }

        $config->clearThirdPartyCache = (bool) $postData['clearThirdPartyCache'];

        if (isset($postData['displayModeSettings']) && DisplayModeSettingsEnum::hasValue($postData['displayModeSettings'])) {
            $config->displayModeSettings = DisplayModeSettingsEnum::fromValue($postData['displayModeSettings']);
        }

        $config->enableDebugConsole = (bool) $postData['enableDebugConsole'];
        $config->enableDebugLogging = (bool) $postData['enableDebugLogging'];
        $config->metaBox = $postData['metaBox'] ?? [];

        if (isset($postData['wordPressAdminSidebarMenuMode']) && WordPressAdminSidebarMenuModeEnum::hasValue($postData['wordPressAdminSidebarMenuMode'])) {
            $config->wordPressAdminSidebarMenuMode = WordPressAdminSidebarMenuModeEnum::fromValue($postData['wordPressAdminSidebarMenuMode']);
        }

        $this->pluginConfig->save($config);
        $this->updater->handleAutomaticUpdateStatus();

        $this->wpFunction->wpSafeRedirect(
            $this->wpFunction->getAdminUrl('admin.php?page=' . self::CONTROLLER_ID . '&action=saved&_wpnonce=' . $this->wpFunction->wpCreateNonce(self::CONTROLLER_ID . '-saved')),
        );

        exit;
    }

    public function validate(RequestDto $request, string $nonce, bool $isValid): bool
    {
        if (isset($request->getData['action'])
            && in_array($request->getData['action'], ['saved'], true)
            && $this->wpFunction->wpVerifyNonce(self::CONTROLLER_ID . '-' . $request->getData['action'], $nonce)
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    /**
     * Returns the overview.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = PluginSettingsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = (array) $this->pluginConfig->get();
        $templateData['options']['postTypes'] = $this->getPostTypes();
        $templateData['enum']['automaticUpdate'] = AutomaticUpdateEnum::getLocalizedKeyValueList();
        $templateData['enum']['automaticUpdate']->sortListByPropertiesNaturally(['value']);
        $templateData['enum']['displayModeSettings'] = DisplayModeSettingsEnum::getLocalizedKeyValueList();
        $templateData['enum']['displayModeSettings']->sortListByPropertiesNaturally(['value']);
        $templateData['enum']['wordPressAdminSidebarMenuMode'] = WordPressAdminSidebarMenuModeEnum::getLocalizedKeyValueList();
        $templateData['enum']['wordPressAdminSidebarMenuMode']->sortListByPropertiesNaturally(['value']);

        return $this->template->getEngine()->render(
            'plugin/plugin.html.twig',
            $templateData,
        );
    }

    /**
     * @return KeyValueDtoList list of post types in alphabetical order
     */
    private function getPostTypes(): KeyValueDtoList
    {
        $getPostTypeArgumentDto = new WpGetPostTypeArgumentDto();
        $getPostTypeArgumentDto->public = true;
        $postTypes = $this->wpFunction->getPostTypes($getPostTypeArgumentDto, 'objects');
        $orderedPostTypes = [];

        foreach ($postTypes as $postType) {
            $orderedPostTypes[$postType->name] = $postType->label;
        }

        asort($orderedPostTypes, SORT_NATURAL | SORT_FLAG_CASE);
        $list = new KeyValueDtoList();

        foreach ($orderedPostTypes as $postType => $label) {
            if (!in_array((string) $postType, ['attachment'], true)) {
                $list->add(new KeyValueDto((string) $postType, $label));
            }
        }

        unset($postTypes, $orderedPostTypes);

        return $list;
    }
}
