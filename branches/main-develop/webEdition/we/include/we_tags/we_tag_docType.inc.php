<?php
function we_tag_docType($attribs, $content){
	$docAttr = we_getTagAttribute("doc", $attribs);
	$doctype = "";
	switch ($docAttr) {
		case "self" :
			if ($GLOBALS["we_doc"]->DocType) {
				$doctype = f(
						"SELECT DocType FROM " . DOC_TYPES_TABLE . " WHERE ID = " . mysql_real_escape_string($GLOBALS["we_doc"]->DocType),
						"DocType",
						new DB_WE());
			}
			break;
		case "top" :
		default :
			if (isset($GLOBALS["WE_MAIN_DOC"])) {
				if ($GLOBALS["WE_MAIN_DOC"]->DocType) {
					$doctype = f(
							"SELECT DocType FROM " . DOC_TYPES_TABLE . " WHERE ID = " . mysql_real_escape_string($GLOBALS["WE_MAIN_DOC"]->DocType),
							"DocType",
							new DB_WE());
				}
			} elseif ($GLOBALS["we_doc"]->DocType) { // if we_doc is the "top-document"
				$doctype = f(
						"SELECT DocType FROM " . DOC_TYPES_TABLE . " WHERE ID = " . mysql_real_escape_string($GLOBALS["we_doc"]->DocType),
						"DocType",
						new DB_WE());
			}
			break;
	}
	return $doctype;
}?>
