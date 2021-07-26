<?php

function sdevs_booking_pro_activated(): bool {
    return class_exists('Sdevs_booking_pro');
}
