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

	protected $name;

	/**
	 * @var string
	 */

	protected $version;

	/**
	 * @var string
	 */
	private $_myPluginPath = null;

	/**
	 * WowCommunity constructor.
	 * @arg string $plugin_path
	 */
	public function __construct($plugin_path)
	{
		$this->_myPluginPath = $plugin_path;

		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'widgets_init', array( &$this, 'widgets_init' ) );

		wp_enqueue_style( "WowCommunity", plugin_dir_url( __FILE__ ) . '../css/wowcommunity.css', array(), '1.0.0', 'all' );

//		register_activation_hook($this->getMyPluginPath(), array($this, 'on_activate'));
		register_activation_hook($this->getMyPluginPath()."/src", array(&$this, 'on_activate'));
		register_deactivation_hook($this->getMyPluginPath() , array($this, 'on_deactivate') );
		register_uninstall_hook($this->getMyPluginPath() , array($this, 'on_deactivate') );
	}

	public function init() {
		if (is_admin()) {
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		}
		//$this->register_settings();
	}

	/**
	 * Widget activation method.
	 */
	function widgets_init () {
		register_widget( 'WowCommunity\Widgets\RealmStatus' );
	}
	/**
	 * Plugin activation method.
	 *
	 * Ensure that the activation of the plugin creates sane default values for the global settings.
	 */
	 static function on_activate() {
//		add_option( 'wc_settings', Battle_Net_API_Plugin::admin_settings_default_values() );
		register_setting('wc_settings','region'); add_option('region','us');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function define_public_hooks() {
//		$plugin_public = new WowCommunity_Public( $this->get_plugin_name(), $this->get_version() );
		add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles' );
//		$this->loader->add_action( 'wp_enqueue_scripts', this, 'enqueue_scripts' );
	}


	public function admin_menu() {

		add_action ('admin_init', array (&$this, 'register_settings'));

		add_menu_page(
			__( 'WoW Community', 'wc' ),
			'Wow Community',
			'administrator',
			'wc_admin',
			array( $this, 'admin_options_page' ));
	}

	public function register_settings () {
		register_setting('wc_settings','apikey');
		register_setting('wc_settings','region');
		register_setting('wc_settings','guild');
		register_setting('wc_settings','realm');
		register_setting('wc_settings','_valid_apikey');

	}

	public function my_admin_error_notice($message = null) {
		$class = "error";
		if (!isset($message)) {
			$message = "Error in saving";
		}
		echo"<div class=\"$class\"> <p>$message</p></div>";
	}

	public function admin_options_page() { ?>
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
				require ($this->getMyPluginPath().'vendor/autoload.php');
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
					$this->my_admin_error_notice('Invalid API Key. Please enter a valid API Key to continue');
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
	 * Plugin deactivation method.
	 *
	 * Make sure to remove the plugins global settings when deactivating it.
	 */
	static function on_deactivate() {
		unregister_setting('wc_settings','apikey'); delete_option('apikey');
		unregister_setting('wc_settings','region'); delete_option('region');
		unregister_setting('wc_settings','guild'); delete_option('guild');
		unregister_setting('wc_settings','realm'); delete_option('realm');
		unregister_setting('wc_settings','_valid_apikey'); delete_option('_valid_apikey');
	}

	/* Getters and Setters */
	/**
	 * @return string
	 */
	public function getMyPluginPath() {
		return $this->_myPluginPath;
	}

	/**
	 * @param string $myPluginPath
	 */
	public function setMyPluginPath( $myPluginPath ) {
		$this->_myPluginPath = $myPluginPath;
	}



}