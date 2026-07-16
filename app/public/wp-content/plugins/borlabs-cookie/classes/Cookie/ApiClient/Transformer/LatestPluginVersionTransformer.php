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

namespace Borlabs\Cookie\ApiClient\Transformer;

use Borlabs\Cookie\ApiClient\Transformer\Traits\SafePropertyAssignmentTrait;
use Borlabs\Cookie\Dto\WordPress\LatestPluginVersionDto;

final class LatestPluginVersionTransformer
{
    use SafePropertyAssignmentTrait;

    /**
     * @throws \Borlabs\Cookie\Exception\IncompatibleTypeException
     */
    public function toDto(object $latestPluginVersion): LatestPluginVersionDto
    {
        $latestPluginVersionDto = new LatestPluginVersionDto();
        $latestPluginVersionDto->compatibility = $this->assignPropertySafely($latestPluginVersionDto, 'compatibility', $latestPluginVersion, 'compatibility', 'array');
        $latestPluginVersionDto->icons = $this->assignPropertySafely($latestPluginVersionDto, 'icons', $latestPluginVersion, 'icons', 'array');
        $latestPluginVersionDto->id = $this->assignPropertySafely($latestPluginVersionDto, 'id', $latestPluginVersion, 'id');
        $latestPluginVersionDto->new_version = $this->assignPropertySafely($latestPluginVersionDto, 'new_version', $latestPluginVersion, 'newVersion');
        $latestPluginVersionDto->package = $this->assignPropertySafely($latestPluginVersionDto, 'package', $latestPluginVersion, 'package');
        $latestPluginVersionDto->plugin = $this->assignPropertySafely($latestPluginVersionDto, 'plugin', $latestPluginVersion, 'plugin');
        $latestPluginVersionDto->requires_php = $this->assignPropertySafely($latestPluginVersionDto, 'requires_php', $latestPluginVersion, 'requiresPhp');
        $latestPluginVersionDto->slug = $this->assignPropertySafely($latestPluginVersionDto, 'slug', $latestPluginVersion, 'slug');
        $latestPluginVersionDto->tested = $this->assignPropertySafely($latestPluginVersionDto, 'tested', $latestPluginVersion, 'tested');
        $latestPluginVersionDto->url = $this->assignPropertySafely($latestPluginVersionDto, 'url', $latestPluginVersion, 'url');

        return $latestPluginVersionDto;
    }
}
