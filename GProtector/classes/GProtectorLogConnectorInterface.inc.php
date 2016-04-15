<?php
/* --------------------------------------------------------------
  G-Protector v1.2
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  --------------------------------------------------------------
*/

interface GProtectorLogConnectorInterface
{
	public function log($p_message, $p_group, $p_filename, $p_severity, $p_error_type, $p_message_details);
}