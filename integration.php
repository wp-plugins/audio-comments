<?php
/**
 * Integration file for Audio Comments Plugin with Wordpress
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

if(session_id() == "") { session_start(); }

/**
 * Audior General Settings
 */

//addRandomNumberToName helps you generate different names for the recordings. If set to 1 all the mp3 names will start with the recordName above and will end with a 6 digit random number. Values: 0 for disabled, 1 for enabled .
$addRandomNumberToName = 1;

//uploadURL is the path to the script that handles the upload of the mp3 file.
$uploadURL ='upload.php';

//canDownload  controls weather or not the user can download the MP3. Values: 0 for disabled, 1 for enabled.
$canDownload = 0;


/**
 * Audior Settings
 */

//recordName controls the name of the recording.
$recordName = $_SESSION['audior-settings']->file_name_prefix;

//weather or not the sound wave is shown, 1 for show, 0 for hidden.
$showSoundWave = $_SESSION['audior-settings']->show_sound_wave;

//Audior will place a marker at every markerDistance seconds. Set to 0 to disable.
$markerDistance = $_SESSION['audior-settings']->marker_distance;

//Path to the used language file.
$languageFile = 'translations/' . $_SESSION['audior-settings']->language_file;

//This setting controls whether or not all of the flash buttons are shown/hidden. Set to 0 to hide the buttons
$showButtons = 1;

//This setting controls whether or not the Save button is enabled/disabled. Set to 1 to disable it
$disableSaveButton = 1;

//This setting controls whether or not the Record again button is enabled/disabled. Set to 1 to disable it
$disableRecordAgainButton = $_SESSION['audior-settings']->disable_record_again_button;

//This setting controls the radius of the corners of the Audior background. Set this to 0 for square corners.
$bgCornerRadius = $_SESSION['audior-settings']->bg_corner_radius;

//This setting controls the background color for Audior.
$bgColor = '0x' . $_SESSION['audior-settings']->bg_color;

//This setting controls the border color of the Audior background.
$borderColor = '0x' . $_SESSION['audior-settings']->border_color;

//This setting controls the border width of the Audior background.
$borderWidth = $_SESSION['audior-settings']->border_width;

//This setting controls the color of the soundwave
$soundWaveColor = '0x' . $_SESSION['audior-settings']->sound_wave_color;

//This setting controls the color with which the generated soundwave is filled with upon playback to indicate the position within the recording
$playBackFillColor = '0x' . $_SESSION['audior-settings']->play_back_fill_color;

/**
 * Audior user Permissions
 */

// maxTimeLimit controls the maximum length of the recording.The values accepted are in seconds.
$maxTimeLimit = $_SESSION['audior-permissions']->time_limit;
