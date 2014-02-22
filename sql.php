<?php
/**
 * SQL file for Audio Comments Plugin
 *
 * @category	Comments
 * @package		Audio Comments Plugin for Wordpress
 * @author		Radu Patron <radu@nusofthq.com>
 * @copyright	2014 Nusofthq.com
 * @license		This Wordpress Plugin is distributed under the terms of the GNU General Public License V2.0.
 * 				you can redistribute it and/or modify it under the terms of the GNU General Public License V2.0
 * 				as published by the Free Software Foundation, either version 3 of the License, or any later version.
 * 				You should have received a copy of the GNU General Public License along with this plugin.  If not, see <http://www.gnu.org/licenses/>.
 * @link		http://www.audior.ec
 * @version		1.0
 */

function AudiorCommentsInstall(){
	global $wpdb;
	global $wp_roles;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$settingsTable = $wpdb->prefix . "audior_settings";
	$commentsTable = $wpdb->prefix . "audior_comments";
	$permissionsTable = $wpdb->prefix . "audior_permissions";

	if (mysql_query("DESCRIBE `".$settingsTable."`")) {
	
	} else {
	
		$sql = "CREATE TABLE " . $settingsTable . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		license_key varchar(500) DEFAULT '960' NOT NULL,
		file_name_prefix varchar(50) DEFAULT 'audio_recording_' NOT NULL,
		show_sound_wave tinyint(1) DEFAULT '1' NOT NULL,
		marker_distance int(11) DEFAULT '5' NOT NULL,
		language_file varchar(10) DEFAULT 'en.xml' NOT NULL,
		disable_record_again_button tinyint(1) DEFAULT '0' NOT NULL,
		bg_corner_radius tinyint(4) DEFAULT '15' NOT NULL,
		bg_color varchar(20) DEFAULT 'efefef' NOT NULL,
		border_color varchar(20) DEFAULT '999999' NOT NULL,
		border_width tinyint(2) DEFAULT '1' NOT NULL,
		sound_wave_color varchar(20) DEFAULT '333333' NOT NULL,
		play_back_fill_color varchar(20) DEFAULT 'FA5223' NOT NULL,
		UNIQUE KEY id (id)
		);
		CREATE TABLE " . $commentsTable . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		comment_id int(11) NOT NULL,
		comment_post_id int(11) NOT NULL,
		user_id int(11) NOT NULL,
		file varchar(255) NOT NULL,
		date int(11) NOT NULL,
		UNIQUE KEY id (id)
		);
		CREATE TABLE " . $permissionsTable . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		user_role varchar(50) DEFAULT '0' NOT NULL,
		enable_audior tinyint(1) DEFAULT '1' NOT NULL,
		time_limit int(11) DEFAULT '300' NOT NULL,
		UNIQUE KEY id (id)
		);";

		
		dbDelta($sql);
			
		$insert = "INSERT INTO " . $settingsTable .
			" (license_key, file_name_prefix, show_sound_wave, marker_distance, language_file, disable_record_again_button, bg_corner_radius, 
				bg_color, border_color, border_width, sound_wave_color, play_back_fill_color) VALUES ('','audio_recording_','1', '5', 'en.xml', '0', '15', 'efefef', '999999', '1', '333333', 'FA5223')";
		$results = $wpdb->query($insert);
		
		
		$user_roles = array();
		foreach($wp_roles->roles as $role => $details){
			$user_roles[$role] = $details["name"];
		}
		$user_roles['visitors'] = "Visitors";
		$user_roles['networkuser'] = "Network user";
		
		//insert data for users role
		foreach($user_roles as $key=>$value){
			$insert = "INSERT INTO " . $permissionsTable . " (user_role, enable_audior, time_limit) VALUES ('" . $key . "', '1', '300')";
			$results = $wpdb->query($insert);
		}
	}
	
	//create upload dir
	$uploadDir = wp_upload_dir();
	$uploadsPath = $uploadDir['basedir'];
	if (!is_dir($uploadsPath . '/audio-comments')) {
		mkdir($uploadsPath . '/audio-comments', 0755);
	}
}
?>