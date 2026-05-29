<?php
/**
 * Template Name: Paypal Webhook ( Recurring Payment )
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 11/09/16
 * Time: 3:30 PM
 *
 * Handles both:
 * - Legacy PayPal IPN (for existing billing agreement subscribers)
 * - PayPal Webhooks (for new Subscriptions API subscribers)
 */
$token = '';
define('DEBUG', 0);

$time = time();
$date = date_i18n( get_option('date_format').' '.get_option('time_format') );

$payload = file_get_contents( 'php://input' );

if ( empty( $payload ) ) {
    http_response_code(400);
    exit;
}

// Detect format: Webhooks send JSON, IPN sends URL-encoded form data
$json_payload = json_decode( $payload, true );

if ( json_last_error() === JSON_ERROR_NONE && !empty($json_payload['event_type']) ) {
    // ─── PayPal Webhooks (new Subscriptions API) ───
    houzez_handle_paypal_webhook( $json_payload, $payload );
} else {
    // ─── Legacy IPN (old Billing Agreements) ───
    houzez_handle_paypal_ipn( $payload );
}

/* -----------------------------------------------------------------------------------------------------------
 *  PayPal Webhook handler — for new Subscriptions API subscribers
 * ----------------------------------------------------------------------------------------------------------*/
function houzez_handle_paypal_webhook( $event, $raw_payload ) {

    $event_type = $event['event_type'];
    $resource   = isset($event['resource']) ? $event['resource'] : array();

    // Verify webhook signature
    if ( !houzez_verify_paypal_webhook_signature( $raw_payload ) ) {
        http_response_code(401);
        exit('Webhook signature verification failed');
    }

    switch ( $event_type ) {

        case 'PAYMENT.SALE.COMPLETED':
        case 'BILLING.SUBSCRIPTION.PAYMENT.COMPLETED':
            // Recurring payment received
            // PAYMENT.SALE.COMPLETED uses billing_agreement_id for subscription ID
            // BILLING.SUBSCRIPTION.PAYMENT.COMPLETED uses resource.id as subscription ID
            if ( $event_type === 'BILLING.SUBSCRIPTION.PAYMENT.COMPLETED' ) {
                $subscription_id = isset($resource['id']) ? $resource['id'] : '';
                $txn_id          = isset($resource['id']) ? $resource['id'] : '';
            } else {
                $subscription_id = isset($resource['billing_agreement_id']) ? $resource['billing_agreement_id'] : '';
                $txn_id          = isset($resource['id']) ? $resource['id'] : '';
            }

            if ( empty($subscription_id) ) {
                http_response_code(200);
                exit;
            }

            $user_id = houzez_retrive_user_by_profile( $subscription_id );

            if ( $user_id == 0 ) {
                http_response_code(200);
                exit;
            }

            $pack_id = get_user_meta( $user_id, 'package_id', true );

            // Check if already processed
            if ( !empty($txn_id) && houzez_retrive_invoice_by_taxid( $txn_id ) ) {
                http_response_code(200);
                exit;
            }

            houzez_save_user_packages_record( $user_id, $pack_id );
            houzez_update_membership_package( $user_id, $pack_id );

            $user_data  = get_userdata( $user_id );
            $user_email = $user_data->user_email;

            $args = array(
                'recurring_package_name' => get_the_title( $pack_id ),
                'merchant'               => 'Paypal'
            );
            houzez_email_type( $user_email, 'recurring_payment', $args );
            break;

        case 'BILLING.SUBSCRIPTION.CANCELLED':
        case 'BILLING.SUBSCRIPTION.SUSPENDED':
        case 'BILLING.SUBSCRIPTION.EXPIRED':
            $subscription_id = isset($resource['id']) ? $resource['id'] : '';

            if ( empty($subscription_id) ) {
                http_response_code(200);
                exit;
            }

            $user_id = houzez_retrive_user_by_profile( $subscription_id );
            if ( $user_id > 0 ) {
                update_user_meta( $user_id, 'houzez_is_recurring_membership', 0 );
                update_user_meta( $user_id, 'houzez_subscription_detail_status', 'expired' );
                update_user_meta( $user_id, 'houzez_has_stripe_recurring', 0 );
                update_user_meta( $user_id, 'houzez_paypal_recurring_profile_id', '' );
                update_user_meta( $user_id, 'fave_paypal_profile', '' );
            }
            break;
    }

    http_response_code(200);
    exit;
}


/* -----------------------------------------------------------------------------------------------------------
 *  Verify PayPal Webhook signature
 * ----------------------------------------------------------------------------------------------------------*/
