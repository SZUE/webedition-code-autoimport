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
function we_parse_tag_votingList($attribs, $content) {
	eval('$attribs = ' . $attribs . ';');
	if (($foo = attributFehltError($attribs, 'name', 'votingList'))) {
		return $foo;
	}

	$attribs['_type'] = 'start';
	return '<?php we_tag(\'votingList\',' . we_tagParserPrintArray($attribs) . '); ' . $content . ' we_tag(\'votingList\',array(\'_type\'=>\'stop\'));?>';
}

function we_tag_votingList($attribs, $content) {
	if (!defined("VOTING_TABLE")) {
		print modulFehltError('Voting', '"VotingList"');
		return;
	}
	$name = we_getTagAttribute('name', $attribs, '');
	$groupid = we_getTagAttribute('groupid', $attribs, 0);
	$rows = we_getTagAttribute('rows', $attribs, 0);
	$desc = we_getTagAttribute('desc', $attribs, "false");
	$order = we_getTagAttribute('order', $attribs, 'PublishDate');
	$subgroup = we_getTagAttribute("subgroup", $attribs, "false");
	$version = we_getTagAttribute("version", $attribs, 1);
	$offset = we_getTagAttribute("offset", $attribs, 0);

	$_type = we_getTagAttribute('_type', $attribs);
	switch ($_type) {
		case 'start':
			include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/voting/weVotingList.php');
			$GLOBALS['_we_voting_list'] = new weVotingList($name, $groupid, ($version > 0 ? ($version - 1) : 0), $rows, $offset, $desc, $order, $subgroup);
			break;
		case 'stop':
			unset($GLOBALS['_we_voting_list']);
			break;
	}
}
