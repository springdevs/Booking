<?php

namespace SpringDevs\Booking;

use SpringDevs\Booking\Admin\Product;
use SpringDevs\Booking\Admin\Post;
use SpringDevs\Booking\Admin\Menu;
use SpringDevs\Booking\Admin\Bookings;
use SpringDevs\Booking\Illuminate\Gateways;
use SpringDevs\Booking\Illuminate\Order;
use SpringDevs\Booking\Illuminate\OrderPage;
use SpringDevs\Booking\Illuminate\Status;

/**
 * The admin class
 */
class Admin
{

    /**
     * Initialize the class
     */
    public function __construct()
    {
        $this->dispatch_actions();
        new Menu();
        new Product();
        new OrderPage();
        new Gateways();
        new Post();
        new Status();
        new Illuminate();
        new Bookings();
        new Order();
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions()
    {
        require_once SDEVS_BOOKING_INCLUDES . "/Illuminate/PaymentGatewayRegister.php";
    }
}
