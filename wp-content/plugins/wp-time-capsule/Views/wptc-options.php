<?php
/**
 * This file contains the contents of the Dropbox admin options page.
 *
 * @copyright Copyright (C) 2011-2014 Awesoft Pty. Ltd. All rights reserved.
 * @author Michael De Wildt (http://www.mikeyd.com.au/)
 * @license This program is free software; you can redistribute it and/or modify
 *          it under the terms of the GNU General Public License as published by
 *          the Free Software Foundation; either version 2 of the License, or
 *          (at your option) any later version.
 *
 *          This program is distributed in the hope that it will be useful,
 *          but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          GNU General Public License for more details.
 *
 *          You should have received a copy of the GNU General Public License
 *          along with this program; if not, write to the Free Software
 *          Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA.
 */

include_once dirname(__FILE__) . '/wptc-plans.php';
include_once dirname(__FILE__) . '/wptc-options-helper.php';

$options_helper = new Wptc_Options_Helper();

try {
	if ($errors = get_option('wptc-init-errors')) {
		delete_option('wptc-init-errors');
		throw new Exception(__('WordPress Time Capsule failed to initialize due to these database errors.', 'wptc') . '<br /><br />' . $errors);
	}

	$config = WPTC_Factory::get('config');
	process_GET_request_wptc($config);

	$dropbox = WPTC_Factory::get(DEFAULT_REPO);

	$is_user_logged_in_var = $config->get_option('is_user_logged_in');
	$main_account_email_var = $config->get_option('main_account_email');
	// $main_account_email_var = $config->get_option('main_account_email');

	$backup = new WPTC_BackupController();

	$config->create_dump_dir();

	$tcStartBackupNow = false;

	$disable_backup_now = $config->get_option('in_progress');

	if (array_key_exists('unlink', $_POST)) {
		check_admin_referer('wordpress_time_capsule_options_save');
		$backup->unlink_current_acc_and_backups();
		$dropbox->unlink_account()->init();
	} elseif (array_key_exists('clear_history', $_POST)) {
		check_admin_referer('wordpress_time_capsule_options_save');
		$config->clear_history();
	} else if (isset($_GET['new_backup'])) {
		$tcStartBackupNow = true;
		$config->set_option('starting_first_backup', true);
		$config->set_option('first_backup_started_atleast_once', true);
		$config->set_main_cycle_time();
		if (DEFAULT_REPO != $config->get_option('default_repo_history')) {
			$config->set_option('default_repo_history', DEFAULT_REPO);
			$backup->clear_prev_repo_backup_files_record();
		}
	}

	list($unixtime, $frequency) = $config->get_schedule();
	if (!$frequency) {
		$frequency = 'weekly';
	}

	if (!get_settings_errors('wptc_options')) {
		$dropbox_location = $config->get_option('dropbox_location');
		$store_in_subfolder = $config->get_option('store_in_subfolder');
	}

	$time = date('H:i', $unixtime);
	$day = date('D', $unixtime);
	add_thickbox();

	//getting schedule options
	$schedule_backup = $config->get_option('schedule_backup');
	$auto_backup = $config->get_option('auto_backup_switch');
	$schedule_interval = $config->get_option('schedule_interval');
	$schedule_day = $config->get_option('schedule_day');
	$schedule_time_str = $config->get_option('schedule_time_str');
	$wptc_timezone = $config->get_option('wptc_timezone');
	$hightlight = '';
	if (isset($_GET['highlight'])) {
		$hightlight = $_GET['highlight'];
	}
	/*if(isset($_GET['error'])){
		if($dropbox){
		$dropbox->unlink_account()->init();
		}
	*/
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo $uri ?>/wptc-dialog.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $uri ?>/wptc-plans.css"/>

	<link rel="stylesheet" type="text/css" href="<?php echo $uri ?>/JQueryFileTree/jqueryFileTree.css"/>
	<script src="<?php echo $uri ?>/JQueryFileTree/jqueryFileTree.js" type="text/javascript" language="javascript"></script>
	<script src="<?php echo $uri ?>/treeView/jquery-ui.custom.js" type="text/javascript" language="javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $uri ?>/treeView/skin/ui.fancytree.css"/>
	<script src="<?php echo $uri ?>/treeView/jquery.fancytree.js" type="text/javascript" language="javascript"></script>

	<div class="wrap" id="wptc">


	<form id="backup_to_dropbox_options" name="backup_to_dropbox_options" action="<?php echo network_admin_url("admin.php?page=wp-time-capsule"); ?>" method="post">

	<?php

		echo_success_and_error_flaps_wptc($config);

		$is_error_empty = empty($_GET['error']);
		$is_user_logged_in = $config->get_option('is_user_logged_in');
		$default_repo_connected = $config->get_option('default_repo');
		$is_uuid = isset($_GET['uid']);
		$is_new_backup = isset($_GET['new_backup']);
		$show_connect_pane = isset($_GET['show_connect_pane']);
		$is_initial_setup = isset($_GET['initial_setup']);
		$is_cloud_auth_action = isset($_GET['cloud_auth_action']);
		$privileges_wptc = $options_helper->get_unserialized_privileges();
		if ($dropbox) {
			$is_auth = $dropbox->is_authorized();
		} else if(empty($dropbox) && DEFAULT_REPO === 'dropbox' ||  DEFAULT_REPO === 's3' ||  DEFAULT_REPO === 'g_drive')  {
			$is_auth = true;
		} else {
			$is_auth = false;
		}

		dark_debug($is_auth, '---------------$is_auth-----------------');
		dark_debug($is_user_logged_in, '---------------$is_user_logged_in-----------------');
		dark_debug($default_repo_connected, '---------------$default_repo_connected-----------------');
		if (	$is_error_empty &&
				$is_user_logged_in &&
				$default_repo_connected &&
				!$is_uuid &&
				!$is_new_backup &&
				!$show_connect_pane &&
				!$is_initial_setup &&
				!$is_cloud_auth_action &&
				($dropbox && $is_auth) ) {

		?>
			<div style="width:100%">
				<h2 style="width: 30%; display: inline-block;"><?php _e('WP Time Capsule Settings', 'wptc');?></h2>
				<div style="width: 43%; display: inline-block;">
					<!--<a style="width: 74%;" href="https://wptimecapsule.uservoice.com/" target="_blank">Got an Idea?</a>-->
				</div>
			</div>
	<?php settings_errors();?>

	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row"> <label>Account</label>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span>Account</span></legend>
					<label title="Account">
						<span class="">
							<?php echo $main_account_email_var; ?>
						</span>
					</label>
					<a class="change_dbox_user_tc" href="<?php echo network_admin_url() . 'admin.php?page=wp-time-capsule&logout=true'; ?>" style="margin-left: 5px;"> Logout </a>
				</fieldset>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"> <label>Active Plan</label>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span>Active Plan</span></legend>
					<label title="Active Plan">
						<span class="" style="text-transform: capitalize;">
							<?php echo $options_helper->get_plan_name_from_privileges(); ?>
						</span>  
						<span class="" style="text-transform: capitalize;">
							<?php echo $options_helper->get_plan_interval_from_subs_info(); ?>
						</span>
					</label>
					<a class="change_dbox_user_tc" href="<?php echo WPTC_APSERVER_URL . '/my-account.php' ?>" target="_blank" style="margin-left: 5px;"> Change </a>
				</fieldset>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"> <label>Cloud Storage Account</label>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span>Cloud Storage Account</span></legend>
							<?php echo get_signed_in_repos_div($config); ?>
					</label>
					<a class="change_dbox_user_tc" id="change_repo_wptc" href="<?php echo network_admin_url() . 'admin.php?page=wp-time-capsule&show_connect_pane=set'; ?>" style="margin-left: 5px;">Change</a>
					<div class="dashicons-before dashicons-warning" style="position: relative;font-size:12px;top: 4px;font-style: italic;"><span style="position: absolute;top: 4px;left: 24px;">Please do not modify the files backed up on the <?php echo DEFAULT_REPO_LABEL; ?> as it will cause problems during restore. </span></div>
				</fieldset>
			</td>
		</tr>
		<?php if (DEFAULT_REPO === 'g_drive') { ?>
		<tr valign="top">
			<th scope="row"> <label>Google Drive Refresh Token</label>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span>Cloud Storage Account</span></legend>
						 <input style="width: 30%" type="text" readonly="readonly" name="gdrive_refresh_token_wptc" id="gdrive_refresh_token_wptc" value="<?php echo htmlspecialchars($config->get_option('gdrive_old_token')); ?>">
						 <a class="copy_gdrive_token_wptc" id="copy_gdrive_token_wptc" style="margin-left: 5px;cursor: pointer;" data-clipboard-target="#gdrive_refresh_token_wptc">Copy</a>
						 <span id="gdrive_token_copy_message_wptc" style="color: #008000; margin-left: 5px; display: none">Copied :)</span>
					</label>
					<div class="dashicons-before dashicons-warning" style="position: relative;font-size:12px;top: 4px;font-style: italic;"><span style="position: absolute;top: 4px;left: 24px;">Copy the above token if you intend to backup more sites to the same Google Account. <a href="http://docs.wptimecapsule.com/article/23-add-new-site-using-existing-google-drive-token" style="text-decoration: none" target="_blank">Show me how.</a> </span></div>
				</fieldset>
			</td>
		</tr>
		<?php } ?>

		 <tr valign="top" style="display: none">
			<th scope="row"> <label>Backup type</label>
			</th>
			<td>
				<fieldset>
					<?php echo get_select_backup_type_setting($config); ?>
				</fieldset>
			</td>
		</tr>



		<tr valign="top">
			<th scope="row"> <label class="init_backup_time_n_zone">Backup Schedule and Timezone</label>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span>Backup Schedule</span></legend>
					<label title="Default Backup Schedule Selected">
						<select name="select_wptc_default_schedule" id="select_wptc_default_schedule">
							<?php echo get_schedule_times_div_wptc($config); ?>
						</select>
						<?php	$tzstring = $config->get_option('wptc_timezone');?>
						<select id="wptc_timezone" name="wptc_timezone"><?php echo select_wptc_timezone(); ?></select>
					</label>
				</fieldset>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"> <label>On-demand backup</label>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span>On-demand backup</span></legend>
					<label title="On-demand backup">
						<a id="start_backup_from_settings" action="start">Backup now</a>
					</label>
					<br>
				<p id="backup_button_status_wptc" class="description">Click Backup Now to backup the latest changes</p>
				</fieldset>
			</td>
		</tr>

		<?php $more_tables_div = apply_filters('inside_settings_wptc_h', '');?>
		<?php echo $more_tables_div; ?>

		<tr valign="top">
			<th scope="row"> <label>Exclude / Include from backup</label> </th>
			<td >
				<?php	//$user_excluded_files_and_folders = $config->get_option('user_excluded_files_and_folders');?>
				<?php	$user_excluded_extenstions = $config->get_option('user_excluded_extenstions');?>
				<fieldset style="float: left;">
					<button class="button button-secondary wptc_dropdown" id="toggle_exlclude_files_n_folders" style="width: 408px; outline:none; text-align: left;">
						<span style="left: 21px; position: relative;">Folders &amp; Files </span>
						<span class="dashicons dashicons-portfolio" style="position: relative;right: 95px;top: 3px;"></span>
						<span class="dashicons dashicons-arrow-down" style="position: relative; top: 3px; left: 255px;"></span>
					</button>
						<!-- <input style="display: none;border: 1px solid gray;" type="hidden" id="user_excluded_files_and_folders" name="user_excluded_files_and_folders"/> -->
						<!-- <input style="display: none;border: 1px solid gray;" type="hidden" id="user_include_files_and_folders" name="user_include_files_and_folders"/> -->
					<div style="display:none" id="wptc_exc_files"></div>
				</fieldset>
				<fieldset style="position: relative;">
					<div style="position: relative; top: 0px;left: 30px; max-width: 0px">
						<button class="button button-secondary wptc_dropdown" id="toggle_wptc_db_tables" style="width: 408px; outline:none; text-align: left;">
							<span style="left: 21px; position: relative;">Database</span>
							<span class="dashicons dashicons-menu" style="position: relative;right: 65px;top: 3px;"></span>
							<!-- <span id="included_db_size" style="left: 331px;position: absolute;"><?php $included_db_size = $config->get_option('included_db_size'); echo ($included_db_size) ? $included_db_size : '0 B';  ?></span> -->
							<span class="dashicons dashicons-arrow-down" style="position: relative;top: 3px;left: 288px;"></span>
						</button>
						<!-- <input style="display: none;border: 1px solid gray;" type="hidden" id="user_excluded_tables" name="user_excluded_tables"/> -->
						<!-- <input style="display: none;border: 1px solid gray;" type="hidden" id="user_included_tables" name="user_included_tables"/> -->
						<div style="display:none" id="wptc_exc_db_files"></div>
					</div>
				</fieldset>
				<fieldset style="float: left; clear: both;">
				<br>
					<label style="width: 100%;" class="wptc-split-column">Enter file extensions to exclude</label>
				</fieldset>
				<fieldset style="float: left;width: 100%;" >
					<input class="wptc-split-column" type="text" name="user_excluded_extenstions" id="user_excluded_extenstions"  placeholder="Eg. .mp4, .mov" value="<?php echo $user_excluded_extenstions; ?>" />
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"> <label>Plugin - Server communication status</label> </th>
			<td >
				<?php
					$is_backup_paused = is_backup_paused_wptc();
					$status = wptc_cron_status(1);
				?>
					<fieldset >
							<div id="wptc_cron_status_paused" <?php echo ($is_backup_paused) ? "style='display:block'" : "style='display:none'"; ?>>
								<div>
									<span class='cron_current_status' style="color:red">Backup stopped due to server communication error</span> -
									<a class="resume_backup_wptc" style="cursor:pointer">Resume backup</a>
								</div>
							</div>
							<div id="wptc_cron_status_div" <?php echo ($is_backup_paused) ? "style='display:none'" : "style='display:block'"; ?> >
								<div id="wptc_cron_status_failed"<?php echo ($status['status'] == 'success') ? "style='display:none'" : "style='display:block'"; ?> >
									<div>
										<span class='cron_current_status' id="wptc_cron_failed_note">Failed</span> -
										<a class="test_cron_wptc">Test again</a>
									</div>
								</div>
						<div id ="wptc_cron_status_passed" <?php echo ($status['status'] == 'success') ? "style='display:block'" : "style='display:none'"; ?> >
							<span class='cron_current_status'>Success</span> - <a class="test_cron_wptc">Test again</a>
						</div>
					</div>
		</fieldset>
		</td>
		</tr>
		<tr valign="top">
			<th scope="row"> <label>Send anonymous data</label>
			</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span>Send anonymous data</span></legend>
					<label title="Yes">
						<input name="anonymous_datasent" type="radio" id="anonymous_datasent_yes" <?php if ($config->get_option('anonymous_datasent') == 'yes') {
			echo 'checked';
		}
		?> value="yes">
						<span class="">
							Yes
						</span>
					</label>
					<br>
					<label title="No">
						<input name="anonymous_datasent" type="radio" id="anonymous_datasent_no" <?php if ($config->get_option('anonymous_datasent') == 'no') {
			echo 'checked';
		}
		?> value="no">
						<span class="">
							No
						</span>
					</label>
					<br>
					<p class="description">Non-personally identifiable usage data will be sent for the sole purpose of improvement of the plugin.</p>
				</fieldset>
			</td>
		</tr>

		</tbody>
	</table>
	<div class="submit" style="position:relative">
		<input type="submit" id="wptc_save_changes" name="wptc_save_changes" class="button-primary" value="Save Changes">
		<div style=" position: absolute; top: 23px; left: 120px; display:none" id="cannot_save_settings_wptc">(A backup is currently running. Please wait until it finishes to change settings.)</div>
	</div>
		<?php wp_nonce_field('wordpress_time_capsule_options_save');?>
	</form>
		<div>If you have any questions, see the <a href="http://wptc.helpscoutdocs.com/article/7-commonly-asked-questions" target="_blank">FAQs</a> or email us at <a href="mailto:help@wptimecapsule.com?Subject=Contact" target="_top">help@wptimecapsule.com</a>.</div>
		<?php

	} else {
		$privileges_wptc = $options_helper->get_unserialized_privileges();
		$options_helper->reload_privileges_if_empty();

		$is_show_privilege_box = $options_helper->is_show_privilege_box();

		$options_helper->set_valid_user_but_no_plans_purchased(false);

		$is_show_login_box = $options_helper->is_show_login_box();

		$is_show_initial_setup = $options_helper->is_show_initial_setup();

		$is_show_connect_pane = $options_helper->is_show_connect_pane();

		if ($is_show_login_box) {
			$login_title_label = 'LOGIN TO YOUR ACCOUNT';
		} else {
			$login_title_label = 'Hi ' . $config->get_option('main_account_name') . ' :)';
		}

		if ($is_show_connect_pane) {
			$connect_pane_title_label = 'CONNECT YOUR STORAGE APP';
		} else {
			$connect_pane_title_label = DEFAULT_REPO_LABEL;
		}

		if (empty($connect_pane_title_label) || $connect_pane_title_label == 'Cloud') {
			$connect_pane_title_label = 'Connect your storage app';
		}
		?>
		<div class="pu_title">Welcome to WP Time Capsule</div>

		<div class="wptc_subtitle" style="text-align: center;">
			<div class="block lg <?php if ($is_show_login_box) {echo 'active';}
		?> "><?php echo $login_title_label; ?></div>
			<div class="block pln <?php if ($is_show_privilege_box) {echo 'active';} ?> ">Plans</div>
			<div class="block cn <?php if(isset($_GET['show_connect_pane']) && $_GET['show_connect_pane'] == 'set' || isset($_GET['not_approved']) || $is_show_connect_pane){echo 'active';}
		?> "><?php echo $connect_pane_title_label; ?></div>
			<div class="block fb <?php if (( !isset($_GET['show_connect_pane']) || $_GET['show_connect_pane'] != 'set' && !isset($_GET['not_approved'])) && (isset($_GET['uid']) && isset($_GET['oauth_token']) || (isset($_GET['code']) && $_GET['cloud_auth_action'] == 'g_drive') || isset($_GET['as3_bucket_region']))) {echo 'active';}
		?> "><?php if (isset($_GET['initial_setup'])) {echo "INITIAL SETUP";} else {echo "Initial setup";}?></div>
		<div class="block fb <?php if (isset($_GET['new_backup'])) {echo 'active';}
		?> "><?php if (isset($_GET['new_backup'])) {echo "TAKE FIRST BACKUP";} else {echo "Take first backup";}?></div>
		</div>


		<div class="wcard clearfix" style="width: 980px;">
			<?php if ($is_show_login_box) {
			?>
					<form id="wptc_main_acc_login"  action="<?php echo network_admin_url("admin.php?page=wp-time-capsule"); ?>" name="wptc_main_acc_login" method="post">
						<div class="l1 wptc_login_msg_div <?php if (!isset($_GET['error'])) {echo 'active';}
			?> ">Login to your WP Time Capsule account below</div>
						<div class="l1 wptc_error_div  <?php if (isset($_GET['error'])) {echo 'active';}
			?> "><?php
echo $config->get_last_login_error_msg();
			$config->set_option('main_account_login_last_error', false);
			?></div>
						<div class="l1"  style="padding: 0px;">
							<input type="text" id="wptc_main_acc_email" name="wptc_main_acc_email" placeholder="Email" autofocus>
						</div>
						<div class="l1"  style="padding: 0px; position: relative;">
							<input type="password" id="wptc_main_acc_pwd" name="wptc_main_acc_pwd" placeholder="Password" >
							<a href=<?php echo WPTC_APSERVER_URL_FORGET; ?> target="_blank" class="forgot_password">Forgot?</a>
						</div>
						<input type="submit" name="wptc_login" id="wptc_login" class="btn_pri" value="Login" />
						<div style="clear:both"></div>
						<div id="mess" class="wptc_signup_link_div">Dont have an account yet?
							<a href=<?php echo WPTC_APSERVER_URL_SIGNUP; ?> target="_blank" >Signup Now</a>
						</div>
					</form>
			<?php } elseif ($is_show_privilege_box) {
					$plans_obj = new Wptc_Plans();
					echo $plans_obj->echo_plan_box_div_wptc();

				  } else if (isset($_GET['new_backup'])) {
			do_action('starting_fresh_new_backup_pre_wptc_h', '');
			do_action('send_basic_analytics', time());
			record_signed_in_repos($config, $dropbox);
			?>
					<div class="l1"  style="padding-bottom: 10px;">We will now backup your website to your <?php echo DEFAULT_REPO_LABEL; ?> account. This being the first backup may take hours or days depending on the size of your website.  That's because, we don't zip your backups thus giving your server more space to breathe. The next set of incremental backups will hardly take a few minutes - <a href="http://docs.wptimecapsule.com/article/15-why-does-your-first-backup-take-too-long-to-complete" target="_blank">Know more</a>.</div>
					<div class="l1 wptc_prog_wrap bp-progress-first-bp"></div>
					<div class="l1"  style="padding-bottom: 10px;">You can close the backup window and take care of your errands, we will e-mail you once the backup is completed. In case you want to check the backup status of all your sites, <a href="https://service.wptimecapsule.com/" style="text-decoration:underline; cursor:pointer" target="_blank">click here.</a></div>
				<?php	} else if (
						(isset($_GET['cloud_auth_action']) &&
						$_GET['cloud_auth_action'] == 'g_drive' &&
						isset($_GET['code']) &&
						!isset($_GET['error']) ||
						isset($_GET['uid']) ||
						isset($_GET['as3_access_key'])) &&
						(DEFAULT_REPO_LABEL != 'Cloud') &&
						!isset($_GET['show_connect_pane'])
					) {
					store_g_drive_data($config);
					?>
					<table style="width: 770px; margin-left: auto; margin-right: auto;">
						<tr style="display: none">
							<td>
								<div class="l1"  style="padding-bottom: 10px;">Backup Type <?php echo get_select_backup_type_setting($config); ?></div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="l1"  style="padding-bottom: 10px;"><span class="init_backup_time_n_zone">Backup Schedule and Timezone</span><select name="select_wptc_default_schedule" id="select_wptc_default_schedule" style="margin-left:4px"> <?php echo get_schedule_times_div_wptc($config); ?> </select>
									<?php	$tzstring = $config->get_option('wptc_timezone');?>
									<select id="wptc_timezone" name="wptc_timezone"><?php echo select_wptc_timezone(); ?> </select>
								</div>
							</td>
						</tr>
					<!--- starting new changes !-->

					<tr>
						<td>
							<div  class="l1" style="top: 0px;position: relative;padding-bottom: 10px; /* left:72px; */ " >
								<a id="show_file_db_exp_for_exc" style="position: absolute;top: 19px;cursor: pointer;right: 290px;"> Include/exclude content &#9660;</a>
							</div>
						</td>
					</tr>
					<tr style="display:none" id="file_db_exp_for_exc_view">
						<td >
							<fieldset style="float: left; margin-top: 20px">
								<button class="button button-secondary wptc_dropdown" id="wptc_init_toggle_files" style="width: 408px; outline:none; text-align: left;">
									<span style="left: 21px; position: relative;">Folders &amp; Files </span>
									<span class="dashicons dashicons-portfolio" style="position: relative;right: 95px;top: 3px;"></span>
									<span class="dashicons dashicons-arrow-down" style="position: relative; top: 2px; left: 255px;"></span>
								</button>
									<!-- <input style="display: none;border: 1px solid gray;" type="hidden" id="user_excluded_files_and_folders" name="user_excluded_files_and_folders"> -->
									<!-- <input style="display: none;border: 1px solid gray;" type="hidden" id="user_include_files_and_folders" name="user_include_files_and_folders"> -->
								<div style="display:none" id="wptc_exc_files"></div>
							</fieldset>
							<fieldset style="position: relative; margin-top: 20px">
								<div style="position: relative; top: 0px;left: 30px;" id="wptc_init_table_div">
									<button class="button button-secondary wptc_dropdown" id="wptc_init_toggle_tables" style="width: 408px; outline:none; text-align: left;">
										<span style="left: 21px; position: relative;">Database</span>
										<span class="dashicons dashicons-menu" style="position: relative;right: 65px;top: 3px;"></span>
										<span class="dashicons dashicons-arrow-down" style="position: relative; top: 2px; left: 283px;"></span>
									</button>
									<!-- <input style="display: none;border: 1px solid gray;" type="hidden" id="user_excluded_tables" name="user_excluded_tables"> -->
									<!-- <input style="display: none;border: 1px solid gray;" type="hidden" id="user_included_tables" name="user_included_tables"> -->
									<div style="display:none" id="wptc_exc_db_files"></div>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr class="view-user-exc-extensions" style="display: none">
						<td>
							<div class="l1"  style="padding-bottom: 10px;">Enter file extensions to exclude</div>
						</td>
					</tr>
					<tr class="view-user-exc-extensions" style="display: none">
						<td>
						<?php	$user_excluded_extenstions = $config->get_option('user_excluded_extenstions');?>
						<input type="text" name="user_excluded_extenstions" placeholder="Eg. .mp4, .mov"  style="width: 42%;margin-left: 220px;" value="<?php echo $user_excluded_extenstions; ?>" >
						</td>
					</tr>
					<tr>
						<td>
							<input type="button" id="skip_initial_set_up" class="btn_pri" style="margin: 50px 140px 30px;width: 240px;text-align: center;display: block;position: relative;top: 13px;left: 0px;background: #999;border-color: #fff;color: #FFF;" value="I'll do it later">
							<input type="button" id="continue_wptc" class="btn_pri" style="width: 240px;text-align: center;display: block;position: relative;top: -57px;left: 393px;" value="Save and continue">
						</td>
					</tr>
					<tr>
						<td>
							<div class="dashicons-before dashicons-warning" id="donot_touch_note" style="font-size: 12px;font-style: italic;"><span>You can do this setup anytime under WP Time Capsule -&gt; Settings</span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="dashicons-before dashicons-warning" id="donot_touch_note" style="font-size: 12px;font-style: italic;"><span >Please do not modify the files backed up on the <span id="donot_touch_note_cloud"><?php echo DEFAULT_REPO_LABEL; ?></span> as it will cause problems during restore. </span></div></div>
						</td>
					</tr>
				</table>

				<?php	}
		else {
			?>
					<div class="l1"  style="padding-bottom: 10px;">The backup of this website will be stored in a folder in your <?php echo DEFAULT_REPO_LABEL; ?> app</div>
					<form id="backup_to_dropbox_continue" name="backup_to_dropbox_continue" method="post">
						<?php echo (get_select_cloud_dialog_div($config, $dropbox)); ?>
					</form>
					<div class="l1 wptc_error_div " style="<?php if (isset($_GET['error']) && !empty($_GET['error'])) {echo "display:block;";} else {echo "display:none;";}
			?>"><?php $last_error_tmp = $config->get_option('last_cloud_error');
			if (empty($last_error_tmp)) {
				echo "Oops. Unable to connect to Cloud. Please check your credentials and try again.";
			} else {
				echo $last_error_tmp;
			}
			$config->set_option('last_cloud_error', false);
			?></div>
			<?php }
		?>
		</div>
		<?php
	}
} catch (Exception $e) {
	echo '<h3>Error</h3>';
	echo '<p>' . __('There was a fatal error loading WordPress Time Capsule. Please fix the problems listed and reload the page.', 'wptc') . '</h3>';
	echo '<p>' . __('If the problem persists please re-install WordPress Time Capsule.', 'wptc') . '</h3>';
	echo '<p><strong>' . __('Error message:') . '</strong> ' . $e->getMessage() . '</p>';

	dark_debug($e, "--------e errors--------");

	//WPTC_Factory::get('config')->set_option('default_repo', false);
}
?>
<div id="dialog_content_id" style="display:none;"> <p> This is my hidden content! It will appear in ThickBox when the link is clicked. </p></div>
<a style="display:none" href="#TB_inline?width=600&height=550&inlineId=dialog_content_id" class="thickbox wptc-thickbox">View my inline content!</a>
</div>
<?php

