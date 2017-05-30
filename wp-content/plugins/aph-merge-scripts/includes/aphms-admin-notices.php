<?php
class Aphms_Admin_Notices
{
	private $admin_notice;
	
    public function __construct()
    {
		$this->admin_notice = get_option('aphms_notice', array());
		register_activation_hook ( APHMS_PLUGIN_PATH . APHMS_DS . APHMS_PLUGIN_FILE_NAME, array($this, 'activate_plugin') );
		add_action('admin_notices', array($this, 'display_notices'));
    }
	
	public function activate_plugin()
	{
		if (!$this->admin_notice) {
			update_option(APHMS_OPTION_NOTICE, array());
		}
	}
	
	/**
		Store notice to database
		@$type success, error, info
	*/
	public function add_notice($msg, $type = 'success', $display_once = true, $dismissible = false)
	{
		$new_notice = array('msg' => $msg,
							'type' => $type,
							'display_once' => $display_once,
							'dismissible' => $dismissible
						);
		
		if (!in_array($new_notice, $this->admin_notice)) {
			$this->admin_notice[] = $new_notice;
			$this->update_option();
		}
	}
	
	
	public function display_notices()	
	{
		foreach ($this->admin_notice as $key => $notice)
		{
			$dismissible = $notice['dismissible'] ? ' is-dismissible' : '';
			echo '<div class="notice notice-' . $notice['type'] . $dismissible.'">
					<p>' . $notice['msg'] . '</p>
				</div>';
					
			if ($notice['display_once'])
				unset($this->admin_notice[$key]);
		}
		
		$this->update_option();
	}
	
	private function update_option()
	{
		update_option(APHMS_OPTION_NOTICE, $this->admin_notice);
	}
}
?>