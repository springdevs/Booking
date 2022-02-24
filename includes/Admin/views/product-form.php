<div id="sdevs_booking_data" class="panel sdevs_panel woocommerce_options_panel sdevs-form <?php echo esc_html($class); ?>">
    <strong><?php echo esc_html__('Booking Settings', 'wc-booking'); ?></strong>
    <?php
    wp_nonce_field("_product_booking_nonce", "_product_booking_nonce", false);
    woocommerce_wp_checkbox([
        "id"          => "enable_booking",
        "label"       => __("Enable Booking", "wc-booking"),
        "value"       => $enable_booking,
        "cbvalue"     => "yes",
        "description" => __("check this box to enable booking for this product", "wc-booking"),
        "desc_tip"    => true,
    ]);

    woocommerce_wp_checkbox([
        "id"          => "bookable_require_conf",
        "label"       => __("Require Confirmations", "wc-booking"),
        "value"       => "yes",
        "cbvalue"     => $bookable_require_conf,
        "description" => __("check this box if admin approval / confirmation is required for booking", "wc-booking"),
        "desc_tip"    => true,
    ]);

    echo "<hr style='margin: 20px 0;' /><strong>Calendar Display Options</strong>";

    woocommerce_wp_text_input([
        "id"    => "display_next_days",
        "label" => __('Display Next Days', 'wc-booking'),
        "type"  => "number",
        "value" => $display_next_days,
    ]);

    woocommerce_wp_text_input([
        "id"    => "display_start_time",
        "label" => __('Display Start Time', 'wc-booking'),
        "type"  => "time",
        "value" => $display_start_time,
        "description" => __("<br/>Display Start Time must be set to <b>AM</b>", "wc-booking"),
    ]);

    woocommerce_wp_text_input([
        "id"    => "display_end_time",
        "label" => __('Display End Time', 'wc-booking'),
        "type"  => "time",
        "value" => $display_end_time,
        "description" => __("<br/>Display End Time must be set to <b>PM</b>", "wc-booking"),
    ]);

    ?>
</div>