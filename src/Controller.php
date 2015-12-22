<?php

/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/6/2015
 * Time: 7:22 PM
 */
namespace WowCommunity;

use WowCommunity\Plugin\Loader;
use WowCommunity\Plugin\i18n;
use WowCommunity\Plugin\PluginAdmin;
use WowCommunity\Plugin\PluginPublic;
use WowCommunity\Plugin\PluginSettings;

/**
 * Provides the WordPress integration
 * @author: Garrett Hunter <garrett.hunter@blacktower.com>
 * Date: 11/22/2015
 * Time: 7:48 PM
 */
class Controller
{
	/**
	 * @var Loader
	 */
	protected $loader;

	/**
	 * @var string
	 */

	protected $plugin_name;

	/**
	 * @var string
	 */

	protected $version;

	/**
	 * WowCommunity constructor.
	 * @arg string $plugin_path
	 */
	public function __construct()
	{
		$this->plugin_name = 'WowCommunity';
		$this->version = '1.0.0';

		$this->loadDependencies();
		$this->setLocale();
		$this->defineAdminHooks();
		$this->definePublicHooks();

//		add_action( 'init', array( &$this, 'init' ) );

//		wp_enqueue_style( "WowCommunity", plugin_dir_url( __FILE__ ) . '../css/wowcommunity.css', array(), '1.0.0', 'all' );

//		register_activation_hook($this->getMyPluginPath()."/src", array(&$this, 'on_activate'));
//		register_deactivation_hook($this->getMyPluginPath() , array($this, 'on_deactivate') );
//		register_uninstall_hook($this->getMyPluginPath() , array($this, 'on_deactivate') );
	}

	private function loadDependencies ()
	{
		$this->loader = new Loader();
	}

	private function setLocale()
	{
		$plugin_i18n = new i18n();
		$plugin_i18n->setDomain( $this->getPluginName() );
		$this->loader->addAction( 'plugins_loaded', $plugin_i18n, 'loadPluginTextdomain' );
	}

	private function defineAdminHooks() {

		$plugin_admin = new PluginAdmin( $this->getPluginName(), $this->getVersion() );
		$plugin_settings = new PluginSettings( $this->getPluginName(), $this->getVersion() );

		/**
		 * Options menu
		 */
		$this->loader->addAction( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->addAction( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		/**
		 * Options Settings
		 */
		$this->loader->addAction( 'admin_menu', $plugin_settings, 'setupPluginOptionsMenu' );
		$this->loader->addAction( 'admin_init', $plugin_settings, 'initializeOptions' );

		/**
		 * Widgets
		 */
		$this->loader->addAction( 'widgets_init', $plugin_admin, 'widgetsInit' );
	}

	private function definePublicHooks() {
		$plugin_public = new PluginPublic( $this->getPluginName(), $this->getVersion() );
		$this->loader->addAction( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->addAction( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}


	public function run()
	{
		$this->loader->run();
	}

	public function init() {
		if (is_admin()) {
			add_action( 'admin_menu', array( &$this, 'adminMenu') );
		}
		//$this->register_settings();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
/*	public function definePublicHooks() {
//		$plugin_public = new WowCommunity_Public( $this->get_plugin_name(), $this->get_version() );
		add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles' );
//		$this->loader->add_action( 'wp_enqueue_scripts', this, 'enqueue_scripts' );
	}
*/

	/**
	 * @return Loader
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * @param Loader $loader
	 */
	public function setLoader($loader)
	{
		$this->loader = $loader;
	}

	/**
	 * @return string
	 */
	public function getPluginName()
	{
		return $this->plugin_name;
	}

	/**
	 * @param string $plugin_ame
	 */
	public function setPluginName($plugin_ame)
	{
		$this->plugin_name = $plugin_ame;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

}