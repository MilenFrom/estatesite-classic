<?php 
global $post, $sticky_hidden, $mobile_sticky_data, $hidden_data;
$hidden_data = '0';
if( !is_404() && !is_search() && isset($post) && is_object($post) ) {
    $adv_search_enable = get_post_meta($post->ID, 'fave_adv_search_enable', true);
    $adv_search = get_post_meta($post->ID, 'fave_adv_search', true);
}
?>
<section class="advanced-search advanced-search-nav mobile-search-nav mobile-search-trigger" data-sticky='<?php echo esc_attr( $mobile_sticky_data ); ?>'>
	<div class="container">
		<div class="advanced-search-v1">
			<div class="d-flex">
				<div class="flex-search flex-grow-1">
					<div class="form-group">
						<div class="search-icon">
							<input type="text" class="form-control" placeholder="<?php echo houzez_option('srh_mobile_title', 'Search'); ?>" onfocus="blur();">
						</div><!-- search-icon -->
					</div><!-- form-group -->
				</div><!-- flex-search -->
			</div><!-- d-flex -->
		</div><!-- advanced-search-v1 -->
	</div><!-- container -->
</section><!-- advanced-search -->

<?php get_template_part('template-parts/search/mobile-search');  ?>