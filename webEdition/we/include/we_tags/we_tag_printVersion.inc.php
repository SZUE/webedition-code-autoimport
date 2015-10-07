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
function we_tag_printVersion($attribs, $content){
	if(($foo = attributFehltError($attribs, 'tid', __FUNCTION__))){
		return $foo;
	}

	$tid = weTag_getAttribute('tid', $attribs, 0, we_base_request::INT);
	$triggerID = weTag_getAttribute('triggerid', $attribs, weTag_getAttribute('triggerID', $attribs, 0, we_base_request::INT), we_base_request::INT);
	$docAttr = weTag_getAttribute("doc", $attribs, weTag_getAttribute("type", $attribs, '', we_base_request::STRING), we_base_request::STRING);
	$link = weTag_getAttribute("Link", $attribs, weTag_getAttribute("link", $attribs, true, we_base_request::BOOL), we_base_request::BOOL);

	$doc = we_getDocForTag($docAttr);

	$id = isset($GLOBALS['we_obj']) ? $GLOBALS['we_obj']->ID : $doc->ID;

	$_query_string = array();

	$hideQuery = array('we_objectID', 'tid', 'id', 'pv_tid', 'pv_id', 'we_cmd', 'responseText', 'we_mode', 'btype', SESSION_NAME, 'PHPSESSID');
	if(isset($_SESSION)){
		$hideQuery[] = session_name();
	}
	if(isset($_REQUEST)){
		$tmp = filterXss($_REQUEST);
		foreach($tmp as $k => $v){
			if((!is_array($v)) && (!in_array($k, $hideQuery))){
				$_query_string[$k] = $v;
			}
		}
	}

	if($doc instanceof we_objectFile){
		//objects are always shown by a dynamic page
		$_query_string['we_objectID'] = $id;
		$_query_string['tid'] = $tid;
		$url = ($triggerID ? id_to_path($triggerID) : (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']));
	} else {
		$triggerID = $triggerID ? : ($doc->IsDynamic ? $doc->ID : 0);
		if($triggerID || $doc->IsDynamic){
			$_query_string['pv_id'] = $id;
			$_query_string['pv_tid'] = $tid;
			$url = $triggerID ? id_to_path($triggerID) : (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
		} else {

			return '';
			/*
			  $_query_string['we_cmd[0]'] = 'show';
			  $_query_string['we_cmd[1]'] = $id;
			  $_query_string['we_cmd[4]'] = $tid;
			  $url = WEBEDITION_DIR . 'we_cmd.php';
			 */
		}
	}

	if($link){
		$attribs = removeAttribs($attribs, array('tid', 'triggerID', 'triggerid', 'doc', 'type', 'link', 'Link')); //	not html - valid
		$attribs['href'] = $url . '?' . http_build_query($_query_string);
		return getHtmlTag('a', $attribs, $content, true);
	}
	return $url . '?' . http_build_query($_query_string);
}
