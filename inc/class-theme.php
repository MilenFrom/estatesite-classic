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

		// One-time migrations on theme upgrade. Fires after sidebars are
		// registered + after menu locations are registered so we can move
		// stored data into the new IDs/slugs without race conditions.
		add_action( 'widgets_init', [ $this, 'maybe_run_migrations' ], 99 );

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
		// Register both our friendly slugs AND the Houzez-compatible slugs the
		// ported template-parts/header/partials/*.php and template-parts/
		// footer/nav.php call wp_nav_menu() with. Without the Houzez slugs,
		// the native (non-Elementor) header path renders no menu items at all.
		//
		// Friendly slugs (preferred surface for new sites):
		//   primary, footer
		// Houzez-compatible slugs (referenced by ported partials):
		//   main-menu             template-parts/header/partials/nav.php:24
		//   main-menu-left        template-parts/header/partials/nav-left.php:5
		//   main-menu-right       template-parts/header/partials/nav-right.php:5
		//   top-menu              template-parts/topbar/partials/nav.php:9
		//   mobile-menu-hed6      template-parts/header/partials/mobile-nav.php
		//   footer-menu           template-parts/footer/nav.php:5
		//
		// maybe_run_migrations() copies any pre-existing `primary`/`footer`
		// assignment into the matching Houzez slug on first activation, so
		// customers who set up the menu under the friendly name don't have
		// to re-assign in Appearance → Menus → Manage Locations.
		register_nav_menus( [
			'primary'          => __( 'Primary Menu', 'estatesite-classic' ),
			'footer'           => __( 'Footer Menu', 'estatesite-classic' ),
			'main-menu'        => __( 'Main Menu (Houzez header)', 'estatesite-classic' ),
			'main-menu-left'   => __( 'Main Menu — Left (split header)', 'estatesite-classic' ),
			'main-menu-right'  => __( 'Main Menu — Right (split header)', 'estatesite-classic' ),
			'top-menu'         => __( 'Top Bar Menu', 'estatesite-classic' ),
			'mobile-menu-hed6' => __( 'Mobile Menu', 'estatesite-classic' ),
			'footer-menu'      => __( 'Footer Menu (Houzez footer)', 'estatesite-classic' ),
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

		// Footer sidebars. The ported template-parts/footer/footer.php opens
		// with a hard guard:
		//
		//   if ( !is_active_sidebar('footer-sidebar-1') &&
		//        !is_active_sidebar('footer-sidebar-2') &&
		//        !is_active_sidebar('footer-sidebar-3') &&
		//        !is_active_sidebar('footer-sidebar-4') ) return;
		//
		// In v1.0.10 these four IDs were not registered, so is_active_sidebar()
		// returned false unconditionally, the guard returned early, and the
		// entire footer widget area was silently dropped — admins couldn't even
		// see the footer columns as drop targets in Appearance → Widgets.
		//
		// Houzez uses a distinct `footer-widget` class on these sidebars'
		// before_widget so footer-only styling rules in main.css apply. We
		// match that contract verbatim.
		$footer_shared = [
			'before_widget' => '<div id="%1$s" class="footer-widget widget widget-wrap mb-4 p-4 %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widget-header"><h3 class="widget-title mb-4">',
			'after_title'   => '</h3></div>',
		];
		for ( $i = 1; $i <= 4; $i++ ) {
			register_sidebar( array_merge(
				[
					'name'        => sprintf( __( 'Footer Sidebar %d', 'estatesite-classic' ), $i ),
					'id'          => 'footer-sidebar-' . $i,
					'description' => sprintf( __( 'Column %d of the footer widget area.', 'estatesite-classic' ), $i ),
				],
				$footer_shared
			) );
		}

		// NOTE: pre-v1.0.12 versions registered 'sidebar-1' as a dead alias
		// for 'default-sidebar'. WP has no aliasing — the only sidebar.php
		// in the theme calls dynamic_sidebar('default-sidebar'), so widgets
		// stored under 'sidebar-1' never rendered. The alias is removed in
		// v1.0.12 and maybe_run_migrations() moves any stored sidebar-1
		// assignments into default-sidebar.
	}

	/**
	 * One-time per-install migrations to keep customer widget/menu assignments
	 * working across the v1.0.10 → v1.0.12 upgrade. Re-runs are no-ops because
	 * each step checks whether its destination is already populated and bails.
	 *
	 * 1. Move widgets from the deprecated 'sidebar-1' alias into 'default-sidebar'
	 *    (only when the destination is empty — never overwrites existing widgets).
	 * 2. Mirror the WP nav-menu location 'primary' into the Houzez slug
	 *    'main-menu' so the native header partials render the customer's existing
	 *    menu assignment without manual re-assignment. Same for 'footer' →
	 *    'footer-menu'.
	 *
	 * Gated by an option flag so it runs at most once per major migration.
	 */
	public function maybe_run_migrations(): void {
		$flag = 'estatesite_classic_migrated_v1012';
		if ( get_option( $flag ) ) {
			return;
		}

		// 1. Move sidebar-1 widgets → default-sidebar.
		$sw = get_option( 'sidebars_widgets' );
		if ( is_array( $sw )
		     && ! empty( $sw['sidebar-1'] )
		     && empty( $sw['default-sidebar'] )
		) {
			$sw['default-sidebar'] = $sw['sidebar-1'];
			$sw['sidebar-1']       = [];
			update_option( 'sidebars_widgets', $sw );
		}

		// 2. Mirror primary → main-menu, footer → footer-menu (only if the
		//    destination slot is empty — don't clobber a deliberate assignment).
		$locations = get_theme_mod( 'nav_menu_locations', [] );
		if ( is_array( $locations ) ) {
			$dirty = false;
			if ( ! empty( $locations['primary'] ) && empty( $locations['main-menu'] ) ) {
				$locations['main-menu'] = $locations['primary'];
				$dirty = true;
			}
			if ( ! empty( $locations['footer'] ) && empty( $locations['footer-menu'] ) ) {
				$locations['footer-menu'] = $locations['footer'];
				$dirty = true;
			}
			if ( $dirty ) {
				set_theme_mod( 'nav_menu_locations', $locations );
			}
		}

		update_option( $flag, time(), false );
	}
}
