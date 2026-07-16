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
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\System\ServiceGroup\ServiceGroupService;
use WP_CLI;

/**
 * Lists, creates, updates and deletes the service groups of the Borlabs Cookie plugin.
 */
class ServiceGroupCommand extends AbstractCommand
{
    /**
     * @const DEFAULT_FIELDS Default fields to display for each object.
     */
    public const DEFAULT_FIELDS = [
        'id',
        'key',
        'name',
        'language',
        'description',
        'pre-selected',
        'position',
        'undeletable',
        'status',
        'services',
    ];

    /**
     * @const OPTIONAL_FIELDS Optional field to display for each object.
     */
    public const OPTIONAL_FIELDS = [];

    /**
     * @var string[] Map that defines which attributes map to which model property.
     *               If an attribute is not listed in this map, it is assumed that attribute and model property are the equal.
     */
    protected array $fieldMap = [
        'pre-selected' => 'preSelected',
    ];

    /**
     * @var string[] list of attributes that can be filtered / ordered with wp cli
     */
    protected array $orderAndFilterableFields = [
        'id',
        'key',
        'name',
        'language',
        'description',
        'pre-selected',
        'position',
        'undeletable',
        'status',
    ];

    private Container $container;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceGroupService $serviceGroupService;

    /**
     * ServiceGroupCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->serviceGroupRepository = $this->container->get(ServiceGroupRepository::class);
        $this->serviceGroupService = $this->container->get(ServiceGroupService::class);
    }

    /**
     * Creates a new service group.
     *
     * ## OPTIONS
     *
     * <key>
     * : The key of the service group.
     *
     * <language>
     * : The language code (e.g. en, de, ...) of the service group.
     *
     * <name>
     * : The name of the service group.
     *
     * <position>
     * : The position of the service group.
     *
     * [--description=<description>]
     * : The description of the service group.
     *
     * [--pre-selected=<pre-selected>]
     * : Whether the service group will be pre selected in the cookie box.
     *
     * [--status=<status>]
     * : Whether the service group will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether the service group will be undeletable.
     *
     * [--porcelain]
     * : Output just the new service group id.
     *
     * ## EXAMPLES
     *
     *     # Create service group
     *     $ wp borlabs-cookie service-group create analytics en "All analytics services" 1
     *     Success: Created service group 3.
     *
     *     # Create service group without success message
     *     $ wp borlabs-cookie service-group create analytics en "All analytics services" 1 --porcelain
     *     4
     */
    public function create(array $args, array $assocArgs): void
    {
        $key = $args[0];
        $language = $args[1];
        $name = $args[2];
        $position = $args[3];
        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description', '');
        $preSelected = WP_CLI\Utils\get_flag_value($assocArgs, 'pre-selected', false);
        $status = WP_CLI\Utils\get_flag_value($assocArgs, 'status', true);
        $undeletable = WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable', false);
        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $serviceGroup = new ServiceGroupModel();
        $serviceGroup->key = $key;
        $serviceGroup->language = $language;
        $serviceGroup->name = $name;
        $serviceGroup->description = $description;
        $serviceGroup->preSelected = (bool) $preSelected;
        $serviceGroup->status = (bool) $status;
        $serviceGroup->undeletable = (bool) $undeletable;
        $serviceGroup->position = (int) $position;

        $savedServiceGroup = $this->serviceGroupRepository->insert($serviceGroup);

        if ($porcelain) {
            WP_CLI::line((string) $savedServiceGroup->id);
        } else {
            WP_CLI::success('Created service group ' . $savedServiceGroup->id);
        }
    }

