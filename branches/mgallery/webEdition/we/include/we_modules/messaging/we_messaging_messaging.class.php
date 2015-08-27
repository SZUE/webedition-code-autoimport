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
/* messaging object class */

class we_messaging_messaging extends we_class{
	/* Flag which is set when the file is not new */
	var $we_transact;
	var $Folder_ID = -1;
	var $userid = -1;
	var $username = '';
	var $used_msgobjs = array();
	var $send_msgobjs = array();
	var $used_msgobjs_names = array();
	var $active_msgobj = NULL;
	var $selected_message = array();
	var $selected_set = array();
	var $last_id = -1;
	var $search_fields = array(
		array('hdrs', 'Subject'),
		array('hdrs', 'From'),
		array('body', 'MessageText'));
	var $search_folder_ids = array();
	var $sortfield = array('hdrs', 'Date');
	var $last_sortfield = '';
	var $sortorder = 'desc';
	var $cont_from_folder = 0;
	var $ids_selected = array();
	var $available_folders = array();
	var $clipboard = array();
	var $clipboard_action = '';
	var $cached = array();

	/* Search Field names */
	var $sf_names = array('subject' => '',
		'sender' => '',
		'mtext' => '');

	/* Header Fields */
	var $si2sf = array('subject' => array('hdrs', 'Subject'),
		'date' => array('hdrs', 'Date'),
		'sender' => array('hdrs', 'From'),
		'isread' => array('hdrs', 'seenStatus'),
		'mtext' => array('body', 'MessageText'),
		'deadline' => array('hdrs', 'Deadline'),
		'creator' => array('hdrs', 'Creator'),
		'priority' => array('hdrs', 'Priority'),
		'status' => array('hdrs', 'Status'));
	var $sf2sh = array('headerSubject' => array('hdrs', 'Subject'),
		'headerDate' => array('hdrs', 'Date'),
		'headerFrom' => array('hdrs', 'From'),
		'seenStatus' => array('hdrs', 'seenStatus'),
		'MessageText' => array('body', 'MessageText'),
		'headerDeadline' => array('hdrs', 'Deadline'),
		'headerCreator' => array('hdrs', 'Creator'),
		'Priority' => array('hdrs', 'Priority'),
		'headerStatus' => array('hdrs', 'Status'));

	function __construct(&$transact){
		parent::__construct();
		$this->Name = 'messaging_' . md5(uniqid(__FILE__, true));
		$this->persistent_slots = array('Name', 'ID', 'Folder_ID', 'selected_message', 'selected_set', 'last_id', 'sortorder', 'last_sortfield', 'available_folders', 'ids_selected', 'search_folder_ids', 'search_fields', 'used_msgobjs_names', 'clipboard_action', 'clipboard', 'cached');
		$this->we_transact = &$transact;

		$this->sf_names['subject'] = g_l('modules_messaging', '[subject]');
		$this->sf_names['sender'] = g_l('modules_messaging', '[sender]');
		$this->sf_names['mtext'] = g_l('modules_messaging', '[content]');
	}

	function add_msgobj($objname, $recover = 0){
		if(in_array($objname, array_keys($this->send_msgobjs))){
			return 0;
		}

		switch($objname){
			case 'we_message':
				$c = new we_messaging_message();
				break;
			case 'we_todo':
				$c = new we_messaging_todo();
				break;
			case 'we_msg_email':
				$c = new we_messaging_email();
				break;
			default:
				t_e('error', 'unkown type', $objname);
				return -1;
		}

		$c->set_login_data($this->userid, $this->username);

		if($recover == 1){
			$c->init(isset($this->we_transact[$objname]) ? $this->we_transact[$objname] : '');
		} else {
			$this->used_msgobjs_names[] = $objname;

			if(!isset($c->msgclass_type) || $c->msgclass_type == we_messaging_email::TYPE_SEND_RECEIVE){
				$this->available_folders = $this->available_folders + $c->get_available_folders();
			}
		}

		$this->send_msgobjs[$objname] = &$c;

		if(!isset($c->msgclass_type) || $c->msgclass_type == we_messaging_email::TYPE_SEND_RECEIVE){
			$this->used_msgobjs[$objname] = &$c;
			$this->active_msgobj = &$c;
		}

		return 1;
	}

