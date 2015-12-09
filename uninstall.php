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

unregister_setting('wc_settings','apikey'); delete_option('apikey');
unregister_setting('wc_settings','region'); delete_option('region');
unregister_setting('wc_settings','guild'); delete_option('guild');
unregister_setting('wc_settings','realm'); delete_option('realm');
unregister_setting('wc_settings','_valid_apikey'); delete_option('_valid_apikey');