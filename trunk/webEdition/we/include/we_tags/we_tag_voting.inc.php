<?php

function we_parse_tag_voting($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('voting', $attribs) . ');?>' . $content . '<?php if(isset($GLOBALS[\'_we_voting\'])) unset($GLOBALS[\'_we_voting\']);?>';
}

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
function we_tag_voting($attribs){
	if(!defined('VOTING_TABLE')){
		return modulFehltError('Voting', __FUNCTION__);
	}
	$id = weTag_getAttribute("id", $attribs, 0);
	$name = weTag_getAttribute("name", $attribs);
	$version = weTag_getAttribute("version", $attribs, 0);

	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	require_once(WE_MODULES_PATH . 'voting/weVoting.php');
	$version = ($version > 0) ? ($version - 1) : 0;
	$GLOBALS["_we_voting_namespace"] = $name;
	$GLOBALS['_we_voting'] = new we_voting_voting();

	if(isset($GLOBALS['we_doc']->elements[$GLOBALS['_we_voting_namespace']]['dat'])){
		$GLOBALS['_we_voting'] = new we_voting_voting($GLOBALS['we_doc']->elements[$GLOBALS['_we_voting_namespace']]['dat']);
	} else if($id != 0){
		$GLOBALS['_we_voting'] = new we_voting_voting($id);
	} else {
		$__voting_matches = array();
		if(preg_match_all('/_we_voting_answer_([0-9]+)_?([0-9]+)?/', implode(',', array_keys($_REQUEST)), $__voting_matches)){
			$GLOBALS['_we_voting'] = new we_voting_voting($__voting_matches[1][0]);
		}
	}

	if(isset($GLOBALS['_we_voting'])){
		$GLOBALS['_we_voting']->setDefVersion($version);
	}
}
