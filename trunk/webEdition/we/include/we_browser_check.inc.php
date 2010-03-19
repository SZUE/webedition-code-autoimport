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

$SAFARI_WYSIWYG = false;
$SAFARI_3 = false;

$_SERVER["HTTP_USER_AGENT"] = (isset($_REQUEST["WE_HTTP_USER_AGENT"]) && $_REQUEST["WE_HTTP_USER_AGENT"]) ? $_REQUEST["WE_HTTP_USER_AGENT"] : (isset(
		$_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "");

if (preg_match('/(ozilla.[23]|MSIE.3)/i', $_SERVER['HTTP_USER_AGENT'])) {
	$BROWSER3 = true;
}
if (stristr($_SERVER['HTTP_USER_AGENT'], 'safari')) {
	$BROWSER = "SAFARI";
	if (eregi('AppleWebKit/([^ ]+)', $_SERVER["HTTP_USER_AGENT"], $regs)) {
		$v = $regs[1];
		if ((abs($v) > 311 && abs($v) < 400) || (abs($v) > 411)) {
			$SAFARI_WYSIWYG = true;
		}
		if (abs($v) > 522) {
			$SAFARI_3 = true;
		}
	}
} else 
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'opera')) {
		$BROWSER = "OPERA";
	} else 
		if (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
			$BROWSER = "IE";
		} else 
			if (stristr($_SERVER['HTTP_USER_AGENT'], 'mozilla')) {
				$BROWSER = "NN";
				if (stristr($_SERVER['HTTP_USER_AGENT'], 'gecko')) {
					$BROWSER = "NN6";
				}
			} else {
				$BROWSER = "UNKNOWN";
			}
$OSX = false;
if (stristr($_SERVER['HTTP_USER_AGENT'], 'X11')) {
	$SYSTEM = "X11";
} else 
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Win')) {
		$SYSTEM = "WIN";
	} else 
		if (stristr($_SERVER['HTTP_USER_AGENT'], 'Mac')) {
			$SYSTEM = "MAC";
			if (stristr($_SERVER['HTTP_USER_AGENT'], 'os x')) {
				$OSX = true;
			}
		} else {
			$SYSTEM = "UNKNOWN";
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


$MOZ13 = false;

if (($BROWSER == "NN6")) {
	if (preg_match('/.*; ?rv:([\d.]+).*/', $_SERVER['HTTP_USER_AGENT'], $regs)) {
		if (abs($regs[1]) >= 1.3) {
			$MOZ13 = true;
		}
	}
}

#### Erkennung fuer Netscape >= 6.0


$NET6 = false;
$FF = ""; 
if (($BROWSER == "NN6")) {
	if (stristr($_SERVER['HTTP_USER_AGENT'], 'Netscape')) {
		$NET6 = true;
	} elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
		$BROWSERVERSION = substr(strstr($_SERVER["HTTP_USER_AGENT"], "Firefox/"),8);
		$_bvArray=explode(".",$BROWSERVERSION);
		$FF = $_bvArray[0];		
	}
}

#### Erkennung fuer ActiveX kompatible Mozilla Browser


$MOZ_AX = false;

?>