	function set_active_msgobj($classname){
		if(isset($this->used_msgobjs[$classname])){
			$this->active_msgobj = $this->used_msgobjs[$classname];
			return 1;
		}

		return 0;
	}

	/* Getters And Setters */

	function get_sortitem(){
		return array_search($this->sortfield, $this->si2sf);
	}

	function get_sortorder(){
		return $this->sortorder === 'desc' ? 'asc' : 'desc';
	}

	function get_ids_selected(){
		if(empty($this->ids_selected)){
			return '';
		}

		return implode(',', $this->ids_selected);
	}

	function set_ids_selected(array $entrsel){
		$this->ids_selected = $entrsel;
	}

	function reset_ids_selected(){
		$this->ids_selected = array();
	}

	function update_last_id(){
		$this->last_id = -1;

		foreach($this->selected_set as $elem){
			if($elem['ID'] > $this->last_id){
				$this->last_id = $elem['ID'];
			}
		}
	}

	function poll_for_new(){
		$c = 0;
		foreach($this->used_msgobjs as $mo){
			$c += $mo->get_newmsg_count();
		}

		return $c;
	}

	private function group_by_msgobjs($arr){
		$ret = array();

		foreach($arr as $elem){
			if(isset($ret[$elem['ClassName']]) && is_array($ret[$elem['ClassName']])){
				$ret[$elem['ClassName']][] = $elem['ID'];
			} else {
				$ret[$elem['ClassName']] = array((string) $elem['ID']);
			}
		}

		return $ret;
	}

	function check_folders(){
		/* FIXME: Use defines for folder constants, instead of class variables in we_msg_proto.inc.php */
		return intval(f('SELECT COUNT(ID) FROM ' . MSG_FOLDERS_TABLE . ' WHERE UserID=' . intval($this->userid) . ' AND (obj_type=3 OR obj_type=5 OR obj_type=9 OR obj_type=11 OR obj_type=13)', '', $this->DB_WE)) >= 5;
	}

	public static function array_ksearch($key, $val, array &$arr, $pos = 0){
		foreach($arr as $pos => $entry){
			if($entry[$key] == $val){
				return $pos;
			}
		}

		return -1;
	}

	/* Clipboard methods */

	function set_clipboard(array $ids, $mode){
		$this->clipboard = array();
		foreach($ids as $id){
			$offs = self::array_ksearch('ID', $id, $this->selected_set);
			$this->clipboard[] = array(
				'ID' => $this->selected_set[$offs]['int_hdrs']['_ID'],
				'ClassName' => $this->selected_set[$offs]['hdrs']['ClassName']
			);
		}

		$this->clipboard_action = $mode;
	}

	function clipboard_paste(&$errs){
		if($this->Folder_ID == -1){
			$errs = g_l('modules_messaging', '[cant_paste]');
			return;
		}

		$s_hash = $this->group_by_msgobjs($this->clipboard);
		$f = $this->get_folder_info($this->Folder_ID);
		$cn = $f['ClassName'];

		if(isset($s_hash[$cn])){
			if($this->clipboard_action === 'cut'){
				$this->used_msgobjs[$cn]->clipboard_cut($s_hash[$cn], $this->Folder_ID);
			} else if($this->clipboard_action === 'copy'){
				$this->used_msgobjs[$cn]->clipboard_copy($s_hash[$cn], $this->Folder_ID);
			}

			if($this->clipboard_action === 'cut'){
				$this->reset_clipboard();
			}
		}
	}

	function reset_clipboard(){
		$this->clipboard = array();
	}

	function reject(&$data){
		$results = array();
		$results['err'] = array();
		$results['ok'] = array();
		$results['failed'] = array();
		$rcpt_elems = explode(',', urldecode($data['rcpts_string']));

		if(empty($this->selected_message)){
			$results['err'][] = g_l('modules_messaging', '[no_selection]');
			$results['failed'] = $rcpt_elems;
			return $results;
		}

		$ret = $this->used_msgobjs[$this->selected_message['hdrs']['ClassName']]->reject($this->selected_message, $data);
		$results['err'] = $ret['err'];
		$results['ok'] = $ret['ok'];
		$results['failed'] = $ret['failed'];

		unset($this->selected_set[self::array_ksearch('ID', $this->selected_message['ID'], $this->selected_set)]);
		$this->selected_message = array();

		return $results;
	}

