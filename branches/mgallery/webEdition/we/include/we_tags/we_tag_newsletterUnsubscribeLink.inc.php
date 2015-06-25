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
function we_tag_newsletterUnsubscribeLink($attribs){
	if(($foo = attributFehltError($attribs, "id", __FUNCTION__))){
		return $foo;
	}
	$id = weTag_getAttribute("id", $attribs, 0, we_base_request::INT);
	$plain = weTag_getAttribute("plain", $attribs, true, we_base_request::BOOL);

	$db = $GLOBALS['DB_WE'];
	$db->query('SELECT pref_name,pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="newsletter" AND pref_name IN ("use_port","use_https_refer")');
	$settings = $db->getAllFirst(false);

	//TODO: how to use $port and $protocol within multidomains and normal mode?
	$port = (!empty($settings["use_port"])) ? ":" . $settings["use_port"] : '';
	$protocol = (!empty($settings["use_https_refer"])) ? 'https://' : 'http://';

	//Fix #9785
	$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE'], true);
	$url = preg_replace($urlReplace, array_keys($urlReplace), id_to_path($id, FILE_TABLE), -1, $cnt);

	$ret = ($cnt ? $url : getServerUrl() . id_to_path($id, FILE_TABLE)). '?we_unsubscribe_email__=' . we_newsletter_base::EMAIL_REPLACE_TEXT;
	return ($plain ? $ret : '<a href="' . $ret . '">' . $ret . '</a>');
}
