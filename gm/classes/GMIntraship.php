<?php
/* --------------------------------------------------------------
	GMIntraship.php 2015-05-20 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class GMIntraship_ORIGIN {
	protected $_config;
	protected $_text;
	protected $_logger;

	public $module_version = '2015-07-13';

	const CONFIG_PREFIX = 'INTRASHIP';
	const NUM_ZONES = 4;
	const DEVID = 'capuno';
	const DEVPWD = '';
	const APPID = 'gambio_1';
	const APPToken = '7692819c896fe2ea5fbd861cead0a08c503b69e3d56b77380a42b02aa9';
	const POSTFINDER_API_KEY_SANDBOX = 'uceegajeephai';
	const POSTFINDER_API_KEY = 'quaBooGhighai';

	public function __construct() {
		$this->_logger = new FileLog('intraship', true);
		$this->_config = array(
			'active' => 0,
			'debug' => 0,
			'send_email' => 0,
			'send_announcement' => 1, // "Paketankündigung" via ShipmentOrder->Shipment->Receiver->Communication->email
			'ekp' => '',
			'user' => '',
			'password' => '',
			'shipper_name' => '',
			'shipper_street' => '',
			'shipper_house' => '',
			'shipper_postcode' => '',
			'shipper_city' => '',
			'shipper_contact' => '',
			'shipper_email' => '',
			'shipper_phone' => '',
			'cod_account_holder' => '',
			'cod_account_number' => '',
			'cod_bank_number' => '',
			'cod_bank_name' => '',
			'cod_iban' => '',
			'cod_bic' => '',
			'status_id_sent' => 0,
			'status_id_storno' => 0,
			'zone_1_countries' => 'DE',
			'zone_1_product' => 'EPN',
			'zone_1_partner_id' => '01',
			'zone_2_countries' => 'AT',
			'zone_2_product' => 'BPI',
			'zone_2_partner_id' => '01',
			'zone_3_countries' => 'BE,CZ,DK,EE,FI,FR,GR,HU,IE,IT,LU,MC,NL,PL,PT,SK,SI,ES,SE,GB',
			'zone_3_product' => 'BPI',
			'zone_3_partner_id' => '01',
			'zone_4_countries' => 'AF,EG,AL,DZ,AD,AO,AI,AQ,AG,GQ,AR,AM,AW,AZ,ET,AU,BS,BH,BD,BB,BY,BZ,BJ,BM,BT,BO,BA,BW,BV,BR,IO,BN,BF,BI,CT,XC,CL,CN,CK,CR,CI,DM,DO,DJ,EC,SV,ER,FO,FK,FJ,TF,GF,PF,FQ,GA,GM,GE,GH,GI,GD,GL,GP,GU,GT,GG,GN,GW,GW,GY,HT,HT,HM,HN,HK,IN,ID,IQ,IR,IS,IL,JM,JP,YE,JE,JT,JO,VG,VI,KY,KH,CM,CA,IC,CV,KZ,QA,KE,KG,KI,CC,CO,CO,KM,CG,CD,KP,KR,HR,CU,KW,LA,LS,LB,LR,LY,LI,MO,MG,MW,MY,MV,ML,IM,MP,MA,MH,MQ,MR,MU,YT,MK,XL,MX,MI,FM,MD,MC,MN,ME,MS,MZ,MM,NA,NR,NP,NC,NZ,NI,AN,NE,NG,NU,NF,NO,OM,PK,PW,PS,PA,PZ,PG,PY,PU,PE,PH,PN,PR,RE,RW,RU,SB,ZM,AS,WS,SM,ST,SA,CH,SN,RS,SC,SL,ZW,SG,SO,SJ,LK,VC,SH,KN,LC,PM,SD,SR,SZ,SY,ZA,GS,TJ,TW,TZ,TH,TL,TG,TK,TO,TT,TD,TN,TM,TC,TV,TR,UM,US,UG',
			'zone_4_product' => 'BPI',
			'zone_4_partner_id' => '01',
			'bpi_use_premium' => 0,
			'use_postfinder' => 0,
			'parcelservice_id' => 0,
		);
		$this->_loadConfig();
		if($this->_config['debug'] == true) {
			$this->log("Configuration:\n".print_r($this->_config, true));
		}
		$this-> _text = new LanguageTextManager('intraship', $_SESSION['languages_id']);
	}

	public function get_text($key) {
		return $this->_text->get_text($key);
	}

	public function log($text) {
		$time = microtime(true);
		$ts = sprintf('%s.%03d | ', date('Y-m-d H:i:s', floor($time)), ($time - floor($time)));
		$this->_logger->write($ts.$text.PHP_EOL);
	}

	protected function _loadConfig() {
		foreach($this->_config as $key => $value) {
			$db_key = self::CONFIG_PREFIX .'_'. strtoupper($key);
			$db_value = gm_get_conf($db_key);
			if(!($db_value === false || $db_value === null)) {
				$this->_config[$key] = $db_value;
			}
		}
	}

	public function saveConfig() {
		foreach($this->_config as $key => $value) {
			$db_key = self::CONFIG_PREFIX .'_'. strtoupper($key);
			$value = xtc_db_input($value);
			gm_set_conf($db_key, $value);
		}
	}

	public function __get($name) {
		if(array_key_exists($name, $this->_config)) {
			return $this->_config[$name];
		}
		return null;
	}

	public function __set($name, $value) {
		if(array_key_exists($name, $this->_config)) {
			$this->_config[$name] = trim($value);
		}
	}

	public function isPostfinderActive() {
		$t_use_postfinder = $this->use_postfinder == true;
		return $t_use_postfinder;
	}

	public function getLabelURL($orders_id) {
		$label_query = "SELECT label_url FROM orders_intraship_labels WHERE orders_id = ".(int)$orders_id;
		$label_result = xtc_db_query($label_query);
		if(xtc_db_num_rows($label_result) > 0) {
			$row = xtc_db_fetch_array($label_result);
			$label_url = $row['label_url'];
		}
		else {
			$label_url  = '';
		}
		return $label_url;
	}

	public function getWSDLLocation() {
		$dhlwsdlurl = 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/1.0/geschaeftskundenversand-api-1.0.wsdl';
		return $dhlwsdlurl;
	}

	public function getWebserviceEndpoint() {
		if($this->debug == true) {
			$endpoint = 'https://cig.dhl.de/services/sandbox/soap';
		}
		else {
			$endpoint = 'https://cig.dhl.de/services/production/soap';
		}

		return $endpoint;
	}

	public function getWebserviceCredentials() {
		$credentials = new stdClass();
		if($this->debug == true) {
			$credentials->user = 'gambio_1';
			$credentials->password = '7692819c896fe2ea5fbd861cead0a08c503b69e3d56b77380a42b02aa9';
		}
		else {
			$credentials->user = 'gambio_1';
			$credentials->password = '7692819c896fe2ea5fbd861cead0a08c503b69e3d56b77380a42b02aa9';
		}
		return $credentials;
	}

	public function getIntrashipPortalURL() {
		if($this->debug == true) {
			$dhlintrashipurl='https://test-intraship.dhl.com/intraship.57/jsp/Login_WS.jsp';
		}
		else {
			$dhlintrashipurl = 'https://www.intraship.de/intraship/jsp/Login_WS.jsp';
		}
		return $dhlintrashipurl;
	}

	public function getProductCode($iso2) {
		$iso2 = strtoupper(trim($iso2));
		$product_code = false;
		for($zone = 1; $zone <= self::NUM_ZONES; $zone++) {
			$countries_config = 'zone_'.$zone.'_countries';
			$countries_list = $this->$countries_config;
			$countries = explode(',', $countries_list);
			if(in_array($iso2, $countries)) {
				$product_code_config = 'zone_'.$zone.'_product';
				$product_code = $this->$product_code_config;
				break;
			}
		}
		return $product_code;
	}

	public function getPartnerID($iso2) {
		$iso2 = strtoupper(trim($iso2));
		$partner_id = false;
		for($zone = 1; $zone <= self::NUM_ZONES; $zone++) {
			$countries_config = 'zone_'.$zone.'_countries';
			$countries_list = $this->$countries_config;
			$countries = explode(',', $countries_list);
			if(in_array($iso2, $countries)) {
				$partner_id_config = 'zone_'.$zone.'_partner_id';
				$partner_id = $this->$partner_id_config;
				break;
			}
		}
		return $partner_id;
	}

	public function storeTrackingNumber($orders_id, $trackingNumber)
	{
		if((int)$this->_config['parcelservice_id'] > 0)
		{
			$parcelServiceReader = MainFactory::create('ParcelServiceReader');
			$parcelTrackingCodeWriter = MainFactory::create('ParcelTrackingCodeWriter');
			$parcelTrackingCodeWriter->insertTrackingCode($orders_id, $trackingNumber, $this->_config['parcelservice_id'], $parcelServiceReader);
		}
	}

	public function findPackstations($street = '', $streetno = '', $zip = '', $city = '', $include_branches = false) {
		if($include_branches == true) {
			$this->log("finding packstations and branches for $street $streetno, $zip $city");
		}
		else {
			$this->log("finding packstations for $street $streetno, $zip $city");
		}
		$options = array(
			'soap_version' => SOAP_1_1,
			'encoding' => 'UTF-8',
			'trace' => 1,
		);
		if($this->debug == true) {
			$service_url = 'http://post.doubleslash.de/webservice/?wsdl';
			$t_api_key = self::POSTFINDER_API_KEY_SANDBOX;
		}
		else {
			$service_url = 'http://standorte.deutschepost.de/webservice/?wsdl';
			$t_api_key = self::POSTFINDER_API_KEY;
		}
		$client = new SoapClient($service_url, $options);
		$params = array(
			'address' => array(
				'street' => $street,
				'streetNo' => $streetno,
				'zip' => $zip,
				'city' => $city,
			),
			'key' => $t_api_key,
		);
		try {
			if($include_branches == true) {
				$response = $client->getPackstationsFilialeDirektByAddress($params);
			}
			else {
				$response = $client->getPackstationsByAddress($params);
			}
		}
		catch(SoapFault $sf) {
			/*
			header('Content-Type: text/plain');
			die(print_r($sf, true));
			*/
			$this->log('ERROR: '.$sf->getMessage());
			$response = false;
		}
		return $response;
	}

	public function findBranches($street = '', $streetno = '', $zip = '', $city = '') {
		$this->log("finding branches for $street $streetno, $zip $city");
		$options = array(
			'soap_version' => SOAP_1_1,
			'encoding' => 'UTF-8',
			'trace' => 1,
		);
		if($this->debug == true) {
			$service_url = 'http://post.doubleslash.de/webservice/?wsdl';
			$t_api_key = self::POSTFINDER_API_KEY_SANDBOX;
		}
		else {
			$service_url = 'http://standorte.deutschepost.de/webservice/?wsdl';
			$t_api_key = self::POSTFINDER_API_KEY;
		}
		$client = new SoapClient($service_url, $options);
		$params = array(
			'address' => array(
				'street' => $street,
				'streetNo' => $streetno,
				'zip' => $zip,
				'city' => $city,
			),
			'key' => $t_api_key,
		);
		try {
			$response = $client->getBranchesByAddress($params);
		}
		catch(SoapFault $sf) {
			/*
			header('Content-Type: text/plain');
			die(print_r($sf, true));
			*/
			$this->log('ERROR: '.$sf->getMessage());
			$response = false;
		}
		return $response;
	}

	public function isPackstationAddress($address_book_id) {
		$query = "SELECT * FROM address_book WHERE address_book_id = :ab_id";
		$query = strtr($query, array(':ab_id' => (int)$address_book_id));
		$this->log($query);
		$result = xtc_db_query($query, 'db_link', false);
		if(xtc_db_num_rows($result) == 0) {
			return false;
		}
		$row = xtc_db_fetch_array($result);
		if($row === false) {
			$this->log("address_book_id invalid");
			return false;
		}
		$this->log("checking this entry:\n".print_r($row, true));
		if(strtolower($row['address_class']) == 'packstation') {
			return true;
		}
		if(strtolower($row['address_class']) == 'postfiliale') {
			return true;
		}
		if(preg_match('/.*(packstation|postfiliale).*/i', $row['entry_street_address'].$row['entry_company']) == 1) {
			return true;
		}
		return false;
	}

	public function isValidPostnummer($postnum) {
		$postnum = sprintf('%010d', $postnum);
		$sum1 = 0;
		for($i = 8; $i >= 0; $i -= 2) {
			$sum1 += $postnum[$i];
		}
		$sum2 = 0;
		for($j = 7; $j >= 1; $j -= 2) {
			$sum2 += $postnum[$j];
		}
		$sum12 = ($sum1 * 4) + ($sum2 * 9);
		$checknum = (10 - ($sum12 % 10)) % 10;
		$is_valid = $postnum[9] == $checknum;
		return $is_valid;
	}
}
MainFactory::load_origin_class('GMIntraship');