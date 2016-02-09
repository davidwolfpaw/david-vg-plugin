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

// require_once plugin_dir_path( __FILE__ ) . 'google/autoload.php';

/**
 *
 * @package    David_VG
 * @subpackage David_VG/includes
 * @author     David Laietta <david@david.vg>
 */
class David_VG_Google_Fit {

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
    private $option_name = 'dvg_google_fit';


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
            'name'                  => _x( 'Google Fit Stream', 'Post Type General Name', 'david-vg' ),
            'singular_name'         => _x( 'Google Fit', 'Post Type Singular Name', 'david-vg' ),
            'menu_name'             => __( 'Google Fit', 'david-vg' ),
            'name_admin_bar'        => __( 'Google Fit', 'david-vg' ),
            'archives'              => __( 'Google Fit Archives', 'david-vg' ),
            'parent_item_colon'     => __( 'Parent Google Fit:', 'david-vg' ),
            'all_items'             => __( 'Google Fit', 'david-vg' ),
            'add_new_item'          => __( 'Add New Google Fit', 'david-vg' ),
            'add_new'               => __( 'Add New', 'david-vg' ),
            'new_item'              => __( 'New Google Fit', 'david-vg' ),
            'edit_item'             => __( 'Edit Google Fit', 'david-vg' ),
            'update_item'           => __( 'Update Google Fit', 'david-vg' ),
            'view_item'             => __( 'View Google Fit', 'david-vg' ),
            'search_items'          => __( 'Search Google Fit', 'david-vg' ),
            'not_found'             => __( 'Not found', 'david-vg' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'david-vg' ),
            'featured_image'        => __( 'Featured Image', 'david-vg' ),
            'set_featured_image'    => __( 'Set featured image', 'david-vg' ),
            'remove_featured_image' => __( 'Remove featured image', 'david-vg' ),
            'use_featured_image'    => __( 'Use as featured image', 'david-vg' ),
            'insert_into_item'      => __( 'Insert into item', 'david-vg' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'david-vg' ),
            'items_list'            => __( 'Google Fit list', 'david-vg' ),
            'items_list_navigation' => __( 'Google Fit list navigation', 'david-vg' ),
            'filter_items_list'     => __( 'Filter items list', 'david-vg' ),
        );
        $args = array(
            'label'                 => __( 'Google Fit', 'david-vg' ),
            'description'           => __( 'Stream of Google Fit Data', 'david-vg' ),
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
        register_post_type( 'google_fit_stream', $args );

    }


    //Check and Schedule Cron job
    public function set_google_fit_schedule() {

        if (!wp_next_scheduled('import_google_fit_as_posts')) {
            wp_schedule_event(time(), 'five_minutes', 'import_google_fit_as_posts');
        }

    }


    /**
     * Import data as post
     *
     * @return posts
     */
    public function import_google_fit_as_posts() {

        // Get settings from Google Fit settings page
        $post_settings_array = $this->get_post_settings_array();

        // Connect to Google Fit OAuth
        // $fit_data = $this->connect_to_google_fit( $post_settings_array );

        // $client->setApplicationName("dvg");
        // $apiKey = "";

        // // Let's play with the fit_data!
        // if( $fit_data ){

        //     foreach( $fit_data['list'] as $save ) {

        //         // Grab the ID of each save
        //         $save_id = $save['item_id'];
        //         $post_exist_args = array(
        //             'post_type' => 'google_fit_stream',
        //             'meta_key' => '_save_id',
        //             'meta_value' => $save_id,
        //             );

        //         // Check to see if the save exists in the DB
        //         $post_exist = get_posts( $post_exist_args );

        //         // Do Nothing with fit_data that exist in the DB already
        //         if( $post_exist ) continue;

        //         // Set save time as post publish date
        //         $publish_date_time = $this->set_publish_time( $save );

        //         // Create post title as sanitized text
        //         $save_post_title = strip_tags( html_entity_decode( $save['resolved_title'] ) );

        //         // Create post content as sanitized text
        //         $save_post_content = strip_tags( html_entity_decode( $save['excerpt'] ) );

        //         // Insert post parameters
        //         $insert_id = $this->create_post( $save_post_content, $save_post_title, $publish_date_time );

        //         // Save's original URL
        //         $save_url  = $save['resolved_url'];

        //         // Update save post meta
        //         update_post_meta( $insert_id, '_save_id', $save_id );
        //         update_post_meta( $insert_id, '_save_url', $save_url );

        //     }

        // }

    }


    /**
     * Connect to Google Fit grab data
     *
     * @return $fit_data
     */
    public function connect_to_google_fit( $post_settings_array ) {


        return $fit_data;

    }


    // public function set_publish_time( $save ) {

    //     // Get time added to account in UTC
    //     $save_created_at = $save['time_added'];
    //     // Set GMT offset (number of hours times minutes times seconds)
    //     $gmt_offset = get_option('gmt_offset') * 60 * 60;
    //     $save_post_time = $save_created_at + $gmt_offset;
    //     // Convert publish date and time
    //     $publish_date_time = date_i18n( 'Y-m-d H:i:s', $save_post_time );

    //     return $publish_date_time;

    // }


    // public function create_post( $save_post_content, $save_post_title, $publish_date_time ) {

    //     // Insert post parameters
    //     $data = array(
    //         'post_content'   => $save_post_content,
    //         'post_title'     => $save_post_title,
    //         'post_status'    => 'publish',
    //         'post_type'      => 'google_fit_stream',
    //         'post_author'    => 1,
    //         'post_date'      => $publish_date_time,
    //         'comment_status' => 'closed'
    //         );

    //     $insert_id = wp_insert_post( $data );

    //     return $insert_id;

    // }


    public function get_post_settings_array() {

        $post_settings_array = array();

        $post_settings_array['google_fit_client_ID'] = get_option('dvg_google_fit_settings')['google_fit_client_ID'];
        $post_settings_array['google_fit_client_secret'] = get_option('dvg_google_fit_settings')['google_fit_client_secret'];

        return $post_settings_array;

    }


}
