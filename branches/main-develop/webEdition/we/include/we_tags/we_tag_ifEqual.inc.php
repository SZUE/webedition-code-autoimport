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
function we_tag_ifEqual($attribs){
	if(($foo = attributFehltError($attribs, "name", "ifEqual"))){
		print($foo);
		return false;
	}
	$name = weTag_getAttribute("name", $attribs);
	$eqname = weTag_getAttribute("eqname", $attribs);
	$value = weTag_getAttribute("value", $attribs);

	if(!$eqname){
		if(($foo = attributFehltError($attribs, "value", "ifEqual"))){
			print($foo);
			return false;
		}
		return ($GLOBALS['we_doc']->getElement($name) == $value);
	}

	if(($foo = attributFehltError($attribs, "eqname", "ifEqual"))){
		print($foo);
		return false;
	}
	if($GLOBALS['we_doc']->getElement($name) && $GLOBALS["WE_MAIN_DOC"]->getElement($eqname)){
		return ($GLOBALS['we_doc']->getElement($name) == $GLOBALS["WE_MAIN_DOC"]->getElement($eqname));
	} else{
		return (isset($GLOBALS[$eqname])) && ($GLOBALS[$eqname] == $GLOBALS['we_doc']->getElement($name));
	}
	return false;
}
