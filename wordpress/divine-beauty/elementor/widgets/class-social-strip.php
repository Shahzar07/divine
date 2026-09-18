<?php
/**
 * Social follow strip.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty\Widgets;

use DivineBeauty\Customizer;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Invitation to follow, using the links set in Appearance → Customise.
 */
class Social_Strip extends Divine_Widget {

	public function get_name(): string {
		return 'divine-social-strip';
	}

	public function get_title(): string {
		return __( 'Social strip', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-social-icons';
	}

	public function get_keywords(): array {
		return array( 'social', 'instagram', 'facebook', 'follow' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'divine-beauty' ) ) );

		$this->add_control(
			'notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'The profile links come from Appearance → Customise → Studio details, so they only need setting once for the whole site.', 'divine-beauty' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Eyebrow', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Follow along', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'New sets, fresh looks and studio news', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => __( 'Come and say hello — that is where the newest work lands first.', 'divine-beauty' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$networks = array(
			'instagram' => Customizer::get( 'instagram_url' ),
			'facebook'  => Customizer::get( 'facebook_url' ),
			'tiktok'    => Customizer::get( 'tiktok_url' ),
		);
		$networks = array_filter( $networks );
		?>
		<section class="section section--tight" aria-label="<?php esc_attr_e( 'Follow the studio', 'divine-beauty' ); ?>">
			<div class="wrap">
				<div class="social-strip">
					<div>
						<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
							<p class="eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
						<?php endif; ?>
						<h3><?php echo esc_html( $s['title'] ); ?></h3>
						<p><?php echo esc_html( $s['text'] ); ?></p>
					</div>

					<?php if ( $networks ) : ?>
						<div class="social">
							<?php foreach ( $networks as $network => $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"
									aria-label="<?php
									/* translators: %s: social network name. */
									echo esc_attr( sprintf( __( 'Divine Beauty on %s', 'divine-beauty' ), ucfirst( $network ) ) );
									?>">
									<?php divine_the_icon( $network ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
