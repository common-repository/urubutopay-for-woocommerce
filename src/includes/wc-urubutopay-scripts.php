<?php
if (!defined('ABSPATH')) {
    exit;
}

class UPGFC_Script
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'upgfc_register_scripts'));
    }

    public function upgfc_register_scripts()
    {
        // custom script
        wp_register_script(
            'upgfc-script',
            plugins_url('../../public/js/pay.js', __FILE__),
            null,
            null,
            true
        );
        wp_enqueue_script('upgfc-script');
        wp_localize_script(
            'upgfc-script',
            'Assets',
            array(
                'failed-icon' => plugins_url('../../public/images/payment/failed.png', __FILE__),
                'success-icon' => plugins_url('../../public/images/payment/success.png', __FILE__),
                'loading-icon-blue' => plugins_url('../../public/images/spinner/blue.svg', __FILE__),
            )
        );
        wp_localize_script(
            'upgfc-script',
            'TransactionStatus',
            array(
                'FAILED' => UPGFC_TRANSACTION_STATUS['FAILED'],
                'VALID' => UPGFC_TRANSACTION_STATUS['VALID'],
                'PENDING' => UPGFC_TRANSACTION_STATUS['PENDING'],
                'INITIATED' => UPGFC_TRANSACTION_STATUS['INITIATED'],
                'CANCELED' => UPGFC_TRANSACTION_STATUS['CANCELED'],
                'PENDING_SETTLEMENT' => UPGFC_TRANSACTION_STATUS['PENDING_SETTLEMENT']
            )
        );
        wp_localize_script('upgfc-script', 'ApiBaseUrl', get_rest_url());

        //custom css styles
        wp_register_style(
            'upgfc-styles',
            plugins_url('../../public/css/styles.css', __FILE__),
            null,
            null,
            false
        );
        wp_enqueue_style('upgfc-styles');
    }
}
