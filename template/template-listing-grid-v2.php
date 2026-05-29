<?php
/**
 * Template Name: Template listings grid v2
 * Created by Waqas Riaz.
 * Date: 16/12/15
 * Time: 3:27 PM
 * Updated: 06/03/25
 * Time: 10:30 AM
 */
get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v2',
    'show_switch' => true
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();