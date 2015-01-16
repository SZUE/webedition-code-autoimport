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

/**
 * General Definition of WebEdition Voting
 *
 */
class we_voting_voting extends weModelBase{
//voting status codes

	const SUCCESS = 1;
	const ERROR = 2;
	const ERROR_REVOTE = 3;
	const ERROR_ACTIVE = 4;
	const ERROR_BLACKIP = 5;
	const ERROR_REQUIRED = 6;

	//properties
	var $ID;
	var $ParentID;
	var $Path;
	var $IsFolder;
	var $Icon;
	var $Text;
	var $PublishDate = 0;
	var $QASet = array();
	var $QASetAdditions = array();
	var $IsRequired = false;
	var $AllowFreeText = false;
	var $AllowImages = false;
	var $AllowMedia = false;
	var $AllowSuccessor = false;
	var $AllowSuccessors = false;
	var $Successor = 0;
	var $Scores;
	var $RevoteControl = 0;
	var $RevoteTime = -1;
	var $Owners = array();
	var $RestrictOwners = false;
	var $Revote = '';
	var $RevoteUserAgent = '';
	var $Valid = 0;
	var $Active = true;
	var $ActiveTime = false;
	var $FallbackIp = false;
	var $UserAgent = false;
	var $FallbackUserID = false;
	var $Log = false;
	var $LogData = array();
	var $RestrictIP = false;
	var $BlackList = array();
	var $answerCount = -1;
	var $defVersion = 0;
	var $LogDB = 0;
	var $protected = array('ID', 'ParentID', 'Icon', 'IsFolder', 'Path', 'Text');
	var $FallbackActive = 0;

	/**
	 * Default Constructor
	 * Can load or create new Newsletter depends of parameter
	 */
	function __construct($votingID = 0){
		parent::__construct(VOTING_TABLE);
		$this->persistent_slots = array(
			'ID' => we_base_request::INT,
			'ParentID' => we_base_request::INT,
			'Path' => we_base_request::FILE,
			'IsFolder' => we_base_request::BOOL,
			'Icon' => we_base_request::FILE,
			'Text' => we_base_request::FILE,
			'PublishDate' => we_base_request::INT,
			'QASet' => we_base_request::RAW,
			'QASetAdditions' => we_base_request::RAW,
			'IsRequired' => we_base_request::BOOL,
			'AllowFreeText' => we_base_request::BOOL,
			'AllowImages' => we_base_request::BOOL,
			'AllowMedia' => we_base_request::BOOL,
			'AllowSuccessor' => we_base_request::BOOL,
			'AllowSuccessors' => we_base_request::BOOL,
			'Successor' => we_base_request::INT,
			'Scores' => we_base_request::RAW,
			'RevoteControl' => we_base_request::INT,
			'RevoteTime' => we_base_request::INT,
			'Owners' => we_base_request::RAW,
			'RestrictOwners' => we_base_request::BOOL,
			'Revote' => we_base_request::RAW,
			'RevoteUserAgent' => we_base_request::RAW,
			'Valid' => we_base_request::INT,
			'Active' => we_base_request::BOOL,
			'ActiveTime' => we_base_request::BOOL,
			'FallbackIp' => we_base_request::BOOL,
			'UserAgent' => we_base_request::BOOL,
			'FallbackUserID' => we_base_request::BOOL,
			'Log' => we_base_request::BOOL,
			'LogData' => we_base_request::RAW,
			'RestrictIP' => we_base_request::BOOL,
			'BlackList' => we_base_request::RAW,
		);

		if($votingID){
			$this->ID = $votingID;
			$this->load($votingID);
		}

		$this->QASet = $this->QASet ? : array(
			0 => array(
				'question' => '',
				'answers' => array(
					0 => '',
					1 => ''
				)
			)
		);

		$this->QASetAdditions = $this->QASetAdditions ? : array(
			0 => array('imageID' => '', 'mediaID' => '', 'successorID' => '')
		);

		$this->Valid = ($this->Valid? : time() + 31536000); //365 days
		$this->Active = ($this->Valid < time() && $this->ActiveTime ? 0 : $this->Active);
		$this->Scores = ($this->Scores? : array());
		$this->PublishDate = ($this->PublishDate ? : time());
	}

