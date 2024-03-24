<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_filter( 'acf/fields/google_map/api', 'my_acf_google_map_api' );

/**
 * Google Maps API Key
 *
 * @param  string $api Google Maps API key.
 * @return string
 */
function my_acf_google_map_api( $api ){
	$api['key'] = 'AIzaSyD0VUb4oRkeOCx2_ep2bwA0fTVO0omS4wA';
	return $api;
}

add_shortcode( 'ncp-map-session', 'fgrweb_ncp_map_session' );
/**
 * NCP Map Session
 *
 * @return string
 */
function fgrweb_ncp_map_session() {
	$return_html = '';
	// Group.
	$group = get_field( 'venue_session' );
	if ( $group ) {
		// Acf google maps field.
		$map = $group['direction_session'];
		// Map address.
		$address = $map['address'];
		// Map city and Country.
		$city_country = $map['city'] . ', ' . $map['country'];
		// $return_html .= '<div class="ncp-map-session">';
		// $return_html .= '<div class="ncp-map-session__address">' . $address . '</div>';
		// $return_html .= '<div class="ncp-map-session__city-country">' . $city_country . '</div>';
		// $return_html .= '</div>';
		// The map.
		$return_html .= '<div class="ncp-map-session__map">';
		$return_html .= '<div class="acf-map" data-zoom="16">
				<div class="marker" data-lat="' . $map['lat'] . '" data-lng="' . $map['lng'] . '"><strong>' . $address . '</strong><p><a href="https://maps.google.com/?q=' . $map['lat'] . ',' . $map['lng'] . '" target="_blank">Open in Google Maps</a></p>
				</div>
			</div>';
		$return_html .= '</div>';
	}
	return $return_html;
}

add_shortcode( 'ncp-location-address', 'fgrweb_ncp_location_address' );
/**
 * NCP Location Address
 *
 * @return string
 */
function fgrweb_ncp_location_address() {
	$return_html = '';
	// Group.
	$group = get_field( 'venue_session' );
	if ( $group ) {
		// Acf google maps field.
		$map = $group['direction_session'];
		// Map address.
		$address      = $map['street_name'] . ', ' . $map['street_number'] . ', ' . $map['post_code'];
		$city_country = $map['city'] . ', ' . $map['country'];
		$return_html .= '<div class="ncp-location-session-header__address">' . $address . '<br/>' . $city_country . '</div>';
	}
	return $return_html;
}

add_shortcode( 'ncp-location-session', 'fgrweb_ncp_location_session' );
/**
 * NCP Location Session
 *
 * @return string
 */
function fgrweb_ncp_location_session() {
	$return_html = '';
	// Group.
	$group = get_field( 'venue_session' );
	if ( $group ) {
		// Acf google maps field.
		$map = $group['direction_session'];
		// Map address.
		$city_country = $map['city'] . ', ' . $map['country'];
		$return_html .= '<div class="ncp-location-session-header__city-country">' . $city_country . '</div>';
		$return_html .= '</div>';
	}
	return $return_html;
}

add_action( 'login_head', 'custom_login_css', 99999 );

/**
 * Custom Login css
 *
 * @return void
 */
function custom_login_css() {
	?>
	<style type="text/css">
	h1 a {
		background: url(<?php echo plugin_dir_url( __FILE__ ); ?>/logo.png) no-repeat top center!important;
		width: 131px!important;
		height: 138px!important;
	}
	</style>
	<?php
}
add_action( 'login_form_lostpassword', 'fgrweb_custom_lost_password' );
/**
 * Custom Lost Password
 *
 * @return void
 */
function fgrweb_custom_lost_password() {
	if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
		if ( is_user_logged_in() ) {
			$this->redirect_logged_in_user();
			exit;
		}
		wp_safe_redirect( home_url( 'sign-in?lost=1' ) );
		exit;
	}

}

add_shortcode( 'custom-password-lost-form', 'fgrweb_password_lost_form' );
/**
 * A shortcode for rendering the form used to initiate the password reset.
 *
 * @param array  $attributes Shortcode attributes.
 * @param string $content The text content for shortcode. Not used.
 *
 * @return string The shortcode output
 *
 */
