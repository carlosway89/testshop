<?php
/* --------------------------------------------------------------
  StyleEdit v2.0
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2010 Gambio GmbH
  --------------------------------------------------------------
*/

class GMBoxesMaster 
{
	private $cooMySQLi;
	private $v_current_template;
	private $font_family 	= array();
	private $size 			= array();
  	
    public function __construct($p_v_current_template, GMSEDatabase $dbConnection) 
    { 
		$this->v_current_template = trim($p_v_current_template);
		$this->cooMySQLi = $dbConnection;
    }


	/**
	 * function to set position
	 * @param string $box_name
	 * @param string $position
	 * 
	 * @return void
	 */
	public function set_position($box_name, $position)
    {
		$query = 	"UPDATE
						`gm_boxes`
					SET
						`position` = '" . addslashes($position) . "'
					WHERE
						`box_name` = '" . addslashes($box_name) . "'
					AND
						`template_name` = '" . addslashes($this->v_current_template) . "'";
		$this->cooMySQLi->query($query);
    }


	/**
	 * function to organize positions
	 * 
	 * @return void
	 */
	public function organize_positions()
	{
		$query = 	"SELECT
						`box_name`
					FROM
						`gm_boxes`
					WHERE
						`template_name` = '" . $this->cooMySQLi->real_escape_string($this->v_current_template) . "'
					AND
						CAST(REPLACE(`position`, 'gm_box_pos_', '') as UNSIGNED) < 101
					GROUP BY
						`box_name`
					ORDER BY
						CAST(REPLACE(`position`, 'gm_box_pos_', '') as UNSIGNED) ASC";
		$result = $this->cooMySQLi->query($query);
		if($this->cooMySQLi->num_rows($result) > 0)
		{
			$count = 2;
			while($t_result_array = $this->cooMySQLi->fetch_array($result))
			{
				$query = 	"UPDATE
								`gm_boxes`
							SET
								`position` = 'gm_box_pos_" . (int)$count . "'
							WHERE
								`template_name` = '" . $this->cooMySQLi->real_escape_string($this->v_current_template) . "'
							AND
								`box_name` = '" . $this->cooMySQLi->real_escape_string($t_result_array['box_name']) . "'";
				$this->cooMySQLi->query($query);
				$count += 2;
			}
		}

		$query = 	"SELECT
						`box_name`
					FROM
						`gm_boxes`
					WHERE
						`template_name` = '" . $this->cooMySQLi->real_escape_string($this->v_current_template) . "'
					AND
						CAST(REPLACE(position, 'gm_box_pos_', '') as UNSIGNED) >= 101
					GROUP BY
						`box_name`
					ORDER BY
						CAST(REPLACE(`position`, 'gm_box_pos_', '') as UNSIGNED) ASC";
		$result = $this->cooMySQLi->query($query);
		if($this->cooMySQLi->num_rows($result) > 0)
		{
			$count = 102;
			while($t_result_array = $this->cooMySQLi->fetch_array($result))
			{
				$query = 	"UPDATE
								`gm_boxes`
							SET
								`position` = 'gm_box_pos_" . (int)$count . "'
							WHERE
								`template_name` = '" . $this->cooMySQLi->real_escape_string($this->v_current_template) . "'
							AND
								`box_name` = '" . $this->cooMySQLi->real_escape_string($t_result_array['box_name']) . "'";
				$this->cooMySQLi->query($query);
				$count += 2;
			}
		}
	}


	/**
	 * function to get position value
	 * @param string $box_name
	 *
	 * @return string|mixed
	 */
	public function get_position($box_name)
    {
		$query = 	"SELECT
						`position`
					FROM
						`gm_boxes`
					WHERE
						`box_name` = '" . addslashes($box_name) . "'
					AND
						`template_name` = '" . addslashes($this->v_current_template) . "'";
		$result = $this->cooMySQLi->query($query);

    	$data = $this->cooMySQLi->fetch_array($result);
    	return $data['position'];
    }


	/**
	 * function to set status
	 * @param string $box_name
	 * @param string $status
	 * 
	 * @return void
	 */
	public function set_status($box_name, $status)
    {
		$query = 	"UPDATE
						`gm_boxes`
					SET
						`box_status` = '" . addslashes($status) . "'
					WHERE
						`box_name` = '"  . addslashes($box_name) . "'
					AND
						`template_name` = '" . addslashes($this->v_current_template) . "'";
		$this->cooMySQLi->query($query);
    }


