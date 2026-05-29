<?php
global $houzez_local;
$current_user = wp_get_current_user();
$userID = get_current_user_id();
$is_agency_agent = false;

if (isset($_GET['edit_user']) && is_numeric($_GET['edit_user'])) {
    $userID = intval($_GET['edit_user']); // Sanitize the input
    $current_user = get_userdata($userID);
    $is_agency_agent = true;
} 

$username               =   get_the_author_meta( 'user_login' , $userID );
$user_title             =   get_the_author_meta( 'fave_author_title' , $userID );
$first_name             =   get_the_author_meta( 'first_name' , $userID );
$last_name              =   get_the_author_meta( 'last_name' , $userID );
$user_email             =   get_the_author_meta( 'user_email' , $userID );
$user_mobile            =   get_the_author_meta( 'fave_author_mobile' , $userID );
$user_whatsapp          =   get_the_author_meta( 'fave_author_whatsapp' , $userID );
$telegram               =   get_the_author_meta( 'fave_author_telegram' , $userID );
$line_id                =   get_the_author_meta( 'fave_author_line_id' , $userID );
$user_phone             =   get_the_author_meta( 'fave_author_phone' , $userID );
$description            =   get_the_author_meta( 'description' , $userID );
$userlangs              =   get_the_author_meta( 'fave_author_language' , $userID );
$user_company           =   get_the_author_meta( 'fave_author_company' , $userID );
$tax_number             =   get_the_author_meta( 'fave_author_tax_no' , $userID );
$fax_number             =   get_the_author_meta( 'fave_author_fax' , $userID );
$user_address           =   get_the_author_meta( 'fave_author_address' , $userID );
$service_areas          =   get_the_author_meta( 'fave_author_service_areas' , $userID );
$specialties            =   get_the_author_meta( 'fave_author_specialties' , $userID );
$license                =   get_the_author_meta( 'fave_author_license' , $userID );
$gdpr_agreement         =   get_the_author_meta( 'gdpr_agreement' , $userID );

