<?php
$activities = Houzez_Activities::get_activities();
$total_records = $activities['data']['total_records'];
$allowed_html_array = array(
    'i' => array(
        'class' => array()
    ),
    'strong' => array(),
    'a' => array(
        'href' => array(),
        'title' => array(),
        'target' => array()
    )
);
?>

<div class="heading d-flex align-items-center justify-content-between">
  <div class="heading-text">
    <h2><?php echo houzez_option('dsh_activities', 'Activities'); ?></h2> 
  </div> 
</div> 

<div class="houzez-data-content mt-4">  
    <div class="houzez-table-filters p-3">
      <form>
        <div class="d-flex align-items-center gap-2">
          <select id="activity_bulk_action" class="form-select">
            <option><?php esc_html_e('Bulk Actions', 'houzez'); ?></option>
            <option value="delete"><?php esc_html_e('Delete', 'houzez'); ?></option>
          </select>
          <a id="activity_bulk_apply" class="btn btn-primary" href="javascript:void(0)"><?php esc_html_e('Apply', 'houzez'); ?></a>
        </div>
      </form>
    </div> 
    
    <div class="houzez-data-table">
      <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
          <thead>
            <tr>
              <th data-label="<?php esc_html_e('Select', 'houzez'); ?>">
                  <label class="control control--checkbox">
                      <input id="activity_select_all" name="activity_multicheck" type="checkbox" class="activity-bulk-delete control control--checkbox">
                      <span class="control__indicator"></span>
                  </label>
              </th>
              <th class="w-100" data-label="<?php esc_html_e('Activity', 'houzez'); ?>"><?php esc_html_e('Activity', 'houzez'); ?></th>
              <th class="text-center" data-label=""></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach( $activities['data']['results'] as $activity ) {
                $permalink = $title = '';
                $meta = maybe_unserialize($activity->meta);
                // Interpret database time as UTC
                $datetime = houzez_mysql_to_wp_timestamp( $activity->time, 'utc' );
                $activity_id = $activity->activity_id;
                $type = isset($meta['type']) ? $meta['type'] : '';
                $subtype = isset($meta['subtype']) ? $meta['subtype'] : '';
            ?>
            <tr>
              <td data-label="<?php esc_html_e('Select', 'houzez'); ?>">
                  <label class="control control--checkbox">
                      <input type="checkbox" class="control control--checkbox checkbox-delete activity-bulk-delete" name="activity-bulk-delete[]" value="<?php echo intval($activity_id); ?>">
                      <span class="control__indicator"></span>
                  </label>
              </td>
              <td class="w-100" data-label="<?php esc_html_e('Activity', 'houzez'); ?>">
                <div class="houzez-customer align-items-start">
                  <div class="text-box">
                    <div class="time-span small"><?php printf( __( '%s ago', 'houzez' ), human_time_diff( $datetime, time() ) ); ?></div>
                    <?php 
                    if($type == 'lead') {
                        $permalink_id = isset($meta['listing_id']) ? $meta['listing_id'] : '';
                        if(!empty($permalink_id)) {
                            $permalink = get_permalink($permalink_id);
                            $title = get_the_title($permalink_id);
                        }
                    } else if($type == 'lead_agent') {
                        $permalink_id = isset($meta['agent_id']) ? $meta['agent_id'] : '';
                        $agent_type = isset($meta['agent_type']) ? $meta['agent_type'] : '';
                        if(!empty($permalink_id)) {
                            if($agent_type == "author_info") {
                                $permalink = get_author_posts_url( $permalink_id );
                                $title = get_the_author_meta( 'display_name', $permalink_id );
                            } else {
                                $permalink = get_permalink($permalink_id);
                                $title = get_the_title($permalink_id);
                            }
                        }
                    } else if($type == 'lead_contact') {
                        $permalink_id = isset($meta['lead_page_id']) ? $meta['lead_page_id'] : '';
                        if(!empty($permalink_id)) {
                            $permalink = get_permalink($permalink_id);
                            $title = get_the_title($permalink_id);
                        }
                    } else if( $type == 'review' ) {
                        $review_stars = isset($meta['review_stars']) ? $meta['review_stars'] : '';
                        $review_title = isset($meta['review_title']) ? $meta['review_title'] : '';
                        $review_link = isset($meta['review_link']) ? $meta['review_link'] : '';
                        $username = isset($meta['username']) ? $meta['username'] : '';
                        
                        echo '<strong>'. esc_html__('Received a new rating', 'houzez') .'</strong> <span>'. esc_html__('from', 'houzez') .'</span> <a href="#"><strong>'.esc_attr($username).'</strong></a>';
                        echo '<div class="rating-container d-flex align-items-center my-2">
                            <div class="rating star d-flex align-items-center gap-1" role="img" aria-label="Rating">
                                '.houzez_get_stars($review_stars, false).'
                            </div>
                        </div>';
                        
                        if(isset($review_title) && !empty($review_title)) {
                            echo '<p><strong>'.esc_attr($review_title).'</strong><br>';
                            echo isset($meta['review_content']) ? $meta['review_content'] : '';
                            echo '</p>';
                        }
                        
                        if(!empty($review_link)) {
                            echo '<a target="_blank" href="'.esc_url($review_link).'"><i class="houzez-icon icon-arrow-button-circle-right me-2"></i> <strong>'.esc_html__('View', 'houzez').'</strong></a>';
                        }
                    }

                    if( $type == 'lead' || $type == 'lead_agent' || $type == "lead_contact") {
                        if( !empty($title)) {
                            echo '<strong>'. esc_html__('New lead', 'houzez') .'</strong> <span>'. esc_html__('from', 'houzez') .'</span> <a href="'.esc_url($permalink).'">'.esc_attr($title).'</a>';
                        } else {
                            echo '<strong>'. esc_html__('New lead', 'houzez') .'</strong>';
                        }
                        
                        if(!empty($permalink_id)) {
                            echo '<div class="my-3 me-2 image-holder">
                                <a href="'.esc_url($permalink).'">
                                    <img src="'.get_the_post_thumbnail_url($permalink_id, 'thumbnail').'" alt="" class="img-fluid">
                                </a>
                            </div>';
                        }
                        
                        echo '<ul class="my-3">';
                        
                        if(isset($meta['name']) && !empty($meta['name'])) {
                            echo '<li><strong>'.esc_html__('Name', 'houzez').':</strong> '.esc_attr($meta['name']).'</li>';
                        }
                        
                        if(isset($meta['email']) && !empty($meta['email'])) {
                            echo '<li><strong>'.esc_html__('Email', 'houzez').':</strong> <a href="mailto:'.esc_attr($meta['email']).'">'.esc_attr($meta['email']).'</a></li>';
                        }
                        
                        if(isset($meta['phone']) && !empty($meta['phone'])) {
                            echo '<li><strong>'.esc_html__('Phone', 'houzez').':</strong> '.esc_attr($meta['phone']).'</li>';
                        }
                        
                        if(isset($meta['user_type']) && !empty($meta['user_type'])) {
                            echo '<li><strong>'.esc_html__('Type', 'houzez').':</strong> '.esc_attr($meta['user_type']).'</li>';
                        }
                        
                        if($subtype == 'schedule_tour') {
                            $sdate = isset($meta['schedule_date']) ? $meta['schedule_date'] : '';
                            $stime = isset($meta['schedule_time']) ? $meta['schedule_time'] : '';
                            $schedule_tour_type = isset($meta['schedule_tour_type']) ? $meta['schedule_tour_type'] : '';
                            
                            if( $schedule_tour_type != '' ) {
                                echo '<li><strong>'.esc_html__('Tour Type', 'houzez').':</strong> '.esc_attr($schedule_tour_type).'</li>';
                            }
                            
                            if(!empty($sdate) || !empty($stime)) {
                                echo '<li class="my-3"><strong>'.esc_html__('Desired tour date', 'houzez').':</strong> '.esc_attr($sdate).' '.esc_html__('at', 'houzez').' '.esc_attr($stime).'</li>';
                            }
                        }
                        
                        echo '</ul>';
                        
                        if(isset($meta['message']) && !empty($meta['message'])) {
                            echo '<p>'.esc_html($meta['message']).'</p>';
                        }
                    }
                    ?>
                  </div>
                </div>
              </td>
              <td class="text-lg-center text-start" data-label="<?php esc_html_e('Actions', 'houzez'); ?>">
                <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php esc_html_e('Actions', 'houzez'); ?>">
                  <a href="#" class="action-btn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="houzez-icon icon-navigation-menu-horizontal"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu3">
                    <li>
                      <a class="delete-activity-js dropdown-item" href="javascript:void(0)" data-id="<?php echo intval($activity_id)?>" data-nonce="<?php echo wp_create_nonce('delete_activity_nonce') ?>">
                        <i class="houzez-icon icon-bin"></i><?php esc_html_e('Delete', 'houzez'); ?>
                      </a>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
  </div>
  
  <?php get_template_part('template-parts/dashboard/board/pagination', null, array('total_records' => $total_records)); ?>
</div>