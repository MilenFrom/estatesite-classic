<?php
$is_side_search = isset($args['is_side_search']) ? $args['is_side_search'] : false;
$columns = 'row-cols-lg-5';
if( $is_side_search ) {
	$columns = 'row-cols-lg-4';
}
?>
<div class="features-list-wrap pt-2 pb-3" role="region">
	<a class="btn-features-list d-flex align-items-center" data-bs-toggle="collapse" href="#features-list" role="button" aria-expanded="false" aria-controls="features-list">
		<i class="houzez-icon icon-add-square me-2" aria-hidden="true"></i> 
		<span id="features-heading"><?php echo houzez_option('srh_other_features', 'Other Features'); ?></span>
	</a>
	<div class="collapse" id="features-list" role="group">
		<div class="container-fluid px-2">
			<div class="features-list row row-cols-2 <?php echo esc_attr($columns); ?> pt-4 g-3" role="list">
				<?php get_template_part('template-parts/search/fields/feature-field'); ?>
			</div>
		</div>
	</div>
</div>