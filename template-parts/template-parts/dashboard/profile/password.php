<div class="block-wrap">
    <div class="block-title-wrap">
        <h2><?php esc_html_e('Change Password','houzez'); ?></h2>
    </div>
    <div class="block-content-wrap">
        <form>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('New Password','houzez'); ?></label>
                    <input type="password" class="form-control" id="newpass" placeholder="<?php esc_html_e('Enter your new password','houzez'); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('Confirm New Password','houzez'); ?></label>
                    <input type="password" class="form-control" id="confirmpass" placeholder="<?php esc_html_e('Enter your new password again','houzez'); ?>">
                </div>
                <div class="col-12">
                    <?php wp_nonce_field( 'houzez_pass_ajax_nonce', 'houzez-security-pass' );   ?>
                    <button id="houzez_change_pass" class="btn btn-primary">
                        <?php get_template_part('template-parts/loader'); ?>
                        <?php esc_html_e('Update Password','houzez'); ?>
                    </button>
                    <div id="password_reset_msgs" class="notify mt-2"></div>
                </div>
            </div>
        </form>
    </div>
</div>