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
use Borlabs\Cookie\System\GeoIp\GeoIp;
use Borlabs\Cookie\System\Message\MessageManager;
use Exception;
use WP_CLI;

/**
 * Manages the GeoIp database of the Borlabs Cookie plugin.
 */
class GeoIpCommand extends AbstractCommand
{
    private Container $container;

    private GeoIp $geoIp;

    private MessageManager $messageManager;

    /**
     * SystemCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->geoIp = $this->container->get(GeoIp::class);
        $this->messageManager = $this->container->get(MessageManager::class);
    }

    /**
     * Remove the GeoIp database.
     *
     * ## EXAMPLES
     *
     *     # Delete geo ip database
     *     $ wp borlabs-cookie geo-ip delete
     *     Delete successful.
     */
    public function delete()
    {
        $this->geoIp->deleteDatabase();
        WP_CLI::success('Delete successful.');
    }

    /**
     * Download the geo ip database.
     *
     * [--force]
     * : Force download again
     *
     * ## EXAMPLES
     *
     *     # Download geo ip database
     *     $ wp borlabs-cookie geo-ip download
     *     Download time: ?s
     *     Success: Download successful.
     *
     * @throws Exception
     */
    public function download(array $args, array $assocArgs): void
    {
        $startTime = microtime(true);
        $force = isset($assocArgs['force']);
        $this->geoIp->downloadGeoIpDatabase($force);
        $this->printMessages($this->messageManager->getRaw());
        $runTime = round(microtime(true) - $startTime, 3);
        WP_CLI::line('Run time: ' . $runTime . 's');
    }

    /**
     * Get country information for IP address.
     *
     * ## OPTIONS
     *
     * <ipAddress>
     * : IP address
     *
     * ## EXAMPLES
     *
     *     # Lookup ip address
     *     $ wp borlabs-cookie geo-ip lookup ?.?.?.?
     *     Lookup time: 0.023s
     *     Country code: DE
     *     EU country: yes
     */
    public function lookup(array $args, array $assocArgs): void
    {
        WP_CLI::line('Lookup for IP address ' . $args[0] . ':');
        WP_CLI::line('----------------------');

        if (!$this->geoIp->isGeoIpDatabaseDownloaded()) {
            WP_CLI::error('GeoIp database not downloaded');

            return;
        }

        $startTime = microtime(true);
        $country = $this->geoIp->getCountryForIpAddress($args[0]);
        $lookupTime = round(microtime(true) - $startTime, 3);

        WP_CLI::line('Lookup time: ' . $lookupTime . 's');

        if ($country !== null) {
            WP_CLI::line('Country code: ' . $country->code);
            WP_CLI::line('EU country: ' . ($country->isEuropeanUnion ? 'yes' : 'no'));
        } else {
            WP_CLI::error('No country information found for the given IP address');
        }
    }
}
