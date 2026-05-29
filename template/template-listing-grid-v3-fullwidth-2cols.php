<?php
/**
 * Template Name: Template listings grid v3 full width 2cols
 * Created by Waqas Riaz.
 * Date: 05/04/17
 * Time: 3:03 PM
 * Updated: 06/01/25
 * Time: 02:30 AM
 */
get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v3',
    'layout' => 'fullwidth',
    'columns' => 2,
    'show_sidebar' => false
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();