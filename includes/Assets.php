<?php

namespace SpringDevs\Booking;

/**
 * Scripts and Styles Class
 */
class Assets
{
    /**
     * Assets constructor.
     */
    function __construct()
    {
        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'register'], 5);
        } else {
            add_action('wp_enqueue_scripts', [$this, 'register'], 5);
        }
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register()
    {
        $this->register_scripts($this->get_scripts());
        $this->register_styles($this->get_styles());
    }

    /**
     * Register scripts
     *
     * @param array $scripts
     *
     * @return void
     */
    private function register_scripts($scripts)
    {
        foreach ($scripts as $handle => $script) {
            $deps      = $script['deps'] ?? false;
            $in_footer = $script['in_footer'] ?? false;
            $version   = $script['version'] ?? SDEVS_BOOKING_VERSION;

            wp_register_script($handle, $script['src'], $deps, $version, $in_footer);
        }
    }

    /**
     * Register styles
     *
     * @param array $styles
     *
     * @return void
     */
    public function register_styles( array $styles)
    {
        foreach ($styles as $handle => $style) {
            $deps = $style['deps'] ?? false;

            wp_register_style($handle, $style['src'], $deps, SDEVS_BOOKING_VERSION);
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_scripts()
    {
        $plugin_js_assets_path = SDEVS_BOOKING_ASSETS . '/js/';

        return [];
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles(): array {
        $plugin_css_assets_path = SDEVS_BOOKING_ASSETS . '/css/';

        return [
            "sdevs_booking_admin_styles" => [
                "src" => $plugin_css_assets_path . 'admin.css'
            ]
        ];
    }
}
