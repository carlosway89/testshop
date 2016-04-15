<?php

class test1_categories extends test1_categories_parent {
	function insert_product($products_data, $dest_category_id, $action = 'insert')
	{
		die('test: insert_product overloaded');
	}
}