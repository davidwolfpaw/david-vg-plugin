<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://david.vg
 * @since      1.0.0
 *
 * @package    David_VG
 * @subpackage David_VG/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    David_VG
 * @subpackage David_VG/admin
 * @author     David Laietta <david@david.vg>
 */
class David_VG_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_name = 'david_vg_settings';


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in David_VG_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The David_VG_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/david-vg-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in David_VG_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The David_VG_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/david-vg-admin.js', array( 'jquery' ), $this->version, false );

        $dvg_data = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        );
        wp_localize_script( $this->plugin_name, 'dvgAdmin', $dvg_data );

	}


	/**
	 * Add once 5 minute interval to wp schedules
	 *
	 * @since    1.0.0
	 * @param	 $interval
	 */
    public function dvg_cron_interval_five_minutes( $interval ) {

        $interval_time = 30;
        $interval['five_minutes'] = array('interval' => $interval_time, 'display' => __('Every 5 minutes') );
        return $interval;

    }



	/**
	 * Check and Schedule Cron Hook
	 *
	 * @since    1.0.0
	 */
    public function dvg_cron_hook_execution() {

        if ( ! wp_next_scheduled( 'dvg_cron_hook' ) ) {
            wp_schedule_event( time(), 'five_minutes', 'dvg_cron_hook' );
        }

    }


}
