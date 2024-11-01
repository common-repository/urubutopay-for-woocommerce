<?php
if (!defined('ABSPATH')) {
    exit;
}

function upgfc_display_payment_fields($accept_card_payment = null)
{
    $payment_mode_list_style = 'margin-left: 15px; margin-right: 15px; margin-top: 10px;';
    $payment_mode_radio_style = 'margin: 0 !important;margin-right: 5px !important;';
    $payment_mode_btn_style = 'outline: none;border: none;background: none;border-radius: 8px;background: transparent !important;
padding: 0px !important;box-shadow: none !important;';

    $upgfc_buy_style = 'width: 100%;margin: auto;font-size: 15px; padding: 0 5px;';

    $upgfc_form_group_style = 'margin: 10px 0 !important; display:flex; flex-direction: column;';

    $upgfc_form_payment_mode_style = 'display: flex; align-items: center;';

    $upgfc_payment_mode_btn_img_style = 'width: 100% !important; margin: 0 !important;';

    $upgfc_phone_number_input_style = 'outline: none;height: 35px;border: 1px solid var(--wc-upg-border-input-color);
  border-radius: 5px;padding: 0 10px;margin-top: 5px;';

?>
    <div class="upgfc-buy" style="<?php echo $upgfc_buy_style; ?>">
        <div class="upgfc-form-group" style="<?php echo $upgfc_form_group_style; ?>">
            <label for="">Choose A Payment Mode</label>
            <div class="upgfc-form-payment-mode" style="<?php echo $upgfc_form_payment_mode_style; ?>">
                <div class="upgfc-payment-mode-list" style="<?php echo $payment_mode_list_style; ?>">
                    <input type="radio" name="upgfc_payment_mode" value="<?php echo esc_attr(UPGFC_CHANNEL_NAME['MOMO']); ?>" style="<?php echo $payment_mode_radio_style; ?>">
                    <button type="button" class="upgfc-form-payment-mode-btn" style="<?php echo $payment_mode_btn_style; ?>">
                        <img src="<?php echo esc_url(plugins_url('../../public/images/mtn.svg', __FILE__)); ?>" alt="mtn" style="<?php echo $upgfc_payment_mode_btn_img_style; ?>" />
                    </button>
                </div>
                <div class="upgfc-payment-mode-list" style="<?php echo $payment_mode_list_style; ?>">
                    <input type="radio" name="upgfc_payment_mode" value="<?php echo esc_attr(UPGFC_CHANNEL_NAME['AIRTEL_MONEY']); ?>" style="<?php echo $payment_mode_radio_style; ?>">
                    <button type="button" class="upgfc-form-payment-mode-btn" style="<?php echo $payment_mode_btn_style; ?>">
                        <img src="<?php echo esc_url(plugins_url('../../public/images/airtel.svg', __FILE__)); ?>" alt="airtel" style="<?php echo $upgfc_payment_mode_btn_img_style; ?>" />
                    </button>
                </div>
                <?php if ($accept_card_payment && $accept_card_payment === UPGFC_RESPONSE_STATUS['YES']) : ?>
                    <div class="upgfc-payment-mode-list" style="<?php echo $payment_mode_list_style; ?>">
                        <input type="radio" name="upgfc_payment_mode" value="<?php echo esc_attr(UPGFC_CHANNEL_NAME['CARD']); ?>" style="<?php echo $payment_mode_radio_style; ?>">
                        <button type="button" class="upgfc-form-payment-mode-btn" style="<?php echo $payment_mode_btn_style; ?>">
                            <img src="<?php echo esc_url(plugins_url('../../public/images/visa.svg', __FILE__)); ?>" alt="visa" style="<?php echo $upgfc_payment_mode_btn_img_style; ?>" />
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="upgfc-form-group upgfc-phone-number-input-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" placeholder="Enter Phone Number (MTN or AIRTEL)" name="upgfc_phone_number" style="<?php echo $upgfc_phone_number_input_style; ?>" />
        </div>
    </div>
<?php
}
