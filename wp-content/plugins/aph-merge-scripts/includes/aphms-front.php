<?php
class Aphms_Front {
	
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
		Is CURL Module is activated. Used when request remote file(CDN)
	*/
	private $iscurl;
	
	/**
		use cache?
	*/
	private $use_cache;
	
	/*
	* Save PATH of handles as we will check the local copy first
	*/
	
	private $curr_handles;
	
	/**
		Merged script initial name. Usage merged-script-435325443.js
	*/
	private $merged_script_name = 'merged-script';
	
	/**
		Merged style initial name. Usage merged-style-435325443.css
	*/
	private $merged_style_name = 'merged-style';
	
	/**
		Wordpress to_do scripts, used both by mergescripts and admin_bar
	*/
	private $to_do = array();
	
	/** 
		Used for save the handles want to deregister both by mmerge_scripts and check_cache method
	*/
	private $deregister = array('style' => array(), 'script' => array());
		
	/**
		Check whether the method is already executed. This is to prevent the method from executed by other plugins
	*/
	private $is_executed = array( 'merge_scripts' => false );
								

	public function __construct ()
	{
		$this->options = get_option(APHMS_OPTION);
		$this->iscurl = function_exists('curl_version');
	}
	
	public function init()
	{
		add_action('init', array($this, 'run_plugin'));
		add_filter('script_loader_src', array($this, 'remove_query_string'));
		add_filter('style_loader_src', array($this, 'remove_query_string'));
	}
	
	public function remove_query_string( $src )
	{
		if (strpos($src, $this->merged_style_name)
			|| strpos($src, $this->merged_script_name)) {
			$src = remove_query_arg('ver', $src);
		}	
		return $src;
	}
	
	public function run_plugin()
	{
		$admin_bar = is_admin_bar_showing();
		if (is_admin()
			|| (is_user_logged_in() && !$admin_bar)
			|| ($admin_bar && $this->options['manage_scripts']))
		{
			return;
		}

		if (isset($this->options['merge_scripts'])) {
			
			add_action( 'wp_enqueue_scripts', array( $this, 'merge_scripts' ), 999 );
			add_action( 'wp_head', array( $this, 'deregister_script' ), 7 );
			add_action( 'wp_footer', array( $this, 'deregister_script' ) );
		}
		
		if (isset($this->options['merge_styles'])) {
			add_action( 'wp_print_styles', array( $this, 'merge_styles' ), 10 );
		}
	}
	
	private function stop_plugin()
	{
		/**
			we could not test 404 page on wp_init, so we check it in other hooks
		*/
		if (is_404() && !$this->options['run_on_404']) {
			return true;
		}
		
		return false;
	}
	
	private function pre_process()
	{
		global $wp_styles;

		$queues = $wp_styles->queue;
		$wp_styles->all_deps($queues);	
		$this->to_do['style'] = $wp_styles->to_do;
		
		global $wp_scripts;

		$wp_scripts->all_deps($wp_scripts->queue);	
		$this->to_do['script'] = $wp_scripts->to_do;
		
		$this->use_cache['style'] = $this->check_cache('style');
		$this->use_cache['script']	= $this->check_cache('script');

		if ($this->options['remote_files'] == 0)
		{
			return;
		}
		
		// REMOTE FILES
		$rest_handles = array();
		foreach( $this->options['rest_of_handles']['script'] as $pos => $handles) 
		{
			foreach ($handles as $handle => $arr)
			{
				$rest_handles[] = $handle;
			}
		}
		
		$remote = array();
		$parse_site_url = parse_url(site_url());
		foreach ($this->use_cache as $type => $use_cache)
		{
			if ($use_cache)
				continue;
			
			$to_do = $type == 'script' ? array_merge($this->to_do[$type], $rest_handles) : $this->to_do[$type];
			foreach( $to_do as $handle) 
			{
				$src = $type == 'script' ? $wp_scripts->registered[$handle]->src : $wp_styles->registered[$handle]->src;
				$parse_url = parse_url($src);
				
				// if src has http(s) 
				if (key_exists('host', $parse_url))
				{
					// External script (CDN) - if src has different host
					if ($this->options['remote_files'] != 0 && $parse_site_url['host'] != $parse_url['host'])
					{
						// IF not merge
						if ( ($type == 'style' && $this->options['merge_styles'])
							|| ($type == 'script' && $this->options['merge_scripts']) )
						
						{	
							$remote[$type][$handle] = $src;
							$this->options['remote_handles'][$type][$handle]['orig_url'] = $src;
						}
					}
				}
			}
		}

		if (!$remote)
			return;
		
		/* We should download all remote files, but first, we'll check whether we really need to
		* Always use local copy - falback to remote
		*/
		if ($this->options['remote_files'] == 2 || $this->options['remote_files'] == 3)
		{
			foreach ($remote as $type => $handles)
			{
				foreach ($handles as $handle => $src)
				{
					$ext = $type == 'style' ? '.css' : '.js';
					if ( file_exists( APHMS_PLUGIN_PATH . APHMS_DS . 'remote' . APHMS_DS . $handle . $ext ) )
					{
						unset($remote[$type][$handle]);
						
					}
				}
			}
		}
		
		if (!$remote['style'] && !$remote['script'])
			return;
		
		$this->download_remote_files($remote);
	}
	
