<?php

namespace SpringDevs\Booking\Admin;

/**
 * Class Product
 * @package SpringDevs\Booking\Admin
 */
class Product
{
    public function __construct()
    {
        add_filter('woocommerce_product_data_tabs', array($this, 'custom_data_tabs'));
        add_filter('woocommerce_product_data_panels', array($this, 'custom_tab_data'));
        add_action('save_post_product', array($this, 'save_bookable_settings'));
    }

    public function custom_data_tabs($tabs)
    {
        $class                 = apply_filters('sdevs_booking_product_data_class', 'show_if_simple');
        $tabs['sdevs_booking'] = array(
            'label'  => __('Booking', 'wc-booking'),
            'class'  => $class,
            'target' => 'sdevs_booking_data',
        );
        return $tabs;
    }

    public function custom_tab_data()
    {
        if (sdevs_wcbooking_pro_activated()) {
            do_action('sdevs_booking_pro_edit_fields_html');
        } else {
            $screen = get_current_screen();
            if ($screen->parent_base == "edit") :
                $post_meta = get_post_meta(get_the_ID(), "bookable_product_meta", true);
                if (empty($post_meta)) :
                    $enable_booking        = false;
                    $display_next_days     = "";
                    $display_start_time    = "";
                    $display_end_time      = "";
                    $bookable_require_conf = false;
                else :
                    $enable_booking        = $post_meta["enable_booking"] ? "yes" : false;
                    $display_next_days     = $post_meta["display_next_days"];
                    $display_start_time    = $post_meta["display_start_time"];
                    $display_end_time      = $post_meta["display_end_time"];
                    $bookable_require_conf = $post_meta["bookable_require_conf"] ? "yes" : false;
                endif;
            else :
                $enable_booking        = false;
                $display_next_days     = "";
                $display_start_time    = "";
                $display_end_time      = "";
                $bookable_require_conf = false;
            endif;
            $class = apply_filters('sdevs_booking_product_data_class', '');
            include 'views/product-form.php';
        }
    }

    public function save_bookable_settings($post_id)
    {
        if (!isset($_POST["_product_booking_nonce"]) || !wp_verify_nonce($_POST['_product_booking_nonce'], '_product_booking_nonce')) {
            return;
        }

        if (sdevs_wcbooking_pro_activated()) {
            return;
        }

        $display_next_days     = isset($_POST['display_next_days']) ? sanitize_text_field($_POST['display_next_days']) : false;
        $display_start_time    = isset($_POST['display_start_time']) ? sanitize_text_field($_POST['display_start_time']) : false;
        $display_end_time      = isset($_POST['display_end_time']) ? sanitize_text_field($_POST['display_end_time']) : false;
        $bookable_require_conf = isset($_POST['bookable_require_conf']);
        $enable_booking        = isset($_POST['enable_booking']);
        update_post_meta($post_id, 'bookable_product_meta', [
            "enable_booking"        => $enable_booking,
            "display_next_days"     => $display_next_days,
            "display_start_time"    => $display_start_time,
            "display_end_time"      => $display_end_time,
            "bookable_require_conf" => $bookable_require_conf,
        ]);
    }
}
