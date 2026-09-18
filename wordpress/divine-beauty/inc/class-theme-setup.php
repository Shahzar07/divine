<?php
/**
 * Theme supports, menus and image sizes.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers everything WordPress needs to know about the theme.
 */
class Theme_Setup {

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'nav_menu_link_attributes', array( $this, 'nav_link_class' ), 10, 3 );
	}

	/**
	 * Declare theme support and register menus.
	 *
	 * @return void
	 */
	public function setup(): void {
		load_theme_textdomain( 'divine-beauty', DIVINE_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style', 'navigation-widgets' )
		);
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 527,
				'width'       => 1600,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		register_nav_menus(
			array(
				'primary' => __( 'Primary menu', 'divine-beauty' ),
				'explore' => __( 'Footer — Explore', 'divine-beauty' ),
				'legal'   => __( 'Footer — Treatments', 'divine-beauty' ),
			)
		);

		// Portrait crops the design leans on, so uploads land at the right shape.
		add_image_size( 'divine-portrait', 900, 1200, true );
		add_image_size( 'divine-card', 800, 1000, true );
		add_image_size( 'divine-square', 860, 860, true );
	}

	/**
	 * Mark pages that Elementor is rendering, so CSS can stand down where needed.
	 *
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public function body_class( array $classes ): array {
		if ( is_singular() && class_exists( '\Elementor\Plugin' ) ) {
			$document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
			if ( $document && $document->is_built_with_elementor() ) {
				$classes[] = 'divine-elementor';
			}
		}

		return $classes;
	}

	/**
	 * Give menu links the theme's own class so the CSS applies to WP menus.
	 *
	 * @param array<string,string> $atts  Link attributes.
	 * @param object               $item  Menu item.
	 * @param object               $args  Menu arguments.
	 * @return array<string,string>
	 */
	public function nav_link_class( array $atts, $item, $args ): array {
		if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
			$atts['class'] = trim( ( $atts['class'] ?? '' ) . ' nav-link' );
		}

		return $atts;
	}
}
