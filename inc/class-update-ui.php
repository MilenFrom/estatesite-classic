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

		// All values that flow into JS string literals go through wp_json_encode
		// so the embedded URL keeps real ampersands (esc_js HTML-encodes them
		// to &amp;, which breaks the nonce on click).
		$slug_json            = wp_json_encode( $slug );
		$href_json            = wp_json_encode( $href );
		$label_json           = wp_json_encode( __( 'Check for updates', 'estatesite-classic' ) );
		$changelog_json       = wp_json_encode( $changelog_html );
		$changelog_label_json = wp_json_encode( __( 'Changelog', 'estatesite-classic' ) );
		?>
		<style>
			.theme[data-slug="<?php echo esc_attr( $slug ); ?>"] .esc-check-updates {
				display: inline-block;
				margin-top: 4px;
				color: #2271b1;
				text-decoration: none;
				font-size: 13px;
			}
			.theme[data-slug="<?php echo esc_attr( $slug ); ?>"] .esc-check-updates:hover {
				color: #135e96;
				text-decoration: underline;
			}
			.theme-overlay .theme-info .esc-check-updates {
				display: inline-block;
				margin-left: 12px;
				font-size: 13px;
				color: #2271b1;
				text-decoration: none;
				vertical-align: middle;
			}
			.theme-overlay .theme-info .esc-check-updates:hover {
				color: #135e96;
				text-decoration: underline;
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
				var mount = card.querySelector('.theme-actions') ||
				            card.querySelector('.theme-name') ||
				            card.querySelector('.theme-author') ||
				            card;
				mount.appendChild(makeLink());
			}

			function injectIntoModal() {
				var overlay = document.querySelector('.theme-overlay');
				if (!overlay) return;
				var displayed = document.querySelector('.theme.displaying-theme');
				if (!displayed) return;
				if (displayed.getAttribute('data-slug') !== slug) return;

				var info = overlay.querySelector('.theme-info');
				if (!info) return;

				// 1. "Check for updates" link next to theme-name
				if (!overlay.querySelector('.esc-check-updates')) {
					var nameEl = info.querySelector('.theme-name');
					if (nameEl) {
						nameEl.appendChild(makeLink());
					} else {
						info.appendChild(makeLink());
					}
				}

				// 2. Collapsible Changelog block at the bottom of .theme-info.
				if (changelogHtml && !overlay.querySelector('.esc-theme-changelog')) {
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

			var observer = new MutationObserver(function () { tryNow(); });
			var themes = document.querySelector('.themes') || document.body;
			if (themes) {
				observer.observe(themes, { childList: true, subtree: true });
			}
		})();
		</script>
		<?php
	}
}
