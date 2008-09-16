<?php
/**
 * webEdition CMS
 *
 * LICENSETEXT_CMS
 *
 *
 * @category   webEdition
 * @package    webEdition_base
 * @copyright  Copyright (c) 2008 living-e AG (http://www.living-e.com)
 * @license    http://www.living-e.de/licence     LICENSETEXT_CMS  TODO insert license type and url
 */

define("WE_LOGGING_VERSIONS_DELETE", 1);
define("WE_LOGGING_VERSIONS_RESET", 2);
define("WE_LOGGING_VERSIONS_PREFS", 3);

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_logging/logging.class.php");


class versionsLog extends logging{
	
	
	public $action;
	public $data;
	
	
	function __construct() {
		
		$this->table = VERSIONS_TABLE_LOG;
		parent::__construct();
		
	}
	
	function saveVersionsLog($logArray, $action=""){
		
		$this->action = $action;
		$this->data = serialize($logArray);
		
		$this->saveLog();

	}
	
	function __destruct() {
		
		parent::__destruct();
		
	}
	
}

?>