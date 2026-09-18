<?php
/**
 * Small template helpers shared by templates and Elementor widgets.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return one of the theme's inline SVG icons.
 *
 * The icons are inlined rather than loaded as files so they inherit colour from
 * CSS and cost no extra request.
 *
 * @param string $name  Icon name: instagram, facebook, phone, mail, pin, clock, whatsapp.
 * @param string $class Optional class attribute.
 * @return string Escaped-safe SVG markup, or an empty string for an unknown name.
 */
function divine_icon( string $name, string $class = '' ): string {
	$paths = array(
		'instagram' => 'M12 2.2c3.2 0 3.6 0 4.9.07 1.2.05 1.8.25 2.2.42.55.21.95.47 1.37.89.42.42.68.82.89 1.37.17.4.37 1 .42 2.2.06 1.3.07 1.7.07 4.9s0 3.6-.07 4.9c-.05 1.2-.25 1.8-.42 2.2-.21.55-.47.95-.89 1.37-.42.42-.82.68-1.37.89-.4.17-1 .37-2.2.42-1.3.06-1.7.07-4.9.07s-3.6 0-4.9-.07c-1.2-.05-1.8-.25-2.2-.42a3.7 3.7 0 0 1-1.37-.89 3.7 3.7 0 0 1-.89-1.37c-.17-.4-.37-1-.42-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.07-4.9c.05-1.2.25-1.8.42-2.2.21-.55.47-.95.89-1.37.42-.42.82-.68 1.37-.89.4-.17 1-.37 2.2-.42C8.4 2.2 8.8 2.2 12 2.2Zm0 1.8c-3.14 0-3.51.01-4.75.07-1.15.05-1.77.24-2.18.4-.55.22-.94.47-1.35.88-.41.41-.66.8-.88 1.35-.16.41-.35 1.03-.4 2.18C2.4 9.9 2.4 10.3 2.4 12s0 2.1.06 3.35c.05 1.15.24 1.77.4 2.18.22.55.47.94.88 1.35.41.41.8.66 1.35.88.41.16 1.03.35 2.18.4 1.24.06 1.61.07 4.73.07s3.49-.01 4.73-.07c1.15-.05 1.77-.24 2.18-.4.55-.22.94-.47 1.35-.88.41-.41.66-.8.88-1.35.16-.41.35-1.03.4-2.18.06-1.25.06-1.62.06-3.35s0-2.1-.06-3.35c-.05-1.15-.24-1.77-.4-2.18a3.6 3.6 0 0 0-.88-1.35 3.6 3.6 0 0 0-1.35-.88c-.41-.16-1.03-.35-2.18-.4C15.51 4.01 15.14 4 12 4Zm0 3.1a4.9 4.9 0 1 1 0 9.8 4.9 4.9 0 0 1 0-9.8Zm0 1.8a3.1 3.1 0 1 0 0 6.2 3.1 3.1 0 0 0 0-6.2Zm5.1-3.05a1.15 1.15 0 1 1 0 2.3 1.15 1.15 0 0 1 0-2.3Z',
		'facebook'  => 'M13.5 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.25-1.5 1.55-1.5h1.65V3.6A22 22 0 0 0 14.3 3.5c-2.4 0-4 1.45-4 4.12v2.28H7.6V13h2.7v8h3.2Z',
		'tiktok'    => 'M16.6 5.82A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 0 1-2.59 2.5 2.59 2.59 0 1 1 .77-5.06V9.69a5.67 5.67 0 0 0-.77-.05A5.68 5.68 0 1 0 15.54 15.3V9.01a7.35 7.35 0 0 0 4.3 1.38V7.3a4.28 4.28 0 0 1-3.24-1.48Z',
		'phone'     => 'M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.58 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.4 11.4 0 0 0 .58 3.6 1 1 0 0 1-.25 1z',
		'mail'      => 'M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm9 8.1 8-5.1H4l8 5.1Z',
		'pin'       => 'M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5Z',
		'clock'     => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 10.6 4.3 2.5-.9 1.6L11 13.6V6h2Z',
		'whatsapp'  => 'M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.65-2.05-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.75-.72 2-1.41.25-.69.25-1.28.17-1.4-.07-.13-.27-.2-.57-.35ZM12.04 21.5h-.01a9.4 9.4 0 0 1-4.8-1.31l-.34-.2-3.57.93.95-3.48-.22-.36a9.38 9.38 0 0 1-1.44-5.01c0-5.19 4.23-9.41 9.43-9.41a9.36 9.36 0 0 1 9.42 9.42c0 5.19-4.23 9.42-9.42 9.42Z',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg viewBox="0 0 24 24" class="%s" fill="currentColor" aria-hidden="true" focusable="false"><path d="%s"/></svg>',
		esc_attr( $class ),
		esc_attr( $paths[ $name ] )
	);
}

/**
 * Echo one of the theme's inline SVG icons.
 *
 * @param string $name  Icon name.
 * @param string $class Optional class attribute.
 * @return void
 */
function divine_the_icon( string $name, string $class = '' ): void {
	// The markup is assembled from a fixed allow-list above, and both injected
	// values are escaped there, so it is safe to print.
	echo divine_icon( $name, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Turn a displayed phone number into a dialable tel: value.
 *
 * UK studio numbers are usually written "07838 063271"; a leading zero has to
 * become +44 for the link to work from abroad.
 *
 * @param string $number Human-readable number.
 * @return string Value for a tel: href, without the scheme.
 */
function divine_tel( string $number ): string {
	$digits = preg_replace( '/[^\d+]/', '', $number );

	if ( '' === (string) $digits ) {
		return '';
	}
	if ( str_starts_with( $digits, '+' ) ) {
		return $digits;
	}
	if ( str_starts_with( $digits, '0' ) ) {
		return '+44' . substr( $digits, 1 );
	}

	return '+' . $digits;
}

/**
 * Build a wa.me link from a number and an opening message.
 *
 * @param string $number  Phone number in any readable format.
 * @param string $message Pre-filled message.
 * @return string Full URL, or an empty string when no number is set.
 */
function divine_whatsapp_url( string $number, string $message = '' ): string {
	$digits = preg_replace( '/[^\d]/', '', $number );

	if ( '' === (string) $digits ) {
		return '';
	}
	// wa.me needs the international form without a plus or leading zero.
	if ( str_starts_with( $digits, '0' ) ) {
		$digits = '44' . substr( $digits, 1 );
	}

	$url = 'https://wa.me/' . $digits;

	return '' === $message ? $url : $url . '?text=' . rawurlencode( $message );
}

/**
 * Resolve an Elementor media control to a usable image URL.
 *
 * Falls back to a bundled theme image so a freshly imported section is never
 * empty while the studio is still uploading its own photography.
 *
 * @param array<string,mixed> $media    Elementor media control value.
 * @param string              $fallback File name inside assets/images/.
 * @return string Image URL.
 */
function divine_image_url( array $media, string $fallback = '' ): string {
	if ( ! empty( $media['url'] ) ) {
		return (string) $media['url'];
	}

	return '' === $fallback ? '' : DIVINE_URI . '/assets/images/' . ltrim( $fallback, '/' );
}
