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
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_browserDetect.inc.php');

function we_browser_check() {
	$GLOBALS['SAFARI_WYSIWYG'] = false;

	$_SERVER["HTTP_USER_AGENT"] = (isset($_REQUEST["WE_HTTP_USER_AGENT"]) && $_REQUEST["WE_HTTP_USER_AGENT"]) ? $_REQUEST["WE_HTTP_USER_AGENT"] : (isset(
									$_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "");

	$_BROWSER = new we_browserDetect();

	$GLOBALS['SYSTEM'] = strtoupper($_BROWSER->getSystem());
	//renaming
	if($GLOBALS['SYSTEM']=='UNIX'){
		$GLOBALS['SYSTEM']='X11';
	}

	switch($_BROWSER->getBrowser()){
		case 'opera':
			$GLOBALS['BROWSER'] = 'OPERA';
			break;
		case 'ie':
			$GLOBALS['BROWSER'] = "IE";
			break;
		case 'appleWebKit':
		case 'safari':
			$GLOBALS['BROWSER'] = "SAFARI";
			$wkV=$_BROWSER->getWebKitVersion();
			$GLOBALS['SAFARI_WYSIWYG'] = (($wkV > 311 && $wkV < 400) || ($wkV > 411));
      $GLOBALS['SAFARI_3'] = ($wkV > 522);
			break;
		case 'mozilla':
		case 'firefox':
		case 'nn':
			$GLOBALS['BROWSER'] = ($_BROWSER->isGecko()?'NN6':'NN');
			break;
		default:
			$GLOBALS['BROWSER'] = "UNKNOWN";

	}


#### Erkennung fuer Netscape >= 6.0

	$GLOBALS['FF'] = ($_BROWSER->getBrowser()=='firefox'?abs($_BROWSER->getBrowserVersion()):'');
}

function checkSupportedBrowser() {
	we_browser_check();

	switch ($GLOBALS['SYSTEM']) {
		case 'WIN' :
			switch ($GLOBALS['BROWSER']) {
				case 'IE':
					return true;
					break;

				case 'OPERA':
				case 'SAFARI':
				case 'NN6':
					return true;
			}
			break;

		case 'MAC':
			switch ($GLOBALS['BROWSER']) {
				case 'OPERA':
				case 'NN6':
				case 'SAFARI':
					return true;
			}
			break;

		case 'X11':
			switch ($GLOBALS['BROWSER']) {
				case 'OPERA':
				case 'NN6':
					return true;
			}

			break;

		case 'UNKNOWN':
			switch ($GLOBALS['BROWSER']) {
				case 'IE':
					return true;
					break;

				case 'OPERA':
				case 'NN6':
				case 'SAFARI':
					return true;
			}

			break;
	}

	return false;
}


if(!isset($GLOBALS['BROWSER'])||$GLOBALS['BROWSER']==''){
	we_browser_check();
}