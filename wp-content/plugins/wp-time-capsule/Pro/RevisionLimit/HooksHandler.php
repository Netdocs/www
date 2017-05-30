<?php

class Wptc_Revision_Limit_Hooks_Hanlder extends Wptc_Base_Hooks_Handler {
	const JS_URL = '/Pro/BackupBeforeUpdate/init.js';

	protected $config;
	protected $Revision_Limit_obj;
	protected $backup_before_auto_update_obj;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Revision_Limit_Config');
	}
}