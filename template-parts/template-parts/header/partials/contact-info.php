<?php
/**
 * Header Contact Information Template
 *
 * Displays contact information in the header including phone, email, address and business hours
 */

// Get contact information settings
$show_contact_info = houzez_option('hd2_contact_info') != '0';
$show_address_info = houzez_option('hd2_address_info') != '0';
$show_timing_info = houzez_option('hd2_timing_info') != '0';

// Get contact details
$contact_phone = houzez_option('hd2_contact_phone');
$contact_email = houzez_option('hd2_contact_email');

// Get address details
$address_line1 = houzez_option('hd2_address_line1');
$address_line2 = houzez_option('hd2_address_line2');

// Get timing details
$timing_hours = houzez_option('hd2_timing_hours');
$timing_days = houzez_option('hd2_timing_days');
?>

<div class="header-contact-wrap navbar-expand-lg d-flex align-items-center justify-content-between">
	<?php if($show_contact_info || $show_address_info || $show_timing_info) : ?>

	<?php
	   $contact_icon = houzez_option('hd2_contact_icon');
	   $contact_phone = houzez_option('hd2_contact_phone');
	   $contact_email = houzez_option('hd2_contact_email');

	   $address_icon = houzez_option('hd2_address_icon');
	   $address_line1 = houzez_option('hd2_address_line1');
	   $address_line2 = houzez_option('hd2_address_line2');

	   $timing_icon = houzez_option('hd2_timing_icon');
	   $timing_hours = houzez_option('hd2_timing_hours');
	   $timing_days = houzez_option('hd2_timing_days');

	    $allowed_html = array(
	        'i' => array(
	            'class' => array()
	        )
	    );
	?>

		<?php if($show_contact_info) : ?>
		<!-- Phone and Email Contact Information -->
		<div class="header-contact header-contact-1 d-flex align-items-center flex-fill">
			<div class="header-contact-left">
				<i class="houzez-icon icon-phone ms-1"></i>
			</div><!-- header-contact-left -->
			<div class="header-contact-right">
				<?php if(!empty($contact_phone)) : ?>
				<div>
					<a href="tel://<?php echo esc_attr($contact_phone); ?>"><?php echo esc_attr($contact_phone); ?></a>
				</div>
				<?php endif; ?>
				<?php if(!empty($contact_email)) : ?>
				<div>
					<a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_attr($contact_email); ?></a>
				</div>
				<?php endif; ?>
			</div><!-- .header-contact-right -->
		</div><!-- .header-contact -->
		<?php endif; ?>

		<?php if($show_address_info) : ?>
		<!-- Address Information -->
		<div class="header-contact header-contact-2 d-flex align-items-center flex-fill">
			<div class="header-contact-left">
				<i class="houzez-icon icon-pin ms-1"></i>
			</div><!-- header-contact-left -->
			<div class="header-contact-right">
				<?php if(!empty($address_line1)) : ?>
				<div>
					<?php echo esc_attr($address_line1); ?>
				</div>
				<?php endif; ?>
				<?php if(!empty($address_line2)) : ?>
				<div>
					<?php echo esc_attr($address_line2); ?>
				</div>
				<?php endif; ?>
			</div><!-- .header-contact-right -->
		</div><!-- .header-contact -->
		<?php endif; ?>

		<?php if($show_timing_info) : ?>
		<!-- Business Hours Information -->
		<div class="header-contact header-contact-3 d-flex align-items-center flex-fill">
			<div class="header-contact-left">
				<i class="houzez-icon icon-time-clock-circle ms-1"></i>
			</div><!-- header-contact-left -->
			<div class="header-contact-right">
				<?php if(!empty($timing_hours)) : ?>
				<div>
					<?php echo esc_attr($timing_hours); ?>
				</div>
				<?php endif; ?>
				<?php if(!empty($timing_days)) : ?>
				<div>
					<?php echo esc_attr($timing_days); ?>
				</div>
				<?php endif; ?>
			</div><!-- .header-contact-right -->
		</div><!-- .header-contact -->
		<?php endif; ?>

	<?php endif; ?>

	<!-- Social Icons -->
	<div class="header-contact header-contact-4 d-flex align-items-center">
		<?php get_template_part('template-parts/header/partials/social-icons'); ?>
	</div><!-- .header-contact -->
</div><!-- .header-contact-wrap -->


