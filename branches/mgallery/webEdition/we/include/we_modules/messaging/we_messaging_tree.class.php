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
class we_messaging_tree extends weTree{
	private $transaction;

	function __construct($frameset, $topFrame, $treeFrame, $cmdFrame, $transaction){
		parent::__construct($frameset, $topFrame, $treeFrame, $cmdFrame);
		$this->transaction = $transaction;
	}

	function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'messaging_tree.js');
	}

	function getJSStartTree(){
		return  '
var table="' . MESSAGES_TABLE . '";

function startTree(){
	frames={
		top:' . $this->topFrame . ',
		cmd:' . $this->cmdFrame . '
	};
	treeData.frames=frames;
	if(frames.cmd===undefined){
	//FIXME: we have too much frames, this module is not separated well
		setTimeout("startTree()",500);
	}else{
		frames.cmd.location=treeData.frameset+"?pnt=cmd&pid=0&we_transaction="+we_transaction;
	}
}';
	}

	public static function getItems($ParentId, $Offset, $Segment, we_messaging_messaging $messaging){
		$items = array();
		$db = new DB_WE();
		foreach($messaging->available_folders as $folder){
			switch($folder['obj_type']){
				case we_messaging_proto::FOLDER_INBOX:
					$iconbasename = $folder['ClassName'] === 'we_todo' ? 'todo_in_folder' : 'msg_in_folder';
					$folder['Name'] = g_l('modules_messaging', $folder['ClassName'] === 'we_todo' ? '[ToDo]' : '[Mitteilungen]');
					break;
				case we_messaging_proto::FOLDER_SENT:
					$iconbasename = 'msg_sent_folder';
					$folder['Name'] = g_l('modules_messaging', '[Gesendet]');
					break;
				case we_messaging_proto::FOLDER_DONE:
					$iconbasename = 'todo_done_folder';
					$folder['Name'] = g_l('modules_messaging', '[Erledigt]');
					break;
				case we_messaging_proto::FOLDER_REJECT:
					$iconbasename = 'todo_reject_folder';
					$folder['Name'] = g_l('modules_messaging', '[Zurueckgewiesen]');
					break;
				default:
					$iconbasename = $folder['ClassName'] === 'we_todo' ? 'todo_folder' : 'msg_folder';
					break;
			}

			$items[] = (($sf_cnt = $messaging->get_subfolder_count($folder['ID'])) ?
					array(
					'id' => intval($folder['ID']),
					'parentid' => intval($folder['ParentID']),
					'text' => $folder['Name'] . ' - (' . $messaging->get_message_count($folder['ID']) . ')',
					'typ' => 'group',
					'open' => 0,
					'contentType' => 'folder',
					'table' => MESSAGES_TABLE,
					'loaded' => 0,
					'checked' => false,
					'leaf_count' => $sf_cnt,
					'viewclass' => $folder['view_class']
					) :
					array(
					'id' => intval($folder['ID']),
					'parentid' => intval($folder['ParentID']),
					'text' => $folder['Name'] . ' - (' . $messaging->get_message_count($folder['ID']) . ')',
					'typ' => 'item',
					'open' => 0,
					'contentType' => 'folder',
					'table' => MESSAGES_TABLE,
					'viewclass' => $folder['view_class']
					)
				);
		}
		return $items;
	}

	function getJSTreeCode(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';
		if(($param = we_base_request::_(we_base_request::INT, 'msg_param'))){
			switch($param){
				case we_messaging_frames::TYPE_TODO:
					$f = $this->messaging->get_inbox_folder('we_todo');
					break;
				case we_messaging_frames::TYPE_MESSAGE:
					$f = $this->messaging->get_inbox_folder('we_message');
					break;
			}
		}

		return we_html_element::cssLink(CSS_DIR . 'tree.css') .
			parent::getJSTreeCode() .
			we_html_element::jsElement('
parent.document.title = "' . $title . '";
var we_transaction = "' . $this->transaction . '";
var we_frameset="' . $this->frameset . '";
var table="' . MESSAGES_TABLE . '";
') .
			we_html_element::jsScript(JS_DIR . 'messaging_tree.js') .
			we_html_element::jsElement('
var treeData = new container();
function cb_incstate() {
		loaded = true;
		loadData();
		' . (isset($f) ?
					'r_tree_open(' . $f['ID'] . ');
we_cmd("show_folder_content", ' . $f['ID'] . ');' :
					'drawTree();'
				) . '
}');
	}

}
