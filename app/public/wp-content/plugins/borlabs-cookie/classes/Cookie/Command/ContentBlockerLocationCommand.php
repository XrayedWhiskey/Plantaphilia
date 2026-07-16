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
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerLocationModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerLocationRepository;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use WP_CLI;

use function WP_CLI\Utils\iterator_map;

/**
 * Lists, creates and deletes the content blocker locations of the Borlabs Cookie plugin.
 */
class ContentBlockerLocationCommand extends AbstractCommand
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

    private ContentBlockerLocationRepository $contentBlockerLocationRepository;

    private ContentBlockerRepository $contentBlockerRepository;

    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->contentBlockerRepository = $this->container->get(ContentBlockerRepository::class);
        $this->contentBlockerLocationRepository = $this->container->get(ContentBlockerLocationRepository::class);
    }

    /**
     * Creates a new content blocker location.
     *
     * ## OPTIONS
     *
     * <contentBlockerId>
     * : The ID of the content blocker.
     *
     * <hostname>
     * : The host name of the content blocker location.
     *
     * <path>
     * : The path of the content blocker location.
     *
     * [--porcelain]
     * : Output just the new id.
     *
     * ## EXAMPLES
     *
     *     # Create service
     *     $ wp borlabs-cookie service-location create 3 some-site.com "/some/path"
     *     Success: Created service 225.
     *
     * @throws \WP_CLI\ExitException
     */
    public function create(array $args, array $assocArgs): void
    {
        $contentBlockerId = (int) ($args[0]);
        $hostname = $args[1];
        $path = $args[2];
        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId);

        if ($contentBlocker === null) {
            WP_CLI::error('Content blocker with id="' . $contentBlockerId . '" does not exist');

            return;
        }

        $contentBlockerLocation = new ContentBlockerLocationModel();
        $contentBlockerLocation->contentBlockerId = $contentBlockerId;
        $contentBlockerLocation->hostname = $hostname;
        $contentBlockerLocation->path = $path;

        $contentBlockerLocation = $this->contentBlockerLocationRepository->insert($contentBlockerLocation);

        if ($porcelain) {
            WP_CLI::line((string) $contentBlockerLocation->id);
        } else {
            WP_CLI::success('Created content blocker location ' . $contentBlockerLocation->id);
        }
    }

    /**
     * Deletes one content blocker location.
     *
     * ## OPTIONS
     *
     * <serviceLocation>
     * : The ID of the content blocker location to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete content blocker location with ID 2
     *     $ wp borlabs-cookie service-location delete 2
     *     Success: Removed content blocker location 2
     *
     * @throws \WP_CLI\ExitException
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $contentBlockerLocation = $this->contentBlockerLocationRepository->findById($id);

        if ($contentBlockerLocation === null) {
            WP_CLI::error('Cannot find content blocker location with id="' . $id . '"');

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the content blocker location id="' . $contentBlockerLocation->id . '"',
            $assocArgs,
        );

        $this->contentBlockerLocationRepository->delete($contentBlockerLocation);

        WP_CLI::success('Removed content blocker location ' . $contentBlockerLocation->id);
    }

    /**
     * Gets a list of content blocker locations.
     *
     * <contentBlockerId>
     * : The ID of the content blocker.
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
     *
     * @throws \WP_CLI\ExitException
     */
    public function list(array $args, array $assocArgs): void
    {
        $contentBlockerId = (int) ($args[0]);
        $formatter = $this->getFormatter(
            $assocArgs,
            self::FIELDS,
        );

        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId);

        if ($contentBlocker === null) {
            WP_CLI::error('Content blocker with id="' . $contentBlockerId . '" does not exist');

            return;
        }

        $contentBlockerLocations = $this->contentBlockerLocationRepository->find([
            'contentBlockerId' => $contentBlockerId,
        ], [], [], [
            'contentBlocker',
        ]);

        $format = $formatter->__get('format') ?? 'table';
        $iterator = iterator_map(
            $contentBlockerLocations,
            function (ContentBlockerLocationModel $contentBlockerLocationModel) use ($format): array {
                return $this->mapToCliTable($contentBlockerLocationModel, $format);
            },
        );

        $formatter->display_items($iterator);
    }

    private function mapToCliTable(ContentBlockerLocationModel $contentBlockerLocationModel, string $format): array
    {
        return [
            'id' => $contentBlockerLocationModel->id,
            'hostname' => $contentBlockerLocationModel->hostname,
            'path' => $contentBlockerLocationModel->path,
            'content-blocker-id' => $contentBlockerLocationModel->contentBlockerId,
            'content-blocker-name' => $contentBlockerLocationModel->contentBlocker->name,
        ];
    }
}
