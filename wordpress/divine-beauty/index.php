<?php
/**
 * Fallback template — the blog index and anything without a closer match.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="page-hero">
	<div class="wrap">
		<h1><?php echo esc_html( is_home() ? get_the_title( (int) get_option( 'page_for_posts' ) ) : get_the_archive_title() ); ?></h1>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="work-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'piece' ); ?>>
						<a class="piece-open" href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<span class="piece-media"><?php the_post_thumbnail( 'divine-card' ); ?></span>
							<?php endif; ?>
							<span class="piece-body">
								<span class="piece-title"><?php the_title(); ?></span>
								<span class="piece-summary"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></span>
								<span class="piece-cue" aria-hidden="true"><?php esc_html_e( 'Read more', 'divine-beauty' ); ?> &#8599;</span>
							</span>
						</a>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( '&larr; Newer', 'divine-beauty' ),
					'next_text' => __( 'Older &rarr;', 'divine-beauty' ),
				)
			);
			?>
		<?php else : ?>
			<p class="lede"><?php esc_html_e( 'Nothing here yet.', 'divine-beauty' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
