<?php
/* --------------------------------------------------------------
  G-Protector v1.2
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  --------------------------------------------------------------
*/

define('GAMBIO_PROTECTOR_DIR', dirname(__FILE__) . '/');
define('GAMBIO_PROTECTOR_CLASSES_DIR', dirname(__FILE__) . '/classes/');
define('GAMBIO_PROTECTOR_CONNECTORS_DIR', dirname(__FILE__) . '/classes/connectors/');
define('GAMBIO_PROTECTOR_FUNCTIONS_DIR', dirname(__FILE__) . '/functions/');
define('GAMBIO_PROTECTOR_FILTER_DIR', dirname(__FILE__) . '/filter/');
define('GAMBIO_PROTECTOR_LOG_DIR', dirname(dirname(__FILE__)) . '/logfiles/');
define('GAMBIO_PROTECTOR_TOKEN_DIR', dirname(dirname(__FILE__)) . '/media/');
define('GAMBIO_PROTECTOR_CACHE_DIR', dirname(dirname(__FILE__)) . '/cache/');
define('GAMBIO_PROTECTOR_BASE_DIR', dirname(dirname(__FILE__)) . '/');
define('GAMBIO_PROTECTOR_FUNCTION_PREFIX', 'gprotector_');
define('GAMBIO_PROTECTOR_TOKEN_FILE_PREFIX', 'gprotector_token_');
define('GAMBIO_PROTECTOR_FILE_PATTERN', '*.inc.php');
define('GAMBIO_PROTECTOR_GZIP_LOG', true);
define('GAMBIO_PROTECTOR_LOG_MAX_FILESIZE', 1); // megabytes
