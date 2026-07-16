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

namespace Borlabs\Cookie\System\Installer\Country;

use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Model\Country\CountryModel;
use Borlabs\Cookie\Repository\Country\CountryRepository;

final class CountrySeeder
{
    public const COUNTRIES = [
        [
            'code' => 'RW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'YE',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'IQ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'IR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CY',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'TZ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SY',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KE',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CD',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'DJ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'UG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CF',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SC',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'JO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'LB',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'OM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'QA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BH',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AE',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'IL',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'ET',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'ER',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'EG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SD',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GR',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'BI',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'EE',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'LV',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'AZ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'LT',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'SJ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GE',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MD',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BY',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'FI',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'AX',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'UA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MK',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'HU',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'BG',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'AL',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PL',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'RO',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'XK',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'ZW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'ZM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'LS',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MU',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SZ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'RE',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'ZA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'YT',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'MZ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AF',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PK',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BD',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TJ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'LK',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BT',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'IN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MV',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'IO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NP',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'UZ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KZ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TF',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'HM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CC',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'VN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TH',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'ID',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'LA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PH',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MY',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'HK',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KH',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'JP',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KP',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CK',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TL',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'RU',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AU',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CX',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MH',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'FM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SB',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TV',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'VU',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NC',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NF',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NZ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'FJ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'LY',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PT',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'LR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CI',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GH',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GQ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BF',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BJ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SL',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'ST',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GI',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'GM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TD',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NE',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'ML',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'EH',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'ES',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'MA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MT',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'DZ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'FO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'DK',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'IS',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GB',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CH',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SE',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'NL',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'AT',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'BE',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'DE',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'LU',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'IE',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'MC',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'FR',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'AD',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'LI',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'JE',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'IM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SK',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'CZ',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'NO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'VA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'IT',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'SI',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'ME',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'HR',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'BA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SH',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BV',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BB',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CV',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GY',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GF',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'SR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GL',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PY',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'UY',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'FK',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GS',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'JM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'DO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CU',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MQ',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'BS',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AI',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TT',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'DM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'LC',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TC',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'VG',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'VC',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MS',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MF',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'BL',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GP',
            'isEuropeanUnion' => true,
        ],
        [
            'code' => 'GD',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KY',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BZ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SV',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GT',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'HN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NI',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'VE',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'EC',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'HT',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CL',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PE',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MX',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PF',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PN',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'KI',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TK',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'TO',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'WF',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'WS',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'NU',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'MP',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'GU',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PR',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'VI',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'UM',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AS',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CA',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'US',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'PS',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'RS',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'AQ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SX',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'CW',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'BQ',
            'isEuropeanUnion' => false,
        ],
        [
            'code' => 'SS',
            'isEuropeanUnion' => false,
        ],
    ];

    private CountryRepository $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function run(string $prefix): AuditDto
    {
        $this->countryRepository->overwriteTablePrefix($prefix);

        $existingCountryCodes = $this->countryRepository->getAllCountryCodes();
        $countryCodes = array_map(function (array $country) {
            return $country['code'];
        }, self::COUNTRIES);

        if (count($existingCountryCodes) === 0 || count(array_diff($existingCountryCodes, $countryCodes)) > 0) {
            $this->countryRepository->truncate();

            foreach (self::COUNTRIES as $country) {
                $countryModel = new CountryModel();
                $countryModel->code = $country['code'];
                $countryModel->isEuropeanUnion = $country['isEuropeanUnion'];
                $this->countryRepository->insert($countryModel);
            }
        }

        // Reset prefix
        $this->countryRepository->overwriteTablePrefix();

        return new AuditDto(true);
    }
}
