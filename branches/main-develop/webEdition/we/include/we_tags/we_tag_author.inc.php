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

	switch($type){
		case 'forename' :
			return trim($foo['First']);
		case 'surname' :
			return trim($foo['Second']);
		case 'name' :
			$out = trim(($foo['First'] ? ($foo['First'] . ' ') : '') . $foo['Second']);
			return empty($out) ? $foo['Username'] : $out;
		case 'initials' :
			$out = trim(($foo['First'] ? $foo['First']{0} : '') . ($foo['Second'] ? $foo['Second']{0} : ''));
			return empty($out) ? $foo['Username'] : $out;
		case 'salutation':
			return trim($foo['Salutation']);
		case 'email':
			return trim($foo['Email']);
		case 'address':
			return trim(($foo['HouseNo'] ? ($foo['Address'] . ' ' . $foo['HouseNo']) : $foo['Address']));
		case 'zip':
			return trim($foo['PLZ']);
		case 'city':
			return trim($foo['City']);
		case 'state':
			return trim($foo['State']);
		case 'country':
			return trim($foo['Country']);
		case 'telephone':
			return trim(($foo['Tel_preselection'] ? ($foo['Tel_preselection'] . ' ' . $foo['Telephone']) : $foo['Telephone']));
		case 'fax':
			return trim(($foo['Fax_preselection'] ? ($foo['Fax_preselection'] . ' ' . $foo['Fax']) : $foo['Fax']));
		case 'mobile':
			return trim($foo['Handy']);
		case 'description':
			return trim($foo['Description']);
		default :
			return $foo['Username'];
	}
}