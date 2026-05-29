<?php
$userID = get_current_user_id();
$edit_user = isset( $_GET['edit_user'] ) ? sanitize_text_field($_GET['edit_user']) : false;
$can_use_package = get_user_meta( $userID, 'houzez_is_agent_can_use_agency_package', true );
if( ! $edit_user && ( houzez_is_agency() || houzez_is_admin() ) ) { ?>
<div class="block-wrap">
    <div class="block-title-wrap">
        <h2><?php esc_html_e( 'Allow agents to use package', 'houzez' ); ?></h2>
    </div>
    <div class="block-content-wrap">
        <form>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <?php wp_nonce_field( 'houzez_agency_package_nonce', 'houzez-agency-package-security' );   ?>
                    <select name="houzez_user_package" id="houzez_user_package" class="selectpicker form-control" data-live-search="false" data-live-search-style="begins" title="">
                        <option value=""> <?php esc_html_e( 'Choose', 'houzez' ); ?> </option>
                        <option value="yes" <?php selected( 'yes', $can_use_package); ?>> <?php esc_html_e( 'Allow', 'houzez' ); ?> </option>
                        <option value="no" <?php selected( 'no', $can_use_package); ?>> <?php esc_html_e( 'Not Allow', 'houzez' ); ?> </option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
<?php } ?>