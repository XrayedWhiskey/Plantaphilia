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

namespace Borlabs\Cookie\Enum\CloudScan;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static PageTypeEnum CUSTOM()
 * @method static PageTypeEnum HOMEPAGE()
 * @method static PageTypeEnum SELECT_OF_SITES_PER_POST_TYPE()
 */
class PageTypeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const CUSTOM = 'custom';

    public const HOMEPAGE = 'homepage';

    public const SELECT_OF_SITES_PER_POST_TYPE = 'selection_of_sites_per_post_type';

    public static function localized(): array
    {
        return [
            self::SELECT_OF_SITES_PER_POST_TYPE => _x('Selection of sites per post type', 'Backend / Services / Cookies', 'borlabs-cookie'),
            self::HOMEPAGE => _x('Homepage', 'Backend / Services / Cookies', 'borlabs-cookie'),
            self::CUSTOM => _x('Custom', 'Backend / Services / Cookies', 'borlabs-cookie'),
        ];
    }
}
