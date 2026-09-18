<?php
/**
 * Divine Beauty — theme bootstrap.
 *
 * Kept deliberately thin: this file defines the constants, loads the classes in
 * inc/ and starts them. Everything with behaviour lives in its own class.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DIVINE_VERSION', '1.0.0' );
define( 'DIVINE_DIR', get_template_directory() );
define( 'DIVINE_URI', get_template_directory_uri() );

require_once DIVINE_DIR . '/inc/helpers.php';
require_once DIVINE_DIR . '/inc/class-nav-walker.php';
require_once DIVINE_DIR . '/inc/class-theme-setup.php';
require_once DIVINE_DIR . '/inc/class-assets.php';
require_once DIVINE_DIR . '/inc/class-customizer.php';
require_once DIVINE_DIR . '/inc/class-enquiry.php';
require_once DIVINE_DIR . '/inc/class-starter-content.php';

new DivineBeauty\Theme_Setup();
new DivineBeauty\Assets();
new DivineBeauty\Customizer();
new DivineBeauty\Enquiry();
new DivineBeauty\Starter_Content();

/*
 * Elementor is what makes the site editable without code, so the widget library
 * only loads when it is actually active. Without it the theme still renders —
 * it simply falls back to the standard WordPress editor.
 */
if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' ) ) {
	require_once DIVINE_DIR . '/inc/class-elementor.php';
	new DivineBeauty\Elementor_Support();
} else {
	add_action( 'admin_notices', 'divine_elementor_notice' );
}

/**
 * Tell the administrator why the visual editing they were promised is missing.
 *
 * @return void
 */
function divine_elementor_notice(): void {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p><strong>%s</strong> %s <a href="%s">%s</a></p></div>',
		esc_html__( 'Divine Beauty:', 'divine-beauty' ),
		esc_html__( 'this theme is built around Elementor — install and activate it to edit your pages visually.', 'divine-beauty' ),
		esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) ),
		esc_html__( 'Install Elementor', 'divine-beauty' )
	);
}
