<?php
/**
 * Shared base for every Divine Beauty widget.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Collects the conventions every widget in this theme shares.
 */
abstract class Divine_Widget extends Widget_Base {

	/**
	 * Put every widget in the theme's own panel category.
	 *
	 * @return string[]
	 */
	public function get_categories(): array {
		return array( 'divine-beauty' );
	}

	/**
	 * Load the theme stylesheets inside the editor preview.
	 *
	 * @return string[]
	 */
	public function get_style_depends(): array {
		return array( 'divine-main', 'divine-layout' );
	}

	/**
	 * The behaviour script, so carousels and dialogs work in the preview too.
	 *
	 * @return string[]
	 */
	public function get_script_depends(): array {
		return array( 'divine-site' );
	}

	/**
	 * Add the eyebrow / heading / lede trio most sections open with.
	 *
	 * The heading accepts a line break so the editorial two-line headings in the
	 * design survive being retyped by a non-technical editor.
	 *
	 * @param string $eyebrow Default eyebrow.
	 * @param string $heading Default heading, may contain <br> and <em>.
	 * @param string $lede    Default supporting paragraph.
	 * @return void
	 */
	protected function add_heading_controls( string $eyebrow = '', string $heading = '', string $lede = '' ): void {
		$this->add_control(
			'eyebrow',
			array(
				'label'       => __( 'Eyebrow', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $eyebrow,
				'description' => __( 'The small gold label above the heading.', 'divine-beauty' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_control(
			'heading',
			array(
				'label'       => __( 'Heading', 'divine-beauty' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => $heading,
				'description' => __( 'Use &lt;br&gt; for a line break and &lt;em&gt;…&lt;/em&gt; for the gold italic words.', 'divine-beauty' ),
				'dynamic'     => array( 'active' => true ),
			)
		);
		$this->add_control(
			'lede',
			array(
				'label'   => __( 'Supporting text', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => $lede,
				'dynamic' => array( 'active' => true ),
			)
		);
	}

	/**
	 * Add a pair of call-to-action buttons.
	 *
	 * @param string $primary_text Default primary label.
	 * @param string $ghost_text   Default secondary label.
	 * @return void
	 */
	protected function add_button_controls( string $primary_text = '', string $ghost_text = '' ): void {
		$this->add_control(
			'primary_text',
			array(
				'label'   => __( 'Primary button', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $primary_text,
			)
		);
		$this->add_control(
			'primary_link',
			array(
				'label'   => __( 'Primary button link', 'divine-beauty' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#booking' ),
			)
		);
		$this->add_control(
			'ghost_text',
			array(
				'label'   => __( 'Secondary button', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $ghost_text,
			)
		);
		$this->add_control(
			'ghost_link',
			array(
				'label' => __( 'Secondary button link', 'divine-beauty' ),
				'type'  => Controls_Manager::URL,
			)
		);
	}

	/**
	 * Print a heading, keeping only the inline tags the design uses.
	 *
	 * @param string $html  Heading text, possibly containing <br> and <em>.
	 * @param string $tag   Wrapping tag.
	 * @param string $attrs Extra attributes, already escaped.
	 * @return void
	 */
	protected function heading( string $html, string $tag = 'h2', string $attrs = '' ): void {
		if ( '' === trim( $html ) ) {
			return;
		}

		$allowed = array(
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'span'   => array( 'class' => array() ),
		);

		printf(
			'<%1$s%2$s>%3$s</%1$s>',
			tag_escape( $tag ),
			$attrs ? ' ' . $attrs : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Callers pass pre-escaped attributes.
			wp_kses( $html, $allowed )
		);
	}

	/**
	 * Print one of the theme's buttons.
	 *
	 * @param string              $text  Label.
	 * @param array<string,mixed> $link  Elementor URL control value.
	 * @param string              $style "gold" or "ghost".
	 * @param bool                $arrow Whether to append the diagonal arrow.
	 * @return void
	 */
	protected function button( string $text, array $link, string $style = 'gold', bool $arrow = true ): void {
		if ( '' === trim( $text ) ) {
			return;
		}

		$url    = $link['url'] ?? '#';
		$target = ! empty( $link['is_external'] ) ? ' target="_blank"' : '';
		$rel    = ! empty( $link['nofollow'] ) ? ' rel="nofollow noopener"' : ( $target ? ' rel="noopener"' : '' );

		printf(
			'<a class="btn btn-%1$s" href="%2$s"%3$s%4$s>%5$s%6$s</a>',
			esc_attr( $style ),
			esc_url( $url ),
			$target, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed literal above.
			$rel,    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed literal above.
			esc_html( $text ),
			$arrow ? ' <span aria-hidden="true">&#8599;</span>' : ''
		);
	}

	/**
	 * Print an image from a media control, falling back to a bundled asset.
	 *
	 * @param array<string,mixed> $media    Elementor media control value.
	 * @param string              $alt      Alternative text.
	 * @param string              $fallback File name in assets/images/.
	 * @param int                 $width    Intrinsic width.
	 * @param int                 $height   Intrinsic height.
	 * @param bool                $eager    Load immediately rather than lazily.
	 * @return void
	 */
	protected function image( array $media, string $alt, string $fallback, int $width, int $height, bool $eager = false ): void {
		$url = divine_image_url( $media, $fallback );

		if ( '' === $url ) {
			return;
		}

		printf(
			'<img src="%s" alt="%s" width="%d" height="%d" %s decoding="async">',
			esc_url( $url ),
			esc_attr( $alt ),
			$width,
			$height,
			$eager ? 'fetchpriority="high"' : 'loading="lazy"'
		);
	}
}
