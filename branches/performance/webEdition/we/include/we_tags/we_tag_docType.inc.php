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

function we_tag_docType($attribs, $content){
	$docAttr = we_getTagAttribute("doc", $attribs);
	$doctype = "";
	switch ($docAttr) {
		case "self" :
			if ($GLOBALS["we_doc"]->DocType) {
				$doctype = f(
						"SELECT DocType FROM " . DOC_TYPES_TABLE . " WHERE ID = " . escape_sql_query($GLOBALS["we_doc"]->DocType),
						"DocType",
						new DB_WE());
			}
			break;
		case "top" :
		default :
			if (isset($GLOBALS["WE_MAIN_DOC"])) {
				if ($GLOBALS["WE_MAIN_DOC"]->DocType) {
					$doctype = f(
							"SELECT DocType FROM " . DOC_TYPES_TABLE . " WHERE ID = " . escape_sql_query($GLOBALS["WE_MAIN_DOC"]->DocType),
							"DocType",
							new DB_WE());
				}
			} elseif ($GLOBALS["we_doc"]->DocType) { // if we_doc is the "top-document"
				$doctype = f(
						"SELECT DocType FROM " . DOC_TYPES_TABLE . " WHERE ID = " . escape_sql_query($GLOBALS["we_doc"]->DocType),
						"DocType",
						new DB_WE());
			}
			break;
	}
	return $doctype;
}
