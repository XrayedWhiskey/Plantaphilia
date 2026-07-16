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

namespace Borlabs\Cookie\Dto\Adapter;

use Borlabs\Cookie\Dto\AbstractDto;

class WpGetPostTypeArgumentDto extends AbstractDto
{
    public ?bool $canExport = null;

    public ?array $capabilities = null;

    public ?array $capabilityType = null;

    public ?bool $deleteWithUser = null;

    public ?string $description = null;

    public ?bool $excludeFromSearch = null;

    public ?bool $hasArchive = null;

    public ?bool $hierarchical = null;

    public ?string $label = null;

    public ?array $labels = null;

    public ?bool $mapMetaCap = null;

    public ?string $menuIcon = null;

    public ?int $menuPosition = null;

    public ?bool $public = null;

    public ?bool $publiclyQueryable = null;

    public ?array $queryVar = null;

    public $registerMetaBoxCb;

    public ?string $restBase = null;

    public ?string $restControllerClass = null;

    public ?string $restNamespace = null;

    public ?array $rewrite = null;

    public ?bool $showInAdminBar = null;

    public ?bool $showInMenu = null;

    public ?bool $showInNavMenus = null;

    public ?bool $showInRest = null;

    public ?bool $showUi = null;

    public ?array $supports = null;

    public ?array $taxonomies = null;

    public ?array $template = null;

    public ?bool $templateLock = null;
}
