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

namespace Borlabs\Cookie\System\CrossDomainCookie;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\PluginCookie\PluginCookieDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\ConsentLog\ServiceGroupConsentDtoList;
use Borlabs\Cookie\Support\Randomizer;
use Borlabs\Cookie\Support\Validator;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Consent\ConsentLogService;
use Borlabs\Cookie\System\ConsentStatistic\ConsentStatisticService;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Option\Option;

final class CrossDomainCookieService
{
    private ConsentLogService $consentLogService;

    private ConsentStatisticService $consentStatisticService;

    private GeneralConfig $generalConfig;

    private Log $log;

    private Option $option;

    private WpFunction $wpFunction;

    public function __construct(
        ConsentLogService $consentLogService,
        ConsentStatisticService $consentStatisticService,
        GeneralConfig $generalConfig,
        Log $log,
        Option $option,
        WpFunction $wpFunction
    ) {
        $this->consentLogService = $consentLogService;
        $this->consentStatisticService = $consentStatisticService;
        $this->generalConfig = $generalConfig;
        $this->log = $log;
        $this->option = $option;
        $this->wpFunction = $wpFunction;
    }

    public function init(RequestDto $request)
    {
        // This should hopefully stop Google Search Console adding the url to its reports
        if (isset($request->getData['__borlabsCookieClientTime']) && (int) $request->getData['__borlabsCookieClientTime'] < (time() - 86400)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 410 Gone', true);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true);
        }

        header('X-Robots-Tag: noindex, nofollow, norarchive', true);

        $this->log->debug('[1/6] CrossDomainCookieService before referrer validation', [
            'getData' => $request->getData,
            'referrer' => $request->serverData['HTTP_REFERER'] ?? '',
        ]);

        if (!$this->isReferrerValid($request->serverData['HTTP_REFERER'] ?? '')) {
            return;
        }

        $languageCode = $this->wpFunction->applyFilter(
            'borlabsCookie/crossDomainCookie/modifyLanguageCode',
            $request->getData['__borlabsCookieLanguage'] ?? '',
            (array) $request,
        );

        $this->log->debug('[2/6] CrossDomainCookieService before JSON validation', [
            'getData' => $request->getData,
            'referrer' => $request->serverData['HTTP_REFERER'] ?? '',
        ]);

        if (!Validator::isStringJSON($request->getData['__borlabsCookieCookieData'] ?? '')) {
            return;
        }

        $cookieData = json_decode($request->getData['__borlabsCookieCookieData'], true);

        $this->log->debug('[3/6] CrossDomainCookieService after JSON decoding and before modifyConsents filter', [
            'cookieData' => $cookieData,
        ]);

        /**
         * @var array $consents
         *
         * Example:
         * <code>
         *  [
         *      "essential": ["borlabs-cookie"],
         *  ]
         * </code>
         */
        $consents = $this->wpFunction->applyFilter(
            'borlabsCookie/crossDomainCookie/modifyConsents',
            $cookieData['consents'] ?? [],
        );

        /**
         * @var array $consents
         *
         * Example
         * <code>
         * [
         *     (object) [
         *         'id' => serviceGroupKey,
         *         'services' => [serviceKey, serviceKey, ...],
         *     ],
         *     (object) [...],
         * ]
         * </code>
         */
        $consents = array_map(
            fn ($services, $serviceGroupKey) => (object) [
                'id' => $serviceGroupKey,
                'services' => $services,
            ],
            $consents,
            array_keys($consents),
        );

        $consents = json_encode($consents);

        $this->log->debug('[4/6] CrossDomainCookieService before JSON validation and after modifyConsents filter', [
            'consents' => $consents,
            'getData' => $request->getData,
            'referrer' => $request->serverData['HTTP_REFERER'] ?? '',
        ]);

        if (!Validator::isStringJSON($consents ?? '')) {
            return;
        }

        $cookieVersion = (int) $this->option->getGlobal('CookieVersion', 1)->value;
        $validatedConsents = $this->consentLogService->getValidatedServiceGroupConsentList($languageCode, $consents);
        $tcString = $request->getData['__borlabsCookieTCString'] ?? '';
        $uid = $this->generateUid($validatedConsents, $tcString);

        $this->log->debug('[5/6] CrossDomainCookieService validated consents', [
            'uid' => $uid,
            'validatedConsents' => $validatedConsents,
        ]);

        // Add consent log
        $this->consentLogService->add(
            $languageCode,
            $uid,
            $cookieVersion,
            $consents,
            $tcString,
        );

        // Add consent statistic
        $this->consentStatisticService->add(
            $validatedConsents,
            $uid,
            $cookieVersion,
        );

        $pluginCookie = $this->getPluginCookie(
            $validatedConsents,
            $uid,
            $cookieVersion,
            $cookieData['expires'] ?? '',
        );

        $this->setCookie($pluginCookie, $tcString);
        $this->output($pluginCookie, $languageCode, $tcString);
    }