	private function download_remote_files($remote)
	{
		if ($this->iscurl)
		{
			$ch      = array();
			$results = array();
			$mh      = curl_multi_init();
			
			foreach ($remote as $type => $handles)
			{
				foreach ($handles as $handle =>  $url)
				{
					$ch[$type][$handle] = curl_init();
					curl_setopt($ch[$type][$handle], CURLOPT_URL, $url);
					curl_setopt($ch[$type][$handle], CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch[$type][$handle], CURLOPT_ENCODING, '');
					curl_setopt($ch[$type][$handle], CURLOPT_CONNECTTIMEOUT, $this->options['request_timeout']);
					curl_setopt($ch[$type][$handle], CURLOPT_TIMEOUT, $this->options['request_timeout'] + 5);
					curl_setopt($ch[$type][$handle], CURLOPT_SSL_VERIFYPEER, false);
					curl_multi_add_handle($mh, $ch[$type][$handle]);
				}
			}

			$running = null;
			do {
				curl_multi_exec($mh, $running);
			}
			while ($running > 0);

			foreach ($ch as $type => $handles)
			{
				foreach ($handles as $handle =>  $resource)
				{
					$content = curl_multi_getcontent($resource);
					if ($content)
					{
						$ext = $type == 'style' ? '.css' : '.js';
						$path = APHMS_PLUGIN_PATH . APHMS_DS . 'remote' . APHMS_DS . $handle . $ext;
						file_put_contents($path, $content);
					}
					curl_multi_remove_handle($mh, $resource);
				}
			}
			curl_multi_close($mh);
		} 
		else 
		{
			$content = '';
			foreach ($remote as $type => $handles)
			{
				foreach ($handles as $handle =>  $url)
				{
					$content = file_get_contents($url);
					if ($content)
					{
						$file_name = APHMS_PLUGIN_PATH . APHMS_DS . 'remote' . APHMS_DS . $handle . '.css';
						file_put_contents($file_name, $content);
						
						$ext = $type == 'style' ? '.css' : '.js';
						$path = APHMS_PLUGIN_URL . '/remote/' . $handle . $ext;
					}
				}
			}			
		}
	}
	
	public function merge_styles()
	{
		if ($this->stop_plugin())
			return false;
		
		if (!$this->options['merge_styles'])
			return false;
					
		$token =  $this->options['curr_handles']['style']['time'];

		if (!$this->use_cache['style'])
		{
			global $wp_styles;

			$token = time();
			$merged_style 	= '';
			$list_handles 	= array();
			
			require_once 'minifier/minify/src/Minify.php';
			require_once 'minifier/minify/src/CSS.php';
			require_once 'minifier/minify/src/Exception.php';
			require_once 'minifier/minify/Converter.php';
			
			foreach( $this->to_do['style'] as $handle) 
			{
				if (!key_exists($handle, $wp_styles->registered))
					continue;
				
				$file_path = $this->file_path($handle, $wp_styles->registered[$handle]->src, 'style');
				
				/**	We have to save the handle outside the file_exists to make sure all handle are saved,
					this is useful when cheching cache
				*/
				$list_handles[] = $handle;
				$log_handles[$handle] = $wp_styles->registered[$handle]->src;
				
				if ($file_path)
				{
					if (file_exists($file_path))
					{
						$this->deregister['style'][] = $handle;
						$minifier = new MatthiasMullie\Minify\CSS($file_path);
						$path = APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . $this->merged_style_name . '-' . $token . '.css';
						
						// if inside remote directory 
						if (strpos($file_path, 'remote') !== false) {
							$minifier->setFromPath( ABSPATH . 'style.css' );
						}
		
						$minifier->setToPath( $path );
						$merged_style .=  $minifier->minify();
					} else {
						$this->deregister['style'][] = $handle;
					}
				}
			}
			
			if ($merged_style) 
			{	
				array_map('unlink', glob(APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . '*.css'));
				file_put_contents($path, '/* APH Merge Scripts v'.APHMS_PLUGIN_VERSION.' */' . "\r\n" . $merged_style);
				
				$log_style_handles = '';
				foreach ($log_handles as $handle => $url) {
					$log_style_handles .= $handle . ': ' . $url . "\r\n";
				}
				$path_list_handle = APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . 'style-handles.txt';
				file_put_contents($path_list_handle, $log_style_handles);
				
				// update current handles
				$this->options['curr_handles']['style']['time'] = $token;
				$this->options['curr_handles']['style']['handles'] = $list_handles;
				update_option(APHMS_OPTION, $this->options);
			}
		}

		foreach ($this->deregister['style'] as $handle)
		{
			wp_deregister_style($handle);
		}
		
		$qstring = $this->options['style_query_string'] ? '?rand='.time() : '';
		$file_css_url = APHMS_PLUGIN_URL . '/merged/' . $this->merged_style_name . '-' . $token . '.css' . $qstring;
		wp_enqueue_style($this->merged_style_name,  $file_css_url, '', APHMS_PLUGIN_VERSION);
	}
	
