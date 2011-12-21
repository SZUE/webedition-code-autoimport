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

function we_tag_writeVoting($attribs, $content) {

	$id = we_getTagAttributeTagParser('id',$attribs,0);
	$additionalFields = we_getTagAttributeTagParser('additionalfields',$attribs,0);
	$allowredirect = we_getTagAttributeTagParser("allowredirect", $attribs, "", true);
	$deletesessiondata = we_getTagAttributeTagParser("deletesessiondata", $attribs, "true", true);
	$writeto = we_getTagAttributeTagParser("writeto", $attribs, "voting");

	include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/voting/weVoting.php');

	if($id) {
		$pattern = '/_we_voting_answer_(' . $id . ')_?([0-9]+)?/';
	} else {
		$pattern = '/_we_voting_answer_([0-9]+)_?([0-9]+)?/';
	}

	$vars = implode(',',array_keys($_REQUEST));

	$_voting = array();

	if(preg_match_all($pattern, $vars, $matches)){
		foreach ($matches[0] as $key=>$value){
			$id = $matches[1][$key];
			if(!isset($_voting[$id]) || !is_array($_voting[$id])) {
				$_voting[$id] = array();
			}
			if (!empty($_REQUEST[$value])) {
				$_voting[$id][]= $_REQUEST[$value];
			}
		}
	}
	$additionalFieldsArray = makeArrayFromCSV($additionalFields);
	$addFields = array();
	foreach ($additionalFieldsArray as $field){
		if(isset($_REQUEST[$field])) {
			$addFields[$field] = $_REQUEST[$field];
		}
	}


	if ($deletesessiondata){	unset($_SESSION['_we_voting_sessionData']);}


	foreach($_voting as $id=>$value){
		if(	$writeto =='voting'){
			$voting = new weVoting($id);
			if ($voting->IsRequired && implode('',$value) =='') {

				$GLOBALS['_we_voting_status'] = VOTING_ERROR;
				if (isset($_SESSION['_we_voting_sessionID'])){$votingsession= $_SESSION['_we_voting_sessionID'];} else {$votingsession=0;}
				if($voting->Log) $voting->logVoting(VOTING_ERROR,$votingsession,'','','');
				break;
			}

			$GLOBALS['_we_voting_status'] = $voting->vote($value,$addFields);
			if($GLOBALS['_we_voting_status'] != VOTING_SUCCESS) {
				break;
			}
		} else {
			$voting = new weVoting($id);
			if ($voting->IsRequired && implode('',$value) =='') {

				$GLOBALS['_we_voting_status'] = VOTING_ERROR;
				if (isset($_SESSION['_we_voting_sessionID'])){$votingsession= $_SESSION['_we_voting_sessionID'];} else {$votingsession=0;}
				if($voting->Log) $voting->logVoting(VOTING_ERROR,$votingsession,'','','');
				break;
			}

			$GLOBALS['_we_voting_status'] = $voting->setSuccessor($value);
			if($GLOBALS['_we_voting_status'] != VOTING_SUCCESS) {
				break;
			}
			$_SESSION['_we_voting_sessionData'][$id] = array ('value' => $value,'addFields' => $addFields );

		}


	}
	if ($allowredirect && !$GLOBALS["WE_MAIN_DOC"]->InWebEdition && isset($GLOBALS['_we_voting_SuccessorID']) && $GLOBALS['_we_voting_SuccessorID'] > 0) {
		$mypath = id_to_path($GLOBALS['_we_voting_SuccessorID']);
		if ($mypath != $_SERVER['SCRIPT_NAME']) {
			header("Location: ".$mypath); /* Redirect browser */

		/* Make sure that code below does not get executed when we redirect. */
			exit;
		}

	}

}
