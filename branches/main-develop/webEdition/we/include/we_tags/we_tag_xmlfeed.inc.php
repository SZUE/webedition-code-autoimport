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

/**
 * @return string
 * @param array $attribs
 * @param string $content
 */
function we_tag_xmlfeed($attribs){
	if(($foo = attributFehltError($attribs, array('name' => false, 'url' => false), __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$url = weTag_getAttribute('url', $attribs, '', we_base_request::URL);
	$refresh = abs(weTag_getAttribute('refresh', $attribs, 30, we_base_request::INT)) * 60;
	$timeout = abs(weTag_getAttribute('timeout', $attribs, 0, we_base_request::INT));

	if(!isset($GLOBALS['xmlfeeds'])){
		$GLOBALS['xmlfeeds'] = array();
	}

	$GLOBALS['xmlfeeds'][$name] = new we_xml_browser();
	$cache = WEBEDITION_PATH . 'xmlfeeds/' . $name;

	$do_refresh = (is_file($cache) && $refresh > 0 ? ((filemtime($cache) + $refresh) < time()) : true);

	if(!is_file($cache) || $do_refresh){
		$ret = $GLOBALS['xmlfeeds'][$name]->getFile($url, $timeout);
		if($ret){
			$GLOBALS['xmlfeeds'][$name]->saveCache($cache, time() + (2 * $refresh)); //keep file longer, in case of timeouts
		} else if($timeout && is_file($cache)){
			//timeout + last file exists
			$GLOBALS['xmlfeeds'][$name]->loadCache($cache);
		}
	} else {
		$GLOBALS['xmlfeeds'][$name]->loadCache($cache);
	}
}
