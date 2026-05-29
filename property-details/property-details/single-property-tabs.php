<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 21/01/16
 * Time: 7:26 PM
 */
global $post, $top_area, $map_street_view;
$layout = houzez_option('property_blocks_tabs');
$schedule = houzez_option('houzez_tabs_schedule');
$schedule_v2 = houzez_option('houzez_tabs_schedule_v2');
$energy_class = houzez_option('houzez_energy_class');
$availability_calendar = houzez_option('houzez_availability_calendar');
$show_similer = houzez_option( 'houzez_similer_properties' );
$property_reviews = houzez_option( 'property_reviews' );
$houzez_mortgage = houzez_option( 'houzez_mortgage' );
$houzez_sublisting = houzez_option( 'houzez_sublisting' );
$layout = isset($layout['enabled']) ? $layout['enabled'] : [];
$floor_plans = get_post_meta( $post->ID, 'floor_plans', true );
$video_url = get_post_meta( $post->ID, 'fave_video_url', true );
$virtual_tour = get_post_meta( $post->ID, 'fave_virtual_tour', true );

$li_start = '<li class="nav-item">';
$li_end = '</li>';
?>

<!--start detail content tabber-->
<div class="listing-tabs horizontal-listing-tabs" role="tablist">
    <ul class="nav nav-tabs nav-justified">
        <?php
        $i = 0;
        if ($layout): foreach ($layout as $key => $value) {

            if( $i == 0 ) { $a_active = 'active'; } else { $a_active = ''; }

            switch($key) {

                case 'description':
                    echo $li_start;
                    echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-description" type="button" role="tab" aria-controls="property-description" aria-selected="'.($i == 0 ? 'true' : 'false').'">'.houzez_option('sps_description', 'Description').'</button>';
                    echo $li_end;
                    $i++;
                    break;

                case 'address':
                    echo $li_start;
                    echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-address" type="button" role="tab" aria-controls="property-address" aria-selected="'.($i == 0 ? 'true' : 'false').'">'.houzez_option('sps_address', 'Address').'</button>';
                    echo $li_end;
                    $i++;
                    break;

                case 'details':
                    echo $li_start;
                    echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-details" type="button" role="tab" aria-controls="property-details" aria-selected="'.($i == 0 ? 'true' : 'false').'">'.houzez_option('sps_details', 'Details').'</button>';
                    echo $li_end;
                    $i++;
                    break;

                case 'features':

                    $property_features = wp_get_post_terms( get_the_ID(), 'property_feature', array("fields" => "all"));

                    if( ! empty($property_features) ) {
                        echo $li_start;
                        echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-features" type="button" role="tab" aria-controls="property-features" aria-selected="'.($i == 0 ? 'true' : 'false').'">'.houzez_option('sps_features', 'Features').'</button>';
                        echo $li_end;
                        $i++;
                    }
                    break;

                case 'floor_plans':
                    if( isset($floor_plans[0]['fave_plan_title']) && !empty( $floor_plans[0]['fave_plan_title'] ) ) {
                        echo $li_start;
                        echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-floor-plans" type="button" role="tab" aria-controls="property-floor-plans" aria-selected="'.($i == 0 ? 'true' : 'false').'">'.houzez_option('sps_floor_plans', 'Floor Plans').'</button>';
                        echo $li_end;
                        $i++;
                    }
                    break;

                case 'video':

                    if( !empty($video_url ) ) {
                        echo $li_start;
                        echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-video" type="button" role="tab" aria-controls="property-video" aria-selected="'.($i == 0 ? 'true' : 'false').'">'.houzez_option('sps_video', 'Video').'</button>';
                        echo $li_end;
                        $i++;
                    }
                    break;

                case 'virtual_tour':

                    if( !empty($virtual_tour) ) {
                        echo $li_start;
                        echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-virtual-tour" type="button" role="tab" aria-controls="property-virtual-tour" aria-selected="'.($i == 0 ? 'true' : 'false').'">'.houzez_option('sps_virtual_tour', '360° Virtual Tour').'</button>';
                        echo $li_end;
                        $i++;
                    }
                    break;

            }
        }

        endif;
        ?>

    </ul>
