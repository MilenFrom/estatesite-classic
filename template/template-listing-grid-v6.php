<?php
/**
 * Template Name: Template listings grid v6
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
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();
