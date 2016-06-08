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
function we_parse_tag_next($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('next', $attribs) . ');?>' . $content . '<?php printElement(' . we_tag_tagParser::printTag('next', array('_type' => 'stop')) . ');?>';
}

function we_tag_next(array $attribs){
	switch(weTag_getAttribute('_type', $attribs, '', we_base_request::STRING)){
		default:
			$attribs = removeAttribs($attribs, array('_type'));
			if(isset($GLOBALS['_we_voting_list'])){
				return $GLOBALS['_we_voting_list']->getNextLink($attribs);
			}
			return $GLOBALS['lv']->getNextLink($attribs);

		case 'stop':
			if(isset($GLOBALS['_we_voting_list'])){
				return ($GLOBALS['_we_voting_list']->hasNextPage() ? '</a>' : '');
			}
			return ($GLOBALS['lv']->hasNextPage() && $GLOBALS['lv']->close_a() ? '</a>' : '');
	}
}
