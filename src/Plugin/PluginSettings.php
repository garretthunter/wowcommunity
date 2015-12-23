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

    /**
     * Provides default values for the Display Options.
     *
     * @return array
     */
    public function defaultApiKeyOptions()
    {

        $defaults = array(
            "region" => "us",
            "apikey" => "",
            "valid_apikey" => false,
        );

        return $defaults;
    }

    public function defaultGuildOptions()
    {

        $defaults = array(
            "realm" => "",
            "guild" => "",
        );

        return $defaults;
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
            } else {
                $active_tab = 'apikey_options';
            } // end if/else ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=wowcommunity_options&tab=apikey_options" class="nav-tab <?php echo $active_tab == 'apikey_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Battle.Net API Key', 'wowcommunity_plugin' ); ?></a>
                <?php
                $options = get_option( 'wowcommunity_apikey_options' );
                if( $options['valid_apikey'] === true ) { ?>

                    <a href="?page=wowcommunity_options&tab=guild_options" class="nav-tab <?php echo $active_tab == 'guild_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Guild Settings', 'wowcommunity_plugin' ); ?></a>

                <?php } ?>
            </h2>

            <form method="post" action="options.php">
                <?php

                if( $active_tab == 'apikey_options' ) {

                    settings_fields( 'wowcommunity_apikey_options');
                    do_settings_sections( 'wowcommunity_apikey_options' );

                } else if( $active_tab == 'guild_options' ) {

                    settings_fields( 'wowcommunity_guild_options');
                    do_settings_sections( 'wowcommunity_guild_options' );

                }

                if( $active_tab == 'apikey_options' ) {
                    if( $options['valid_apikey'] == false ) {
                        submit_button( "Validate" );
                    } else {
                        submit_button( "Change" );
                    }
                } else {
                    submit_button();
                }

                ?>

            </form>

        </div><!-- /.wrap -->
        <?php
    }

    public function initializeApiKeyOptions () {

        if( false == get_option( 'wowcommunity_apikey_options' ) ) {
            $default_array = $this->defaultApiKeyOptions();
            add_option( 'wowcommunity_apikey_options', $default_array );
        }

        add_settings_section(
            'validate_apikey_section',			                // ID used to identify this section and with which to register options
            __( '', 'wowcommunity-plugin' ),	// Title to be displayed on the administration page
            array( $this, 'validate_apikey_section_callback'),	        // Callback used to render the description of the section
            'wowcommunity_apikey_options'		                // Page on which to add this section of options
        );

        add_settings_field(
            'option_apikey',						        // ID used to identify the field throughout the theme
            __( 'API Key', 'wowcommunity-plugin' ),					// The label to the left of the option interface element
            array( $this, 'apikey_options_callback'),	// The name of the function responsible for rendering the option interface
            'wowcommunity_apikey_options',	            // The page on which this option will be displayed
            'validate_apikey_section',			        // The name of the section to which this field belongs
            array(								        // The array of arguments to pass to the callback. In this case, just a description.
                __( 'Find yours at dev.Battle.net', 'wowcommunity-plugin' ),
            )
        );

        add_settings_field(
            'option_region',						        // ID used to identify the field throughout the theme
            __( 'Region', 'wowcommunity-plugin' ),					// The label to the left of the option interface element
            array( $this, 'region_options_callback'),	// The name of the function responsible for rendering the option interface
            'wowcommunity_apikey_options',	            // The page on which this option will be displayed
            'validate_apikey_section',			        // The name of the section to which this field belongs
            array(								        // The array of arguments to pass to the callback. In this case, just a description.
                __( '(Future regions coming soon...)', 'wowcommunity-plugin' ),
            )
        );

        register_setting(
            'wowcommunity_apikey_options',
            'wowcommunity_apikey_options',
            array( $this, 'validate_apikey_options')
        );
    }

    public function validate_apikey_section_callback () {

        echo '<p>' . __( "Please enter a valid Battle.net API Key to add your Realm and Guild. Get your free Battle.net API key at <a href=\"https://dev.battle.net\" target=\"_blank\">dev.Battle.net</a>.") . '</p>';

    }

    /**
     * This function provides a simple description for the General Options page.
     *
     * It's called from the 'wppb-demo_initialize_theme_options' function by being passed as a parameter
     * in the add_settings_section function.
     */
    public function apikey_options_callback( $messages ) {
        $options = get_option('wowcommunity_apikey_options');
        var_dump($options); echo "<br />"; // gehDEBUG

        if( $options['valid_apikey'] == true ) { ?>
            <input type="text" name="wowcommunity_apikey_options[apikey]" value="<?php echo esc_attr( $options['apikey'] );?>" maxlength="32" size="40" readonly />
            <input type="hidden" name="wowcommunity_apikey_options[valid_apikey]" value="0" />
        <?php } else { ?>
            <input type="text" name="wowcommunity_apikey_options[apikey]" value="<?php echo esc_attr($options['apikey']); ?>" maxlength="32" size="40"/>
            <?php echo $messages[0];
        }
    } // end apikey_options_callback

    public function region_options_callback ( $messages ) {
        $options = get_option('wowcommunity_apikey_options');

        if( $options['valid_apikey'] == true ) { ?>
            <input type="text" name="wowcommunity_apikey_options[region]" value="<?php echo esc_attr( strtoupper($options['region']) ); ?>" readonly />
        <?php } else { ?>
            <input type="text" name="wowcommunity_apikey_options[region]" value="<?php echo esc_attr( strtoupper($options['region']) ); ?>" readonly /> <?php echo $messages[0];
        }
    } // end region_options_callback

    public function validate_apikey_options ( $input ) {

        $options = array();

        if ( isset( $input ) ){
            foreach ( $input as $key => $value) {
                $options[$key] = $value;
            }

            /**
             * Make a test call to validate API
             */
            require (plugin_dir_path(__FILE__).'../../vendor/autoload.php');
            $factory = new ClientFactory( $options['apikey'] );
            $client = $factory->warcraft(new \Pwnraid\Bnet\Region( strtolower( $options['region'] ) ) );
            try {
                $realmNames = $client->realms()->all();

                //$character = $client->characters()->on('Arathor')->find('loganfive');
                //$race = ClassEntity::fromId(1);
                //$character = $client->characters()->find('loganfive');
                //print_r($character);
                //echo "I am a ".$character['race']['name']." ". $character['class']['name'];

                $options['valid_apikey'] = true;
            } catch (\Pwnraid\Bnet\Exceptions\BattleNetException $exception) {
                add_settings_error(
                    'wowcommunity_apikey_options',
                    'apikey',
                    'Invalid API Key. Please enter a valid API Key to continue',
                    'error' );

                $options['valid_apikey'] = false;
            }
        }

        return apply_filters( 'validate_apikey_options', $options, $input );
    }

    /**
     * Sanitization callback for the social options. Since each of the social options are text inputs,
     * this function loops through the incoming option and strips all tags and slashes from the value
     * before serializing it.
     *
     * @params	$input	The unsanitized collection of options.
     *
     * @returns			The collection of sanitized values.
     */
    public function sanitize_apikey_options( $input ) {

//        var_dump( $input );

        // Define the array for the updated options
        $output = array();
        // Loop through each of the options sanitizing the data
        foreach( $input as $key => $val ) {
            if( isset ( $input[$key] ) ) {
                $output[$key] = esc_url_raw( strip_tags( stripslashes( $input[$key] ) ) );
            } // end if
        } // end foreach
        // Return the new collection
        return apply_filters( 'sanitize_apikey_options', $output, $input );
    } // end sanitize_apikey_options

    public function initializeGuildOptions () {

        if( false == get_option( 'wowcommunity_guild_options' ) ) {
            $default_array = $this->defaultGuildOptions();
            add_option( 'wowcommunity_guild_options', $default_array );
        }

        add_settings_section(
            'guild_settings_section',			            // ID used to identify this section and with which to register options
            __( 'WoW Guild', 'wowcommunity-plugin' ),		        // Title to be displayed on the administration page
            array( $this, 'guild_options_callback'),	    // Callback used to render the description of the section
            'wowcommunity_options'		                // Page on which to add this section of options
        );

        add_settings_field(
            'guild_region',						        // ID used to identify the field throughout the theme
            __( 'Guild Region', 'wowcommunity-plugin' ),					// The label to the left of the option interface element
            array( $this, 'guild_region_callback'),	// The name of the function responsible for rendering the option interface
            'wowcommunity_options',	            // The page on which this option will be displayed
            'guild_settings_section',			        // The name of the section to which this field belongs
            array(								        // The array of arguments to pass to the callback. In this case, just a description.
                __( '(other regions will be added in the future)', 'wowcommunity-plugin' ),
            )
        );

        add_settings_field(
            'guild_realm',						        // ID used to identify the field throughout the theme
            __( 'Guild Realm', 'wowcommunity-plugin' ),					// The label to the left of the option interface element
            array( $this, 'guild_realm_callback'),	// The name of the function responsible for rendering the option interface
            'wowcommunity_options',	            // The page on which this option will be displayed
            'guild_settings_section',			        // The name of the section to which this field belongs
            array(								        // The array of arguments to pass to the callback. In this case, just a description.
                __( 'Select your realm', 'wowcommunity-plugin' ),
            )
        );

        add_settings_field(
            'guild_name',						        // ID used to identify the field throughout the theme
            __( 'Guild Name', 'wowcommunity-plugin' ),					// The label to the left of the option interface element
            array( $this, 'guild_name_callback'),	// The name of the function responsible for rendering the option interface
            'wowcommunity_guild_options',	            // The page on which this option will be displayed
            'guild_settings_section',			        // The name of the section to which this field belongs
            array(								        // The array of arguments to pass to the callback. In this case, just a description.
                __( 'Choose your guild name', 'wowcommunity-plugin' ),
            )
        );

        register_setting(
            'wowcommunity_guild_options',
            'wowcommunity_guild_options',
            array( $this, 'sanitize_guild_options')
        );
    }

    public function guild_options_callback () {
        $options = get_option('wowcommunity_guild_options');
        var_dump( $options );
    ?>
        <table class="form-table">
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

    <?php
    }

    public function sanitize_guild_options( $input ) {
        // Define the array for the updated options
        $output = array();
        // Loop through each of the options sanitizing the data
        foreach( $input as $key => $val ) {
            if( isset ( $input[$key] ) ) {
                $output[$key] = esc_url_raw( strip_tags( stripslashes( $input[$key] ) ) );
            } // end if
        } // end foreach
        // Return the new collection
        return apply_filters( 'sanitize_guild_options', $output, $input );
    } // end sanitize_guild_options

    public function myAdminErrorNotice($message = null) {
        $class = "error";
        if (!isset($message)) {
            $message = "Error in saving";
        }
        echo"<div class=\"$class\"> <p>$message</p></div>";
    }

}