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

namespace Borlabs\Cookie\RestEndpoint;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\SetupAssistant\SetupConfigurationDto;
use Borlabs\Cookie\DtoList\Config\LanguageOptionDtoList;
use Borlabs\Cookie\Enum\SetupAssistant\SetupTypeEnum;
use Borlabs\Cookie\Enum\System\DisplayModeSettingsEnum;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;
use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\SetupAssistant\SetupAssistantService;
use DateTime;
use WP_REST_Request;

final class SetupAssistantFinalizeEndpoint
{
    private DialogSettingsConfig $dialogSettingsConfig;

    private DialogStyleConfig $dialogStyleConfig;

    private PluginConfig $pluginConfig;

    private SetupAssistantService $setupAssistantService;

    private WpFunction $wpFunction;

    public function __construct(
        DialogSettingsConfig $dialogSettingsConfig,
        DialogStyleConfig $dialogStyleConfig,
        PluginConfig $pluginConfig,
        SetupAssistantService $setupAssistantService,
        WpFunction $wpFunction
    ) {
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->pluginConfig = $pluginConfig;
        $this->setupAssistantService = $setupAssistantService;
        $this->wpFunction = $wpFunction;
    }

    public function finalize(WP_REST_Request $request)
    {
        $defaultDialogSettingsConfig = $this->dialogSettingsConfig->defaultConfig();
        $defaultDialogStyleConfig = $this->dialogStyleConfig->defaultConfig();
        $defaultPluginConfig = $this->pluginConfig->defaultConfig();
        $nestedParams = Transformer::buildNestedArray($request->get_params());
        $setupConfiguration = new SetupConfigurationDto();
        $setupConfiguration->cloudScanId = (int) ($nestedParams['cloudScanId'] ?? -1);
        $setupConfiguration->dialogStyle = $defaultDialogStyleConfig;
        $setupConfiguration->displayModeSettings = isset($nestedParams['displayModeSettings']) && DisplayModeSettingsEnum::hasValue($nestedParams['displayModeSettings'])
            ? DisplayModeSettingsEnum::fromValue($nestedParams['displayModeSettings'])
            : $defaultPluginConfig->displayModeSettings;
        $setupConfiguration->imprintPages = Transformer::toKeyValueDtoList($nestedParams['imprintPages'] ?? [], 'languageCode', 'value');
        $setupConfiguration->languageOptions = LanguageOptionDtoList::fromJson(
            Transformer::objectToStdClass((object) ['list' => $nestedParams['languageOptions'] ?? []]),
        );
        $setupConfiguration->layout = (string) ($nestedParams['layout'] ?? $defaultDialogSettingsConfig->layout);
        $setupConfiguration->logo = (string) ($nestedParams['logo'] ?? $defaultDialogSettingsConfig->logo);
        $setupConfiguration->privacyPages = Transformer::toKeyValueDtoList($nestedParams['privacyPages'] ?? [], 'languageCode', 'value');
        $setupConfiguration->selectedLanguageCode = (string) ($nestedParams['selectedLanguageCode'] ?? 'en');
        $setupConfiguration->showLogo = (bool) ($nestedParams['showLogo'] ?? $defaultDialogSettingsConfig->showLogo);
        $setupConfiguration->setupFinishedAt = (new DateTime());
        $setupConfiguration->setupStartedAt = (new DateTime())->setTimestamp((int) $nestedParams['startedAt']);
        $setupConfiguration->setupType = SetupTypeEnum::hasValue($nestedParams['setupType'])
            ? SetupTypeEnum::fromValue($nestedParams['setupType'])
            : null;
        $setupConfiguration->wordPressAdminSidebarMenuMode = isset($nestedParams['wordPressAdminSidebarMenuMode']) && WordPressAdminSidebarMenuModeEnum::hasValue($nestedParams['wordPressAdminSidebarMenuMode'])
            ? WordPressAdminSidebarMenuModeEnum::fromValue($nestedParams['wordPressAdminSidebarMenuMode'])
            : $defaultPluginConfig->wordPressAdminSidebarMenuMode;

        if (isset($nestedParams['dialogStyle']) && is_array($nestedParams['dialogStyle'])) {
            foreach ($nestedParams['dialogStyle'] as $key => $value) {
                if (property_exists($setupConfiguration->dialogStyle, $key)) {
                    if (is_int($setupConfiguration->dialogStyle->{$key})) {
                        $setupConfiguration->dialogStyle->{$key} = (int) $value;
                    } elseif (is_bool($setupConfiguration->dialogStyle->{$key})) {
                        $setupConfiguration->dialogStyle->{$key} = (bool) $value;
                    } elseif (is_string($setupConfiguration->dialogStyle->{$key})) {
                        $setupConfiguration->dialogStyle->{$key} = (string) $value;
                    }
                }
            }
        }

        $this->setupAssistantService->finalizeSetup(
            $setupConfiguration,
            $this->wpFunction->wpGetCurrentUser()->user_email,
        );

        return true;
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/setup-assistant/finalize',
            [
                'methods' => 'POST',
                'callback' => [$this, 'finalize'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }
}
