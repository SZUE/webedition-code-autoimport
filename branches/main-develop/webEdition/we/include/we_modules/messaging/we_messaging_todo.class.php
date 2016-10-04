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
we_base_moduleInfo::isActive(we_base_moduleInfo::MESSAGING);
/* todo object class */

class we_messaging_todo extends we_messaging_proto{
	/* Flag which is set when the file is not new */

	var $selected_message = [];
	var $selected_set = [];
	var $search_fields = array('m.headerSubject', 'm.headerCreator', 'm.MessageText');
	var $search_folder_ids = [];
	var $sortfield = 'm.headerDeadline';
	var $last_sortfield = '';
	var $sortorder = 'desc';
	var $ids_selected = [];
	var $available_folders = [];
	var $sql_class_nr = 2;
	var $Short_Description = 'webEdition TODO';
	var $table = MSG_TODO_TABLE;
	var $view_class = 'todo';
	var $sf2sqlfields = array(
		'm.headerSubject' => array('hdrs', 'Subject'),
		'm.headerDate' => array('hdrs', 'Date'),
		'm.headerDeadline' => array('hdrs', 'Deadline'),
		'm.headerCreator' => array('hdrs', 'Creator'),
		'm.seenStatus' => array('hdrs', 'seenStatus'),
		'm.MessageText' => array('body', 'MessageText')
	);
	var $so2sqlso = array(
		'desc' => 'asc',
		'asc' => 'desc');

	function __construct(){
		parent::__construct();
		$this->ClassName = 'we_todo';
		$this->Short_Description = g_l('modules_messaging', '[we_todo]');
		$this->Name = 'todo_' . md5(uniqid(__FILE__, true));
		$this->persistent_slots = array('ClassName', 'Name', 'ID', 'Folder_ID', 'selected_message', 'sortorder', 'last_sortfield', 'available_folders', 'search_folder_ids', 'search_fields', 'default_folders');
	}

