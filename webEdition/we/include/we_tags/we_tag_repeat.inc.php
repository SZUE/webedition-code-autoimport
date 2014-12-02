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
function we_parse_tag_repeat($attribs, $content){
	return '<?php while(' . we_tag_tagParser::printTag('repeat', $attribs) . '){
	if(isset($_SESSION[\'weS\'][\'we_mode\']) && $_SESSION[\'weS\'][\'we_mode\'] == we_base_constants::MODE_SEE){
		echo we_SEEM::getSeemAnchors();
	}?>' .
			$content . '<?php }?>';
}

function we_tag_repeat(){
	if(isset($GLOBALS['_we_voting_list'])){
		return $GLOBALS['_we_voting_list']->getNext();
	}
	if(isset($GLOBALS['lv'])){
		if($GLOBALS['lv']->next_record()){
			$GLOBALS["we_lv_array"][(count($GLOBALS["we_lv_array"]) - 1)] = clone($GLOBALS["lv"]);
			if($GLOBALS['lv'] instanceof we_object_listview){
				$GLOBALS['_we_object_listview_flag'] = true;
			}
			return true;
		} //last entry
		unset($GLOBALS['_we_object_listview_flag']);
	}

	return false;
}
