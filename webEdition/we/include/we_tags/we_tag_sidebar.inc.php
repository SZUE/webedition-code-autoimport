<?php
/**
 * webEdition CMS
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

function we_tag_sidebar($attribs, $content){
	$_out = "";

	if (we_tag('ifNotSidebar',$attribs, $content) && we_tag('ifEditmode',$attribs, $content)) {

		$id = we_getTagAttribute("id", $attribs, 0);
		$file = we_getTagAttribute("file", $attribs);
		$url = we_getTagAttribute("url", $attribs);
		$anchor = we_getTagAttribute("anchor", $attribs);
		$width = we_getTagAttribute("width", $attribs, (defined("WE_SIDEBAR_WIDTH") ? WE_SIDEBAR_WIDTH : 300));

		removeAttribs($attribs, array(
			'id', 'file', 'url', 'width', 'href'
		));

		if (trim($content) == "") {
			include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/tags.inc.php");
			$content = $GLOBALS["l_tags"]["open_sidebar"];

		}

		$href = "#";
		if ($id == 0){
			if($file != "") {
			$href = "javascript:top.weSidebar.load('" . $file . "');top.weSidebar.resize(" . $width . ");";
			} else if($url != "") {
				$href = "javascript:top.weSidebar.load('" . $url . "');top.weSidebar.resize(" . $width . ");";
			}else{
				return;
			}
		}else{
				$href = "javascript:top.weSidebar.open('" . $id . "', " . $width . ");";
		}
		$attribs['href'] = $href;

		$_out .= getHtmlTag("a", $attribs, $content);

	}
	return $_out;

}
