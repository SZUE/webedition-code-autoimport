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
/* message object class */

class we_messaging_message extends we_messaging_proto{
	/* Flag which is set when the file is not new */
	var $selected_message = [];
	var $selected_set = [];
	var $search_fields = array('m.headerSubject', 'm.headerFrom', 'm.MessageText');
	var $search_folder_ids = [];
	var $sortfield = 'm.headerDate';
	var $last_sortfield = '';
	var $sortorder = 'desc';
	var $ids_selected = [];
	var $available_folders = [];
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
	protected $obj_type = we_messaging_proto::MESSAGE_NR;

	/* Constructor */

	function __construct(){
		parent::__construct();
		$this->ClassName = 'we_message';
		$this->Name = 'message_' . md5(uniqid(__FILE__, true));
		$this->persistent_slots = array('ClassName', 'Name', 'ID', 'Table', 'Folder_ID', 'selected_message', 'sortorder', 'last_sortfield', 'available_folders', 'search_folder_ids', 'search_fields');
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

		if($init_folders){
			$this->DB_WE->query('SELECT ID, obj_type FROM ' . MSG_FOLDERS_TABLE . ' WHERE UserID=' . intval($this->userid) . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND (obj_type=' . $this->DB_WE->escape(implode(' OR obj_type=', $init_folders)) . ')');
			while($this->DB_WE->next_record()){
				$this->default_folders[$this->DB_WE->f('obj_type')] = $this->DB_WE->f('ID');
			}
		}
	}

	function clipboard_cut($items, $target_fid){
		if(!$items){
			return;
		}
		foreach($items as $key => $val){
			$items[$key] = intval($val);
		}
		$id_str = 'ID IN ( ' . implode(',', $items) . ')';
		$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET ParentID=' . intval($target_fid) . ' WHERE (' . $id_str . ') AND UserID=' . intval($this->userid));

		return 1;
	}

	function clipboard_copy($items, $target_fid){
		if(empty($items)){
			return;
		}

		foreach($items as $item){
			$tmp = [];
			$this->DB_WE->query('SELECT ParentID, msg_type, obj_type, headerDate, headerSubject, headerUserID, headerFrom, Priority, MessageText, seenStatus, tag FROM ' . $this->DB_WE->escape($this->table) . ' WHERE ID=' . intval($item) . ' AND UserID=' . intval($this->userid));
			while($this->DB_WE->next_record()){
				$tmp['ParentID'] = isset($this->DB_WE->Record['ParentID']) ? $this->DB_WE->Record['ParentID'] : 'NULL';
				$tmp['msg_type'] = $this->DB_WE->f('msg_type');
				$tmp['obj_type'] = $this->DB_WE->f('obj_type');
				$tmp['headerDate'] = isset($this->DB_WE->Record['headerDate']) ? $this->DB_WE->Record['headerDate'] : 'NULL';
				$tmp['headerSubject'] = isset($this->DB_WE->Record['headerSubject']) ? $this->DB_WE->Record['headerSubject'] : 'NULL';
				$tmp['headerUserID'] = isset($this->DB_WE->Record['headerUserID']) ? $this->DB_WE->Record['headerUserID'] : 'NULL';
				$tmp['headerFrom'] = isset($this->DB_WE->Record['headerFrom']) ? $this->DB_WE->Record['headerFrom'] : 'NULL';
				$tmp['Priority'] = $this->DB_WE->f('Priority');
				$tmp['MessageText'] = $this->DB_WE->f('MessageText');
				$tmp['seenStatus'] = $this->DB_WE->f('seenStatus');
				$tmp['tag'] = $this->DB_WE->f('tag');
			}

			$this->DB_WE->query('INSERT INTO ' . $this->DB_WE->escape($this->table) . ' SET ' . we_database_base::arraySetter(array(
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

			//$pending_ids[] = $this->DB_WE->getInsertId();
		}

		return 1;
	}

	function send(&$rcpts, &$data){
		$results = array(
			'err' => [],
			'ok' => [],
			'failed' => [],
		);
		$db = new DB_WE();

		foreach($rcpts as $rcpt){
			//FIXME: Put this out of the loop
			if(($userid = we_users_user::getUserID($rcpt, $db)) == -1){
				$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
				$results['failed'][] = $rcpt;
				continue;
			}

			/* FIXME: replace this by default_folders[inbox] or something */
			$in_folder = f('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE obj_type=' . we_messaging_proto::FOLDER_INBOX . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND UserID = ' . intval($userid), '', $this->DB_WE);
			if(!$in_folder){
				/* Create default Folders for target user */
				if(we_messaging_messaging::createFolders($userid) == 1){
					$this->DB_WE->query('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE obj_type=' . we_messaging_proto::FOLDER_INBOX . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND UserID = ' . intval($userid));
					$this->DB_WE->next_record();
					$in_folder = $this->DB_WE->f('ID');
					if(!isset($in_folder) || !$in_folder){
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

			$this->DB_WE->query('INSERT INTO ' . $this->DB_WE->escape($this->table) . " (ParentID, UserID, msg_type, obj_type, headerDate, headerSubject, headerUserID, Priority, MessageText,seenStatus) VALUES (" . intval($in_folder) . ", " . intval($userid) . ', ' . intval($this->sql_class_nr) . ', ' . we_messaging_proto::MESSAGE_NR . ', UNIX_TIMESTAMP(NOW()), "' . $this->DB_WE->escape(($data['subject'])) . '", ' . intval($this->userid) . ', 0, "' . $this->DB_WE->escape($data['body']) . '", 0)');
			$results['ok'][] = $rcpt;
		}
		/* Copy sent message into 'Sent' Folder of the sender */
		if(!isset($this->default_folders[we_messaging_proto::FOLDER_SENT]) || $this->default_folders[we_messaging_proto::FOLDER_SENT] < 0){
			$this->default_folders[we_messaging_proto::FOLDER_SENT] = f('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE obj_type = ' . we_messaging_proto::FOLDER_SENT . ' AND msg_type = ' . intval($this->sql_class_nr) . ' AND UserID = ' . intval($_SESSION['user']["ID"]), 'ID', $this->DB_WE);
		}
		$to_str = implode(', ', $rcpts);
		$this->DB_WE->query('INSERT INTO ' . $this->DB_WE->escape($this->table) . ' (ParentID, UserID, msg_type, obj_type, headerDate, headerSubject, headerUserID, headerTo, Priority, MessageText, seenStatus) VALUES (' . $this->default_folders[we_messaging_proto::FOLDER_SENT] . ', ' . intval($this->userid) . ', ' . intval($this->sql_class_nr) . ', ' . we_messaging_proto::MESSAGE_NR . ', UNIX_TIMESTAMP(NOW()), "' . $this->DB_WE->escape($data['subject']) . '", ' . intval($this->userid) . ', "' . $this->DB_WE->escape(strlen($to_str) > 60 ? substr($to_str, 0, 60) . '...' : $to_str) . '", 0, "' . $this->DB_WE->escape($data['body']) . '", 0)');

		return $results;
	}

	function get_msg_set(&$criteria){
		$sfield_cond = '';

		if(isset($criteria['search_fields'])){
			$arr = array('hdrs', 'From');
			$sf_uoff = self::arr_offset_arraysearch($arr, $criteria['search_fields']);

			if($sf_uoff > -1){
				$sfield_cond .= 'u.username LIKE "%' . escape_sql_query($criteria['searchterm']) . '%" OR
		u.First LIKE "%' . escape_sql_query($criteria['searchterm']) . '%" OR
		u.Second LIKE "%' . escape_sql_query($criteria['searchterm']) . '%" OR ';

				unset($criteria['search_fields'][$sf_uoff]);
			}

			foreach($criteria['search_fields'] as $sf){
				$sfield_cond .= array_search($sf, $this->sf2sqlfields) . ' LIKE "%' . escape_sql_query($criteria['searchterm']) . '%" OR ';
			}

			$sfield_cond = substr($sfield_cond, 0, -3);

			$folders_cond = implode(' OR m.ParentID = ', $criteria['search_folder_ids']);
		} else if(isset($criteria['folder_id'])){

			$folders_cond = $criteria['folder_id'];

			if($this->cached['sortfield'] != 1 || $this->cached['sortorder'] != 1){
				$this->init_sortstuff($criteria['folder_id']);
			}

			$this->Folder_ID = $criteria['folder_id'];
		}

		if(isset($criteria['message_ids'])){
			$message_ids_cond = implode(' OR m.ID = ', $criteria['message_ids']);
		}

		$this->selected_set = [];
		$this->DB_WE->query('SELECT m.ID, m.ParentID, m.headerDate, m.headerSubject, m.headerUserID, m.Priority, m.seenStatus, u.username
		FROM ' . $this->DB_WE->escape($this->table) . ' AS m, ' . USER_TABLE . ' AS u
		WHERE ((m.msg_type = ' . intval($this->sql_class_nr) . ' AND m.obj_type = ' . we_messaging_proto::MESSAGE_NR . ') ' . ($sfield_cond ? " AND ($sfield_cond)" : '') . ($folders_cond ? " AND (m.ParentID=$folders_cond)" : '') . (!empty($message_ids_cond) ? " AND (m.ID=$message_ids_cond)" : '') . ") AND m.UserID=" . $this->userid . " AND m.headerUserID=u.ID
		ORDER BY " . $this->sortfield . ' ' . $this->so2sqlso[$this->sortorder]);

		$i = isset($criteria['start_id']) ? $criteria['start_id'] + 1 : 0;

		$seen_ids = [];

		while($this->DB_WE->next_record()){
			if(!($this->DB_WE->f('seenStatus') & we_messaging_proto::STATUS_SEEN)){
				$seen_ids[] = $this->DB_WE->f('ID');
			}

			$this->selected_set[] = array('ID' => $i++,
				'hdrs' => array('Date' => $this->DB_WE->f('headerDate'),
					'Subject' => $this->DB_WE->f('headerSubject'),
					'From' => $this->DB_WE->f('username'),
					'Priority' => $this->DB_WE->f('Priority'),
					'seenStatus' => $this->DB_WE->f('seenStatus'),
					'ClassName' => $this->ClassName),
				'int_hdrs' => array('_from_userid' => $this->DB_WE->f('headerUserID'),
					'_ParentID' => $this->DB_WE->f('ParentID'),
					'_ClassName' => $this->ClassName,
					'_ID' => $this->DB_WE->f('ID')));
		}

		/* mark selected_set messages as seen */
		if($seen_ids){
			$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET seenStatus = (seenStatus | ' . we_messaging_proto::STATUS_SEEN . ') WHERE (ID = ' . implode(' OR ID = ', $seen_ids) . ') AND UserID = ' . $this->userid);
		}

		return $this->selected_set;
	}

	function retrieve_items($int_hdrs){
		$ret = [];
		$i = 0;

		if(!$int_hdrs){
			return $ret;
		}

		$id_str = '';
		foreach($int_hdrs as $ih){
			$id_str .= 'm.ID = ' . escape_sql_query($ih['_ID']);
		}

		$this->DB_WE->query('SELECT m.ID, m.headerDate, m.headerSubject, m.headerUserID, m.headerTo, m.MessageText, m.seenStatus, u.username, u.First, u.Second FROM ' . $this->DB_WE->escape($this->table) . " as m, " . USER_TABLE . " as u WHERE ($id_str) AND u.ID=m.headerUserID AND m.UserID=" . intval($this->userid));

		$read_ids = [];

		while($this->DB_WE->next_record()){
			if(!($this->DB_WE->f('seenStatus') & we_messaging_proto::STATUS_READ)){
				$read_ids[] = $this->DB_WE->f('ID');
			}

			$ret[] = array('ID' => $i++,
				'hdrs' => array('Date' => $this->DB_WE->f('headerDate'),
					'Subject' => $this->DB_WE->f('headerSubject'),
					'From' => $this->DB_WE->f('First') . ' ' . $this->DB_WE->f('Second') . ' (' . $this->DB_WE->f('username') . ')',
					'To' => $this->DB_WE->f('headerTo'),
					'Priority' => $this->DB_WE->f('Priority'),
					'seenStatus' => $this->DB_WE->f('seenStatus'),
					'ClassName' => $this->ClassName),
				'int_hdrs' => array('_from_userid' => $this->DB_WE->f('headerUserID'),
					'_ID' => $this->DB_WE->f('ID'),
					'_reply_to' => $this->DB_WE->f('username')),
				'body' => array('MessageText' => $this->DB_WE->f('MessageText')));
		}

		if($read_ids){
			$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET seenStatus = (seenStatus | ' . we_messaging_proto::STATUS_READ . ') WHERE ID IN (' . implode(', ', $read_ids) . ') AND UserID = ' . intval($this->userid));
		}

		return $ret;
	}

	/* generate new webedition message */

	static function newMessage(&$rcpts, $subject, $body, &$errs){
		$m = new we_messaging_message();
		$m->set_login_data($_SESSION['user']["ID"], isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : "");
		$data = array('subject' => $subject, 'body' => $body);

		$res = $m->send($rcpts, $data);

		if($res['err']){
			$errs = $res['err'];
			return $res;
		}

		return $res;
	}

	static function showNewMsg(){
		$msg = we_base_request::_(we_base_request::INT, 'msg', 0) - we_base_request::_(we_base_request::INT, 'omsg', 0);
		$todo = we_base_request::_(we_base_request::INT, 'todo', 0) - we_base_request::_(we_base_request::INT, 'otodo', 0);

		$text = ($msg > 0 ? sprintf(g_l('modules_messaging', '[newHeaderMsg]'), '<a href="' . "javascript:top.opener.we_cmd('messaging_start', " . we_messaging_frames::TYPE_MESSAGE . ');">' . $msg, '</a>') . '<br/>' : '') .
			($todo > 0 ? sprintf(g_l('modules_messaging', '[newHeaderTodo]'), '<a href="' . "javascript:top.opener.we_cmd('messaging_start', " . we_messaging_frames::TYPE_TODO . ');">' . $todo, '</a>') . '<br/>' : '');
		$parts = [
			[
				"headline" => we_html_tools::htmlAlertAttentionBox($text, we_html_tools::TYPE_INFO, 500, false),
				"html" => '',
				'space' => we_html_multiIconBox::SPACE_SMALL,
				'noline' => 1],
		];

		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', '', we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_multiIconBox::getHTML("", $parts, 30, '<div style="width:100%;text-align:right;">' . we_html_button::create_button(we_html_button::OK, "javascript:self.close();") . '</div>')
			)
		);
	}

}
