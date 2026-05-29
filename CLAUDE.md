# EstateSite Classic — Theme Notes

## Status

Phase 0 scaffold. Minimal functional templates. Stylesheet is presentable, not designed. Reads property meta directly via `esc_property_meta()` helper that falls back from `esc_*` → `fave_*`.

## Architecture

- **PSR-4 namespace**: `EstateSite\Classic\` → `inc/`
- **Class prefix**: `ESC_`
- **Constants**: `ESC_THEME_VERSION`, `ESC_THEME_DIR`, `ESC_THEME_URL`
- **Hard dependency**: EstateSite Core plugin. Theme refuses to activate without it.

## Files of note

- `style.css` — main stylesheet + theme header
- `functions.php` — bootstrap, dependency check, loads `inc/class-theme.php`
- `theme.json` — block editor support (palette, font sizes, layout sizes)
- `inc/class-theme.php` — theme supports, menus, sidebars, asset enqueue, image sizes
- `inc/template-helpers.php` — `esc_property_meta()`, `esc_format_price()`, `esc_render_phase_banner()`

## Templates

- `index.php` — generic archive/blog
- `page.php`, `single.php` — pages and posts
- `single-property.php` — single property (title, hero image, meta grid, content, taxonomies)
- `archive-property.php` — property grid with pagination
- `404.php`, `searchform.php`, `comments.php`
- `header.php`, `footer.php`

## Phase 3 work (later)

- Port template-parts/ from Houzez (1:1 with text-domain renamed)
- Port property-details/ from Houzez
- Replace `esc_property_meta()` calls with `\EstateSite\Core\Property::get()`
- Replace inline styles with proper component CSS
- Real screenshot.png

## Dependencies

- WordPress 6.4+
- PHP 7.4+
- EstateSite Core plugin
