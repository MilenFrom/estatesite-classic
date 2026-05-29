<?php
/**
 * Template Name: Template listings list v2 full width
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 23/01/16
 * Time: 7:13 PM
 */
get_header();

$args = array(
    'default_view' => 'list',
    'item_template' => 'v2',
    'layout' => 'fullwidth',
    'columns' => 1,
    'show_sidebar' => false,
    'show_switch' => true
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();