<?php
if (!defined('ABSPATH')) {
    exit;
}

class UPGFC_PaymentHelper
{
    public static function upgfc_generate_transaction_id()
    {
        $timestamp = getdate()[0];
        //woocommerce-plugin
        return 'WC-PLUGIN-' . $timestamp;
    }

    public static function upgfc_manage_order_status($upg_transaction_status)
    {
        switch ($upg_transaction_status) {
            case UPGFC_TRANSACTION_STATUS['PENDING']:
            case UPGFC_TRANSACTION_STATUS['INITIATED']:
                return array('status' => UPGFC_ORDER_STATUS['PENDING'], 'message' => 'Pending');

            case UPGFC_TRANSACTION_STATUS['FAILED']:
                return array('status' => UPGFC_ORDER_STATUS['FAILED'], 'message' => 'Failed');

            case UPGFC_TRANSACTION_STATUS['VALID']:
                return array('status' => UPGFC_ORDER_STATUS['COMPLETED'], 'message' => 'Completed');

            case UPGFC_TRANSACTION_STATUS['PENDING_SETTLEMENT']:
                return array('status' => UPGFC_ORDER_STATUS['COMPLETED'], 'message' => 'Pending For Settlement');

            case UPGFC_TRANSACTION_STATUS['CANCELED']:
                return array('status' => UPGFC_ORDER_STATUS['CANCELED'], 'message' => 'Cancelled');

            default:
                return array('status' => $upg_transaction_status, 'message' => $upg_transaction_status);
        }
    }
}
