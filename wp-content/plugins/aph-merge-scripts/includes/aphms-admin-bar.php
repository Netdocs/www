<?php
class Aphms_Admin_Bar {
	
	/**
		Plugin options stored in database
	*/
	private $options;
	
	/**
		Save script loaded by wp_header and wp_footer than used in admin_bar
	*/
	private $scripts = array(
								'header' => array(), 
								'footer' => array()
							);
	
	/**
		Wordpress to_do scripts, used both by mergescripts and admin_bar
	*/
	private $to_do;

	
	/**
		Check whether the method is already executed. This is to prevent the method from executed by other plugins
	*/
	private $is_executed = array('print_scripts' => false,
								'footer_scripts' => false,
								'header_scripts' => false
								);
								
	
	public function __construct ()
	{
		$this->options = get_option(APHMS_OPTION);
	}
	
	public function init()
	{
		if (!$this->options['manage_scripts'])
			return;
		
		add_action( 'wp_ajax_nopriv_aphms-save-assets' , array( $this , 'ajax_no_priv' ) );
		add_action( 'wp_ajax_aphms-save-assets', array($this, 'ajax_save_assets') );
		
		if (is_admin())
			return;	

		add_action( 'admin_bar_init', array($this, 'admin_bar_init'));
	}
	
	public function admin_bar_init()
	{
		add_action( 'wp_print_scripts', array( $this, 'load_current_scripts' ),10 );
		add_action( 'wp_head', array( $this, 'load_head_scripts' ),9999 );
		add_action( 'wp_footer', array( $this, 'load_footer_scripts' ),9999 );
		
		// Print all script into admin bar
		add_action( 'wp_footer', array( $this, 'print_all_scripts' ),9999 );
		
		// add_action( 'wp_head', array( $this, 'deregisterScript2' ) );
		add_action( 'wp_enqueue_scripts', array($this, 'register_scripts') );
		add_action( 'admin_bar_menu', array($this, 'aph_admin_bar'));
	}
	
	public function ajax_no_priv()
	{
		// echo 'xxx';  die;
		
	}
	
	// When the Save button is clicked 
	public function ajax_save_assets()
	{
		parse_str($_POST['input'], $inputs);
		$all_handles = explode(',', $inputs['all_handles']);
		$to_do = explode(',', $inputs['to_do_handles']);
		
		// $scripts = json_decode(file_get_contents(APHMS_PLUGIN_PATH . APHMS_DS . 'temp' . APHMS_DS . 'temp.dt'), true);
		$scripts = json_decode($inputs['resource_handles'], true);
		
		foreach ($scripts as $pos => $handles) 
		{
			foreach ($handles as $handle => $handle_value)
			{
				// Key send by ajax will replace . to _, so we use in_array, instead of key_exists
				if (in_array($handle, $inputs))
				{
					if (in_array($handle, $this->options['excluded_handles']))
					{
						unset($this->options['excluded_handles'][$handle]);
					} 					
				} else {
					// unset($this->options['rest_of_handles'][$handle]);
					if (!in_array($handle, $this->options['excluded_handles']))
						$this->options['excluded_handles'][$handle] = $handle;
				}
				
				if (!in_array($handle, $to_do))
					$this->options['rest_of_handles']['script'][$pos][$handle] = $handle_value;
			}
		}
		
		// $this->options['rest_of_handles']['script'] = $rest;
		update_option( APHMS_OPTION, $this->options );
		wp_send_json_success(
			array(
				'msg' => '<strong>Saved</strong>. If you uncheck some of handle, clear cache file to view the changes',
				'script' => $this->options
			)
		);
	}
	
	public function aph_admin_bar()
	{
		global $wp_admin_bar;
		$menu_id = 'aphms-admin-bar';
		$wp_admin_bar->add_menu(
			array('id' => $menu_id, 
					'title' => _('APH Show Scripts'),  
					'parent' => 'top-secondary'
				)
		);		
	}
	
	public function register_scripts()
	{
		wp_register_style( 'aph-merge-script', APHMS_PLUGIN_URL . '/assets/css/aphms.css?rand='.time(), '', APHMS_PLUGIN_VERSION);
		wp_enqueue_style('aph-merge-script');
		
		wp_enqueue_script('aphms-admin-bar', APHMS_PLUGIN_URL . '/assets/js/aphms.js?r='.time(), array('jquery'), APHMS_PLUGIN_VERSION, true);
		wp_localize_script (
			'aphms-admin-bar', 
			'aphms', 
			array(
				'nonce'	=> wp_create_nonce('aph-merge-scripts'),
				'ajaxurl' => admin_url('admin-ajax.php')
				
			)
		);
	}
	
	public function load_head_scripts()
	{
		if ($this->is_executed['header_scripts'])
			return;

		global $wp_scripts;
		
		$this->scripts['header'] = array();
		foreach ($wp_scripts->done as $handle)
		{
			if (key_exists($handle, $wp_scripts->registered))
			{
				$this->scripts['header'][$handle]['group'] = $wp_scripts->groups[$handle];
				foreach ($wp_scripts->registered[$handle] as $key => $val)
				{
					$this->scripts['header'][$handle][$key] = $val;
				}
			}
		}
				
		$this->is_executed['header_scripts'] = true;
	}
	