function echo_success_and_error_flaps_wptc(&$config){
	$email = $config->get_option('main_account_email');

	if(!empty($_GET['show_plan_success_flap'])){
		echo '<div class="bs-callout bs-callout-success flap_boxes_group_wptc wptc_success_box" style="display: none;"></div>';
	} elseif(!empty($_GET['show_plan_error_flap'])){
		echo '<div class="bs-callout bs-callout-danger flap_boxes_group_wptc wptc_error_box "><span class="error_label">Error:</span><span class="err_msg">'.$_GET['err_msg'].'</span></div>';
	}
}

function get_s3_select_box_div($selected_bucket_region) {
	$buc_region_arr = array('' => 'Select Bucket Region', '' => 'US Standard', 'us-west-2' => 'US West (Oregon) Region', 'us-west-1' => 'US West (Northern California) Region', 'eu-west-1' => 'EU (Ireland) Region', 'ap-southeast-1' => 'Asia Pacific (Singapore) Region', 'ap-southeast-2' => 'Asia Pacific (Sydney) Region', 'ap-northeast-1' => 'Asia Pacific (Tokyo) Region', 'sa-east-1' => 'South America (Sao Paulo) Region', 'eu-central-1' => 'EU (Frankfurt)', 'cn-north-1' => 'China (Beijing) Region');

	$div = '<select name="as3_bucket_region" id="as3_bucket_region" class="wptc_general_inputs" style="width:45%; height: 38px;">';

	foreach ($buc_region_arr as $k => $v) {
		$selected = '';
		if ($k == $selected_bucket_region) {
			$selected = 'selected';
		}
		$div = $div . '<option value="' . $k . '" ' . $selected . ' class="dropOption" >' . $v . '</option>';
	}
	$div = $div . '</select>';
	return $div;
}

