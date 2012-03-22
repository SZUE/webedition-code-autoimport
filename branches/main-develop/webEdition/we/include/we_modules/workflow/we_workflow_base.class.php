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
include_once(WE_MESSAGING_MODULE_PATH . "messaging_interfaces.inc.php");

/**
 * Document Definition base class
 */
class we_workflow_base{

	var $uid;
	var $db;
	var $persistents = array();
	var $table = "";
	var $ClassName=__CLASS__;
	var $Log;

	function __construct(){
		$this->uid = "wf_" . md5(uniqid(rand()));
		$this->db = new DB_WE();
		$this->Log = new we_workflow_log();
	}

	function load(){
		$tableInfo = $this->db->metadata($this->table);
		$this->db->query("SELECT * FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->ID));
		if($this->db->next_record())
			for($i = 0; $i < sizeof($tableInfo); $i++){
				$fieldName = $tableInfo[$i]["name"];
				if(in_array($fieldName, $this->persistents)){
					$foo = $this->db->f($fieldName);
					$this->$fieldName = $foo;
				}
			}
	}

	function save(){
		$sets = array();
		$wheres = array();
		foreach($this->persistents as $key => $val){
			//FIXME: remove eval
			if($val == "ID")
				eval('$wheres[]="' . $val . '=\'".$this->' . $val . '."\'";');
			eval('$sets[]="' . $val . '=\'".$this->' . $val . '."\'";');
		}
		$where = implode(",", $wheres);
		$set = implode(",", $sets);

		if($this->ID == 0){

			$query = 'INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . $set;
			$this->db->query($query);
			# get ID #
			$this->db->query("SELECT LAST_INSERT_ID()");
			$this->db->next_record();
			$this->ID = $this->db->f(0);
		} else{
			$query = 'UPDATE ' . $this->db->escape($this->table) . ' SET ' . $set . ' WHERE ' . $where;
			$this->db->query($query);
		}
	}

	function delete(){
		if($this->ID){
			$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->ID));
			return true;
		}
		else
			return false;
	}

	function sendMessage($userID, $subject, $description){
		$errs = array();
		$foo = f("SELECT username FROM " . USER_TABLE . " WHERE ID=" . intval($userID), "username", $this->db);
		$rcpts = array($foo); /* user names */
		$res = msg_new_message($rcpts, $subject, $description, $errs);
	}

	function sendMail($userID, $subject, $description, $contecttype='text/plain'){
		$errs = array();
		$foo = f("SELECT Email FROM " . USER_TABLE . " WHERE ID=" . intval($userID), "Email", $this->db);
		if(!empty($foo) && we_check_email($foo)){
			$this_user = getHash("SELECT First,Second,Email FROM " . USER_TABLE . " WHERE ID=" . intval($_SESSION["user"]["ID"]), $this->db);
			we_mail($foo, correctUml($subject), $description, (isset($this_user["Email"]) && $this_user["Email"] != "" ? $this_user["First"] . " " . $this_user["Second"] . " <" . $this_user["Email"] . ">" : ""));
		}
	}

	function sendTodo($userID, $subject, $description, $deadline){
		$errs = array();
		$foo = f("SELECT username FROM " . USER_TABLE . " WHERE ID=" . intval($userID), "username", $this->db);
		$rcpts = array($foo); /* user names */
		return msg_new_todo($rcpts, $subject, $description, $errs, "html", $deadline);
	}

	function doneTodo($id){
		$errs = "";
		return msg_done_todo($id, $errs);
	}

	function removeTodo($id){
		return msg_rm_todo($id);
	}

	function rejectTodo($id){
		return msg_reject_todo($id);
	}

}

?>