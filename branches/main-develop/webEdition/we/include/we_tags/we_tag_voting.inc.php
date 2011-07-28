<?php

/**
 * webEdition CMS
 *
 * $Rev: 2633 $
 * $Author: mokraemer $
 * $Date: 2011-03-08 01:16:50 +0100 (Di, 08. Mär 2011) $
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
function we_tag_voting($attribs, $content) {
	if (!defined("VOTING_TABLE")) {
		return modulFehltError('Voting', '"voting"');
	}
	$id = we_getTagAttribute("id", $attribs, 0);
	$name = we_getTagAttribute("name", $attribs, '');
	$version = we_getTagAttribute("version", $attribs, 0);

	if (($foo = attributFehltError($attribs, 'name', 'voting'))) {
		return $foo;
	}

	include_once($_SERVER["DOCUMENT_ROOT"] . '/webEdition/we/include/we_modules/voting/weVoting.php');
	$version = ($version > 0) ? ($version - 1) : 0;
	$GLOBALS["_we_voting_namespace"] = $name;
	$GLOBALS['_we_voting'] = new weVoting();

	if (isset($GLOBALS['we_doc']->elements[$GLOBALS['_we_voting_namespace']]['dat'])) {
		$GLOBALS['_we_voting'] = new weVoting($GLOBALS['we_doc']->elements[$GLOBALS['_we_voting_namespace']]['dat']);
	} else if ($id != 0) {
		$GLOBALS['_we_voting'] = new weVoting($id);
	} else {
		$__voting_matches = array();
		if (preg_match_all('/_we_voting_answer_([0-9]+)_?([0-9]+)?/', implode(',', array_keys($_REQUEST)), $__voting_matches)) {
			$GLOBALS['_we_voting'] = new weVoting($__voting_matches[1][0]);
		}
	}
	
	if (isset($GLOBALS['_we_voting'])) {
		$GLOBALS['_we_voting']->setDefVersion($version);
	}
}