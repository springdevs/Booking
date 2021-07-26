<table class="form-table sdevs-form">
    <tbody>
        <tr>
            <th class="sdevs_th" scope="row"><label for="bookable_order_date"><?php echo esc_html__("Product", "wc-booking"); ?></label></th>
            <td>
                <p class="description" id="tagline-description">
                    <a href="<?php the_permalink($post_meta["product_id"]); ?>" target="_blank">
                        <?php echo esc_html($product->get_title()); ?>
                    </a>
                    <br />
                    <?php foreach ($attributes as $key => $value) : ?>
                        <strong><?php echo esc_html($key); ?> : </strong> <?php echo esc_html($value); ?><br />
                    <?php endforeach; ?>
                </p>
            </td>
        </tr>
        <tr>
            <th class="sdevs_th" scope="row"><label for="bookable_order_date"><?php echo esc_html__("Date", "wc-booking"); ?></label></th>
            <td><input name="bookable_order_date" type="text" id="bookable_order_date" class="regular-text pac-target-input" value="<?php echo esc_html($date); ?>" required />
                <p class="description" id="tagline-description">Ex : Sep 14, 2020</p>
            </td>
        </tr>
        <tr>
            <th class="sdevs_th" scope="row"><label for="bookable_order_time">Time</label></th>
            <td><input name="bookable_order_time" type="time" id="bookable_order_time" value="<?php echo esc_html($time); ?>" class="regular-text pac-target-input" required /></td>
        </tr>
        <tr>
            <th class="sdevs_th" scope="row"><label for="bookable_order_status">Status</label></th>
            <td>
                <select name="bookable_order_status" id="bookable_order_status">
                    <?php foreach ($statuses as $value => $label) : ?>
                        <option value="<?php echo esc_html($value); ?>" <?php if ($post->post_status == $value) {
                                                                            echo "selected";
                                                                        } ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </tbody>
</table>
