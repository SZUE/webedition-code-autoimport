<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class rpcTriggerJSErrorCmd extends we_rpc_cmd{

	function execute(){
		if(isset($_REQUEST['we_cmd']) && function_exists('log_error_message')){
			$br = we_base_browserDetect::inst();
			$line = empty($_REQUEST['we_cmd']['line']) ? 0 : $_REQUEST['we_cmd']['line'];

			if($br->getBrowser() == we_base_browserDetect::IE && $line == 111){
				//bad ie, don't log this anymore
				return;
			}
			$file = empty($_REQUEST['we_cmd']['file']) ? '' : $_REQUEST['we_cmd']['file'];
			$errobj = empty($_REQUEST['we_cmd']['errObj']) ? true : $_REQUEST['we_cmd']['errObj'];
			unset($_REQUEST['we_cmd']['file'], $_REQUEST['we_cmd']['line'], $_REQUEST['we_cmd']['errObj']);

			$_REQUEST['we_cmd']['detected'] = array(
				'Browser' => $br->getBrowser() . ' ' . $br->getBrowserVersion(),
				'System' => $br->getSystem(),
			);
			$data = str_replace($_SERVER['SERVER_NAME'], 'HOST', print_r($_REQUEST['we_cmd'], true));
			unset($_REQUEST);
			log_error_message(E_JS, $data, $file, $line, $errobj);
		}
	}

}
