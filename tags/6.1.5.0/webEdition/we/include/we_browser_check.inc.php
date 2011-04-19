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

function we_browser_check() {
	global $SAFARI_WYSIWYG, $BROWSER, $SYSTEM, $NET6, $FF, $IE55, $MOZ13,$SAFARI_3;
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
			break;
		case 'appleWebKit':
		case 'safari':
			$BROWSER = "SAFARI";
			$wkV=$_BROWSER->getWebKitVersion();
			$SAFARI_WYSIWYG = (($wkV > 311 && $wkV < 400) || ($wkV > 411));
      $SAFARI_3 = ($wkV > 522);
			break;
		case 'mozilla':
		case 'firefox':
		case 'nn':
			$BROWSER = ($_BROWSER->isGecko()?'NN6':'NN');
			break;
		default:
			$BROWSER = "UNKNOWN";

	}


#### Erkennung fuer Mozilla >= 1.3


	$MOZ13 = ($BROWSER == 'NN6' && $_BROWSER->getBrowserVersion()>=1.3);

#### Erkennung fuer Netscape >= 6.0

	//nobody is using netscape - this browser is really dead.
	$NET6 = false;

	$FF = ($_BROWSER->getBrowser()=='firefox'?abs($_BROWSER->getBrowserVersion()):'');
}

function checkSupportedBrowser() {
	global $SYSTEM, $BROWSER, $IE55;
	we_browser_check();

	switch ($SYSTEM) {
		case 'WIN' :
			switch ($BROWSER) {
				case 'IE':
					if ($IE55) {
						return true;
					}
					break;

				case 'OPERA':
				case 'SAFARI':
				case 'NN6':
					return true;
			}
			break;

		case 'MAC':
			switch ($BROWSER) {
				case 'OPERA':
				case 'NN6':
				case 'SAFARI':
					return true;
			}
			break;

		case 'X11':
			switch ($BROWSER) {
				case 'OPERA':
				case 'NN6':
					return true;
			}

			break;

		case 'UNKNOWN':
			switch ($BROWSER) {
				case 'IE':
					if ($IE55) {
						return true;
					}
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


if(!isset($BROWSER)||$BROWSER==''){
	we_browser_check();
}