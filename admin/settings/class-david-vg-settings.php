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
			array( $this, 'render_settings_page_content'),
			'dashicons-admin-settings',
			3.14159
		);

		add_submenu_page(
			$this->plugin_name,
			'DavidVG Settings',
			'Settings',
			'manage_options',
			$this->plugin_name,
			''
		);

	}


	/**
	 * Provide default values for the Twitter options
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

	public function default_pocket_settings() {

		$defaults = array(
			'dvg_pocket_consumer_key'	=> '',
		);

		return  $defaults;

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
			} else if( $active_tab == 'pocket_settings' ) {
				$active_tab = 'pocket_settings';
			} else if( $active_tab == 'input_examples' ) {
				$active_tab = 'input_examples';
			} else {
				$active_tab = 'twitter_settings';
			}
			?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=david-vg&tab=twitter_settings" class="nav-tab <?php echo $active_tab == 'twitter_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Twitter', 'david-vg' ); ?></a>
				<a href="?page=david-vg&tab=pocket_settings" class="nav-tab <?php echo $active_tab == 'pocket_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Pocket', 'david-vg' ); ?></a>
				<a href="?page=david-vg&tab=input_examples" class="nav-tab <?php echo $active_tab == 'input_examples' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Input Examples', 'david-vg' ); ?></a>
			</h2>

			<form method="post" action="options.php">
				<?php

				if( $active_tab == 'twitter_settings' ) {

					settings_fields( 'dvg_twitter_settings' );
					do_settings_sections( 'dvg_twitter_settings' );

				} elseif( $active_tab == 'pocket_settings' ) {

					settings_fields( 'dvg_pocket_settings' );
					do_settings_sections( 'dvg_pocket_settings' );

				} else {

					settings_fields( 'dvg_input_examples' );
					do_settings_sections( 'dvg_input_examples' );

				}

				submit_button();

				?>
			</form>

		</div><!-- /.wrap -->
	<?php
	}


	/**
	 * This function provides a simple description for the Social Options page.
	 */
	public function twitter_settings_callback() {

		$options = get_option('dvg_twitter_settings');
		var_dump($options);
		echo '<p>' . __( 'Modify Twitter Settings', 'david-vg' ) . '</p>';

	}

	public function pocket_settings_callback() {

		$options = get_option('dvg_pocket_settings');
		var_dump($options);
		echo '<p>' . __( 'Modify Pocket Settings', 'david-vg' ) . '</p>';

	}


	/**
	 * Initializes the Twitter settings by registering the Sections, Fields, and Settings.
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function initialize_twitter_settings() {

		if( false == get_option( 'dvg_twitter_settings' ) ) {
			$default_array = $this->default_twitter_settings();
			update_option( 'dvg_twitter_settings', $default_array );
		}

		add_settings_section(
			'twitter_settings_section',					// ID used to identify this section and with which to register options
			__( 'Twitter Settings', 'david-vg' ),		// Title to be displayed on the administration page
			array( $this, 'twitter_settings_callback'),	// Callback used to render the description of the section
			'dvg_twitter_settings'						// Page on which to add this section of options
		);

		add_settings_field(
			'dvg_twitter_user',
			__( 'Twitter Username', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'dvg_twitter_user', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_user' )
		);

		add_settings_field(
			'dvg_twitter_consumer_key',
			__( 'Twitter Consumer Key', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'dvg_twitter_consumer_key', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_consumer_key' )
		);

		add_settings_field(
			'dvg_twitter_consumer_secret',
			__( 'Twitter Consumer Secret', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'dvg_twitter_consumer_secret', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_consumer_secret' )
		);

		add_settings_field(
			'dvg_twitter_access_token',
			__( 'Twitter Access Token', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'dvg_twitter_access_token', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_access_token' )
		);

		add_settings_field(
			'dvg_twitter_access_token_secret',
			__( 'Twitter Access Token Secret', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'dvg_twitter_access_token_secret', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_access_token_secret' )
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
	 * Initializes the Pocket settings by registering the Sections, Fields, and Settings.
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function initialize_pocket_settings() {

		if( false == get_option( 'dvg_pocket_settings' ) ) {
			$default_array = $this->default_pocket_settings();
			update_option( 'dvg_pocket_settings', $default_array );
		}

		add_settings_section(
			'pocket_settings_section',
			__( 'Pocket Settings', 'david-vg' ),
			array( $this, 'pocket_settings_callback'),
			'dvg_pocket_settings'
		);

		add_settings_field(
			'dvg_pocket_consumer_key',
			__( 'Pocket Consumer Key', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_pocket_settings',
			'pocket_settings_section',
			array( 'label_for' => 'dvg_pocket_consumer_key', 'option_group' => 'dvg_pocket_settings', 'option_id' => 'pocket_consumer_key' )
		);


		register_setting(
			'dvg_pocket_settings',
			'dvg_pocket_settings'
		);

	}





	/**
	 * Input Callbacks
	 */
	public function text_input_callback( $text_input ) {

		// Get arguments from setting
		$option_group = $text_input['option_group'];
		$option_id = $text_input['option_id'];
		$option_name = $option_group . '[' . $option_id . ']';

		// Get existing option from database
		$options = get_option( $option_group );

		// Render the output
		echo '<input type="text" id="' . $option_id . '" name="' . $option_name . ']" value="' . $options[$option_id] . '" />';

	}

	/**
	 * TWITTER SETTINGS
	 */
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