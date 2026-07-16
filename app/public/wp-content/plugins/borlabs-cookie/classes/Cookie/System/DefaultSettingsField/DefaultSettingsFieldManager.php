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

namespace Borlabs\Cookie\System\DefaultSettingsField;

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;

class DefaultSettingsFieldManager
{
    public array $registry = [];

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get(string $languageCode): SettingsFieldDtoList
    {
        $settingsFieldList = new SettingsFieldDtoList();

        foreach ($this->registry as $settingsFieldClass) {
            $settingsFieldList->add(
                $this->container->get($settingsFieldClass)->get($languageCode),
            );
        }

        return $settingsFieldList;
    }
}