	public function load_footer_scripts()
	{
		if ($this->is_executed['footer_scripts'])
			return;
		
		global $wp_scripts;

		$this->scripts['footer'] = array();
		foreach ($wp_scripts->done as $handle)
		{
			if (key_exists($handle, $this->scripts['header']))
				continue;
			
			if (key_exists($handle, $wp_scripts->registered))
			{
				// $this->scripts['footer'][$handle]['group'] = $wp_scripts->groups[$handle];
				foreach ($wp_scripts->registered[$handle] as $key => $val)
				{
					$this->scripts['footer'][$handle][$key] = $val;
				}
			}
		}
		$this->is_executed['footer_scripts'] = true;
	}
	
	public function load_current_scripts()
	{
		if ($this->is_executed['print_scripts'])
			return;
		
		global $wp_scripts;
		$aphms_scripts 	= $wp_scripts;
		
		$queues 		= $wp_scripts->queue;
		$aphms_scripts->all_deps($queues);	
		$this->to_do  	= $aphms_scripts->to_do;
		
		$this->is_executed['print_scripts'] = true;
	}
	
	public function print_all_scripts()
	{
		$notMerged = $not_detected = $not_in_current = 0;

		$merged['header'] = array_merge($this->scripts['header'], $this->options['rest_of_handles']['script']['header']);
		$merged['footer'] = array_merge($this->scripts['footer'], $this->options['rest_of_handles']['script']['footer']);

		$all_handles = array();
		$rest = array('header' => $this->scripts['header'], 'footer' => $this->scripts['footer']);
		
		echo '
		
		<div id="aphms-panel">
			<p>Choose scripts you want to merge. <a href="#" id="aphms-adminbar-showinfo">Show Info</a></p>
			<div id="aphms-adminbar-info">
				<p>Note that the merged scripts will be deregistered, this will make wordpress deregister the dependent script too. For example: admin-bar script need jquery, so if we merge the jquery script, the admin-bar script will be deregistered too</p>
				<p>If the admin-bar script included in the merged script, then it will be ok, otherwise, it will not be loaded  by the wordpress that probably cause your page has not functionality as it should</p>
			</div>
			<form id="aphms-form" method="post" action="">';
			
				foreach ($merged as $pos => $handles)
				{
					echo '<h1>' . ucfirst($pos) . '</h1>
					<ul>';
						// echo '<pre>'; print_r($this->to_do);
						foreach ($handles as $handle => $val)
						{
							$all_handles[] = $handle;
							
							/* If used in all pages both in wp_print_scripts and database */
							$checked = (in_array($handle, $this->to_do) 
										|| key_exists($handle, $this->options['rest_of_handles']['script'][$pos])
										) && !$this->skip_handle($handle) ? ' checked="checked"' : '';
							
							/* Counting scripts that will not processed  */
							if (
								$this->skip_handle($handle) ||
								(!in_array($handle, $this->to_do) 
								&& !key_exists($handle, $this->options['rest_of_handles']['script'][$pos])) 
							) {
								$notMerged++;
							}
							
							/* Styling scripts that exists in wp_print_scripts */
							if (in_array($handle, $this->to_do)) {
							
								$class =  ' class="aphms-autoloadhandle"';
							} 
							
							/* Styling scripts that not used in current page */
							else if (!in_array($handle, $this->to_do) 
									 && !key_exists($handle, $this->scripts[$pos])) {
								
									$class = ' class="aphms-notincurrent"';
									$not_in_current++;
							} 
							
							else {
								$class = '';
								$not_detected++;
							}
							
							echo '<li class="aphms-clearfix">
										<input type="checkbox" id="aphms-'.$handle.'" name="'.$handle.'" value="'.$handle.'"'.$checked.'/>
										<div>
											<label for="aphms-'.$handle.'"'.$class.'>' . $handle . '</label>
											<a target="_blank" href="'.$val['src'].'">Open File</a>
										</div>';
									if ($val['deps'])
									{
										echo '<div class="aphms-deps-list">Dependency: ' . join($val['deps'], ', ') . '</div>';
									}
										
							echo '
								</li>';	
								
						}
					echo '</ul>';
				}
				echo'
				<input type="hidden" name="all_handles" value="'.join($all_handles, ',').'"/>
				<input type="hidden" name="to_do_handles" value="'.join($this->to_do, ',').'"/>
				
				<div class="aphms-legend aphms-autoloadhandle aphms-clearfix">
					<div></div><span>Auto detected - used in this page</span>
				</div>';
				if ($not_detected)
				{
					echo '<div class="aphms-legend aphms-notdetected aphms-clearfix">
							<div></div><span>Not auto detected - used in this page</span>
						</div>';
				}
				
				if ($not_in_current)
				{
					echo '<div class="aphms-legend aphms-notincurrent aphms-clearfix">
							<div></div><span>Not used in this page but probably used in other pages (added manually)</span>
						</div>';
				}
				
				echo '<textarea name="resource_handles" style="display:none">' . json_encode($rest, JSON_HEX_QUOT) . '</textarea>';
				
				echo '
				<div id="aphms-submit-wrapper">
					<a href="#" class="button submit">Save</a>
					<span class="aphms-loader"></span>
					<span class="aphms-msg"></span>
				</div>
			</form>';
			if ($notMerged)
			{
				echo '<span id="aphms-notmerged" style="display:none">' . $notMerged . '</span>';;
			}
		echo '
		</div>
		';
	}
	
	private function skip_handle($handle)
	{
		$excluded_script = $this->options['excluded_handles'];

		if (in_array($handle, $excluded_script))
			return true;
		else 
			return false;
	}
	
	public function display_notice($msg, $type) {
		add_settings_error(
			'aph_notice',
			'option-notice',
			$msg,
			$type
		);
	}
}