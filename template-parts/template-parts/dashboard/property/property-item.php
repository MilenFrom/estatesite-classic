<?php
global $post, $houzez_local, $delete_properties_nonce;
$post_id    = get_the_ID();
$submit_link = houzez_dashboard_add_listing();
$listings_page = houzez_dashboard_listings();
$edit_link  	= add_query_arg( 'edit_property', get_the_ID(), $submit_link );
$delete_link  	= add_query_arg( 'property_id', get_the_ID(), $listings_page );
$paid_submission_type  = houzez_option('enable_paid_submission');
$property_status = get_post_status ( $post->ID );
$payment_status = get_post_meta( get_the_ID(), 'fave_payment_status', true );

$payment_page = houzez_get_template_link('template/template-payment.php');
$insights_page = houzez_get_template_link_2('template/user_dashboard_insight.php');
$payment_page_link = add_query_arg( 'prop-id', $post_id, $payment_page );
$payment_page_link_featured = add_query_arg( 'upgrade_id', $post_id, $payment_page );
$insights_page_link = add_query_arg( 'listing_id', $post_id, $insights_page );
$fave_featured = get_post_meta( $post->ID, 'fave_featured', true );

$is_user_can_manage = houzez_is_admin() || houzez_is_editor();

if( $paid_submission_type == 'membership' ) {
    $put_on_hold_class = 'put-on-hold-package';
} else {
    $put_on_hold_class = 'put-on-hold';
}

if( $property_status == 'publish' ) {
    $status_badge = '<span class="dashboard-label bg-success">'.esc_html__('Approved', 'houzez').'</span>';
} elseif( $property_status == 'on_hold' ) {
    $status_badge = '<span class="dashboard-label bg-info">'.esc_html__('On Hold', 'houzez').'</span>';
} elseif( $property_status == 'houzez_sold' ) {
    $status_badge = '<span class="dashboard-label bg-danger">'.esc_html__('Sold', 'houzez').'</span>';
} elseif( $property_status == 'pending' ) {
    $status_badge = '<span class="dashboard-label bg-warning">'.esc_html__('Pending', 'houzez').'</span>';
} elseif( $property_status == 'expired' ) {
    $status_badge = '<span class="dashboard-label bg-danger">'.esc_html__('Expired', 'houzez').'</span>';
} elseif( $property_status == 'disapproved' ) {
    $status_badge = '<span class="dashboard-label bg-danger">'.esc_html__('Disapproved', 'houzez').'</span>';
} elseif( $property_status == 'draft' ) {
    $status_badge = '<span class="dashboard-label bg-dark">'.esc_html__('Draft', 'houzez').'</span>';
} else {
    $status_badge = '';
}

$payment_status_label = '';
if( $property_status != 'expired' && $property_status != 'disapproved' ) {
    if ($paid_submission_type != 'no' && $paid_submission_type != 'membership' && $paid_submission_type != 'free_paid_listing' ) {
        if ($payment_status == 'paid') {
            $payment_status_label = '<span class="dashboard-label dashboard-label-small">' . esc_html__('PAID', 'houzez') . '</span>';
        } elseif ($payment_status == 'not_paid') {
            $payment_status_label = '<span class="dashboard-label dashboard-label-small">' . esc_html__('NOT PAID', 'houzez') . '</span>';
        } else {
            $payment_status_label = '';
        }
    } else {
        $payment_status_label = '';
    }
}
?>

