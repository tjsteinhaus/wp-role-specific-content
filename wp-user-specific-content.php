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

/**
  * Create our Autoload so we don't have to include files.
  */
spl_autoload_register( function( $class ) {
    $class = ltrim( $class, '\\' );

    if( strpos( $class, __NAMESPACE__ ) !== 0 ) return;

    // modify the file structure to support lowercase folders
    $class = str_replace( __NAMESPACE__, '', $class );
    $path = str_replace( '\\', DIRECTORY_SEPARATOR, $class );
    $split_path = explode( DIRECTORY_SEPARATOR, $class );
    if( count( $split_path ) > 2 ) {
        $file_name = $split_path[count($split_path)-1];
        unset( $split_path[count($split_path)-1] );
        $split_path = array_map( 'strtolower', $split_path );
        $path = implode( DIRECTORY_SEPARATOR, $split_path );

        $path = __DIR__ . DIRECTORY_SEPARATOR . 'src' . $path . DIRECTORY_SEPARATOR . $file_name . '.php';
    } else {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'src' . $path . '.php';
    } 

    require_once( $path );
} );

add_action( 'init', function() {
	// Start the engines
	$GLOBALS[\WPRoleSpecificContent\Setup::PLUGIN_ID] = new \WPRoleSpecificContent\Setup();
	$GLOBALS[\WPRoleSpecificContent\Setup::PLUGIN_ID]->init();
}, 0 );