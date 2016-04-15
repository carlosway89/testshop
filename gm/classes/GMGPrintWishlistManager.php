<?php
/* --------------------------------------------------------------
   GMGPrintWishlistManager.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

class GMGPrintWishlistManager_ORIGIN
{
	var $v_elements = array();
	var $v_files = array();
	
	function __construct()
	{
		$this->restore();
	}
	
	function add($p_products_id, $p_elements_id, $p_value)
	{
		$this->v_elements[$p_products_id][$p_elements_id] = $this->correct_length($p_elements_id, $p_value);
	}
	
	function correct_length($p_elements_id, $p_value)
	{
		$c_elements_id = (int)$p_elements_id;
		$t_value = $p_value;
		
		$t_get_max_characters = xtc_db_query("SELECT e.max_characters
												FROM 
													" . TABLE_GM_GPRINT_ELEMENTS . " e,
													" . TABLE_GM_GPRINT_ELEMENTS_GROUPS . " g
												WHERE
													e.gm_gprint_elements_id = '" . $c_elements_id . "'
													AND e.gm_gprint_elements_groups_id = g.gm_gprint_elements_groups_id
													AND (g.group_type = 'text_input' OR g.group_type = 'textarea')");
		if(xtc_db_num_rows($t_get_max_characters) == 1)
		{
			$t_max_characters = xtc_db_fetch_array($t_get_max_characters);
			$t_max_characters = (int)$t_max_characters['max_characters'];
						
			if($t_max_characters > 0)
			{
				if(gm_get_conf('GM_GPRINT_EXCLUDE_SPACES') == 1)
				{
					$t_value = str_replace(' ', '', $t_value);
				}
				$t_value = str_replace("\n", '', $t_value);
				$t_value = str_replace("\r", '', $t_value);
				$t_value = str_replace("\t", '', $t_value);
				$t_value = str_replace("\v", '', $t_value);
				
				if(strlen_wrapper($t_value) > (int)$t_max_characters)
				{
					$t_value = substr_wrapper($p_value, 0, (strlen_wrapper($t_value) - (int)$t_max_characters) * -1);
				}
				else
				{
					$t_value = $p_value;
				}
			}
		}
		
		return $t_value;		
	}
	
	function add_file($p_products_id, $p_elements_id, $p_filename)
	{
		$c_customers_id = (int)$_SESSION['customer_id'];
		$c_elements_id = (int)$p_elements_id;
		$c_filename = basename(gm_prepare_string($p_filename));
		
		$t_random_filename = rand(10000000, 99999999);
		
		$t_download_key = md5(time() . $t_random_filename);
		
		if($_SERVER['HTTP_X_FORWARDED_FOR'])
		{
			$t_customers_ip_hash = md5($_SERVER['HTTP_X_FORWARDED_FOR']);
		}
		else
		{
			$t_customers_ip_hash = md5($_SERVER['REMOTE_ADDR']);
		}		
		
		$t_create_db_entry = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_UPLOADS . "
											SET 
												datetime = NOW(),
												customers_id = '" . $c_customers_id . "',
												filename = '" . $c_filename . "',
												download_key = '" . $t_download_key . "',
												ip_hash = '" . $t_customers_ip_hash . "'");
		$t_uploads_id = xtc_db_insert_id($t_create_db_entry);
		$t_filename = $t_uploads_id . '_' . $t_random_filename;
		
		$t_save_filename = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_UPLOADS . "
											SET encrypted_filename = '" . $t_filename . "'
											WHERE gm_gprint_uploads_id = '" . $t_uploads_id . "'");
		
		$this->v_files[$p_products_id][$p_elements_id] = $t_uploads_id;
		
		return $t_filename;
	}
	
	function get_allowed_extensions($p_elements_id)
	{
		$c_elements_id = (int)$p_elements_id;
		$t_allowed_extensions = '';
		
		$t_get_allowed_extensions = xtc_db_query("SELECT allowed_extensions
													FROM " . TABLE_GM_GPRINT_ELEMENTS . "
													WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
		if(xtc_db_num_rows($t_get_allowed_extensions) == 1)
		{
			$t_elements_data = xtc_db_fetch_array($t_get_allowed_extensions);
			$t_allowed_extensions = $t_elements_data['allowed_extensions'];
		}
		
		return $t_allowed_extensions;
	}
	
	function get_minimum_filesize($p_elements_id)
	{
		$c_elements_id = (int)$p_elements_id;
		$t_minimum_filesize = 0;
		
		$t_get_minimum_filesize = xtc_db_query("SELECT minimum_filesize
													FROM " . TABLE_GM_GPRINT_ELEMENTS . "
													WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
		if(xtc_db_num_rows($t_get_minimum_filesize) == 1)
		{
			$t_elements_data = xtc_db_fetch_array($t_get_minimum_filesize);
			$t_minimum_filesize = (double)$t_elements_data['minimum_filesize'];
		}
		
		return $t_minimum_filesize;
	}
	
	function get_maximum_filesize($p_elements_id)
	{
		$c_elements_id = (int)$p_elements_id;
		$t_maximum_filesize = 0;
		
		$t_get_maximum_filesize = xtc_db_query("SELECT maximum_filesize
													FROM " . TABLE_GM_GPRINT_ELEMENTS . "
													WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
		if(xtc_db_num_rows($t_get_maximum_filesize) == 1)
		{
			$t_elements_data = xtc_db_fetch_array($t_get_maximum_filesize);
			$t_maximum_filesize = (double)$t_elements_data['maximum_filesize'];
		}
		
		return $t_maximum_filesize;
	}
	
	function restore_file($p_products_id, $p_elements_id, $p_uploads_id)
	{
		$this->v_files[$p_products_id][$p_elements_id] = $p_uploads_id;
	}
	
	function save()
	{
		$c_customers_id = (int)$_SESSION['customer_id'];
		
		if($c_customers_id > 0)
		{
			$t_remove = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . "
											WHERE customers_id = '" . $c_customers_id . "'");
			
			foreach($this->v_elements AS $t_products_id => $t_element){
				foreach($this->v_elements[$t_products_id] AS $t_elements_id => $t_elements_value){
					
					if(isset($this->v_files[$t_products_id][$t_elements_id]))
					{
						$c_uploads_id = (int)$this->v_files[$t_products_id][$t_elements_id];
						$t_uploads_id = ", gm_gprint_uploads_id = '" . $c_uploads_id . "'";
					}
					else
					{
						$t_uploads_id = '';
					}
					
					$c_elements_id = (int)$t_elements_id;
					$c_products_id = gm_string_filter($t_products_id, '0-9{}x');
					$c_elements_value = mysql_real_escape_string(stripslashes($t_elements_value));
					
					$t_insert = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . "
												SET gm_gprint_elements_id = '" . $c_elements_id . "',
													products_id = '" . $c_products_id . "',
													customers_id = '" . $c_customers_id . "',
													elements_value = '" . $c_elements_value . "'
													" . $t_uploads_id . "");
				}
			}
		}
	}
	
	function remove($p_products_id)
	{
		$c_customers_id = (int)$_SESSION['customer_id'];
		$c_products_id = gm_prepare_string($p_products_id);
		
		unset($this->v_elements[$p_products_id]);
		unset($this->v_files[$p_products_id]);
		
		$t_remove = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . "
										WHERE customers_id = '" . $c_customers_id . "'
												AND products_id = '" . $c_products_id . "'");
	}
	
	function restore()
	{
		$this->clean_up();
		
		$c_customers_id = (int)$_SESSION['customer_id'];
		
		$t_old_product_ids = array();
		
		if($c_customers_id > 0)
		{
			$t_get_customers_wishlist = xtc_db_query("SELECT
															gm_gprint_elements_id,
															products_id,
															elements_value,
															gm_gprint_uploads_id
														FROM " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . "
														WHERE customers_id = '" . $c_customers_id . "'");
			while($t_customers_wishlist = xtc_db_fetch_array($t_get_customers_wishlist))
			{
				$t_new_products_id = $this->check_wishlist($t_customers_wishlist['products_id'], 'wishList');
				
				if($t_new_products_id !== false)
				{
					if(!in_array($t_customers_wishlist['products_id'], $t_old_product_ids))
					{
						$t_old_product_ids[] = $t_customers_wishlist['products_id'];
					}
					
					$this->add($t_new_products_id, $t_customers_wishlist['gm_gprint_elements_id'], $t_customers_wishlist['elements_value']);
					
					if($t_customers_wishlist['gm_gprint_uploads_id'] > 0)
					{
						$this->restore_file($t_new_products_id, $t_customers_wishlist['gm_gprint_elements_id'], $t_customers_wishlist['gm_gprint_uploads_id']);
					}				
				}
				else
				{
					$this->add($t_customers_wishlist['products_id'], $t_customers_wishlist['gm_gprint_elements_id'], $t_customers_wishlist['elements_value']);
					
					if($t_customers_wishlist['gm_gprint_uploads_id'] > 0)
					{
						$this->restore_file($t_customers_wishlist['products_id'], $t_customers_wishlist['gm_gprint_elements_id'], $t_customers_wishlist['gm_gprint_uploads_id']);
					}	
				}
			}
			
			for($i = 0; $i < count($t_old_product_ids); $i++)
			{
				$this->remove($t_old_product_ids[$i]);
			}
			
			$this->save();
		}
		else
		{
			foreach($this->v_elements AS $t_products_id => $t_content)
			{
				$t_new_products_id = $this->check_wishlist($t_products_id, 'wishList');
			}
		}
	}
	
	function clean_up(){
		$t_get_old_data = xtc_db_query("SELECT DISTINCT products_id
										FROM " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . "");
		while($t_old_data = xtc_db_fetch_array($t_get_old_data))
		{
			$c_products_id = (int)xtc_get_prid($t_old_data['products_id']);
			
			$t_check = xtc_db_query("SELECT COUNT(*) AS count
										FROM " . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS . "
										WHERE products_id = '" . $c_products_id . "'");
			$t_check_result = xtc_db_fetch_array($t_check);
			
			if($t_check_result['count'] == 0)
			{
				$t_delete = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . "
											WHERE products_id = '" . $t_old_data['products_id'] . "'");
				unset($this->v_elements[$t_old_data['products_id']]);
				unset($this->v_files[$t_old_data['products_id']]);
			}
		}
		
		$t_get_old_data = xtc_db_query("SELECT 
											c.gm_gprint_wishlist_elements_id, 
											c.products_id,
											c.gm_gprint_elements_id, 
											e.gm_gprint_elements_id
										FROM
											" . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . " c
										LEFT JOIN " . TABLE_GM_GPRINT_ELEMENTS . " AS e USING (gm_gprint_elements_id)
										WHERE e.gm_gprint_elements_id IS NULL");
		while($t_old_data = xtc_db_fetch_array($t_get_old_data))
		{
			$t_delete = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . "
										WHERE gm_gprint_wishlist_elements_id = '" . $t_old_data['gm_gprint_wishlist_elements_id'] . "'");
			unset($this->v_elements[$t_old_data['products_id']][$t_old_data['gm_gprint_wishlist_elements_id']]);
			unset($this->v_files[$t_old_data['products_id']][$t_old_data['gm_gprint_wishlist_elements_id']]);
		}
	}
	
	function empty_wishlist()
	{
		$c_customers_id = (int)$_SESSION['customer_id'];
		
		$t_empty_cart = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . "
										WHERE customers_id = '" . $c_customers_id . "'");
		
		$this->v_elements = array();
		$this->v_files = array();
	}
	
	function get_filename($p_elements_id, $p_product, $p_decrypted_filename = false)
	{
		$t_filename = false;
		$c_customers_id = (int)$_SESSION['customer_id'];
		$c_elements_id = (int)$p_elements_id;
		$c_product = gm_prepare_string($p_product);
		
		if(isset($_SESSION['coo_gprint_wishlist']->v_files[$c_product][$p_elements_id]))
		{
			$t_gm_gprint_uploads_id = (int)$_SESSION['coo_gprint_wishlist']->v_files[$c_product][$p_elements_id];
			
			$t_get_filename = xtc_db_query("SELECT 
												filename,
												encrypted_filename
											FROM " . TABLE_GM_GPRINT_UPLOADS . "
											WHERE
												gm_gprint_uploads_id = '" . $t_gm_gprint_uploads_id . "'
												AND customers_id = '" . $c_customers_id . "'");
			if(xtc_db_num_rows($t_get_filename) == 1)
			{
				$t_file = xtc_db_fetch_array($t_get_filename);
				$t_filename = $t_file['encrypted_filename'];
				if($p_decrypted_filename)
				{
					$t_filename = $t_file['filename'];
				}
			}
		}		
		
		return $t_filename;
	}
	
	function check_wishlist($p_product, $p_source, $p_fix = true)
	{
		$count_found = 0;
		$t_new_key = false;
		
		$t_product = str_replace('}', '{', $p_product);
		$t_product = explode('{', $t_product);
		
		if($p_source == 'wishList' && !empty($t_product))
		{
			foreach($_SESSION['wishList']->contents AS $t_products_id => $t_content)
			{
				$t_products_ids = str_replace('}', '{', $t_products_id);
				$t_products_ids = explode('{', $t_products_ids);
				
				if($t_product[0] == $t_products_ids[0])
				{
					for($i = 1; $i < count($t_product); $i = $i+2)
					{
						for($j = 1; $j < count($t_products_ids); $j = $j+2)
						{
							if($t_product[$i] == $t_products_ids[$j] && $t_product[$i+1] == $t_products_ids[$j+1])
							{
								$count_found++;
							}
						}
					}
				}
				
				if($count_found == ((count($t_product) - 1) / 2))
				{
					$t_new_key = $t_products_id;	
					
					if($p_product !== $t_new_key && $p_fix === true)
					{
						$this->fix_product_key($p_product, $t_new_key);
					}
					elseif($p_fix === true)
					{
						$t_new_key = false;
					}
				}
				
				$count_found = 0;
			}
		}
		elseif($p_source = 'coo_gprint_wishlist' && !empty($t_product))
		{
			foreach($_SESSION['coo_gprint_wishlist']->v_elements AS $t_products_id => $t_content)
			{
				$t_products_ids = str_replace('}', '{', $t_products_id);
				$t_products_ids = explode('{', $t_products_ids);
				
				if($t_product[0] == $t_products_ids[0])
				{
					for($i = 1; $i < count($t_product); $i = $i+2)
					{
						for($j = 1; $j < count($t_products_ids); $j = $j+2)
						{
							if($t_product[$i] == $t_products_ids[$j] && $t_product[$i+1] == $t_products_ids[$j+1])
							{
								$count_found++;
							}
						}
					}
				}
				
				if($count_found == ((count($t_product) - 1) / 2))
				{
					$t_new_key = $t_products_id;	
				
					if($t_products_id !== $p_product && $p_fix === true)
					{
						$this->fix_product_key($t_products_id, $t_new_key);
					}
					elseif($p_fix === true)
					{
						$t_new_key = false;
					}
				}
				
				$count_found = 0;
			}
		}
		else
		{
			$t_new_key = false;
		}
		
		return $t_new_key;
	}
	
	function fix_product_key($p_old_key, $p_new_key)
	{
		$c_customers_id = (int)$_SESSION['customer_id'];
		$c_old_key = gm_string_filter($p_old_key, '0-9{}x');
		$c_new_key = gm_string_filter($p_new_key, '0-9{}x');
		
		if($c_customers_id > 0)
		{
			$t_update = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_WISHLIST_ELEMENTS . " 
										SET products_id = '" . $c_new_key  . "'
										WHERE 
											products_id = '" . $c_old_key  . "'
											AND customers_id = '" . $c_customers_id . "'");
		}
		
		if(isset($this->v_elements[$c_old_key]))
		{
			$this->v_elements[$c_new_key] = $this->v_elements[$c_old_key];
			unset($this->v_elements[$c_old_key]);
		}
		if(isset($this->v_files[$c_old_key]))
		{
			$this->v_files[$c_new_key] = $this->v_files[$c_old_key];
			unset($this->v_files[$c_old_key]);
		}
	}
}
MainFactory::load_origin_class('GMGPrintWishlistManager');