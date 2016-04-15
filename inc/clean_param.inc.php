<?php
/* --------------------------------------------------------------
   clean_param.inc.php 2014-08-24 dwue
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

  	function clean_param($p_param, $p_backend = false)
	{
		$t_filtered_params = array(
							'action',
							'cID',
							'coID',
							'pID',
							);
		
		
		if(empty($p_param))
			return $p_param;
		
		
		if($p_backend == false)
		{
			$p_param = str_replace('&amp;', '&', $p_param);
			$p_param = str_replace('&', '&amp;', $p_param);
			
			
			$t_param_delimiter = '&amp;';
		}
		else
		{
			strpos($p_param, '&amp;') !== false ? $t_param_delimiter = '&amp;' : $t_param_delimiter = '&';
		}
		$p_param = str_replace('#', '%23', $p_param);
		
		$t_params_pairs = explode($t_param_delimiter, $p_param);
		
		$t_param = array();
		

		// if more than one GET-parameter is passed...
		if(is_array($t_params_pairs) && count($t_params_pairs))
		{
			foreach($t_params_pairs as $t_param_pair)
			{
				preg_match('/(.*?)=(.*)/', $t_param_pair, $t_matches);
				// if GET-parameter has a value
				if(is_array($t_matches) && isset($t_matches[2]))
				{
					$t_param[] = array($t_matches[1], $t_matches[2]);
				}
				else
				{
					$t_param[] = array($t_param_pair, '');
				}
			}
		}
		
		// if just one GET-parameter is passed...
		else
		{
			preg_match('/(.*?)=(.*)/', $p_param, $t_matches);
			// if GET-parameter has a value
			if(is_array($t_matches) && isset($t_matches[2]))
			{
				$t_param[] = array($t_matches[1], $t_matches[2]);
			}
			else
			{
				$t_param[] = array($p_param, '');
			}
		}
		

		for($i=0; $i<count($t_param); $i++)
		{
			if(in_array($t_param[$i][0], $t_filtered_params));
			{
				if(preg_match("/[<>\"]/", $t_param[$i][1]))
				{
					$t_param[$i][1] = '';
				}
			}
			$t_param[$i] = implode('=', $t_param[$i]);
		}

		$t_clean_param = implode($t_param_delimiter, $t_param);
		
		
		return $t_clean_param;
	}