<?php

namespace SpringDevs\Booking\Frontend;

/**
 * Class Products
 * @package SpringDevs\Booking\Frontend
 */
class Products
{
    public function __construct()
    {
        add_filter('woocommerce_is_purchasable', [$this, "check_if_purchasable"], 10, 2);
        add_action('woocommerce_single_product_summary', [$this, "text_if_active"]);
        add_filter('woocommerce_loop_add_to_cart_link', [$this, 'change_button'], 10, 2);
        add_filter('woocommerce_is_sold_individually', [$this, 'remove_quantity_field'], 10, 2);
        add_action('woocommerce_before_add_to_cart_button', [$this, 'date_time_placed_html']);
        add_filter('woocommerce_add_to_cart_validation', [$this, 'filter_add_to_cart_validation'], 10, 4);
        add_filter('woocommerce_product_single_add_to_cart_text', [$this, 'change_single_add_to_cart_text']);
        add_filter('woocommerce_add_cart_item_data', [$this, 'add_to_cart_item_data'], 10, 3);
        add_filter('woocommerce_get_item_data', [$this, 'display_on_cart_item'], 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'save_order_item_product_meta'], 10, 4);
        add_action('woocommerce_thankyou', [$this, 'change_order_status'], 10, 1);
    }

    public function check_if_purchasable($is_purchasable, $product)
    {
        $status = $this->check_product_in_request($product->get_id());
        if ($status) return false;
        return $is_purchasable;
    }

    public function text_if_active()
    {
        global $product;
        if ($product->is_type('variable')) return;
        $status = $this->check_product_in_request($product->get_id());
        if ($status) {
            _e('<strong>You already request this product !!</strong>', 'sdevs_booking');
        }
    }

    public function check_product_in_request($product_id)
    {
        $status = false;
        $book_meta = get_post_meta($product_id, "bookable_product_meta", true);
        if (!empty($book_meta) && $book_meta["enable_booking"]) {
            $customer_orders = get_posts(array(
                'numberposts' => -1,
                'meta_key' => '_customer_user',
                'orderby' => 'date',
                'order' => 'DESC',
                'meta_value' => get_current_user_id(),
                'post_type' => wc_get_order_types(),
                'post_status' => array_keys(wc_get_order_statuses()), 'post_status' => array('wc-reconf'),
            ));
            foreach ($customer_orders as $customer_order) {
                $order = wc_get_order($customer_order->ID);
                foreach ($order->get_items() as $order_item) {
                    if ($order_item['product_id'] == $product_id) {
                        $status = true;
                    }
                }
                if ($status) {
                    break;
                }
            }
        }
        return $status;
    }

    public function change_button($button, $product)
    {
        $book_meta = get_post_meta($product->get_ID(), "bookable_product_meta", true);
        if (!empty($book_meta) && $book_meta["enable_booking"]) {
            $button_text = __("Read more", "sdevs_booking");
            $button = '<a class="button" href="' . $product->get_permalink() . '">' . $button_text . '</a>';
            return $button;
        } else {
            return $button;
        }
    }

    public function remove_quantity_field($return, $product)
    {
        $book_meta = get_post_meta($product->get_ID(), "bookable_product_meta", true);
        if (!empty($book_meta) && $book_meta["enable_booking"]) {
            return true;
        } else {
            return $return;
        }
    }

    public function date_time_placed_html()
    {
        global $product;
        $post_meta = get_post_meta($product->get_ID(), "bookable_product_meta", true);
        // Only on bookable products
        if (!(!empty($post_meta) && $post_meta["enable_booking"])) return;

        if (sdevs_booking_pro_activated()) {
            do_action('sdevs_booking_pro_single_date_time_html', $product, $post_meta);
        } else {
            $dateFields = [];

            for ($i = 0; $i < $post_meta["display_next_days"]; $i++) {
                $date = strtotime("+{$i} day");
                array_push($dateFields, date('M d, Y', $date));
            }

            include 'views/date-html.php';

            $timeFields = [];

            $start_time = strtotime($post_meta['display_start_time']);
            $end_time = strtotime($post_meta['display_end_time']);
            for ($i = 0; $i < 25; $i++) {
                $curr_time = date('h:i A', strtotime("+{$i} hours", $start_time));
                if (strtotime($curr_time) >= $end_time) {
                    break;
                }
                $timeFields[] = $curr_time;
            }

            include 'views/time-html.php';
        }
    }

    public function filter_add_to_cart_validation($passed, $product_id, $quantity, $variation_id = 0)
    {
        $book_meta = get_post_meta($product_id, "bookable_product_meta", true);
        $passed = $this->cartProductCheck($book_meta);
        if (!(!empty($book_meta) && $book_meta["enable_booking"]))
            return $passed;

        if (isset($_POST['booking-date']) && empty($_POST['booking-date'])) {
            wc_add_notice(__("Please choose your Date.", "sdevs_booking"), 'error');
            $passed = false;
        } elseif (isset($_POST['booking-time']) && empty($_POST["booking-time"])) {
            wc_add_notice(__("Please choose your Time.", "sdevs_booking"), 'error');
            $passed = false;
        }
        return $passed;
    }

    public function cartProductCheck($current_product_meta)
    {
        $cartProductStatus = true;
        $cartProducts = WC()->cart->get_cart();
        foreach ($cartProducts as $key => $values) {
            $_product = $values['data'];
            $post_meta = get_post_meta($_product->get_id(), "bookable_product_meta", true);
            if (!empty($post_meta) && $post_meta["enable_booking"] && $post_meta["bookable_require_conf"]) :
                $cartProductStatus = false;
                $error_notice = __("Currently You have Confirmation product in a cart !!", "sdevs_booking");
                if (!empty($current_product_meta) && $current_product_meta["enable_booking"]) :
                    if ($current_product_meta["bookable_require_conf"]) :
                        $cartProductStatus = true;
                    endif;
                endif;
            else :
                if (!empty($current_product_meta) && $current_product_meta["enable_booking"] && $current_product_meta["bookable_require_conf"]) :
                    $error_notice = __("Currently You have Non-Confirmation product in a cart !!", "sdevs_booking");
                    $cartProductStatus = false;
                endif;
            endif;
        }

        if (!$cartProductStatus) {
            wc_add_notice($error_notice, 'error');
        }
        return $cartProductStatus;
    }

    public function change_single_add_to_cart_text($text)
    {
        $book_meta = get_post_meta(get_the_ID(), "bookable_product_meta", true);
        if (!empty($book_meta) && $book_meta["enable_booking"]) {
            if ($book_meta["bookable_require_conf"]) {
                return __('Check Availability', 'sdevs_booking');
            } else {
                return __('Book Now', 'sdevs_booking');
            }
        } else {
            return $text;
        }
    }

    public function add_to_cart_item_data($cart_item_data, $product_id, $variation_id)
    {
        if (isset($_POST['booking-date'])) {
            $cart_item_data['booking-date'] = esc_attr($_POST['booking-date']);
        }
        if (isset($_POST['booking-time'])) {
            $cart_item_data['booking-time'] = esc_attr($_POST['booking-time']);
        }
        return $cart_item_data;
    }

    public function display_on_cart_item($cart_item_data, $cart_item)
    {
        if (isset($cart_item['booking-date'])) {
            $cart_item_data[] = array(
                'name' => __('Date', 'sdevs_booking'),
                'value' => $cart_item['booking-date'],
            );
        }
        if (isset($cart_item['booking-time'])) {
            $cart_item_data[] = array(
                'name' => __('Time', 'sdevs_booking'),
                'value' => $cart_item['booking-time'],
            );
        }
        return $cart_item_data;
    }

    public function save_order_item_product_meta($item, $cart_item_key, $cart_item, $order)
    {
        if (isset($cart_item['booking-date']) && isset($cart_item['booking-time'])) {
            $item->update_meta_data('Date', $cart_item['booking-date']);
            $item->update_meta_data('Time', $cart_item['booking-time']);
        }
    }

    public function change_order_status($order_id)
    {
        if (!$order_id)
            return;
        $post_meta = get_post_meta($order_id, "_bookable_order_data_change", true);

        $conf_status = false;
        $order = wc_get_order($order_id);
        $items = $order->get_items();
        foreach ($items as $item) {
            $book_meta = get_post_meta($item["product_id"], "bookable_product_meta", true);
            if (!empty($book_meta) && $book_meta["enable_booking"]) {
                if ($book_meta["bookable_require_conf"]) $conf_status = true;
            }
        }

        if ($conf_status) :
            if (empty($post_meta)) {
                $new_data = ["save" => true, "status" => false];
                update_post_meta($order_id, "_bookable_order_data_change", $new_data);
                $order->update_status('reconf');
            } else {
                if ($post_meta["status"]) {
                    $post_meta["status"] = false;
                    update_post_meta($order_id, "_bookable_order_data_change", $post_meta);
                    $order->update_status('reconf');
                }
            }
        endif;

        if (empty($post_meta)) {
            $new_data = ["save" => false, "status" => false];
            update_post_meta($order_id, "_bookable_order_data_change", $new_data);
            $this->save_data_bookable_order($order_id);
        } else {
            if ($post_meta["save"]) {
                $post_meta["save"] = false;
                update_post_meta($order_id, "_bookable_order_data_change", $post_meta);
                $this->save_data_bookable_order($order_id);
            }
        }
    }

    public function save_data_bookable_order($order_id)
    {
        $order = wc_get_order($order_id);

        $post_status = "confirmed";

        switch ($order->get_status()) {
            case "pending";
                $post_status = "pending_conf";
                break;

            case "reconf";
                $post_status = "pending_conf";
                break;

            case "on-hold";
                $post_status = "pending_conf";
                break;

            case "completed";
                $post_status = "paid";
                break;

            case "cancelled";
                $post_status = "cancelled";
                break;

            case "failed";
                $post_status = "cancelled";
                break;

            default;
                $post_status = "confirmed";
                break;
        }

        foreach ($order->get_items() as $key => $item) {

            $book_meta = get_post_meta($item["product_id"], "bookable_product_meta", true);

            if (!empty($book_meta) && $book_meta["enable_booking"]) :
                $booking_date = $item->get_meta('Date');
                $booking_time = $item->get_meta('Time');

                $date_time = [
                    "order_id" => $order_id,
                    "date" => $booking_date,
                    "time" => $booking_time,
                    "product_id" => $item["product_id"]
                ];

                $args = [
                    "post_title" => "booking",
                    "post_type" => "bookable_order",
                    "post_status" => $post_status
                ];
                $post_id = wp_insert_post($args);
                $args["ID"] = $post_id;
                $args["post_title"] = "Booking #{$post_id}";
                wp_update_post($args);
                update_post_meta($order_id, "_booking_post_meta", [
                    'post_id' => $post_id
                ]);
                update_post_meta($post_id, "_booking_order_meta", $date_time);
            endif;
        }
    }
}
