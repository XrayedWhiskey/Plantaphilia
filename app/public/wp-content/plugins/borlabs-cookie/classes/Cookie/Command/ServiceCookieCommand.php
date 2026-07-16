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
use Borlabs\Cookie\Enum\Service\CookiePurposeEnum;
use Borlabs\Cookie\Enum\Service\CookieTypeEnum;
use Borlabs\Cookie\Model\Service\ServiceCookieModel;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use WP_CLI;

/**
 * Lists, creates and deletes the service cookies of the Borlabs Cookie plugin.
 */
class ServiceCookieCommand extends AbstractCommand
{
    /**
     * @const FIELDS Default fields to display for each object.
     */
    public const FIELDS = [
        'id',
        'name',
        'lifetime',
        'type',
        'purpose',
    ];

    /**
     * @var string[] Map that defines which attributes map to which model property.
     *               If an attribute is not listed in this map, it is assumed that attribute and model property are the equal.
     */
    protected array $fieldMap = [];

    private Container $container;

    private ServiceCookieRepository $serviceCookieRepository;

    private ServiceRepository $serviceRepository;

    /**
     * ServiceCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->serviceRepository = $this->container->get(ServiceRepository::class);
        $this->serviceCookieRepository = $this->container->get(ServiceCookieRepository::class);
    }

    /**
     * Creates a new service cookie.
     * Note: The option --path is reserved by WP-CLI and cannot be used.
     *
     * ## OPTIONS
     *
     * <serviceId>
     * : The key of the service.
     *
     * <name>
     * : The name of the service cookie.
     *
     * <lifetime>
     * : The lifetime of the service cookie.
     *
     * <type>
     * : The type of the service cookie.
     * ---
     * options:
     *   - http
     *   - session_storage
     *   - local_storage
     * ---
     *
     * <purpose>
     * : The purpose of the service cookie.
     * ---
     * options:
     *   - tracking
     *   - functional
     * ---
     *
     * [--hostname=<hostname>]
     * : The hostname of the service cookie.
     *
     * [--url-path=<url-path>]
     * : The path of the service cookie.
     *
     * [--porcelain]
     * : Output just the new service cookie id.
     *
     * ## EXAMPLES
     *
     *     # Create service cookie for service with ID=23
     *     $ wp borlabs-cookie service-cookie create 23 cookie_name_1 "1 month" session_storage tracking
     *     Success: Created service cookie 244
     */
    public function create(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $name = $args[1];
        $lifetime = $args[2];
        $type = $args[3];
        $purpose = $args[4];
        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);
        $path = WP_CLI\Utils\get_flag_value($assocArgs, 'url-path', null);
        $hostname = WP_CLI\Utils\get_flag_value($assocArgs, 'hostname', null);

        $service = $this->serviceRepository->findById($serviceId);

        if ($service === null) {
            WP_CLI::error('Service with id=' . $serviceId . ' does not exist');

            return;
        }

        $serviceCookie = new ServiceCookieModel();
        $serviceCookie->serviceId = $serviceId;
        $serviceCookie->name = $name;
        $serviceCookie->lifetime = $lifetime;
        $serviceCookie->type = CookieTypeEnum::fromValue($type);
        $serviceCookie->purpose = CookiePurposeEnum::fromValue($purpose);
        $serviceCookie->path = $path;
        $serviceCookie->hostname = $hostname;

        $serviceCookie = $this->serviceCookieRepository->insert($serviceCookie);

        if ($porcelain) {
            WP_CLI::line((string) $serviceCookie->id);
        } else {
            WP_CLI::success('Created service cookie ' . $serviceCookie->id);
        }
    }

    /**
     * Deletes one service cookie.
     *
     * ## OPTIONS
     *
     * <serviceCookie>
     * : The id of the service cookie to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete service 244
     *     $ wp borlabs-cookie service-cookie delete 244 --yes
     *     Success: Removed service cookie 244
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $serviceCookie = $this->serviceCookieRepository->findById($id);

        if ($serviceCookie === null) {
            WP_CLI::error('Cannot find service cookie with id=' . $id);

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the service cookie id=' . $serviceCookie->id . '',
            $assocArgs,
        );

        $this->serviceCookieRepository->delete($serviceCookie);

        WP_CLI::success('Removed service cookie ' . $serviceCookie->id);
    }

    /**
     * Gets a list of service cookies.
     *
     * <service>
     * : Service id
     *
     * ## OPTIONS
     *
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
     *     $ wp borlabs-cookie service-cookie list 1
     */
    public function list(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $formatter = $this->getFormatter(
            $assocArgs,
            self::FIELDS,
        );

        $service = $this->serviceRepository->findById($serviceId, [
            'serviceCookies',
        ]);

        if ($service === null) {
            WP_CLI::error('Service with id=' . $serviceId . ' does not exist');

            return;
        }
        $serviceCookies = $service->serviceCookies;

        $format = $formatter->__get('format') ?? 'table';
        $iterator = \WP_CLI\Utils\iterator_map(
            $serviceCookies,
            function (ServiceCookieModel $serviceCookieModel) use ($format): array {
                foreach ($this->fieldMap as $cliField => $modelField) {
                    $serviceCookieModel->{$cliField} = $serviceCookieModel->{$modelField};
                    unset($serviceCookieModel->{$modelField});
                }

                return $this->mapToCliTable($serviceCookieModel, $format);
            },
        );

        $formatter->display_items($iterator);
    }

    private function mapToCliTable(ServiceCookieModel $serviceCookieModel, string $format): array
    {
        return [
            'id' => $serviceCookieModel->id,
            'name' => $serviceCookieModel->name,
            'lifetime' => $serviceCookieModel->lifetime,
            'type' => $serviceCookieModel->type->value,
            'purpose' => $serviceCookieModel->purpose->value,
            'hostname' => $serviceCookieModel->hostname,
            'url-path' => $serviceCookieModel->path,
        ];
    }
}
