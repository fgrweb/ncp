<?php
require '../../../../wp-blog-header.php';
header( 'HTTP/1.1 200 OK' );
$return_array = array();
global $wpdb;
$table_ncp_users = $wpdb->prefix . 'ncp_users';
$sql             = "SELECT * FROM {$table_ncp_users} WHERE id_user IS NULL OR id_user = ''";
$users           = $wpdb->get_results( $sql );
if ( $users ) {
	foreach ( $users as $user ) {
		switch ( $user->status ) {
			case 'pending':
				$status_user  = 'Pending';
				$approve      = '<a href="' . admin_url( 'users.php?page=ncp_users_request&request=' . $user->id ) . '" class="ncp-admin-user-approve button action">Approve/Deny</a>';
				break;
			case 'approved':
				$status_user  = 'Approved';
				$approve      = '';
				break;
			case 'denied':
				$status_user = 'Denied';
				$approve     = '';
				break;
			default:
				$status_user  = 'Pending';
				$approve       = '<a href="' . admin_url( 'users.php?page=ncp_users_request&request=' . $user->id ) . '" class="ncp-admin-user-approve button action">Approve/Deny</a>';
				break;
		}
		$return_array[] = array(
			'id'   => $user->id,
			'form_entry_id' => $user->form_entry_id,
			'name' => $user->name,
			'surname'  => $user->surname,
			'organisation' => $user->organisation,
			'position' => $user->position,
			'email' => $user->email,
			'message' => $user->message,
			'status' => $status_user,
			'approve' => $approve,
		);
		
	}
} else {
	$return_array[] = array(
		'id'            => '',
		'form_entry_id' => '',
		'name'          => '',
		'surname'       => '',
		'organisation'  => '',
		'position'      => '',
		'email'         => '',
		'message'       => '',
		'approve'       => '',
		'status'        => '',
	);
}
echo serialize( $return_array );