	/**
	 * function to get status as json
	 * 
	 * @return json|string
	 */
	public function get_status_json()
    {
    	$box_name 	= array();
    	$box_status	= array();
    	
		$query = 	"SELECT
						`box_name`,
						`box_status`
					FROM
						`gm_boxes`
					WHERE
						`template_name` = '" . addslashes($this->v_current_template) . "'
					ORDER BY
						`position`
					ASC";
    	$result = $this->cooMySQLi->query($query);
    	while(($row = $this->cooMySQLi->fetch_array($result) ))
    	{
    		$box_name[] 	= '"'. $row['box_name'] 	.'"';
    		$box_status[] = '"'. $row['box_status'] .'"';
    	}
    	
    	$box_name_values 		= implode(',', $box_name);
    	$box_status_values 	= implode(',', $box_status);
    	
    	$json = '{
    		"box_name": 	['.$box_name_values		.'],
    		"box_status": ['.$box_status_values	.']
    	}';
    	return $json;
    }


	/**
	 * function to get status
	 * @param string $box_name
	 *
	 * @return string|mixed
	 */
	public function get_status($box_name)
    {
		$query = 	"SELECT
						`box_status`
					FROM
						`gm_boxes`
					WHERE
						`box_name` = '" . addslashes($box_name) . "'
					AND
						`template_name` = '" . addslashes($this->v_current_template) . "'";
		$result = $this->cooMySQLi->query($query);
    	$data = $this->cooMySQLi->fetch_array($result);
    	return $data['box_status'];
    }


	/**
	 * function to get the page menu
	 * @param string $p_box_name
	 * @param array  $p_se_cfg_frontend_areas
	 *
	 * @return string
	 */
	public function get_page_menu($p_box_name, array $p_se_cfg_frontend_areas)
    {
		$t_box_id = $this->get_box_id_by_box_name($p_box_name);
		$t_page_menu_row = '';
		
		foreach($p_se_cfg_frontend_areas as $t_area => $t_area_name)
		{
			$t_checked = '';
			if($this->area_exists($t_box_id, $t_area))
			{
				$t_checked = 'checked="checked"';
			}

			$t_page_menu_row .= '<tr><td><input ' . $t_checked . ' type="checkbox" id="se_boxes_area_' . $t_area . '" /></td><td>' . $t_area_name . '</td></tr>';
		}
		
		$t_page_menu = '<div id="se_boxes_page_menu"><div id="se_boxes_page_menu_close">x</div><table border="0" width="100%" cellspacing="2" cellpadding="2">' . $t_page_menu_row . '</table></div>';
		return $t_page_menu;
	}


	/**
	 * function to get a id from a box with the box name
	 * @param string|$p_box_name
	 *
	 * @return string|bool
	 */
	private function get_box_id_by_box_name($p_box_name)
    {
		$query = 	"SELECT
						`boxes_id` AS `id`
					FROM
						`gm_boxes`
					WHERE
						`box_name` = '" . addslashes($p_box_name) . "'
					AND
						`template_name` = '" . addslashes($this->v_current_template) . "'";
		$result = $this->cooMySQLi->query($query);

		if((int)$result->num_rows > 0)
		{
			$t_row = $result->fetch_assoc();

			return $t_row['id'];
		}
		else
		{
			return false;
		}
	}


	/**
	 * function to insert a new area
	 * @param int    $p_box_id
	 * @param string $p_area_name
	 * 
	 * @return void
	 */
	private function insert_area($p_box_id, $p_area_name)
    {
		$query = 	"INSERT INTO
						`gm_boxes_area`
					SET
						`boxes_id` = '" . (int)$p_box_id . "',
						`area` = '" . addslashes($p_area_name) . "'";
		$this->cooMySQLi->query($query);
	}


	/**
	 * function to delete an area
	 * @param int    $p_box_id
	 * @param string $p_area_name
	 * 
	 * @return void
	 */
	private function delete_area($p_box_id, $p_area_name)
    {
		$query = 	"DELETE FROM
						`gm_boxes_area`
					WHERE
						`boxes_id` = '" . (int)$p_box_id . "'
					AND
						`area` = '" . addslashes($p_area_name) . "'";
		$this->cooMySQLi->query($query);
	}


	/**
	 * function to delete all areas by a box id
	 * @param int $p_box_id
	 * 
	 * @return void
	 */
	private function delete_all_areas_by_box_id($p_box_id)
    {
		$query = 	"DELETE FROM
						`gm_boxes_area`
					WHERE
						`boxes_id` = '" . (int)$p_box_id . "'";
		$this->cooMySQLi->query($query);
	}


	/**
	 * @param int    $p_box_id
	 * @param string $p_area_name
	 *
	 * @return bool
	 */
	private function area_exists($p_box_id, $p_area_name)
    {
		$query = 	"SELECT
						`boxes_area_id`
					FROM
						`gm_boxes_area`
					WHERE
						`boxes_id` = '" . (int)$p_box_id . "'
					AND
						`area` = '" . addslashes($p_area_name) . "'";
		$result = $this->cooMySQLi->query($query);

		if((int)$result->num_rows > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * function to set all areas
	 * @param int   $p_box_id
	 * @param array $p_se_cfg_frontend_areas
	 * 
	 * @return void
	 */
	private function set_all_areas($p_box_id, $p_se_cfg_frontend_areas)
    {
		$query = 	"SELECT
						`boxes_area_id`
					FROM
						`gm_boxes_area`
					WHERE
						`boxes_id` = '" . (int)$p_box_id . "'
					AND
						`area` = 'all'";
		$result = $this->cooMySQLi->query($query);

		if((int)$result->num_rows > 0)
		{
			$this->delete_all_areas_by_box_id($p_box_id);
			
			foreach($p_se_cfg_frontend_areas as $t_area => $t_area_name)
			{
				if($t_area != 'all')
				{
					$query = 	"INSERT INTO
									`gm_boxes_area`
								SET
									`boxes_id` = '" . (int)$p_box_id . "',
									`area` = '" . addslashes($t_area) . "'";
					$this->cooMySQLi->query($query);
				}
			}
		}
	}


	/**
	 * function to update the page menu
	 * @param string 		$p_box_name
	 * @param string 		$p_area_name
	 * @param string|bool 	$p_area_active
	 * @param array 		$p_se_cfg_frontend_areas
	 * 
	 * @return void
	 */
	public function update_page_menu($p_box_name, $p_area_name, $p_area_active, $p_se_cfg_frontend_areas)
    {
		$t_box_id = $this->get_box_id_by_box_name($p_box_name);
		
		if($p_area_name == 'all')
		{
			$this->delete_all_areas_by_box_id($t_box_id);
		}

		if($p_area_active == 'false')
		{
			$this->set_all_areas($t_box_id, $p_se_cfg_frontend_areas);
			$this->delete_area($t_box_id, $p_area_name);
		}
		else
		{			
			if($this->area_exists($t_box_id, $p_area_name) === false)
			{
				$this->insert_area($t_box_id, $p_area_name);
			}
		}
	}    
  }
?>