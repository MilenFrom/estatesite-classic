<?php
/**
 * Theme-side admin UI for the update system.
 *
 * Renders the "Check for updates" link inside WP's Theme Details view
 * (next to the theme name).
 *
 * Pulls data + URLs from the EstateSite Core Update_Checker instance the
 * theme created in functions.php. Core owns the generic update infrastructure
 * (manifest polling, transient injection, force-check handler, recovery URL).
 * This class owns ONLY presentation.
 *
 * The Changelog block experiment (v1.0.4-1.0.8) was removed in v1.0.9 —
 * fighting WP's modal/single-theme/theme-wrap DOM variants caused more
 * trouble than the feature was worth. Customers can read the changelog
 * via the GitHub release page or the manifest JSON.
 *
 * @package EstateSite\Classic
 */

namespace EstateSite\Classic;

use EstateSite\Core\Update_Checker;

defined( 'ABSPATH' ) || exit;

final class Update_UI {

	/** @var Update_Checker */
	private $checker;

	public function __construct( Update_Checker $checker ) {
		$this->checker = $checker;
		add_action( 'admin_print_footer_scripts-themes.php', [ $this, 'render' ] );
	}

	/**
	 * Emit the inline CSS + JS shim that injects the "Check for updates" link
	 * into WP's Theme Details view (any container — .theme-overlay,
	 * .single-theme, .theme-wrap — all render the same .theme-info structure).
	 */
	public function render(): void {
		$slug        = $this->checker->get_slug();
		$href        = $this->checker->get_force_check_url();

		// Theme name from style.css — used by the JS to identify our theme by
		// matching the .theme-name h2 text content (the Theme Details view
		// doesn't carry a data-slug attribute on its root container).
		$theme       = wp_get_theme( $slug );
		$theme_name  = $theme->get( 'Name' );

		// All values that flow into JS string literals go through wp_json_encode
		// so the embedded URL keeps real ampersands (esc_js HTML-encodes them to
		// &amp;, which breaks the nonce on click).
		$slug_json   = wp_json_encode( $slug );
		$name_json   = wp_json_encode( $theme_name );
		$href_json   = wp_json_encode( $href );
		$label_json  = wp_json_encode( __( 'Check for updates', 'estatesite-classic' ) );
		?>
		<style>
			.theme-overlay .theme-info .theme-name .esc-check-updates,
			.single-theme .theme-info .theme-name .esc-check-updates,
			.theme-wrap .theme-info .theme-name .esc-check-updates {
				display: inline-block;
				margin-left: 12px;
				font-size: 13px;
				font-weight: normal;
				color: #2271b1;
				text-decoration: underline;
				vertical-align: middle;
			}
			.theme-overlay .theme-info .theme-name .esc-check-updates:hover,
			.single-theme .theme-info .theme-name .esc-check-updates:hover,
			.theme-wrap .theme-info .theme-name .esc-check-updates:hover {
				color: #135e96;
			}
		</style>
		<script>
		(function () {
			var slug      = <?php echo $slug_json; ?>;
			var themeName = <?php echo $name_json; ?>;
			var href      = <?php echo $href_json; ?>;
			var label     = <?php echo $label_json; ?>;

			function makeLink() {
				var link = document.createElement('a');
				link.className = 'esc-check-updates';
				link.href = href;
				link.textContent = label;
				return link;
			}

			function tryInject() {
				// WP's Theme Details view renders the same tmpl-theme-single
				// Backbone template under three possible containers depending on
				// flow: .theme-overlay (modal), .single-theme (full-page filter
				// view), .theme-wrap (direct render). Match any of them.
				var roots = document.querySelectorAll('.theme-overlay, .single-theme, .theme-wrap');
				for (var i = 0; i < roots.length; i++) {
					var root = roots[i];
					if (root.querySelector('.esc-check-updates')) continue;

					var info = root.querySelector('.theme-info');
					if (!info) continue;

					var nameEl = info.querySelector('.theme-name');
					if (!nameEl) continue;

					// Identify our theme by .theme-name text content (stripped of
					// "Active:" / "Version: X.Y.Z" prefixes/suffixes WP injects).
					var nameText = nameEl.textContent
						.replace(/Active:\s*/, '')
						.replace(/Version:\s*\S+\s*$/, '')
						.trim();
					if (nameText.toLowerCase() !== themeName.toLowerCase()) continue;

					nameEl.appendChild(makeLink());
				}
			}

			if (document.readyState !== 'loading') tryInject();
			else document.addEventListener('DOMContentLoaded', tryInject);
			window.addEventListener('load', tryInject);

			// Observe body for Backbone re-renders (modal open/close, theme
			// switches via arrow buttons, filter / search / pagination).
			var observer = new MutationObserver(function () { tryInject(); });
			observer.observe(document.body, { childList: true, subtree: true });
		})();
		</script>
		<?php
	}
}
