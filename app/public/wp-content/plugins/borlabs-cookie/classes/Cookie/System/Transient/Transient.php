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

namespace Borlabs\Cookie\System\Transient;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Support\Validator;
use LogicException;

final class Transient
{
    public const TRANSIENT_PREFIX = 'BorlabsCookie';

    private WpFunction $wpFunction;

    public function __construct(WpFunction $wpFunction)
    {
        $this->wpFunction = $wpFunction;
    }

    public function get(string $name): ?KeyValueDto
    {
        $this->ensureValidTransientName($name);
        $transientValue = $this->wpFunction->getTransient(self::TRANSIENT_PREFIX . $name);

        if (!is_string($transientValue) || !Validator::isStringJSON($transientValue)) {
            return null;
        }

        return KeyValueDto::fromJson(json_decode($transientValue));
    }

    public function set(string $name, KeyValueDto $value, int $expiration = 0): bool
    {
        $this->ensureValidTransientName($name);

        return $this->wpFunction->setTransient(self::TRANSIENT_PREFIX . $name, json_encode($value), $expiration);
    }

    /**
     * This method ensures that an transient name matches `[A-Z]+[a-zA-Z]+`.
     */
    private function ensureValidTransientName(string $name): void
    {
        if (preg_match('/[A-Z]+[a-zA-Z]+/', $name) === false) {
            throw new LogicException('The option name is not valid. A valid option name must match `[A-Z]+[a-zA-Z]+`.', E_USER_ERROR);
        }
    }
}
