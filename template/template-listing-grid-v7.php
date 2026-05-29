<?php
/**
 * Template Name: Template listings grid v7
 * Created by Waqas Riaz.
 * Date: 16/12/15
 * Time: 3:27 PM
 * Updated: 06/02/25
 * Time: 10:30 AM
 */
get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v7',
    'show_switch' => true
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();