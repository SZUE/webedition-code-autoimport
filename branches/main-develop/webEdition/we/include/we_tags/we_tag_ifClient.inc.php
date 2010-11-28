<?php
function we_tag_ifClient($attribs, $content){
	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we_browserDetect.inc.php");

	$version = we_getTagAttribute("version", $attribs);
	$browser = we_getTagAttribute("browser", $attribs);
	$system = we_getTagAttribute("system", $attribs);

	if ($version) {
		if (!(ereg('up[0-9\.]+', $version) || ereg('down[0-9\.]+', $version) || ereg('eq[0-9\.]+', $version))) {
			exit(parseError($GLOBALS["l_parser"]["client_version"]));
		}
	}

	$br = new we_browserDetect();
	if ($browser) {
		$bro = explode(",", $browser);
		$_browserOfClient = $br->getBrowser();
		$foo_br = in_array($_browserOfClient, $bro);
		// for backwards compatibility
		if (!$foo_br && $_browserOfClient == "firefox" && in_array("mozilla", $bro)) {
			$foo_br = true;
		} else
			if (!$foo_br && $_browserOfClient == "appleWebKit" && in_array("safari", $bro)) {
				$foo_br = true;
			}
	} else {
		$foo_br = true;
	}

	$brv = $br->getBrowserVersion();
	$foo_v = true;
	$ver = str_replace('up', '>=', $version);
	$ver = str_replace('down', '<', $ver);
	$ver = str_replace('eq', '==', $ver);

	if (ereg("==", $ver)) {
		eval('$foo_v = (floor($brv)' . $ver . ');');
	} else {
		eval('$foo_v = ($brv' . $ver . ');');
	}
	if ($system) {
		$sys = explode(",", $system);
		$foo_sys = in_array($br->getSystem(), $sys);
	} else {
		$foo_sys = true;
	}
	return $foo_br && $foo_v && $foo_sys;
}?>
