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

namespace Borlabs\Cookie\System\IabTcf;

/**
 * "This was no fun to code..." - ChatGPT.
 */
final class IabTcfCoreStringParser
{
    private $binary;

    private $coreFormat = [
        'Version' => 6,
        'Created' => 36,
        'LastUpdated' => 36,
        'CmpId' => 12,
        'CmpVersion' => 12,
        'ConsentScreen' => 6,
        'ConsentLanguage' => 12,
        'VendorListVersion' => 12,
        'TcfPolicyVersion' => 6,
        'IsServiceSpecific' => 1,
        'UseNonStandardStacks' => 1,
        'SpecialFeatureOptIns' => 12,
        'PurposesConsent' => 24,
        'PurposesLITransparency' => 24,
        'PurposeOneTreatment' => 1,
        'PublisherCC' => 12,
        'VendorConsentSection' => [
            'MaxVendorId' => 16,
            'IsRangeEncoding' => 1,
            // BitField and RangeEntries are variable in length
            'BitField' => 0,
            'RangeEntries' => [
                'NumEntries' => 12,
                'IsARange' => 1,
                'StartOrOnlyVendorId' => 16,
                'EndVendorId' => 16,
            ],
        ],
        'VendorLegitimateInterestSection' => [
            'MaxVendorId' => 16,
            'IsRangeEncoding' => 1,
            // BitField and RangeEntries are variable in length
            'BitField' => 0,
            'RangeEntries' => [
                'NumEntries' => 12,
                'IsARange' => 1,
                'StartOrOnlyVendorId' => 16,
                'EndVendorId' => 16,
            ],
        ],
        'NumPubRestrictions' => 12,
        'PurposeId' => 6,
        'RestrictionType' => 2,
        'PublisherRestrictionsEntries' => [
            'RangeEntries' => [
                'NumEntries' => 12,
                'IsARange' => 1,
                'StartOrOnlyVendorId' => 16,
                'EndVendorId' => 16,
            ],
        ],
    ];

    public function __construct($tcString)
    {
        $decoded = base64_decode(str_pad(strtr($tcString, '-_', '+/'), strlen($tcString) % 4, '=', STR_PAD_RIGHT), true);
        $this->binary = '';

        for ($i = 0; $i < strlen($decoded); ++$i) {
            $this->binary .= str_pad(decbin(ord($decoded[$i])), 8, '0', STR_PAD_LEFT);
        }
    }

    public function parse()
    {
        $values = [];
        $bitIndex = 0;

        foreach ($this->coreFormat as $key => $value) {
            $sectionData = is_array($value) ? $value : null;
            $bitLength = is_int($value) ? $value : null;

            if ($sectionData) {
                $values[$key] = $this->extractSection($sectionData, $bitIndex);
            } else {
                $chunk = substr($this->binary, $bitIndex, $bitLength);
                $bitIndex += $bitLength;

                if (in_array($key, ['SpecialFeatureOptIns', 'PurposesConsent', 'PurposesLITransparency',], true)) {
                    $values[$key] = $this->extractArrayWith1BitValues($chunk);
                } else {
                    $values[$key] = $this->extractSingleValue($chunk);
                }
            }

            if ($bitIndex >= strlen($this->binary)) {
                break;
            }
        }

        return $values;
    }

    private function extractArrayWith1BitValues($chunk)
    {
        return array_map(function ($bit) {
            return (int) $bit;
        }, str_split($chunk, 1));
    }

    private function extractRange($rangeSchema, &$bitIndex)
    {
        $chunk = substr($this->binary, $bitIndex, $rangeSchema['NumEntries']);
        $bitIndex += $rangeSchema['NumEntries'];

        $sectionEntries = [];
        $numOfEntries = $this->extractSingleValue($chunk);

        for ($i = 0; $i < $numOfEntries; ++$i) {
            $isARange = (int) substr($this->binary, $bitIndex, $rangeSchema['IsARange']);
            $bitIndex += $rangeSchema['IsARange'];

            $startOrOnlyVendorId = 0;

            for ($j = 0; $j < $rangeSchema['StartOrOnlyVendorId']; ++$j) {
                $startOrOnlyVendorId = ($startOrOnlyVendorId << 1) | (substr($this->binary, $bitIndex, 1) === '1' ? 1 : 0);
                ++$bitIndex;
            }

            if ($isARange) {
                $endVendorId = 0;

                for ($j = 0; $j < $rangeSchema['EndVendorId']; ++$j) {
                    $endVendorId = ($endVendorId << 1) | (substr($this->binary, $bitIndex, 1) === '1' ? 1 : 0);
                    ++$bitIndex;
                }
            } else {
                $endVendorId = null;
            }

            $sectionEntries[] = [
                'IsARange' => $isARange,
                'StartOrOnlyVendorId' => $startOrOnlyVendorId,
                'EndVendorId' => $endVendorId,
            ];
        }

        return $sectionEntries;
    }

    private function extractSection($sectionSchema, &$bitIndex)
    {
        $sectionValues = [];

        foreach ($sectionSchema as $key => $value) {
            $bitLength = is_int($value) ? $value : null;
            $rangeData = is_array($value) ? $value : null;

            // Skip BitField when IsRangeEncoding is 1
            if (isset($sectionValues['IsRangeEncoding']) && $sectionValues['IsRangeEncoding'] === 1 && $key === 'BitField') {
                continue;
            }

            // Skip RangeEntries when IsRangeEncoding is 0
            if (isset($sectionValues['IsRangeEncoding']) && $sectionValues['IsRangeEncoding'] === 0 && $key === 'RangeEntries') {
                continue;
            }

            // Extract RangeEntries
            if ($key === 'RangeEntries' && $rangeData) {
                $sectionValues[$key] = $this->extractRange($rangeData, $bitIndex);

                continue;
            }

            $bitLength = $key === 'BitField' ? $sectionValues['MaxVendorId'] : $bitLength;
            $chunk = substr($this->binary, $bitIndex, $bitLength);
            $bitIndex += $bitLength;

            if ($key === 'BitField') {
                $sectionValues[$key] = $this->extractArrayWith1BitValues($chunk);
            } else {
                $sectionValues[$key] = $this->extractSingleValue($chunk);
            }

            // If IsRangeEncoding is 0, BitField bit range needs to be set to MaxVendorId value
            if ($key === 'IsRangeEncoding' && $sectionValues[$key] === 0) {
                $sectionSchema['BitField'] = $sectionValues['MaxVendorId'];
            }
        }

        return $sectionValues;
    }

    private function extractSingleValue($chunk)
    {
        $value = 0;

        for ($i = 0; $i < strlen($chunk); ++$i) {
            $value = ($value << 1) | ($chunk[$i] === '1' ? 1 : 0);
        }

        return $value;
    }
}
