<?php
/*
Plugin Name: Audio Comments for WordPress
Plugin URI: http://www.audior.ec
Description: <a href='http://audior.ec' onclick="window.open(this.href,"_blank");return false;" target="_blank" title='Record mp3 in browser'>Audior is a simple mp3 recording tool for the web</a> that can record your microphone, encode the sound to MP3, and save it to your computer or upload it to a web server. Audior is developed in Flash and works from any browser. For a complete list of features check out the <a href='http://audior.ec/features' target="_blank" title='Audior Features'>Audior features page</a>. For pricing check out the <a href='http://audior.ec/order' title='Audior Pricing Page' onclick="window.open(this.href,"_blank");return false;" target="_blank">Audior pricing page</a>.
Author: NusoftHQ
Version: 1.0
Author URI: http://www.nusofthq.com


Copyright (C) 2009-2014 NusoftHQ

This WordPress Plugin is distributed under the terms of the GNU General Public License.
You can redistribute it and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation, either version 3 of the License, or any later version.

You should have received a copy of the GNU General Public License
along with this plugin.  If not, see <http://www.gnu.org/licenses/>.
*/

//DEFINES

if(session_id() == "") { session_start(); }
require_once(ABSPATH . 'wp-content/plugins/audio-comments/sql.php');

function  deleteAudiorComment($commentId) {
	global $wpdb;
	
	$uploadDir = wp_upload_dir();
	$uploadsPath = $uploadDir['basedir'] . '/audio-comments/';
	
	$audioComment = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "audior_comments WHERE comment_id =" . intval($commentId));
	
	if ($audioComment->id > 0) {
		//delete file
		@unlink($uploadsPath . $audioComment->file);
	
		//delete db record
		$wpdb->delete($wpdb->prefix . 'audior_comments', array('id' => $audioComment->id));
	}
}

function audiorDeleteFile() {
	global $wpdb;

	$uploadDir = wp_upload_dir();
	$uploadsPath = $uploadDir['basedir'] . '/audio-comments/';
	
	$audioComment = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "audior_comments WHERE id =" . esc_sql(intval($_POST['aid'])));

	if ($audioComment->id > 0) {
		//delete file
		@unlink($uploadsPath . $audioComment->file);
		
		//delete db record
		$wpdb->delete($wpdb->prefix . 'audior_comments', array('id' => $audioComment->id));
		
		//send msg to user
		echo 'The audio comment has been removed!';
	} else {
		echo 'invalid-id';
	}
	die();
}


function audiorCommentsJavaScript() {
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		jQuery('#audior-delete-file').click(function() {
			if (confirm('Are you sure you want to delete this audio comment?')) {
				var data = {
					action: 'audiorDeleteFile',
					aid: jQuery(this).attr('acid')
				};
			
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				$.post(ajaxurl, data, function(response) {
					jQuery('.admin-audior-player-wrapper').html(response);
				});
			}
			return false;
		});
	});
	</script>
	<?php 
}

function audiorCommentsAdminBox($comment) {
	global $wpdb;
	
	$uploadDir = wp_upload_dir();
	$uploadsPath = $uploadDir['basedir'] . '/audio-comments/';
	$filesPath = $uploadDir['baseurl'] . '/audio-comments/';
	
	$audioComment = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "audior_comments WHERE comment_id =" . $comment->comment_ID);
	$audioFile = $filesPath . $audioComment->file;
	
	if (file_exists($uploadsPath . $audioComment->file) && $audioComment->file != '') {
		$html .= <<<EOF
		<div class="admin-audior-player-wrapper">
			<div style="float:left;padding-right:15px;"><audio controls><source src="{$audioFile}" type="audio/mpeg">Your browser does not support the audio element.</audio></div>
			<a style="float:left;" class="button" href="#" id="audior-delete-file" acid="{$audioComment->id}">Delete audio file</a>
			<br style="clear:both;" />
		</div>
EOF;
	} else {
		$html = '<p>This comment doesn\'t have audio!</p>';
	}
	
	echo $html;
}