function get_select_backup_type_setting(&$config){
	$select_start = '<select id="backup_type" name="backup_type">';
	$current_setting = $config->get_option('backup_type_setting');
	$daily_backup_selected = '';
	$weekly_backup_selected = '';
	if ($current_setting == 'SCHEDULE') {
		$daily_backup_selected = 'selected';
	} else if($current_setting == 'WEEKLYBACKUP'){
		$weekly_backup_selected = 'selected';
	}
	// $body_content = apply_filters('inside_backup_type_settings_wptc_h', '')."<option value='SCHEDULE' ".$daily_backup_selected.">Daily</option> <option value='WEEKLYBACKUP' ".$weekly_backup_selected.">Weekly</option>"; // Weekly disabled
	$body_content = apply_filters('inside_backup_type_settings_wptc_h', '')."<option value='SCHEDULE' ".$daily_backup_selected.">Daily</option>";
	$select_end = '</select>';
	return $select_start.$body_content.$select_end;
}

function get_select_cloud_dialog_div(&$config) {
	$div = '';
	$sub_div = '';
	if ((isset($_GET['cloud_auth_action']) && $_GET['cloud_auth_action'] == 'g_drive' && !isset($_GET['code']) && !isset($_GET['error'])) || !isset($_GET['cloud_auth_action']) && !isset($_GET['uid']) || DEFAULT_REPO_LABEL == 'Cloud') {
		$sites_count = $config->get_option('connected_sites_count');
		if (!empty($sites_count) && $sites_count >= WPTC_GDRIVE_TOKEN_ON_INIT_LIMIT) {
			$div .= '<div style="text-align: center; padding: 10px 5px; line-height: 22px; display:none" id="google_limit_reached_text_wptc">Google has a limit on the number of sites you can authenticate per app. If you are backing up all sites to the same Google Account, use a previously generated token. <a href="http://docs.wptimecapsule.com/article/23-add-new-site-using-existing-google-drive-token" style="text-decoration:none" target="_blank">Show me how.</a></div>';
		}
		$div .= '<div class="l1"  style="padding-bottom: 10px; padding-top: 10px">
					<select name="select_wptc_cloud_storage" id="select_wptc_cloud_storage" class="wptc_general_inputs" style="width:45%;height: 38px;">
						<option value="" class="dummy_select">Select your cloud storage app</option>';
		if (is_php_version_compatible_for_dropbox_wptc()) {
			$dropbox_not_eligible = 'display:none';
			$div .= '<option value="dropbox" label="Dropbox">Dropbox</option>;';
		} else {
			$dropbox_not_eligible = 'display:block';
			$div .= '<option disabled="disabled" value="dropbox" label="Dropbox">Dropbox</option>;';
		}
		if (is_php_version_compatible_for_g_drive_wptc()) {
			$gdrive_not_eligible = 'display:none';
			$div .= '<option value="g_drive" label="Google Drive">Google Drive</option>';
		} else {
			$div .= '<option disabled="disabled" value="g_drive" label="Google Drive">Google Drive</option>';
			$gdrive_not_eligible = 'display:block';
		}
		if (is_php_version_compatible_for_s3_wptc()) {
			$div .= '<option value="s3" label="Amazon S3" >Amazon S3</option>';
		} else {
			$div .= '<option disabled="disabled" value="s3" label="Amazon S3" >Amazon S3</option>';
		}
		$div .= '</select>
				</div>';
		if (!empty($sites_count) && $sites_count >= WPTC_GDRIVE_TOKEN_ON_INIT_LIMIT) {
			$div .= '<input type="text" id="gdrive_refresh_token_input_wptc" placeholder="Paste token here" style="display:none; width: 45%;position: relative;left: 268px;top: -10px;" class="wptc_general_inputs">';
			$div .= '<a href="http://docs.wptimecapsule.com/article/23-add-new-site-using-existing-google-drive-token" id="see_how_to_add_refresh_token_wptc" target="_blank" style="text-decoration:none; display:none; position: absolute;cursor: pointer;top: 287px;right: 286px;font-size: 12px;">Need help ?</a>';
		}
		if (is_php_version_compatible_for_s3_wptc()) {
			$s3_not_eligible = 'display:none';
			$div .=  get_s3_creds_box_div($config);
		} else {
			$s3_not_eligible = 'display:block';
		}
		$display_status = ($s3_not_eligible == 'display:block' || $gdrive_not_eligible == 'display:block' || $dropbox_not_eligible == 'display:block') ? 'display:block' : 'display:none';
		if (!empty($sites_count) && $sites_count >= WPTC_GDRIVE_TOKEN_ON_INIT_LIMIT) {
			$div = $div . '<div id="google_token_add_btn" style="display:none"><div class="cloud_error_mesg_g_drive_token"></div><input type="button" id="save_g_drive_refresh_token" class="btn_pri cloud_go_btn" style="margin: 10px 32.9% 20px; width: 330px; text-align: center;" value="Authenticate Token" ><div style="text-align: center; margin-bottom: 20px;">(OR)</div></div>';
		}
		$div = $div . '<div class="cloud_error_mesg"></div><input type="button" id="connect_to_cloud" class="btn_pri cloud_go_btn" style="margin: 0px 32.9% 30px; width: 330px; text-align: center; display: none;" value="Connect my cloud account" >';
	}
	$div .= '<div style="clear:both"></div>';
	$div .= '<div id="mess" style="text-align: center; font-size: 13px; padding-top: 10px; padding-bottom: 10px; display: none;">You will be redirected to the specific Cloud Site for allowing access to the plugin.<br> Click on <strong>Allow</strong> when prompted.</div>';
	$div .= "<div class='dashicons-before dashicons-warning' id='s3_seperate_bucket_note' style='display:none; font-style: italic; left: 10px; font-size: 13px;'><span style='line-height: 22px'>Please create a separate bucket on Amazon S3 since we will be enabling versioning on that bucket. We create subfolders for each site, so you don't have to create a new bucket everytime.</span></div>";
	$div .= "<div style='height: 60px; position: relative;".$display_status." ' id='php_req_note_wptc'><div class='dashicons-before dashicons-warning' id='dropbox_php_req_note' style='position: absolute;font-size: 12px;top: -27px;width: 100%;font-style: italic;left: 10px;padding-top: 10px;padding-bottom: 10px; ".$dropbox_not_eligible."'><span style='position: absolute;top: 11px;left: 24px; '>Dropbox requires PHP v5.3.1+. Please upgrade your PHP to use Dropbox.</span></div><div class='dashicons-before dashicons-warning' id='g_drive_php_req_note' style='position: absolute;font-size: 12px;top: 0px;width: 100%;font-style: italic;left: 10px;padding-top: 10px;padding-bottom: 10px; ".$gdrive_not_eligible."'><span style='position: absolute;top: 11px;left: 24px; '>Google Drive requires PHP v5.4.0+. Please upgrade your PHP to use Google Drive.</span></div><div class='dashicons-before dashicons-warning' id='s3_php_req_note' style='position: absolute;font-size: 12px;top: 26px;width: 100%;font-style: italic;left: 10px;padding-top: 10px;padding-bottom: 10px; ".$s3_not_eligible."'><span style='position: absolute;top: 11px;left: 24px;''>Amazon S3 requires PHP v5.3.3+. Please upgrade your PHP to use Amazon S3.</span></div></div>";
	if (isset($_GET['uid'])) {
		// $div = $div . '<input type="button" name="continue" id="continue_wptc" class="btn_pri cloud_go_btn" style="margin: 20px 136px 30px; width: 330px; text-align: center; " value="Continue" />';
		//$div = $div . '<input type="button" name="continue" id="continue_to_initial_setup" class="btn_pri cloud_go_btn" style="margin: 25px 220px 10px;width: 330px;text-align: center;top: 0px;position: relative;" value="Continue" />';
	}
	if ((isset($_GET['cloud_auth_action']) && $_GET['cloud_auth_action'] == 's3') && !empty($_GET['as3_access_key']) && !empty($_GET['as3_secure_key']) && !empty($_GET['as3_bucket_name']) && DEFAULT_REPO_LABEL != 'Cloud') {
		// $div = $div . '<input type="button" name="continue" id="continue_to_initial_setup" class="btn_pri cloud_go_btn" style="margin: 25px 220px 10px;width: 330px;text-align: center;top: 0px;position: relative;" value="Continue" />';
	}
	return $div;
}

