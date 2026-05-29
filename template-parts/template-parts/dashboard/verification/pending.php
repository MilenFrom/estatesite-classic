<?php
global $verification_data;
?>
<p class="fw-bold"><?php esc_html_e('Thanks for submitting your documents!', 'houzez'); ?></p>
<p class="mb-3"><?php esc_html_e('We\'ve received your verification request and it\'s currently pending review.', 'houzez'); ?></p>
<p class="mb-3"><?php esc_html_e('Our team will check your documents and take one of the following steps:', 'houzez'); ?></p>
<ul class="list-group">
    <li class="list-group-item"><?php echo wp_kses(__('If everything is in order, your profile will be marked as <strong>Verified</strong> and you\'ll receive a confirmation email.', 'houzez'), array('strong' => array())); ?></li>
    <li class="list-group-item"><?php esc_html_e('If something\'s missing or unclear, we may ask you to provide more information.', 'houzez'); ?></li>
    <li class="list-group-item"><?php esc_html_e('If your request doesn\'t meet the requirements, you\'ll be notified with a reason for rejection.', 'houzez'); ?></li>
</ul>
<p class="mt-3"><strong><?php esc_html_e('Submitted on:', 'houzez'); ?></strong> 
    <?php echo isset($verification_data['submitted_on']) ? esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($verification_data['submitted_on']))) : ''; ?>
</p>