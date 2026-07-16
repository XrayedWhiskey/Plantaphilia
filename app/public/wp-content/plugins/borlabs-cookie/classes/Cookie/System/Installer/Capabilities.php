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

namespace Borlabs\Cookie\System\Installer;

use Borlabs\Cookie\Adapter\WpRoles;

final class Capabilities
{
    /**
     * @var \Borlabs\Cookie\Adapter\WpRoles
     */
    private $wpRoles;

    public function __construct(WpRoles $wpRoles)
    {
        $this->wpRoles = $wpRoles;
    }

    public function add(): void
    {
        $capabilities = $this->get();

        foreach ($capabilities as $cap) {
            $this->wpRoles->add_cap('administrator', $cap);
        }
    }

    public function get(): array
    {
        return [
            // Dashboard, Support & Help
            'manage_borlabs_cookie',
            // Content Blocker, Script Blocker
            'manage_borlabs_cookie_blocker',
            // Dialog Appearance, Dialog Settings, Dialog Localization
            'manage_borlabs_cookie_dialog',
            // License
            'manage_borlabs_cookie_license',
            // Services, Service Groups, TCF
            'manage_borlabs_cookie_services',
            // Settings
            'manage_borlabs_cookie_settings',
        ];
    }

    public function remove(): void
    {
        $capabilities = $this->get();

        foreach ($capabilities as $cap) {
            $this->wpRoles->remove_cap('administrator', $cap);
        }
    }
}
