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
require_once plugin_dir_path( __FILE__ ) . 'twitter/twitteroauth.php';

/**
 *
 * @package    David_VG
 * @subpackage David_VG/includes
 * @author     David Laietta <david@david.vg>
 */
class David_VG_Twitter {

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
    private $option_name = 'dvg_twitter';


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
            'name'                  => _x( 'Twitter Stream', 'Post Type General Name', 'david-vg' ),
            'singular_name'         => _x( 'Twitter', 'Post Type Singular Name', 'david-vg' ),
            'menu_name'             => __( 'Twitter', 'david-vg' ),
            'name_admin_bar'        => __( 'Twitter', 'david-vg' ),
            'archives'              => __( 'Twitter Archives', 'david-vg' ),
            'parent_item_colon'     => __( 'Parent Twitter:', 'david-vg' ),
            'all_items'             => __( 'Twitter', 'david-vg' ),
            'add_new_item'          => __( 'Add New Twitter', 'david-vg' ),
            'add_new'               => __( 'Add New', 'david-vg' ),
            'new_item'              => __( 'New Twitter', 'david-vg' ),
            'edit_item'             => __( 'Edit Twitter', 'david-vg' ),
            'update_item'           => __( 'Update Twitter', 'david-vg' ),
            'view_item'             => __( 'View Twitter', 'david-vg' ),
            'search_items'          => __( 'Search Twitter', 'david-vg' ),
            'not_found'             => __( 'Not found', 'david-vg' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'david-vg' ),
            'featured_image'        => __( 'Featured Image', 'david-vg' ),
            'set_featured_image'    => __( 'Set featured image', 'david-vg' ),
            'remove_featured_image' => __( 'Remove featured image', 'david-vg' ),
            'use_featured_image'    => __( 'Use as featured image', 'david-vg' ),
            'insert_into_item'      => __( 'Insert into item', 'david-vg' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'david-vg' ),
            'items_list'            => __( 'Twitter list', 'david-vg' ),
            'items_list_navigation' => __( 'Twitter list navigation', 'david-vg' ),
            'filter_items_list'     => __( 'Filter items list', 'david-vg' ),
        );
        $args = array(
            'label'                 => __( 'Twitter', 'david-vg' ),
            'description'           => __( 'Stream of Twitter Data', 'david-vg' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'author', 'thumbnail', ),
            'taxonomies'            => array( 'category', 'post_tag' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => $this->plugin_name,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-twitter',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );
        register_post_type( 'twitter_stream', $args );

    }

        /*= Add once 5 minute interval to wp schedules
    -------------------------------------------------- */
    public function import_interval_minutes($interval) {

        $interval_time = 300;
        $interval['five_minutes'] = array('interval' => $interval_time, 'display' => __('Every 5 minutes') );
        return $interval;

    }

    //Check and Schedule Cron job
    public function set_twitter_schedule() {

        if (!wp_next_scheduled('import_tweets_as_posts')) {
            wp_schedule_event(time(), 'five_minutes', 'import_tweets_as_posts');
        }

    }


    /**
     * Import data as post
     *
     * @return posts
     */
    public function import_tweets_as_posts() {

        // Get settings from Twitter settings page
        $post_settings_array = $this->get_post_settings_array();

        // Connect to Twitter OAuth
        $connection = $this->connect_to_twitter( $post_settings_array );

        // Create $tweet_api_url from settings
        $tweet_api_url = $this->create_tweet_api_url( $post_settings_array );

        // Now let's grab some tweets!
        $tweets = $connection->get($tweet_api_url);

        // Let's play with the tweets!
        if( $tweets ){

            foreach( $tweets as $tweet ) {

                // Grab the ID of each tweet
                $tweet_id = $tweet->id_str;
                $post_exist_args = array(
                    'post_type' => 'twitter_stream',
                    'meta_key' => '_tweet_id',
                    'meta_value' => $tweet_id,
                    );

                // Check to see if the tweet exists in the DB
                $post_exist = get_posts( $post_exist_args );

                // Do Nothing with tweets that exist in the DB already
                if( $post_exist ) continue;

                // Do nothing with retweets if posting them is disabled
                if( $post_settings_array['exclude_retweets'] == 1 && $tweet->retweeted_status ) continue;

                // Convert tweet links into usable links
                $tweet_text = $this->convert_tweet_links( $tweet );

                // Convert @ to follow
                $tweet_text = $this->convert_replies_to_follows( $tweet_text );

                // Link hashtags to search queries
                $tweet_text = $this->convert_hashtags_to_search( $tweet, $tweet_text );

                // Set tweet time as post publish date
                $publish_date_time = $this->set_publish_time( $tweet );

                // Create post title as sanitized tweet text
                $twitter_post_title = strip_tags( html_entity_decode( $tweet_text ) );

                // Insert post parameters
                $insert_id = $this->create_post( $tweet_text, $twitter_post_title, $publish_date_time );

                // Add featured image to post
                $this->create_featured_image( $tweet, $insert_id );

                // Tweet's original URL
                $tweet_url  = $tweet_url = 'https://twitter.com/' . $tweet->user->screen_name . '/status/' . $tweet_id;

                // Update tweet post meta for the ID and URL
                update_post_meta( $insert_id, '_tweet_id', $tweet_id );
                update_post_meta( $insert_id, '_tweet_url', $tweet_url );

            }

        }

    }


    public function connect_to_twitter( $post_settings_array ) {

        $connection = new TwitterOAuth(
            $post_settings_array['consumerkey'],
            $post_settings_array['consumersecret'],
            $post_settings_array['accesstoken'],
            $post_settings_array['accesstokensecret']
            );

        return $connection;

    }



    public function create_tweet_api_url( $post_settings_array ){

        // Create $tweet_api_url from settings
        $tweet_api_url = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $post_settings_array['twitteruser'] . '&count=10';


        if( isset( $post_settings_array['exclude_retweets'] ) ) {
            if( $post_settings_array['exclude_retweets'] == 1 ) {
                $tweet_api_url .= '&include_rts=false';
            }
        }

        if( isset( $post_settings_array['exclude_replies'] ) ) {
            if( $post_settings_array['exclude_replies'] == 1 ) {
                $tweet_api_url .= '&exclude_replies=true';
            }
        }

        // Get the most recent tweet to add the ID to the URL
        $args = array(
            'posts_per_page' => 1,
            'post_type' => 'twitter_stream',
            'meta_key' => '_tweet_id',
            'order' => 'DESC'
            );

        $posts = get_posts( $args );
        if( $posts ) {

            foreach($posts as $post){
                $post_tweet_id = get_post_meta($post->ID, '_tweet_id', true);
            }

            // Get twitter feeds after the recent tweet (by id) in WordPress database
            if($post_tweet_id){
                $tweet_api_url .= '&since_id=' . $post_tweet_id;
            }

        }

        return $tweet_api_url;

    }

    // TODO: Display username feature
    public function convert_tweet_links( $tweet ) {

        // Convert tweet links into usable links
        $pattern = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        $replace = '<a href="${0}" target="_blank">${0}</a>';
        $tweet_text = $tweet->text;

        if($tweet->retweeted_status) {

            $display_username = get_option('dvg_display_retweets_username');
            $tweet_text = "RT ";

            if($display_username=='yes'){
                $tweet_text .= $tweet->retweeted_status->user->name .' ';
            }

            $tweet_text .= "@".$tweet->retweeted_status->user->screen_name .": ". $tweet->retweeted_status->text;

        }

        $tweet_text = preg_replace( $pattern, $replace, $tweet_text );

        return $tweet_text;

    }

    public function convert_replies_to_follows( $tweet_text ) {

        // Convert @ to follow
        $follow_pattern = '/(@([_a-z0-9\-]+))/i';
        $follow_replace = '<a href="https://twitter.com/${0}" target="_blank">${0}</a>';
        $tweet_text = preg_replace($follow_pattern, $follow_replace, $tweet_text);

        return $tweet_text;

    }

    public function convert_hashtags_to_search( $tweet, $tweet_text ) {

        // Link Search Querys under tweet text
        $hashtags = $tweet->entities->hashtags;

        if( $hashtags ){

            foreach( $hashtags as $hashtag ){

                $hashFindPattern = '/#' . $hashtag->text . '/';
                $hashUrl = 'https://twitter.com/hashtag/' . $hashtag->text . '?src=hash';
                $hashReplace = '<a href="' . $hashUrl . '" target="_blank">#' . $hashtag->text . '</a>';
                $tweet_text = preg_replace( $hashFindPattern, $hashReplace, $tweet_text );

            }

        }

        return $tweet_text;

    }

    public function create_featured_image( $tweet, $insert_id ) {

        // Add Featured Image to Post
        $tweet_media = $tweet->entities->media;

        if( $tweet_media && $insert_id ) {

            $tweet_media_url = $tweet_media[0]->media_url; // Define the image URL here
            $upload_dir = wp_upload_dir(); // Set upload folder
            $image_data = file_get_contents($tweet_media_url); // Get image data
            $filename   = basename($tweet_media_url); // Create image file name

            // Check folder permission and define file location
            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            // Create the image  file on the server
            file_put_contents( $file, $image_data );

            // Check image file type
            $wp_filetype = wp_check_filetype( $filename, null );

            // Set attachment data
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name( $filename ),
                'post_content'   => '',
                'post_status'    => 'inherit'
                );

            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $file, $insert_id );

            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // And finally assign featured image to post
            set_post_thumbnail( $insert_id, $attach_id );

        }

    }

    // TODO - Update
    public function set_publish_time( $tweet ) {

        // Set tweet time as post publish date
        $tweet_created_at = strtotime( $tweet->created_at );
        $dvg_set_timezone = get_option( 'dvg_wp_time_as_published_date' );
        $tweet_post_time = $tweet_created_at + $tweet->user->utc_offset;

        if($dvg_set_timezone=='yes'){
            $wp_offset = get_option('gmt_offset');
            if($wp_offset){
                $tweet_post_time = $tweet_created_at + ($wp_offset * 3600);
            }
        }
        $publish_date_time = date_i18n( 'Y-m-d H:i:s', $tweet_post_time );

        return $publish_date_time;

    }

    public function create_post( $tweet_text, $twitter_post_title, $publish_date_time ) {

        // Insert post parameters
        $data = array(
            'post_content'   => $tweet_text,
            'post_title'     => $twitter_post_title,
            'post_status'    => 'publish',
            'post_type'      => 'twitter_stream',
            'post_author'    => 1,
            'post_date'      => $publish_date_time,
            'comment_status' => 'closed'
            );

        $insert_id = wp_insert_post( $data );

        return $insert_id;

    }


    public function get_post_settings_array() {

        $post_settings_array['twitteruser'] = get_option('dvg_twitter_settings')['twitter_user'];
        $post_settings_array['consumerkey'] = get_option('dvg_twitter_settings')['twitter_consumer_key'];
        $post_settings_array['consumersecret'] = get_option('dvg_twitter_settings')['twitter_consumer_secret'];
        $post_settings_array['accesstoken'] = get_option('dvg_twitter_settings')['twitter_access_token'];
        $post_settings_array['accesstokensecret'] = get_option('dvg_twitter_settings')['twitter_access_token_secret'];

        if(isset(get_option('dvg_twitter_settings')['twitter_exclude_retweets']))
            $post_settings_array['exclude_retweets'] = get_option('dvg_twitter_settings')['twitter_exclude_retweets'];
        if(isset(get_option('dvg_twitter_settings')['twitter_exclude_replies']))
            $post_settings_array['exclude_replies'] = get_option('dvg_twitter_settings')['twitter_exclude_replies'];

        return $post_settings_array;
        // var_dump($post_settings_array);

    }


}
