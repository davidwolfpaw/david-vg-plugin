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

// $admin_path = realpath(__DIR__ . '/../..');
// require_once $admin_path . '/includes/streams/pocket/Pocket.php';

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
			'twitter_user'	=> '',
			'twitter_consumer_key'	=> '',
			'twitter_consumer_secret'	=> '',
			'twitter_access_token'	=> '',
			'twitter_access_token_secret'	=> '',
			'twitter_exclude_retweets'	=> 'checked',
			'twitter_exclude_replies'	=> 'checked',
		);

		return  $defaults;

	}

	public function default_pocket_settings() {

		$defaults = array(
			'pocket_consumer_key'	=> '',
			'pocket_request_token'	=> '',
			'pocket_access_token'	=> '',
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
			'twitter_user',
			__( 'Twitter Username', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'twitter_user', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_user' )
		);

		add_settings_field(
			'twitter_consumer_key',
			__( 'Twitter Consumer Key', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'twitter_consumer_key', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_consumer_key' )
		);

		add_settings_field(
			'twitter_consumer_secret',
			__( 'Twitter Consumer Secret', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'twitter_consumer_secret', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_consumer_secret' )
		);

		add_settings_field(
			'twitter_access_token',
			__( 'Twitter Access Token', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'twitter_access_token', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_access_token' )
		);

		add_settings_field(
			'twitter_access_token_secret',
			__( 'Twitter Access Token Secret', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'twitter_access_token_secret', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_access_token_secret' )
		);

		add_settings_field(
			'twitter_exclude_retweets',
			__( 'Exclude Retweets?', 'david-vg' ),
			array( $this, 'checkbox_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'twitter_exclude_retweets', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_exclude_retweets', 'option_description' => 'Should retweets be excluded?' )
		);

		add_settings_field(
			'twitter_exclude_replies',
			__( 'Exclude Replies?', 'david-vg' ),
			array( $this, 'checkbox_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'twitter_exclude_replies', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'twitter_exclude_replies', 'option_description' => 'Should @replies be excluded?' )
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
			'pocket_consumer_key',
			__( 'Pocket Consumer Key', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_pocket_settings',
			'pocket_settings_section',
			array( 'label_for' => 'pocket_consumer_key', 'option_group' => 'dvg_pocket_settings', 'option_id' => 'pocket_consumer_key' )
		);

		add_settings_field(
			'pocket_access_token',
			__( 'Pocket Access Token', 'david-vg' ),
			array( $this, 'text_input_callback'),
			'dvg_pocket_settings',
			'pocket_settings_section',
			array( 'label_for' => 'pocket_access_token', 'option_group' => 'dvg_pocket_settings', 'option_id' => 'pocket_access_token' )
		);

		// add_settings_field(
		// 	'pocket_request_token',
		// 	__( 'Pocket Request Token', 'david-vg' ),
		// 	array( $this, 'pocket_request_token_callback'),
		// 	'dvg_pocket_settings',
		// 	'pocket_settings_section',
		// 	array( 'label_for' => 'pocket_request_token', 'option_group' => 'dvg_pocket_settings', 'option_id' => 'pocket_request_token' )
		// );

		// add_settings_field(
		// 	'pocket_access_token',
		// 	__( 'Pocket Access Token', 'david-vg' ),
		// 	array( $this, 'pocket_access_token_callback'),
		// 	'dvg_pocket_settings',
		// 	'pocket_settings_section',
		// 	array( 'label_for' => 'pocket_access_token', 'option_group' => 'dvg_pocket_settings', 'option_id' => 'pocket_access_token' )
		// );


		register_setting(
			'dvg_pocket_settings',
			'dvg_pocket_settings',
			array( $this, 'validate_inputs' )
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
		echo '<input type="text" id="' . $option_id . '" name="' . $option_name . '" value="' . $options[$option_id] . '" />';

	}

	public function checkbox_input_callback( $checkbox_input ) {

		// Get arguments from setting
		$option_group = $checkbox_input['option_group'];
		$option_id = $checkbox_input['option_id'];
		$option_name = $option_group . '[' . $option_id . ']';
		$option_description = $checkbox_input['option_description'];

		// Get existing option from database
		$options = get_option( $option_group );

		// Render the output
		$input = '';
		$input .= '<input type="checkbox" id="' . $option_id . '" name="' . $option_name . '" value="1" ' . checked( isset($options[$option_id]), 1, false ) . ' />';
		$input .= '<label for="' . $option_id . '">' . $option_description . '</label>';

		echo $input;

	}


	// Validate inputs
	public function validate_inputs( $input ) {
		// Create our array for storing the validated options
		$output = array();
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
			}
			// elseif( $input[$key] === NULL ) {
			// 	$output[$key] = '';
			// }
		} // end foreach
		// Return the array processing any additional functions filtered by this action
		return apply_filters( 'validate_inputs', $output, $input );
	}

}