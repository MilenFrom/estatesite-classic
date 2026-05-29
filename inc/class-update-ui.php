<?php
/**
 * Theme-side admin UI for the update system.
 *
 * Renders:
 *   1. "Check for updates" link on the theme card (Appearance → Themes)
 *   2. "Check for updates" link in the Theme Details overlay
 *   3. Collapsible Changelog block in the Theme Details overlay
 *
 * Pulls data + URLs from the EstateSite Core Update_Checker instance the
 * theme created in functions.php. Core owns the generic update infrastructure
 * (manifest polling, transient injection, force-check handler, recovery URL).
 * This class owns ONLY presentation.
 *
 * WP has no theme_action_links filter equivalent of plugin_action_links, and
 * no themes_api equivalent of plugins_api's "View details" lightbox. We work
 * around both with an inline JS+CSS shim emitted via the themes.php footer
 * hook.
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
	 * Render the inline CSS + JS shim. Fires once per themes.php load.
	 *
	 * WP renders theme cards client-side via Backbone, so a one-shot
	 * DOMContentLoaded listener races the render. We use a MutationObserver
	 * that watches the themes container and inserts the link whenever our
	 * card appears — covers initial render, filter changes, search, and
	 * pagination.
	 */
	public function render(): void {
		$slug            = $this->checker->get_slug();
		$href            = $this->checker->get_force_check_url();
		$manifest        = $this->checker->manifest();
		$changelog_html  = $manifest['sections']['changelog'] ?? '';

		// Theme name from style.css — used by the single-theme view injection
		// to identify our theme by matching the .theme-name h2 text (that view
		// doesn't carry a data-slug attribute).
		$theme           = wp_get_theme( $slug );
		$theme_name      = $theme->get( 'Name' );

		// All values that flow into JS string literals go through wp_json_encode
		// so the embedded URL keeps real ampersands (esc_js HTML-encodes them
		// to &amp;, which breaks the nonce on click).
		$slug_json            = wp_json_encode( $slug );
		$name_json            = wp_json_encode( $theme_name );
		$href_json            = wp_json_encode( $href );
		$label_json           = wp_json_encode( __( 'Check for updates', 'estatesite-classic' ) );
		$changelog_json       = wp_json_encode( $changelog_html );
		$changelog_label_json = wp_json_encode( __( 'Changelog', 'estatesite-classic' ) );
		?>
		<style>
			/* Theme card link. Lives inside .theme-actions (dark footer on the
			   active theme card, light row on inactive cards). Float right so
			   it sits opposite the Customize/Activate button. */
			.theme[data-slug="<?php echo esc_attr( $slug ); ?>"] .theme-actions .esc-check-updates {
				float: right;
				margin-top: 8px;
				margin-right: 12px;
				font-size: 13px;
				color: #72aee6;
				text-decoration: underline;
			}
			.theme[data-slug="<?php echo esc_attr( $slug ); ?>"] .theme-actions .esc-check-updates:hover {
				color: #fff;
			}
			/* Theme overlay / single-theme view link. Lives next to the h2 name. */
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
			.esc-theme-changelog {
				margin-top: 24px;
				padding-top: 20px;
				border-top: 1px solid #dcdcde;
			}
			.esc-theme-changelog summary {
				font-weight: 600;
				font-size: 14px;
				cursor: pointer;
				color: #1d2327;
				padding: 6px 0;
				outline: none;
			}
			.esc-theme-changelog summary:hover { color: #135e96; }
			.esc-theme-changelog[open] summary { margin-bottom: 12px; }
			.esc-theme-changelog .esc-changelog-body { font-size: 13px; line-height: 1.6; }
			.esc-theme-changelog .esc-changelog-body h4 {
				margin: 16px 0 6px;
				font-size: 14px;
				color: #1d2327;
			}
			.esc-theme-changelog .esc-changelog-body h4:first-child { margin-top: 0; }
			.esc-theme-changelog .esc-changelog-body ul {
				margin: 0 0 10px 18px;
				padding: 0;
				list-style: disc;
			}
			.esc-theme-changelog .esc-changelog-body li { margin-bottom: 4px; }
			.esc-theme-changelog .esc-changelog-body code {
				background: #f0f0f1;
				padding: 1px 5px;
				border-radius: 2px;
				font-size: 12px;
			}
		</style>
		<script>
		(function () {
			var slug           = <?php echo $slug_json; ?>;
			var themeName      = <?php echo $name_json; ?>;
			var href           = <?php echo $href_json; ?>;
			var label          = <?php echo $label_json; ?>;
			var changelogHtml  = <?php echo $changelog_json; ?>;
			var changelogLabel = <?php echo $changelog_label_json; ?>;

			function makeLink() {
				var link = document.createElement('a');
				link.className = 'esc-check-updates';
				link.href = href;
				link.textContent = label;
				return link;
			}

			function injectIntoCard(card) {
				if (!card || card.querySelector('.esc-check-updates')) return;
				// Mount inside the .theme-actions row (sits next to Customize/
				// Live Preview/Activate). On the active theme's card this row
				// IS the dark footer bar — the link is styled to read as a
				// secondary action there.
				var mount = card.querySelector('.theme-actions') ||
				            card.querySelector('.theme-name') ||
				            card;
				mount.appendChild(makeLink());
			}

			function injectIntoModal() {
				// WP renders the "Theme Details" view in two slightly different
				// layouts depending on flow:
				//   - `.theme-overlay`           — modal overlay over the grid
				//                                  (click "Theme Details" on a card)
				//   - `.themes.single-theme`     — full-page replacement when the
				//                                  grid is filtered to one theme
				//
				// Both render the same Backbone template (tmpl-theme-single) with
				// .theme-info containing .theme-name, so we look up the rendered
				// view as a single root then identify our theme by matching the
				// name in the .theme-name <h2> (the active theme's data-slug
				// attribute isn't on this view).
				var roots = document.querySelectorAll('.theme-overlay, .single-theme, .theme-wrap');
				for (var i = 0; i < roots.length; i++) {
					tryInjectInfo(roots[i]);
				}
			}

			function tryInjectInfo(root) {
				if (!root) return;
				var info = root.querySelector('.theme-info');
				if (!info) return;
				// Only inject when this view is showing OUR theme. The view
				// doesn't carry a data-slug, so we match against the h2.theme-name
				// text content (stripped of "Active:" / "Version: x.y.z" labels).
				var nameEl = info.querySelector('.theme-name');
				if (!nameEl) return;
				var nameText = nameEl.textContent
					.replace(/Active:\s*/, '')
					.replace(/Version:\s*\S+\s*$/, '')
					.trim();
				if (nameText.toLowerCase() !== themeName.toLowerCase()) return;

				// 1. "Check for updates" link next to .theme-name
				if (!root.querySelector('.esc-check-updates')) {
					nameEl.appendChild(makeLink());
				}

				// 2. Collapsible Changelog <details> at the bottom of .theme-info
				if (changelogHtml && !root.querySelector('.esc-theme-changelog')) {
					var details = document.createElement('details');
					details.className = 'esc-theme-changelog';
					var summary = document.createElement('summary');
					summary.textContent = changelogLabel;
					var body = document.createElement('div');
					body.className = 'esc-changelog-body';
					body.innerHTML = changelogHtml; // pre-sanitized server-side (h4/ul/li/strong/code only)
					details.appendChild(summary);
					details.appendChild(body);
					info.appendChild(details);
				}
			}

			function tryNow() {
				var card = document.querySelector('.theme[data-slug="' + slug + '"]');
				if (card) injectIntoCard(card);
				injectIntoModal();
				return !!card;
			}

			if (document.readyState !== 'loading') tryNow();
			else document.addEventListener('DOMContentLoaded', tryNow);
			window.addEventListener('load', tryNow);

			// Observe the entire body for DOM changes. When the user clicks
			// "Theme Details" on a card, WP injects the .theme-wrap markup
			// somewhere under .wrap (or directly under body for the overlay),
			// not necessarily inside .themes. Watching body catches both.
			var observer = new MutationObserver(function () { tryNow(); });
			observer.observe(document.body, { childList: true, subtree: true });
		})();
		</script>
		<?php
	}
}
