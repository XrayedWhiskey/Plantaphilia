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
use Borlabs\Cookie\Dto\System\SettingsFieldDto;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Service\DefaultSettingsField\AsynchronousOptOutCode;
use Borlabs\Cookie\System\Service\DefaultSettingsField\BlockCookiesBeforeConsent;
use Borlabs\Cookie\System\Service\DefaultSettingsField\DisableCodeExecution;
use Borlabs\Cookie\System\Service\DefaultSettingsField\Prioritize;
use Borlabs\Cookie\System\Service\ServiceDefaultSettingsFieldManager;
use Borlabs\Cookie\System\Service\ServiceService;
use Borlabs\Cookie\Validator\Service\ServiceValidator;
use WP_CLI;

/**
 * Lists, creates, updates and deletes the services of the Borlabs Cookie plugin.
 */
class ServiceCommand extends AbstractCommand
{
    /**
     * @const DEFAULT_FIELDS Default fields to display for each object.
     */
    public const DEFAULT_FIELDS = [
        'id',
        'name',
        'key',
        'language',
        'provider-name',
        'description',
        'position',
        'undeletable',
        'status',
    ];

    /**
     * @const OPTIONAL_FIELDS Optional field to display for each object.
     */
    public const OPTIONAL_FIELDS = [
        'provider-id',
        'provider-address',
        'provider-partners',
        'opt-in-code',
        'opt-out-code',
        'fallback-code',
        // settings
        'block-cookies-before-consent',
        'prioritize',
    ];

    /**
     * @const PROVIDER_FIELDS Fields to display for each provider.
     */
    public const PROVIDER_FIELDS = [
        'key',
        'name',
        'description',
    ];

    /**
     * @var array<string, string> Map that defines which attributes map to which model property.
     *                            If an attribute is not listed in this map, it is assumed that attribute and model property are the equal.
     */
    protected array $fieldMap = [
        'provider-id' => 'providerId',
    ];

    /**
     * @var array<string> list of attributes that can be filtered / ordered with wp cli
     */
    protected array $orderAndFilterableFields = [
        'id',
        'name',
        'key',
        'language',
        'provider-id',
        'description',
        'position',
        'undeletable',
        'status',
    ];

    private Container $container;

    private MessageManager $message;

    private ProviderRepository $providerRepository;

    private ServiceRepository $serviceRepository;

    private ServiceService $serviceService;

    private ServiceValidator $serviceValidation;

