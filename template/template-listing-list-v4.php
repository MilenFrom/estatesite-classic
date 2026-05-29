<?php
/**
 * Template Name: Template listings list v4
 * Created by Waqas Riaz.
 * Date: 16/12/23
 * Time: 3:27 PM
 * Updated: 06/02/25
 * Time: 10:30 AM
 */
get_header();

$args = array(
    'default_view' => 'list',
    'item_template' => 'v4',
    'layout' => 'fullwidth',
    'columns' => 1,
    'show_sidebar' => false,
    'custom_listing_class' => 'listing-view list-view row row-cols-lg-1 row-cols-md-2 gy-4',
);
get_template_part('template-parts/listing/listing', 'common', $args);

get_footer();