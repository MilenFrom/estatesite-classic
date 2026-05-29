<?php
/**
 * Login/Register Header Component
 *
 * Displays login, register, and create listing buttons in the header
 * based on theme options and user authentication status.
 */

// Get theme options
$create_listing_enable = houzez_option('create_lisiting_enable');
$login_register_type = houzez_option('login_register_type', 'as_icon');
$header_login = houzez_option('header_login');
$header_register = houzez_option('header_register');
$add_to_favorite = houzez_option('add_to_favorite', 0);

// Template links
$header_create_listing_template = houzez_get_template_link_2('template/user_dashboard_submit.php');
$favorite_template = houzez_get_template_link_2('template/user_dashboard_favorites.php');

// Button text options
$create_listing_title = houzez_option('dsh_create_listing', 'Create a Listing');

// Custom create listing button options
$custom_create_listing_btn = houzez_option('custom_create_lisiting_btn', 0);
$custom_create_listing_link = houzez_option('custom_create_lisiting_link');
$custom_create_listing_title = houzez_option('custom_create_lisiting_title');

// Override default settings if custom create listing button is enabled
if ($custom_create_listing_btn && !empty($custom_create_listing_link)) {
    $header_create_listing_template = $custom_create_listing_link;
    $create_listing_title = !empty($custom_create_listing_title) ? $custom_create_listing_title : $create_listing_title;
}
?>

<div class="login-register on-hover-menu">
	<ul class="login-register-nav dropdown d-flex align-items-center" role="menubar">

		<?php 
		// Include phone number component
		get_template_part('template-parts/header/partials/phone-header-1-4'); 
		
		// Login/Register section - only show if the plugin is active
		if (class_exists('Houzez_login_register')): 
			
			// Only show login/register for non-logged in users and not on the login page
			if (($header_login || $header_register) && !is_user_logged_in() && !houzez_is_login_page()): 
				
				// Text-based login/register links
				if ($login_register_type == 'as_text'): ?>
					
					<?php if ($header_login): ?>
					<li class="login-link">
						<a href="#" data-bs-toggle="modal" data-bs-target="#login-register-form" role="menuitem">
							<?php esc_html_e('Login', 'houzez'); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if ($header_register): ?>
					<li class="register-link">
						<a href="#" data-bs-toggle="modal" data-bs-target="#login-register-form" role="menuitem">
							<?php esc_html_e('Register', 'houzez'); ?>
						</a>
					</li>
					<?php endif; ?>

					<?php if (!$add_to_favorite): ?>
					<li class="favorite-link" role="none">
						<a class="favorite-btn" href="<?php echo esc_url($favorite_template); ?>" role="menuitem">
							<?php echo houzez_option('dsh_favorite', 'Favorites'); ?>
							<span class="btn-bubble frvt-count">0</span>
						</a>
					</li>
					<?php endif; ?>
				
				<?php 
				// Icon-based login/register button
				else: ?>
					<li class="nav-item login-link" role="none">
						<!-- Login/Register Icon Button - Triggers modal -->
						<a class="btn btn-icon-login-register dropdown-toggle" 
						   href="#" 
						   data-bs-toggle="modal" 
						   data-bs-target="#login-register-form"
						   role="menuitem">
							<i class="houzez-icon icon-single-neutral-circle" aria-hidden="true"></i>
						</a>

						<?php if (!$add_to_favorite): ?>
						<ul class="dropdown-menu dropdown-menu-favorites rounded" role="menu">
							<!-- Favorites Link with Counter -->
							<li class="nav-item" role="none">
								<a class="favorite-btn dropdown-item" href="<?php echo esc_url($favorite_template); ?>" role="menuitem">
									<i class="houzez-icon icon-love-it me-2" aria-hidden="true"></i> 
									<?php echo houzez_option('dsh_favorite', 'Favorites'); ?> 
									<span class="btn-bubble frvt-count">0</span>
								</a>
							</li>
						</ul>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				
			<?php endif; // End login/register condition ?>
		<?php endif; // End Houzez_login_register class check ?>

		<?php 
		// Create Listing Button
		if ($create_listing_enable != 0 && !empty($header_create_listing_template)): ?>
		<li role="none">
			<a class="btn btn-create-listing hidden-xs hidden-sm" 
			   href="<?php echo esc_url($header_create_listing_template); ?>"
			   role="menuitem"
			   data-wazlytics-event="create_listing"
			   data-wazlytics-button-location="main-menu">
				<?php echo esc_attr($create_listing_title); ?>
			</a>
		</li>
		<?php endif; ?>
	</ul>
</div>