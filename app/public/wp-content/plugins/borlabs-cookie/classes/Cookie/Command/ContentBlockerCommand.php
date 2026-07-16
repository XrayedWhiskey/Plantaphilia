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
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\SettingsFieldDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerDefaultSettingsFieldManager;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerService;
use Borlabs\Cookie\System\ContentBlocker\DefaultSettingsField\ExecuteGlobalCodeBeforeUnblocking;
use Exception;
use WP_CLI;

/**
 * Lists, creates, updates and deletes the content blocker of the Borlabs Cookie plugin.
 */
class ContentBlockerCommand extends AbstractCommand
{
    /**
     * @const DEFAULT_FIELDS Default fields to display for each object.
     */
    public const DEFAULT_FIELDS = [
        'id',
        'key',
        'language',
        'name',
        'status',
        'undeletable',
        'package-key',
    ];

    /**
     * @const OPTIONAL_FIELDS Optional field to display for each object.
     */
    public const OPTIONAL_FIELDS = [
        'provider-id',
        'provider-name',
        'description',
        'preview-html',
        'preview-css',
        'preview-image',
        'global-js',
        'init-js',
        // settings
        'execute-global-code-before-unblocking',
    ];

    /**
     * @var string[] Map that defines which attributes map to which model property.
     *               If an attribute is not listed in this map, it is assumed that attribute and model property are the equal.
     */
    protected array $fieldMap = [
        'preview-html' => 'previewHtml',
        'preview-css' => 'previewCss',
        'preview-image' => 'previewImage',
        'global-js' => 'javaScriptGlobal',
        'init-js' => 'javaScriptInitialization',
        'package-key' => 'borlabsServicePackageKey',
    ];

    /**
     * @var string[] list of attributes that can be filtered / ordered with wp cli
     */
    protected array $orderAndFilterableFields = [
        'id',
        'language',
        'name',
        'status',
        'undeletable',
    ];

    private Container $container;

    private ContentBlockerRepository $contentBlockerRepository;

    private ContentBlockerService $contentBlockerService;

    private ProviderRepository $providerRepository;

