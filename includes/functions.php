<?php

function sdevs_wcbooking_pro_activated(): bool {
    return class_exists('Sdevs_booking_pro');
}