function fgrweb_password_lost_form( $attributes, $content = null ) {
	// Parse shortcode attributes.
	$default_attributes = array( 'show_title' => false );
	$attributes         = shortcode_atts( $default_attributes, $attributes );
	if ( is_user_logged_in() ) {
		return __( 'You are already signed in.', 'personalize-login' );
	} else {
		?>
		<div id="password-lost-form" class="ncp-loggin-form">
			<?php if ( $attributes['show_title'] ) : ?>
				<h3><?php _e( 'Forgot Your Password?', 'personalize-login' ); ?></h3>
			<?php endif; ?>
			<p>
				<?php
					esc_attr_e( 'Enter your email address and we will send you a link you can use to pick a new password.', 'ncp' );
				?>
			</p>

			<form id="lostpasswordform" action="<?php echo wp_lostpassword_url( home_url() ); ?>" method="post">
				<fieldset>
					<label for="user_login"><?php esc_attr_e( 'Email', 'ncp' ); ?>
						<input type="text" name="user_login" id="user_login">
				</fieldset>
				<input type="submit" name="submit" class="ncp-button-primary" value="<?php esc_attr_e( 'Reset Password', 'ncp' ); ?>"/>
			</form>
		</div>
		<?php
	}
}

add_action( 'login_form_lostpassword', 'do_password_lost' );
/**
 * Initiates password reset.
 **/

function do_password_lost() {
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		$errors = retrieve_password();
		if ( is_wp_error( $errors ) ) {
			// Errors found
			$redirect_url = home_url( 'sign-in?lost=1' );
			$redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
		} else {
			// Email sent
			$redirect_url = home_url( 'sign-in?lost=1' );
			$redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
		}
		wp_safe_redirect( $redirect_url );
		exit;
	}
}

/**
 * URL parameter for dynamic conditional.
 *
 * @return string
 */
function fgrweb_currenturl_callback() {
	global $wp;
	$current_url = home_url( add_query_arg( $_GET, $wp->request ) );
	return $current_url;
}

// Get entry after forms submission. Fluent Forms.
add_action( 'fluentform/submission_inserted', 'fgrweb_fluentform_after_submission', 20, 3 );
/**
 * Get entry after forms submission.
 *
 * @param integer $entryId The submission id.
 * @param array   $formData The form data.
 * @param object  $form The form object.
 *
 * @return void
 */
function fgrweb_fluentform_after_submission( $entryId, $formData, $form ) {
	if ( 4 != $form->id ) {
		return;
	}
	// Get the form data.
	$name         = $formData['name'];
	$surname      = $formData['surname'];
	$organisation = $formData['organisation'];
	$position     = $formData['position'];
	$email        = $formData['email'];
	$message      = $formData['message'];
	// Save record in ncp_users table.
	global $wpdb;
	$prefix = $wpdb->prefix;
	$ncp_users = $prefix . 'ncp_users';
	$wpdb->insert(
		$ncp_users,
		array(
			'form_entry_id' => $entryId,
			'name'          => $name,
			'surname'       => $surname,
			'organisation'  => $organisation,
			'position'      => $position,
			'email'         => $email,
			'message'       => $message,
			'status'        => 'pending',
		)
	);

}


/**
 * Encrypt text with AES-128-CTR.
 *
 * @param  string $text Text to encrypt.
 * @return string
 */
function fgrweb_encrypt_string( $text ) {
	$ciphering = 'AES-128-CTR';
	$iv_length = openssl_cipher_iv_length( $ciphering );
	$options   = 0;
	// Non-NULL Initialization Vector for encryption.
	$encryption_iv  = '1234567891011121';
	$encryption_key = 'Fgr';
	$encryption     = openssl_encrypt( $text, $ciphering, $encryption_key, $options, $encryption_iv );
	return $encryption;
}

/**
 * Decrypt text with AES-128-CTR.
 *
 * @param  string $text Text to descrypt.
 * @return string
 */
function fgrweb_decrypt_string( $text ) {
	$ciphering = 'AES-128-CTR';
	$iv_length = openssl_cipher_iv_length( $ciphering );
	$options   = 0;
	// Non-NULL Initialization Vector for encryption.
	$decryption_iv  = '1234567891011121';
	$decryption_key = 'Fgr';
	$decryption     = openssl_decrypt( $text, $ciphering, $decryption_key, $options, $decryption_iv );
	return $decryption;
}

add_shortcode( 'ncp-register-form', 'fgrweb_register_form' );
/**
 * Register Form
 *
 * @return string
 */
function fgrweb_register_form() {
	ob_start();
	$return_html = '';
	if ( isset( $_GET['request'] ) && ! empty( $_GET['request'] ) ) {
		$request = fgrweb_decrypt_string( $_GET['request'] );
		global $wpdb;
		$prefix    = $wpdb->prefix;
		$ncp_users = $prefix . 'ncp_users';
		$user      = $wpdb->get_row( "SELECT id FROM $ncp_users WHERE id = $request" );
		if ( $user ) {
			$return_html .= do_shortcode( '[fluentform id="5"]' );
		}
	}
	ob_end_clean();
	return $return_html;
}

