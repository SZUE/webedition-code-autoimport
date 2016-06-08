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
function we_tag_sidebar(array $attribs, $content){
	if(SIDEBAR_DISABLED || (we_tag('ifNotEditmode') && !defined('WE_SIDEBAR'))){
		return '';
	}
	$id = intval(weTag_getAttribute('id', $attribs, 0, we_base_request::INT));
	$file = weTag_getAttribute('file', $attribs, '', we_base_request::FILE);
	$url = weTag_getAttribute('url', $attribs, '', we_base_request::URL);
	//$anchor = weTag_getAttribute('anchor', $attribs);
	$width = weTag_getAttribute('width', $attribs, SIDEBAR_DEFAULT_WIDTH, we_base_request::UNIT);
	$params = weTag_getAttribute('params', $attribs, '', we_base_request::RAW);
	if($params && strpos($params, '?') === 0){
		$params = substr($params, 1);
	}

	removeAttribs($attribs, array('id', 'file', 'url', 'width', 'href', 'params', 'anchor'));

	if(!trim($content)){
		$content = g_l('tags', '[open_sidebar]');
	}

	if($id){
		if(!f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id))){
			return $content;
		}

		$attribs['href'] = "javascript:WE().layout.sidebar.open('" . $id . "', " . $width . ",'" . $params . "');";
	} else {
		$file = ($file ? : $url);
		if(!$file){
			return '';
		}
		$attribs['href'] = "javascript:WE().layout.sidebar.load('" . $file . "');WE().layout.sidebar.resize(" . $width . ",'" . $params . "');";
	}

	return getHtmlTag('a', $attribs, $content);
}
