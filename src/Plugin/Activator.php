<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/9/15
 * Time: 9:29 PM
 */

namespace WowCommunity\Plugin;


class Activator
{
    public static function activate ()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "wowcommunity_characters";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                created_by bigint(20) unsigned NOT NULL,
                created_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                updated_by bigint(20) unsigned NOT NULL,
                updated_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                UNIQUE KEY id (id)
                ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}