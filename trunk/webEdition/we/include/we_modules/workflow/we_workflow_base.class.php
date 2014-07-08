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
 * Document Definition base class
 */
class we_workflow_base{

	var $uid;
	var $db;
	var $persistents = array();
	var $table = "";
	var $ClassName = __CLASS__;
	var $Log;

	function __construct(){
		$this->uid = 'wf_' . md5(uniqid(__FILE__, true));
		$this->db = new DB_WE();
		$this->Log = new we_workflow_log();
	}

	function load(){
		$tableInfo = $this->db->metadata($this->table);
		$this->db->query('SELECT * FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->ID));
		if($this->db->next_record()){
			foreach($tableInfo as $cur){
				$fieldName = $cur["name"];
				if(isset($this->persistents[$fieldName])){
					$this->$fieldName = $this->db->f($fieldName);
				}
			}
		}
	}

	function save(){
		$sets = $wheres = array();
		foreach(array_keys($this->persistents) as $val){
			if($val == "ID"){
				$wheres[] = $val . '=' . intval($this->{$val});
			} else {
				$sets[$val] = $this->{$val};
			}
		}
		$where = implode(',', $wheres);
		$set = we_database_base::arraySetter($sets);

		if($this->ID == 0){
			$this->db->query('INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . $set);
			# get ID #
			$this->ID = $this->db->getInsertId();
		} else {
			$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . $set . ' WHERE ' . $where);
		}
	}

	function delete(){
		if($this->ID){
			$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->ID));
			return true;
		}
		return false;
	}

	function sendMessage($userID, $subject, $description){
		$errs = array();
		$foo = f("SELECT username FROM " . USER_TABLE . " WHERE ID=" . intval($userID), "", $this->db);
		$rcpts = array($foo); /* user names */
		we_messaging_message::newMessage($rcpts, $subject, $description, $errs);
	}

	function sendMail($userID, $subject, $description){
		$foo = f('SELECT Email FROM ' . USER_TABLE . ' WHERE ID=' . intval($userID), "", $this->db);
		if($foo && we_check_email($foo)){
			$this_user = getHash('SELECT First,Second,Email FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION["user"]["ID"]), $this->db);
			we_mail($foo, correctUml($subject), $description, (isset($this_user["Email"]) && $this_user["Email"] ? $this_user["First"] . " " . $this_user["Second"] . " <" . $this_user["Email"] . ">" : ""));
		}
	}

	/* generate new ToDo */
	/* return the ID of the created ToDo, 0 on error */

	function sendTodo($userID, $subject, $description, $deadline){
		$errs = array();
		$foo = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userID), '', $this->db);
		$rcpts = array($foo); /* user names */
		$m = new we_messaging_todo();
		$m->set_login_data($_SESSION["user"]["ID"], isset($_SESSION["user"]["Name"]) ? $_SESSION["user"]["Name"] : "");
		$data = array('subject' => $subject, 'body' => $description, 'deadline' => $deadline, 'Content_Type' => 'html', 'priority' => 5);

		$res = $m->send($rcpts, $data);

		if($res['err']){
			$errs = $res['err'];
			return 0;
		}

		return $res['id'];
	}

	/* Mark ToDo as done */
	/* $id - value of the 'ID' field in MSG_TODO_TABLE */

	function doneTodo($id){
		$errs = '';
		$m = new we_messaging_todo();

		$i_headers = array('_ID' => $id);

		$userid = f('SELECT UserID FROM ' . MSG_TODO_TABLE . ' WHERE ID=' . intval($id), '', new DB_WE());

		$m->set_login_data($userid, isset($_SESSION["user"]["Name"]) ? $_SESSION["user"]["Name"] : "");
		$m->init();

		$data = array('todo_status' => 100);

		$res = $m->update_status($data, $i_headers, $userid);

		if(isset($res['msg'])){
			$errs = $res['msg'];
		}

		return ($res['err'] == 0);
	}

	/* remove ToDo */
	/* $id - value of the 'ID' field in MSG_TODO_TABLE */

	function removeTodo($id){
		$m = new we_messaging_todo();
		$m->set_login_data($_SESSION["user"]["ID"], isset($_SESSION["user"]["Name"]) ? $_SESSION["user"]["Name"] : "");

		$i_headers = array('_ID' => $id);

		return $m->delete_items($i_headers);
	}

	/* Mark ToDo as rejected */
	/* $id - value of the 'ID' field in MSG_TODO_TABLE */

	function rejectTodo($id){
		$m = new we_messaging_todo();
		$db = new DB_WE();
		$userid = f('SELECT UserID FROM ' . MSG_TODO_TABLE . ' WHERE ID=' . intval($id), '', $db);

		$m->set_login_data($userid, isset($_SESSION["user"]["Name"]) ? $_SESSION["user"]["Name"] : "");
		$m->init();

		$msg = array('int_hdrs' => array('_ID' => $id, '_from_userid' => $userid));
		$data = array('body' => '');

		$m->reject($msg, $data);
	}

}
