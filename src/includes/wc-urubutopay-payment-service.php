<?php
if (!defined('ABSPATH')) {
    exit;
}
class UPGFC_PaymentService
{
    public function __construct()
    {
        add_filter('http_request_timeout', array($this, 'upgfc_timeout_extend'));
    }

    public function upgfc_timeout_extend($time)
    {
        // Default timeout is 5s
        // extends timeout to 20min which is 1200seconds
        return 1200;
    }

    public static function upgfc_init_payment($args)
    {
        $setting_id = UPGFC_SETTING_ID;
        $option = get_option('woocommerce_' . $setting_id . '_settings');
        $uri = $option['api_base_url'] . UPGFC_ENDPOINT['INITIATE_PAYMENT'];
        $token = 'Bearer ' . $option['api_key'];
        $body = array(
            'merchant_code' => $option['merchant_code'],
            'service_code' => $option['service_code'],
            'channel_name' =>  $args['channel_name'],
            'payer_code' => strval($args['payer_code']),
            'phone_number' => $args['phone_number'],
            'payment_initiator_type' => UPGFC_PAYMENT_INITIATOR_TYPE,
            'transaction_id' => $args['transaction_id'],
            'amount' => $args['amount'],
            'payer_email' => $args['payer_email'],
            'payer_names' => $args['payer_names']
        );

        $payload = $args['channel_name'] === UPGFC_CHANNEL_NAME['CARD'] ? array_merge(
            $body,
            array('redirection_url' =>  $args['rdurl'])
        ) : $body;

        $response = wp_remote_post($uri, array(
            'body' => json_encode($payload),
            'headers' => array(
                'authorization' => $token,
                'Content-Type' => 'application/json'
            )
        ));

        return UPGFC_PaymentService::upgfc_format_response($response);
    }

    private static function upgfc_format_response($response)
    {
        $code = wp_remote_retrieve_response_code($response);

        $body = wp_remote_retrieve_body($response);

        return array('code' => $code, 'data' => json_decode($body));
    }

    public static function check_transaction($args)
    {
        $setting_id = UPGFC_SETTING_ID;
        $option = get_option('woocommerce_' . $setting_id . '_settings');
        $uri = $option['api_base_url'] . UPGFC_ENDPOINT['CHECK_TRANSACTION'];
        $token = 'Bearer ' . $option['api_key'];

        $transaction_id = $args['transaction_id'];
        $merchant_code = $option['merchant_code'];

        $response = wp_remote_post($uri, array(
            'body' => json_encode(array(
                'transaction_id' => $transaction_id,
                'merchant_code' => $merchant_code
            )),
            'headers' => array(
                'authorization' => $token,
                'Content-Type' => 'application/json'
            )
        ));

        return UPGFC_PaymentService::upgfc_format_response($response);
    }

    public static function upgfc_get_merchant_detail()
    {
        $setting_id = UPGFC_SETTING_ID;

        $option = get_option('woocommerce_' . $setting_id . '_settings');

        $merchant_code = $option['merchant_code'];

        $uri = $option['api_base_url'] . UPGFC_ENDPOINT['VALIDATION'];

        $body = array('merchant_code' => $merchant_code, 'payer_code' => 'N/A');

        $response = wp_remote_post($uri, array(
            'body' => json_encode($body),
            'headers' => array('Content-Type' => 'application/json')
        ));

        return UPGFC_PaymentService::upgfc_format_response($response);
    }
}
