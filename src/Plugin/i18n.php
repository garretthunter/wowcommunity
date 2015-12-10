<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/9/15
 * Time: 10:32 PM
 */

namespace WowCommunity\Plugin;


class i18n
{
    private $domain;

    public function loadPluginTextdomain() {
        load_plugin_textdomain(
            $this->domain,
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }

    public function setDomain( $domain ) {
        $this->domain = $domain;
    }
}