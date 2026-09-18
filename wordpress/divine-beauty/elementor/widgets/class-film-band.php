<?php
/**
 * Featured film band.
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
 * A short studio film beside the story behind it.
 */
class Film_Band extends Divine_Widget {

	public function get_name(): string {
		return 'divine-film-band';
	}

	public function get_title(): string {
		return __( 'Film band', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-play';
	}

	public function get_keywords(): array {
		return array( 'video', 'film', 'reel', 'feature' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'media', array( 'label' => __( 'Film', 'divine-beauty' ) ) );

		$this->add_control(
			'video',
			array(
				'label'        => __( 'Video file', 'divine-beauty' ),
				'type'         => Controls_Manager::MEDIA,
				'media_types'  => array( 'video' ),
				'description'  => __( 'An MP4 from your media library. It plays muted and loops.', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'poster',
			array(
				'label'       => __( 'Poster frame', 'divine-beauty' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => __( 'Shown before the film loads, and if it cannot play.', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'tag',
			array(
				'label'   => __( 'Corner label', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Soft glam', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'video_label',
			array(
				'label'       => __( 'Describe the film', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'A client\'s finished look, filmed in the studio', 'divine-beauty' ),
				'description' => __( 'Read aloud to visitors who cannot see it.', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'flip',
			array(
				'label'        => __( 'Film on the right', 'divine-beauty' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'divine-beauty' ) ) );

		$this->add_heading_controls(
			__( 'Makeup & glam', 'divine-beauty' ),
			__( 'Golden lid,<br><em>sharp liner.</em>', 'divine-beauty' ),
			__( 'A warm champagne lid, a clean wing and lashes set to open the eye.', 'divine-beauty' )
		);

		$detail = new Repeater();
		$detail->add_control(
			'text',
			array(
				'label'   => __( 'Line', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => __( '<b>Service</b> — Makeup & Glam, occasion look', 'divine-beauty' ),
			)
		);

		$this->add_control(
			'details',
			array(
				'label'       => __( 'Detail lines', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $detail->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => __( '<b>Service</b> — Makeup & Glam, occasion look', 'divine-beauty' ) ),
					array( 'text' => __( '<b>Result</b> — full coverage with a lit-from-within finish', 'divine-beauty' ) ),
				),
			)
		);

		$this->add_button_controls( __( 'Book this look', 'divine-beauty' ), __( 'About makeup & glam', 'divine-beauty' ) );

		$this->end_controls_section();
	}

	protected function render(): void {
		$s       = $this->get_settings_for_display();
		$id      = 'film-' . $this->get_id();
		$classes = 'band' . ( 'yes' === ( $s['flip'] ?? '' ) ? ' flip' : '' );
		$src     = $s['video']['url'] ?? '';
		$poster  = $s['poster']['url'] ?? '';
		?>
		<article class="<?php echo esc_attr( $classes ); ?>">
			<div class="band-media">
				<div class="player ratio-portrait">
					<video id="<?php echo esc_attr( $id ); ?>" muted loop playsinline preload="none"
						<?php if ( $poster ) : ?>poster="<?php echo esc_url( $poster ); ?>"<?php endif; ?>
						<?php if ( $src ) : ?>data-src="<?php echo esc_url( $src ); ?>"<?php endif; ?>
						aria-label="<?php echo esc_attr( $s['video_label'] ); ?>"></video>
					<?php if ( ! empty( $s['tag'] ) ) : ?>
						<span class="player-tag"><?php echo esc_html( $s['tag'] ); ?></span>
					<?php endif; ?>
					<button class="player-btn" type="button" data-player="<?php echo esc_attr( $id ); ?>"
						aria-label="<?php esc_attr_e( 'Play film', 'divine-beauty' ); ?>">
						<span class="glyph" aria-hidden="true">&#9654;</span>
						<span class="player-label"><?php esc_html_e( 'Play film', 'divine-beauty' ); ?></span>
					</button>
				</div>
			</div>

			<div class="band-body">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
					<p class="eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>
				<?php $this->heading( (string) $s['heading'], 'h2' ); ?>
				<?php if ( ! empty( $s['lede'] ) ) : ?>
					<p><?php echo esc_html( $s['lede'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $s['details'] ) ) : ?>
					<ul class="benefit-list">
						<?php foreach ( $s['details'] as $detail ) : ?>
							<li><?php echo wp_kses( $detail['text'], array( 'b' => array(), 'strong' => array(), 'em' => array() ) ); ?></li>
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
