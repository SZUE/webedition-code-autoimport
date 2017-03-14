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
class we_messaging_tree extends we_tree_base{
	private $transaction;

	function __construct(we_base_jsCmd $jsCmd, $frameset, $topFrame, $treeFrame, $cmdFrame, $transaction){
		parent::__construct($jsCmd, $frameset, $topFrame, $treeFrame, $cmdFrame);
		$this->transaction = $transaction;
	}

	protected function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'messaging_tree.js', 'initTree();');
	}

	public static function getItems($ParentID, $offset = 0, $segment = 500, $sort = false){
		$items = [];
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
				['id' => intval($folder['ID']),
				'parentid' => intval($folder['ParentID']),
				'text' => $folder['Name'] . ' - (' . $messaging->get_message_count($folder['ID']) . ')',
				'typ' => 'group',
				'open' => 0,
				'contenttype' => we_base_ContentTypes::FOLDER,
				'table' => MESSAGES_TABLE,
				'loaded' => 0,
				'checked' => false,
				'leaf_count' => $sf_cnt,
				'viewclass' => $folder['view_class']
				] :
				['id' => intval($folder['ID']),
				'parentid' => intval($folder['ParentID']),
				'text' => $folder['Name'] . ' - (' . $messaging->get_message_count($folder['ID']) . ')',
				'typ' => 'item',
				'open' => 0,
				'contenttype' => we_base_ContentTypes::FOLDER,
				'table' => MESSAGES_TABLE,
				'viewclass' => $folder['view_class']
				]
				);
		}
		return $items;
	}

	function getMsgJSTreeCode(we_messaging_messaging $messaging){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';
		if(($param = we_base_request::_(we_base_request::INT, 'msg_param'))){
			switch($param){
				case we_messaging_frames::TYPE_TODO:
					$f = $messaging->get_inbox_folder('we_todo');
					break;
				case we_messaging_frames::TYPE_MESSAGE:
					$f = $messaging->get_inbox_folder('we_message');
					break;
			}
		}

		return we_html_element::cssLink(CSS_DIR . 'tree.css') .
			we_html_element::jsElement('
parent.document.title = "' . $title . '";
var we_transaction = "' . $this->transaction . '";
function cb_incstate() {
		loaded = true;
		loadData();
		' . (isset($f) ?
				'r_tree_open(' . $f['ID'] . ');
we_cmd("show_folder_content", ' . $f['ID'] . ');' :
				'drawTree();'
				) . '
}') .
			parent::getJSTreeCode();
	}

}
