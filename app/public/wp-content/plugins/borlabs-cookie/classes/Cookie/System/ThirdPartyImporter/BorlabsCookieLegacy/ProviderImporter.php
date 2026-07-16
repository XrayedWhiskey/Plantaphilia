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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy;

use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Log\Log;

final class ProviderImporter
{
    private Log $log;

    private ProviderRepository $providerRepository;

    public function __construct(
        Log $log,
        ProviderRepository $providerRepository
    ) {
        $this->log = $log;
        $this->providerRepository = $providerRepository;
    }

    public function getOrAddProviderFromLegacyData(
        string $legacyProviderNameAndAddress,
        string $legacyPurpose,
        string $legacyPrivacyUrl,
        string $languageCode
    ): ?ProviderModel {
        $providerKey = $this->generateProviderKey($legacyProviderNameAndAddress);

        $existingModel = $this->providerRepository->getByKey($providerKey, $languageCode);

        if ($existingModel !== null) {
            return $existingModel;
        }

        $newModel = new ProviderModel();
        $newModel->address = ($this->guessAddress($legacyProviderNameAndAddress) ?? 'MISSING ADDRESS');
        $newModel->description = $legacyPurpose;
        $newModel->key = $providerKey;
        $newModel->language = $languageCode;
        $newModel->name = $this->guessName($legacyProviderNameAndAddress);
        $newModel->privacyUrl = $legacyPrivacyUrl !== '' ? $legacyPrivacyUrl : 'https://MISSING-PRIVACY-URL.TLD';
        $providerModel = null;

        try {
            $providerModel = $this->providerRepository->insert($newModel);
        } catch (GenericException $e) {
            $this->log->error('[Import] Failed to insert Provider.', ['exception' => $e]);
        }

        $this->log->info(
            '[Import] Provider "{{ providerName }}" ({{ languageCode }}) imported: {{ status }}',
            [
                'languageCode' => $languageCode,
                'providerName' => $newModel->name,
                'status' => $providerModel ? 'Yes' : 'No',
            ],
        );

        return $providerModel;
    }

    private function generateProviderKey(string $provider): string
    {
        $providerKey = $this->guessName($provider);
        $providerKey = preg_replace('/[^a-z\-_]/i', '', $providerKey);

        // Prefix `ic-` = import custom
        return 'ic-' . Formatter::toKebabCase($providerKey);
    }

    private function guessAddress(string $provider): ?string
    {
        // Expected format: Name, Address, City, Country
        $providerNameParts = explode(',', $provider);

        if (count($providerNameParts) <= 2) {
            return null;
        }

        // At least one address part must contain a number
        $foundDigits = false;

        foreach ($providerNameParts as &$part) {
            $part = trim($part);

            if (preg_match('/\d/', $part)) {
                $foundDigits = true;
            }
        }

        if (!$foundDigits) {
            return null;
        }

        array_splice($providerNameParts, 0, 1);

        return implode(', ', $providerNameParts);
    }

    private function guessName(string $provider): string
    {
        $providerNameParts = explode(',', $provider);

        return $providerNameParts[0] ?? $provider;
    }
}
