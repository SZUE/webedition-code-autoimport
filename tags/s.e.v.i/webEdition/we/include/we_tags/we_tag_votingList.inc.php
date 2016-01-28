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
		echo modulFehltError('Voting', __FUNCTION__);
		return;
	}
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$groupid = weTag_getAttribute('groupid', $attribs, 0, we_base_request::INT);
	$rows = weTag_getAttribute('rows', $attribs, 0, we_base_request::INT);
	$desc = weTag_getAttribute('desc', $attribs, false, we_base_request::BOOL);
	$order = weTag_getAttribute('order', $attribs, 'PublishDate', we_base_request::STRING);
	$subgroup = weTag_getAttribute("subgroup", $attribs, false, we_base_request::BOOL);
	$version = weTag_getAttribute("version", $attribs, 1, we_base_request::INT);
	$offset = weTag_getAttribute("offset", $attribs, 0, we_base_request::INT);
	$_type = weTag_getAttribute('_type', $attribs, '', we_base_request::STRING);
	$start = max(intval(we_base_request::_(we_base_request::INT, '_we_vl_start_' . $name, 0)), 0);

	switch($_type){
		case 'start':
			$GLOBALS['_we_voting_list'] = new we_voting_list($name, $groupid, ($version > 0 ? ($version - 1) : 0), $rows, $offset, $desc, $order, $subgroup, $start);
			break;
		case 'stop':
			unset($GLOBALS['_we_voting_list']);
			break;
	}
}
