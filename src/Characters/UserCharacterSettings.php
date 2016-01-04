<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 1/3/16
 * Time: 9:06 PM
 */

namespace WowCommunity\Characters;


class UserCharacterSettings
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
    public function defaultUserCharacterOptions()
    {
        $defaults = array(
            "characters" => array(),
        );
        return $defaults;
    }

    /**
     * Renders a simple page to display for the theme menu defined above.
     */
    public function renderUserCharacterSettingsPageContent( $active_tab = '' )
    { ?>
        <div class="wrap">
            <?php settings_errors(); ?>

            <?php if( isset( $_GET[ 'tab' ] ) ) {
                $active_tab = $_GET[ 'tab'];
            } else {
                $active_tab = 'apikey_options';
            } // end if/else ?>

            <h2 class="nav-tab-wrapper">
                <span class="nav-tab nav-tab-active"><?php _e( 'Characters', 'wowcommunity_plugin' ); ?></span>
            </h2>

            <form method="post" action="options.php">

            </form>

        </div><!-- /.wrap -->
        <?php
    }

    public function initializeUserCharacterOptions () {

        if( false == get_option( 'wowcommunity_usercharacter_options' ) ) {
            $default_array = $this->defaultUserCharacterOptions();
            add_option( 'wowcommunity_usercharacter_options', $default_array );
        }

        add_settings_section(
            'usercharacter_section',			                // ID used to identify this section and with which to register options
            __( '', 'wowcommunity-plugin' ),	                // Title to be displayed on the administration page
            array( $this, 'usercharacter_section_callback'),    // Callback used to render the description of the section
            'wowcommunity_usercharacter_options'                // Page on which to add this section of options
        );

        register_setting(
            'wowcommunity_usercharacter_options',
            'wowcommunity_usercharacter_options',
            array( $this, 'sanitize_usercharacter_options')
        );
    }

}