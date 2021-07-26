<p class="form-row form-row-wide" id="booking-date-field">
    <label for="booking-date"><?php echo esc_html__('Select Date', 'wc-booking'); ?> &nbsp;<abbr class="required" title="required">*</abbr></label>
    <select class="booking-date" name="booking-date" id="booking-date">
        <option value=""><?php echo esc_html__("Choose your Date", "wc-booking"); ?></option>';
        <?php foreach ($dateFields as $dateField) : ?>
            <option value="<?php echo esc_html($dateField); ?>"><?php echo esc_html__($dateField, "wc-booking"); ?></option>
        <?php endforeach; ?>
    </select>
</p>
<br>
