<?php

namespace SpringDevs\Booking;

use SpringDevs\Booking\Illuminate\Email;

/**
 * The Illuminate class
 * 
 * load these class's on everyWhere ( Admin & Frontend )
 */
class Illuminate
{

    /**
     * Initialize the class
     */
    public function __construct()
    {
        $this->dispatch_actions();
        new Email;
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions()
    {
        // require_once SDEVS_BOOKING_INCLUDES . "/Illuminate/PaymentGatewayRegister.php";
    }
}
