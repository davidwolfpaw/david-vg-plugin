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

/**
 *
 * @package    David_VG
 * @subpackage David_VG/includes
 * @author     David Laietta <david@david.vg>
 */
class David_VG_Datum {

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
        // $this->post_type = create_custom_post_type();

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


    /**
     * Import data as post
     *
     * @param 
     *
     * @return 
     */
    public function import_as_posts() {

        $post_tweet_id;

        // Get settings from Twitter settings page
        $post_settings_array = $this->get_post_settings_array();

        // Create $tweet_api_url from settings
        if( $post_settings_array['tweet_from'] ==' Search Query' ) { // Import from search query

            $tweet_api_url = "https://api.twitter.com/1.1/search/tweets.json?q=". rawurlencode($post_settings_array['tweet_search_string']) ."&result_type=".$post_settings_array['search_result_type']."&count=".$post_settings_array['notweets'];

        } else { // Import from user timeline

            $tweet_api_url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$post_settings_array['twitteruser']."&count=".$post_settings_array['notweets'];

            if( $post_settings_array['import_retweets'] == 'no' ) {
                $tweet_api_url .= "&include_rts=false";
            }

            if( $post_settings_array['exclude_replies'] == 'yes' ) {
                $tweet_api_url .= "&exclude_replies=true";
            }

        }


        $args = array(
            'posts_per_page' => 1,
            'post_type' => $post_settings_array['post_type'],
            'meta_key' => '_tweet_id',
            'post_status' => $post_settings_array['post_status_check'],
            'order' => 'DESC'
            );

        $posts = get_posts( $args );
        if( $posts ) {

            foreach($posts as $post){
                $post_tweet_id = get_post_meta($post->ID, '_tweet_id', true);
            }

            if($post_tweet_id){
                $tweet_api_url .= "&since_id=".$post_tweet_id; // Get twitter feeds after the recent tweet (by id) in WordPress database
            }

        }

        // Connect to Twitter OAuth
        // $connection = new TwitterOAuth(
            // $post_settings_array['consumerkey'],
            // $post_settings_array['consumersecret'],
            // $post_settings_array['accesstoken'],
            // $post_settings_array['accesstokensecret']
            // );

        // $tweets = $connection->get($tweet_api_url);
        if( $post_settings_array['tweet_from'] == 'Search Query' ) {
            $tweets = $tweets->statuses;
        }

        if($tweets){

            foreach($tweets as $tweet){
                // $tweet_id = abs((int)$tweet->id);
                $tweet_id = $tweet->id_str;
                $post_exist_args = array(
                    'post_type' => $post_settings_array['post_type'],
                    'post_status' => $post_settings_array['post_status_check'],
                    'meta_key' => '_tweet_id',
                    'meta_value' => $tweet_id,
                    );

                $post_exist = get_posts( $post_exist_args );
                if($post_exist) continue; // Do Nothing
                if($post_settings_array['import_retweets'] == 'no' AND $tweet->retweeted_status) continue; // IF Exclude retweet option enabled

                // Convert links to real links.
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

                $tweet_text = preg_replace($pattern, $replace, $tweet_text);

                // Convert @ to follow
                $follow_pattern = '/(@([_a-z0-9\-]+))/i';
                $follow_replace = '<a href="https://twitter.com/${0}" target="_blank">${0}</a>';
                $tweet_text = preg_replace($follow_pattern, $follow_replace, $tweet_text);

                // Link Search Querys under tweet text
                $hashtags = $tweet->entities->hashtags;
                if($hashtags){

                    foreach($hashtags as $hashtag){

                        $hashFindPattern = "/#". $hashtag->text ."/";
                        $hashUrl = 'https://twitter.com/hashtag/'. $hashtag->text .'?src=hash';
                        $hashReplace = '<a href="'.$hashUrl.'" target="_blank">#'. $hashtag->text .'</a>';
                        $tweet_text = preg_replace($hashFindPattern, $hashReplace, $tweet_text);

                    }

                }

                // Set tweet time as post publish date
                $tweet_created_at = strtotime($tweet->created_at);
                $dvg_set_timezone = get_option('dvg_wp_time_as_published_date');
                $tweet_post_time = $tweet_created_at + $tweet->user->utc_offset;

                if($dvg_set_timezone=='yes'){
                    $wp_offset = get_option('gmt_offset');
                    if($wp_offset){
                        $tweet_post_time = $tweet_created_at + ($wp_offset * 3600);
                    }
                }
                $publish_date_time = date_i18n( 'Y-m-d H:i:s', $tweet_post_time );


                // Get full twitter text
                $twitter_post_title = strip_tags(html_entity_decode($tweet_text));

                // Add prefix to twitter post title
                if(get_option('dvg_post_title')){
                    $twitter_post_title = get_option('dvg_post_title') .' '. $twitter_post_title;
                }

                // Limit characters limit in twitter post title
                if(get_option('dvg_post_title_limit')){

                    $charLimit = get_option('dvg_post_title_limit');

                    if(strlen($twitter_post_title)<=$charLimit){
                        $twitter_post_title = $twitter_post_title;
                    } else {
                        $twitter_post_title = substr($twitter_post_title, 0, $charLimit).'...';
                    }

                }

                // Twitter Post's Comment status
                $comment_status = ($post_settings_array['post_comment_status']) ? $post_settings_array['post_comment_status'] : 'closed'; 

                // Insert post parameters
                $data = array(
                    'post_content'   => $tweet_text,
                    'post_title'     => $twitter_post_title,
                    'post_status'    => $post_settings_array['twitter_post_status'],
                    'post_type'      => $post_settings_array['post_type'],
                    'post_author'    => 1,
                    'post_date'      => $publish_date_time,
                    'comment_status' => $comment_status
                    );

                if($post_settings_array['post_type'] == 'post')
                    $data['post_category'] = array( $post_settings_array['twitter_posts_category'] );

                $insert_id = wp_insert_post($data);

                // Add Featured Image to Post
                $tweet_media = $tweet->entities->media;
                if($tweet_media AND $insert_id){

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

                //Tweet's Original URL
                $tweet_url  = $tweet_url = 'https://twitter.com/'.$tweet->user->screen_name .'/status/'. $tweet_id;

                // Update tweet meta
                update_post_meta($insert_id, '_tweet_id', $tweet_id); // Tweet id
                update_post_meta($insert_id, '_tweet_url', $tweet_url); //Tweet URL

            } //end foreach

        } // end if

    }


    public function get_post_settings_array() {

        $post_settings_array = array();

        $post_settings_array['tweet_from'] = get_option( 'dvg_tweet_from' );
        $post_settings_array['twitteruser'] = get_option( 'dvg_user_id' );
        $post_settings_array['tweet_search_string'] = get_option( 'dvg_search_string' );
        $post_settings_array['search_result_type'] = get_option( 'dvg_search_result_type' );

        $post_settings_array['consumerkey'] = get_option( 'dvg_consumer_key' );
        $post_settings_array['consumersecret'] = get_option( 'dvg_consumer_secret' );
        $post_settings_array['accesstoken'] = get_option( 'dvg_access_token' );
        $post_settings_array['accesstokensecret'] = get_option( 'dvg_access_token_secret' );

        $post_settings_array['notweets'] = ( get_option( 'dvg_tweets_count' ) ) ? get_option( 'dvg_tweets_count' ) : 30;
        $post_settings_array['twitter_posts_category'] = get_option( 'dvg_assigned_category' );

        $post_settings_array['twitter_post_status'] = get_option( 'dvg_post_status' );
        $post_settings_array['post_comment_status'] = get_option( 'post_comment_status' );
        $post_settings_array['import_retweets'] = get_option( 'dvg_import_retweets' );
        $post_settings_array['exclude_replies'] = get_option( 'dvg_exclude_replies' );

        $post_settings_array['post_status_check'] =  array( 'publish','pending','draft','auto-draft', 'future', 'private', 'inherit','schedule' );
        $post_settings_array['post_type'] = get_option( 'dvg_post_type' );

        return $post_settings_array;

    }





    /**
     * Register all Twitter settings
     *
     * @since  1.0.0
     */
    public function register_settings() {
        add_settings_section(
            $this->option_name . '_twitter',
            __( 'General', 'david-vg' ),
            array( $this, $this->option_name . '_twitter_cb' ),
            $this->plugin_name
        );
        add_settings_field(
            $this->option_name . '_position',
            __( 'Text position', 'david-vg' ),
            array( $this, $this->option_name . '_position_cb' ),
            $this->plugin_name,
            $this->option_name . '_twitter',
            array( 'label_for' => $this->option_name . '_position' )
        );
        add_settings_field(
            $this->option_name . '_day',
            __( 'Post is outdated after', 'david-vg' ),
            array( $this, $this->option_name . '_day_cb' ),
            $this->plugin_name,
            $this->option_name . '_twitter',
            array( 'label_for' => $this->option_name . '_day' )
        );
        register_setting( $this->plugin_name, $this->option_name . '_position', array( $this, $this->option_name . '_sanitize_position' ) );
        register_setting( $this->plugin_name, $this->option_name . '_day', 'intval' );
    }







    /**
     * Save a post to the stream. Returns the post ID or a WP_Error.
     *
     * @param array $post
     *
     * @return int|WP_Error
     */
    public function save(array $post) {

        if (!empty($post['ID'])) {
            return wp_update_post($post, true);
        }

        return wp_insert_post($post, true);

    }


    /**
     * Find a post using the given post ID.
     *
     * @param int $id
     *
     * @return WP_Post|null
     */
    public function find_by_id($id)
    {
        $query = new WP_Query(array(
            'p' => $id,
            'posts_per_page' => 1,
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ));
        $posts = $query->get_posts();

        return !empty($posts[0]) ? $posts[0] : null;
    }

    /**
     * Remove the given post from the repository.
     *
     * @param WP_Post $post
     * @param bool    $force
     */
    public function remove(WP_Post $post, $force = false)
    {
        wp_delete_post($post->ID, $force);
    }

}
