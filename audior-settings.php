<?php
/**
 * Settings file for Audio Comments Plugin
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

global $wp_roles;
global $wpdb;

$user_roles = array();

foreach($wp_roles->roles as $role => $details){
	$user_roles[$role] = $details["name"];
}
$user_roles['visitors'] = "Visitors";
if (is_multisite()){
	$user_roles['networkuser'] = "Network user";
}

$location = get_option('siteurl') . '/wp-admin/admin.php?page=audio-comments/audior-settings.php'; 
$settingsTable = $wpdb->prefix . "audior_settings";
$permissionsTable = $wpdb->prefix . "audior_permissions";

$generalSettingsInputs = array(
	'license_key' => array(
		'label' => 'License key',
		'type' => 'text',
	),
	'file_name_prefix' => array(
		'label' => 'File name prefix',
		'type' => 'small-text',
	),
	'show_sound_wave' => array(
		'label' => 'Show sound wave',
		'type' => 'select',
		'values' => array('1'=>'Yes', '0'=>'No'),
	),
	'marker_distance' => array(
		'label' => 'Marker distance',
		'type' => 'small-text',
	),
	'language_file' => array(
		'label' => 'Language file',
		'type' => 'small-text',
	),
	'disable_record_again_button' => array(
		'label' => 'Disable record again button',
		'type' => 'select',
		'values' => array('1'=>'Yes', '0'=>'No'),
	),
);

$designSettingsInputs = array(
	'bg_corner_radius' => array(
			'label' => 'Corner radius',
			'type' => 'small-text',
	),
	'bg_color' => array(
			'label' => 'Background color',
			'type' => 'small-text',
	),
	'border_color' => array(
			'label' => 'Border color',
			'type' => 'small-text',
	),
	'border_width' => array(
			'label' => 'Border width',
			'type' => 'small-text',
	),
	'sound_wave_color' => array(
			'label' => 'Sound wave color',
			'type' => 'small-text',
	),
	'play_back_fill_color' => array(
			'label' => 'Play back fill color',
			'type' => 'small-text',
	),
);

$permissionsInputs = array(
	'enable_audior' => array(
		'label' => 'Enable Audior Comments',
		'type' => 'checkbox',
	),
	'time_limit' => array(
		'label' => 'Max time limit',
		'type' => 'small-text',
	),
);

if (isset($_POST['audior-setting-submit'])) {
	
	$data = array();
	foreach ($generalSettingsInputs as $key=>$value) {
		$data[$key] = trim($_POST['general_setting_' . $key]);
	}
	
	foreach ($designSettingsInputs as $key=>$value) {
		$data[$key] = trim($_POST['design_setting_' . $key]);
	}
	
	$wpdb->update($settingsTable, $data, array('id'=>'1'));
	
	foreach ($user_roles as $user_role => $name) {
		$dataPermissions = array();
		foreach ($permissionsInputs as $key=>$value) {
			$dataPermissions[$key] = trim($_POST[$user_role . '_permissions_' . $key]);
		}
		
		$wpdb->update($permissionsTable, $dataPermissions, array('user_role'=>$user_role));
	}
}

$generalSettings = $wpdb->get_results( "SELECT * FROM ".$settingsTable . " LIMIT 1 ");
?>

<div class="wrap">
	<h2 style="margin-bottom:20px;">Audio Comments</h2>
</div>
<form name="form1" method="post" action="<?php echo $location; ?>">
	<table cellpadding="0" cellspacing="0" class="audior-table">
		
		<tr>
			<th></th>
			<?php foreach($user_roles as $role => $name){?>
				<th style="padding:0 5px !important;text-align:center;"><?php echo $name;?></th>
			<?php } ?>
		</tr>
		
		<tr><td colspan="15" style="text-align:left"><h3>Permissions</h3></td>
		<?php foreach($permissionsInputs as $key=>$value){ ?>
			
			<tr>
				<td style="text-align:left;padding:0px 0px 15px 0px;"><?php echo $value['label'];?></td>
				<?php 
					foreach ($user_roles as $user_role => $name){
						$user_permissions = $wpdb->get_results( "SELECT * FROM ". $permissionsTable . " WHERE user_role = '".$user_role."'" );
				?>
					<td style="padding:0px 5px 15px 5px;text-align:center;">
						<?php 
						switch ($value['type']) {
							case 'checkbox':
								?>
								<input type="checkbox" <?php  if($user_permissions[0]->$key){ echo 'checked="checked"';} ?> value="1" name="<?php echo strtolower($user_role);?>_permissions_<?php echo $key;?>" />
								<?php
								break;
							default:
							?>
							<input size="7" type="text" name="<?php echo strtolower($user_role);?>_permissions_<?php echo $key;?>" value="<?php echo $user_permissions[0]->$key; ?>" />
							<?php
						}
						?>
					</td>
				<?php }?>
			</tr>
		<?php }?>
		
		<tr><td colspan="5" style="text-align:left"><h3>General Settings</h3></td>
		<?php 
			foreach($generalSettingsInputs as $key=>$value){	
		?>
		<tr>
			<td style="text-align:left;padding-bottom:15px;"><?php echo $value['label'];?></td>
			<td style="text-align:left;padding-bottom:15px;padding-left:15px;" colspan="15">
				<?php 
				switch ($value['type']) {
					case 'select':
					?>
						<select name="general_setting_<?php echo $key?>" >
							<?php foreach($value['values'] AS $selectVal=>$selectLabel) { ?>
							<option <?php if ($generalSettings[0]->$key == $selectVal) {echo 'selected="selected"';}?> value="<?php echo $selectVal;?>"><?php echo $selectLabel;?></option>
							<?php } ?>
						</select>
					<?php
						break;
					case 'small-text':
					?>
						<input size="20" type="text" name="general_setting_<?php echo $key;?>" value="<?php echo $generalSettings[0]->$key; ?>" />
					<?php
						break;
					default :
					?>
						<input size="50" type="text" name="general_setting_<?php echo $key;?>" value="<?php echo $generalSettings[0]->$key; ?>" />
				<?php }?>
			</td>
		</tr>
		<?php } ?>
		<tr><td colspan="5" style="text-align:left"><h3>Design Settings</h3></td>
		<?php 
			foreach($designSettingsInputs as $key=>$value){	
		?>
		<tr>
			<td style="text-align:left;padding-bottom:15px;"><?php echo $value['label'];?></td>
			<td style="text-align:left;padding-bottom:15px;padding-left:15px;" colspan="15">
				<?php 
				switch ($value['type']) {
					case 'select':
					?>
						<select name="design_setting_<?php echo $key?>" >
							<?php foreach($value['values'] AS $selectVal=>$selectLabel) { ?>
							<option <?php if ($generalSettings[0]->$key == $selectVal) {echo 'selected="selected"';}?> value="<?php echo $selectVal;?>"><?php echo $selectLabel;?></option>
							<?php } ?>
						</select>
					<?php
						break;
					case 'small-text':
					?>
						<input size="20" type="text" name="design_setting_<?php echo $key;?>" value="<?php echo $generalSettings[0]->$key; ?>" />
					<?php
						break;
					default :
					?>
						<input size="50" type="text" name="design_setting_<?php echo $key;?>" value="<?php echo $generalSettings[0]->$key; ?>" />
				<?php }?>
			</td>
		</tr>
		<?php } ?>
	</table>
	<p class="submit"><input type="submit" name="audior-setting-submit" value="Update Options" class="button-primary" /></p>
</form>