<?php
/**
 * Template Name: Stripe Charge Page
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 27/06/16
 * Time: 5:18 AM
 */
require_once( get_template_directory() . '/framework/stripe-php/init.php' );
$allowed_html = array();

$current_user = wp_get_current_user();
$userID       =   $current_user->ID;
$user_email   =   $current_user->user_email;
$admin_email  =  get_bloginfo('admin_email');
$username     =   $current_user->user_login;
$submission_currency = houzez_option('currency_paid_submission');
$thankyou_page_link = houzez_get_template_link('template/template-thankyou.php');
$paymentMethod = 'Stripe';
$time = time();
//$date = date('Y-m-d H:i:s',$time);
$date = date_i18n( get_option('date_format').' '.get_option('time_format') );
$api_error = '';

$stripe_secret_key = houzez_option('stripe_secret_key');
$stripe_publishable_key = houzez_option('stripe_publishable_key');
$stripe_api = array(
    "secret_key"      => $stripe_secret_key,
    "publishable_key" => $stripe_publishable_key
);
\Stripe\Stripe::setApiKey($stripe_api['secret_key']);

/*--------------------------------------------------------------
* Webhook Start
---------------------------------------------------------------*/
$payload = @file_get_contents('php://input');
$event_json = json_decode( $payload );

