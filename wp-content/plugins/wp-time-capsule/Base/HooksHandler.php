<?php

class Wptc_Base_Hooks_Handler {
	public function __construct() {

	}

	public function action_hanlder($arg1 = '', $arg2 = null, $arg3 = null, $arg4 = null) {
		dark_debug_func_map(func_get_args(), "--- function " . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);
	}

	public function filter_hanlder($data, $dets1 = null, $dets2 = null, $dets3 = null) {
		dark_debug_func_map(func_get_args(), "--- function " . __FUNCTION__ . "--------", WPTC_WPTC_DARK_TEST_PRINT_ALL);
		return $data;
	}

	//WPTC's specific hooks start

	public function just_initialized_wptc_h($arg1 = '', $arg2 = null, $arg3 = null, $arg4 = null) {
		wptc_init_flags();
		WPTC_Base_Factory::get('Wptc_Backup')->init();
		WPTC_Base_Factory::get('Wptc_Common')->init();
		WPTC_Base_Factory::get('Wptc_Analytics')->init();
		WPTC_Base_Factory::get('Wptc_Exclude')->init();
		WPTC_Base_Factory::get('Wptc_Sentry')->init();
	}

	//WPTC's specific hooks end

}