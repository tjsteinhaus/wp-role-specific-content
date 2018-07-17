<?php

namespace WPRoleSpecificContent\Admin;

class CreateMetaBox {

    /**
     * Available on which post types
     */
    const POST_TYPES = array( 'page', 'post' );

    /**
     * Where should the box be displayed?
     */
    const POSITION = 'side';

    /**
     * What order should the box be displayed in
     */
    const PRIORITY = 'core';

    /**
     * WP Nonce Action
     */
    const NONCE_ACTION = 'WP_Role_Specific_Content_Save';

    /**
     * WP Nonce Name
     */
    const NONCE_NAME = 'WP_Role_Specific_Content';

    /**
     * Runs all the fancy actions and filters to create our meta box.
     * 
     * @since 07/17/2018
     * @author Tyler Steinhaus
     */
    public function init() {
        add_action( 'add_meta_boxes', array( __CLASS__, 'createMetaBox' ) );
        add_actioN( 'save_post', array( __CLASS__, 'saveMetaData' ) );
    }

    /**
     * Create the post meta box for our fields.
     * 
     * @since 07/17/2018
     * @author Tyler Steinhaus
     */
    public function createMetaBox() {
        add_meta_box( 
            \WPRoleSpecificContent\Setup::PLUGIN_ID, // Metabox ID
            \WPRoleSpecificContent\Setup::PLUGIN_NAME, // Metabox Name
            array( __CLASS__, 'createView' ), // Metabox Callback
            self::POST_TYPES, // Metabox Post Types
            self::POSITION, // Metabox Position
            self::PRIORITY // Metabox Priority
        );
    }

    /**
     * Display's our meta box
     * 
     * @param $post \WP_Post
     * 
     * @since 07/17/2018
     * @author Tyler Steinhaus
     */
    public function createView( \WP_Post $post ) {
        require( WPRoleSpecificContent_DIR . '/src/templates/admin/meta_box.phtml' );
    }

    /**
     * Save the post meta data from our plugin
     * 
     * @param $post_id int
     * 
     * @since 07/17/2018
     * @author Tyler Steinhaus
     */
    public function saveMetaData( $post_id ) {
        // Check to see if multiple items should flag so the data isn't saved
        if( 
            !wp_verify_nonce( $_POST[self::NONCE_NAME], self::NONCE_ACTION )
        ) {
            return;
        }

        // Set the data we want to be saved
        $selected_roles = $_POST['wp_role_specific_content__role'];
        $message = esc_html( $_POST['wp_role_specific_content__message'] );
        $redirect = esc_html( $_POST['wp_role_specific_content__redirect'] );

        update_post_meta( $post_id, 'wp_role_specific_content__role', $selected_roles );
        update_post_meta( $post_id, 'wp_role_specific_content__message', $message );
        update_post_meta( $post_id, 'wp_role_specific_content__redirect', $redirect );
    }

    /**
     * Get a list of all the user roles
     * 
     * @since 07/17/2018
     * @author Tyler Steinhaus
     */
    public function getUserRoles() {
        global $wp_roles;

        return $wp_roles->get_names();
    }
} 