    private function generateUid(ServiceGroupConsentDtoList $consents, string $tcString = ''): string
    {
        // Only consent for service group 'essential', UID is always 'anonymous' in this case
        if (count($consents->list) === 1 && $tcString === '') {
            return 'anonymous';
        }

        return Randomizer::randomString(8)
            . '-' . Randomizer::randomString(8)
            . '-' . Randomizer::randomString(8)
            . '-' . Randomizer::randomString(8);
    }

    private function getPluginCookie(
        ServiceGroupConsentDtoList $consents,
        string $uid,
        int $cookieVersion,
        string $expires
    ): PluginCookieDto {
        $pluginCookie = new PluginCookieDto();
        $pluginCookie->consents = (object) array_column($consents->list, 'services', 'key');
        $pluginCookie->expires = $expires;
        $pluginCookie->uid = $uid;
        $pluginCookie->v3 = true;
        $pluginCookie->version = $cookieVersion;

        $homeUrl = $this->wpFunction->getHomeUrl();
        $homeUrlInfo = parse_url($homeUrl);
        $cookiePath = !empty($homeUrlInfo['path']) ? $homeUrlInfo['path'] : '/';

        if (!$this->generalConfig->get()->automaticCookieDomainAndPath) {
            $cookiePath = $this->generalConfig->get()->cookiePath;
        }

        $pluginCookie->domainPath = $this->generalConfig->get()->cookieDomain . $cookiePath;

        $this->log->debug('[6/6] CrossDomainCookieService before setting cookie via header() and JavaScript', [
            'pluginCookie' => $pluginCookie,
        ]);

        return $pluginCookie;
    }

    private function isReferrerValid(string $referrer): bool
    {
        $domains = $this->generalConfig->get()->crossCookieDomains;
        $referrerUrlInfo = parse_url($referrer);

        foreach ($domains as $domain) {
            $referrerUrl = sprintf(
                '%s://%s%s%s',
                $referrerUrlInfo['scheme'],
                $referrerUrlInfo['host'],
                isset($referrerUrlInfo['port']) ? ':' . $referrerUrlInfo['port'] : '',
                $referrerUrlInfo['path'] ?? '/',
            );

            if (strpos($referrerUrl, $domain) !== false) {
                return true;
            }
        }

        return false;
    }

    private function output(PluginCookieDto $pluginCookie, string $languageCode, string $tcString = ''): void
    {
        $cookieData = [];
        $tcfCookieData = [];
        $cookieData['value'] = 'borlabs-cookie=' . rawurlencode(json_encode($pluginCookie));

        if ($tcString !== '') {
            $tcfCookieData[] = 'TCF_COOKIE=' . $tcString;
        }

        $homeUrl = $this->wpFunction->getHomeUrl();
        $homeUrlInfo = parse_url($homeUrl);
        $cookiePath = !empty($homeUrlInfo['path']) ? $homeUrlInfo['path'] : '/';

        // Cookie Domain
        if ($this->generalConfig->get()->cookieDomain !== '' && !$this->generalConfig->get()->automaticCookieDomainAndPath) {
            $cookieData['domain'] = 'domain=' . $this->generalConfig->get()->cookieDomain;
            $tcfCookieData[] = $cookieData['domain'];
        }

        // Cookie Path
        if (!$this->generalConfig->get()->automaticCookieDomainAndPath && $this->generalConfig->get()->cookiePath !== '') {
            $cookiePath = $this->generalConfig->get()->cookiePath;
        }

        $cookieData['path'] = 'path=' . $cookiePath;
        $tcfCookieData[] = $cookieData['path'];

        // Expiration Date
        $cookieData['expires'] = 'expires=' . $pluginCookie->expires;
        $tcfCookieData[] = $cookieData['expires'];

        // Set cookie
        $javaScript = '<script>document.cookie = "' . implode(';', $cookieData) . '";</script>';

        if ($tcString !== '') {
            $javaScript .= '<script>document.cookie = "' . implode(';', $tcfCookieData) . '";</script>';
        }

        echo '<html lang="' . $languageCode . '"><head><title></title><meta name="robots" content="noindex,nofollow,norarchive"></head><body>'
            . $javaScript . '</body></html>';

        exit;
    }

    private function setCookie(PluginCookieDto $pluginCookie, string $tcString = ''): void
    {
        // Cross-Cookie workaround due SameSite Policy - Does not work in incognito mode because browsers block third-party cookies in that mode by default
        header(
            'Set-Cookie: borlabs-cookie=' . rawurlencode(json_encode($pluginCookie)) . ';'
            . ($tcString !== '' ? 'TCF_COOKIE=' . $tcString . ';' : '')
            . ' SameSite=None; Secure',
        );
    }
}