<tr>
    <td data-label="<?php esc_html_e('Select', 'houzez'); ?>">
        <label class="control control--checkbox">
            <input type="checkbox" class="control control--checkbox checkbox-delete listing-bulk-delete" name="listing-bulk-delete[]" value="<?php echo intval($post->ID); ?>">
            <span class="control__indicator"></span>
        </label>
    </td>
  <td data-label="<?php echo esc_html__('Thumbnail', 'houzez'); ?>" class="px-0">
    <div class="image-holder">
      <?php echo $payment_status_label; ?>

      <?php if( $fave_featured ) { ?>
        <span class="dashboard-label dashboard-label-featured">
          <img src="<?php echo HOUZEZ_IMAGE; ?>full-star.svg" alt="star">
        </span>
      <?php } ?>
      
      <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
        <?php
        $thumbnail_size = 'thumbnail';
        if( has_post_thumbnail() && get_the_post_thumbnail(get_the_ID()) != '') {
            the_post_thumbnail($thumbnail_size);
        } else {
            houzez_image_placeholder( $thumbnail_size );
        }
        ?> 
      </a>
    </div>
  </td>
  <td data-label="<?php echo esc_html__('Title', 'houzez'); ?>">
    <div class="text-box">
      <a class="fw-bold" href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php the_title(); ?></a><br>
      <address class="mb-0"><?php echo houzez_get_listing_data('property_map_address'); ?></address> 
      <?php if( houzez_user_role_by_post_id($post_id) != 'administrator' && get_post_status ( $post_id ) == 'publish' ) { ?>
            <?php if( $paid_submission_type == 'membership' ) { ?>
            <span class="expire">
                <strong><?php echo esc_html__('Expiration:', 'houzez'); ?></strong> 
                <?php houzez_listing_expire(); ?>
                <?php houzez_featured_listing_expire(); ?>
            </span>
            <?php } else if($paid_submission_type != 'no' && houzez_option('per_listing_expire_unlimited') ) { ?>
            <span class="expire">
                <strong><?php echo esc_html__('Expiration:', 'houzez'); ?></strong> 
                <?php houzez_listing_expire(); ?>
                <?php houzez_featured_listing_expire(); ?>
            </span>
            <?php } ?>
      <?php } ?>
    </div>
  </td>
  
  <td data-label="<?php echo esc_html__('Status', 'houzez'); ?>"><?php echo houzez_taxonomy_simple('property_status'); ?></td>
  
  <td data-label="" class="px-2"><?php echo $status_badge; ?></td>
  
  <td data-label="<?php echo esc_html__('ID', 'houzez'); ?>"><?php echo houzez_get_listing_data('property_id'); ?></td>
  
  <td data-label="<?php echo esc_html__('Price', 'houzez'); ?>"><?php houzez_property_price_admin(); ?></td>
  
  <td data-label="<?php echo esc_html__('Type', 'houzez'); ?>" class="px-2"><?php echo houzez_taxonomy_simple('property_type'); ?></td>
  
  <td data-label="<?php echo esc_html__('Date', 'houzez'); ?>" class="px-2">
      <?php
      echo date_i18n( get_option('date_format'), strtotime( $post->post_date ) ).' '.date_i18n( get_option('time_format'), strtotime( $post->post_date ) );
      echo '<br>';
      echo ( empty( $post->post_author ) ? __( 'by a guest', 'houzez' ) : sprintf( __( 'by %s', 'houzez' ), '<a href="' . esc_url( add_query_arg( 'user', $post->post_author, $listings_page ) ) . '">' . get_the_author() . '</a>' ) );
      ?>
  </td>
  
  <td data-label="<?php echo esc_html__('Actions', 'houzez'); ?>" class="text-lg-center text-start px-0">
    <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php echo esc_html__('Actions', 'houzez'); ?>">
      <a href="javascript:void(0)" class="action-btn" data-bs-toggle="dropdown" aria-expanded="false"><i class="houzez-icon icon-navigation-menu-horizontal"></i></a>
      <ul class="dropdown-menu dropdown-menu3">
        <?php 
        if( class_exists('Fave_Insights') && !empty($insights_page) ) { ?>
        <li><a class="dropdown-item" href="<?php echo esc_url($insights_page_link); ?>">
          <i class="houzez-icon icon-analytics-bars-circle"></i> <?php esc_html_e('View Stats', 'houzez'); ?></a>
        </li>
        <?php 
        } ?>
        
        <?php if( $property_status != 'expired' ) { ?>
        <li>
          <a class="dropdown-item" href="<?php echo esc_url($edit_link); ?>">
          <i class="houzez-icon icon-pencil"></i> <?php esc_html_e('Edit', 'houzez'); ?></a>
        </li>
        <?php 
        } ?>

        <li>
          <a href="javascript:void(0)" class="delete-property dropdown-item" data-id="<?php echo intval($post->ID); ?>" data-nonce="<?php echo esc_attr($delete_properties_nonce); ?>">
          <i class="houzez-icon icon-bin"></i> <?php esc_html_e('Delete', 'houzez'); ?></a>
        </li>

        <li>
          <a class="clone-property dropdown-item" data-nonce="<?php echo wp_create_nonce('clone_property_nonce') ?>" data-property="<?php echo $post->ID; ?>" href="#">
          <i class="houzez-icon icon-real-estate-action-house-add"></i> <?php esc_html_e('Duplicate', 'houzez'); ?></a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <?php 
				if(houzez_is_published( $post->ID )) { ?>
          <li>
            <a href="javascript:void(0)" class="<?php echo esc_attr($put_on_hold_class); ?> dropdown-item" data-property="<?php echo intval($post->ID); ?>" data-nonce="<?php echo wp_create_nonce('puthold_property_nonce') ?>"> 
              <i class="houzez-icon icon-alert-triangle"></i> <?php esc_html_e('Put On Hold', 'houzez');?>
            </a>
          </li>
          <?php 
        } elseif (houzez_on_hold( $post->ID )) { ?>
          <li>
            <a href="javascript:void(0)" class="<?php echo esc_attr($put_on_hold_class); ?> dropdown-item" data-property="<?php echo intval($post->ID); ?>" data-nonce="<?php echo wp_create_nonce('puthold_property_nonce') ?>"> 
                <i class="houzez-icon icon-upload-button"></i> <?php esc_html_e('Go Live', 'houzez');?>
            </a>
          </li>
        <?php 
        } ?>

        <?php 
        if(houzez_is_published( $post->ID ) && houzez_option('enable_mark_as_sold', 0) ) { ?>
          <li>
            <a href="javascript:void(0)" class="mark_as_sold_js dropdown-item" data-property="<?php echo intval($post->ID); ?>" data-nonce="<?php echo wp_create_nonce('sold_property_nonce') ?>"> 
                <i class="houzez-icon icon-real-estate-sign-house-sold"></i> <?php esc_html_e('Mark as Sold', 'houzez');?>
            </a>
          </li>
        <?php 
        } ?>

        <?php
        if( $is_user_can_manage ) {

            if ( in_array( $post->post_status, array( 'pending', 'disapproved' ) ) ) { 
                echo '<li><a href="javascript:void(0)" data-propid="'.intval( $post->ID ).'" data-type="approve" class="dropdown-item houzez-prop-action-js">
                <i class="houzez-icon icon-check-circle-1"></i>' . esc_html__('Approve', 'houzez') . '</a></li>';
            }

            if ( in_array( $post->post_status, array( 'pending', 'publish' ) ) ) { 
                echo '<li><a href="javascript:void(0)" data-propid="'.intval( $post->ID ).'" data-type="disapprove" class="dropdown-item houzez-prop-action-js">
                <i class="houzez-icon icon-remove-circle"></i>' . esc_html__('Disapproved', 'houzez') . '</a></li>';
            }

            if ( in_array( $post->post_status, array( 'publish' ) ) && ! $fave_featured ) { 
                echo '<li><a href="javascript:void(0)" data-propid="'.intval( $post->ID ).'" data-type="set_featured" class="dropdown-item houzez-prop-action-js">
                <i class="houzez-icon icon-rating-star"></i>' . esc_html__('Mark as Featured', 'houzez') . '</a></li>';
            }

            if ( in_array( $post->post_status, array( 'publish' ) ) && $fave_featured ) { 
                echo '<li><a href="javascript:void(0)" data-propid="'.intval( $post->ID ).'" data-type="remove_featured" class="dropdown-item houzez-prop-action-js">
                <i class="houzez-icon icon-remove-circle"></i>' . esc_html__('Remove from Featured', 'houzez') . '</a></li>';
            }

            if ( in_array( $post->post_status, array( 'publish' ) ) ) { 
                echo '<li><a href="javascript:void(0)" data-propid="'.intval( $post->ID ).'" data-type="expire" class="dropdown-item houzez-prop-action-js">
                <i class="houzez-icon icon-calendar-3"></i> ' . esc_html__('Mark as Expired', 'houzez') . '</a></li>';
            }

            if ( in_array( $post->post_status, array( 'expired', 'houzez_sold', 'draft' ) ) ) { 
                echo '<li><a href="javascript:void(0)" data-propid="'.intval( $post->ID ).'" data-type="publish" class="dropdown-item houzez-prop-action-js">
                <i class="houzez-icon icon-upload-button"></i> ' . esc_html__('Publish', 'houzez') . '</a></li>';
            }

        } else {

            if( $paid_submission_type == 'per_listing' && $property_status != 'expired' ) {
                if ($payment_status != 'paid') {

                    echo '<li><hr class="dropdown-divider"></li>';
                    if( houzez_is_woocommerce() ) {
                        echo '<li><a href="javascript:void(0)" class="houzez-woocommerce-pay dropdown-item pay-btn" data-listid="'.intval($post_id).'"><i class="houzez-icon icon-real-estate-action-house-dollar"></i> ' . esc_html__('Pay Now', 'houzez') . '</a></li>';
                    } else {
                        echo '<li><a href="' . esc_url($payment_page_link) . '" class="dropdown-item pay-btn"><i class="houzez-icon icon-real-estate-action-house-dollar"></i> ' . esc_html__('Pay Now', 'houzez') . '</a></li>';
                    }
                } else {
                    if( houzez_get_listing_data('featured') != 1 && $property_status == 'publish' ) {

                        echo '<li><hr class="dropdown-divider"></li>';
                        if( houzez_is_woocommerce() ) {
                            echo '<li><a href="' . esc_url($payment_page_link_featured) . '" class="houzez-woocommerce-pay dropdown-item pay-btn" data-featured="1" data-listid="'.intval($post_id).'"><i class="houzez-icon icon-real-estate-action-house-star"></i> ' . esc_html__('Upgrade to Featured', 'houzez') . '</a></li>';
                        } else {
                            echo '<li><a href="' . esc_url($payment_page_link_featured) . '" class="dropdown-item pay-btn"><i class="houzez-icon icon-real-estate-action-house-star"></i> ' . esc_html__('Upgrade to Featured', 'houzez') . '</a></li>';
                        }
                        
                    }
                }
            }

            if( $property_status == 'expired' && ( $paid_submission_type == 'per_listing') ) {
                
                if( houzez_is_woocommerce() ) {
                    echo '<li><a href="javascript:void(0)" data-listid="'.intval($post_id).'" class="houzez-woocommerce-pay dropdown-item pay-btn"><i class="houzez-icon icon-real-estate-update-house-sync"></i> '.esc_html__( 'Re-List', 'houzez' ).'</a></li>';
                } else {

                    $payment_page_link_expired = add_query_arg( array('prop-id' => $post_id, 'mode' => 'relist'), $payment_page );
                    echo '<li><a href="' . esc_url($payment_page_link_expired) . '" class="dropdown-item pay-btn"><i class="houzez-icon icon-real-estate-update-house-sync"></i> '.esc_html__( 'Re-List', 'houzez' ).'</a></li>';
                }
                
            }

            if( $property_status == 'expired' && ( $paid_submission_type == 'free_paid_listing' || $paid_submission_type == 'no' ) ) {

                echo '<li><a href="javascript:void(0)" data-property="'.$post->ID.'" class="relist-free dropdown-item pay-btn"><i class="houzez-icon icon-real-estate-update-house-sync"></i> '.esc_html__( 'Re-List', 'houzez' ).'</a></li>';

            }

            // Publish draft property for agency/agent users
            if ( $property_status == 'draft' ) {
                $current_user_id = get_current_user_id();
                $post_author = $post->post_author;
                $is_owner = ($current_user_id == $post_author);

                // Check if agency owns this agent's property
                $agency_agents = array();
                if (houzez_is_agency()) {
                    $agents = houzez_get_agency_agents($current_user_id);
                    if (is_array($agents)) {
                        $agency_agents = $agents;
                    }
                }
                $is_agency_property = in_array($post_author, $agency_agents, true);

                if ($is_owner || $is_agency_property) {
                    $listings_admin_approved = houzez_option('listings_admin_approved');
                    $button_label = ($listings_admin_approved === 'yes')
                        ? esc_html__('Submit for Approval', 'houzez')
                        : esc_html__('Publish', 'houzez');

                    echo '<li><a href="javascript:void(0)" data-propid="'.intval($post->ID).'" data-type="submit_listing" class="dropdown-item houzez-prop-action-js"><i class="houzez-icon icon-upload-button"></i> ' . $button_label . '</a></li>';
                }
            }

            if( houzez_check_post_status( $post->ID ) ) {

                // Membership
                if ( $paid_submission_type == 'membership' && houzez_get_listing_data('featured') != 1 && $property_status == 'publish' ) {
                    // Check if user has featured listings available in their package
                    $current_user_id = get_current_user_id();
                    $featured_remaining = houzez_get_featured_remaining_listings($current_user_id);
                    
                    if( $featured_remaining > 0 ) {
                        echo '<li><a href="javascript:void(0)" data-proptype="membership" data-propid="'.intval( $post->ID ).'" class="make-prop-featured dropdown-item btn pay-btn"><i class="houzez-icon icon-rating-star"></i> ' . esc_html__('Set as Featured', 'houzez') . '</a></li>';
                    }
                }
                if ( $paid_submission_type == 'membership' && houzez_get_listing_data('featured') == 1 ) {
                    
                    echo '<li><a href="javascript:void(0)" data-proptype="membership" data-propid="'.intval( $post->ID ).'" class="remove-prop-featured dropdown-item btn pay-btn"><i class="houzez-icon icon-remove-circle"></i> ' . esc_html__('Remove From Featured', 'houzez') . '</a></li>';
                    
                }
                if( $property_status == 'expired' && $paid_submission_type == 'membership' ) {
                    
                    echo '<li><a href="javascript:void(0)" data-propid="'.intval( $post->ID ).'" class="resend-for-approval dropdown-item btn pay-btn"><i class="houzez-icon icon-real-estate-update-house-sync"></i> ' . esc_html__('Re-List', 'houzez') . '</a></li>';
                    
                }

                //Paid Featured
                if( $paid_submission_type == 'free_paid_listing' && $property_status == 'publish' ) {
                    
                    if( houzez_get_listing_data('featured') != 1 ) {

                        if( houzez_is_woocommerce() ) {
                            echo '<li><a href="javascript:void(0)" class="houzez-woocommerce-pay dropdown-item btn pay-btn" data-featured="1" data-listid="'.intval($post_id).'"><i class="houzez-icon icon-real-estate-action-house-star"></i>' . esc_html__('Upgrade to Featured', 'houzez') . '</a></li>';
                        } else {
                            echo '<li><a href="' . esc_url($payment_page_link_featured) . '" class="dropdown-item btn pay-btn"><i class="houzez-icon icon-real-estate-action-house-star"></i>' . esc_html__('Upgrade to Featured', 'houzez') . '</a></li>';
                        }
                    }
                    
                }

            }
        }
        ?> 
      </ul> 
    </div>
  </td>
</tr> 