function addAudiorMetaBox() {
	add_meta_box('audior_comments_box', __('Audio Comment'), 'audiorCommentsAdminBox', 'comment', 'normal');
}

function showAudiorPlayer($text) {
	global $wpdb;
	
	$uploadDir = wp_upload_dir();
	$uploadsPath = $uploadDir['basedir'] . '/audio-comments/';
	$filesPath = $uploadDir['baseurl'] . '/audio-comments/';
	
	$audioComment = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "audior_comments WHERE comment_id =" . get_comment_ID());
	
	if (file_exists($uploadsPath . $audioComment->file) && $audioComment->file != '') {
		$html .= '<div class="audior-player-wrapper"><audio controls><source src="' . $filesPath . $audioComment->file . '" type="audio/mpeg">Your browser does not support the audio element.</audio></div>';
	}
	
	return $text . $html;
}

function saveAudior($commentId, $status) {
	global $current_user;
	global $wpdb;
	global $blog_id;
	get_currentuserinfo();
	
	$uploadDir = wp_upload_dir();
	$uploadsPath = $uploadDir['basedir'] . '/audio-comments/';
	
	if (file_exists($uploadsPath . $_POST['audior-file']) && $_POST['audior-file'] != '') {
		$insert = "INSERT INTO " . $wpdb->prefix . "audior_comments (comment_id, comment_post_id, user_id, file, date) VALUES 
					('" . $commentId . "', '" . esc_sql($_POST['comment_post_ID']) . "', '".$current_user->ID."', '" . esc_sql($_POST['audior-file']) . "', '" . time() . "')";
		$results = $wpdb->query($insert);
	}
}

function audior($commentField) {
	//define vars
	$flashVersion = '12.0.0.44';
	
	//get wordpress and user info
	global $current_user;
	global $wpdb;
	global $blog_id;
	get_currentuserinfo();
	
	$pluginPath = plugin_dir_url(__FILE__);
	$uploadDir = wp_upload_dir();
	$_SESSION['audior-upload-path'] = $uploadDir['basedir'] . '/audio-comments/';
	
	$audiorSettings = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "audior_settings LIMIT 1 ");
	$_SESSION['audior-settings'] = $audiorSettings[0];
	
	$uniqID = uniqid();
	$licenseKey= $audiorSettings[0]->license_key;
	
	//detect user role
	if (($current_user->ID != NULL) && ($current_user->ID != "") && ($current_user->ID > 0)) {
		if ($current_user->roles[0] != "") {
			$userRole = $current_user->roles[0];
		} else {
			$userRole = "networkuser";
		}
	} else {
		$userRole = "visitors";
	}
	
	//select user role permissions
	$query = "SELECT * FROM " . $wpdb->prefix . "audior_permissions WHERE user_role = '".$userRole."'";
	$userPermissions = $wpdb->get_results($query);
	$_SESSION['audior-permissions'] = $userPermissions[0];
	
	//this user role can't post audio comments
	if ($userPermissions[0]->enable_audior == 0) { return $commentField; }
	
	//check if audior exists
	if (!file_exists(dirname(__FILE__) . '/audior/Audior.swf')) { return $commentField . '<div class="audior-comments-wrapper"><label class="audior-label">Record audio comment</label><p>For the plugin to work, you need the Audior files to be copied to the plugin directory! You can buy it from <a href="http://audior.ec/order" title="Buy Audior" onclick="window.open(this.href); return false;">audior.ec</a> or review the <a href="http://wordpress.org/plugins/audio-comments/installation/" title="Audior documentation" onclick="window.open(this.href); return false;">Documentation!</a></p></div>'; }
	
	//html for select
	//$htmlSelect .= 'here we will show a select';
	
	//default html comment field
	$commentField = '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">Audio comment</textarea></p>';
	
	$logged = 0;
	if (($current_user->ID != NULL) && ($current_user->ID != "") && ($current_user->ID > 0)) {
		$logged = 1;
	}
	
	$html .= '<script type="text/javascript" src="'.$pluginPath.'audior/swfobject.js"></script>';
	$html .= '<script type="text/javascript" src="'.$pluginPath.'javascript/javascript.js"></script>';
	$html .=  <<<EOF
