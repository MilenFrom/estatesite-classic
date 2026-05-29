<?php
/**
 * User: waqasriaz
 * Date: 5 Sep 2019
 */
$virtual_tour = houzez_get_listing_data('virtual_tour');

if( !empty( $virtual_tour ) ) { ?>
<div class="property-virtual-tour-wrap property-section-wrap" id="property-virtual-tour-wrap" role="region">
	<div class="block-wrap">
		<div class="block-title-wrap d-flex justify-content-between align-items-center">
			<h2><?php echo houzez_option('sps_virtual_tour', '360° Virtual Tour'); ?></h2>
		</div><!-- block-title-wrap -->
		<div class="block-content-wrap" role="presentation">
			<div class="block-virtual-video-wrap" role="presentation">
				<?php 
				// Check if the content contains either <iframe> or <embed> tags
				if (strpos($virtual_tour, '<iframe') !== false || strpos($virtual_tour, '<embed') !== false) {
					$virtual_tour = houzez_ensure_iframe_closing_tag($virtual_tour);
				    echo $virtual_tour;
				} else { 
				    $virtual_tour = '<iframe class="ratio ratio-4x3" src="'.$virtual_tour.'" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
				    echo $virtual_tour;
				}
				?>
			</div>
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- property-virtual-tour-wrap -->
<?php } ?>