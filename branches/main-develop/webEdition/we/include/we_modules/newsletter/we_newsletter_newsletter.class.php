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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * General Definition of WebEdition Newsletter
 *
 */
class we_newsletter_newsletter extends we_newsletter_base{

	const SAVE_PATH_NOK = -10;
	const MALFORMED_SENDER = -1;
	const MALFORMED_REPLY = -2;
	const MALFORMED_TEST = -3;
	const OP_EQ = 0;
	const OP_NEQ = 1;
	const OP_LE = 2;
	const OP_LEQ = 3;
	const OP_GE = 4;
	const OP_GEQ = 5;
	const OP_LIKE = 6;
	const OP_CONTAINS = 7;
	const OP_STARTS = 8;
	const OP_ENDS = 9;

	//properties
	var $ID = 0;
	var $ParentID = 0;
	var $IsFolder = 0;
	var $Text = '';
	var $Path = '/';
	var $Icon = 'newsletter.gif';
	var $Subject = '';
	var $Sender = '';
	var $Reply = '';
	var $Attachments = '';
	var $Customers = '';
	var $Emails = '';
	var $Test = '';
	var $Step = 0;
	var $Offset = 0;
	var $Charset = '';
	var $log = array();
	var $blocks = array();
	var $groups = array();
	var $isEmbedImages = '';

	/**
	 * Default Constructor
	 * Can load or create new Newsletter depends of parameter
	 *
	 * @param int $newsletterID
	 * @return we_newsletter_newsletter
	 */
	function __construct($newsletterID = 0){
		parent::__construct();

		$this->table = NEWSLETTER_TABLE;
		array_push($this->persistents, 'ID', 'ParentID', 'Text', 'Path', 'Icon', 'Subject', 'Sender', 'Reply', 'Test', 'Step', 'Offset', 'IsFolder', 'Charset', 'isEmbedImages');
		$this->Charset = $GLOBALS['WE_BACKENDCHARSET'];

		$this->addBlock();
		$this->addGroup();
		$this->ID = $newsletterID;

		if($newsletterID){
			$this->load($newsletterID);
		}
	}

	/**
	 * get newsletter from database
	 *
	 * @param int $newsletterID
	 */
	function load($newsletterID){
		parent::load($newsletterID);
		$this->Text = stripslashes($this->Text);
		$this->Path = ($this->Path ? $this->Path : '/');
		$this->Subject = stripslashes($this->Subject);
		$this->groups = we_newsletter_group::__getAllGroups($newsletterID, $this->db);
		$this->blocks = we_newsletter_block::__getAllBlocks($newsletterID, $this->db);
		if(empty($this->Charset)){
			$this->Charset = $GLOBALS['WE_BACKENDCHARSET'];
		}
	}

	/**
	 * save newsletter in db
	 *
	 * @param string $message
	 * @param bool $check
	 * @return int
	 */
	function save(&$message, $check = true){
		//check addesses
		if($check && ($ret = $this->checkEmails($message)) != 0){
			return $ret;
		}

		if(!$this->checkParents($this->ParentID)){
			$message = g_l('modules_newsletter', '[path_nok]');
			return self::SAVE_PATH_NOK;
		}

		if($this->IsFolder){
			$this->Icon = we_base_ContentTypes::FOLDER_ICON;
			$this->fixChildsPaths();
		}

		if($this->Step != 0 || $this->Offset != 0){
			$this->addLog('log_campagne_reset');
		}
		$this->Step = 0;
		$this->Offset = 0;

		parent::save();


		$this->db->query('DELETE FROM ' . NEWSLETTER_GROUP_TABLE . ' WHERE NewsletterID=' . intval($this->ID));
		$this->db->query('DELETE FROM ' . NEWSLETTER_BLOCK_TABLE . ' WHERE NewsletterID=' . intval($this->ID));

		foreach($this->groups as $group){
			$group->NewsletterID = $this->ID;
			$group->save();
		}
		$count_group = count($this->groups);
		$groups = array();
		foreach($this->blocks as $block){
			$groups = makeArrayFromCSV($block->Groups);
			foreach($groups as $k => $v){
				if($v > $count_group){
					array_splice($groups, $k);
				}
			}
			$block->Groups = makeCSVFromArray($groups);
			$block->NewsletterID = $this->ID;
			$block->Source = addslashes($block->Source);
			$block->Html = addslashes($block->Html);
			$block->save();
			$block->Source = stripslashes($block->Source);
			$block->Html = stripslashes($block->Html);
			/* TODO: 
			 * Why addslashes() before saving to the database?
			 * Was it once intended to prevent strip_tags() in weNewslettrView::getContent() from compeletely deleting links 
			 * when converting html-content to plain-text?
			 * => see weNewslettrView::getContent(): links are deleted when converting html-content to plain-text on line 2103ff,
			 * but href's (and img-sources) should be preserved (and converted from internal to external links).
			 * 
			 * As a fast fix for wrong escaped href's (bug #8199) I added stripslashes($content); to weNewslettrView::getContent()
			 * in case weNewsletterBlock::TEXT.
			 * 
			 * Check this and throw out addsllashes() later.
			 */
		}

		$this->addLog('log_save_newsletter');
		return 0;
	}

