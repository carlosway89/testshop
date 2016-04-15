<?php
/* --------------------------------------------------------------
   xtc_href_link.inc.php 2012-09-26 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_href_link.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_href_link.inc.php 804 2005-02-26 16:42:03Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once (DIR_FS_INC.'clean_param.inc.php');

// The HTML href link wrapper function
  function xtc_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true, $p_relative_url = false) {
	static $t_ssl_urls_array;
	if($t_ssl_urls_array === NULL) $t_ssl_urls_array = array();

	global $request_type, $session_started, $http_domain, $https_domain,$truncate_session_id;

	# read ssl urls into a static variable
	if(count($t_ssl_urls_array) == 0 && function_exists('xtc_db_query') && function_exists('gm_get_env_info'))
	{
		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$t_ssl_urls_array[] = $coo_seo_boost->get_boosted_content_url($coo_seo_boost->get_content_id_by_content_group(14), $_SESSION['languages_id']);
		$t_ssl_urls_array[] = $coo_seo_boost->get_boosted_content_url($coo_seo_boost->get_content_id_by_content_group(7), $_SESSION['languages_id']);

		$t_ssl_urls_array[] = 'coID=14'; // callback
		$t_ssl_urls_array[] = 'coID=7'; // contact

		$t_ssl_urls_array[] = 'newsletter.php';
		$t_ssl_urls_array[] = 'gm_price_offer.php';
		$t_ssl_urls_array[] = 'product_reviews_write.php';
	}

	foreach($t_ssl_urls_array AS $t_content_url)
	{
		if((strpos($parameters, $t_content_url) !== false || strpos($page, $t_content_url) !== false) && $connection == 'NONSSL' && strpos(gm_get_env_info('SCRIPT_NAME'), '/admin/') === false)
		{
			// force SSL
			$connection = 'SSL';
		}
	}

	// bof gm
	$parameters = clean_param($parameters);
	// eof gm

    if (!xtc_not_null($page)) {
		// BOF GM_MOD:
		$page='index.php';
    }

	if($p_relative_url === true)
	{
		$link = '';
	}
    elseif ($connection == 'NONSSL') {
      $link = HTTP_SERVER . DIR_WS_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL == true) {
        $link = HTTPS_SERVER . DIR_WS_CATALOG;
      } else {
        $link = HTTP_SERVER . DIR_WS_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b><br /><br />');
    }

    if (xtc_not_null($parameters)) {
      $link .= $page . '?' . $parameters;
      $separator = '&amp;';
    } else {
      $link .= $page;
      $separator = '?';
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
      if (defined('SID') && xtc_not_null(SID)) {
        $sid = SID;
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
        if ($http_domain != $https_domain) {
          $sid = session_name() . '=' . session_id();
        }
      }
    }

	// remove session if useragent is a known Spider
    if ($truncate_session_id) $sid=NULL;

    if (isset($sid)) {
      $link .= $separator . $sid;
    }

    if ( (SEARCH_ENGINE_FRIENDLY_URLS == 'true') && ($search_engine_safe == true) ) {
      while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);

      $link = str_replace('?', '/', $link);
      $link = str_replace('&amp;', '/', $link);
      $link = str_replace('=', '/', $link);
      $separator = '?';
    }

    return ($link);
  }

    function xtc_href_link_admin($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    global $request_type, $session_started, $http_domain, $https_domain;

    if (!xtc_not_null($page)) {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine the page link!<br /><br />');
    }

    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . DIR_WS_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL == true) {
        $link = HTTPS_SERVER . DIR_WS_CATALOG;
      } else {
        $link = HTTP_SERVER . DIR_WS_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><b>Error!</b></font><br /><br /><b>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</b><br /><br />');
    }

    if (xtc_not_null($parameters)) {
      $link .= $page . '?' . $parameters;
      $separator = '&';
    } else {
      $link .= $page;
      $separator = '?';
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
      if (defined('SID') && xtc_not_null(SID)) {
        $sid = SID;
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
        if ($http_domain != $https_domain) {
          $sid = session_name() . '=' . session_id();
        }
      }
    }

    if ($truncate_session_id) $sid=NULL;

    if (isset($sid)) {
      $link .= $separator . $sid;
    }


    return $link;
  }

 ?>