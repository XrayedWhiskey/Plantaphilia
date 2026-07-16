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

namespace Borlabs\Cookie\Enum\Service;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static ServiceOptionEnum DATA_COLLECTION()
 * @method static ServiceOptionEnum DATA_PURPOSE()
 * @method static ServiceOptionEnum DISTRIBUTION()
 * @method static ServiceOptionEnum LEGAL_BASIS()
 * @method static ServiceOptionEnum PROCESSING_LOCATION()
 * @method static ServiceOptionEnum TECHNOLOGY()
 */
final class ServiceOptionEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const DATA_COLLECTION = 'data_collection';

    public const DATA_PURPOSE = 'data_purpose';

    public const DISTRIBUTION = 'distribution';

    public const LEGAL_BASIS = 'legal_basis';

    public const PROCESSING_LOCATION = 'processing_location';

    public const TECHNOLOGY = 'technology';

    public static function localized(): array
    {
        return [
            self::DATA_COLLECTION => _x('Data Collection', 'Backend / Services / Options', 'borlabs-cookie'),
            self::DATA_PURPOSE => _x('Data Purpose', 'Backend / Services / Options', 'borlabs-cookie'),
            self::DISTRIBUTION => _x('Distribution', 'Backend / Services / Options', 'borlabs-cookie'),
            self::LEGAL_BASIS => _x('Legal Basis', 'Backend / Services / Options', 'borlabs-cookie'),
            self::PROCESSING_LOCATION => _x('Processing Location', 'Backend / Services / Options', 'borlabs-cookie'),
            self::TECHNOLOGY => _x('Technology', 'Backend / Services / Options', 'borlabs-cookie'),
        ];
    }
}
