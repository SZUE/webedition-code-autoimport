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

function we_tag_printVersion($attribs, $content){
	$foo = attributFehltError($attribs, "tid", "printVersion");
	if ($foo)
		return $foo;

	$tid = we_getTagAttribute("tid", $attribs);
	$triggerID = we_getTagAttribute("triggerID", $attribs); // :ATTENTION: difference between tag wizzard and program
	$triggerID = $triggerID ? $triggerID : we_getTagAttribute("triggerid", $attribs);

	$docAttr = we_getTagAttribute("doc", $attribs);
	if (!$docAttr) {
		$docAttr = we_getTagAttribute("type", $attribs);
	}

	$link = isset($attribs["Link"]) ? $attribs["Link"] : "";
	if (!$link) {
		$link = isset($attribs["link"]) ? $attribs["link"] : "";
	}

	$doc = we_getDocForTag($docAttr);

	$id = isset($doc->OF_ID) ? $doc->OF_ID : $doc->ID;

	$_query_string = "";

	$hideQuery = array(
		"we_objectID", "tid", "id", "pv_tid", "pv_id", "we_cmd", "responseText", "we_mode", "btype"
	);
	if (isset($_SESSION)) {
		array_push($hideQuery, session_name());
	}
	if (isset($_POST)) {
		foreach ($_POST as $k => $v) {
			if ((!is_array($v)) && (!in_array($k, $hideQuery))) {
				$_query_string .= "&" . rawurlencode($k) . "=" . rawurlencode($v);
			}
		}
	}
	if (isset($_GET)) {
		foreach ($_GET as $k => $v) {
			if ((!is_array($v)) && (!in_array($k, $hideQuery))) {
				$_query_string .= "&" . rawurlencode($k) . "=" . rawurlencode($v);
			}
		}
	}
	if ($_query_string) {
		$_query_string = htmlspecialchars($_query_string);
	}

	if (isset($doc->TableID)) {
		if ($triggerID) {
			$url = id_to_path($triggerID) . "?we_objectID=$id&amp;tid=$tid" . $_query_string;
		} else {
			$url = "/webEdition/we_cmd.php?we_cmd[0]=preview_objectFile&amp;we_objectID=$id&amp;we_cmd[2]=$tid" . $_query_string;
		}
	} else {
		if ($triggerID) {
			$loc = id_to_path($triggerID) . "?";
			$url = $loc . 'pv_id=' . $id . '&amp;pv_tid=' . $tid . $_query_string;
		} else {
			$loc = "/webEdition/we_cmd.php?we_cmd[0]=show&amp;";
			$url = $loc . 'we_cmd[1]=' . $id . '&amp;we_cmd[4]=' . $tid . $_query_string;
		}
	}
	$attr = we_make_attribs($attribs, "tid,doc,link,Link,triggerid");

	if ($link == "off" || $link == "false") {
		return $url;
	} else {
		$GLOBALS["we_tag_start_printVersion"] = 1;
		return '<a href="' . $url . '"' . ($attr ? " $attr" : '') . '>';
	}
}
