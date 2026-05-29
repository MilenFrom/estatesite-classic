<?php
global $all_enquires, $dashboard_crm;
$hpage = isset($_GET['hpage']) ? sanitize_text_field($_GET['hpage']) : '';
$keyword = isset($_GET['keyword']) ? sanitize_text_field(trim($_GET['keyword'])) : '';
$all_enquires = Houzez_Enquiry::get_enquires();
$total_records = $all_enquires['data']['total_records'];

$dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');
?>

<div class="heading d-flex align-items-center justify-content-between">
  <div class="heading-text">
    <h2><?php echo houzez_option('dsh_inquiries', 'Inquiries'); ?></h2> 
  </div>
  <div class="add-export-btn">
    <ul class="d-flex align-items-center gap-2">
      <li>
        <a href="javascript:void(0)" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasInquiry" data-bs-backdrop="true" aria-controls="offcanvasInquiry">
            <i class="houzez-icon icon-add-circle me-2"></i><?php esc_html_e('Add New Inquiry', 'houzez'); ?>
        </a>
    </li> 
    </ul>
  </div>
</div>

<?php 
if(!empty($all_enquires['data']['results']) || isset( $_GET['keyword'] ) ) {

    get_template_part('template-parts/dashboard/statistics/statistic-inquiries'); ?>

<div class="houzez-data-content"> 
    <div class="houzez-table-filters d-flex align-items-center justify-content-between p-3">
        <form name="search-inquiries" method="get" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" class="d-flex align-items-center justify-content-between w-100">
            <input type="hidden" name="hpage" value="<?php echo esc_attr($hpage); ?>">
            <div class="dashboard-filter-left">
            <ul class="d-flex gap-2"> 
                <li>
                    <a id="export-inquiries" href="#" class="btn btn-primary-outlined">
                        <?php get_template_part('template-parts/loader'); ?>
                        <i class="houzez-icon icon-common-file-download me-2"></i><?php esc_html_e('Export', 'houzez'); ?>
                    </a>
                </li>
                <li><a id="enquiry_delete_multiple" href="javascript:void(0)" class="btn btn-grey-outlined"><i class="houzez-icon icon-bin me-2"></i><?php esc_html_e('Delete', 'houzez'); ?></a></li>
            </ul>
            </div>
            <p class="small"><i class="houzez-icon icon-single-neutral-flag-2 me-2"></i> <?php echo esc_html($total_records); ?> <?php esc_html_e('Inquiries Found', 'houzez'); ?></p>
            <div class="dashboard-search-filter d-flex align-items-center gap-2">
            <div class="relative">
                <input name="keyword" type="text" class="form-control dashboard-search" placeholder="<?php echo esc_html__('Inquiry Type', 'houzez'); ?>" />
                <span><i class="houzez-icon icon-search"></i></span>
            </div>
            <div class="dropdown">
                <button type="submit" class="btn btn-secondary"><?php esc_html_e('Search', 'houzez'); ?></button> 
            </div>
            </div>
        </form>
    </div> 

  
    <div class="houzez-data-table">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
            <thead>
                <tr>
                <th>
                    <label class="control control--checkbox">
                        <input type="checkbox" class="enquiry_multi_delete" id="enquiry_select_all" name="enquiry_multicheck">
                        <span class="control__indicator"></span>
                    </label>
                </th>
                <th data-label="<?php esc_html_e('Contact', 'houzez'); ?>"><?php esc_html_e('Contact', 'houzez'); ?></th>
                <th data-label="<?php esc_html_e('Inquiry Type', 'houzez'); ?>"><?php esc_html_e('Inquiry Type', 'houzez'); ?></th>
                <th data-label="<?php esc_html_e('Listing Type', 'houzez'); ?>"><?php esc_html_e('Listing Type', 'houzez'); ?></th>
                <th data-label="<?php esc_html_e('Price', 'houzez'); ?>"><?php esc_html_e('Price', 'houzez'); ?></th>
                <th data-label="<?php esc_html_e('Beds', 'houzez'); ?>"><?php esc_html_e('Beds', 'houzez'); ?></th>
                <th data-label="<?php esc_html_e('Baths', 'houzez'); ?>"><?php esc_html_e('Baths', 'houzez'); ?></th>
                <th data-label="<?php esc_html_e('Area', 'houzez'); ?>"><?php esc_html_e('Area', 'houzez'); ?></th>
                <th data-label="<?php esc_html_e('Date', 'houzez'); ?>"><?php esc_html_e('Date', 'houzez'); ?></th>
                <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($all_enquires['data']['results'] as $enquiry) { 

                    $lead = Houzez_Leads::get_lead($enquiry->lead_id);
                    $meta = maybe_unserialize($enquiry->enquiry_meta);

                    $datetime = $enquiry->time;

                    // Use helper to properly interpret MySQL TIMESTAMP (stored in UTC)
                    $datetime_unix = houzez_mysql_to_wp_timestamp( $datetime, 'utc' );
                    $get_date = houzez_return_formatted_date($datetime_unix);
                    $get_time = houzez_get_formatted_time($datetime_unix);

                    $detail_enquiry = add_query_arg(
                        array(
                            'hpage' => 'enquiries',
                            'enquiry' => $enquiry->enquiry_id,
                        ), $dashboard_crm
                    );
                ?>
                <tr>  
                    <td>
                        <label class="control control--checkbox">
                            <input type="checkbox" class="enquiry_multi_delete" name="enquiry_multi_delete[]" value="<?php echo intval($enquiry->enquiry_id); ?>">
                            <span class="control__indicator"></span>
                        </label>
                    </td>
                    <td data-label="<?php esc_html_e('Contact', 'houzez'); ?>">
                        <div class="text-box">
                        <?php 
                        if(isset($lead->display_name)) {
                            echo '<strong>'.esc_attr($lead->display_name).'</strong><br>';
                        }?>
                        <a href="mailto:<?php echo esc_attr($lead->email); ?>"><?php echo esc_attr($lead->email); ?></a>
                        </div>
                    </td> 
                    <td data-label="<?php esc_html_e('Inquiry Type', 'houzez'); ?>"><?php echo esc_attr($enquiry->enquiry_type); ?></td>
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
                    <td data-label="<?php esc_html_e('Beds', 'houzez'); ?>">
                        <?php 
                        if(isset($meta['min_beds'])) {
                            echo esc_attr($meta['min_beds']); 
                        }

                        if(isset($meta['max_beds'])) {
                            echo ' - '.esc_attr($meta['max_beds']); 
                        }?>
                    </td>
                    <td data-label="<?php esc_html_e('Baths', 'houzez'); ?>">
                        <?php 
                        if(isset($meta['min_baths'])) {
                            echo esc_attr($meta['min_baths']); 
                        }

                        if(isset($meta['max_baths'])) {
                            echo ' - '.esc_attr($meta['max_baths']); 
                        }?>
                    </td>
                    <td data-label="<?php esc_html_e('Area', 'houzez'); ?>">
                        <?php 
                        if(isset($meta['min_area'])) {
                            echo esc_attr($meta['min_area']); 
                        }

                        if(isset($meta['max_area'])) {
                            echo ' - '.esc_attr($meta['max_area']); 
                        }?>
                    </td>
                    <td data-label="<?php esc_html_e('Date', 'houzez'); ?>">
                        <?php echo esc_attr($get_date); ?><br>
                        <?php echo esc_html__('at', 'houzez'); ?> <?php echo esc_attr($get_time); ?>
                    </td>
                    <td class="text-lg-center text-start">
                        <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php esc_html_e('Actions', 'houzez'); ?>">
                        <a href="#" class="action-btn" data-bs-toggle="dropdown" aria-expanded="false"><i class="houzez-icon icon-navigation-menu-horizontal"></i></a>
                        <ul class="dropdown-menu dropdown-menu3">
                            <li>
                                <a class="dropdown-item active" href="<?php echo esc_url($detail_enquiry); ?>">
                                    <i class="houzez-icon icon-share-2"></i> <?php esc_html_e('View', 'houzez'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="edit_enquiry_js dropdown-item" href="javascript:void(0)" data-id="<?php echo intval($enquiry->enquiry_id)?>" data-bs-toggle="offcanvas" data-bs-target="#offcanvasInquiry" aria-controls="offcanvasInquiry">
                                    <i class="houzez-icon icon-pencil"></i><?php esc_html_e('Edit', 'houzez'); ?>
                                </a>
                            </li>
                            <li>
                                <a class="delete_enquiry_js dropdown-item" href="javascript:void(0)" data-id="<?php echo intval($enquiry->enquiry_id)?>">
                                    <i class="houzez-icon icon-bin"></i><?php esc_html_e('Delete', 'houzez'); ?>
                                </a>
                            </li> 
                        </ul> 
                        </div>
                    </td>
                </tr>
                <?php
                } ?>
            </tbody>
            </table>
        </div>
    </div> 

    <?php get_template_part('template-parts/dashboard/board/pagination', null, array('total_records' => $total_records)); ?>
</div> 

<?php 
} else { ?>
    <div class="stats-box">
    <?php esc_html_e("Don't have any inquiry at this moment.", 'houzez'); ?> <a href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasInquiry" aria-controls="offcanvasInquiry"><strong><?php esc_html_e('Add New Inquiry', 'houzez'); ?></strong></a>
    </div>
<?php 
} ?>

<?php get_template_part('template-parts/dashboard/board/enquires/add-new-enquiry'); ?>