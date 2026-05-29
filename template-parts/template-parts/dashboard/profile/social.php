<?php
global $houzez_local;
$userID = get_current_user_id();
if (isset($_GET['edit_user']) && is_numeric($_GET['edit_user'])) {
    $userID = intval($_GET['edit_user']); // Sanitize the input
}

$facebook               =   get_the_author_meta( 'fave_author_facebook' , $userID );
$twitter                =   get_the_author_meta( 'fave_author_twitter' , $userID );
$linkedin               =   get_the_author_meta( 'fave_author_linkedin' , $userID );
$pinterest              =   get_the_author_meta( 'fave_author_pinterest' , $userID );
$instagram              =   get_the_author_meta( 'fave_author_instagram' , $userID );
$googleplus             =   get_the_author_meta( 'fave_author_googleplus' , $userID );
$youtube                =   get_the_author_meta( 'fave_author_youtube' , $userID );
$tiktok                 =   get_the_author_meta( 'fave_author_tiktok' , $userID );
$vimeo                  =   get_the_author_meta( 'fave_author_vimeo' , $userID );
$zillow_url             =   get_the_author_meta( 'fave_author_zillow' , $userID );
$realtor_com_url        =   get_the_author_meta( 'fave_author_realtor_com' , $userID );
$user_skype             =   get_the_author_meta( 'fave_author_skype' , $userID );
$website_url            =   get_the_author_meta( 'user_url' , $userID );

// Check if any social media fields are enabled
$show_social_section = false;
$social_fields = ['facebook', 'twitter', 'linkedin', 'instagram', 'youtube', 'pinterest', 'googleplus', 'vimeo', 'skype', 'tiktok', 'zillow', 'realtor_com', 'website'];
foreach($social_fields as $field) {
    if(houzez_show_profile_field($field)) {
        $show_social_section = true;
        break;
    }
}

if($show_social_section): ?>
<div class="block-wrap">
    <div class="block-title-wrap">
        <h2><?php esc_html_e('Social Media','houzez');?></h2>
    </div>
    <div class="block-content-wrap">
        <div class="row">
            <?php if( houzez_show_profile_field('facebook') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Facebook', 'houzez'); ?></label>
                <input type="url" class="form-control" name="facebook" value="<?php echo esc_url($facebook); ?>" placeholder="<?php esc_html_e('Enter your Facebook profile URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>
            
            <?php if( houzez_show_profile_field('twitter') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('X (Twitter)', 'houzez'); ?></label>
                <input type="url" class="form-control" name="twitter" value="<?php echo esc_url($twitter); ?>" placeholder="<?php esc_html_e('Enter your X (Twitter) profile URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>
            
            <?php if( houzez_show_profile_field('linkedin') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('LinkedIn', 'houzez'); ?></label>
                <input type="url" class="form-control" name="linkedin" value="<?php echo esc_url($linkedin); ?>" placeholder="<?php esc_html_e('Enter your LinkedIn profile URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>
            
            <?php if( houzez_show_profile_field('instagram') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Instagram', 'houzez'); ?></label>
                <input type="url" class="form-control" name="instagram" value="<?php echo esc_url($instagram); ?>" placeholder="<?php esc_html_e('Enter your Instagram profile URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>
            
            <?php if( houzez_show_profile_field('youtube') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('YouTube', 'houzez'); ?></label>
                <input type="url" class="form-control" name="youtube" value="<?php echo esc_url($youtube); ?>" placeholder="<?php esc_html_e('Enter your YouTube channel URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>
            
            <?php if( houzez_show_profile_field('pinterest') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Pinterest', 'houzez'); ?></label>
                <input type="url" class="form-control" name="pinterest" value="<?php echo esc_url($pinterest); ?>" placeholder="<?php esc_html_e('Enter your Pinterest profile URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>
            
            <?php if( houzez_show_profile_field('googleplus') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Google', 'houzez'); ?></label>
                <input type="url" class="form-control" name="googleplus" value="<?php echo esc_url($googleplus); ?>" placeholder="<?php esc_html_e('Enter your Google URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>
            
            <?php if( houzez_show_profile_field('vimeo') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Vimeo', 'houzez'); ?></label>
                <input type="url" class="form-control" name="vimeo" value="<?php echo esc_url($vimeo); ?>" placeholder="<?php esc_html_e('Enter your Vimeo URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>
            
            <?php if( houzez_show_profile_field('skype') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Skype', 'houzez'); ?></label>
                <input type="text" class="form-control" name="userskype" value="<?php echo esc_attr($user_skype); ?>" placeholder="<?php esc_html_e('Enter your Skype ID', 'houzez'); ?>">
            </div>
            <?php endif; ?>

            <?php if( houzez_show_profile_field('tiktok') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('TikTok', 'houzez'); ?></label>
                <input type="url" class="form-control" name="tiktok" value="<?php echo esc_url($tiktok); ?>" placeholder="<?php esc_html_e('Enter your TikTok profile URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>

            <?php if( houzez_show_profile_field('zillow') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Zillow Profile', 'houzez'); ?></label>
                <input type="url" class="form-control" name="zillow" value="<?php echo esc_url($zillow_url); ?>" placeholder="<?php esc_html_e('Enter your Zillow profile URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>

            <?php if( houzez_show_profile_field('realtor_com') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Realtor.com Profile', 'houzez'); ?></label>
                <input type="url" class="form-control" name="realtor_com" value="<?php echo esc_url($realtor_com_url); ?>" placeholder="<?php esc_html_e('Enter your Realtor.com profile URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>

            <?php if( houzez_show_profile_field('website') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Website', 'houzez'); ?></label>
                <input type="url" class="form-control" name="website_url" value="<?php echo esc_url($website_url); ?>" placeholder="<?php esc_html_e('Enter your website URL', 'houzez'); ?>">
            </div>
            <?php endif; ?>
            
            <div class="col-12">
                <button type="submit" class="houzez_update_profile btn btn-primary">
                    <?php get_template_part('template-parts/loader'); ?>
                    <?php esc_html_e('Save Changes', 'houzez'); ?>
                </button>
                <div class="notify mt-2"></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>