	function forward(&$data){
		$results = array();
		$results['err'] = array();
		$results['ok'] = array();
		$results['failed'] = array();
		$rcpt_elems = explode(',', urldecode($data['rcpts_string']));
		$rcpts = array();

		if(empty($this->selected_message)){
			$results['err'][] = g_l('modules_messaging', '[no_selection]');
			$results['failed'] = $rcpt_elems;
			return $results;
		}

		foreach($rcpt_elems as $elem){
			$rcpt_info = array();
			$elem = urldecode($elem);
			if(!$this->get_recipient_info($elem, $rcpt_info, "")){
				$results['err'][] = g_l('modules_messaging', '[rcpt_parse_error]');
				$results['failed'][] = $elem;
				continue;
			}

			$rcpts[$rcpt_info['msg_obj']][] = $rcpt_info['address'];
		}

		unset($data['rcpts_string']);

		foreach($rcpts as $vals){
			$ret = $this->used_msgobjs[$this->selected_message['hdrs']['ClassName']]->forward($vals, $data, $this->selected_message);
			$results['err'] = array_merge($results['err'], $ret['err']);
			$results['ok'] = array_merge($results['ok'], $ret['ok']);
			$results['failed'] = array_merge($results['failed'], $ret['failed']);
		}

		return $results;
	}

	/* Get all values for $key in an array of hashes
	  params: key, hash
	  returns: array of the values for the key */

	static function array_get_kvals($key, array $hash){
		$ret_arr = array();

		foreach($hash as $elem){
			$ret_arr[] = $elem[$key];
		}
		return $ret_arr;
	}

	function delete_items(){
		$s_hash = array();
		foreach($this->ids_selected as $id){
			$offset = self::array_ksearch('ID', $id, $this->selected_set);
			if($offset == -1){
				continue;
			}
			$cn = $this->selected_set[$offset]['hdrs']['ClassName'];
			if(isset($s_hash[$cn])){
				$s_hash[$cn][] = array(
					'ID' => $id,
					'hdrs' => $this->selected_set[$offset]['int_hdrs']
				);
			} else {
				$s_hash[$cn] = array(
					array(
						'ID' => $id,
						'hdrs' => $this->selected_set[$offset]['int_hdrs']
					)
				);
			}
		}

		foreach($s_hash as $cn => $val){
			$kvals = self::array_get_kvals('hdrs', $val);
			if(is_object($this->used_msgobjs[$cn])){
				$di = $this->used_msgobjs[$cn]->delete_items($kvals);
				if($di == 1){
					$ids = self::array_get_kvals('ID', $val);
					foreach($ids as $id){
						$index = self::array_ksearch('ID', $id, $this->selected_set);
						unset($this->selected_set[$index]);
						$this->update_last_id();
					}
					continue;
				}
			}
			t_e('error in delete items', $_REQUEST, $this->used_msgobjs[$cn], $val);
			echo 'Couldn\'t delete Message ID = ' . $val['ID'] . '<br/>';
		}
	}

	function set_login_data($userid, $username){
		$this->userid = $userid;
		$this->username = $username;
	}

	function save_settings($settings){
		if(isset($settings['check_step'])){
			//  Check if there are already saved settings for this user in the DB
			$this->DB_WE->query('REPLACE INTO ' . PREFS_TABLE . ' SET userID=' . intval($this->userid) . ',strKey="check_step",strVal=' . intval($settings['check_step']));
		}

		return 1;
	}

	function get_settings(){
		return f('SELECT strVal FROM ' . PREFS_TABLE . ' WHERE userID=' . intval($this->userid) . ' AND strKey="check_step"', '', $this->DB_WE, 10);
	}

	function get_subfolder_count($id){
		$classname = $this->available_folders[$id]['ClassName'];
		return ($classname ? $this->used_msgobjs[$classname]->get_subfolder_count($id) : -1);
	}

