<?php
/**
 * Single post.
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
	?>
	<article <?php post_class(); ?>>
		<section class="page-hero">
			<div class="wrap">
				<p class="eyebrow centred"><?php echo esc_html( get_the_date() ); ?></p>
				<h1><?php the_title(); ?></h1>
			</div>
		</section>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="wrap">
				<div class="frame frame--gold"><?php the_post_thumbnail( 'large' ); ?></div>
			</div>
		<?php endif; ?>

		<section class="section">
			<div class="wrap-narrow prose">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<nav class="page-links">', 'after' => '</nav>' ) );
				?>
			</div>
		</section>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) {
		?>
		<section class="section on-paper">
			<div class="wrap-narrow"><?php comments_template(); ?></div>
		</section>
		<?php
	}

endwhile;

get_footer();
