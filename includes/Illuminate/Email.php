<?php

namespace SpringDevs\Booking\Illuminate;

/**
 * Class Email
 * @package SpringDevs\Booking\Global
 */

class Email
{
    public function __construct()
    {
        add_filter('woocommerce_email_classes', array($this, 'custom_init_emails'));
        add_filter('woocommerce_get_order_item_totals', array($this, 'remove_payment_method'), 10, 2);
    }

    public function custom_init_emails($emails)
    {
        // Include the email class file if it's not included already
        if (!isset($emails['sdevs_booking_confirmed_order'])) {
            $emails['sdevs_booking_confirmed_order'] = include 'class-booking-confirmed-email.php';
        }
        return $emails;
    }

    public function remove_payment_method($rows, $order)
    {
        if ($order->has_status("pending")) {
            unset($rows['payment_method']);
        }
        return $rows;
    }
}