	function set_search_settings($search_fields, $search_folder_ids){
		$this->search_fields = $this->search_folder_ids = array();

		if(isset($search_fields)){
			foreach($search_fields as $elem){
				if($this->si2sf[$elem]){
					$this->search_fields[] = $this->si2sf[$elem];
				}
			}
		}

		if(isset($search_folder_ids)){
			foreach($search_folder_ids as $elem){
				if(isset($this->available_folders[$elem])){
					$this->search_folder_ids[] = $elem;
				}
			}
		}

		return 1;
	}

	function init(){
		if($this->we_transact && isset($this->we_transact['we_messaging'])){
			$this->initSessionDat($this->we_transact['we_messaging']);
		}

		/* initialize the used message objects */
		foreach($this->used_msgobjs_names as $elem){
			$this->add_msgobj($elem, 1);
		}

		if(empty($this->available_folders)){
			$this->get_available_folders();
		}

		if(count($this->search_folder_ids) < 1){
			$this->search_folder_ids = array($this->Folder_ID);
		}
	}

	function initSessionDat($sessDat){
		if($sessDat){
			foreach($this->persistent_slots as $cur){
				if(isset($sessDat[0][$cur])){
					$this->{$cur} = $sessDat[0][$cur];
				}
			}

			if(isset($sessDat[1])){
				$this->elements = $sessDat[1];
			}
		}
	}

	function saveInSession(&$save){
		$save = array(
			'we_messaging' => array(
				array(),
				isset($this->elements) ? $this->elements : array()
			)
		);

		foreach($this->persistent_slots as $cur){
			$save["we_messaging"][0][$cur] = $this->{$cur};
		}

		/* save the used message objects */
		foreach(array_keys($this->used_msgobjs) as $key){
			$this->used_msgobjs[$key]->saveInSession($save[$key]);
		}
	}

	function save_addresses(&$addressbook){
		$this->DB_WE->query('DELETE FROM ' . MSG_ADDRBOOK_TABLE . ' WHERE UserID=' . intval($this->userid));
		foreach($addressbook as $elem){
			if(!empty($elem)){
				$this->DB_WE->query('INSERT INTO ' . MSG_ADDRBOOK_TABLE . ' ' . we_database_base::arraySetter(array(
						'UserID' => $this->userid,
						'strMsgType' => $elem[0],
						'strID' => $elem[1],
						'strAlias' => $elem[2],
						/* 'strFirstname',
						  'strSurname' */
						)
				));
			}
		}

		return true;
	}

	function get_folder_info($fid){
		return isset($this->available_folders[$fid]) ? $this->available_folders[$fid] : NULL;
	}

	function get_folder($fid){
		return isset($this->available_folders[$fid]) ? $this->available_folders[$fid] : null;
	}

	function get_inbox_folder($classname){
		$c = 0;

		if(!isset($this->used_msgobjs[$classname])){
			return NULL;
		}
		foreach($this->available_folders as $folder){
			if($folder['obj_type'] == we_messaging_proto::FOLDER_INBOX && $folder['ClassName'] == $classname){
				return $folder;
			}
		}
		return null;
	}

	function get_addresses(){
		$this->DB_WE->query('SELECT strMsgType, strID, strAlias FROM ' . MSG_ADDRBOOK_TABLE . ' WHERE UserID=' . intval($this->userid));
		return $this->DB_WE->getAll(false, MYSQL_NUM);
	}

	function get_available_folders(){
		$this->available_folders = array();

		foreach($this->used_msgobjs as $val){
			$this->available_folders = $this->available_folders + $val->get_available_folders();
		}
	}

	function get_message_count($folderid){
		$classname = $this->available_folders[$folderid]['ClassName'];
		return ($classname ?
				$this->used_msgobjs[$classname]->get_count($folderid) :
				-1);
	}

