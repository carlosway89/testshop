<?php
/* --------------------------------------------------------------
   SampleExtender.inc.php 2014-01-01 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SampleExtender extends SampleExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		$this->v_output_buffer['marked_elements']['sample']['contents_array'][] = array('text' => '<div align="center"><input type="submit" value="Sample marked" onclick="this.blur();" name="sample" class="button" /></div>');
		$this->v_output_buffer['new_element']['sample']['contents_array'][] = array('text' => '<div align="center"><input type="submit" value="Sample new" onclick="this.blur();" name="sample" class="button" /></div>');
		$this->v_output_buffer['bottom']['sample']['content'] = '<a href="#" class="button float_right">Sample bottom</a>';
		$this->v_output_buffer['information_category']['sample']['contents_array'][] = array('text' => '<div align="center">Sample category info</div>');
		$this->v_output_buffer['information_product']['sample']['contents_array'][] = array('text' => '<div align="center">Sample product info</div>');
		$this->v_output_buffer['active_element_category']['sample']['contents_array'][] = array('text' => '<div align="center"><a href="#" class="button">Sample active category</a></div>');
		$this->v_output_buffer['active_element_product']['sample']['contents_array'][] = array('text' => '<div align="center"><a href="#" class="button">Sample active product</a></div>');
	}
}