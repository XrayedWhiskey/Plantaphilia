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
use Borlabs\Cookie\Dto\WordPress\PluginInformationDto;

final class PluginInformationTransformer
{
    use SafePropertyAssignmentTrait;

    /**
     * @throws \Borlabs\Cookie\Exception\IncompatibleTypeException
     */
    public function toDto(object $pluginInformation): PluginInformationDto
    {
        $pluginInformationDto = new PluginInformationDto();
        $pluginInformationDto->added = $this->assignPropertySafely($pluginInformationDto, 'added', $pluginInformation, 'added');
        $pluginInformationDto->author = $this->assignPropertySafely($pluginInformationDto, 'author', $pluginInformation, 'author');
        $pluginInformationDto->author_profile = $this->assignPropertySafely($pluginInformationDto, 'author_profile', $pluginInformation, 'authorProfile');
        $pluginInformationDto->banners = $this->assignPropertySafely($pluginInformationDto, 'banners', $pluginInformation, 'banners', 'array');
        $pluginInformationDto->compatibility = $this->assignPropertySafely($pluginInformationDto, 'compatibility', $pluginInformation, 'compatibility', 'array');
        $pluginInformationDto->contributors = array_map(
            fn ($contributor) => (array) $contributor,
            $pluginInformation->contributors ?? [],
        );
        $pluginInformationDto->donate_link = $this->assignPropertySafely($pluginInformationDto, 'donate_link', $pluginInformation, 'donateLink');
        $pluginInformationDto->download_link = $this->assignPropertySafely($pluginInformationDto, 'download_link', $pluginInformation, 'downloadLink');
        $pluginInformationDto->homepage = $this->assignPropertySafely($pluginInformationDto, 'homepage', $pluginInformation, 'homepage');
        $pluginInformationDto->icons = $this->assignPropertySafely($pluginInformationDto, 'icons', $pluginInformation, 'icons', 'array');
        $pluginInformationDto->last_updated = $this->assignPropertySafely($pluginInformationDto, 'last_updated', $pluginInformation, 'lastUpdated');
        $pluginInformationDto->name = $this->assignPropertySafely($pluginInformationDto, 'name', $pluginInformation, 'name');
        $pluginInformationDto->num_ratings = $this->assignPropertySafely($pluginInformationDto, 'num_ratings', $pluginInformation, 'numRatings');
        $pluginInformationDto->rating = $this->assignPropertySafely($pluginInformationDto, 'rating', $pluginInformation, 'rating');
        $pluginInformationDto->ratings = $this->assignPropertySafely($pluginInformationDto, 'ratings', $pluginInformation, 'ratings', 'array');
        $pluginInformationDto->requires = $this->assignPropertySafely($pluginInformationDto, 'requires', $pluginInformation, 'requires');
        $pluginInformationDto->requires_php = $this->assignPropertySafely($pluginInformationDto, 'requires_php', $pluginInformation, 'requiresPhp');
        $pluginInformationDto->screenshots = $this->assignPropertySafely($pluginInformationDto, 'screenshots', $pluginInformation, 'screenshots', 'array');
        $pluginInformationDto->sections = [
            'changelog' => $pluginInformation->sections->changelog ?? '',
            'description' => $pluginInformation->sections->description ?? '',
            'reviews' => $pluginInformation->sections->reviews ?? '',
        ];
        $pluginInformationDto->slug = $this->assignPropertySafely($pluginInformationDto, 'slug', $pluginInformation, 'slug');
        $pluginInformationDto->tags = $this->assignPropertySafely($pluginInformationDto, 'tags', $pluginInformation, 'tags', 'array');
        $pluginInformationDto->tested = $this->assignPropertySafely($pluginInformationDto, 'tested', $pluginInformation, 'tested');
        $pluginInformationDto->version = $this->assignPropertySafely($pluginInformationDto, 'version', $pluginInformation, 'version');
        $pluginInformationDto->versions = $this->assignPropertySafely($pluginInformationDto, 'versions', $pluginInformation, 'versions', 'array');

        return $pluginInformationDto;
    }
}
