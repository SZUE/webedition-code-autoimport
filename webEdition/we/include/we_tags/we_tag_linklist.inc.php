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
function we_parse_tag_linklist($attribs, $content){
	return '<?php $GLOBALS[\'we\'][\'ll\']=' . we_tag_tagParser::printTag('linklist', $attribs) . '; while($GLOBALS[\'we\'][\'ll\']->next()){?>' . $content . '<?php } $GLOBALS[\'we\'][\'ll\']->last(); unset($GLOBALS[\'we\'][\'ll\']);' . we_tag_tagParser::printTag('linklist', ['_type' => 'stop']) . ';?>';
}

function we_tag_linklist(array $attribs){
	switch(weTag_getAttribute('_type', $attribs, '', we_base_request::STRING)){
		default:
			$name = weTag_getAttribute("name", $attribs, '', we_base_request::STRING);
			$hidedirindex = weTag_getAttribute("hidedirindex", $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, we_base_request::BOOL);
			$objectseourls = weTag_getAttribute("objectseourls", $attribs, TAGLINKS_OBJECTSEOURLS, we_base_request::BOOL);
			if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
				return $foo;
			}
			$isInListview = isset($GLOBALS["lv"]);

			$linklist = ($isInListview ? $GLOBALS["lv"]->f($name) : (isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->getElement($name) : ''));

			$ll = new we_base_linklist(we_unserialize($linklist), $hidedirindex, $objectseourls, $GLOBALS['we_doc']->Name, $attribs);
			$ll->setName($name);
			return $ll;
		case 'stop':
			return '';
	}
}
