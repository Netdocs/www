<?php

class Wptc_Revision_Limit_Config extends Wptc_Base_Config {
	protected $config;
	protected $used_options;
	protected $used_wp_options;

	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->set_used_options();
	}

	protected function set_used_options() {
		$this->used_options = array(
			'privileges_wptc' => 'retainable',
			'privileges_args' => 'retainable',
			'revision_limit' => 'retainable',
		);
		$this->used_wp_options = array(
		);
	}

	public function get_all_as_str() {
		$str = '' . PHP_EOL;
		foreach ($this->used_options as $option_name => $v) {
			$str .= $option_name . ' -> ' . $this->get_option($option_name) . PHP_EOL;
		}
		$str .= 'Lock -> ' . get_option('auto_updater.lock');
		return $str;
	}

}