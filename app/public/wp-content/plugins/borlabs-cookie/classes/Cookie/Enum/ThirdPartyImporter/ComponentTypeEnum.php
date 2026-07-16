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

namespace Borlabs\Cookie\Enum\ThirdPartyImporter;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static ComponentTypeEnum CONTENT_BLOCKER()
 * @method static ComponentTypeEnum SCRIPT_BLOCKER()
 * @method static ComponentTypeEnum SERVICE()
 * @method static ComponentTypeEnum SERVICE_GROUP()
 */
class ComponentTypeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const CONTENT_BLOCKER = 'content-blocker';

    public const SCRIPT_BLOCKER = 'script-blocker';

    public const SERVICE = 'service';

    public const SERVICE_GROUP = 'service-group';

    public static function localized(): array
    {
        return [
            self::CONTENT_BLOCKER => _x('Content Blocker', 'Backend / ThirdPartyImporter / Component Type', 'borlabs-cookie'),
            self::SCRIPT_BLOCKER => _x('Script Blocker', 'Backend / ThirdPartyImporter / Component Type', 'borlabs-cookie'),
            self::SERVICE => _x('Service', 'Backend / ThirdPartyImporter / Component Type', 'borlabs-cookie'),
            self::SERVICE_GROUP => _x('Service Group', 'Backend / ThirdPartyImporter / Component Type', 'borlabs-cookie'),
        ];
    }
}
