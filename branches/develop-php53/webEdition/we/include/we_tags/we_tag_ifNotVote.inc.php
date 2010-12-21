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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/voting/weVoting.php');

function we_tag_ifNotVote($attribs, $content){
	$foo = attributFehltError($attribs,"type","ifNotVote");if($foo) return $foo;
	$type = we_getTagAttribute("type",$attribs,"error");

	if(isset($GLOBALS["_we_voting_status"])){
		switch ($type) {
			case "error":
				return ($GLOBALS["_we_voting_status"]==VOTING_ERROR);
			break;
			case "revote":
				return ($GLOBALS["_we_voting_status"]==VOTING_ERROR_REVOTE);
			break;
			case "active":
				return ($GLOBALS["_we_voting_status"]==VOTING_ERROR_ACTIVE);
			break;
			case "forbidden":
				return ($GLOBALS["_we_voting_status"]==VOTING_ERROR_BLACKIP);
			break;
			default: return ($GLOBALS["_we_voting_status"]>0);

		}
	}
	return false;
}
