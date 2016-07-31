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
	var $persistents = [];
	var $table = "";
	var $ClassName = __CLASS__;

	function __construct(){
		$this->uid = 'wf_' . md5(uniqid(__FILE__, true));
		$this->db = new DB_WE();
	}

	function load(){
		$tableInfo = $this->db->metadata($this->table);
		$this->db->query('SELECT * FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->ID));
		if($this->db->next_record()){
			foreach($tableInfo as $cur){
				$fieldName = $cur['name'];
				if(isset($this->persistents[$fieldName])){
					$this->$fieldName = ($this->persistents[$fieldName] == we_base_request::INTLISTA ?
							($this->db->f($fieldName) === '' ?
								[] :
								explode(',', trim($this->db->f($fieldName), ','))) :
							$this->db->f($fieldName));
				}
			}
		}
	}

	function save(){
		$sets = $wheres = [];
		foreach(array_keys($this->persistents) as $val){
			if($val === 'ID'){
				$wheres[] = 'ID=' . intval($this->{$val});
			} else {
				$sets[$val] = is_array($this->{$val}) ? implode(',', array_unique($this->{$val})) : $this->{$val};
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
		$errs = [];
		$rcpts = array(f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userID), '', $this->db));
		we_messaging_message::newMessage($rcpts, $subject, $description, $errs);
	}

	function sendMail($userID, $subject, $description){
		$foo = f('SELECT Email FROM ' . USER_TABLE . ' WHERE ID=' . intval($userID), "", $this->db);
		if($foo && we_check_email($foo)){
			$this_user = getHash('SELECT First,Second,Email FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION['user']["ID"]), $this->db);
			we_mail($foo, correctUml($subject), $description, '', (!empty($this_user["Email"]) ? $this_user["First"] . " " . $this_user["Second"] . " <" . $this_user["Email"] . ">" : ""));
		}
	}

	/* generate new To Do */
	/* return the ID of the created To Do, 0 on error */

	function sendTodo($userID, $subject, $description, $deadline){
		$errs = [];
		$foo = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userID), '', $this->db);
		$rcpts = array($foo); /* user names */
		$m = new we_messaging_todo();
		$m->set_login_data($_SESSION['user']["ID"], isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : "");
		$data = array('subject' => $subject, 'body' => $description, 'deadline' => $deadline, 'Content_Type' => 'html', 'priority' => 5);

		$res = $m->send($rcpts, $data);

		if($res['err']){
			$errs = $res['err'];
			return 0;
		}

		return $res['id'];
	}

	/* Mark To Do as done */
	/* $id - value of the 'ID' field in MSG_TODO_TABLE */

	function doneTodo($id = 0){
		$errs = '';
		$m = new we_messaging_todo();

		$i_headers = array('_ID' => $id);

		$userid = f('SELECT UserID FROM ' . MSG_TODO_TABLE . ' WHERE ID=' . intval($id), '', new DB_WE());

		$m->set_login_data($userid, isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : "");
		$m->init();

		$data = array('todo_status' => 100);

		$res = $m->update_status($data, $i_headers, $userid);

		if(isset($res['msg'])){
			$errs = $res['msg'];
		}

		return (!isset($res['err']) || $res['err'] == 0);
	}

	/* remove To Do */
	/* $id - value of the 'ID' field in MSG_TODO_TABLE */

	function removeTodo($id = 0){
		$m = new we_messaging_todo();
		$m->set_login_data($_SESSION['user']["ID"], isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : "");

		$i_headers = array('_ID' => $id);

		return $m->delete_items($i_headers);
	}

	/* Mark To Do as rejected */
	/* $id - value of the 'ID' field in MSG_TODO_TABLE */

	function rejectTodo($id = 0){
		$m = new we_messaging_todo();
		$db = new DB_WE();
		$userid = f('SELECT UserID FROM ' . MSG_TODO_TABLE . ' WHERE ID=' . intval($id), '', $db);

		$m->set_login_data($userid, isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : "");
		$m->init();

		$msg = array('int_hdrs' => array('_ID' => $id, '_from_userid' => $userid));
		$data = array('body' => '');

		$m->reject($msg, $data);
	}

}
