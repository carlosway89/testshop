<?php
/* --------------------------------------------------------------
   CustomerWriter.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerWriterInterface');

/**
 * Class CustomerWriter
 * 
 * This class is used for writing customer data to the database
 *
 * @category System
 * @package Customers
 * @implements CustomerWriterInterface
 */
class CustomerWriter implements CustomerWriterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * Constructor of the class CustomerWriter
	 * 
	 * @param CI_DB_query_builder $dbQueryBuilder
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}

	/**
	 * Writes customer data.
	 * 
	 * If customer does not exists it will perform an _insert(), if not it will perform an _update().
	 * 
	 * @param CustomerInterface $customer
	 */
	public function write(CustomerInterface $customer)
	{
		if($customer->getId() == null)
		{
			$this->_insert($customer);
		}
		else
		{
			$this->_update($customer);
		}
	}
	
	/**
	 * Helper method to insert customer data into the database.
	 * 
	 * @param CustomerInterface $customer
	 */
	protected function _insert(CustomerInterface $customer)
	{	
		// Insert customer record.
		$customerDataArray = array(
			'account_type' => (int)$customer->isGuest(),
			'customers_status' => (int)$customer->getStatusId(),
			'customers_cid' => (string)$customer->getCustomerNumber(),
			'customers_gender' => (string)$customer->getGender(),
			'customers_firstname' => (string)$customer->getFirstname(),
			'customers_lastname' => (string)$customer->getLastname(),
			'customers_email_address' => (string)$customer->getEmail(),
			'customers_password' => (string)$customer->getPassword(),
			'customers_vat_id' => (string)$customer->getVatNumber(),
			'customers_vat_id_status' => (string)$customer->getVatNumberStatus(),
			'customers_dob' => (string)$customer->getDateOfBirth()->format('Y-m-d'),
			'customers_telephone' => (string)$customer->getTelephoneNumber(),
			'customers_fax' => (string)$customer->getFaxNumber(),
			'customers_default_address_id' => (string)$customer->getDefaultAddress()->getId(),
			'customers_date_added' => date('Y-m-d H:i:s')
		);
		$this->db->insert('customers', $customerDataArray);
		$customer->setId(MainFactory::create('Id', $this->db->insert_id()));
		
		// Insert customer info record. 
		$customerInfoDataArray = array(
			'customers_info_id' => (string)$customer->getId(),	
			'customers_info_date_of_last_logon' => '0000-00-00',	
			'customers_info_number_of_logons' => '0',	
			'customers_info_date_account_created' => date('Y-m-d H:i:s'),
			'customers_info_date_account_last_modified' => '0000-00-00'
		);
		$this->db->insert('customers_info', $customerInfoDataArray);
	}
	
	/**
	 * Helper method to update customer data in the database.
	 * 
	 * @param CustomerInterface $customer
	 * @todo Use wrapper function getDefaultAddressId() instead of getDefaultAddress()->getId()
	 */
	protected function _update(CustomerInterface $customer)
	{
		$customerId = $customer->getId();
		
		// Update customer record. 
		$customerDataArray = array(
			'account_type' => (int)$customer->isGuest(),
			'customers_status' => (int)$customer->getStatusId(),
			'customers_cid' => (string)$customer->getCustomerNumber(),
			'customers_gender' => (string)$customer->getGender(),
			'customers_firstname' => (string)$customer->getFirstname(),
			'customers_lastname' => (string)$customer->getLastname(),
			'customers_email_address' => (string)$customer->getEmail(),
			'customers_password' => (string)$customer->getPassword(),
			'customers_vat_id' => (string)$customer->getVatNumber(),
			'customers_vat_id_status' => (string)$customer->getVatNumberStatus(),
			'customers_dob' => (string)$customer->getDateOfBirth()->format('Y-m-d'),
			'customers_telephone' => (string)$customer->getTelephoneNumber(),
			'customers_fax' => (string)$customer->getFaxNumber(),
			'customers_default_address_id' => (string)$customer->getDefaultAddress()->getId(),
			'customers_last_modified' => date('Y-m-d H:i:s')
		);

		$this->db->update('customers',
						  $customerDataArray,
						  array('customers_id' => $customerId));
		
		// Update customer info record.
		$customerInfoDataArray = array(
			'customers_info_date_account_last_modified' => date('Y-m-d H:i:s')
		);
		$this->db->update('customers_info',
						  $customerInfoDataArray,
						  array('customers_info_id' => $customerId));
	}
} 