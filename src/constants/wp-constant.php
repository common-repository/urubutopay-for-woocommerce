<?php
if (!defined('ABSPATH')) {
    exit;
}
// prefix UPGFC_TRANSACTION_STATUS
define('UPGFC_TRANSACTION_STATUS', array(
    'PENDING' => 'PENDING',
    'VALID' => 'VALID',
    'FAILED' => 'FAILED',
    'PENDING_SETTLEMENT' => 'PENDING_SETTLEMENT',
    'INITIATED' => 'INITIATED',
    'CANCELED' => 'CANCELED'
));

define('UPGFC_ORDER_STATUS', array(
    'ON_HOLD' => 'on-hold',
    'PENDING' => 'pending',
    'FAILED' => 'failed',
    'COMPLETED' => 'completed',
    'CANCELED' => 'cancelled'
));

define('UPGFC_PAYMENT_INITIATOR_TYPE', 'ECOMMERCE_REQUEST');

define('UPGFC_JWT_ALGORITHM', 'HS256');

define('UPGFC_CHANNEL_NAME', array(
    'MOMO' => 'MOMO',
    'AIRTEL_MONEY' => 'AIRTEL_MONEY',
    'CARD' => 'CARD'
));

define(
    'UPGFC_ENDPOINT',
    array(
        'INITIATE_PAYMENT' => '/api/v2/payment/initiate',
        'CHECK_TRANSACTION' => '/api/payment/transaction/status',
        'VALIDATION' => '/api/payment/validate'
    )
);

define('UPGFC_OPTION_FIELDS', array(
    'MERCHANT_CODE' => 'merchant_code',
    'SERVICE_CODE' => 'service_code',
    'API_KEY' => 'api_key',
    'API_BASE_URL' => 'api_base_url',
    'USERNAME' => 'username',
    'PASSWORD' => 'password',
    'SECRET_KEY' => 'secret_key',
    'DESCRIPTION' =>  'DESCRIPTION',
    'TITE' => 'TITLE',
    'BUY_BUTTON' => 'buy_button'
));

define('UPGFC_POST_TYPE', array('PAGE' => 'page'));

define('UPGFC_PAGE_SLUG', array('PAYMENT_COMPLETION' => 'upgfc-complete-payment'));

define('UPGFC_PAGE_TITLE', array('PAYMENT_COMPLETION' => 'Complete Payment'));

define('UPGFC_META', array('PAYMENT_DETAILS' => 'upgfc_payment_details'));

define('UPGFC_SETTING_ID', 'upgfc_setting');

define('UPGFC_RESPONSE_STATUS', array(
    'YES' => 'YES',
    'NO' => 'NO'
));
