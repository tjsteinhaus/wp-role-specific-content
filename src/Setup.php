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

        if( !is_admin() ) {
            add_action( 'wp', array( __CLASS__, 'setupFrontend' ), 999 );
            add_filter( 'the_content', array( __CLASS__, 'the_content' ) );
        }
    }

    /**
     * Figure out if the page needs to redirect or 404.
     * 
     * @since 07/17/2018
     * @author Tyler Steinhaus
     */
    public static function setupFrontend() {
        global $post;

        if( !in_array( $post->post_type, \WPRoleSpecificContent\Admin\CreateMetaBox::POST_TYPES ) ) {
            return false;
        }

        add_action( 'pre_get_posts', array( __CLASS__, 'modifyWPQuery' ) );

        add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'removeMenuItems' ), 10, 2 );

        $selected_roles = get_post_meta( $post->ID, 'wp_role_specific_content__role', true );
        $redirect = get_post_meta( $post->ID, 'wp_role_specific_content__redirect', true );
        
        if( !self::hasAccessToPost( $post->ID ) ) {
            if( !empty( $redirect ) ) { 
                header( 'Location: ' . $redirect );
            }
        }
    }

    /**
     * Removes menu items if user doesn't have access.
     * 
     * @since 08/02/2018
     * @author Tyler Steinhaus
     * 
     * @param $menu_items (object)
     * @param $args (object)
     * 
     * @return object
     */
    public static function removeMenuItems( $menu_items, $args ) {
        $_menu_items = array();

        if( count( $menu_items ) > 0 ) {
            foreach( $menu_items as $key => $item ) {
                $post_id = $item->object_id;

                if( $post_id != $item->ID ) {
                    if( !self::shouldShowInMenu( $post_id ) ) {
                        unset( $menu_items[$key] );
                    }
                }
            }
        }

        return $menu_items;
    }

    /**
     * the_content - separate function that does the restricting content
     * 
     * @since 07/26/2018
     * @author Tyler Steinhaus
     */
    public static function the_content( $content ) {
        global $post;

        if( !in_array( $post->post_type, \WPRoleSpecificContent\Admin\CreateMetaBox::POST_TYPES ) ) {
            return false;
        }

        if( !self::hasAccessToPost( $post->ID ) ) {
            $message = get_post_meta( $post->ID, 'wp_role_specific_content__message', true );
    
            // If message is empty, use the default message.
            if( empty( trim( $message ) ) ) {
                $message = get_option( \WPRoleSpecificContent\Setup::PLUGIN_ID . '__default_message' );
            }

            $message = str_replace( '{{ PAGE_TITLE }}', get_the_title(), $message );
                
            return wp_specialchars_decode( $message );
        }

        return $content;
    }

    /**
     * Modify the WP Query so we never see pages that 
     * are hidden from public queries
     * 
     * @since 07/30/2018
     * @author Tyler Steinhaus
     */
    public static function modifyWPQuery( $query ) {   
        if( !is_admin() ) {
            global $wpdb;
        
            $exclude_posts = $wpdb->get_col( "SELECT post_id from $wpdb->postmeta WHERE meta_key = 'wp_role_specific_content__hide' && meta_value = '1'" );
            $exclude_ids = array();
            if( count( $exclude_posts ) > 0 ) {
                foreach( $exclude_posts as $post ) {
                    if( !self::hasAccessToPost( $post ) ) {
                        $exclude_ids[] = $post;
                    }
                }
            }

            $query->set( 'post__not_in', $exclude_ids );
        }
    }

    /**
     * Check if user role is able to see said post
     * 
     * @since 07/30/2018
     * @author Tyler Steinhaus
     * 
     * @param $post_id (int) Post Id of the post you want to check
     * @param $hide (bool) Whether you want to check to see if the post is hidden from user roles that don't have access
     * 
     * @return (bool) Whether the user role has access to the post
     */
    public static function hasAccessToPost( int $post_id = null ) {
        global $wpdb;

        // if no post_id is given grab the global id
        if( is_null( $post_id ) ) {
            global $post;

            $post_id = $post->ID;
        }

        // Get the current user role
        $current_user_role = wp_get_current_user();

        if( empty( $current_user_role->roles ) ) {
            $current_user_role = 'public';
        } else {
            $current_user_role = $current_user_role->roles[0];
        }

        // Selected Roles
        $selected_roles = (array) get_post_meta( $post_id, 'wp_role_specific_content__role', true );

        // If there are no selected roles, show to everyone
        if( count( $selected_roles ) <= 0 ) {
            return true;
        }

        // Show if current role is in the selected roles
        if( in_array( $current_user_role, (array) $selected_roles ) ) {
            return true;
        }

        // Otherwise return false
        return false;
    }

    /**
     * Should the post be hidden from the user roles that don't have access
     * 
     * @since 07/31/2018
     * @author Tyler Steinhaus
     * 
     * @param $post_id (int)
     * 
     * @return (bool) Whether it should be displayed
     */
    public static function shouldShowPost( int $post_id ) {
        if( self::hasAccessToPost( $post_id ) ) {
            $hide = get_post_meta( $post_id, 'wp_role_specific_content__hide', true ) == "1" ? true : false;

            if( $hide ) {
                return false;
            }

            return true;
        }

        return true;
    }

    /**
     * Should the post be displayed in the menu
     * 
     * @since 07/31/2018
     * @author Tyler Steinhaus
     * 
     * @param $post_id (int)
     * 
     * @return (bool) Whether it should be displayed
     */
    public static function shouldShowInMenu( int $post_id ) {
        if( !self::hasAccessToPost( $post_id ) ) {
            $hideInMenu = get_post_meta( $post_id, 'wp_role_specific_content__hide_menus', true ) == "1" ? true : false;

            if( $hideInMenu ) {
                return false;
            }

            return true;
        }

        return true;
    }
}