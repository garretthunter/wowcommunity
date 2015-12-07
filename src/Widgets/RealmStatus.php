<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 11/27/2015
 * Time: 6:32 PM
 */
namespace WowCommunity\Widgets;

use Pwnraid\Bnet\ClientFactory;

class RealmStatus extends \WP_Widget {
    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        parent::__construct(
            'wowcommunity_realm_status_widget', // Base ID
            __( 'WOW: Realm Status', 'bna' ), // Name
            array( 'description' => __( 'Display your realm\'s status', 'bna' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        /**
         * Display the widget
         */
        require( 'vendor/autoload.php' );
        $factory = new ClientFactory(get_option('apikey'));
        $client = $factory->warcraft(new \Pwnraid\Bnet\Region("us")); //gehDEBUG - hard coding region for now

        try {
            /**
             * Only way to test the API key is to make call to Battle.net site with the key. It knows
             */
	        global $plugin;
            $myRealm = $client->realms()->find('Arathor');
	        if ($myRealm['status'] == 1) {
		        $status = 'up';
	        } else {
		        $status = 'down';
	        }
	        echo '<div class="status-icon '. $status . '"tooltip="' . ucfirst($status) . '"></div>';

        } catch (\Pwnraid\Bnet\Exceptions\BattleNetException $exception) {
            $this->my_admin_error_notice('Invalid API Key. Please enter a valid API Key to continue');
            $option_valid_apikey = false;

	        echo "error";
        }

        /**
         * Display whatever comes next
         */
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Realm Status', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            crafp;
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance )
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }
}
