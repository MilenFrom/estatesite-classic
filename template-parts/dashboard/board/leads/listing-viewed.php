<?php
$viewed = Houzez_Leads::get_lead_viewed_listings();
$total_records = $viewed['data']['total_records'] ?? 0;

if(!empty($viewed['data']['results'])) { ?>

<div class="inquiry-data">
  <div class="inquiry-listing d-flex align-items-center justify-content-between mb-4">
    <p class="ps-3"><strong><?php echo esc_attr($viewed['data']['total_records']); ?></strong> <?php esc_html_e('Listings Viewed', 'houzez'); ?></p>
    <div class="d-flex gap-2">
      <button id="listing_viewed_delete" class="btn btn-primary">
        <i class="houzez-icon icon-remove-circle me-2"></i> <?php esc_html_e('Delete', 'houzez'); ?>
      </button>
    </div>
  </div>
  <div class="houzez-data-table">
    <div class="table-responsive">
      <table class="table table-hover align-middle m-0">
        <thead>
          <tr>
            <th data-label="<?php esc_html_e('Select', 'houzez'); ?>">
              <label class="control control--checkbox">
                <input class="form-check-input" type="checkbox" id="listing_viewed_select_all" name="listing_viewed_select_all">
                <span class="control__indicator"></span>
              </label>
            </th>
            <th data-label="<?php esc_html_e('Property', 'houzez'); ?>"><?php esc_html_e('Property', 'houzez'); ?></th>
            <th></th>
            <th data-label="<?php esc_html_e('Date', 'houzez'); ?>"><?php esc_html_e('Date', 'houzez'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($viewed['data']['results'] as $view) { 
              $listing_id = $view->listing_id; 
              $datetime = $view->time; 

              // Use helper to properly interpret MySQL TIMESTAMP (stored in UTC)
              $datetime_unix = houzez_mysql_to_wp_timestamp( $datetime, 'utc' );
              $get_date = houzez_return_formatted_date($datetime_unix);
              $get_time = houzez_get_formatted_time($datetime_unix);

              $thumbnail = get_the_post_thumbnail_url($listing_id, 'thumbnail');
              if(empty($thumbnail)) {
                  $thumbnail = 'https://placehold.it/50x50';
              }
          ?>

            <tr>
              <td data-label="<?php esc_html_e('Select', 'houzez'); ?>">
                <label class="control control--checkbox">
                  <input class="form-check-input listing_viewed_multi_delete" type="checkbox" value="<?php echo intval($view->id); ?>">
                  <span class="control__indicator"></span>
                </label>
              </td>
              <td data-label="<?php esc_html_e('Property', 'houzez'); ?>">
                <img src="<?php echo esc_url($thumbnail); ?>" width="50" height="50">
              </td>
              <td class="text-nowrap w-100">
                <a target="_blank" href="<?php echo get_permalink($listing_id); ?>">
                  <strong><?php echo get_the_title($listing_id); ?></strong>
                </a><br>
                <?php echo get_post_meta($listing_id, 'fave_property_map_address', true); ?>
              </td>
              <td class="text-nowrap" data-label="<?php esc_html_e('Date', 'houzez'); ?>">
                <?php echo esc_attr($get_date); ?><br>
                <?php echo esc_html__('at', 'houzez'); ?> <?php echo esc_attr($get_time); ?>
              </td>
            </tr>
          
          <?php 
          }?>
          
        </tbody>
      </table>
    </div>
  </div>
  <?php get_template_part('template-parts/dashboard/board/pagination', '', array('total_records' => $total_records)); ?>
</div>

<?php
} else {?>
    <div class="alert alert-info mb-4" role="alert">
          
       <p><i class="houzez-icon icon-Information-Circle me-2"></i> <?php esc_html_e('No record found.', 'houzez'); ?></p> 
    </div>
<?php } ?>