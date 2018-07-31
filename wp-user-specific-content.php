<?php
/**
 * 
 * Plugin Name: WP Role Specific Content
 * Plugin URI: https://github.com/tjsteinhaus/wp-role-specific-content
 * Description: Choose which roles can access the page on the frontend. Display a message or redirect if a user doesn't have permission.
 * Author: Tyler Steinhaus
 * Version: 1.0
 * Author URI: https://tylersteinhaus.com
*/

namespace WPRoleSpecificContent;

define( 'WPRoleSpecificContent_DIR', plugin_dir_path( __FILE__ ) );

// use Composer autoload
require __DIR__ . '/vendor/autoload.php';

add_action( 'init', function() {
	// Start the engines
	$GLOBALS[\WPRoleSpecificContent\Setup::PLUGIN_ID] = new \WPRoleSpecificContent\Setup();
	$GLOBALS[\WPRoleSpecificContent\Setup::PLUGIN_ID]->init();
}, 0 );