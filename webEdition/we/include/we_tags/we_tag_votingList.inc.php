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
function we_parse_tag_votingList($a, $content, array $attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$attribs['_type'] = 'start';
	return '<?php ' . we_tag_tagParser::printTag('votingList', $attribs) . '; ?>' . $content . '<?php ' . we_tag_tagParser::printTag('votingList', array('_type' => 'stop')) . ';?>';
}

function we_tag_votingList($attribs){
	if(!defined('VOTING_TABLE')){
		print modulFehltError('Voting', __FUNCTION__);
		return;
	}
	$name = weTag_getAttribute('name', $attribs);
	$groupid = weTag_getAttribute('groupid', $attribs, 0);
	$rows = weTag_getAttribute('rows', $attribs, 0);
	$desc = weTag_getAttribute('desc', $attribs, false, true);
	$order = weTag_getAttribute('order', $attribs, 'PublishDate');
	$subgroup = weTag_getAttribute("subgroup", $attribs, false, true);
	$version = weTag_getAttribute("version", $attribs, 1);
	$offset = weTag_getAttribute("offset", $attribs, 0);
	$_type = weTag_getAttribute('_type', $attribs);
	$start = (isset($_REQUEST['_we_vl_start_' . $name]) && $_REQUEST['_we_vl_start_' . $name]) ? abs($_REQUEST['_we_vl_start_' . $name]) : 0;

	switch($_type){
		case 'start':
			$GLOBALS['_we_voting_list'] = new we_voting_list($name, $groupid, ($version > 0 ? ($version - 1) : 0), $rows, $offset, $desc, $order, $subgroup, $start);
			break;
		case 'stop':
			unset($GLOBALS['_we_voting_list']);
			break;
	}
}
