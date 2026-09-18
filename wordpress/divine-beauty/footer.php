<?php
/**
 * Site footer, including the floating WhatsApp button.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

use DivineBeauty\Customizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$divine_phone    = Customizer::get( 'phone' );
$divine_email    = Customizer::get( 'email' );
$divine_networks = array_filter(
	array(
		'instagram' => Customizer::get( 'instagram_url' ),
		'facebook'  => Customizer::get( 'facebook_url' ),
		'tiktok'    => Customizer::get( 'tiktok_url' ),
	)
);
?>
</main>

<footer class="site-footer">
	<div class="footer-grid">
		<div class="footer-brand">
			<?php
			printf(
				'<img src="%s" width="1600" height="527" alt="%s" loading="lazy">',
				esc_url( DIVINE_URI . '/assets/images/logo.png' ),
				esc_attr( get_bloginfo( 'name' ) )
			);
			?>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
			<?php if ( $divine_networks ) : ?>
				<div class="social">
					<?php foreach ( $divine_networks as $divine_net => $divine_url ) : ?>
						<a href="<?php echo esc_url( $divine_url ); ?>" target="_blank" rel="noopener"
							aria-label="<?php echo esc_attr( sprintf( /* translators: %s: social network. */ __( 'Divine Beauty on %s', 'divine-beauty' ), ucfirst( $divine_net ) ) ); ?>">
							<?php divine_the_icon( $divine_net ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( has_nav_menu( 'explore' ) ) : ?>
			<div class="footer-col">
				<h3><?php esc_html_e( 'Explore', 'divine-beauty' ); ?></h3>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'explore',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
		<?php endif; ?>

		<?php if ( has_nav_menu( 'legal' ) ) : ?>
			<div class="footer-col">
				<h3><?php esc_html_e( 'Treatments', 'divine-beauty' ); ?></h3>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>
		<?php endif; ?>

		<div class="footer-col">
			<h3><?php esc_html_e( 'Visit the studio', 'divine-beauty' ); ?></h3>
			<address>
				<strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong>
				<?php echo nl2br( esc_html( Customizer::get( 'address' ) ) ); ?><br>
				<?php if ( $divine_phone ) : ?>
					<a href="tel:<?php echo esc_attr( divine_tel( $divine_phone ) ); ?>"><?php echo esc_html( $divine_phone ); ?></a><br>
				<?php endif; ?>
				<?php if ( $divine_email ) : ?>
					<a href="mailto:<?php echo esc_attr( $divine_email ); ?>"><?php echo esc_html( $divine_email ); ?></a>
				<?php endif; ?>
			</address>
			<p class="footer-hours"><?php echo nl2br( esc_html( Customizer::get( 'hours' ) ) ); ?></p>
		</div>

		<?php if ( is_active_sidebar( 'footer-extras' ) ) : ?>
			<?php dynamic_sidebar( 'footer-extras' ); ?>
		<?php endif; ?>
	</div>

	<div class="footer-bar">
		<span>
			<?php
			printf(
				/* translators: 1: year, 2: site name. */
				esc_html__( '© %1$s %2$s', 'divine-beauty' ),
				esc_html( (string) gmdate( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
		</span>
		<nav aria-label="<?php esc_attr_e( 'Footer', 'divine-beauty' ); ?>">
			<a href="#main"><?php esc_html_e( 'Back to top', 'divine-beauty' ); ?> &uarr;</a>
		</nav>
		<span class="powered"><?php esc_html_e( 'Powered by', 'divine-beauty' ); ?> <b>Eagle Studio</b></span>
	</div>
</footer>

<?php if ( Customizer::whatsapp_enabled() ) : ?>
	<?php
	$divine_wa = divine_whatsapp_url(
		Customizer::get( 'whatsapp_number' ),
		Customizer::get( 'whatsapp_message' )
	);
	?>
	<?php if ( $divine_wa ) : ?>
		<a class="whatsapp-float" id="whatsapp-float" href="<?php echo esc_url( $divine_wa ); ?>"
			target="_blank" rel="noopener"
			aria-label="<?php esc_attr_e( 'Message the studio on WhatsApp', 'divine-beauty' ); ?>">
			<?php divine_the_icon( 'whatsapp' ); ?>
			<span class="whatsapp-label"><?php esc_html_e( 'Chat on WhatsApp', 'divine-beauty' ); ?></span>
		</a>
	<?php endif; ?>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
