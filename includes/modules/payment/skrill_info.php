<?php
/* --------------------------------------------------------------
   skrill_info.php 2014-04-23 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * This is a dummy module for Skrill. Its only purpose is to provide an entry point to the real configuration page for the Skrill modules.
 */
class skrill_info_ORIGIN {
	var $code, $title, $description, $enabled;

	public function __construct() {
		$this->code = 'skrill_info';
		$this->title = '<img src="'.DIR_WS_CATALOG.'images/icons/skrill/skrillfuture-logo.jpg" style="max-width:100px;">&nbsp;'.MODULE_PAYMENT_SKRILL_INFO_TEXT_TITLE;
		$styles = '<style>';
		$styles .= 'td.infoBoxContent a.button { display: none; }';
		$styles .= 'a.skrill_cfg { display: block; width: 10em; margin: 0 auto; padding: 10px; background: #fff; ';
		$styles .= 'color: #852064; text-align: center; font-size: 1.2em; font-weight: bold; text-transform: uppercase; border-radius: 1em; box-shadow: 0 0 3px #852064; }';
		$styles .= '</style>';
		$config_url = DIR_WS_ADMIN.'configuration.php?gID=32';
		$this->description = MODULE_PAYMENT_SKRILL_INFO_TEXT_DESCRIPTION.$styles;
		$this->description = strtr($this->description, array(
			':config_url' => $config_url,
			':logo_url' => DIR_WS_CATALOG.'images/icons/skrill/skrillfuture-logo.jpg',
		));
	}

	function check() {
		return false;
	}

	function keys() {
		return array();
	}

	function install() { }
	function remove() { }

}

MainFactory::load_origin_class('skrill_info');