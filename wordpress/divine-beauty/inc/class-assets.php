<?php
/**
 * Stylesheet and script registration.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues the front-end assets and hands the studio's settings to JavaScript.
 */
class Assets {

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor' ) );
		add_filter( 'style_loader_tag', array( $this, 'preconnect_fonts' ), 10, 2 );
	}

	/**
	 * Load the design system and behaviour.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		wp_enqueue_style(
			'divine-fonts',
			'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap',
			array(),
			null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google serves its own cache key.
		);

		wp_enqueue_style( 'divine-main', DIVINE_URI . '/assets/css/main.css', array( 'divine-fonts' ), DIVINE_VERSION );
		wp_enqueue_style( 'divine-layout', DIVINE_URI . '/assets/css/layout.css', array( 'divine-main' ), DIVINE_VERSION );

		wp_enqueue_script( 'divine-site', DIVINE_URI . '/assets/js/site.js', array(), DIVINE_VERSION, true );

		wp_localize_script(
			'divine-site',
			'divineSite',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'divine_enquiry' ),
				'studioEmail'  => Customizer::get( 'email' ),
				'strings'      => array(
					'copied'   => __( 'Message copied. Paste it into your email to Dee.', 'divine-beauty' ),
					'copyFail' => __( 'Select and copy the message above, or choose “Open email app”.', 'divine-beauty' ),
				),
			)
		);
	}

	/**
	 * Show the real design system inside the Elementor editor canvas.
	 *
	 * @return void
	 */
	public function enqueue_editor(): void {
		wp_enqueue_style( 'divine-main', DIVINE_URI . '/assets/css/main.css', array(), DIVINE_VERSION );
		wp_enqueue_style( 'divine-layout', DIVINE_URI . '/assets/css/layout.css', array( 'divine-main' ), DIVINE_VERSION );
	}

	/**
	 * Add the font preconnects next to the Google Fonts stylesheet.
	 *
	 * @param string $tag    Link tag.
	 * @param string $handle Stylesheet handle.
	 * @return string
	 */
	public function preconnect_fonts( string $tag, string $handle ): string {
		if ( 'divine-fonts' !== $handle ) {
			return $tag;
		}

		return '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n"
			. '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n"
			. $tag;
	}
}