	function delete_folders(array $ids){
		$ret = array(
			'ids' => array()
		);
		$nids = $m = array();
		for($i = 0, $len = count($ids); $i < $len; $i++){
			preg_match('/\d+$/', $ids[$i], $m);
			$nids[] = $m[0];
		}

		$s_hash = array();
		foreach($nids as $f_id){
			$cn = $this->available_folders[$f_id]['ClassName'];
			if(isset($s_hash[$cn]) && is_array($s_hash[$cn])){
				$s_hash[$cn][] = (string) $f_id;
			} else {
				$s_hash[$cn] = array((string) $f_id);
			}
		}

		foreach($s_hash as $key => $val){
			$mo_ret = $this->used_msgobjs[$key]->delete_folders($val);
			if($mo_ret['res'] == 1){
				$ret['ids'] = array_merge($ret['ids'], $mo_ret["ids"]);
				foreach($mo_ret['ids'] as $id){
					if(isset($this->available_folders[$id])){
						unset($this->available_folders[$id]);
					}
				}
			}
		}

		$ret['ids'] = $ret['ids'];
		$ret['res'] = 1;

		return $ret;
	}

	function cmp_asc($a, $b){
		if($a[$this->sortfield[0]][$this->sortfield[1]] == $b[$this->sortfield[0]][$this->sortfield[1]]){
			return 0;
		}

		return ($a[$this->sortfield[0]][$this->sortfield[1]] > $b[$this->sortfield[0]][$this->sortfield[1]] ? -1 : 1);
	}

	function cmp_desc($a, $b){
		if($a[$this->sortfield[0]][$this->sortfield[1]] == $b[$this->sortfield[0]][$this->sortfield[1]]){
			return 0;
		}

		return ($a[$this->sortfield[0]][$this->sortfield[1]] > $b[$this->sortfield[0]][$this->sortfield[1]] ? 1 : -1);
	}

	function sort_set(){
		if($this->selected_set){
			if(($this->last_sortfield != $this->sortfield) || $this->sortorder != 'desc'){
				usort($this->selected_set, array($this, 'cmp_desc'));
				$this->sortorder = 'desc';
			} else {
				usort($this->selected_set, array($this, 'cmp_asc'));
				$this->sortorder = 'asc';
			}

			$this->last_sortfield = $this->sortfield;
		}
	}

	function save_sortstuff($id, $sortfield, $sortorder){
		return $this->active_msgobj->save_sortstuff($id, $sortfield, $sortorder);
	}

	function init_sortstuff($id, $classname){
		if(empty($classname)){
			$this->sortfield = $this->sf2sh[$this->active_msgobj->get_sortfield($id)];
			$this->sortorder = $this->sf2sh[$this->active_msgobj->get_sortorder()];
		} else {
			$this->sortfield = $this->sf2sf[$this->used_msgobjs[$classname]->get_sortfield($id)];
			$this->sortorder = $this->sf2sf[$this->used_msgobjs[$classname]->get_sortorder()];
		}
	}

	function get_header($id, $headername){
		return $this->selected_message[$headername];
	}

	private function get_recipient_info($r, array &$rcpt_info, $msgobj_name = ''){
		$addr_is_email = 0;
		$rcpt_info['address'] = trim($r);

		if(strpos($rcpt_info['address'], '@', 1) != 0){
			$addr_is_email = 1;
		}

		if(!empty($msgobj_name)){
			$rcpt_info['msg_obj'] = $msgobj_name;
			if(!empty($addr_is_email) && ($rcpt_info['msg_obj'] != 'we_msg_email')){
				return 0;
			}
		} else if(!empty($addr_is_email)){
			$rcpt_info['msg_obj'] = 'we_msg_email';
		} else {
			$rcpt_info['msg_obj'] = 'we_message';
		}

		return 1;
	}

	function send(&$data, $msgobj_name = ''){
		$results = array(
			'err' => array(),
			'ok' => array(),
			'failed' => array(),
		);
		$rcpt_elems = explode(',', urldecode($data['rcpts_string']));
		$rcpts = array();

		foreach($rcpt_elems as $elem){
			$rcpt_info = array();
			$elem = urldecode($elem);
			if(!$this->get_recipient_info($elem, $rcpt_info, isset($msgobj_name) ? $msgobj_name : "")){
				$results['err'][] = g_l('modules_messaging', '[rcpt_parse_error]');
				$results['failed'][] = $elem;
				continue;
			}

			$rcpts[$rcpt_info['msg_obj']][] = $rcpt_info['address'];
		}

		unset($data['rcpts_string']);

		foreach($rcpts as $obj_name => $vals){
			$ret = $this->send_msgobjs[$obj_name]->send($vals, $data);
			$results['err'] = array_merge($results['err'], $ret['err']);
			$results['ok'] = array_merge($results['ok'], $ret['ok']);
			$results['failed'] = array_merge($results['failed'], $ret['failed']);
		}

		return $results;
	}