    /**
     * ContentBlockerCommand constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->contentBlockerRepository = $this->container->get(ContentBlockerRepository::class);
        $this->contentBlockerService = $this->container->get(ContentBlockerService::class);
        $this->providerRepository = $this->container->get(ProviderRepository::class);
    }

    /**
     * Creates a new content blocker.
     *
     * ## OPTIONS
     *
     * <key>
     * : The key of the content blocker.
     *
     * <language>
     * : The language code (e.g. en, de, ...) of the content blocker.
     *
     * <name>
     * : The name of the content blocker.
     *
     * <providerId>
     * : The id of the provider.
     *
     * [--description=<description>]
     * : The description of the content blocker.
     *
     * [--status=<status>]
     * : Whether the content blocker will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether the content blocker will be undeletable.
     *
     * [--preview-html=<preview-html>]
     * : The preview HTML of the content blocker.
     *
     * [--preview-css=<preview-css>]
     * : The preview CSS of the content blocker.
     *
     * [--preview-image=<preview-image>]
     * : The preview image of the content blocker.
     *
     * [--global-js=<global-js>]
     * : The global JS of the content blocker.
     *
     * [--init-js=<init-js>]
     * : The init JS of the content blocker.
     *
     * [--execute-global-code-before-unblocking=<execute-global-code-before-unblocking>]
     * : Whether the global code will be executed before unblocking.
     *
     * [--porcelain]
     * : Output just the new content blocker id.
     *
     * ## EXAMPLES
     *
     *     # Create content blocker
     *     $ wp borlabs-cookie content-blocker create some-content-blocker en "Some content blocker"
     *     Success: Created content blocker 93
     *
     *     # Create content blocker without success message
     *     $ wp borlabs-cookie content-blocker create some-content-blocker en "Some content blocker" --porcelain
     *     93
     */
    public function create(array $args, array $assocArgs): void
    {
        $key = $args[0];
        $language = $args[1];
        $name = $args[2];
        $providerId = (int) $args[3];

        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description', '');
        $status = (bool) (WP_CLI\Utils\get_flag_value($assocArgs, 'status', true));
        $undeletable = (bool) (WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable', false));
        $previewHtml = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-html', '');
        $previewCss = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-css', '');
        $previewImage = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-image', '');
        $globalJs = WP_CLI\Utils\get_flag_value($assocArgs, 'global-js', '');
        $initJs = WP_CLI\Utils\get_flag_value($assocArgs, 'init-js', '');
        $executeGlobalCodeBeforeUnblocking = (bool) (WP_CLI\Utils\get_flag_value($assocArgs, 'execute-global-code-before-unblocking', false));

        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $contentBlocker = new ContentBlockerModel();
        $contentBlocker->id = -1;
        $contentBlocker->key = $key;
        $contentBlocker->language = $language;
        $contentBlocker->name = $name;
        $contentBlocker->description = $description;
        $contentBlocker->previewHtml = $previewHtml;
        $contentBlocker->previewImage = $previewImage;
        $contentBlocker->previewCss = $previewCss;
        $contentBlocker->javaScriptGlobal = $globalJs;
        $contentBlocker->javaScriptInitialization = $initJs;
        $contentBlocker->status = $status;
        $contentBlocker->undeletable = $undeletable;
        $contentBlocker->providerId = $providerId;
        $contentBlocker->languageStrings = new KeyValueDtoList([]);

        $settingsFields = new SettingsFieldDtoList();
        $defaultSettingsFields = $this->container->get(ContentBlockerDefaultSettingsFieldManager::class)->get($language);

        foreach ($defaultSettingsFields->list as $defaultSettingsField) {
            $defaultSettingsField->value = $defaultSettingsField->defaultValue;
            $settingsFields->add($defaultSettingsField, true);
        }
        $settingsFields->getByKey(ExecuteGlobalCodeBeforeUnblocking::KEY)->value = $executeGlobalCodeBeforeUnblocking ? '1' : '0';
        $contentBlocker->settingsFields = $settingsFields;

        $contentBlocker = $this->contentBlockerRepository->insert($contentBlocker);

        if ($porcelain) {
            WP_CLI::line((string) $contentBlocker->id);
        } else {
            WP_CLI::success('Created content blocker ' . $contentBlocker->id);
        }
    }

    /**
     * Deletes one content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : The id of the content blocker to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete content blocker 2
     *     $ wp borlabs-cookie content-blocker delete 2
     *     Success: Removed content blocker 2
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $contentBlocker = $this->contentBlockerRepository->findById($id);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $id);

            return;
        }

        if ($contentBlocker->undeletable) {
            WP_CLI::error('The content blocker with id=' . $contentBlocker->id . ' is undeletable');

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the content blocker id=' . $contentBlocker->id . '',
            $assocArgs,
        );

        $this->contentBlockerRepository->delete($contentBlocker);

        WP_CLI::success('Removed content blocker ' . $contentBlocker->id);
    }

    /**
     * Remove language string of a content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : The id of the content blocker to update.
     *
     * <key>
     * : The key of the language string.
     *
     * ## EXAMPLES
     *
     *     # Update language string of content blocker
     *     $ wp borlabs-cookie content-blocker set-language-string 13 unblockButton "Unblock content"
     *     Success: Deleted language string "unblockButton" of content blocker 13.
     *
     * @subcommand delete-language-string
     */
    public function deleteLanguageString(array $args, array $assocArgs): void
    {
        $contentBlockerId = (int) ($args[0]);
        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $contentBlockerId);

            return;
        }

        $key = $args[1];

        if ($contentBlocker->languageStrings === null) {
            WP_CLI::error('Language strings of content blocker are invalid');

            return;
        }

        $languageString = $contentBlocker->languageStrings->getByKey($key);

        if ($languageString === null) {
            WP_CLI::error('Language strings has not language string with key "' . $key . '"');

            return;
        }

        $contentBlocker->languageStrings->remove($languageString);

        $success = $this->contentBlockerRepository->update($contentBlocker);

