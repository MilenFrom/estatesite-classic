<?php
global $post;
$mcal_down_payment = '';
$currency_symbol = currency_maker();
$currency_symbol = $currency_symbol['currency'];
$mcal_terms = houzez_option('mcal_terms', 12);
$mcal_down_payment = houzez_option('mcal_down_payment', 15);
$mcal_interest_rate = houzez_option('mcal_interest_rate', 3.5);
$mcal_prop_tax_enable = houzez_option('mcal_prop_tax_enable', 1);
$mcal_prop_tax = houzez_option('mcal_prop_tax', 3000);
$mcal_hi_enable = houzez_option('mcal_hi_enable', 1);
$mcal_hi = houzez_option('mcal_hi', 1000);
$mcal_hoa_enable = houzez_option('mcal_hoa_enable', 1);
$mcal_hoa = houzez_option('mcal_hoa', 250);
$mcal_pmi_enable = houzez_option('mcal_pmi_enable', 1);
$mcal_pmi = houzez_option('mcal_pmi');
$property_price = get_post_meta($post->ID, 'fave_property_price', true); 
$property_price = intval($property_price);

if ( class_exists( 'FCC_Rates' ) && houzez_currency_switcher_enabled() && isset( $_COOKIE[ "houzez_set_current_currency" ] ) ) {

    $currency_data = Fcc_get_currency($_COOKIE['houzez_set_current_currency']);
    $currency_symbol = $currency_data['symbol'];

    if( function_exists('houzez_get_plain_price') ) {
	    $property_price = houzez_get_plain_price($property_price );
	}
}

if($property_price == 0) {
	$mcal_terms = $mcal_down_payment = $mcal_interest_rate = $mcal_prop_tax = $mcal_hi = $mcal_pmi = $mcal_hoa = $property_price = '';
}