	function get_fc_data($id, $sortitem, $searchterm, $usecache = 1){
		$sortfield = isset($this->si2sf[$sortitem]) ? $this->si2sf[$sortitem] : "";
		if(!$id && !$searchterm && $usecache){
			$this->cont_from_folder = 0;
			if(!empty($sortfield)){
				$this->sortfield = $sortfield;
				$this->sort_set();
				$this->save_sortstuff($this->Folder_ID, array_search($sortfield, $this->sf2sh), $this->sortorder);
			}
		} else {
			$this->selected_set = array();
			$this->last_id = -1;

			if($id){
				$this->cont_from_folder = 1;
				$this->Folder_ID = $id;
				if($this->search_folder_ids[0] == -1){
					$this->search_folder_ids = array($id);
				}
			}

			if($searchterm && $this->search_fields){
				$this->Folder_ID = -1;
				$s_hash = array();
				if(!$id){
					foreach($this->available_folders as $afolder){
						$cn = $afolder['ClassName'];
						if(isset($s_hash[$cn]) && is_array($s_hash[$cn])){
							$s_hash[$cn][] = $afolder['ID'];
						} else {
							$s_hash[$cn] = array($afolder['ID']);
						}
					}
				} else {
					foreach($this->search_folder_ids as $sfolder){
						$cn = $this->available_folders[$sfolder]['ClassName'];
						if(isset($s_hash[$cn]) && is_array($s_hash[$cn])){
							$s_hash[$cn][] = $sfolder;
						} else {
							$s_hash[$cn] = array("$sfolder");
						}
					}
				}
				foreach($s_hash as $m_key => $m_val){
					$arr = array('searchterm' => $searchterm,
						'search_fields' => $this->search_fields,
						'search_folder_ids' => $m_val,
						'start_id' => $this->last_id);
					$this->selected_set = array_merge($this->selected_set, $this->used_msgobjs[$m_key]->get_msg_set($arr));
					$this->update_last_id();
				}
			} else {
				/* 		if (empty($sortfield))
				  $this->init_sortstuff($id, '');
				  else
				  $this->save_sortstuff($id, array_search($sortfield, $this->sf2sh), $this->sortorder); */

				//		$this->ids_selected = array();
				//		echo "ID=$id<br/>\n";
				if(isset($this->available_folders[$id])){
					$o = $this->used_msgobjs[$this->available_folders[$id]['ClassName']];
				} else {
					$o = null;
				}
				$arr = array('folder_id' => $id, 'last_id' => $this->last_id);
				$this->selected_set = isset($o) ? $o->get_msg_set($arr) : array();
				$this->update_last_id();

				$this->last_sortfield = (isset($o) && isset($this->sf2sh[$o->get_sortfield()])) ? $this->sf2sh[$o->get_sortfield()] : "";
				$this->sortfield = $this->last_sortfield;
				$this->sortorder = isset($o) ? $o->get_sortorder() : "";
			}
		}
	}

	/* Message-Data for the messaging_message_view Frame
	  params: ID - id of the shown message */

	function get_mv_data($id, $classname = ''){ // imi: find selected_message here
		$this->selected_message = array();
		if(isset($id)){
			if(self::array_ksearch('ID', $id, $this->selected_set) != "-1"){
				$m = $this->selected_set[self::array_ksearch('ID', $id, $this->selected_set)];
			}
			if($m){
				$arr = array($m['int_hdrs']);
				$this->selected_message = array_pop($this->used_msgobjs[$m['hdrs']['ClassName']]->retrieve_items($arr));
			}
		}
	}

