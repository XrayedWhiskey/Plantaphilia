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

namespace Borlabs\Cookie\System\SafeMode;

use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Support\Hmac;
use Borlabs\Cookie\System\License\License;

final class SafeMode
{
    public const PAYLOAD = ['borlabsCookieSafeModeHash' => true];

    private License $license;

    public function __construct(License $license)
    {
        $this->license = $license;
    }

    public function handle(RequestDto $request): void
    {
        if (isset($request->getData['borlabsCookieSafeModeHash'], $request->getData['borlabsCookieSafeModeTime'])
            && $this->validate((int) $request->getData['borlabsCookieSafeModeTime'], $request->getData['borlabsCookieSafeModeHash'])
        ) {
            setcookie('borlabsCookieSafeModeHash', Hmac::hash($this->buildHmacData((int) $request->getData['borlabsCookieSafeModeTime']), $this->license->get()->siteSalt), ['httponly' => true]);
            setcookie('borlabsCookieSafeModeTime', $request->getData['borlabsCookieSafeModeTime'], ['httponly' => true]);
        }
    }

    public function isEnabled(): bool
    {
        if ($this->isEnabledByConstant()) {
            return true;
        }

        return $this->isEnabledByCookie();
    }

    public function isEnabledByConstant(): bool
    {
        return defined('BORLABS_COOKIE_ENABLE_SAFE_MODE') && constant('BORLABS_COOKIE_ENABLE_SAFE_MODE') === true;
    }

    public function isEnabledByCookie(): bool
    {
        return isset($_COOKIE['borlabsCookieSafeModeHash'], $_COOKIE['borlabsCookieSafeModeTime']) && $this->validate((int) $_COOKIE['borlabsCookieSafeModeTime'], $_COOKIE['borlabsCookieSafeModeHash']);
    }

    private function buildHmacData(int $time): object
    {
        $data = self::PAYLOAD;
        $data['borlabsCookieSafeModeTime'] = $time;

        return (object) $data;
    }

    private function validate(int $time, string $hash): bool
    {
        if (time() - $time > 3600) {
            return false;
        }

        return Hmac::isValid($this->buildHmacData($time), $this->license->get()->siteSalt, $hash);
    }
}
