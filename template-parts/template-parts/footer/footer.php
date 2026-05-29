<?php 
if ( !is_active_sidebar( 'footer-sidebar-1' )
    && ! is_active_sidebar( 'footer-sidebar-2' )
    && ! is_active_sidebar( 'footer-sidebar-3' )
    && ! is_active_sidebar( 'footer-sidebar-4' ) )
    return;

$footer_cols = houzez_option('footer_cols');
?>
<div class="footer-top-wrap">
	<div class="container">
		<div class="row">
			<?php
			if( $footer_cols === 'one_col' ) {
                if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                    echo '<div class="col-12">';
                        dynamic_sidebar( 'footer-sidebar-1' );
                    echo '</div>';
                }
            } elseif( $footer_cols === 'two_col' ) {
                if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                    echo '<div class="col-lg-4 col-md-4">';
                        dynamic_sidebar( 'footer-sidebar-1' );
                    echo '</div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                    echo '<div class="col-lg-8 col-md-8">';
                        dynamic_sidebar( 'footer-sidebar-2' );
                    echo '</div>';
                }
            } elseif( $footer_cols === 'three_cols_middle' ) {
                if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                    echo '<div class="col-lg-3 col-md-6 mb-4 mb-lg-0">';
                        dynamic_sidebar( 'footer-sidebar-1' );
                    echo '</div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                    echo '<div class="col-lg-6 col-md-6 mb-4 mb-lg-0">';
                        dynamic_sidebar( 'footer-sidebar-2' );
                    echo '</div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
                    echo '<div class="col-lg-3 col-md-12">';
                        dynamic_sidebar( 'footer-sidebar-3' );
                    echo '</div>';
                }
            } elseif( $footer_cols === 'three_cols' ) {
                if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                    echo '<div class="col-lg-3 col-md-6 mb-4 mb-lg-0">';
                        dynamic_sidebar( 'footer-sidebar-1' );
                    echo '</div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                    echo '<div class="col-lg-3 col-md-6 mb-4 mb-lg-0">';
                        dynamic_sidebar( 'footer-sidebar-2' );
                    echo '</div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
                    echo '<div class="col-lg-6 col-md-12">';
                        dynamic_sidebar( 'footer-sidebar-3' );
                    echo '</div>';
                }
            } elseif( $footer_cols === 'four_cols' ) {
                if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                    echo '<div class="col-lg-3 col-md-6 mb-4 mb-lg-0">';
                        dynamic_sidebar( 'footer-sidebar-1' );
                    echo '</div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                    echo '<div class="col-lg-3 col-md-6 mb-4 mb-lg-0">';
                        dynamic_sidebar( 'footer-sidebar-2' );
                    echo '</div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
                    echo '<div class="col-lg-3 col-md-6 mb-4 mb-lg-0">';
                        dynamic_sidebar( 'footer-sidebar-3' );
                    echo '</div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-4' ) ) {
                    echo '<div class="col-lg-3 col-md-6">';
                        dynamic_sidebar( 'footer-sidebar-4' );
                    echo '</div>';
                }
            } elseif( $footer_cols === 'v6' ) {
                if ( is_active_sidebar( 'footer-sidebar-1' ) ) {
                    echo '<div class="col-lg-5 col-md-6">';
                        dynamic_sidebar( 'footer-sidebar-1' );
                    echo '</div><div class="col-lg-1"><!-- empty --></div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                    echo '<div class="col-lg-3 col-md-6">';
                        dynamic_sidebar( 'footer-sidebar-2' );
                    echo '</div>';
                }
                if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
                    echo '<div class="col-lg-3 col-md-6">';
                        dynamic_sidebar( 'footer-sidebar-3' );
                    echo '</div>';
                }
            }
			?>
		</div><!-- row -->
	</div><!-- container -->
</div><!-- footer-top-wrap -->