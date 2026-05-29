<?php 
global $post, $ele_thumbnail_size, $image_size; 
$image_size = houzez_get_image_size_for('listing_grid_v6');

$image_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;
?>
<div class="item-listing-wrap item-wrap-v6" aria-hidden="true">
	<div class="item-wrap">
		<div class="d-flex flex-column align-items-center flex-fill h-100">
			<div class="item-header">
				<div class="listing-image-wrap">
					<div class="listing-thumb">
						<div class="listing-featured-thumb item-v6-image placeholder-glow">
							<span class="placeholder col-12 h-100" style="min-height: 250px;"></span>
						</div>
					</div>
				</div>
				<div class="preview_loader"></div>
			</div>
			<div class="item-body w-100 flex-fill d-flex flex-column justify-content-between">
				<!-- Item Title placeholder -->
				<h5 class="placeholder-glow">
					<span class="placeholder col-8"></span>
				</h5>
				<div class="d-flex flex-column amenities-price-wrap gap-3">
					<!-- Price placeholder -->
					<ul class="item-price-wrap">
						<li class="item-price mb-xl-0 placeholder-glow">
							<span class="placeholder col-6"></span>
						</li>
					</ul>
					<!-- Features placeholders -->
					<div class="item-amenities placeholder-glow">
						<span class="placeholder col-3 me-2"></span>
						<span class="placeholder col-2 me-2"></span>
						<span class="placeholder col-3"></span>
					</div>
					<!-- Button placeholder -->
					<div class="placeholder-glow">
						<span class="btn btn-primary disabled placeholder col-4" aria-disabled="true"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> 