function store_g_drive_data($config){
	if ((isset($_GET['cloud_auth_action']) && $_GET['cloud_auth_action'] == 'g_drive') && isset($_GET['code']) && !isset($_GET['error'])) {
		$config->set_option('oauth_state_g_drive', 'access');
		$req_token_dets['refresh_token'] = $_GET['code'];
		$config->set_option('gdrive_old_token', serialize($req_token_dets));
	}
}
function get_s3_creds_box_div(&$config) {
	$div = '';
	$sub_div = '<a class="s3_doc_wptc" href="http://wptc.helpscoutdocs.com/article/4-connect-your-amazon-s3-account" target="_blank">See how to connect my AS3 account</a>';
	$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;"> <input type="text" name="as3_access_key" class="wptc_general_inputs" style="width: 45%;" placeholder="Access Key" id="as3_access_key" required value="' . $config->get_option('as3_access_key') . '" /> </div>';
	$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;"> <input type="text" name="as3_secure_key" class="wptc_general_inputs" style="width: 45%;" placeholder="Secure Key" id="as3_secure_key" required value="' . $config->get_option('as3_secure_key') . '" /> </div>';
	$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;">' . get_s3_select_box_div($config->get_option('as3_bucket_region')) . '</div>';
	$sub_div = $sub_div . '<div class="l1"  style="padding: 0px;"> <input type="text" class="wptc_general_inputs" style="width: 45%;" name="as3_bucket_name" placeholder="Bucket Name" id="as3_bucket_name" required value="' . $config->get_option('as3_bucket_name') . '" /> </div>';

	$div = $div . '<div class="l1 s3_inputs creds_box_inputs"  style="padding-bottom: 10px; display:none; margin-top: -36px; position: relative;"><div style="text-align: center; font-size: 13px; padding-bottom: 10px;">' . $sub_div . '</div></div>';

	return $div;
}

