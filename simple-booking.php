<?php
/*
 Module Name : simple booking
*/

// don't call the file directly
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Sdevs_Wc_Booking class
 *
 * @class Sdevs_Wc_Booking The class that holds the entire Wc_Booking plugin
 */
final class Sdevs_Wc_Booking
{
    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.1';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = [];

    /**
     * Constructor for the Wc_Booking class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    private function __construct()
    {
        $this->define_constants();

        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Initializes the Sdevs_Wc_Booking() class
     *
     * Checks for an existing Sdevs_Wc_Booking() instance
     * and if it doesn't find one, creates it.
     *
     * @return Sdevs_Wc_Booking|bool
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new Sdevs_Wc_Booking();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container)) {
            return $this->container[$prop];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('WCBOOKING_ASSETS_VERSION', self::version);
        define('WCBOOKING_ASSETS_FILE', __FILE__);
        define('WCBOOKING_ASSETS_PATH', dirname(WCBOOKING_ASSETS_FILE));
        define('WCBOOKING_ASSETS_INCLUDES', WCBOOKING_ASSETS_PATH . '/includes');
        define('WCBOOKING_TEMPLATES', WCBOOKING_ASSETS_PATH . '/templates/');
        define('WCBOOKING_ASSETS_URL', plugins_url('', WCBOOKING_ASSETS_FILE));
        define('WCBOOKING_ASSETS_ASSETS', WCBOOKING_ASSETS_URL . '/assets');
    }

    /**
     * Load the plugin after all plugins are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {
        if ($this->is_request('admin')) {
            $this->container['admin'] = new SpringDevs\WcBooking\Admin();
        }

        if ($this->is_request('frontend')) {
            $this->container['frontend'] = new SpringDevs\WcBooking\Frontend();
        }

        if ($this->is_request('ajax')) {
            // require_once WCBOOKING_ASSETS_INCLUDES . '/class-ajax.php';
        }
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks()
    {
        add_action('init', [$this, 'init_classes']);
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {
        if ($this->is_request('ajax')) {
            // $this->container['ajax'] =  new SpringDevs\WcBooking\Ajax();
        }

        $this->container['api']    = new SpringDevs\WcBooking\Api();
        $this->container['assets'] = new SpringDevs\WcBooking\Assets();
    }

    /**
     * What type of request is this?
     *
     * @param string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();

            case 'ajax':
                return defined('DOING_AJAX');

            case 'rest':
                return defined('REST_REQUEST');

            case 'cron':
                return defined('DOING_CRON');

            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }
    }
} // Wc_Booking

/**
 * Initialize the main plugin
 *
 * @return Sdevs_Wc_Booking|bool
 */
function sdevs_wc_booking()
{
    return Sdevs_Wc_Booking::init();
}

/**
 *  kick-off the plugin
 */
sdevs_wc_booking();
