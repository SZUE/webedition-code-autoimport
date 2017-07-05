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
class we_thumbnail_tree extends we_tree_base{

	protected function customJSFile(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'thumbnail/thumb_tree.js', 'initTree(0);');
	}

	public static function getItems($ParentID, $offset = 0, $segment = 500, $sort = false){
		$db = new DB_WE();

		$items = [];
		$db->query('SELECT ID,Name FROM ' . THUMBNAILS_TABLE.' ORDER BY Name');

		while($db->next_record(MYSQLI_ASSOC)){
			$items[] = [
				'id' => intval($db->f('ID')),
				'parentid' => 0,
				'text' => addslashes($db->f('Name')),
				'typ' => 'item',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $db->f('ID'),
				'offset' => $offset,
				'contenttype' => 'we/thumbnail',
				'published' => 1,
				'table' => THUMBNAILS_TABLE
			];
		}
		return $items;
	}

}
