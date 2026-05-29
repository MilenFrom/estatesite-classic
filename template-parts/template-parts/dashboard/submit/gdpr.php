<?php
global $is_multi_steps;
?>
<div id="hz-gdpr" class="<?php echo esc_attr($is_multi_steps);?>">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option( 'cls_gdpr', 'GDPR Agreement *' ); ?></h2>
		</div>
		<div class="block-content-wrap">
			<div class="row">
				<div class="col-md-12 col-sm-12">
					<div class="form-group">
						<label class="control control--checkbox" for="gdpr_agreement" style="font-weight: 400;">
							<input type="checkbox" required id="gdpr_agreement" name="gdpr_agreement">
							<?php echo houzez_option('add-prop-gdpr-label'); ?>
							<span class="control__indicator"></span>
						</label>
					</div>
				</div>

				<div class="col-md-12 col-sm-12">
					<div class="form-group">
						<textarea rows="5" readonly="readonly" class="form-control"><?php echo houzez_option('add-prop-gdpr-agreement-content');?></textarea>
					</div>
				</div>
			</div><!-- row -->			
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- #hz-gdpr -->