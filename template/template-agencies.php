<?php
/**
 * Template Name: Template all agencies
 *
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 20/10/16
 * Time: 4:44 PM
 */
get_header();

$agencies_layout = houzez_option('agencies-template-layout', 'v1');

$valid_layouts = array('v1', 'v2', 'v3');
if( isset( $_GET['agencies-layout'] ) && in_array($_GET['agencies-layout'], $valid_layouts, true) ) {
    $agencies_layout = $_GET['agencies-layout'];
}

get_template_part('template-parts/realtors/agency/layout', $agencies_layout);

get_footer(); ?>