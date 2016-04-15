<?php
/* --------------------------------------------------------------
   GMJanolaw.php 2015-05-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMJanolaw_ORIGIN
{
	public $m_user_id = false;
	public $m_shop_id = false;
	public $m_cache_seconds = 7200; # 2hours
	
	protected $content_types_array = array();
	protected $mode_suffix_array = array();
	protected $formats_array = array();
			
	function __construct() 
	{
		$this->m_user_id = xtc_cleanName(MODULE_GAMBIO_JANOLAW_USER_ID);
		$this->m_shop_id = xtc_cleanName(MODULE_GAMBIO_JANOLAW_SHOP_ID);

		$this->content_types_array[] = 'agb';
		$this->content_types_array[] = 'widerrufsbelehrung';
		$this->content_types_array[] = 'datenschutzerklaerung';
		$this->content_types_array[] = 'impressum';
		
		$this->mode_suffix_array[] = '';
		$this->mode_suffix_array[] = '_include';
		
		$this->formats_array[] = 'html';
		$this->formats_array[] = 'txt';

		if($this->get_status() == true)
		{
			# phantom call for creating checkout cache-file
			$this->get_page_content('widerrufsbelehrung', false, true, 'checkout-widerrufsbelehrung');
			$this->get_page_content('agb', false, true, 'checkout-agb');
		}
	}
	
	function get_status()
	{
    	if(defined('MODULE_GAMBIO_JANOLAW_STATUS') == false || MODULE_GAMBIO_JANOLAW_STATUS == 'False')
    	{
    		# module not found or not activated.
    		return false;
    	}
    	# module installed and active
		return true;
	}
	
    function get_page_content($p_page_name, $p_include_mode=true, $p_html_format=true, $p_cache_filename='')
    {
    	if($this->get_status() == false) {
    		return 'Das Janolaw-Modul ist nicht aktiviert.';
    	}
    	
		$c_page_name = xtc_cleanName($p_page_name);
		
		if($p_include_mode) {
			$t_include_mode_suffix = '_include';
		} else {
			$t_include_mode_suffix = '';
		}

		if($p_html_format) {
			$t_format_suffix = 'html';
		} else {
			$t_format_suffix = 'txt';
		}
		
		
		if($p_cache_filename != '')
		{
			$t_cache_file = DIR_FS_CATALOG . 'cache/'. xtc_cleanName($p_cache_filename) .'.'. $t_format_suffix;
		}
		else {
			# build page-specific source path for cache file
			$t_cache_file = DIR_FS_CATALOG . 'cache/'. 
								$this->m_user_id .'-'.
								$this->m_shop_id .'-'.
								$c_page_name.
								$t_include_mode_suffix.'.'.$t_format_suffix;
		}
		
		$t_create_cache = false;		
		
		if(file_exists($t_cache_file) == false) {
			$t_create_cache = true;
		}
		elseif(filesize($t_cache_file) < 100) {
			$t_create_cache = true;
		}
		elseif(filemtime($t_cache_file) < time() - $this->m_cache_seconds) {
			$t_create_cache = true;
		}
		
		# load page and create cache
		if($t_create_cache)
		{
			$this->update_cache_file($t_cache_file, $c_page_name, $t_include_mode_suffix, $t_format_suffix);
		}
		
		# use cache file for output
		$t_content = file_get_contents($t_cache_file);
		
		if($p_html_format)
		{
			# append needed css styles
			$t_content = '<style type="text/css">#janolaw-paragraph, #janolaw-footer { margin-top: 20px; } #janolaw-body ol { margin: 8px 4px 4px 8px; } #janolaw-body li { margin: 8px 4px 4px 24px; }</style>' . $t_content;
		}	
			
		# display page content
		return $t_content;
    }
	
	# Janolaw server down -> update cache file dates to stop updating for next 2 hours
	function touch_cache_files()
	{
		foreach($this->content_types_array AS $t_content_type)
		{
			foreach($this->mode_suffix_array AS $t_mode_suffix)
			{
				foreach($this->formats_array AS $t_format)
				{
					$t_cache_file = DIR_FS_CATALOG . 'cache/'. 
									$this->m_user_id .'-'.
									$this->m_shop_id .'-'.
									$t_content_type.
									$t_mode_suffix.'.'.$t_format;
					
					if(file_exists($t_cache_file))
					{
						touch($t_cache_file);
					}
				}				
			}
		}
	}
	
	public function update_cache_file($p_cache_file, $p_page_name, $p_include_mode_suffix, $p_format_suffix)
	{
		$c_page_name = xtc_cleanName($p_page_name);
		
		if(strpos($p_cache_file, DIR_FS_CATALOG . 'cache/') !== 0 
			|| strpos($p_cache_file, '..') !== false
			|| in_array($p_include_mode_suffix, $this->mode_suffix_array) === false
			|| in_array($p_format_suffix, $this->formats_array) === false)
		{
			return false;
		}
				
		# build source url for getting page content
		$t_source_url = 'http://www.janolaw.de/agb-service/shops/'.
							$this->m_user_id .'/'.
							$this->m_shop_id .'/'.
							$c_page_name.
							$p_include_mode_suffix.'.'.$p_format_suffix;

		$t_content = '';					

		# load page from janolaw site
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $t_source_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			$info = curl_getinfo($ch);
			$http_status = $info['http_status'];
			$t_content = curl_exec($ch);
			curl_close($ch);
		}
		elseif(function_exists('file_get_contents'))
		{
			$headers = get_headers($t_source_url);
			$http_status = substr($headers[0], 9, 3);
			$t_content = @file_get_contents($t_source_url);
		}

		# looking for success
		if($t_content != false || (strlen($t_content) > 100 && $http_status != 200))
		{
			# write page content to cache file on success
			$fp = fopen($p_cache_file, 'w+');
			$t_content = utf8_encode_wrapper($t_content);
			fwrite($fp, $t_content);
			fclose($fp);
		}
		else
		{
			$this->touch_cache_files();
		}
		
		return true;
	}
}
MainFactory::load_origin_class('GMJanolaw');