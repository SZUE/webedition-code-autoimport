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
function we_tag_author($attribs){
	// attributes
	$type = weTag_getAttribute('type', $attribs);
	$creator = weTag_getAttribute('creator', $attribs, false, true);
	$docAttr = weTag_getAttribute('doc', $attribs);

	$doc = we_getDocForTag($docAttr, true);

	$foo = getHash('SELECT Username,First,Second,Address,HouseNo,City,PLZ,State,Country,Tel_preselection,Telephone,Fax_preselection,Fax,Handy,Email,Description,Salutation FROM ' . USER_TABLE . ' WHERE ID=' . intval($creator ? $doc->CreatorID : $doc->ModifierID), $GLOBALS['DB_WE']);

	$out = "";
	switch($type){
		case 'forename' :
			$out = trim($foo['First']);
			return $out;
		case 'surname' :
			$out = trim($foo['Second']);
			return $out;
		case 'name' :
			$out = trim(($foo['First'] ? ($foo['First'] . ' ') : '') . $foo['Second']);
			if(!$out){
				$out = $foo['Username'];
			}
			return $out;
		case 'initials' :
			$out = trim(($foo['First'] ? substr($foo['First'], 0, 1) : '') . ($foo['Second'] ? substr($foo['Second'], 0, 1) : ''));
			if(!$out){
				$out = $foo['Username'];
			}
			return $out;
		case 'salutation':
			$out = trim($foo['Salutation']);
			return $out;
		case 'email':
			$out = trim($foo['Email']);
			return $out;
		case 'address':
			$out = trim(($foo['HouseNo'] ? ($foo['Address'] . ' ' . $foo['HouseNo']) : $foo['Address']));
			return $out;
		case 'zip':
			$out = trim($foo['PLZ']);
			return $out;
		case 'city':
			$out = trim($foo['City']);
			return $out;
		case 'state':
			$out = trim($foo['State']);
			return $out;
		case 'country':
			$out = trim($foo['Country']);
			return $out;
		case 'telephone':
			$out = trim(($foo['Tel_preselection'] ? ($foo['Tel_preselection'] . ' ' . $foo['Telephone']) : $foo['Telephone']));
			return $out;
		case 'fax':
			$out = trim(($foo['Fax_preselection'] ? ($foo['Fax_preselection'] . ' ' . $foo['Fax']) : $foo['Fax']));
			return $out;
		case 'mobile':
			$out = trim($foo['Handy']);
			return $out;
		case 'description':
			$out = trim($foo['Description']);
			return $out;
		default :
			return $foo['Username'];
	}
}