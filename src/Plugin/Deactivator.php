<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/9/15
 * Time: 9:42 PM
 */

namespace WowCommunity\Plugin;


class Deactivator
{
    public static function deactivate()
    {
        unregister_setting('wc_settings','apikey'); delete_option('apikey');
        unregister_setting('wc_settings','region'); delete_option('region');
        unregister_setting('wc_settings','guild'); delete_option('guild');
        unregister_setting('wc_settings','realm'); delete_option('realm');
        unregister_setting('wc_settings','_valid_apikey'); delete_option('_valid_apikey');
    }
}