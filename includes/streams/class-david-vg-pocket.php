<?php

/**
 * Base Class for Datum
 *
 * @link       http://david.vg
 * @since      1.0.0
 *
 * @package    David_VG
 * @subpackage David_VG/includes
 */

// Require Twitter OAuth package
require_once plugin_dir_path( __FILE__ ) . 'pocket/Pocket.php';

/**
 *
 * @package    David_VG
 * @subpackage David_VG/includes
 * @author     David Laietta <david@david.vg>
 */
class David_VG_Pocket {

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
     * @since   1.0.0
     * @access  private
     * @var     string      $option_name    Option name of this plugin
     */
    private $option_name = 'dvg_pocket';


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
     * Create a Custom Post Type to manage this stream
     *
     * @param array $post
     *
     * @return int|WP_Error
     */
    public function create_custom_post_type() {

        // Register Custom Post Type

        $labels = array(
            'name'                  => _x( 'Pocket Stream', 'Post Type General Name', 'david-vg' ),
            'singular_name'         => _x( 'Pocket', 'Post Type Singular Name', 'david-vg' ),
            'menu_name'             => __( 'Pocket', 'david-vg' ),
            'name_admin_bar'        => __( 'Pocket', 'david-vg' ),
            'archives'              => __( 'Pocket Archives', 'david-vg' ),
            'parent_item_colon'     => __( 'Parent Pocket:', 'david-vg' ),
            'all_items'             => __( 'Pocket', 'david-vg' ),
            'add_new_item'          => __( 'Add New Pocket', 'david-vg' ),
            'add_new'               => __( 'Add New', 'david-vg' ),
            'new_item'              => __( 'New Pocket', 'david-vg' ),
            'edit_item'             => __( 'Edit Pocket', 'david-vg' ),
            'update_item'           => __( 'Update Pocket', 'david-vg' ),
            'view_item'             => __( 'View Pocket', 'david-vg' ),
            'search_items'          => __( 'Search Pocket', 'david-vg' ),
            'not_found'             => __( 'Not found', 'david-vg' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'david-vg' ),
            'featured_image'        => __( 'Featured Image', 'david-vg' ),
            'set_featured_image'    => __( 'Set featured image', 'david-vg' ),
            'remove_featured_image' => __( 'Remove featured image', 'david-vg' ),
            'use_featured_image'    => __( 'Use as featured image', 'david-vg' ),
            'insert_into_item'      => __( 'Insert into item', 'david-vg' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'david-vg' ),
            'items_list'            => __( 'Pocket list', 'david-vg' ),
            'items_list_navigation' => __( 'Pocket list navigation', 'david-vg' ),
            'filter_items_list'     => __( 'Filter items list', 'david-vg' ),
        );
        $args = array(
            'label'                 => __( 'Pocket', 'david-vg' ),
            'description'           => __( 'Stream of Pocket Data', 'david-vg' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'author', 'thumbnail', ),
            'taxonomies'            => array( 'category', 'post_tag' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => $this->plugin_name,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-media-text',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );
        register_post_type( 'pocket_stream', $args );

    }


    //Check and Schedule Cron job
    public function set_twitter_schedule() {

        if (!wp_next_scheduled('import_pocket_as_posts')) {
            wp_schedule_event(time(), 'five_minutes', 'import_pocket_as_posts');
        }

    }


    /**
     * Import data as post
     *
     * @return posts
     */
    public function import_pocket_as_posts() {

        // Get settings from Pocket settings page
        $post_settings_array = $this->get_post_settings_array();

        // Connect to Pocket OAuth
        $connection = $this->connect_to_pocket( $post_settings_array );

        echo '<pre>';

        var_dump($connection);

        echo '</pre>';



        // Retrieve the user's list of unread items (limit 5)
        // http://getpocket.com/developer/docs/v3/retrieve for a list of params
        // $params = array(
        //     'state' => 'unread',
        //     'sort' => 'newest',
        //     'detailType' => 'simple',
        //     'count' => 5
        // );
        // $items = $connection->pocket->retrieve( $params, $user['access_token'] );
        // print_r($items);

        // // Create $tweet_api_url from settings
        // $tweet_api_url = $this->create_tweet_api_url( $post_settings_array );

        // // Now let's grab some tweets!
        // $tweets = $connection->get($tweet_api_url);
        // if( $post_settings_array['tweet_from'] == 'Search Query' ) {
        //     $tweets = $tweets->statuses;
        // }

        // // Let's play with the tweets!
        // if( $tweets ){

        //     foreach( $tweets as $tweet ) {

        //         // Grab the ID of each tweet
        //         $pocket_id = $tweet->id_str;
        //         $post_exist_args = array(
        //             'post_type' => 'pocket_stream',
        //             'meta_key' => '_pocket_id',
        //             'meta_value' => $pocket_id,
        //             );

        //         // Check to see if the tweet exists in the DB
        //         $post_exist = get_posts( $post_exist_args );

        //         // Do Nothing with tweets that exist in the DB already
        //         if( $post_exist ) continue;

        //         // Convert tweet links into usable links
        //         $tweet_text = $this->convert_tweet_links( $tweet );

        //         // Set tweet time as post publish date
        //         $publish_date_time = $this->set_publish_time( $tweet );

        //         // Create post title as sanitized tweet text
        //         $twitter_post_title = strip_tags( html_entity_decode( $tweet_text ) );

        //         // Insert post parameters
        //         $insert_id = $this->create_post( $tweet_text, $twitter_post_title, $publish_date_time );

        //         // Add featured image to post
        //         $this->create_featured_image( $tweet, $insert_id );

        //         // Tweet's original URL
        //         $tweet_url  = $tweet_url = 'https://twitter.com/' . $tweet->user->screen_name . '/status/' . $pocket_id;

        //         // Update tweet post meta for the ID and URL
        //         update_post_meta( $insert_id, '_pocket_id', $pocket_id );
        //         update_post_meta( $insert_id, '_tweet_url', $tweet_url );

        //     }

        // }

    }


    public function connect_to_pocket( $post_settings_array ) {

        $params = array(
            'consumerKey' => $post_settings_array['consumerkey'] // fill in your Pocket App Consumer Key
        );

        if ( empty( $params['consumerKey'] ) ) {
            die( __( 'Please fill in your Pocket App Consumer Key', 'david-vg' ) );
        }

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
            $pocket->setAccessToken( $user['access_token'] );


            // Retrieve the user's list of unread items (limit 5)
            // http://getpocket.com/developer/docs/v3/retrieve for a list of params
            $params = array(
                'state' => 'unread',
                'sort' => 'newest',
                'detailType' => 'simple',
                'count' => 5
            );
            $items = $pocket->retrieve( $params, $user['access_token'] );
            // print_r($items);

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

        return $items;

    }



    // // public function create_featured_image( $tweet, $insert_id ) {

    // //     // Add Featured Image to Post
    // //     $tweet_media = $tweet->entities->media;
    // //     if($tweet_media && $insert_id){

    // //         $tweet_media_url = $tweet_media[0]->media_url; // Define the image URL here
    // //         $upload_dir = wp_upload_dir(); // Set upload folder
    // //         $image_data = file_get_contents($tweet_media_url); // Get image data
    // //         $filename   = basename($tweet_media_url); // Create image file name

    // //         // Check folder permission and define file location
    // //         if( wp_mkdir_p( $upload_dir['path'] ) ) {
    // //             $file = $upload_dir['path'] . '/' . $filename;
    // //         } else {
    // //             $file = $upload_dir['basedir'] . '/' . $filename;
    // //         }

    // //         // Create the image  file on the server
    // //         file_put_contents( $file, $image_data );

    // //         // Check image file type
    // //         $wp_filetype = wp_check_filetype( $filename, null );

    // //         // Set attachment data
    // //         $attachment = array(
    // //             'post_mime_type' => $wp_filetype['type'],
    // //             'post_title'     => sanitize_file_name( $filename ),
    // //             'post_content'   => '',
    // //             'post_status'    => 'inherit'
    // //             );

    // //         // Create the attachment
    // //         $attach_id = wp_insert_attachment( $attachment, $file, $insert_id );

    // //         // Define attachment metadata
    // //         $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

    // //         // Assign metadata to attachment
    // //         wp_update_attachment_metadata( $attach_id, $attach_data );

    // //         // And finally assign featured image to post
    // //         set_post_thumbnail( $insert_id, $attach_id );

    // //     }

    // // }

    // // public function set_publish_time( $tweet ) {

    // //     // Set tweet time as post publish date
    // //     $tweet_created_at = strtotime($tweet->created_at);
    // //     $dvg_set_timezone = get_option('dvg_wp_time_as_published_date');
    // //     $tweet_post_time = $tweet_created_at + $tweet->user->utc_offset;

    // //     if($dvg_set_timezone=='yes'){
    // //         $wp_offset = get_option('gmt_offset');
    // //         if($wp_offset){
    // //             $tweet_post_time = $tweet_created_at + ($wp_offset * 3600);
    // //         }
    // //     }
    // //     $publish_date_time = date_i18n( 'Y-m-d H:i:s', $tweet_post_time );

    // //     return $publish_date_time;

    // // }

    // public function create_post( $tweet_text, $twitter_post_title, $publish_date_time ) {

    //     // Insert post parameters
    //     $data = array(
    //         'post_content'   => $tweet_text,
    //         'post_title'     => $twitter_post_title,
    //         'post_status'    => 'publish',
    //         'post_type'      => 'pocket_stream',
    //         'post_author'    => 1,
    //         'post_date'      => $publish_date_time,
    //         'comment_status' => 'closed'
    //         );

    //     $insert_id = wp_insert_post( $data );

    //     return $insert_id;

    // }


    public function get_post_settings_array() {

        $post_settings_array = array();

        $post_settings_array['consumerkey'] = get_option('dvg_pocket_settings')['pocket_consumer_key'];

        return $post_settings_array;

    }


}
