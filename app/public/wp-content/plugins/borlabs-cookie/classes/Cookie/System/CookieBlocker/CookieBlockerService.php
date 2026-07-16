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

namespace Borlabs\Cookie\System\CookieBlocker;

use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Language\Language;

final class CookieBlockerService
{
    private Language $language;

    private ServiceRepository $serviceRepository;

    public function __construct(
        Language $language,
        ServiceRepository $serviceRepository
    ) {
        $this->language = $language;
        $this->serviceRepository = $serviceRepository;
    }

    public function init()
    {
        $servicesWithCookies = $this->serviceRepository->getAllOfLanguage(
            $this->language->getCurrentLanguageCode(),
            ['serviceCookies'],
            true,
        );
        $cookiesToBlock = [];

        /**
         * @var \Borlabs\Cookie\Model\Service\ServiceModel $serviceModel
         */
        foreach ($servicesWithCookies as $serviceModel) {
            if (isset($serviceModel->settingsFields->list)) {
                $shouldBlockCookiesBeforeConsent = Searcher::findObject($serviceModel->settingsFields->list, 'key', 'block-cookies-before-consent');

                if ($shouldBlockCookiesBeforeConsent !== null && (bool) $shouldBlockCookiesBeforeConsent->value === true) {
                    $cookiesToBlock = array_merge($cookiesToBlock, array_column($serviceModel->serviceCookies, 'name'));
                }
            }
        }

        foreach ($cookiesToBlock as $cookieName) {
            $cookieName = (string) $cookieName;

            if (strpos($cookieName, '*') !== false) {
                $this->deleteImpreciseCookie($cookieName);
            } else {
                $this->deletePreciseCookie($cookieName);
            }
        }
    }

    private function deleteImpreciseCookie(string $impreciseCookieName)
    {
        if (empty($_COOKIE)) {
            return;
        }

        $impreciseCookieName = str_replace('*', '', $impreciseCookieName);

        foreach ($_COOKIE as $cookieName => $cookieData) {
            $cookieName = (string) $cookieName;

            if (strpos($cookieName, $impreciseCookieName) === false) {
                continue;
            }

            unset($_COOKIE[$cookieName]);
            setcookie($cookieName, '', -1, '/');
        }
    }

    private function deletePreciseCookie(string $cookieName)
    {
        if (empty($_COOKIE[$cookieName])) {
            return;
        }

        unset($_COOKIE[$cookieName]);
        setcookie($cookieName, '', -1, '/');
    }
}
