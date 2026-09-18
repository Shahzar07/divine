<?php
/**
 * Enquiry form and contact block.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty\Widgets;

use DivineBeauty\Customizer;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The closing section: how to reach the studio, and a form that emails it.
 */
class Booking extends Divine_Widget {

	public function get_name(): string {
		return 'divine-booking';
	}

	public function get_title(): string {
		return __( 'Enquiry form', 'divine-beauty' );
	}

	public function get_icon(): string {
		return 'eicon-form-horizontal';
	}

	public function get_keywords(): array {
		return array( 'booking', 'contact', 'form', 'enquiry', 'appointment' );
	}

	protected function register_controls(): void {

		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'divine-beauty' ) ) );

		$this->add_control(
			'notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Enquiries are emailed to the address in Appearance → Customise → Studio details.', 'divine-beauty' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_heading_controls(
			__( 'Reserve your moment', 'divine-beauty' ),
			__( "Let's make it<br><em>beautiful.</em>", 'divine-beauty' ),
			__( 'Tell Dee what you have in mind and she will come back with pricing and availability.', 'divine-beauty' )
		);

		$this->end_controls_section();

		$this->start_controls_section( 'form', array( 'label' => __( 'Form', 'divine-beauty' ) ) );

		$option = new Repeater();
		$option->add_control(
			'label',
			array(
				'label'   => __( 'Treatment', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Massage', 'divine-beauty' ),
			)
		);

		$this->add_control(
			'services',
			array(
				'label'       => __( 'Treatments in the dropdown', 'divine-beauty' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $option->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array_map(
					static fn( string $l ): array => array( 'label' => $l ),
					array(
						'Massage',
						'Cupping Therapy',
						'Makeup & Glam',
						'Facials & Skin',
						'Gel Nails',
						'Nail Extensions',
						'Nail Art & Detail',
						'Manicure & Pedicure',
						'Brow & Lash Finish',
						"I'd like some guidance",
					)
				),
			)
		);

		$this->add_control(
			'submit_text',
			array(
				'label'   => __( 'Submit button', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Send my enquiry', 'divine-beauty' ),
			)
		);
		$this->add_control(
			'consent_text',
			array(
				'label'   => __( 'Note under the button', 'divine-beauty' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => __( 'Your details are used only to answer this enquiry. No appointment is confirmed until Dee replies.', 'divine-beauty' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$s      = $this->get_settings_for_display();
		$phone  = Customizer::get( 'phone' );
		$email  = Customizer::get( 'email' );
		$form   = 'divine-form-' . $this->get_id();
		?>
		<section class="section" id="booking">
			<div class="wrap booking-inner">
				<div class="booking-aside">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
						<p class="eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
					<?php endif; ?>
					<?php $this->heading( (string) $s['heading'], 'h2' ); ?>
					<?php if ( ! empty( $s['lede'] ) ) : ?>
						<p><?php echo esc_html( $s['lede'] ); ?></p>
					<?php endif; ?>

					<dl class="contact-block">
						<?php if ( Customizer::get( 'address' ) ) : ?>
							<div class="contact-item">
								<span class="ico"><?php divine_the_icon( 'pin' ); ?></span>
								<div>
									<dt><?php esc_html_e( 'The studio', 'divine-beauty' ); ?></dt>
									<dd><?php echo nl2br( esc_html( Customizer::get( 'address' ) ) ); ?></dd>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $phone ) : ?>
							<div class="contact-item">
								<span class="ico"><?php divine_the_icon( 'phone' ); ?></span>
								<div>
									<dt><?php esc_html_e( 'Call or WhatsApp', 'divine-beauty' ); ?></dt>
									<dd><a href="tel:<?php echo esc_attr( divine_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></dd>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $email ) : ?>
							<div class="contact-item">
								<span class="ico"><?php divine_the_icon( 'mail' ); ?></span>
								<div>
									<dt><?php esc_html_e( 'Email', 'divine-beauty' ); ?></dt>
									<dd><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></dd>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( Customizer::get( 'hours' ) ) : ?>
							<div class="contact-item">
								<span class="ico"><?php divine_the_icon( 'clock' ); ?></span>
								<div>
									<dt><?php esc_html_e( 'Studio hours', 'divine-beauty' ); ?></dt>
									<dd><?php echo nl2br( esc_html( Customizer::get( 'hours' ) ) ); ?></dd>
								</div>
							</div>
						<?php endif; ?>
					</dl>
				</div>

				<div class="form-card">
					<form id="booking-form" class="<?php echo esc_attr( $form ); ?>" novalidate>
						<div id="selected-look" class="chosen-look" hidden>
							<div>
								<span><?php esc_html_e( 'Inspiration from the portfolio', 'divine-beauty' ); ?></span>
								<strong></strong>
							</div>
							<button type="button" id="clear-look" aria-label="<?php esc_attr_e( 'Remove the chosen look', 'divine-beauty' ); ?>">&times;</button>
						</div>

						<div class="field-row">
							<p class="field">
								<label for="enq-name"><?php esc_html_e( 'Your name', 'divine-beauty' ); ?></label>
								<input type="text" id="enq-name" name="name" autocomplete="name" required>
							</p>
							<p class="field">
								<label for="enq-email"><?php esc_html_e( 'Email', 'divine-beauty' ); ?></label>
								<input type="email" id="enq-email" name="email" autocomplete="email" required>
							</p>
						</div>

						<div class="field-row">
							<p class="field">
								<label for="enq-phone"><?php esc_html_e( 'Phone (optional)', 'divine-beauty' ); ?></label>
								<input type="tel" id="enq-phone" name="phone" autocomplete="tel">
							</p>
							<p class="field">
								<label for="preferred-date"><?php esc_html_e( 'Preferred date (optional)', 'divine-beauty' ); ?></label>
								<input type="date" id="preferred-date" name="date">
							</p>
						</div>

						<p class="field">
							<label for="service-select"><?php esc_html_e( 'Treatment', 'divine-beauty' ); ?></label>
							<select name="service" id="service-select" required>
								<option value=""><?php esc_html_e( 'Select a treatment', 'divine-beauty' ); ?></option>
								<?php foreach ( (array) $s['services'] as $option ) : ?>
									<option><?php echo esc_html( $option['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</p>

						<p class="field">
							<label for="enq-message"><?php esc_html_e( 'Anything else? (optional)', 'divine-beauty' ); ?></label>
							<textarea id="enq-message" name="message" rows="4"></textarea>
						</p>

						<?php /* Hidden from people, irresistible to bots. */ ?>
						<p class="field field--trap" aria-hidden="true">
							<label for="enq-website"><?php esc_html_e( 'Leave this field empty', 'divine-beauty' ); ?></label>
							<input type="text" id="enq-website" name="website" tabindex="-1" autocomplete="off">
						</p>

						<button class="btn btn-gold btn-block" type="submit">
							<?php echo esc_html( $s['submit_text'] ); ?> <span aria-hidden="true">&#8599;</span>
						</button>

						<?php if ( ! empty( $s['consent_text'] ) ) : ?>
							<p class="form-note"><?php echo esc_html( $s['consent_text'] ); ?></p>
						<?php endif; ?>

						<p id="enquiry-result" class="form-result" role="status" tabindex="-1" hidden></p>
					</form>
				</div>
			</div>
		</section>
		<?php
	}
}
