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

namespace Borlabs\Cookie\System\Template\CustomFunction;

use Borlabs\Cookie\Dependencies\Twig\TwigFunction;
use Borlabs\Cookie\Localization\System\DisplayModeSettingsToggleLocalizationStrings;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Template\Template;

final class DisplayModeSettingsToggle
{
    private DisplayModeSettingsToggleLocalizationStrings $displayModeSettingsToggleLocalizationStrings;

    private PluginConfig $pluginConfig;

    private Template $template;

    public function __construct(
        DisplayModeSettingsToggleLocalizationStrings $displayModeSettingsToggleLocalizationStrings,
        PluginConfig $pluginConfig,
        Template $template
    ) {
        $this->displayModeSettingsToggleLocalizationStrings = $displayModeSettingsToggleLocalizationStrings;
        $this->pluginConfig = $pluginConfig;
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction(
                'displayModeSettingsToggle',
                function () {
                    return $this->template->getEngine()->render(
                        'system/display-mode-settings-toggle.html.twig',
                        [
                            'data' => (array) $this->pluginConfig->get(),
                            'localized' => $this->displayModeSettingsToggleLocalizationStrings->get(),
                        ],
                    );
                },
            ),
        );
    }
}
