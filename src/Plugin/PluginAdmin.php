<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/9/15
 * Time: 10:45 PM
 */

namespace WowCommunity\Plugin;

use Pwnraid\Bnet\ClientFactory;
use WowCommunity\Plugin\OptionsEntity;
use Pwnraid\Bnet\Warcraft\Characters\ClassEntity;

class PluginAdmin
{
    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../../css/admin.css', array(), $this->version, 'all' );
    }

    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../../js/admin.js', array( 'jquery' ), $this->version, false );
    }

    /**
     * Widget activation method.
     */
    function widgetsInit () {
        register_widget( 'WowCommunity\Widgets\RealmStatus' );
    }
}