	function init($sessDat = ''){
		$init_folders = [];

		if($sessDat){
			$this->initSessionDat($sessDat);
		}

		foreach($this->default_folders as $id => $fid){
			if($fid == -1){
				$init_folders[] = $id;
			}
		}

		if(!empty($init_folders)){
			$this->DB_WE->query('SELECT ID, obj_type FROM ' . MSG_FOLDERS_TABLE . ' WHERE UserID=' . intval($this->userid) . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND (obj_type=' . $this->DB_WE->escape(implode(' OR obj_type=', $init_folders)) . ')');
			while($this->DB_WE->next_record()){
				$this->default_folders[$this->DB_WE->f('obj_type')] = $this->DB_WE->f('ID');
			}
		}
	}

	function initSessionDat($sessDat){
		if($sessDat){
			/* move sizeof out of loop */
			foreach($this->persistent_slots as $pers){
				if(isset($sessDat[0][$pers])){
					$this->{$pers} = $sessDat[0][$pers];
				}
			}

			if(isset($sessDat[1])){
				$this->elements = $sessDat[1];
			}
		}
	}

	function saveInSession(&$save){
		$save = [];
		$save[0] = [];
		foreach($this->persistent_slots as $pers){
			$save[0][$pers] = $this->{$pers};
		}
		$save[1] = isset($this->elements) ? $this->elements : "";
	}

	/* Getters And Setters */

	function get_newmsg_count(){
		return intval(f('SELECT COUNT(1) FROM ' . $this->DB_WE->escape($this->table) . ' WHERE (seenStatus & ' . we_messaging_proto::STATUS_READ . '=0) AND obj_type=' . we_messaging_proto::TODO_NR . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND ParentID=' . $this->default_folders[we_messaging_proto::FOLDER_INBOX] . ' AND UserID=' . intval($this->userid), '', $this->DB_WE));
	}

	function get_count($folder_id){
		$cnt = f('SELECT COUNT(1) FROM ' . $this->DB_WE->escape($this->table) . ' WHERE ParentID=' . intval($folder_id) . ' AND obj_type=' . we_messaging_proto::TODO_NR . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND UserID=' . intval($this->userid), '', $this->DB_WE);
		return $cnt === '' ? -1 : $cnt;
	}

	function format_from_line($userid){
		$tmp = getHash('SELECT First, Second, username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userid), new DB_WE());
		return $tmp['First'] . ' ' . $tmp['Second'] . ' (' . $tmp['username'] . ')';
	}

	function create_folder($name, $parent, $aid = -1){
		return parent::create_folder($name, $parent, $aid);
	}

	/* get subtree starting with node $id */

	function &get_f_children($id){
		$this->DB_WE->query('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE ParentID=' . intval($id) . ' AND UserID=' . intval($this->userid));
		$fids = $this->DB_WE->getAll(true);

		foreach($fids as $fid){
			$fids = array_merge($fids, $this->get_f_children($fid));
		}

		return $fids;
	}

	function delete_items(&$i_headers){
		if(!$i_headers){
			return -1;
		}

		$ids = [];
		foreach($i_headers as $ih){
			$ids [] = intval($ih['_ID']);
		}

		$this->DB_WE->query('DELETE FROM ' . $this->DB_WE->escape($this->table) . ' WHERE ID IN (' . implode(',', $ids) . ') AND obj_type=' . we_messaging_proto::TODO_NR . ' AND UserID=' . intval($this->userid));
		return 1;
	}

	function history_update($id, $userid, $fromuserid, $comment, $action, $status = -1){
		return $this->DB_WE->query('INSERT INTO ' . MSG_TODOHISTORY_TABLE . ' SET ' .
						we_database_base::arraySetter(['ParentID' => $id,
							'UserID' => $userid,
							'fromUserID' => $fromuserid,
							'Comment' => $comment,
							'Created' => sql_function('UNIX_TIMESTAMP()'),
							'action' => $action,
							'status' => ($status < 0 ? sql_function('NULL') : $status)
							]));
	}

	function add_comment(){
		return ($this->history_update($id, $this->userid, $this->userid, $comment, we_messaging_proto::ACTION_COMMENT) == 1);
	}

	function update_status(&$data, &$msg, $userid = ''){
		if(empty($data)){
			return array(
				'changed' => 0,
				'msg' => g_l('modules_messaging', '[todo_no_changes]')
			);
		}

		if(empty($msg)){
			return array(
				'changed' => 0,
				'msg' => g_l('modules_messaging', '[todo_none_selected]')
			);
		}

		$set_query = [];

		$ret = array(
			'changed' => 0,
			'msg' => ''
		);

		if(isset($data['todo_comment'])){
			$userid = $userid? : $this->userid;

			//use current assignee instead of userid
			if($this->history_update($msg['_ID'], $userid, $userid, $data['todo_comment'], we_messaging_proto::ACTION_COMMENT)){
				$ret['msg'] = g_l('modules_messaging', '[update_successful]');
				$ret['changed'] = 1;
			} else {
				$ret['msg'] = g_l('modules_messaging', '[error_occured]');
				$ret['err'] = 1;
			}
		}

		if(isset($data['todo_status'])){
			if(!is_numeric($data['todo_status']) || ($data['todo_status'] < 0)){
				$ret['msg'] = g_l('modules_messaging', '[todo_status_inv_input]');
				$ret['err'] = 1;
				return $ret;
			}

			$set_query['headerStatus'] = $data['todo_status'];
			if($data['todo_status'] >= 100){
				if($this->default_folders[we_messaging_proto::FOLDER_DONE] < 0){
					$ret['msg'] = g_l('modules_messaging', '[todo_move_error]') . ': ' . g_l('modules_messaging', '[no_done_folder]');
					return $ret;
				}
				$set_query['ParentID'] = $this->default_folders[we_messaging_proto::FOLDER_DONE];
			} else {
				if(f('SELECT ParentID FROM ' . $this->DB_WE->escape($this->table) . ' WHERE ID=' . intval($msg['_ID']), 'ParentID', $this->DB_WE) == $this->default_folders[we_messaging_proto::FOLDER_DONE]){
					$set_query['ParentID'] = $this->default_folders[we_messaging_proto::FOLDER_INBOX];
				}
			}
		}

		if(isset($data['deadline'])){
			$set_query['headerDeadline'] = $data['deadline'];
		}

		if(isset($data['todo_priority'])){
			$set_query['Priority'] = $data['todo_priority'];
		}

		$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET ' . we_database_base::arraySetter($set_query) . ' WHERE ID=' . intval($msg['_ID']));
		$ret['msg'] = g_l('modules_messaging', '[update_successful]');
		$ret['changed'] = 1;
		$ret['err'] = 0;

		return $ret;
	}

	/* Forward is actually "reassign", so no copy is made */

	function forward(&$rcpts, &$data, &$msg){
		$results = [];
		$results['err'] = [];
		$results['ok'] = [];
		$results['failed'] = [];
		$in_folder = '';

		$rcpt = $rcpts[0];

		if(($userid = we_users_user::getUserID($rcpt, $this->DB_WE)) == -1){
			$results['err'][] = g_l('modules_messaging', '[username_not_found]');
			$results['failed'][] = $rcpt;
			return $results;
		}

		$id = f('SELECT ID FROM ' . $this->DB_WE->escape($this->table) . ' WHERE Properties=' . we_messaging_proto::TODO_PROP_IMMOVABLE . ' AND ID=' . intval($msg['int_hdrs']['_ID']), 'ID', $this->DB_WE);
		if($id == $msg['int_hdrs']['_ID']){
			$results['err'][] = g_l('modules_messaging', '[todo_no_forward]');
			$results['failed'][] = $this->userid;
			return $results;
		}

		$in_folder = f('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE obj_type=' . we_messaging_proto::FOLDER_INBOX . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND UserID=' . intval($userid), 'ID', $this->DB_WE);
		if(!$in_folder){
			$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
			$results['failed'][] = $rcpt;
			return $results;
		}

		if($this->history_update($msg['int_hdrs']['_ID'], $userid, $this->userid, $data['body'], we_messaging_proto::ACTION_FORWARD) == 1){
			$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . " SET ParentID=" . intval($in_folder) . ", UserID=" . intval($userid) . ', seenStatus=0, headerAssigner=' . intval($this->userid) . " WHERE ID=" . intval($msg['int_hdrs']['_ID']) . ' AND UserID=' . intval($this->userid));
			$results['ok'][] = $rcpt;
		} else {
			$results['err'][] = g_l('modules_messaging', '[todo_err_history_update]');
			$results['failed'][] = $rcpt;
		}

		return $results;
	}

	function reject(&$msg, &$data){
		$results = [];
		$results['err'] = [];
		$results['ok'] = [];
		$results['failed'] = [];


		$rej_folder = f('SELECT ID FROM ' . MSG_FOLDERS_TABLE . ' WHERE obj_type=' . we_messaging_proto::FOLDER_REJECT . ' AND UserID=' . intval($msg['int_hdrs']['_from_userid']), 'ID', $this->DB_WE);
		if(empty($rej_folder)){
			$results['err'][] = g_l('modules_messaging', '[no_reject_folder]');
			$results['failed'][] = we_users_user::getUsername($msg['int_hdrs']['_from_userid'], $this->DB_WE);
			return $results;
		}

		$tmpId = f('SELECT ID FROM ' . $this->DB_WE->escape($this->table) . ' WHERE Properties=' . we_messaging_proto::TODO_PROP_IMMOVABLE . ' AND ID=' . intval($msg['int_hdrs']['_ID']), 'ID', $this->DB_WE);
		if($tmpId == $msg['int_hdrs']['_ID']){
			$results['err'][] = g_l('modules_messaging', '[todo_no_reject]');
			$results['failed'][] = we_users_user::getUsername($msg['int_hdrs']['_from_userid'], $this->DB_WE);
			return $results;
		}

		$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET UserID=' . intval($msg['int_hdrs']['_from_userid']) . ', ParentID=' . intval($rej_folder) . ' WHERE ID=' . intval($msg['int_hdrs']['_ID']));
		$this->history_update($msg['int_hdrs']['_ID'], $msg['int_hdrs']['_from_userid'], $this->userid, $data['body'], we_messaging_proto::ACTION_REJECT);

		$results['err'][] = '';
		$results['ok'][] = we_users_user::getUsername($msg['int_hdrs']['_from_userid'], $this->DB_WE);

		return $results;
	}

	function clipboard_cut($items, $target_fid){
		if(empty($items)){
			return;
		}

		$id_str = 'ID IN (' . implode(',', $items) . ')';
		$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET ParentID=' . intval($target_fid) . ' WHERE (' . $this->DB_WE->escape($id_str) . ') AND UserID=' . intval($this->userid));

		return 1;
	}

	function clipboard_copy($items, $target_fid){
		$tmp_msgs = [];

		if(empty($items)){
			return;
		}

		foreach($items as $item){
			$row = getHash('SELECT msg_type, obj_type, headerDate, headerSubject, headerCreator, headerAssigner, headerStatus, headerDeadline, Priority, Content_Type, MessageText, seenStatus, tag FROM ' . $this->DB_WE->escape($this->table) . " WHERE ID=" . intval($item) . " AND UserID=" . intval($this->userid), $this->DB_WE);
			$tmp = array(
				'ParentID' => $target_fid,
				'UserID' => $this->userid,
				'msg_type' => $row('msg_type'),
				'obj_type' => $row('obj_type'),
				'headerDate' => $row['headerDate'] ? : sql_function('NULL'),
				'headerSubject' => $row['headerSubject'] ? : sql_function('NULL'),
				'headerCreator' => $row['headerCreator'] ? : sql_function('NULL'),
				'headerAssigner' => $row['headerAssigner'] ? : sql_function('NULL'),
				'headerStatus' => $row['headerStatus'] ? : sql_function('NULL'),
				'headerDeadline' => $row['headerDeadline'] ? : sql_function('NULL'),
				'Priority' => $row['Priority'] ? : sql_function('NULL'),
				'MessageText' => $row['MessageText'],
				'Content_Type' => $row['Content_Type'],
				'seenStatus' => intval($row['seenStatus']),
				'tag' => $row['tag'] ? : '',
			);

			$this->DB_WE->query('INSERT INTO ' . $this->DB_WE->escape($this->table) . ' ' . we_database_base::arraySetter($tmp));
		}

		return 1;
	}

	function send($rcpts, $data){
		$results = array(
			'err' => [],
			'ok' => [],
			'failed' => [],
		);
		$db = new DB_WE();

		foreach($rcpts as $rcpt){
			$in_folder = '';
			//FIXME: Put this out of the loop (the select statement)
			if(($userid = we_users_user::getUserID($rcpt, $db)) == -1){
				$results['err'][] = "Username '" . $rcpt . "' existiert nicht'";
				$results['failed'][] = $rcpt;
				continue;
			}

			$in_folder = f('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE obj_type=' . we_messaging_proto::FOLDER_INBOX . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND UserID=' . intval($userid), 'ID', $this->DB_WE);
			if(!$in_folder){
				/* Create default Folders for target user */
				if(we_messaging_messaging::createFolders($userid) == 1){
					$in_folder = f('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE obj_type=' . we_messaging_proto::FOLDER_INBOX . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND UserID=' . intval($userid), 'ID', $this->DB_WE);
					if(!$in_folder){
						$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
						$results['failed'][] = $rcpt;
						continue;
					}
				} else {
					$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
					$results['failed'][] = $rcpt;
					continue;
				}
			}

			$this->DB_WE->query('INSERT INTO ' . $this->DB_WE->escape($this->table) . ' SET ' . we_database_base::arraySetter(['ParentID' => intval($in_folder),
						'UserID' => intval($userid),
						'msg_type' => $this->sql_class_nr,
						'obj_type' => we_messaging_proto::TODO_NR,
						'headerDate' => sql_function('UNIX_TIMESTAMP()'),
						'headerSubject' => $data['subject'],
						'headerCreator' => intval(intval($this->userid) ? $this->userid : $userid),
						'headerStatus' => 0,
						'headerDeadline' => $data['deadline'],
						'Properties' => we_messaging_proto::TODO_PROP_NONE,
						'MessageText' => $data['body'],
						'seenStatus' => 0,
						'Priority' => $data['priority'] ? : sql_function('NULL'),
						'Content_Type' => !empty($data['Content_Type']) ? $data['Content_Type'] : sql_function('NULL')
						]));

			$results['id'] = $this->DB_WE->getInsertId();
			$results['ok'][] = $rcpt;
		}

		return $results;
	}

	function get_msg_set(&$criteria){
		$sfield_cond = '';

		if(isset($criteria['search_fields'])){

			$arr = array('hdrs', 'From');
			$sf_uoff = self::arr_offset_arraysearch($arr, $criteria['search_fields']);

			if($sf_uoff > -1){
				$sfield_cond .= 'u.username LIKE "%' . $this->DB_WE->escape($criteria['searchterm']) . '%" OR
				u.First LIKE "%' . $this->DB_WE->escape($criteria['searchterm']) . '%" OR
				u.Second LIKE "%' . $this->DB_WE->escape($criteria['searchterm']) . '%" OR ';

				unset($criteria['search_fields'][$sf_uoff]);
			}

			foreach($criteria['search_fields'] as $sf){
				$sfield_cond .= array_search($sf, $this->sf2sqlfields) . ' LIKE "%' . $this->DB_WE->escape($criteria['searchterm']) . '%" OR ';
			}

			$sfield_cond = substr($sfield_cond, 0, -4);

			$folders_cond = implode(' OR m.ParentID=', $criteria['search_folder_ids']);
		} else if(isset($criteria['folder_id'])){
			$folders_cond = $criteria['folder_id'];

			if($this->cached['sortfield'] != 1 || $this->cached['sortorder'] != 1){
				$this->init_sortstuff($criteria['folder_id']);
			}

			$this->Folder_ID = $criteria['folder_id'];
		}

		if(isset($criteria['message_ids'])){
			$message_ids_cond = implode(' OR m.ID=', $criteria['message_ids']);
		}

		$this->selected_set = [];
		$this->DB_WE->query('SELECT m.ID, m.ParentID, m.headerDeadline, m.headerSubject, m.headerCreator, m.Priority, m.seenStatus, m.headerStatus, u.username
		FROM ' . $this->table . ' as m, ' . USER_TABLE . ' as u
		WHERE ((m.msg_type=' . intval($this->sql_class_nr) . ' AND m.obj_type=' . we_messaging_proto::TODO_NR . ') ' . ($sfield_cond ? " AND ($sfield_cond)" : '') . ($folders_cond ? " AND (m.ParentID=$folders_cond)" : '') . ( (!isset($message_ids_cond) || !$message_ids_cond ) ? '' : " AND (m.ID=$message_ids_cond)") . ") AND m.UserID=" . $this->userid . " AND m.headerCreator=u.ID
		ORDER BY " . $this->sortfield . ' ' . $this->so2sqlso[$this->sortorder]);

		$i = isset($criteria['start_id']) ? $criteria['start_id'] + 1 : 0;

		$seen_ids = [];

		while($this->DB_WE->next_record()){
			if(!($this->DB_WE->f('seenStatus') & we_messaging_proto::STATUS_SEEN)){
				$seen_ids[] = $this->DB_WE->f('ID');
			}

			$this->selected_set[] = array('ID' => $i++,
				'hdrs' => array('Deadline' => $this->DB_WE->f('headerDeadline'),
					'Subject' => $this->DB_WE->f('headerSubject'),
					'Creator' => $this->DB_WE->f('username'),
					'Priority' => $this->DB_WE->f('Priority'),
					'seenStatus' => $this->DB_WE->f('seenStatus'),
					'status' => $this->DB_WE->f('headerStatus'),
					'ClassName' => $this->ClassName),
				'int_hdrs' => array('_from_userid' => $this->DB_WE->f('headerCreator'),
					'_ParentID' => $this->DB_WE->f('ParentID'),
					'_ID' => $this->DB_WE->f('ID')));
		}

		/* mark selected_set messages as seen */
		if($seen_ids){
			$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET seenStatus=(seenStatus | ' . we_messaging_proto::STATUS_SEEN . ') WHERE ID IN (' . implode(',', $seen_ids) . ') AND UserID=' . intval($this->userid));
		}

		return $this->selected_set;
	}

	function retrieve_items($int_hdrs){
		if(!$int_hdrs){
			return [];
		}

		$ids = [];
		foreach($int_hdrs as $ih){
			$ids[] = intval($ih['_ID']);
		}


		$this->DB_WE->query('SELECT m.ID,m.headerDate,m.headerSubject,m.headerCreator,m.headerAssigner,m.headerStatus,m.headerDeadline,m.Priority,m.MessageText,m.Content_Type,m.seenStatus,u.username,u.First,u.Second FROM ' . $this->DB_WE->escape($this->table) . ' AS m, ' . USER_TABLE . " AS u WHERE m.ID IN(" . implode(',', $ids) . ") AND u.ID=m.headerCreator AND m.UserID=" . intval($this->userid));

		$entries = $this->DB_WE->getAll();

		$read_ids = [];

		$ret = [];
		$i = 0;
		foreach($entries as $entry){
			if(!($entry['seenStatus'] && we_messaging_proto::STATUS_READ)){
				$read_ids[] = $entry['ID'];
			}

			$history = [];
			/* FIXME: get the ids; use one query outside of the loop; */
			$this->DB_WE->query('SELECT u.username,t.Comment,t.Created,t.action,t.fromUserID FROM ' . MSG_TODOHISTORY_TABLE . ' AS t, ' . USER_TABLE . ' AS u WHERE t.ParentID=' . $entry['ID'] . ' AND t.UserID=u.ID ORDER BY Created');
			while($this->DB_WE->next_record()){
				$history[] = array(
					'username' => $this->DB_WE->f('username'),
					'from_userid' => $this->DB_WE->f('fromUserID'),
					'date' => $this->DB_WE->f('Created'),
					'action' => $this->DB_WE->f('action'),
					'comment' => $this->DB_WE->f('Comment'));
			}

			$from = $entry['First'] . ' ' . $entry['Second'] . ' (' . $entry['username'] . ')';
			$ret[] = array(
				'ID' => $i++,
				'hdrs' => array(
					'Date' => $entry['headerDate'],
					'Deadline' => $entry['headerDeadline'],
					'Subject' => $entry['headerSubject'],
					'From' => $from,
					'Assigner' => !$entry['headerAssigner'] ? $from : $this->format_from_line($entry['headerAssigner']),
					'status' => $entry['headerStatus'],
					'Priority' => $entry['Priority'],
					'seenStatus' => $entry['seenStatus'],
					'Content_Type' => $entry['Content_Type'],
					'ClassName' => $this->ClassName
				),
				'int_hdrs' => array(
					'_from_userid' => $entry['headerCreator'],
					'_ID' => $entry['ID'],
					'_reply_to' => $entry['username']),
				'body' => array(
					'MessageText' => $entry['MessageText'],
					'History' => $history)
			);
		}

		if($read_ids){
			$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET seenStatus=(seenStatus | ' . we_messaging_proto::STATUS_READ . ') WHERE ID IN (' . implode(',', $read_ids) . ') AND UserID=' . $this->userid);
		}

		return $ret;
	}

}
