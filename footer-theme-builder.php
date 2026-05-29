</main><!-- .main-wrap start in header.php-->

<?php 

if ( ! houzez_is_splash() ) {
    if ( houzez_is_dashboard() ) {
        get_template_part('template-parts/dashboard/dashboard-footer'); 
    } else {

        do_action( 'houzez_before_footer' );

        if ((!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('footer')) && 
            (!houzez_is_half_map() || (houzez_is_half_map() && houzez_option('halfmap-footer', 1) == 1))) 
        {
            
            if( function_exists('fts_footer_enabled') && fts_footer_enabled() ) {
                do_action( 'houzez_footer_studio' );
            } else { 
                do_action( 'houzez_footer' );
            }
        }
    }
}

do_action( 'houzez_after_footer' );

do_action( 'houzez_before_wp_footer' );

wp_footer(); ?>

</body>
</html>