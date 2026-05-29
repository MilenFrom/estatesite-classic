<?php
global $listing_id;
?>
<div class="form-group insight-search">
    <select id="insights_filter" class="selectpicker form-control bs-select-hidden" title="<?php esc_html_e('All Listings', 'houzez'); ?>" data-live-search="true">
        <option value=""><?php esc_html_e('All Listings', 'houzez'); ?></option>

        <?php

        // Calculate the total number of posts
        $total_posts = wp_count_posts('property')->publish;
        $batch_size = 500; // Adjust batch size as needed
        $iterations = ceil($total_posts / $batch_size);
        $userID     = get_current_user_id();

        for ($i = 1; $i <= $iterations; $i++) {
            $args = array(
                'post_type' => 'property',
                'posts_per_page' => $batch_size,
                'paged' => $i,
                'post_status' => 'publish',
            );

            if( houzez_is_admin() || houzez_is_editor() ) {
                if( isset( $_GET['user'] ) && $_GET['user'] != '' ) {
                    $args['author'] = intval($_GET['user']);

                } else if( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'mine' ) {
                    $args['author'] = $userID;
                }
            } else if( houzez_is_agency() ) {

                $agents = houzez_get_agency_agents($userID);
                
                if( isset( $_GET['user'] ) && $_GET['user'] != '' ) {
                    $args['author'] = intval($_GET['user']);

                } else if( isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'mine' ) {
                    $args['author'] = $userID;

                } else if( $agents ) {
                    if (!in_array($userID, $agents)) {
                        $agents[] = $userID;
                    }
                    $args['author__in'] = $agents;
                } else {
                    $args['author'] = $userID;
                }

            } else {
                $args['author'] = $userID;
            }
            
            $the_query = new WP_Query($args);

            if ($the_query->have_posts()) {
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    echo '<option ' . selected($listing_id, get_the_ID(), false) . ' value="' . intval(get_the_ID()) . '">' . get_the_title() . '</option>';
                }
            }
            wp_reset_postdata(); // Reset post data after each batch
        }

        ?>

    </select><!-- selectpicker -->
</div><!-- form-group -->

<script>
    jQuery(document).ready(function($) {
        $('#insights_filter').on('change', function() {
            var selectedValue = $(this).val();
            if (selectedValue) {
                window.location.href = window.location.pathname + '?listing_id=' + selectedValue;
            } else {
                window.location.href = window.location.pathname;
            }
        });
    });
</script>