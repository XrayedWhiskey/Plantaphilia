<?php
/*
 * ----------------------------------------------------------------------
 *
 *                          Borlabs Cookie
 *                    developed by Borlabs GmbH
 *
 * ----------------------------------------------------------------------
 *
 * Copyright 2018-2022 Borlabs GmbH. All rights reserved.
 * This file may not be redistributed in whole or significant part.
 * Content of this file is protected by international copyright laws.
 *
 * ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 * @copyright Borlabs GmbH, https://borlabs.io
 * @author Benjamin A. Bornschein
 *
 */

namespace BorlabsCookie\Cookie\Frontend;

use BorlabsCookie\Cookie\Config;

class ConsentLog
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
    }

    public function __clone()
    {
        trigger_error('Cloning is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserialize is forbidden.', E_USER_ERROR);
    }

    /**
     * add function.
     *
     * @param mixed $cookieData
     * @param mixed $language
     * @param mixed $essentialStatistic
     */
    public function add($cookieData, $language, $essentialStatistic = false)
    {
        global $wpdb;

        $consents = [];
        $table = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)
            . 'borlabs_cookie_legacy_consent_log';

        // Validate cookie data
        if (!empty($cookieData['uid'])) {
            // Validate uid
            if (preg_match('/[0-9a-z]{8}\-[0-9a-z]{8}\-[0-9a-z]{8}\-[0-9a-z]{8}/', $cookieData['uid'])) {
                // Sanitize language
                $language = strtolower(preg_replace('/[^a-z\-_]+/', '', $language));

                // Get all valid cookie group ids
                $allowedCookieGroups = Cookies::getInstance()->getAllCookieGroupsOfLanguage($language);
                $allowedCookies = Cookies::getInstance()->getAllCookiesOfLanguage($language);

                // Validate consents
                if (!empty($cookieData['consents'])) {
                    foreach ($cookieData['consents'] as $cookieGroup => $cookies) {
                        if (!empty($allowedCookieGroups[$cookieGroup])) {
                            $consents[$cookieGroup] = [];

                            if (!empty($cookies)) {
                                foreach ($cookies as $cookie) {
                                    if (!empty($allowedCookies[$cookie])) {
                                        $consents[$cookieGroup][] = $cookie;
                                    }
                                }
                            }
                        }
                    }
                }

                // Get last log
                $lastLog = $wpdb->get_results(
                    '
                    SELECT
                        `cookie_version`,
                        `consents`
                    FROM
                        `' . $table . "`
                    WHERE
                        `uid` = '" . esc_sql($cookieData['uid']) . "'
                        AND
                        `is_latest` = 1
                "
                );

                if (!empty($cookieData['version'])) {
                    $cookieVersion = (int) ($cookieData['version']);
                } else {
                    $cookieVersion = (int) (get_site_option('BorlabsCookieCookieVersion', 1));
                }

                $newConsents = serialize($consents);

                if (
                    empty($lastLog[0]->consents)
                    || ($lastLog[0]->consents !== $newConsents
                        && $lastLog[0]->cookie_version !== $cookieVersion)
                ) {
                    $countEssential = true;

                    if (!empty($lastLog[0]->consents)) {
                        // Set "is_latest" of all old entries of the uid to 0
                        $wpdb->query(
                            '
                            UPDATE
                                `' . $table . "`
                            SET
                                `is_latest` = 0
                            WHERE
                                `uid` = '" . esc_sql($cookieData['uid']) . "'
                        "
                        );
                        $countEssential = false;
                    }

                    // Insert log
                    $wpdb->query(
                        '
                        INSERT INTO
                            `' . $table . "`
                        (
                            `log_id`,
                            `uid`,
                            `cookie_version`,
                            `consents`,
                            `is_latest`,
                            `stamp`
                        )
                        VALUES
                        (
                            null,
                            '" . esc_sql($cookieData['uid']) . "',
                            '" . $cookieVersion . "',
                            '" . esc_sql($newConsents) . "',
                            '1',
                            NOW()
                        )
                    "
                    );

                    $tableStatistics = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)
                        . 'borlabs_cookie_legacy_statistics';
                    $stats = [];
                    $lastConsents = !empty($lastLog[0]->consents) ? unserialize($lastLog[0]->consents) : [];

                    if ($countEssential && $essentialStatistic === true) {
                        $stats[] = "('essential', NOW())";
                    }

                    foreach ($consents as $cookieGroup => $cookie) {
                        if ($cookieGroup !== 'essential' && !isset($lastConsents[$cookieGroup])) {
                            $stats[] = "('" . esc_sql($cookieGroup) . "', NOW())";
                        }
                    }

                    if (!empty($stats)) {
                        $wpdb->query('
                            INSERT INTO
                                `' . $tableStatistics . '`
                            (
                                `service_group`,
                                `stamp`
                            )
                            VALUES
                            ' . implode(',', $stats) . '
                        ');
                    }
                }
            } elseif ($cookieData['uid'] === 'anonymous') {
                if ($essentialStatistic === true) {
                    $tableStatistics = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)
                        . 'borlabs_cookie_legacy_statistics';
                    $wpdb->query('
                        INSERT INTO
                            `' . $tableStatistics . "`
                        (
                            `service_group`,
                            `stamp`
                        )
                        VALUES
                        (
                            'essential',
                            NOW()
                        )
                    ");
                }
            }
        }

        return true;
    }

    /**
     * getHistory function.
     *
     * @param mixed $uid
     * @param mixed $language
     */
    public function getConsentHistory($uid, $language)
    {
        global $wpdb;

        $consentHistory = [];

        $uid = trim(strtolower($uid));

        $table = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)
            . 'borlabs_cookie_legacy_consent_log';

        if (preg_match('/[0-9a-z]{8}\-[0-9a-z]{8}\-[0-9a-z]{8}\-[0-9a-z]{8}/', $uid)) {
            // Sanitize language
            $language = strtoupper(preg_replace('/[^a-z\-_]+/', '', $language));

            $availableCookieGroups = Cookies::getInstance()->getAllCookieGroupsOfLanguage($language);
            $availableCookies = Cookies::getInstance()->getAllCookiesOfLanguage($language);

            $logs = $wpdb->get_results(
                '
                SELECT
                    `log_id`,
                    `cookie_version`,
                    `consents`,
                    `stamp`
                FROM
                    `' . $table . "`
                WHERE
                    `uid` = '" . esc_sql($uid) . "'
                ORDER BY
                    `stamp` DESC
            "
            );

            foreach ($logs as $logItem) {
                $consentsTranslated = [];
                $finalConsentList = [];

                $consents = unserialize($logItem->consents);

                if (!empty($consents)) {
                    foreach ($consents as $cookieGroup => $cookies) {
                        if (!empty($availableCookieGroups[$cookieGroup])) {
                            $consentsTranslated[$cookieGroup]['cookieGroup'] = $availableCookieGroups[$cookieGroup];

                            if (!empty($cookies)) {
                                foreach ($cookies as $cookie) {
                                    if (!empty($availableCookies[$cookie])) {
                                        $consentsTranslated[$cookieGroup]['cookies'][] = $availableCookies[$cookie];
                                    }
                                }
                            }
                        }
                    }

                    foreach ($consentsTranslated as $data) {
                        $finalConsentList[] = $data['cookieGroup'] . (!empty($data['cookies']) ? ': ' . implode(
                            ', ',
                            $data['cookies']
                        ) : '');
                    }
                }

                $consentHistory[] = [
                    'version' => $logItem->cookie_version,
                    'consent' => implode('<br>', $finalConsentList),
                    'stamp' => $logItem->stamp,
                ];
            }
        }

        return $consentHistory;
    }
}
