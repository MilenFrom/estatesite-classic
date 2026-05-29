<?php
global $enquiry, $matched_query, $lead, $dashboard_crm;
$enquiry = Houzez_Enquiry::get_enquiry($_GET['enquiry']);
$matched_query = matched_listings($enquiry->enquiry_meta);
$belong_to = isset($_GET['enquiry']) ? intval($_GET['enquiry']) : 0;

$dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');

$lead = Houzez_Leads::get_lead($enquiry->lead_id);

$total_matched_listings = '0';
if(!empty($matched_query)) {
    $total_matched_listings = $matched_query->found_posts;
}

$enquiry_notes = Houzez_CRM_Notes::get_notes($belong_to, 'enquiry');
$back_link = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$match_link = add_query_arg(
    array(
        'hpage' => 'enquiries',
        'enquiry' => $enquiry->enquiry_id,
        'tab' => 'matching-listings',
    ), $dashboard_crm
);

$notes_link = add_query_arg(
    array(
        'hpage' => 'enquiries',
        'enquiry' => $enquiry->enquiry_id,
        'tab' => 'notes',
    ), $dashboard_crm
);

$matching_listings = $notes = '';
$tab = '';
if( isset($_GET['tab']) && $_GET['tab'] == 'matching-listings' ) {
    $matching_listings = 'active';
    $tab = 'matching-listings';

} else if( isset($_GET['tab']) && $_GET['tab'] == 'notes' ) {
    $notes = 'active';
    $tab = 'notes';
} else {
    $matching_listings = 'active';
    $tab = 'matching-listings';
}
?>
<div class="heading d-flex align-items-center justify-content-between">
  <div class="heading-text">
    <h2><?php esc_html_e('Details', 'houzez'); ?></h2> 
  </div> 
</div>  

<div class="houzez-inquiry">
  <?php get_template_part('template-parts/dashboard/board/enquires/enquiry-info'); ?>
  
  <div class="houzez-inquiry-right">
    <div class="propertie-list mt-0">
        <ul class="d-flex align-items-center gap-2">
            <li>
                <a href="<?php echo esc_url($match_link); ?>" class="nav-link <?php echo esc_attr($matching_listings); ?>"><?php esc_html_e('Matching Listings', 'houzez'); ?></a>
            </li>
            <li>
                <a href="<?php echo esc_url($notes_link); ?>" class="nav-link <?php echo esc_attr($notes); ?>"><?php esc_html_e('Notes', 'houzez'); ?></a>
            </li>
        </ul>
    </div> 

    <?php 
    if( $tab == 'matching-listings' ) {
        get_template_part('template-parts/dashboard/board/match-listings'); 

    } else if( $tab == 'notes' ) {
        get_template_part('template-parts/dashboard/board/enquires/notes');
    }?> 
  </div> 

  <?php get_template_part('template-parts/dashboard/board/enquires/add-new-enquiry'); ?>
</div>  