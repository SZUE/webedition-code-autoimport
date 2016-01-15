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
function we_tag_author($attribs){
	// attributes
	$creator = weTag_getAttribute('creator', $attribs, false, we_base_request::BOOL);
	$docAttr = weTag_getAttribute('doc', $attribs, '', we_base_request::STRING);

	$author = $creator ? 'CreatorID' : 'ModifierID';

	switch($docAttr){
		case 'listview' :
			$authorID = 0;
			switch(get_class($GLOBALS['lv'])){
				case 'we_object_tag'://we:object
				case 'we_listview_object'://listview type=object
					$objID = $GLOBALS['lv']->f('WE_ID');
					break;
				case 'we_listview_search'://listview type=search
					if($GLOBALS['lv']->f('ClassID')){//object
						$objID = $GLOBALS['lv']->f('WE_ID');
					} else {//document
						$docID = $GLOBALS['lv']->f('WE_ID');
					}
					break;
				default://we_listview (document)
					$author = 'wedoc_' . $author;
					$authorID = $GLOBALS['lv']->f($author);
			}

			if(!$authorID){
				$authorID = (!empty($objID) ?
								f('SELECT ' . $author . ' FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($objID)) :
								f('SELECT ' . $author . ' FROM ' . FILE_TABLE . ' WHERE ID=' . intval($docID)));
			}

			break;
		case 'self' :
		default :
			$doc = we_getDocForTag($docAttr, true);
			$authorID = $doc->$author;
			break;
	}

	$foo = $authorID ? getHash('SELECT Username,First,Second,Address,HouseNo,City,PLZ,State,Country,Tel_preselection,Telephone,Fax_preselection,Fax,Handy,Email,Description,Salutation FROM ' . USER_TABLE . ' WHERE ID=' . intval($authorID)) : 0;
	if(!$foo){
		return '';
	}

	switch(weTag_getAttribute('type', $attribs, '', we_base_request::STRING)){
		case 'forename' :
			return trim($foo['First']);
		case 'surname' :
			return trim($foo['Second']);
		case 'name' :
			$out = trim(($foo['First'] ? ($foo['First'] . ' ') : '') . $foo['Second']);
			return $out ? : $foo['Username'];
		case 'initials' :
			$out = trim(($foo['First'] ? $foo['First']{0} : '') . ($foo['Second'] ? $foo['Second']{0} : ''));
			return $out ? : $foo['Username'];
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
