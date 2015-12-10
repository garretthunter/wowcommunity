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
        register_setting('wc_settings','region'); add_option('region','us');
    }
}