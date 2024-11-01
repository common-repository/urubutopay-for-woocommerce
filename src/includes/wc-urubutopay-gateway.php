<?php
if (!defined('ABSPATH')) {
    exit;
}

class UPGFC_Gateway extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = UPGFC_SETTING_ID;
        $this->method_title = 'UrubutoPay';
        $this->method_description = 'Pay using UrubutoPay';
        $this->icon = plugins_url('../../public/images/logo.svg', __FILE__);
        $this->init_form_fields();
        $this->init_settings();
        $this->title = $this->get_option('title');
        $this->has_fields = true;

        // add save hook for my settings
        add_action('woocommerce_update_options_payment_gateways_' .
            $this->id, array($this, 'process_admin_options'));

        // using init form fields to set form fields for my payment
        $this->form_fields = UPGFC_Setting::upgfc_form_fields();
        $this->order_button_text = $this->get_option(UPGFC_OPTION_FIELDS['BUY_BUTTON']);
    }

    public function payment_fields()
    {
        $response = UPGFC_PaymentService::upgfc_get_merchant_detail();

        if ($response['code'] !== UPGFC_HTTP_CODE['OK'] && $response['code'] !== UPGFC_HTTP_CODE['CREATED']) {
            return;
        }

        $data = $response['data']->data;

        $accept_card_payment = isset($data->accept_card_payment) ? $data->accept_card_payment : null;

        upgfc_display_payment_fields($accept_card_payment);
    }

    public function validate_fields()
    {
        $channel_name = UPGFC_Validation::upgfc_test_input(sanitize_text_field($_POST['upgfc_payment_mode']));
        $phone_number = UPGFC_Validation::upgfc_test_input(sanitize_text_field($_POST['upgfc_phone_number']));
        if (empty($channel_name)) {
            wc_add_notice('Choose payment mode', 'error');
            return false;
        }
        if (
            $channel_name !== UPGFC_CHANNEL_NAME['MOMO'] &&
            $channel_name !== UPGFC_CHANNEL_NAME['CARD'] &&
            $channel_name !== UPGFC_CHANNEL_NAME['AIRTEL_MONEY']
        ) {
            wc_add_notice('Invalid payment mode', 'error');
            return false;
        }

        if (empty($phone_number) && ($channel_name !== UPGFC_CHANNEL_NAME['CARD'])) {
            wc_add_notice('Phone Number is required', 'error');
            return false;
        }

        return true;
    }

    public function process_payment($order_id)
    {
        global $woocommerce;
        $phone_number = sanitize_text_field($_POST['upgfc_phone_number']);

        $channel_name = sanitize_text_field($_POST['upgfc_payment_mode']);

        $order = new WC_Order($order_id);

        $get_posts = new WP_Query(array(
            'name' => UPGFC_PAGE_SLUG['PAYMENT_COMPLETION'],
            'post_type' => UPGFC_POST_TYPE['PAGE']
        ));

        if (!$get_posts->have_posts()) {
            wc_add_notice('internal server error', 'error');
            return false;
        }

        $rdurl = $get_posts->posts[0]->guid . '/?order_id=' . $order_id;

        $payer_names = $order->get_formatted_billing_full_name() !== null && !empty($order->get_formatted_billing_full_name()) ? $order->get_formatted_billing_full_name() : null;

        $payer_email = $order->get_billing_email() !== null && !empty($order->get_billing_email()) ? $order->get_billing_email() : null;

        $args = array(
            'phone_number' => $phone_number,
            'channel_name' => $channel_name,
            'amount' => $order->get_total(),
            'payer_code' => $order_id,
            'rdurl' => $rdurl,
            'transaction_id' => UPGFC_PaymentHelper::upgfc_generate_transaction_id(),
            'payer_email' => $payer_email,
            'payer_names' => $payer_names
        );

        $init = UPGFC_PaymentService::upgfc_init_payment($args);

        if ($init['code'] !== UPGFC_HTTP_CODE['OK'] && $init['code'] !== UPGFC_HTTP_CODE['CREATED']) {
            $errorMessage = isset($init['data']) && isset($init['data']->message) ?
                $init['data']->message : 'unable to process payment';
            wc_add_notice($errorMessage, 'error');
            return false;
        }

        $status = UPGFC_PaymentHelper::upgfc_manage_order_status(UPGFC_ORDER_STATUS['PENDING']);

        $order->update_status($status['status'], $status['message']);

        add_post_meta($order_id, UPGFC_META['PAYMENT_DETAILS'], json_encode(array_merge(
            $args,
            array('transaction_status' => UPGFC_TRANSACTION_STATUS['PENDING'])
        )));

        // Remove cart
        $woocommerce->cart->empty_cart();

        if ($channel_name === UPGFC_CHANNEL_NAME['CARD']) {
            return array(
                'result' => 'success',
                'redirect' => $init['data']->card_processing_url
            );
        }
        return array(
            'result' => 'success',
            'redirect' => $rdurl
        );
    }
}
