<?php
/**
 * Scrolling collection of treatments.
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
 * The treatment collection rail, with arrows and dots.
 */
class Service_Carousel extends Divine_Widget {

	public function get_name(): string {
		return 'divine-service-carousel';
	}

	public function get_title(): string {
		return __( 'Treatment carousel', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-slider-push';
	}

	public function get_keywords(): array {
		return array( 'services', 'treatments', 'carousel', 'slider' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'head', array( 'label' => __( 'Section heading', 'divine-beauty' ) ) );
		$this->add_heading_controls(
			__( 'The treatment collection', 'divine-beauty' ),
			__( 'Everything the<br><em>studio offers.</em>', 'divine-beauty' ),
			__( 'Slide through the collection, then open the full menu for benefits and booking.', 'divine-beauty' )
		);
		$this->end_controls_section();

		/* --------------------------------------------------------- the cards */
		$this->start_controls_section( 'cards', array( 'label' => __( 'Treatments', 'divine-beauty' ) ) );

		$card = new Repeater();
		$card->add_control(
			'title',
			array(
				'label'   => __( 'Treatment name', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Massage', 'divine-beauty' ),
			)
		);
		$card->add_control(
			'image',
			array(
				'label' => __( 'Photograph', 'divine-beauty' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$card->add_control(
			'alt',
			array(
				'label'       => __( 'Describe the photograph', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Read aloud to visitors who cannot see it.', 'divine-beauty' ),
			)
		);
		$card->add_control(
			'tags',
			array(
				'label'       => __( 'Tags', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Head, Back & shoulder, Full body', 'divine-beauty' ),
				'description' => __( 'Separated by commas.', 'divine-beauty' ),
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
		$card->add_control(
			'link',
			array(
				'label'   => __( 'Learn more link', 'divine-beauty' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Treatments', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $card->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => $this->default_items(),
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'Button below the rail', 'divine-beauty' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'View the full service menu', 'divine-beauty' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'cta_link',
			array(
				'label' => __( 'Button link', 'divine-beauty' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * The treatments flagged as highlights, so the rail is useful immediately.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function default_items(): array {
		$items = array();

		foreach ( divine_treatments() as $t ) {
			if ( empty( $t['featured'] ) ) {
				continue;
			}

			$benefits = array_filter( array_map( 'trim', explode( "\n", (string) $t['benefits'] ) ) );
			$sentence = explode( '. ', (string) $t['text'] )[0];

			$items[] = array(
				'title' => $t['name'],
				'image' => array( 'url' => DIVINE_URI . '/assets/images/' . $t['image'] ),
				'alt'   => $t['alt'],
				// The first words of the first two benefits make natural tags.
				'tags'  => implode( ', ', array_map(
					static fn( string $b ): string => trim( explode( '—', $b )[0] ),
					array_slice( $benefits, 0, 2 )
				) ),
				'text'  => rtrim( $sentence, '.' ) . '.',
				'link'  => array( 'url' => '/services/#' . $t['slug'] ),
			);
		}

		return $items;
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();
		?>
		<section class="section" id="services">
			<div class="wrap">
				<div class="section-head">
					<div>
						<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
							<p class="eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
						<?php endif; ?>
						<?php $this->heading( (string) $s['heading'], 'h2' ); ?>
					</div>
					<?php if ( ! empty( $s['lede'] ) ) : ?>
						<p class="lede"><?php echo esc_html( $s['lede'] ); ?></p>
					<?php endif; ?>
				</div>

				<div class="carousel" data-carousel>
					<div class="rail rail-3" data-rail tabindex="0" role="group"
						aria-label="<?php esc_attr_e( 'Treatment collection, scrollable', 'divine-beauty' ); ?>">
						<?php foreach ( (array) $s['items'] as $i => $item ) : ?>
							<article class="s-card">
								<div class="s-card-media">
									<?php $this->image( (array) $item['image'], (string) ( $item['alt'] ?: $item['title'] ), '', 800, 1000 ); ?>
									<span class="s-card-no"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								</div>
								<div class="s-card-body">
									<h3><?php echo esc_html( $item['title'] ); ?></h3>
									<?php
									$tags = array_filter( array_map( 'trim', explode( ',', (string) $item['tags'] ) ) );
									if ( $tags ) :
										?>
										<div class="tag-row">
											<?php foreach ( $tags as $tag ) : ?>
												<span class="tag"><?php echo esc_html( $tag ); ?></span>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
									<p><?php echo esc_html( $item['text'] ); ?></p>
									<?php if ( ! empty( $item['link']['url'] ) ) : ?>
										<a class="link-more" href="<?php echo esc_url( $item['link']['url'] ); ?>">
											<?php esc_html_e( 'Learn more', 'divine-beauty' ); ?> <span aria-hidden="true">&#8599;</span>
										</a>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>

					<div class="carousel-nav spread">
						<div class="dots" data-dots role="tablist" aria-label="<?php esc_attr_e( 'Choose a slide', 'divine-beauty' ); ?>"></div>
						<div class="carousel-nav">
							<button class="round-btn" type="button" data-prev aria-label="<?php esc_attr_e( 'Previous treatments', 'divine-beauty' ); ?>">&#8592;</button>
							<button class="round-btn" type="button" data-next aria-label="<?php esc_attr_e( 'Next treatments', 'divine-beauty' ); ?>">&#8594;</button>
						</div>
					</div>
				</div>

				<?php if ( ! empty( $s['cta_text'] ) ) : ?>
					<p class="centred" style="margin-top:34px">
						<?php $this->button( (string) $s['cta_text'], (array) $s['cta_link'], 'ghost' ); ?>
					</p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
