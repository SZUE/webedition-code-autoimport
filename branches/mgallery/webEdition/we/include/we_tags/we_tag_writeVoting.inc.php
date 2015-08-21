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
function we_tag_writeVoting($attribs){

	$id = weTag_getAttribute('id', $attribs, 0, we_base_request::INT);
	$additionalFields = weTag_getAttribute('additionalfields', $attribs, array(), we_base_request::INTLISTA);
	$allowredirect = weTag_getAttribute("allowredirect", $attribs, false, we_base_request::BOOL);
	$deletesessiondata = weTag_getAttribute("deletesessiondata", $attribs, false, we_base_request::BOOL);
	$writeto = weTag_getAttribute("writeto", $attribs, "voting", we_base_request::STRING);

	$pattern = '/_we_voting_answer_(' . ($id ? : '[0-9]+') . ')_?([0-9]+)?/';

	$vars = implode(',', array_keys($_REQUEST));
	$_voting = array();

	$matches = array();
	if(preg_match_all($pattern, $vars, $matches)){
		foreach($matches[0] as $key => $value){
			$id = $matches[1][$key];
			if(!isset($_voting[$id]) || !is_array($_voting[$id])){
				$_voting[$id] = array();
			}
			if(($dat = we_base_request::_(we_base_request::STRING, $value)) !== false && $dat !== ''){// Bug #6118: !empty geht hier nicht, da es die 0 nicht durch lässt
				$_voting[$id][] = $dat;
			}
		}
	}
	$addFields = array();
	foreach($additionalFields as $field){
		if(($dat = we_base_request::_(we_base_request::STRING, $field)) !== false){
			$addFields[$field] = $dat;
		}
	}


	if($deletesessiondata){
		unset($_SESSION['_we_voting_sessionData']);
	}


	foreach($_voting as $id => $value){
		if($writeto === 'voting'){
			$voting = new we_voting_voting($id);
			if($voting->IsRequired && implode('', $value) == ''){

				$GLOBALS['_we_voting_status'] = we_voting_voting::ERROR;
				if(isset($_SESSION['_we_voting_sessionID'])){
					$votingsession = $_SESSION['_we_voting_sessionID'];
				} else {
					$votingsession = 0;
				}
				if($voting->Log){
					$voting->logVoting(we_voting_voting::ERROR, $votingsession, '', '', '');
				}
				break;
			}

			$GLOBALS['_we_voting_status'] = $voting->vote($value, $addFields);
			if($GLOBALS['_we_voting_status'] != we_voting_voting::SUCCESS){
				break;
			}
		} else {
			$voting = new we_voting_voting($id);
			if($voting->IsRequired && implode('', $value) == ''){

				$GLOBALS['_we_voting_status'] = we_voting_voting::ERROR;
				$votingsession = (isset($_SESSION['_we_voting_sessionID']) ? $_SESSION['_we_voting_sessionID'] : 0);
				if($voting->Log){
					$voting->logVoting(we_voting_voting::ERROR, $votingsession, '', '', '');
				}
				break;
			}

			$GLOBALS['_we_voting_status'] = $voting->setSuccessor($value);
			if($GLOBALS['_we_voting_status'] != we_voting_voting::SUCCESS){
				break;
			}
			$_SESSION['_we_voting_sessionData'][$id] = array('value' => $value, 'addFields' => $addFields);
		}
	}
	if($allowredirect && !$GLOBALS["WE_MAIN_DOC"]->InWebEdition && isset($GLOBALS['_we_voting_SuccessorID']) && $GLOBALS['_we_voting_SuccessorID'] > 0){
		$mypath = id_to_path($GLOBALS['_we_voting_SuccessorID']);
		if($mypath != $_SERVER['SCRIPT_NAME']){
			header("Location: " . $mypath); /* Redirect browser */

			/* Make sure that code below does not get executed when we redirect. */
			exit;
		}
	}
}
