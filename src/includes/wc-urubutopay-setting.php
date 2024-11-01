<?php
if (!defined('ABSPATH')) {
    exit;
}

class UPGFC_Setting
{
    public static function upgfc_form_fields()
    {
        return array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable UrubutoPay Payment', 'woocommerce'),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __('Title', 'woocommerce'),
                'type' => 'text',
                'description' => __('Title will be displayed on the checkout page', 'woocommerce')
            ),
            'description' => array(
                'title' => __('Description', 'woocommerce'),
                'type' => 'textarea',
                'default' => 'Pay via UrubutoPay',
                'description' => __('This controls the description which the user sees during checkout.', 'woocommerce')
            ),
            'buy_button' => array(
                'title' => __('Pay Button', 'woocommerce'),
                'type' => 'text',
                'default' => 'Place Order',
                'description' => __('This allows to change buy button name', 'woocommerce')
            ),
            'api_base_url' => array(
                'title' => __('Base URL', 'woocommerce'),
                'type' => 'text',
                'description' => __('Enter Base URL Provided by UrubutoPay'),
            ),
            'api_key' => array(
                'title' => __('API Key', 'woocommerce'),
                'type' => 'text',
                'description' => __('Enter API Key Provided by UrubutoPay'),
                'required' => true
            ),
            'merchant_code' => array(
                'title' => __('Merchant Code', 'woocommerce'),
                'type' => 'text',
                'description' => __('Provide merchant code you have been given by UrubutoPay', 'woocommerce'),
            ),
            'service_code' => array(
                'title' => __('Service Code', 'woocommerce'),
                'type' => 'text',
                'description' => __('Enter service code', 'woocommerce'),
            ),
            'username' => array(
                'title' => __('Username', 'woocommerce'),
                'type' => 'text',
                'description' => __('Provide Username', 'woocommerce'),
            ),
            'password' => array(
                'title' => __('Password', 'woocommerce'),
                'type' => 'password',
                'description' => __('Provide Password', 'woocommerce'),
            ),
            'secret_key' => array(
                'title' => __('Secret Key', 'woocommerce'),
                'type' => 'password',
                'description' => __('Provide Secret Key', 'woocommerce'),
            ),
        );
    }
}
