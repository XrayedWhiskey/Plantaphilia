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

namespace Borlabs\Cookie\Dto\System;

use Borlabs\Cookie\Dto\AbstractDto;

/**
 * The **KeyValueDto** class is used as a typed object that contains a simple key-value pair.
 *
 * @see \Borlabs\Cookie\Dto\System\KeyValueDto::$key
 * @see \Borlabs\Cookie\Dto\System\KeyValueDto::$value
 */
final class KeyValueDto extends AbstractDto
{
    /**
     * @var string the key should be unique
     */
    public string $key;

    /**
     * @var mixed any serializable data
     */
    public $value;

    /**
     * @param string $key   the key should be unique
     * @param mixed  $value any serializable data
     */
    public function __construct(
        string $key,
        $value
    ) {
        $this->key = $key;
        $this->value = $value;
    }

    protected static function __valueFromJson(string $value)
    {
        return unserialize($value);
    }

    protected static function __valueToJson($value): string
    {
        return serialize($value);
    }
}
