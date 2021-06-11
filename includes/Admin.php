<?php

namespace SpringDevs\Booking;

use SpringDevs\Booking\Admin\BookingForm;
use SpringDevs\Booking\Admin\BookingOrder;
use SpringDevs\Booking\Admin\Menu;
use SpringDevs\Booking\Admin\OrderPosts;
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
        new BookingForm();
        new OrderPage();
        new Gateways();
        new BookingOrder();
        new Status();
        new OrderPosts();
        new Order();
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions()
    {
        require_once WCBOOKING_ASSETS_INCLUDES . "/Illuminate/PaymentGatewayRegister.php";
    }
}
