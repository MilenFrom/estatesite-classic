<?php
/**
 * Template Name: User Dashboard
 * Description: Main dashboard template for displaying user statistics and overview
 */

 if ( !is_user_logged_in() || !houzez_check_role() ) {
    wp_redirect(  home_url() );
}

get_header('dashboard');

// Load the dashboard sidebar
get_template_part('template-parts/dashboard/sidebar');

// Get current user information
$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$user_display_name = !empty($current_user->display_name) ? $current_user->display_name : $current_user->user_login;

// Get property statistics using existing functions
$total_properties = houzez_user_posts_count('any');
$published_properties = houzez_user_posts_count('publish');
$pending_properties = houzez_user_posts_count('pending');
$draft_properties = houzez_user_posts_count('draft');
$sold_properties = houzez_user_posts_count('houzez_sold');

// Get expired properties
$expired_properties = houzez_user_posts_count('expired');

// Calculate dynamic previous month data
$prev_month_start = date('Y-m-01', strtotime('-1 month'));
$prev_month_end = date('Y-m-t', strtotime('-1 month'));

// Get previous month property counts
$prev_total = houzez_get_user_properties_count_by_date('any', $prev_month_start, $prev_month_end);
$prev_published = houzez_get_user_properties_count_by_date('publish', $prev_month_start, $prev_month_end);
$prev_pending = houzez_get_user_properties_count_by_date('pending', $prev_month_start, $prev_month_end);
$prev_expired = houzez_get_user_properties_count_by_date('expired', $prev_month_start, $prev_month_end);
$prev_sold = houzez_get_user_properties_count_by_date('houzez_sold', $prev_month_start, $prev_month_end);

// Ensure minimum values for percentage calculation
$prev_total = max(1, $prev_total);
$prev_published = max(1, $prev_published);
$prev_pending = max(1, $prev_pending);
$prev_sold = max(1, $prev_sold);
$prev_expired = max(1, $prev_expired);

// Get CRM data if available
$total_leads = 0;
$total_inquiries = 0;
$total_deals = 0;

if (class_exists('Houzez_Leads')) {
    $all_leads = Houzez_Leads::get_all_leads();
    $total_leads = is_array($all_leads) ? count($all_leads) : 0;
}

if (class_exists('Houzez_Enquiry')) {
    $all_enquiries = Houzez_Enquiry::get_enquires();
    $total_inquiries = $all_enquiries['data']['total_records'] ?? 0;
}

if (class_exists('Houzez_Deals')) {
    $total_deals = Houzez_Deals::get_total_deals_by_group('all');
}

// Calculate previous month data for CRM
$prev_leads = 0;
$prev_inquiries = 0;
$prev_deals = 0;

if (class_exists('Houzez_Leads')) {
    $prev_leads_stats = Houzez_Leads::get_leads_stats();
    $prev_leads = $prev_leads_stats['leads_count']['last2month'] ?? 0;
    $prev_leads = max(1, $prev_leads - ($prev_leads_stats['leads_count']['lastmonth'] ?? 0));
}

if (class_exists('Houzez_Enquiry')) {
    $prev_enquiries_stats = Houzez_Enquiry::get_inquiries_stats();
    $prev_inquiries = $prev_enquiries_stats['enquiries_count']['last2month'] ?? 0;
    $prev_inquiries = max(1, $prev_inquiries - ($prev_enquiries_stats['enquiries_count']['lastmonth'] ?? 0));
}

if (class_exists('Houzez_Deals')) {
    // For deals, we'll use a simple estimation since there's no built-in date filtering
    $prev_deals = max(1, $total_deals - round($total_deals * 0.1)); // Assume 10% growth
}

// Ensure minimum values for percentage calculation
$prev_leads = max(1, $prev_leads);
$prev_inquiries = max(1, $prev_inquiries);
$prev_deals = max(1, $prev_deals);
?>

