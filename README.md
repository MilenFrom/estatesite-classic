# EstateSite Classic

The presentation layer for [EstateSite](https://estatesite.eu) — header, footer, archives, property detail templates.

A WordPress theme forked from Houzez 4.1.6 with the framework stripped out (now lives in [EstateSite Core](https://github.com/MilenFrom/estatesite-wpcore)).

## Companion packages

- **[EstateSite Core](https://github.com/MilenFrom/estatesite-wpcore)** (plugin, hard dependency) — CPTs, search, options, metaboxes
- **[EstateSite Elementor](https://github.com/MilenFrom/estatesite-wpelementor)** (plugin, optional) — Elementor widgets + theme builder

## Install

1. Make sure EstateSite Core is installed and active.
2. Download the latest `.zip` from the [Releases](https://github.com/MilenFrom/estatesite-classic/releases) page.
3. WordPress Admin → Appearance → Themes → Add New → Upload Theme → choose the zip.
4. Activate.

Future updates appear automatically in Dashboard → Updates.

The theme refuses to activate if EstateSite Core is missing — you'll see an admin notice telling you why.

## What's included

- 600 PHP files across `template-parts/` (header/footer/dashboard/listing/search/membership/realtors) — ported 1:1 from Houzez with text-domain renamed
- Property detail layouts (v3, v4)
- Top-level templates: `single-property.php`, `archive-property.php`, `404.php`, etc.
- WooCommerce template overrides **dropped** (standard WooCommerce templates apply)
- WPBakery support **dropped** (use Elementor or block editor)

## Requirements

- WordPress 6.4+
- PHP 7.4+
- EstateSite Core plugin

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
