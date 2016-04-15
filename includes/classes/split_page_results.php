<?php
/* --------------------------------------------------------------
   split_page_results.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(split_page_results.php,v 1.14 2003/05/27); www.oscommerce.com
   (c) 2003	 nextcommerce (split_page_results.php,v 1.6 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: split_page_results.php 1166 2005-08-21 00:52:02Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  class splitPageResults_ORIGIN {
    var $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page;

    // class constructor
    public function __construct($query, $page, $max_rows, $count_key = '*') {
      $this->sql_query = $query;

      if (empty($page) || (is_numeric($page) == false)) $page = 1;
      $this->current_page_number = $page;

      $this->number_of_rows_per_page = $max_rows;

      $pos_to = strlen_wrapper($this->sql_query);
      $pos_from = strpos_wrapper($this->sql_query, ' FROM', 0);

      $pos_group_by = strpos_wrapper($this->sql_query, ' GROUP BY', $pos_from);
      if (($pos_group_by < $pos_to) && ($pos_group_by != false)) $pos_to = $pos_group_by;

      $pos_having = strpos_wrapper($this->sql_query, ' HAVING', $pos_from);
      if (($pos_having < $pos_to) && ($pos_having != false)) $pos_to = $pos_having;

      $pos_order_by = strpos_wrapper($this->sql_query, ' ORDER BY', $pos_from);
      if (($pos_order_by < $pos_to) && ($pos_order_by != false)) $pos_to = $pos_order_by;

      if (strpos_wrapper($this->sql_query, 'DISTINCT') || strpos_wrapper($this->sql_query, 'GROUP BY')) {
        $count_string = 'DISTINCT ' . xtc_db_input($count_key);
        //$count_string = xtc_db_input($count_key);
      } else {
        $count_string = xtc_db_input($count_key);
      }

      $count_query = xtDBquery($query);
      $count = xtc_db_num_rows($count_query,true);

      $this->number_of_rows = $count;
      @$this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

      if ($this->current_page_number > $this->number_of_pages) {
        $this->current_page_number = $this->number_of_pages;
      }

      $offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));
      if($offset < 1) $offset = 0;

      $this->sql_query .= " LIMIT " . $offset . ", " . $this->number_of_rows_per_page;
    }

    // class functions

    // display split-page-number-links
    function display_links($max_page_links, $parameters = '') {
		global $PHP_SELF, $request_type;

		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');

		$t_href_link_base = '';

		if($coo_seo_boost->boost_categories == true && strpos_wrapper(gm_get_env_info('SCRIPT_NAME'), 'index.php') !== false) {
			# use boost url for splitting urls
			$t_href_link_base = $coo_seo_boost->get_current_boost_url();
		}
		else {
			# use default url for splitting urls
			$t_href_link_base = basename($PHP_SELF);
		}


		$display_links_string = '';

		$class = 'class="pageResults"';

		if (xtc_not_null($parameters) && (substr_wrapper($parameters, -1) != '&')) $parameters .= '&';

		// previous button - not displayed on first page
		if ($this->current_page_number > 1) $display_links_string .= '<a href="' . xtc_href_link($t_href_link_base, $parameters . 'page=' . ($this->current_page_number - 1), $request_type) . '" class="pageResults" title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' ">' . PREVNEXT_BUTTON_PREV . '</a>&nbsp;&nbsp;';

		$t_page_numbers_array = $this->get_page_numbers_array($this->current_page_number, $max_page_links, $this->number_of_pages);
		
		for($i = 0; $i < count($t_page_numbers_array); $i++)
		{
			if((int)$t_page_numbers_array[$i]['PAGE'] == $this->current_page_number)
			{
				$display_links_string .= '&nbsp;<strong>' . $t_page_numbers_array[$i]['TEXT'] . '</strong>&nbsp;';
			}
			elseif($t_page_numbers_array[$i]['TEXT'] == '...')
			{
				if($i == 1)
				{
					$display_links_string .= '<a href="' . xtc_href_link($t_href_link_base, $parameters . 'page=' . $t_page_numbers_array[$i]['PAGE'], $request_type) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>';
				}
				else
				{
					$display_links_string .= '<a href="' . xtc_href_link($t_href_link_base, $parameters . 'page=' . $t_page_numbers_array[$i]['PAGE'], $request_type) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>&nbsp;';
				}
			}
			else
			{
				$display_links_string .= '&nbsp;<a href="' . xtc_href_link($t_href_link_base, $parameters . 'page=' . $t_page_numbers_array[$i]['PAGE'], $request_type) . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $t_page_numbers_array[$i]['PAGE']) . ' ">' . $t_page_numbers_array[$i]['TEXT'] . '</a>&nbsp;';
			}
		}

		// next button
		if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '&nbsp;<a href="' . xtc_href_link($t_href_link_base, $parameters . 'page=' . ($this->current_page_number + 1), $request_type) . '" class="pageResults" title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' ">' . PREVNEXT_BUTTON_NEXT . '</a>&nbsp;';

		return $display_links_string;
    }

	function get_page_numbers_array($p_current_page, $p_max_page_number, $p_max_page)
	{
		$t_pages_array = array();

		$t_check_sum = 1 + (($p_max_page_number - 1) * 2) + 2;
		$count_index = 0;

		if($t_check_sum >= $p_max_page)
		{
			for($i = 1; $i <= $p_max_page; $i++)
			{
				$t_pages_array[$i-1]['PAGE'] = $i;
				$t_pages_array[$i-1]['TEXT'] = $i;
			}
		}
		else
		{

			$t_pages_before_and_after = $p_max_page_number - 1;
			if($p_current_page - $t_pages_before_and_after > 1)
			{
				$t_pages_array[$count_index]['PAGE'] = 1;
				$t_pages_array[$count_index]['TEXT'] = 1;
			}
			if($p_current_page - $t_pages_before_and_after > 2)
			{
				$count_index = count($t_pages_array);
				$t_pages_array[$count_index]['PAGE'] = $p_current_page - $t_pages_before_and_after - 1;
				$t_pages_array[$count_index]['TEXT'] = '...';
			}
			for($i = 0; $i < $t_pages_before_and_after * 2 + 1; $i++)
			{
				if($p_current_page - $t_pages_before_and_after + $i < $p_max_page && $p_current_page - $t_pages_before_and_after + $i > 0)
				{
					$count_index = count($t_pages_array);
					$t_pages_array[$count_index]['PAGE'] = $p_current_page - $t_pages_before_and_after + $i;
					$t_pages_array[$count_index]['TEXT'] = $p_current_page - $t_pages_before_and_after + $i;
				}
				
			}
			if($t_pages_array[count($t_pages_array)-1]['PAGE'] == $p_max_page - 2)
			{
				$count_index = count($t_pages_array);
				$t_pages_array[$count_index]['PAGE'] = $p_max_page - 1;
				$t_pages_array[$count_index]['TEXT'] = $p_max_page - 1;
			}
			elseif($t_pages_array[count($t_pages_array)-1]['PAGE'] < $p_max_page - 1)
			{
				$count_index = count($t_pages_array);
				$t_pages_array[$count_index]['PAGE'] = $p_current_page + $t_pages_before_and_after + 1;
				$t_pages_array[$count_index]['TEXT'] = '...';
			}
			$count_index = count($t_pages_array);
			$t_pages_array[$count_index]['PAGE'] = $p_max_page;
			$t_pages_array[$count_index]['TEXT'] = $p_max_page;
		}

		return $t_pages_array;
	}

    // display number of total products found
    function display_count($text_output) {
      $to_num = ($this->number_of_rows_per_page * $this->current_page_number);
      if ($to_num > $this->number_of_rows) $to_num = $this->number_of_rows;

      $from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

      if ($to_num == 0) {
        $from_num = 0;
      } else {
        $from_num++;
      }

      return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
    }
  }