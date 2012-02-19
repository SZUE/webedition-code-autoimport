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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/base/we_base_browserDetect.class.php');

function we_browser_check(){
	$_SERVER["HTTP_USER_AGENT"] = (isset($_REQUEST["WE_HTTP_USER_AGENT"]) && $_REQUEST["WE_HTTP_USER_AGENT"]) ? $_REQUEST["WE_HTTP_USER_AGENT"] : (isset(
			$_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "");

	$GLOBALS['SYSTEM'] = strtoupper(we_base_browserDetect::inst()->getSystem());
	//renaming
	if($GLOBALS['SYSTEM'] == 'UNIX'){
		$GLOBALS['SYSTEM'] = 'X11';
	}

	switch(we_base_browserDetect::inst()->getBrowser()){
		case 'opera':
			$GLOBALS['BROWSER'] = 'OPERA';
			break;
		case 'ie':
			$GLOBALS['BROWSER'] = "IE";
			break;
		case 'appleWebKit':
		case 'safari':
			$GLOBALS['BROWSER'] = "SAFARI";
			break;
		case 'mozilla':
		case 'firefox':
		case 'nn':
			$GLOBALS['BROWSER'] = (we_base_browserDetect::inst()->isGecko() ? 'NN6' : 'NN');
			break;
		default:
			$GLOBALS['BROWSER'] = "UNKNOWN";
	}
}

we_browser_check();