function check_cloud_min_php_min_req() {
	$cloud_eligible = array();
	if (is_php_version_compatible_for_g_drive_wptc()) {
		$cloud_eligible[] = 'gdrive';
	}
	if (is_php_version_compatible_for_s3_wptc()) {
		$cloud_eligible[] = 's3';
	}
	$cloud_eligible[] = 'dropbox'; // available all version of php
	return json_encode($cloud_eligible);
}

function remove_old_user_excluded_files_wptc() {
	$config = WPTC_Factory::get('config');
	$config->init_delete_user_recorded_exculuded_files();
}

function process_GET_request_wptc(&$config) {
	if ($config->get_option('main_account_login_last_error')) {
		$config->set_option('main_account_login_last_error', false);
	}
	if (isset($_GET['error'])) {
		if (isset($_GET['cloud_auth_action'])) {
			$config->set_option('last_cloud_error', $_GET['error']);
		} else {
			$last_cloud_error = $config->get_option('last_cloud_error');
			if ($last_cloud_error) {
				$config->set_option('main_account_login_last_error', $last_cloud_error);
			}
		}
	}
	if (!empty($_POST['wptc_main_acc_email']) && !empty($_POST['wptc_main_acc_pwd'])) {
		process_wptc_login();
	}
	if (!empty($_GET['logout'])) {
		process_wptc_logout('logout');
	}
}

function process_wptc_login() {
	$config = WPTC_Factory::get('config');
	$config->set_option('wptc_main_acc_email_temp', base64_encode($_POST['wptc_main_acc_email']));
	$config->set_option('wptc_main_acc_pwd_temp', base64_encode(md5(trim( wp_unslash( $_POST[ 'wptc_main_acc_pwd' ] ) ))));
	$config->set_option('wptc_token', false);

	$auth_result = $config->is_main_account_authorized($_POST['wptc_main_acc_email'], trim( wp_unslash( $_POST[ 'wptc_main_acc_pwd' ] ) ));

	$privileges_wptc = $config->get_option('privileges_wptc');
	$privileges_wptc = json_decode($privileges_wptc);

	dark_debug($privileges_wptc, "--------privileges_wptc-----process_wptc_login---");

	if (empty($auth_result)) {
		$_GET['error'] = true;
	}
}

function record_signed_in_repos(&$config, &$dropbox) {
	$signed_in_arr = $config->get_option('signed_in_repos');
	if (empty($signed_in_arr)) {
		$signed_in_arr = array();
	} else {
		$signed_in_arr = unserialize($signed_in_arr);
	}
	if (empty($dropbox)) {
		return false;
	}
	wipe_out_prev_acc_backups_wptc($signed_in_arr, $config->get_option('default_repo'), $dropbox->get_quota_div());
	$signed_in_arr[$config->get_option('default_repo')] = $dropbox->get_quota_div();
	$config->set_option('signed_in_repos', serialize($signed_in_arr));
}

function wipe_out_prev_acc_backups_wptc($signed_in_arr, $default_repo, $email) {
	if (empty($signed_in_arr) || !is_array($signed_in_arr)) {
		return false;
	}

	if ($default_repo == 'g_drive' && array_key_exists('g_drive', $signed_in_arr) && $signed_in_arr['g_drive'] != $email) {
		dark_debug('google gdrive exisiting account completely wiped out', '-------wipe_out_prev_acc_backups_wptc---------');
		clear_prev_acc_backup_data_wptc();
	} else if ($default_repo == 'dropbox' && array_key_exists('dropbox', $signed_in_arr) && $signed_in_arr['dropbox'] != $email) {
		dark_debug('dropbox exisiting account completely wiped out', '-------wipe_out_prev_acc_backups_wptc---------');
		clear_prev_acc_backup_data_wptc();
	} else if ($default_repo == 's3' && array_key_exists('s3', $signed_in_arr) && $signed_in_arr['s3'] != $email) {
		dark_debug('s3 exisiting account completely wiped out', '-------wipe_out_prev_acc_backups_wptc---------');
		clear_prev_acc_backup_data_wptc();
	}
}

function clear_prev_acc_backup_data_wptc() {
	$backup = new WPTC_BackupController();
	$backup->clear_prev_repo_backup_files_record();
}

function get_signed_in_repos_div(&$config) {
	$div = '';
	$signed_in_repos_arr = $config->get_option('signed_in_repos');
	if (empty($signed_in_repos_arr)) {
		$signed_in_repos_arr = array();
	} else {
		$signed_in_repos_arr = unserialize($signed_in_repos_arr);
	}
	$currently_selected = array();
	foreach ($signed_in_repos_arr as $k => $v) {
		if ($k == DEFAULT_REPO) {
			$div .= $v;
		}
	}
	return $div;
}

function get_schedule_times_div_wptc(&$config) {
	$times = array();
	$div = '';

	$this_time_zone = $config->get_option('wptc_timezone');
	if (!$this_time_zone) {
		$wp_default_time_zone = get_option('timezone_string');
		if (!$wp_default_time_zone) {
			$this_time_zone = 'UTC';
		} else {
			$this_time_zone = $wp_default_time_zone;
		}
	}

	$already_selected_schedule = $config->get_option('schedule_time_str');

	for ($i = 1; $i <= 24; $i++) {
		$selected = '';
		$this_date_text = date("g:i a", strtotime("$i:00"));
		$frequency = 'daily';
		$this_data_val = $this_date_text;
		if ($already_selected_schedule == $this_data_val) {
			$div .= '<option selected value="' . $this_data_val . '">' . $this_date_text . '</option>';
		} else {
			$div .= '<option value="' . $this_data_val . '">' . $this_date_text . '</option>';
		}
	}

	return $div;
}

