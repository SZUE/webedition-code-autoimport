<?php

/**
 * webEdition CMS
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

function we_broser_check() {
	global $SAFARI_WYSIWYG, $BROWSER, $SYSTEM, $NET6, $FF, $MOZ13;
	$SAFARI_WYSIWYG = false;

	$_SERVER["HTTP_USER_AGENT"] = (isset($_REQUEST["WE_HTTP_USER_AGENT"]) && $_REQUEST["WE_HTTP_USER_AGENT"]) ? $_REQUEST["WE_HTTP_USER_AGENT"] : (isset(
									$_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "");

	$_BROWSER = new we_browserDetect();

	$SYSTEM = strtoupper($_BROWSER->getSystem());
	//renaming
	if($SYSTEM=='UNIX'){
		$SYSTEM='X11';
	}

	switch($_BROWSER->getBrowser()){
		case 'opera':
			$BROWSER = 'OPERA';
			break;
		case 'ie':
			$BROWSER = "IE";
			$IE55 =($_BROWSER->getBrowserVersion()>=5.5);
			$IE4 = ($_BROWSER->getBrowserVersion()<5);
			break;
		case 'appleWebKit':
		case 'safari':
			$BROWSER = "SAFARI";
			$wkV=$_BROWSER->getWebKitVersion();
			$SAFARI_WYSIWYG = (($wkV > 311 && $wkV < 400) || ($wkV > 411));
			break;
		case 'mozilla':
		case 'firefox':
		case 'nn':
			$BROWSER = ($_BROWSER->isGecko()?'NN6':'NN');
			break;
		default:
			$BROWSER = "UNKNOWN";

	}


	if (($BROWSER == "IE") && ($SYSTEM == "WIN")) {
		$foo = explode(";", $_SERVER["HTTP_USER_AGENT"]);
		$foo = abs(preg_replace('/[^\d.]/', '', $foo[1]));
		if ($foo >= 5.5) {
			$IE55 = true;
		}
		if ($foo < 5) {
			$IE4 = true;
		}
	}

#### Erkennung fuer Mozilla >= 1.3


	$MOZ13 = ($BROWSER == 'NN6' && $_BROWSER->getBrowserVersion()>=1.3);

#### Erkennung fuer Netscape >= 6.0

	//nobody is using netscape - this browser is really dead.
	$NET6 = false;

	$FF = ($_BROWSER->getBrowser()=='firefox'?abs($_BROWSER->getBrowserVersion()):'');
}

if(!isset($BROWSER)){
	we_broser_check();
}