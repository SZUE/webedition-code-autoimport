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

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_tagParser.inc.php");

function we_tag_sessionLogout($attribs,$content){
	if(($foo = attributFehltError($attribs,"id","sessionLogout"))) return $foo;

	$id = ($id == "self") ? $GLOBALS["WE_MAIN_DOC"]->ID : we_getTagAttribute("id",$attribs);
	$row = getHash("SELECT Path,IsFolder,IsDynamic FROM " . FILE_TABLE . " WHERE ID=".abs($id)."",new DB_WE);

	$url = (!empty($row))?$row["Path"].($row["IsFolder"] ? '/' : '') : '';

	$attr = we_make_attribs($attribs,'id');

	//  then lets parse the content
    $tp = new we_tagParser();
    $tags = $tp->getAllTags($content);
    $tp->parseTags($tags,$content);

	return '<a href="'.$url.'?we_webUser_logout=1" '.($attr ? $attr : '').'>'.$content.'</a>';
}