//function for return the timezone select options
function select_wptc_timezone() {
	return '<optgroup label="Africa">
				<option value="Africa/Abidjan">Abidjan</option><option value="Africa/Accra">Accra</option><option value="Africa/Addis_Ababa">Addis Ababa</option><option value="Africa/Algiers">Algiers</option><option value="Africa/Asmara">Asmara</option><option value="Africa/Bamako">Bamako</option><option value="Africa/Bangui">Bangui</option><option value="Africa/Banjul">Banjul</option><option value="Africa/Bissau">Bissau</option><option value="Africa/Blantyre">Blantyre</option><option value="Africa/Brazzaville">Brazzaville</option><option value="Africa/Bujumbura">Bujumbura</option><option value="Africa/Cairo">Cairo</option><option value="Africa/Casablanca">Casablanca</option><option value="Africa/Ceuta">Ceuta</option><option value="Africa/Conakry">Conakry</option><option value="Africa/Dakar">Dakar</option><option value="Africa/Dar_es_Salaam">Dar es Salaam</option><option value="Africa/Djibouti">Djibouti</option><option value="Africa/Douala">Douala</option><option value="Africa/El_Aaiun">El Aaiun</option><option value="Africa/Freetown">Freetown</option><option value="Africa/Gaborone">Gaborone</option><option value="Africa/Harare">Harare</option><option value="Africa/Johannesburg">Johannesburg</option><option value="Africa/Juba">Juba</option><option value="Africa/Kampala">Kampala</option><option value="Africa/Khartoum">Khartoum</option><option value="Africa/Kigali">Kigali</option><option value="Africa/Kinshasa">Kinshasa</option><option value="Africa/Lagos">Lagos</option><option value="Africa/Libreville">Libreville</option><option value="Africa/Lome">Lome</option><option value="Africa/Luanda">Luanda</option><option value="Africa/Lubumbashi">Lubumbashi</option><option value="Africa/Lusaka">Lusaka</option><option value="Africa/Malabo">Malabo</option><option value="Africa/Maputo">Maputo</option><option value="Africa/Maseru">Maseru</option><option value="Africa/Mbabane">Mbabane</option><option value="Africa/Mogadishu">Mogadishu</option><option value="Africa/Monrovia">Monrovia</option><option value="Africa/Nairobi">Nairobi</option><option value="Africa/Ndjamena">Ndjamena</option><option value="Africa/Niamey">Niamey</option><option value="Africa/Nouakchott">Nouakchott</option><option value="Africa/Ouagadougou">Ouagadougou</option><option value="Africa/Porto-Novo">Porto-Novo</option><option value="Africa/Sao_Tome">Sao Tome</option><option value="Africa/Tripoli">Tripoli</option><option value="Africa/Tunis">Tunis</option><option value="Africa/Windhoek">Windhoek</option>
			</optgroup>
			<optgroup label="America">
				<option value="America/Adak">Adak</option><option value="America/Anchorage">Anchorage</option><option value="America/Anguilla">Anguilla</option><option value="America/Antigua">Antigua</option><option value="America/Araguaina">Araguaina</option><option value="America/Argentina/Buenos_Aires">Argentina - Buenos Aires</option><option value="America/Argentina/Catamarca">Argentina - Catamarca</option><option value="America/Argentina/Cordoba">Argentina - Cordoba</option><option value="America/Argentina/Jujuy">Argentina - Jujuy</option><option value="America/Argentina/La_Rioja">Argentina - La Rioja</option><option value="America/Argentina/Mendoza">Argentina - Mendoza</option><option value="America/Argentina/Rio_Gallegos">Argentina - Rio Gallegos</option><option value="America/Argentina/Salta">Argentina - Salta</option><option value="America/Argentina/San_Juan">Argentina - San Juan</option><option value="America/Argentina/San_Luis">Argentina - San Luis</option><option value="America/Argentina/Tucuman">Argentina - Tucuman</option><option value="America/Argentina/Ushuaia">Argentina - Ushuaia</option><option value="America/Aruba">Aruba</option><option value="America/Asuncion">Asuncion</option><option value="America/Atikokan">Atikokan</option><option value="America/Bahia">Bahia</option><option value="America/Bahia_Banderas">Bahia Banderas</option><option value="America/Barbados">Barbados</option><option value="America/Belem">Belem</option><option value="America/Belize">Belize</option><option value="America/Blanc-Sablon">Blanc-Sablon</option><option value="America/Boa_Vista">Boa Vista</option><option value="America/Bogota">Bogota</option><option value="America/Boise">Boise</option><option value="America/Cambridge_Bay">Cambridge Bay</option><option value="America/Campo_Grande">Campo Grande</option><option value="America/Cancun">Cancun</option><option value="America/Caracas">Caracas</option><option value="America/Cayenne">Cayenne</option><option value="America/Cayman">Cayman</option><option value="America/Chicago">Chicago</option><option value="America/Chihuahua">Chihuahua</option><option value="America/Costa_Rica">Costa Rica</option><option value="America/Creston">Creston</option><option value="America/Cuiaba">Cuiaba</option><option value="America/Curacao">Curacao</option><option value="America/Danmarkshavn">Danmarkshavn</option><option value="America/Dawson">Dawson</option><option value="America/Dawson_Creek">Dawson Creek</option><option value="America/Denver">Denver</option><option value="America/Detroit">Detroit</option><option value="America/Dominica">Dominica</option><option value="America/Edmonton">Edmonton</option><option value="America/Eirunepe">Eirunepe</option><option value="America/El_Salvador">El Salvador</option><option value="America/Fortaleza">Fortaleza</option><option value="America/Glace_Bay">Glace Bay</option><option value="America/Godthab">Godthab</option><option value="America/Goose_Bay">Goose Bay</option><option value="America/Grand_Turk">Grand Turk</option><option value="America/Grenada">Grenada</option><option value="America/Guadeloupe">Guadeloupe</option><option value="America/Guatemala">Guatemala</option><option value="America/Guayaquil">Guayaquil</option><option value="America/Guyana">Guyana</option><option value="America/Halifax">Halifax</option><option value="America/Havana">Havana</option><option value="America/Hermosillo">Hermosillo</option><option value="America/Indiana/Indianapolis">Indiana - Indianapolis</option><option value="America/Indiana/Knox">Indiana - Knox</option><option value="America/Indiana/Marengo">Indiana - Marengo</option><option value="America/Indiana/Petersburg">Indiana - Petersburg</option><option value="America/Indiana/Tell_City">Indiana - Tell City</option><option value="America/Indiana/Vevay">Indiana - Vevay</option><option value="America/Indiana/Vincennes">Indiana - Vincennes</option><option value="America/Indiana/Winamac">Indiana - Winamac</option><option value="America/Inuvik">Inuvik</option><option value="America/Iqaluit">Iqaluit</option><option value="America/Jamaica">Jamaica</option><option value="America/Juneau">Juneau</option><option value="America/Kentucky/Louisville">Kentucky - Louisville</option><option value="America/Kentucky/Monticello">Kentucky - Monticello</option><option value="America/Kralendijk">Kralendijk</option><option value="America/La_Paz">La Paz</option><option value="America/Lima">Lima</option><option value="America/Los_Angeles">Los Angeles</option><option value="America/Lower_Princes">Lower Princes</option><option value="America/Maceio">Maceio</option><option value="America/Managua">Managua</option><option value="America/Manaus">Manaus</option><option value="America/Marigot">Marigot</option><option value="America/Martinique">Martinique</option><option value="America/Matamoros">Matamoros</option><option value="America/Mazatlan">Mazatlan</option><option value="America/Menominee">Menominee</option><option value="America/Merida">Merida</option><option value="America/Metlakatla">Metlakatla</option><option value="America/Mexico_City">Mexico City</option><option value="America/Miquelon">Miquelon</option><option value="America/Moncton">Moncton</option><option value="America/Monterrey">Monterrey</option><option value="America/Montevideo">Montevideo</option><option value="America/Montserrat">Montserrat</option><option value="America/Nassau">Nassau</option><option value="America/New_York">New York</option><option value="America/Nipigon">Nipigon</option><option value="America/Nome">Nome</option><option value="America/Noronha">Noronha</option><option value="America/North_Dakota/Beulah">North Dakota - Beulah</option><option value="America/North_Dakota/Center">North Dakota - Center</option><option value="America/North_Dakota/New_Salem">North Dakota - New Salem</option><option value="America/Ojinaga">Ojinaga</option><option value="America/Panama">Panama</option><option value="America/Pangnirtung">Pangnirtung</option><option value="America/Paramaribo">Paramaribo</option><option value="America/Phoenix">Phoenix</option><option value="America/Port-au-Prince">Port-au-Prince</option><option value="America/Port_of_Spain">Port of Spain</option><option value="America/Porto_Velho">Porto Velho</option><option value="America/Puerto_Rico">Puerto Rico</option><option value="America/Rainy_River">Rainy River</option><option value="America/Rankin_Inlet">Rankin Inlet</option><option value="America/Recife">Recife</option><option value="America/Regina">Regina</option><option value="America/Resolute">Resolute</option><option value="America/Rio_Branco">Rio Branco</option><option value="America/Santa_Isabel">Santa Isabel</option><option value="America/Santarem">Santarem</option><option value="America/Santiago">Santiago</option><option value="America/Santo_Domingo">Santo Domingo</option><option value="America/Sao_Paulo">Sao Paulo</option><option value="America/Scoresbysund">Scoresbysund</option><option value="America/Sitka">Sitka</option><option value="America/St_Barthelemy">St Barthelemy</option><option value="America/St_Johns">St Johns</option><option value="America/St_Kitts">St Kitts</option><option value="America/St_Lucia">St Lucia</option><option value="America/St_Thomas">St Thomas</option><option value="America/St_Vincent">St Vincent</option><option value="America/Swift_Current">Swift Current</option><option value="America/Tegucigalpa">Tegucigalpa</option><option value="America/Thule">Thule</option><option value="America/Thunder_Bay">Thunder Bay</option><option value="America/Tijuana">Tijuana</option><option value="America/Toronto">Toronto</option><option value="America/Tortola">Tortola</option><option value="America/Vancouver">Vancouver</option><option value="America/Whitehorse">Whitehorse</option><option value="America/Winnipeg">Winnipeg</option><option value="America/Yakutat">Yakutat</option><option value="America/Yellowknife">Yellowknife</option>
			</optgroup>
			<optgroup label="Antarctica">
				<option value="Antarctica/Casey">Casey</option><option value="Antarctica/Davis">Davis</option><option value="Antarctica/DumontDUrville">DumontDUrville</option><option value="Antarctica/Macquarie">Macquarie</option><option value="Antarctica/Mawson">Mawson</option><option value="Antarctica/McMurdo">McMurdo</option><option value="Antarctica/Palmer">Palmer</option><option value="Antarctica/Rothera">Rothera</option><option value="Antarctica/Syowa">Syowa</option><option value="Antarctica/Troll">Troll</option><option value="Antarctica/Vostok">Vostok</option>
			</optgroup>
			<optgroup label="Arctic">
				<option value="Arctic/Longyearbyen">Longyearbyen</option>
			</optgroup>
			<optgroup label="Asia">
				<option value="Asia/Aden">Aden</option><option value="Asia/Almaty">Almaty</option><option value="Asia/Amman">Amman</option><option value="Asia/Anadyr">Anadyr</option><option value="Asia/Aqtau">Aqtau</option><option value="Asia/Aqtobe">Aqtobe</option><option value="Asia/Ashgabat">Ashgabat</option><option value="Asia/Baghdad">Baghdad</option><option value="Asia/Bahrain">Bahrain</option><option value="Asia/Baku">Baku</option><option value="Asia/Bangkok">Bangkok</option><option value="Asia/Beirut">Beirut</option><option value="Asia/Bishkek">Bishkek</option><option value="Asia/Brunei">Brunei</option><option value="Asia/Chita">Chita</option><option value="Asia/Choibalsan">Choibalsan</option><option value="Asia/Colombo">Colombo</option><option value="Asia/Damascus">Damascus</option><option value="Asia/Dhaka">Dhaka</option><option value="Asia/Dili">Dili</option><option value="Asia/Dubai">Dubai</option><option value="Asia/Dushanbe">Dushanbe</option><option value="Asia/Gaza">Gaza</option><option value="Asia/Hebron">Hebron</option><option value="Asia/Ho_Chi_Minh">Ho Chi Minh</option><option value="Asia/Hong_Kong">Hong Kong</option><option value="Asia/Hovd">Hovd</option><option value="Asia/Irkutsk">Irkutsk</option><option value="Asia/Jakarta">Jakarta</option><option value="Asia/Jayapura">Jayapura</option><option value="Asia/Jerusalem">Jerusalem</option><option value="Asia/Kabul">Kabul</option><option value="Asia/Kamchatka">Kamchatka</option><option value="Asia/Karachi">Karachi</option><option value="Asia/Kathmandu">Kathmandu</option><option value="Asia/Khandyga">Khandyga</option><option value="Asia/Kolkata">Kolkata</option><option value="Asia/Krasnoyarsk">Krasnoyarsk</option><option value="Asia/Kuala_Lumpur">Kuala Lumpur</option><option value="Asia/Kuching">Kuching</option><option value="Asia/Kuwait">Kuwait</option><option value="Asia/Macau">Macau</option><option value="Asia/Magadan">Magadan</option><option value="Asia/Makassar">Makassar</option><option value="Asia/Manila">Manila</option><option value="Asia/Muscat">Muscat</option><option value="Asia/Nicosia">Nicosia</option><option value="Asia/Novokuznetsk">Novokuznetsk</option><option value="Asia/Novosibirsk">Novosibirsk</option><option value="Asia/Omsk">Omsk</option><option value="Asia/Oral">Oral</option><option value="Asia/Phnom_Penh">Phnom Penh</option><option value="Asia/Pontianak">Pontianak</option><option value="Asia/Pyongyang">Pyongyang</option><option value="Asia/Qatar">Qatar</option><option value="Asia/Qyzylorda">Qyzylorda</option><option value="Asia/Rangoon">Rangoon</option><option value="Asia/Riyadh">Riyadh</option><option value="Asia/Sakhalin">Sakhalin</option><option value="Asia/Samarkand">Samarkand</option><option value="Asia/Seoul">Seoul</option><option value="Asia/Shanghai">Shanghai</option><option value="Asia/Singapore">Singapore</option><option value="Asia/Srednekolymsk">Srednekolymsk</option><option value="Asia/Taipei">Taipei</option><option value="Asia/Tashkent">Tashkent</option><option value="Asia/Tbilisi">Tbilisi</option><option value="Asia/Tehran">Tehran</option><option value="Asia/Thimphu">Thimphu</option><option value="Asia/Tokyo">Tokyo</option><option value="Asia/Ulaanbaatar">Ulaanbaatar</option><option value="Asia/Urumqi">Urumqi</option><option value="Asia/Ust-Nera">Ust-Nera</option><option value="Asia/Vientiane">Vientiane</option><option value="Asia/Vladivostok">Vladivostok</option><option value="Asia/Yakutsk">Yakutsk</option><option value="Asia/Yekaterinburg">Yekaterinburg</option><option value="Asia/Yerevan">Yerevan</option>
			</optgroup>
			<optgroup label="Atlantic">
				<option value="Atlantic/Azores">Azores</option><option value="Atlantic/Bermuda">Bermuda</option><option value="Atlantic/Canary">Canary</option><option value="Atlantic/Cape_Verde">Cape Verde</option><option value="Atlantic/Faroe">Faroe</option><option value="Atlantic/Madeira">Madeira</option><option value="Atlantic/Reykjavik">Reykjavik</option><option value="Atlantic/South_Georgia">South Georgia</option><option value="Atlantic/Stanley">Stanley</option><option value="Atlantic/St_Helena">St Helena</option>
			</optgroup>
			<optgroup label="Australia">
				<option value="Australia/Adelaide">Adelaide</option><option value="Australia/Brisbane">Brisbane</option><option value="Australia/Broken_Hill">Broken Hill</option><option value="Australia/Currie">Currie</option><option value="Australia/Darwin">Darwin</option><option value="Australia/Eucla">Eucla</option><option value="Australia/Hobart">Hobart</option><option value="Australia/Lindeman">Lindeman</option><option value="Australia/Lord_Howe">Lord Howe</option><option value="Australia/Melbourne">Melbourne</option><option value="Australia/Perth">Perth</option><option value="Australia/Sydney">Sydney</option>
			</optgroup>
			<optgroup label="Europe">
				<option value="Europe/Amsterdam">Amsterdam</option><option value="Europe/Andorra">Andorra</option><option value="Europe/Athens">Athens</option><option value="Europe/Belgrade">Belgrade</option><option value="Europe/Berlin">Berlin</option><option value="Europe/Bratislava">Bratislava</option><option value="Europe/Brussels">Brussels</option><option value="Europe/Bucharest">Bucharest</option><option value="Europe/Budapest">Budapest</option><option value="Europe/Busingen">Busingen</option><option value="Europe/Chisinau">Chisinau</option><option value="Europe/Copenhagen">Copenhagen</option><option value="Europe/Dublin">Dublin</option><option value="Europe/Gibraltar">Gibraltar</option><option value="Europe/Guernsey">Guernsey</option><option value="Europe/Helsinki">Helsinki</option><option value="Europe/Isle_of_Man">Isle of Man</option><option value="Europe/Istanbul">Istanbul</option><option value="Europe/Jersey">Jersey</option><option value="Europe/Kaliningrad">Kaliningrad</option><option value="Europe/Kiev">Kiev</option><option value="Europe/Lisbon">Lisbon</option><option value="Europe/Ljubljana">Ljubljana</option><option value="Europe/London">London</option><option value="Europe/Luxembourg">Luxembourg</option><option value="Europe/Madrid">Madrid</option><option value="Europe/Malta">Malta</option><option value="Europe/Mariehamn">Mariehamn</option><option value="Europe/Minsk">Minsk</option><option value="Europe/Monaco">Monaco</option><option value="Europe/Moscow">Moscow</option><option value="Europe/Oslo">Oslo</option><option value="Europe/Paris">Paris</option><option value="Europe/Podgorica">Podgorica</option><option value="Europe/Prague">Prague</option><option value="Europe/Riga">Riga</option><option value="Europe/Rome">Rome</option><option value="Europe/Samara">Samara</option><option value="Europe/San_Marino">San Marino</option><option value="Europe/Sarajevo">Sarajevo</option><option value="Europe/Simferopol">Simferopol</option><option value="Europe/Skopje">Skopje</option><option value="Europe/Sofia">Sofia</option><option value="Europe/Stockholm">Stockholm</option><option value="Europe/Tallinn">Tallinn</option><option value="Europe/Tirane">Tirane</option><option value="Europe/Uzhgorod">Uzhgorod</option><option value="Europe/Vaduz">Vaduz</option><option value="Europe/Vatican">Vatican</option><option value="Europe/Vienna">Vienna</option><option value="Europe/Vilnius">Vilnius</option><option value="Europe/Volgograd">Volgograd</option><option value="Europe/Warsaw">Warsaw</option><option value="Europe/Zagreb">Zagreb</option><option value="Europe/Zaporozhye">Zaporozhye</option><option value="Europe/Zurich">Zurich</option>
			</optgroup>
			<optgroup label="Indian">
				<option value="Indian/Antananarivo">Antananarivo</option><option value="Indian/Chagos">Chagos</option><option value="Indian/Christmas">Christmas</option><option value="Indian/Cocos">Cocos</option><option value="Indian/Comoro">Comoro</option><option value="Indian/Kerguelen">Kerguelen</option><option value="Indian/Mahe">Mahe</option><option value="Indian/Maldives">Maldives</option><option value="Indian/Mauritius">Mauritius</option><option value="Indian/Mayotte">Mayotte</option><option value="Indian/Reunion">Reunion</option>
			</optgroup>
			<optgroup label="Pacific">
				<option value="Pacific/Apia">Apia</option><option value="Pacific/Auckland">Auckland</option><option value="Pacific/Chatham">Chatham</option><option value="Pacific/Chuuk">Chuuk</option><option value="Pacific/Easter">Easter</option><option value="Pacific/Efate">Efate</option><option value="Pacific/Enderbury">Enderbury</option><option value="Pacific/Fakaofo">Fakaofo</option><option value="Pacific/Fiji">Fiji</option><option value="Pacific/Funafuti">Funafuti</option><option value="Pacific/Galapagos">Galapagos</option><option value="Pacific/Gambier">Gambier</option><option value="Pacific/Guadalcanal">Guadalcanal</option><option value="Pacific/Guam">Guam</option><option value="Pacific/Honolulu">Honolulu</option><option value="Pacific/Johnston">Johnston</option><option value="Pacific/Kiritimati">Kiritimati</option><option value="Pacific/Kosrae">Kosrae</option><option value="Pacific/Kwajalein">Kwajalein</option><option value="Pacific/Majuro">Majuro</option><option value="Pacific/Marquesas">Marquesas</option><option value="Pacific/Midway">Midway</option><option value="Pacific/Nauru">Nauru</option><option value="Pacific/Niue">Niue</option><option value="Pacific/Norfolk">Norfolk</option><option value="Pacific/Noumea">Noumea</option><option value="Pacific/Pago_Pago">Pago Pago</option><option value="Pacific/Palau">Palau</option><option value="Pacific/Pitcairn">Pitcairn</option><option value="Pacific/Pohnpei">Pohnpei</option><option value="Pacific/Port_Moresby">Port Moresby</option><option value="Pacific/Rarotonga">Rarotonga</option><option value="Pacific/Saipan">Saipan</option><option value="Pacific/Tahiti">Tahiti</option><option value="Pacific/Tarawa">Tarawa</option><option value="Pacific/Tongatapu">Tongatapu</option><option value="Pacific/Wake">Wake</option><option value="Pacific/Wallis">Wallis</option>
			</optgroup>
			<optgroup label="UTC">
				<option value="UTC">UTC</option>
			</optgroup>';
}
?>
<script src="<?php echo $uri ?>/wp-time-capsule.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript" language="javascript">

	<?php