if( ! empty( $payload ) ) {
   
    try {
        $stripe = new \Stripe\StripeClient( $stripe_api['secret_key'] );
        $event =  $stripe->events->retrieve(
          $event_json->id,
          []
        );
        
        // Get stripe customer id
        $customer_stripe_id = $event->data->object->customer;

        if ( 'customer.subscription.deleted' == $event->type ) {

            $deleted_subscription_id = isset( $event->data->object->id ) ? $event->data->object->id : '';

            $customer_args = array(
                'meta_key'     => 'fave_stripe_user_profile',
                'meta_value'   => $customer_stripe_id,
                'meta_compare' => '=',
            );
            $customers = get_users( $customer_args );

            if ( ! empty( $customers ) ) {

                foreach ( $customers as $customer ) {
                    // Guard against a delayed delete event for an old subscription
                    // wiping a newer subscription that the same customer already started.
                    $current_subscription_id = get_user_meta( $customer->ID, 'houzez_stripe_subscription_id', true );
                    if ( ! empty( $deleted_subscription_id ) && ! empty( $current_subscription_id )
                         && $current_subscription_id !== $deleted_subscription_id ) {
                        continue;
                    }
                    $current_membership = get_user_meta( $customer->ID, 'package_id', true );
                    houzez_stripe_cancel_subscription( $customer->ID, $current_membership );
                }
            }

        } elseif ( 'customer.subscription.created' === $event->type ) {

            $reminder = 0;

            $customer_args = array(
                'meta_key'     => 'fave_stripe_user_profile',
                'meta_value'   => $customer_stripe_id,
                'meta_compare' => '=',
            );
            $customers     = get_users( $customer_args );

            if ( ! empty( $customers ) ) {
                foreach ( $customers as $customer ) {
                    update_user_meta( $customer->ID, 'houzez_user_membership_reminder_mail', $reminder );
                }
            }

        } elseif ( 'invoice.payment_succeeded' === $event->type ) {

            // The very first invoice for a new subscription (billing_reason
            // 'subscription_create') is handled by checkout.session.completed,
            // which generates the activation invoice and sends the activation
            // email. Skip it here to avoid duplicating those for resubscribers
            // whose Stripe customer is reused.
            $invoice_billing_reason = isset( $event->data->object->billing_reason ) ? $event->data->object->billing_reason : '';
            if ( $invoice_billing_reason === 'subscription_create' ) {
                http_response_code( 200 );
                exit();
            }

            $customer_args = array(
                'meta_key'     => 'fave_stripe_user_profile',
                'meta_value'   => $customer_stripe_id,
                'meta_compare' => '=',
            );
            $customers     = get_users( $customer_args );

            if ( ! empty( $customers ) ) {
                foreach ( $customers as $customer ) {

                    $package_id = get_user_meta( $customer->ID, 'package_id', true );
                    $subscription_id  = get_user_meta( $customer->ID, 'houzez_stripe_subscription_id', true );
                    $subscription     = \Stripe\Subscription::retrieve( $subscription_id );
                    $subscription_due = $subscription->current_period_end;
                    update_user_meta( $customer->ID, 'houzez_stripe_subscription_due', $subscription_due );

                    if( $customer->ID != 0 && $package_id != 0 ) {
                        houzez_save_user_packages_record( $customer->ID, $package_id );
                        if( houzez_check_user_existing_package_status( $customer->ID, $package_id ) ) {
                            houzez_downgrade_package(  $customer->ID, $package_id  );
                            houzez_update_membership_package( $customer->ID, $package_id );
                        } else {
                            houzez_update_membership_package( $customer->ID, $package_id );
                        } 

                        $invoiceID = houzez_generate_invoice( 'package', 'recurring', $package_id, $date, $customer->ID, 0, 0, '', $paymentMethod, 1 );
                        update_post_meta( $invoiceID, 'invoice_payment_status', 1 );

                        $args = array(
                            'recurring_package_name' => get_the_title($package_id),
                            'merchant'               => 'Stripe'
                        );
                        houzez_email_type( $customer->user_email, 'recurring_payment', $args );  
                    
                    } else {
                       // echo 'no user exist';           
                    } 
                }
            }
        }
        elseif ( 'invoice.created' === $event->type ) {

            $customer_args = array(
                'meta_key'     => 'fave_stripe_user_profile',
                'meta_value'   => $customer_stripe_id,
                'meta_compare' => '=',
            );
            $customers     = get_users( $customer_args );

            if ( ! empty( $customers ) ) {
                foreach ( $customers as $customer ) {

                    $membership_id = get_user_meta( $customer->ID, 'package_id', true );
                    $reminder_user = get_user_meta( $customer->ID, 'houzez_user_membership_reminder_mail', true );
                    if ( ! empty( $membership_id ) && ! empty( $reminder_user ) ) {
                        // send payment reminder email
                    }
                    update_user_meta( $customer->ID, 'houzez_user_membership_reminder_mail', 0 );
                }
            }
        }
        elseif ( 'checkout.session.completed' === $event->type ) {

            // Server-side completion handler. Mirrors the success-URL redirect
            // logic so the WP record is updated even if the customer never
            // returns to the success URL (closed tab, network drop, etc.).
            // Idempotent via the session_id user meta written at the end.

            $session                 = $event->data->object;
            $session_id              = isset( $session->id ) ? $session->id : '';
            $session_mode            = isset( $session->mode ) ? $session->mode : '';
            $session_payment_status  = isset( $session->payment_status ) ? $session->payment_status : '';
            $session_customer_id     = isset( $session->customer ) ? $session->customer : '';
            $session_subscription_id = isset( $session->subscription ) ? $session->subscription : '';

            $metadata_user_id        = 0;
            $metadata_package_id     = 0;
            $metadata_submission_pay = 0;

            if ( ! empty( $session->metadata ) ) {
                if ( isset( $session->metadata->userID ) ) {
                    $metadata_user_id = intval( $session->metadata->userID );
                } elseif ( isset( $session->metadata->user_id ) ) {
                    $metadata_user_id = intval( $session->metadata->user_id );
                }
                if ( isset( $session->metadata->package_id ) ) {
                    $metadata_package_id = intval( $session->metadata->package_id );
                }
                if ( isset( $session->metadata->submission_pay ) ) {
                    $metadata_submission_pay = intval( $session->metadata->submission_pay );
                }
            }

            // Per-listing payments use a separate flow that lives in the
            // success-URL handler; skip them here.
            if ( $metadata_submission_pay == 1 ) {
                http_response_code( 200 );
                exit();
            }

            // Need a user and a package to do anything useful.
            if ( $metadata_user_id <= 0 || $metadata_package_id <= 0 ) {
                http_response_code( 200 );
                exit();
            }

            $user_data = get_userdata( $metadata_user_id );
            if ( ! $user_data ) {
                http_response_code( 200 );
                exit();
            }
            $customer_email = $user_data->user_email;

            if ( $session_mode === 'subscription' ) {

                if ( empty( $session_customer_id ) || empty( $session_subscription_id ) ) {
                    http_response_code( 200 );
                    exit();
                }

                // Idempotency: skip if the success-URL handler already processed
                // this exact session for this user.
                $already_processed = get_user_meta( $metadata_user_id, 'houzez_subscription_session_id', true );
                if ( ! empty( $session_id ) && $already_processed === $session_id ) {
                    http_response_code( 200 );
                    exit();
                }

                try {
                    $subscription = $stripe->subscriptions->retrieve( $session_subscription_id );
                } catch ( Exception $e ) {
                    http_response_code( 200 );
                    exit();
                }

                $subscription_current_period_start = $subscription->current_period_start;
                $subscription_current_period_end   = $subscription->current_period_end;

                $stripePlanId = '';
                if ( ! empty( $subscription->items->data[0]->plan->id ) ) {
                    $stripePlanId = $subscription->items->data[0]->plan->id;
                }

                $stripeInvoiceNumber = '';
                if ( ! empty( $subscription->latest_invoice ) ) {
                    try {
                        $invoice             = $stripe->invoices->retrieve( $subscription->latest_invoice );
                        $stripeInvoiceNumber = $invoice->number;
                    } catch ( Exception $e ) {
                        // best effort
                    }
                }

                houzez_save_user_packages_record( $metadata_user_id, $metadata_package_id );
                if ( houzez_check_user_existing_package_status( $metadata_user_id, $metadata_package_id ) ) {
                    houzez_downgrade_package( $metadata_user_id, $metadata_package_id );
                    houzez_update_membership_package( $metadata_user_id, $metadata_package_id );
                } else {
                    houzez_update_membership_package( $metadata_user_id, $metadata_package_id );
                }

                $invoiceID = houzez_generate_invoice( 'package', 'recurring', $metadata_package_id, $date, $metadata_user_id, 0, 0, '', $paymentMethod, 1 );
                update_post_meta( $invoiceID, 'invoice_payment_status', 1 );

                update_user_meta( $metadata_user_id, 'houzez_subscription_detail_status', 'active' );
                update_user_meta( $metadata_user_id, 'fave_stripe_user_profile', $session_customer_id );
                update_user_meta( $metadata_user_id, 'houzez_stripe_subscription_id', $session_subscription_id );
                update_user_meta( $metadata_user_id, 'houzez_stripe_subscription_start', $subscription_current_period_start );
                update_user_meta( $metadata_user_id, 'houzez_stripe_subscription_due', $subscription_current_period_end );
                update_user_meta( $metadata_user_id, 'houzez_has_stripe_recurring', 1 );
                update_user_meta( $metadata_user_id, 'houzez_is_recurring_membership', 1 );
                update_user_meta( $metadata_user_id, 'houzez_subscription_order_number', $stripeInvoiceNumber );
                update_user_meta( $metadata_user_id, 'houzez_subscription_session_id', $session_id );
                update_user_meta( $metadata_user_id, 'houzez_subscription_plan_id', $stripePlanId );
                update_user_meta( $metadata_user_id, 'houzez_membership_id', $metadata_package_id );
                update_user_meta( $metadata_user_id, 'houzez_payment_method', $paymentMethod );

                $args = array();
                houzez_email_type( $customer_email, 'purchase_activated_pack', $args );

            } elseif ( $session_mode === 'payment' && $session_payment_status === 'paid' ) {

                // Idempotency
                $already_processed = get_user_meta( $metadata_user_id, 'houzez_simple_package_session_id', true );
                if ( ! empty( $session_id ) && $already_processed === $session_id ) {
                    http_response_code( 200 );
                    exit();
                }

                houzez_save_user_packages_record( $metadata_user_id, $metadata_package_id );
                if ( houzez_check_user_existing_package_status( $metadata_user_id, $metadata_package_id ) ) {
                    houzez_downgrade_package( $metadata_user_id, $metadata_package_id );
                    houzez_update_membership_package( $metadata_user_id, $metadata_package_id );
                } else {
                    houzez_update_membership_package( $metadata_user_id, $metadata_package_id );
                }

                $invoiceID = houzez_generate_invoice( 'package', 'one_time', $metadata_package_id, $date, $metadata_user_id, 0, 0, '', $paymentMethod, 1 );
                update_post_meta( $invoiceID, 'invoice_payment_status', 1 );

                if ( ! empty( $session_customer_id ) ) {
                    update_user_meta( $metadata_user_id, 'fave_stripe_user_profile', $session_customer_id );
                }
                update_user_meta( $metadata_user_id, 'houzez_has_stripe_recurring', 0 );
                update_user_meta( $metadata_user_id, 'houzez_is_recurring_membership', 0 );
                update_user_meta( $metadata_user_id, 'houzez_simple_package_session_id', $session_id );
                update_user_meta( $metadata_user_id, 'houzez_payment_method', $paymentMethod );

                $args = array();
                houzez_email_type( $customer_email, 'purchase_activated_pack', $args );
            }
        }

        http_response_code( 200 );
        exit();

    } catch(\UnexpectedValueException $e) {
      // Invalid payload
      http_response_code(400);
      exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      http_response_code(400);
      exit();
    }
}

