=== EstateSite Classic ===
Contributors: estatesite
Tags: real estate, property listings, blog, two-columns, right-sidebar, custom-menu, custom-logo, featured-images
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.5
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

EstateSite Classic — WordPress theme for real estate sites. Pairs with the EstateSite Core plugin.

== Description ==

EstateSite Classic is a fork of the Houzez 4.1.6 theme rebuilt as the visual layer for EstateSite Core. The plugin owns all data logic (CPTs, search, agents, options); the theme owns presentation (headers, footers, archive grids, single-property layouts).

* 64 page-templates (Search Results, Property Half Map, listing grids v1-v7, listing lists, dashboard, login, packages, agencies, agents)
* Full template-parts/ tree (header variants, footer variants, listing cards, dashboard widgets, search forms)
* property-details/ subtree for single-property layout variants
* Hard depends on EstateSite Core plugin
* Self-hosted updates via the EstateSite update server (no third-party services)

== Changelog ==

= 1.0.5 =
* Fix: "Check for updates" link wasn't appearing in the Theme Details view. The injection JS was looking only for `.theme-overlay` (the modal overlay rendered over the grid), but WP sometimes renders the same template under `.single-theme` or `.theme-wrap` in a full-page layout — those weren't matched.
* Fix: MutationObserver was watching `.themes` (the grid container), but WP injects the Theme Details markup OUTSIDE that container in some flows. Now watches `document.body` so the injection fires regardless of where WP mounts the view.
* Fix: Theme card link is now `float: right` inside `.theme-actions` so it sits opposite the Customize button on the dark footer bar instead of crowding it.
* Internal: The single-theme view doesn't carry a data-slug attribute, so identification is now done by matching the `.theme-name <h2>` text content (stripped of "Active:" / "Version: X.Y.Z" prefixes/suffixes) against the theme's Name from style.css.

= 1.0.4 =
* New: Theme owns its own admin UI for the update system. inc/class-update-ui.php renders the "Check for updates" link on the theme card (Appearance → Themes) and adds a collapsible Changelog section to WP's Theme Details overlay. Reads data via Core's new manifest() and get_force_check_url() public API rather than having Core inject the markup itself. Pairs with estatesite-wpcore v1.0.7 which strips the theme-specific code out of Update_Checker.

= 1.0.3 =
* Fix: Update_Checker hook timing. functions.php registered Update_Checker on plugins_loaded — but themes load AFTER plugins_loaded fires, so the add_action was registering a callback for an action that already passed. Theme's site_transient_update_themes filter was never registered either, which is why customers never saw theme update notifications. Switched to after_setup_theme. After this fix: theme update notifications work, and the "Check for updates" link on the theme card appears.

= 1.0.2 =
* New: screenshot.png (1200x900) so the theme card on Appearance → Themes stops being a transparent checkerboard. Generated via PHP-GD with deep slate-blue background, white "EstateSite" title, gold "Classic" subtitle, "Real Estate WordPress Theme" tagline, abstract rooftop silhouette.

= 1.0.1 =
* Port: 1,164 files from Houzez 4.1.6 to make WP's page-template dropdown populate. WordPress scans the active theme's directory for files with `Template Name:` PHP headers — without them, the dropdown only shows Default + Elementor variants. After v1.0.0 customers couldn't set "Search Results" as a page template, which broke the entire search flow.
  - template/ — 64 page-template files
  - template-parts/ — 892 partial template files (header variants, listing cards, search forms, dashboard widgets)
  - property-details/ — 208 single-property layout files
* All 1,164 files pass PHP 8.4 lint. WP's wp_get_theme()->get_page_templates() now returns 64 templates.

= 1.0.0 =
* First production release.
* Scaffold: functions.php, header.php, footer.php, index.php, archive-property.php, single-property.php, 404.php, page.php, single.php.
* Taxonomy archive templates: property_type, property_status, property_city, property_area, property_country, property_state, property_label, property_feature.
* Agent/agency archive + single templates.
* localization.php, comments.php, searchform.php, sidebars.
* inc/class-theme.php for theme bootstrap (image sizes, menus, supports, sidebars).
* inc/register-scripts.php (790 lines, ported from Houzez inc/) for asset enqueuing.
* inc/styling-options.php for dynamic CSS based on customizer options.
* Hard dependency on EstateSite Core plugin (validated at after_switch_theme).
* Update pipeline via \EstateSite\Core\Update_Checker — manifest at https://dev.estatesite.eu/updates/estatesite-classic.json.
