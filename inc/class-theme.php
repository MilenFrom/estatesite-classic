<?php
/**
 * Theme setup singleton.
 *
 * @package EstateSite\Classic
 */

namespace EstateSite\Classic;

defined( 'ABSPATH' ) || exit;

final class Theme {

	/** @var Theme|null */
	private static $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->boot();
		}
		return self::$instance;
	}

	private function __construct() {}

	private function boot(): void {
		add_action( 'after_setup_theme', [ $this, 'theme_supports' ] );
		add_action( 'after_setup_theme', [ $this, 'register_menus' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'widgets_init', [ $this, 'register_sidebars' ] );

		// Houzez script-registration system (ported, self-hooks into wp_enqueue_scripts).
		require_once ESC_THEME_DIR . '/inc/register-scripts.php';

		// Houzez styling-options system — outputs inline CSS based on CSF options.
		// Loaded conditionally to avoid running in WP-CLI / cron contexts.
		if ( ! wp_doing_ajax() && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			require_once ESC_THEME_DIR . '/inc/styling-options.php';
		}

		// Yelp auth (Houzez's external integration loader — required by some templates).
		$yelpauth = ESC_THEME_DIR . '/inc/yelpauth/yelpoauth.php';
		if ( is_readable( $yelpauth ) ) {
			require_once $yelpauth;
		}

		// Houzez Library — the "Templates" button inside Elementor editor that
		// opens the blocks/pages library modal. Ported from Houzez inc/blocks/.
		// Self-hooks into elementor/editor/* and only instantiates when Elementor
		// is available.
		$blocks = ESC_THEME_DIR . '/inc/blocks/blocks.php';
		if ( is_readable( $blocks ) ) {
			require_once $blocks;
		}
	}

	public function theme_supports(): void {
		load_theme_textdomain( 'estatesite-classic', ESC_THEME_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'custom-logo', [
			'height'      => 60,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		] );

		// Property archive thumbnails.
		add_image_size( 'esc-card-grid', 600, 400, true );
		add_image_size( 'esc-card-list', 400, 300, true );
		add_image_size( 'esc-single-hero', 1600, 900, true );
	}

	public function register_menus(): void {
		register_nav_menus( [
			'primary' => __( 'Primary Menu', 'estatesite-classic' ),
			'footer'  => __( 'Footer Menu', 'estatesite-classic' ),
		] );
	}

	public function enqueue_assets(): void {
		// Main stylesheet — style.css at theme root.
		wp_enqueue_style(
			'estatesite-classic',
			get_stylesheet_uri(),
			[],
			ESC_THEME_VERSION
		);
	}

	public function register_sidebars(): void {
		// Sidebars ported from Houzez functions.php to match the IDs the
		// theme's template files reference (single-property, search-sidebar,
		// agent-sidebar, etc.). Without these, widget areas that used to
		// hold listing-filter widgets, agent-bio widgets, etc. show empty
		// even though widget data is still in the DB.
		//
		// All sidebars share the same Houzez markup contract:
		//   <div class="widget widget-wrap mb-4 p-4 widget_xxx">
		//     <div class="widget-header"><h3 class="widget-title">Title</h3></div>
		//     ...widget content...
		//   </div>
		// so existing widget styling carries over.
		$shared = [
			'before_widget' => '<div id="%1$s" class="widget widget-wrap mb-4 p-4 %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widget-header"><h3 class="widget-title mb-4">',
			'after_title'   => '</h3></div>',
		];

		$sidebars = [
			[
				'name'        => __( 'Default Sidebar', 'estatesite-classic' ),
				'id'          => 'default-sidebar',
				'description' => __( 'Widgets shown in the blog sidebar.', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Property Listings', 'estatesite-classic' ),
				'id'          => 'property-listing',
				'description' => __( 'Widgets shown in the property listings sidebar (archive / listing-grid page templates).', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Search Sidebar', 'estatesite-classic' ),
				'id'          => 'search-sidebar',
				'description' => __( 'Widgets shown on the search results page.', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Single Property', 'estatesite-classic' ),
				'id'          => 'single-property',
				'description' => __( 'Widgets shown in the single property sidebar.', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Page Sidebar', 'estatesite-classic' ),
				'id'          => 'page-sidebar',
				'description' => __( 'Widgets shown in the page sidebar (regular pages with sidebar layout).', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Agency Sidebar', 'estatesite-classic' ),
				'id'          => 'agency-sidebar',
				'description' => __( 'Widgets shown on agency archive + single agency pages.', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Agent Sidebar', 'estatesite-classic' ),
				'id'          => 'agent-sidebar',
				'description' => __( 'Widgets shown on agent archive + single agent pages.', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Mobile Menu', 'estatesite-classic' ),
				'id'          => 'hz-mobile-menu',
				'description' => __( 'Widgets shown in the mobile menu drawer.', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Custom Widget Area 1', 'estatesite-classic' ),
				'id'          => 'hz-custom-widget-area-1',
				'description' => __( 'Assignable to any page via widget settings.', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Custom Widget Area 2', 'estatesite-classic' ),
				'id'          => 'hz-custom-widget-area-2',
				'description' => __( 'Assignable to any page via widget settings.', 'estatesite-classic' ),
			],
			[
				'name'        => __( 'Custom Widget Area 3', 'estatesite-classic' ),
				'id'          => 'hz-custom-widget-area-3',
				'description' => __( 'Assignable to any page via widget settings.', 'estatesite-classic' ),
			],
		];

		foreach ( $sidebars as $sb ) {
			register_sidebar( array_merge( $sb, $shared ) );
		}

		// Backwards-compat alias: WP's default theme scaffold uses 'sidebar-1'
		// for the primary sidebar. Houzez uses 'default-sidebar'. Register
		// 'sidebar-1' as an alias so any pre-existing widget assignments to it
		// continue to render.
		register_sidebar( array_merge(
			[
				'name'        => __( 'Primary Sidebar (alias)', 'estatesite-classic' ),
				'id'          => 'sidebar-1',
				'description' => __( 'WP scaffold alias for default-sidebar.', 'estatesite-classic' ),
			],
			$shared
		) );
	}
}
