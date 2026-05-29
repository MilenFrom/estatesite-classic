<div class="offcanvas offcanvas-end offcanvas-lead" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" id="offcanvasLead" aria-labelledby="offcanvasRightLabel">
	<?php get_template_part('template-parts/dashboard/loader'); ?>
	<div class="offcanvas-head">
		<h5><?php esc_html_e('Add New Lead', 'houzez'); ?></h5>  
		<a href="#" class="close-btn" data-bs-dismiss="offcanvas"><i class="houzez-icon icon-close"></i></a> 
	</div>
	
	<div class="offcanvas-body">
		<div class="filter-inner">
			<form id="lead-form">
			<input type="hidden" name="action" value="houzez_crm_add_lead">
			<div class="offcanvas-box">
				<h4><?php esc_html_e('Information', 'houzez'); ?></h4>
				<div class="mb-3">
					<label><?php esc_html_e('Title*', 'houzez'); ?></label>
					<select id="prefix" name="prefix" class="selectpicker form-control bs-select-hidden" title="<?php esc_html_e('Select', 'houzez'); ?>">
						<option value=""><?php esc_html_e('Select', 'houzez'); ?></option>
						<?php
						$prefix = hcrm_get_option('prefix', 'hcrm_lead_settings', esc_html__('Mr, Mrs, Ms, Miss, Dr, Prof, Mr & Mrs', 'houzez'));
						if(!empty($prefix)) {

							$prefixs = explode(',', $prefix);
							foreach( $prefixs as $prefix ) {
								echo '<option value="'.trim($prefix).'">'.esc_attr($prefix).'</value>';
							}
						}
						?>

					</select>
				</div>
				<div class="mb-3">
					<label><?php esc_html_e('Full Name', 'houzez'); ?></label>
					<input class="form-control" id="name" name="name" placeholder="<?php esc_html_e('Enter your full name', 'houzez'); ?>" type="text">
				</div>
				<div class="mb-3">
					<label><?php esc_html_e('First Name', 'houzez'); ?></label>
					<input class="form-control" id="first_name" name="first_name" placeholder="<?php esc_html_e('Enter your first name', 'houzez'); ?>" type="text">
				</div>
				<div class="mb-3">
					<label><?php esc_html_e('Last Name', 'houzez'); ?></label>
					<input class="form-control" id="last_name" name="last_name" placeholder="<?php esc_html_e('Enter your lastname', 'houzez'); ?>" type="text">
				</div>
				<div class="mb-3">
					<label><?php esc_html_e('Type', 'houzez'); ?></label>
					<input class="form-control" id="user_type" name="user_type" placeholder="<?php esc_html_e('Enter user type (buyer, agent etc)', 'houzez'); ?>" type="text">
				</div>
			</div>
			<div class="offcanvas-box">
				<h4><?php esc_html_e('Contacts', 'houzez'); ?></h4>
				<div class="mb-3">
					<label><?php esc_html_e('Email', 'houzez'); ?></label>
					<input class="form-control" id="email" name="email" placeholder="<?php esc_html_e('Enter the email', 'houzez'); ?>" type="text">
				</div><!-- col-md-6 col-sm-12 -->
				<div class="mb-3">
					<label><?php esc_html_e('Mobile', 'houzez'); ?></label>
					<input class="form-control" id="mobile" name="mobile" placeholder="<?php esc_html_e('Enter the mobile phone number', 'houzez'); ?>" type="text">
				</div><!-- col-md-6 col-sm-12 -->
				<div class="mb-3">
					<label><?php esc_html_e('Home Phone', 'houzez'); ?></label>
					<input class="form-control" id="home_phone" name="home_phone" placeholder="<?php esc_html_e('Enter the home phone number', 'houzez'); ?>" type="text">
				</div><!-- col-md-6 col-sm-12 -->
				<div class="mb-3">
					<label><?php esc_html_e('Work', 'houzez'); ?></label>
					<input class="form-control" id="work_phone" name="work_phone" placeholder="<?php esc_html_e('Enter the work phone number', 'houzez'); ?>" type="text">
				</div><!-- col-md-6 col-sm-12 -->
			</div>
			<div class="offcanvas-box">
				<h4><?php esc_html_e('Address', 'houzez'); ?></h4>
				<div class="mb-3">
					<label><?php esc_html_e('Address', 'houzez'); ?></label>
					<input class="form-control" id="address" name="address" placeholder="<?php esc_html_e('Enter the address', 'houzez'); ?>" type="text">
				</div><!-- col-md-6 col-sm-12 -->
				<div class="mb-3">
					<label><?php esc_html_e('Country', 'houzez'); ?></label>
					<input class="form-control" id="country" name="country" placeholder="<?php esc_html_e('Enter the country', 'houzez'); ?>" type="text">
				</div><!-- col-md-6 col-sm-12 -->
				<div class="mb-3">
					<label><?php esc_html_e('City', 'houzez'); ?></label>
					<input class="form-control" id="city" name="city" placeholder="<?php esc_html_e('Enter the city', 'houzez'); ?>" type="text">
				</div><!-- col-md-6 col-sm-12 -->
				<div class="mb-3">
					<label><?php esc_html_e('County / State', 'houzez'); ?></label>
					<input class="form-control" id="state" name="state" placeholder="<?php esc_html_e('Enter the county/state', 'houzez'); ?>" type="text">
				</div><!-- col-md-6 col-sm-12 -->
				<div class="mb-3">
					<label><?php esc_html_e('Postal Code / Zip', 'houzez'); ?></label>
					<input class="form-control" id="zip" name="zip" placeholder="<?php esc_html_e('Enter the zip code', 'houzez'); ?>" type="text">
				</div><!-- col-md-6 col-sm-12 -->
			</div>
			<div class="offcanvas-box">
				<h4><?php esc_html_e('Source', 'houzez'); ?></h4>
				<div class="mb-3 insight-search">
					<select id="source" name="source" class="selectpicker form-control bs-select-hidden" title="<?php esc_html_e('Select', 'houzez'); ?>">
						<option value=""><?php esc_html_e('Select', 'houzez'); ?></option>
						<?php
						$sources = hcrm_get_option('source', 'hcrm_lead_settings', esc_html__('Website, Newspaper, Friend, Google, Facebook', 'houzez'));
						if(!empty($sources)) {

							$sources = explode(',', $sources);
							foreach( $sources as $source ) {
								echo '<option value="'.trim($source).'">'.esc_attr($source).'</value>';
							}
						}
						?>
					</select><!-- selectpicker -->
				</div><!-- form-group -->
			</div>
			<div class="offcanvas-box">
				<h4><?php esc_html_e('Social', 'houzez'); ?></h4>
				<div class="mb-3">
					<label><?php esc_html_e('Facebook', 'houzez'); ?></label>
					<input class="form-control" id="facebook" name="facebook" placeholder="<?php esc_html_e('Enter facebook profile url', 'houzez'); ?>" type="text">
				</div>
				<div class="mb-3">
					<label><?php esc_html_e('Twitter', 'houzez'); ?></label>
					<input class="form-control" id="twitter" name="twitter" placeholder="<?php esc_html_e('Enter twitter profile url', 'houzez'); ?>" type="text">
				</div>
				<div class="mb-3">
					<label><?php esc_html_e('LinkedIn', 'houzez'); ?></label>
					<input class="form-control" id="linkedin" name="linkedin" placeholder="<?php esc_html_e('Enter linkedin profile url', 'houzez'); ?>" type="text">
				</div>
			</div>

			<div class="offcanvas-box">
				<h4><?php esc_html_e('Private Note', 'houzez'); ?></h4>
				<div class="mb-3">
					<textarea class="form-control" rows="5" id="private_note" name="private_note"></textarea>
				</div>
			</div>
			<input type="hidden" name="dashboard_lead" value="yes">
			<div id="lead-msgs"></div>	
			</form>
		</div>
	</div>
	<div class="offcanvas-footer">
	<button id="add_new_lead" type="button" class="btn btn-primary w-100">
		<?php esc_html_e('Save', 'houzez'); ?>
	</button>
	</div>
</div><!-- dashboard-slide-panel-wrap -->