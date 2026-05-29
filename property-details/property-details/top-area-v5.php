<?php
$fave_property_images = get_post_meta(get_the_ID(), 'fave_property_images', false);

if( !empty($fave_property_images) ) { ?>
<div class="property-top-wrap">
	<div class="property-banner" role="region">
		<div class="container d-none d-md-block" role="region">
			<?php get_template_part('property-details/partials/banner-nav'); ?>
		</div><!-- container -->
		<div class="tab-content" id="pills-tabContent" role="tablist">
			<?php get_template_part('property-details/partials/media-tabs'); ?>
		</div><!-- tab-content -->		
	</div><!-- property-banner -->
</div><!-- property-top-wrap -->
<?php } ?>
<?php get_template_part('property-details/property-title'); ?>


