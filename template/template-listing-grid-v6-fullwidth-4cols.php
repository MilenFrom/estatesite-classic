<?php
/**
 * Template Name: Template listings grid v6 full width 4cols
 * Created by Waqas Riaz.
 * Date: 11/04/2025
 * Time: 11:16 AM
 */
get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v6',
    'layout' => 'fullwidth',
    'columns' => 4,
    'show_sidebar' => false
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();



