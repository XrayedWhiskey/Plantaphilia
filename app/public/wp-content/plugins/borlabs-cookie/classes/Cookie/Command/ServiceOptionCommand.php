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
use Borlabs\Cookie\Enum\Service\ServiceOptionEnum;
use Borlabs\Cookie\Model\Service\ServiceOptionModel;
use Borlabs\Cookie\Repository\Service\ServiceOptionRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use WP_CLI;

/**
 * Lists, creates and deletes the service options of the Borlabs Cookie plugin.
 */
class ServiceOptionCommand extends AbstractCommand
{
    /**
     * @const FIELDS Default fields to display for each object.
     */
    public const FIELDS = [
        'id',
        'type',
        'description',
    ];

    private Container $container;

    private ServiceOptionRepository $serviceOptionRepository;

    private ServiceRepository $serviceRepository;

    /**
     * ServiceCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->serviceRepository = $this->container->get(ServiceRepository::class);
        $this->serviceOptionRepository = $this->container->get(ServiceOptionRepository::class);
    }

    /**
     * Creates a new service option.
     *
     * ## OPTIONS
     *
     * <serviceId>
     * : The ID of the service.
     *
     * <type>
     * : The type of the service option.
     * ---
     * options:
     *  - data_collection
     *  - data_purpose
     *  - distribution
     *  - legal_basis
     *  - processing_location
     *  - technology
     * ---
     *
     * <description>
     * : The description of the service option.
     *
     * [--porcelain]
     * : Output just the new id.
     *
     * ## EXAMPLES
     *
     *     # Create service option
     *     $ wp borlabs-cookie service-option create 3 data_collection "This is a description"
     *     Success: Created service option 225.
     */
    public function create(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $type = $args[1];
        $description = $args[2];
        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $service = $this->serviceRepository->findById($serviceId);

        if ($service === null) {
            WP_CLI::error('Service with id=' . $serviceId . ' does not exist');

            return;
        }

        $serviceOption = new ServiceOptionModel();
        $serviceOption->serviceId = $serviceId;
        $serviceOption->description = $description;
        $serviceOption->type = ServiceOptionEnum::fromValue($type);

        $serviceOption = $this->serviceOptionRepository->insert($serviceOption);

        if ($porcelain) {
            WP_CLI::line((string) $serviceOption->id);
        } else {
            WP_CLI::success('Created service option ' . $serviceOption->id);
        }
    }

    /**
     * Deletes one service option.
     *
     * ## OPTIONS
     *
     * <serviceOption>
     * : The id of the service option to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete service option with ID 2
     *     $ wp borlabs-cookie service-option delete 2
     *     Success: Removed service option 2
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $serviceOption = $this->serviceOptionRepository->findById($id);

        if ($serviceOption === null) {
            WP_CLI::error('Cannot find service option with id=' . $id);

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the service option id="' . $serviceOption->id . '"?',
            $assocArgs,
        );

        $this->serviceOptionRepository->delete($serviceOption);

        WP_CLI::success('Removed service option ' . $serviceOption->id);
    }

    /**
     * Gets a list of service options.
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
     *     # List the ids of all service options of one service
     *     $ wp borlabs-cookie service-option list 20
     *     +----+---------------------+-------------+
     *     | id | type                | description |
     *     +----+---------------------+-------------+
     *     | 1  | processing_location | Test        |
     *     +----+---------------------+-------------+
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

        $serviceOptions = $this->serviceOptionRepository->find([
            'serviceId' => $serviceId,
        ], [], [], [
            'service',
        ]);

        $format = $formatter->__get('format') ?? 'table';
        $iterator = \WP_CLI\Utils\iterator_map(
            $serviceOptions,
            function (ServiceOptionModel $serviceOptionModel) use ($format): array {
                return $this->mapToCliTable($serviceOptionModel, $format);
            },
        );

        $formatter->display_items($iterator);
    }

    private function mapToCliTable(ServiceOptionModel $serviceOptionModel, string $format): array
    {
        return [
            'id' => $serviceOptionModel->id,
            'type' => $serviceOptionModel->type->value,
            'description' => $serviceOptionModel->description,
            'service-id' => $serviceOptionModel->serviceId,
            'service-name' => $serviceOptionModel->service->name,
        ];
    }
}
