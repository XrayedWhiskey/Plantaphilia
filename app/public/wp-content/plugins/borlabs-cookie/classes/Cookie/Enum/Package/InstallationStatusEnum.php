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

namespace Borlabs\Cookie\Enum\Package;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static InstallationStatusEnum FAILURE()
 * @method static InstallationStatusEnum SUCCESS()
 */
class InstallationStatusEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const FAILURE = 'failure';

    public const SUCCESS = 'success';

    public static function localized(): array
    {
        return [
            self::FAILURE => _x('Failed', 'Backend / Package / Installation Status', 'borlabs-cookie'),
            self::SUCCESS => _x('Successful', 'Backend / Package / Installation Status', 'borlabs-cookie'),
        ];
    }
}
