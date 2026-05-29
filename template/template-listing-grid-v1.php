<?php
/**
 * Template Name: Template listings grid v1
 * Created by Waqas Riaz.
 * Date: 16/12/15
 * Time: 3:27 PM
 * Updated: 12/01/25
 * Time: 10:30 AM
 */

get_header();

// Allow filtering of template arguments before they're passed to the common template
$args = array(
    'default_view' => 'grid',
    'item_template' => 'v1',
    'show_switch' => true
);
$args = apply_filters('houzez_grid_v1_template_args', $args);

// Allow adding content before the listing template
do_action('houzez_before_grid_v1_template');

get_template_part('template-parts/listing/listing', 'common', $args);

// Allow adding content after the listing template
do_action('houzez_after_grid_v1_template');

get_footer();