// Rendering data in register form.
add_filter( 'fluentform/rendering_field_data_input_text', 'fgrweb_register_rendering_field_data_input_text', 10, 2 );
add_filter( 'fluentform/rendering_field_data_input_email', 'fgrweb_register_rendering_field_data_input_email', 10, 2 );

/**
 * Rendering data in register form.
 *
 * @param  array  $data The field data.
 * @param  object $form The form object.
 * @return array
 */
function fgrweb_register_rendering_field_data_input_text( $data, $form ) {
	if ( 5 != $form->id ) {
		return $data;
	}
	if ( isset( $_GET['request'] ) && ! empty( $_GET['request'] ) ) {
		$request = fgrweb_decrypt_string( $_GET['request'] );
		global $wpdb;
		$prefix    = $wpdb->prefix;
		$ncp_users = $prefix . 'ncp_users';
		$user      = $wpdb->get_row( "SELECT * FROM $ncp_users WHERE id = $request" );
		if ( $user ) {
			// First Name.
			if ( \FluentForm\Framework\Helpers\ArrayHelper::get( $data, 'attributes.name' ) === 'firstname' ) {
				$data['attributes']['value'] = $user->name;
			}
			// Surname.
			if ( \FluentForm\Framework\Helpers\ArrayHelper::get( $data, 'attributes.name' ) === 'surname' ) {
				$data['attributes']['value'] = $user->surname;
			}
			// Position.
			if ( \FluentForm\Framework\Helpers\ArrayHelper::get( $data, 'attributes.name' ) === 'position' ) {
				$data['attributes']['value'] = $user->position;
			}
			// Organisation.
			if ( \FluentForm\Framework\Helpers\ArrayHelper::get( $data, 'attributes.name' ) === 'organisation' ) {
				$data['attributes']['value'] = $user->organisation;
			}
		}
	}
	return $data;
}

/**
 * Rendering data in register form.
 *
 * @param  array  $data The field data.
 * @param  object $form The form object.
 * @return array
 */
function fgrweb_register_rendering_field_data_input_email( $data, $form ) {
	if ( 5 != $form->id ) {
		return $data;
	}
	if ( isset( $_GET['request'] ) && ! empty( $_GET['request'] ) ) {
		$request = fgrweb_decrypt_string( $_GET['request'] );
		global $wpdb;
		$prefix    = $wpdb->prefix;
		$ncp_users = $prefix . 'ncp_users';
		$user      = $wpdb->get_row( "SELECT email FROM $ncp_users WHERE id = $request" );
		if ( $user ) {
			// Email.
			if ( \FluentForm\Framework\Helpers\ArrayHelper::get( $data, 'attributes.name' ) === 'email' ) {
				$data['attributes']['value'] = $user->email;
			}
		}
	}
	return $data;
}

// Redirect subscribers users after loggin.
add_filter( 'login_redirect', 'fgrweb_login_redirect', 10, 3 );
/**
 * Redirect subscribers users after loggin.
 *
 * @param  string $redirect_to The redirect url.
 * @param  string $request The request url.
 * @param  object $user The user object.
 * @return string
 */
function fgrweb_login_redirect( $redirect_to, $request, $user ) {
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		if ( in_array( 'subscriber', $user->roles ) ) {
			return home_url();
		}
	}
	return $redirect_to;
}

// Hide admin bar to subscribers users.
add_filter( 'show_admin_bar', 'fgrweb_hide_admin_bar', 10, 1 );
/**
 * Hide admin bar to subscribers users.
 *
 * @param  boolean $show Show admin bar.
 * @return boolean
 */
function fgrweb_hide_admin_bar( $show ) {
	if ( current_user_can( 'subscriber' ) ) {
		return false;
	}
	return $show;
}

add_filter( 'get_search_form', 'custom_search_form' );
/**
 * Custom search form.
 *
 * @param  string $form The form.
 * @return string
 */
