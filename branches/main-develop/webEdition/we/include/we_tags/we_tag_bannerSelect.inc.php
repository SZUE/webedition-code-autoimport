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
function we_tag_bannerSelect(array $attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$customer = weTag_getAttribute('customer', $attribs, false, we_base_request::BOOL);
	//$rootdir = weTag_getAttribute('rootdir', $attribs, '/');
	$firstentry = weTag_getAttribute('firstentry', $attribs, '', we_base_request::STRING);
	$showpath = weTag_getAttribute('showpath', $attribs, false, we_base_request::BOOL);
	$submitonchange = weTag_getAttribute('submitonchange', $attribs, false, we_base_request::BOOL);

	$where = ' WHERE IsFolder=0 ';

	$newAttribs = removeAttribs($attribs, ['showpath', 'rootdir', 'firstentry', 'submitonchange', 'customer']);
	if($submitonchange){
		$newAttribs['onchange'] = 'we_submitForm();';
	}

	$options = ($firstentry ? getHtmlTag('option', ['value' => ''], $firstentry, true) : '');

	$GLOBALS['DB_WE']->query('SELECT ID,Text,Path,Customers FROM ' . BANNER_TABLE . ' ' . $where . ' ORDER BY Path');
	$res = $GLOBALS['DB_WE']->getAll();
	foreach($res as $record){
		if((!defined('CUSTOMER_TABLE')) || (!$customer) || ($customer && defined('CUSTOMER_TABLE') && !empty($_SESSION['webuser']['registered']) &&
			we_banner_banner::customerOwnsBanner($_SESSION['webuser']['ID'], $record['ID'], $GLOBALS['DB_WE']))){
			$rName = we_base_request::_(we_base_request::HTML, $name, $record['Path']);
			$options .= ($rName == $record['Path'] ?
					getHtmlTag('option', ['value' => $record['Path'], 'selected' => 'selected'], $showpath ? $record['Path'] : $record['Text']) :
					getHtmlTag('option', ['value' => $record['Path']], $showpath ? $record['Path'] : $record['Text']));
		}
	}

	if(isset($_REQUEST[$name])){
		$GLOBALS[$name] = we_base_request::_(we_base_request::FILE, $name);
	}
	return getHtmlTag('select', $newAttribs, $options, true);
}
