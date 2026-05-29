<?php
$header = houzez_option('header_style');
$phone_number = houzez_option('hd1_4_phone');
$phone_enabled = houzez_option('hd1_4_phone_enable', 0);

if (!empty($phone_number) && $phone_enabled && ($header == 1 || $header == 4)) { ?> 
<li class="btn-phone-number" role="none">
	<a href="tel:<?php echo esc_attr($phone_number); ?>" role="menuitem">
		<i class="houzez-icon icon-phone-actions-ring me-1" aria-hidden="true"></i>
		<span><?php echo esc_html($phone_number); ?></span>
	</a>
</li>
<?php } ?>