function custom_search_form( $form ) {
	if ( is_archive( 'resources' ) ) {
		$type = 'resources';
	} else {
		$page_id = get_the_ID();
		switch ( $page_id ) {
			case '155':
				$type = 'sessions';
				break;
			default:
				$type = '';
				break;
		}
	}
	// Search page.
	if ( is_search() || is_page( 192 ) ) { // 192 is the search page.
		if ( isset( $_GET['type'] ) && ! empty( $_GET['type'] ) ) {
			$type = esc_attr( $_GET['type'] );
		} else {
			$type = 'none';
		}
		$form  = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
			<label for="type" class="ncp-h5 ncp-search-option-label">' . __( 'Select type of search', 'ncp' ) . '</label>
			<fieldset>';
		$form .= '<input type="radio" name="type" id="type_session" value="sessions" ';
		if ( 'sessions' === $type ) {
			$form .= ' checked="checked" >';
		} else {
			$form .= ' >';
		}
		$form .= '<span class="npc-search-type__name" id="type_session">' . __( 'Sessions', 'ncp' ) . '</span>';
		$form .= '<input type="radio" name="type" id="type_resources" value="resources" ';
		if ( 'resources' === $type ) {
			$form .= ' checked="checked" >';
		} else {
			$form .= ' >';
		}
		$form .= '<span class="npc-search-type__name" id="type_resources">' . __( 'Resources', 'ncp' ) . '</span>';
		$form .= '</fieldset>
		<div><label class="screen-reader-text" for="s">' . __( 'Search for:' ) . '</label>
		<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="Search..." />
		<input type="submit" id="searchsubmit" class="ncp-button-primary" value="' . esc_attr__('Search') . '" />
		</div>
		</form>';
	} else {
		$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
		<div><label class="screen-reader-text" for="s">' . __( 'Search for:' ) . '</label>
		<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="Search..." />
		<input type="hidden" name="type" value="' . $type . '">
		<input type="submit" id="searchsubmit" class="ncp-button-primary" value="' . esc_attr__('Search') . '" />
		</div>
		</form>';
	}
	return $form;
}

// Oxygen conditions.
add_action( 'init', 'fgrweb_oxygen_conditions' );
/**
 * Oxygen conditions.
 *
 * @return void
 */
function fgrweb_oxygen_conditions() {
	if ( function_exists( 'oxygen_vsb_register_condition' ) ) {
		oxygen_vsb_register_condition(
			// Condition Name.
			'Search type parameter',
			// Values.
			array(
				'options' => array(),
				'custom'  => true,
			),
			// Operators.
			array( '==', '!=' ),
			// Callback Function.
			'fgrweb_oxygen_conditions_get_parameter',
			// Condition Category.
			'NCP'
		);
		oxygen_vsb_register_condition(
			// Condition Name.
			'Participants in session',
			// Values.
			array(
				'options' => array( 'true', 'false' ),
				'custom'  => false,
			),
			// Operators.
			array( '==', '!=' ),
			// Callback Function.
			'fgrweb_oxygen_conditions_participants_in_session',
			// Condition Category.
			'NCP'
		);
		oxygen_vsb_register_condition(
			// Condition Name.
			'Preparatory documents in session',
			// Values.
			array(
				'options' => array( 'true', 'false' ),
				'custom'  => false,
			),
			// Operators.
			array( '==', '!=' ),
			// Callback Function.
			'fgrweb_oxygen_conditions_preparatory_in_session',
			// Condition Category.
			'NCP'
		);
		oxygen_vsb_register_condition(
			// Condition Name.
			'Meeting Notes in session',
			// Values.
			array(
				'options' => array( 'true', 'false' ),
				'custom'  => false,
			),
			// Operators.
			array( '==', '!=' ),
			// Callback Function.
			'fgrweb_oxygen_conditions_meeting_notes_in_session',
			// Condition Category.
			'NCP'
		);
	}
}

/**
 * Get parameter.
 *
 * @param  string $value The value.
 * @param  string $operator The operator to use.
 * @return boolean
 */
function fgrweb_oxygen_conditions_get_parameter( $value, $operator ) {
	global $OxygenConditions;
	if ( isset( $_GET[ 'type' ] ) && ! empty( $_GET[ 'type' ] ) && ( $value === $_GET[ 'type' ] ) ) {
		return ( '==' === $operator ) ? true : false;
	} else {
		return ( '!=' === $operator ) ? true : false;
	}
}

/**
 * Participants in session.
 *
 * @param  string $value The value.
 * @param  string $operator The operator to use.
 * @return boolean
 */
function fgrweb_oxygen_conditions_participants_in_session( $value, $operator ) {
	global $OxygenConditions;
	$session_id   = get_the_ID();
	$participants = get_post_meta( $session_id, 'participants_session', true );
	if ( ! empty( $participants ) && ( 'true' === $value ) ) {
		return ( '==' === $operator ) ? true : false;
	} else {
		return ( '!=' === $operator ) ? true : false;
	}
}

