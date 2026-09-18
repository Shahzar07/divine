<?php
/**
 * Inner-page hero with breadcrumbs.
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
 * The compact, centred opening used on Services and Portfolio.
 */
class Page_Hero extends Divine_Widget {

	public function get_name(): string {
		return 'divine-page-hero';
	}

	public function get_title(): string {
		return __( 'Page hero', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-single-page';
	}

	public function get_keywords(): array {
		return array( 'hero', 'title', 'page', 'breadcrumb' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'divine-beauty' ) ) );

		$this->add_control(
			'crumb',
			array(
				'label'       => __( 'Breadcrumb label', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Portfolio', 'divine-beauty' ),
				'description' => __( 'Shown after "Home /". Leave empty to hide the breadcrumb.', 'divine-beauty' ),
			)
		);

		$this->add_heading_controls(
			__( 'The work', 'divine-beauty' ),
			__( 'Photographed<br><em>in the room.</em>', 'divine-beauty' ),
			__( 'Every piece below was created at the studio and photographed on the day it was finished.', 'divine-beauty' )
		);

		$this->add_control(
			'cta_text',
			array(
				'label'     => __( 'Button', 'divine-beauty' ),
				'type'      => Controls_Manager::TEXT,
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

	protected function render(): void {
		$s = $this->get_settings_for_display();
		?>
		<section class="page-hero">
			<div class="wrap">
				<?php if ( ! empty( $s['crumb'] ) ) : ?>
					<nav class="crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'divine-beauty' ); ?>">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'divine-beauty' ); ?></a>
						<span aria-hidden="true">&#47;</span>
						<span><?php echo esc_html( $s['crumb'] ); ?></span>
					</nav>
				<?php endif; ?>

				<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
					<p class="eyebrow centred"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php $this->heading( (string) $s['heading'], 'h1' ); ?>

				<?php if ( ! empty( $s['lede'] ) ) : ?>
					<p class="lede"><?php echo esc_html( $s['lede'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $s['cta_text'] ) ) : ?>
					<p style="margin-top:32px"><?php $this->button( (string) $s['cta_text'], (array) $s['cta_link'], 'gold' ); ?></p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
