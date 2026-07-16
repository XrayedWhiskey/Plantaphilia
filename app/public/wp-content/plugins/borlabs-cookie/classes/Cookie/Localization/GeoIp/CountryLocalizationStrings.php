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

namespace Borlabs\Cookie\Localization\GeoIp;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class CountryLocalizationStrings implements LocalizationInterface
{
    public static function get(): array
    {
        return [
            // Countries
            'countries' => [
                'AD' => _x(
                    'Andorra',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AE' => _x(
                    'United Arab Emirates',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AF' => _x(
                    'Afghanistan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AG' => _x(
                    'Antigua and Barbuda',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AI' => _x(
                    'Anguilla',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AL' => _x(
                    'Albania',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AM' => _x(
                    'Armenia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AO' => _x(
                    'Angola',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AQ' => _x(
                    'Antarctica',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AR' => _x(
                    'Argentina',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AS' => _x(
                    'American Samoa',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AT' => _x(
                    'Austria',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AU' => _x(
                    'Australia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AW' => _x(
                    'Aruba',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AX' => _x(
                    'Åland Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'AZ' => _x(
                    'Azerbaijan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BA' => _x(
                    'Bosnia and Herzegovina',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BB' => _x(
                    'Barbados',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BD' => _x(
                    'Bangladesh',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BE' => _x(
                    'Belgium',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BF' => _x(
                    'Burkina Faso',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BG' => _x(
                    'Bulgaria',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BH' => _x(
                    'Bahrain',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BI' => _x(
                    'Burundi',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BJ' => _x(
                    'Benin',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BL' => _x(
                    'Saint Barthélemy',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BM' => _x(
                    'Bermuda',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BN' => _x(
                    'Brunei',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BO' => _x(
                    'Bolivia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BQ' => _x(
                    'Bonaire, Sint Eustatius, and Saba',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BR' => _x(
                    'Brazil',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BS' => _x(
                    'Bahamas',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BT' => _x(
                    'Bhutan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BV' => _x(
                    'Bouvet Island',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BW' => _x(
                    'Botswana',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BY' => _x(
                    'Belarus',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'BZ' => _x(
                    'Belize',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CA' => _x(
                    'Canada',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CC' => _x(
                    'Cocos [Keeling] Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CD' => _x(
                    'DR Congo',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CF' => _x(
                    'Central African Republic',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CG' => _x(
                    'Congo Republic',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CH' => _x(
                    'Switzerland',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CI' => _x(
                    'Ivory Coast',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CK' => _x(
                    'Cook Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CL' => _x(
                    'Chile',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CM' => _x(
                    'Cameroon',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CN' => _x(
                    'China',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CO' => _x(
                    'Colombia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CR' => _x(
                    'Costa Rica',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CU' => _x(
                    'Cuba',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CV' => _x(
                    'Cabo Verde',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CW' => _x(
                    'Curaçao',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CX' => _x(
                    'Christmas Island',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CY' => _x(
                    'Cyprus',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'CZ' => _x(
                    'Czechia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'DE' => _x(
                    'Germany',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'DJ' => _x(
                    'Djibouti',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'DK' => _x(
                    'Denmark',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'DM' => _x(
                    'Dominica',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'DO' => _x(
                    'Dominican Republic',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'DZ' => _x(
                    'Algeria',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'EC' => _x(
                    'Ecuador',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'EE' => _x(
                    'Estonia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'EG' => _x(
                    'Egypt',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'EH' => _x(
                    'Western Sahara',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ER' => _x(
                    'Eritrea',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ES' => _x(
                    'Spain',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ET' => _x(
                    'Ethiopia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'FI' => _x(
                    'Finland',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'FJ' => _x(
                    'Fiji',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'FK' => _x(
                    'Falkland Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'FM' => _x(
                    'Federated States of Micronesia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'FO' => _x(
                    'Faroe Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'FR' => _x(
                    'France',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GA' => _x(
                    'Gabon',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GB' => _x(
                    'United Kingdom',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GD' => _x(
                    'Grenada',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GE' => _x(
                    'Georgia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GF' => _x(
                    'French Guiana',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GG' => _x(
                    'Guernsey',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GH' => _x(
                    'Ghana',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GI' => _x(
                    'Gibraltar',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GL' => _x(
                    'Greenland',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GM' => _x(
                    'Gambia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GN' => _x(
                    'Guinea',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GP' => _x(
                    'Guadeloupe',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GQ' => _x(
                    'Equatorial Guinea',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GR' => _x(
                    'Greece',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GS' => _x(
                    'South Georgia and the South Sandwich Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GT' => _x(
                    'Guatemala',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GU' => _x(
                    'Guam',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GW' => _x(
                    'Guinea-Bissau',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'GY' => _x(
                    'Guyana',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'HK' => _x(
                    'Hong Kong',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'HM' => _x(
                    'Heard Island and McDonald Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'HN' => _x(
                    'Honduras',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'HR' => _x(
                    'Croatia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'HT' => _x(
                    'Haiti',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'HU' => _x(
                    'Hungary',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ID' => _x(
                    'Indonesia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'IE' => _x(
                    'Ireland',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'IL' => _x(
                    'Israel',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'IM' => _x(
                    'Isle of Man',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'IN' => _x(
                    'India',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'IO' => _x(
                    'British Indian Ocean Territory',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'IQ' => _x(
                    'Iraq',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'IR' => _x(
                    'Iran',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'IS' => _x(
                    'Iceland',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'IT' => _x(
                    'Italy',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'JE' => _x(
                    'Jersey',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'JM' => _x(
                    'Jamaica',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'JO' => _x(
                    'Jordan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'JP' => _x(
                    'Japan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KE' => _x(
                    'Kenya',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KG' => _x(
                    'Kyrgyzstan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KH' => _x(
                    'Cambodia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KI' => _x(
                    'Kiribati',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KM' => _x(
                    'Comoros',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KN' => _x(
                    'St Kitts and Nevis',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KP' => _x(
                    'North Korea',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KR' => _x(
                    'South Korea',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KW' => _x(
                    'Kuwait',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KY' => _x(
                    'Cayman Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'KZ' => _x(
                    'Kazakhstan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LA' => _x(
                    'Laos',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LB' => _x(
                    'Lebanon',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LC' => _x(
                    'Saint Lucia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LI' => _x(
                    'Liechtenstein',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LK' => _x(
                    'Sri Lanka',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LR' => _x(
                    'Liberia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LS' => _x(
                    'Lesotho',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LT' => _x(
                    'Lithuania',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LU' => _x(
                    'Luxembourg',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LV' => _x(
                    'Latvia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'LY' => _x(
                    'Libya',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MA' => _x(
                    'Morocco',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MC' => _x(
                    'Principality of Monaco',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MD' => _x(
                    'Moldova',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ME' => _x(
                    'Montenegro',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MF' => _x(
                    'Saint Martin',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MG' => _x(
                    'Madagascar',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MH' => _x(
                    'Marshall Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MK' => _x(
                    'North Macedonia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ML' => _x(
                    'Mali',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MM' => _x(
                    'Myanmar',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MN' => _x(
                    'Mongolia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MO' => _x(
                    'Macao',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MP' => _x(
                    'Northern Mariana Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MQ' => _x(
                    'Martinique',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MR' => _x(
                    'Mauritania',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MS' => _x(
                    'Montserrat',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MT' => _x(
                    'Malta',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MU' => _x(
                    'Mauritius',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MV' => _x(
                    'Maldives',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MW' => _x(
                    'Malawi',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MX' => _x(
                    'Mexico',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MY' => _x(
                    'Malaysia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'MZ' => _x(
                    'Mozambique',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NA' => _x(
                    'Namibia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NC' => _x(
                    'New Caledonia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NE' => _x(
                    'Niger',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NF' => _x(
                    'Norfolk Island',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NG' => _x(
                    'Nigeria',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NI' => _x(
                    'Nicaragua',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NL' => _x(
                    'Netherlands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NO' => _x(
                    'Norway',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NP' => _x(
                    'Nepal',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NR' => _x(
                    'Nauru',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NU' => _x(
                    'Niue',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'NZ' => _x(
                    'New Zealand',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'OM' => _x(
                    'Oman',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PA' => _x(
                    'Panama',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PE' => _x(
                    'Peru',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PF' => _x(
                    'French Polynesia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PG' => _x(
                    'Papua New Guinea',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PH' => _x(
                    'Philippines',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PK' => _x(
                    'Pakistan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PL' => _x(
                    'Poland',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PM' => _x(
                    'Saint Pierre and Miquelon',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PN' => _x(
                    'Pitcairn Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PR' => _x(
                    'Puerto Rico',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PS' => _x(
                    'Palestine',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PT' => _x(
                    'Portugal',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PW' => _x(
                    'Palau',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'PY' => _x(
                    'Paraguay',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'QA' => _x(
                    'Qatar',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'RE' => _x(
                    'Réunion',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'RO' => _x(
                    'Romania',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'RS' => _x(
                    'Serbia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'RU' => _x(
                    'Russia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'RW' => _x(
                    'Rwanda',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SA' => _x(
                    'Saudi Arabia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SB' => _x(
                    'Solomon Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SC' => _x(
                    'Seychelles',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SD' => _x(
                    'Sudan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SE' => _x(
                    'Sweden',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SG' => _x(
                    'Singapore',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SH' => _x(
                    'Saint Helena',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SI' => _x(
                    'Slovenia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SJ' => _x(
                    'Svalbard and Jan Mayen',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SK' => _x(
                    'Slovakia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SL' => _x(
                    'Sierra Leone',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SM' => _x(
                    'San Marino',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SN' => _x(
                    'Senegal',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SO' => _x(
                    'Somalia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SR' => _x(
                    'Suriname',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SS' => _x(
                    'South Sudan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ST' => _x(
                    'São Tomé and Príncipe',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SV' => _x(
                    'El Salvador',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SX' => _x(
                    'Sint Maarten',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SY' => _x(
                    'Syria',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'SZ' => _x(
                    'Eswatini',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TC' => _x(
                    'Turks and Caicos Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TD' => _x(
                    'Chad',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TF' => _x(
                    'French Southern Territories',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TG' => _x(
                    'Togo',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TH' => _x(
                    'Thailand',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TJ' => _x(
                    'Tajikistan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TK' => _x(
                    'Tokelau',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TL' => _x(
                    'East Timor',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TM' => _x(
                    'Turkmenistan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TN' => _x(
                    'Tunisia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TO' => _x(
                    'Tonga',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TR' => _x(
                    'Turkey',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TT' => _x(
                    'Trinidad and Tobago',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TV' => _x(
                    'Tuvalu',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TW' => _x(
                    'Taiwan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'TZ' => _x(
                    'Tanzania',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'UA' => _x(
                    'Ukraine',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'UG' => _x(
                    'Uganda',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'UM' => _x(
                    'U.S. Minor Outlying Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'US' => _x(
                    'United States',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'UY' => _x(
                    'Uruguay',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'UZ' => _x(
                    'Uzbekistan',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'VA' => _x(
                    'Vatican City',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'VC' => _x(
                    'Saint Vincent and the Grenadines',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'VE' => _x(
                    'Venezuela',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'VG' => _x(
                    'British Virgin Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'VI' => _x(
                    'U.S. Virgin Islands',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'VN' => _x(
                    'Vietnam',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'VU' => _x(
                    'Vanuatu',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'WF' => _x(
                    'Wallis and Futuna',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'WS' => _x(
                    'Samoa',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'XK' => _x(
                    'Kosovo',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'YE' => _x(
                    'Yemen',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'YT' => _x(
                    'Mayotte',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ZA' => _x(
                    'South Africa',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ZM' => _x(
                    'Zambia',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
                'ZW' => _x(
                    'Zimbabwe',
                    'Backend / Country / Country',
                    'borlabs-cookie',
                ),
            ],

            // Unions
            'unions' => [
                'eu' => _x(
                    'European Union',
                    'Backend / Country / Union',
                    'borlabs-cookie',
                ),
                'nonEu' => _x(
                    'Non EU',
                    'Backend / Country / Union',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
