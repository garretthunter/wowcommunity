<?php

/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/6/2015
 * Time: 7:22 PM
 */
namespace WowCommunity;

use Pwnraid\Bnet\ClientFactory;
use Pwnraid\Bnet\Warcraft\Characters\ClassEntity;
use WowCommunity\Plugin\Loader;
use WowCommunity\Plugin\i18n;
use WowCommunity\Plugin\PluginAdmin;
use WowCommunity\Plugin\PluginPublic;

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
//		add_action( 'widgets_init', array( &$this, 'widgets_init' ) );

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
		$this->loader->addAction( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function defineAdminHooks() {
		$plugin_admin = new PluginAdmin( $this->getPluginName(), $this->getVersion() );
		$this->loader->addAction( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->addAction( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	private function definePublicHooks() {
		$plugin_public = new PluginPublic( $this->getPluginName(), $this->getVersion() );
		$this->loader->addAction( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->addAction( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	public function init() {
		if (is_admin()) {
			add_action( 'admin_menu', array( &$this, 'adminMenu') );
		}
		//$this->register_settings();
	}

	/**
	 * Widget activation method.
	 */
	function widgetsInit () {
		register_widget( 'WowCommunity\Widgets\RealmStatus' );
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

	public function adminMenu() {

		add_action ('admin_init', array (&$this, 'registerSettings'));

		add_menu_page(
			__( 'WoW Community', 'wc' ),
			'Wow Community',
			'administrator',
			'wc_admin',
			array( $this, 'adminOptionsPage'));
	}

	public function registerSettings () {
		register_setting('wc_settings','apikey');
		register_setting('wc_settings','region');
		register_setting('wc_settings','guild');
		register_setting('wc_settings','realm');
		register_setting('wc_settings','_valid_apikey');

	}

	public function myAdminErrorNotice($message = null) {
		$class = "error";
		if (!isset($message)) {
			$message = "Error in saving";
		}
		echo"<div class=\"$class\"> <p>$message</p></div>";
	}

	public function adminOptionsPage() { ?>
		<div class="wrap">
			<h2><?php _e( 'World of Warcraft Community Setup', 'wc' ) ?></h2>
			<?php

			$option_apikey = get_option('apikey');
			$option_realm = get_option('realm');
			$option_guild = get_option('guild');
			$option_valid_apikey = get_option('_valid_apikey');
			$option_region = get_option('region');

			/*
			 * We have a key, now test if it's valid
			 */
			if (true == $option_apikey) {
				require (plugin_dir_path(__FILE__).'vendor/autoload.php');
				$factory = new ClientFactory($option_apikey);
				$client = $factory->warcraft(new \Pwnraid\Bnet\Region($option_region));
				try {
					/**
					 * Only way to test the API key is to make call to Battle.net site with the key. It knows
					 */
					$realmNames = $client->realms()->all();

					//$character = $client->characters()->on('Arathor')->find('loganfive');
					//$race = ClassEntity::fromId(1);
					//$character = $client->characters()->find('loganfive');
					//print_r($character);
					//echo "I am a ".$character['race']['name']." ". $character['class']['name'];

					$option_valid_apikey = true;
				} catch (\Pwnraid\Bnet\Exceptions\BattleNetException $exception) {
					$this->myAdminErrorNotice('Invalid API Key. Please enter a valid API Key to continue');
					$option_valid_apikey = false;
				}
			}

			if (false == $option_valid_apikey){
				/**
				 * No key has been stored yet, do not issue an error
				 */
				?>
				<p>Please enter a valid Battle.net API Key to add your Realm and Guild. Get your free Battle.net API key at <a href="https://dev.battle.net" target="_blank">dev.Battle.net</a>.</p>
				<form method="post" name="options" action="options.php">
					<?php
					settings_fields( 'wc_settings' );
					//                do_settings_sections('wc_settings');
					?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">Battle.net API Key</th>
							<td><input type="text" name="apikey" value="<?php echo esc_attr($option_apikey); ?>" maxlength="32" size="40"/> <input type="submit" name="submit" value="Validate" class="button button-primary" />
							</td>
						</tr>
					</table>
				</form>
				<?php
			} else {
				/**
				 * If it's a valid key, proceed.
				 */
				if (true == $option_valid_apikey) { ?>

					<form method="post" name="options" action="options.php">
						<?php
						settings_fields( 'wc_settings' );
						//                do_settings_sections('wc_settings');
						?>
						<table class="form-table">
							<tr valign="top">
								<th scope="row">Battle.net API Key</th>
								<td><input type="text" name="apikey" value="<?php echo esc_attr( $option_apikey );?>" maxlength="32" size="40" readonly /></td>
							</tr>
							<tr valign="top">
								<th scope="row">Guild Region</th>
								<td><input type="text" name="" value="<?php echo esc_attr( strtoupper($option_region) ); ?>" readonly /> (other regions will be added in the future)
									<input type="hidden" name="region" value="<?php echo esc_attr( $option_region ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">Guild Realm</th>
								<td><select name="realm">
										<?php
										$myRealm = $option_realm;
										foreach ($realmNames as $realm) { ?>
											<option value="<?php echo $realm['name']; ?>"<?php if (!strcasecmp($myRealm, $realm['name'])) :?> SELECTED <?php endif ?>><?php echo esc_attr($realm['name']); ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">Guild Name</th>
								<td><input type="text" name="guild" size="40" value="<?php echo esc_attr( $option_guild ); ?>" /></td>
							</tr>
						</table>
						<?php submit_button(); ?>
					</form>
					<?php
				}
			}
			?>
		</div>
		<?php
	}

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