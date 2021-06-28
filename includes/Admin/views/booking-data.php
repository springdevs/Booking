<table class="booking-customer-details" style="width: 100%;">
    <tbody>
        <tr>
            <th><?php _e('Name', 'sdevs_booking'); ?>:</th>
            <td><?php echo $order->get_formatted_billing_full_name(); ?></td>
        </tr>
        <tr>
            <th><?php _e('Email', 'sdevs_booking'); ?>:</th>
            <td><a href="mailto:<?php echo $order->get_billing_email(); ?>"><?php echo $order->get_billing_email(); ?></a></td>
        </tr>
        <tr>
            <th><?php _e('Address', 'sdevs_booking'); ?>:</th>
            <td><?php echo $order->get_formatted_billing_address(); ?></td>
        </tr>
        <tr>
            <th><?php _e('Phone', 'sdevs_booking'); ?>:</th>
            <td><?php echo $order->get_billing_phone(); ?></td>
        </tr>
        <tr class="view">
            <th>&nbsp;</th>
            <td><a class="button button-small" target="_blank" href="<?php echo get_edit_post_link($post_meta['order_id']); ?>"><?php _e('View Order', 'sdevs_booking'); ?></a></td>
        </tr>
    </tbody>
</table>