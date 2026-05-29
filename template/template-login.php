<?php
/**
 * Template Name: Login & Register
 * User: waqasriaz
 * Date: 1 July 2020
 * Time: 11:47 AM
 */
global $houzez_local;
$houzez_local = houzez_get_localization();
/**
 * @package Houzez
 * @since Houzez 1.0
 */
if( isset($_GET['fid']) && !empty( $_GET['fid'] ) ) { 
	
	$fb_info = get_option('houzez_user_facebook_info_'.$_GET['fid']);	
	$fid = isset($fb_info['id']) ? $fb_info['id'] : '';
	$picture_url = isset($fb_info['picture_url']) ? $fb_info['picture_url'] : '';
	$first_name = isset($fb_info['first_name']) ? $fb_info['first_name'] : '';
	$last_name = isset($fb_info['last_name']) ? $fb_info['last_name'] : '';
	$username = $first_name.'_'.$last_name;

	if( $fid != $_GET['fid'] ) {
		wp_die('Invalid social id');
	}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
    <meta name="format-detection" content="telephone=no">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="hz-fb-main-wrap">
    <div class="hz-fb-wrap">
        <div class="hz-fb-avatar-wrap">
            <img src="<?php echo esc_url($picture_url); ?>" alt="<?php echo esc_attr($username); ?>">
        </div><!-- .hz-fb-avatar-wrap -->
        
		<div class="main-step-wrap">
			<div class="hz-fb-header-wrap">
				<div class="hz-fb-welcome-message">
					<i class="houzez-icon icon-social-media-facebook"></i> <?php esc_html_e('Hi', 'houzez'); ?> <?php echo esc_html( $first_name ).' '.esc_html( $last_name ); ?>
				</div>
				<p><?php esc_html_e('You\'re now signed in with your Facebook account but you are still one step away of getting into our website.', 'houzez'); ?></p>
			</div><!-- .hz-fb-header-wrap -->
			<div class="hz-fb-footer-wrap">
				<div class="hz-fb-footer-columns">
					<div class="hz-fb-footer-title">
						<strong><?php esc_html_e('Already have an account?', 'houzez'); ?></strong>
					</div>
					<div>
						<p><?php esc_html_e('Link your existing account on your website to you Facebook ID.', 'houzez'); ?></p>
					</div>
					<button class="btn-link-account btn btn-primary"><?php esc_html_e('Link my account', 'houzez'); ?></button>
				</div><!-- .hz-fb-footer-columns -->
				<div class="hz-fb-footer-columns">
					<div class="hz-fb-footer-title">
						<strong><?php esc_html_e('New to our website?', 'houzez'); ?></strong>
					</div>
					<div>
						<p><?php esc_html_e('Create a new account and it will be associated with your Facebook ID.', 'houzez'); ?></p>
					</div>
					<button class="btn-create-account btn btn-primary"><?php esc_html_e('Create a new account', 'houzez'); ?></button>
				</div><!-- .hz-fb-footer-columns -->
			</div><!-- .hz-fb-footer-wrap -->
		</div><!-- .main-step-wrap -->

        <!--************* Link Account Form *************-->
        <div class="link-account-wrap" style="display: none;">
	        <div class="hz-fb-header-wrap">
	            <div class="hz-fb-welcome-message">
	                <?php esc_html_e( 'Already have an account?', 'houzez' ); ?>
	            </div>
	            <p><?php esc_html_e( 'Please enter your username and password of your existing account on our website. Once verified, it will linked to your Facebook ID.', 'houzez' ); ?></p>
	        </div><!-- .hz-fb-header-wrap -->

	        <div class="hz-fb-form-wrap">
	        	<div id="hz-link-messages" class="hz-social-messages"></div>
	            <form action="#" method="post">
	            	<?php wp_nonce_field( 'link_account_nonce', 'link_account_security' ); ?>
				    <input type="hidden" name="action" value="houzez_link_account">
				    <input type="hidden" name="lid" value="<?php echo esc_attr($fid);?>">
				    <input type="hidden" name="redirect_to" value="<?php echo esc_url(houzez_after_login_redirect()); ?>">
		            <div class="login-form-wrap">
		                <div class="form-group">
		                    <div class="form-group-field username-field">
		                        <input type="text" name="lusername" class="form-control" placeholder="<?php esc_html_e( 'Username', 'houzez' ); ?>" required>
		                    </div><!-- input-group -->
		                </div><!-- form-group -->
		                <div class="form-group">
		                    <div class="form-group-field password-field">
		                        <input type="password" name="lpassword" class="form-control" placeholder="<?php esc_html_e( 'Password', 'houzez' ); ?>" required>
		                    </div><!-- input-group -->
		                </div><!-- form-group -->
		            </div><!-- login-form-wrap -->
		            <div class="form-tools">
		                <a class="hz-fb-cancel" href="#"><?php esc_html_e( 'Cancel', 'houzez' ); ?></a>
		                <button id="houzez-link-account" type="submit" class="btn btn-primary">
						    <?php get_template_part('template-parts/loader'); ?>
						    <?php esc_html_e('Continue','houzez');?>
						</button>    
		            </div><!-- form-tools -->
		        </form>
	        </div><!-- .hz-fb-form-wrap -->
	    </div>

        <!--************* Create Account Form *************-->
        <div class="new-account-wrap" style="display: none;">
	        <div class="hz-fb-header-wrap">
	            <div class="hz-fb-welcome-message">
	                <?php esc_html_e( 'New to our website?', 'houzez' ); ?>
	            </div>
	            <p><?php echo esc_html__( 'Please fill in your information in the form below. Once completed, you will be able to automatically sign into our website through your Facebook ID.', 'houzez' )?></p>
	        </div><!-- .hz-fb-header-wrap -->

	        <div class="hz-fb-form-wrap">
	        	<div id="hz-create-messages" class="hz-social-messages"></div>
	            <form action="#" method="post">
	            	<input type="hidden" name="action" value="houzez_social_create_account">
				    <input type="hidden" name="id" value="<?php echo esc_attr($fid);?>">
				    <input type="hidden" name="term_condition" value="on">
				    <?php wp_nonce_field( 'houzez_social_register_nonce', 'houzez_social_register_security' ); ?>
				    <input type="hidden" name="redirect_to" value="<?php echo esc_url(houzez_after_login_redirect()); ?>">
		            <div class="login-form-wrap">
		                <div class="form-group">
		                    <div class="form-group-field username-field">
		                        <input type="text" name="username" value="<?php echo esc_attr($username);?>" class="form-control" placeholder="<?php esc_html_e( 'Username', 'houzez' ); ?>" required>
		                    </div><!-- input-group -->
		                </div><!-- form-group -->
		                <div class="form-group">
		                    <div class="form-group-field password-field">
		                        <input type="email" name="useremail" class="form-control" placeholder="<?php esc_html_e( 'Email', 'houzez' ); ?>" required>
		                    </div><!-- input-group -->
		                </div><!-- form-group -->
		            </div><!-- login-form-wrap -->
		            <div class="form-tools">
		                <a class="hz-fb-cancel" href="#"><?php esc_html_e( 'Cancel', 'houzez' ); ?></a>
		                <button id="houzez-create-account-btn" type="submit" class="btn btn-primary">
						    <?php get_template_part('template-parts/loader'); ?>
						    <?php esc_html_e('Continue','houzez');?>
						</button>   
		            </div><!-- form-tools -->
		        </form>
	        </div><!-- .hz-fb-form-wrap -->
	    </div>

    </div><!-- .hz-fb-wrap -->
    <a href="<?php echo home_url('/');?>" class="hz-fb-help-link"><i class="houzez-icon icon-arrow-left-1"></i> <?php echo esc_html__('Back to', 'houzez').' '.get_bloginfo('name'); ?></a> 
</div><!-- .hz-fb-main-wrap -->
<?php wp_footer(); ?>
</body>
<?php } else { ?>

<?php get_header(); ?>
<section class="blog-wrap">
    <div class="container">
    	<div class="page-title-wrap login-page-title">
            <div class="d-flex align-items-center text-center">
                <div class="page-title flex-grow-1">
					<h1><?php the_title(); ?></h1>
				</div><!-- page-title --> 
            </div><!-- d-flex -->  
        </div>
        <div class="row">
            <div class="col-lg-12">                      
                
                <?php if( !is_user_logged_in() ) { ?>
                
                	<?php if( isset( $_GET['verrify-email'] ) && isset( $_GET['token'] ) && $_GET['token'] != '' ) { ?>
		                <div class="login-form-page-wrap">

		                	<?php
		                	$email_verification_token = $_GET['token'];
						    $user_id = intval($_GET['verrify-email']);
						    $template = houzez_get_template_link('template/template-login.php');

						    $stored_token = get_user_meta( $user_id, 'houzez_email_verification_token', true );
						    if ( $email_verification_token == $stored_token ) {
						        update_user_meta( $user_id, 'houzez_email_verified', true );
						        delete_user_meta( $user_id, 'houzez_email_verification_token' );
						        echo esc_html__('We are pleased to inform you that your email address has been successfully verified. You can now log in to your account.', 'houzez');

						        if( houzez_option('header_login') != 0 ) { ?>
								<a href="<?php echo esc_url($template);?>"><?php esc_html_e('Login', 'houzez'); ?></a>
								<?php }

						    } else {
						        echo esc_html__('Invalid verification token.', 'houzez');
						    }
		                	?>
			                
			                
		               </div>
		           <?php } else { ?>

		             
		               <div class="login-form-page-wrap">
			                <div class="login-register-tabs">
			                    <ul class="nav nav-tabs">
			                        <li class="nav-item">
			                            <a class="modal-toggle-1 nav-link active" data-bs-toggle="tab" href="#login-form-tab" role="tab"><?php esc_html_e('Login', 'houzez'); ?></a>
			                        </li>
			                        <?php if( houzez_option('header_register') ) { ?>
			                        <li class="nav-item">
			                            <a class="modal-toggle-2 nav-link" data-bs-toggle="tab" href="#register-form-tab" role="tab"><?php esc_html_e('Register', 'houzez'); ?></a>
			                        </li>
			                    	<?php } ?>
			                    </ul>    
			                </div><!-- login-register-tabs -->
			                <div class="tab-content">
			                    <div class="tab-pane fade login-form-tab active show" id="login-form-tab" role="tabpanel">
			                        <?php get_template_part('template-parts/login-register/login-form'); ?>
			                    </div><!-- login-form-tab -->

			                    <?php if( houzez_option('header_register') ) { ?>
			                    <div class="tab-pane fade register-form-tab" id="register-form-tab" role="tabpanel">
			                       <?php get_template_part('template-parts/login-register/register-form'); ?>
			                   </div><!-- register-form-tab -->
			               		<?php } ?>
			               </div><!-- tab-content -->
		               </div>
           			<?php 
           		}?>
               <?php 
           		} else { 
           			echo '<div class="login-form-page-text">'; 
           			echo '<strong>'.esc_html__('You are already logged in!', 'houzez').'</strong>';
           			echo '</div>';
               }?>
               
           </div><!-- col-lg-12 -->
       </div><!-- row -->
   </div><!-- container -->
</section>
<?php get_footer(); ?>
<?php } ?>