<?php
/* --------------------------------------------------------------
   set_memory_limit.php 2013-03-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function set_memory_limit($p_mega_bytes = 128)
{
	$t_success          = false;
	$t_new_memory_limit = (string)$p_mega_bytes . 'M';

	$oneGigabyteInBytes = 1073741824;
	$numberAsRegEx        = '/\d+/';

	if(function_exists('ini_get') && function_exists('ini_set'))
	{
		$t_memory_limit = @ini_get('memory_limit');
		if(preg_match('/[\d]+M/', (string)$t_memory_limit))
		{
			$t_memory_limit_number = (int)substr($t_memory_limit, 0, -1);
			if($t_memory_limit_number < $p_mega_bytes)
			{
				@ini_set('memory_limit', $t_new_memory_limit);
				if(@ini_get('memory_limit') === $t_new_memory_limit)
				{
					$t_success = true;
				}
			}
			else
			{
				$t_success = true;
			}
		}
		else if($t_memory_limit >= $oneGigabyteInBytes && preg_match($numberAsRegEx, (string)$t_memory_limit))
		{
			$t_success = true;
		}
	}

	return $t_success;
}