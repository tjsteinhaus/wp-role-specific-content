<?php

namespace WPRoleSpecificContent\Admin;

class CreateSettingsPage {

    /**
     * What user level can modify the settings
     */
    const CAPABILITY = 'manage_options';

    /**
     * Menu slug, for the url
     */
    const MENU_SLUG = \WPRoleSpecificContent\Setup::PLUGIN_ID;


    /**
     * Runs all the fancy actions and filters to create our settings page.
     * 
     * @since 07/17/2018
     * @author Tyler Steinhaus
     */
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'createOptionsMenu' ) );
        add_action( 'admin_init', array( __CLASS__, 'registerSettings' ) );
    }

    /**
     * Register the settings for the plugin
     * 
     * @since 07/18/2018
     * @author Tyler Steinhaus
     */
    public static function registerSettings() {
        register_setting( \WPRoleSpecificContent\Setup::PLUGIN_ID, \WPRoleSpecificContent\Setup::PLUGIN_ID . '__default_message', array( 'type' => 'string' ) );
    }

    /**
     * Create the options page for the plugin so we can setup
     * the default setting options.
     * 
     * @since 07/18/2018
     * @author Tyler Steinhaus
     */
    public static function createOptionsMenu() {
        add_options_page(
            \WPRoleSpecificContent\Setup::PLUGIN_NAME,
            \WPRoleSpecificContent\Setup::PLUGIN_NAME,
            self::CAPABILITY,
            self::MENU_SLUG,
            array( __CLASS__, 'createPageView' )
        );
    }

    /**
     * Create the page view for the settings page
     * 
     * @since 07/18/2018
     * @author Tyler Steinhaus
     */
    public static function createPageView() {
        require( WPRoleSpecificContent_DIR . 'src/templates/admin/settings_page.phtml' );
    }
}