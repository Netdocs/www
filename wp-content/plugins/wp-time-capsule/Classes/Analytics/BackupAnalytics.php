<?php

class Wptc_Backup_Analytics extends Wptc_Analytics {
	protected $config;
	protected $logger;
	private $cron_server_curl;

	public function __construct() {
		$this->config = WPTC_Factory::get('config');
		$this->cron_server_curl = WPTC_Base_Factory::get('Wptc_Cron_Server_Curl_Wrapper');
		$this->backup_obj = WPTC_Base_Factory::get('Wptc_Backup');
	}

	public function get_then_send_any_backup_completed_details() {
		dark_debug_func_map(func_get_args(), "--- function " . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);
		$post_arr = array(
			'event' => 'update_site_meta',
			'size' => $this->get_total_size_of_processsed_files_during_current_backup(),
			'noOfFiles' => $this->get_recent_backup_processsed_files(),
			'no_of_calls' => $this->get_last_value_of_backup_calls_record(),
			'plugin_version' => WPTC_VERSION,
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function get_then_send_first_backup_completed_details() {
		dark_debug_func_map(func_get_args(), "--- function " . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);
		$post_arr = array(
			'event' => 'update_complete_backup',
			'size' => $this->get_total_size_of_processsed_files_during_current_backup(),
			'noOfFiles' => $this->get_recent_backup_processsed_files(),
			'no_of_calls' => $this->get_last_value_of_backup_calls_record(),
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function send_backups_data_to_server(){
		set_time_limit(300);
		$backups_data = array();
		$backups_data['event'] = 'update_full_backup_data';
		$backups_data['app_id'] = $this->config->get_option('appID');
		$backups_data['timeZone'] = $this->config->get_option('wptc_timezone');
		$backups_data['cloud_account'] = $this->config->get_option('default_repo');
		$backups_data['revision_limit'] = $this->config->get_option('revision_limit');
		$backups_data['plugin_version'] = WPTC_VERSION;

		$processed_files = WPTC_Factory::get('processed-files', true);
		$backups_data['no_of_backups'] = $processed_files->get_no_of_backups();

		$backups_data['backups'] = $processed_files->get_backups_meta();

		// dark_debug($backups_data, '--------$backups_data--------');
		// dark_debug(json_encode($backups_data), '--------$backups_data--------');
		$this->cron_server_curl->do_call('users/stats', $backups_data);
	}

	public function send_basic_analytics(){
		dark_debug_func_map(func_get_args(), "--- function " . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);
		$post_arr = array(
			'event' => 'update_site_meta',
			'size' => '0',
			'noOfFiles' => '0',
			'no_of_calls' => '0',
			'plugin_version' => WPTC_VERSION,
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);

		$this->config->send_ip_address_to_server();
	}

	public function send_database_size(){
		dark_debug_func_map(func_get_args(), "--- function " . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);
		$post_arr = array(
			'event' => 'update_database_info',
			'db_size' => $this->get_recent_database_size(),
		);
		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	private function get_last_value_of_backup_calls_record() {
		$backup_calls_record = $this->get_backup_calls_record_arr();

		if (!$backup_calls_record || !is_array($backup_calls_record)) {
			return 0;
		}
		$last_record_val = $this->get_last_value_of_array($backup_calls_record);

		return $last_record_val;
	}

	private function get_last_value_of_array($array = array()) {
		if (!is_array($array)) {
			return 0;
		}

		$last_record_val = '';
		foreach ($array as $v) {
			$last_record_val = $v;
		}

		return $last_record_val;
	}

	public function get_total_size_of_processsed_files_during_current_backup() {
		$current_backup_id = getTcCookie('backupID');
		if (!$current_backup_id) {
			return 0;
		}
		// $current_backup_id = '1467191833.5798';
		global $wpdb;
		// $prepared_query = $wpdb->prepare('SELECT SUM(`uploaded_file_size`) FROM ' . $wpdb->base_prefix . 'wptc_processed_files WHERE backupID = %0.4f', $current_backup_id);
		$get_size = "SELECT SUM(uploaded_file_size) FROM " . $wpdb->base_prefix . "wptc_processed_files WHERE backupID = $current_backup_id";
		$total_size = $wpdb->get_var($get_size);
		return $total_size;
	}

	public function get_total_size_of_processsed_files_during_all_backup() {
		global $wpdb;
		$get_size = "SELECT SUM(uploaded_file_size) FROM " . $wpdb->base_prefix . "wptc_processed_files";
		$total_size = $wpdb->get_var($get_size);
		return $total_size;
	}

	public function get_total_backup_processsed_files() {
		global $wpdb;
		$total_files = "SELECT COUNT(*) FROM " . $wpdb->base_prefix . "wptc_processed_files WHERE is_dir != 1";
		return $wpdb->get_var($total_files);
	}
	public function get_recent_database_size(){
		global $wpdb;
		$sql = "SELECT uploaded_file_size FROM {$wpdb->base_prefix}wptc_processed_files WHERE file LIKE '%-backup.sql%' ORDER BY  file_id DESC LIMIT 1";
		$size = $wpdb->get_var($sql);
		if (empty($size)) {
			return 7;
		}
		return $size;
	}

	public function get_recent_backup_processsed_files() {
		$current_backup_id = getTcCookie('backupID');
		if (!$current_backup_id) {
			return 0;
		}
		global $wpdb;
		$prepared_query = $wpdb->prepare('SELECT COUNT(*) FROM ' . $wpdb->base_prefix . 'wptc_processed_files WHERE is_dir != %d AND backupID = %s', 1, $current_backup_id);
		return $wpdb->get_var($prepared_query);
	}

	public function get_backup_calls_record_arr() {
		$call_records = $this->config->get_option('backup_calls_record');
		if ($call_records) {
			return json_decode($call_records, true);
		}
		return array();
	}

	public function update_backup_calls_record() {
		$current_backup_id = getTcCookie('backupID');
		if (!$current_backup_id) {
			return 0;
		}

		$call_records = $this->get_backup_calls_record_arr();

		if (empty($call_records[$current_backup_id])) {
			$call_records[$current_backup_id] = 0;
		}

		$call_records[$current_backup_id] += 1;

		$this->config->set_option('backup_calls_record', json_encode($call_records, JSON_UNESCAPED_SLASHES));
	}

	public function flush_backup_calls_record() {
		$this->config->set_option('backup_calls_record', false);
	}

	public function send_cloud_account_used() {
		dark_debug_func_map(func_get_args(), "--- function " . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);

		$post_arr = array(
			'event' => 'update_cloud_account',

			'cloud_account' => $this->config->get_option('default_repo'),
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}

	public function reset_stats(){
		dark_debug_func_map(func_get_args(), "--- function " . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);
		$post_arr = array(
			'event' => 'update_complete_backup',
			'size' => 0,
			'noOfFiles' => 0,
			'no_of_calls' => 0,
		);

		$this->cron_server_curl->do_call('users/stats', $post_arr);

		sleep(2);

		$post_arr = array(
			'event' => 'update_complete_backup',
			'size' =>  $this->get_total_size_of_processsed_files_during_all_backup(),
			'noOfFiles' =>  $this->get_total_backup_processsed_files(),
			'no_of_calls' => $this->get_last_value_of_backup_calls_record(),
		);
		$this->cron_server_curl->do_call('users/stats', $post_arr);
	}
}