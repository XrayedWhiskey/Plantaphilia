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

namespace Borlabs\Cookie\Model\ContentBlocker;

use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\System\RepeatableSettingsFieldDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Model\Service\ServiceModel;

/**
 * The **ContentBlockerModel** class is used as a typed object that is passed within the system.
 *
 * @see \Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository The repository class for this model.
 */
final class ContentBlockerModel extends AbstractModel
{
    public ?string $borlabsServicePackageKey;

    /**
     * @var null|array<\Borlabs\Cookie\Model\ContentBlocker\ContentBlockerLocationModel> list of locations that triggers the system to automatically use this content blocker
     */
    public ?array $contentBlockerLocations;

    /**
     * @var string Description of the Content Blocker. Displayed only when editing the Content Blocker at the top.
     */
    public string $description = '';

    /**
     * @var string optional; Default: `''`; Global JavaScript is executed once when a Content Blocker of this type is
     *             unblocked
     */
    public string $javaScriptGlobal = '';

    /**
     * @var string optional; Default: `''`; Initial JavaScript is executed when a Content Blocker of this type is
     *             unlocked
     */
    public string $javaScriptInitialization = '';

    /**
     * @var string unique key within the language
     */
    public string $key;

    /**
     * @var string language code
     */
    public string $language;

    /**
     * Language strings of placeholders in HTML preview.
     */
    public ?KeyValueDtoList $languageStrings;

    /**
     * @var string name of the Content Blocker
     */
    public string $name;

    /**
     * @var string optional; Custom CSS rules for this Content Blocker
     */
    public string $previewCss = '';

    /**
     * @var string A html layout that is displayed instead of the blocked content
     */
    public string $previewHtml;

    /**
     * @var string URL of the preview image
     */
    public string $previewImage = '';

    public ?ProviderModel $provider;

    public int $providerId;

    public ?RepeatableSettingsFieldDtoList $repeatableSettingsFields;

    public ?ServiceModel $service;

    public ?int $serviceId;

    public ?SettingsFieldDtoList $settingsFields;

    /**
     * @var bool optional; Default: `false`; `true`: Content Blocker is active and can block content
     */
    public bool $status = false;

    /**
     * @var bool optional; Default: `false`; `true`: Content Blocker cannot be deleted by a user
     */
    public bool $undeletable = false;
}
