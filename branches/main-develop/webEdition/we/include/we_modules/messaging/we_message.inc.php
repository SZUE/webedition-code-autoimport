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
include_once(WE_MESSAGING_MODULE_PATH . "messaging_std.inc.php");

/* message object class */

class we_message extends we_msg_proto{
	/*	 * ************************************************************** */
	/* Class Properties ********************************************* */
	/*	 * ************************************************************** */

	/* Name of the class => important for reconstructing the class from outside the class */

	var $ClassName = __CLASS__;
	/* In this array are all storagable class variables */
	var $persistent_slots = array();
	/* Name of the Object that was createt from this class */
	var $Name = '';

	/* ID from the database record */
	var $ID = 0;

	/* Database Object */
	var $DB_WE;

	/* Flag which is set when the file is not new */
	var $wasUpdate = 0;
	var $InWebEdition = false;
	var $selected_message = array();
	var $selected_set = array();
	var $search_fields = array('m.headerSubject', 'm.headerFrom', 'm.MessageText');
	var $search_folder_ids = array();
	var $sortfield = 'm.headerDate';
	var $last_sortfield = '';
	var $sortorder = 'desc';
	var $ids_selected = array();
	var $available_folders = array();
	var $sql_class_nr = 1;
	var $Short_Description = 'webEdition Message';
	var $view_class = 'message';
	var $sf2sqlfields = array('m.headerSubject' => array('hdrs', 'Subject'),
		'm.headerDate' => array('hdrs', 'Date'),
		'm.headerFrom' => array('hdrs', 'From'),
		'm.seenStatus' => array('hdrs', 'seenStatus'),
		'm.MessageText' => array('body', 'MessageText'));
	var $so2sqlso = array('desc' => 'asc',
		'asc' => 'desc');

	/* Constructor */

	function __construct(){
		$this->Name = 'message_' . md5(uniqid(rand()));
		array_push($this->persistent_slots, 'ClassName', 'Name', 'ID', 'Table', 'Folder_ID', 'selected_message', 'sortorder', 'last_sortfield', 'available_folders', 'search_folder_ids', 'search_fields');
		$this->DB = new DB_WE();
	}

	function init($sessDat = ''){
		$init_folders = array();

		if($sessDat){
			$this->initSessionDat($sessDat);
		}

		foreach($this->default_folders as $id => $fid)
			if($fid == -1){
				$init_folders[] = $id;
			}

		if(!empty($init_folders)){
			$this->DB->query('SELECT ID, obj_type FROM ' . MSG_FOLDERS_TABLE . ' WHERE UserID=' . intval($this->userid) . ' AND msg_type=' . $this->sql_class_nr . ' AND (obj_type=' . addslashes(join(' OR obj_type=', $init_folders)) . ')');
			while($this->DB->next_record()) {
				$this->default_folders[$this->DB->f('obj_type')] = $this->DB->f('ID');
			}
		}
	}