    /**
     * ServiceCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->serviceRepository = $this->container->get(ServiceRepository::class);
        $this->providerRepository = $this->container->get(ProviderRepository::class);
        $this->serviceService = $this->container->get(ServiceService::class);
        $this->serviceValidation = $this->container->get(ServiceValidator::class);
        $this->message = $this->container->get(MessageManager::class);
    }

    /**
     * Creates a new service.
     *
     * ## OPTIONS
     *
     * <key>
     * : The key of the service.
     *
     * <language>
     * : The language code (e.g. en, de, ...) of the service.
     *
     * <name>
     * : The name of the service.
     *
     * <position>
     * : The position of the service.
     *
     * <service-group-id>
     * : The ID of the service group the service should belong to.
     *
     * <provider-id>
     * : The ID of the provider the service should use.
     *
     * [--description=<description>]
     * : The description of the service.
     *
     * [--block-cookies-before-consent=<block-cookies-before-consent>]
     * : Whether the service will block cookies before the consent.
     *
     * [--prioritize=<prioritize>]
     * : Whether the service will be prioritized.
     *
     * [--opt-in-code=<opt-in-code>]
     * : The opt-in code of the service.
     *
     * [--opt-out-code=<opt-out-code>]
     * : The opt-out code of the service.
     *
     * [--fallback-code=<fallback-code>]
     * : The fallback code of the service.
     *
     * [--status=<status>]
     * : Whether the service will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether the service will be undeletable.
     *
     * [--porcelain]
     * : Output just the new id.
     *
     * ## EXAMPLES
     *
     *     # Create service
     *     $ wp borlabs-cookie service create some-service en "Some Service" 10 70 "A Provider"
     *     Success: Created service 93
     *
     *     # Create service without success message
     *     $ wp borlabs-cookie service create some-service en "Some Service" 10 70 "A Provider" --porcelain
     *     93
     */
    public function create(array $args, array $assocArgs): void
    {
        $key = $args[0];
        $language = $args[1];
        $name = $args[2];
        $position = $args[3];
        $serviceGroupId = (int) ($args[4]);
        $providerId = (int) $args[5];
        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description', '');
        $optInCode = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-in-code', '');
        $optOutCode = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-out-code', '');
        $fallbackCode = WP_CLI\Utils\get_flag_value($assocArgs, 'fallback-code', '');
        $status = (bool) WP_CLI\Utils\get_flag_value($assocArgs, 'status', true);
        $undeletable = (bool) WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable', false);
        $porcelain = (bool) WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        // settings fields
        $asynchronousOptOutCode = (bool) WP_CLI\Utils\get_flag_value($assocArgs, 'asynchronous-opt-out-code', false);
        $blockCookiesBeforeConsent = (bool) WP_CLI\Utils\get_flag_value(
            $assocArgs,
            'block-cookies-before-consent',
            false,
        );
        $disableCodeExecution = (bool) WP_CLI\Utils\get_flag_value($assocArgs, 'disable-code-execution', false);
        $prioritize = (bool) WP_CLI\Utils\get_flag_value($assocArgs, 'prioritize', false);

        $validationData = [
            'id' => '-1',
            'key' => $key,
            'name' => $name,
        ];

        if (!$this->serviceValidation->isValid($validationData, $language)) {
            $this->printMessages($this->message->getRaw());
            WP_CLI::error('Service creation failed', false);

            return;
        }

        $provider = $this->providerRepository->findById($providerId);

        if ($provider === null) {
            WP_CLI::error('Provider with id=' . $providerId . ' does not exist');

            return;
        }

        $settingsFields = new SettingsFieldDtoList();
        $defaultSettingsFields = $this->container->get(ServiceDefaultSettingsFieldManager::class)->get($language);

        foreach ($defaultSettingsFields->list as $defaultSettingsField) {
            $defaultSettingsField->value = $defaultSettingsField->defaultValue;
            $settingsFields->add($defaultSettingsField, true);
        }
        $settingsFields->getByKey(AsynchronousOptOutCode::KEY)->value = $asynchronousOptOutCode ? '1' : '0';
        $settingsFields->getByKey(BlockCookiesBeforeConsent::KEY)->value = $blockCookiesBeforeConsent ? '1' : '0';
        $settingsFields->getByKey(DisableCodeExecution::KEY)->value = $disableCodeExecution ? '1' : '0';
        $settingsFields->getByKey(Prioritize::KEY)->value = $prioritize ? '1' : '0';

        $service = new ServiceModel();
        $service->key = $key;
        $service->language = $language;
        $service->serviceGroupId = $serviceGroupId;
        $service->name = $name;
        $service->description = $description;
        $service->optInCode = $optInCode;
        $service->optOutCode = $optOutCode;
        $service->fallbackCode = $fallbackCode;
        $service->position = (int) $position;
        $service->status = $status;
        $service->undeletable = $undeletable;
        $service->settingsFields = $settingsFields;
        $service->providerId = $provider->id;

        $service = $this->serviceRepository->insert($service);

        if ($porcelain) {
            WP_CLI::line((string) $service->id);
        } else {
            WP_CLI::success('Created service ' . $service->id);
        }
    }

    /**
     * Deletes one service.
     *
     * ## OPTIONS
     *
     * <serviceGroup>
     * : The id of the service to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete service 2
     *     $ wp borlabs-cookie service delete 2
     *     Success: Removed service 2
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $service = $this->serviceRepository->findById($id);

        if ($service === null) {
            WP_CLI::error('Cannot find service with id=' . $id, true);

            return;
        }

        if ($service->undeletable) {
            WP_CLI::error('The service with id=' . $service->id . ' is undeletable', true);

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the service "' . $service->name . '" in language "' . $service->language
            . '" ',
            $assocArgs,
        );

        $success = $this->serviceRepository->delete($service) === 1;

        if ($success) {
            WP_CLI::success('Removed service ' . $service->id);
        } else {
            WP_CLI::error('Remove failed');
        }
    }

    /**
     * Get details about a service.
     *
     * ## OPTIONS
     *
     * <service>
     * : Service id
     *
     * [--field=<field>]
     * : Instead of returning the whole service, returns the value of a single field.
     * ---
     * options:
     *   - id
     *   - name
     *   - key
     *   - provider-id
     *   - provider-name
     *   - language
     *   - partners
     *   - description
     *   - position
     *   - undeletable
     *   - status
     *   - opt-in-code
     *   - opt-out-code
     *   - fallback-code
     *   - block-cookies-before-consent
     *   - prioritize
     * ---
     *
     * [--fields=<fields>]
     * : Get a specific subset of the service's fields.
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
     * These fields will be displayed by default for the service:
     *
     * * id
     * * name
     * * key
     * * language
     * * provider-name
     * * description
     * * position
     * * undeletable
     * * status
     *
     * These fields are optionally available:
     *
     * * provider-id
     * * provider-address
     * * provider-partners
     * * opt-in-code
     * * opt-out-code
     * * fallback-code
     * * block-cookies-before-consent
     * * prioritize
     *
     * ## EXAMPLES
     *
     *     # Get service
     *     $ wp borlabs-cookie service get 1 --field=name
     *     supervisor
     *
     *     # Get service and export to JSON file
     *     $ wp borlabs-cookie service get 1 --format=json > service.json
     */
    public function get(array $args, array $assocArgs): void
    {
        $service = $this->serviceRepository->findById((int) ($args[0]), [
            'provider',
        ]);

        if ($service === null) {
            WP_CLI::error('Cannot find service with id=' . $args[0], true);

            return;
        }
        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);

