<?php


namespace SpringDevs\Booking\Admin;


/**
 * Class Post
 * @package SpringDevs\Booking\Admin
 */
class Post
{
    public function __construct()
    {
        add_action("init", [$this, "create_post_type"]);
        add_action("admin_enqueue_scripts", [$this, "enqueue_scripts"]);
        add_filter('post_row_actions', [$this, 'post_row_actions'], 10, 2);
        add_filter('manage_bookable_order_posts_columns', [$this, 'add_custom_columns']);
        add_action('manage_bookable_order_posts_custom_column', [$this, 'add_custom_columns_data'], 10, 2);
        add_filter('wp_untrash_post_status', [$this, 'filter_untrash_status'], 10, 3);
    }

    /**
     *Create Custom Post Type : bookable_order
     */
    public function create_post_type()
    {
        $labels = array(
            "name" => __("Bookings", "wc-booking"),
            "singular_name" => __("Booking", "wc-booking"),
            'name_admin_bar'        => __('Booking\'s', 'wc-booking'),
            'archives'              => __('Item Archives', 'wc-booking'),
            'attributes'            => __('Item Attributes', 'wc-booking'),
            'parent_item_colon'     => __('Parent :', 'wc-booking'),
            'all_items'             => __('Bookings', 'wc-booking'),
            'add_new_item'          => __('Add New Booking', 'wc-booking'),
            'add_new'               => __('Add Booking', 'wc-booking'),
            'new_item'              => __('New Booking', 'wc-booking'),
            'edit_item'             => __('Edit Booking', 'wc-booking'),
            'update_item'           => __('Update Booking', 'wc-booking'),
            'view_item'             => __('View Booking', 'wc-booking'),
            'view_items'            => __('View Booking', 'wc-booking'),
            'search_items'          => __('Search Booking', 'wc-booking'),
            'not_found' =>  __('No Bookings Found', 'wc-booking'),
            'not_found_in_trash' => __('No Bookings found in Trash', 'wc-booking'),
        );

        $args = array(
            "label" => __("Bookings", "wc-booking"),
            "labels" => $labels,
            "description" => "",
            "public" => false,
            "publicly_queryable" => true,
            "show_ui" => true,
            "delete_with_user" => false,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => false,
            "show_in_menu" => false,
            "show_in_nav_menus" => false,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            'capabilities' => array(
                'create_posts' => false
            ),
            "hierarchical" => false,
            "rewrite" => array("slug" => "bookable_order", "with_front" => true),
            "query_var" => true,
            "supports" => array("title"),
        );

        register_post_type("bookable_order", $args);
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('sdevs_booking_admin_styles');
    }

    /**
     * @param $unset_actions
     * @param $post
     * @return mixed
     */
    public function post_row_actions($unset_actions, $post)
    {
        global $current_screen;
        if ($current_screen->post_type != 'bookable_order')
            return $unset_actions;
        unset($unset_actions['inline hide-if-no-js']);
        unset($unset_actions['view']);
        return $unset_actions;
    }

    public function add_custom_columns($columns)
    {
        $columns['booked'] = __('Booked', 'wc-booking');
        $columns['order_id'] = __('Order', 'wc-booking');
        $columns['customer'] = __('Customer', 'wc-booking');
        $columns['booking_status'] = __('Status', 'wc-booking');
        $new = [];
        $order_id = $columns['order_id'];
        $booked = $columns['booked'];
        $customer = $columns['customer'];
        $booking_status = $columns['booking_status'];
        unset($columns['booked']);

        foreach ($columns as $key => $value) {
            if ($key == 'date') {
                $new['order_id'] = $order_id;
                $new['customer'] = $customer;
                $new['booked'] = $booked;
                $new['booking_status'] = $booking_status;
            }
            $new[$key] = $value;
        }

        return $new;
    }

    public function add_custom_columns_data($column, $post_id)
    {
        $post_meta = get_post_meta($post_id, "_booking_order_meta", true);
        $product = wc_get_product($post_meta["product_id"]);
        $order = wc_get_order($post_meta["order_id"]);
        if (!$order) return;
        $order_items = $order && is_array($order->get_items()) ? $order->get_items() : [];
        $attributes = [];
        foreach ($order_items as $key => $item) {
            foreach ($item->get_meta_data() as $data) {
                if ($data->key != "Date" && $data->key != "Time") $attributes[$data->key] = $data->value;
            }
        }
        if ($column == "booked") {
?>
            <a href="<?php the_permalink($post_meta["product_id"]); ?>">
                <?php echo esc_html($product->get_title()); ?>
            </a>
            <br />
            <?php foreach ($attributes as $key => $value) : ?>
                <strong><?php echo esc_html($key); ?> : </strong> <?php echo esc_html($value); ?> <br />
            <?php endforeach; ?>
            <hr />
        <?php
            echo $post_meta["date"] . ' - ' . $post_meta["time"];
        } elseif ($column == "customer") {
            echo $order->get_formatted_billing_full_name();
        ?>
            <br />
            <a href="mailto:<?php echo esc_html($order->get_billing_email()); ?>"><?php echo esc_html($order->get_billing_email()); ?></a>
            <br />
            <?php echo esc_html__("Phone :", "wc-booking"); ?> <a href="tel:<?php echo esc_html($order->get_billing_phone()); ?>"><?php echo esc_html($order->get_billing_phone()); ?></a>
        <?php
        } elseif ($column == "order_id") {
        ?>
            <a href="<?php echo esc_html(get_edit_post_link($post_meta["order_id"])); ?>" target='__blank'><?php echo esc_html($post_meta["order_id"]); ?></a>
<?php
        } elseif ($column == "booking_status") {
            $status = [
                "paid"         => __("Paid", "wc-booking"),
                "processing"   => __("Processing", "wc-booking"),
                "unpaid"       => __("Unpaid", "wc-booking"),
                "pending_conf" => __("Pending Confirmation", "wc-booking"),
                "confirmed"    => __("Request Confirmed", "wc-booking"),
                "complete"     => __("Complete", "wc-booking"),
                "cancelled"    => __("Cancelled", "wc-booking"),
                "trash"        => __("Trash", "wc-booking"),
            ];
            echo esc_html($status[get_post_status($post_id)]);
        }
    }

    public function filter_untrash_status($new_status, $post_id, $previous_status)
    {
        $post_type = get_post_type($post_id);
        return $post_type == 'bookable_order' ? $previous_status : $new_status;
    }
}
