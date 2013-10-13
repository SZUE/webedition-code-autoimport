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
function we_tag_ifSelf($attribs){

	$id = weTag_getAttribute('id', $attribs);

	if(!$id){
		$id = (isset($GLOBALS['we_obj']) ?
				$GLOBALS['we_obj']->ID :
				$GLOBALS['WE_MAIN_DOC']->ID);
	}
	$type = weTag_getAttribute('doc', $attribs, weTag_getAttribute('type', $attribs));

	$ids = explode(',', $id);

	switch($type){
		case 'listview':
			switch($GLOBALS['lv']->ClassName){
				case 'we_listview_object':
					return in_array($GLOBALS['lv']->getDBf('OF_ID'), $ids);
				case 'we_search_listview':
					return in_array($GLOBALS['lv']->getDBf('WE_ID'), $ids);
				case 'we_shop_listviewShopVariants':
					reset($GLOBALS['lv']->Record);
					$key = key($GLOBALS['lv']->Record);
					if(isset($GLOBALS['we_doc']->Variant)){
						return ($key == $GLOBALS['we_doc']->Variant);
					}
					return ($key == $GLOBALS['lv']->DefaultName);

				default:
					return in_array($GLOBALS['lv']->IDs[$GLOBALS['lv']->count - 1], $ids);
			}
		case 'self' :
			if(isset($GLOBALS['we']['ll'])){
				return $GLOBALS['we']['ll']->getID() == $GLOBALS['we_doc']->ID;
			} else {
				return in_array($GLOBALS['we_doc']->ID, $ids);
			}
		default :
			if(isset($GLOBALS['we']['ll'])){
				return $GLOBALS['we']['ll']->getID() == $GLOBALS['WE_MAIN_DOC']->ID;
			} else {
				return in_array($GLOBALS['WE_MAIN_DOC']->ID, $ids);
			}
	}
}