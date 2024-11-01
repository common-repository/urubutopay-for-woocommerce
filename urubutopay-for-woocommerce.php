<?php

/**
 * @package UrubutoPayForWooCommerce
 * @author BKTechouse
 * @copyright: 2023
 */

/**
 * Plugin Name: UrubutoPay For WooCommerce
 * Plugin URI: https://urubutopay.rw
 * Author: BKTechouse
 * Author URI: https://bktechouse.rw
 * Description: Accept online payments from your client's mobile wallets, credit card and debit card
 * Version: 1.0
 * License: GPLv2
 * Text Domain: urubutopay-for-woocommerce
 * Requires at least: PHP 7
 */

/*
UrubutoPay is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

UrubutoPay is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with UrubutoPay. If not, see https://urubutopay.rw.
*/
if (!defined('ABSPATH')) {
    exit;
}

// check if woocommerce plugin is installed
$plugin_path = trailingslashit(WP_PLUGIN_DIR) . 'woocommerce/woocommerce.php';
if (
    in_array($plugin_path, wp_get_active_and_valid_plugins()) ||
    in_array($plugin_path, wp_get_active_network_plugins())
) {
    require_once plugin_dir_path(__FILE__) . './vendor/autoload.php';
    /**
     * Require Files From /src/constants/*
     */
    require_once plugin_dir_path(__FILE__) . './src/constants/wp-constant.php';
    require_once plugin_dir_path(__FILE__) . './src/constants/http-code.php';

    /**
     * Require Files From /src/includes/*
     */
    require_once plugin_dir_path(__FILE__) . './src/includes/wc-urubutopay-payment-service.php';
    require_once plugin_dir_path(__FILE__) . './src/includes/wc-urubutopay-setting.php';
    require_once plugin_dir_path(__FILE__) . './src/includes/wc-urubutopay-scripts.php';
    require_once plugin_dir_path(__FILE__) . './src/includes/wc-urubutopay-payment-completion.php';
    require_once plugin_dir_path(__FILE__) . './src/includes/wc-urubutopay-shortcode.php';
    require_once plugin_dir_path(__FILE__) . './src/includes/wc-urubutopay-payment-validation.php';
    require_once plugin_dir_path(__FILE__) . './src/includes/wc-urubutopay-payment-helper.php';
    require_once plugin_dir_path(__FILE__) . './src/includes/wc-urubutopay-payment-route.php';

    /**
     * Require Files From /src/templates/*
     */
    require_once plugin_dir_path(__FILE__) . './src/templates/wc-urubutopay-payment-fields.php';
    require_once plugin_dir_path(__FILE__) . './src/templates/wc-urubutopay-shortcode.php';
    require_once plugin_dir_path(__FILE__) . './src/templates/wc-urubutopay-not-found.php';

    add_action('plugins_loaded', 'upgfc_init_urubutopay_gateway');

    add_filter('woocommerce_payment_gateways', 'upgfc_add_urubutopay_gateway');

    register_deactivation_hook(__FILE__, 'upgfc_deactivate');

    register_activation_hook(__FILE__, 'upgfc_activate');

    function upgfc_init_urubutopay_gateway()
    {
        require_once plugin_dir_path(__FILE__) . './src/includes/wc-urubutopay-gateway.php';

        /**
         * Payment completion post type
         */
        new UPGFC_PaymentCompletion();

        /**
         * Shortcode
         */
        new UPGFC_Shortcode();

        /**
         * Load Script
         */
        new UPGFC_Script();

        /**
         * Payment Validation
         */
        new UPGFC_Validation();

        /**
         * Payment Rest API Class
         */
        new UPGFC_PaymentRoute();

        /**
         * Payment Helper Class
         */
        new UPGFC_PaymentHelper();

        /**
         * Setting Class
         */
        new UPGFC_Setting();

        /**
         * Payment Service Class
         */
        new UPGFC_PaymentService();
    }

    function upgfc_add_urubutopay_gateway($methods)
    {
        // WC_Gateway_UrubutoPay should be the name of my gateway class
        $methods[] = 'UPGFC_Gateway';
        return $methods;
    }

    function upgfc_deactivate()
    {
        // delete payment completion page
        UPGFC_PaymentCompletion::upgfc_remove_payment_completion_page();
        flush_rewrite_rules();
    }

    function upgfc_activate()
    {
        UPGFC_PaymentCompletion::upgfc_register_payment_completion_page();
        flush_rewrite_rules();
    }
} else {
    die('Woocommerce must be installed and activated');
}
