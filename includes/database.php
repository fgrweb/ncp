<?php
// If file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
global $wpdb;
$prefix = $wpdb->prefix;
// Check if table ncp_users exists.
$ncp_users = $prefix . 'ncp_users';
if ( $wpdb->get_var( "SHOW TABLES LIKE '$ncp_users'" ) != $ncp_users ) {
	// Table ncp_users does not exist.
	// Create table ncp_users.
	$sql = "CREATE TABLE " . $ncp_users . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		form_entry_id bigint(20) NOT NULL,
		id_user bigint(20) NULL,
		name varchar(255) NULL,
		surname varchar(255) NULL,
		organisation varchar(255) NULL,
		position varchar(255) NULL,
		email varchar(255) NULL,
		message text NULL,
		status varchar(255) NULL,
		PRIMARY KEY  (id)
	);";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
