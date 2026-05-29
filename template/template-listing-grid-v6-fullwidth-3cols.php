<?php
/**
 * Template Name: Template listings grid v6 full width 3cols
 * Created by Waqas Riaz.
 * Date: 16/12/15
 * Time: 3:27 PM
 * Updated: 06/02/25
 * Time: 10:30 AM
 */
get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v6',
    'layout' => 'fullwidth',
    'columns' => 3,
    'show_sidebar' => false
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();



