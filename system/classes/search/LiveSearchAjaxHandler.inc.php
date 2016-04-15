<?php
/* --------------------------------------------------------------
   LiveSearchAjaxHandler.inc.php 2015-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class LiveSearchAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		if(defined('_GM_VALID_CALL') === false) die('x0');

		$f_needle 				= $this->v_data_array['GET']['needle'];
		$t_needle 				= stripslashes($f_needle);
		$c_needle 				= mysql_real_escape_string($t_needle);

		$module_content = array();

		$t_group_check = '';
		if (GROUP_CHECK == 'true') {
			$t_group_check = " and p.group_permission_".(int)$_SESSION['customers_status']['customers_status_id']."=1 ";
		}

		$t_attr_from = '';
		$t_attr_where = '';
		if(SEARCH_IN_ATTR == 'true')
		{
			$t_attr_from .= " LEFT OUTER JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " AS pa ON (p.products_id = pa.products_id) LEFT OUTER JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " AS pov ON (pa.options_values_id = pov.products_options_values_id) LEFT OUTER JOIN products_properties_combis AS ppc ON (p.products_id = ppc.products_id)  LEFT OUTER JOIN products_properties_index AS ppi ON (p.products_id = ppi.products_id) ";
			$t_attr_where .= "OR pa.attributes_model LIKE ('%".$c_needle."%') ";
			$t_attr_where .= "OR ppc.combi_model LIKE ('%".$c_needle."%') ";
            $t_attr_where .= "OR (ppi.properties_name LIKE ('%".$c_needle."%') ";
            $t_attr_where .= "AND ppi.language_id = '".(int) $_SESSION['languages_id']."')";
            $t_attr_where .= "OR (ppi.values_name LIKE ('%".$c_needle."%') ";
            $t_attr_where .= "AND ppi.language_id = '".(int) $_SESSION['languages_id']."')";
			$t_attr_where .= "OR (pov.products_options_values_name LIKE ('%" . $c_needle . "%') AND pov.language_id = '". (int)$_SESSION['languages_id'] . "')";
		}

		$t_desc_where = '';
		if (SEARCH_IN_DESC == 'true')
		{
		   $t_desc_where .= "OR pd.products_description LIKE ('%". $c_needle ."%') ";
		   $t_desc_where .= "OR pd.products_short_description LIKE ('%". $c_needle ."%') ";
		}
		
		$t_cat_from = '';
		$t_cat_where = '';
		if(!empty($this->v_data_array['GET']['categories_id']))
		{
			$c_category_id = (int)$this->v_data_array['GET']['categories_id'];
			$t_cat_from .= 'categories_index ci,';
			$t_cat_where .= ' p.products_id = ci.products_id AND ci.categories_index LIKE "%-' . $c_category_id . '-%" AND ';
		}
		
		$result = xtc_db_query('
			SELECT DISTINCT
				pd.products_id AS products_id,
				pd.products_name AS products_name
			FROM
				products p
				' . $t_attr_from . ',
				' . $t_cat_from . '
				products_description pd
			WHERE
				p.products_status = 1 AND
				' . $t_cat_where . '
				p.products_id = pd.products_id AND
				(pd.products_name LIKE "%' . $c_needle . '%" 
					OR p.products_model LIKE ("%'.$c_needle.'%") 
					OR p.products_ean LIKE ("%'.$c_needle.'%") '
					. $t_desc_where . ' '
					. $t_attr_where . ')
				AND
				pd.language_id = "'	. (int)$_SESSION['languages_id'] . '"
				' . $t_group_check . '
			ORDER BY
				pd.products_name
			LIMIT 0,10
		');

		while(($row = xtc_db_fetch_array($result) ))
		{
			$module_content[] = array(
														'PRODUCTS_ID' 	=> $row['products_id'],
														'PRODUCTS_URL'	=> xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($row['products_id'], $row['products_name']) ),
														'PRODUCTS_NAME' => $row['products_name']
													);
		}

		if(sizeof($module_content) > 0) {
			$smarty = new Smarty;
			$smarty->assign('module_content', $module_content);

			$smarty->assign('language', $_SESSION['language']);
			$smarty->caching = 0;

			$this->v_output_buffer = $smarty->fetch(CURRENT_TEMPLATE.'/module/gm_live_search.html');
		}

		return true;
	}
}
?>