        if ($success) {
            WP_CLI::success('Deleted language string "' . $key . '" of content blocker ' . $contentBlocker->id . '.');
        } else {
            WP_CLI::error('Update failed.');
        }
    }

    /**
     * Get details about a content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : Content blocker id
     *
     * [--field=<field>]
     * : Instead of returning the whole content blocker, returns the value of a single field.
     * ---
     * options:
     *   - id
     *   - key
     *   - language
     *   - name
     *   - hosts
     *   - status
     *   - undeletable
     *   - description
     *   - preview-html
     *   - preview-css
     *   - global-js
     *   - init-js
     *   - unblock-all
     *   - execute-global-code-before-unblocking
     * ---
     *
     * [--fields=<fields>]
     * : Get a specific subset of the content blocker's fields.
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
     * ## AVAILABLE FIELDS
     *
     * These fields will be displayed by default for a content blocker:
     *
     * * id
     * * key
     * * language
     * * name
     * * hosts
     * * status
     * * undeletable
     *
     * These fields are optionally available:
     *
     * * description
     * * preview-html
     * * preview-css
     * * global-js
     * * init-js
     * * unblock-all
     * * execute-global-code-before-unblocking
     *
     * ## EXAMPLES
     *
     *     # Get content blocker
     *     $ wp borlabs-cookie content-blocker get 2 --field=name
     *     Default
     *
     *     # Get content blocker and export to JSON file
     *     $ wp borlabs-cookie content-blocker get 2 --format=json > contentBlocker.json
     */
    public function get(array $args, array $assocArgs): void
    {
        $contentBlocker = $this->contentBlockerRepository->findById((int) ($args[0]), [
            'provider',
        ]);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $args[0], true);

            return;
        }

        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $format = $formatter->__get('format') ?? 'table';
        $data = $this->mapToCliTable($contentBlocker, $format);

        $formatter->display_item($data);
    }

    /**
     * Gets a list of content blockers.
     *
     * ## OPTIONS
     *
     * [--field=<field>]
     * : Prints the value of a single field for each content blocker.
     * ---
     * options:
     *   - id
     *   - key
     *   - language
     *   - name
     *   - hosts
     *   - status
     *   - undeletable
     *   - description
     *   - preview-html
     *   - preview-css
     *   - global-js
     *   - init-js
     *   - unblock-all
     *   - execute-global-code-before-unblocking
     * ---
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     * [--orderby=<orderby>]
     * : Order the list by an attribute.
     * ---
     * default: id
     * options:
     *   - id
     *   - key
     *   - language
     *   - name
     *   - status
     *   - undeletable
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
     * : Filter by content blocker id.
     *
     * [--language=<language>]
     * : Filter by language.
     *
     * [--name=<name>]
     * : Filter by name.
     *
     * [--status=<status>]
     * : Filter by status.
     *
     * [--undeletable=<undeletable>]
     * : Filter by undeletable.
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
     * These fields will be displayed by default for each content blocker:
     *
     * * id
     * * key
     * * language
     * * name
     * * hosts
     * * status
     * * undeletable
     *
     * These fields are optionally available:
     *
     * * description
     * * preview-html
     * * preview-css
     * * global-js
     * * init-js
     * * unblock-all
     * * execute-global-code-before-unblocking
     *
     * ## EXAMPLES
     *
     *     # List the ids of all content blockers
     *     $ wp borlabs-cookie content-blocker list --field=id
     *     1
     *     3
     *     4
     *     5
     *
     *     # List one field of content blockers in JSON
     *     $ wp borlabs-cookie content-blocker list --field=id --format=json
     *     [94,95,96,97,98,99,100,101]
     *
     *     # List all active content blockers in a table
     *     $ wp borlabs-cookie content-blocker list --status=1 --fields=id,name
     *     +----+---------------+
     *     | id | name          |
     *     +----+---------------+
     *     | 2  | Default       |
     *     | 1  | Facebook      |
     *     | 3  | Google Maps   |
     *     | 4  | Instagram     |
     *     | 5  | OpenStreetMap |
     *     | 6  | Twitter       |
     *     | 7  | Vimeo         |
     *     | 8  | YouTube       |
     *     +----+---------------+
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
            $orderby = 'id';
        }

        if ($assocArgs['order'] && in_array(strtoupper($assocArgs['order']), ['ASC', 'DESC'], true)) {
            $order = strtoupper($assocArgs['order']);
        } else {
            $order = 'ASC';
        }
        $contentBlockers = $this->contentBlockerRepository->find(
            $filters,
            [
                $orderby => $order,
            ],
            [],
            [
                'provider',
            ],
        );

        $format = $formatter->__get('format') ?? 'table';
        $iterator = WP_CLI\Utils\iterator_map(
            $contentBlockers,
            function (ContentBlockerModel $contentBlockerModel) use ($format): array {
                return $this->mapToCliTable($contentBlockerModel, $format);
            },
        );

        $formatter->display_items($iterator);
    }

    /**
     * List language strings of a content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : The id of the content blocker to update.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *  - table
     *  - csv
     *  - json
     *  - count
     *  - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     # List language strings of content blocker
     *     $ wp borlabs-cookie content-blocker list-language-strings 93
     *     +---------------------------------------+-----------+---------------+-------+
     *     | key                                   | data-type | default-value | value |
     *     +---------------------------------------+-----------+---------------+-------+
     *     | execute-global-code-before-unblocking | boolean   | 0             | 0     |
     *     +---------------------------------------+-----------+---------------+-------+
     *
     * @subcommand list-language-strings
     */
    public function listLanguageStrings(array $args, array $assocArgs): void
    {
        $contentBlockerId = (int) ($args[0]);
        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $contentBlockerId);

            return;
        }

        if ($contentBlocker->languageStrings === null) {
            WP_CLI::error('Language strings of content blocker are invalid');

            return;
        }

        $formatter = $this->getFormatter($assocArgs, [
            'key',
            'value',
        ]);
        $format = $formatter->__get('format') ?? 'table';
        $iterator = WP_CLI\Utils\iterator_map($contentBlocker->languageStrings->list, function (KeyValueDto $keyValueDto): array {
            return [
                'key' => $keyValueDto->key,
                'value' => $keyValueDto->value,
            ];
        });

        $formatter->display_items($iterator);
    }

    /**
     * List settings field of a content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : The id of the content blocker to update.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *  - table
     *  - csv
     *  - json
     *  - count
     *  - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     # List settings fields of content blocker
     *     $ wp borlabs-cookie content-blocker list-settings-fields 93
     *     +---------------------------------------+-----------+---------------+-------+
     *     | key                                   | data-type | default-value | value |
     *     +---------------------------------------+-----------+---------------+-------+
     *     | execute-global-code-before-unblocking | boolean   | 0             | 0     |
     *     +---------------------------------------+-----------+---------------+-------+
     *
     * @subcommand list-settings-fields
     */
    public function listSettingsFields(array $args, array $assocArgs): void
    {
        $contentBlockerId = (int) ($args[0]);
        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $contentBlockerId);

            return;
        }

        if ($contentBlocker->settingsFields === null) {
            WP_CLI::error('Settings fields of content blocker are invalid');

            return;
        }

        $formatter = $this->getFormatter($assocArgs, [
            'key',
            'data-type',
            'default-value',
            'value',
        ]);
        $format = $formatter->__get('format') ?? 'table';
        $iterator = WP_CLI\Utils\iterator_map($contentBlocker->settingsFields->list, function (SettingsFieldDto $settingsFieldDto) use ($format): array {
            return $this->mapSettingsFieldToCliTable($settingsFieldDto, $format);
        });

        $formatter->display_items($iterator);
    }

    /**
     * Reset default content blockers.
     *
     * ## EXAMPLES
     *
     *     # Reset default content blockers
     *     $ wp borlabs-cookie content-blocker reset
     *     Success: Reset was successful
     *
     * @throws \WP_CLI\ExitException
     */
    public function reset(array $args, array $assocArgs): void
    {
        $success = $this->contentBlockerService->reset();

        if ($success) {
            WP_CLI::success('Reset was successful');
        } else {
            WP_CLI::error('Reset failed');
        }
    }

    /**
     * Add or update a language string of a content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : The id of the content blocker to update.
     *
     * <key>
     * : The key of the language string.
     *
     * <value>
     * : The value of the language string.
     *
     * ## EXAMPLES
     *
     *     # Update language string of content blocker
     *     $ wp borlabs-cookie content-blocker set-language-string 13 unblockButton "Unblock content"
     *     Success: Updated language string "unblockButton" of content blocker 13.
     *
     * @subcommand set-language-string
     */
    public function setLanguageString(array $args, array $assocArgs): void
    {
        $contentBlockerId = (int) ($args[0]);
        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $contentBlockerId);

            return;
        }

        $key = $args[1];
        $value = $args[2];

        if ($contentBlocker->languageStrings === null) {
            WP_CLI::error('Language strings of content blocker are invalid');

            return;
        }

        $languageString = $contentBlocker->languageStrings->getByKey($key);

        if ($languageString !== null) {
            $languageString->value = $value;
        } else {
            $languageString = new KeyValueDto($key, $value);
            $contentBlocker->languageStrings->add($languageString);
        }

        $success = $this->contentBlockerRepository->update($contentBlocker);

        if ($success) {
            WP_CLI::success('Updated language string "' . $key . '" of content blocker ' . $contentBlocker->id . '.');
        } else {
            WP_CLI::error('Update failed.');
        }
    }

    /**
     * Update an existing content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : The id of the content blocker to update.
     *
     * [--key=<key>]
     * : The key of the content blocker.
     *
     * [--language=<language>]
     * : The language code (e.g. en, de, ...) of the content blocker.
     *
     * [--name=<name>]
     * : The name of the content blocker.
     *
     * [--provider-id=<provider-id>]
     * : The id of the provider.
     *
     * [--status=<status>]
     * : Whether the content blocker will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether the content blocker will be undeletable.
     *
     * [--preview-image=<preview-image>]
     * : The preview image of the content blocker.
     *
     * [--preview-html=<preview-html>]
     * : The preview HTML of the content blocker.
     *
     * [--preview-css=<preview-css>]
     * : The preview CSS of the content blocker.
     *
     * [--global-js=<global-js>]
     * : The global JS of the content blocker.
     *
     * [--init-js=<init-js>]
     * : The init JS of the content blocker.
     *
     * [--description=<description>]
     * : The description of the content blocker.
     *
     * [--execute-global-code-before-unblocking=<execute-global-code-before-unblocking>]
     * : Whether the global code will be executed before unblocking.
     *
     * [--porcelain]
     * : Output just the new content blocker id.
     *
     * ## EXAMPLES
     *
     *     # Update name of content blocker
     *     $ wp borlabs-cookie content-blocker update 93 --description="This is a content blocker x."
     *     Success: Updated content blocker 93.
     */
    public function update(array $args, array $assocArgs): void
    {
        $contentBlockerId = (int) ($args[0]);
        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $contentBlockerId);

            return;
        }

        $providerId = WP_CLI\Utils\get_flag_value($assocArgs, 'provider-id');

        if ($providerId !== null) {
            $provider = $this->providerRepository->findById((int) $providerId);

            if ($provider === null) {
                WP_CLI::error('Provider with id=' . $providerId . ' does not exist');

                return;
            }
            $contentBlocker->providerId = $provider->id;
        }

        $key = WP_CLI\Utils\get_flag_value($assocArgs, 'key', null);

        if ($key !== null) {
            $contentBlocker->key = $key;
        }
        $language = WP_CLI\Utils\get_flag_value($assocArgs, 'language', null);

        if ($language !== null) {
            $contentBlocker->language = $language;
        }
        $name = WP_CLI\Utils\get_flag_value($assocArgs, 'name', null);

        if ($name !== null) {
            $contentBlocker->name = $name;
        }
        $status = WP_CLI\Utils\get_flag_value($assocArgs, 'status', null);

        if ($status !== null) {
            $contentBlocker->status = (bool) $status;
        }
        $undeletable = WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable', null);

        if ($undeletable !== null) {
            $contentBlocker->undeletable = (bool) $undeletable;
        }

        $previewImage = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-image', null);

        if ($previewImage !== null) {
            $contentBlocker->previewImage = $previewImage;
        }

        $previewHtml = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-html', null);

        if ($previewHtml !== null) {
            $contentBlocker->previewHtml = $previewHtml;
        }
        $previewCss = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-css', null);

        if ($previewCss !== null) {
            $contentBlocker->previewCss = $previewCss;
        }
        $globalJs = WP_CLI\Utils\get_flag_value($assocArgs, 'global-js', null);

        if ($globalJs !== null) {
            $contentBlocker->javaScriptGlobal = $globalJs;
        }
        $initJs = WP_CLI\Utils\get_flag_value($assocArgs, 'init-js', null);

        if ($initJs !== null) {
            $contentBlocker->javaScriptInitialization = $initJs;
        }
        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description', null);

        if ($description !== null) {
            $contentBlocker->description = $description;
        }
        $executeGlobalCodeBeforeUnblocking = WP_CLI\Utils\get_flag_value(
            $assocArgs,
            'execute-global-code-before-unblocking',
            null,
        );

        if ($executeGlobalCodeBeforeUnblocking !== null) {
            $contentBlocker->settingsFields->getByKey(ExecuteGlobalCodeBeforeUnblocking::KEY)->value = $executeGlobalCodeBeforeUnblocking ? '1' : '0';
        }

        $success = $this->contentBlockerRepository->update($contentBlocker);

        if ($success) {
            WP_CLI::success('Updated content blocker ' . $contentBlocker->id . '.');
        } else {
            WP_CLI::error('Update failed.');
        }
    }

    /**
     * Update a settings field of a content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : The id of the content blocker to update.
     *
     * <key>
     * : The key of the settings field.
     *
     * <value>
     * : The value of the settings field.
     *
     * ## EXAMPLES
     *
     *     # Update settings field of content blocker
     *     $ wp borlabs-cookie content-blocker update-settings-field 93 "execute-global-code-before-unblocking" "1"
     *     Success: Updated settings field "execute-global-code-before-unblocking" of content blocker 93.
     *
     * @subcommand update-settings-field
     */
    public function updateSettingsField(array $args, array $assocArgs): void
    {
        $contentBlockerId = (int) ($args[0]);
        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $contentBlockerId);

            return;
        }

        $key = $args[1];
        $value = $args[2];

        if ($contentBlocker->settingsFields === null) {
            WP_CLI::error('Settings field of content blocker are invalid');

            return;
        }

        $settingsField = $contentBlocker->settingsFields->getByKey($key);

        if ($settingsField === null) {
            WP_CLI::error('Content blocker has no settings field with key "' . $key . '"');

            return;
        }

        if ($settingsField->dataType->is(SettingsFieldDataTypeEnum::BOOLEAN()) && !in_array($value, ['0', '1'], true)) {
            WP_CLI::error('Value of boolean settings field needs to be "0" or "1"');

            return;
        }
        $settingsField->value = $value;

        $success = $this->contentBlockerRepository->update($contentBlocker);

        if ($success) {
            WP_CLI::success('Updated settings field "' . $key . '" of content blocker ' . $contentBlocker->id . '.');
        } else {
            WP_CLI::error('Update failed.');
        }
    }

    private function mapToCliTable(ContentBlockerModel $contentBlockerModel, string $format): array
    {
        return [
            'id' => $contentBlockerModel->id,
            'key' => $contentBlockerModel->key,
            'language' => $contentBlockerModel->language,
            'name' => $contentBlockerModel->name,
            'status' => $this->formatBoolean($contentBlockerModel->status, $format),
            'undeletable' => $this->formatBoolean($contentBlockerModel->undeletable, $format),
            'package-key' => $contentBlockerModel->borlabsServicePackageKey,
            // optional
            'provider-id' => $contentBlockerModel->provider->id,
            'provider-name' => $contentBlockerModel->provider->name,
            'description' => $contentBlockerModel->description,
            'preview-html' => $contentBlockerModel->previewHtml,
            'preview-css' => $contentBlockerModel->previewCss,
            'preview-image' => $contentBlockerModel->previewImage,
            'global-js' => $contentBlockerModel->javaScriptGlobal,
            'init-js' => $contentBlockerModel->javaScriptInitialization,
            'execute-global-code-before-unblocking' => $this->formatBoolean(
                $this->getBooleanSettingsField(
                    $contentBlockerModel->settingsFields,
                    ExecuteGlobalCodeBeforeUnblocking::KEY,
                ),
                $format,
            ),
        ];
    }
}