function houzez_verify_paypal_webhook_signature( $raw_payload ) {

    $webhook_id = houzez_option('paypal_webhook_id');

    // If no webhook ID configured, only allow in sandbox mode
    if ( empty($webhook_id) ) {
        if ( houzez_option('paypal_api') === 'live' ) {
            return false;
        }
        return true;
    }

    $headers = getallheaders();
    $headers = array_change_key_case($headers, CASE_UPPER);

    $required_headers = array(
        'PAYPAL-TRANSMISSION-ID',
        'PAYPAL-TRANSMISSION-TIME',
        'PAYPAL-TRANSMISSION-SIG',
        'PAYPAL-CERT-URL',
        'PAYPAL-AUTH-ALGO'
    );

    foreach ($required_headers as $header) {
        if ( !isset($headers[$header]) ) {
            return false;
        }
    }

    $is_paypal_live = houzez_option('paypal_api');
    $host = 'https://api.sandbox.paypal.com';
    if ( $is_paypal_live == 'live' ) {
        $host = 'https://api.paypal.com';
    }

    $url      = $host.'/v1/oauth2/token';
    $postArgs = 'grant_type=client_credentials';
    $access_token = houzez_get_paypal_access_token($url, $postArgs);

    $verify_url = $host.'/v1/notifications/verify-webhook-signature';

    $verify_data = array(
        'auth_algo'         => $headers['PAYPAL-AUTH-ALGO'],
        'cert_url'          => $headers['PAYPAL-CERT-URL'],
        'transmission_id'   => $headers['PAYPAL-TRANSMISSION-ID'],
        'transmission_sig'  => $headers['PAYPAL-TRANSMISSION-SIG'],
        'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'],
        'webhook_id'        => $webhook_id,
        'webhook_event'     => json_decode($raw_payload, true),
    );

    $json_resp = houzez_execute_paypal_request($verify_url, json_encode($verify_data), $access_token);

    return ( isset($json_resp['verification_status']) && $json_resp['verification_status'] === 'SUCCESS' );
}


/* -----------------------------------------------------------------------------------------------------------
 *  Legacy IPN handler — for existing Billing Agreement subscribers
 * ----------------------------------------------------------------------------------------------------------*/
function houzez_handle_paypal_ipn( $payload ) {

    $payload_array = explode( '&', $payload );
    $myPost        = array();

    if ( empty( $payload_array ) ) {
        return false;
    }

    foreach ($payload_array as $keyval) {
        $keyval = explode( '=', $keyval );
        if ( count($keyval) == 2 ) {
            $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
    }

    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-validate';
    $get_magic_quotes_exists = false;
    if( function_exists('get_magic_quotes_gpc') ) {
        $get_magic_quotes_exists = true;
    }

    foreach ($myPost as $key => $value) {
        if( $get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1 ) {
            $value = urlencode(stripslashes($value));
        } else {
            $value = urlencode($value);
        }
        $req .= "&$key=$value";
    }

    // POST IPN data back to PayPal to validate
    $is_paypal_live  = houzez_option('paypal_api');
    $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";

    if( $is_paypal_live == 'live' ){
        $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
    }

    $args = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'sslverify' => false,
        'blocking' => true,
        'body' =>  $req,
    );

    $response   = wp_remote_post( $paypal_url, $args );
    $res        = '';

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        wp_die($error_message);
    } else {
        $res = wp_remote_retrieve_body( $response );
    }

    if (strcmp ($res, "VERIFIED") == 0) {

        $allowed_html   = array();
        $date = date_i18n( get_option('date_format').' '.get_option('time_format') );

        $payer_email            = isset($myPost['payer_email']) ? sanitize_text_field($myPost['payer_email']) : '';
        $amount                 = isset($myPost['amount']) ? sanitize_text_field($myPost['amount']) : '';
        $recurring_payment_id   = isset($myPost['recurring_payment_id']) ? sanitize_text_field($myPost['recurring_payment_id']) : '';

        $payment_status         = isset($myPost['payment_status']) ? sanitize_text_field($myPost['payment_status']) : '';
        $txn_id                 = isset($myPost['txn_id']) ? sanitize_text_field($myPost['txn_id']) : '';
        $txn_type               = isset($myPost['txn_type']) ? sanitize_text_field($myPost['txn_type']) : '';
        $receiver_email         = isset($myPost['receiver_email']) ? sanitize_text_field($myPost['receiver_email']) : '';
        $payer_id               = isset($myPost['payer_id']) ? sanitize_text_field($myPost['payer_id']) : '';

        $user_id                = houzez_retrive_user_by_profile($recurring_payment_id);

        // user with no profile id
        if( $user_id == 0 ) {
            exit();
        }

        $pack_id                = get_user_meta($user_id, 'package_id',true);
        $price                  = get_post_meta($pack_id, 'fave_package_price', true);

        if( $payment_status=='Completed' ) {

            // payment already processed
            if( houzez_retrive_invoice_by_taxid($txn_id) ) {
                exit();
            }

            // Received payment different than pack value
            if( $amount != $price){
                exit();
            }

            houzez_save_user_packages_record($user_id, $pack_id);
            houzez_update_membership_package($user_id, $pack_id);

            $user_data = get_userdata($user_id);
            $user_email = $user_data->user_email;

            $args = array(
                'recurring_package_name' => get_the_title($pack_id),
                'merchant'               => 'Paypal'
            );
            houzez_email_type( $user_email, 'recurring_payment', $args );

        } else {

            if($txn_type == 'recurring_payment_profile_cancel') {
                update_user_meta( $user_id, 'houzez_is_recurring_membership', 0 );
                update_user_meta( $user_id, 'houzez_subscription_detail_status', 'expired' );
                update_user_meta( $user_id, 'houzez_has_stripe_recurring', 0 );
                update_user_meta( $user_id, 'houzez_is_recurring_membership', 0 );
                update_user_meta( $user_id, 'houzez_paypal_recurring_profile_id', '' );
                update_user_meta( $user_id, 'fave_paypal_profile', '' );
            }
        }

    } else if (strcmp ($res, "INVALID") == 0) {
        exit('invalid exit');
    }
}
