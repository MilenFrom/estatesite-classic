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
		register_sidebar( [
			'name'          => __( 'Primary Sidebar', 'estatesite-classic' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Default sidebar shown in blog posts and pages.', 'estatesite-classic' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		] );
	}
}
