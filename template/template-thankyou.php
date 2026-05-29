<?php
/**
 * Template Name: Thank You & Payment Process complete
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 06/09/16
 * Time: 5:50 PM
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( !is_user_logged_in() ) {
    wp_redirect( home_url() );
}
global $houzez_local, $current_user;
wp_get_current_user();
$userID = $current_user->ID;
$is_paypal_live  =   houzez_option('paypal_api');

$user_email = $current_user->user_email;
$admin_email      =  get_bloginfo('admin_email');

$allowed_html   =   array();
$listings_admin_approved = houzez_option('listings_admin_approved');
$enable_paid_submission = houzez_option('enable_paid_submission');
$dash_properties_link = houzez_get_template_link('template/user_dashboard_properties.php');

if( $enable_paid_submission == 'per_listing' || $enable_paid_submission == 'free_paid_listing' ) {

    $price_per_submission = houzez_option('price_listing_submission');
    $price_featured_submission = houzez_option('price_featured_listing_submission');
    $currency = houzez_option('currency_paid_submission');

    $is_paypal_live  =   houzez_option('paypal_api');
    $host            =   'https://api.sandbox.paypal.com';

    if( $is_paypal_live == 'live' ){
        $host = 'https://api.paypal.com';
    }

    $return_link            =   houzez_get_template_link('template/template-thankyou.php');
    $clientId               =   houzez_option('paypal_client_id');
    $clientSecret           =   houzez_option('paypal_client_secret_key');
    $price_per_submission   =   floatval( $price_per_submission );
    $price_per_submission   =   number_format($price_per_submission, 2, '.', '');
    $submission_curency     =   esc_html( $currency );
    $headers                =   'From: My Name <myname@example.com>' . "\r\n";


    if ( isset($_GET['token']) ){
        $token    = wp_kses ( $_GET['token'], $allowed_html );

        /* Get saved data in database during execution
         -----------------------------------------------*/
        $transfered_data     = get_option('houzez_paypal_transfer');
        $prop_id             = $transfered_data[ $userID ]['property_id'];
        $saved_token         = $transfered_data[ $userID ]['paypal_token'];
        $order_id            = isset($transfered_data[ $userID ]['order_id']) ? $transfered_data[ $userID ]['order_id'] : '';
        $is_prop_featured    = $transfered_data[ $userID ]['is_prop_featured'];
        $is_prop_upgrade     = $transfered_data[ $userID ]['is_prop_upgrade'];
        $relist_mode         = $transfered_data[ $userID ]['relist_mode'];

        // Orders API v2: Capture the approved order
        $capture_url = $host.'/v2/checkout/orders/'.$order_id.'/capture';
        $json_response = houzez_execute_paypal_request( $capture_url, '{}', $saved_token );

        $paymentMethod = 'Paypal';

        $order_status = isset($json_response['status']) ? $json_response['status'] : '';
        if( $order_status == 'COMPLETED' ) {

            // Clear transfer data only after confirmed capture
            $transfered_data[$current_user->ID ] = array();
            update_option('houzez_paypal_transfer', $transfered_data);

            $time = time();
            $date = date( 'Y-m-d H:i:s', $time );

            if( $is_prop_upgrade == 1 ) {

                $invoiceID = houzez_generate_invoice( 'Upgrade to Featured','one_time', $prop_id, $date, $userID, 0, 1, '', $paymentMethod );
                update_post_meta( $invoiceID, 'invoice_payment_status', 1 );
                update_post_meta( $prop_id, 'fave_featured', 1 );
                update_post_meta( $prop_id, 'houzez_featured_listing_date', current_time( 'mysql' ) );

                $args = array(
                    'listing_title'  =>  get_the_title($prop_id),
                    'listing_id'     =>  $prop_id,
                    'invoice_no' =>  $invoiceID,
                    'listing_url'    =>  get_permalink($prop_id),
                );

                /*
                 * Send email
                 * */
                houzez_email_type( $user_email, 'featured_submission_listing', $args);
                houzez_email_type( $admin_email, 'admin_featured_submission_listing', $args);

            } else {

                update_post_meta( $prop_id, 'fave_payment_status', 'paid' );

                if( $listings_admin_approved != 'yes' ){
                    $post = array(
                        'ID'            => $prop_id,
                        'post_status'   => 'publish'
                    );

                    if( $relist_mode == "relist" ) {
                        $post['post_date'] = current_time( 'mysql' );
                    }

                    $post_id =  wp_update_post($post );
                }  else {
                    $post = array(
                        'ID'            => $prop_id,
                        'post_status'   => 'pending'
                    );

                    if( $relist_mode == "relist" ) {
                        $post['post_date'] = current_time( 'mysql' );
                    }
                    $post_id =  wp_update_post($post );
                }

                if( $is_prop_featured == 1 ) {
                    update_post_meta( $prop_id, 'fave_featured', 1 );
                    $invoiceID = houzez_generate_invoice( 'Listing with Featured','one_time', $prop_id, $date, $userID, 1, 0, '', $paymentMethod );
                } else {
                    $invoiceID = houzez_generate_invoice( 'Listing','one_time', $prop_id, $date, $userID, 0, 0, '', $paymentMethod );
                }

                update_post_meta( $invoiceID, 'invoice_payment_status', 1 );

                $args = array(
                    'listing_title'  =>  get_the_title($prop_id),
                    'listing_id'     =>  $prop_id,
                    'invoice_no'     =>  $invoiceID,
                    'listing_url'    =>  get_permalink($prop_id),
                );

                /*
                 * Send email
                 * */
                houzez_email_type( $user_email, 'paid_submission_listing', $args);
                houzez_email_type( $admin_email, 'admin_paid_submission_listing', $args);
            }

        }
    }

}  // end perlisting

