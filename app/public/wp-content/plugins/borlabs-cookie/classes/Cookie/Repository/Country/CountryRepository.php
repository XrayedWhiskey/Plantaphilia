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

namespace Borlabs\Cookie\Repository\Country;

use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Model\Country\CountryModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;

/**
 * @extends AbstractRepository<CountryModel>
 */
final class CountryRepository extends AbstractRepository implements RepositoryInterface
{
    public const MODEL = CountryModel::class;

    public const TABLE = 'borlabs_cookie_countries';

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('code', 'country_code'),
            new PropertyMapItemDto('isEuropeanUnion', 'is_european_union'),
        ]);
    }

    public function getAllCountryCodes(): array
    {
        $countries = $this->find();

        return array_map(function (CountryModel $countryModel) {
            return $countryModel->code;
        }, $countries);
    }

    public function truncate(): void
    {
        $this->wpdb->query('TRUNCATE ' . $this->wpdb->prefix . self::TABLE);
    }
}
