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

namespace Borlabs\Cookie\Container;

/**
 * Class RawArgument.
 *
 * The **RawArgument** class is a wrapper for scalar parameters,
 * {@see \Borlabs\Cookie\Container\ContainerService}.
 */
final class RawArgument
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * RawArgument constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
