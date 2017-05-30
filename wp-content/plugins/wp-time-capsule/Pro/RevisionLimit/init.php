<?php

class Wptc_Revision_Limit extends WPTC_Privileges {
	protected $db;
	protected $config;
	protected $logger;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Revision_Limit_Config');
		$this->logger = WPTC_Factory::get('logger');
	}

	public function init() {
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Pro_Factory::get($supposed_hooks_class)->register_hooks();

			$this->update_revision_limit();
		}
	}

	private function is_switch_on()
	{
		return true;
	}

	private function update_revision_limit(){
		$args = $this->config->get_option('privileges_args');

		// dark_debug($args, "--------update_revision_limit--------");

		if(!empty($args)){
			$args = json_decode($args, true);
			$this_class_name = get_class($this);
			$revision_obj = $args[$this_class_name];
			$revision_days = $revision_obj['days'];
		}

		if(empty($revision_days)){
			$revision_days = WPTC_FALLBACK_REVISION_LIMIT_DAYS;
		}

		// dark_debug($revision_days, "--------revision_days--------");

		$this->config->set_option('revision_limit', $revision_days);
	}

}