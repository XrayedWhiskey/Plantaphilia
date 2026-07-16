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

namespace Borlabs\Cookie\System\License;

use Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException;
use Borlabs\Cookie\Localization\License\LicenseLocalizationStrings;
use Borlabs\Cookie\System\Message\MessageManager;

final class LicenseStatusMessage
{
    private License $license;

    private LicenseLocalizationStrings $localization;

    private MessageManager $messageManager;

    public function __construct(
        License $license,
        LicenseLocalizationStrings $localization,
        MessageManager $messageManager
    ) {
        $this->license = $license;
        $this->localization = $localization;
        $this->messageManager = $messageManager;
    }

    public function getLicenseMessageKeyExpired(): string
    {
        return $this->localization::get()['alert']['licenseExpired'];
    }

    public function getMessageEnterLicenseKey(): string
    {
        return $this->localization::get()['alert']['enterLicenseKey'];
    }

    public function handleMessageActivateLicenseKey(): void
    {
        if (!isset($this->license->get()->licenseKey)) {
            $this->messageManager->error($this->localization::get()['alert']['activateLicenseKey']);
        }
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException
     */
    public function handleMessageLicenseExpired(): void
    {
        if (
            isset($this->license->get()->licenseValidUntil)
            && $this->license->isLicenseValid() === false
        ) {
            // Try to re-validate
            try {
                $this->license->validateLicense();
                $this->license->get();

                if ($this->license->isLicenseValid() === false) {
                    $this->messageManager->error($this->localization::get()['alert']['licenseExpired']);
                }
            } catch (LicenseApiClientException $e) {
                $this->messageManager->error($e->getTranslatedMessage());
            }
        }
    }

    public function handleMessageLicenseNotValidForCurrentBuild(): void
    {
        if (!$this->license->isLicenseValidForCurrentBuild()) {
            $this->messageManager->error($this->localization::get()['alert']['licenseNotValidForCurrentBuild']);
        }
    }

    public function handleMessageValidLicenseRequired(): void
    {
        if (!$this->license->isLicenseValid()) {
            $this->messageManager->error($this->localization::get()['alert']['validLicenseRequired']);
        }
    }
}
