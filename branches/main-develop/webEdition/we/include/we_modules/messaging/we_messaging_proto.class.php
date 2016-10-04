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
/* message protocol root class */
abstract class we_messaging_proto /* extends we_class */{
	const FOLDER_DONE = 13;
	const FOLDER_REJECT = 11;
	const FOLDER_TRASH = 9;
	const FOLDER_SENT = 5;
	const FOLDER_INBOX = 3;
	const FOLDER_NR = 1;
	const MESSAGE_NR = 2;
	const TODO_NR = 4;
	const STATUS_SEEN = 1;
	const STATUS_READ = 2;
	const CLIPBOARD_COPY = 0;
	const CLIPBOARD_CUT = 1;

	/* History action codes */
	const ACTION_COMMENT = 1;
	const ACTION_FORWARD = 2;
	const ACTION_REJECT = 3;
	const ACTION_DONE = 4;

	/* To DO properties */
	const TODO_PROP_NONE = 0;
	const TODO_PROP_IMMOVABLE = 1;

	/* Flag which is set when the file is not new */
	var $Folder_ID = -1;
	var $userid = -1;
	var $username = '';
	var $selected_message = [];
	var $selected_set = [];
	var $search_ids = [];
	var $search_fields = ['headerSubject', 'headerFrom', 'MessageText'];
	var $search_folder_ids = [];
	var $sortfield = 'headerDate';
	var $last_sortfield = '';
	var $sortorder = 'desc';
	var $ids_selected = [];
	var $available_folders = [];
	var $cached = ['sortorder' => 0,
		'sortfield' => 0];
//    var $got_sortstuff_from_db = 0;
	var $update_interval = 10;
	var $default_folders = [
		self::FOLDER_DONE => -1,
		self::FOLDER_REJECT => -1,
		self::FOLDER_TRASH => -1,
		self::FOLDER_SENT => -1,
		self::FOLDER_INBOX => -1];
	var $table = MESSAGES_TABLE;
	var $folder_tbl = MSG_FOLDERS_TABLE;
	var $DB_WE;
	var $ClassName;
	var $ID;
	var $Table;

	function __construct(){
		$this->ClassName = get_class($this); //$this is different from self!
		$this->DB_WE = new DB_WE();

		$this->Name = 'msg_proto_' . md5(uniqid(__FILE__, true));
		$this->persistent_slots = array('ClassName', 'Name', 'ID', 'Table', 'Folder_ID', 'selected_message', 'sortorder', 'last_sortfield', 'search_ids', 'available_folders', 'search_folder_ids', 'search_fields', 'cached');
	}

	/* Getters And Setters */

	function get_sortitem(){
		if(empty($this->sortfield)){
			$this->init_sortstuff($this->Folder_ID);
		}

		return $this->sf2si[$this->sortfield];
	}

	function get_entries_selected(){
		if(empty($this->ids_selected)){
			return '';
		}

		return '"' . implode('","', $this->ids_selected) . '"';
	}

	function set_entries_selected($entrsel){
		$this->ids_selected = explode(',', $entrsel);
	}

	function reset_entries_selected(){
		$this->ids_selected = [];
	}

	function set_login_data($userid, $username){
		$this->userid = $userid;
		$this->username = $username;
	}

	function get_sortfield(){
		if($this->cached['sortfield'] != 1){
			$this->init_sortstuff($this->Folder_ID);
		}

		return $this->sortfield;
	}

	function get_sortorder(){
		if($this->cached['sortorder'] != 1){
			$this->init_sortstuff($this->Folder_ID);
		}

		return $this->sortorder;
	}

	function get_subfolder_count($id){
		$this->DB_WE->query('SELECT count(ID) as c FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE ParentID=' . intval($id) . ' AND UserID=' . intval($this->userid));

		if($this->DB_WE->next_record() && $this->DB_WE->f('c') > 0){
			return $this->DB_WE->f('c');
		}

		return -1;
	}

