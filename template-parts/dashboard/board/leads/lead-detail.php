<?php
global $all_enquires, $dashboard_crm;

$dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');
$all_enquires = Houzez_Enquiry::get_enquires();

$lead_id = isset($_GET['lead-id']) ? intval($_GET['lead-id']) : 0;

$lead_data = Houzez_Leads::get_lead($lead_id);

$enquires_link = add_query_arg(
    array(
        'hpage' => 'lead-detail',
        'lead-id' => $lead_id,
        'tab' => 'enquires',
    ), $dashboard_crm
);

$events_link = add_query_arg(
    array(
        'hpage' => 'lead-detail',
        'lead-id' => $lead_id,
        'tab' => 'events',
    ), $dashboard_crm
);

$viewed_link = add_query_arg(
    array(
        'hpage' => 'lead-detail',
        'lead-id' => $lead_id,
        'tab' => 'viewed',
    ), $dashboard_crm
);
$searches_link = add_query_arg(
    array(
        'hpage' => 'lead-detail',
        'lead-id' => $lead_id,
        'tab' => 'searches',
    ), $dashboard_crm
);

$notes_link = add_query_arg(
    array(
        'hpage' => 'lead-detail',
        'lead-id' => $lead_id,
        'tab' => 'notes',
    ), $dashboard_crm
);

$enquires = $events = $viewed = $searches = $notes = '';
$tab = '';
if( isset($_GET['tab']) && $_GET['tab'] == 'enquires' ) {
    $enquires = 'class=active';
    $tab = 'enquires';

} else if( isset($_GET['tab']) && $_GET['tab'] == 'events' ) {
    $events = 'class=active';
    $tab = 'events';

} else if( isset($_GET['tab']) && $_GET['tab'] == 'viewed' ) {
    $viewed = 'class=active';
    $tab = 'viewed';
} else if( isset($_GET['tab']) && $_GET['tab'] == 'searches' ) {
    $searches = 'class=active';
    $tab = 'searches';
} else if( isset($_GET['tab']) && $_GET['tab'] == 'notes' ) {
    $notes = 'class=active';
    $tab = 'notes';
}
?>

<div class="heading d-flex align-items-center justify-content-between">
  <div class="heading-text">
    <h2><?php esc_html_e('Lead Details', 'houzez'); ?></h2> 
  </div> 
</div> 

<div class="houzez-inquiry">
    <?php get_template_part('template-parts/dashboard/board/leads/lead-info'); ?>
    
    <div class="houzez-inquiry-right">
        <div class="propertie-list mt-0">
            <ul class="d-flex align-items-center gap-2">
                <li><a href="<?php echo esc_url($enquires_link); ?>" <?php echo esc_attr($enquires); ?>><?php esc_html_e('Inquiries', 'houzez'); ?></a></li>
                <li><a href="<?php echo esc_url($viewed_link); ?>" <?php echo esc_attr($viewed); ?>><?php esc_html_e('Listings Viewed', 'houzez'); ?></a></li>
                <li><a href="<?php echo esc_url($searches_link); ?>" <?php echo esc_attr($searches); ?>><?php esc_html_e('Saved Searches', 'houzez'); ?></a></li>
                <li><a href="<?php echo esc_url($notes_link); ?>" <?php echo esc_attr($notes); ?>><?php esc_html_e('Notes', 'houzez'); ?></a></li>
            </ul>
        </div> 

        <?php 
        if( $tab == 'enquires' ) {
            get_template_part('template-parts/dashboard/board/leads/lead-inquiries'); 

        } else if( $tab == 'events' ) {
            
        } else if( $tab == 'viewed' ) {
            get_template_part('template-parts/dashboard/board/leads/listing-viewed');

        } else if( $tab == 'searches' ) {
            get_template_part('template-parts/dashboard/board/leads/saved-searches');

        } else if( $tab == 'notes' ) {
            get_template_part('template-parts/dashboard/board/notes');

        }?>
    </div> 
    
    <?php get_template_part('template-parts/dashboard/board/leads/new-lead-panel'); ?>
</div> 