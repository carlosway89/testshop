<?php
/* --------------------------------------------------------------
  MagnalisterModuleCenterModule.inc.php 2015-09-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class MagnalisterModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class MagnalisterModuleCenterModule extends AbstractModuleCenterModule
{
	protected function _init()
	{
		$this->title       = $this->languageTextManager->get_text('magnalister_title');
		$this->description = $this->languageTextManager->get_text('magnalister_description');
		$this->sortOrder   = 13490;
	}


	/**
	 * Installs the module
	 */
	public function install()
	{
		parent::install();

		$columnsQuery = $this->db->query('DESCRIBE `admin_access` \'magnalister\'');
		if(!$columnsQuery->num_rows())
		{
			$this->db->query('ALTER TABLE `admin_access` ADD `magnalister` INT( 1 ) NOT NULL DEFAULT \'0\';');
		}

		$this->db->set('magnalister', '1')->where('customers_id', '1')->limit(1)->update('admin_access');
		$this->db->set('magnalister', '1')
		         ->where('customers_id', $_SESSION['customer_id'])
		         ->limit(1)
		         ->update('admin_access');

		$this->db->insert('configuration', array(
			'configuration_key'      => 'MODULE_MAGNALISTER_STATUS',
			'configuration_value'    => 'True',
			'configuration_group_id' => '6',
			'sort_order'             => '1',
			'set_function'           => '',
			'date_added'             => 'NOW()'
		));
	}


	/**
	 * Uninstalls the module
	 */
	public function uninstall()
	{
		parent::uninstall();

		$this->db->query('ALTER TABLE `admin_access` DROP `magnalister`');
		$this->db->where_in('configuration_key', 'MODULE_MAGNALISTER_STATUS')->delete('configuration');
	}
}