	public function merge_scripts($position)
	{
		if ($this->is_executed['merge_scripts'])
			return;
				
		$this->pre_process();
		$this->merge_styles();
		
		if (!$this->options['merge_scripts'])
			return false;

		$token =  $this->options['curr_handles']['script']['time'];
		
		if (!$this->use_cache['script'])
		{
			global $wp_scripts;
			$token = time();
			
			$merged_script	= '';
			$list_handles 	= array();

			// Loop javascript files and save to $merged_script variable
			foreach( $this->to_do['script'] as $handle) 
			{
				if ($this->skip_handle($handle) || !key_exists($handle, $wp_scripts->registered))
					continue;
				
				$obj = $wp_scripts->registered[$handle];
						
				// wp_localize_script
				$localize = '';
				if (@key_exists('data', $obj->extra))
					$localize = $obj->extra['data'] . ';';
				
				$file_path = $this->file_path($handle, $obj->src, 'script');
				
				/**	We have to save the handle outside the file_exists to make sure all handle are saved,
					this is useful for cheching cache
				*/
				$list_handles[] = $handle;
				if ($file_path) {
					if (file_exists($file_path))
					{	
						$log_handles[$handle] = $wp_scripts->registered[$handle]->src;
						$merged_script .= '/* Handle: ' . $handle . ' */' . "\r\n\r\n" . $localize . file_get_contents($file_path) . ';' . "\r\n\r\n";
						$this->deregister['script'][] = $handle;
					} else {
						$this->deregister['script'][] = $handle;
					}
				}
			}
		
			// Merge rest of scripts
			foreach( $this->options['rest_of_handles']['script'] as $pos => $handles) 
			{
				foreach ($handles as $handle => $arr)
				{
					if (in_array($handle, $this->to_do) || $this->skip_handle($handle))
						continue;
					
					// wp_localize_script
					$localize = '';
					if (@key_exists('data', $arr['extra']))
						$localize = $arr['extra']['data'] . ';';
					
					$file_path = $this->file_path($handle, $arr['src'], 'script');
					if ($file_path && file_exists($file_path))
					{
						$list_handles[] = $handle;
						$log_handles[$handle] = $arr['src'];
						$merged_script .= '/* Handle: ' . $handle . ' */' . "\r\n\r\n" . $localize . file_get_contents($file_path) . ';' . "\r\n\r\n";
						$this->deregister['script'][] = $handle;
					}
				}
			}
			
			// Save $merged_script into file
			if ($merged_script) {
				$this->write_js_file($merged_script, $token);
				
				$log_script_handles = '';
				foreach ($log_handles as $handle => $url) {
					$log_script_handles .= $handle . ': ' . $url . "\r\n";
				}
				$path = APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . 'script-handles.txt';
				file_put_contents($path, $log_script_handles);
				
				// update current handles
				$this->options['curr_handles']['script']['time'] = $token;
				$this->options['curr_handles']['script']['handles'] = $list_handles;
				update_option(APHMS_OPTION, $this->options);
			}
		}
		
		foreach ($this->deregister['script'] as $handle)
		{
			wp_deregister_script($handle);
		}
		
		$qstring = $this->options['style_query_string'] ? '?rand='.time() : '';
		$location = $this->options['merge_scripts'] == 2 ? true : false;
		$file_js_url = APHMS_PLUGIN_URL . '/merged/' . $this->merged_script_name . '-' . $token . '.js' . $qstring;
		wp_enqueue_script($this->merged_script_name,  $file_js_url, '', APHMS_PLUGIN_VERSION, $location);
		
		$this->is_executed['merge_scripts'] = true;
	}
	
