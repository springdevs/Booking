<?php

namespace SpringDevs\Booking\Admin;

/**
 * Class Bookings
 * @package SpringDevs\Booking\Admin
 */
class Bookings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, "remove_default_meta_box"]);
        add_action('add_meta_boxes', [$this, "create_meta_boxes"]);
        add_action('admin_head-post.php', [$this, "some_styles"]);
        add_action('admin_head-post-new.php', [$this, "some_styles"]);
        add_action('admin_footer-post.php', [$this, "some_scripts"]);
        add_action('admin_footer-post-new.php', [$this, "some_scripts"]);
        add_action("save_post", [$this, "save_bookable_order_post"], 25);
    }

    public function remove_default_meta_box()
    {
        remove_meta_box('submitdiv', 'bookable_order', 'side');
    }

    public function create_meta_boxes()
    {
        // Save Data
        add_meta_box(
            'bookable_order_save_post',
            'Save',
            [$this, 'bookable_order_save_post'],
            'bookable_order',
            'side',
            'default'
        );

        // User Data
        add_meta_box(
            'bookable_order_customer_data',
            'Customer Details',
            [$this, 'bookable_order_customer_data'],
            'bookable_order',
            'side',
            'default'
        );

        // Form Fields
        add_meta_box(
            'bookable_order_meta_fields',
            'Data',
            [$this, 'bookable_order_meta_fields'],
            'bookable_order',
            'normal',
            'default'
        );
    }

    public function bookable_order_save_post()
    {
        include 'views/booking-save.php';
    }

    public function bookable_order_customer_data()
    {
        $post_meta = get_post_meta(get_the_ID(), "_booking_order_meta", true);
        $order     = wc_get_order($post_meta["order_id"]);
        include 'views/booking-data.php';
    }

    public function some_styles()
    {
        global $post;
        if ($post->post_type == "bookable_order") :
            include 'views/styles.php';
        endif;
    }

    public function some_scripts()
    {
        global $post;
        if ($post->post_type == "bookable_order") :
            include 'views/scripts.php';
        endif;
    }

    public function bookable_order_meta_fields()
    {
        global $post;
        $statuses = [
            "paid"         => __("Paid", "wc-booking"),
            "processing"   => __("Processing", "wc-booking"),
            "unpaid"       => __("Unpaid", "wc-booking"),
            "pending_conf" => __("Pending Confirmation", "wc-booking"),
            "confirmed"    => __("Request Confirmed", "wc-booking"),
            "complete"     => __("Complete", "wc-booking"),
            "cancelled"    => __("Cancelled", "wc-booking"),
        ];
        $post_meta  = get_post_meta($post->ID, "_booking_order_meta", true);
        $product    = wc_get_product($post_meta["product_id"]);
        $attributes = [];

        if (empty($post_meta)) {
            $date = null;
            $time = null;
        } else {
            $date  = $post_meta["date"];
            $time  = strtotime($post_meta["time"]);
            $time  = date("H:i", $time);
            $order = wc_get_order($post_meta["order_id"]);
            foreach ($order->get_items() as $key => $item) {
                foreach ($item->get_meta_data() as $data) {
                    if ($data->key != "Date" && $data->key != "Time") {
                        $attributes[$data->key] = $data->value;
                    }
                }
            }
        }
        include 'views/booking-meta-form.php';
    }

    public function save_bookable_order_post($post_id)
    {
        if (wp_is_post_revision($post_id)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST["bookable_order_date"])) {
            return;
        }

        remove_action("save_post", [$this, "save_bookable_order_post"], 25);

        $date              = sanitize_text_field($_POST["bookable_order_date"]);
        $time              = sanitize_text_field($_POST["bookable_order_time"]);
        $time              = date("h:i a", strtotime($time));
        $status            = sanitize_text_field($_POST["bookable_order_status"]);
        $post_meta         = get_post_meta($post_id, "_booking_order_meta", true);
        $post_meta["date"] = $date;
        $post_meta["time"] = $time;
        $order_id = $post_meta["order_id"];
        $order = wc_get_order($order_id);
        if ($status === "paid") {
            $order->update_status('completed');
        } elseif ($status === "processing") {
            $order->update_status('processing');
        } elseif ($status === "unpaid") {
            $order->update_status('pending');
        } elseif ($status === "pending_conf") {
            $order->update_status('reconf');
        } elseif ($status === "confirmed") {
            $order->update_status('pending');
            WC()->mailer();
            do_action('sdevs_booking_confirmed', $order->get_id());
        } elseif ($status === "complete") {
            $order->update_status('completed');
        } elseif ($status === "cancelled") {
            $order->update_status('cancelled');
        }
        update_post_meta($post_id, "_booking_order_meta", $post_meta);
        $post = array('ID' => $post_id, 'post_status' => $status);
        wp_update_post($post);

        add_action("save_post", [$this, "save_bookable_order_post"], 25);
        return;
    }
}
