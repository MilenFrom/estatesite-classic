<?php
/**
 * Template Name: Template listings grid v4
 * Created by Waqas Riaz.
 * Date: 16/12/15
 * Time: 3:27 PM
 * Updated: 28/01/25
 * Time: 11:30 AM
 */
get_header();

// Allow filtering of template arguments before they're passed to the common template
$args = array(
    'default_view' => 'grid',
    'item_template' => 'v4',
    'columns' => 1,
    'layout' => 'default',
    'show_sidebar' => true,
    'custom_listing_class' => 'listing-view grid-view row gy-4 gx-4',
);
$args = apply_filters('houzez_grid_v4_template_args', $args);

// Allow adding content before the listing template
do_action('houzez_before_grid_v4_template');

get_template_part('template-parts/listing/listing', 'common', $args);

// Allow adding content after the listing template
do_action('houzez_after_grid_v4_template');

get_footer();