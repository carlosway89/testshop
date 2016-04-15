<?php
/* --------------------------------------------------------------
   CustomersApiController.inc.php 2015-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class CustomersApiV2Controller
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class CustomersApiV2Controller extends HttpApiV2Controller
{
	/**
	 * @api        {post} /customers Create Customer
	 * @apiVersion 2.0.0
	 * @apiName    CreateCustomer
	 * @apiGroup   Customers
	 *
	 * @apiDescription
	 * This method enables the creation of a new customer (whether registree or a guest). Additionally
	 * the user can provide new address information or just set the id of an existing one. Check the
	 * examples bellow.
	 *
	 * @apiParamExample {json} Registree (New Address)
	 * {
	 *   "gender": "m",
	 *   "firstname": "John",
	 *   "lastname": "Doe",
	 *   "dateOfBirth": "1985-02-13",
	 *   "vatNumber": "0923429837942",
	 *   "telephone": "2343948798345",
	 *   "fax": "2093049283",
	 *   "email": "customer@email.de",
	 *   "password": "0123456789",
	 *   "type": "registree",
	 *   "address": {
	 *     "company": "Test Company",
	 *     "street": "Test Street",
	 *     "suburb": "Test Suburb",
	 *     "postcode": "23983",
	 *     "city": "Test City",
	 *     "countryId": 81,
	 *     "zoneId": 84,
	 *     "b2bStatus": true
	 *   }
	 * }
	 *
	 * @apiParamExample {json} Registree (Existing Address)
	 * {
	 *   "gender": "m",
	 *   "firstname": "John",
	 *   "lastname": "Doe",
	 *   "dateOfBirth": "1985-02-13",
	 *   "vatNumber": "0923429837942",
	 *   "telephone": "2343948798345",
	 *   "fax": "2093049283",
	 *   "email": "customer@email.de",
	 *   "password": "0123456789",
	 *   "type": "registree",
	 *   "addressId": 57
	 * }
	 *
	 *
	 * @apiParamExample {json} Guest (New Address)
	 * {
	 *   "gender": "m",
	 *   "firstname": "John",
	 *   "lastname": "Doe",
	 *   "dateOfBirth": "1985-02-13",
	 *   "vatNumber": "0923429837942",
	 *   "telephone": "2343948798345",
	 *   "fax": "2093049283",
	 *   "email": "customer@email.de",
	 *   "type": "guest",
	 *   "address": {
	 *     "company": "Test Company",
	 *     "street": "Test Street",
	 *     "suburb": "Test Suburb",
	 *     "postcode": "23983",
	 *     "city": "Test City",
	 *     "countryId": 81,
	 *     "zoneId": 84,
	 *     "b2bStatus": false
	 *   }
	 * }
	 *
	 * @apiParamExample {json} Guest (Existing Address)
	 * {
	 *   "gender": "m",
	 *   "firstname": "John",
	 *   "lastname": "Doe",
	 *   "dateOfBirth": "1985-02-13",
	 *   "vatNumber": "0923429837942",
	 *   "telephone": "2343948798345",
	 *   "fax": "2093049283",
	 *   "email": "customer@email.de",
	 *   "type": "guest",
	 *   "addressId": 57
	 *   }
	 * }
	 *
	 * @apiParam {string} gender Customer's gender, provide "m" for mail and "f" for female.
	 * @apiParam {string} firstname Customer's first name.
	 * @apiParam {string} lastname Customer's last name.
	 * @apiParam {string} dateOfBirth Customer's date of birth in "yyyy-mm-dd" format.
	 * @apiParam {string} vatNumber Valid customer VAT number.
	 * @apiParam {string} telephone Customer's telephone number.
	 * @apiParam {string} fax Customer's fax number.
	 * @apiParam {string} email Valid email address for the customer.
	 * @apiParam {string} password (Optional) Customer's password, only registree records need this value.
	 * @apiParam {string} type Customer's record type, must be either "registree" or "guest".
	 * @apiParam {int} addressId Provide a record ID if the address already exist in the database (otherwise omit this
	 *           property).
	 * @apiParam {object} address (Optional) Contains the customer's address data, can be omitted if the "addressId" is
	 *           provided.
	 * @apiParam {string} address.company Customer's company name.
	 * @apiParam {string} address.suburb Customer's suburb.
	 * @apiParam {string} address.postcode Customer's postcode.
	 * @apiParam {string} address.city Customer's city.
	 * @apiParam {int} address.countryId Must be a country ID registered in the shop database.
	 * @apiParam {int} address.zoneId The country zone ID, as registered in the shop database.
	 *
	 * @apiSuccess (Success 201 - New Customer Created) {string} Response-Body If successful, this
	 * method returns a complete Customers resource in the response body.
	 *
	 * @apiError (Error 409 - Conflict) {string} The API will return this status code if the customer's
	 * email already exists in the database (only applies on registree records).
	 */
	public function post()
	{
		$customerJson = json_decode($this->api->request->getBody());

		if(empty($customerJson))
		{
			throw new HttpApiV2Exception('Customer data were not provided.', 400);
		}

		$customerWriteService = StaticGXCoreLoader::getService('CustomerWrite');
		$customerReadService  = StaticGXCoreLoader::getService('CustomerRead');

		// Check if customer email already exists. 
		if($customerJson->email !== null && $customerJson->type === 'registree'
		   && $customerReadService->registreeEmailExists
		)
		{
			throw new HttpApiV2Exception('Registree email address already exists in the database.', 409);
		}

		$countryService = StaticGXCoreLoader::getService('Country');
		$country        = $countryService->getCountryById(MainFactory::create('Id', $customerJson->address->countryId));
		$zone           = $countryService->getCountryZoneById(MainFactory::create('Id',
		                                                                          $customerJson->address->zoneId));
		if($customerJson->addressId !== null)
		{
			$addressService = StaticGXCoreLoader::getService('AddressBook');
			$address        = $addressService->findAddressById(MainFactory::create('Id',
			                                                                       (int)$customerJson->addressId));

			$addressBlock = MainFactory::create('AddressBlock', $address->getGender(), $address->getFirstname(),
			                                    $address->getLastname(), $address->getCompany(),
			                                    $address->getB2BStatus(), $address->getStreet(), $address->getSuburb(),
			                                    $address->getPostcode(), $address->getCity(), $address->getCountry(),
			                                    $address->getCountryZone());
		}
		else
		{
			$addressBlock = MainFactory::create('AddressBlock',
			                                    MainFactory::create('CustomerGender', $customerJson->gender),
			                                    MainFactory::create('CustomerFirstname', $customerJson->firstname),
			                                    MainFactory::create('CustomerLastname', $customerJson->lastname),
			                                    MainFactory::create('CustomerCompany', $customerJson->address->company),
			                                    MainFactory::create('CustomerB2BStatus',
			                                                        $customerJson->address->b2bStatus),
			                                    MainFactory::create('CustomerStreet', $customerJson->address->street),
			                                    MainFactory::create('CustomerSuburb', $customerJson->address->suburb),
			                                    MainFactory::create('CustomerPostcode',
			                                                        $customerJson->address->postcode),
			                                    MainFactory::create('CustomerCity', $customerJson->address->city),
			                                    $country, $zone);
		}

		if($customerJson->isGuest === true)
		{
			$customer = $customerWriteService->createNewGuest(MainFactory::create('CustomerEmail',
			                                                                      $customerJson->email),
			                                                  MainFactory::create('DateTime',
			                                                                      $customerJson->dateOfBirth),
			                                                  MainFactory::create('CustomerVatNumber',
			                                                                      $customerJson->vatNumber),
			                                                  MainFactory::create('CustomerCallNumber',
			                                                                      $customerJson->telephone),
			                                                  MainFactory::create('CustomerCallNumber',
			                                                                      $customerJson->fax), $addressBlock);
		}
		else
		{
			$customer = $customerWriteService->createNewRegistree(MainFactory::create('CustomerEmail',
			                                                                          $customerJson->email),
			                                                      MainFactory::create('CustomerPassword',
			                                                                          $customerJson->password),
			                                                      MainFactory::create('DateTime',
			                                                                          $customerJson->dateOfBirth),
			                                                      MainFactory::create('CustomerVatNumber',
			                                                                          $customerJson->vatNumber),
			                                                      MainFactory::create('CustomerCallNumber',
			                                                                          $customerJson->telephone),
			                                                      MainFactory::create('CustomerCallNumber',
			                                                                          $customerJson->fax),
			                                                      $addressBlock);
		}

		$customerSerializer = MainFactory::create('CustomerJsonSerializer');
		$response           = $customerSerializer->serialize($customer, false);
		$this->_linkResponse($response);
		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /customers/:id Update Customer
	 * @apiVersion 2.0.0
	 * @apiName    UpdateCustomer
	 * @apiGroup   Customers
	 *
	 * @apiDescription
	 * This method will update the information of an existing customer record. You will
	 * need to provide all the customer information with the request (except from password
	 * and customer id). Also note that you only have to include the "addressId" property.
	 *
	 * @apiParamExample {json} Registree
	 * {
	 *   "number": "234982739",
	 *   "gender": "m",
	 *   "firstname": "John",
	 *   "lastname": "Doe",
	 *   "dateOfBirth": "1985-02-13",
	 *   "vatNumber": "0923429837942",
	 *   "vatNumberStatus": true,
	 *   "telephone": "2343948798345",
	 *   "fax": "2093049283",
	 *   "email": "customer@email.de",
	 *   "statusId": 2,
	 *   "type": "registree",
	 *   "addressId": 54
	 * }
	 *
	 * @apiParamExample {json} Guest
	 * {
	 *   "number": "234982739",
	 *   "gender": "m",
	 *   "firstname": "John",
	 *   "lastname": "Doe",
	 *   "dateOfBirth": "1985-02-13",
	 *   "vatNumber": "0923429837942",
	 *   "vatNumberStatus": true,
	 *   "telephone": "2343948798345",
	 *   "fax": "2093049283",
	 *   "email": "customer@email.de",
	 *   "statusId": 1,
	 *   "type": "guest",
	 *   "addressId": 98
	 * }
	 *
	 * @apiSuccess (Success 200 - Customer Record Updated) {string} Response-Body If successful, this
	 * method returns the updated customer resource in the response body.
	 *
	 * @apiError (Error 409 - Conflict) {string} The API will return this status code if the customer's
	 * email already exists in the database (only applies on registree records).
	 */
	public function put()
	{
		// Validate Request

		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Customer record id was not provided or is invalid: '
			                             . gettype($this->uri[1]), 400);
		}

		$customerJsonString = $this->api->request->getBody();

		if(empty($customerJsonString))
		{
			throw new HttpApiV2Exception('Customer data were not provided.', 400);
		}

		// Fetch existing customer record. 
		$customerReadService = StaticGXCoreLoader::getService('CustomerRead');
		$customers = $customerReadService->filterCustomers(array('customers_id' => (int)$this->uri[1]));
		
		if(empty($customers))
		{
			throw new HttpApiV2Exception('Customer record was not found.', 404); 
		}
		
		$customer = array_shift($customers);

		// Apply provided values into it. 
		$customerSerializer = MainFactory::create('CustomerJsonSerializer');
		$customer           = $customerSerializer->deserialize($customerJsonString, $customer);

		// Check if new email belongs to another customer.
		$query  = '
			SELECT customers_id 
			FROM customers 
			WHERE customers_email_address = "' . xtc_db_input((string)$customer->getEmail()) . '"
			AND customers_id <> "' . xtc_db_input((string)$customer->getId()) . '"
		';
		$result = xtc_db_query($query);
		if(xtc_db_num_rows($result))
		{
			throw new HttpApiV2Exception('Provided email address is used by another customer: '
			                             . (string)$customer->getEmail(), 409);
		}

		// Save record and respond to client.
		$customerWriteService = StaticGXCoreLoader::getService('CustomerWrite');
		$customerWriteService->updateCustomer($customer);
		$response = $customerSerializer->serialize($customer, false);
		$this->_linkResponse($response);
		$this->_writeResponse($response);
	}


	/**
	 * @api        {delete} /customers/:id Delete Customer
	 * @apiVersion 2.0.0
	 * @apiName    DeleteCustomer
	 * @apiGroup   Customers
	 *
	 * @apiDescription
	 * Remove a customer record from the system. This method will always return success
	 * even if the customer does not exist (due to internal CustomerService architecture
	 * decisions, which strive to avoid unnecessary failures).
	 *
	 * @apiExample {curl} Delete Customer with ID = 84
	 *             curl -X DELETE --user admin@shop.de:12345 http://shop.de/api.php/v2/customers/84
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "customerId": 84
	 * }
	 */
	public function delete()
	{
		// Check if record ID was provided.
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Customer record ID was not provided in the resource URL.', 400);
		}

		$customerId = (int)$this->uri[1];

		// Remove customer from database.
		$customerService = StaticGXCoreLoader::getService('CustomerWrite');
		$customerService->deleteCustomerById(MainFactory::create('Id', $customerId));

		// Return response JSON.
		$response = array(
				'code'       => 200,
				'status'     => 'success',
				'action'     => 'delete',
				'customerId' => $customerId
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /customers/:id Get Customers
	 * @apiVersion 2.0.0
	 * @apiName    GetCustomer
	 * @apiGroup   Customers
	 *
	 * @apiDescription
	 * Get multiple or a single customer record through the GET method. This resource supports
	 * the following GET parameters as described in the first section of documentation: sorting
	 * minimization, search, pagination and links. Additionally you can filter customers by providing
	 * the GET parameter "type=guest" or "type=registree". Sort and pagination GET parameters do not
	 * apply when a single customer record is selected (e.g. api.php/v2/customers/84).
	 *
	 * **Important**:
	 * Currently the CustomerReadService does not support searching in address information of
	 * a customer.
	 *
	 * @apiExample {curl} Get All Customers
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers
	 *
	 * @apiExample {curl} Get Customer With ID = 982
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers/982
	 *
	 * @apiExample {curl} Get Guest Customers
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers?type=guest
	 *
	 * @apiExample {curl} Search Customers
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers?q=admin@shop.de
	 *
	 * @apiExample {curl} Get Customer Addresses
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers/57/addresses
	 *
	 * @apiParam {int} id (Optional) Customer record id to be returned. If omitted all the customer records
	 * will be included in the response.
	 */
	public function get()
	{
		// Sub-Resource Customer addresses: api.php/v2/customers/:id/addresses
		if(isset($this->uri[2]) && $this->uri[2] === 'addresses')
		{
			$this->_getCustomerAddresses();

			return;
		}

		$customerService    = StaticGXCoreLoader::getService('CustomerRead');
		$customerSerializer = MainFactory::create('CustomerJsonSerializer');

		// Get Single Customer Record
		if(isset($this->uri[1]) && is_numeric($this->uri[1]))
		{
			$customers = $customerService->filterCustomers(array('customers_id' => (int)$this->uri[1]));

			if(empty($customers))
			{
				throw new HttpApiV2Exception('Customer record could not be found.', 404);
			}
		}
		// Search Customer Records
		else if($this->api->request->get('q') !== null)
		{
			$searchKey = '%' . $this->api->request->get('q') . '%';
			$search    = array(
					'customers_cid LIKE '           => $searchKey,
					'customers_vat_id LIKE '        => $searchKey,
					'customers_gender LIKE '        => $searchKey,
					'customers_firstname LIKE '     => $searchKey,
					'customers_lastname LIKE '      => $searchKey,
					'customers_dob LIKE '           => $searchKey,
					'customers_email_address LIKE ' => $searchKey,
					'customers_telephone LIKE '     => $searchKey,
					'customers_fax LIKE '           => $searchKey
			);

			$customers = $customerService->filterCustomers($search);
		}
		// Filter customers by type ("guest" or "registree")
		else if($this->api->request->get('type') !== null)
		{
			$type = $this->api->request->get('type');

			if($type === 'guest')
			{
				$customers = $customerService->filterCustomers(array('account_type' => '1'));
			}
			else if($type === 'registree')
			{
				$customers = $customerService->filterCustomers(array('account_type' => '0'));
			}
			else
			{
				throw new HttpApiV2Exception('Invalid customer type filter provided, expected "guest" or "registree" and got: '
				                             . $type);
			}
		}
		// Get all registered customer records without applying filters.
		else
		{
			$customers = $customerService->filterCustomers();
		}

		// Prepare response data. 
		$response = array();
		foreach($customers as $customer)
		{
			$response[] = $customerSerializer->serialize($customer, false);
		}

		$this->_paginateResponse($response);
		$this->_sortResponse($response);
		$this->_minimizeResponse($response);
		$this->_linkResponse($response);

		// Return single resource to client and not array.
		if(isset($this->uri[1]) && is_numeric($this->uri[1]) && count($response) > 0)
		{
			$response = $response[0];
		}

		$this->_writeResponse($response);
	}


	/**
	 * Sub-Resource Customer Addresses
	 *
	 * This method will return all the addresses of the required customer, providing a fast
	 * way to access relations between customers and addresses.
	 *
	 * @see CustomersApiV2Controller::get()
	 *
	 * @throws HttpApiV2Exception
	 */
	protected function _getCustomerAddresses()
	{
		if(!isset($this->uri[1]) && is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Invalid customer ID provided: ' . gettype($this->uri[1]), 400);
		}

		$customerReadService = StaticGXCoreLoader::getService('CustomerRead');
		$addressBookService  = StaticGXCoreLoader::getService('AddressBook');
		$addressSerializer   = MainFactory::create('AddressJsonSerializer');

		$customer  = $customerReadService->getCustomerById(MainFactory::create('Id', (int)$this->uri[1]));
		$addresses = $addressBookService->getCustomerAddresses($customer);

		$response = array();
		foreach($addresses as $address)
		{
			$response[] = $addressSerializer->serialize($address, false);
		}

		$this->_sortResponse($response);
		$this->_paginateResponse($response);
		$this->_minimizeResponse($response);
		$this->_linkResponse($response);
		$this->_writeResponse($response);
	}
}