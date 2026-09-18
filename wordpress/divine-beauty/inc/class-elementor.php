<?php
/**
 * Elementor integration.
 *
 * Every section of the site is a widget in the "Divine Beauty" category, so the
 * studio can rearrange, duplicate or retype any part of any page without code.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the widget category and the widget library.
 */
class Elementor_Support {

	/**
	 * Widget class names, in the order they appear in the panel.
	 *
	 * @var string[]
	 */
	private const WIDGETS = array(
		'Hero',
		'Page_Hero',
		'Marquee',
		'Welcome',
		'Service_Carousel',
		'Service_List',
		'Band',
		'Promises',
		'Gallery',
		'Film_Band',
		'Testimonial',
		'Social_Strip',
		'CTA_Band',
		'Booking',
	);

	/**
	 * Hook into Elementor.
	 */
	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Give the theme's widgets their own panel section.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor manager.
	 * @return void
	 */
	public function register_category( $elements_manager ): void {
		$elements_manager->add_category(
			'divine-beauty',
			array(
				'title' => __( 'Divine Beauty', 'divine-beauty' ),
				'icon'  => 'eicon-heart',
			)
		);
	}

	/**
	 * Load and register every widget.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ): void {
		require_once DIVINE_DIR . '/elementor/class-widget-base.php';

		foreach ( self::WIDGETS as $widget ) {
			$file  = DIVINE_DIR . '/elementor/widgets/class-' . str_replace( '_', '-', strtolower( $widget ) ) . '.php';
			$class = __NAMESPACE__ . '\\Widgets\\' . $widget;

			if ( ! is_readable( $file ) ) {
				continue;
			}
			require_once $file;

			if ( class_exists( $class ) ) {
				$widgets_manager->register( new $class() );
			}
		}
	}
}
