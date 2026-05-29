<?php
/**
 * Template Name: Template listings grid v1 full width 3Cols
 * Created by Waqas Riaz.
 * Date: 16/12/15
 * Time: 3:27 PM
 * Updated: 02/02/25
 * Time: 13:30 PM
 */

get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v1',
    'layout' => 'fullwidth',
    'columns' => 3,
    'show_sidebar' => false,
    'show_switch' => true
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();