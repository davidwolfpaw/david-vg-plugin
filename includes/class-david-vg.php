<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://david.vg
 * @since      1.0.0
 *
 * @package    David_VG
 * @subpackage David_VG/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    David_VG
 * @subpackage David_VG/includes
 * @author     David Laietta <david@david.vg>
 */
class David_VG {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      David_VG_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'david-vg';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - David_VG_Loader. Orchestrates the hooks of the plugin.
	 * - David_VG_i18n. Defines internationalization functionality.
	 * - David_VG_Admin. Defines all hooks for the admin area.
	 * - David_VG_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-david-vg-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-david-vg-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-david-vg-admin.php';

        /**
         * The class responsible for settings
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/settings/class-david-vg-settings.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-david-vg-public.php';

		/**
		 * The class responsible for defining a base data stream
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-david-vg-datum.php';

		/**
		 * Stream Classes
		 */
		require_once plugin_dir_path( __FILE__ ) . 'streams/class-david-vg-twitter.php';
		require_once plugin_dir_path( __FILE__ ) . 'streams/class-david-vg-pocket.php';
		require_once plugin_dir_path( __FILE__ ) . 'streams/class-david-vg-google-fit.php';
		// require_once plugin_dir_path( __FILE__ ) . 'streams/google/autoload.php';
		require_once plugin_dir_path( __FILE__ ) . 'streams/class-david-vg-open-weather.php';
		require_once plugin_dir_path( __FILE__ ) . 'streams/class-david-vg-daily.php';

		$this->loader = new David_VG_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the David_VG_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new David_VG_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new David_VG_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_settings = new David_VG_Admin_Settings( $this->get_plugin_name(), $this->get_version() );
		$twitter_includes = new David_VG_Twitter( $this->get_plugin_name(), $this->get_version() );
		$pocket_includes = new David_VG_Pocket( $this->get_plugin_name(), $this->get_version() );
		$google_fit_includes = new David_VG_Google_Fit( $this->get_plugin_name(), $this->get_version() );
		$open_weather_includes = new David_VG_Open_Weather( $this->get_plugin_name(), $this->get_version() );
		$daily_includes = new David_VG_Daily( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// // Ajax
		// $this->loader->add_action( 'wp_ajax_pocket_generate_request_token', $plugin_settings, 'pocket_generate_request_token' );
		// $this->loader->add_action( 'wp_ajax_nopriv_pocket_generate_request_token', $plugin_settings, 'pocket_generate_request_token' );
		// $this->loader->add_action( 'wp_ajax_pocket_generate_access_token', $plugin_settings, 'pocket_generate_access_token' );
		// $this->loader->add_action( 'wp_ajax_nopriv_pocket_generate_access_token', $plugin_settings, 'pocket_generate_access_token' );

		// Plugin Settings
		// Priority of 9 on admin_menu to place settings at top of menu page
        $this->loader->add_action( 'admin_menu', $plugin_settings, 'setup_plugin_options_menu', 9 );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_twitter_settings' );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_pocket_settings' );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_google_fit_settings' );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_open_weather_settings' );

		// Twitter Hooks
		$this->loader->add_action( 'init', $twitter_includes, 'create_custom_post_type' );
		// $this->loader->add_action( 'cron_schedules', $twitter_includes, 'import_interval_minutes' );
		// $this->loader->add_action( 'init', $twitter_includes, 'set_twitter_schedule' );
		$this->loader->add_action( 'admin_head', $twitter_includes, 'import_tweets_as_posts' );

		// Pocket Hooks 
		$this->loader->add_action( 'init', $pocket_includes, 'create_custom_post_type' );
		// $this->loader->add_action( 'init', $pocket_includes, 'set_pocket_schedule' );
		$this->loader->add_action( 'admin_head', $pocket_includes, 'import_pocket_as_posts' );

		// Google Fit Hooks
		$this->loader->add_action( 'init', $google_fit_includes, 'create_custom_post_type' );
		$this->loader->add_action( 'the_content', $google_fit_includes, 'import_google_fit_as_posts' );

		// Daily Hooks
		$this->loader->add_action( 'init', $daily_includes, 'create_custom_post_type' );
		$this->loader->add_action( 'wp', $daily_includes, 'import_daily_as_posts', 5 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new David_VG_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    David_VG_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
