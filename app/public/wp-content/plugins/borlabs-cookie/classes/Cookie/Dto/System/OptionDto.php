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
 * The **OptionDto** class is used as a typed object that is passed within the system.
 *
 * @see \Borlabs\Cookie\Dto\System\OptionDto::$name
 * @see \Borlabs\Cookie\Dto\System\OptionDto::$value
 * @see \Borlabs\Cookie\Dto\System\OptionDto::$isGlobal
 * @see \Borlabs\Cookie\Dto\System\OptionDto::$language
 * @see \Borlabs\Cookie\System\Option\Option
 */
final class OptionDto extends AbstractDto
{
    /**
     * @var bool default: `false`; `true`: The option is used for all instances of a multisite network
     */
    public bool $isGlobal;

    /**
     * @var null|string default: `null`; `string`: The option is used for a specific language
     */
    public ?string $language = null;

    /**
     * @var string The name of the option, which must match `[A-Z]+[a-zA-Z]+` when used for non-third-party options.
     *             The prefix `BorlabsCookie` is set by {@see \Borlabs\Cookie\System\Option\Option} when used for
     *             non-third-party options.
     */
    public string $name;

    /**
     * @var mixed any serializable data
     */
    public $value;

    /**
     * OptionDto constructor.
     *
     * @param string      $name     The name of the option, which must match `[A-Z]+[a-zA-Z]+` when used for non-third-party
     *                              options. The prefix `BorlabsCookie` is set by {@see \Borlabs\Cookie\System\Option\Option} when used for
     *                              non-third-party options.
     * @param null        $value    any serializable data
     * @param bool        $isGlobal default: `false`; `true`: The option is used for all instances of a multisite network
     * @param null|string $language default: `null`; `string`: The option is used for a specific language
     */
    public function __construct(
        string $name,
        $value = null,
        bool $isGlobal = false,
        ?string $language = null
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->isGlobal = $isGlobal;
        $this->language = $language;
    }
}
