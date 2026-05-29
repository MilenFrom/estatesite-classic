<?php
/**
 * Template Name: Reset Password
 *
 */
if ( is_user_logged_in() ) {
	wp_redirect( home_url() );
}
get_header();

get_template_part('template-parts/page-title');

$rp_key = '';
$rp_login = '';
$resetpass = false;

if ( isset( $_REQUEST['key'] ) && !empty( $_REQUEST['key'] ) ) :

	$rp_key = $_REQUEST['key'];

endif;

if ( isset( $_REQUEST['login'] ) && !empty( $_REQUEST['login'] ) ) :

	$rp_login = $_REQUEST['login'];

endif;

if ( !empty( $rp_key ) && !empty( $rp_login ) ) :

	$resetpass = true;

endif;

?>
<section class="frontend-submission-page my-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
            	<div id="reset_pass_msg_2"></div>

                <div class="dashboard-content-block hz-password-reset-page shadow-sm p-4 rounded">
                    <?php if ( $rp_login == 'invalidkey' ) : $resetpass = false; ?>
						<div class="alert alert-danger" role="alert"> <?php esc_html_e('Oops something went wrong.', 'houzez'); ?>  </div>
					<?php endif; ?>
					<?php if ( $rp_login == 'expiredkey' ) : $resetpass = false; ?>
			        	<div class="alert alert-danger"><?php esc_html_e('Session key expired.', 'houzez'); ?></div>
					<?php endif; ?>
					<?php if ( isset( $_REQUEST['password'] ) && $_REQUEST['password'] == 'changed' ) : $resetpass = false; ?>
			        	<div class="alert alert-success"><?php esc_html_e('Password has been changed, you can login now.', 'houzez'); ?></div>
			                <a href="#" data-bs-toggle="modal" data-bs-target="#login-register-form" class="back text-center"> <?php esc_html_e('Log in | Register', 'houzez'); ?> </a>
			            </div>
					<?php endif; ?>
					<?php if ( $resetpass ) : ?>
                        <div class="text-center mb-4">
                            <h3><?php esc_html_e('Reset Your Password', 'houzez'); ?></h3>
                        </div>
			            <form id="houzez_reset_password_form" onsubmit="return false;" autocomplete="off">
				            <input type="hidden" name="rp_login" value="<?php echo esc_attr($rp_login); ?>" autocomplete="off" />
							<input type="hidden" name="rp_key" value="<?php echo esc_attr($rp_key); ?>" />
							<?php wp_nonce_field( 'resetpassword_nonce', 'resetpassword_security' ); ?>
			                <div class="form-group mb-3">
			                    <input type="password" name="pass1" class="form-control" placeholder="<?php esc_html_e('New Password', 'houzez'); ?>">
			                </div>
			                <div class="form-group mb-4">
			                    <input type="password" name="pass2" class="form-control" placeholder="<?php esc_html_e('Confirm Password', 'houzez'); ?>">
			                </div>
			                <button type="submit" id="houzez_reset_password" class="btn btn-primary btn-block w-100">
			                	<?php get_template_part('template-parts/loader'); ?>
			                	<?php esc_html_e('Reset Password', 'houzez'); ?>		
			                </button>
			            </form>
			        <?php endif; ?>
                </div><!-- dashboard-content-block -->
            </div>
        </div><!-- row -->
    </div><!-- container -->
</section><!-- frontend-submission-page -->
<?php
get_footer();