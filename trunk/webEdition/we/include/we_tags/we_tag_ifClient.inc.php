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
function we_tag_ifClient($attribs){
	$version = weTag_getAttribute('version', $attribs);
	$browser = weTag_getAttribute('browser', $attribs);
	$operator = weTag_getAttribute('operator', $attribs);
	$system = weTag_getAttribute('system', $attribs);

	$br = we_base_browserDetect::inst();
	if($browser){
		$bro = explode(',', $browser);
		$_browserOfClient = $br->getBrowser();
		$browserMatched = in_array($_browserOfClient, $bro);
		// for backwards compatibility
		if(!$browserMatched){
			$browserMatched = ((we_base_browserDetect::isNN() && in_array('mozilla', $bro)) ||
				($_browserOfClient == we_base_browserDetect::APPLE && in_array('safari', $bro)));
		}
	} else {
		$browserMatched = true;
	}

	if($browserMatched && $version){
		$brv = $br->getBrowserVersion();
		switch($operator){
			case 'equal':
				$versionMatched = (floor(floatval($brv)) == floor($version));
				break;
			case 'less':
				$versionMatched = (floatval($brv) < $version);
				break;
			case 'less|equal':
				$versionMatched = (floatval($brv) <= $version);
				break;
			case 'greater':
				$versionMatched = (floatval($brv) > $version);
				break;
			case 'greater|equal':
				$versionMatched = (floatval($brv) >= $version);
				break;
			default://old behaviour
				$versionMatched = true;
				$ver = str_replace(array('up', 'down', 'eq'), array('>=', '<', '=='), $version);

				if(strpos($ver, '==') !== false){
					eval('$versionMatched=(' . floor(floatval($brv)) . $ver . ');');
				} else {
					eval('$versionMatched=(' . $brv . $ver . ');');
				}
				break;
		}
	}

	$systemMatched = ($system && $browserMatched && $versionMatched ? in_array($br->getSystem(), explode(',', $system)) : true);

	return $browserMatched && $versionMatched && $systemMatched;
}
