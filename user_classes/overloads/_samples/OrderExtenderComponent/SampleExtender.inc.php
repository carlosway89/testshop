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
	public function proceed()
	{
		$this->v_output_buffer['below_withdrawal_heading'] = 'SampleExtender';
		$this->v_output_buffer['below_withdrawal']         = '<div style="color: #0000FF">'
		                                                     . $this->getContent()
		                                                     . '</div>';
		
		echo 'This will appear below the order information box.';
		
		//The following two rows need to be at the end of every overload of the OrderExtender
		$this->addContent();
		parent::proceed();
	}
	
	
	protected function getContent()
	{
		return 'This could be your content!';
	}
}