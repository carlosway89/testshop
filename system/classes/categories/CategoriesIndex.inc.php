<?php
/* --------------------------------------------------------------
   CategoriesIndex.inc.php 2014-10-18 tt
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

class CategoriesIndex
{
	
	/*
	 * constructor
	 */
	function CategoriesIndex()
	{
		
	}
	
	
	
	function build_categories_index($p_products_id)
	{
		# get categories_ids the products_id is linked to
        $t_sql = "SELECT categories_id FROM products_to_categories WHERE products_id='".(int)$p_products_id."'";
        $t_query = xtc_db_query($t_sql);

		$t_categories_id_array = array();

		# collect category's parent_ids from parent tree
        for($i=0; $i<xtc_db_num_rows($t_query); $i++)
		{
            $t_row = xtc_db_fetch_array($t_query);
			$t_parent_id_array = $this->get_categories_parents_array($t_row['categories_id']);

			if($t_parent_id_array !== false)
			{
				$t_categories_id_array[] = $t_row['categories_id'];
				$t_categories_id_array = array_merge($t_categories_id_array, $t_parent_id_array);
			}
		}

		sort($t_categories_id_array); # sort array for cleaning
		$t_categories_id_array = array_unique($t_categories_id_array); # delete doubled categories_ids
		$t_categories_id_array = array_values($t_categories_id_array); # close key gaps after deleting duplicates


		# build index string
		$t_index_field = '';
		for($i=0; $i<sizeof($t_categories_id_array); $i++)
		{
			$t_index_field .= '-'.$t_categories_id_array[$i].'-';
		}

		# declare data_object for saving
		$coo_index_data_object = false;

		# check for existing index
		$coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('categories_index', array('products_id' => $p_products_id)));
		$t_data_object_array = $coo_data_object_group->get_data_objects_array();

		if(sizeof($t_data_object_array) > 0)
		{
			# existing index found
			$coo_index_data_object = $t_data_object_array[0];
		}
		else
		{
			# no index found. create new data object
			$coo_index_data_object = MainFactory::create_object('GMDataObject', array('categories_index'));
			$coo_index_data_object->set_keys(array('products_id' => false));
			$coo_index_data_object->set_data_value('products_id', $p_products_id);
		}

		# save built index
		$coo_index_data_object->set_data_value('categories_index', $t_index_field);
		$coo_index_data_object->save_body_data();

		// Unset objects to prevent out of memory error
		unset($coo_data_object_group, $coo_index_data_object);
	}


	
	function get_categories_parents_array($p_categories_id)
	{
		$t_output_array = array();

		if($p_categories_id == 0)
		{
			# categories_id is root and has no parents. return empty array.
			return $t_output_array;
		}

		# get category's status and parent_id
		$coo_data_object = MainFactory::create_object('GMDataObject', array('categories', array('categories_id' => $p_categories_id)));

		if($coo_data_object->get_data_value('categories_status') == '0')
		{
			# cancel recursion with false on inactive category
			return false;
		}

		$t_parent_id = $coo_data_object->get_data_value('parent_id');
		$t_output_array[] = $t_parent_id;

		if($t_parent_id != 0)
		{
			# get more parents, if category is not root
			$t_parent_id_array = $this->get_categories_parents_array($t_parent_id);
			if($t_parent_id_array === false)
			{
				# cancel recursion with false on inactive category
				return false;
			}
			# merge category's parent tree to categories_id
			$t_output_array = array_merge($t_output_array, $t_parent_id_array);
		}
		return $t_output_array;
	}
}
?>