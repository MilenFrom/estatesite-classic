<?php
global $verification_data, $apply_verification_link;
?>
<p><?php esc_html_e('Your verification request was rejected.', 'houzez'); ?></p>
<?php if (isset($verification_data['rejection_reason']) && !empty($verification_data['rejection_reason'])): ?>
    <p class="mt-3"><strong><?php esc_html_e('Reason:', 'houzez'); ?></strong> 
        <?php echo esc_html($verification_data['rejection_reason']); ?>
    </p>
<?php endif; ?>
<p><?php esc_html_e('You can submit a new verification request with updated information.', 'houzez'); ?></p>
<div class="mt-3">
    <a href="<?php echo esc_url($apply_verification_link); ?>" class="btn btn-primary">
        <?php esc_html_e('Apply for Verification', 'houzez'); ?>
    </a>
</div>