<?php
/**
 * Universal Captcha Template
 *
 * Displays the appropriate captcha widget based on selected provider
 * Supports: Google reCaptcha (v2/v3) and Cloudflare Turnstile
 *
 * @package Houzez
 * @since 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if captcha is enabled
if ( ! houzez_is_captcha_enabled() ) {
    return;
}

$provider = houzez_get_captcha_provider();

if ( $provider === 'recaptcha' ) {
    // Google reCaptcha
    $recaptcha_type = houzez_option( 'recaptha_type', 'v2' );
    ?>
    <div class="form-group captcha_wrapper houzez-grecaptcha-<?php echo esc_attr( $recaptcha_type ); ?>">
        <div class="houzez_google_reCaptcha"></div>
    </div>
    <?php
} elseif ( $provider === 'turnstile' ) {
    // Cloudflare Turnstile
    ?>
    <div class="form-group captcha_wrapper houzez-turnstile-wrapper">
        <div class="houzez-turnstile"></div>
    </div>
    <?php
}
