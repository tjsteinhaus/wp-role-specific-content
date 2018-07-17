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
        
    }

}