    /**
     * Deletes one service group.
     *
     * ## OPTIONS
     *
     * <serviceGroup>
     * : The id of the service group to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete service group 2
     *     $ wp borlabs-cookie service-group delete 2
     *     Success: Removed service group 2
     *
     * @throws \WP_CLI\ExitException
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $serviceGroup = $this->serviceGroupRepository->findById($id);

        if ($serviceGroup === null) {
            WP_CLI::error('Cannot find service group with id=' . $id, true);

            return;
        }

        if ($serviceGroup->undeletable) {
            WP_CLI::error('The service group with id=' . $serviceGroup->id . ' is undeletable', true);

            return;
        }

        if ($this->serviceGroupRepository->hasService($serviceGroup)) {
            WP_CLI::error(
                'The service group with id=' . $serviceGroup->id . ' can not be deleted, because it still has a service',
                true,
            );
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the service group "' . $serviceGroup->name . '" in language "'
            . $serviceGroup->language . '" ',
            $assocArgs,
        );

        $this->serviceGroupRepository->delete($serviceGroup);

        WP_CLI::success('Removed service group ' . $serviceGroup->id);
    }

    /**
     * Get details about a service group.
     *
     * ## OPTIONS
     *
     * <service>
     * : Service group id
     *
     * [--field=<field>]
     * : Instead of returning the whole service group, returns the value of a single field.
     * ---
     * options:
     *   - id
     *   - key
     *   - name
     *   - language
     *   - description
     *   - pre-selected
     *   - position
     *   - undeletable
     *   - status
     *   - services
     * ---
     *
     * [--fields=<fields>]
     * : Get a specific subset of the service group's fields.
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
     *
     * ## EXAMPLES
     *
     *     # Get service group
     *     $ wp borlabs-cookie service-group get 1 --field=name
     *     supervisor
     *
     *     # Get service group and export to JSON file
     *     $ wp borlabs-cookie service-group get 1 --format=json > serviceGroup.json
     */
    public function get(array $args, array $assocArgs): void
    {
        $serviceGroup = $this->serviceGroupRepository->findById((int) ($args[0]), [
            'services',
        ]);

        if ($serviceGroup === null) {
            WP_CLI::error('Cannot find service group with id=' . $args[0], true);

            return;
        }

        $data = (array) $serviceGroup;

        foreach ($this->fieldMap as $cliField => $modelField) {
            if (isset($data[$modelField])) {
                $data[$cliField] = $data[$modelField];
                unset($data[$modelField]);
            }
        }

        $data['services'] = array_map(
            function (ServiceModel $service) {
                return $service->id;
            },
            $serviceGroup->services,
        );

        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $formatter->display_item($data);
    }

    /**
     * Gets a list of service groups.
     *
     * ## OPTIONS
     *
     *
     * [--field=<field>]
     * : Prints the value of a single field for each service group.
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     * [--orderby=<orderby>]
     * : Order the list by an attribute.
     * ---
     * default: position
     * options:
     *   - id
     *   - key
     *   - name
     *   - language
     *   - description
     *   - pre-selected
     *   - position
     *   - undeletable
     *   - status
     * ---
     *
     * [--order=<order>]
     * : Order
     * ---
     * default: asc
     * options:
     *   - desc
     *   - asc
     * ---
     *
     * [--id=<id>]
     * : Filter by id.
     *
     * [--key=<key>]
     * : Filter by key.
     *
     * [--name=<name>]
     * : Filter by name.
     *
     * [--language=<language>]
     * : Filter by language.
     *
     * [--description=<description>]
     * : Filter by description.
     *
     * [--undeletable=<undeletable>]
     * : Filter by undeletable.
     *
     * [--pre-selected=<pre-selected>]
     * : Filter by preselected.
     *
     * [--position=<position>]
     * : Filter by position.
     *
     * [--position=<undeletable>]
     * : Filter by undeletable.
     *
     * [--status=<status>]
     * : Filter by status.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - count
     *   - yaml
     * ---
     *
     * ## AVAILABLE FIELDS
     *
     * These fields will be displayed by default for each post:
     *
     * * id
     * * key
     * * name
     * * language
     * * pre-selected
     * * position
     * * undeletable
     * * status
     * * services
     *
     * These fields are optionally available:
     *
     * * description
     *
     * ## EXAMPLES
     *
     *     # List the ids of all service groups
     *     $ wp borlabs-cookie service-group list --field=id
     *     1
     *     3
     *     4
     *     5
     *
     *     # List service groups in JSON
     *     $ wp borlabs-cookie service-group list --key=essential --format=json
     *     [{"id":1,"key":"essential","name":"Essential","language":"en","description":"Essential services enable
     * basic functions and are necessary for the proper function of the
     * website.","pre-selected":true,"position":1,"undeletable":true,"status":true,"services":[19]}]
     *
     *     # List all service groups active services in table
     *     $ wp borlabs-cookie service-group list --status=1 --fields=id,name
     *     +-------------+-------------+
     *     | id          | name        |
     *     +-------------+-------------+
     *     | 1           | Essential   |
     *     +-------------+-------------+
     */
    public function list(array $args, array $assocArgs): void
    {
        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);

        $defaults = [];
        $filters = array_intersect_key(array_merge($defaults, $assocArgs), array_flip($this->orderAndFilterableFields));

        foreach ($this->fieldMap as $cliField => $modelField) {
            if (isset($filters[$cliField])) {
                $filters[$modelField] = $filters[$cliField];
                unset($filters[$cliField]);
            }
        }