	private function write_js_file($merged_script, $token)
	{
		if ($this->options['minify_script']) {
			
			/* require_once 'minifier/minify/src/Minify.php';
			require_once 'minifier/minify/src/JS.php';
			require_once 'minifier/minify/src/Exception.php';
					
			$minifier = new MatthiasMullie\Minify\JS($merged_script);
			$merged_script = $minifier->minify(); */
			
			require_once 'minifier/JShrink/Minifier.php';
			$merged_script = \JShrink\Minifier::minify($merged_script, array('flaggedComments' => false));
		}
		
		array_map('unlink', glob(APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . '*.js'));
		$path = APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . $this->merged_script_name . '-' . $token . '.js';
		$merged_script = '/*! APH Merge Scripts v'.APHMS_PLUGIN_VERSION.' */' . "\r\n" . $merged_script;
		
		file_put_contents($path, $merged_script);
		
		if ($this->options['minify_script'] == 2)
		{
			if (function_exists('exec'))
			{
				$plugin_dir = 'wp-content' . APHMS_DS . 'plugins' . APHMS_DS . APHMS_PLUGIN_DIR_NAME;
				$yui_jar = $plugin_dir . APHMS_DS . 'includes' . APHMS_DS . 'minifier' . APHMS_DS . 'yuicompressor' . APHMS_DS . 'yuicompressor.jar';
				
				$file_rel_path = 'wp-content' . APHMS_DS . 'plugins'  . APHMS_DS . APHMS_PLUGIN_DIR_NAME . APHMS_DS . 'merged' . APHMS_DS . $this->merged_script_name . '-' . $token . '.js';
				
				$cmd = 'java -jar ' . $yui_jar . ' ' . $file_rel_path . ' -o ' . $file_rel_path;
						
				@exec($cmd . ' 2>&1');
			}
		}
	}
	
	/**
		Check cache make sure that all handles are processed
	*/
	private function check_cache($type)
	{
		if (!$this->options['curr_handles'][$type]['time'] 
			|| !$this->options['curr_handles'][$type]['handles']
		)
			return 0;
		
		// Check whether the merged file exists
		$ext = $type == 'style' ? '.css' : '.js';
		$file = APHMS_PLUGIN_PATH . APHMS_DS . 'merged' . APHMS_DS . 'merged-' . $type . '-' . $this->options['curr_handles'][$type]['time'] . $ext;
		if (!file_exists($file))
			return 0;
		
		// Check whether cache is expired
		$date = $this->options['curr_handles'][$type]['time'];
		$expired = strtotime('+'.$this->options['cache_age'].' day', $date);
					
		if ($expired < time())
			return 0;
		
		
		$list_of_handles = array_merge($this->to_do, 
											array_keys($this->options['rest_of_handles'][$type]['header']),
											array_keys($this->options['rest_of_handles'][$type]['footer'])
										);
		
		foreach($list_of_handles[$type] as $handle)
		{
			/**
				As we save the handle of merged files into curr_handles, then we need to make sure 
				that all of list handles (curr handles (to_do) + handles saved by user in the admin bar)
				are exists in the curr_handles, if not, don't use the cache
			*/
			if ($this->skip_handle($handle))
				continue;
			
			if (!in_array($handle, $this->options['curr_handles'][$type]['handles'])) {
				return 0;
			}
			$deregister[] = $handle;
		}
		
		// USE CACHE
		foreach($list_of_handles as $handle) {
			$this->deregister[$type] = $deregister;
		}
		return 1;
	}

	/**
		File path
		Find relative path of each style or script
	*/
	private function file_path($handle, $src, $type)
	{
		$clean_hash = $clean = strtok($src,'?');
		
		$site_url =  site_url();
		$parse_site_url = parse_url($site_url);
		$parse_url = parse_url($clean_hash);
		
		$site_path = '';
		if (key_exists('path', $parse_site_url))
		{
			$site_path = $parse_site_url['path'];
		}
		
		if (key_exists('host', $parse_url))
		{
			// External script (CDN)
			if ($this->options['remote_files'] != 0 && $parse_site_url['host'] != $parse_url['host'])
			{
				$site_path = site_url();
				$ext = $type == 'style' ? '.css' : '.js';
				$parse_url['path'] = APHMS_PLUGIN_URL . '/remote/' . $handle . $ext;
			}
			
			$file_path = str_replace($site_path, '', $parse_url['path']);
			$file_path = ltrim ($file_path, '/');
		} 
		else 
		{
			$file_path = ltrim($parse_url['path'], '/');
		}
		return $file_path;
	}
	
	private function skip_handle($handle)
	{
		$excluded_script = $this->options['excluded_handles'];

		if (in_array($handle, $excluded_script))
			return true;
		else 
			return false;
	}
	
	/**
		Used in the init method to deregister th scripts earlier
	*/
	public function deregister_script()
	{
		if ($this->options['merge_scripts'] == 0)
			return;
		
		foreach( $this->options['rest_of_handles']['script'] as $pos => $handles) 
		{
			foreach ($handles as $handle => $arr)
			{
				if (!in_array($handle, $this->options['excluded_handles'])) {
					wp_deregister_script($handle);
				}
			}
		}
	}
}