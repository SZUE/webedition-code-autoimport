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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_pref($attribs){
	if(($foo = attributFehltError($attribs, array('type' => false, 'name' => false), __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('name', $attribs);

	switch(($type = weTag_getAttribute('type', $attribs))){
		case 'shop':
			break;
		case 'banner':
			switch($name){
				case 'DefaultBanner':
					$id = f('SELECT pref_value FROM ' . BANNER_PREFS_TABLE . " WHERE pref_name='DefaultBannerID'");
					return f('SELECT bannerID FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($id));
			}
			break;
		case 'newsletter':
			static $newsSet = false;
			$newsSet = $newsSet ? $newsSet : we_newsletter_view::getSettings();
			if(isset($newsSet[$name])){
				return $newsSet[$name];
			}
			break;
	}
	t_e('pref ' . $name . 'not found in module ' . $type);
}