?>
<div class="d-flex align-items-center flex-column flex-sm-row gap-4">
	<div class="mortgage-calculator-chart d-flex align-items-center mb-4" role="complementary">
		<div class="mortgage-calculator-monthly-payment-wrap w-100 text-center">
			<div id="m_monthly_val" class="mortgage-calculator-monthly-payment mb-1"></div>
			<div class="mortgage-calculator-monthly-requency"><?php echo houzez_option('spc_monthly', 'Monthly'); ?></div>
		</div>

		<canvas id="mortgage-calculator-chart" class="m-auto" width="250" height="250"></canvas>
	</div><!-- mortgage-calculator-chart -->

	<div class="mortgage-calculator-data w-100 mb-4" role="complementary">
		<ul class="list-unstyled list-lined" role="list">
			<li class="mortgage-calculator-data-1 d-flex align-items-center justify-content-between stats-data-1" role="listitem">
				<div class="list-lined-item w-100 d-flex justify-content-between py-2">	
					<span>
						<i class="houzez-icon icon-sign-badge-circle me-1" aria-hidden="true"></i> <strong><?php echo houzez_option('spc_down_payment', 'Down Payment'); ?></strong> 
					</span>
					<span id="downPaymentResult"></span>
				</div>
			</li>

			<li class="mortgage-calculator-data-1 d-flex align-items-center justify-content-between stats-data-01" role="listitem">
				<div class="list-lined-item w-100 d-flex justify-content-between py-2">	
					<span>
						<i class="houzez-icon icon-sign-badge-circle me-1" aria-hidden="true"></i> <strong><?php echo houzez_option('spc_loan_amount', 'Loan Amount'); ?></strong> 
					</span>
					<span id="loadAmountResult"></span>
				</div>
			</li>

			<li class="mortgage-calculator-data-1 d-flex align-items-center justify-content-between stats-data-1" role="listitem">
				<div class="list-lined-item w-100 d-flex justify-content-between py-2">	
					<span>
						<i class="houzez-icon icon-sign-badge-circle me-1" aria-hidden="true"></i> <strong><?php echo houzez_option('spc_monthly_mortgage_payment', 'Monthly Mortgage Payment'); ?></strong> 
					</span>
					<span id="monthlyMortgagePaymentResult"></span>
				</div>
			</li>

			<?php if($mcal_prop_tax_enable) { ?>
			<li class="mortgage-calculator-data-2 d-flex align-items-center justify-content-between stats-data-2" role="listitem">
				<div class="list-lined-item w-100 d-flex justify-content-between py-2">	
					<span>
						<i class="houzez-icon icon-sign-badge-circle me-1" aria-hidden="true"></i> <strong><?php echo houzez_option('spc_prop_tax', 'Property Tax'); ?></strong> 
					</span>
					<span id="monthlyPropertyTaxResult"></span>
				</div>
			</li>
			<?php } ?>

			<?php if($mcal_hi_enable) { ?>
			<li class="mortgage-calculator-data-3 d-flex align-items-center justify-content-between stats-data-3" role="listitem">
				<div class="list-lined-item w-100 d-flex justify-content-between py-2">	
					<span>
						<i class="houzez-icon icon-sign-badge-circle me-1" aria-hidden="true"></i> <strong><?php echo houzez_option('spc_hi', 'Home Insurance'); ?></strong> 
					</span>
					<span id="monthlyHomeInsuranceResult"></span>
				</div>
			</li>
			<?php } ?>

			<?php if($mcal_pmi_enable) { ?>
			<li class="mortgage-calculator-data-4 d-flex align-items-center justify-content-between stats-data-4" role="listitem">
				<div class="list-lined-item w-100 d-flex justify-content-between py-2">	
					<span>
						<i class="houzez-icon icon-sign-badge-circle me-1" aria-hidden="true"></i> <strong><?php echo houzez_option('spc_pmi', 'PMI'); ?></strong> 
					</span>
					<span id="monthlyPMIResult"></span>
				</div>
			</li>
			<?php } ?>

			<?php if($mcal_hoa_enable) { ?> 
			<li class="mortgage-calculator-data-5 d-flex align-items-center justify-content-between stats-data-5" role="listitem">
				<div class="list-lined-item w-100 d-flex justify-content-between py-2">
					<span>
						<i class="houzez-icon icon-sign-badge-circle me-1" aria-hidden="true"></i> <strong><?php echo houzez_option('spc_hoa', 'Monthly HOA Fees'); ?></strong> 
					</span>
					<span id="monthlyHOAResult"></span>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div><!-- mortgage-calculator-data -->
</div><!-- d-flex -->

<form id="houzez-calculator-form" method="post">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group mb-3">
				<label for="homePrice" class="form-label"><?php echo houzez_option('spc_total_amt', 'Total Amount'); ?></label>
				<div class="input-group">
					<span class="input-group-text" aria-hidden="true"><?php echo esc_attr($currency_symbol);?></span>
					<input type="text" class="form-control" id="homePrice" placeholder="<?php echo houzez_option('spc_total_amt', 'Total Amount'); ?>" value="<?php echo intval($property_price); ?>">
				</div><!-- input-group -->

			</div><!-- form-group -->
		</div><!-- col-md-6 -->

		<div class="col-md-6">
			<div class="form-group mb-3">
				<label class="form-label" for="down-payment"><?php echo houzez_option('spc_down_payment', 'Down Payment'); ?></label>
				<div class="input-group">
					<span class="input-group-text" aria-hidden="true">%</span>
					<input type="text" class="form-control" id="downPaymentPercentage" placeholder="<?php echo houzez_option('spc_down_payment', 'Down Payment'); ?>" value="<?php echo esc_attr($mcal_down_payment); ?>">
				</div><!-- input-group -->
			</div><!-- form-group -->
		</div><!-- col-md-6 -->

		<div class="col-md-6">
			<div class="form-group mb-3">
				<label class="form-label" for="annualInterestRate"><?php echo houzez_option('spc_ir', 'Interest Rate'); ?></label>
				<div class="input-group">
					<span class="input-group-text" aria-hidden="true">%</span>
					<input type="text" class="form-control" id="annualInterestRate" placeholder="<?php echo houzez_option('spc_ir', 'Interest Rate'); ?>" value="<?php echo esc_attr($mcal_interest_rate); ?>">
				</div><!-- input-group -->
			</div><!-- form-group -->
		</div><!-- col-md-6 -->
		
		<div class="col-md-6">
			<div class="form-group mb-3">
				<label class="form-label" for="loanTermInYears"><?php echo houzez_option('spc_load_term', 'Loan Terms (Years)'); ?></label>
				<div class="input-group">
					<span class="input-group-text" aria-hidden="true">
						<i class="houzez-icon icon-calendar-3"></i>
					</span>
					<input id="loanTermInYears" type="text" class="form-control" placeholder="<?php echo houzez_option('spc_load_term', 'Loan Terms (Years)'); ?>" value="<?php echo esc_attr($mcal_terms); ?>">
				</div><!-- input-group -->
			</div><!-- form-group -->
		</div><!-- col-md-6 -->

		<?php if($mcal_prop_tax_enable) { ?>
		<div class="col-md-6">
			<div class="form-group mb-3">
				<label class="form-label" for="annualPropertyTaxRate"><?php echo houzez_option('spc_prop_tax', 'Annual Property Tax Rate'); ?></label>
				<div class="input-group">
					<span class="input-group-text" aria-hidden="true">%</span>
					<input id="annualPropertyTaxRate" type="text" class="form-control" placeholder="<?php echo houzez_option('spc_prop_tax', 'Property Tax'); ?>" value="<?php echo esc_attr($mcal_prop_tax); ?>">
				</div><!-- input-group -->
			</div><!-- form-group -->
		</div><!-- col-md-6 -->
		<?php } ?>


		<?php if($mcal_hi_enable) { ?>
		<div class="col-md-6">
			<div class="form-group mb-3">
				<label class="form-label" for="annualHomeInsurance"><?php echo houzez_option('spc_hi', 'Annual Home Insurance'); ?></label>
				<div class="input-group">
					<span class="input-group-text" aria-hidden="true"><?php echo esc_attr($currency_symbol);?></span>
					<input id="annualHomeInsurance" type="text" class="form-control" placeholder="<?php echo houzez_option('spc_hi', 'Home Insurance'); ?>" value="<?php echo esc_attr($mcal_hi); ?>">
				</div><!-- input-group -->
			</div><!-- form-group -->
		</div><!-- col-md-6 -->
		<?php } ?>

		<?php if($mcal_hoa_enable) { ?>
		<div class="col-md-6">
			<div class="form-group mb-3">
				<label class="form-label" for="monthlyHOAFees"><?php echo houzez_option('spc_hoa', 'Monthly HOA Fees'); ?></label>
				<div class="input-group">
					<span class="input-group-text" aria-hidden="true"><?php echo esc_attr($currency_symbol);?></span>
					<input id="monthlyHOAFees" type="text" class="form-control" placeholder="<?php echo houzez_option('spc_hoa', 'Monthly HOA Fees'); ?>" value="<?php echo esc_attr($mcal_hoa); ?>">
				</div><!-- input-group -->
			</div><!-- form-group -->
		</div><!-- col-md-6 -->
		<?php } ?>

		<?php if($mcal_pmi_enable) { ?>
		<div class="col-md-6">
			<div class="form-group mb-3">
				<label class="form-label" for="pmi"><?php echo houzez_option('spc_pmi', 'PMI'); ?></label>
				<div class="input-group">
					<span class="input-group-text" aria-hidden="true">%</span>
					<input id="pmi" type="text" class="form-control" placeholder="<?php echo houzez_option('spc_pmi', 'PMI'); ?>" value="<?php echo esc_attr($mcal_pmi); ?>">
				</div><!-- input-group -->
			</div><!-- form-group -->
		</div><!-- col-md-6 -->
		<?php } ?>
	</div><!-- row -->
</form>