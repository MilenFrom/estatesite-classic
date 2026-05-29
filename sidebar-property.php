<?php
global $post;
$agent_form = houzez_option('agent_form_sidebar');
$agent_form_sidebar_tabs = houzez_option('agent_form_sidebar_tabs');
$sidebar_meta = houzez_get_sidebar_meta(houzez_postid());
$agent_display = houzez_get_listing_data('agent_display_option');

if( isset( $_GET['agent_form']) && $_GET['agent_form'] == 'yes' ) {
    $agent_form = 1;
}
?>

<aside id="sidebar" class="sidebar-wrap mb-4">
    <?php
    if( is_singular('property') ) { 

        if( $agent_form != 0 && $agent_display != 'none' ) {

            if( $agent_form_sidebar_tabs == 1 ) {
                get_template_part( 'property-details/agent', 'form-tabs' );
            } else {
                echo '<div class="widget widget-wrap p-4 mb-4 widget-property-form">';
                get_template_part( 'property-details/agent', 'form' );
                echo '</div>';
            }
            
        } 

        if( is_active_sidebar( 'single-property' ) ) {
            dynamic_sidebar( 'single-property' );
        }
    } else {
        if(isset($sidebar_meta['specific_sidebar']) && $sidebar_meta['specific_sidebar'] == 'yes' ) {
            if( is_active_sidebar( $sidebar_meta['selected_sidebar'] ) ) {
                dynamic_sidebar( $sidebar_meta['selected_sidebar'] );
            }
        } else {
            if( is_active_sidebar( 'property-listing' ) ) {
                dynamic_sidebar( 'property-listing' );
            }
        }
    }

    ?>
</aside>