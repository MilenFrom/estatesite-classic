<div class="form-group mb-3">
    <label class="form-label" for="diagnostic_date">
        <?php echo houzez_option('cl_diagnostic_date', 'Diagnostic Date').houzez_required_field('diagnostic_date'); ?>
    </label>
    <input type="text" id="diagnostic_date" <?php houzez_required_field_2('diagnostic_date'); ?> class="form-control" name="diagnostic_date" value="<?php echo sanitize_text_field( houzez_get_field_meta('diagnostic_date') ); ?>" placeholder="<?php echo houzez_option('cl_diagnostic_date_plac', 'e.g., After July 1, 2021'); ?>">
</div>