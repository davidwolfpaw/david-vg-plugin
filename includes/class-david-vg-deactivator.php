<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://david.vg
 * @since      1.0.0
 *
 * @package    David_VG
 * @subpackage David_VG/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    David_VG
 * @subpackage David_VG/includes
 * @author     David Laietta <david@david.vg>
 */
class David_VG_Deactivator {

	/**
	 * All tasks to be run on deactivationk
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		register_deactivation_hook(__FILE__,'dvg_crontask_deactivation');

	}


	/**
	 * Remove all cron tasks scheduled by plugin
	 *
	 * @since    1.0.0
	 */
	public static function dvg_crontask_deactivation() {

        wp_clear_scheduled_hook( 'dvg_cron_hook' );

	}

}