else if( $enable_paid_submission == 'membership' ) {
    /*-----------------------------------------------------------------------------------*/
    // Paypal payments for membeship packages
    /*-----------------------------------------------------------------------------------*/
    if (isset($_GET['token'])) {
        $allowed_html = array();
        $token = wp_kses($_GET['token'], $allowed_html);
        $token_recursive = wp_kses($_GET['token'], $allowed_html);
        $paymentMethod = 'Paypal';
        $time = time();
        //$date = date('Y-m-d H:i:s',$time);
        $date = date_i18n( get_option('date_format').' '.get_option('time_format') );

        // get transfer data
        $save_data = get_user_meta($userID, 'houzez_paypal_package', true);
        $saved_access_token = isset($save_data['access_token']) ? $save_data['access_token'] : '';
        $pack_id = isset($save_data['package_id']) ? $save_data['package_id'] : '';
        $order_id_pkg = isset($save_data['order_id']) ? $save_data['order_id'] : '';

        $recursive = 0;
        if (isset ($save_data['recursive'])) {
            $recursive = $save_data['recursive'];
        }

        if ($recursive != 1) {
            if ( !empty($order_id_pkg) ) {

                // Orders API v2: Capture the approved order
                $host_pkg = 'https://api.sandbox.paypal.com';
                if( $is_paypal_live == 'live' ){
                    $host_pkg = 'https://api.paypal.com';
                }
                $capture_url_pkg = $host_pkg.'/v2/checkout/orders/'.$order_id_pkg.'/capture';
                $json_resp = houzez_execute_paypal_request($capture_url_pkg, '{}', $saved_access_token);

                $pkg_order_status = isset($json_resp['status']) ? $json_resp['status'] : '';
                if ($pkg_order_status == 'COMPLETED') {

                    // Clear transfer data only after confirmed capture
                    $save_data[$current_user->ID] = array();
                    update_option('houzez_paypal_package_transfer', $save_data);
                    update_user_meta($userID, 'houzez_paypal_package', '');

                    houzez_save_user_packages_record($userID, $pack_id);
                    if( houzez_check_user_existing_package_status( $current_user->ID, $pack_id ) ){
                        houzez_downgrade_package( $current_user->ID, $pack_id );
                        houzez_update_membership_package( $userID, $pack_id);
                    }else{
                        houzez_update_membership_package($userID, $pack_id);
                    }

                    $invoiceID = houzez_generate_invoice( 'package', 'one_time', $pack_id, $date, $userID, 0, 0, '', $paymentMethod, 1 );
                    update_post_meta( $invoiceID, 'invoice_payment_status', 1 );
                    update_user_meta( $userID, 'houzez_is_recurring_membership', 0 );
                    update_user_meta( $userID, 'houzez_payment_method', $paymentMethod);

                    $args = array();

                    houzez_email_type( $user_email,'purchase_activated_pack', $args );

                }
            } //end if Get
         //end recursive if condition
        } else {

            // Subscriptions API: subscription activates on PayPal approval
            // Fetch subscription details to confirm status
            $subscription_id = isset($save_data['subscription_id']) ? $save_data['subscription_id'] : '';

            if( !empty($subscription_id) ) {
                $host_check = 'https://api.sandbox.paypal.com';
                if( $is_paypal_live == 'live' ){
                    $host_check = 'https://api.paypal.com';
                }

                $url_token   = $host_check.'/v1/oauth2/token';
                $postArgs    = 'grant_type=client_credentials';
                $fresh_token = houzez_get_paypal_access_token( $url_token, $postArgs );

                $sub_url = $host_check.'/v1/billing/subscriptions/'.$subscription_id;
                $json_resp = houzez_paypal_get_request($sub_url, $fresh_token);
            } else {
                $json_resp = array();
            }

            $sub_status = isset($json_resp['status']) ? $json_resp['status'] : '';

            if( $sub_status == 'ACTIVE' || $sub_status == 'APPROVED' ) {

                $profileID = $json_resp['id'];
                $payer_id  = isset($json_resp['subscriber']['payer_id']) ? $json_resp['subscriber']['payer_id'] : '';

                houzez_save_user_packages_record($userID, $pack_id);
                if( houzez_check_user_existing_package_status( $current_user->ID, $pack_id ) ) {
                    houzez_downgrade_package( $current_user->ID, $pack_id );
                    houzez_update_membership_package( $userID, $pack_id );
                }else{
                    houzez_update_membership_package( $userID, $pack_id );
                }

                $invoiceID = houzez_generate_invoice( 'package', 'recurring', $pack_id, $date, $userID, 0, 0, '', $paymentMethod, 1 );
                update_post_meta( $invoiceID, 'invoice_payment_status', 1 );

                update_user_meta( $userID, 'houzez_paypal_recurring_profile_id', $profileID );
                update_user_meta( $userID, 'fave_paypal_profile', $profileID );
                update_user_meta( $userID, 'houzez_paypal_payer_id', $payer_id );
                update_user_meta( $userID, 'houzez_is_recurring_membership', 1 );
                update_user_meta( $userID, 'houzez_payment_method', $paymentMethod);
                update_user_meta( $userID, 'houzez_paypal_package', '');
                update_user_meta( $userID, 'houzez_membership_id', $pack_id);
                update_user_meta( $userID, 'houzez_subscription_detail_status', 'active');

                $args = array();

                houzez_email_type( $user_email,'purchase_activated_pack', $args );
            }

        } // End else

    }
}
get_header(); ?>

