<?php
/**
 * Template Name: Template listings grid v2 full width 3Cols
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 16/12/15
 * Time: 3:27 PM
 */
get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v2',
    'layout' => 'fullwidth',
    'columns' => 3,
    'show_sidebar' => false,
    'show_switch' => true
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();