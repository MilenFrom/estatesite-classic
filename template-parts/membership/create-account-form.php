
<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('First Name', 'houzez'); ?></label>
            <input type="text" name="first_name" class="form-control" placeholder="<?php esc_html_e('Enter your first name', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Last Name', 'houzez'); ?></label>
            <input type="text" name="last_name" class="form-control" placeholder="<?php esc_html_e('Enter your last name', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Username *', 'houzez'); ?> </label>
            <input type="text" name="username" class="form-control" placeholder="<?php esc_html_e('Enter username', 'houzez'); ?> ">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Email *', 'houzez'); ?> </label>
            <input type="email" name="useremail" class="form-control" placeholder="<?php esc_html_e('Enter your email address', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Password *', 'houzez'); ?> </label>
            <input type="password" name="register_pass" class="form-control" placeholder="<?php esc_html_e('Password', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Confirm Password *', 'houzez'); ?> </label>
            <input type="password" name="register_pass_retype" class="form-control" placeholder="<?php esc_html_e('Confirm Password', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->

    <?php
    $user_show_roles = houzez_option('user_show_roles');
    $show_hide_roles = houzez_option('show_hide_roles');

    if($user_show_roles != 0) { ?>
    <div class="col-md-12 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Account Type *', 'houzez'); ?></label>
            <select name="user_role" class="form-select" required>
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
                if( isset($show_hide_roles['seller']) && $show_hide_roles['seller'] != 1 ) {
                    echo '<option value="houzez_seller">'.houzez_option('seller_role').'</option>';
                }
                ?>
            </select>
        </div>
    </div><!-- col-md-12 col-sm-12 -->
    <?php } ?>

</div><!-- row -->
<?php do_action( 'houzez_register_form_fields'); ?>

<?php get_template_part('template-parts/captcha'); ?>

<?php wp_nonce_field( 'houzez_register_nonce2', 'houzez_register_security2' ); ?>
<input type="hidden" name="action" value="houzez_register_user_with_membership">

