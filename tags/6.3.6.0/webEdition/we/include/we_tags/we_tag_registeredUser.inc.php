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
function we_tag_registeredUser($attribs){

	$id = weTag_getAttribute("id", $attribs);
	$show = weTag_getAttribute("show", $attribs);
	$docAttr = weTag_getAttribute("doc", $attribs);
	$regs = array();
	if(preg_match('|^field:(.+)$|', $id, $regs)){
		$doc = we_getDocForTag($docAttr);
		$field = $regs[1];
		if(strlen($field))
			$id = $doc->getElement($field);
	}
	if($id){
		$h = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($id), $GLOBALS['DB_WE']);
		unset($h['Password']);
		if($show){
			$foo = array();
			preg_match_all("|%([^ ]+) ?|i", $show, $foo, PREG_SET_ORDER);
			foreach($foo as $f){
				$show = str_replace("%" . $f[1], $h[$f[1]], $show);
			}
			return $show;
		} else{
			return $h["Username"];
		}
	}
	return '';
}
