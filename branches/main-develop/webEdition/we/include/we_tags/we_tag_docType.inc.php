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
function we_tag_docType($attribs){
	switch($weTag_getAttribute("doc", $attribs, '', we_base_request::STRING)){
		case "self" :
			if($GLOBALS['we_doc']->DocType){
				return f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID = ' . $GLOBALS['DB_WE']->escape($GLOBALS['we_doc']->DocType), "DocType", $GLOBALS['DB_WE']);
			}
			break;
		case "top" :
		default :
			if(isset($GLOBALS["WE_MAIN_DOC"])){
				if($GLOBALS["WE_MAIN_DOC"]->DocType){
					return f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID = ' . $GLOBALS['DB_WE']->escape($GLOBALS["WE_MAIN_DOC"]->DocType), 'DocType', $GLOBALS['DB_WE']);
				}
			} elseif($GLOBALS['we_doc']->DocType){ // if we_doc is the "top-document"
				return f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID = ' . $GLOBALS['DB_WE']->escape($GLOBALS['we_doc']->DocType), 'DocType', $GLOBALS['DB_WE']);
			}
			break;
	}
	return '';
}
