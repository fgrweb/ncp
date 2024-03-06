<?php
/**
 * Plugin Name: NCP
 * Plugin URI: http://yourwebsite.com/
 * Description: Functionality plugin for Neglected Crisis Platform.
 * Version: 1.0.4
 * Author: Rewire Design
 * Author URI: http://yourwebsite.com/
 * Text Domain: ncp
 * License: GPL2
 **/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Load plugin textdomain.
 *
 * @return void
 */
function ncp_load_textdomain() {
	load_plugin_textdomain( 'ncp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// Include the core functionality file.
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';
// Include the database file.
require_once plugin_dir_path( __FILE__ ) . 'includes/database.php';

// Include the admin settings file.
if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/settings.php';
	// Check for updates.
	// require_once plugin_dir_path( __FILE__ ) . 'updater/class-ncp-updater.php';
	// $updater = new NCP_Updater( __FILE__ );
	// $updater->init();
}

// Include the public display file.
require_once plugin_dir_path( __FILE__ ) . 'public/display.php';