/*--------------------------------------------------------------
* Webhook End
---------------------------------------------------------------*/              

if( isset( $_GET['session_id'] ) && ! empty( $_GET['session_id'] ) && isset($_GET['mode']) && $_GET['mode'] == 'per_listing' ) { 
    $session_id = $_GET['session_id']; 

    $stripe = new \Stripe\StripeClient( $stripe_api['secret_key'] );

    // Fetch the Checkout Session to display the JSON result on the success page 
    try { 
        $stripeSessionInfo = $stripe->checkout->sessions->retrieve($session_id); 

        $userID         = $stripeSessionInfo->metadata->user_id;
        $submission_pay = $stripeSessionInfo->metadata->submission_pay;
        $is_featured    = $stripeSessionInfo->metadata->with_featured;
        $is_upgrade     = $stripeSessionInfo->metadata->is_upgrade;
        $relist_mode    = $stripeSessionInfo->metadata->relist_mode;
        $listing_id     = $stripeSessionInfo->metadata->property_id;
        $payment_status     = $stripeSessionInfo->payment_status;
        
        if( isset( $submission_pay ) && $submission_pay == 1 && $payment_status == 'paid' ) {
            
            if( isset( $is_upgrade ) && $is_upgrade == 1 ) {
                update_post_meta( $listing_id, 'fave_featured', 1 );
                update_post_meta( $listing_id, 'houzez_featured_listing_date', current_time( 'mysql' ) );
                $invoice_id = houzez_generate_invoice( 'Upgrade to Featured', 'one_time', $listing_id, $date, $userID, 0, 1, '', $paymentMethod );
                update_post_meta( $invoice_id, 'invoice_payment_status', 1 );

                $args = array(
                    'listing_title'  =>  get_the_title($listing_id),
                    'listing_id'     =>  $listing_id,
                    'invoice_no'     =>  $invoice_id,
                    'listing_url'    =>  get_permalink($listing_id),
                );

                /*
                 * Send email
                 * */
                houzez_email_type( $user_email, 'featured_submission_listing', $args);
                houzez_email_type( $admin_email, 'admin_featured_submission_listing', $args);

            } else {
                update_post_meta( $listing_id, 'fave_payment_status', 'paid' );

                $paid_submission_status    = houzez_option('enable_paid_submission');
                $listings_admin_approved = houzez_option('listings_admin_approved');

                

                if( $listings_admin_approved != 'yes'  && $paid_submission_status == 'per_listing' ){
                    $post = array(
                        'ID'            => $listing_id,
                        'post_status'   => 'publish'
                    );

                    if( isset($_POST['relist_mode']) &&  $_POST['relist_mode'] != "" ) {
                        $post['post_date'] = current_time( 'mysql' );
                    }

                    $post_id =  wp_update_post($post );
                } else {
                    $post = array(
                        'ID'            => $listing_id,
                        'post_status'   => 'pending'
                    );

                    if( isset( $relist_mode ) &&  $relist_mode != "" ) {
                        $post['post_date'] = current_time( 'mysql' );
                    }

                    $post_id =  wp_update_post($post );
                }


                if( isset( $is_featured ) && $is_featured == 1 ) {
                    update_post_meta( $listing_id, 'fave_featured', 1 );
                    $invoice_id = houzez_generate_invoice( 'Publish Listing with Featured', 'one_time', $listing_id, $date, $userID, 1, 0, '', $paymentMethod );
                } else {
                    $invoice_id = houzez_generate_invoice( 'Listing', 'one_time', $listing_id, $date, $userID, 0, 0, '', $paymentMethod );
                }
                update_post_meta( $invoice_id, 'invoice_payment_status', 1 );

                $args = array(
                    'listing_title'  =>  get_the_title($listing_id),
                    'listing_id'     =>  $listing_id,
                    'invoice_no'     =>  $invoice_id,
                    'listing_url'    =>  get_permalink($listing_id),
                );

                /*
                 * Send email
                 * */
                houzez_email_type( $user_email, 'paid_submission_listing', $args);
                houzez_email_type( $admin_email, 'admin_paid_submission_listing', $args);
            }

            wp_redirect( $thankyou_page_link ); exit;
        }

    } catch(Exception $e) {  
        $api_error = $e->getMessage();  
    } 

} else if( isset( $_GET['is_houzez_membership'] ) && $_GET['is_houzez_membership'] == 1 ) {
    if ( isset($_REQUEST['session_id']) ) {
        $session_id = $_GET['session_id']; 
    
        $stripe = new \Stripe\StripeClient( $stripe_api['secret_key'] );

        try { 
            $stripeSessionInfo = $stripe->checkout->sessions->retrieve($session_id);
            
            $stripeCustomerInfo = $stripe->customers->retrieve($stripeSessionInfo->customer);
            $stripePlanId = $stripeSessionInfo->display_items[0]->plan->id;
            $stripe_customer_id = $stripeCustomerInfo->id;

            $stripeSubscriptionInfo = $stripe->subscriptions->retrieve($stripeSessionInfo['subscription']);

            $subscription_id = $stripeSubscriptionInfo->id;
            $pack_id = $stripeSubscriptionInfo->metadata->package_id;
            $user_id = $stripeSubscriptionInfo->metadata->userID;
            $subscription_current_period_start = $stripeSubscriptionInfo->current_period_start;
            $subscription_current_period_end = $stripeSubscriptionInfo->current_period_end;

            if ( isset($stripeCustomerInfo->id) ) {

                // Idempotency: skip if the webhook (checkout.session.completed)
                // already processed this exact session.
                $already_processed = get_user_meta( $user_id, 'houzez_subscription_session_id', true );
                if ( ! empty( $session_id ) && $already_processed === $session_id ) {
                    wp_redirect( $thankyou_page_link ); exit;
                }

                $stripeInvoiceInfo = $stripe->invoices->retrieve($stripeSubscriptionInfo['latest_invoice']);
                $stripeInvoiceNumber = $stripeInvoiceInfo['number'];

                houzez_save_user_packages_record($user_id, $pack_id);
                if( houzez_check_user_existing_package_status($user_id, $pack_id) ) { 
                    houzez_downgrade_package( $user_id, $pack_id );
                    houzez_update_membership_package($user_id, $pack_id);
                } else { 
                    houzez_update_membership_package($user_id, $pack_id);
                }

                $invoiceID = houzez_generate_invoice( 'package', 'recurring', $pack_id, $date, $user_id, 0, 0, '', $paymentMethod, 1 );
                update_post_meta( $invoiceID, 'invoice_payment_status', 1 );

                /*$current_stripe_customer_id =  get_user_meta( $user_id, 'fave_stripe_user_profile', true );
                $is_stripe_recurring        =   get_user_meta( $user_id, 'houzez_has_stripe_recurring',true );
                if ($current_stripe_customer_id !=='' && $is_stripe_recurring == 1 ) {
                    if( $current_stripe_customer_id !== $stripe_customer_id ){
                        houzez_stripe_cancel_subscription();
                    }
                }*/

                update_user_meta( $user_id, 'houzez_subscription_detail_status', 'active');
                update_user_meta( $user_id, 'fave_stripe_user_profile', $stripe_customer_id );
                update_user_meta( $user_id, 'houzez_stripe_subscription_id', $subscription_id );
                update_user_meta( $user_id, 'houzez_stripe_subscription_start', $subscription_current_period_start );
                update_user_meta( $user_id, 'houzez_stripe_subscription_due', $subscription_current_period_end );
                update_user_meta( $user_id, 'houzez_has_stripe_recurring', 1 );
                update_user_meta( $user_id, 'houzez_is_recurring_membership', 1 );

                update_user_meta( $user_id, 'houzez_subscription_order_number', $stripeInvoiceNumber);
                update_user_meta( $user_id, 'houzez_subscription_session_id', $_REQUEST['session_id']);
                update_user_meta( $user_id, 'houzez_subscription_plan_id', $stripePlanId);
                update_user_meta( $user_id, 'houzez_membership_id', $pack_id);
                update_user_meta( $user_id, 'houzez_payment_method', $paymentMethod);

                $args = array();
                houzez_email_type( $user_email,'purchase_activated_pack', $args );

                wp_redirect( $thankyou_page_link ); exit;

            }

            //echo '<pre>';
            //echo $stripe_customer_id.' = '.$user_id.' = '.$subscription_current_period_end;
            //print_r($stripeInvoiceInfo);

        } catch(Exception $e) {  
            $api_error = $e->getMessage();  
        } 
    }

} else if ( isset( $_GET['mode'] ) && $_GET['mode'] == 'simple_package' ) { 

  if ( isset($_REQUEST['session_id']) ) {
      $session_id = $_GET['session_id']; 
  
      $stripe = new \Stripe\StripeClient( $stripe_api['secret_key'] );
      try {
          $stripeSessionInfo = $stripe->checkout->sessions->retrieve($session_id); 
          $user_id         = $stripeSessionInfo->metadata->user_id;
          $pack_id   = $stripeSessionInfo->metadata->package_id;
          $payment_status     = $stripeSessionInfo->payment_status; 

          $stripe_customer_id = '';
          
          if($stripeSessionInfo->customer != "") {
              $stripeCustomerInfo = $stripe->customers->retrieve($stripeSessionInfo->customer);

              $stripe_customer_id = $stripeCustomerInfo->id;
          }

          if ( $payment_status == 'paid' ) {

              // Idempotency: skip if the webhook (checkout.session.completed)
              // already processed this exact session.
              $already_processed = get_user_meta( $user_id, 'houzez_simple_package_session_id', true );
              if ( ! empty( $session_id ) && $already_processed === $session_id ) {
                  wp_redirect( $thankyou_page_link ); exit;
              }

              houzez_save_user_packages_record($user_id, $pack_id);
              if( houzez_check_user_existing_package_status($user_id, $pack_id) ) { 
                  houzez_downgrade_package( $user_id, $pack_id );
                  houzez_update_membership_package($user_id, $pack_id);
              } else { 
                  houzez_update_membership_package($user_id, $pack_id);
              }

              $invoiceID = houzez_generate_invoice( 'package', 'one_time', $pack_id, $date, $user_id, 0, 0, '', $paymentMethod, 1 );
              update_post_meta( $invoiceID, 'invoice_payment_status', 1 );

              update_user_meta( $user_id, 'fave_stripe_user_profile', $stripe_customer_id );
              update_user_meta( $user_id, 'houzez_has_stripe_recurring', 0 );
              update_user_meta( $user_id, 'houzez_is_recurring_membership', 0 );
              update_user_meta( $user_id, 'houzez_simple_package_session_id', $_REQUEST['session_id']);
              update_user_meta( $user_id, 'houzez_payment_method', $paymentMethod);

              $args = array();
              houzez_email_type( $user_email,'purchase_activated_pack', $args );

                wp_redirect( $thankyou_page_link ); exit;
 
             }
 
             //echo '<pre>';
             //echo $stripe_customer_id.' = '.$user_id.' = '.$subscription_current_period_end;
             //print_r($stripeInvoiceInfo);
 
         } catch(Exception $e) {  
             $api_error = $e->getMessage();  
         } 
     }
 }
