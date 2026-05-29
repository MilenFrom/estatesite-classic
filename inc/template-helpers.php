<?php
/**
 * Template helpers — procedural functions called from PHP templates.
 *
 * Phase 0: reads property meta directly via raw `fave_*` keys so existing
 * Houzez-synced data renders. Phase 1 swaps these for `\EstateSite\Core\Property::get()`.
 *
 * @package EstateSite\Classic
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get a property meta value via the Core accessor.
 *
 * Routes through `\EstateSite\Core\Property::get()` which knows the active
 * compat mode and the logical→physical key map. Falls back to raw post meta
 * if Core is not loaded (defensive — admin notice will already flag this).
 *
 * @param int    $post_id Property post ID.
 * @param string $field   Logical field name (e.g. 'price', 'bedrooms', 'size').
 * @return string
 */
function esc_property_meta( int $post_id, string $field ): string {
	if ( class_exists( '\EstateSite\Core\Property' ) ) {
		$value = \EstateSite\Core\Property::get( $post_id, $field, '' );
	} else {
		// Defensive fallback if Core plugin is somehow not loaded.
		$value = get_post_meta( $post_id, 'fave_property_' . $field, true );
	}
	return is_scalar( $value ) ? (string) $value : '';
}

/**
 * Bulk-fetch property meta in one DB query.
 * Use this on archive/grid views instead of multiple esc_property_meta() calls.
 *
 * @param int      $post_id Property post ID.
 * @param string[] $fields  List of logical field names.
 * @return array<string,string> Logical key => string value (empty if null).
 */
function esc_property_meta_many( int $post_id, array $fields ): array {
	if ( ! class_exists( '\EstateSite\Core\Property' ) ) {
		// Defensive: fall back to single reads.
		$out = [];
		foreach ( $fields as $f ) {
			$v        = get_post_meta( $post_id, 'fave_property_' . $f, true );
			$out[ $f ] = is_scalar( $v ) ? (string) $v : '';
		}
		return $out;
	}
	$raw = \EstateSite\Core\Property::get_many( $post_id, $fields );
	$out = [];
	foreach ( $raw as $k => $v ) {
		$out[ $k ] = is_scalar( $v ) ? (string) $v : '';
	}
	return $out;
}

/**
 * Format a price with the configured currency symbol.
 *
 * @param string $raw  Raw numeric price string.
 * @return string Formatted price, or empty string if no price.
 */
function esc_format_price( string $raw ): string {
	if ( $raw === '' ) {
		return '';
	}
	$opts = (array) get_option( 'estatesite_options', [] );
	$sym  = $opts['currency_symbol']    ?? '€';
	$pos  = $opts['currency_position']  ?? 'before';
	$ts   = $opts['thousand_separator'] ?? ',';
	$ds   = $opts['decimal_separator']  ?? '.';

	$num   = is_numeric( $raw ) ? floatval( $raw ) : null;
	$shown = $num !== null ? number_format( $num, 0, $ds, $ts ) : esc_html( $raw );

	return $pos === 'after' ? $shown . ' ' . $sym : $sym . $shown;
}

/**
 * Render the standard site header.
 */
function esc_render_phase_banner(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="esc-phase-banner">' . esc_html__( 'EstateSite Classic — Phase 0 scaffold. Templates are intentionally minimal; full styling lands in Phase 3.', 'estatesite-classic' ) . '</div>';
}
