<?php
/*
	--------------------------------------------------------------
	gm_get_privacy_link.inc.php 2013-11-06 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	/*
	*	-> function to get the privacy link
	*/	
	function gm_get_privacy_link($p_key) 
	{		
		$t_privacy_link = '0';

		if(gm_get_conf($p_key) == 1) 
		{ 
			$gm_query = xtc_db_query("
										SELECT
											*
										FROM 
											content_manager
										WHERE 
											languages_id	=	'" . (int)$_SESSION['languages_id']."'
										AND 
											content_group		= '2'
			");

			$gm_array = xtc_db_fetch_array($gm_query);

			$SEF_parameter = '';
			if (SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			{
				$SEF_parameter = '&content=' . xtc_cleanName($gm_array['content_title']);
			}
			$t_privacy_link = xtc_href_link('popup_content.php', 'lightbox_mode=1&coID=' . $gm_array['content_group'] . $SEF_parameter, 'SSL');
			
			$t_privacy_link = sprintf(ENTRY_SHOW_PRIVACY, $t_privacy_link);
		}	
		return $t_privacy_link;
	}
