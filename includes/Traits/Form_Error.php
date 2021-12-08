<?php

namespace SpringDevs\Booking\Traits;

/**
 * Error handler trait
 */
trait Form_Error
{

    /**
     * Holds the errors
     *
     * @var array
     */
    public array $errors = [];

    /**
     * Check if the form has error
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has_error( string $key): bool {
        return isset($this->errors[$key]);
    }

    /**
     * Get the error by key
     *
     * @param $key
     *
     * @return string | false
     */
    public function get_error($key)
    {
        if (isset($this->errors[$key])) {
            return $this->errors[$key];
        }

        return false;
    }
}
