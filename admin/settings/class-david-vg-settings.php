<?php

/**
 * The settings of the plugin.
 *
 * @link       http://devinvinson.com
 * @since      1.0.0
 *
 * @package    Wppb_Demo_Plugin
 * @subpackage Wppb_Demo_Plugin/admin
 */

/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class David_VG_Admin_Settings {

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
	 * Creates main settings menu page, as well as submenu page
	 */
	public function setup_plugin_options_menu() {

		add_menu_page(
			__( 'David VG Settings', 'david-vg' ),
			__( 'David VG', 'david-vg' ),
			'manage_options',
			$this->plugin_name,
			// array( $this, 'render_settings_page_content'),
			'',
			'dashicons-admin-settings',
			3.14159
		);


		add_submenu_page(
			$this->plugin_name,
			'DavidVG Settings',
			'Settings',
			'manage_options',
			'dvg-settings',
			array( $this, 'render_settings_page_content')
		);

	}


	/**
	 * Provide default values for the Social Options.
	 *
	 * @return array
	 */
	public function default_twitter_settings() {

		$defaults = array(
			'dvg_twitter_consumer_key'	=> '',
			'dvg_twitter_consumer_secret'	=> '',
			'dvg_twitter_access_token'	=> '',
			'dvg_twitter_access_token_secret'	=> '',
		);

		return  $defaults;

	}

	/**
	 * Provides default values for the Input Options.
	 *
	 * @return array
	 */
	public function default_input_options() {

		$defaults = array(
			'input_example'		=>	'default input example',
			'textarea_example'	=>	'',
			'checkbox_example'	=>	'',
			'radio_example'		=>	'2',
			'time_options'		=>	'default'
		);

		return $defaults;

	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_settings_page_content( $active_tab = '' ) {
		?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<h2><?php _e( 'David VG Settings', 'david-vg' ); ?></h2>
			<?php settings_errors(); ?>

			<?php
			if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} else if( $active_tab == 'twitter_settings' ) {
				$active_tab = 'twitter_settings';
			} else if( $active_tab == 'input_examples' ) {
				$active_tab = 'input_examples';
			} else {
				$active_tab = 'twitter_settings';
			} // end if/else ?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=dvg-settings&tab=twitter_settings" class="nav-tab <?php echo $active_tab == 'twitter_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Twitter', 'david-vg' ); ?></a>
				<a href="?page=dvg-settings&tab=input_examples" class="nav-tab <?php echo $active_tab == 'input_examples' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Input Examples', 'david-vg' ); ?></a>
			</h2>

			<form method="post" action="options.php">
				<?php

				if( $active_tab == 'twitter_settings' ) {

					settings_fields( 'dvg_twitter_settings' );
					do_settings_sections( 'dvg_twitter_settings' );

				} else {

					settings_fields( 'dvg_input_examples' );
					do_settings_sections( 'dvg_input_examples' );

				} // end if/else

				submit_button();

				?>
			</form>

		</div><!-- /.wrap -->
	<?php
	}


	/**
	 * This function provides a simple description for the Social Options page.
	 *
	 * It's called from the 'dvg_theme_initialize_twitter_settings' function by being passed as a parameter
	 * in the add_settings_section function.
	 */
	public function twitter_settings_callback() {

		$options = get_option('dvg_twitter_settings');
		var_dump($options);
		echo '<p>' . __( 'Modify Twitter Settings', 'david-vg' ) . '</p>';

	} // end general_options_callback

	/**
	 * This function provides a simple description for the Input Examples page.
	 *
	 * It's called from the 'dvg_theme_initialize_input_examples_options' function by being passed as a parameter
	 * in the add_settings_section function.
	 */
	public function input_examples_callback() {

		$options = get_option('dvg_input_examples');
		var_dump($options);
		echo '<p>' . __( 'Provides examples of the five basic element types.', 'david-vg' ) . '</p>';

	} // end general_options_callback


	/**
	 * Initializes the twitter settings by registering the Sections, Fields, and Settings.
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function initialize_twitter_settings() {

		if( false == get_option( 'dvg_twitter_settings' ) ) {
			$default_array = $this->default_twitter_settings();
			update_option( 'dvg_twitter_settings', $default_array );
		} // end if

		add_settings_section(
			'twitter_settings_section',					// ID used to identify this section and with which to register options
			__( 'Twitter Settings', 'david-vg' ),		// Title to be displayed on the administration page
			array( $this, 'twitter_settings_callback'),	// Callback used to render the description of the section
			'dvg_twitter_settings'						// Page on which to add this section of options
		);

		add_settings_field(
			'dvig_twitter_user',
			__( 'Twitter Username', 'david-vg' ),
			array( $this, 'twitter_user_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section'
		);

		add_settings_field(
			'dvg_twitter_consumer_key',
			__( 'Twitter Consumer Key', 'david-vg' ),
			array( $this, 'twitter_consumer_key_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section'
		);

		add_settings_field(
			'dvg_twitter_consumer_secret',
			__( 'Twitter Consumer Secret', 'david-vg' ),
			array( $this, 'twitter_consumer_secret_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section'
		);

		add_settings_field(
			'dvg_twitter_access_token',
			__( 'Twitter Access Token', 'david-vg' ),
			array( $this, 'twitter_access_token_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section'
		);

		add_settings_field(
			'dvg_twitter_access_token_secret',
			__( 'Twitter Access Token Secret', 'david-vg' ),
			array( $this, 'twitter_access_token_secret_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section'
		);

		add_settings_field(
			'dvg_twitter_include_retweets',
			__( 'Exclude Retweets?', 'david-vg' ),
			array( $this, 'twitter_include_retweets_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section'
		);

		add_settings_field(
			'dvg_twitter_include_replies',
			__( 'Exclude Replies?', 'david-vg' ),
			array( $this, 'twitter_include_replies_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section'
		);


		register_setting(
			'dvg_twitter_settings',
			'dvg_twitter_settings'
		);

	}


	/**
	 * Initializes the theme's input example by registering the Sections,
	 * Fields, and Settings. This particular group of options is used to demonstration
	 * validation and sanitization.
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function initialize_input_examples() {

		if( false == get_option( 'dvg_input_examples' ) ) {
			$default_array = $this->default_input_options();
			update_option( 'dvg_input_examples', $default_array );
		} // end if


		add_settings_section(
			'general_settings_section',			        // ID used to identify this section and with which to register options
			__( 'Display Options', 'wppb-demo' ),		// Title to be displayed on the administration page
			array( $this, 'general_options_callback'),	// Callback used to render the description of the section
			'wppb_demo_display_options'		            // Page on which to add this section of options
		);

		add_settings_field(
			'show_header',						        // ID used to identify the field throughout the theme
			__( 'Header', 'wppb-demo' ),				// The label to the left of the option interface element
			array( $this, 'toggle_header_callback'),	// The name of the function responsible for rendering the option interface
			'wppb_demo_display_options',	            // The page on which this option will be displayed
			'general_settings_section',			        // The name of the section to which this field belongs
			array(								        // The array of arguments to pass to the callback. In this case, just a description.
				__( 'Activate this setting to display the header.', 'wppb-demo' ),
			)
		);

		register_setting(
			'dvg_input_examples',
			'dvg_input_examples',
			array( $this, 'validate_input_examples')
		);

	}


	/**
	 * TWITTER SETTINGS
	 */
	public function twitter_user_callback() {

		// First, we read the social options collection
		$options = get_option( 'dvg_twitter_settings' );

		// Render the output
		echo '<input type="text" id="twitter_user" name="dvg_twitter_settings[twitter_user]" value="' . $options['twitter_user'] . '" />';

	} // end twitter_user_callback

	public function twitter_consumer_key_callback() {

		// First, we read the social options collection
		$options = get_option( 'dvg_twitter_settings' );

		// Render the output
		echo '<input type="text" id="twitter_consumer_key" name="dvg_twitter_settings[twitter_consumer_key]" value="' . $options['twitter_consumer_key'] . '" />';

	} // end twitter_consumer_key_callback

	public function twitter_consumer_secret_callback() {

		// First, we read the social options collection
		$options = get_option( 'dvg_twitter_settings' );

		// Render the output
		echo '<input type="text" id="twitter_consumer_secret" name="dvg_twitter_settings[twitter_consumer_secret]" value="' . $options['twitter_consumer_secret'] . '" />';

	} // end twitter_consumer_secret_callback

	public function twitter_access_token_callback() {

		// First, we read the social options collection
		$options = get_option( 'dvg_twitter_settings' );

		// Render the output
		echo '<input type="text" id="twitter_access_token" name="dvg_twitter_settings[twitter_access_token]" value="' . $options['twitter_access_token'] . '" />';

	} // end twitter_access_token_callback

	public function twitter_access_token_secret_callback() {

		// First, we read the social options collection
		$options = get_option( 'dvg_twitter_settings' );

		// Render the output
		echo '<input type="text" id="twitter_access_token_secret" name="dvg_twitter_settings[twitter_access_token_secret]" value="' . $options['twitter_access_token_secret'] . '" />';

	} // end twitter_access_token_secret_callback

	public function twitter_include_retweets_callback() {

		$options = get_option( 'dvg_twitter_settings' );

		$html = '<input type="checkbox" id="exclude_retweets" name="dvg_twitter_settings[exclude_retweets]" value="1"' . checked( 1, $options['exclude_retweets'], false ) . '/>';
		$html .= '<label for="exclude_retweets">Should retweets be excluded?</label>';

		echo $html;

	} // end twitter_include_retweets_callback

	public function twitter_include_replies_callback() {

		$options = get_option( 'dvg_twitter_settings' );

		$html = '<input type="checkbox" id="exclude_replies" name="dvg_twitter_settings[exclude_replies]" value="1"' . checked( 1, $options['exclude_replies'], false ) . '/>';
		$html .= '<label for="exclude_replies">Should @replies be excluded?</label>';

		echo $html;

	} // end twitter_include_replies_callback



	/**
	 * Sanitization callback
	 *
	 * @params	$input	The unsanitized collection of options.
	 *
	 * @returns			The collection of sanitized values.
	 */
	public function validate_input_examples( $input ) {

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.

			if( isset( $input[$key] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );

			}

		}

		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'validate_input_examples', $output, $input );
	} // end validate_input_examples

}