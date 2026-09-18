<?php
/**
 * Home page hero.
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
 * The opening scene: headline, two actions, three proof points and a collage.
 */
class Hero extends Divine_Widget {

	public function get_name(): string {
		return 'divine-hero';
	}

	public function get_title(): string {
		return __( 'Hero', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-banner';
	}

	public function get_keywords(): array {
		return array( 'hero', 'banner', 'header', 'opening' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'divine-beauty' ) ) );

		$this->add_heading_controls(
			__( 'Divine Beauty & Nails By Dee', 'divine-beauty' ),
			__( 'Beauty, in<br>every <em>detail.</em>', 'divine-beauty' ),
			__( 'Massage, cupping, makeup, nails and facials — in a calm private studio where every treatment starts with you.', 'divine-beauty' )
		);

		$this->add_button_controls(
			__( 'Book an appointment', 'divine-beauty' ),
			__( 'Explore services', 'divine-beauty' )
		);

		$this->end_controls_section();

		/* ------------------------------------------------------- proof points */
		$this->start_controls_section( 'proof', array( 'label' => __( 'Proof points', 'divine-beauty' ) ) );

		$stat = new Repeater();
		$stat->add_control(
			'value',
			array(
				'label'   => __( 'Figure', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '9',
			)
		);
		$stat->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Signature treatments', 'divine-beauty' ),
			)
		);

		$this->add_control(
			'stats',
			array(
				'label'       => __( 'Figures', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $stat->get_controls(),
				'title_field' => '{{{ value }}} — {{{ label }}}',
				'default'     => array(
					array(
						'value' => '9',
						'label' => __( 'Signature treatments', 'divine-beauty' ),
					),
					array(
						'value' => '1:1',
						'label' => __( 'Private appointments', 'divine-beauty' ),
					),
					array(
						'value' => 'Tue&ndash;Sat',
						'label' => '9:00 – 19:00',
					),
				),
			)
		);

		$this->end_controls_section();

		/* ----------------------------------------------------------- collage */
		$this->start_controls_section( 'art', array( 'label' => __( 'Images', 'divine-beauty' ) ) );

		foreach ( array(
			'tall'   => array( __( 'Tall image', 'divine-beauty' ), __( 'Glossy dark burgundy gel nails on softly lit hands', 'divine-beauty' ) ),
			'upper'  => array( __( 'Upper square image', 'divine-beauty' ), __( 'A warm, luminous glam makeup finish', 'divine-beauty' ) ),
			'lower'  => array( __( 'Lower square image', 'divine-beauty' ), __( 'Warm oil massage worked through the shoulders', 'divine-beauty' ) ),
		) as $key => $meta ) {
			$this->add_control(
				$key . '_image',
				array(
					'label'   => $meta[0],
					'type'    => Controls_Manager::MEDIA,
					'dynamic' => array( 'active' => true ),
				)
			);
			$this->add_control(
				$key . '_alt',
				array(
					'label'       => __( 'Describe it for screen readers', 'divine-beauty' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => $meta[1],
					'description' => __( 'Read aloud to visitors who cannot see the photograph.', 'divine-beauty' ),
				)
			);
		}

		$this->add_control(
			'badge',
			array(
				'label'     => __( 'Circular badge text', 'divine-beauty' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => __( 'Relax,<br>recharge<br>&amp; feel divine', 'divine-beauty' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		/* ---------------------------------------------------------- blessing */
		$this->start_controls_section( 'blessing', array( 'label' => __( 'Blessing emblem', 'divine-beauty' ) ) );

		$this->add_control(
			'show_blessing',
			array(
				'label'        => __( 'Show the emblem', 'divine-beauty' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'blessing_image',
			array(
				'label'     => __( 'Emblem', 'divine-beauty' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'show_blessing' => 'yes' ),
			)
		);
		$this->add_control(
			'blessing_text',
			array(
				'label'     => __( 'Emblem caption', 'divine-beauty' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Blessed beginnings', 'divine-beauty' ),
				'condition' => array( 'show_blessing' => 'yes' ),
			)
		);
		$this->add_control(
			'blessing_sub',
			array(
				'label'     => __( 'Emblem sub-caption', 'divine-beauty' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Shubh Labh', 'divine-beauty' ),
				'condition' => array( 'show_blessing' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();
		?>
		<section class="hero" aria-labelledby="hero-title">

			<?php if ( 'yes' === ( $s['show_blessing'] ?? '' ) ) : ?>
				<div class="blessing">
					<p class="blessing-text">
						<?php echo esc_html( $s['blessing_text'] ); ?>
						<small><?php echo esc_html( $s['blessing_sub'] ); ?></small>
					</p>
					<figure>
						<?php
						$this->image(
							(array) ( $s['blessing_image'] ?? array() ),
							__( 'The studio\'s emblem of blessed beginnings', 'divine-beauty' ),
							'ganesha.jpg',
							620,
							915,
							true
						);
						?>
					</figure>
				</div>
			<?php endif; ?>

			<div class="wrap hero-inner">
				<div class="hero-copy">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
						<p class="eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
					<?php endif; ?>

					<?php $this->heading( (string) $s['heading'], 'h1', 'id="hero-title"' ); ?>

					<?php if ( ! empty( $s['lede'] ) ) : ?>
						<p class="hero-lede"><?php echo esc_html( $s['lede'] ); ?></p>
					<?php endif; ?>

					<div class="hero-actions">
						<?php
						$this->button( (string) $s['primary_text'], (array) $s['primary_link'], 'gold' );
						$this->button( (string) $s['ghost_text'], (array) $s['ghost_link'], 'ghost', false );
						?>
					</div>

					<?php if ( ! empty( $s['stats'] ) ) : ?>
						<div class="hero-proof">
							<?php foreach ( $s['stats'] as $stat ) : ?>
								<div>
									<strong><?php echo wp_kses( $stat['value'], array( 'span' => array(), 'br' => array() ) ); ?></strong>
									<span><?php echo esc_html( $stat['label'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="hero-art">
					<div class="frame frame--gold ratio-tall a1">
						<?php $this->image( (array) $s['tall_image'], (string) $s['tall_alt'], 'hero-nails.jpg', 1000, 1250, true ); ?>
					</div>
					<div class="frame frame--gold ratio-square a2">
						<?php $this->image( (array) $s['upper_image'], (string) $s['upper_alt'], 'hero-glam.jpg', 860, 860, true ); ?>
					</div>
					<div class="frame frame--gold ratio-square a3">
						<?php $this->image( (array) $s['lower_image'], (string) $s['lower_alt'], 'hero-treatment.jpg', 860, 860, true ); ?>
					</div>
					<?php if ( ! empty( $s['badge'] ) ) : ?>
						<p class="hero-badge"><?php echo wp_kses( $s['badge'], array( 'br' => array() ) ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
