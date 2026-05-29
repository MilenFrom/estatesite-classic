<?php
$dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');
$import_link = add_query_arg( 'hpage', 'import-leads', $dashboard_crm );
$hpage = isset($_GET['hpage']) ? sanitize_text_field($_GET['hpage']) : '';
$keyword = isset($_GET['keyword']) ? sanitize_text_field(trim($_GET['keyword'])) : '';
$leads = Houzez_leads::get_leads();

$total_records = $leads['data']['total_records'];
?>

<div class="heading d-flex align-items-center justify-content-between">
  <div class="heading-text">
    <h2><?php echo houzez_option('dsh_leads', 'Leads'); ?></h2>
  </div>
  <div class="add-export-btn">
    <ul class="d-flex align-items-center gap-2">
      <li>
        <a href="#" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLead" aria-controls="offcanvasLead">
          <i class="houzez-icon icon-add-circle me-2"></i><?php esc_html_e('Add New Lead', 'houzez'); ?>
        </a>
      </li>
    </ul>
  </div>
</div>

<?php 

if(!empty($leads['data']['results']) || isset( $_GET['keyword'] ) ) {
    get_template_part('template-parts/dashboard/statistics/statistic-leads'); ?>

    <div class="houzez-data-content">

        <!-- Table Filters -->
        <div class="houzez-table-filters d-flex align-items-center justify-content-between p-3">
            <form method="get" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" class="d-flex align-items-center justify-content-between w-100">
                <div class="dashboard-filter-left">
                <ul class="d-flex gap-2"> 
                    <li><a href="<?php echo esc_url($import_link);?>" class="btn btn-primary-outlined"><i class="houzez-icon icon-common-file-upload me-2"></i>Import</a></li>
                    <li>
                        <a id="export-leads" href="#" class="btn btn-primary-outlined">
                            <?php get_template_part('template-parts/loader'); ?>
                            <i class="houzez-icon icon-common-file-download me-2"></i><?php esc_html_e( 'Export', 'houzez' ); ?>
                        </a>
                    </li>
                    <li><a id="bulk-delete-leads" href="javascript:void(0)" class="btn btn-grey-outlined"><i class="houzez-icon icon-bin me-2"></i><?php echo esc_html__( 'Delete', 'houzez' ); ?></a></li>
                </ul>
                </div>
                <!-- <p class="small"><i class="houzez-icon icon-single-neutral-flag-2 me-2"></i> <?php echo esc_attr($total_records); ?> <?php esc_html_e('Results Found', 'houzez'); ?></p> -->
                <div class="dashboard-search-filter d-flex align-items-center gap-2">
                    <input type="hidden" name="hpage" value="<?php echo esc_attr($hpage); ?>">
                    <div class="relative">
                        <input name="keyword" type="text" value="<?php echo esc_attr($keyword); ?>" class="form-control dashboard-search" placeholder="<?php echo esc_html__('Search', 'houzez'); ?>">
                        <span><i class="houzez-icon icon-search"></i></span>
                    </div>
                    <div class="dropdown"> 
                        <button type="submit" class="btn btn-search btn-secondary"><?php echo esc_html__( 'Search', 'houzez' ); ?></button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="houzez-data-table">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                    <th data-label="<?php esc_html_e('Select', 'houzez'); ?>">
                        <label class="control control--checkbox">
                            <input id="leads_select_all" name="leads_multicheck" type="checkbox" class="lead-bulk-delete control control--checkbox">
                            <span class="control__indicator"></span>
                        </label>
                    </th>
                    <th data-label="<?php esc_html_e('Name', 'houzez'); ?>"><?php esc_html_e('Name', 'houzez'); ?></th>
                    <th data-label="<?php esc_html_e('Email', 'houzez'); ?>"><?php esc_html_e('Email', 'houzez'); ?></th>
                    <th data-label="<?php esc_html_e('Phone', 'houzez'); ?>"><?php esc_html_e('Phone', 'houzez'); ?></th>
                    <th data-label="<?php esc_html_e('Type', 'houzez'); ?>"><?php esc_html_e('Type', 'houzez'); ?></th>
                    <th data-label="<?php esc_html_e('Agent', 'houzez'); ?>"><?php esc_html_e('Agent', 'houzez'); ?></th>
                    <th data-label="<?php esc_html_e('Date', 'houzez'); ?>"><?php esc_html_e('Date', 'houzez'); ?></th>
                    <th data-label="<?php esc_html_e('Edit', 'houzez'); ?>" class="text-center">
                        <?php esc_html_e('Actions', 'houzez'); ?>
                    </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($leads['data']['results'] as $result) { 
                        $detail_link = add_query_arg(
                            array(
                                'hpage' => 'lead-detail',
                                'lead-id' => $result->lead_id,
                                'tab' => 'enquires',
                            ), $dashboard_crm
                        );

                        $datetime = $result->time;

                        $enquiry_to = $result->enquiry_to;
                        $enquiry_user_type = $result->enquiry_user_type;

                        $agent_info = houzezcrm_get_assigned_agent( $enquiry_to, $enquiry_user_type );

                        // Use helper to properly interpret MySQL TIMESTAMP (stored in UTC)
                        $datetime_unix = houzez_mysql_to_wp_timestamp( $datetime, 'utc' );
                        $get_date = houzez_return_formatted_date($datetime_unix);
                        $get_time = houzez_get_formatted_time($datetime_unix);
                    ?>
                    <tr>
                        <td data-label="<?php esc_html_e('Select', 'houzez'); ?>">
                            <label class="control control--checkbox">
                                <input type="checkbox" class="control control--checkbox checkbox-delete lead-bulk-delete" name="lead-bulk-delete[]" value="<?php echo intval($result->lead_id); ?>">
                                <span class="control__indicator"></span>
                            </label>
                        </td>
                        <td data-label="<?php esc_html_e('Name', 'houzez'); ?>">
                            <div class="text-box">
                            <?php echo esc_attr($result->display_name); ?>
                            </div>
                        </td>
                        <td data-label="<?php esc_html_e('Email', 'houzez'); ?>">
                            <a href="mailto:<?php echo esc_attr($result->email); ?>">
                                <strong><?php echo esc_attr($result->email); ?></strong>
                            </a>
                        </td>
                        <td data-label="<?php esc_html_e('Phone', 'houzez'); ?>"><?php echo esc_attr($result->mobile); ?></td>
                        <td data-label="<?php esc_html_e('Type', 'houzez'); ?>">
                            <?php 
                            if( $result->type ) {
                                $type = stripslashes($result->type);
                                $type = htmlentities($type);
                                echo esc_attr($type); 
                            }?>
                        </td>
                        <td data-label="<?php esc_html_e('Agent', 'houzez'); ?>">
                            <i class="houzez-icon icon-single-neutral-circle me-2 grey"></i> 
                            <?php if(!empty($agent_info['name'])) {
                                echo esc_attr($agent_info['name']);
                            } else {
                                echo '-';
                            } ?>
                        </td>
                        <td data-label="<?php esc_html_e('Date', 'houzez'); ?>">
                            <?php echo esc_attr($get_date); ?><br>
                            <?php echo esc_html__('at', 'houzez'); ?> <?php echo esc_attr($get_time); ?>
                        </td>
                        <td data-label="<?php esc_html_e('Edit', 'houzez'); ?>" class="text-lg-center text-start">
                            <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php esc_html_e('Actions', 'houzez'); ?>">
                            <a href="#" class="action-btn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="houzez-icon icon-navigation-menu-horizontal"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu3">
                                <li>
                                <a class="dropdown-item active" href="<?php echo esc_url($detail_link); ?>">
                                    <i class="houzez-icon icon-share-2"></i> <?php esc_html_e('Detail', 'houzez'); ?>
                                </a>
                                </li>
                                <li>
                                <a class="edit-lead dropdown-item" data-id="<?php echo intval($result->lead_id)?>" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLead" aria-controls="offcanvasLead">
                                    <i class="houzez-icon icon-pencil"></i> <?php esc_html_e('Edit', 'houzez'); ?>
                                </a>
                                </li>
                                <li>
                                <a class="delete-lead dropdown-item" href="#" data-id="<?php echo intval($result->lead_id); ?>" data-nonce="<?php echo wp_create_nonce('delete_lead_nonce') ?>">
                                    <i class="houzez-icon icon-bin"></i> <?php esc_html_e('Delete', 'houzez'); ?>
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
    <?php esc_html_e("You don't have any contact at this moment.", 'houzez'); ?> <a href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLead" aria-controls="offcanvasLead"><strong><?php esc_html_e('Add New Lead', 'houzez'); ?></strong></a>
    </div>
<?php 
} ?>

<?php get_template_part('template-parts/dashboard/board/leads/new-lead-panel'); ?>