	//FIXME: put following 2 methods out of the class (same goes for we_todo.inc.php)
	/* Methods dealing with USER_TABLE and other userstuff */
	function userid_to_username($id){
		$db2 = new DB_WE();
		$user = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($id), 'username', $db2);
		return $user ? $user : g_l('modules_messaging', '[userid_not_found]');
	}

	function username_to_userid($username){
		$db2 = new DB_WE();
		$id = f('SELECT ID FROM ' . USER_TABLE . ' WHERE username="' . $db2->escape($username) . '"', 'ID', $db2);
		return ($id === '' ? -1 : $id);
	}

	/* Getters And Setters */

	function get_newmsg_count(){
		return intval(f('SELECT COUNT(1) AS c FROM ' . $this->table . ' WHERE (seenStatus & ' . we_msg_proto::STATUS_READ . '=0) AND obj_type = ' . we_msg_proto::MESSAGE_NR . ' AND msg_type = ' . intval($this->sql_class_nr) . ' AND ParentID = ' . $this->default_folders[we_msg_proto::FOLDER_INBOX] . ' AND UserID = ' . intval($this->userid), 'c', $this->DB));
	}

	function get_count($folder_id){
		return f('SELECT COUNT(1) AS c FROM ' . $this->table . ' WHERE ParentID = ' . intval($folder_id) . ' AND obj_type = ' . we_msg_proto::MESSAGE_NR . ' AND msg_type = ' . intval($this->sql_class_nr) . ' AND UserID = ' . intval($this->userid), 'c', $this->DB);
	}

	/* 	function get_userids_by_nick($nick){
	  $ret_ids = array();

	  $DB2 = new DB_WE();
	  $DB2->query('SELECT ID FROM ' . USER_TABLE . ' WHERE username LIKE "%' . $DB2->escape($nick) . '%" OR First LIKE "%' . $DB2->escape($nick) . '%" OR Second LIKE "%' . $DB2->escape($nick) . '%"');
	  while($DB2->next_record())
	  $ret_ids[] = $DB2->f('ID');

	  return $ret_ids;
	  } */

	function create_folder($name, $parent){
		return parent::create_folder($name, $parent);
	}

	function delete_items(&$i_headers){
		if(empty($i_headers)){
			return -1;
		}

		$cond = '';
		foreach($i_headers as $ih){
			$cond .= 'ID = ' . intval($ih['_ID']) . ' OR ';
		}

		$cond = substr($cond, 0, -4);

		$this->DB->query('DELETE FROM ' . $this->table . " WHERE ($cond) AND obj_type=" . we_msg_proto::MESSAGE_NR . " AND UserID=" . intval($this->userid));

		return 1;
	}

	function clipboard_cut($items, $target_fid){
		if(empty($items)){
			return;
		}
		foreach($items as $key => $val){
			$_items[$key] = intval($val);
		}
		$id_str = 'ID IN ( ' . implode(',', $_items) . ')';
		$this->DB->query('UPDATE ' . $this->table . ' SET ParentID=' . intval($target_fid) . ' WHERE (' . $id_str . ') AND UserID=' . intval($this->userid));

		return 1;
	}

	function clipboard_copy($items, $target_fid){
		$tmp_msgs = array();

		if(empty($items)){
			return;
		}

		foreach($items as $item){
			$tmp = array();
			$this->DB->query('SELECT ParentID, msg_type, obj_type, headerDate, headerSubject, headerUserID, headerFrom, Priority, MessageText, seenStatus, tag FROM ' . $this->table . " WHERE ID=" . intval($item) . " AND UserID=" . intval($this->userid));
			while($this->DB->next_record()) {
				$tmp['ParentID'] = isset($this->DB->Record['ParentID']) ? $this->DB->Record['ParentID'] : 'NULL';
				$tmp['msg_type'] = $this->DB->f('msg_type');
				$tmp['obj_type'] = $this->DB->f('obj_type');
				$tmp['headerDate'] = isset($this->DB->Record['headerDate']) ? $this->DB->Record['headerDate'] : 'NULL';
				$tmp['headerSubject'] = isset($this->DB->Record['headerSubject']) ? $this->DB->Record['headerSubject'] : 'NULL';
				$tmp['headerUserID'] = isset($this->DB->Record['headerUserID']) ? $this->DB->Record['headerUserID'] : 'NULL';
				$tmp['headerFrom'] = isset($this->DB->Record['headerFrom']) ? $this->DB->Record['headerFrom'] : 'NULL';
				$tmp['Priority'] = $this->DB->f('Priority');
				$tmp['MessageText'] = $this->DB->f('MessageText');
				$tmp['seenStatus'] = $this->DB->f('seenStatus');
				$tmp['tag'] = $this->DB->f('tag');
			}

			$this->DB->query('INSERT INTO ' . $this->DB->escape($this->table) . ' SET ' . we_database_base::arraySetter(array(
					'ParentID' => intval($target_fid),
					'UserID' => $this->userid,
					'msg_type' => $tmp['msg_type'],
					'obj_type' => $tmp['obj_type'],
					'headerDate' => $tmp['headerDate'],
					'headerSubject' => $tmp['headerSubject'],
					'headerUserID' => $tmp['headerUserID'],
					'headerFrom' => $tmp['headerFrom'],
					'Priority' => $tmp['Priority'],
					'MessageText' => $tmp['MessageText'],
					'seenStatus' => $tmp['seenStatus'],
					'tag' => $tmp['tag'],
				)));

			$pending_ids[] = $this->DB->getInsertId();
		}

		return 1;
	}

	function &send(&$rcpts, &$data){
		$results = array();
		$results['err'] = array();
		$results['ok'] = array();
		$results['failed'] = array();

		foreach($rcpts as $rcpt){
			$in_folder = '';
			//FIXME: Put this out of the loop
			if(($userid = $this->username_to_userid($rcpt)) == -1){
				$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
				$results['failed'][] = $rcpt;
				continue;
			}

			/* FIXME: replace this by default_folders[inbox] or something */
			$this->DB->query('SELECT ID FROM ' . $this->DB->escape($this->folder_tbl) . ' WHERE obj_type = ' . we_msg_proto::FOLDER_INBOX . ' AND msg_type = ' . intval($this->sql_class_nr) . ' AND UserID = ' . intval($userid));
			$this->DB->next_record();
			$in_folder = $this->DB->f('ID');
			if(!isset($in_folder) || $in_folder == ''){
				/* Create default Folders for target user */
				include_once(WE_MESSAGING_MODULE_PATH . "messaging_interfaces.inc.php");
				if(msg_create_folders($userid) == 1){
					$this->DB->query('SELECT ID FROM ' . $this->DB->escape($this->folder_tbl) . ' WHERE obj_type = ' . we_msg_proto::FOLDER_INBOX . ' AND msg_type = ' . intval($this->sql_class_nr) . ' AND UserID = ' . intval($userid));
					$this->DB->next_record();
					$in_folder = $this->DB->f('ID');
					if(!isset($in_folder) || $in_folder == ''){
						$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
						$results['failed'][] = $rcpt;
						continue;
					}
				} else{
					$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
					$results['failed'][] = $rcpt;
					continue;
				}
			}

			$this->DB->query('INSERT INTO ' . $this->DB->escape($this->table) . " (ParentID, UserID, msg_type, obj_type, headerDate, headerSubject, headerUserID, Priority, MessageText,seenStatus) VALUES (" . intval($in_folder) . ", " . intval($userid) . ', ' . $this->sql_class_nr . ', ' . we_msg_proto::MESSAGE_NR . ', UNIX_TIMESTAMP(NOW()), "' . $this->DB->escape(($data['subject'])) . '", ' . intval($this->userid) . ', 0, "' . $this->DB->escape($data['body']) . '", 0)');
			$results['ok'][] = $rcpt;
		}
		/* Copy sent message into 'Sent' Folder of the sender */
		if(!isset($this->default_folders[we_msg_proto::FOLDER_SENT]) || $this->default_folders[we_msg_proto::FOLDER_SENT] < 0){
			$this->DB->query('SELECT ID FROM ' . $this->DB->escape($this->folder_tbl) . ' WHERE obj_type = ' . we_msg_proto::FOLDER_SENT . ' AND msg_type = ' . $this->sql_class_nr . ' AND UserID = ' . intval($_SESSION["user"]["ID"]));
			$this->DB->next_record();
			$this->default_folders[we_msg_proto::FOLDER_SENT] = $this->DB->f('ID');
		}
		$to_str = join(', ', $rcpts);
		$this->DB->query('INSERT INTO ' . $this->DB->escape($this->table) . ' (ParentID, UserID, msg_type, obj_type, headerDate, headerSubject, headerUserID, headerTo, Priority, MessageText, seenStatus) VALUES (' . $this->default_folders[we_msg_proto::FOLDER_SENT] . ', ' . intval($this->userid) . ', ' . $this->sql_class_nr . ', ' . we_msg_proto::MESSAGE_NR . ', UNIX_TIMESTAMP(NOW()), "' . $this->DB->escape($data['subject']) . '", ' . intval($this->userid) . ', "' . $this->DB->escape(strlen($to_str) > 60 ? substr($to_str, 0, 60) . '...' : $to_str) . '", 0, "' . $this->DB->escape($data['body']) . '", 0)');

		return $results;
	}

	function get_msg_set(&$criteria){
		$sfield_cond = '';

		if(isset($criteria['search_fields'])){
			$arr = array('hdrs', 'From');
			$sf_uoff = arr_offset_arraysearch($arr, $criteria['search_fields']);

			if($sf_uoff > -1){
				$sfield_cond .= 'u.username LIKE "%' . addslashes($criteria['searchterm']) . '%" OR
		u.First LIKE "%' . addslashes($criteria['searchterm']) . '%" OR
		u.Second LIKE "%' . addslashes($criteria['searchterm']) . '%" OR ';

				array_splice($criteria['search_fields'], $sf_uoff, 1);
			}

			foreach($criteria['search_fields'] as $sf){
				$sfield_cond .= array_key_by_val($sf, $this->sf2sqlfields) . ' LIKE "%' . addslashes($criteria['searchterm']) . '%" OR ';
			}

			$sfield_cond = substr($sfield_cond, 0, -3);

			$folders_cond = join(' OR m.ParentID = ', $criteria['search_folder_ids']);
		} else if(isset($criteria['folder_id'])){

			$folders_cond = $criteria['folder_id'];

			if($this->cached['sortfield'] != 1 || $this->cached['sortorder'] != 1){
				$this->init_sortstuff($criteria['folder_id']);
			}

			$this->Folder_ID = $criteria['folder_id'];
		}

		if(isset($criteria['message_ids'])){
			$message_ids_cond = join(' OR m.ID = ', $criteria['message_ids']);
		}

		$this->selected_set = array();
		$query = 'SELECT m.ID, m.ParentID, m.headerDate, m.headerSubject, m.headerUserID, m.Priority, m.seenStatus, u.username
		FROM ' . $this->DB->escape($this->table) . ' as m, ' . USER_TABLE . ' as u
		WHERE ((m.msg_type = ' . $this->sql_class_nr . ' AND m.obj_type = ' . we_msg_proto::MESSAGE_NR . ') ' . ($sfield_cond == '' ? '' : " AND ($sfield_cond)") . ($folders_cond == '' ? '' : " AND (m.ParentID=$folders_cond)") . ( (!isset($message_ids_cond) || $message_ids_cond == '') ? '' : " AND (m.ID=$message_ids_cond)") . ") AND m.UserID=" . $this->userid . " AND m.headerUserID=u.ID
		ORDER BY " . $this->sortfield . ' ' . $this->so2sqlso[$this->sortorder];

		$this->DB->query($query);

		$i = isset($criteria['start_id']) ? $criteria['start_id'] + 1 : 0;

		$seen_ids = array();

		while($this->DB->next_record()) {
			if(!($this->DB->f('seenStatus') & we_msg_proto::STATUS_SEEN)){
				$seen_ids[] = $this->DB->f('ID');
			}

			$this->selected_set[] =
				array('ID' => $i++,
					'hdrs' => array('Date' => $this->DB->f('headerDate'),
						'Subject' => $this->DB->f('headerSubject'),
						'From' => $this->DB->f('username'),
						'Priority' => $this->DB->f('Priority'),
						'seenStatus' => $this->DB->f('seenStatus'),
						'ClassName' => $this->ClassName),
					'int_hdrs' => array('_from_userid' => $this->DB->f('headerUserID'),
						'_ParentID' => $this->DB->f('ParentID'),
						'_ClassName' => $this->ClassName,
						'_ID' => $this->DB->f('ID')));
		}

		/* mark selected_set messages as seen */
		if(!empty($seen_ids)){
			$query = 'UPDATE ' . $this->DB->escape($this->table) . ' SET seenStatus = (seenStatus | ' . we_msg_proto::STATUS_SEEN . ') WHERE (ID = ' . join(' OR ID = ', $seen_ids) . ') AND UserID = ' . $this->userid;
			$this->DB->query($query);
		}

		return $this->selected_set;
	}

	function &retrieve_items(&$int_hdrs){
		$ret = array();
		$i = 0;

		if(empty($int_hdrs)){
			return $ret;
		}

		$id_str = '';
		foreach($int_hdrs as $ih){
			$id_str .= 'm.ID = ' . addslashes($ih['_ID']);
		}

		$this->DB->query('SELECT m.ID, m.headerDate, m.headerSubject, m.headerUserID, m.headerTo, m.MessageText, m.seenStatus, u.username, u.First, u.Second FROM ' . $this->DB->escape($this->table) . " as m, " . USER_TABLE . " as u WHERE ($id_str) AND u.ID=m.headerUserID AND m.UserID=" . intval($this->userid));

		$read_ids = array();

		while($this->DB->next_record()) {
			if(!($this->DB->f('seenStatus') & we_msg_proto::STATUS_READ))
				$read_ids[] = $this->DB->f('ID');

			$ret[] = array('ID' => $i++,
				'hdrs' => array('Date' => $this->DB->f('headerDate'),
					'Subject' => $this->DB->f('headerSubject'),
					'From' => $this->DB->f('First') . ' ' . $this->DB->f('Second') . ' (' . $this->DB->f('username') . ')',
					'To' => $this->DB->f('headerTo'),
					'Priority' => $this->DB->f('Priority'),
					'seenStatus' => $this->DB->f('seenStatus'),
					'ClassName' => $this->ClassName),
				'int_hdrs' => array('_from_userid' => $this->DB->f('headerUserID'),
					'_ID' => $this->DB->f('ID'),
					'_reply_to' => $this->DB->f('username')),
				'body' => array('MessageText' => $this->DB->f('MessageText')));
		}

		if(!empty($read_ids)){
			$query = 'UPDATE ' . $this->table . ' SET seenStatus = (seenStatus | ' . we_msg_proto::STATUS_READ . ') WHERE ID IN (' . implode(', ', $read_ids) . ') AND UserID = ' . intval($this->userid);
			$this->DB->query($query);
		}

		return $ret;
	}

}
