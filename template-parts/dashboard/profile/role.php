<?php
$userID = get_current_user_id();
$edit_flag = false;
if (isset($_GET['edit_user']) && is_numeric($_GET['edit_user'])) {
    $userID = intval($_GET['edit_user']); // Sanitize the input
    $edit_flag = true;
} 
$user_data         = get_userdata($userID);
$role              = $user_data->roles[0];
$show_hide_roles = houzez_option('show_hide_roles');

if( ( houzez_option('user_show_roles_profile') != 0 && !houzez_is_admin() && ! houzez_is_manager() && ! $edit_flag ) ) { ?>
<div class="block-wrap">
    <div class="block-title-wrap">
        <h2><?php esc_html_e( 'Account Role', 'houzez' ); ?></h2>
    </div>
    <div class="block-content-wrap">
        <form>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <?php wp_nonce_field( 'houzez_role_pass_ajax_nonce', 'houzez-role-security-pass' );   ?>
                    <select name="houzez_user_role" id="houzez_user_role" class="selectpicker form-control" data-live-search="false" data-live-search-style="begins" title="">
                        <option value=""><?php esc_html_e( 'Select Role', 'houzez' )?></option>
                        <?php
                        if( $show_hide_roles['agent'] != 1 ) {
                            echo '<option value="houzez_agent" '.selected( 'houzez_agent', $role  ).'> '.houzez_option('agent_role').' </option>';
                        }
                        if( $show_hide_roles['agency'] != 1 ) {
                            echo '<option value="houzez_agency" ' . selected('houzez_agency', $role) . '> ' . houzez_option('agency_role') . ' </option>';
                        }
                        if( $show_hide_roles['owner'] != 1 ) {
                            echo '<option value="houzez_owner" ' . selected('houzez_owner', $role) . '> ' . houzez_option('owner_role') . '  </option>';
                        }
                        if( $show_hide_roles['buyer'] != 1 ) {
                            echo '<option value="houzez_buyer" ' . selected('houzez_buyer', $role) . '> ' . houzez_option('buyer_role') . '  </option>';
                        }
                        if( $show_hide_roles['seller'] != 1 ) {
                            echo '<option value="houzez_seller" ' . selected('houzez_seller', $role) . '> ' . houzez_option('seller_role') . '  </option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
<?php } ?>