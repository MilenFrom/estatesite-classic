<div class="modal fade reset-password-form" id="reset-password-form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title fw-normal"><?php esc_html_e( 'Forgot Password', 'houzez' ); ?></div>
                <button type="button" class="btn close ms-auto" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div><!-- modal-header -->
            <div class="modal-body">
                <div id="reset_pass_msg"></div>
                <p><?php esc_html_e( 'Please enter your username or email address. You will receive a link to create a new password via email.', 'houzez' ); ?></p>
                <form id="houzez_forgot_password_form" onsubmit="return false;">
                    <div class="form-group mb-3">
                        <input type="text" class="form-control forgot-password" name="user_login" id="user_login" placeholder="<?php esc_html_e( 'Enter your username or email address', 'houzez' ); ?>">
                    </div>
                    <?php wp_nonce_field( 'fave_resetpassword_nonce', 'fave_resetpassword_security' ); ?>
                    <button type="submit" id="houzez_forgetpass" class="btn-reset-password btn btn-primary w-100">
                        <?php get_template_part('template-parts/loader'); ?>
                        <?php esc_html_e( 'Submit', 'houzez' ); ?>
                    </button>
                </form>
            </div><!-- modal-body -->
        </div><!-- modal-content -->
    </div><!-- modal-dialog -->
</div><!-- login-register-form -->