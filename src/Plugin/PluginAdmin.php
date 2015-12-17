<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/9/15
 * Time: 10:45 PM
 */

namespace WowCommunity\Plugin;


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

    public function registerSettings () {
     die("REGISTER");
        register_setting('wc_settings','apikey');
        register_setting('wc_settings','region');
        register_setting('wc_settings','guild');
        register_setting('wc_settings','realm');
        register_setting('wc_settings','_valid_apikey');

    }

    public function adminMenu() {

        add_menu_page(
            __( $this->plugin_name, 'wc' ),
            'Wow Community',
            'administrator',
            'wc_admin',
            array( $this, 'adminOptionsPage'));
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

}