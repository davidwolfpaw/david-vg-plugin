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
class David_VG_Daily {

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
    private $option_name = 'dvg_daily';


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
            'name'                  => _x( 'Dailies Stream', 'Post Type General Name', 'david-vg' ),
            'singular_name'         => _x( 'Dailies', 'Post Type Singular Name', 'david-vg' ),
            'menu_name'             => __( 'Dailies', 'david-vg' ),
            'name_admin_bar'        => __( 'Dailies', 'david-vg' ),
            'archives'              => __( 'Dailies Archives', 'david-vg' ),
            'parent_item_colon'     => __( 'Parent Dailies:', 'david-vg' ),
            'all_items'             => __( 'Dailies', 'david-vg' ),
            'add_new_item'          => __( 'Add New Dailies', 'david-vg' ),
            'add_new'               => __( 'Add New', 'david-vg' ),
            'new_item'              => __( 'New Dailies', 'david-vg' ),
            'edit_item'             => __( 'Edit Dailies', 'david-vg' ),
            'update_item'           => __( 'Update Dailies', 'david-vg' ),
            'view_item'             => __( 'View Dailies', 'david-vg' ),
            'search_items'          => __( 'Search Dailies', 'david-vg' ),
            'not_found'             => __( 'Not found', 'david-vg' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'david-vg' ),
            'featured_image'        => __( 'Featured Image', 'david-vg' ),
            'set_featured_image'    => __( 'Set featured image', 'david-vg' ),
            'remove_featured_image' => __( 'Remove featured image', 'david-vg' ),
            'use_featured_image'    => __( 'Use as featured image', 'david-vg' ),
            'insert_into_item'      => __( 'Insert into item', 'david-vg' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'david-vg' ),
            'items_list'            => __( 'Dailies list', 'david-vg' ),
            'items_list_navigation' => __( 'Dailies list navigation', 'david-vg' ),
            'filter_items_list'     => __( 'Filter items list', 'david-vg' ),
        );
        $args = array(
            'label'                 => __( 'Dailies', 'david-vg' ),
            'description'           => __( 'Stream of Dailies Data', 'david-vg' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'thumbnail', ),
            'taxonomies'            => array(),
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
        register_post_type( 'daily_stream', $args );

    }


    //Check and Schedule Cron job
    public function set_daily_schedule() {

        if (!wp_next_scheduled('import_daily_as_posts')) {
            wp_schedule_event(time(), 'five_minutes', 'import_daily_as_posts');
        }

    }


    /**
     * Import data as post
     *
     * @return posts
     */
    public function import_daily_as_posts() {

        // Get settings from Dailies settings page
        $post_settings_array = $this->get_post_settings_array();

        // Grab the ID of each save
        $daily_id = $daily['item_id'];
        $daily_post_args = array(
            'post_type' => 'daily_stream',
            'numberposts' => 5,
            );

        // Check to see if the save exists in the DB
        $daily_posts = get_posts( $daily_post_args );

        // Set the current date
        $current_date = current_time('j F Y');

        // Create an array of dates from the posts
        $dates = array();
        foreach ( $daily_posts as $daily_post ) {
            $dates[] = $daily_post->post_title;
        }

        // Check to see if post for current day exists
        $post_exist = in_array( $current_date, $dates );

        // Don't create a daily post if one already exists for today
        if( $post_exist ) continue;

        // Set save time as post publish date
        $publish_date_time = $this->set_publish_time();

        var_dump($publish_date_time);

        // Create post title as sanitized date
        $daily_post_title = strip_tags( html_entity_decode( $current_date ) );

        // Insert post parameters
        $insert_id = $this->create_post( $daily_post_title, $publish_date_time );


        // // Update save post meta
        // update_post_meta( $insert_id, '_save_id', $daily_id );
        // update_post_meta( $insert_id, '_save_url', $daily_url );

    }


    public function set_publish_time() {

        // Set the current date in Y-m-d format
        $current_date = current_time('Y-m-d');
        // Set time for 12:00:00 AM
        $time = '00:00:00';
        // Append time to current date and turn into unix timestamp
        $daily_post_time = strtotime( $current_date . ' ' . $time );
        // Convert publish date and time
        $publish_date_time = date_i18n( 'Y-m-d H:i:s', $daily_post_time );

        return $publish_date_time;

    }


    public function create_post( $daily_post_title, $publish_date_time ) {

        // Insert post parameters
        $data = array(
            'post_title'     => $daily_post_title,
            'post_date'      => $publish_date_time,
            'post_status'    => 'publish',
            'post_type'      => 'daily_stream',
            'post_author'    => 1,
            'comment_status' => 'closed'
            );

        $insert_id = wp_insert_post( $data );

        return $insert_id;

    }


    public function get_post_settings_array() {

        $post_settings_array = array();

        // $post_settings_array['daily_client_ID'] = get_option('dvg_daily_settings')['daily_client_ID'];

        return $post_settings_array;

    }


}