	function set_search_settings($search_fields, $search_folder_ids){
		$this->search_fields = [];
		$this->search_folder_ids = [];

		if(isset($search_fields)){
			foreach($search_fields as $elem){
				if(!empty($this->si2sf[$elem])){
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
	}

	/* Intialize the class. If $sessDat (array) is set, the class will be initialized from this array */

	function init($sessDat){
		if($sessDat){
			$this->initSessionDat($sessDat);
		}

		/* 	if (empty($this->available_folders))
		  $this->get_available_folders(); */
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
		$save = [];
		$save[0] = [];


		foreach($this->persistent_slots as $cur){
			$save[0][$cur] = $this->{$cur};
		}

		$save[1] = isset($this->elements) ? $this->elements : [];
	}

	function get_available_folders(){
		$this->available_folders = [];

		$this->DB_WE->query('SELECT ID,ParentID,Name,obj_type FROM  ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE msg_type=' . intval($this->sql_class_nr) . ' AND UserID=' . intval($this->userid));
		while($this->DB_WE->next_record(MYSQL_ASSOC)){
			$this->available_folders[$this->DB_WE->f('ID')] = $this->DB_WE->getRecord() + [
				'ClassName' => $this->ClassName,
				'view_class' => $this->view_class,
				];
		}

		return $this->available_folders;
	}

	function create_folder($name, $parent){
		$this->DB_WE->query('INSERT INTO ' . $this->DB_WE->escape($this->folder_tbl) . ' ' .
			we_database_base::arraySetter([
				'ParentID' => $parent,
				'UserID' => $this->userid,
				'account_id' => -1,
				'msg_type' => $this->sql_class_nr,
				'obj_type' => we_messaging_proto::FOLDER_NR,
				'Name' => $name
				]));
		return $this->DB_WE->getInsertId();
	}

	function modify_folder($fid, $folder_name, $parent_folder){
		if(!is_numeric($fid) || !is_numeric($parent_folder)){
			return -1;
		}

		$query = 'UPDATE ' . $this->DB_WE->escape($this->folder_tbl) . ' SET Name="' . $this->DB_WE->escape($folder_name) . '", ParentID=' . intval($parent_folder) . ' WHERE ID=' . intval($fid) . ' AND UserID=' . intval($this->userid);
		$this->DB_WE->query($query);
		return 1;
	}

	/* get subtree starting with node $id (only the folders) */

	function get_f_children($id){
		$this->DB_WE->query('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE ParentID=' . intval($id) . ' AND UserID=' . intval($this->userid));
		$fids = $this->DB_WE->getAll(true);

		foreach($fids as $fid){
			$fids = array_merge($fids, $this->get_f_children($fid));
		}

		return $fids;
	}

	function delete_folders(array $f_arr){
		$ret = array(
			"res" => 0,
			"ids" => []
		);

		if(!$f_arr){
			return $ret;
		}

		$rm_fids = $f_arr;

		foreach($f_arr as $id){
			$rm_fids = array_merge($rm_fids, $this->get_f_children($id));
		}

		$this->DB_WE->query('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE ID IN(' . implode(',', array_map('intval', $rm_fids)) . ') AND UserID=' . intval($this->userid) . ' AND Properties & ' . we_messaging_proto::FOLDER_NR . '!=1');
		$rm_folders = $this->DB_WE->getAll(true);

		if(!$rm_folders){
			return $ret;
		}
		$this->DB_WE->query('DELETE FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE (ID=' . implode(' OR ID=', $rm_folders) . ') AND UserID=' . intval($this->userid));


		$ret["res"] = 1;
		$ret["ids"] = $rm_folders;

		return $ret;
	}

	function cmp_asc($a, $b){
		if($a[$this->sortfield] == $b[$this->sortfield]){
			return 0;
		}

		return ($a[$this->sortfield] > $b[$this->sortfield] ? 1 : -1);
	}

	function cmp_desc($a, $b){
		if($a[$this->sortfield] == $b[$this->sortfield]){
			return 0;
		}

		return ($a[$this->sortfield] > $b[$this->sortfield] ? -1 : 1);
	}

	function sort_set(){
		if(!empty($this->selected_set)){
			if(($this->last_sortfield != $this->sortfield) || $this->sortorder != 'asc'){
				usort($this->selected_set, [$this, 'cmp_asc']);
				$this->sortorder = 'asc';
			} else {
				usort($this->selected_set, [$this, 'cmp_desc']);
				$this->sortorder = 'desc';
			}

			$this->last_sortfield = $this->sortfield;
		}
	}

	function save_sortstuff($id, $sortfield, $sortorder){
		$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->folder_tbl) . ' SET ' . we_database_base::arraySetter(['sortItem' => $sortfield,
				'sortOrder' => ($sortorder === 'asc' ? 'desc' : 'asc')
				]) .
			' WHERE ID=' . intval($id) . ' AND UserID=' . intval($this->userid));
	}

	function init_sortstuff($id){
		$this->DB_WE->query('SELECT sortItem, sortOrder FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE ID=' . intval($id) . ' AND UserID=' . intval($this->userid));
		$this->DB_WE->next_record();

		if(($this->DB_WE->f('sortItem'))){
			$this->sortfield = $this->DB_WE->f('sortItem');
		}

		if(($this->DB_WE->f('sortOrder'))){
			$this->sortorder = ($this->DB_WE->f('sortOrder') === 'asc') ? 'desc' : 'asc';
		}

		$this->cached[] = 'sortfield';
		$this->cached[] = 'sortorder';
		//	$this->got_sortstuff_from_db = 1;
	}

	function delete_items(&$i_headers){
		if(empty($i_headers)){
			return -1;
		}

		$cond = [];
		foreach($i_headers as $ih){
			$cond[] = 'ID=' . intval($ih['_ID']);
		}

		$this->DB_WE->query('DELETE FROM ' . $this->table . ' WHERE (' . implode(' OR ', $cond) . ') AND obj_type=' . $this->obj_type . ' AND UserID=' . intval($this->userid));

		return 1;
	}

	function get_newmsg_count(){
		return intval(f('SELECT COUNT(1) FROM ' . $this->table . ' WHERE (seenStatus&' . we_messaging_proto::STATUS_READ . '=0) AND obj_type=' . $this->obj_type . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND ParentID=' . $this->default_folders[we_messaging_proto::FOLDER_INBOX] . ' AND UserID=' . intval($this->userid), '', $this->DB_WE));
	}

	function get_count($folder_id){
		return f('SELECT COUNT(1) FROM ' . $this->table . ' WHERE ParentID=' . intval($folder_id) . ' AND obj_type=' . $this->obj_type . ' AND msg_type=' . intval($this->sql_class_nr) . ' AND UserID=' . intval($this->userid), '', $this->DB_WE);
	}

	static function arr_offset_arraysearch(&$needle, &$haystack){
		$pos = 0;

		foreach($haystack as $elem){
			if(we_messaging_messaging::array_cmp($elem, $needle) == 1){
				return $pos;
			}
			$pos++;
		}

		return -1;
	}

}
