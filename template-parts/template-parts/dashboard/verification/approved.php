<?php
global $verification_data;
?>
<p><?php esc_html_e('Your account is verified! A verification badge will appear on your profile across the platform.', 'houzez'); ?></p>
<?php if (isset($verification_data['processed_on'])): ?>
    <p><strong><?php esc_html_e('Approved on:', 'houzez'); ?></strong> 
        <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($verification_data['processed_on']))); ?>
    </p>
<?php endif; ?>