<?php
/**
* A class with functions the perform a backup of WordPress
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

class WPTC_DropboxFacade {

	const CONSUMER_KEY = 'hp7g2dq9tc81dwe';
	const CONSUMER_SECRET = '2yj55pkigkh8586';
	const RETRY_COUNT = 3;

	private static $instance = null;

	private
	$dropbox,
	$request_token,
	$access_token,
	$oauth_state,
	$oauth,
	$account_info_cache,
	$config,
	$directory_cache = array()
	;

	public function __construct() {
		$this->config = WPTC_Factory::get('config');
		$this->init();
	}

	public function init() {
		try {
			if (!extension_loaded('curl')) {
				throw new Exception(sprintf(
					__('The cURL extension is not loaded. %sPlease ensure its installed and activated.%s', 'wpbtd'),
					'<a href="http://php.net/manual/en/curl.installation.php">',
					'</a>'
				));
			}

			$this->oauth = new WPTC_Dropbox_OAuth_Consumer_Curl(self::CONSUMER_KEY, self::CONSUMER_SECRET);

			$this->oauth_state = $this->config->get_option('oauth_state');
			$this->request_token = $this->get_token('request');
			$this->access_token = $this->get_token('access');

		if ($this->oauth_state == 'request') {
			//If we have not got an access token then we need to grab one
			try {
				$this->oauth->setToken($this->request_token);
				$this->access_token = $this->oauth->getAccessToken();
				$this->oauth_state = 'access';
				$this->oauth->setToken($this->access_token);
				$this->save_tokens();
			} catch (Exception $e) {
				//Supress the error because unlink, then init should be called
			}
		} elseif ($this->oauth_state == 'access') {
			$this->oauth->setToken($this->access_token);
		} else {
			//If we don't have an acess token then lets setup a new request
			$this->request_token = $this->oauth->getRequestToken();
			$this->oauth->setToken($this->request_token);
			$this->oauth_state = 'request';
			$this->save_tokens();
		}


			$this->dropbox = new WPTC_Dropbox_API($this->oauth);
			$this->dropbox->setTracker(new WPTC_UploadTracker());
		}
		catch (Exception $e) {
			if ($this->oauth_state != 'request') {
				return $this->process_exception($e);
			}
		}
	}

	private function is_auth_error($http_status) {
		if ($http_status === 403 || $http_status === 401) {
			return true;
		}
		return false;
	}

	private function get_token($type) {
		$token = $this->config->get_option("{$type}_token");
		$token_secret = $this->config->get_option("{$type}_token_secret");
		$ret = new stdClass;
		$ret->oauth_token = null;
		$ret->oauth_token_secret = null;

		if ($token && $token_secret) {
			$ret = new stdClass;
			$ret->oauth_token = $token;
			$ret->oauth_token_secret = $token_secret;
		}

		return $ret;
	}

	public function is_authorized() {
		try {
			if (!$this->config->is_main_account_authorized()) {
				return false;
			}

			$this->get_account_info();
			$this->ping_server_if_storage_quota_low();

		} catch (Exception $e) {
			if ($this->oauth_state != 'request') {
				return $this->process_exception($e);
			}
			return false;
		}
		return true;
	}

	public function ping_server_if_storage_quota_low() {
		$account_info = $this->get_account_info();

		$total_quota = $account_info->quota_info->quota;
		$used_quota = $account_info->quota_info->normal + $account_info->quota_info->shared;

		$remaining_quota = $total_quota - $used_quota;

		dark_debug($remaining_quota, "--------remaining_quota--------");

		if (!empty($remaining_quota) && $remaining_quota <= 51200) {
			$name = $account_info->display_name;
			$connectedEmail = $account_info->email;
			$cloudAccount = $this->config->get_option('default_repo');

			$err_info = array(
				'name' => $name,
				'cloudAccount' => $cloudAccount,
				'connectedEmail' => $connectedEmail,
				'type' => 'limit_exceed',
			);

			error_alert_wptc_server($err_info);
		}

		return true;
	}

	public function get_authorize_url() {
		return $this->oauth->getAuthoriseUrl();
	}

	public function get_account_info() {
		if (!isset($this->account_info_cache)) {
			if (!$this->dropbox) {
				return false;
			}
			$response = $this->dropbox->accountInfo();
			$this->account_info_cache = $response['body'];
		}

		return $this->account_info_cache;
	}

	private function save_tokens() {
		$this->config->set_option('oauth_state', $this->oauth_state);

		if ($this->request_token) {
			$this->config->set_option('request_token', $this->request_token->oauth_token);
			$this->config->set_option('request_token_secret', $this->request_token->oauth_token_secret);
		} else {
			$this->config->set_option('request_token', null);
			$this->config->set_option('request_token_secret', null);
		}

		if ($this->access_token) {
			$this->config->set_option('access_token', $this->access_token->oauth_token);
			$this->config->set_option('access_token_secret', $this->access_token->oauth_token_secret);
		} else {
			$this->config->set_option('access_token', null);
			$this->config->set_option('access_token_secret', null);
		}

		return $this;
	}

	public function upload_file($path, $file) {
		$i = 0;
		$backup_id = getTcCookie('backupID');
		while ($i++ < self::RETRY_COUNT) {
			try {
				return $this->dropbox->putFile($file, remove_secret($file), $path);
			} catch (Exception $e) {
				if ($i > self::RETRY_COUNT) {
					$base_name_file = basename($file);
					return array('error' => "File upload error ($file).");
				} else {
					dark_debug($e->getMessage(), '-----------Retry uploading-------------');
					dark_debug($file,'--------------$file-------------');
					WPTC_Factory::get('logger')->log(__("Retry uploading " . $e->getMessage(), 'wptc'), 'backup_progress', $backup_id);
				}
			}
		}
		throw $e;
	}

	public function download_file($path, $file, $revision = '', $isChunkDownload = null, $download_current_path = null) {
		$i = 0;
		$restore_action_id = $this->config->get_option('restore_action_id');
		while ($i++ < self::RETRY_COUNT) {
			try {
				return $this->dropbox->getFile($path, $file, $revision);
			} catch (Exception $e) {
				if ($i > self::RETRY_COUNT) {
					$base_name_file = basename($file);
					return array('error' => $e->getMessage()." - File chunk download error ($file).");
				} else {
					WPTC_Factory::get('logger')->log(__("Retry downloading " . $e->getMessage(), 'wptc'), 'restore_process', $restore_action_id);
				}
			}
		}
	}

	public function chunk_download_file($path, $file, $revision = '', $isChunkDownload = null, $extra, $meta_file_download = null) {
		$i = 0;
		$restore_action_id = $this->config->get_option('restore_action_id');
		while ($i++ < self::RETRY_COUNT) {
			try {
				return $this->dropbox->chunkedDownload($path, $file, $revision, $isChunkDownload, $meta_file_download);
			} catch (Exception $e) {
				if ($i > self::RETRY_COUNT) {
					$base_name_file = basename($file);
					return array('error' => $e->getMessage()." - File chunk download error ($file).");
				} else {
					WPTC_Factory::get('logger')->log(__("Retry chunk downloading " . $e->getMessage(), 'wptc'), 'restore_process', $restore_action_id);
				}
			}
		}
	}

	public function chunk_upload_file($path, $file, $processed_file, $starting_backup_path_time = false, $meta_data_backup = null) {
		dark_debug_func_map(func_get_args(), "--------" . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);
		$offest = $upload_id = null;
		if ($meta_data_backup == 1) {
			$offest = $processed_file['offset'];
			$upload_id = $processed_file['upload_id'];
		} else if ($processed_file ) {
			$offest = $processed_file->offset;
			$upload_id = $processed_file->uploadid;
		}
		return $this->dropbox->chunkedUpload($file, remove_secret($file), $path, true, $offest, $upload_id, $starting_backup_path_time);
	}

	public function get_file_details($path) {
		return $this->dropbox->metaData($path);
	}

	public function delete_file($file) {
		return $this->dropbox->delete($file);
	}

	public function create_directory($path) {
		//dark_debug_func_map(func_get_args(), "--- function " . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);
		try {
			$this->dropbox->create($path);
		} catch (Exception $e) {}
	}

	public function get_directory_contents($path) {
		if (!isset($this->directory_cache[$path])) {
			try {
				$this->directory_cache[$path] = array();
				$response = $this->dropbox->metaData($path, null, 10000, false, false); //($path, null, 10000, false, false)
				foreach ($response['body']->contents as $val) {
					if (!$val->is_dir) {
						$this->directory_cache[$path][] = basename($val->path);
					}
				}
			} catch (Exception $e) {
				$this->create_directory($path);
			}
		}

		return $this->directory_cache[$path];
	}

	public function unlink_account() {
		$this->oauth->resetToken();
		$this->request_token = null;
		$this->access_token = null;
		$this->oauth_state = null;

		return $this->save_tokens();
	}

	public function get_quota_div() {
		$account_info = $this->get_account_info();
		$return_var = '';
		$return_var = 'Dropbox - ' . $account_info->email;
		return $return_var;
	}

	private function process_exception($e){
		$err_msg = $e->getMessage();
		$http_code = $e->getCode();

		dark_debug($http_code, '---------------$http_code-----------------');
		dark_debug($err_msg, "--------e---dropbox init-----");

		$this->config->set_option('last_cloud_error', $err_msg);

		if ($this->is_auth_error($http_code)) {
			$this->config->set_option('default_repo', false);
		}
		if(is_wptc_server_req()){
			backup_proper_exit_wptc($err_msg);
		} else if((isset($_POST['action']) && $_POST['action'] === 'get_dropbox_authorize_url_wptc')) {
			die('(HTTP Code :'.$http_code.')'.$err_msg);
		} else {
			$this->config->set_option('show_user_php_error', '(HTTP Code :'.$http_code.')'.$err_msg);
			return 'TEMPORARY_CONNECTION_ISSUE';
		}
	}
}