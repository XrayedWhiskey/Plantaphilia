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

namespace BorlabsCookie\Cookie\Frontend\Services;

class WooCommerce
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * __construct function.
     */
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
     * getDefault function.
     */
    public function getDefault()
    {
        return [
            'cookieId' => 'woocommerce',
            'service' => 'WooCommerce',
            'name' => 'WooCommerce',
            'provider' => _x('Owner of this website', 'Frontend / Cookie / WooCommerce / Name', 'borlabs-cookie'),
            'purpose' => _x(
                'Helps WooCommerce determine when cart contents/data changes. Contains a unique code for each customer so that it knows where to find the cart data in the database for each customer. Allows customers to dismiss the store notifications.',
                'Frontend / Cookie / WooCommerce / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => '',
            'hosts' => [],
            'cookieName' => 'woocommerce_cart_hash, woocommerce_items_in_cart, wp_woocommerce_session_, woocommerce_recently_viewed, store_notice[notice id]',
            'cookieExpiry' => _x('Session / 2 Days', 'Frontend / Cookie / WooCommerce / Text', 'borlabs-cookie'),
            'optInJS' => '',
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => false,
            ],
            'status' => true,
            'undeletetable' => false,
        ];
    }
}