	function load($id = 0, $isAdvanced = false){
		if(parent::load($id, true)){
			$this->QASet = unserialize($this->QASet);
			$this->QASetAdditions = unserialize($this->QASetAdditions);
			$this->Scores = unserialize($this->Scores);
			$this->Owners = makeArrayFromCSV($this->Owners);
			$this->BlackList = makeArrayFromCSV($this->BlackList);
			if(!$this->LogData || $this->LogData === 'a:0:{}'){
				$this->LogDB = true;
			} else {
				$this->switchToLogDataDB();
			}
		}
	}

	function save($with_scores = true){
		$this->Icon = ($this->IsFolder == 1 ? we_base_ContentTypes::FOLDER_ICON : we_base_ContentTypes::FILE_ICON);

		if($this->ActiveTime && $this->Valid < time()){
			$this->Active = 0;
		}

		if(!$this->IsFolder && ($with_scores || $this->ID == 0)){
			$qaset_count = count($this->QASet[$this->defVersion]['answers']);
			$scores_count = count($this->Scores);

			if($qaset_count != $scores_count){
				$diff = $qaset_count - $scores_count;
				if($diff > 0){
					for($i = 0; $i < $diff; $i++){
						$this->Scores[] = 0;
					}
				}
				if($diff < 0){
					$diff = abs($diff);
					for($i = 0; $i < $diff; $i++){
						array_splice($this->Scores, (count($this->Scores) - 1), 1);
					}
				}
			}
		}

		$this->ParentID = $this->ParentID ? : 0;
		if(isset($_SESSION['user']['ID']) && ($this->RestrictOwners && empty($this->Owners) || !in_array($_SESSION['user']['ID'], $this->Owners))){
			$this->Owners[] = $_SESSION['user']['ID'];
		}

		/* $_old_QASet = f('SELECT QASet FROM ' . VOTING_TABLE . " WHERE Text='" . $GLOBALS['DB_WE']->escape($this->Text) . "'");
		  $_new_QASet = $this->QASet;
		 */
		if($with_scores || $this->ID == 0){
			$this->Scores = serialize($this->Scores);
		} elseif(we_base_request::_(we_base_request::BOOL, 'updateScores')){
			$ic = we_base_request::_(we_base_request::INT, 'item_count');
			for($_xcount = 0; $_xcount < $ic; $_xcount++){
				if(($tmp = we_base_request::_(we_base_request::FLOAT, 'scores_' . $_xcount))){
					$temp[$_xcount] = $tmp;
				}
				$this->Scores = serialize($temp);
			}
		} else {
			$temp = $this->Scores;
			unset($this->Scores);
		}



		$logdata = $this->LogData;
		unset($this->LogData);
		$oldid = $this->ID;

		$this->Owners = array_unique($this->Owners);
		$this->Owners = makeCSVFromArray($this->Owners, true);

		$this->BlackList = array_unique($this->BlackList);
		$this->BlackList = makeCSVFromArray($this->BlackList, true);

		parent::save(false, true);

		$this->Scores = ($with_scores || $oldid == 0 ? unserialize($this->Scores) : $temp);

		$this->Owners = makeArrayFromCSV($this->Owners);
		$this->BlackList = makeArrayFromCSV($this->BlackList);
		$this->LogData = $logdata;
	}

