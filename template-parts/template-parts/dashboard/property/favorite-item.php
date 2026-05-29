<?php
global $post;
$post_id = get_the_ID();
$listings_page = get_option('fave_listings_page');
?>
<tr>
  <td data-label="<?php echo esc_html__('Thumbnail', 'houzez'); ?>">
    <div class="image-holder">
      <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
        <?php
        $thumbnail_size = 'thumbnail';
        if( has_post_thumbnail() && get_the_post_thumbnail(get_the_ID()) != '') {
            the_post_thumbnail($thumbnail_size);
        } else {
            houzez_image_placeholder( $thumbnail_size );
        }
        ?> 
      </a>
    </div>
  </td>

  <td data-label="<?php echo esc_html__('Title', 'houzez'); ?>">
    <div class="text-box">
      <a class="fw-bold" href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php the_title(); ?></a><br>
      <address class="mb-0"><?php echo houzez_get_listing_data('property_map_address'); ?></address> 
    </div>
  </td>

  <td data-label="<?php echo esc_html__('Status', 'houzez'); ?>"><?php echo houzez_taxonomy_simple('property_status'); ?></td>
  <td data-label="<?php echo esc_html__('ID', 'houzez'); ?>"><?php echo houzez_get_listing_data('property_id'); ?></td>
  
  <td data-label="<?php echo esc_html__('Price', 'houzez'); ?>"><?php houzez_property_price_admin(); ?></td>
  
  <td data-label="<?php echo esc_html__('Type', 'houzez'); ?>"><?php echo houzez_taxonomy_simple('property_type'); ?></td>
  
  <?php if( is_user_logged_in() ): ?>
  <td data-label="<?php echo esc_html__('Date', 'houzez'); ?>">
      <?php
      echo date_i18n( get_option('date_format'), strtotime( $post->post_date ) ).' '.date_i18n( get_option('time_format'), strtotime( $post->post_date ) );
      echo '<br>';
      echo ( empty( $post->post_author ) ? __( 'by a guest', 'houzez' ) : sprintf( __( 'by %s', 'houzez' ), '<a href="' . esc_url( add_query_arg( 'user', $post->post_author, $listings_page ) ) . '">' . get_the_author() . '</a>' ) );
      ?>
  </td>
  <?php endif; ?>
  <td data-label="<?php echo esc_html__('Actions', 'houzez'); ?>" class="text-lg-center text-start">
    <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php echo esc_html__('Actions', 'houzez'); ?>">
      <a href="#" class="action-btn" data-bs-toggle="dropdown" aria-expanded="false"><i class="houzez-icon icon-navigation-menu-horizontal"></i></a>
      <ul class="dropdown-menu dropdown-menu3">
        <li><a class="dropdown-item active" target="_blank" href="<?php echo esc_url(get_permalink()); ?>"><i class="houzez-icon icon-share-2"></i> <?php esc_html_e('View', 'houzez'); ?></a></li>
        <li><a class="remove_fav dropdown-item" href="#" data-listid="<?php echo intval(get_the_ID())?>"><i class="houzez-icon icon-bin"></i><?php esc_html_e('Delete', 'houzez'); ?></a></li> 
      </ul> 
    </div>
  </td>
</tr> 