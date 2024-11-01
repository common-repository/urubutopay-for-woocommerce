<?php
if (!defined('ABSPATH')) {
    exit;
}

class UPGFC_Shortcode
{
    public function __construct()
    {
        add_action('init', array($this, 'upgfc_register_shortcode'));
    }

    public function upgfc_register_shortcode()
    {
        add_shortcode('upgfc-payment-completion', array($this, 'upgfc_complete_payment'));
    }

    public function upgfc_complete_payment($attr)
    {
        $order_id = $attr['order_id'];
        $order = wc_get_order($order_id);

        if (!$order) {
            return upgfc_not_found_func('Order not found');
        }

        $post_meta = get_post_meta($order_id, UPGFC_META['PAYMENT_DETAILS'], true);

        if (!$post_meta) {
            return upgfc_not_found_func('Order not found');
        }

        $transaction_id = json_decode($post_meta)->transaction_id;
        $check_transaction_args = array('transaction_id' => $transaction_id);
        $response = UPGFC_PaymentService::check_transaction($check_transaction_args);

        if ($response['code'] !== UPGFC_HTTP_CODE['OK'] && $response['code'] !== UPGFC_HTTP_CODE['CREATED']) {
            $errorMessage = $response['data']->message ? $response['data']->message : 'Unable to verify transaction';
            return upgfc_not_found_func($errorMessage);
        }

        $data = $response['data']->data;
        $order_status = UPGFC_PaymentHelper::upgfc_manage_order_status($data->transaction_status);
        $order->update_status($order_status['status'], $order_status['message']);

        return wp_kses_post(upgfc_display_check_transaction($transaction_id, $order_status['status']));
    }
}
