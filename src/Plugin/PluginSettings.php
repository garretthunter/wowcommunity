<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/21/2015
 * Time: 7:16 PM
 */

namespace WowCommunity\Plugin;

use Pwnraid\Bnet\ClientFactory;

class PluginSettings
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Provides default values for the Display Options.
     *
     * @return array
     */
    public function defaultOptions()
    {

        $defaults = array(
            "apiKey" => "",
            "region" => "us",
            "realm" => "",
            "guild" => "",
        );

        return $defaults;
    }

    public function initializeOptions () {

        if( false == get_option( 'wowcommunity_options' ) ) {
            $default_array = $this->defaultOptions();
            add_option( 'wowcommunity_options', $default_array );
        }

        register_setting('wowcommunity_options','wowcommunity_options');
    }

    /**
     * This function introduces the theme options into the 'Appearance' menu and into a top-level
     * 'WPPB Demo' menu.
     */
    public function setupPluginOptionsMenu()
    {

        /**
         * Add a new menu option for our plugin
         */
        add_menu_page(
            __($this->plugin_name, 'wowcommunity_plugin'),
            'Wow Community',
            'manage_options',
            'wowcommunity_options',
            array($this, 'renderSettingsPageContent'));

    }

    public function myAdminErrorNotice($message = null) {
        $class = "error";
        if (!isset($message)) {
            $message = "Error in saving";
        }
        echo"<div class=\"$class\"> <p>$message</p></div>";
    }

    /**
     * Renders a simple page to display for the theme menu defined above.
     */
    public function renderSettingsPageContent($active_tab = '')
    { ?>
        <div class="wrap">
            <h2><?php _e( 'World of Warcraft Community Setup', 'wowcommunity_plugin' ) ?></h2>
            <?php settings_errors(); ?>

            <?php if( isset( $_GET[ 'tab' ] ) ) {
                $active_tab = $_GET[ 'tab'];
            } else if( $active_tab == 'apikey_options' ) {
                $active_tab = 'apikey_options';
            } // end if/else ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=wowcommunity_options&apikey_options" class="nav-tab <?php echo $active_tab == 'apikey_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Battle.net API Key', 'wowcommunity_plugin' ); ?></a>
            </h2>

            <?php
            $options = get_option('wowcommunity_options');
            $option_valid_apikey = false;

            /*
             * Do we have a valid API key?
             */
            if (true == $options['apikey']) {
                require (plugin_dir_path(__FILE__).'../../vendor/autoload.php');
                $factory = new ClientFactory($options['apikey']);
                $client = $factory->warcraft(new \Pwnraid\Bnet\Region($options['region']));
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
                    settings_fields( 'wowcommunity_options' );
                    //                do_settings_sections('wowcommunity_options');
                    ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Battle.net API Key</th>
                            <td><input type="text" name="wowcommunity_options[apikey]" value="<?php echo esc_attr($options['apikey']); ?>" maxlength="32" size="40"/> <input type="submit" name="submit" value="Validate" class="button button-primary" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Guild Region</th>
                            <td><input type="text" name="" value="<?php echo esc_attr( strtoupper($options['region']) ); ?>" readonly /> (other regions will be added in the future)
                                <input type="hidden" name="wowcommunity_options[region]" value="<?php echo esc_attr( $options['region'] ); ?>" />
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
                        settings_fields( 'wowcommunity_options' );
                        //                do_settings_sections('wowcommunity_options');
                        ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Battle.net API Key</th>
                                <td><input type="text" name="wowcommunity_options[apikey]" value="<?php echo esc_attr( $options['apikey'] );?>" maxlength="32" size="40" readonly /></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Guild Region</th>
                                <td><input type="text" name="" value="<?php echo esc_attr( strtoupper($options['region']) ); ?>" readonly /> (other regions will be added in the future)
                                    <input type="hidden" name="wowcommunity_options[region]" value="<?php echo esc_attr( $options['region'] ); ?>" />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Guild Realm</th>
                                <td><select name="wowcommunity_options[realm]">
                                        <?php
                                        $myRealm = $options['realm'];
                                        foreach ($realmNames as $realm) { ?>
                                            <option value="<?php echo $realm['name']; ?>"<?php if (!strcasecmp($myRealm, $realm['name'])) :?> SELECTED <?php endif ?>><?php echo esc_attr($realm['name']); ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Guild Name</th>
                                <td><input type="text" name="wowcommunity_options[guild]" size="40" value="<?php echo esc_attr( $options['guild'] ); ?>" /></td>
                            </tr>
                        </table>
                        <?php submit_button(); ?>
                    </form>
                    <?php
                }
            }
            ?>
        </div><!-- /.wrap -->
        <?php
    }

    /**
     * This function provides a simple description for the General Options page.
     *
     * It's called from the 'wppb-demo_initialize_theme_options' function by being passed as a parameter
     * in the add_settings_section function.
     */
    public function apikey_options_callback() {
        $options = get_option('apikey');
        var_dump($options);
        echo '<p>' . __( 'Select which areas of content you wish to display.', 'wowcommunity_options' ) . '</p>';
    } // end general_options_callback



}