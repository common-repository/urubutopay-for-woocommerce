<?php
if (!defined('ABSPATH')) {
    exit;
}

class UPGFC_PaymentCompletion
{
    public function __construct()
    {
        add_action('init', array($this, 'upgfc_register_query_parameter'));
        add_action('admin_init', array($this, 'upgfc_register_payment_completion_page'));
        add_filter('the_content', array($this, 'upgfc_display_payment_completion_content'));
    }

    public static function upgfc_register_payment_completion_page()
    {
        $check_post = get_posts(array(
            'post_type' => UPGFC_POST_TYPE['PAGE'],
            'name' => UPGFC_PAGE_SLUG['PAYMENT_COMPLETION']
        ));

        if (count($check_post) <= 0) {
            $post = array(
                'post_title' => UPGFC_PAGE_TITLE['PAYMENT_COMPLETION'],
                'post_type' => UPGFC_POST_TYPE['PAGE'],
                'post_status' => 'publish',
                'post_name' => UPGFC_PAGE_SLUG['PAYMENT_COMPLETION']
            );
            wp_insert_post($post);
        }
    }

    public function upgfc_register_query_parameter()
    {
        global $wp;
        $wp->add_query_var('order_id');
    }

    public function upgfc_display_payment_completion_content($content)
    {
        global $post;

        if (
            $post->post_type === UPGFC_POST_TYPE['PAGE'] &&
            $post->post_name === UPGFC_PAGE_SLUG['PAYMENT_COMPLETION']
        ) {
            if (get_query_var('order_id')) {
                $output = $content;
                $output .= '[upgfc-payment-completion order_id="' . get_query_var('order_id') . '"]';
                return $output;
            } else {
                $output = $content;
                $output .= upgfc_not_found_func();
                return $output;
            }
        }

        return $content;
    }

    public static function upgfc_remove_payment_completion_page()
    {
        $check_post = get_posts(array(
            'post_type' => UPGFC_POST_TYPE['PAGE'],
            'name' => UPGFC_PAGE_SLUG['PAYMENT_COMPLETION']
        ));

        if (count($check_post) > 0) {
            wp_delete_post($check_post[0]->ID, true);
        }
    }
}
