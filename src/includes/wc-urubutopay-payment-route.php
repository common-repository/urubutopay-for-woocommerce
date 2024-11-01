<?php
if (!defined('ABSPATH')) {
    exit;
}

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class UPGFC_PaymentRoute
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'upgfc_register_payment_route'));
    }

    public function upgfc_register_payment_route()
    {
        $namespace = 'wc-urubutopay';

        register_rest_route(
            $namespace,
            '/transaction/check',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'upgfc_check_transaction'),
                'permission_callback' => '__return_true'
            )
        );

        register_rest_route(
            $namespace,
            '/payment/callback',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'upgfc_payment_callback'),
                'permission_callback' => '__return_true'
            ),
        );

        register_rest_route(
            $namespace,
            '/authenticate',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'upgfc_authenticate'),
                'permission_callback' => '__return_true'
            )
        );
    }

    public function upgfc_check_transaction($request)
    {
        try {
            $body = json_decode($request->get_body());
            // validation
            $v = new UPGFC_Validation();

            $validate = $v->validate_check_transaction($body);

            $validationError = 'validation error';

            if (count($validate) > 0) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['BAD_REQUEST'],
                    $validationError,
                    array('status' => UPGFC_HTTP_CODE['BAD_REQUEST'], 'errors' => $validate)
                );
            }

            $transaction_id = $body->transaction_id;
            $args = array('transaction_id' => $transaction_id);
            $response = UPGFC_PaymentService::check_transaction($args);

            if (
                $response['code'] !== UPGFC_HTTP_CODE['OK'] &&
                $response['code'] !== UPGFC_HTTP_CODE['CREATED']
            ) {
                return new WP_Error(
                    $response['code'],
                    'unable to check transaction',
                    array('status' => $response['code'], 'message' => 'unable to check transaction')
                );
            }

            $data = $response['data']->data;

            $order = wc_get_order($data->payer_code);
            if (!$order) {
                $not_found = 'Order not found';
                return new WP_Error(
                    UPGFC_HTTP_CODE['NOT_FOUND'],
                    $not_found,
                    array('status' => UPGFC_HTTP_CODE['NOT_FOUND'], 'message' => $not_found)
                );
            }

            if (null === $order) {
                $not_found = 'Order not found';
                return new WP_Error(
                    UPGFC_HTTP_CODE['NOT_FOUND'],
                    $not_found,
                    array('status' => UPGFC_HTTP_CODE['NOT_FOUND'],  'message' => $not_found)
                );
            }

            $manage_order = UPGFC_PaymentHelper::upgfc_manage_order_status($data->transaction_status);

            $order->update_status($manage_order['status'], $manage_order['message']);

            $post_meta = get_post_meta($order->get_id(), UPGFC_META['PAYMENT_DETAILS'], true);

            if ($post_meta) {
                //update post meta
                $decode = json_decode($post_meta);
                if ($decode->transaction_id !== $data->transaction_id) {
                    //check if transaction id is the same one we generated
                    return new WP_Error(
                        UPGFC_HTTP_CODE['CONFLICT'],
                        'invalid transaction ID',
                        array(
                            'status' => UPGFC_HTTP_CODE['CONFLICT'],
                            'message' => 'invalid transaction ID'
                        )
                    );
                }
                $decode->transaction_status = $data->transaction_status;
                $decode->external_transaction_id = $data->internal_transaction_id;
                $decode->transaction_date = $data->payment_date_time;

                update_post_meta($order->get_id(), UPGFC_META['PAYMENT_DETAILS'], json_encode($decode));
            }

            $res = array(
                'transaction_id' => $data->transaction_id,
                'transaction_status' => $data->transaction_status
            );
            return rest_ensure_response(array('data' => $res));
        } catch (\Throwable $th) {
            return new WP_Error(
                UPGFC_HTTP_CODE['INTERNAL_SERVER_ERROR'],
                'Internal server error',
                array(
                    'status' => UPGFC_HTTP_CODE['INTERNAL_SERVER_ERROR'],
                    'message' => 'Internal server error'
                )
            );
        }
    }

    public function upgfc_payment_callback($request)
    {
        try {
            $body = json_decode($request->get_body());

            $token = $request->get_header('x_authorization');

            $unauth_message = 'unauthorized access';

            $validationError = 'validation error';

            if (null === $token) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['UNAUTHORIZED'],
                    $unauth_message,
                    array('status' => UPGFC_HTTP_CODE['UNAUTHORIZED'], 'message' => $unauth_message)
                );
            }

            $explode = explode(" ", $token);
            if (count($explode) <= 0) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['UNAUTHORIZED'],
                    $unauth_message,
                    array('status' => UPGFC_HTTP_CODE['UNAUTHORIZED'], 'message' => $unauth_message)
                );
            }
            $pickToken = count($explode) === 1 ? $explode[0] : $explode[1];
            $setting_id = UPGFC_SETTING_ID;

            $option = get_option('woocommerce_' . $setting_id . '_settings');

            $decode = JWT::decode($pickToken, new Key(
                $option[UPGFC_OPTION_FIELDS['SECRET_KEY']],
                UPGFC_JWT_ALGORITHM
            ));

            // check if decoded username from token is not equal to the one from setting page
            if ($decode->user_name !== $option[UPGFC_OPTION_FIELDS['USERNAME']]) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['UNAUTHORIZED'],
                    $unauth_message,
                    array('status' => UPGFC_HTTP_CODE['UNAUTHORIZED'], 'message' => $unauth_message)
                );
            }
            // validate transaction id, external transaction id, transaction status
            $v = new UPGFC_Validation();
            $validate = $v->validate_payment_callback($body);
            if (count($validate) > 0) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['BAD_REQUEST'],
                    $validationError,
                    array(
                        'status' => UPGFC_HTTP_CODE['BAD_REQUEST'],
                        'message' => $validationError,
                        'errors' => $validate
                    )
                );
            }

            $posts = wc_get_order(intval($body->payer_code));

            $not_found_msg = 'Order not found';

            if (null === $posts || !$posts) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['NOT_FOUND'],
                    $not_found_msg,
                    array(
                        'status' => UPGFC_HTTP_CODE['NOT_FOUND'],
                        'message' => $not_found_msg
                    )
                );
            }

            $order = $posts;

            $post_meta = get_post_meta($order->get_id(), UPGFC_META['PAYMENT_DETAILS'], true);

            if (null === $post_meta || !isset($post_meta) || empty($post_meta)) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['NOT_FOUND'],
                    $not_found_msg,
                    array(
                        'status' => UPGFC_HTTP_CODE['NOT_FOUND'],
                        'message' => $not_found_msg
                    )
                );
            }
            $decode = json_decode($post_meta);

            if ($decode->transaction_id !== $body->transaction_id) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['CONFLICT'],
                    'invalid transaction ID',
                    array('status' => UPGFC_HTTP_CODE['CONFLICT'], 'message' => 'invalid transaction ID')
                );
            }
            $order_status = UPGFC_PaymentHelper::upgfc_manage_order_status($body->transaction_status);
            $posts->update_status($order_status['status'], $order_status['message']);

            $decode->external_transaction_id = $body->internal_transaction_id;
            $decode->transaction_status = $body->transaction_status;
            $decode->transaction_date = $body->payment_date_time;

            update_post_meta($order->get_id(), UPGFC_META['PAYMENT_DETAILS'], json_encode($decode));

            $response = array(
                'internal_transaction_id' => $decode->transaction_id,
                'external_transaction_id' => $decode->external_transaction_id,
                'payer_phone_number' => $decode->phone_number
            );
            return rest_ensure_response(array('data' => $response));
        } catch (\Throwable $th) {
            return new WP_Error(
                UPGFC_HTTP_CODE['INTERNAL_SERVER_ERROR'],
                'Internal server error',
                array(
                    'status' => UPGFC_HTTP_CODE['INTERNAL_SERVER_ERROR'],
                    'message' => 'Internal server error'
                )
            );
        }
    }

    public function upgfc_authenticate($request)
    {
        try {
            $body = json_decode($request->get_body());

            $validationError = 'validation error';

            // validation
            $v = new UPGFC_Validation();
            $validate = $v->validate_auth($body);
            if ($validate && count($validate) > 0) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['BAD_REQUEST'],
                    $validationError,
                    array('status' => UPGFC_HTTP_CODE['BAD_REQUEST'], 'errors' => $validate)
                );
            }

            $setting_id = UPGFC_SETTING_ID;
            $option = get_option('woocommerce_' . $setting_id . '_settings');
            if (
                !isset($option[UPGFC_OPTION_FIELDS['USERNAME']]) ||
                !isset($option[UPGFC_OPTION_FIELDS['PASSWORD']]) ||
                !isset($option[UPGFC_OPTION_FIELDS['SECRET_KEY']])
            ) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['UNAUTHORIZED'],
                    'incorrect username and password',
                    array('status' => UPGFC_HTTP_CODE['UNAUTHORIZED'])
                );
            }

            if (
                $body->user_name !== $option[UPGFC_OPTION_FIELDS['USERNAME']] ||
                $body->password !== $option[UPGFC_OPTION_FIELDS['PASSWORD']]
            ) {
                return new WP_Error(
                    UPGFC_HTTP_CODE['UNAUTHORIZED'],
                    'incorrect username and password',
                    array('status' => UPGFC_HTTP_CODE['UNAUTHORIZED'])
                );
            }

            $jwt = JWT::encode(
                array(
                    "user_name" => $body->user_name,
                    "exp" => time() + (60 * 60) // expire in 1 hour
                ),
                $option[UPGFC_OPTION_FIELDS['SECRET_KEY']],
                UPGFC_JWT_ALGORITHM
            );

            return rest_ensure_response(array(
                'data' => array(
                    'token' => 'Bearer ' . $jwt,
                    'message' => 'authenticated successfully',
                )
            ));
        } catch (\Throwable $th) {
            return new WP_Error(
                UPGFC_HTTP_CODE['INTERNAL_SERVER_ERROR'],
                'Internal server error',
                array('status' => UPGFC_HTTP_CODE['INTERNAL_SERVER_ERROR'])
            );
        }
    }
}
