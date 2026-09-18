<?php
/**
 * Not found.
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
		<p class="eyebrow centred"><?php esc_html_e( 'Error 404', 'divine-beauty' ); ?></p>
		<h1><?php esc_html_e( 'This page has', 'divine-beauty' ); ?><br><em><?php esc_html_e( 'moved on.', 'divine-beauty' ); ?></em></h1>
		<p class="lede">
			<?php esc_html_e( 'The page you were looking for is not here. Try the treatment menu or the portfolio — or message the studio and Dee will point you the right way.', 'divine-beauty' ); ?>
		</p>
		<p style="margin-top:32px">
			<a class="btn btn-gold" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Back to the studio', 'divine-beauty' ); ?> <span aria-hidden="true">&#8599;</span>
			</a>
		</p>
	</div>
</section>

<?php
get_footer();
