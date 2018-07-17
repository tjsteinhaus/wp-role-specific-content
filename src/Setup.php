<?php
/**
 * Setup everything that runs the plugin
 */

namespace WPRoleSpecificContent;

class Setup {

    /**
     * Plugin ID
     */
    const PLUGIN_ID = 'WP_Role_Specific_Content';

    /**
     * Plugin Name
     */
    const PLUGIN_NAME = 'WP Role Specific Content';

    /**
     * Initialization of the plugin
     */
    public function init() {
        \WPRoleSpecificContent\Admin\CreateMetaBox::init();
        add_action( 'wp', array( __CLASS__, 'setupFrontend' ) );
    }

    /**
     * Figure out if the page needs to redirect or 404.
     * 
     * @since 07/17/2018
     * @author Tyler Steinhaus
     */
    public function setupFrontend() {
        global $post;

        // Get the current user role
        $current_user_role = wp_get_current_user()->roles[0];

        $selected_roles = get_post_meta( $post->ID, 'wp_role_specific_content__role', true );
        $message = get_post_meta( $post->ID, 'wp_role_specific_content__message', true );
        $redirect = get_post_meta( $post->ID, 'wp_role_specific_content__redirect', true );

        if( count( $selected_roles ) > 0 ) {
            if( !in_array( $current_user_role, $selected_roles ) ) {
                if( !empty( $redirect ) ) { 
                    header( 'Location: ' . $redirect );
                } else {
                    add_filter( 'the_content', function() use ( $message ) {
                        return wp_specialchars_decode( $message );
                    }, 999 );;
                }
            }
        }
        
    }

}