        if ($assocArgs['orderby'] && in_array($assocArgs['orderby'], $this->orderAndFilterableFields, true)) {
            if (isset($this->fieldMap[$assocArgs['orderby']])) {
                $orderby = $this->fieldMap[$assocArgs['orderby']];
            } else {
                $orderby = $assocArgs['orderby'];
            }
        } else {
            $orderby = 'position';
        }

        if ($assocArgs['order'] && in_array(strtoupper($assocArgs['order']), ['ASC', 'DESC'], true)) {
            $order = strtoupper($assocArgs['order']);
        } else {
            $order = 'ASC';
        }
        $serviceGroups = $this->serviceGroupRepository->find(
            $filters,
            [
                $orderby => $order,
            ],
            [],
            [
                'services',
            ],
        );

        $format = $formatter->__get('format') ?? 'table';
        $iterator = WP_CLI\Utils\iterator_map(
            $serviceGroups,
            function (ServiceGroupModel $serviceGroup) use ($format): array {
                return $this->mapToCliTable($serviceGroup, $format);
            },
        );

        $formatter->display_items($iterator);
    }

    /**
     * Reset default service groups.
     *
     * ## EXAMPLES
     *
     *     # Sync services
     *     $ wp borlabs-cookie service reset
     *     Reset was successful
     */
    public function reset(array $args, array $assocArgs): void
    {
        $success = $this->serviceGroupService->reset();

        if ($success) {
            WP_CLI::success('Reset was successful');
        } else {
            WP_CLI::error('Reset failed');
        }
    }

    /**
     * Updates an existing service group.
     *
     * ## OPTIONS
     *
     * <service-group>
     * : The id of the service group to update.
     *
     * [--name=<name>]
     * : The name of the service group.
     *
     * [--position=<position>]
     * : The position of the service group.
     *
     * [--description=<description>]
     * : The description of the service group.
     *
     * [--pre-selected=<pre-selected>]
     * : Whether the service group will be pre selected in the cookie box.
     *
     * [--status=<status>]
     * : Whether the service group will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether the service group will be undeletable.
     *
     * ## EXAMPLES
     *
     *     # Update service group
     *     $ wp borlabs-cookie service-group update 3 --status=0 --name='Marketing (old)'
     *     Success: Updated service group 3.
     */
    public function update(array $args, array $assocArgs): void
    {
        $serviceGroupId = (int) ($args[0]);
        $serviceGroup = $this->serviceGroupRepository->findById($serviceGroupId);

        if ($serviceGroup === null) {
            WP_CLI::error('Cannot find service group with id=' . $serviceGroupId);

            return;
        }

        $name = WP_CLI\Utils\get_flag_value($assocArgs, 'name', null);

        if ($name !== null) {
            $serviceGroup->name = $name;
        }
        $position = WP_CLI\Utils\get_flag_value($assocArgs, 'position', null);

        if ($position !== null) {
            $serviceGroup->position = (int) $position;
        }
        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description', null);

        if ($description !== null) {
            $serviceGroup->description = $description;
        }
        $preSelected = WP_CLI\Utils\get_flag_value($assocArgs, 'pre-selected', null);

        if ($preSelected !== null) {
            $serviceGroup->preSelected = (bool) $preSelected;
        }
        $status = WP_CLI\Utils\get_flag_value($assocArgs, 'status', null);

        if ($status !== null) {
            $serviceGroup->status = (bool) $status;
        }
        $undeletable = WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable', null);

        if ($undeletable !== null) {
            $serviceGroup->undeletable = (bool) $undeletable;
        }

        $success = $this->serviceGroupRepository->update($serviceGroup);

        if ($success) {
            WP_CLI::success('Updated service group ' . $serviceGroup->id . '.');
        } else {
            WP_CLI::error('Update failed.');
        }
    }

    private function mapToCliTable(ServiceGroupModel $serviceGroupModel, string $format): array
    {
        return [
            'id' => $serviceGroupModel->id,
            'key' => $serviceGroupModel->key,
            'name' => $serviceGroupModel->name,
            'language' => $serviceGroupModel->language,
            'description' => $serviceGroupModel->description,
            'pre-selected' => $this->formatBoolean($serviceGroupModel->preSelected, $format),
            'position' => $serviceGroupModel->position,
            'undeletable' => $this->formatBoolean($serviceGroupModel->undeletable, $format),
            'status' => $this->formatBoolean($serviceGroupModel->status, $format),
            'services' => array_map(
                function (ServiceModel $service) {
                    return $service->id;
                },
                $serviceGroupModel->services,
            ),
        ];
    }
}
