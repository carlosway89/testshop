<?php
/* --------------------------------------------------------------
  CategoriesMenuBoxContentView.inc.php 2014-10-24 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class CategoriesMenuBoxContentView extends ContentView
{
	public $v_tree_depth = 0;
	protected $categoryId = 0;
	protected $currentCategoryId = 0;
	protected $categoriesParentsArray	= array();

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_categories.html');
		$this->set_caching_enabled(true);
	}

	public function prepare_data()
	{
		$this->add_cache_id_elements(array(
										 $this->get_tree_depth(),
										 $this->currentCategoryId,
										 $this->categoryId
									 ));

		if($this->is_cached() == false)
		{
			if(is_object($GLOBALS['coo_debugger']))
			{
				$GLOBALS['coo_debugger']->log('CategoriesMenuBoxContentView get_html NO_CACHE', 'SmartyCache');
			}

			$c_current_category_id = (int)$this->currentCategoryId;
			$t_tree_depth = $this->get_tree_depth();

			/** @var CategoriesAgent $coo_categories_agent */
			$coo_categories_agent =& MainFactory::create_object('CategoriesAgent', array(), true);
			$t_categories_info_array = $coo_categories_agent->get_categories_info_tree($c_current_category_id, $_SESSION['languages_id'], $t_tree_depth);
			if(isset($_GET['cPath']))
			{
				$this->categoriesParentsArray = $coo_categories_agent->getPartentsIds($_GET['cPath']);
			}

			$this->_generateParentsIds();
			
			$this->set_content_data('current_category_id', $c_current_category_id);
			$this->set_content_data('CATEGORIES_DATA', $t_categories_info_array);
		}
		else
		{
			if(is_object($GLOBALS['coo_debugger']))
			{
				$GLOBALS['coo_debugger']->log('CategoriesMenuBoxContentView get_html USE_CACHE', 'SmartyCache');
			}
		}

		$this->set_content_data('category_id', $this->categoryId);
	}

	public function set_tree_depth($p_depth)
	{
		$this->v_tree_depth = (int)$p_depth;
	}

	public function get_tree_depth()
	{
		return $this->v_tree_depth;
	}

	public function setCategoryId($p_categoryId){
		$this->categoryId = (int)$p_categoryId;
	}

	public function setCurrentCategoryId($p_currentCategoryId){
		$this->currentCategoryId = (int)$p_currentCategoryId;
	}

	protected function _generateParentsIds()
	{
		$categoriesParents = '<script type="text/javascript">parentsIds = ' . json_encode($this->categoriesParentsArray) . ';</script>';
		$this->set_content_data('categories_parents_ids', $categoriesParents);
	}

}