<script type="text/javascript">
	var swfVersionStr = "{$flashVersion}";
	var xiSwfUrlStr = "";
    
	var flashvars = {};
	flashvars.lstext="Loading..."; //you can provide a translation here for the "Laoding..." text taht shows up while this file and the external language file is loaded
	flashvars.recorderId = "recorder_{$uniqID}"; //set this var if you have multiple instances of Audior on a page and you want to identify them
	flashvars.userId ="user1";//this variable is sent back to upload.php when the user presses the [SAVE] button
	flashvars.licenseKey = "{$licenseKey}"; //licensekey variable, you get it when you purchase the software
	flashvars.sscode="php" //instructs Audior to use the PHP set of server side files (switch to sscode="aspx") to use the .NET/ASPX set of files
	flashvars.settingsFile = "audior_settings.php" //this setting instructs Audior what setting file to load. Either the static .XML or the dynamic .PHP that generates a dynamic xml.

	var params = {};
	params.quality = "high";
	params.bgcolor = "#ffffff";
	params.allowscriptaccess = "sameDomain";
	params.allowfullscreen = "true";
	params.base = "{$pluginPath}audior/";
	
	var attributes = {};
	attributes.id = "Audior";
	attributes.name = "Audior";
	attributes.align = "middle";
	
	swfobject.embedSWF("{$pluginPath}audior/Audior.swf", "audiorContent", "600", "140", swfVersionStr, xiSwfUrlStr, flashvars, params, attributes);
	swfobject.createCSS("#audiorContent", "display:block;text-align:left;");
EOF;
	$html .= 'var logged = ' . $logged . ';';
	$html .=  <<<EOF
	window.onload = function() {
		document.getElementById("submit").onclick = function() {
			
			if (logged == 0) {
				var author = document.getElementById('author').value;
				if (author == '') { alert('Please complete the author field!'); return false; }
			
				var email = document.getElementById('email').value;
				if (!validateEmail(email)) { alert('Please complete the e-mail field!'); return false; }
			}
		
			var commentText = document.getElementById('comment').value;
			if (commentText == '') { alert('Please complete the comment box'); return false; }
			
			if (startedRec == 0) {
				HTMLFormElement.prototype.submit.call(document.getElementById("commentform"));
			} else if (recording == 0) {
				document.Audior.save();
			} else {
				alert('Please Stop recording before posting the comment!');
			}
			return false;
		}
	}
</script>
EOF;
	
	$html .= '<div class="audior-comments-wrapper"><label class="audior-label">Record audio comment</label>';
	$html .= '<div id="audiorContent"><p>To view this page ensure that Adobe Flash Player version '.$flashVersion.' or greater is installed.</p><p><a href="http://www.adobe.com/go/getflashplayer" onclick="window.open(this.href); return false;">Get Adobe Flash Player</a></p></div>';
	$html .= '<input type="hidden" name="audior-file" id="audior-file" /></div>';
	
	return $htmlSelect.$commentField.$html;
}

function AudiorCommentsAdminConfing(){
	add_options_page('Audio Comments Settings', 'Audio Comments',  'manage_options', 'audio-comments/audior-settings.php');
}

register_activation_hook(__FILE__,'AudiorCommentsInstall');
add_action('admin_menu', 'AudiorCommentsAdminConfing');

//display audior in comment form
//add_action('comment_form_logged_in_after', 'audior');
add_action('comment_form_field_comment', 'audior');

//save audior file to disk
add_action('comment_post', 'saveAudior', 10, 2);

//show audior player
add_filter('comment_text', 'showAudiorPlayer');

//add box to adin comments area
add_action('add_meta_boxes', addAudiorMetaBox);

//bind javascript
add_action('admin_footer', 'audiorCommentsJavaScript');

//delete audio file when comment is deleted
add_action('delete_comment', 'deleteAudiorComment', 10, 1);

//bind function to ajax call
add_action('wp_ajax_audiorDeleteFile', 'audiorDeleteFile');
?>