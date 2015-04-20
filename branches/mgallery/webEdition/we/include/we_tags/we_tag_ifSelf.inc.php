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
function we_tag_ifSelf($attribs){

	$ids = weTag_getAttribute('id', $attribs, array(), we_base_request::INTLISTA)? :
		(explode(',', isset($GLOBALS['we_obj']) ?
				$GLOBALS['we_obj']->ID :
				(isset($GLOBALS['lv']) && $GLOBALS['lv'] instanceof stdClass ?
					$GLOBALS['lv']->ID :
					$GLOBALS['WE_MAIN_DOC']->ID)));

	$type = weTag_getAttribute('doc', $attribs, weTag_getAttribute('type', $attribs, '', we_base_request::STRING), we_base_request::STRING);


	switch($type){
		case 'listview':
			switch(get_class($GLOBALS['lv'])){
				case 'we_object_listview':
				case 'we_listview_search':
					return in_array($GLOBALS['lv']->f('WE_ID'), $ids);
				case 'we_listview_variants':
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
			}
			return in_array($GLOBALS['we_doc']->ID, $ids);

		default :
			if(isset($GLOBALS['we']['ll'])){
				return $GLOBALS['we']['ll']->getID() == $GLOBALS['WE_MAIN_DOC']->ID;
			}
			return in_array($GLOBALS['WE_MAIN_DOC']->ID, $ids);
	}
}
