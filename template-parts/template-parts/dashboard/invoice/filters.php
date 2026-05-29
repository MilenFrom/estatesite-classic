<?php
global $houzez_local;
$invoice_status = isset($_GET['invoice_status']) ? sanitize_text_field($_GET['invoice_status']) : '';
$invoice_type = isset($_GET['invoice_type']) ? sanitize_text_field($_GET['invoice_type']) : '';
$startDate = isset($_GET['startDate']) ? sanitize_text_field($_GET['startDate']) : '';
$endDate = isset($_GET['endDate']) ? sanitize_text_field($_GET['endDate']) : '';
$invoices_page = houzez_get_template_link_2('template/user_dashboard_invoices.php');
?>
<div class="houzez-invoice-filter filter-inner mb-4">
  <form>
    <div class="form-group">
      <label><?php echo $houzez_local['start_date']; ?></label>
      <input id="startDate" type="date" placeholder="Min" class="form-control db_input_date" value="<?php echo $startDate; ?>"> 
    </div> 
    <div class="form-group">
      <label><?php echo $houzez_local['end_date']; ?></label>
      <input id="endDate" type="date" placeholder="Min" class="form-control db_input_date" value="<?php echo $endDate; ?>"> 
    </div>
    <div class="form-group">
      <label for="invoice_type"><?php echo $houzez_local['invoice_type']; ?></label>
      <div class="relative">
        <select class="form-control" id="invoice_type">
          <option value=""><?php echo $houzez_local['any']; ?></option>
          <option <?php selected( $invoice_type, 'Listing' ); ?> value="Listing"><?php echo $houzez_local['invoice_listing']; ?></option>
          <option <?php selected( $invoice_type, 'package' ); ?> value="package"><?php echo $houzez_local['invoice_package']; ?></option>
          <option <?php selected( $invoice_type, 'Listing with Featured' ); ?> value="Listing with Featured"><?php echo $houzez_local['invoice_feat_list']; ?></option>
          <option <?php selected( $invoice_type, 'Upgrade to Featured' ); ?> value="Upgrade to Featured"><?php echo $houzez_local['invoice_upgrade_list']; ?></option>
        </select>
        <a href="#" class="sort-arrow"><i class="houzez-icon icon-arrow-down-1"></i></a>
      </div>
    </div>
    <div class="form-group">
      <label for="invoice_status"><?php echo $houzez_local['invoice_status']; ?></label>
      <div class="relative">
        <select class="form-control" id="invoice_status">
          <option value=""><?php echo $houzez_local['any']; ?></option>
          <option <?php selected( $invoice_status, '1' ); ?> value="1"><?php echo $houzez_local['paid']; ?></option>
          <option <?php selected( $invoice_status, '0' ); ?> value="0"><?php echo $houzez_local['not_paid']; ?></option>
        </select>
        <a href="#" class="sort-arrow"><i class="houzez-icon icon-arrow-down-1"></i></a>
      </div>
    </div>  
    <input type="hidden" id="invoices_page" value="<?php echo esc_url($invoices_page); ?>">
  </form>
</div>

