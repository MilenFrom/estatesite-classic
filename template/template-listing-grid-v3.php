<?php
/**
 * Template Name: Template listings grid v3
 * Created by Waqas Riaz.
 * Date: 05/04/17
 * Time: 3:03 PM
 * Updated: 02/01/25
 * Time: 10:30 AM
 */
get_header();

$args = array(
    'default_view' => 'grid',
    'item_template' => 'v3',
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();