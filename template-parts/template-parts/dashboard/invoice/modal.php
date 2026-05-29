<?php
global $houzez_local, $dashboard_invoices;

$userID = 0;

$invoice_id = get_the_ID();

// Get user ID from the invoice post meta or similar
//$user_id_from_invoice = get_post_field('post_author', $invoice_id);
$user_id_from_invoice = get_post_meta($invoice_id, 'HOUZEZ_invoice_buyer', true);

// Get user info by user ID
$user_info = get_userdata($user_id_from_invoice);

// Check if user exists
if ($user_info) {
    $userID = $user_info->ID ?? 0;
    $user_login = $user_info->user_login;
    $user_email = $user_info->user_email;
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;
} 

$user_address = get_user_meta( $userID, 'fave_author_address', true);
if( !empty($first_name) && !empty($last_name) ) {
    $fullname = $first_name.' '.$last_name;
} else if(!empty($current_user->display_name)) {
    $fullname = $current_user->display_name ?? '-';
} else {
    $fullname = $user_login ?? '';
}

$post = get_post( $invoice_id );
$invoice_data = houzez_get_invoice_meta( $invoice_id );

$publish_date = $post->post_date;
$publish_date = date_i18n( get_option('date_format'), strtotime( $publish_date ) );
$invoice_logo = houzez_option( 'invoice_logo', false, 'url' );
$invoice_company_name = houzez_option( 'invoice_company_name' );
$invoice_address = houzez_option( 'invoice_address' );
$invoice_phone = houzez_option( 'invoice_phone' );
$invoice_additional_info = houzez_option( 'invoice_additional_info' );
$invoice_thankyou = houzez_option( 'invoice_thankyou' );
$invoice_status = get_post_meta(  get_the_ID(), 'invoice_payment_status', true );


