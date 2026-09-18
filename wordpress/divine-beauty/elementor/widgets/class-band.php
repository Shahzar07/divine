<?php
/**
 * Signature treatment band.
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
 * A full-width alternating row: photograph one side, the case for the treatment
 * on the other.
 */
class Band extends Divine_Widget {

	public function get_name(): string {
		return 'divine-band';
	}

	public function get_title(): string {
		return __( 'Treatment band', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-image-before-after';
	}

	public function get_keywords(): array {
		return array( 'band', 'feature', 'treatment', 'split' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'divine-beauty' ) ) );

		$this->add_control(
			'number',
			array(
				'label'       => __( 'Number', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '01',
				'description' => __( 'The small figure before the label. Leave empty to hide it.', 'divine-beauty' ),
			)
		);

		$this->add_heading_controls(
			__( 'Nails', 'divine-beauty' ),
			__( 'Shaped to you.<br><em>Sealed to last.</em>', 'divine-beauty' ),
			__( 'Gel colour, sculpted extensions or hand-painted detail — prepped properly and finished so it wears instead of lifting.', 'divine-beauty' )
		);

		$benefit = new Repeater();
		$benefit->add_control(
			'text',
			array(
				'label'       => __( 'Benefit', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => __( '<b>Shaped to your nail bed</b> — almond, square, coffin or stiletto.', 'divine-beauty' ),
				'description' => __( 'Wrap the opening words in &lt;b&gt;…&lt;/b&gt; to highlight them.', 'divine-beauty' ),
			)
		);

		$this->add_control(
			'benefits',
			array(
				'label'       => __( 'Benefits', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $benefit->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( '<b>Shaped to your nail bed</b> — almond, square, coffin or stiletto.', 'divine-beauty' ) ),
					array( 'text' => __( '<b>Built over the stress point</b> so length holds without snapping.', 'divine-beauty' ) ),
					array( 'text' => __( '<b>Hand-painted art</b>, never stickers — bring a saved photo.', 'divine-beauty' ) ),
				),
			)
		);

		$this->add_button_controls( __( 'Book nails', 'divine-beauty' ), __( 'Learn more', 'divine-beauty' ) );

		$this->end_controls_section();

		/* ------------------------------------------------------------- media */
		$this->start_controls_section( 'media', array( 'label' => __( 'Image & layout', 'divine-beauty' ) ) );

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Photograph', 'divine-beauty' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'alt',
			array(
				'label'   => __( 'Describe the photograph', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Long dark almond nails finished and worn with gold rings', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'flip',
			array(
				'label'        => __( 'Image on the right', 'divine-beauty' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Alternate this between bands so the page zig-zags.', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'stat_value',
			array(
				'label'       => __( 'Badge figure', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '2–3',
				'separator'   => 'before',
				'description' => __( 'The small card over the photograph. Leave empty to hide it.', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'stat_unit',
			array(
				'label'   => __( 'Badge unit', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'wks', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'stat_label',
			array(
				'label'   => __( 'Badge label', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Typical wear', 'divine-beauty' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s       = $this->get_settings_for_display();
		$classes = 'band' . ( 'yes' === ( $s['flip'] ?? '' ) ? ' flip' : '' );
		?>
		<article class="<?php echo esc_attr( $classes ); ?>">
			<div class="band-media">
				<div class="frame frame--gold ratio-poster">
					<?php $this->image( (array) $s['image'], (string) $s['alt'], 'band-nails.jpg', 900, 1200 ); ?>
				</div>
				<?php if ( ! empty( $s['stat_value'] ) ) : ?>
					<p class="stat">
						<strong>
							<?php echo esc_html( $s['stat_value'] ); ?>
							<?php if ( ! empty( $s['stat_unit'] ) ) : ?>
								<span style="font-size:.5em"><?php echo esc_html( $s['stat_unit'] ); ?></span>
							<?php endif; ?>
						</strong>
						<span><?php echo esc_html( $s['stat_label'] ); ?></span>
					</p>
				<?php endif; ?>
			</div>

			<div class="band-body">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
					<p class="eyebrow">
						<?php
						if ( ! empty( $s['number'] ) ) {
							echo esc_html( $s['number'] ) . ' &mdash; ';
						}
						echo esc_html( $s['eyebrow'] );
						?>
					</p>
				<?php endif; ?>

				<?php $this->heading( (string) $s['heading'], 'h2' ); ?>

				<?php if ( ! empty( $s['lede'] ) ) : ?>
					<p><?php echo esc_html( $s['lede'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $s['benefits'] ) ) : ?>
					<ul class="benefit-list">
						<?php foreach ( $s['benefits'] as $benefit ) : ?>
							<li><?php echo wp_kses( $benefit['text'], array( 'b' => array(), 'strong' => array(), 'em' => array() ) ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<div class="band-actions">
					<?php
					$this->button( (string) $s['primary_text'], (array) $s['primary_link'], 'gold' );
					$this->button( (string) $s['ghost_text'], (array) $s['ghost_link'], 'ghost', false );
					?>
				</div>
			</div>
		</article>
		<?php
	}
}
