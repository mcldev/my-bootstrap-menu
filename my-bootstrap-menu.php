<?php
/*
Plugin Name: My Bootstrap Menu
Plugin URI: https://github.com/mcldev/my-bootstrap-menu
Description: Boostraps a menu using flexible and easy to use settings. Fully customizable for advanced users - including set ids and classes.
Version: 1.0
Author: Michael Carder
Author URI: http://www.michaelcarder.com
License: GPL3
*/


/*
Define project paths and constants
*******************************************
*/
if ( !defined('MY_BOOTSTRAP_MENU_PLUGIN_PATH'))
    define( 'MY_BOOTSTRAP_MENU_PLUGIN_PATH', dirname( __FILE__ ) );
if ( !defined('MY_BOOTSTRAP_MENU_PLUGIN_URL'))
    define( 'MY_BOOTSTRAP_MENU_PLUGIN_URL', plugins_url( '', __FILE__ ) );
if ( !defined('MY_BOOTSTRAP_MENU_PLUGIN_BASENAME'))
    define( 'MY_BOOTSTRAP_MENU_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( !defined('MY_BOOTSTRAP_MENU_PLUGIN_INC'))
    define( 'MY_BOOTSTRAP_MENU_PLUGIN_INC', MY_BOOTSTRAP_MENU_PLUGIN_PATH . '/inc' );
if ( !defined('MY_BOOTSTRAP_MENU_PLUGIN_ASSETS'))
    define( 'MY_BOOTSTRAP_MENU_PLUGIN_ASSETS', MY_BOOTSTRAP_MENU_PLUGIN_PATH . '/assets' );


/**
 * Project name constants
 */
if( !defined('OPTION_GROUP_PAGE_NAME'))
    define('OPTION_GROUP_PAGE_NAME', 'my_bootstrap_menu');
if( !defined('OPTION_SETTINGS_DB_NAME'))
    define('OPTION_SETTINGS_DB_NAME', 'my_bootstrap_menu_settings');

/*
Load required common dependencies here
*******************************************
*/
require_once(MY_BOOTSTRAP_MENU_PLUGIN_INC . '/my-bootstrap-menu-funcs.php');
require_once(MY_BOOTSTRAP_MENU_PLUGIN_INC . '/nav-menu/nav-menu-consts.php');


/**
 * Debug if required here
 * *******************************************
 */
$MY_PLUGIN_ENABLE_DEBUG = true;
global $MY_BOOTSTRAP_MENU_DEBUG;

if( $MY_PLUGIN_ENABLE_DEBUG ){
    //Register required plugin files
    if ( !defined('MY_BOOTSTRAP_DEBUG_FILE'))
        define( 'MY_BOOTSTRAP_DEBUG_FILE', dirname(dirname(__FILE__)) . '/my-bootstrap-menu.log' );
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_ASSETS . '/my-plugin-settings-helper/inc/my-plugin-debug.php');

    //Create the DEBUG object to use throughout the code
    $MY_BOOTSTRAP_MENU_DEBUG = new My_Plugin_Debug(MY_BOOTSTRAP_DEBUG_FILE, 0);
    $MY_BOOTSTRAP_MENU_DEBUG->MSG('**** Start My Bootstrap Menu ****');
};

/*
Create Admin or Public Class and load required dependencies
*******************************************
*/
if ( is_admin() ) {

    //**  Admin **

    //Add My_Plugin_Settings
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_ASSETS . '/my-plugin-settings-helper/my-plugin-admin.php');

    //Inc project files
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_PATH . '/my-bootstrap-menu-installer.php');
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_INC . '/my-bootstrap-menu-admin.php');
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_INC . '/my-bootstrap-menu-admin-settings.php');

    //Load installer to register Activate/Deactivate/Uninstall hooks...
    // (needs to be run before init hook of main plugin)
    $installer = new My_Bootstrap_Menu_Installer(array('option_group_page_name' => OPTION_GROUP_PAGE_NAME,
                                                        'option_settings_db_name' => OPTION_SETTINGS_DB_NAME,
                                                        'plugin_basefile' => __FILE__));
} else {

    //** Public **

    //Add My_Plugin_Settings
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_ASSETS . '/my-plugin-settings-helper/my-plugin-public.php');
    //Inc project files
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_INC . '/my-bootstrap-menu-public.php');
    //Nav Menu code
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_INC . '/nav-menu/nav-menu-markup.php');
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_INC . '/nav-menu/nav-menu-filters.php');
    require_once(MY_BOOTSTRAP_MENU_PLUGIN_INC . '/nav-menu/nav-menu-walker.php');
};


/*
Start the plugin here... hook to run on init
*******************************************
*/
add_action('init', 'run_my_bootstrap_menu');

function run_my_bootstrap_menu()
{
    //Set $my_bootstrap_menu_class as public or admin;
    $my_bootstrap_menu_class = 'My_Bootstrap_Menu_';
    $my_bootstrap_menu_class .= (is_admin()) ? 'Admin' : 'Public' ;

    new $my_bootstrap_menu_class(array('plugin_basename' => plugin_basename(__FILE__),
                                        'plugin_basefile' => __FILE__,
                                        'option_group_page_name' => OPTION_GROUP_PAGE_NAME,
                                        'option_settings_db_name' => OPTION_SETTINGS_DB_NAME,
                                        'min_required_version' => 0,
                                        'current_plugin_version' => My_Bootstrap_Menu_Funcs::get_plugin_version(),
                                 ));

}

