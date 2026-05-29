<?php
/**
 * Template Name: Template listings grid v3 full width 3cols
 * Created by Waqas Riaz.
 * Date: 05/04/
 * Time: 3:03 PM
 * Updated: 06/02/25
 * Time: 12:30 AM
 */
get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v3',
    'layout' => 'fullwidth',
    'columns' => 3,
    'show_sidebar' => false
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();