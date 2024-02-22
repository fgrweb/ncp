<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ncp_add_admin_menu() { 
	add_menu_page( 'NCP', 'NCP', 'manage_options', 'ncp', 'ncp_options_page' );
}

function ncp_settings_init() { 
	register_setting( 'pluginPage', 'ncp_settings' );

	add_settings_section(
		'ncp_pluginPage_section', 
		__( 'NCP Settings', 'ncp' ), 
		'ncp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'ncp_text_field_0', 
		__( 'Setting name', 'ncp' ), 
		'ncp_text_field_0_render', 
		'pluginPage', 
		'ncp_pluginPage_section' 
	);
}

function ncp_text_field_0_render() { 
	$options = get_option( 'ncp_settings' );
	?>
	<input type='text' name='ncp_settings[ncp_text_field_0]' value='<?php echo $options['ncp_text_field_0']; ?>'>
	<?php
}

function ncp_settings_section_callback() { 
	echo __( 'Enter your settings below:', 'ncp' );
}

function ncp_options_page() { 
	?>
	<form action='options.php' method='post'>
		<h2>NCP</h2>
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
	</form>
	<?php
}

// add_action( 'admin_menu', 'ncp_add_admin_menu' );
// add_action( 'admin_init', 'ncp_settings_init' );

// Create admin page inside users menu.
add_action( 'admin_menu', 'fgrweb_users_request' );

function fgrweb_users_request() {
	add_users_page(
		'Users Request', // Page Title
		'Users Request', // Menu Title
		'manage_options', // Capability
		'ncp_users_request', // Menu Slug
		'fgrweb_users_request_content' // Function
	);
}

/**
 * Display the content of the admin page.
 *
 * @return void
 */
function fgrweb_users_request_content() {
	?>
	<div class="wrap">
		<h2>Users Request</h2>
		<?php
		global $wpdb;
		$table_ncp_users = $wpdb->prefix . 'ncp_users';
		if ( isset( $_GET['request'] ) && ! empty( $_GET['request'] ) ) {
			// DShow data.
			$sql             = "SELECT * FROM {$table_ncp_users} WHERE id = " . $_GET['request'];
			$user            = $wpdb->get_row( $sql );
			if ( $user ) {
				?>
				<h3><?php echo $user->name . ' ' . $user->surname; ?></h3>
				<p>
					<strong>Organisation:</strong> <?php echo $user->organisation; ?><br>
					<strong>Position:</strong> <?php echo $user->position; ?><br>
					<strong>Email:</strong> <?php echo $user->email; ?><br>
					<strong>Message:</strong> <?php echo $user->message; ?>
				</p>
				<form action="" method="post">
					<input type="hidden" name="user_id" value="<?php echo $user->id; ?>">
					<input type="submit" name="approve" value="Approve">
					<input type="submit" name="deny" value="Deny">
				</form>
				<?php
			}
			if ( isset( $_POST['approve'] ) ) {
				$wpdb->update(
					$table_ncp_users,
					array(
						'status' => 'approved',
					),
					array( 'id' => $_GET['request'] )
				);
				?>
				<script>
					window.location = "<?php echo admin_url( 'users.php?page=ncp_users_request' ); ?>";
				</script>
				<?php
				// Send email to user.
				$to 	= $user->email;
				$subject = 'Your request has been approved';
				$message = 'Your request has been approved. Please, complete registration in this link: <a href="' . home_url( 'register?request=' . fgrweb_encrypt_string( $_GET['request'] ) ) . '">Register</a>';
				$headers = array('Content-Type: text/html; charset=UTF-8');
				wp_mail( $to, $subject, $message, $headers );
			}
			if ( isset( $_POST['deny'] ) ) {
				$wpdb->update(
					$table_ncp_users,
					array(
						'status' => 'denied',
					),
					array( 'id' => $_GET['request'] )
				);
				?>
				<script>
					window.location = "<?php echo admin_url( 'users.php?page=ncp_users_request' ); ?>";
				</script>
				<?php
				// Send email to user.
				$to 	= $user->email;
				$subject = 'Your request has been denied';
				$message = 'Your request has been denied. Please, contact us for more information.';
				$headers = array('Content-Type: text/html; charset=UTF-8');
				wp_mail( $to, $subject, $message, $headers );
			}
		} else {
			// Display the table
			echo do_shortcode( '[wpdatatable id=1]' );
		}
		?>
	</div>
	<?php
}
