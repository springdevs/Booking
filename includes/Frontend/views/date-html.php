<p class="form-row form-row-wide" id="booking-date-field">
    <label for="booking-date"><?php echo esc_html__('Select Date', 'sdevs_booking'); ?> &nbsp;<abbr class="required" title="required">*</abbr></label>
    <select class="booking-date" name="booking-date" id="booking-date">
        <option value=""><?php echo esc_html__("Choose your Date", "sdevs_booking"); ?></option>';
        <?php foreach ($dateFields as $dateField) : ?>
            <option value="<?php echo esc_html($dateField); ?>"><?php echo esc_html__($dateField, "sdevs_booking"); ?></option>
        <?php endforeach; ?>
    </select>
</p>
<br>