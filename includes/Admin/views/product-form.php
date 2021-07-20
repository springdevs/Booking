<div id="sdevs_booking_data" class="panel sdevs_panel woocommerce_options_panel sdevs-form <?php echo esc_html($class); ?>">
    <strong><?php echo esc_html__('Booking Settings', 'sdevs_booking'); ?></strong>
    <?php
    wp_nonce_field("_product_booking_nonce", "_product_booking_nonce", false);
    woocommerce_wp_checkbox([
        "id"          => "enable_booking",
        "label"       => __("Enable Booking", "sdevs_booking"),
        "value"       => $enable_booking,
        "cbvalue"     => "yes",
        "description" => __("check this box to enable booking for this product", "sdevs_booking"),
        "desc_tip"    => true,
    ]);

    woocommerce_wp_checkbox([
        "id"          => "bookable_require_conf",
        "label"       => __("Require Confirmations", "sdevs_booking"),
        "value"       => "yes",
        "cbvalue"     => $bookable_require_conf,
        "description" => __("check this box if admin approval / confirmation is required for booking", "sdevs_booking"),
        "desc_tip"    => true,
    ]);

    echo "<hr style='margin: 20px 0;' /><strong>Calendar Display Options</strong>";

    woocommerce_wp_text_input([
        "id"    => "display_next_days",
        "label" => __('Display Next Days', 'sdevs_booking'),
        "type"  => "number",
        "value" => $display_next_days,
    ]);

    woocommerce_wp_text_input([
        "id"    => "display_start_time",
        "label" => __('Display Start Time', 'sdevs_booking'),
        "type"  => "time",
        "value" => $display_start_time,
    ]);

    woocommerce_wp_text_input([
        "id"    => "display_end_time",
        "label" => __('Display End Time', 'sdevs_booking'),
        "type"  => "time",
        "value" => $display_end_time,
    ]);

    ?>
</div>