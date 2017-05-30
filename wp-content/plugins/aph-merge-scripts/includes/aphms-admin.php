<?php
class Aphms_Admin
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
	
	/* Admin notices */
	private $admin_notices;
	
	/**
		Initiate options when the plugin is activated
	*/
	private $data = array(
							// Styles
							'merge_styles'     => 1,
							'minify_style'     => 1,
							'style_location'   => 'default',
							'style_file_name'  	=> 'merged_style.css',
							'style_query_string' => 0,
							
							// Scripts
							'merge_scripts'    => 1,
							'minify_script'    => 1,
							'script_location'  => 'default',
							'script_file_name' => 'merged_script.js',
							'cache_age'        => 1,
							'manage_scripts'   => 0,
							'script_query_string' => 0,
							
							// CDN
							'remote_files' => 3,
							'remote_files_age' => 30, // day
							'request_timeout' => 10, // sec
							
							// Optiond
							'run_on_404' => 0
						);
	
	/**
		Additional options that do not include in the admin form. We use it on the admin bar section.
		We use wordpress settings API so when we save the options, only input elements that have name attribute
		will be pass the value, and overwrite the existing options.
	*/	
	private $add_options = array(
							'excluded_handles' => array('merged-script' => 'merged-script'),
							'rest_of_handles'  => array('script' => array(
																			'header' => array(), 
																			'footer' => array()
																		),
														'style' =>	array(
																			'header' => array(), 
																			'footer' => array()
																		)
														),
																			
							// Current handles used to build the merged file (.js or .css), useful if we use the cache option
							'curr_handles' => array('style' => array('time' => '',
																	'handles' => '')
													, 
													'script' => array('time' => '',
																	'handles' => '')
													),
													
							'remote_handles' => array('style' => array(),
														'script' => array()
													)
						);
						

    /**
     * Start up
     */
    public function __construct()
    {
		$this->options = get_option( APHMS_OPTION, array() );
		$this->admin_notices = new Aphms_Admin_Notices;

		register_activation_hook ( APHMS_PLUGIN_PATH . APHMS_DS . APHMS_PLUGIN_FILE_NAME, array($this, 'activate_plugin') );
		register_deactivation_hook ( APHMS_PLUGIN_PATH . APHMS_DS . APHMS_PLUGIN_FILE_NAME, array($this, 'deactivate_plugin') );
		
		add_action( 'wp_ajax_aphms-check-exec', array($this, 'ajax_check_exec') );
		
		add_filter( 'plugin_action_links', array($this, 'action_link'), 10, 5);
		add_action( 'admin_enqueue_scripts', array($this, 'register_scripts') );
		
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }
	
	public function deactivate_plugin()
	{
		// Clear cache
		array_map('unlink', glob(APHMS_PLUGIN_PATH . APHMS_DS . 'merged/*.*'));
	}
	
	public function activate_plugin()
	{
		$data = array_merge ($this->data, $this->add_options);
		$plugin_version = get_option( APHMS_OPTION_VERSION, array() );
	
		if ($this->options) {
			if (!$plugin_version) {
				$new_opt = array();
				if (key_exists('merge_styles', $this->options))
				{
					$this->options['minify_style'] = 1;
				} else {
					$new_opt['merge_styles'] = 0;
					$new_opt['minify_style'] = 1;
				}
				
				
				if ($this->options['merge_scripts'] == 'merge-header')
					$this->options['merge_scripts'] = 1;
				else if ($this->options['merge_scripts'] == 'merge-footer')
					$this->options['merge_scripts'] = 2;
				else if ($this->options['merge_scripts'] == 'donot-merge')
					$this->options['merge_scripts'] = 0;
				
				if (!key_exists('minify_script', $this->options))
				{
					$new_opt['minify_script'] = 0;
				}
				
				if (key_exists('force_use_newest_style', $this->options))
				{
					unset($this->options['force_use_newest_style']);
				}
				$new_opt['style_query_string'] = 0;
				
				if (key_exists('force_use_newest_script', $this->options))
				{
					unset($this->options['force_use_newest_script']);
				}
				$new_opt['script_query_string'] = 0;
				
				$new_options = array_merge($new_opt, $this->options);
				update_option(APHMS_OPTION, $new_options);
				update_option(APHMS_OPTION_VERSION, APHMS_PLUGIN_VERSION);
			}
			else 
			{
				if (version_compare('1.3', $plugin_version) > 0)
				{
					$this->options['remote_handles'] = array('style' => array(),
														'script' => array()
													);
					$this->options['remote_files'] = 3;
					$this->options['remote_files_age'] = 30;
					$this->options['request_timeout'] = 10;
							
					$this->options['run_on_404'] = 0;
					
					update_option(APHMS_OPTION, $this->options);
					update_option(APHMS_OPTION_VERSION, APHMS_PLUGIN_VERSION);
				}
			}
			
		} else {
			update_option(APHMS_OPTION, $data);
			update_option(APHMS_OPTION_VERSION, APHMS_PLUGIN_VERSION);
		}
	}
	
	/**
	* Add Settings ling to plugin list Settings | Deactivate | Edit
	*/
	public function action_link($links, $file)	
	{
		static $plugin;
		
		if (!isset($plugin))
			$plugin = APHMS_PLUGIN_DIR_NAME . '/' . APHMS_PLUGIN_FILE_NAME;
		
		// echo $plugin; die; 
		if ($plugin == $file)
		{
			$setting_link = '<a href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page='.APHMS_PLUGIN_DIR_NAME.'">Settings</a>';
			
			array_unshift($links, $setting_link);
		}
		return $links;
	}

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
		$page_title	= 'APH Merge Scripts Options';
		$menu_title	= 'APH Merge Scripts';
        add_options_page(
            $page_title, 
            $menu_title, 
            'manage_options', 
            'aph-merge-scripts', 
            array( $this, 'admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function admin_page()
    {
        ?>
		<div class="wrap">
            <h2 style="display:none">Options</h2>
			<div class="aphms-wrap">
				<h2 class="title">APH Merge Scripts Options</h2>
				<div class="aphms-form-container"> 
					<form method="post" action="options.php">
					<?php
						settings_fields ('aph_merge_group');
						do_settings_sections('style_options');
						do_settings_sections('script_options');
						do_settings_sections('remote_options');
						do_settings_sections('general_options');
					?>
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"/>
					</form>
				</div>
			</div>
		</div>
        <?php
    }
	
	public function register_scripts()
	{
		wp_enqueue_style('aphms-style', APHMS_PLUGIN_URL . '/assets/css/aphms.css?rand='.time(), '', APHMS_PLUGIN_VERSION);
		wp_enqueue_script('aphms-admin-script', APHMS_PLUGIN_URL . '/assets/js/aphms-admin.js?rand='.time(), 'jquery', APHMS_PLUGIN_VERSION);
		wp_localize_script (
			'aphms-admin-script', 
			'aphms', 
			array(
				'nonce'	=> wp_create_nonce('aphms-check-exec')
			)
		);
	}
	
	public function page_init()
    {    
		if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'aphms_clear_cache') )
			$this->delete_cache();
		
		if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'aphms_clear_local_copy') )
			$this->clear_local_copy();
		
		register_setting(
            'aph_merge_group', // Option group
            APHMS_OPTION,
			array ($this, 'submit_validation')// Option name
        );
		
		// Merge Style Options
		add_settings_section('option_page', 'Merge CSS Files', '__return_true', 'style_options');
		add_settings_field('style_merge', 'Merge & Minify CSS Files', array($this, 'style_options'), 'style_options', 'option_page');
		
		// Merge Script Options
		add_settings_section('option_page', 'Merge Javascript Files', '__return_true', 'script_options');
		add_settings_field('script_merge', 'Merge & Minify Scripts', array($this, 'script_options'), 'script_options', 'option_page');
		
		// Remote Files Options
		add_settings_section('option_page', 'Remote (CDN) File Options', '__return_true', 'remote_options');
		add_settings_field('remote_files', 'Remote Files', array($this, 'remote_files'), 'remote_options','option_page');
		add_settings_field('request_timeout', 'Request Timeout', array($this, 'request_timeout'), 'remote_options', 'option_page');
		add_settings_field('local_copy', 'Local Copy', array($this, 'local_copy'), 'remote_options', 'option_page');
		
		// General Options
		add_settings_section('option_page', 'General Options', '__return_true', 'general_options');
		add_settings_field('cache_age', 'Cache Age', array($this, 'cache_age'), 'general_options', 'option_page');
		add_settings_field('run_on_404', 'Run on 404 Page', array($this, 'run_on_404'), 'general_options', 'option_page');
		add_settings_field('manage_scripts', 'Manage Scripts', array($this, 'manage_scripts'), 'general_options', 'option_page');
		add_settings_field('clear_cache', 'Clear All Cache', array($this, 'clear_cache_options'), 'general_options', 'option_page');
		add_settings_field('excluded_handles', 'Excluded Handles', array($this, 'excluded_handles'), 'general_options', 'option_page');
	}
	
	public function style_options() 
	{		
		$checked_query_string = $this->options['style_query_string'] ? ' checked="checked"' : '';
		?>
		<p>
			<?php
			$lists = array(0 => 'Do not merge', 1 => 'Merge all CSS files');?>
			<select name="<?php echo APHMS_OPTION?>[merge_styles]" id="aphms-merge-style-option">
				<?php
				foreach ($lists as $opt_value => $opt_display)
				{
					$selected = '';
					if ($opt_value == $this->options['merge_styles'])
						$selected = ' selected="selected"';
					echo '<option value="'.$opt_value.'"'.$selected.'>'.$opt_display.'</option>';
				}
				?>
			</select>
		</p>
		<div id="aphms-minify-style-options">
			<p>
				<?php
				$lists = array(0 => 'Do not minify', 1 => 'Minify using default minifier');?>
				<select name="<?php echo APHMS_OPTION?>[minify_style]">
					<?php
					foreach ($lists as $opt_value => $opt_display)
					{
						$selected = '';
						if ($opt_value == $this->options['minify_style'])
							$selected = ' selected="selected"';
						echo '<option value="'.$opt_value.'"'.$selected.'>'.$opt_display.'</option>';
					}
					?>
				</select>
			</p>
			<p>
				<label><input type="checkbox" name="<?php echo APHMS_OPTION?>[style_query_string]" value="1" <?php echo $checked_query_string?>>Force not to use browser's cache</label>
				<p class="aphms-info">
					This option will add a random value of query string (eq. merged_style.css?rand=43562). This is useful when we need the fresh content of the css file that maybe ever cached by the browser.
				</p>
			</p>
		</div>
	<?php }
	
	public function script_options() {
			
		$lists = array(0 => 'Do not merge', 
						1 => 'Merge and place it on the header',
						2 => 'Merge and place it on the footer'
				);?>
		<select name="<?php echo APHMS_OPTION?>[merge_scripts]" id="aphms-merge-script-option">
			<?php
			foreach ($lists as $opt_value => $opt_display)
			{
				$selected = '';
				if ($opt_value == $this->options['merge_scripts'])
					$selected = ' selected="selected"';
				
				echo '<option value="'.$opt_value.'"'.$selected.'>'.$opt_display.'</option>';
			}
			?>
		</select>
		<div id="aphms-minify-script-options">
			<p>
				<?php
					$lists = array(0 => 'Do not minify', 
							1 => 'Minify using default minifier',
							2 => 'Minify using default minifier + YUI Compressor'
						);
					
					$disabled = $warn = '';
					if (!function_exists('exec')) {
						$warn = "PHP exec() function is disabled, so, we don't able to use YUI Compressor";
						$disabled = ' disabled="disabled"';
					} else {
						if(!shell_exec('java -version 2>&1')) {
							$warn = "Your server doesn't have java installed, so, we don't able to use YUI Compressor";
						}
					}
				?>
				<select name="<?php echo APHMS_OPTION?>[minify_script]" id="aphms-option-minify">
					<?php
					foreach ($lists as $opt_value => $opt_display)
					{
						$selected = '';
						if ($opt_value == $this->options['minify_script'])
							$selected = ' selected="selected"';
						
						$option = '<option value="'.$opt_value.'"'.$selected.'>'.$opt_display.'</option>';
						if ($opt_value == 2 && $disabled) {
							$selected = '';
							$option = '<option value="' . $opt_value . '"' . $selected . $disabled  . '>' . $opt_display .'</option>';
						} 
						echo $option;
					}
					?>
				</select>
				<?php if($warn) {
					echo '<p class="aphms-warning">' . $warn . '</p>';
				}
				?>
			</p>
			<p class="aphms-info">
				We don't use Closure Compiler as it's not 100% safe, tested on jQuery, <a href="http://webdevcorner.com/compress-javascript-file-using-closure-compiler/#compress-file" target="_blank">read here</a>.
			</p>
		
			<p>
				<?php $checked = $this->options['script_query_string'] ? ' checked="checked"' : ''; ?>
				<label><input type="checkbox" name="<?php echo APHMS_OPTION?>[script_query_string]" value="1" <?php echo $checked?>>Force not to use browser's cache</label>
				<p class="aphms-info">
					This option will add a random value of query string (eq. merged_script.css?rand=43562). This is useful when we need the fresh content of the script that maybe ever cached by the browser.
				</p>
			</p>
		</div>
	<?php }
	
	public function remote_files() {
			
		$lists = array(	0 => 'Don not process remote file',
						1 => 'Always use remote file - fallback to local copy', 
						2 => 'Always use local copy - fallback to remote file',
						3 => 'Use local copy if the file age less than'
				);?>
		<div class="aphms-input-container aphms-clearfix">
			<select name="<?php echo APHMS_OPTION?>[remote_files]" id="aphms-use-remote-files">
				<?php
				foreach ($lists as $opt_value => $opt_display)
				{
					$selected = '';
					if ($opt_value == $this->options['remote_files'])
						$selected = ' selected="selected"';
					
					echo '<option value="'.$opt_value.'"'.$selected.'>'.$opt_display.'</option>';
				}
				?>
			</select>
			<?php
				$hidden = $this->options['remote_files'] == 3 ? '' : ' style="display:none"';
			?>
			<div id="aphms-local-copy-day"<?php echo $hidden?>>
				<input type="text" name="<?php echo APHMS_OPTION?>[remote_files_age]" class="aphms-small-input" value="<?php echo $this->options['remote_files_age'] ?>"/><span class="description">day(s) - fallback to remote file</span>
			</div>
		</div>
		<p class="description">Getting remote files will take a moment at the first run</p>
		
	<?php }
	
	public function request_timeout() 
	{
		if (function_exists('curl_version'))
		{?>
			<input type="text" name="<?php echo APHMS_OPTION?>[request_timeout]" class="small-text" value="<?php echo $this->options['request_timeout'] ?>"/><br/>
		<?php 
		} else {
			echo 'This feature is disabled because CURL module not active';
		}
		echo '<p class="description">How many second(s) request to remote files will timeout</p>';
	}
	
	public function local_copy()
	{
		$lists = array('style', 'script');
		foreach ($lists as $type)
		{
			${'remote_' . $type} = false;
			if ($this->options['remote_handles'][$type])
			{
				$html .=
					'<strong>Remote ' . ucfirst($type) . 's :</strong>
					<p>
						<ol>';
					
						foreach ($this->options['remote_handles'][$type] as $handle => $val)
						{
							$url = $val['orig_url'];
							$ext = $type == 'style' ? '.css' : '.js';
							$local_url = APHMS_PLUGIN_URL . '/remote/' . $handle . $ext;
							$html .= '<li> <label>' . $handle . '</label><br/>
							Remote Url: <a href="' . $val['orig_url'] . '" target="_blank" title="' . $handle . '">' . $val['orig_url'] . '</a><br/>
							Local Url: <a href="' . $local_url . '" target="_blank" title="' . $handle . '">' . $local_url . '</a>
							
							</li>';
						}
				
				$html .= '</ol>
					</p>';
				${'remote_' . $type} = true;
			}
		}
		
		if (!$remote_style && !$remote_script)
		{
			echo 'Local copy file not found';
			return;
		}
		
		$parse_url = parse_url($_SERVER['REQUEST_URI']);
		echo '<a href="' . wp_nonce_url('?'.$parse_url['query'], 'aphms_clear_local_copy'). '" class="button">Clear All Local Copy Files</a> <a href="#" class="button" id="aphms-show-local-copy">Show Files</a>';
		
		echo '
		<div class="aphms-listcache" id="aphms-list-local-copy" style="display:none">' . $html . '</div>';
	}
	
	public function clear_local_copy()
	{
		$deleted = array_map('unlink', glob(APHMS_PLUGIN_PATH . APHMS_DS . 'remote/*.*'));
		
		$this->options['remote_handles']['style'] = array();
		$this->options['remote_handles']['script'] = array();
		
		update_option(APHMS_OPTION, $this->options);
		
		// Save notice
		if ($deleted[0])
			$msg = 'Local copy file(s) have successfully been deleted.';
		else
			$msg = 'Local copy file(s) could not be deleted.';
		
		$this->admin_notices->add_notice($msg, 'success', true, true);
		
		wp_redirect( remove_query_arg('_wpnonce') );
		exit;
	}

	public function submit_validation($inputs)
	{
		foreach ($this->add_options as $key => $val)
		{
			if (!key_exists($key, $inputs))
			{
				if (key_exists($key, $this->options))
					$inputs[$key] = $this->options[$key];
				else
					$inputs[$key] = $val;
			}
		}
		// echo '<pre>'; print_r($inputs); die;
		return $inputs;
	}
	
	public function cache_age()
	{ ?>
	
		<input type="text" class="small-text code" name="<?php echo APHMS_OPTION?>[cache_age]" value="<?php echo $this->options['cache_age']?>"/> day(s)
		<p class="description">Fill 0 to force the plugin to generate new merged file</p>
		<p class="aphms-info">
			While using the cache, the plugin will always check what style and script files are merged into the cache file, if those files are meet all of the files needed by the current page, then cached version is used, otherwise, the plugin will generate new merged file.
		</p>
		
	<?php }
	
	public function run_on_404()
	{
		$lists = array('No', 'Yes');
		echo '<select name="' . APHMS_OPTION . '[run_on_404]">';
		foreach ($lists as $key => $list) {
			$selected = $key == $this->options['run_on_404'] ? ' selected="selected"' : '';
			echo '<option value="' . $key . '"' . $selected . '>' . $list . '</option>';
		}
		?>
		</select><br/>
		<p class="aphms-warning">
			<em>If chosing yes, then MAKE SURE that there is NO broken link of assets (css, js, fonts)</em>. In broken links, WordPress will redirect to 404 page in the background, so this plugin will also run in the background and break the merged CSS and JS.
		</p>
	<?php	
	}
	public function manage_scripts()
	{ 
		$checked = $this->options['manage_scripts'] ? ' checked="checked"' : '';
		?>
		<label><input type="checkbox" name="<?php echo APHMS_OPTION?>[manage_scripts]" value="1" <?php echo $checked?>>Show Manage Scripts Menu on the Admin Bar</label>
		<div class="aphms-info">
			<p>
				While opening a page, the plugin will automatically detect and merge javascript files that are loaded, but on certain conditions, not all of those scripts can be detected (typically scripts loaded very late in the wp_footer such as: akismet-form).
			</p>
			<p>
				You can find these files through the "APH Merge Scripts" menu located on the admin bar on each page, this menu appears when you open a page, from that menu, you can add those kind of files to manually combine with others.
			</p>
			
		</div>
		<p class="aphms-warning">
				When this option is activated and the admin bar is displayed, the plugin will stop merging scripts
			</p>
	<?php }
	
	public function excluded_handles() {
		if (count($this->options['excluded_handles']) == 1  && in_array('merged-script', $this->options['excluded_handles']))
		{
			echo 'Excluded handle not found';
		} else {
			echo '<p>The following script handle(s) are excluded from merging: <ol>';
			foreach ($this->options['excluded_handles'] as $handle)
			{
				if ($handle == 'merged-script')
					continue;
				
				echo '<li>' . $handle . '</li>';
			}
			echo '</ol>
			You can include those handle(s) to be merged with others through Admin Bar on the page <em>where the script is loaded</em>. Don\'t forget to activate the option: "Show Manage Scripts Menu on the Admin Bar"</p>';
		}
	}
	public function clear_cache_options()
	{
		$file_style = APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . 'merged-style-' . $this->options['curr_handles']['style']['time'] . '.css';
		$file_script = APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . 'merged-script-' . $this->options['curr_handles']['script']['time'] . '.js';
		
		$file_style_exists = file_exists($file_style);
		$file_script_exists = file_exists($file_script);

		if ($file_style_exists || $file_script_exists)
		{
			$site_url = site_url();
			$parse_url = parse_url($_SERVER['REQUEST_URI']);
			echo '<a href="' . wp_nonce_url('?'.$parse_url['query'], 'aphms_clear_cache'). '" class="button">Clear All Cache</a>
			<div class="aphms-listcache">';
			
			$lists = array('style', 'script');
			foreach ($lists as $type)
			{
				if (${'file_' . $type . '_exists'}) 
				{	
					$file_merged_url = APHMS_PLUGIN_URL . '/merged/merged-' . $type . '-' . $this->options['curr_handles'][$type]['time'] . '.js';
					$file_merged_handles = APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . $type . '-handles.txt';				
					$show_handle = file_exists($file_merged_handles) ? '<a class="aphms-show-handles" href="#" id="aphms-show-mergedstyle-handles">Show Handles</a>' : '-'; 
					
					echo '
						<p>
						<strong>' . ucfirst($type) . ' Cache:</strong><hr/>
						<p>File: <a href="'.$file_merged_url.'" title="Open File" target="_blank">' .  $file_merged_url . '</a> ('.round(filesize(${'file_' . $type})/1024) . ' KB )<br/>
						Handles: ' . $show_handle . '</p>';
					
					if (file_exists($file_merged_handles))
					{
						$exp = explode (PHP_EOL, file_get_contents($file_merged_handles));
						echo '<ol style="display:none">';
						foreach ($exp as $line)
						{
							if (trim($line) == '')
								continue;
							
							$split = explode(':', $line);
							$handle = $split[0];
							unset($split[0]);
							$url = trim(join($split, ':'));
							$url = ltrim($url, '/');
							
							$site_url = rtrim(site_url(),'/');
							$domain = preg_replace('/https?:\/\//i', '', $site_url);
							
							
							/* 
								SITE URL with or without http;
							*/
							
							if (strpos($url, $domain) !== false)
							{
								//rel path with site url without http
								if (strpos($url, 'http') === false) {
									$domain_regex = str_replace('/', '\/', $domain); // FOR regex error
									$url = $site_url . preg_replace('/.*' . $domain_regex . '/i', '', $url);
								}
							} 
							// Rel path or remote
							else {
								//rel path
								if (strpos($url, 'http') === false) {
									$url = $site_url . '/' . $url;
								}
								
							}
							
							echo '<li>' . $handle . ': <a href="' . $url . '"  title="Open File: ' . $handle . '" target="_blank"> ' . $url . '</a></li>';
						}
						echo '</ol>';
					}
					echo '</p>';
				}
			}
			
			echo '</div>';
		} else {
			echo 'Cache file not found';
		}
	}
	
	public function delete_cache()
	{
		$deleted = array_map('unlink', glob(APHMS_PLUGIN_PATH . APHMS_DS . 'merged/*.*'));
		
		// Save notice
		if ($deleted[0])
			$msg = 'Cache file(s) have successfully been deleted.';
		else
			$msg = 'Cache file(s) could not be deleted.';
		
		$this->admin_notices->add_notice($msg, 'success', true, true);
		
		wp_redirect( remove_query_arg('_wpnonce') );
		exit;
	}
}
?>