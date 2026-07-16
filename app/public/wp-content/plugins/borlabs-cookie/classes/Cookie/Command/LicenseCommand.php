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
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\License\License;
use DateTime;
use Exception;
use WP_CLI;

/**
 * Manages the license of the Borlabs Cookie plugin.
 */
class LicenseCommand extends AbstractCommand
{
    private Container $container;

    private License $license;

    /**
     * LicenseCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->license = $this->container->get(License::class);
    }

    /**
     * Get information about the currently registered license.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     # Get information about the current license
     *     $ wp borlabs-cookie license info
     *     +---------------+--------------+
     *     | Field         | Value        |
     *     +---------------+--------------+
     *     | status        | valid        |
     *     | name          | Agency       |
     *     | valid-until   | May 14, 2030 |
     *     | support-until | May 14, 2030 |
     *     | max-sites     | 99           |
     *     +---------------+--------------+
     *
     *     # Get information about the current license in JSON
     *     $ wp borlabs-cookie license info --format=json
     *     {"status":"valid","name":"Agency","valid-until":"May 14, 2022","support-until":"May 14, 2022","max-sites":99}
     *
     * @throws Exception
     */
    public function info(array $args, array $assocArgs): void
    {
        $licenseData = $this->license->get();

        if ($licenseData === null) {
            WP_CLI::error('No license registered');

            return;
        }
        $licenseStatus = $this->license->isLicenseValid() ? 'valid' : 'expired';
        $licenseName = $licenseData->licenseName;

        $licenseValidUntil = '';
        $validUntil = !empty($licenseData->licenseValidUntil) ? new DateTime($licenseData->licenseValidUntil) : null;

        if (!empty($validUntil)) {
            $licenseValidUntil = Formatter::timestamp($validUntil->getTimestamp(), null, '');
        }

        $licenseMaxSites = Searcher::findObject($licenseData->licenseMeta->list, 'key', 'sites')->value ?? '';

        $formatter = $this->getFormatter($assocArgs, [
            'status',
            'name',
            'valid-until',
            'max-sites',
        ]);
        $formatter->display_item([
            'status' => $licenseStatus,
            'name' => $licenseName,
            'valid-until' => $licenseValidUntil,
            'max-sites' => $licenseMaxSites,
        ]);
    }

    /**
     * Register a license key for the Borlabs Cookie plugin.
     * If the argument `--license-key` is not used, the command will ask for the license key.
     *
     * Warning: To prevent the license key from ending up in the bash history,
     * use the argument `--license-key` only with an environment variable or use the command without the argument.
     *
     * [--license-key=<license-key>]
     * : License key for Borlabs Cookie.
     *
     * ## EXAMPLES
     *
     *     # Register license for Borlabs Cookie
     *     $ wp borlabs-cookie license register
     *     Please enter your license key:
     *     ****
     *     Success: License key was successfully registered
     *
     *     # Register license for Borlabs Cookie with an environment variable
     *     $ wp borlabs-cookie license register --license-key="$BORLABS_LICENSE_KEY"
     *     Success: License key was successfully registered
     *
     * @throws \WP_CLI\ExitException
     */
    public function register(array $args, array $assocArgs): void
    {
        // Get license key from user
        $licenseKey = WP_CLI\Utils\get_flag_value($assocArgs, 'license-key', null);

        if ($licenseKey === null) {
            WP_CLI::line('Please enter your license key:');
            $licenseKey = trim(fgets(STDIN));
        }

        try {
            $this->license->register($licenseKey);
        } catch (TranslatedException $e) {
            WP_CLI::error($e->getTranslatedMessage());
        }

        WP_CLI::success('License key was successfully registered');
    }

    /**
     * Remove the current Borlabs Cookie license.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete current Borlabs Cookie license
     *     $ wp borlabs-cookie license remove
     *     Are you sure you want to remove the current Borlabs Cookie license? [y/n] y
     *     Success: Removed license
     *
     *     # Delete current Borlabs Cookie license without confirmation
     *     $ wp borlabs-cookie license remove --yes
     *     Success: Removed license
     */
    public function remove(array $args, array $assocArgs): void
    {
        WP_CLI::confirm(
            'Are you sure you want to remove the current Borlabs Cookie license?',
            $assocArgs,
        );

        $this->license->remove();

        WP_CLI::success('Removed license');
    }
}
