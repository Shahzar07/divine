<?php
/**
 * Scrolling treatment marquee.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A slow band of treatment names between two sections.
 */
class Marquee extends Divine_Widget {

	public function get_name(): string {
		return 'divine-marquee';
	}

	public function get_title(): string {
		return __( 'Treatment marquee', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-animation-text';
	}

	public function get_keywords(): array {
		return array( 'marquee', 'ticker', 'scroll', 'treatments' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'divine-beauty' ) ) );

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Treatments', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'default'     => "Facials & skin\nMicroneedling\nDermaplaning\nKorean glass skin\nSwedish massage\nDeep tissue\nLymphatic drainage\nPregnancy massage\nBody contouring\nCupping therapy\nGel nails\nNail extensions\nManicure & pedicure\nMakeup & glam\nBrows & lashes",
				'description' => __( 'One treatment per line. The list is repeated automatically so the band never shows a gap.', 'divine-beauty' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$items = array_filter( array_map( 'trim', explode( "\n", (string) $s['items'] ) ) );

		if ( empty( $items ) ) {
			return;
		}
		?>
		<div class="marquee" aria-hidden="true">
			<ul>
				<?php
				// Printed twice so the animation loops without a visible seam.
				for ( $pass = 0; $pass < 2; $pass++ ) {
					foreach ( $items as $item ) {
						printf( '<li>%s</li>', esc_html( $item ) );
					}
				}
				?>
			</ul>
		</div>
		<?php
	}
}