	function get_short_description($type){
		if(!empty($type) && isset($this->used_msgobjs[$type])){
			return $this->used_msgobjs[$type]->Short_Description;
		}

		return;
	}

	/* php 4.0.0 does not support array comparison using the == operator */

	static function array_cmp(&$arr1, &$arr2){
		if(count($arr1) != count($arr2)){
			return 0;
		}

		for($i = 0; $i < count($arr1); $i++){
			if($arr1[$i] != $arr2[$i]){
				return 0;
			}
		}

		return 1;
	}

	/* in_array in PHP versions prior to 4.2.0 can not take an */
	/* array as needle */

	private static function arr_in_array(&$needle, &$haystack){
		foreach($haystack as $elem){
			if(self::array_cmp($needle, $elem)){
				return 1;
			}
		}

		return 0;
	}

	function print_select_search_fields(){
		$out = "";
		foreach($this->sf_names as $key => $val){
			$out .= '<option value="' . $key . '"' . (self::arr_in_array($this->si2sf[$key], $this->search_fields) ? ' selected' : '') . '>' . $val . '</option>';
		}
		return $out;
	}

	function create_folder($name, $parent_id, $type){
		/* Sanity Checks */
		if(!$type || !isset($this->used_msgobjs[$type])){
			return array(1, g_l('modules_messaging', '[msg_type_not_found]'));
		}

		if((($ind = self::array_ksearch('Name', $name, $this->available_folders)) >= 0) && ($this->available_folders[$ind]['ParentID'] == $parent_id)){
			return array(-1, g_l('modules_messaging', '[children_same_name]'));
		}

		$parent_id = $parent_id == -1 ? 0 : $parent_id;

		//FIXME: Parent-check must be done by $type object;
		if($parent_id != 0 && !isset($this->available_folders[$parent_id])){
			return array(-1, g_l('modules_messaging', '[no_parent_folder]'));
		}

		if(($id = $this->used_msgobjs[$type]->create_folder($name, $parent_id)) != -1){
			$this->available_folders[] = array(
				'ID' => $id,
				'ParentID' => $parent_id,
				'ClassName' => $type,
				'Name' => $name);

			return array($id, g_l('modules_messaging', '[folder_created]'));
		}
		return array(-1, g_l('modules_messaging', '[folder_create_error]'));
	}

	function modify_folder($fid, $folder_name, $parent_folder){
		$ret = array();

		if(!is_numeric($fid) || !is_numeric($parent_folder)){
			$ret[] = -1;
			$ret[] = g_l('modules_messaging', '[param_wrong_type]');
			return $ret;
		}

		if($parent_folder != -1 && ($fid == $parent_folder || !$this->valid_parent_folder($fid, $parent_folder))){
			$ret[] = -1;
			$ret[] = g_l('modules_messaging', '[parentfolder_invalid]');
			return $ret;
		}

		if(($f = $this->get_folder($fid)) == NULL){
			$ret[] = -1;
			$ret[] = g_l('modules_messaging', '[folderid_invalid]');
			return $ret;
		}

		if($this->used_msgobjs[$f['ClassName']]->modify_folder($fid, $folder_name, $parent_folder)){
			$this->available_folders[$fid]['Name'] = $folder_name;
			$this->available_folders[$fid]['ParentID'] = $parent_folder;
			$ret[] = 1;
			$ret[] = g_l('modules_messaging', '[folder_modified]');
		} else {
			$ret[] = -1;
			$ret[] = g_l('modules_messaging', '[folder_change_failed]');
		}

		return $ret;
	}

	function valid_parent_folder($folder, $parent){
		static $children = array();

		foreach($this->available_folders as $f){
			if($f['ParentID'] == $folder){
				$children[] = $f['ID'];
				$this->valid_parent_folder($f['ID'], $parent);
			}
		}

		if(in_array($parent, $children)){
			return 0;
		}

		return 1;
	}

