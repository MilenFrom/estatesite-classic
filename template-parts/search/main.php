<?php
/**
 * Main search template handler
 * Manages the display of search functionality based on various configuration options
 * and page settings.
 *
 * @package Houzez
 * @since 1.0
 */

defined('ABSPATH') || exit;

global $post, $sticky_hidden, $desktop_sticky_data, $mobile_sticky_data, $hidden_data;

// Define search style constants
const SEARCH_STYLE_DEFAULT = 'style_1';
const VALID_SEARCH_STYLES = ['style_1', 'style_2', 'style_3'];

// Initialize variables
$sticky_hidden = $sticky_data = '';
$hidden_data = '0';

// Get search configuration for non-404 and non-search pages
if (!is_404() && !is_search()) {
    $adv_search_enable = get_post_meta(houzez_postid(), 'fave_adv_search_enable', true);
    $adv_search = get_post_meta(houzez_postid(), 'fave_adv_search', true);
}

// Get sticky settings
$mobile_search_sticky = houzez_option('mobile-search-sticky');
$main_search_sticky = houzez_option('main-search-sticky');
$main_menu_sticky = houzez_option('main-menu-sticky');
$mobile_menu_sticky = houzez_option('mobile-menu-sticky');

// Configure sticky behavior based on search settings
if (!empty($adv_search_enable) && $adv_search_enable !== 'global') {
    if ($adv_search === 'hide_show') {
        $desktop_sticky_data = $mobile_sticky_data = '1';
        $sticky_hidden = 'search-hidden';
        $hidden_data = '1';
    } else {
        $desktop_sticky_data = $main_search_sticky;
        $mobile_sticky_data = $mobile_search_sticky;
        $sticky_hidden = '';
        $hidden_data = '0';
    }
} else {
    $desktop_sticky_data = $main_search_sticky;
    $mobile_sticky_data = $mobile_search_sticky;
}

// Override sticky settings if advanced search is visible
if (houzez_adv_search_visible()) {
    $desktop_sticky_data = $mobile_sticky_data = $hidden_data = '0';
    $sticky_hidden = '';
}

// Override sticky settings if main menu is sticky
if ($main_menu_sticky == 1) {
    $desktop_sticky_data = $hidden_data = '0';
    $sticky_hidden = '';
}

if ($mobile_menu_sticky == 1) {
    $mobile_sticky_data = '0';
} 

// Get search style from options or URL parameter
$search_style = houzez_option('search_style', SEARCH_STYLE_DEFAULT);

// Handle search style from URL parameter with validation
if (isset($_GET['search_style'])) {
    $requested_style = sanitize_text_field($_GET['search_style']);
    if (in_array($requested_style, VALID_SEARCH_STYLES, true)) {
        $search_style = $requested_style;
    }
}

// Load appropriate search template based on style
$template_path = 'template-parts/search/search-v' . substr($search_style, -1);
get_template_part($template_path);