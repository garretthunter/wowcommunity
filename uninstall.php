<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/8/2015
 * Time: 10:34 PM
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option('wowcommunity_apikey_options');
delete_option('wowcommunity_guild_options');

global $wpdb;

$table_name = $wpdb->prefix . "wowcommunity_characters";

$sql = "DROP $table_name";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );