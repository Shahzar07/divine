<?php
/**
 * Full treatment menu with benefits.
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
 * The Services page list: every treatment, its photograph and its benefits,
 * with a jump-to row above it.
 */
class Service_List extends Divine_Widget {

	public function get_name(): string {
		return 'divine-service-list';
	}

	public function get_title(): string {
		return __( 'Treatment menu', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-post-list';
	}

	public function get_keywords(): array {
		return array( 'services', 'menu', 'treatments', 'list', 'benefits' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'options', array( 'label' => __( 'Options', 'divine-beauty' ) ) );

		$this->add_control(
			'show_chips',
			array(
				'label'        => __( 'Show the jump-to row', 'divine-beauty' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'booking_url',
			array(
				'label'       => __( 'Enquiry page', 'divine-beauty' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '/#booking' ),
				'description' => __( 'Each "Book now" adds the treatment name to this link.', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'work_url',
			array(
				'label'   => __( 'Portfolio page', 'divine-beauty' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '/portfolio/' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'items', array( 'label' => __( 'Treatments', 'divine-beauty' ) ) );

		$svc = new Repeater();
		$svc->add_control(
			'name',
			array(
				'label'   => __( 'Treatment name', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Massage', 'divine-beauty' ),
			)
		);
		$svc->add_control(
			'slug',
			array(
				'label'       => __( 'Anchor', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'massage',
				'description' => __( 'Lowercase, hyphens only. Used for the jump link.', 'divine-beauty' ),
			)
		);
		$svc->add_control(
			'kicker',
			array(
				'label'   => __( 'Group', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Body & wellbeing', 'divine-beauty' ),
			)
		);
		$svc->add_control(
			'image',
			array(
				'label' => __( 'Photograph', 'divine-beauty' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$svc->add_control(
			'alt',
			array(
				'label' => __( 'Describe the photograph', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$svc->add_control(
			'text',
			array(
				'label' => __( 'Description', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 4,
			)
		);
		$svc->add_control(
			'benefits',
			array(
				'label'       => __( 'Benefits', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 6,
				'description' => __( 'One benefit per line.', 'divine-beauty' ),
			)
		);

		$this->add_control(
			'services',
			array(
				'label'       => __( 'Treatments', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $svc->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => $this->default_services(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * The studio's nine treatments.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function default_services(): array {
		$rows = array(
			array( 'Massage', 'massage', 'Body & wellbeing', 'band-massage.jpg', 'Head, neck, shoulder, back or full body — worked at the pressure that suits you, with warm oil and a room quiet enough to properly switch off.', "Relieves everyday stress and muscular tension\nHelps ease tension-related headaches\nEncourages deeper, easier sleep\nReleases tightness across neck and shoulders" ),
			array( 'Cupping Therapy', 'cupping', 'Body & wellbeing', 'band-cupping.jpg', 'Dry cupping places suction cups along the back and shoulders to lift the tissue rather than press into it. The round marks it leaves are normal and usually fade within a few days.', "Targets stubborn tightness across the upper back\nA deep, different sensation to hands-on massage\nPairs naturally with a back and shoulder massage\nA favourite for post-training recovery days" ),
			array( 'Makeup & Glam', 'makeup', 'Glam', 'svc-makeup.jpg', 'Skin prepped and primed first, then a base matched in natural light so it never turns grey in photographs. Day looks, occasions, bridal and bridal parties.', "Shade matched in daylight for true-to-skin colour\nLong-wear formulas built to last a full day\nLashes chosen to suit your eye shape\nGroup and bridal-party timings available" ),
			array( 'Facials & Skin Treatments', 'facials', 'Skin', 'band-facials.jpg', 'A full double cleanse, gentle exfoliation, facial and lymphatic massage and a mask chosen for your skin on the day — finished with chilled glass globes.', "Thoroughly cleansed without stripping the skin\nCooling globes settle redness and de-puff\nFacial massage for a lifted, rested look\nTailored aftercare advice, never a product upsell" ),
			array( 'Gel Nails', 'gel-nails', 'Nails', 'svc-gel-nails.jpg', 'Careful prep, precise shaping and a smooth, glass-like gel finish in the colour you have been saving a photo of. Sealed properly so it wears without lifting or chipping.', "High-shine finish that lasts two to three weeks\nShaped to suit your own nail bed\nAdds strength to natural nails\nTouch-dry the moment you leave" ),
			array( 'Nail Extensions', 'extensions', 'Nails', 'svc-extensions.jpg', 'Sculpted acrylic or builder-gel extensions in your preferred length and shape — almond, square, coffin or stiletto — balanced to the width of your natural nail.', "Any length and shape you like\nStrong enough for everyday wear\nInfills available to keep the set going\nSafe removal, never prised or forced off" ),
			array( 'Nail Art & Detail', 'nail-art', 'Nails', 'svc-nail-art.jpg', 'Fine hand-painted lines, gold foil, chrome, ombré or a scatter of crystals. Bring a saved photo and Dee will adapt it to your nail shape and length.', "Completely bespoke — no two sets the same\nHand-painted detail, not stickers\nAccent nails or a full set, your call\nDesigned to suit your nail shape" ),
			array( 'Manicure & Pedicure', 'mani-pedi', 'Hands & feet', 'svc-mani-pedi.jpg', 'Classic shaping, cuticle work, buffing and a flawless polish. The pedicure adds a soak, hard-skin care and a warm massage.', "Tidy, healthy-looking nails and cuticles\nHard-skin and callus care for comfortable feet\nHydrating hand and foot massage\nRegular or long-wear gel polish finish" ),
			array( 'Brow & Lash Finish', 'brows', 'Finishing touches', 'svc-brows.jpg', 'Brows mapped to your features, shaped, tidied and tinted if you want more depth — with lashes chosen to match.', "Brows mapped to your face, not a template\nDefines the eyes without heavy makeup\nTint for extra depth where you want it\nPairs perfectly with a makeup or facial booking" ),
		);

		$items = array();
		foreach ( $rows as $row ) {
			$items[] = array(
				'name'     => $row[0],
				'slug'     => $row[1],
				'kicker'   => $row[2],
				'image'    => array( 'url' => DIVINE_URI . '/assets/images/' . $row[3] ),
				'alt'      => $row[0],
				'text'     => $row[4],
				'benefits' => $row[5],
			);
		}

		return $items;
	}

	protected function render(): void {
		$s        = $this->get_settings_for_display();
		$services = (array) $s['services'];
		$booking  = $s['booking_url']['url'] ?? '/#booking';
		$work     = $s['work_url']['url'] ?? '/portfolio/';
		$joiner   = str_contains( $booking, '?' ) ? '&' : '?';
		?>
		<?php if ( 'yes' === ( $s['show_chips'] ?? '' ) && $services ) : ?>
			<section class="section section--tight">
				<div class="wrap">
					<nav class="filters" aria-label="<?php esc_attr_e( 'Jump to a treatment', 'divine-beauty' ); ?>">
						<?php foreach ( $services as $svc ) : ?>
							<a class="btn btn-ghost btn-sm" href="#<?php echo esc_attr( $svc['slug'] ); ?>">
								<?php echo esc_html( $svc['name'] ); ?>
							</a>
						<?php endforeach; ?>
					</nav>
				</div>
			</section>
		<?php endif; ?>

		<section class="section" style="padding-top:0" aria-label="<?php esc_attr_e( 'Treatment details', 'divine-beauty' ); ?>">
			<div class="wrap">
				<?php foreach ( $services as $i => $svc ) : ?>
					<article class="svc" id="<?php echo esc_attr( $svc['slug'] ); ?>">
						<div class="svc-media">
							<div class="frame frame--gold ratio-portrait">
								<?php $this->image( (array) $svc['image'], (string) ( $svc['alt'] ?: $svc['name'] ), '', 900, 1200 ); ?>
							</div>
						</div>
						<div class="svc-body">
							<p class="svc-kicker">
								<b><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></b>
								<?php echo esc_html( $svc['kicker'] ); ?>
							</p>
							<h2><?php echo esc_html( $svc['name'] ); ?></h2>
							<p><?php echo esc_html( $svc['text'] ); ?></p>

							<?php
							$benefits = array_filter( array_map( 'trim', explode( "\n", (string) $svc['benefits'] ) ) );
							if ( $benefits ) :
								?>
								<dl class="svc-benefits">
									<dt><?php esc_html_e( 'Benefits', 'divine-beauty' ); ?></dt>
									<?php foreach ( $benefits as $benefit ) : ?>
										<dd><?php echo esc_html( $benefit ); ?></dd>
									<?php endforeach; ?>
								</dl>
							<?php endif; ?>

							<div class="band-actions">
								<a class="btn btn-gold" href="<?php echo esc_url( $booking . $joiner . 'service=' . rawurlencode( (string) $svc['name'] ) ); ?>">
									<?php esc_html_e( 'Book now', 'divine-beauty' ); ?> <span aria-hidden="true">&#8599;</span>
								</a>
								<a class="btn btn-ghost" href="<?php echo esc_url( $work ); ?>">
									<?php esc_html_e( 'See the work', 'divine-beauty' ); ?>
								</a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
