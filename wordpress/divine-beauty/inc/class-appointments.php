<?php
/**
 * Appointments — the enquiries the website receives.
 *
 * Every submission is stored as a post of type `divine_appointment` before any
 * email goes out, so an enquiry is never lost to a mail server having a bad day.
 * The studio works through them in Appointments in the admin menu.
 *
 * @package DivineBeauty
 */

declare( strict_types = 1 );

namespace DivineBeauty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the appointment record and the screens for working through them.
 */
class Appointments {

	/**
	 * The post type that stores an enquiry.
	 */
	public const POST_TYPE = 'divine_appointment';

	/**
	 * Where an appointment is up to. Keys are stored; labels are shown.
	 *
	 * @var array<string,string>
	 */
	private const STATUSES = array(
		'new'       => 'New',
		'contacted' => 'Contacted',
		'confirmed' => 'Confirmed',
		'completed' => 'Completed',
		'cancelled' => 'Cancelled',
	);

	/**
	 * The fields captured from the booking form.
	 *
	 * @var array<string,string>
	 */
	private const FIELDS = array(
		'name'    => 'Name',
		'email'   => 'Email',
		'phone'   => 'Phone',
		'service' => 'Treatment',
		'date'    => 'Preferred date',
		'look'    => 'Inspiration',
		'message' => 'Message',
	);

