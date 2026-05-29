<?php
$userID       = get_current_user_id();
$dash_profile_link = houzez_get_template_link_2('template/user_dashboard_profile.php');
$dash_props_link = houzez_get_template_link_2('template/user_dashboard_properties.php');
$delete_agents_nonce = wp_create_nonce( 'delete_agents_nonce' );

$agency_agent_add = add_query_arg( 'agents', 'add_new', $dash_profile_link );

// Search functionality
$search_term = isset($_GET['agent_search']) ? sanitize_text_field($_GET['agent_search']) : '';

// Pagination
$records_array = array(10, 20, 50, 100);
$agents_per_page = isset($_GET['per_page']) && in_array(intval($_GET['per_page']), $records_array) ? intval($_GET['per_page']) : 10;
$paged = isset($_GET['agent_paged']) ? max(1, intval($_GET['agent_paged'])) : 1;

$meta_query = array(
    array(
        'key' => 'fave_agent_agency',
        'value' => $userID
    )
);

$user_query_args = array(
    'role' => 'houzez_agent',
    'meta_query' => $meta_query,
    'number' => $agents_per_page,
    'offset' => ($paged - 1) * $agents_per_page
);

// Add search condition if search term is provided
if (!empty($search_term)) {
    $user_query_args['search'] = '*' . $search_term . '*';
    $user_query_args['search_columns'] = array('user_login', 'user_email', 'display_name', 'user_nicename', 'first_name', 'last_name');
}

$wp_user_query = new WP_User_Query($user_query_args);
$agents = $wp_user_query->get_results();

// Get total users for pagination
$total_query = new WP_User_Query(array(
    'role' => 'houzez_agent',
    'meta_query' => $meta_query,
    'search' => !empty($search_term) ? '*' . $search_term . '*' : '',
    'search_columns' => !empty($search_term) ? array('user_login', 'user_email', 'display_name', 'user_nicename', 'first_name', 'last_name') : array(),
    'fields' => 'ID',
    'count_total' => true
));

$total_agents = $total_query->get_total();
$total_pages = ceil($total_agents / $agents_per_page);

// Calculate item range for display
$start = ($paged - 1) * $agents_per_page + 1;
$end = min($total_agents, $paged * $agents_per_page);
?>
<div class="heading d-flex align-items-center justify-content-between mb-4">
  <div class="heading-text">
    <h2><?php echo esc_html__('All Agents', 'houzez');?></h2> 
  </div>
  <div class="add-export-btn">
    <ul class="d-flex align-items-center gap-2">
      <li><a href="<?php echo esc_url($agency_agent_add); ?>" class="btn btn-primary"><i class="houzez-icon icon-add-circle me-2"></i><?php esc_html_e('Add New', 'houzez'); ?></a></li> 
    </ul>
  </div>
</div> 

