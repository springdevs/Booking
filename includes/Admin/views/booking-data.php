<table class="booking-customer-details" style="width: 100%;">
    <tbody>
        <tr>
            <th><?php _e('Name', 'wc-booking'); ?>:</th>
            <td><?php echo esc_html($order->get_formatted_billing_full_name()); ?></td>
        </tr>
        <tr>
            <th><?php _e('Email', 'wc-booking'); ?>:</th>
            <td><a href="mailto:<?php echo esc_html($order->get_billing_email()); ?>"><?php echo esc_html($order->get_billing_email()); ?></a></td>
        </tr>
        <tr>
            <th><?php _e('Address', 'wc-booking'); ?>:</th>
            <td><?php echo esc_sql($order->get_formatted_billing_address()); ?></td>
        </tr>
        <tr>
            <th><?php _e('Phone', 'wc-booking'); ?>:</th>
            <td><?php echo esc_html($order->get_billing_phone()); ?></td>
        </tr>
        <tr class="view">
            <th>&nbsp;</th>
            <td><a class="button button-small" target="_blank" href="<?php echo esc_html(get_edit_post_link($post_meta['order_id'])); ?>"><?php echo esc_html__('View Order', 'wc-booking'); ?></a></td>
        </tr>
    </tbody>
</table>