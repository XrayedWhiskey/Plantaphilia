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

namespace Borlabs\Cookie\DtoList\Package;

use Borlabs\Cookie\Dto\Package\ContentBlockerComponentDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<ContentBlockerComponentDto>
 */
final class ContentBlockerComponentDtoList extends AbstractDtoList
{
    public const DTO_CLASS = ContentBlockerComponentDto::class;

    public const UNIQUE_PROPERTY = 'key';

    public function __construct(
        ?array $componentList = null
    ) {
        parent::__construct($componentList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $componentData) {
            $contentBlockerComponent = new ContentBlockerComponentDto(
                $componentData->key,
                $componentData->name,
                LanguageSpecificSettingsFieldDtoList::fromJson($componentData->languageSpecificSetupSettingsFieldsList),
            );
            $list[$key] = $contentBlockerComponent;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $contentBlockerComponent) {
            $list[$key] = ContentBlockerComponentDto::prepareForJson($contentBlockerComponent);
        }

        return $list;
    }
}
