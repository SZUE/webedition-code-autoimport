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

	function customJSFile(){
		return parent::customJSFile() . we_html_element::jsScript(JS_DIR . 'messaging_tree.js');
	}

	function getJSStartTree(){
		return parent::getTree_g_l() . '
var save_changed_folder="' . g_l('modules_messaging', '[save_changed_folder]') . '";
var we_dir="' . WEBEDITION_DIR . '";
var messaging_module_dir="' . WE_MESSAGING_MODULE_DIR . '";

var table="' . MESSAGES_TABLE . '";

function startTree(){
			frames={
	"top":' . $this->topFrame . ',
	"cmd":' . $this->cmdFrame . '
	};
	treeData.frames=frames;
	frames.cmd.location=treeData.frameset+"?pnt=cmd&pid=0";
}

function translate(inp){
	if(inp.substring(0,12).toLowerCase() == "messages - ("){
		return "' . g_l('modules_messaging', '[Mitteilungen]') . ' - ("+inp.substring(12,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "task - ("){
		return "' . g_l('modules_messaging', '[ToDo]') . ' - ("+inp.substring(8,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "todo - ("){
		return "' . g_l('modules_messaging', '[ToDo]') . ' - ("+inp.substring(8,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "done - ("){
		return "' . g_l('modules_messaging', '[Erledigt]') . ' - ("+inp.substring(8,inp.length);
	}else if(inp.substring(0,12).toLowerCase() == "rejected - ("){
		return "' . g_l('modules_messaging', '[Zurueckgewiesen]') . ' - ("+inp.substring(12,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "sent - ("){
		return "' . g_l('modules_messaging', '[Gesendet]') . ' - ("+inp.substring(8,inp.length);
	}else{
		return inp;
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
					'id' => $folder['ID'],
					'parentid' => $folder['ParentID'],
					'text' => $folder['Name'] . ' - (' . $messaging->get_message_count($folder['ID'], '') . ')',
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
					'id' => $folder['ID'],
					'parentid' => $folder['ParentID'],
					'text' => $folder['Name'] . ' - (' . $messaging->get_message_count($folder['ID'], '') . ')',
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

}
