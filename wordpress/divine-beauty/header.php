<?php
/**
 * Site header: announcement bar, logo, navigation and the booking button.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

use DivineBeauty\Customizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$divine_phone     = Customizer::get( 'phone' );
$divine_topbar    = Customizer::get( 'topbar_text' );
$divine_networks  = array_filter(
	array(
		'instagram' => Customizer::get( 'instagram_url' ),
		'facebook'  => Customizer::get( 'facebook_url' ),
		'tiktok'    => Customizer::get( 'tiktok_url' ),
	)
);
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#0b0a08">
	<script>document.documentElement.classList.remove('no-js');</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip" href="#main"><?php esc_html_e( 'Skip to content', 'divine-beauty' ); ?></a>

<?php if ( $divine_topbar || $divine_phone ) : ?>
	<div class="topbar">
		<?php if ( $divine_topbar ) : ?>
			<p class="topbar-main"><?php echo esc_html( $divine_topbar ); ?></p>
		<?php endif; ?>
		<?php if ( $divine_phone ) : ?>
			<a class="topbar-contact" href="tel:<?php echo esc_attr( divine_tel( $divine_phone ) ); ?>">
				<?php divine_the_icon( 'phone' ); ?>
				<span><?php echo esc_html( $divine_phone ); ?></span>
			</a>
		<?php endif; ?>
		<span class="topbar-side"><?php esc_html_e( 'Book your appointment today', 'divine-beauty' ); ?></span>
	</div>
<?php endif; ?>

<header class="header" id="site-header">
	<div class="header-inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"
			aria-label="<?php echo esc_attr( sprintf( /* translators: %s: site name. */ __( '%s — home', 'divine-beauty' ), get_bloginfo( 'name' ) ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				printf(
					'<img src="%s" width="1600" height="527" alt="%s">',
					esc_url( DIVINE_URI . '/assets/images/logo.png' ),
					esc_attr( get_bloginfo( 'name' ) )
				);
			}
			?>
		</a>

		<nav class="nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Main', 'divine-beauty' ); ?>">
			<div class="nav-head">
				<span><?php esc_html_e( 'Menu', 'divine-beauty' ); ?></span>
				<button class="nav-close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'divine-beauty' ); ?>">&times;</button>
			</div>

			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'depth'          => 1,
					'walker'         => new \DivineBeauty\Nav_Walker(),
					'fallback_cb'    => false,
				)
			);
			?>

			<div class="nav-foot">
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
				<?php if ( $divine_phone ) : ?>
					<p><?php esc_html_e( 'Call or message the studio', 'divine-beauty' ); ?></p>
					<p><a class="plain" href="tel:<?php echo esc_attr( divine_tel( $divine_phone ) ); ?>"><?php echo esc_html( $divine_phone ); ?></a></p>
				<?php endif; ?>
			</div>
		</nav>

		<div class="header-actions">
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

			<a class="btn btn-gold btn-sm" href="<?php echo esc_url( home_url( '/#booking' ) ); ?>">
				<?php esc_html_e( 'Book now', 'divine-beauty' ); ?> <span aria-hidden="true">&#8599;</span>
			</a>

			<button class="nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'divine-beauty' ); ?>"
				aria-expanded="false" aria-controls="primary-nav">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
</header>
<div class="nav-scrim" hidden></div>

<main id="main">