if( houzez_is_agency() ) {
    $title_position_lable = esc_html__('Agency Name','houzez');
    $about_lable = esc_html__( 'About Agency', 'houzez' );
} else {
    $title_position_lable =  esc_html__('Title / Position','houzez');
    $about_lable = esc_html__( 'About me', 'houzez' );
}
?>
<div id="profile_message"></div>
<div class="block-wrap">
    <div class="block-title-wrap">
        <h2><?php esc_html_e( 'Information', 'houzez' ); ?></h2>
    </div>
    <div class="profile-form">
        <div class="row">
            <?php if( houzez_show_profile_field('username') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Username','houzez');?></label>
                <input disabled type="text" name="username" class="form-control" value="<?php echo esc_attr( $username );?>" placeholder="<?php esc_html_e('Enter your username','houzez');?>">
            </div>
            <?php endif; ?>

            <?php if( houzez_show_profile_field('email') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Email','houzez');?></label>
                <input type="email" name="useremail" class="form-control" value="<?php echo esc_attr( $user_email );?>" placeholder="<?php esc_html_e('Enter your email address','houzez');?>">
            </div>
            <?php endif; ?>

            <?php if( (!houzez_is_agency() || $is_agency_agent) && houzez_show_profile_field('first_name') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('First Name','houzez');?></label>
                <input type="text" name="firstname" class="form-control" value="<?php echo esc_attr( $first_name );?>" placeholder="<?php esc_html_e('Enter your first name','houzez');?>">
            </div>
            <?php endif; ?>
            
            <?php if( (!houzez_is_agency() || $is_agency_agent) && houzez_show_profile_field('last_name') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Last Name','houzez');?></label>
                <input type="text" name="lastname" class="form-control" value="<?php echo esc_attr( $last_name );?>" placeholder="<?php esc_html_e('Enter your last name','houzez');?>">
            </div>
            <?php endif; ?>

            <?php if( houzez_show_profile_field('display_name') ): ?>
            <div class="col-md-6 mb-3">
                <label class="form-label"><?php esc_html_e('Select Your Public Name', 'houzez'); ?></label>
                <select name="display_name" class="form-control" id="display_name" data-live-search="false">
                    <?php
                        $public_display = array();
                        $public_display['display_username']  = $current_user->user_login;
                        $public_display['display_nickname']  = $current_user->nickname;
                        
                        if(!empty($current_user->first_name)) {
                            $public_display['display_firstname'] = $current_user->first_name;
                        }
                        
                        if(!empty($current_user->last_name)) {
                            $public_display['display_lastname'] = $current_user->last_name;
                        }
                        
                        if(!empty($current_user->first_name) && !empty($current_user->last_name) ) {
                            $public_display['display_firstlast'] = $current_user->first_name . ' ' . $current_user->last_name;
                            $public_display['display_lastfirst'] = $current_user->last_name . ' ' . $current_user->first_name;
                        }
                        
                        if(!in_array( $current_user->display_name, $public_display)) {
                            $public_display = array( 'display_displayname' => $current_user->display_name ) + $public_display;
                            $public_display = array_map( 'trim', $public_display );
                            $public_display = array_unique( $public_display );
                        }

                        foreach ($public_display as $id => $item) {
                    ?>
                        <option id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($item); ?>"<?php selected( $current_user->display_name, $item ); ?>><?php echo esc_attr($item); ?></option>
                    <?php
                        }
                    ?>
                </select>
            </div>
            <?php endif; ?>

            <?php if(houzez_not_buyer()) { ?>
                <?php if( houzez_show_profile_field('title') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo esc_attr($title_position_lable); ?></label>
                    <input type="text" name="title" value="<?php echo esc_attr( $user_title );?>" class="form-control" placeholder="<?php esc_html_e('Enter your job position','houzez');?>">
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('license') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('License','houzez');?></label>
                    <input type="text" name="license" value="<?php echo esc_attr( $license );?>" class="form-control" placeholder="<?php esc_html_e('Enter your license','houzez');?>">
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('mobile') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('Mobile Number','houzez');?></label>
                    <input type="tel" name="usermobile" class="form-control" value="<?php echo esc_attr( $user_mobile );?>" placeholder="<?php esc_html_e('Enter your mobile number','houzez');?>">
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('phone') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('Office Number','houzez');?></label>
                    <input type="text" name="userphone" class="form-control" value="<?php echo esc_attr( $user_phone );?>" placeholder="<?php esc_html_e('Enter your phone number','houzez');?>">
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('whatsapp') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('WhatsApp','houzez');?></label>
                    <input type="text" name="whatsapp" class="form-control" value="<?php echo esc_attr( $user_whatsapp );?>" placeholder="<?php esc_html_e('Enter your whatsapp number','houzez');?>">
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('telegram') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e( 'Telegram', 'houzez' ); ?></label>
                    <input class="form-control" name="telegram" value="<?php echo esc_attr( $telegram );?>" placeholder="<?php esc_html_e( 'Enter your telegram number', 'houzez' ); ?>" type="text">
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('line_id') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e( 'LINE ID', 'houzez' ); ?></label>
                    <input class="form-control" name="line_id" value="<?php echo esc_attr( $line_id );?>" placeholder="<?php esc_html_e( 'Enter your line id', 'houzez' ); ?>" type="text">
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('fax') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('Fax Number','houzez');?></label>
                    <input type="text" name="fax_number" class="form-control" value="<?php echo esc_attr( $fax_number );?>" placeholder="<?php esc_html_e('Enter your fax number','houzez');?>">
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('tax_number') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('Tax Number','houzez');?></label>
                    <input type="text" name="tax_number" value="<?php echo esc_attr( $tax_number );?>" class="form-control" placeholder="<?php esc_html_e('Enter your tax number','houzez');?>">
                </div>
                <?php endif; ?>
                
                <?php if( houzez_show_profile_field('language') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('Language','houzez');?></label>
                    <input type="text" name="userlangs" placeholder="<?php echo esc_html__('English, Spanish, French', 'houzez'); ?>" class="form-control" value="<?php echo esc_attr( $userlangs );?>">
                </div>
                <?php endif; ?>

                <?php if( (!houzez_is_agency() || $is_agency_agent) && houzez_show_profile_field('company') ): ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php esc_html_e('Company Name','houzez');?></label>
                    <input type="text" name="user_company" placeholder="<?php esc_html_e('Enter your company name','houzez');?>" class="form-control" value="<?php echo esc_attr( $user_company );?>">
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('address') ): ?>
                <div class="col-12 mb-3">
                    <label class="form-label"><?php esc_html_e( 'Address', 'houzez' ); ?></label>
                    <textarea name="user_address" class="form-control" rows="4" placeholder="<?php esc_html_e('Enter your address','houzez');?>"><?php echo esc_attr( $user_address );?></textarea>
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('service_areas') ): ?>
                <div class="col-12 mb-3">
                    <label class="form-label"><?php esc_html_e( 'Service Areas', 'houzez' ); ?></label>
                    <textarea name="service_areas" class="form-control" rows="4" placeholder="<?php esc_html_e('Enter your service areas','houzez');?>"><?php echo esc_attr( $service_areas );?></textarea>
                </div>
                <?php endif; ?>

                <?php if( houzez_show_profile_field('specialties') ): ?>
                <div class="col-12 mb-3">
                    <label class="form-label"><?php esc_html_e( 'Specialties', 'houzez' ); ?></label>
                    <textarea name="specialties" class="form-control" rows="4" placeholder="<?php esc_html_e('Enter your specialties','houzez');?>"><?php echo esc_attr( $specialties );?></textarea>
                </div>
                <?php endif; ?>

            <?php } ?>
            
            <?php if( houzez_show_profile_field('about') ): ?>
            <div class="col-12 mb-3">
                <label class="form-label"><?php echo esc_attr($about_lable); ?></label>
                <?php
                $editor_id = 'about';
                $settings = array(
                    'media_buttons' => false,
                    'textarea_rows' => 6,
                );
                if ( !empty($description) ) {
                    wp_editor($description, $editor_id, $settings);
                } else {
                    wp_editor('', $editor_id, $settings);
                }
                ?>
            </div>
            <?php endif; ?>
            
            <div class="col-12">
                <button class="houzez_update_profile btn btn-primary">
                    <?php get_template_part('template-parts/loader'); ?>
                    <?php esc_html_e('Save Changes', 'houzez'); ?>
                </button>
                <div class="notify mt-2"></div>
            </div>
        </div><!-- row -->
    </div><!-- profile-form -->
</div><!-- block-wrap -->