</div>

    <!--start tab-content-->
    <div class="tab-content horizontal-tab-content" id="property-tab-content">
        <?php
        $j = 0;
        if ($layout): foreach ($layout as $key=>$value) {

            if( $j == 0 ) { $tab_active = 'show active'; } else { $tab_active = ''; }

            switch($key) {

                case 'description':
                    echo '<div class="tab-pane fade '.$tab_active.'" id="property-description" role="tabpanel" aria-labelledby="property-description-tab">';
                        get_template_part('property-details/description'); 
                    echo '</div>';
                    $j++;
                    break;

                case 'address':
                    echo '<div class="tab-pane fade '.$tab_active.'" id="property-address" role="tabpanel" aria-labelledby="property-address-tab">';
                        get_template_part('property-details/address');
                    echo '</div>';
                    $j++;
                    break;

                case 'details':
                    echo '<div class="tab-pane fade '.$tab_active.'" id="property-details" role="tabpanel" aria-labelledby="property-details-tab">';
                        get_template_part('property-details/detail');
                    echo '</div>';
                    $j++;
                    break;

                case 'features':
                    echo '<div class="tab-pane fade '.$tab_active.'" id="property-features" role="tabpanel" aria-labelledby="property-features-tab">';
                        get_template_part('property-details/features');
                    echo '</div>';
                    $j++;
                    break;

                case 'floor_plans':
                    echo '<div class="tab-pane fade '.$tab_active.'" id="property-floor-plans" role="tabpanel" aria-labelledby="property-floor-plans-tab">';
                        get_template_part('property-details/floor-plans');
                    echo '</div>';
                    $j++;
                    break;

                case 'video':

                    if( !empty($video_url ) ) {
                        echo '<div class="tab-pane fade '.$tab_active.'" id="property-video" role="tabpanel" aria-labelledby="property-video-tab">';
                            get_template_part('property-details/video');
                        echo '</div>';
                        $j++;
                    }
                    break;

                case 'virtual_tour':
                    if( !empty($virtual_tour) ) {
                        echo '<div class="tab-pane fade '.$tab_active.'" id="property-virtual-tour" role="tabpanel" aria-labelledby="property-virtual-tour-tab">';
                            get_template_part('property-details/virtual-tour');
                        echo '</div>';
                        $j++;
                    }
                    break;
            }
        }

        endif;
        ?>

    </div>
    <!--end tab-content-->
    
    <?php
    if( $top_area != 'v6' && $top_area != 'v7' ) {
        get_template_part('property-details/overview'); 
    }

    if( $houzez_mortgage != 0 && houzez_hide_calculator() ) {
        get_template_part('property-details/mortgage-calculator');
    }

    if($houzez_sublisting != 0) {
        get_template_part('property-details/sub-listing-main');
    }

    if($energy_class != 0) {
        get_template_part('property-details/energy');
    }

    get_template_part('property-details/walkscore');

    get_template_part('property-details/yelp-nearby');

    if( $schedule != 0 && houzez_hide_schedule_tour() ) { 
        get_template_part('property-details/schedule-a-tour');
    }

    if( $schedule_v2 != 0  && houzez_hide_schedule_tour() ) { 
        get_template_part('property-details/schedule-a-tour-v2');
    }

    if($availability_calendar != 0) {
        get_template_part('property-details/availability-calendar');
    }

    if( houzez_option('tabs_agent_bottom', 1) ) {
        get_template_part('property-details/agent-form-bottom');
    }

    if($property_reviews != 0) {
        get_template_part('property-details/reviews');
    }

    get_template_part('property-details/similar-properties');
    ?>
