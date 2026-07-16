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

use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\System\Installer\Install;
use Borlabs\Cookie\System\SystemCheck\SystemCheck;
use Borlabs\Cookie\System\Uninstaller\Uninstaller;
use WP_CLI;

/**
 * Manages the Borlabs Cookie plugin.
 */
class SystemCommand extends AbstractCommand
{
    /**
     * Systems checks that are ignored in the output of the "borlabs-cookie system check" command.
     */
    private const IGNORED_SYSTEM_CHECKS = [
        'system' => [
            'ssl',
        ],
    ];

    private Container $container;

    private Install $install;

    private SystemCheck $systemCheck;

    private Uninstaller $uninstaller;

    /**
     * SystemCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->systemCheck = $this->container->get(SystemCheck::class);
        $this->install = $this->container->get(Install::class);
        $this->uninstaller = $this->container->get(Uninstaller::class);
    }

    /**
     * Runs the system check of the Borlabs Cookie plugin.
     *
     * ## EXAMPLES
     *
     *     # Install Borlabs Cookie
     *     $ wp borlabs-cookie system check
     *     tableConsentLog: Success
     *     tableContentBlocker: Success
     *     tableScriptBlocker: Success
     *     tableServiceGroup: Success
     *     tableServiceCookie: Success
     *     tableServiceLocation: Success
     *     tableService: Success
     *     entryContentBlocker: Success
     *     entryServiceGroup: Success
     *     entryService: Success
     *     defaultServicesInitialSync: Success
     *     cacheFolder: Success
     *     languageSetting: Success
     */
    public function check(array $args, array $assocArgs): void
    {
        $success = true;
        $report = $this->systemCheck->report();

        foreach ($report as $reportCategoryName => $reportCategory) {
            foreach ($reportCategory as $reportName => $reportAudit) {
                if (!in_array($reportName, self::IGNORED_SYSTEM_CHECKS[$reportCategoryName] ?? [], true)) {
                    if (!$reportAudit->success) {
                        $success = false;
                    }
                    WP_CLI::log(
                        $reportCategoryName . '.' . $reportName . ': ' . ($reportAudit->success
                            ? WP_CLI::colorize('%GSuccess%n')
                            : WP_CLI::colorize(
                                '%RFailed%n',
                            )) . (!$reportAudit->success && $reportAudit->message !== '' ? ' (' . $reportAudit->message . ')'
                            : ''),
                    );
                }
            }
        }
    }

    /**
     * Run the installation of the Borlabs Cookie plugin.
     *
     * ## EXAMPLES
     *
     *     # Install Borlabs Cookie
     *     $ wp borlabs-cookie system install
     *     Success: Borlabs Cookie installed successfully.
     */
    public function install(array $args, array $assocArgs): void
    {
        $this->install->pluginActivated();
        WP_CLI::success('Borlabs Cookie installed successfully.');
    }

    /**
     * Run the uninstallation of the Borlabs Cookie plugin.
     *
     * ## EXAMPLES
     *
     *     # Uninstall Borlabs Cookie
     *     $ wp borlabs-cookie system uninstall
     *     Success: Borlabs Cookie uninstalled successfully.
     */
    public function uninstall(array $args, array $assocArgs): void
    {
        $this->uninstaller->run();
        WP_CLI::success('Borlabs Cookie uninstalled successfully.');
    }
}
