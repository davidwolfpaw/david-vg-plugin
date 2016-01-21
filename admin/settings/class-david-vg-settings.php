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
			'dvg_twitter_consumer_key'	=> '',
			'dvg_twitter_consumer_secret'	=> '',
			'dvg_twitter_access_token'	=> '',
			'dvg_twitter_access_token_secret'	=> '',
			'dvg_twitter_exclude_retweets'	=> '1',
			'dvg_twitter_exclude_replies'	=> '1',
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
			'dvg_twitter_exclude_retweets',
			__( 'Exclude Retweets?', 'david-vg' ),
			array( $this, 'checkbox_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'dvg_twitter_exclude_retweets', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'exclude_retweets', 'option_description' => 'Should retweets be excluded?' )
		);

		add_settings_field(
			'dvg_twitter_exclude_replies',
			__( 'Exclude Replies?', 'david-vg' ),
			array( $this, 'checkbox_input_callback'),
			'dvg_twitter_settings',
			'twitter_settings_section',
			array( 'label_for' => 'dvg_twitter_exclude_replies', 'option_group' => 'dvg_twitter_settings', 'option_id' => 'exclude_replies', 'option_description' => 'Should @replies be excluded?' )
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

		add_settings_field(
			'dvg_pocket_access_token',
			__( 'Pocket Access Token', 'david-vg' ),
			array( $this, 'pocket_access_token_callback'),
			'dvg_pocket_settings',
			'pocket_settings_section',
			array( 'label_for' => 'dvg_pocket_access_token', 'option_group' => 'dvg_pocket_settings', 'option_id' => 'pocket_access_token' )
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
		$html = '<input type="checkbox" id="' . $option_id . '" name="' . $option_name . '" value="1" ' . checked( 1, $options[$option_id], false ) . ' />';
		$html .= '<label for="' . $option_id . '">' . $option_description . '</label>';

		echo $html;

	}


	public function pocket_access_token_callback( $text_input ) {

		// Get arguments from setting
		$option_group = $text_input['option_group'];
		$option_id = $text_input['option_id'];
		$option_name = $option_group . '[' . $option_id . ']';

		// Get existing option from database
		$options = get_option( $option_group );

		$consumer_key = $options['pocket_consumer_key'];

        if ( empty( $consumer_key ) ) {
            _e( 'Please fill in your Pocket App Consumer Key', 'david-vg' );
        }

        echo '<input type="text" id="' . $option_id . '" name="' . $option_name . '" value="" />&nbsp;&nbsp;';
        echo '<input type="button" id="pocket_generate_access_token" class="button button-primary" value="Generate" onclick="pocketGenerateAccessToken(\'' . $consumer_key . '\');">';

	}


	public function pocket_generate_access_token() {

		$params = array(
            'consumerKey' => $_POST['consumerKey'],
            // 'consumerKey' => '50361-b51b548eed15a771bfda7136'
        );

        $pocket = new Pocket( $params );

        if ( isset( $_GET['authorized'] ) ) {
            // Convert the requestToken into an accessToken
            // Note that a requestToken can only be covnerted once
            // Thus refreshing this page will generate an auth error
            $user = $pocket->convertToken( $_GET['authorized'] );
            /*
             * $user['access_token']   the user's access token for calls to Pocket
             * $user['username']   the user's pocket username
             */

            // Set the user's access token to be used for all subsequent calls to the Pocket API
            $access_token = $pocket->setAccessToken( $user['access_token'] );

        	return $access_token;


            // // Retrieve the user's list of unread items (limit 5)
            // // http://getpocket.com/developer/docs/v3/retrieve for a list of params
            // $params = array(
            //     'state' => 'unread',
            //     'sort' => 'newest',
            //     'detailType' => 'simple',
            //     'count' => 5
            // );
            // $items = $pocket->retrieve( $params, $user['access_token'] );

        } else {
            // Attempt to detect the url of the current page to redirect back to
            // Normally you wouldn't do this
            $redirect = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https' : 'http') . '://'  . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?authorized=';

            // Request a token from Pocket
            $result = $pocket->requestToken($redirect);
            /*
             * $result['redirect_uri']     this is the URL to send the user to getpocket.com to authorize your app
             * $result['request_token']    this is the request_token which you will need to use to
             *                             obtain the user's access token after they have authorized your app
            */

            /*
             * This is a hack to redirect back to us with the requestToken
             * Normally you should save the 'request_token' in a session so it can be
             * retrieved when the user is redirected back to you
             */
            $result['redirect_uri'] = str_replace(
                urlencode('?authorized='),
                urlencode('?authorized=' . $result['request_token']),
                $result['redirect_uri']
            );
            // END HACK

            header('Location: ' . $result['redirect_uri']);
        }

	}


}