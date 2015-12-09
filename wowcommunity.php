<?php
/**
 * Plugin Name: World of Warcraft Community
 * Plugin URI: http:/www.blacktower.com/wowcommunity
 * Description: Brings the World of Warcraft community experience to WordPress
 * Version: 1.0.0
 * Author: Garrett Hunter
 * Author URI: http://www.blacktower.com/author/garretthunter/
 *
 * Text Domain: wowcommunity
 * Domain Path: /i18n/languages/
 *
 * @package WowCommunity
 * @author Garrett Hunter
 *
 */

use WowCommunity\Controller;

if ( ! defined( 'WPINC' ) ) {
	die;
}

spl_autoload_register( 'wowcommunity_autoload' );
function wowcommunity_autoload($class) {
	$prefix = 'WowCommunity\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

$controller = new Controller(plugin_dir_path(__FILE__));

//register_activation_hook(__FILE__, array(&$controller, 'on_activate'));



//$wc_plugin = new WowCommunity\Controller(plugin_dir_path(__FILE__));

//add_action( 'init', array( &$wc_plugin, 'init' ) );
//add_action( 'admin_menu', array( &$wc_plugin, 'admin_menu' ) );
//add_action( 'admin_init', array( &$wc_plugin, 'admin_init' ) );
//add_action( 'widgets_init', array( &$wc_plugin, 'widget_init' ) );
//add_action( 'admin_notices', 'my_admin_error_notice' );
// add_action( 'wp_ajax_admin_ajax_realms', array( &$wc_plugin, 'admin_ajax_realms' ) );
// add_shortcode( 'armory-character', array( &$wc_plugin, 'shortcode' ) );

// These methods need to be defined as static in the class.
//register_activation_hook( $wc_plugin->getMyPluginPath(), array( &$wc_plugin, 'on_activate' ) );
//register_deactivation_hook( $wc_plugin->getMyPluginPath(), array( &$wc_plugin, 'on_deactivate' ) );