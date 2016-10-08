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
class we_workflow_tree extends we_tree_base{

	protected function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'workflow_tree.js');
	}

	public static function getItems($ParentId, $Offset = 0, $Segment = 500, $sort = false){
		$items = [];
		$db = new DB_WE();
		$db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' ORDER BY Text ASC');
		$ids = $db->getAll(true);
		foreach($ids as $id){
			$workflowDef = new we_workflow_workflow($id);
			$items[] = array(
				'id' => $workflowDef->ID,
				'parentid' => 0,
				'text' => oldHtmlspecialchars(addslashes($workflowDef->Text)),
				'typ' => 'group',
				'open' => 0,
				'contenttype' => 'folder',
				'table' => WORKFLOW_TABLE,
				'loaded' => 0,
				'checked' => false,
				'class' => ($workflowDef->Status ? '' : 'blue')
			);

			foreach($workflowDef->documents as $v){
				$items[] = array(
					'id' => $v["ID"],
					'parentid' => $workflowDef->ID,
					'text' => oldHtmlspecialchars(addslashes($v["Text"])),
					'typ' => 'item',
					'open' => 0,
					'contenttype' => 'file',
					'table' => FILE_TABLE,
				);
			}
		}

		return $items;
	}

}
