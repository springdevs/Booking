<?php

/**
 * Class Sdevs_Booking_Confirmed_Email file
 *
 * @package SpringDevs\Emails
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Sdevs_Booking_Confirmed_Email')) :

    /**
     * Booking Confirmed Order Email.
     *
     * An email sent to the admin when a booking is confirmed by admin.
     *
     * @class       Sdevs_Booking_Confirmed_Email
     * @version     1.0.0
     * @package     SpringDevs\Emails
     * @extends     WC_Email
     */
    class Sdevs_Booking_Confirmed_Email extends WC_Email
    {

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->id             = 'sdevs_booking_confirmed_order';
            $this->title          = __('Booking Confirmed Order', 'wc-booking');
            $this->description    = __('This email is received when an Booking is confirmed.', 'wc-booking');
            $this->customer_email = true;
            $this->template_base = SDEVS_BOOKING_TEMPLATES;
            $this->template_html    = 'emails/booking-confirmed.php';
            $this->template_plain   = 'emails/plain/booking-confirmed.php';
            $this->placeholders   = array(
                '{order_date}'   => '',
                '{order_number}' => '',
            );

            // Triggers for this email.
            add_action('sdevs_booking_confirmed', array($this, 'trigger'), 10);

            // Call parent constructor.
            parent::__construct();
        }

        /**
         * Get email subject.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_subject()
        {
            return __('[{site_title}]: Booking has been confirmed #{order_number}', 'wc-booking');
        }

        /**
         * Get email heading.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_heading()
        {
            return __('Booking Confirmed: #{order_number}', 'wc-booking');
        }

        /**
         * Trigger the sending of this email.
         *
         * @param int            $order_id The order ID.
         */
        public function trigger($order_id)
        {
            $this->setup_locale();

            if ($order_id) {
                $order = wc_get_order($order_id);
            }

            if (is_a($order, 'WC_Order')) {
                $this->object                         = $order;
                $this->recipient                      = $this->object->get_billing_email();
                $this->placeholders['{order_date}']   = wc_format_datetime($this->object->get_date_created());
                $this->placeholders['{order_number}'] = $this->object->get_order_number();
            }

            if ($this->is_enabled() && $this->get_recipient()) {
                $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
            }

            $this->restore_locale();
        }

        /**
         * Get content html.
         *
         * @return string
         */
        public function get_content_html()
        {
            return wc_get_template_html(
                $this->template_html,
                array(
                    'order'              => $this->object,
                    'email_heading'      => $this->get_heading(),
                    'additional_content' => $this->get_additional_content(),
                    'sent_to_admin'      => false,
                    'plain_text'         => false,
                    'email'              => $this,
                ),
                "",
                $this->template_base
            );
        }

        /**
         * Get content plain.
         *
         * @return string
         */
        public function get_content_plain()
        {
            return wc_get_template_html(
                $this->template_plain,
                array(
                    'order'              => $this->object,
                    'email_heading'      => $this->get_heading(),
                    'additional_content' => $this->get_additional_content(),
                    'sent_to_admin'      => false,
                    'plain_text'         => true,
                    'email'              => $this,
                ),
                "",
                $this->template_base
            );
        }

        /**
         * Default content to show below main email content.
         *
         * @since 3.7.0
         * @return string
         */
        public function get_default_additional_content()
        {
            return __('Congratulations. Your booking has been confirmed !!', 'wc-booking');
        }
    }

endif;

return new Sdevs_Booking_Confirmed_Email();
