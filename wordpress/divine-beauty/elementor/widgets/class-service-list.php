<?php
/**
 * The full treatment menu, grouped.
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
 * Every treatment the studio offers, arranged into groups with a jump-to row.
 *
 * At seventeen treatments a single alternating list became a very long scroll,
 * so the menu is grouped and each treatment is a card carrying its own benefits
 * and booking link — readable without opening anything.
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
		return array( 'services', 'menu', 'treatments', 'facials', 'massage', 'benefits' );
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
				'description' => __( 'Each "Book now" adds its treatment name to this link.', 'divine-beauty' ),
			)
		);

		$this->end_controls_section();

		/* ------------------------------------------------------------ groups */
		$this->start_controls_section( 'groups_section', array( 'label' => __( 'Groups', 'divine-beauty' ) ) );

		$group = new Repeater();
		$group->add_control(
			'key',
			array(
				'label'       => __( 'Group key', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Lowercase, no spaces. Each treatment below is tagged with one of these.', 'divine-beauty' ),
			)
		);
		$group->add_control(
			'label',
			array(
				'label' => __( 'Group name', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$group->add_control(
			'heading',
			array(
				'label'       => __( 'Group heading', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => __( 'Use &lt;br&gt; for a line break and &lt;em&gt;…&lt;/em&gt; for the gold italic words.', 'divine-beauty' ),
			)
		);
		$group->add_control(
			'standfirst',
			array(
				'label' => __( 'Group introduction', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 3,
			)
		);

		$this->add_control(
			'groups',
			array(
				'label'       => __( 'Groups', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $group->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => divine_treatment_groups(),
			)
		);

		$this->end_controls_section();

		/* -------------------------------------------------------- treatments */
		$this->start_controls_section( 'items', array( 'label' => __( 'Treatments', 'divine-beauty' ) ) );

		$svc = new Repeater();
		$svc->add_control(
			'name',
			array(
				'label' => __( 'Treatment name', 'divine-beauty' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$svc->add_control(
			'slug',
			array(
				'label'       => __( 'Anchor', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Lowercase, hyphens only. Used for the jump link.', 'divine-beauty' ),
			)
		);
		$svc->add_control(
			'group',
			array(
				'label'       => __( 'Group key', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Must match one of the group keys above.', 'divine-beauty' ),
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
				'label'       => __( 'Describe the photograph', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Read aloud to visitors who cannot see it.', 'divine-beauty' ),
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
				'rows'        => 5,
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
				'default'     => $this->defaults(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * The studio's menu, with the bundled photography resolved to real URLs.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function defaults(): array {
		$items = array();

		foreach ( divine_treatments() as $t ) {
			$items[] = array(
				'name'     => $t['name'],
				'slug'     => $t['slug'],
				'group'    => $t['group'],
				'image'    => array( 'url' => DIVINE_URI . '/assets/images/' . $t['image'] ),
				'alt'      => $t['alt'],
				'text'     => $t['text'],
				'benefits' => $t['benefits'],
			);
		}

		return $items;
	}

	protected function render(): void {
		$s        = $this->get_settings_for_display();
		$groups   = (array) $s['groups'];
		$services = (array) $s['services'];
		$booking  = $s['booking_url']['url'] ?? '/#booking';
		$joiner   = str_contains( (string) $booking, '?' ) ? '&' : '?';

		// Only offer a jump link for a group that has treatments in it.
		$used = array();
		foreach ( $services as $svc ) {
			$used[ (string) $svc['group'] ] = true;
		}
		?>

		<?php if ( 'yes' === ( $s['show_chips'] ?? '' ) && $groups ) : ?>
			<section class="section section--tight">
				<div class="wrap">
					<nav class="filters" aria-label="<?php esc_attr_e( 'Jump to a group', 'divine-beauty' ); ?>">
						<?php foreach ( $groups as $group ) : ?>
							<?php if ( empty( $used[ (string) $group['key'] ] ) ) { continue; } ?>
							<a class="btn btn-ghost btn-sm" href="#<?php echo esc_attr( $group['key'] ); ?>">
								<?php echo esc_html( $group['label'] ); ?>
							</a>
						<?php endforeach; ?>
					</nav>
				</div>
			</section>
		<?php endif; ?>

		<?php foreach ( $groups as $group ) : ?>
			<?php
			$key  = (string) $group['key'];
			$rows = array_values( array_filter( $services, static fn( $x ): bool => (string) $x['group'] === $key ) );
			if ( ! $rows ) {
				continue;
			}
			?>
			<section class="section" id="<?php echo esc_attr( $key ); ?>"
				aria-labelledby="<?php echo esc_attr( $key ); ?>-title">
				<div class="wrap">
					<div class="section-head">
						<div>
							<p class="eyebrow"><?php echo esc_html( $group['label'] ); ?></p>
							<?php $this->heading( (string) $group['heading'], 'h2', 'id="' . esc_attr( $key ) . '-title"' ); ?>
						</div>
						<?php if ( ! empty( $group['standfirst'] ) ) : ?>
							<p class="lede"><?php echo esc_html( $group['standfirst'] ); ?></p>
						<?php endif; ?>
					</div>

					<div class="card-grid card-grid-3">
						<?php foreach ( $rows as $svc ) : ?>
							<article class="s-card" id="<?php echo esc_attr( $svc['slug'] ); ?>">
								<div class="s-card-media">
									<?php $this->image( (array) $svc['image'], (string) ( $svc['alt'] ?: $svc['name'] ), '', 800, 1000 ); ?>
								</div>
								<div class="s-card-body">
									<h3><?php echo esc_html( $svc['name'] ); ?></h3>
									<p><?php echo esc_html( $svc['text'] ); ?></p>

									<?php
									$benefits = array_filter( array_map( 'trim', explode( "\n", (string) $svc['benefits'] ) ) );
									if ( $benefits ) :
										?>
										<dl class="s-card-benefits">
											<dt><?php esc_html_e( 'Benefits', 'divine-beauty' ); ?></dt>
											<?php foreach ( $benefits as $benefit ) : ?>
												<dd><?php echo esc_html( $benefit ); ?></dd>
											<?php endforeach; ?>
										</dl>
									<?php endif; ?>

									<a class="btn btn-gold btn-sm"
										href="<?php echo esc_url( $booking . $joiner . 'service=' . rawurlencode( (string) $svc['name'] ) ); ?>">
										<?php esc_html_e( 'Book now', 'divine-beauty' ); ?> <span aria-hidden="true">&#8599;</span>
									</a>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endforeach; ?>
		<?php
	}
}
