<div class="block-wrap profile-card-wrap">
    <div class="profile-card">
        <div class="profile-img text-center mb-4">
            <?php
            global $houzez_local;
            $userID = get_current_user_id();
            if (isset($_GET['edit_user']) && is_numeric($_GET['edit_user'])) {
                $userID = intval($_GET['edit_user']); // Sanitize the input
            }

            $user_custom_picture    =   get_the_author_meta( 'fave_author_custom_picture' , $userID );
            $author_picture_id      =   get_the_author_meta( 'fave_author_picture_id' , $userID );
            $user_default_currency  =   get_the_author_meta( 'fave_author_currency' , $userID );
            if($user_custom_picture =='' ) {
                $user_custom_picture = HOUZEZ_IMAGE. 'profile-avatar.png';
            }
            
            // Get user information
            $first_name             =   get_the_author_meta( 'first_name' , $userID );
            $last_name              =   get_the_author_meta( 'last_name' , $userID );
            $display_name           =   get_the_author_meta( 'display_name' , $userID );
            $user_title             =   get_the_author_meta( 'fave_author_title' , $userID );
            $user_email             =   get_the_author_meta( 'user_email' , $userID );
            $user_mobile            =   get_the_author_meta( 'fave_author_mobile' , $userID );
            $user_whatsapp          =   get_the_author_meta( 'fave_author_whatsapp' , $userID );
            $user_phone             =   get_the_author_meta( 'fave_author_phone' , $userID );
            $user_address           =   get_the_author_meta( 'fave_author_address' , $userID );
            
            // Get social media
            $facebook               =   get_the_author_meta( 'fave_author_facebook' , $userID );
            $twitter                =   get_the_author_meta( 'fave_author_twitter' , $userID );
            $linkedin               =   get_the_author_meta( 'fave_author_linkedin' , $userID );
            $instagram              =   get_the_author_meta( 'fave_author_instagram' , $userID );
            ?>
            
            <div id="houzez_profile_photo" class="profile-image">
            <?php
            if( !empty( $author_picture_id ) ) {
                echo '<a href="#" class="delete-profile-pic"><i class="houzez-icon icon-close"></i></a>';
                $author_picture_id = intval( $author_picture_id );
                if ( $author_picture_id ) {
                    echo wp_get_attachment_image( $author_picture_id, 'large', "", array( "class" => "img-fluid rounded-circle", "width" => "150" ) );
                    echo '<input type="hidden" class="profile-pic-id" id="profile-pic-id" name="profile-pic-id" value="' . esc_attr( $author_picture_id ).'"/>';
                }
            } else {
                print '<img class="img-fluid rounded-circle" id="profile-image" src="'.esc_url( $user_custom_picture ).'" alt="user image" width="150">';
            }
            ?>
            </div>
        </div>

        <div class="profile-info">
            <div class="profile-name text-center border-bottom pb-3 mb-4">
            <h4 class="mb-1"><?php echo esc_html($display_name); ?></h4>
            <p class="text-muted mb-1"><?php echo esc_html($user_title); ?></p>
            
            <button id="select_user_profile_photo" type="button" class="btn btn-primary btn-sm mt-3 w-100">
                <?php echo esc_html__('Update Profile Picture', 'houzez'); ?>
            </button>
            <small class="d-block text-muted mt-1 mb-0"><?php echo esc_html__('Minimum size 300 x 300 px', 'houzez'); ?></small>
            <div id="upload_errors"></div>
        </div>

        <div class="contact-info border-bottom pb-3 mb-4">
            <h6 class="text-uppercase small fw-bold mb-3"><?php echo esc_html__('Contact Information', 'houzez'); ?></h6>
            <div class="d-flex align-items-center mb-2">
                <i class="houzez-icon icon-envelope text-muted me-2"></i>
                <span class="text-break"><?php echo esc_html($user_email); ?></span>
            </div>
            <?php if(!empty($user_whatsapp)) { ?>
            <div class="d-flex align-items-center mb-2">
                <i class="houzez-icon icon-messaging-whatsapp text-muted me-2"></i>
                <span><?php echo esc_html($user_whatsapp); ?></span>
            </div>
            <?php } elseif(!empty($user_mobile)) { ?>
            <div class="d-flex align-items-center mb-2">
                <i class="houzez-icon icon-phone text-muted me-2"></i>
                <span><?php echo esc_html($user_mobile); ?></span>
            </div>
            <?php } ?>
            <?php if(!empty($user_address)) { ?>
            <div class="d-flex align-items-center">
                <i class="houzez-icon icon-pin text-muted me-2"></i>
                <span><?php echo esc_html($user_address); ?></span>
            </div>
            <?php } ?>
        </div>

        <div class="social-links">
            <h6 class="text-uppercase small fw-bold mb-3"><?php echo esc_html__('Social Media', 'houzez'); ?></h6>
            <div class="d-flex gap-3">
                <?php if(!empty($facebook)) { ?>
                <a href="<?php echo esc_url($facebook); ?>" target="_blank" class="text-muted"><i class="houzez-icon icon-social-media-facebook"></i></a>
                <?php } ?>
                <?php if(!empty($twitter)) { ?>
                <a href="<?php echo esc_url($twitter); ?>" target="_blank" class="text-muted"><i class="houzez-icon icon-x-logo-twitter-logo-2"></i></a>
                <?php } ?>
                <?php if(!empty($linkedin)) { ?>
                <a href="<?php echo esc_url($linkedin); ?>" target="_blank" class="text-muted"><i class="houzez-icon icon-professional-network-linkedin"></i></a>
                <?php } ?>
                <?php if(!empty($instagram)) { ?>
                <a href="<?php echo esc_url($instagram); ?>" target="_blank" class="text-muted"><i class="houzez-icon icon-social-instagram"></i></a>
                <?php } ?>
            </div>
        </div>
    </div>
    </div>
</div>