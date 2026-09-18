<?php
/**
 * Studio details — the values that appear in the header, footer, contact block
 * and WhatsApp button, editable in Appearance → Customise.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the "Studio details" Customizer panel.
 */
class Customizer {

	/**
	 * Setting keys, their defaults and how each one is sanitised.
	 *
	 * @var array<string,array{default:string,label:string,sanitize:string,type:string}>
	 */
	private const FIELDS = array(
		'phone'            => array( 'default' => '07838 063271', 'label' => 'Phone number', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ),
		'email'            => array( 'default' => 'hello@divinebeautybydee.com', 'label' => 'Enquiry email', 'sanitize' => 'sanitize_email', 'type' => 'email' ),
		'address'          => array( 'default' => 'Manchester, United Kingdom', 'label' => 'Address', 'sanitize' => 'sanitize_textarea_field', 'type' => 'textarea' ),
		'hours'            => array( 'default' => "Tuesday – Saturday · 9:00 – 19:00\nSunday & Monday · Closed", 'label' => 'Opening hours', 'sanitize' => 'sanitize_textarea_field', 'type' => 'textarea' ),
		'instagram_url'    => array( 'default' => '', 'label' => 'Instagram URL', 'sanitize' => 'esc_url_raw', 'type' => 'url' ),
		'facebook_url'     => array( 'default' => '', 'label' => 'Facebook URL', 'sanitize' => 'esc_url_raw', 'type' => 'url' ),
		'tiktok_url'       => array( 'default' => '', 'label' => 'TikTok URL', 'sanitize' => 'esc_url_raw', 'type' => 'url' ),
		'whatsapp_number'  => array( 'default' => '07838 063271', 'label' => 'WhatsApp number', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ),
		'whatsapp_message' => array( 'default' => "Hi Dee! I'd like to book an appointment at Divine Beauty.", 'label' => 'WhatsApp opening message', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ),
		'topbar_text'      => array( 'default' => 'Your moment. Your beauty. Entirely you.', 'label' => 'Announcement bar text', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ),
	);

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'register' ) );
	}

	/**
	 * Read a studio detail.
	 *
	 * @param string $key Field key.
	 * @return string
	 */
	public static function get( string $key ): string {
		$default = self::FIELDS[ $key ]['default'] ?? '';

		return (string) get_theme_mod( 'divine_' . $key, $default );
	}

	/**
	 * Whether the floating WhatsApp button should be shown.
	 *
	 * @return bool
	 */
	public static function whatsapp_enabled(): bool {
		return (bool) get_theme_mod( 'divine_whatsapp_enabled', true ) && '' !== self::get( 'whatsapp_number' );
	}

	/**
	 * Build the Customizer section.
	 *
	 * @param \WP_Customize_Manager $wp_customize Customizer instance.
	 * @return void
	 */
	public function register( \WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_section(
			'divine_studio',
			array(
				'title'       => __( 'Studio details', 'divine-beauty' ),
				'description' => __( 'Phone, email, address, opening hours and social links. These appear in the header, the footer, the contact block and the WhatsApp button.', 'divine-beauty' ),
				'priority'    => 25,
			)
		);

		foreach ( self::FIELDS as $key => $field ) {
			$wp_customize->add_setting(
				'divine_' . $key,
				array(
					'default'           => $field['default'],
					'sanitize_callback' => $field['sanitize'],
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				'divine_' . $key,
				array(
					'label'   => __( $field['label'], 'divine-beauty' ), // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- Labels are theme-owned constants.
					'section' => 'divine_studio',
					'type'    => $field['type'],
				)
			);
		}

		$wp_customize->add_setting(
			'divine_whatsapp_enabled',
			array(
				'default'           => true,
				'sanitize_callback' => static fn( $v ): bool => (bool) $v,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'divine_whatsapp_enabled',
			array(
				'label'   => __( 'Show the floating WhatsApp button', 'divine-beauty' ),
				'section' => 'divine_studio',
				'type'    => 'checkbox',
			)
		);
	}
}
