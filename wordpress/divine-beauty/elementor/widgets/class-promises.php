<?php
/**
 * Numbered promise cards.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * "Why clients stay" — a grid of numbered reassurances.
 */
class Promises extends Divine_Widget {

	public function get_name(): string {
		return 'divine-promises';
	}

	public function get_title(): string {
		return __( 'Promise cards', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-number-field';
	}

	public function get_keywords(): array {
		return array( 'promises', 'why', 'reasons', 'usp' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'head', array( 'label' => __( 'Section heading', 'divine-beauty' ) ) );
		$this->add_heading_controls(
			__( 'Why clients stay', 'divine-beauty' ),
			__( 'Small studio.<br><em>Serious care.</em>', 'divine-beauty' ),
			''
		);
		$this->end_controls_section();

		$this->start_controls_section( 'cards', array( 'label' => __( 'Promises', 'divine-beauty' ) ) );

		$card = new Repeater();
		$card->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'One client at a time', 'divine-beauty' ),
			)
		);
		$card->add_control(
			'text',
			array(
				'label' => __( 'Description', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Promises', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $card->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title' => __( 'One client at a time', 'divine-beauty' ),
						'text'  => __( 'No queue, no rush and no overlap — the room is yours for the whole appointment.', 'divine-beauty' ),
					),
					array(
						'title' => __( 'Prepped and sanitised', 'divine-beauty' ),
						'text'  => __( 'Tools are sterilised between every client and the room is reset before you arrive.', 'divine-beauty' ),
					),
					array(
						'title' => __( 'Built around you', 'divine-beauty' ),
						'text'  => __( 'Pressure, shape, colour and finish are decided with you, not from a fixed menu.', 'divine-beauty' ),
					),
					array(
						'title' => __( 'Honest advice', 'divine-beauty' ),
						'text'  => __( 'If a treatment is not right for you today, Dee will say so and suggest what is.', 'divine-beauty' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();
		?>
		<section class="section on-paper">
			<div class="wrap">
				<div class="section-head stacked">
					<div>
						<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
							<p class="eyebrow centred"><?php echo esc_html( $s['eyebrow'] ); ?></p>
						<?php endif; ?>
						<?php $this->heading( (string) $s['heading'], 'h2' ); ?>
					</div>
					<?php if ( ! empty( $s['lede'] ) ) : ?>
						<p class="lede"><?php echo esc_html( $s['lede'] ); ?></p>
					<?php endif; ?>
				</div>

				<div class="card-grid card-grid-4">
					<?php foreach ( (array) $s['items'] as $i => $item ) : ?>
						<article class="s-card">
							<div class="s-card-body">
								<span class="s-card-step" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<h3><?php echo esc_html( $item['title'] ); ?></h3>
								<p><?php echo esc_html( $item['text'] ); ?></p>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
