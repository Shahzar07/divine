<?php
/**
 * Two-column studio story.
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
 * The quiet section: a photograph of the room beside the studio's own words.
 */
class Welcome extends Divine_Widget {

	public function get_name(): string {
		return 'divine-welcome';
	}

	public function get_title(): string {
		return __( 'Studio story', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-image-box';
	}

	public function get_keywords(): array {
		return array( 'about', 'story', 'welcome', 'studio' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'divine-beauty' ) ) );

		$this->add_heading_controls(
			__( 'Welcome to Divine', 'divine-beauty' ),
			__( 'More than an appointment.<br><em>Your moment.</em>', 'divine-beauty' ),
			''
		);

		$this->add_control(
			'body',
			array(
				'label'       => __( 'Story', 'divine-beauty' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => '<p>' . __( 'Divine Beauty &amp; Nails By Dee is a private studio where beauty and wellbeing sit side by side. One client at a time, no rush, no queue — just a quiet room, careful hands and an eye for the smallest detail.', 'divine-beauty' ) . '</p>',
				'dynamic'     => array( 'active' => true ),
			)
		);

		$point = new Repeater();
		$point->add_control(
			'text',
			array(
				'label'   => __( 'Promise', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Private, one-to-one appointments', 'divine-beauty' ),
			)
		);

		$this->add_control(
			'points',
			array(
				'label'       => __( 'Promises', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $point->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( 'Private, one-to-one appointments', 'divine-beauty' ) ),
					array( 'text' => __( 'Hygienic, fully sanitised tools', 'divine-beauty' ) ),
					array( 'text' => __( 'Treatments tailored to you', 'divine-beauty' ) ),
					array( 'text' => __( 'Honest advice, never a hard sell', 'divine-beauty' ) ),
				),
			)
		);

		$this->add_button_controls(
			__( 'Make time for yourself', 'divine-beauty' ),
			__( 'See the work', 'divine-beauty' )
		);

		$this->add_control(
			'signature',
			array(
				'label'     => __( 'Signature name', 'divine-beauty' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Dee',
				'separator' => 'before',
			)
		);
		$this->add_control(
			'signature_prefix',
			array(
				'label'   => __( 'Signature prefix', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'With care,', 'divine-beauty' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------ images */
		$this->start_controls_section( 'media', array( 'label' => __( 'Images', 'divine-beauty' ) ) );

		$this->add_control(
			'main_image',
			array(
				'label'   => __( 'Main image', 'divine-beauty' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'main_alt',
			array(
				'label'   => __( 'Describe the main image', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'A calm, prepared treatment room with folded towels and a lit candle', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'inset_image',
			array(
				'label'       => __( 'Overlapping detail image', 'divine-beauty' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'The smaller photograph that overlaps the corner. Leave empty to hide it.', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'inset_alt',
			array(
				'label'   => __( 'Describe the detail image', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'A candle burning beside rolled towels in the studio', 'divine-beauty' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();
		?>
		<section class="welcome section on-paper" id="welcome">
			<div class="wrap welcome-inner">
				<div class="welcome-media">
					<div class="frame frame--gold ratio-portrait">
						<?php $this->image( (array) $s['main_image'], (string) $s['main_alt'], 'studio-room.jpg', 980, 1225 ); ?>
					</div>
					<?php if ( ! empty( $s['inset_image']['url'] ) || ! empty( $s['inset_alt'] ) ) : ?>
						<span class="welcome-still">
							<?php $this->image( (array) $s['inset_image'], (string) $s['inset_alt'], 'studio-detail.jpg', 560, 700 ); ?>
						</span>
					<?php endif; ?>
				</div>

				<div class="welcome-body">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
						<p class="eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
					<?php endif; ?>

					<?php $this->heading( (string) $s['heading'], 'h2' ); ?>

					<?php echo wp_kses_post( $s['body'] ); ?>

					<?php if ( ! empty( $s['points'] ) ) : ?>
						<ul class="welcome-points">
							<?php foreach ( $s['points'] as $point ) : ?>
								<li><?php echo esc_html( $point['text'] ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<div class="band-actions">
						<?php
						$this->button( (string) $s['primary_text'], (array) $s['primary_link'], 'gold' );
						$this->button( (string) $s['ghost_text'], (array) $s['ghost_link'], 'ghost', false );
						?>
					</div>

					<?php if ( ! empty( $s['signature'] ) ) : ?>
						<p class="signature">
							<span><?php echo esc_html( $s['signature_prefix'] ); ?></span>
							<em><?php echo esc_html( $s['signature'] ); ?></em>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
