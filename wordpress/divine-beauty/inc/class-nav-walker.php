<?php
/**
 * Navigation walker.
 *
 * The header lays its links out as flex children directly inside `.nav`.
 * WordPress's default walker wraps each one in an `<li>`, which becomes the
 * flex item instead and brings a list marker with it. This walker emits the
 * links on their own, so a WordPress menu renders exactly like the design.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Emits `<a class="nav-link">` elements with no list wrapper.
 */
class Nav_Walker extends \Walker_Nav_Menu {

	/**
	 * Database fields to use.
	 *
	 * @var array<string,string>
	 */
	public $db_fields = array(
		'parent' => 'menu_item_parent',
		'id'     => 'db_id',
	);

	/**
	 * No sub-menus: the header menu is intentionally one level deep.
	 *
	 * @param string $output Walker output.
	 * @param int    $depth  Current depth.
	 * @param array  $args   Menu arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * No sub-menus.
	 *
	 * @param string $output Walker output.
	 * @param int    $depth  Current depth.
	 * @param array  $args   Menu arguments.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * Render one link.
	 *
	 * @param string   $output Walker output.
	 * @param \WP_Post $item   Menu item.
	 * @param int      $depth  Current depth.
	 * @param array    $args   Menu arguments.
	 * @param int      $id     Menu item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = array( 'nav-link' );
		if ( in_array( 'current-menu-item', (array) $item->classes, true ) ) {
			$classes[] = 'is-current';
		}

		$current = in_array( 'current-menu-item', (array) $item->classes, true )
			|| ( is_front_page() && in_array( 'current_page_item', (array) $item->classes, true ) );

		$output .= sprintf(
			'<a class="%1$s" href="%2$s"%3$s%4$s%5$s>%6$s</a>',
			esc_attr( implode( ' ', $classes ) ),
			esc_url( $item->url ?: '#' ),
			$current ? ' aria-current="page"' : '',
			$item->target ? ' target="' . esc_attr( $item->target ) . '"' : '',
			$item->xfn ? ' rel="' . esc_attr( $item->xfn ) . '"' : '',
			esc_html( $item->title )
		);
	}

	/**
	 * Nothing to close — `start_el()` emits a complete element.
	 *
	 * @param string   $output Walker output.
	 * @param \WP_Post $item   Menu item.
	 * @param int      $depth  Current depth.
	 * @param array    $args   Menu arguments.
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}
