<?php
/**
 * One-click site setup.
 *
 * Activating a theme normally leaves you with an empty site and a pile of
 * instructions. This builds the three pages as real Elementor layouts, points
 * the front page at Home and fills the menus — so the studio opens Elementor
 * and edits the finished site rather than assembling it.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates the starter pages, menus and front-page settings.
 */
class Starter_Content {

	/**
	 * Option that records the setup has already run.
	 */
	private const FLAG = 'divine_starter_content_done';

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		add_action( 'after_switch_theme', array( $this, 'maybe_install' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_post_divine_install_content', array( $this, 'handle_manual_install' ) );
	}

	/**
	 * Build the site the first time the theme is activated.
	 *
	 * @return void
	 */
	public function maybe_install(): void {
		if ( get_option( self::FLAG ) ) {
			return;
		}
		$this->install();
	}

	/**
	 * Add the "Set up site" screen under Appearance.
	 *
	 * @return void
	 */
	public function add_page(): void {
		add_theme_page(
			__( 'Divine Beauty setup', 'divine-beauty' ),
			__( 'Divine Beauty setup', 'divine-beauty' ),
			'edit_theme_options',
			'divine-setup',
			array( $this, 'render_page' )
		);
	}

	/**
	 * The setup screen.
	 *
	 * @return void
	 */
	public function render_page(): void {
		$done = (bool) get_option( self::FLAG );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Divine Beauty setup', 'divine-beauty' ); ?></h1>

			<p>
				<?php esc_html_e( 'This builds the Home, Services and Portfolio pages as Elementor layouts, sets Home as the front page and fills the menus. Every section it creates is a Divine Beauty widget you can edit in Elementor.', 'divine-beauty' ); ?>
			</p>

			<?php if ( $done ) : ?>
				<div class="notice notice-success inline">
					<p><?php esc_html_e( 'The starter pages have been created. Running it again will create a second set — only do that if you deleted the originals.', 'divine-beauty' ); ?></p>
				</div>
			<?php endif; ?>

			<h2><?php esc_html_e( 'Before you launch', 'divine-beauty' ); ?></h2>
			<ol>
				<li><?php esc_html_e( 'Appearance → Customise → Studio details: set the phone, email, address, hours, WhatsApp number and the real Instagram and Facebook links.', 'divine-beauty' ); ?></li>
				<li><?php esc_html_e( 'Replace any photograph on Home and Services with your own once you have it — every image is an Elementor control.', 'divine-beauty' ); ?></li>
				<li><?php esc_html_e( 'The Portfolio gallery should only ever show your own work. Add to it as you photograph new sets.', 'divine-beauty' ); ?></li>
				<li><?php esc_html_e( 'Reviews: publish only quotes a client actually left, with their name.', 'divine-beauty' ); ?></li>
			</ol>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="divine_install_content">
				<?php wp_nonce_field( 'divine_install_content' ); ?>
				<p>
					<button type="submit" class="button button-primary">
						<?php
						echo $done
							? esc_html__( 'Create the starter pages again', 'divine-beauty' )
							: esc_html__( 'Set up my site', 'divine-beauty' );
						?>
					</button>
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle the button on the setup screen.
	 *
	 * @return void
	 */
	public function handle_manual_install(): void {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to set this site up.', 'divine-beauty' ) );
		}
		check_admin_referer( 'divine_install_content' );

		$this->install();

		wp_safe_redirect( add_query_arg( 'page', 'divine-setup', admin_url( 'themes.php' ) ) );
		exit;
	}

	/**
	 * Create the pages, menus and settings.
	 *
	 * @return void
	 */
	private function install(): void {
		$home      = $this->create_page( __( 'Home', 'divine-beauty' ), 'home', $this->home_layout() );
		$services  = $this->create_page( __( 'Services', 'divine-beauty' ), 'services', $this->services_layout() );
		$portfolio = $this->create_page( __( 'Portfolio', 'divine-beauty' ), 'portfolio', $this->portfolio_layout() );

		if ( $home ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home );
		}

		$this->build_menu( 'primary', __( 'Primary menu', 'divine-beauty' ), array( $home, $services, $portfolio ) );
		$this->build_menu( 'explore', __( 'Footer — Explore', 'divine-beauty' ), array( $home, $services, $portfolio ) );

		update_option( self::FLAG, time() );
	}

	/**
	 * Create one Elementor page.
	 *
	 * @param string                    $title  Page title.
	 * @param string                    $slug   Page slug.
	 * @param array<int,array<string,mixed>> $layout Elementor document data.
	 * @return int The new page ID, or 0 on failure.
	 */
	private function create_page( string $title, string $slug, array $layout ): int {
		$page_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( is_wp_error( $page_id ) ) {
			return 0;
		}

		// Elementor stores its document as JSON in post meta and needs these two
		// flags before it will treat the page as one of its own.
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $page_id, '_elementor_version', '3.0.0' );
		update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
		// wp_slash keeps Elementor's JSON intact through WordPress's unslashing.
		update_post_meta( $page_id, '_elementor_data', wp_slash( (string) wp_json_encode( $layout ) ) );

