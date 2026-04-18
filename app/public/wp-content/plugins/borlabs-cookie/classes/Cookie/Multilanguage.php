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

namespace BorlabsCookie\Cookie;

class Multilanguage
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

    public function getAvailableLanguagesForChooser()
    {
        if ($this->isLanguagePluginWeglotActive()) {
            $languages = [];
            array_push($languages, [
                'code' => weglot_get_original_language(),
                'name' => $this->getLanguageName(weglot_get_original_language()) . ' (' . weglot_get_original_language() . ')',
            ]);

            foreach (weglot_get_destination_languages() as $destination) {
                array_push($languages, [
                    'code' => $destination['custom_code'] ?? $destination['language_to'],
                    'name' => ($destination['custom_local_name'] ?? $this->getLanguageName($destination['language_to'])) . ' (' . ($destination['custom_code'] ?? $destination['language_to']) . ')',
                ]);
            }

            return $languages;
        }

        if (function_exists('Falang')) {
            $languages = [];

            foreach (Falang()->get_model()->get_languages_list() as $language) {
                array_push($languages, [
                    'code' => $language->locale,
                    'name' => $language->name . ' (' . $language->locale . ')',
                ]);
            }

            return $languages;
        }
    }

    /**
     * getCurrentLanguageCode function.
     */
    public function getCurrentLanguageCode()
    {
        $currentLanguage = null;

        if ($this->isMultilanguagePluginActive()) {
            // Polylang
            if (function_exists('pll_current_language')) {
                $currentLanguage = pll_current_language();

                // If currentLanguage is still empty, we have to get the default language
                if (empty($currentLanguage)) {
                    $currentLanguage = pll_default_language();

                    // Fallback: Add action to reload Config later. Necessary when the content defines the language
                    if (is_admin() === false) {
                        add_action('pll_language_defined', [$this, 'polylangLanguageDefined']);
                    }
                }
            } elseif (function_exists('Falang')) {
                $currentLanguage = Falang()->get_current_language()->locale;

                if (is_admin()) {
                    $userLanguage = get_option('BorlabsCookieLegacyUserLanguage_' . get_current_user_id());

                    if (isset($_GET['borlabsLang'])) {
                        if (Falang()->get_model()->get_language_by_locale($_GET['borlabsLang']) !== null) {
                            update_option('BorlabsCookieLegacyUserLanguage_' . get_current_user_id(), $_GET['borlabsLang']);
                            $currentLanguage = $_GET['borlabsLang'];
                        }
                    } elseif ($userLanguage) {
                        $currentLanguage = $userLanguage;
                    }
                }
            } elseif ($this->isLanguagePluginWeglotActive()) {
                // Weglot
                if (is_admin()) {
                    $userLanguage = get_option('BorlabsCookieLegacyUserLanguage_' . get_current_user_id());

                    if (isset($_GET['borlabsLang'])) {
                        if ($this->getWeglotLanguageName($_GET['borlabsLang']) !== null) {
                            update_option('BorlabsCookieLegacyUserLanguage_' . get_current_user_id(), $_GET['borlabsLang']);
                            $currentLanguage = $_GET['borlabsLang'];
                        }
                    } elseif ($userLanguage) {
                        $currentLanguage = $userLanguage;
                    } else {
                        $currentLanguage = weglot_get_original_language();
                    }
                } else {
                    $currentLanguage = $this->getWeglotCurrentLanguageCode();
                }
            } else {
                // WPML
                $null = null;
                $currentLanguage = apply_filters('wpml_current_language', $null);
            }

            // Fallback
            if ($currentLanguage === 'all') {
                $currentLanguage = $this->getDefaultLanguageCode();
            }
        } else {
            $currentLanguage = BORLABS_COOKIE_DEFAULT_LANGUAGE;
        }

        return $currentLanguage;
    }

    /**
     * getCurrentLanguageFlag function.
     */
    public function getCurrentLanguageFlag()
    {
        $currentLanguageFlag = '';

        // Get the flag, works with WPML & Polylang
        if ($this->isMultilanguagePluginActive()) {
            $currentLanguageCode = $this->getCurrentLanguageCode();

            $currentLanguageFlag = $this->getLanguageFlag($currentLanguageCode);
        }

        return $currentLanguageFlag;
    }

    /**
     * getCurrentLanguageName function.
     * Only returns the name when WPML/Polylang is active and loaded!
     */
    public function getCurrentLanguageName()
    {
        $currentLanguageName = '';

        // Polylang
        if (function_exists('pll_current_language')) {
            $currentLanguageName = pll_current_language('name');

            // If currentLanguage is still empty, we have to get the default language
            if (empty($currentLanguageName)) {
                $currentLanguageName = pll_default_language('name');
            }
        } elseif (function_exists('Falang')) {
            $currentLanguageName = Falang()->get_current_language()->name;
        } elseif ($this->isLanguagePluginWeglotActive()) {
            // Weglot
            $languageCode = $this->getWeglotCurrentLanguageCode();

            if ($this->getWeglotLanguageName($languageCode) !== null) {
                $currentLanguageName = $this->getWeglotLanguageName($languageCode);
            }
        } elseif (defined('ICL_LANGUAGE_NAME')) {
            // WPML
            $currentLanguageName = ICL_LANGUAGE_NAME;
        } else {
            $currentLanguageName = '-';
        }

        return $currentLanguageName;
    }

    /**
     * getDefaultLanguageCode function.
     */
    public function getDefaultLanguageCode()
    {
        $defaultLanguage = null;

        if ($this->isMultilanguagePluginActive()) {
            // Polylang
            if (function_exists('pll_default_language')) {
                $defaultLanguage = pll_default_language();
            } elseif (function_exists('Falang')) {
                $defaultLanguage = Falang()->get_default_language()->locale;
            } elseif ($this->isLanguagePluginWeglotActive()) {
                // Weglot
                $defaultLanguage = weglot_get_original_language();
            } else {
                // WPML
                $null = null;
                $defaultLanguage = apply_filters('wpml_default_language', $null);
            }
        } else {
            $defaultLanguage = BORLABS_COOKIE_DEFAULT_LANGUAGE;
        }

        return $defaultLanguage;
    }

    /**
     * getLanguageFlag function.
     *
     * @param mixed $languageCode
     *
     * @return string
     */
    public function getLanguageFlag($languageCode)
    {
        $languageFlag = '';

        // Get the flag, works with WPML & Polylang
        if ($this->isMultilanguagePluginActive()) {
            if (!$this->isLanguagePluginWeglotActive()) {
                if (function_exists('Falang')) {
                    return plugins_url('flags/' . Falang()->get_current_language()->flag_code . '.png', FALANG_FILE);
                }

                $null = null;
                $listOfActiveLanguages = apply_filters('wpml_active_languages', $null);

                if (!empty($listOfActiveLanguages[$languageCode]['country_flag_url'])) {
                    $languageFlag = $listOfActiveLanguages[$languageCode]['country_flag_url'];
                }
            }
        }

        return $languageFlag;
    }

    /**
     * getLanguageName function.
     *
     * @param mixed $languageCode
     */
    public function getLanguageName($languageCode)
    {
        $languageName = '';

        // WPML & Polylang
        if ($this->isMultilanguagePluginActive()) {
            if ($this->isLanguagePluginWeglotActive()) {
                // Weglot
                if ($this->getWeglotLanguageName($languageCode) !== null) {
                    $languageName = $this->getWeglotLanguageName($languageCode);
                }
            } elseif (function_exists('Falang')) {
                $languageName = Falang()->get_model()->get_language_by_locale($languageCode)->name;
            } else {
                $null = null;
                $languages = apply_filters('wpml_active_languages', $null, []);

                if (!empty($languages[$languageCode])) {
                    $languageName = $languages[$languageCode]['native_name'];
                }
            }
        }

        return $languageName;
    }

    public function getWeglotCurrentLanguageCode()
    {
        $currentLanguageCode = weglot_get_current_language();

        // Check if default language code is available
        $languageEntry = weglot_get_languages_available()[$currentLanguageCode];

        if (isset($languageEntry) && is_object($languageEntry)) {
            return $languageEntry->getExternalCode();
        }

        return $currentLanguageCode;
    }

    public function getWeglotLanguageName($languageCode)
    {
        if (strlen($languageCode) === 2) {
            return weglot_get_languages_available()[$languageCode]->getLocalName();
        }

        // In case of custom language
        $languages = weglot_get_destination_languages();

        foreach ($languages as $language) {
            if (isset($language['custom_code']) && $language['custom_code'] === $languageCode) {
                return $language['custom_local_name'];
            }
        }
    }

    public function isLanguagePluginWeglotActive()
    {
        return function_exists('weglot_get_current_language');
    }

    /**
     * isMultilanguagePluginActive function.
     *
     * @return bool
     */
    public function isMultilanguagePluginActive()
    {
        $status = false;

        if (
            defined('ICL_LANGUAGE_CODE') || defined('POLYLANG_FILE')
            || $this->isLanguagePluginWeglotActive() || function_exists('Falang')
        ) {
            $status = true;
        }

        return $status;
    }

    public function needsLanguageChooser()
    {
        return $this->isLanguagePluginWeglotActive() || function_exists('Falang');
    }

    /**
     * polylangLanguageDefined function.
     *
     * @param mixed $languageCode
     */
    public function polylangLanguageDefined($languageCode)
    {
        // Load config with new language code
        Config::getInstance()->loadConfig($languageCode);

        // Load Content Blocker settings with new language code
        Frontend\ContentBlocker::getInstance()->init();
    }
}
