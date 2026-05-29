<?php 
global $dashboard_crm;
$all_enquires = Houzez_Enquiry::get_enquires();

$total_records = $all_enquires['data']['total_records'] ?? 0;

if(!empty($all_enquires['data']['results'])) {
?>
<div class="inquiry-data">
  <div class="houzez-data-table">
    <div class="table-responsive">
      <table class="table table-hover align-middle m-0">
        <thead>
          <tr>
            <th data-label="<?php esc_html_e('ID', 'houzez'); ?>"><?php esc_html_e('ID', 'houzez'); ?></th>
            <th data-label="<?php esc_html_e('Inquiry Type', 'houzez'); ?>"><?php esc_html_e('Inquiry Type', 'houzez'); ?></th>
            <th data-label="<?php esc_html_e('Listing Type', 'houzez'); ?>"><?php esc_html_e('Listing Type', 'houzez'); ?></th>
            <th data-label="<?php esc_html_e('Price', 'houzez'); ?>"><?php esc_html_e('Price', 'houzez'); ?></th>
            <th data-label="<?php esc_html_e('Bedrooms', 'houzez'); ?>"><?php esc_html_e('Bedrooms', 'houzez'); ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>

          <?php 
          foreach ($all_enquires['data']['results'] as $enquiry) { 

              $lead = Houzez_Leads::get_lead($enquiry->lead_id);
              $meta = maybe_unserialize($enquiry->enquiry_meta);

              $detail_enquiry = add_query_arg(
                  array(
                      'hpage' => 'enquiries',
                      'enquiry' => $enquiry->enquiry_id,
                  ), $dashboard_crm
              );

          ?>
          <tr>  
              <td data-label="<?php esc_html_e('ID', 'houzez'); ?>">
                  <?php echo esc_attr($enquiry->enquiry_id); ?>
              </td>
              <td data-label="<?php esc_html_e('Inquiry Type', 'houzez'); ?>">
                  <?php echo esc_attr($enquiry->enquiry_type); ?>
              </td>
              <td data-label="<?php esc_html_e('Listing Type', 'houzez'); ?>">
                  <?php 
                  if(isset($meta['property_type']['name'])) {
                      echo esc_attr($meta['property_type']['name']); 
                  }?>
              </td>

              <td data-label="<?php esc_html_e('Price', 'houzez'); ?>">
                  <?php 
                  if(isset($meta['min_price'])) {
                      echo esc_attr($meta['min_price']); 
                  }

                  if(isset($meta['max_price'])) {
                      echo ' - '.esc_attr($meta['max_price']); 
                  }?>
              </td>

              <td data-label="<?php esc_html_e('Bedrooms', 'houzez'); ?>">
                  <?php 
                  if(isset($meta['min_beds'])) {
                      echo esc_attr($meta['min_beds']); 
                  }

                  if(isset($meta['max_beds'])) {
                      echo ' - '.esc_attr($meta['max_beds']); 
                  }?>
              </td>
              <td class="text-lg-center text-start" data-label="<?php esc_html_e('View', 'houzez'); ?>">
                  <a href="<?php echo esc_url($detail_enquiry); ?>"><?php esc_html_e('View', 'houzez'); ?></a>
              </td>
          </tr> 
          <?php
          } ?>
        </tbody>
      </table>
    </div>
  </div> 
  <?php get_template_part('template-parts/dashboard/board/pagination', '', array('total_records' => $total_records)); ?>
</div> 
<?php } else { ?>
  <div class="inquiry-data pb-4">
    <div class="alert alert-info mb-4" role="alert">
      <i class="houzez-icon icon-info-circle me-2"></i>
      <?php esc_html_e("Don't have any inquiry at this moment.", 'houzez'); ?>
    </div>
<?php } ?>