		return (int) $page_id;
	}

	/**
	 * Wrap widgets in the section/column structure Elementor expects.
	 *
	 * @param array<int,array{0:string,1:array<string,mixed>}> $widgets Widget type and settings pairs.
	 * @return array<int,array<string,mixed>>
	 */
	private function sections( array $widgets ): array {
		$sections = array();

		foreach ( $widgets as $widget ) {
			list( $type, $settings ) = $widget;

			$sections[] = array(
				'id'       => $this->id(),
				'elType'   => 'section',
				'settings' => array( 'layout' => 'full_width', 'gap' => 'no' ),
				'elements' => array(
					array(
						'id'       => $this->id(),
						'elType'   => 'column',
						'settings' => array( '_column_size' => 100, '_inline_size' => null ),
						'elements' => array(
							array(
								'id'         => $this->id(),
								'elType'     => 'widget',
								'widgetType' => $type,
								'settings'   => $settings,
							),
						),
					),
				),
			);
		}

		return $sections;
	}

	/**
	 * An Elementor-style element id.
	 *
	 * @return string
	 */
	private function id(): string {
		return substr( md5( uniqid( '', true ) ), 0, 7 );
	}

	/**
	 * The home page, section by section.
	 *
	 * Settings are left empty wherever the widget's own defaults already match
	 * the design; only the five treatment bands differ from one another.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function home_layout(): array {
		$img = static fn( string $file ): array => array( 'url' => DIVINE_URI . '/assets/images/' . $file );

		$bands = array(
			array( '01', 'Facials & skin', 'Six facials.<br><em>One skin — yours.</em>', 'band-facials.jpg', '', '6', '', 'Facials offered' ),
			array( '02', 'Massage & body', 'Five ways<br><em>to unwind.</em>', 'svc-swedish.jpg', 'yes', '5', '', 'Massage treatments' ),
			array( '03', 'Body contouring', 'Sculpted<br><em>by hand.</em>', 'work-contouring-waist.jpg', '', '0', '', 'Machines used' ),
			array( '04', 'Nails', 'Shaped to you.<br><em>Sealed to last.</em>', 'band-nails.jpg', 'yes', '2–3', 'wks', 'Typical wear' ),
			array( '05', 'Makeup & glam', 'Skin first.<br><em>Then the drama.</em>', 'band-makeup.jpg', '', '', '', '' ),
		);

		$widgets = array(
			array( 'divine-hero', array() ),
			array( 'divine-marquee', array() ),
			array( 'divine-welcome', array() ),
			array( 'divine-service-carousel', array() ),
		);

		foreach ( $bands as $band ) {
			$widgets[] = array(
				'divine-band',
				array(
					'number'     => $band[0],
					'eyebrow'    => $band[1],
					'heading'    => $band[2],
					'image'      => $img( $band[3] ),
					'alt'        => $band[1],
					'flip'       => $band[4],
					'stat_value' => $band[5],
					'stat_unit'  => $band[6],
					'stat_label' => $band[7],
				),
			);
		}

		$widgets[] = array( 'divine-promises', array() );
		$widgets[] = array(
			'divine-cta-band',
			array(
				'eyebrow'      => __( 'The portfolio', 'divine-beauty' ),
				'heading'      => __( 'Every set, every look,<br><em>photographed here.</em>', 'divine-beauty' ),
				'lede'         => __( 'The gallery is the studio\'s own work, shot in the room it was finished in.', 'divine-beauty' ),
				'primary_text' => __( 'View the portfolio', 'divine-beauty' ),
				'primary_link' => array( 'url' => '/portfolio/' ),
				'ghost_text'   => __( 'Browse treatments', 'divine-beauty' ),
				'ghost_link'   => array( 'url' => '/services/' ),
			),
		);
		$widgets[] = array( 'divine-testimonial', array() );
		$widgets[] = array( 'divine-social-strip', array() );
		$widgets[] = array( 'divine-booking', array() );

		return $this->sections( $widgets );
	}

	/**
	 * The services page.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function services_layout(): array {
		return $this->sections(
			array(
				array(
					'divine-page-hero',
					array(
						'crumb'   => __( 'Services', 'divine-beauty' ),
						'eyebrow' => __( 'The menu', 'divine-beauty' ),
						'heading' => __( 'Every treatment,<br><em>in full.</em>', 'divine-beauty' ),
						'lede'    => __( 'Seventeen treatments across skin, body, nails and glam — what each one involves, and what it is actually for.', 'divine-beauty' ),
					),
				),
				array( 'divine-service-list', array() ),
				array( 'divine-cta-band', array() ),
			)
		);
	}

	/**
	 * The portfolio page.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function portfolio_layout(): array {
		return $this->sections(
			array(
				array( 'divine-page-hero', array() ),
				array( 'divine-gallery', array() ),
				array( 'divine-social-strip', array() ),
				array(
					'divine-cta-band',
					array(
						'eyebrow' => __( 'Your turn', 'divine-beauty' ),
						'heading' => __( 'Seen something<br><em>you love?</em>', 'divine-beauty' ),
						'lede'    => __( 'Pick a look from the gallery and Dee will tailor the shape, colour and finish to you.', 'divine-beauty' ),
					),
				),
			)
		);
	}

	/**
	 * Create a menu and assign it to a location.
	 *
	 * @param string $location Theme menu location.
	 * @param string $name     Menu name.
	 * @param int[]  $pages    Page IDs, in order.
	 * @return void
	 */
	private function build_menu( string $location, string $name, array $pages ): void {
		$pages = array_filter( $pages );
		if ( empty( $pages ) ) {
			return;
		}

		$menu = wp_get_nav_menu_object( $name );
		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $name );
			if ( is_wp_error( $menu_id ) ) {
				return;
			}
		} else {
			$menu_id = (int) $menu->term_id;
		}

		// Only fill a menu that is empty, so a re-run never duplicates items.
		if ( ! empty( wp_get_nav_menu_items( $menu_id ) ) ) {
			return;
		}

		foreach ( $pages as $page_id ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-object-id' => $page_id,
					'menu-item-object'    => 'page',
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-title'     => get_the_title( $page_id ),
				)
			);
		}

		$locations              = (array) get_theme_mod( 'nav_menu_locations', array() );
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