<section class="frontend-submission-page mt-4" style="min-height: 100vh;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="block-wrap">
                    <?php 
                    if( isset( $_GET['directy_pay'] ) && $_GET['directy_pay'] != '' ) {
                        $orderID = $_GET['directy_pay'];
                        $invoice_meta = houzez_get_invoice_meta( $orderID );
                        ?>
                        <p><strong><?php echo houzez_option('thankyou_wire_title'); ?></strong></p>
                        <ul style="text-align: left;">
                            <li><?php echo $houzez_local['order_number'].':'; ?> <strong><?php echo esc_attr($orderID); ?></strong> </li>
                            <li><?php echo $houzez_local['date'].':'; ?> <strong><?php echo get_the_date('', $orderID); ?></strong> </li>
                            <li><?php echo $houzez_local['total'].':'; ?> <strong><?php echo houzez_get_invoice_price( $invoice_meta['invoice_item_price'] );?></strong> </li>
                            <li><?php echo $houzez_local['payment_method'].':'; ?>
                                <strong>
                                    <?php if( $invoice_meta['invoice_payment_method'] == 'Direct Bank Transfer' ) {
                                        echo $houzez_local['bank_transfer'];
                                    } else {
                                        echo $invoice_meta['invoice_payment_method'];
                                    } ?>
                                </strong>
                            </li>
                        </ul>
                        <p> <?php echo houzez_option('thankyou_wire_des'); ?></p>

                    <?php
                    } else { ?>

                    <p><strong><?php echo houzez_option('thankyou_title'); ?></strong></p>
                    <p><?php echo houzez_option('thankyou_des'); ?></p>
                    <?php } ?>
                    <a class="btn btn-primary-outlined" href="<?php echo esc_url( $dash_properties_link ); ?>"><?php echo $houzez_local['goto_dash']; ?></a>
                </div><!-- dashboard-content-block -->
            </div>
        </div><!-- row -->
    </div><!-- container -->
</section><!-- frontend-submission-page -->

<?php get_footer(); ?>