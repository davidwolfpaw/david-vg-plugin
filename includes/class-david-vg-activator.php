<?php

/**
 * Fired during plugin activation
 *
 * @link       http://david.vg
 * @since      1.0.0
 *
 * @package    David_VG
 * @subpackage David_VG/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    David_VG
 * @subpackage David_VG/includes
 * @author     David Laietta <david@david.vg>
 */
class David_VG_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		register_activation_hook( __FILE__, 'dvg_crontask_activation' );

	}


	function dvg_crontask_activation(){

		if ( ! wp_next_scheduled( 'dvg_cron_hook' ) ) {
			wp_schedule_event( time(), 'five_minutes', 'dvg_cron_hook' );
		}

	}

}
