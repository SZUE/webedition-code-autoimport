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
function we_tag_ifClient(array $attribs){
	$br = we_base_browserDetect::inst();

	if(($browser = weTag_getAttribute('browser', $attribs, '', we_base_request::STRING))){
		$bro = explode(',', $browser);
		$_browserOfClient = $br->getBrowser();
		$browserMatched = in_array($_browserOfClient, $bro);
		// for backwards compatibility
		if(!$browserMatched){
			$browserMatched = ((we_base_browserDetect::isNN() && in_array('mozilla', $bro)) ||
				($_browserOfClient == we_base_browserDetect::APPLE && in_array('safari', $bro)));
		}
		if(!$browserMatched){
			return false;
		}
	} else {
		$browserMatched = true;
	}

	if(($version = weTag_getAttribute('version', $attribs, '', we_base_request::FLOAT))){
		$brv = $br->getBrowserVersion();
		switch(weTag_getAttribute('operator', $attribs, '', we_base_request::STRING)){
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
				//FIXME: add to deprecated, remove in 7.0.1
				$versionMatched = true;
				$ver = str_replace(array('up', 'down', 'eq'), array('>=', '<', '=='), $version);

				if(strpos($ver, '==') !== false){
					eval('$versionMatched=(' . floor(floatval($brv)) . $ver . ');');
				} else {
					eval('$versionMatched=(' . floatval($brv) . $ver . ');');
				}
				break;
		}
		if(!$versionMatched){
			return false;
		}
	} else {
		$versionMatched = true;
	}

	$system = weTag_getAttribute('system', $attribs, '', we_base_request::STRING);

	$systemMatched = ($system && $browserMatched && $versionMatched ? in_array($br->getSystem(), explode(',', $system)) : true);

	return $browserMatched && $versionMatched && $systemMatched;
}