	private static function array_hash_construct($arr_hash, $keys, $map = ""){
		$ret_arr = array();
		$len_arr = count($arr_hash);

		for($i = 0; $i < $len_arr; $i++){
			$tmp_hash = array();

			foreach($keys as $key){
				if(is_array($map) && !empty($map)){
					if(isset($map[$key])){
						foreach($map[$key] as $k => $v){
							if(strtolower($k) == strtolower($arr_hash[$i][$key])){
								$arr_hash[$i][$key] = $v;
								break;
							}
						}
					}
				}
				$tmp_hash[$key] = $arr_hash[$i][$key];
			}

			$ret_arr[] = $tmp_hash;
		}

		return $ret_arr;
	}

	/*
	 * Convert array of hashes to a single hash, using the first and second field
	 * of each hash as key => val of the returned hash.
	 */

	private static function arr_hash_to_wesel_hash($arr_hash, $keys){
		$ret_hash = array();
		$len_arr = count($arr_hash);

		for($i = 0; $i < $len_arr; $i++){
			$ret_hash[$arr_hash[$i][$keys[0]]] = $arr_hash[$i][$keys[1]];
		}

		return $ret_hash;
	}

	function get_wesel_available_folders(){
		$fooArray = array(
			"sent" => g_l('modules_messaging', '[folder_sent]'),
			"messages" => g_l('modules_messaging', '[folder_messages]'),
			"done" => g_l('modules_messaging', '[folder_done]'),
			"task" => g_l('modules_messaging', '[folder_todo]'),
			"rejected" => g_l('modules_messaging', '[folder_rejected]'),
			"todo" => g_l('modules_messaging', '[folder_todo]')
		);

		$matchArray = array("Name" => $fooArray);

		$mergedArray = array_merge(
			array(
			array(
				'ID' => 0,
				'Name' => "-- " . g_l('modules_messaging', '[nofolder]') . " --"
			)
			), self::array_hash_construct($this->available_folders, array('ID', 'Name'), $matchArray)
		);

		$_arr1 = array('ID', 'Name');

		$_ret = self::arr_hash_to_wesel_hash($mergedArray, $_arr1);
		return $_ret;
	}

	function get_wesel_folder_types(){
		$ret_arr = array();

		foreach($this->used_msgobjs as $mo)
			$ret_arr[$mo->ClassName] = $mo->Short_Description;

		return $ret_arr;
	}

	function print_select_search_folders(){
		$out = '';
		foreach($this->available_folders as $key => $val){
			$out .= '<option value="' . $key . '"' . (in_array($key, $this->search_folder_ids) ? ' selected' : '') . '>' . $val['Name'] . "</option>\n";
		}
		return $out;
	}

	/* Create the default folders for the given $userid */

	static function createFolders($userid){
		$default_folders = array(
			1 => array(
				5 => "sent",
				3 => "messages"),
			2 => array(
				13 => "done",
				11 => "rejected",
				3 => "todo"));

		$db = new DB_WE();

		$pfolders = array(1 => -1, 2 => -1);

		$db->query('SELECT ID,msg_type,obj_type FROM ' . MSG_FOLDERS_TABLE . ' WHERE obj_type IN(3,5,9,11,13) AND UserID=' . intval($userid));
		while($db->next_record()){
			if(isset($default_folders[$db->f('msg_type')][$db->f('obj_type')])){
				if($db->f('obj_type') == 3){
					$pfolders[$db->f('msg_type')] = $db->f('ID');
				}
				unset($default_folders[$db->f('msg_type')][$db->f('obj_type')]);
			}
		}

		foreach($default_folders as $mt => $farr){
			if($pfolders[$mt] != -1){
				$pf_id = $pfolders[$mt];
			} else {
				$db->query('INSERT INTO ' . MSG_FOLDERS_TABLE . ' (ID, ParentID, UserID, msg_type, obj_type, Properties, Name) VALUES (NULL, 0, ' . intval($userid) . ", $mt, 3, 1, '" . $default_folders[$mt]['3'] . '\')');
				$pf_id = $db->getInsertId();
				unset($farr['3']);
			}

			foreach($farr as $df => $fname){
				$db->query('INSERT INTO ' . MSG_FOLDERS_TABLE . " (ID, ParentID, UserID, msg_type, obj_type, Properties, Name) VALUES (NULL, $pf_id, " . intval($userid) . ", $mt, " . $df . ', 1, "' . $fname . '")');
			}
		}

		return 1;
	}

}
