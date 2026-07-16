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

namespace Borlabs\Cookie\Dto\WordPress;

use Borlabs\Cookie\Dto\AbstractDto;

/**
 * Class LatestPluginVersionDto is passed to the WordPress update system,
 * so the property names do not conform to our coding standards.
 */
class LatestPluginVersionDto extends AbstractDto
{
    /**
     * @var bool this flag is cannot be used by premium plugins
     */
    public bool $autoupdate = false;

    public array $compatibility = [];

    /**
     * @var bool we can set this flag `true` to override the auto-update flag of the user
     */
    public bool $disable_autoupdate = false;

    public array $icons = [];

    public string $id;

    public ?string $new_version = null;

    /**
     * @var null|string Can by null when license is not valid
     */
    public ?string $package = null;

    public string $plugin;

    public string $requires_php;

    public string $slug;

    public string $tested;

    public string $url;
}
