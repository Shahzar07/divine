<?php
/**
 * Appointment enquiry handling.
 *
 * The static site could only hand the visitor a mailto: draft. On WordPress the
 * enquiry is posted properly: validated, rate-limited and emailed to the studio,
 * so nothing depends on the visitor having an email client configured.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Receives and emails appointment enquiries.
 */
class Enquiry {

	/**
	 * Most enquiries a single visitor may send in an hour.
	 */
	private const HOURLY_LIMIT = 5;

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		add_action( 'wp_ajax_divine_enquiry', array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_divine_enquiry', array( $this, 'handle' ) );
	}

	/**
	 * Validate and send an enquiry.
	 *
	 * @return void Always ends in a JSON response.
	 */
	public function handle(): void {
		if ( ! check_ajax_referer( 'divine_enquiry', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Your session expired — please reload the page and try again.', 'divine-beauty' ) ), 403 );
		}

		// Bots fill in every field they find; a real visitor never sees this one.
		if ( '' !== trim( (string) filter_input( INPUT_POST, 'website' ) ) ) {
			wp_send_json_success( array( 'message' => __( 'Thank you — your enquiry is on its way.', 'divine-beauty' ) ) );
		}

		if ( $this->is_rate_limited() ) {
			wp_send_json_error( array( 'message' => __( 'That is a few enquiries in a short time. Please call or WhatsApp the studio instead.', 'divine-beauty' ) ), 429 );
		}

		$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
		$service = sanitize_text_field( wp_unslash( $_POST['service'] ?? '' ) );
		$date    = sanitize_text_field( wp_unslash( $_POST['date'] ?? '' ) );
		$look    = sanitize_text_field( wp_unslash( $_POST['look'] ?? '' ) );
		$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

		if ( '' === $name || '' === $service ) {
			wp_send_json_error( array( 'message' => __( 'Please add your name and choose a treatment.', 'divine-beauty' ) ), 400 );
		}
		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Please check your email address so Dee can reply.', 'divine-beauty' ) ), 400 );
		}

		/*
		 * Store first, send second. Mail can fail for reasons that have nothing
		 * to do with the visitor — a host with no mailer, a provider throttling
		 * — and an enquiry that only ever existed as an email is an enquiry the
		 * studio loses. The record in Appointments is the source of truth.
		 */
		$appointment = Appointments::store(
			array(
				'name'    => $name,
				'email'   => $email,
				'phone'   => $phone,
				'service' => $service,
				'date'    => $date,
				'look'    => $look,
				'message' => $message,
				'source'  => wp_get_referer() ?: home_url( '/' ),
			)
		);

		$to = Customizer::get( 'email' );
		if ( ! is_email( $to ) ) {
			$to = get_option( 'admin_email' );
		}

		$lines = array(
			__( 'A new appointment enquiry from the website.', 'divine-beauty' ),
			'',
			sprintf( '%s: %s', __( 'Name', 'divine-beauty' ), $name ),
			sprintf( '%s: %s', __( 'Email', 'divine-beauty' ), $email ),
		);
		if ( '' !== $phone ) {
			$lines[] = sprintf( '%s: %s', __( 'Phone', 'divine-beauty' ), $phone );
		}
		$lines[] = sprintf( '%s: %s', __( 'Treatment', 'divine-beauty' ), $service );
		if ( '' !== $date ) {
			$lines[] = sprintf( '%s: %s', __( 'Preferred date', 'divine-beauty' ), $date );
		}
		if ( '' !== $look ) {
			$lines[] = sprintf( '%s: %s', __( 'Inspiration from the portfolio', 'divine-beauty' ), $look );
		}
		if ( '' !== $message ) {
			$lines[] = '';
			$lines[] = $message;
		}
		if ( $appointment ) {
			$lines[] = '';
			$lines[] = __( 'Open it in your dashboard:', 'divine-beauty' );
			$lines[] = (string) get_edit_post_link( $appointment, 'raw' );
		}

		$sent = wp_mail(
			$to,
			sprintf(
				/* translators: %s: the treatment the visitor chose. */
				__( 'Appointment enquiry · %s', 'divine-beauty' ),
				$service
			),
			implode( "\n", $lines ),
			array(
				'Content-Type: text/plain; charset=UTF-8',
				// Send from the site's own domain so SPF passes; replies go to the visitor.
				sprintf( 'Reply-To: %s <%s>', $name, $email ),
			)
		);

		if ( ! $sent && ! $appointment ) {
			wp_send_json_error(
				array(
					'message' => __( 'The enquiry could not be sent just now. Please call or WhatsApp the studio.', 'divine-beauty' ),
				),
				500
			);
		}

		if ( ! $sent ) {
			// It is saved and will be seen; only the notification failed.
			update_post_meta( $appointment, '_divine_mail_failed', '1' );
		}

		$this->record_send();

		wp_send_json_success(
			array(
				'message' => __( 'Thank you — your enquiry is with Dee. She will come back to you with pricing and availability.', 'divine-beauty' ),
			)
		);
	}

	/**
	 * Whether this visitor has already sent the hourly maximum.
	 *
	 * @return bool
	 */
	private function is_rate_limited(): bool {
		return (int) get_transient( $this->rate_key() ) >= self::HOURLY_LIMIT;
	}

	/**
	 * Count one successful send against this visitor's hourly allowance.
	 *
	 * @return void
	 */
	private function record_send(): void {
		$key = $this->rate_key();
		set_transient( $key, (int) get_transient( $key ) + 1, HOUR_IN_SECONDS );
	}

	/**
	 * A per-visitor transient key.
	 *
	 * @return string
	 */
	private function rate_key(): string {
		$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );

		return 'divine_enq_' . md5( $ip . wp_salt() );
	}
}
