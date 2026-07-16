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
use Borlabs\Cookie\Model\Service\ServiceLocationModel;
use Borlabs\Cookie\Repository\Service\ServiceLocationRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use WP_CLI;

/**
 * Lists, creates and deletes the service locations of the Borlabs Cookie plugin.
 */
class ServiceLocationCommand extends AbstractCommand
{
    /**
     * @const FIELDS Default fields to display for each object.
     */
    public const FIELDS = [
        'id',
        'hostname',
        'path',
    ];

    /**
     * @var string[] Map that defines which attributes map to which model property.
     *               If an attribute is not listed in this map, it is assumed that attribute and model property are the equal.
     */
    protected array $fieldMap = [];

    private Container $container;

    private ServiceLocationRepository $serviceLocationRepository;

    private ServiceRepository $serviceRepository;

    /**
     * ServiceCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->serviceRepository = $this->container->get(ServiceRepository::class);
        $this->serviceLocationRepository = $this->container->get(ServiceLocationRepository::class);
    }

    /**
     * Creates a new service location.
     *
     * ## OPTIONS
     *
     * <serviceId>
     * : The ID of the service.
     *
     * <hostname>
     * : The host name of the service location.
     *
     * <path>
     * : The path of the service location.
     *
     * [--porcelain]
     * : Output just the new id.
     *
     * ## EXAMPLES
     *
     *     # Create service
     *     $ wp borlabs-cookie service-location create 3 some-site.com "/some/path"
     *     Success: Created service 225.
     */
    public function create(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $hostname = $args[1];
        $path = $args[2];
        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $service = $this->serviceRepository->findById($serviceId);

        if ($service === null) {
            WP_CLI::error('Service with id=' . $serviceId . ' does not exist');

            return;
        }

        $serviceLocation = new ServiceLocationModel();
        $serviceLocation->serviceId = $serviceId;
        $serviceLocation->hostname = $hostname;
        $serviceLocation->path = $path;

        $serviceLocation = $this->serviceLocationRepository->insert($serviceLocation);

        if ($porcelain) {
            WP_CLI::line((string) $serviceLocation->id);
        } else {
            WP_CLI::success('Created service location ' . $serviceLocation->id);
        }
    }

    /**
     * Deletes one service location.
     *
     * ## OPTIONS
     *
     * <serviceLocation>
     * : The id of the service location to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete service location with ID 2
     *     $ wp borlabs-cookie service-location delete 2
     *     Success: Removed service location 2
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $serviceLocation = $this->serviceLocationRepository->findById($id);

        if ($serviceLocation === null) {
            WP_CLI::error('Cannot find service location with id=' . $id);

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the service location id=' . $serviceLocation->id . '',
            $assocArgs,
        );

        $this->serviceLocationRepository->delete($serviceLocation);

        WP_CLI::success('Removed service location ' . $serviceLocation->id);
    }

    /**
     * Gets a list of service locations.
     *
     * <service>
     * : Service id
     *
     * ## OPTIONS
     *
     * [--field=<field>]
     * : Prints the value of a single field for each service.
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
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
     *     # List the ids of all service cookies of one service
     *     $ wp borlabs-cookie service-location list 20
     *     +-----+---------------+
     *     | id  | hostname      |
     *     +-----+---------------+
     *     | 222 | www.vimeo.com |
     *     +-----+---------------+
     */
    public function list(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $formatter = $this->getFormatter(
            $assocArgs,
            self::FIELDS,
        );

        $service = $this->serviceRepository->findById($serviceId);

        if ($service === null) {
            WP_CLI::error('Service with id=' . $serviceId . ' does not exist');

            return;
        }

        $serviceLocations = $this->serviceLocationRepository->find([
            'serviceId' => $serviceId,
        ], [], [], [
            'service',
        ]);

        $format = $formatter->__get('format') ?? 'table';
        $iterator = \WP_CLI\Utils\iterator_map(
            $serviceLocations,
            function (ServiceLocationModel $serviceLocationModel) use ($format): array {
                return $this->mapToCliTable($serviceLocationModel, $format);
            },
        );

        $formatter->display_items($iterator);
    }

    private function mapToCliTable(ServiceLocationModel $serviceLocationModel, string $format): array
    {
        return [
            'id' => $serviceLocationModel->id,
            'hostname' => $serviceLocationModel->hostname,
            'path' => $serviceLocationModel->path,
            'service-id' => $serviceLocationModel->serviceId,
            'service-name' => $serviceLocationModel->service->name,
        ];
    }
}
