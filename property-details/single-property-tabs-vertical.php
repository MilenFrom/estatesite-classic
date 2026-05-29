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

$tab_end = '</div>';
$li_start = '<li class="nav-item" role="presentation">';
$li_end = '</li>';
?>

<!--start detail content tabber-->
<div class="listing-tabs vertical-listing-tabs" role="tablist">
    <ul class="nav nav-tabs d-flex flex-column">
        <?php
        $i = 0;
        if ($layout): foreach ($layout as $key => $value) {

            if( $i == 0 ) { $a_active = 'active'; $a_selected = 'true'; } else { $a_active = ''; $a_selected = 'false'; }

            switch($key) {

                case 'description':
                    echo $li_start;
                    echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-description" type="button" role="tab" aria-controls="property-description" aria-selected="'.$a_selected.'"><i class="houzez-icon icon-task-list-plain-1"></i></button>';
                    echo $li_end;
                    $i++;
                    break;

                case 'address':
                    echo $li_start;
                    echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-address" type="button" role="tab" aria-controls="property-address" aria-selected="'.$a_selected.'"><i class="houzez-icon icon-maps"></i></button>';
                    echo $li_end;
                    $i++;
                    break;

                case 'details':
                    echo $li_start;
                    echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-details" type="button" role="tab" aria-controls="property-details" aria-selected="'.$a_selected.'"><i class="houzez-icon icon-house-nature"></i></button>';
                    echo $li_end;
                    $i++;
                    break;

                case 'features':

                    $property_features = wp_get_post_terms( get_the_ID(), 'property_feature', array("fields" => "all"));

                    if( ! empty($property_features) ) {
                        echo $li_start;
                        echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-features" type="button" role="tab" aria-controls="property-features" aria-selected="'.$a_selected.'"><i class="houzez-icon icon-cog"></i></button>';
                        echo $li_end;
                        $i++;
                    }
                    break;

                case 'floor_plans':
                    if( isset($floor_plans[0]['fave_plan_title']) && !empty( $floor_plans[0]['fave_plan_title'] ) ) {
                        echo $li_start;
                        echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-floor-plans" type="button" role="tab" aria-controls="property-floor-plans" aria-selected="'.$a_selected.'"><i class="houzez-icon icon-real-estate-dimensions-plan-1"></i></button>';
                        echo $li_end;
                        $i++;
                    }
                    break;

                case 'video':

                    if( !empty($video_url ) ) {
                        echo $li_start;
                        echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-video" type="button" role="tab" aria-controls="property-video" aria-selected="'.$a_selected.'"><i class="houzez-icon icon-social-video-youtube-clip"></i></button>';
                        echo $li_end;
                        $i++;
                    }
                    break;

                case 'virtual_tour':
                    if( !empty($virtual_tour) ) {
                        echo $li_start;
                        echo '<button class="nav-link '.$a_active.'" data-bs-toggle="tab" data-bs-target="#property-virtual-tour" type="button" role="tab" aria-controls="property-virtual-tour" aria-selected="'.$a_selected.'"><i class="houzez-icon icon-social-video-youtube-clip"></i></button>';
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
<div class="tab-content vertical-tab-content my-4" id="property-tab-content">
    <?php
    $j = 0;
    if ($layout): foreach ($layout as $key=>$value) {

        if( $j == 0 ) { $tab_active = 'show active'; } else { $tab_active = ''; }

        switch($key) {

            case 'description':
                echo '<div class="tab-pane fade '.$tab_active.'" id="property-description" role="tabpanel">';
                    get_template_part('property-details/description'); 
                echo '</div>';
                $j++;
                break;

            case 'address':
                echo '<div class="tab-pane fade '.$tab_active.'" id="property-address" role="tabpanel">';
                    get_template_part('property-details/address');
                echo '</div>';
                $j++;
                break;

            case 'details':
                echo '<div class="tab-pane fade '.$tab_active.'" id="property-details" role="tabpanel">';
                    get_template_part('property-details/detail');
                echo '</div>';
                $j++;
                break;

            case 'features':
                echo '<div class="tab-pane fade '.$tab_active.'" id="property-features" role="tabpanel">';
                    get_template_part('property-details/features');
                echo '</div>';
                $j++;
                break;

            case 'floor_plans':
                echo '<div class="tab-pane fade '.$tab_active.'" id="property-floor-plans" role="tabpanel">';
                    get_template_part('property-details/floor-plans');
                echo '</div>';
                $j++;
                break;

            case 'video':
                if( !empty($video_url ) ) {
                    echo '<div class="tab-pane fade '.$tab_active.'" id="property-video" role="tabpanel">';
                        get_template_part('property-details/video');
                    echo '</div>';
                    $j++;
                }
                break;

            case 'virtual_tour':
                if( !empty($virtual_tour) ) {
                    echo '<div class="tab-pane fade '.$tab_active.'" id="property-virtual-tour" role="tabpanel">';
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

if( $schedule_v2 != 0 && houzez_hide_schedule_tour() ) { 
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