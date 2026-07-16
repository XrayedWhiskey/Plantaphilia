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

namespace Borlabs\Cookie\Enum\SetupAssistant;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * @method static SetupTypeEnum CUSTOM()
 * @method static SetupTypeEnum GUIDED()
 * @method static SetupTypeEnum QUICK()
 */
final class SetupTypeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const CUSTOM = 'custom';

    public const GUIDED = 'guided';

    public const QUICK = 'quick';

    public static function localized(): array
    {
        return [
            self::CUSTOM => _x('<translation-key id="Setup-Mode-Custom-Setup">Custom Setup</translation-key>', 'Backend / Setup Assistant / Option', 'borlabs-cookie'),
            self::GUIDED => _x('<translation-key id="Setup-Mode-Guided-Setup">Guided Setup</translation-key>', 'Backend / Setup Assistant / Option', 'borlabs-cookie'),
            self::QUICK => _x('<translation-key id="Setup-Mode-Quick-Setup">Quick Setup</translation-key>', 'Backend / Setup Assistant / Option', 'borlabs-cookie'),
        ];
    }
}
