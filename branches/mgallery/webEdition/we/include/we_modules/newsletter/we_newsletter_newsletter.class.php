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
	protected $MediaLinks = array();

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
		$this->persistents = array(
			'ID' => we_base_request::INT,
			'ParentID' => we_base_request::INT,
			'Text' => we_base_request::STRING,
			'Path' => we_base_request::FILE,
			'Subject' => we_base_request::STRING,
			'Sender' => we_base_request::EMAIL,
			'Reply' => we_base_request::EMAIL,
			'Test' => we_base_request::EMAIL,
			'Step' => we_base_request::INT,
			'Offset' => we_base_request::INT,
			'IsFolder' => we_base_request::BOOL,
			'Charset' => we_base_request::STRING,
			'isEmbedImages' => we_base_request::BOOL,
		);
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
		$this->Path = ($this->Path ? : '/');
		$this->Subject = stripslashes($this->Subject);
		$this->groups = we_newsletter_group::__getAllGroups($newsletterID, $this->db);
		$this->blocks = we_newsletter_block::__getAllBlocks($newsletterID, $this->db);
		$this->Charset = $this->Charset ? : $GLOBALS['WE_BACKENDCHARSET'];
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
					unset($groups[$k]);
				}
			}
			$block->Groups = implode(',', $groups);
			$block->NewsletterID = $this->ID;
			$block->save();
			//#8199: thrown out all addslashes() and stripslashes() here!
		}

		$this->registerMediaLinks();
		$this->addLog('log_save_newsletter');
		return 0;
	}

	public function registerMediaLinks(){
		$this->unregisterMediaLinks();
		foreach($this->blocks as $block){
			switch($block->Type){
				case 6:
				case 1:
				case 0:
					if($block->LinkID){
						$this->MediaLinks[] = $block->LinkID;
					}
					break;
				case 5:
					if($block->Html){
						$this->MediaLinks = array_merge($this->MediaLinks, we_wysiwyg_editor::reparseInternalLinks($content));
					}
			}
		}

		$c = count($this->MediaLinks);
		for($i = 0; $i < $c; $i++){
			if(!$this->MediaLinks[$i] || !is_numeric($this->MediaLinks[$i])){
				unset($this->MediaLinks[$i]);
			}
		}

		// the following would be obsolete, when class was based on we_modelBase
		if(!empty($this->MediaLinks)){
			$whereType = 'AND ContentType IN ("' . we_base_ContentTypes::APPLICATION . '","' . we_base_ContentTypes::FLASH . '","' . we_base_ContentTypes::IMAGE . '","' . we_base_ContentTypes::QUICKTIME . '","' . we_base_ContentTypes::VIDEO . '")';
			$this->db->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE ID IN (' . implode(',', array_unique($this->MediaLinks)) . ') ' . $whereType);
			$this->MediaLinks = array();
			while($this->db->next_record()){
				$this->MediaLinks[] = $this->db->f('ID');
			}
		}

		foreach(array_unique($this->MediaLinks) as $remObj){
			$this->db->query('REPLACE INTO ' . FILELINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'ID' => $this->ID,
					'DocumentTable' => stripTblPrefix($this->table),
					'type' => 'media',
					'remObj' => $remObj,
					'remTable' => stripTblPrefix(FILE_TABLE),
					'position' => 0,
					'isTemp' => 0
			)));
		}
	}

	function unregisterMediaLinks(){
		$this->db->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . $this->db->escape(stripTblPrefix($this->table)) . '"  AND type="media"');
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
		$this->unregisterMediaLinks();

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
					if($k == $where){
						$temp[] = new we_newsletter_block();
					}
					$temp[] = $v;
				}
				$this->blocks = $temp;
			}
		} else {
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
				unset($this->blocks[$id]);
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
					unset($arr[$k]);
				}
			}
			$this->blocks[$bk]->Groups = implode(',', $arr);
		}
		unset($this->groups[$group]);
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
