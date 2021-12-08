<?php

namespace SpringDevs\Booking;

use SpringDevs\Booking\Frontend\Gateways;
use SpringDevs\Booking\Frontend\MyAccount;
use SpringDevs\Booking\Frontend\Products;
use SpringDevs\Booking\Illuminate\Gateways as IlluminateGateways;
use SpringDevs\Booking\Illuminate\Order;
use SpringDevs\Booking\Illuminate\OrderPage;
use SpringDevs\Booking\Illuminate\Status;

/**
 * Frontend handler class
 */
class Frontend
{
    /**
     * Frontend constructor.
     */
    public function __construct()
    {
        require_once SDEVS_BOOKING_INCLUDES . "/Illuminate/PaymentGatewayRegister.php";
        new Products();
        new Gateways();
        new IlluminateGateways();
        new OrderPage();
        new MyAccount();
        new Status();
        new Order();
        new Illuminate();
    }
}