$billing_for_if = get_post_meta( $invoice_id, 'HOUZEZ_invoice_for', true );
?>
<div class="modal fade invoice-modal" id="invoice-modal-<?php echo get_the_ID(); ?>" tabindex="-1" aria-labelledby="invoice-modal-label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title fs-5" id="invoice-modal-label"><p><?php echo $houzez_local['invoice_details']; ?></p></h4>
        <a href="#" class="close-btn" data-bs-dismiss="modal" aria-label="Close"><i class="houzez-icon icon-close"></i></a> 
      </div>
      <div class="modal-body">
        <?php if( !empty($invoice_logo) ) { ?>
          <img class="img-fluid mb-3"  src="<?php echo esc_url($invoice_logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
          <?php } ?>
        <div class="invoice-detail d-flex align-items-center justify-content-between pe-4">
          <div class="modal-head">
          <?php if( !empty($invoice_company_name) ) { ?>
            <address>
            <?php echo '<strong>'.esc_attr($invoice_company_name).'</strong><br>'; ?>
            <?php echo ($invoice_address); ?><br>
            <?php echo esc_attr($invoice_phone); ?>
            </address>
          <?php } ?>
          </div>
          <div class="modal-list">
            <p class="fw-bold"><?php esc_html_e('Invoice', 'houzez'); ?> #<?php echo esc_attr($invoice_id); ?></p>
            <p class="mb-3"><strong><?php esc_html_e('Date', 'houzez'); ?>:</strong> <?php echo esc_attr($publish_date); ?></p>
            
            <span class="dashboard-label <?php echo ($invoice_status == 0) ? 'bg-danger' : 'bg-success'; ?> text-white fw-bold">
            <?php
            if( $invoice_status == 0 ) {
                echo esc_html__( 'Not Paid', 'houzez' );
            } else {
                echo esc_html__( 'Paid', 'houzez' );
            }
            ?>
            </span>
          </div>
        </div>   

        <div class="invoice-bill mb-4">
          <span><?php esc_html_e('To', 'houzez'); ?></span>
        <ul>
            <li class="fw-bold"><?php echo esc_attr($fullname); ?></li>
            <li><?php echo esc_attr($user_address); ?></li>
            <li><?php echo esc_attr($user_email ?? '-'); ?></li>
          </ul>
        </div>
        
        <div class="invoce-content">
            <div class="invoice-details p-4 border rounded mb-4">
                <div class="invoice-item d-flex justify-content-between mb-3">
                    <div class="fw-bold"><?php echo $houzez_local['billing_for']; ?></div>
                    <div class="text-end">
                        <?php
                        if( $invoice_data['invoice_billion_for'] != 'package' && $invoice_data['invoice_billion_for'] != 'Package' ) {
                            if( $billing_for_if == 'listing' || $billing_for_if == 'Listing' ) {
                                echo esc_html__('Listing', 'houzez');
                            } elseif ( $billing_for_if == 'UPGRADE TO FEATURED' ) {
                                echo esc_html__('Upgrade to Featured', 'houzez');
                            } else {
                                echo esc_html($invoice_data['invoice_billion_for']);
                            }
                        } else {
                            echo esc_html__('Membership Plan', 'houzez').' '. get_the_title( get_post_meta( $invoice_id, 'HOUZEZ_invoice_item_id', true) );
                        }
                        ?>
                    </div>
                </div>
                <div class="invoice-item d-flex justify-content-between mb-3">
                    <div class="fw-bold"><?php echo $houzez_local['billing_type']; ?></div>
                    <div class="text-end">
                        <?php 
                        if( get_post_meta( $invoice_id, 'HOUZEZ_invoice_type', true ) == 'Recurring' ) {
                            echo esc_html__( 'Recurring', 'houzez' );
                        } else if ( get_post_meta( $invoice_id, 'HOUZEZ_invoice_type', true ) == 'One Time' ) {
                            echo esc_html__( 'One Time', 'houzez' );
                        } else {
                            echo esc_html( $invoice_data['invoice_billing_type'] ); 
                        }
                        ?>
                    </div>
                </div>
                <div class="invoice-item d-flex justify-content-between mb-3">
                    <div class="fw-bold"><?php echo $houzez_local['payment_method']; ?></div>
                    <div class="text-end">
                        <?php if( $invoice_data['invoice_payment_method'] == 'Direct Bank Transfer' ) {
                            echo $houzez_local['bank_transfer'];
                        } else {
                            echo $invoice_data['invoice_payment_method'];
                        } ?>
                    </div>
                </div>
                <?php 
                $invoice_tax = isset($invoice_data['invoice_tax']) ? floatval($invoice_data['invoice_tax']) : 0;
                $invoice_price = floatval($invoice_data['invoice_item_price']);
                $invoice_subtotal = $invoice_price - $invoice_tax;
                
                if($invoice_tax > 0) { ?>
                <div class="invoice-item d-flex justify-content-between mb-3">
                    <div class="fw-bold"><?php echo esc_html__('Subtotal', 'houzez'); ?></div>
                    <div class="text-end">
                        <?php echo houzez_get_invoice_price( $invoice_subtotal )?>
                    </div>
                </div>
                <div class="invoice-item d-flex justify-content-between mb-3">
                    <div class="fw-bold"><?php echo esc_html__('Tax', 'houzez'); ?></div>
                    <div class="text-end">
                        <?php echo houzez_get_invoice_price( $invoice_tax )?>
                    </div>
                </div>
                <?php } ?>
                <div class="invoice-item d-flex justify-content-between">
                    <div class="fw-bold"><?php echo $houzez_local['invoice_price']; ?></div>
                    <div class="text-end fw-bold">
                        <?php echo houzez_get_invoice_price( $invoice_data['invoice_item_price'] )?>
                    </div>
                </div>
            </div>
        </div><!-- invoce-content -->
        
        
        <?php if( !empty($invoice_additional_info) || !empty($invoice_thankyou) ) { ?>
        <div class="invoice-description mt-4 p-3 bg-light rounded">
          
          <div class="text-inner mb-3">
            <p class="fw-bold"><?php echo esc_html__('Additional Information:', 'houzez'); ?></p>
            <p><?php echo $invoice_additional_info; ?></p>
          </div>

          <div class="text-box fw-bold">
            <p><?php echo $invoice_thankyou; ?></p>
          </div>
        </div> 
      <?php } ?>

      </div>
      <div class="modal-footer">
        <ul class="d-flex align-items-center gap-2"> 
          <li><a href="javascript:void(0);" class="btn btn-primary-outlined invoice-print" data-invoice-id="<?php echo intval($invoice_id); ?>"><i class="houzez-icon icon-print-text me-2"></i><?php echo esc_html__('Print', 'houzez'); ?></a></li>
        </ul>
      </div>
    </div>
  </div>
</div> 