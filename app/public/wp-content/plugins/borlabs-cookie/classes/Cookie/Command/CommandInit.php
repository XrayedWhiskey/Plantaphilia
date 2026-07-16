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

namespace Borlabs\Cookie\Command;

use Exception;
use WP_CLI;

class CommandInit
{
    /**
     * @throws Exception
     */
    public function init(): void
    {
        // Note: MainCommand is important even though it is empty, because otherwise WP_CLI::add_command(..) fails
        WP_CLI::add_command('borlabs-cookie', MainCommand::class);
        WP_CLI::add_command('borlabs-cookie content-blocker', ContentBlockerCommand::class);
        WP_CLI::add_command('borlabs-cookie content-blocker-location', ContentBlockerLocationCommand::class);
        WP_CLI::add_command('borlabs-cookie dialog-settings', DialogSettingsCommand::class);
        WP_CLI::add_command('borlabs-cookie general-config', GeneralConfigCommand::class);
        WP_CLI::add_command('borlabs-cookie geo-ip', GeoIpCommand::class);
        WP_CLI::add_command('borlabs-cookie library', LibraryCommand::class);
        WP_CLI::add_command('borlabs-cookie license', LicenseCommand::class);
        WP_CLI::add_command('borlabs-cookie plugin-config', PluginConfigCommand::class);
        WP_CLI::add_command('borlabs-cookie service', ServiceCommand::class);
        WP_CLI::add_command('borlabs-cookie service-cookie', ServiceCookieCommand::class);
        WP_CLI::add_command('borlabs-cookie service-group', ServiceGroupCommand::class);
        WP_CLI::add_command('borlabs-cookie service-location', ServiceLocationCommand::class);
        WP_CLI::add_command('borlabs-cookie service-option', ServiceOptionCommand::class);
        WP_CLI::add_command('borlabs-cookie system', SystemCommand::class);
    }
}
