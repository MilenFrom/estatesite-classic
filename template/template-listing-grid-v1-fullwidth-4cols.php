<?php
/**
 * Template Name: Template listings grid v1 full width 4Cols
 * Created by Waqas Riaz.
 * Date: 16/12/15
 * Time: 3:27 PM
 * Updated: 01/12/25
 * Time: 10:30 AM
 */

get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v1',
    'layout' => 'fullwidth',
    'columns' => 4,
    'show_sidebar' => false,
    'show_switch' => true
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();