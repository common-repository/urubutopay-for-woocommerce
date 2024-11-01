<?php
if (!defined('ABSPATH')) {
    exit;
}

function upgfc_display_check_transaction($transaction_id, $order_status = null)
{
    $message = 'Thank you for initiating payment, Kindly wait for confirmation';
    $content_class = 'upgfc-check-transaction-content--primary';
    $image = plugins_url('../../public/images/spinner/blue.svg', __FILE__);

    if ($order_status === UPGFC_ORDER_STATUS['FAILED']) {
        $message = 'Payment failed';
        $content_class = 'upgfc-check-transaction-content--failed';
        $image = plugins_url('../../public/images/payment/failed.png', __FILE__);
    }

    if ($order_status === UPGFC_ORDER_STATUS['CANCELED']) {
        $message = 'Payment cancelled';
        $content_class = 'upgfc-check-transaction-content--failed';
        $image = plugins_url('../../public/images/payment/failed.png', __FILE__);
    }

    if ($order_status === UPGFC_ORDER_STATUS['COMPLETED']) {
        $message = 'Payment succeed';
        $content_class = 'upgfc-check-transaction-content--success';
        $image = plugins_url('../../public/images/payment/success.png', __FILE__);
    }

    $upgfc_check_transaction_style = ' margin: auto !important; width: 400px !important; display: flex; flex-direction: column; justify-content: center; align-items: center;
  background: white !important; padding: 30px; border-radius: 10px !important; min-height: 400px !important;';

    $upgfc_check_transaction_image_style = 'width: 100px; height: 100px;';

    $img_style = 'width: 100%; height: 100%; object-fit: cover;';

    $upgfc_check_transaction_content_style = 'font-size: 15px; font-weight: bold; text-align: center; margin-top: 20px;'

?>
    <div class="upgfc-check-transaction" data-attr="<?php echo esc_attr($order_status) ?>" data-transaction="<?php echo esc_attr($transaction_id); ?>" style="<?php echo $upgfc_check_transaction_style; ?>">
        <div class="upgfc-check-transaction-image" style="<?php echo $upgfc_check_transaction_image_style; ?>">
            <img src="<?php echo esc_url($image); ?>" alt="image" style="<?php echo $img_style; ?>">
        </div>
        <div class='upgfc-check-transaction-content <?php echo esc_attr($content_class) ?>' style="<?php echo $upgfc_check_transaction_content_style; ?>">
            <?php echo esc_html($message) ?></div>
    </div>
<?php
}
