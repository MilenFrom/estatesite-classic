<?php
$allowed_html_array = array(
    'a' => array(
        'href' => array(),
        'target' => array(),
        'title' => array()
    )
);
$user_show_roles = houzez_option('user_show_roles');
$show_hide_roles = houzez_option('show_hide_roles');
?>
<div id="hz-register-messages" class="hz-social-messages"></div>
<?php if( get_option('users_can_register') ) { ?>
<form id="houzez_register_form" method="post">
<div class="register-form-wrap">
    
    <?php if( houzez_option('register_first_name', 0) == 1 ) { ?>
    <div class="form-group">
        <div class="form-group-field username-field">
            <input type="text" class="form-control" name="first_name" placeholder="<?php esc_html_e('First Name','houzez'); ?>" />
        </div>
    </div>
    <?php } ?>

    <?php if( houzez_option('register_last_name', 0) == 1 ) { ?>
    <div class="form-group">
        <div class="form-group-field username-field">
            <input type="text" class="form-control" name="last_name" placeholder="<?php esc_html_e('Last Name','houzez'); ?>" />
        </div>
    </div>
    <?php } ?>

    <div class="form-group">
        <div class="form-group-field username-field">
            <input type="text" class="form-control" name="username" placeholder="<?php esc_html_e('Username','houzez'); ?>" />
        </div>
    </div>

    <div class="form-group">
        <div class="form-group-field email-field">
            <input type="email" class="form-control" name="useremail" autocomplete="username" placeholder="<?php esc_html_e('Email','houzez'); ?>" />
        </div>
    </div>

    <?php if( houzez_option('register_mobile', 0) == 1 ) { ?>
    <div class="form-group">
        <div class="form-group-field phone-field">
            <input type="number" class="form-control" name="phone_number" placeholder="<?php esc_html_e('Phone','houzez'); ?>" />
        </div>
    </div>
    <?php } ?>

    <?php if( houzez_option('enable_password') == 'yes' ) { ?>
    <div class="form-group">
        <div class="form-group-field password-field">
            <input type="password" class="form-control" name="register_pass" autocomplete="new-password" placeholder="<?php esc_html_e('Password','houzez'); ?>" />
        </div>
    </div>
    <div class="form-group">
        <div class="form-group-field password-field">
            <input type="password" class="form-control" name="register_pass_retype" autocomplete="new-password" placeholder="<?php esc_html_e('Retype Password','houzez'); ?>" />
        </div>
    </div>
    <?php } ?>
    
</div>

<?php do_action('houzez_register_form_fields'); ?>

<?php if($user_show_roles != 0) { ?>
<div class="form-group mt-2">
    <select name="role" class="selectpicker form-control" data-style="btn-default" data-width="100%" title="<?php esc_html_e('Select your account type', 'houzez'); ?>">
        <option value=""><?php esc_html_e('Select your account type', 'houzez'); ?></option>
        <?php
        if( isset($show_hide_roles['agent']) && $show_hide_roles['agent'] != 1 ) {
            echo '<option value="houzez_agent">'.houzez_option('agent_role').'</option>';
        }
        if( isset($show_hide_roles['agency']) && $show_hide_roles['agency'] != 1 ) {
            echo '<option value="houzez_agency">'.houzez_option('agency_role').'</option>';
        }
        if( isset($show_hide_roles['owner']) && $show_hide_roles['owner'] != 1 ) {
            echo '<option value="houzez_owner">'.houzez_option('owner_role').'</option>';
        }
        if( isset($show_hide_roles['buyer']) && $show_hide_roles['buyer'] != 1 ) {
            echo '<option value="houzez_buyer">'.houzez_option('buyer_role').'</option>';
        }
        if( isset($show_hide_roles['seller']) && $show_hide_roles['seller'] != 1 ) {
            echo '<option value="houzez_seller">'.houzez_option('seller_role').'</option>';
        }
        ?>
    </select>
</div>
<?php } ?>

<div class="form-tools">
    <label class="control control--checkbox">
        <input type="checkbox" name="term_condition">
        <span>
        <?php echo sprintf( __( 'I agree with your <a target="_blank" href="%s">Terms & Conditions</a>', 'houzez' ), 
            get_permalink(houzez_option('login_terms_condition') )); ?>
        </span>
        <span class="control__indicator"></span>
    </label>
</div>

<?php get_template_part('template-parts/captcha'); ?>

<?php do_action('houzez_after_register_form_fields'); ?>

<?php wp_nonce_field( 'houzez_register_nonce', 'houzez_register_security' ); ?>
<input type="hidden" name="action" value="houzez_register" id="register_action">
<button type="submit" id="houzez-register-btn" class="btn-register btn btn-primary w-100">
    <?php get_template_part('template-parts/loader'); ?>
    <?php esc_html_e('Register','houzez');?>
</button>
</form>

<?php if( houzez_option('facebook_login') == 'yes' || houzez_option('google_login') == 'yes' ) { ?>
<div class="social-login-wrap">
    <?php if( houzez_option('facebook_login') == 'yes' ) { ?>
    <button type="button" class="hz-facebook-login btn btn-facebook-login w-100">
        <?php get_template_part('template-parts/loader'); ?>
        <?php esc_html_e( 'Continue with Facebook', 'houzez' ); ?>
    </button>
    <?php } ?>

    <?php if( houzez_option('google_login') == 'yes' ) { ?>
    <button type="button" class="hz-google-login btn btn-google-plus-lined w-100">
        <?php get_template_part('template-parts/loader'); ?>
        <img class="google-icon" src="<?php echo HOUZEZ_IMAGE; ?>Google__G__Logo.svg" alt="<?php esc_html_e( 'Sign in with google', 'houzez' ); ?>"/> <?php esc_html_e( 'Sign in with google', 'houzez' ); ?>
    </button>
    <?php } ?>
</div>
<?php } ?>

<?php } else {
    esc_html_e('User registration is disabled for demo purpose.', 'houzez');
} ?>