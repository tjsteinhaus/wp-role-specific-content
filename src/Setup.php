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
        \WPRoleSpecificContent\Admin\CreateSettingsPage::init();
        add_action( 'wp', array( __CLASS__, 'setupFrontend' ), 999 );
        add_action( 'the_post', array( __CLASS__, 'setupFrontend' ) );
    }

    /**
     * Figure out if the page needs to redirect or 404.
     * 
     * @since 07/17/2018
     * @author Tyler Steinhaus
     */
    public function setupFrontend() {
        global $post;

        if( !in_array( $post->post_type, \WPRoleSpecificContent\Admin\CreateMetaBox::POST_TYPES ) ) {
            return false;
        }

        // Get the current user role
        $current_user_role = wp_get_current_user()->roles[0];

        $selected_roles = get_post_meta( $post->ID, 'wp_role_specific_content__role', true );
        $message = get_post_meta( $post->ID, 'wp_role_specific_content__message', true );
        $redirect = get_post_meta( $post->ID, 'wp_role_specific_content__redirect', true );

        // If message is empty, use the default message.
        if( empty( $message ) ) {
            $message = get_option( \WPRoleSpecificContent\Setup::PLUGIN_ID . '__default_message' );
        }

        if( count( $selected_roles ) > 0 ) {
            if( !in_array( $current_user_role, (array) $selected_roles ) ) {
                if( !empty( $redirect ) && !did_action( 'the_post' ) ) { 
                    header( 'Location: ' . $redirect );
                } else {
                    add_filter( 'the_content', array( __CLASS__, 'the_content' ), 9999 );
                }
            }
        }
    }

    /**
     * the_content - this replaces the content with the restricted message
     * 
     * @since 07/18/2018
     * @author Tyler Steinhaus
     */
    public function the_content() {
        $message = get_post_meta( get_the_ID(), 'wp_role_specific_content__message', true );
        
        // If message is empty, use the default message.
        if( empty( $message ) ) {
            $message = get_option( \WPRoleSpecificContent\Setup::PLUGIN_ID . '__default_message' );
        }

        $message = str_replace( '{{ PAGE_TITLE }}', get_the_title(), $message );
             
        return wp_specialchars_decode( $message );
    }

}