        $format = $formatter->__get('format') ?? 'table';
        $data = $this->mapToCliTable($service, $format);

        $formatter->display_item($data);
    }

    /**
     * Gets a list of services.
     *
     * ## OPTIONS
     *
     * [--field=<field>]
     * : Prints the value of a single field for each service.
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     * [--orderby=<orderby>]
     * : Order the list by an attribute.
     * ---
     * default: position
     * options:
     *    - id
     *    - name
     *    - key
     *    - provider-key
     *    - provider-name
     *    - language
     *    - address
     *    - partners
     *    - description
     *    - privacy-url
     *    - cookie-url
     *    - opt-out-url
     *    - position
     *    - undeletable
     *    - status
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
     * [--name=<name>]
     * : Filter by name.
     *
     * [--key=<key>]
     * : Filter by service id.
     *
     * [--provider-id=<provider-id>]
     * : Filter by provider key.
     *
     * [--language=<language>]
     * : Filter by language.
     *
     * [--description=<description>]
     * : Filter by description.
     *
     * [--position=<position>]
     * : Filter by position.
     *
     * [--undeletable=<undeletable>]
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
     * These fields will be displayed by default for each service:
     *
     * * id
     * * name
     * * key
     * * provider-key
     * * provider-name
     * * language
     * * address
     * * partners
     * * description
     * * position
     * * undeletable
     * * status
     *
     * These fields are optionally available:
     *
     * * data-purpose
     * * technology
     * * data-collection
     * * legal-basis
     * * location-processing
     * * distribution
     * * privacy-url
     * * cookie-url
     * * opt-out-url
     * * opt-in-code
     * * opt-out-code
     * * fallback-code
     * * last-synced-at
     * * block-cookies-before-consent
     * * prioritize
     *
     * ## EXAMPLES
     *
     *     # List the ids of all services
     *     $ wp borlabs-cookie service list --field=id
     *     1
     *     3
     *     4
     *     5
     *
     *     # List one field of services in JSON
     *     $ wp borlabs-cookie service list --field=id --format=json
     *     [94,95,96,97,98,99,100,101]
     *
     *     # List all services active services in table
     *     $ wp borlabs-cookie service list --status=1 --fields=id,name
     *     +-----+-------------------+
     *     | id  | name              |
     *     +-----+-------------------+
     *     | 94  | Borlabs Cookie    |
     *     | 95  | Facebook          |
     *     | 96  | Google Maps       |
     *     | 97  | Instagram         |
     *     | 98  | OpenStreetMap     |
     *     | 99  | Twitter           |
     *     | 100 | Vimeo             |
     *     | 101 | YouTube           |
     *     +-----+-------------------+
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
        $services = $this->serviceRepository->find(
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
        $iterator = WP_CLI\Utils\iterator_map($services, function (ServiceModel $service) use ($format): array {
            return $this->mapToCliTable($service, $format);
        });

        $formatter->display_items($iterator);
    }

    /**
     * List settings field of a service.
     *
     * ## OPTIONS
     *
     * <service>
     * : The id of the service to update.
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
     *     # List settings fields of service
     *     $ wp borlabs-cookie service list-settings-fields 93
     *     +------------------------------+-----------+---------------+-------+
     *     | key                          | data-type | default-value | value |
     *     +------------------------------+-----------+---------------+-------+
     *     | prioritize                   | boolean   | 0             | 0     |
     *     | block-cookies-before-consent | boolean   | 0             | 0     |
     *     | asynchronous-opt-out-code    | boolean   | 0             | 1     |
     *     +------------------------------+-----------+---------------+-------+
     *
     * @subcommand list-settings-fields
     */
    public function listSettingsField(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $service = $this->serviceRepository->findById($serviceId);

        if ($service === null) {
            WP_CLI::error('Cannot find service with id=' . $serviceId);

            return;
        }

        if ($service->settingsFields === null) {
            WP_CLI::error('Settings field of service are invalid');

            return;
        }

        $formatter = $this->getFormatter($assocArgs, [
            'key',
            'data-type',
            'default-value',
            'value',
        ]);
        $format = $formatter->__get('format') ?? 'table';
        $iterator = WP_CLI\Utils\iterator_map($service->settingsFields->list, function (SettingsFieldDto $settingsFieldDto) use ($format): array {
            return $this->mapSettingsFieldToCliTable($settingsFieldDto, $format);
        });

        $formatter->display_items($iterator);
    }

    /**
     * Reset default services.
     *
     * ## EXAMPLES
     *
     *     # Reset default services
     *     $ wp borlabs-cookie service reset
     *     Reset was successful
     */
    public function reset(array $args, array $assocArgs): void
    {
        $success = $this->serviceService->reset();

        if ($success) {
            WP_CLI::success('Reset was successful');
        } else {
            WP_CLI::error('Reset failed');
        }
    }

    /**
     * Update an existing service.
     *
     * ## OPTIONS
     *
     * <service>
     * : The id of the service to update.
     *
     * [--name=<name>]
     * : The name of the service.
     *
     * [--position=<position>]
     * : The position of the service.
     *
     * [--service-group-id=<service-group-id>]
     * : The ID of the service group the service should belong to.
     *
     * [--provider-id=<provider-id>]
     * : The provider id of the service.
     *
     * [--description=<description>]
     * : The description of the service.
     *
     * [--block-cookies-before-consent=<block-cookies-before-consent>]
     * : Whether the service will block cookies before the consent.
     *
     * [--prioritize=<prioritize>]
     * : Whether the service will be prioritized.
     *
     * [--opt-in-code=<opt-in-code>]
     * : The opt-in code of the service.
     *
     * [--opt-out-code=<opt-out-code>]
     * : The opt-out code of the service.
     *
     * [--fallback-code=<fallback-code>]
     * : The fallback code of the service.
     *
     * [--status=<status>]
     * : Whether the service will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether the service will be undeletable.
     *
     * [--porcelain]
     * : Output just the new id.
     *
     * ## EXAMPLES
     *
     *     # Update description of service
     *     $ wp borlabs-cookie service update 93 --description="This is a tracking service."
     *     Success: Updated service 93.
     */
    public function update(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $service = $this->serviceRepository->findById($serviceId);

        if ($service === null) {
            WP_CLI::error('Cannot find service with id=' . $serviceId);

            return;
        }

        $name = WP_CLI\Utils\get_flag_value($assocArgs, 'name');

        if ($name !== null) {
            $service->name = $name;
        }

        $position = WP_CLI\Utils\get_flag_value($assocArgs, 'position');

        if ($position !== null) {
            $service->position = (int) $position;
        }

        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description');

        if ($description !== null) {
            $service->description = $description;
        }

        $status = WP_CLI\Utils\get_flag_value($assocArgs, 'status');

        if ($status !== null) {
            $service->status = (bool) $status;
        }
        $providerId = WP_CLI\Utils\get_flag_value($assocArgs, 'provider-id');

        if ($providerId !== null) {
            $provider = $this->providerRepository->findById((int) $providerId);

            if ($provider === null) {
                WP_CLI::error('Provider with id=' . $providerId . ' does not exist');

                return;
            }
            $service->providerId = $provider->id;
        }

        $blockCookiesBeforeConsent = WP_CLI\Utils\get_flag_value($assocArgs, 'block-cookies-before-consent');

        if ($blockCookiesBeforeConsent !== null) {
            $service->settingsFields->getByKey(BlockCookiesBeforeConsent::KEY)->value = $blockCookiesBeforeConsent ? '1'
                : '0';
        }

        $prioritize = WP_CLI\Utils\get_flag_value($assocArgs, 'prioritize');

        if ($prioritize !== null) {
            $service->settingsFields->getByKey(Prioritize::KEY)->value = $prioritize ? '1' : '0';
        }

        $optInCode = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-in-code');

        if ($optInCode !== null) {
            $service->optInCode = $optInCode;
        }

        $optOutCode = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-out-code');

        if ($optOutCode !== null) {
            $service->optOutCode = $optOutCode;
        }

        $fallbackCode = WP_CLI\Utils\get_flag_value($assocArgs, 'fallback-code');

        if ($fallbackCode !== null) {
            $service->fallbackCode = $fallbackCode;
        }
        $undeletable = WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable');

        if ($undeletable !== null) {
            $service->undeletable = (bool) $undeletable;
        }

        $validationData = [
            'id' => '' . $serviceId,
            'name' => $service->name,
        ];

        if (!$this->serviceValidation->isValid($validationData, $service->language)) {
            $this->printMessages($this->message->getRaw());
            WP_CLI::error('Service creation failed');

            return;
        }

        $success = $this->serviceRepository->update($service);

        if ($success) {
            WP_CLI::success('Updated service ' . $service->id . '.');
        } else {
            WP_CLI::error('Update failed.');
        }
    }

    /**
     * Update a settings field of a service.
     *
     * ## OPTIONS
     *
     * <service>
     * : The id of the service to update.
     *
     * <key>
     * : The key of the settings field.
     *
     * <value>
     * : The value of the settings field.
     *
     * ## EXAMPLES
     *
     *     # Update settings field of service
     *     $ wp borlabs-cookie service update-settings-field 93 "asynchronous-opt-out-code" "1"
     *     Success: Updated settings field "asynchronous-opt-out-code" of service 93.
     *
     * @subcommand update-settings-field
     */
    public function updateSettingsField(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $service = $this->serviceRepository->findById($serviceId);

        if ($service === null) {
            WP_CLI::error('Cannot find service with id=' . $serviceId);

            return;
        }

        $key = $args[1];
        $value = $args[2];

        if ($service->settingsFields === null) {
            WP_CLI::error('Settings fields of service are invalid');

            return;
        }

        $settingsField = $service->settingsFields->getByKey($key);

        if ($settingsField === null) {
            WP_CLI::error('Service has no settings field with key "' . $key . '"');

            return;
        }

        if ($settingsField->dataType->is(SettingsFieldDataTypeEnum::BOOLEAN()) && !in_array($value, ['0', '1'], true)) {
            WP_CLI::error('Value of boolean settings field needs to be "0" or "1"');

            return;
        }
        $settingsField->value = $value;

        $success = $this->serviceRepository->update($service);

        if ($success) {
            WP_CLI::success('Updated settings field "' . $key . '" of service ' . $service->id . '.');
        } else {
            WP_CLI::error('Update failed.');
        }
    }

    private function mapToCliTable(ServiceModel $serviceModel, string $format): array
    {
        return [
            'id' => $serviceModel->id,
            'name' => $serviceModel->name,
            'key' => $serviceModel->key,
            'language' => $serviceModel->language,
            'provider-name' => $serviceModel->provider->name,
            'description' => $serviceModel->description,
            'position' => $serviceModel->position,
            'undeletable' => $this->formatBoolean($serviceModel->undeletable, $format),
            'status' => $this->formatBoolean($serviceModel->status, $format),
            // optional fields
            'provider-id' => $serviceModel->provider->id,
            'provider-address' => $serviceModel->provider->address,
            'provider-partners' => $serviceModel->provider->partners,
            'opt-in-code' => $serviceModel->optInCode,
            'opt-out-code' => $serviceModel->optOutCode,
            'fallback-code' => $serviceModel->fallbackCode,
            // settings
            'asynchronous-opt-out-code' => $this->formatBoolean(
                $this->getBooleanSettingsField($serviceModel->settingsFields, AsynchronousOptOutCode::KEY),
                $format,
            ),
            'block-cookies-before-consent' => $this->formatBoolean(
                $this->getBooleanSettingsField($serviceModel->settingsFields, BlockCookiesBeforeConsent::KEY),
                $format,
            ),
            'disable-code-execution' => $this->formatBoolean(
                $this->getBooleanSettingsField($serviceModel->settingsFields, DisableCodeExecution::KEY),
                $format,
            ),
            'prioritize' => $this->formatBoolean(
                $this->getBooleanSettingsField($serviceModel->settingsFields, Prioritize::KEY),
                $format,
            ),
        ];
    }
}