	/**
	 * Hook into WordPress.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'column' ), 10, 2 );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( $this, 'sortable_columns' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_status' ), 10, 2 );

		add_action( 'restrict_manage_posts', array( $this, 'status_filter' ) );
		add_action( 'pre_get_posts', array( $this, 'apply_status_filter' ) );

		add_action( 'admin_post_divine_export_appointments', array( $this, 'export_csv' ) );
		add_action( 'admin_notices', array( $this, 'export_button' ) );

		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'new_count_bubble' ) );
		add_action( 'admin_head', array( $this, 'admin_css' ) );
	}

	/**
	 * The appointment record.
	 *
	 * Not publicly queryable: an enquiry contains a client's contact details and
	 * has no business being served as a page.
	 *
	 * @return void
	 */
	public function register_post_type(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'               => __( 'Appointments', 'divine-beauty' ),
					'singular_name'      => __( 'Appointment', 'divine-beauty' ),
					'menu_name'          => __( 'Appointments', 'divine-beauty' ),
					'all_items'          => __( 'All appointments', 'divine-beauty' ),
					'edit_item'          => __( 'Appointment', 'divine-beauty' ),
					'view_item'          => __( 'View appointment', 'divine-beauty' ),
					'search_items'       => __( 'Search appointments', 'divine-beauty' ),
					'not_found'          => __( 'No appointments yet.', 'divine-beauty' ),
					'not_found_in_trash' => __( 'No appointments in the bin.', 'divine-beauty' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'menu_icon'           => 'dashicons-calendar-alt',
				'menu_position'       => 26,
				'supports'            => array( 'title' ),
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'capabilities'        => array(
					// Enquiries arrive from the website; nobody types one by hand.
					'create_posts' => 'do_not_allow',
				),
			)
		);
	}

	/* ====================================================== storing ======= */

	/**
	 * Store an enquiry.
	 *
	 * @param array<string,string> $data Sanitised form values.
	 * @return int The new appointment ID, or 0 if it could not be stored.
	 */
	public static function store( array $data ): int {
		$title = sprintf(
			/* translators: 1: client name, 2: treatment. */
			__( '%1$s — %2$s', 'divine-beauty' ),
			$data['name'] ?? __( 'Unnamed', 'divine-beauty' ),
			$data['service'] ?? __( 'No treatment given', 'divine-beauty' )
		);

		$id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_title'  => $title,
				'post_status' => 'publish',
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			return 0;
		}

		foreach ( array_keys( self::FIELDS ) as $field ) {
			if ( isset( $data[ $field ] ) && '' !== $data[ $field ] ) {
				update_post_meta( $id, '_divine_' . $field, $data[ $field ] );
			}
		}

		update_post_meta( $id, '_divine_status', 'new' );
		update_post_meta( $id, '_divine_source', $data['source'] ?? home_url( '/' ) );

		/**
		 * Fires once an enquiry has been stored.
		 *
		 * @param int                  $id   Appointment ID.
		 * @param array<string,string> $data Submitted values.
		 */
		do_action( 'divine_appointment_stored', (int) $id, $data );

		return (int) $id;
	}

	/* ======================================================== list ======== */

	/**
	 * The columns the studio actually needs at a glance.
	 *
	 * @param array<string,string> $columns Default columns.
	 * @return array<string,string>
	 */
	public function columns( array $columns ): array {
		return array(
			'cb'              => $columns['cb'] ?? '',
			'divine_client'   => __( 'Client', 'divine-beauty' ),
			'divine_service'  => __( 'Treatment', 'divine-beauty' ),
			'divine_when'     => __( 'Preferred date', 'divine-beauty' ),
			'divine_contact'  => __( 'Contact', 'divine-beauty' ),
			'divine_status'   => __( 'Status', 'divine-beauty' ),
			'divine_received' => __( 'Received', 'divine-beauty' ),
		);
	}

	/**
	 * Render one cell.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Appointment ID.
	 * @return void
	 */
	public function column( string $column, int $post_id ): void {
		$get = static fn( string $k ): string => (string) get_post_meta( $post_id, '_divine_' . $k, true );

		switch ( $column ) {
			case 'divine_client':
				printf(
					'<strong><a href="%s">%s</a></strong>',
					esc_url( (string) get_edit_post_link( $post_id ) ),
					esc_html( $get( 'name' ) ?: __( '(no name)', 'divine-beauty' ) )
				);
				if ( $get( 'look' ) ) {
					printf( '<br><span class="divine-muted">%s</span>', esc_html( $get( 'look' ) ) );
				}
				break;

			case 'divine_service':
				echo esc_html( $get( 'service' ) ?: '—' );
				break;

			case 'divine_when':
				$date = $get( 'date' );
				echo $date
					? esc_html( mysql2date( (string) get_option( 'date_format' ), $date ) )
					: '<span class="divine-muted">' . esc_html__( 'No preference', 'divine-beauty' ) . '</span>';
				break;

			case 'divine_contact':
				if ( $get( 'email' ) ) {
					printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $get( 'email' ) ) );
				}
				if ( $get( 'phone' ) ) {
					printf(
						'%s<a href="tel:%s">%s</a>',
						$get( 'email' ) ? '<br>' : '',
						esc_attr( divine_tel( $get( 'phone' ) ) ),
						esc_html( $get( 'phone' ) )
					);
				}
				break;

			case 'divine_status':
				$status = $get( 'status' ) ?: 'new';
				printf(
					'<span class="divine-pill divine-pill--%s">%s</span>',
					esc_attr( $status ),
					esc_html( self::STATUSES[ $status ] ?? $status )
				);
				break;

			case 'divine_received':
				printf(
					'%s<br><span class="divine-muted">%s</span>',
					esc_html( (string) get_the_date( (string) get_option( 'date_format' ), $post_id ) ),
					esc_html( (string) get_the_time( (string) get_option( 'time_format' ), $post_id ) )
				);
				break;
		}
	}

	/**
	 * Let the studio sort by treatment and by when the enquiry arrived.
	 *
	 * @param array<string,string> $columns Sortable columns.
	 * @return array<string,string>
	 */
	public function sortable_columns( array $columns ): array {
		$columns['divine_received'] = 'date';
		$columns['divine_service']  = 'divine_service';
		return $columns;
	}

	/* ====================================================== single ======== */

	/**
	 * Add the detail and status panels.
	 *
	 * @return void
	 */
	public function add_meta_boxes(): void {
		add_meta_box(
			'divine-appointment-detail',
			__( 'Enquiry', 'divine-beauty' ),
			array( $this, 'render_detail' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
		add_meta_box(
			'divine-appointment-status',
			__( 'Status', 'divine-beauty' ),
			array( $this, 'render_status' ),
			self::POST_TYPE,
			'side',
			'high'
		);
	}

	/**
	 * Everything the client sent, read-only.
	 *
	 * @param \WP_Post $post Appointment.
	 * @return void
	 */
	public function render_detail( \WP_Post $post ): void {
		echo '<table class="divine-detail widefat striped">';

		foreach ( self::FIELDS as $field => $label ) {
			$value = (string) get_post_meta( $post->ID, '_divine_' . $field, true );
			if ( '' === $value ) {
				continue;
			}

			echo '<tr><th scope="row">' . esc_html__( $label, 'divine-beauty' ) . '</th><td>'; // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- Theme-owned labels.

			if ( 'email' === $field ) {
				printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $value ) );
			} elseif ( 'phone' === $field ) {
				printf( '<a href="tel:%s">%s</a>', esc_attr( divine_tel( $value ) ), esc_html( $value ) );
			} elseif ( 'date' === $field ) {
				echo esc_html( mysql2date( (string) get_option( 'date_format' ), $value ) );
			} elseif ( 'message' === $field ) {
				echo nl2br( esc_html( $value ) );
			} else {
				echo esc_html( $value );
			}

			echo '</td></tr>';
		}

		printf(
			'<tr><th scope="row">%s</th><td>%s</td></tr>',
			esc_html__( 'Received', 'divine-beauty' ),
			esc_html( (string) get_the_date( 'j F Y, g:ia', $post ) )
		);

		$source = (string) get_post_meta( $post->ID, '_divine_source', true );
		if ( $source ) {
			printf(
				'<tr><th scope="row">%s</th><td><a href="%s">%s</a></td></tr>',
				esc_html__( 'Sent from', 'divine-beauty' ),
				esc_url( $source ),
				esc_html( $source )
			);
		}

		echo '</table>';

		$email   = (string) get_post_meta( $post->ID, '_divine_email', true );
		$service = (string) get_post_meta( $post->ID, '_divine_service', true );
		if ( $email ) {
			printf(
				'<p style="margin-top:14px"><a class="button button-primary" href="mailto:%s?subject=%s">%s</a></p>',
				esc_attr( $email ),
				esc_attr( rawurlencode( sprintf( __( 'Your appointment enquiry — %s', 'divine-beauty' ), $service ) ) ),
				esc_html__( 'Reply to this client', 'divine-beauty' )
			);
		}
	}

	/**
	 * The status selector.
	 *
	 * @param \WP_Post $post Appointment.
	 * @return void
	 */
	public function render_status( \WP_Post $post ): void {
		$current = (string) get_post_meta( $post->ID, '_divine_status', true ) ?: 'new';
		wp_nonce_field( 'divine_save_status', 'divine_status_nonce' );

		echo '<select name="divine_status" style="width:100%">';
		foreach ( self::STATUSES as $key => $label ) {
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $key ),
				selected( $current, $key, false ),
				esc_html__( $label, 'divine-beauty' ) // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- Theme-owned labels.
			);
		}
		echo '</select>';
		printf(
			'<p class="description">%s</p>',
			esc_html__( 'Press Update to save. This is for your own tracking — the client is not notified.', 'divine-beauty' )
		);
	}

	/**
	 * Persist the status.
	 *
	 * @param int      $post_id Appointment ID.
	 * @param \WP_Post $post    Appointment.
	 * @return void
	 */
	public function save_status( int $post_id, \WP_Post $post ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$nonce = isset( $_POST['divine_status_nonce'] )
			? sanitize_text_field( wp_unslash( $_POST['divine_status_nonce'] ) )
			: '';
		if ( ! wp_verify_nonce( $nonce, 'divine_save_status' ) ) {
			return;
		}

		$status = isset( $_POST['divine_status'] )
			? sanitize_key( wp_unslash( $_POST['divine_status'] ) )
			: 'new';

		if ( isset( self::STATUSES[ $status ] ) ) {
			update_post_meta( $post_id, '_divine_status', $status );
		}
	}

	/* ====================================================== filters ======= */

	/**
	 * A status dropdown above the list.
	 *
	 * @param string $post_type Current post type.
	 * @return void
	 */
	public function status_filter( string $post_type ): void {
		if ( self::POST_TYPE !== $post_type ) {
			return;
		}

		$current = isset( $_GET['divine_status'] ) ? sanitize_key( wp_unslash( $_GET['divine_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only list filter.

		echo '<select name="divine_status">';
		printf( '<option value="">%s</option>', esc_html__( 'All statuses', 'divine-beauty' ) );
		foreach ( self::STATUSES as $key => $label ) {
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $key ),
				selected( $current, $key, false ),
				esc_html__( $label, 'divine-beauty' ) // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- Theme-owned labels.
			);
		}
		echo '</select>';
	}

	/**
	 * Apply the status filter and the treatment sort.
	 *
	 * @param \WP_Query $query Current query.
	 * @return void
	 */
	public function apply_status_filter( \WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() || self::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}

		$status = isset( $_GET['divine_status'] ) ? sanitize_key( wp_unslash( $_GET['divine_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only list filter.
		if ( $status && isset( self::STATUSES[ $status ] ) ) {
			$query->set(
				'meta_query',
				array(
					array(
						'key'   => '_divine_status',
						'value' => $status,
					),
				)
			);
		}

		if ( 'divine_service' === $query->get( 'orderby' ) ) {
			$query->set( 'meta_key', '_divine_service' );
			$query->set( 'orderby', 'meta_value' );
		}
	}

	/* ======================================================= export ======= */

	/**
	 * An export button above the list.
	 *
	 * @return void
	 */
	public function export_button(): void {
		$screen = get_current_screen();
		if ( ! $screen || 'edit-' . self::POST_TYPE !== $screen->id ) {
			return;
		}

		printf(
			'<div class="divine-export"><a class="button" href="%s">%s</a></div>',
			esc_url(
				wp_nonce_url(
					admin_url( 'admin-post.php?action=divine_export_appointments' ),
					'divine_export_appointments'
				)
			),
			esc_html__( 'Export all appointments (CSV)', 'divine-beauty' )
		);
	}

	/**
	 * Stream every appointment as a spreadsheet.
	 *
	 * @return void
	 */
	public function export_csv(): void {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to export appointments.', 'divine-beauty' ) );
		}
		check_admin_referer( 'divine_export_appointments' );

		$rows = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=divine-appointments-' . gmdate( 'Y-m-d' ) . '.csv' );

		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array_merge( array( 'Received' ), array_values( self::FIELDS ), array( 'Status' ) ) );

		foreach ( $rows as $row ) {
			$line = array( get_the_date( 'Y-m-d H:i', $row ) );
			foreach ( array_keys( self::FIELDS ) as $field ) {
				$line[] = (string) get_post_meta( $row->ID, '_divine_' . $field, true );
			}
			$line[] = (string) get_post_meta( $row->ID, '_divine_status', true ) ?: 'new';
			fputcsv( $out, $line );
		}

		fclose( $out );
		exit;
	}

	/* ========================================================= chrome ===== */

	/**
	 * "View" makes no sense for a record that is not public.
	 *
	 * @param array<string,string> $actions Row actions.
	 * @param \WP_Post             $post    Appointment.
	 * @return array<string,string>
	 */
	public function row_actions( array $actions, \WP_Post $post ): array {
		if ( self::POST_TYPE === $post->post_type ) {
			unset( $actions['view'], $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Show how many new enquiries are waiting, next to the menu item.
	 *
	 * @return void
	 */
	public function new_count_bubble(): void {
		global $menu;

		$new = new \WP_Query(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => false,
				'meta_query'     => array(
					array(
						'key'   => '_divine_status',
						'value' => 'new',
					),
				),
			)
		);

		$count = (int) $new->found_posts;
		if ( ! $count ) {
			return;
		}

		$slug = 'edit.php?post_type=' . self::POST_TYPE;
		foreach ( $menu as $i => $item ) {
			if ( isset( $item[2] ) && $slug === $item[2] ) {
				$menu[ $i ][0] .= sprintf( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Standard way to add a count bubble.
					' <span class="awaiting-mod"><span class="pending-count">%d</span></span>',
					$count
				);
				break;
			}
		}
	}

	/**
	 * A little styling for the status pills and the detail table.
	 *
	 * @return void
	 */
	public function admin_css(): void {
		$screen = get_current_screen();
		if ( ! $screen || ! str_contains( (string) $screen->id, self::POST_TYPE ) ) {
			return;
		}
		?>
		<style>
			.divine-muted { color: #646970; font-size: 12px; }
			.divine-pill {
				display: inline-block; padding: 2px 10px; border-radius: 999px;
				font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em;
			}
			.divine-pill--new       { background: #e5f3ff; color: #0a4b78; }
			.divine-pill--contacted { background: #fcf3d8; color: #7a5c00; }
			.divine-pill--confirmed { background: #e4f5e7; color: #14562a; }
			.divine-pill--completed { background: #ededed; color: #50575e; }
			.divine-pill--cancelled { background: #fbeaea; color: #8a1f1f; }
			.divine-detail th { width: 170px; text-align: left; }
			.divine-detail td, .divine-detail th { padding: 10px 12px; vertical-align: top; }
			.divine-export { margin: 12px 0 0; }
			/* An appointment has one status — the studio's. WordPress's own
			   post status and visibility rows only invite confusion. */
			.post-type-divine_appointment #minor-publishing-actions,
			.post-type-divine_appointment #visibility,
			.post-type-divine_appointment .misc-pub-post-status,
			.post-type-divine_appointment #misc-publishing-actions .misc-pub-curtime { display: none; }
		</style>
		<?php
	}
}
