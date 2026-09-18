<?php
/**
 * Filterable portfolio gallery.
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
 * The studio's own work, filterable by treatment and openable in a lightbox.
 */
class Gallery extends Divine_Widget {

	public function get_name(): string {
		return 'divine-gallery';
	}

	public function get_title(): string {
		return __( 'Portfolio gallery', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}

	public function get_keywords(): array {
		return array( 'portfolio', 'gallery', 'work', 'filter', 'lightbox' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'head', array( 'label' => __( 'Section heading', 'divine-beauty' ) ) );
		$this->add_heading_controls(
			__( 'The gallery', 'divine-beauty' ),
			__( 'Browse by<br><em>treatment.</em>', 'divine-beauty' ),
			__( 'Filter the gallery, then open any piece to see the treatment, the result and how it was done.', 'divine-beauty' )
		);
		$this->end_controls_section();

		/* -------------------------------------------------------- categories */
		$this->start_controls_section( 'cats', array( 'label' => __( 'Filters', 'divine-beauty' ) ) );

		$cat = new Repeater();
		$cat->add_control(
			'key',
			array(
				'label'       => __( 'Filter key', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'nails',
				'description' => __( 'Lowercase, no spaces. Each piece below is tagged with one of these.', 'divine-beauty' ),
			)
		);
		$cat->add_control(
			'label',
			array(
				'label'   => __( 'Filter label', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Nails', 'divine-beauty' ),
			)
		);

		$this->add_control(
			'all_label',
			array(
				'label'   => __( '"All" filter label', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'All work', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'categories',
			array(
				'label'       => __( 'Filters', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $cat->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array( 'key' => 'nails', 'label' => __( 'Nails', 'divine-beauty' ) ),
					array( 'key' => 'glam', 'label' => __( 'Makeup & glam', 'divine-beauty' ) ),
					array( 'key' => 'skin', 'label' => __( 'Facials & skin', 'divine-beauty' ) ),
					array( 'key' => 'body', 'label' => __( 'Massage & cupping', 'divine-beauty' ) ),
				),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------ pieces */
		$this->start_controls_section( 'pieces', array( 'label' => __( 'Work', 'divine-beauty' ) ) );

		$piece = new Repeater();
		$piece->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Burgundy Shimmer Almond', 'divine-beauty' ),
			)
		);
		$piece->add_control(
			'image',
			array(
				'label' => __( 'Photograph', 'divine-beauty' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$piece->add_control(
			'alt',
			array(
				'label' => __( 'Describe the photograph', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$piece->add_control(
			'category',
			array(
				'label'       => __( 'Filter key', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'nails',
				'description' => __( 'Must match one of the filter keys above.', 'divine-beauty' ),
			)
		);
		$piece->add_control(
			'service',
			array(
				'label'       => __( 'Treatment', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Gel Nails', 'divine-beauty' ),
				'description' => __( 'Shown on the card and carried into the enquiry form.', 'divine-beauty' ),
			)
		);
		$piece->add_control(
			'summary',
			array(
				'label' => __( 'Card summary', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 2,
			)
		);
		$piece->add_control(
			'story',
			array(
				'label'       => __( 'Full description', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'description' => __( 'Shown when the piece is opened.', 'divine-beauty' ),
			)
		);
		$piece->add_control(
			'result',
			array(
				'label' => __( 'Result', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 2,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Work', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $piece->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => $this->default_items(),
			)
		);

		$this->add_control(
			'empty_text',
			array(
				'label'   => __( 'Empty-filter message', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'No work in this category yet — try another filter.', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'booking_url',
			array(
				'label'       => __( 'Enquiry page', 'divine-beauty' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '/#booking' ),
				'description' => __( 'Where "Enquire about this" sends the visitor.', 'divine-beauty' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * The studio's own photographs, shipped so the gallery is never empty.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function default_items(): array {
		$rows = array(
			array( 'Burgundy Shimmer Almond', 'nails-burgundy.jpg', 'nails', 'Gel Nails', 'A deep wine gel with fine pearl shimmer, shaped into a soft almond and sealed to a glass finish.', 'High-shine, chip-resistant colour that holds its gloss for two to three weeks.' ),
			array( 'Champagne Soft Glam', 'glam-portrait-poster.jpg', 'glam', 'Makeup & Glam', 'Daylight-matched base, a warm champagne lid, clean liner and lashes chosen for the eye shape.', 'Luminous, even skin that lasted a full event and photographed true under flash.' ),
			array( 'Back & Shoulder Cupping', 'cupping-back.jpg', 'body', 'Cupping Therapy', 'Ten cups placed in two even rows either side of the spine, across the tightest areas.', 'Noticeably looser shoulders; the client booked cupping as a monthly standing session.' ),
			array( 'Body Contouring — Before & After', 'work-contouring-before-after.jpg', 'body', 'Manual Body Contouring', 'The same client photographed before and after one manual body contouring session.', 'Visibly smoother and flatter through the abdomen after one session, with results building over a course.' ),
			array( 'Waist Definition', 'work-contouring-waist.jpg', 'body', 'Manual Body Contouring', 'Sculpting and drainage worked through the waist and lower abdomen.', 'A more defined waistline and noticeably less fluid retention.' ),
			array( 'Microneedling in Progress', 'work-microneedling.jpg', 'skin', 'Microneedling', 'Controlled micro-channels worked across the brow and forehead to prompt new collagen.', 'Smoother texture and softened scarring, building over a course of sessions.' ),
			array( 'Microneedling — Straight After', 'work-glow-profile.jpg', 'skin', 'Microneedling', 'Straight off the couch. The flush is the treatment working, and it settles within a day or two.', 'Redness for a day or two, then visibly smoother, firmer skin as the collagen rebuilds.' ),
			array( 'The Glow Afterwards', 'work-glow-close.jpg', 'skin', 'Microneedling', 'Close up, no makeup and no filter, at the end of the appointment.', 'Hydrated, even-toned skin that continues to improve over the following fortnight.' ),
			array( 'Post-Facial Glow', 'facial-glow.jpg', 'skin', 'Facials & Skin', 'Straight off the couch — no makeup, no filter, just skin after a full facial.', 'Even tone, a natural glow and skin that feels hydrated rather than tight.' ),
			array( 'Cooling Globe Facial', 'facial-globes.jpg', 'skin', 'Facials & Skin', 'Chilled glass globes worked across the cheeks and jaw to close a full cleanse-and-massage facial.', 'Redness settled, puffiness reduced and skin left calm, hydrated and comfortable.' ),
			array( 'Jawline Detail Work', 'facial-detail.jpg', 'skin', 'Facials & Skin', 'A single globe worked along the jaw and up towards the ear, following the drainage lines.', 'A visibly lifted, more defined jawline and a cooler, calmer finish.' ),
			array( 'Full Facial Sequence', 'facial-therapy.jpg', 'skin', 'Facials & Skin', 'Cleanse, exfoliate, massage, mask, cool — with products chosen for your skin on the day.', 'A complete reset for congested or stressed skin, in one appointment.' ),
		);

		$items = array();
		foreach ( $rows as $row ) {
			$items[] = array(
				'title'    => $row[0],
				'image'    => array( 'url' => DIVINE_URI . '/assets/images/' . $row[1] ),
				'alt'      => $row[0],
				'category' => $row[2],
				'service'  => $row[3],
				'summary'  => $row[4],
				'story'    => $row[4],
				'result'   => $row[5],
			);
		}

		return $items;
	}

	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$items = (array) $s['items'];

		// Only offer a filter that actually has work behind it.
		$counts = array();
		foreach ( $items as $item ) {
			$key            = (string) $item['category'];
			$counts[ $key ] = ( $counts[ $key ] ?? 0 ) + 1;
		}

		$booking = $s['booking_url']['url'] ?? '/#booking';
		?>
		<section class="section on-ink-raise" id="work">
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

				<div class="filters" role="group" aria-label="<?php esc_attr_e( 'Filter the portfolio', 'divine-beauty' ); ?>">
					<button type="button" data-filter="all" aria-pressed="true">
						<?php echo esc_html( $s['all_label'] ); ?>
						<span class="chip-count"><?php echo esc_html( (string) count( $items ) ); ?></span>
					</button>
					<?php
					foreach ( (array) $s['categories'] as $cat ) :
						$key = (string) $cat['key'];
						if ( empty( $counts[ $key ] ) ) {
							continue;
						}
						?>
						<button type="button" data-filter="<?php echo esc_attr( $key ); ?>" aria-pressed="false">
							<?php echo esc_html( $cat['label'] ); ?>
							<span class="chip-count"><?php echo esc_html( (string) $counts[ $key ] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>

				<p id="work-count" class="visually-hidden" role="status"></p>

				<div class="work-grid" id="work-grid">
					<?php foreach ( $items as $item ) : ?>
						<article class="piece"
							data-category="<?php echo esc_attr( $item['category'] ); ?>"
							data-title="<?php echo esc_attr( $item['title'] ); ?>"
							data-service="<?php echo esc_attr( $item['service'] ); ?>"
							data-media="<?php echo esc_url( divine_image_url( (array) $item['image'] ) ); ?>"
							data-alt="<?php echo esc_attr( $item['alt'] ?: $item['title'] ); ?>"
							data-result="<?php echo esc_attr( $item['result'] ); ?>"
							data-text="<?php echo esc_attr( $item['story'] ?: $item['summary'] ); ?>">
							<button class="piece-open" type="button">
								<span class="piece-media">
									<?php $this->image( (array) $item['image'], (string) ( $item['alt'] ?: $item['title'] ), '', 800, 1000 ); ?>
								</span>
								<span class="piece-body">
									<?php if ( ! empty( $item['service'] ) ) : ?>
										<span class="piece-tag"><?php echo esc_html( $item['service'] ); ?></span>
									<?php endif; ?>
									<span class="piece-title"><?php echo esc_html( $item['title'] ); ?></span>
									<?php if ( ! empty( $item['summary'] ) ) : ?>
										<span class="piece-summary"><?php echo esc_html( $item['summary'] ); ?></span>
									<?php endif; ?>
									<span class="piece-cue" aria-hidden="true"><?php esc_html_e( 'View work', 'divine-beauty' ); ?> &#8599;</span>
								</span>
							</button>
						</article>
					<?php endforeach; ?>
				</div>

				<p class="gallery-empty" id="work-empty" hidden><?php echo esc_html( $s['empty_text'] ); ?></p>
			</div>
		</section>

		<dialog id="look-dialog" class="look-dialog" aria-labelledby="look-title">
			<button class="dialog-close" type="button" aria-label="<?php esc_attr_e( 'Close', 'divine-beauty' ); ?>">&times;</button>
			<button class="dialog-nav prev" type="button" id="dialog-prev" aria-label="<?php esc_attr_e( 'Previous piece', 'divine-beauty' ); ?>" hidden>&#8592;</button>
			<button class="dialog-nav next" type="button" id="dialog-next" aria-label="<?php esc_attr_e( 'Next piece', 'divine-beauty' ); ?>" hidden>&#8594;</button>
			<div class="dialog-grid">
				<div class="dialog-media" id="dialog-media"></div>
				<div class="dialog-copy">
					<p class="dialog-counter" id="dialog-counter" aria-live="polite"></p>
					<p class="eyebrow" id="dialog-eyebrow"></p>
					<h2 id="look-title"></h2>
					<p id="dialog-text"></p>
					<p class="dialog-result" id="dialog-result" hidden><b><?php esc_html_e( 'Result', 'divine-beauty' ); ?></b> <span></span></p>
					<a class="btn btn-gold" id="use-look" href="<?php echo esc_url( $booking ); ?>">
						<?php esc_html_e( 'Enquire about this', 'divine-beauty' ); ?> <span aria-hidden="true">&#8599;</span>
					</a>
					<p class="dialog-note"><?php esc_html_e( 'Carried into your enquiry. No commitment needed.', 'divine-beauty' ); ?></p>
				</div>
			</div>
		</dialog>
		<?php
	}
}
