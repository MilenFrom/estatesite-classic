<?php
$currentUserID = get_current_user_id();

$permissionToEdit = true;
if( isset($_GET['edit_user']) && $_GET['edit_user'] != '' && ! current_user_can('administrator') ) {
    $agency_id = get_user_meta(intval($_GET['edit_user']), 'fave_agent_agency', true);
    if ($agency_id != $currentUserID ) {
        $permissionToEdit = false;
    }
}

$id_flag = false;
if( isset( $_GET['edit_user'] ) && !empty( $_GET['edit_user'] ) ) {
    $currentUserID = intval($_GET['edit_user']);
    $id_flag = true;
}
$user_agent_id = get_user_meta( $currentUserID, 'fave_author_agent_id', true );
$user_agency_id = get_user_meta( $currentUserID, 'fave_author_agency_id', true );
if(houzez_is_agency()){
    $id_for_permalink = $user_agency_id;
} elseif(houzez_is_agent()) {
    $id_for_permalink = $user_agent_id;
}

if( !empty( $id_for_permalink ) ) {
    if( 'publish' == get_post_status ( $id_for_permalink ) ) {
        $agent_permalink = get_permalink($id_for_permalink);
    } else {
        $agent_permalink = get_author_posts_url( $currentUserID );
    }

} else {
    $agent_permalink = get_author_posts_url( $currentUserID );
}
?>
<div class="container">
  <div class="heading mb-4 d-flex justify-content-between align-items-center">
    <div class="heading-text">
      <h2><?php echo houzez_option('dsh_profile', 'My profile'); ?></h2>
    </div>
    <div class="add-export-btn">
      <ul class="d-flex align-items-center gap-2">
        <?php if(houzez_not_buyer()) { ?>
            <li>
                <a href="<?php echo esc_url($agent_permalink); ?>" target="_blank" class="btn btn-primary">
                    <i class="houzez-icon icon-share-2 me-2"></i>
                    <?php esc_html_e('View Public Profile','houzez');?>
                </a>
            </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
        <?php
        if( !$permissionToEdit ){
            echo '
            <div class="col-md-12 col-lg-12 col-xl-12">
                <div class="alert alert-danger" role="alert"><i class="houzez-icon icon-check-circle-1 me-1"></i> '.esc_html__('You do not have permission to access this.', 'houzez').'</div>
            </div>';
        } else { ?>

        <div class="col-md-12 col-lg-4 col-xl-3">
            <?php get_template_part('template-parts/dashboard/profile/card'); ?>
        </div>

        <div class="col-md-12 col-lg-8 col-xl-9">
            <form method="post">
                <?php get_template_part('template-parts/dashboard/profile/information'); ?>

                <?php get_template_part('template-parts/dashboard/profile/social'); ?>
                <?php wp_nonce_field( 'houzez_profile_ajax_nonce', 'houzez-security-profile' ); ?>
                <input type="hidden" name="action" value="houzez_ajax_update_profile">
                <?php if( $id_flag ) { ?>
                <input type="hidden" id="user_id" name="user_id" value="<?php echo intval($currentUserID); ?>">
                <?php } ?>
            </form>

            <?php get_template_part('template-parts/dashboard/profile/package'); ?>

            <?php get_template_part('template-parts/dashboard/profile/role'); ?>

            <?php get_template_part('template-parts/dashboard/profile/currency'); ?>

            <?php get_template_part('template-parts/dashboard/profile/password'); ?>

            <?php get_template_part('template-parts/dashboard/profile/delete-account'); ?>
        </div>
        <?php } ?>
  </div>
</div>