	function saveField($name, $serialize = false){
		$field = ($serialize ? serialize($this->$name) : $this->$name);
		return $this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . $this->db->escape($name) . '="' . $this->db->escape($field) . '" WHERE ID=' . intval($this->ID) . ';');
	}

	function delete(){
		if(!$this->ID){
			return false;
		}
		if($this->IsFolder){
			$this->deleteChilds();
		}
		parent::delete();
		return true;
	}

	function deleteChilds(){
		$this->db->query('SELECT ID FROM ' . VOTING_TABLE . ' WHERE ParentID=' . intval($this->ID));
		while($this->db->next_record()){
			$child = new we_voting_voting($this->db->f('ID'));
			$child->delete();
		}
	}

	function setPath(){
		$this->Path = f('SELECT Path FROM ' . VOTING_TABLE . ' WHERE ID=' . intval($this->ParentID), '', $this->db) . '/' . $this->Text;
	}

	function pathExists($path){
		return f('SELECT 1 FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($path) . '" AND ID!=' . intval($this->ID) . ' LIMIT 1', '', $this->db);
	}

	function isSelf(){
		return (strpos(we_base_file::clearPath(dirname($this->Path) . '/'), '/' . $this->Text . '/') !== false);
	}

	function evalPath($id = 0){
		$db_tmp = new DB_WE();
		$path = '';
		if($id == 0){
			$id = $this->ParentID;
			$path = $this->Text;
		}

		$foo = getHash('SELECT Text,ParentID FROM ' . VOTING_TABLE . ' WHERE ID=' . intval($id), $db_tmp);
		$path = '/' . (isset($foo['Text']) ? $foo['Text'] : '') . $path;

		$pid = isset($foo['ParentID']) ? $foo['ParentID'] : '';
		while($pid > 0){
			$db_tmp->query('SELECT Text,ParentID FROM ' . VOTING_TABLE . ' WHERE ID=' . intval($pid));
			while($db_tmp->next_record()){
				$path = '/' . $db_tmp->f('Text') . $path;
				$pid = $db_tmp->f('ParentID');
			}
		}
		return $path;
	}

	function initByName($name){
		$id = f('SELECT ID FROM ' . VOTING_TABLE . ' WHERE Text="' . $this->db->escape($name) . '"', '', $this->db);
		return $this->load($id);
	}

	function clearSessionVars(){
		if(isset($_SESSION['weS']['voting_session'])){
			unset($_SESSION['weS']['voting_session']);
		}
	}

	function filenameNotValid($text){
		return preg_match('%[^a-z0-9äüöß\._\@\ \-]%i', $text);
	}

	function getNext(){
		$this->answerCount++;
//	if($this->AllowFreeText){
		$addOne = 0;
		return ($this->answerCount < count($this->QASet[$this->defVersion]['answers']) + $addOne);
	}

	function resetSets(){
		$this->answerCount = -1;
	}

	function getAnswer($ansn = -1){
		return $this->QASet[$this->defVersion]['answers'][$this->answerCount];
	}

	function getResult($type = 'count', $num_format = '', $precision = we_util_Strings::PRECISION){
		switch($type){
			case 'percent':
				$total = $this->getResult('total');
				if($total <= 0){
					return 0;
				}
				$_scores = isset($this->Scores[$this->answerCount]) ? $this->Scores[$this->answerCount] : 0;
				$result = ($total > 0 && $this->answerCount >= 0 ? round((($_scores / $total) * 100), $precision) : 100);
				break;
			case 'total':
				$result = array_sum($this->Scores);
				break;
			case 'count':
			default:
				if($this->answerCount >= 0 && isset($this->Scores[$this->answerCount])){
					$result = $this->Scores[$this->answerCount];
				}
				break;
		}

		if($num_format){
			$result = we_util_Strings::formatNumber($result, $num_format, $precision);
		}
		return $result;
	}

	function isLastSet(){
		return ($this->answerCount >= count($this->QASet[$this->defVersion]['answers']) - 1);
	}

	function setDefVersion($version){
		if($version < 0){
			$this->defVersion = 0;
		}
		$this->defVersion = ($version < count($this->QASet) ? $version : count($this->QASet) - 1);
	}

	function isAllowedForUser(){
		if($this->RestrictOwners == 0 || permissionhandler::hasPerm('ADMINISTRATOR') || in_array($_SESSION['user']['ID'], $this->Owners)){
			return true;
		}

		$userids = array();
		we_readParents($_SESSION['user']['ID'], $userids, USER_TABLE, 'IsFolder', 1, $this->db);

		return array_intersect($userids, $this->Owners) ? true : false;
	}

	function getOwnersSql(){
		$owners_sql = '';
		if(!permissionhandler::hasPerm('ADMINISTRATOR')){
			$userids = array(
				$_SESSION['user']['ID']
			);
			we_readParents($_SESSION['user']['ID'], $userids, USER_TABLE, 'IsFolder', 1);

			$sqlarr = array();
			foreach($userids as $uid){
				$sqlarr[] = 'FIND_IN_SET(' . $uid . ',Owners)';
			}
			$owners_sql = ' AND (RestrictOwners=0 OR (' . implode(' OR ', $sqlarr) . ')) ';
		}

		return $owners_sql;
	}

	function getSuccessor($answers){
		$answerID = $answers[0];
		$mySuccessorID = stripslashes($this->QASetAdditions[$this->defVersion]['successorID'][$answerID]);
		return $mySuccessorID;
	}

	function setSuccessor($answers){
		foreach($answers as &$answer){
			if($this->AllowSuccessors){
				$mySuccessorID = stripslashes($this->QASetAdditions[$this->defVersion]['successorID'][$answer]);
				if(is_numeric($mySuccessorID) && $mySuccessorID > 0){
					$GLOBALS['_we_voting_SuccessorID'] = $mySuccessorID;
				}
			}
		}
		if($this->AllowSuccessor){
			$mySuccessorID = $this->Successor;
			if(is_numeric($mySuccessorID) && $mySuccessorID > 0){
				$GLOBALS['_we_voting_SuccessorID'] = $mySuccessorID;
			}
		}
		return self::SUCCESS;
	}

	function vote($answers, $addfields = NULL){
		$votingsession = (isset($_SESSION['_we_voting_sessionID']) ? $_SESSION['_we_voting_sessionID'] : 0);
		$answerID = makeCSVFromArray($answers);
		$answertext = '';
		$successor = 0;
		if(!is_array($answers) || !(count($answers) > 0)){
			if($this->Log){
				$this->logVoting(self::ERROR, $votingsession, $answerID, $answertext, $successor);
			}
			return self::ERROR;
		}

		$ret = $this->canVote();

		if($ret != self::SUCCESS){
			if($this->Log){
				$this->logVoting($ret, $votingsession, $answerID, $answertext, $successor);
			}
			return $ret;
		}

		$countanswers = count($this->QASet[$this->defVersion]['answers']);
		$mySuccessorID = -1;
		foreach($answers as &$answer){
			if(is_numeric($answer) && $answer < $countanswers){
				if($answer > -1 && $answer < count($this->Scores)){
					$this->Scores[$answer] ++;
				}
			} else {
				$answertext = $answer;
				$answer = $countanswers - 1;
				$this->Scores[$answer] ++;
			}
			if($this->AllowSuccessors){
				$mySuccessorID = stripslashes($this->QASetAdditions[$this->defVersion]['successorID'][$answer]);
				if(is_numeric($mySuccessorID) && $mySuccessorID > 0){
					$GLOBALS['_we_voting_SuccessorID'] = $mySuccessorID;
				}
			}
		}
		if($this->AllowSuccessor){
			$mySuccessorID = $this->Successor;
			if(is_numeric($mySuccessorID) && $mySuccessorID > 0){
				$GLOBALS['_we_voting_SuccessorID'] = $mySuccessorID;
			}
		}
		$answerID = makeCSVFromArray($answers);
		$this->saveField('Scores', true);
		if($this->RevoteTime != 0){
			if($this->RevoteControl == 1){
				$revotetime = ($this->RevoteTime < 0 ?
						630720000 : //20 years
						$this->RevoteTime);
				setcookie(md5('_we_voting_' . $this->ID), time(), time() + $revotetime);
			} else {
				if(!is_array($this->Revote)){
					$this->Revote = array();
				}
				$this->Revote[$_SERVER['REMOTE_ADDR']] = time();
				$this->saveField('Revote', true);
				if($this->UserAgent){
					$this->RevoteUserAgent = unserialize($this->RevoteUserAgent);
					if(!is_array($this->RevoteUserAgent)){
						$this->RevoteUserAgent = array();
					}
					if(!isset($this->RevoteUserAgent[$_SERVER['REMOTE_ADDR']]) || !is_array($this->RevoteUserAgent[$_SERVER['REMOTE_ADDR']])){
						$this->RevoteUserAgent[$_SERVER['REMOTE_ADDR']] = array();
					}
					$this->RevoteUserAgent[$_SERVER['REMOTE_ADDR']][] = $_SERVER['HTTP_USER_AGENT'];
					$this->saveField('RevoteUserAgent', true);
				}
			}
		}
		if($mySuccessorID <= 0){
			$mySuccessorID = '';
		}
		$addfieldsdata = (is_array($addfields) && $addfields ? serialize($addfields) : '');

		if($this->Log){
			$this->logVoting($ret, $votingsession, $answerID, $answertext, $mySuccessorID, $addfieldsdata);
		}

		return $ret;
	}

	function canVote(){
		if(!$this->isActive()){
			return self::ERROR_ACTIVE;
		}
		if($this->isBlackIP()){
			return self::ERROR_BLACKIP;
		}
		if($this->RevoteTime == 0){
			return self::SUCCESS;
		}

		switch($this->RevoteControl){
			case 2:
				return $this->canVoteUserID();
			case 1:
				return $this->canVoteCookie();
			default:
				return $this->canVoteIP();
		}
	}

	function canVoteCookie(){
		if($this->cookieDisabled() && $this->FallbackIp){
			$this->RevoteControl = 0;
			$this->FallbackActive = 1;
			return $this->canVoteIP();
		}

		if(isset($_COOKIE[md5('_we_voting_' . $this->ID)])){
			return self::ERROR_REVOTE;
		}
		return ($this->FallbackUserID ? $this->canVoteUserID() : self::SUCCESS);
	}

	function canVoteIP(){

		$this->Revote = unserialize($this->Revote);
		if(!is_array($this->Revote)){
			$this->Revote = array();
		}

		$revotetime = ($this->RevoteTime < 0 ? time() + 5 : $this->RevoteTime);

		if(in_array($_SERVER['REMOTE_ADDR'], array_keys($this->Revote))){
			if(($revotetime + $this->Revote[$_SERVER['REMOTE_ADDR']]) > time()){
				$revoteua = unserialize($this->RevoteUserAgent);
				if($this->UserAgent){
					if(!is_array($revoteua)){
						return self::ERROR_REVOTE;
					}
					if(isset($revoteua[$_SERVER['REMOTE_ADDR']]) && is_array($revoteua[$_SERVER['REMOTE_ADDR']])){
						if(in_array($_SERVER['HTTP_USER_AGENT'], $revoteua[$_SERVER['REMOTE_ADDR']])){
							return self::ERROR_REVOTE;
						}
						return ($this->FallbackUserID ? $this->canVoteUserID() : self::SUCCESS);
					}
				}

				return self::ERROR_REVOTE;
			}
			return ($this->FallbackUserID ? $this->canVoteUserID() : self::SUCCESS);
		}
		return ($this->FallbackUserID ? $this->canVoteUserID() : self::SUCCESS);
	}

	function canVoteUserID(){
		$userid = (defined('CUSTOMER_TABLE') && isset($_SESSION['webuser']['registered']) && isset($_SESSION['webuser']['ID']) && $_SESSION['webuser']['registered'] && $_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : -1;

		if(!$this->LogDB || ($userid <= 0)){
			return self::SUCCESS;
		} else {
			$testtime = ($this->RevoteTime < 0 ? 0 : time() - $this->RevoteTime);

			if(f('SELECT 1 FROM `' . VOTING_LOG_TABLE . '` WHERE `' . VOTING_LOG_TABLE . '`.`voting` = ' . intval($this->ID) . ' AND `' . VOTING_LOG_TABLE . '`.`userid` = ' . intval($userid) . ' AND `' . VOTING_LOG_TABLE . '`.`time` > ' . intval($testtime) . ' LIMIT 1', '', $this->db)){
				return self::ERROR_REVOTE;
			}
			return self::SUCCESS;
		}
	}

	function isActive(){
		if(!$this->Active){
			return false;
		}
		if($this->ActiveTime == 0){
			return true;
		}
		if(time() > $this->Valid){
			return false;
		}
		return true;
	}

	function cookieDisabled(){
		return !(isset($_SESSION['_we_cookie_']));
	}

	function isBlackIP(){
		if(!$this->RestrictIP){
			return false;
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		foreach($this->BlackList as $fip){
			$reg = str_replace('*', '[0-9]+', $fip);
			if(preg_match('/' . $reg . '/', $ip)){
				return true;
			}
		}
		return false;
	}

	function resetIpData(){
		$this->Revote = '';
		$this->RevoteUserAgent = '';
		$this->saveField('Revote');
		$this->saveField('RevoteUserAgent');
	}

	function logVoting($status, $votingsession, $answer, $answertext, $successor, $additionalfields = ''){
		if($this->LogDB){
			$this->logVotingDB($status, $votingsession, $answer, $answertext, $successor, $additionalfields);
		} else {
			$this->LogData = unserialize($this->LogData);
			if(!is_array($this->LogData)){
				$this->LogData = array();
			}
			$this->LogData[] = array(
				'time' => time(),
				'ip' => $_SERVER['REMOTE_ADDR'],
				'agent' => $_SERVER['HTTP_USER_AGENT'],
				'cookie' => $this->cookieDisabled() ? 0 : 1,
				'fallback' => $this->FallbackActive,
				'status' => $status
			);
			$this->saveField('LogData', true);
		}
	}

	function deleteLogData(){
		if($this->IsFolder){
			$this->deleteGroupLogData();
			return true;
		} else {
			if($this->LogDB){
				$this->deleteLogDataDB();
				$this->LogData = '';
				$this->saveField('LogData', false);
			} else {
				$this->LogData = '';
				$this->saveField('LogData', false);
				$this->LogDB = true;
			}
			return true;
		}
	}

	function deleteGroupLogData(){
		$this->db->query('SELECT ID FROM ' . VOTING_TABLE . " WHERE `Path` LIKE '" . $this->db->escape($this->Path) . "%'");
		while($this->db->next_record()){
			$child = new we_voting_voting($this->db->f('ID'));
			$child->deleteLogDataDB();
		}
		return true;
	}

	/* ================================= NEW FUNCTIONS FOR LOGGING TO VOTING_LOG_TABLE ================================= */
	/**
	 * @abstract new functions for logging votings to a separate database table
	 * @internal new logging mode logs votings to a new table called "tblvotinglog" instead of saving them
	 * 			to the field "LogData" as a serialized array. So $this->LogData contains an array if the new mode
	 * 			is used (for newly created votings). For existing votings using the old logging style nothing has changed
	 * 			and the variable will still contain a string with the serialized logging data array from tblvoting.LogData
	 * 			In the first case the variable $this->LogDB will be set to true, else it will stay false
	 * @author Alexander Lindenstruth
	 * @since 5.1.1.2 - 05.05.2008
	 */

	/**
	 * fetches all log entries for current voting from database
	 * @author Alexander Lindenstruth
	 * @since 5.1.1.2 - 02.05.2008
	 */
	function loadDB($id = '0'){

		$logQuery = ($this->IsFolder ?
				'SELECT A.*, B.* FROM `' . VOTING_TABLE . '` A, `' . VOTING_LOG_TABLE . "` B WHERE A.Path LIKE '" . $this->db->escape($this->Path) . "%' AND A.IsFolder = '0' AND A.ID = B.voting ORDER BY B.time" :
				'SELECT * FROM `' . VOTING_LOG_TABLE . '` WHERE `' . VOTING_LOG_TABLE . '`.`voting` = ' . intval($id) . ' ORDER BY time'
			);

		$this->db->query($logQuery);
		$this->LogData = array();
		while($this->db->next_record()){
			$this->LogData[] = array('votingsession' => $this->db->f('votingsession'), 'voting' => $this->db->f('voting'),
				'time' => $this->db->f('time'),
				'ip' => $this->db->f('ip'),
				'agent' => $this->db->f('agent'),
				'userid' => $this->db->f('userid'),
				'cookie' => $this->db->f('cookie'),
				'fallback' => $this->db->f('fallback'),
				'status' => $this->db->f('status'),
				'answer' => $this->db->f('answer'),
				'answertext' => $this->db->f('answertext'),
				'successor' => $this->db->f('successor'),
				'additionalfields' => $this->db->f('additionalfields'),
			);
		}
		return $this->LogData;
	}

	/**
	 * writes a single entry to the voting log table VOTING_LOG_TABLE
	 * @return boolean success or failure of this operation
	 * @author Alexander Lindenstruth
	 * @since 5.1.1.2 - 02.05.2008
	 */
	function logVotingDB($status = NULL, $votingsession = NULL, $answer = NULL, $answertext = NULL, $successor = NULL, $additionalfields = NULL){
		if(is_null($status)){
			$status = 0;
		}
		if(is_null($votingsession)){
			$votingsession = 0;
		}
		if(is_null($answer)){
			$answer = -1;
		}
		if(is_null($answertext)){
			$answertext = '';
		}
		if(is_null($successor)){
			$successor = '';
		}
		if(is_null($additionalfields)){
			$additionalfields = '';
		}
		$_cookieStatus = $this->cookieDisabled() ? 0 : 1;
		$userid = (defined('CUSTOMER_TABLE') && isset($_SESSION["webuser"]["registered"]) && isset($_SESSION["webuser"]["ID"]) && $_SESSION["webuser"]["registered"] && $_SESSION["webuser"]["ID"] ?
				$_SESSION["webuser"]["ID"] : 0);
		$this->db->query('INSERT INTO `' . VOTING_LOG_TABLE . '` SET ' . we_database_base::arraySetter(array(
				'votingsession' => $votingsession,
				'voting' => $this->ID,
				'time' => sql_function('UNIX_TIMESTAMP()'),
				'ip' => $_SERVER['REMOTE_ADDR'],
				'agent' => $_SERVER['HTTP_USER_AGENT'],
				'userid' => $userid,
				'cookie' => $_cookieStatus,
				'fallback' => $this->FallbackActive,
				'answer' => $answer,
				'answertext' => $answertext,
				'successor' => $successor,
				'additionalfields' => $additionalfields,
				'status' => $status,
		)));

		return true;
	}

	/**
	 * deletes all log entries for a given voting
	 * @return boolean success or failure of this operation
	 * @author Alexander Lindenstruth
	 * @since 5.1.1.2 - 02.05.2008
	 */
	function deleteLogDataDB(){
		$this->db->query('DELETE FROM `' . VOTING_LOG_TABLE . '` WHERE `' . VOTING_LOG_TABLE . '`.`voting`=' . intval($this->ID));
		return true;
	}

	/**
	 * switches from storing LogData in table VOTING_TABLE to table VOTING_LOG_TABLE
	 * @return boolean success or failure of this operation
	 * @author Dr. Armin Schulz
	 * @since 6.1.0.2 - 019.09.2010
	 */
	function switchToLogDataDB(){

		$LogData = @unserialize($this->LogData);
		if(is_array($LogData) && !empty($LogData)){
			foreach($LogData as $ld){
				$this->db->query('INSERT INTO `' . VOTING_LOG_TABLE . '` SET ' . we_database_base::arraySetter(array(
						'votingsession' => '',
						'voting' => $this->ID,
						'time' => $ld['time'],
						'ip' => $ld['ip'],
						'agent' => $ld['agent'],
						'userid' => 0,
						'cookie' => $ld['cookie'],
						'fallback' => $ld['fallback'],
						'answer' => '',
						'answertext' => '',
						'successor' => '',
						'additionalfields' => '',
						'status' => $ld['status'],
				)));
			}
			$this->LogData = '';
			$this->saveField('LogData', false);
			$this->LogDB = true;
			return true;
		}
	}

}