global $wpdb;
$fcount = $wpdb->get_results('SELECT COUNT(*) as files FROM ' . $wpdb->base_prefix . 'wptc_processed_files');
$fresh = (!empty($fcount) && !empty($fcount[0]->files) && $fcount[0]->files > 0) ? 'yes' : 'no';
?>

	jQuery(document).ready(function ($) {
		var temp_obj = '<?php $config = WPTC_Factory::get('config'); ?>';
		wptc_update_progress = '<?php echo $config->get_option('wptc_update_progress'); ?>';
		adminUrlWptc = '<?php echo network_admin_url(); ?>';
		freshBackupWptc = '<?php echo $fresh; ?>';
		check_cloud_min_php_min_req = '<?php echo check_cloud_min_php_min_req(); ?>';
		var tcStartBackupNow = '<?php echo $tcStartBackupNow; ?>';
		var cur_backup_type = $("#backup_type").val();
		if (cur_backup_type == 'WEEKLYBACKUP' || cur_backup_type == 'AUTOBACKUP') {
			$('#select_wptc_default_schedule').hide();
			$('.init_backup_time_n_zone').html('Timezone');
		}
		//get_current_backup_status_wptc();

		if(tcStartBackupNow){
			wtc_start_backup_func('');
		}

		$('#start_backup_from_settings').click(function(){
			if (jQuery("#start_backup_from_settings").hasClass('disabled')) {
				console.log('button disabled');
				return false;
			}
			start_manual_backup_wptc(this);
		});

		$('#store_in_subfolder').click(function (e) {
			if ($('#store_in_subfolder').is(':checked')) {
				$('.dropbox_location').show('fast', function() {
					$('#dropbox_location').focus();
				});
			} else {
				$('#dropbox_location').val('');
				$('.dropbox_location').hide();
			}
		});

		$("#backup_type").on('change', function(){
			var cur_backup_type = $(this).val();
			if(cur_backup_type == 'WEEKLYBACKUP' || cur_backup_type == 'AUTOBACKUP'){
				$('#select_wptc_default_schedule').hide();
				$('.init_backup_time_n_zone').html('Timezone');
			} else {
				$('#select_wptc_default_schedule').show();
				$('.init_backup_time_n_zone').html('Backup Schedule and Timezone');
			}
		});

		$("#select_wptc_cloud_storage").on('change', function(){
			$(".creds_box_inputs", this_par).hide();
			jQuery('#connect_to_cloud').show();
			jQuery('#s3_seperate_bucket_note, #see_how_to_add_refresh_token_wptc, #gdrive_refresh_token_input_wptc, #google_token_add_btn, #google_limit_reached_text_wptc').hide();
			jQuery('.dummy_select').remove();

			$(".cloud_error_mesg, .cloud_error_mesg_g_drive_token").hide();
			var cur_cloud = $(this).val();
			if(cur_cloud == ""){
				return false;
			}
			var cur_cloud_label = getCloudLabelFromVal(cur_cloud);
			var this_par = $(this).closest(".wcard");
			$("#connect_to_cloud, #save_g_drive_refresh_token").attr("cloud_type", cur_cloud);
			$("#connect_to_cloud").val("Connect to " + cur_cloud_label).show();
			$("#mess").show();
			$("#donot_touch_note").show();
			$("#donot_touch_note_cloud").html(cur_cloud_label);

			if(cur_cloud == 's3'){
				jQuery("#mess, #s3_seperate_bucket_note").toggle();
				if (check_cloud_min_php_min_req.indexOf('s3') == -1) {
					$(".cloud_error_mesg").show();
					$(".cloud_error_mesg").html('Amazon S3 requires PHP v5.3.3+. Please upgrade your PHP to use Amazon S3.');
					jQuery('#connect_to_cloud').hide();
					return false;
				}
				$(".s3_inputs", this_par).show();
			}
			else if(cur_cloud == 'g_drive'){
				if (check_cloud_min_php_min_req.indexOf('gdrive') == -1) {
					$(".cloud_error_mesg").show();
					$(".cloud_error_mesg").html('Google Drive requires PHP v5.4.0+. Please upgrade your PHP to use Google Drive.');
					jQuery('#connect_to_cloud').hide();
					return false;
				}
				$('#see_how_to_add_refresh_token_wptc, #gdrive_refresh_token_input_wptc, #google_token_add_btn, #google_limit_reached_text_wptc').show();
				if (jQuery('#google_token_add_btn').length) {
					$("#connect_to_cloud, #save_g_drive_refresh_token").attr("cloud_type", cur_cloud);
					$("#connect_to_cloud").val("Connect to " + cur_cloud_label).show();
				}
				$(".g_drive_inputs", this_par).show();
			}
		});

		$("#select_wptc_default_repo").on('change', function(){
			var newDefaultRepo = '';
			newDefaultRepo = jQuery(this).val();
			if(!newDefaultRepo){
				return false;
			}
			jQuery.post(ajaxurl, { action : 'change_wptc_default_repo', new_default_repo: newDefaultRepo }, function(data) {
				if(typeof data.success != 'undefined'){
					parent.location.assign('<?php echo network_admin_url('admin.php?page=wp-time-capsule'); ?>');
				}
			});
		});

		// $('#see_how_to_add_refresh_token_wptc').click(function (e) {
		// 	$('#gdrive_refresh_token_input_wptc').toggle();
		// 	if($('#gdrive_refresh_token_input_wptc').is(':visible')){
		// 		$(this).html('Cancel');
		// 	} else {
		// 		$(this).html('Enter token');
		// 	}
		// });

		$('#continue_wptc').click(function(){
			get_deselected_nodes();
			jQuery(this).attr('disabled', 'disabled').addClass('disabled').val('Saving...');
			continue_wptc_obj = this;
			var schedule_time = jQuery( "#select_wptc_default_schedule option:selected" ).val();
			var timezone = jQuery( "#wptc_timezone option:selected" ).val();
			var exclude_extensions = jQuery("input[name='user_excluded_extenstions']").val()
			var user_excluded_files_and_folders = jQuery("#user_excluded_files_and_folders").val()
			var user_include_files_and_folders = jQuery("#user_include_files_and_folders").val()
			var user_included_tables = jQuery("#user_included_tables").val()
			var user_excluded_tables = jQuery("#user_excluded_tables").val()
			var exclude_extensions = jQuery("input[name='user_excluded_extenstions']").val()
			var backup_type = jQuery('#backup_type').val();
			jQuery.post(
				ajaxurl,{
					action : 'save_initial_setup_data_wptc',
					data : {
						schedule_time : schedule_time,
						timezone:timezone,
						exclude_extensions:exclude_extensions,
						user_include_files_and_folders:user_include_files_and_folders,
						user_excluded_files_and_folders:user_excluded_files_and_folders,
						user_included_tables:user_included_tables,
						user_excluded_tables:user_excluded_tables,
						backup_type:backup_type,
					},
				}, function(data) {
					jQuery(continue_wptc_obj).removeClass('disabled').val('Saved');
					parent.location.assign('<?php echo network_admin_url('admin.php?page=wp-time-capsule&new_backup=set'); ?>');
				});
		});
		$('#skip_initial_set_up').click(function(){
			parent.location.assign('<?php echo network_admin_url('admin.php?page=wp-time-capsule&new_backup=set'); ?>');
		});

		$('#continue_to_initial_setup').click(function(){
			jQuery.post(ajaxurl, { action : 'continue_with_wtc' }, function(data) {
				if(data=='authorized'){
					parent.location.assign('<?php echo network_admin_url('admin.php?page=wp-time-capsule&initial_setup=set'); ?>');
				}
				else{
					var data_str = '';
					if(typeof data == 'string'){
						data_str = data;
					}
					parent.location.assign('<?php echo network_admin_url('admin.php?page=wp-time-capsule'); ?>&error='+data_str);;
				}
			});
		});

		$(".wcard").on('keypress', '#wptc_main_acc_email', function(e){
			triggerLoginWptc(e);

		});

		$(".wcard").on('keypress', '#wptc_main_acc_pwd', function(e){
			triggerLoginWptc(e);
		});

		//call trigger when page is load by options on DB
		<?php	if (isset($wptc_timezone) && $wptc_timezone != "") {?>
			$('#wptc_timezone').val('<?php echo $wptc_timezone; ?>');
		<?php	}
?>
	});

	function getCloudLabelFromVal(val){
		if(typeof val == 'undefined' || val == ''){
			return 'Cloud';
		}
		var cloudLabels = {};
		cloudLabels['g_drive'] = 'Google Drive';
		cloudLabels['s3'] = 'Amazon S3';
		cloudLabels['dropbox'] = 'Dropbox';

		return cloudLabels[val];
	}

	function yes_change_acc(){
		document.getElementById('unlink').click();
	}

	function no_change(){
		tb_remove();
	}

	function dropbox_authorize(url) {
		window.open(url);
		document.getElementById('continue').style.display = "block";
		document.getElementById('authorize').style.display = "none";
		document.getElementById('mess').style.display = "none";
		document.getElementById('donot_touch_note').style.display = "none";
	}

	function ChangeAccount(){
		dialog_for_changeAccount();
	}

   //  function include_file_db_init_setup(){
   //  	jQuery.post(ajaxurl, {
   //          action: 'include_file_db_init_setup',
   //  	}, function(data) {
			// var data = jQuery.parseJSON(data);
		 //    console.log("wptc_get_included_file_db_size", data);
			// if (typeof data.status != 'undefined' && data.status == 'same_repo') {
			// 	jQuery("#calculating_file_db_size").hide();
			// 	jQuery("#continue_wptc, #skip_initial_set_up").removeAttr('disabled').toggleClass('disabled')
			// 	return false;
			// }
			// jQuery("#calculating_file_db_size").hide();
			// jQuery("#got_file_db_size").show();
			// jQuery("#included_db_size").html(data.db_size);
			// jQuery("#db_size_in_bytes").html(data.db_size_in_bytes);
			// wptc_db_size_in_bytes = data.db_size_in_bytes;
			// jQuery("#included_file_size").html(data.file_size);
			// jQuery("#file_size_in_bytes").html(data.file_size_in_bytes);
			// wptc_file_size_in_bytes = data.file_size_in_bytes;
   //          jQuery("#continue_wptc, #skip_initial_set_up").removeAttr('disabled').toggleClass('disabled')
			// // change_init_setup_button_state();
			// // jQuery("#wptc_init_toggle_tables").click();
			// // jQuery("#wptc_init_toggle_files").click();
   //  });

	// }

</script>
<script type="text/javascript" language="javascript">
	var service_url_wptc = '<?php echo WPTC_APSERVER_URL;?>';
	var wptcOptionsPageURl = '<?php echo plugins_url('wp-time-capsule'); ?>' ;
</script>
<script src="<?php echo $uri ?>/Views/wptc-plans.js" type="text/javascript" language="javascript"></script>