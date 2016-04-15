<?php
/* --------------------------------------------------------------
   SampleExtender.inc.php 2015-12-01 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleExtender
 *
 * This is a sample overload for the AdminOrderOverviewTableExtenderComponent.
 *
 * @extends SampleExtender_parent
 */
class SampleExtender extends SampleExtender_parent
{
	/**
	 * @var string The name of the column. It's just used to address the content of the column
	 */
	private $columnName = 'sample';
	
	
	/**
	 * The following properties should be set within the constructor:
	 * - setHeadCell:    Content of the head cell
	 * - setWidth:       Width of the column
	 * - setHeadClasses: Any custom classes (space separated)
	 * 
	 * The parent constructor MUST be called.
	 */
	public function __construct()
	{
		$this->setHeadCell($this->columnName, 'Beispiel');
		$this->setWidth($this->columnName, '50px');
		$this->setHeadClasses($this->columnName, 'sample_class');
		
		parent::__construct();
	}
	
	
	/**
	 * The following properties should be set within the proceed method:
	 * - setContent: Content of the cell with the given order ID
	 * - setClasses: Any custom classes (space separated)
	 * 
	 * The parent proceed method MUST be called with the given order ID.
	 * 
	 * @param \IdInterface $orderId The order ID of the current cell
	 */
	public function proceed(IdInterface $orderId)
	{
		$this->setClasses($this->columnName, 'sample_class_2');
		$this->setContent($this->columnName, $this->getContent($orderId));
		
		parent::proceed($orderId);
	}
	
	
	/**
	 * Returns the calculated content by a given order ID
	 * 
	 * @param \IdInterface $orderId The order ID of the current cell
	 *
	 * @return string The calculated content
	 */
	private function getContent(IdInterface $orderId)
	{
		$content = 'Beispieldaten ' . $orderId;
		
		return $content;
	}
}