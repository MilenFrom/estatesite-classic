<?php
/**
 * Template Name: Template listings grid v7 full width 4Cols
 * Created by Waqas Riaz.
 * Date: 16/12/18
 * Time: 3:27 PM
 * Updated: 06/02/25
 * Time: 10:30 AM
 */
get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v7',
    'layout' => 'fullwidth',
    'columns' => 4,
    'show_sidebar' => false,
    'show_switch' => true
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();