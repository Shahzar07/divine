<?php
/**
 * Single page.
 *
 * Pages built with Elementor render their own sections, so the theme steps out
 * of the way and prints the content alone. Pages written in the block editor
 * get the standard framing instead.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$divine_is_elementor = false;
	if ( class_exists( '\Elementor\Plugin' ) ) {
		$divine_document     = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
		$divine_is_elementor = $divine_document && $divine_document->is_built_with_elementor();
	}

	if ( $divine_is_elementor ) {
		the_content();
	} else {
		?>
		<section class="page-hero">
			<div class="wrap">
				<h1><?php the_title(); ?></h1>
			</div>
		</section>
		<section class="section">
			<div class="wrap-narrow prose">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<nav class="page-links">', 'after' => '</nav>' ) );
				?>
			</div>
		</section>
		<?php
	}

endwhile;

get_footer();