<div class="houzez-data-content">  
  
    <div class="houzez-table-filters d-flex align-items-center justify-content-between p-3">
        <form class="d-flex align-items-center justify-content-between w-100" method="GET" action="">
            <div class="dashboard-filter-right d-flex align-items-center gap-2">
            <select id="agent-bulk-action-select" class="form-select">
                <option><?php echo esc_html__('Bulk Actions', 'houzez');?></option>
                <option><?php echo esc_html__('Delete', 'houzez');?></option>
            </select>
            <input type="hidden" id="bulk-action-nonce" value="<?php echo esc_attr($delete_agents_nonce); ?>">
            <a id="agent-bulk-action-apply" class="btn btn-primary" href="#"><?php echo esc_html__('Apply', 'houzez');?></a>
            </div>
            <p class="small"><i class="houzez-icon icon-single-neutral-flag-2 me-2"></i> <?php echo sprintf(esc_html__('%s Results Found', 'houzez'), $total_agents);?></p>
            <div class="dashboard-search-filter d-flex align-items-center gap-2">
                <div class="relative">
                    <input type="text" name="agent_search" id="agent_search" class="form-control dashboard-search" placeholder="<?php echo esc_html__('Search agents...', 'houzez');?>" value="<?php echo esc_attr($search_term); ?>" />
                    <span><i class="houzez-icon icon-search"></i></span>
                </div>
                <div>
                    <button type="submit" class="btn btn-secondary"><?php echo esc_html__('Search', 'houzez');?></button>
                </div>
                <?php if (!empty($search_term)) : ?>
                <div>
                    <a href="<?php echo esc_url(remove_query_arg('agent_search')); ?>" class="btn btn-outline-secondary"><?php echo esc_html__('Clear', 'houzez');?></a>
                </div>
                <?php endif; ?>
            </div>
            <?php 
            // Preserve any existing query parameters
            foreach ($_GET as $key => $value) {
                if ($key !== 'agent_search') {
                    echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                }
            }
            ?>
        </form>
    </div> 
    
  <?php if( !empty($agents) ) { ?>
  <div class="houzez-data-table">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th data-label="Select">
                            <label class="control control--checkbox">
                                <input type="checkbox" class="control control--checkbox" id="agent_select_all" name="agent_multicheck">
                                <span class="control__indicator"></span>
                            </label> 
                        </th>
                        <th data-label="<?php echo esc_html__('Image', 'houzez');?>"><?php echo esc_html__('Image', 'houzez');?></th>
                        <th data-label="<?php echo esc_html__('Agent Name', 'houzez');?>"><?php echo esc_html__('Agent Name', 'houzez');?></th>
                        <th data-label="<?php echo esc_html__('Email', 'houzez');?>"><?php echo esc_html__('Email', 'houzez');?></th>
                        <th data-label="<?php echo esc_html__('Listings', 'houzez');?>"><?php echo esc_html__('Listings', 'houzez');?></th>
                        <th data-label="<?php echo esc_html__('Phone', 'houzez');?>"><?php echo esc_html__('Phone', 'houzez');?></th>
                        <th data-label="<?php echo esc_html__('Mobile', 'houzez');?>"><?php echo esc_html__('Mobile', 'houzez');?></th>
                        <th data-label="<?php echo esc_html__('Actions', 'houzez');?>" class="text-center">
                            <?php echo esc_html__('Actions', 'houzez');?>
                        </th>
                    </tr>
                </thead>
                <tbody> 
                    <?php
                    foreach ($agents as $agent) {
                        $agent_info = get_userdata($agent->ID);
                        $agency_agent_edit = add_query_arg(
                            array(
                                'edit_user' => $agent->ID,
                            ),$dash_profile_link
                        );

                        $view_listings = add_query_arg(
                            array(
                                'user' => $agent->ID,
                            ),$dash_props_link
                        );

                        $first_name = $agent_info->first_name;
                        $last_name = $agent_info->last_name;

                        if( !empty($first_name) && !empty($last_name) ) {
                            $agent_name = $first_name.' '.$last_name;
                        } else {
                            $agent_name = $agent_info->display_name;
                        }
                        $user_agent_id = get_user_meta( $agent->ID, 'fave_author_agent_id', true );

                        if( !empty( $user_agent_id ) ) {
                            if( 'publish' == get_post_status ( $user_agent_id ) ) {
                                $agent_permalink = get_permalink($user_agent_id);
                            } else {
                                $agent_permalink = get_author_posts_url( $agent->ID );
                            }

                        } else {
                            $agent_permalink = get_author_posts_url( $agent->ID );
                        }

                        $image_url = get_user_meta( $agent->ID, 'fave_author_custom_picture', true );
                        if( empty( $image_url ) ) {
                            $image_url = get_avatar_url($agent->ID);
                        }
                        ?>
                        <tr>
                            <td data-label="<?php echo esc_html__('Select', 'houzez');?>"><label class="control control--checkbox">
                                <input type="checkbox" class="control control--checkbox agent-bulk-delete" name="agent-bulk-delete[]" value="<?php echo intval($agent->ID); ?>">
                                <span class="control__indicator"></span>
                            </label> </td>
                            <td data-label="<?php echo esc_html__('Image', 'houzez');?>">
                                <div class="image-holder">
                                    <a>
                                        <img src="<?php echo esc_url($image_url); ?>" alt="" width="50" height="50" class="img-fluid">
                                    </a>
                                </div>
                            </td>
                            <td data-label="<?php echo esc_html__('Agent Name', 'houzez');?>">
                                <div class="text-box">
                                <a class="fw-bold"><?php echo esc_attr($agent_name); ?></a>
                                </div>
                            </td> 
                            <td data-label="<?php echo esc_html__('Email', 'houzez');?>"><?php echo $agent_info->user_email; ?></td>
                            <td data-label="<?php echo esc_html__('Listings', 'houzez');?>"><?php echo count_user_posts( $agent->ID , 'property' );?></td>
                            <td data-label="<?php echo esc_html__('Phone', 'houzez');?>"><?php echo get_user_meta( $agent->ID, 'fave_author_phone', true); ?></td>
                            <td data-label="<?php echo esc_html__('Mobile', 'houzez');?>"><?php echo get_user_meta( $agent->ID, 'fave_author_mobile', true); ?></td>
                            <td data-label="<?php echo esc_html__('Actions', 'houzez');?>" class="text-lg-center text-start">
                                <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php echo esc_html__('Actions', 'houzez');?>">
                                <a href="#" class="action-btn" data-bs-toggle="dropdown" aria-expanded="false"><i class="houzez-icon icon-navigation-menu-horizontal"></i></a>
                                <ul class="dropdown-menu dropdown-menu3">
                                    <li><a class="dropdown-item active" href="<?php echo esc_url($agent_permalink); ?>"><i class="houzez-icon icon-share-2"></i> <?php echo esc_html__('View', 'houzez');?></a></li>
                                    <li><a class="dropdown-item" href="<?php echo esc_url($agency_agent_edit); ?>"><i class="houzez-icon icon-pencil"></i> <?php echo esc_html__('Edit', 'houzez');?> </a></li>
                                    <li><a class="houzez_delete_agency_agent dropdown-item" href="#" data-id="<?php echo intval($agent->ID); ?>" data-nonce="<?php echo esc_attr($delete_agents_nonce); ?>"><i class="houzez-icon icon-bin"></i> <?php echo esc_html__('Delete', 'houzez');?> </a></li> 
                                    <li><a class="dropdown-item" href="<?php echo esc_url($view_listings); ?>"><i class="houzez-icon icon-share-2"></i> <?php echo esc_html__('View Listings', 'houzez');?></a></li>
                                </ul> 
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
  <?php } else { ?>
    <div class="stats-box">
      <?php if (!empty($search_term)) : ?>
        <?php esc_html_e("No agents found matching your search criteria.", 'houzez'); ?> <a href="<?php echo esc_url(remove_query_arg('agent_search')); ?>"><strong><?php esc_html_e('Clear search', 'houzez'); ?></strong></a>
      <?php else : ?>
        <?php esc_html_e("You don't have any agent listed.", 'houzez'); ?> <a href="<?php echo esc_url($agency_agent_add); ?>"><strong><?php esc_html_e('Add a new agent', 'houzez'); ?></strong></a>
      <?php endif; ?>
    </div>
  <?php } ?>

  <?php if ($total_pages > 0) : ?>
  <div class="houzez-sorting d-flex align-items-center justify-content-between p-4">
    <p class="m-0 small"><?php echo sprintf(esc_html__('Showing %1$s-%2$s of %3$s items', 'houzez'), $start, $end, $total_agents); ?></p>
    <div class="relative">
      <select class="form-control" onchange="var p=new URLSearchParams(location.search);p.set('per_page',this.value);p.delete('agent_paged');location.search=p.toString();">
        <?php foreach ($records_array as $pp): ?>
          <option value="<?php echo esc_attr($pp); ?>" <?php selected($agents_per_page, $pp); ?>><?php echo esc_html(sprintf(__('%s per page','houzez'), $pp)); ?></option>
        <?php endforeach; ?>
      </select>
      <span class="sort-arrow"><i class="houzez-icon icon-arrow-down-1"></i></span>
    </div>
    <div class="pagination">
      <ul class="pagination list-unstyled d-flex align-items-center justify-content-center gap-1 m-0">
        <?php if ($paged > 1) : ?>
          <li class="page-item">
            <a class="page-link" href="<?php 
              $prev_page_args = array('agent_paged' => $paged - 1);
              if (!empty($search_term)) {
                $prev_page_args['agent_search'] = $search_term;
              }
              if ($agents_per_page != 3) {
                $prev_page_args['per_page'] = $agents_per_page;
              }
              echo esc_url(add_query_arg($prev_page_args)); 
            ?>">
              <i class="houzez-icon icon-arrow-left-1"></i>
            </a>
          </li>
        <?php endif; ?>
        
        <?php
        // Determine which page numbers to show
        $show_pages = 5; // Number of page links to show
        $start_page = max(1, min($paged - floor($show_pages/2), $total_pages - $show_pages + 1));
        $end_page = min($total_pages, $start_page + $show_pages - 1);
        
        // Show first page and ellipsis if needed
        if ($start_page > 1) : ?>
          <li class="page-item">
            <a class="page-link" href="<?php 
              $page_args = array('agent_paged' => 1);
              if (!empty($search_term)) {
                $page_args['agent_search'] = $search_term;
              }
              if ($agents_per_page != 3) {
                $page_args['per_page'] = $agents_per_page;
              }
              echo esc_url(add_query_arg($page_args)); 
            ?>">1</a>
          </li>
          <?php if ($start_page > 2) : ?>
          <li class="page-item">
            <a class="page-link">...</a>
          </li>
          <?php endif;
        endif;
        
        // Show page numbers
        for ($i = $start_page; $i <= $end_page; $i++) : ?>
          <li class="page-item <?php echo ($paged == $i) ? 'active' : ''; ?>">
            <a class="page-link" href="<?php 
              $page_args = array('agent_paged' => $i);
              if (!empty($search_term)) {
                $page_args['agent_search'] = $search_term;
              }
              if ($agents_per_page != 3) {
                $page_args['per_page'] = $agents_per_page;
              }
              echo esc_url(add_query_arg($page_args)); 
            ?>"><?php echo esc_html($i); ?></a>
          </li>
        <?php endfor;
        
        // Show last page and ellipsis if needed
        if ($end_page < $total_pages) : 
          if ($end_page < $total_pages - 1) : ?>
          <li class="page-item">
            <a class="page-link">...</a>
          </li>
          <?php endif; ?>
          <li class="page-item">
            <a class="page-link" href="<?php 
              $page_args = array('agent_paged' => $total_pages);
              if (!empty($search_term)) {
                $page_args['agent_search'] = $search_term;
              }
              if ($agents_per_page != 3) {
                $page_args['per_page'] = $agents_per_page;
              }
              echo esc_url(add_query_arg($page_args)); 
            ?>"><?php echo esc_html($total_pages); ?></a>
          </li>
        <?php endif; ?>
        
        <?php if ($paged < $total_pages) : ?>
          <li class="page-item">
            <a class="page-link" href="<?php 
              $next_page_args = array('agent_paged' => $paged + 1);
              if (!empty($search_term)) {
                $next_page_args['agent_search'] = $search_term;
              }
              if ($agents_per_page != 3) {
                $next_page_args['per_page'] = $agents_per_page;
              }
              echo esc_url(add_query_arg($next_page_args)); 
            ?>">
              <i class="houzez-icon icon-arrow-right-1"></i>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  <?php endif; ?>

</div>