/**
 * Meeting notes in session.
 *
 * @param  mixed $value
 * @param  mixed $operator
 * @return void
 */
function fgrweb_oxygen_conditions_meeting_notes_in_session( $value, $operator ) {
	global $OxygenConditions;
	$session_id = get_the_ID();
	$documents  = get_post_meta( $session_id, 'notes_session', true );
	if ( ! empty( $documents ) && ( 'true' === $value ) ) {
		return ( '==' === $operator ) ? true : false;
	} else {
		return ( '!=' === $operator ) ? true : false;
	}
}

/**
 * Preparatory documents in session.
 *
 * @param  string $value The value.
 * @param  string $operator The operator to use.
 * @return boolean
 */
function fgrweb_oxygen_conditions_preparatory_in_session( $value, $operator ) {
	global $OxygenConditions;
	$session_id = get_the_ID();
	$documents  = get_post_meta( $session_id, 'preparatory_session', true );
	if ( ! empty( $documents ) && ( 'true' === $value ) ) {
		return ( '==' === $operator ) ? true : false;
	} else {
		return ( '!=' === $operator ) ? true : false;
	}
}

add_action( 'pre_get_posts', 'fgrweb_sessions_query' );

/**
 * Modify Queries.
 *
 * @param  object $query The query object.
 * @return void
 */
function fgrweb_sessions_query( $query ) {
	if ( ! is_admin() && $query->is_post_type_archive( 'sessions' ) ) {
		$query->set( 'meta_key', 'date_session' );
		$query->set(
			'meta_query',
			array(
				'relation' => 'AND',
				array(
					'key'     => 'date_session',
					'value'   => date( 'Ymd' ),
					'compare' => '<',
					'type'    => 'DATE',
				),
			),
		);
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'DESC' );
	} elseif ( ! is_admin() && $query->is_post_type_archive( 'resources' ) ) {
		if ( ! is_user_logged_in() ) {
			$tax_query = array(
				array(
						'taxonomy' => 'clasifications',
						'field'    => 'slug',
						'terms'    => 'confidential',
						'operator' => 'NOT IN',
				),
			);
			$query->set( 'tax_query', $tax_query );
		}
	}
}

// Facet fselect hide count.
add_filter( 'facetwp_facet_dropdown_show_counts', '__return_false' );

/**
 * Get resource type.
 *
 * @return string
 */
function fgrweb_get_resource_type() {
	$terms = get_the_terms( get_the_ID(), 'document-types' );
	if ( ! empty( $terms ) ) {
		$term = array_shift( $terms );
		return $term->name;
	}
	return '';
}

/**
 * Get resource download link.
 *
 * @return string
 */
function fgrweb_get_resource_download_link() {
	$origin = get_post_meta( get_the_ID(), 'link_origin_resource', true );
	if ( 'media' === $origin ) {
		$attachment = get_post_meta( get_the_ID(), 'media_file_resource', true );
		$attachment_url = wp_get_attachment_url( $attachment );
		return $attachment_url;
	} else {
		$link = get_post_meta( get_the_ID(), 'download_resources', true );
		return $link;
	}
}

add_shortcode( 'ncp-resource-confidential', 'fgrweb_resource_confidential' );

/**
 * Resource confidential.
 *
 * @return string
 */
function fgrweb_resource_confidential() {
	$terms = get_the_terms( get_the_ID(), 'clasifications' );
	if ( ! empty( $terms ) ) {
		$term = array_shift( $terms );
		if ( 'confidential' === $term->slug ) {
			return '<div class="ncp-resource-confidential"><span class="ncp-resource-confidential__value">Confidential</span></div>';
		}
	}
	return '';

}



/**
 * Login form.
 */
/**
 * Start the session for error message handling.
 *
 * @return void
 */
function fgrweb_start_session() {
	if ( ! session_id() ) {
		session_start();
	}
}
add_action( 'init', 'fgrweb_start_session', 1 );

/**
 * Display custom login errors.
 *
 * @return void
 */
function fgrweb_show_error_messages() {
	if ( $codes = fgrweb_get_error_codes() ) {
		echo '<div class="ncp_login_error_messages">';
		foreach ( $codes as $code ) {
			$message = fgrweb_get_error_message( $code );
			echo '<span class="error"><strong>' . esc_html__( 'Error', 'ncp' ) . '</strong>: ' . esc_html( $message ) . '</span><br/>';
		}
		echo '</div>';
	}
}

