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
        unregister_setting('wowcommunity_plugin','wowcommunity_apikey_options'); delete_option('wowcommunity_apikey_options');
        unregister_setting('wowcommunity_plugin','wowcommunity_guild_options'); delete_option('wowcommunity_guild_options');
    }
}