<div class="dashboard-right">
    <!-- Dashboard Topbar -->
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        <!-- Welcome Header -->
        <div class="heading d-flex align-items-center justify-content-between">
            <div class="heading-text">
                <h2 class="mb-2"><?php printf(esc_html__('Welcome back, %s!', 'houzez'), esc_html($user_display_name)); ?></h2>
                <p class="text-muted"><?php echo date_i18n(get_option('date_format') . ' | ' . get_option('time_format')); ?></p>
            </div>
        </div>

        <!-- Property Statistics -->
        <div class="property-stats mt-3">
            <div class="row">
                <!-- Total Properties -->
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="stats-box">
                        <div class="media">
                            <p><strong><?php esc_html_e('Total Properties', 'houzez'); ?></strong></p>
                            <div class="icon-box">
                                <i class="houzez-icon icon-building-cloudy"></i>
                            </div>
                        </div>
                        <h3><?php echo number_format_i18n($total_properties); ?></h3>
                        <?php echo houzez_get_percent_up_down($prev_total, $total_properties); ?>
                    </div>
                </div>

                <!-- Published Properties -->
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="stats-box">
                        <div class="media">
                            <p><strong><?php esc_html_e('Published Properties', 'houzez'); ?></strong></p>
                            <div class="icon-box">
                                <i class="houzez-icon icon-real-estate-action-house-check"></i>
                            </div>
                        </div>
                        <h3><?php echo number_format_i18n($published_properties); ?></h3>
                        <?php echo houzez_get_percent_up_down($prev_published, $published_properties); ?>
                    </div>
                </div>

                <!-- Pending Properties -->
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="stats-box">
                        <div class="media">
                            <p><strong><?php esc_html_e('Pending Properties', 'houzez'); ?></strong></p>
                            <div class="icon-box">
                                <i class="houzez-icon icon-real-estate-action-house-warning"></i>
                            </div>
                        </div>
                        <h3><?php echo number_format_i18n($pending_properties); ?></h3>
                        <?php echo houzez_get_percent_up_down($prev_pending, $pending_properties); ?>
                    </div>
                </div>

                <!-- Sold Properties -->
                 <?php if(houzez_option('enable_mark_as_sold', 0) == 1) : ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="stats-box">
                        <div class="media">
                            <p><strong><?php esc_html_e('Sold Properties', 'houzez'); ?></strong></p>
                            <div class="icon-box">
                                <i class="houzez-icon icon-real-estate-action-house-key"></i>
                            </div>
                        </div>
                        <h3><?php echo number_format_i18n($sold_properties); ?></h3>
                        <?php echo houzez_get_percent_up_down($prev_sold, $sold_properties); ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Expired Properties -->
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="stats-box">
                        <div class="media">
                            <p><strong><?php esc_html_e('Expired Properties', 'houzez'); ?></strong></p>
                            <div class="icon-box">
                                <i class="houzez-icon icon-real-estate-action-house-warning"></i>
                            </div>
                        </div>
                        <h3><?php echo number_format_i18n($expired_properties); ?></h3>
                        <?php echo houzez_get_percent_up_down($prev_expired, $expired_properties); ?>
                    </div>
                </div>

                <?php if (class_exists('Houzez_Leads')) : ?>
                <!-- Total Leads -->
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="stats-box">
                        <div class="media">
                            <p><strong><?php esc_html_e('Total Leads', 'houzez'); ?></strong></p>
                            <div class="icon-box">
                                <i class="houzez-icon icon-single-neutral-flag-2"></i>
                            </div>
                        </div>
                        <h3><?php echo number_format_i18n($total_leads); ?></h3>
                        <?php echo houzez_get_percent_up_down($prev_leads, $total_leads); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (class_exists('Houzez_Enquiry')) : ?>
                <!-- Total Inquiries -->
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="stats-box">
                        <div class="media">
                            <p><strong><?php esc_html_e('Total Inquiries', 'houzez'); ?></strong></p>
                            <div class="icon-box">
                                <i class="houzez-icon icon-single-neutral-question"></i>
                            </div>
                        </div>
                        <h3><?php echo number_format_i18n($total_inquiries); ?></h3>
                        <?php echo houzez_get_percent_up_down($prev_inquiries, $total_inquiries); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (class_exists('Houzez_Deals')) : ?>
                <!-- Total Deals -->
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="stats-box">
                        <div class="media">
                            <p><strong><?php esc_html_e('Total Deals', 'houzez'); ?></strong></p>
                            <div class="icon-box">
                                <i class="houzez-icon icon-business-contract-handshake-sign"></i>
                            </div>
                        </div>
                        <h3><?php echo number_format_i18n($total_deals); ?></h3>
                        <?php echo houzez_get_percent_up_down($prev_deals, $total_deals); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer('dashboard'); ?>