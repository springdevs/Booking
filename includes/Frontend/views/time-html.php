<p class="form-row form-row-wide" id="booking-time-field">
    <label for="booking-time"><?php echo esc_html__('Select Time', 'sdevs_booking'); ?> &nbsp;<abbr class="required" title="required">*</abbr></label>
    <select class="booking-time" name="booking-time" id="booking-time">
        <option value=""><?php echo esc_html__("Choose your Time", "sdevs_booking"); ?></option>';
        <?php foreach ($timeFields as $timeField) : ?>
            <option value="<?php echo esc_html($timeField); ?>"><?php echo esc_html__($timeField, "sdevs_booking"); ?></option>'
        <?php endforeach; ?>
    </select>
</p>
<br>