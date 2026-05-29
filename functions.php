<?php
/**
 * EstateSite Classic — theme bootstrap.
 *
 * Phase 0: minimal functional theme. All business logic lives in EstateSite Core plugin.
 *
 * @package EstateSite\Classic
 */

defined( 'ABSPATH' ) || exit;

define( 'ESC_THEME_VERSION', '1.0.10' );
define( 'ESC_THEME_DIR',     get_template_directory() );
define( 'ESC_THEME_URL',     get_template_directory_uri() );

// ---------------------------------------------------------------------------
// Update pipeline — native WP filter pointing at our own JSON manifest.
// Uses the Update_Checker class from EstateSite Core (hard dependency).
// Themes load AFTER plugins_loaded fires, so we can't hook plugins_loaded
// from here — by the time functions.php runs, it's already passed.
// Use after_setup_theme instead, which fires once functions.php has loaded.
// ---------------------------------------------------------------------------
add_action( 'after_setup_theme', static function () {
	if ( ! class_exists( '\EstateSite\Core\Update_Checker' ) ) {
		return; // Core missing — theme dep check below will surface this.
	}
	$checker = new \EstateSite\Core\Update_Checker(
		'theme',
		'estatesite-classic', // theme slug = directory name
		ESC_THEME_VERSION,
		defined( 'ESTATESITE_UPDATE_ENDPOINT_CLASSIC' )
			? ESTATESITE_UPDATE_ENDPOINT_CLASSIC
			: 'https://dev.estatesite.eu/updates/estatesite-classic.json'
	);

	// Theme-side UI (theme card "Check for updates" link + Theme Details
	// overlay Changelog block). These used to live inside Update_Checker
	// itself but presentation belongs in the theme — Core only owns the
	// generic update infrastructure.
	if ( is_admin() ) {
		require_once ESC_THEME_DIR . '/inc/class-update-ui.php';
		new \EstateSite\Classic\Update_UI( $checker );
	}
} );

/*
 * Houzez constants aliased to our theme paths so ported templates resolve
 * URLs/paths to OUR assets, not the inactive Houzez theme dir. Defined here
 * (before any other includes) so they're available globally.
 *
 * The Core plugin's houzez-function-aliases.php also defines some of these
 * pointing at the plugin's assets/ dir. The defined() guard means whichever
 * is loaded FIRST wins — and since theme loads after Core, the plugin defines
 * HOUZEZ_IMAGE pointing at its own dir. We override that here.
 */
if ( ! defined( 'HOUZEZ_THEME_NAME' ) )      { define( 'HOUZEZ_THEME_NAME',     'EstateSite Classic' ); }
if ( ! defined( 'HOUZEZ_THEME_SLUG' ) )      { define( 'HOUZEZ_THEME_SLUG',     'estatesite-classic' ); }
if ( ! defined( 'HOUZEZ_THEME_VERSION' ) )   { define( 'HOUZEZ_THEME_VERSION',  ESC_THEME_VERSION ); }
if ( ! defined( 'HOUZEZ_FRAMEWORK' ) )       { define( 'HOUZEZ_FRAMEWORK',      ESC_THEME_DIR . '/framework/' ); }
if ( ! defined( 'HOUZEZ_WIDGETS' ) )         { define( 'HOUZEZ_WIDGETS',        ESC_THEME_DIR . '/inc/widgets/' ); }
if ( ! defined( 'HOUZEZ_INC' ) )             { define( 'HOUZEZ_INC',            ESC_THEME_DIR . '/inc/' ); }
if ( ! defined( 'HOUZEZ_TEMPLATE_PARTS' ) )  { define( 'HOUZEZ_TEMPLATE_PARTS', ESC_THEME_DIR . '/template-parts/' ); }
// HOUZEZ_IMAGE is defined by Core on after_setup_theme priority 0,
// pointing at this theme's img/ dir.
if ( ! defined( 'HOUZEZ_CSS_DIR_URI' ) )     { define( 'HOUZEZ_CSS_DIR_URI',    ESC_THEME_URL . '/css/' ); }
if ( ! defined( 'HOUZEZ_JS_DIR_URI' ) )      { define( 'HOUZEZ_JS_DIR_URI',     ESC_THEME_URL . '/js/' ); }

/**
 * Hard dependency: EstateSite Core plugin.
 * If Core is missing, revert to a safe default theme and explain.
 */
add_action( 'after_switch_theme', function () {
	if ( ! defined( 'ESCORE_VERSION' ) ) {
		switch_theme( WP_DEFAULT_THEME ?: 'twentytwentyfour' );
		wp_die(
			esc_html__( 'EstateSite Classic requires the EstateSite Core plugin to be active. Please activate it first, then re-activate this theme.', 'estatesite-classic' ),
			esc_html__( 'Missing Dependency', 'estatesite-classic' ),
			[ 'back_link' => true ]
		);
	}
} );

/**
 * Admin notice if Core gets deactivated AFTER theme activation.
 */
add_action( 'admin_notices', function () {
	if ( ! defined( 'ESCORE_VERSION' ) ) {
		echo '<div class="notice notice-error"><p>'
			. esc_html__( 'EstateSite Classic is active but EstateSite Core plugin is not. Most features will not work until Core is reactivated.', 'estatesite-classic' )
			. '</p></div>';
	}
} );

// Theme bootstrap.
require_once ESC_THEME_DIR . '/inc/class-theme.php';
\EstateSite\Classic\Theme::instance();

// Template helpers (procedural, called from templates).
require_once ESC_THEME_DIR . '/inc/template-helpers.php';
