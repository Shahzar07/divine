<?php
/**
 * Closing call to action.
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
 * The centred band that closes a page.
 */
class CTA_Band extends Divine_Widget {

	public function get_name(): string {
		return 'divine-cta-band';
	}

	public function get_title(): string {
		return __( 'Call to action', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-call-to-action';
	}

	public function get_keywords(): array {
		return array( 'cta', 'banner', 'closing', 'book' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'divine-beauty' ) ) );

		$this->add_heading_controls(
			__( 'Your turn', 'divine-beauty' ),
			__( 'Ready when<br><em>you are.</em>', 'divine-beauty' ),
			__( 'Tell Dee what you have in mind and she will come back with pricing and availability.', 'divine-beauty' )
		);
		$this->add_button_controls( __( 'Book an appointment', 'divine-beauty' ), __( 'View all services', 'divine-beauty' ) );

		$this->add_control(
			'style',
			array(
				'label'   => __( 'Background', 'divine-beauty' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ink',
				'options' => array(
					'ink'   => __( 'Dark', 'divine-beauty' ),
					'paper' => __( 'Ivory', 'divine-beauty' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s       = $this->get_settings_for_display();
		$classes = 'cta-band section' . ( 'paper' === $s['style'] ? ' on-paper' : '' );
		?>
		<section class="<?php echo esc_attr( $classes ); ?>">
			<div class="wrap-narrow">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
					<p class="eyebrow centred"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>
				<?php $this->heading( (string) $s['heading'], 'h2' ); ?>
				<?php if ( ! empty( $s['lede'] ) ) : ?>
					<p class="lede"><?php echo esc_html( $s['lede'] ); ?></p>
				<?php endif; ?>
				<div class="cta-actions">
					<?php
					$this->button( (string) $s['primary_text'], (array) $s['primary_link'], 'gold' );
					$this->button( (string) $s['ghost_text'], (array) $s['ghost_link'], 'ghost', false );
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
