<?php


class test1_Order extends test1_Order_parent {

	function __construct($order_id)
	{
//		parent::__construct($order_id);
		var_dump($this);
		die('AdminOrder');
	}

/*	function insert_product($products_data, $dest_category_id, $action = 'insert')
	{
		die('test: insert_product overloaded');
	}
*/
}