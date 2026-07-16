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
 * Class PluginInformationDto is passed to the WordPress update system,
 * so the property names do not conform to our coding standards.
 */
class PluginInformationDto extends AbstractDto
{
    public int $active_installs = 0;

    public string $added;

    public string $author;

    public string $author_profile;

    public array $banners = [];

    public array $compatibility = [];

    public array $contributors = [];

    public string $donate_link = '';

    public string $download_link;

    public string $homepage;

    public array $icons = [];

    public string $last_updated;

    public string $name;

    public int $num_ratings;

    public int $rating;

    public array $ratings = [];

    public string $requires;

    public string $requires_php;

    public array $screenshots = [];

    public array $sections = [];

    public string $slug;

    public array $tags = [];

    public string $tested;

    public string $version;

    public array $versions = [];
}