/**
 * Get error codes from the session.
 *
 * @return array
 */
function fgrweb_get_error_codes() {
	return isset( $_SESSION['ncp_login_errors'] ) ? $_SESSION['ncp_login_errors'] : array();
}

/**
 * Get the corresponding error message for a code.
 *
 * @param  string $error_code The error code.
 * @return string
 */
function fgrweb_get_error_message( $error_code ) {
	switch ( $error_code ) {
		case 'empty_username':
			return __('The username field is empty.', 'ncp' );
		case 'empty_password':
			return __(  'The password field is empty.', 'ncp' );
		case 'invalid_username':
			return __( 'The username is incorrect.', 'ncp' );
		case 'incorrect_password':
			return __( 'The password is incorrect.', 'ncp' );
		default:
			return $error_code;//__( 'An error occurred.', 'ncp' );
	}
}

/**
 * Create the custom login form.
 *
 * @return string
 */
function fgrweb_custom_login_form() {
	if ( is_user_logged_in() ) {
		return '<p>' . esc_html__( 'You are already logged in.', 'ncp' ) . '</p>';
	}
	ob_start();
	fgrweb_show_error_messages();
	unset( $_SESSION['ncp_login_errors'] );
	?>
	<div class="ncp-loggin-form">
		<form method="post">
			<fieldset>
				<label for="user_login"><?php esc_attr_e( 'Username or email', 'ncp' ); ?></label>
				<input type="text" name="log" id="user_login" class="input" value="" placeholder="<?php esc_attr_e( 'Type your username or email', 'ncp' ); ?>" size="20"  />
			</fieldset>
			<fieldset>
				<label for="user_pass"><?php esc_attr_e( 'Password', 'ncp' ); ?></label>
				<input type="password" name="pwd" id="user_pass" class="input" value="" placeholder="<?php esc_attr_e( 'Type your password', 'ncp' ); ?>" size="20" />
			</fieldset>
			<input type="hidden" name="ncp_login_nonce" value="<?php echo esc_attr( wp_create_nonce( 'ncp-login-nonce' ) ); ?>"/>
			<?php do_action( 'login_form' ); ?>
			<div class="ncp-loggin-form__remember">
				<label for="rememberme">
					<input name="rememberme" type="checkbox" id="rememberme" value="forever" />
					<?php esc_attr_e( 'Remember me', 'ncp' ); ?>
				</label>
				<a href="<?php echo esc_url( wp_lostpassword_url(get_permalink()) ); ?>" title="<?php esc_attr_e( 'Lost Password', 'ncp' ); ?>">
					<?php esc_attr_e( 'Forgot your password?', 'ncp' ); ?>
				</a>
			</div>
			<input type="submit" class="ncp-button-primary" name="wp-submit" id="wp-submit" value="<?php esc_attr_e( 'Log In', 'ncp' ); ?>" />
		</form>
	</div>
	<?php
	return ob_get_clean();
}

// Shortcode to insert the custom login form.
add_shortcode( 'custom_login_form', 'fgrweb_custom_login_form' );

/**
 * Process the login form submission.
 *
 * @return void
 */
function fgrweb_process_login_form() {
	if ( isset( $_POST['log'] ) && isset( $_POST['pwd'] ) && isset( $_POST['ncp_login_nonce'] ) && wp_verify_nonce( $_POST['ncp_login_nonce'], 'ncp-login-nonce' ) ) {
		$username = sanitize_text_field( $_POST['log'] );
		$password = $_POST['pwd'];
		$remember = isset( $_POST['rememberme'] );

		$credentials = [
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => $remember,
		];

		$user = wp_signon( $credentials, false );

		if ( is_wp_error( $user ) ) {
			$_SESSION['ncp_login_errors'] = $user->get_error_codes();
			wp_safe_redirect( home_url( 'sign-in' ) );
			exit;
		} else {
			// Fetch the user's display name to create a profile URL slug.
			$user_info = get_userdata( $user->ID );
			wp_safe_redirect( home_url() );
			exit;
		}
	}
}
add_action( 'init', 'fgrweb_process_login_form' );

// /**
//  * Change login url.
//  *
//  * @param  mixed $login_url
//  * @return void
//  */
// function ncp_custom_url( $login_url ) {
// 	return home_url( 'sign-in' );
// }
// add_filter( 'login_url', 'ncp_custom_url' );
