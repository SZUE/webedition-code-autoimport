<?php

/**
 * webEdition CMS
 *
 * $Rev: 2836 $
 * $Author: mokraemer $
 * $Date: 2011-04-30 00:42:42 +0200 (Sa, 30. Apr 2011) $
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
function we_parse_tag_answers($attribs, $content) {
	if (!defined("VOTING_TABLE")) {
		return modulFehltError('Voting', '"Answer"');
	}
	return '<?php while('.we_tagParser::printTag('answers',$attribs).'){?>' . $content . '<?php }?>';
}

function we_tag_answers($attribs, $content) {
	if (isset($GLOBALS["_we_voting"]) && $GLOBALS["_we_voting"]->getNext()) {
		return true;
	}

	if (isset($GLOBALS['_we_voting']))
		$GLOBALS['_we_voting']->resetSets();
	return false;
}