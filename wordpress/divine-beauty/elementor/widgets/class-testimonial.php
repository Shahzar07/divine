<?php
/**
 * Client review.
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
 * Real, attributed reviews. Deliberately plain: only add quotes a client
 * actually left.
 */
class Testimonial extends Divine_Widget {

	public function get_name(): string {
		return 'divine-testimonial';
	}

	public function get_title(): string {
		return __( 'Reviews', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-testimonial';
	}

	public function get_keywords(): array {
		return array( 'testimonial', 'review', 'quote', 'clients' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'head', array( 'label' => __( 'Section heading', 'divine-beauty' ) ) );
		$this->add_heading_controls(
			__( 'In their words', 'divine-beauty' ),
			__( 'What clients<br><em>say after.</em>', 'divine-beauty' ),
			''
		);
		$this->end_controls_section();

		$this->start_controls_section( 'quotes', array( 'label' => __( 'Reviews', 'divine-beauty' ) ) );

		$this->add_control(
			'notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Only publish reviews a client actually left, with their name. Invented quotes are misleading and, in the UK, can breach consumer protection law.', 'divine-beauty' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			)
		);

		$quote = new Repeater();
		$quote->add_control(
			'quote',
			array(
				'label' => __( 'Review', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 4,
			)
		);
		$quote->add_control(
			'name',
			array(
				'label' => __( 'Client name', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$quote->add_control(
			'service',
			array(
				'label' => __( 'Treatment', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Reviews', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $quote->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'quote'   => __( 'The studio feels calm and immaculate. It is genuinely the most relaxing hour of my month, and my hands have never looked better.', 'divine-beauty' ),
						'name'    => 'Priya S.',
						'service' => __( 'Luxury manicure', 'divine-beauty' ),
					),
				),
			)
		);

		$this->add_control(
			'invite',
			array(
				'label'     => __( 'Invitation below the reviews', 'divine-beauty' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => __( 'Been in for a treatment? Dee would love to hear how it went.', 'divine-beauty' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();

		if ( empty( $s['items'] ) ) {
			return;
		}

		$multiple = count( (array) $s['items'] ) > 1;
		?>
		<section class="section on-ink-raise" id="testimonials">
			<div class="wrap">
				<div class="section-head stacked">
					<div>
						<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
							<p class="eyebrow centred"><?php echo esc_html( $s['eyebrow'] ); ?></p>
						<?php endif; ?>
						<?php $this->heading( (string) $s['heading'], 'h2' ); ?>
					</div>
				</div>

				<div class="quotes"<?php echo $multiple ? ' data-carousel data-autoplay="7000"' : ''; ?>>
					<span class="quote-mark" aria-hidden="true">&ldquo;</span>
					<?php if ( $multiple ) : ?><div class="rail" data-rail tabindex="0"><?php endif; ?>
						<?php foreach ( (array) $s['items'] as $item ) : ?>
							<figure class="quote">
								<blockquote><?php echo esc_html( $item['quote'] ); ?></blockquote>
								<cite>
									<?php echo esc_html( $item['name'] ); ?>
									<?php if ( ! empty( $item['service'] ) ) : ?>
										<span><?php echo esc_html( $item['service'] ); ?></span>
									<?php endif; ?>
								</cite>
							</figure>
						<?php endforeach; ?>
					<?php if ( $multiple ) : ?></div><?php endif; ?>

					<?php if ( ! empty( $s['invite'] ) ) : ?>
						<p class="quotes-invite"><?php echo esc_html( $s['invite'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