	/**
	 * delete newsletter from database
	 *
	 * @return bool
	 */
	function delete(){

		if($this->IsFolder){
			$this->deleteChilds();
		}
		foreach($this->blocks as $block){
			$block->delete();
			$block = new we_newsletter_block();
		}
		foreach($this->groups as $group){
			$group->delete();
			$group = new we_newsletter_group();
		}
		$this->clearLog();
		parent::delete();
		return true;
	}

	/**
	 * delete childs from database
	 *
	 */
	function deleteChilds(){
		$this->db->query('SELECT ID FROM ' . NEWSLETTER_TABLE . ' WHERE ParentID=' . intval($this->ID));
		$ids = $this->db->getAll(true);
		foreach($ids as $id){
			$child = new self($id);
			$child->delete();
			$child = new self();
		}
	}

	/**
	 * gets all newsletter names from database
	 *
	 * gets all newsletter names from databes and returns them as an associated array with id as key and name as value
	 *
	 * @return array
	 */
	function getAllNewsletter(){
		$db = new DB_WE();

		$db->query('SELECT ID,Text FROM ' . NEWSLETTER_TABLE . ' ORDER BY ID');
		return $db->getAllFirst(false);
	}

	/**
	 * add a new block to a newsletter
	 *
	 * @param string $where
	 */
	function addBlock($where = -1){
		if($where != -1){
			if($where > count($this->blocks) - 1){
				$this->blocks[] = new we_newsletter_block();
			} else {
				$temp = array();
				foreach($this->blocks as $k => $v){
					if($k == $where)
						$temp[] = new we_newsletter_block();
					$temp[] = $v;
				}
				$this->blocks = $temp;
			}
		}else {
			$this->blocks[] = new we_newsletter_block();
		}
	}

	/**
	 * remove newsletters block
	 *
	 * @param int $id
	 */
	function removeBlock($id){
		foreach($this->blocks as $k => $v){
			if($id == $k){
				$v->delete();
				$v = new we_newsletter_block();
				array_splice($this->blocks, $id, 1);
			}
		}
	}

	/**
	 * add new group to newsletter
	 *
	 */
	function addGroup(){
		$this->groups[] = new we_newsletter_group();
	}

	/**
	 * remove newsletter group
	 *
	 * @param int $group
	 */
	function removeGroup($group){
		$link = $group + 1;
		foreach($this->blocks as $bk => $block){
			$arr = makeArrayFromCSV($block->Groups);
			foreach($arr as $k => $v){
				if($v == $link){
					$arr[$k] = -1;
				}
				if($v > $link){
					$arr[$k] = $v - 1;
				}
			}
			foreach($arr as $k => $v){
				if($v == -1){
					array_splice($arr, $k, 1);
				}
			}
			$this->blocks[$bk]->Groups = makeCSVFromArray($arr, true);
		}
		array_splice($this->groups, $group, 1);
	}

	/**
	 * check email syntax
	 *
	 * @param string $malformed
	 * @return int
	 */
	function checkEmails(&$malformed){

		if(!$this->check_email($this->Sender)){
			$malformed = $this->Sender;
			return self::MALFORMED_SENDER;
		}
		if(!$this->check_email($this->Reply)){
			$malformed = $this->Reply;
			return self::MALFORMED_REPLY;
		}
		if(!$this->check_email($this->Test)){
			$malformed = $this->Test;
			return self::MALFORMED_TEST;
		}

		foreach($this->groups as $k => $v){
			if(($ret = $v->checkEmails($k + 1, $malformed)) != 0){
				return $ret;
			}
		}
		return 0;
	}

	/**
	 * set log in db
	 *
	 * @param string $log
	 * @param string $param
	 */
	function addLog($log, $param = ''){
		$this->db->query('INSERT INTO ' . NEWSLETTER_LOG_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'NewsletterID' => $this->ID,
				'LogTime' => sql_function('UNIX_TIMESTAMP()'),
				'Log' => $log,
				'Param' => $param
		)));
	}

	/**
	 * clear log in db
	 *
	 */
	function clearLog(){
		$this->db->query('DELETE FROM ' . NEWSLETTER_LOG_TABLE . ' WHERE NewsletterID=' . intval($this->ID));
	}

	/**
	 * checks recursive the parents to detect if path is ok or not
	 *
	 * @param int $id
	 * @return bool
	 */
	function checkParents($id){
		$count = 0;
		while($id > 0){
			if($count > 1000){
				break;
			}
			if($id == $this->ID){
				return false;
			}
			$h = getHash('SELECT IsFolder,ParentID FROM ' . NEWSLETTER_TABLE . ' WHERE ID=' . $id, $this->db);
			if($h['IsFolder'] != 1){
				return false;
			}
			$id = $h['ParentID'];
			$count++;
		}
		return true;
	}

	/**
	 * fix childs path if parets changes
	 *
	 */
	function fixChildsPaths(){
		$oldpath = f('SELECT Path FROM ' . NEWSLETTER_TABLE . ' WHERE ID=' . intval($this->ID), 'Path', $this->db);

		if(trim($oldpath) != '' && trim($oldpath) != '/'){
			$this->db->query('UPDATE ' . NEWSLETTER_TABLE . ' SET Path=REPLACE(Path,"' . $this->db->escape($oldpath) . '","' . $this->db->escape($this->Path) . '") WHERE Path LIKE "' . $this->db->escape($oldpath) . '%"');
		}
	}

	/**
	 * checks if filename is well formated
	 *
	 * @return bool
	 */
	function filenameNotValid(){
		return preg_match('|[^a-z0-9\ \._\-]|i', $this->Text);
	}

}