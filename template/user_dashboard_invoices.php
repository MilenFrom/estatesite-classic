<?php
/**
 * Template Name: User Dashboard Invoices
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 11/09/16
 * Time: 11:00 PM
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url() );
}

global $paged, $houzez_local, $current_user, $dashboard_invoices;
$dashboard_invoices = houzez_get_template_link_2('template/user_dashboard_invoices.php');
$mine_link = add_query_arg( array('mine' => 1 ), $dashboard_invoices );

get_header('dashboard');

$invoice_status = isset($_GET['invoice_status']) ? sanitize_text_field($_GET['invoice_status']) : '';
$invoice_type = isset($_GET['invoice_type']) ? sanitize_text_field($_GET['invoice_type']) : '';
$startDate = isset($_GET['startDate']) ? sanitize_text_field($_GET['startDate']) : '';
$endDate = isset($_GET['endDate']) ? sanitize_text_field($_GET['endDate']) : '';

// Default number of properties and page number
$allowed_per_page = [10,20,50,100];
$no_of_items = isset($_GET['per_page']) && in_array( intval($_GET['per_page']), $allowed_per_page ) ? intval($_GET['per_page']) : 10;
$paged = get_query_var('paged') ?: get_query_var('page') ?: 1;

$meta_query = array();
$date_query = array();

$results_found = false;

$invoices_args = array(
    'post_type' => 'houzez_invoice',
    'posts_per_page' => $no_of_items,
    'paged' => $paged
);

if ( ! houzez_is_admin() && ! houzez_is_editor() ) { 
    $meta_query[] = array(
        'key' => 'HOUZEZ_invoice_buyer',
        'value' => get_current_user_id(),
        'compare' => '='
    );
}

if( (isset($_GET['mine']) && $_GET['mine']) ) {
    $invoices_args['author'] = get_current_user_id();
}

if( $invoice_status !='' ){
    $meta_query[] = array(
        'key' => 'invoice_payment_status',
        'value' => $invoice_status,
        'type' => 'NUMERIC',
        'compare' => '=',
    );
    $results_found = true;
}

if( $invoice_type !='' ){

    $meta_query[] = array(
        'key' => 'HOUZEZ_invoice_for',
        'value' => $invoice_type,
        'type' => 'CHAR',
        'compare' => 'LIKE',
    );

    $results_found = true;
}

if( $startDate !='' ) {
    $temp_array = array();
    $temp_array['after'] = $startDate;
    $date_query[] = $temp_array;
    $results_found = true;
}

if( $endDate !='' ){
    $temp_array = array();
    $temp_array['before'] = $endDate;
    $date_query[] = $temp_array;
    $results_found = true;
}

$meta_count = count($meta_query);
$meta_query['relation'] = 'AND';
if ($meta_count > 0) {
    $invoices_args['meta_query'] = $meta_query;
}

if( $date_query ) {
    $invoices_args['date_query'] = $date_query;
}

$invoice_query = new WP_Query($invoices_args);
$total_invoices = $invoice_query->found_posts;
?>
<!-- Load the dashboard sidebar -->
<?php get_template_part('template-parts/dashboard/sidebar'); ?>

<div class="dashboard-right">
    <!-- Dashboard Topbar --> 
    <?php get_template_part('template-parts/dashboard/topbar'); ?>

    <div class="dashboard-content">
        <div class="heading d-flex align-items-center justify-content-between">
            <div class="heading-text">
                <h2><?php echo houzez_option('dsh_invoices', 'Invoices'); ?></h2>
            </div>
        </div>

        <?php get_template_part('template-parts/dashboard/invoice/filters'); ?>

        <div class="houzez-data-content"> 
            <div class="houzez-data-table">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr> 
                                <th data-label="<?php echo $houzez_local['order']; ?>"><?php echo $houzez_local['order']; ?></th>
                                <th data-label="<?php echo $houzez_local['date']; ?>"><?php echo $houzez_local['date']; ?></th>
                                <th data-label="<?php echo $houzez_local['billing_for']; ?>"><?php echo $houzez_local['billing_for']; ?></th>  
                                <th data-label="<?php echo $houzez_local['billing_type']; ?>"><?php echo $houzez_local['billing_type']; ?></th>  
                                <th data-label="<?php echo esc_html__('Client', 'houzez'); ?>"><?php echo esc_html__('Client', 'houzez'); ?></th>
                                <th data-label="<?php echo $houzez_local['payment_method']; ?>"><?php echo $houzez_local['payment_method']; ?></th>
                                <th data-label="<?php echo $houzez_local['total']; ?>"><?php echo $houzez_local['total']; ?></th>
                                <th data-label="<?php echo $houzez_local['invoice_status']; ?>"><?php echo $houzez_local['invoice_status']; ?></th>
                                <th data-label="<?php echo esc_html__('View', 'houzez'); ?>" class="text-center"><?php echo esc_html__('View', 'houzez'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($invoice_query->have_posts()) :
                                while ($invoice_query->have_posts()) : $invoice_query->the_post();

                                    get_template_part('template-parts/dashboard/invoice/invoice-item'); 

                                    get_template_part('template-parts/dashboard/invoice/modal');

                                endwhile; 
                            endif;
                            wp_reset_postdata();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php get_template_part('template-parts/dashboard/invoice/pagination'); ?>
        </div>

    </div>
</div>

<?php get_footer('dashboard'); ?>