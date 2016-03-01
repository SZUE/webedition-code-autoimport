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
class we_export_tree extends weTree{

	protected function customJSFile(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'export/export_tree.js');
	}

	function getJSLoadTree($clear, array $treeItems){
		$js = $this->topFrame . '.treeData.table=' . $this->topFrame . '.table;';

		foreach($treeItems as $item){

			$js.=($clear ? '' : 'if(' . $this->topFrame . '.treeData.indexOfEntry("' . $item['id'] . '")<0){' ) .
					$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node({';
			$elems = '';
			foreach($item as $k => $v){
				$elems.='"' . strtolower($k) . '":' .
						(strtolower($k) === "checked" ?
								'(WE().util.in_array("' . $item["id"] . '", ' . $this->topFrame . '.SelectedItems.' . $item['table'] . ')?
	\'1\':
	\'' . $v . '\'),
' :
								'\'' . $v . '\',');
			}
			$js.=rtrim($elems, ',') . '}));' . ($clear ? '' : '}');
		}
		$js.=$this->topFrame . '.treeData.setState(' . $this->topFrame . '.treeData.tree_states["select"]);' .
				$this->topFrame . '.drawTree();';

		return $js;
	}

	function getJSStartTree(){
		return 'function startTree(){
	treeData.frames={
		top:' . $this->topFrame . ',
		cmd:' . $this->cmdFrame . ',
		tree:' . $this->treeFrame . '
	};
	treeData.frames.cmd.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=load&cmd=load&tab="+treeData.frames.top.table+"&pid=0&openFolders="+treeData.frames.top.openFolders[treeData.frames.top.table];
}';
	}

	function getHTMLMultiExplorer($width = 500, $height = 250, $useSelector = true){
		$js = $this->getJSTreeCode() . we_html_element::jsElement('
var SelectedItems={
	' . FILE_TABLE . ':[],
	' . TEMPLATES_TABLE . ':[],' .
						(defined('OBJECT_FILES_TABLE') ? ('
	' . OBJECT_FILES_TABLE . ':[],
	' . OBJECT_TABLE . ':[],
	') : ''
						) . '
};

var openFolders= {
	' . FILE_TABLE . ':"",
	' . TEMPLATES_TABLE . ':"",' .
						(defined('OBJECT_FILES_TABLE') ? ('
	' . OBJECT_FILES_TABLE . ':"",
	' . OBJECT_TABLE . ':"",
') : ''
						) . '
};' . $this->getJSStartTree()) . we_html_element::cssLink(CSS_DIR . 'tree.css');

		if($useSelector){
			$captions = array();
			if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
				$captions[FILE_TABLE] = g_l('export', '[documents]');
			}
			if(permissionhandler::hasPerm('CAN_SEE_TEMPLATES')){
				$captions[TEMPLATES_TABLE] = g_l('export', '[templates]');
			}
			if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
				$captions[OBJECT_FILES_TABLE] = g_l('export', '[objects]');
			}
			if(defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS")){
				$captions[OBJECT_TABLE] = g_l('export', '[classes]');
			}
			if(we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION) && permissionhandler::hasPerm("CAN_SEE_COLLECTIONS")){
				$captions[VFILE_TABLE] = g_l('export', '[collections]');
			}

			$header = we_html_element::htmlDiv(array('style' => 'margin:5px 0px;'), we_html_tools::htmlSelect('headerSwitch', $captions, 1, we_base_request::_(we_base_request::TABLE, 'headerSwitch', 0), false, array('onchange' => "setHead(this.value);"), 'value', $width));
		} else {
			$header = '';
		}
		return $js . $header . we_html_element::htmlDiv(array('id' => 'treetable', 'class' => 'blockWrapper', 'style' => 'position: static;width: ' . $width . 'px; height: ' . $height . 'px; border:1px #dce6f2 solid;'), '');
	}

	private static function getQueryParents($path){
		$out = array();
		while($path != '/' && $path){
			$out[] = 'Path="' . $path . '"';
			$path = dirname($path);
		}
		return $out ? implode(' OR ', $out) : '';
	}

	private static function getItems($table, $ParentID, array &$treeItems, $of = array(), we_database_base $DB_WE = null){
		static $openFolders = -1;
		if($openFolders == -1){
			$openFolders = $of;
		}

		$DB_WE = $DB_WE? : new DB_WE();
		$elem = 'ID,ParentID,Path,Text,IsFolder,ModDate,ContentType';

		switch($table){
			case FILE_TABLE :
				$selDocs = isset($_SESSION['weS']['export_session']) ? explode(',', $_SESSION['weS']['export_session']->selDocs) : array();
				$elem.=',Published,0 AS IsClassFolder';
				break;
			case (defined('TEMPLATES_TABLE') ? TEMPLATES_TABLE : 'TEMPLATES_TABLE'):
				$selDocs = isset($_SESSION['weS']['export_session']) ? explode(',', $_SESSION['weS']['export_session']->selTempl) : array();
				$elem.=',ModDate AS Published,0 AS IsClassFolder';
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$elem.=',Published,IsClassFolder';
				$selDocs = isset($_SESSION['weS']['export_session']) ? explode(',', $_SESSION['weS']['export_session']->selObjs) : array();
				break;
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$selDocs = isset($_SESSION['weS']['export_session']) ? explode(',', $_SESSION['weS']['export_session']->selClasses) : array();
				$elem.=',ModDate AS Published,0 AS IsClassFolder';
				break;
		}

		$DB_WE->query('SELECT ' . $elem . ' FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($ParentID) . ' AND((1' . we_users_util::makeOwnersSql() . ')' . $GLOBALS['wsQuery'] . ') ORDER BY IsFolder DESC,(text REGEXP "^[0-9]") DESC,ABS(text),Text');

		$entries = $DB_WE->getAll();

		foreach($entries as $entry){
			$ID = $entry["ID"];
			$IsFolder = $entry["IsFolder"];
			$published = $entry["Published"];

			$OpenCloseStatus = in_array($ID, $openFolders);

			$treeItems[] = array(
				'id' => $ID,
				'parentid' => $entry['ParentID'],
				'text' => $entry['Text'],
				'contenttype' => $entry['ContentType'],
				'isclassfolder' => $entry['IsClassFolder'],
				'table' => $table,
				'checked' => (isset($selDocs) && in_array($ID, $selDocs)),
				'typ' => $IsFolder ? 'group' : 'item',
				'open' => $OpenCloseStatus,
				'published' => ($published && ($published < $entry['ModDate'])) ? -1 : $published,
				'disabled' => in_array($entry['Path'], $GLOBALS['parentpaths']),
				'tooltip' => $ID
			);

			if($IsFolder && $OpenCloseStatus){
				self::getItems($table, $ID, $treeItems, $of, $DB_WE);
			}
		}
	}

	public function loadHTML($table, $parentFolder, $openFolders){
		$GLOBALS['parentpaths'] = $wsQuery = array();

		if(($ws = get_ws($table))){
			$wsPathArray = id_to_path($ws, $table, $GLOBALS['DB_WE'], false, true);
			foreach($wsPathArray as $path){
				$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%" OR ' . self::getQueryParents($path);
				while($path != '/' && $path){
					$GLOBALS['parentpaths'][] = $path;
					$path = dirname($path);
				}
			}
		}

		switch($table){
			case FILE_TABLE:
				if(!permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")){
					return 0;
				}
				break;
			case TEMPLATES_TABLE:
				if(!permissionhandler::hasPerm("CAN_SEE_TEMPLATES")){
					return 0;
				}
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				if(!permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
					return 0;
				}
				if(!permissionhandler::hasPerm("ADMINISTRATOR")){
					$ac = we_users_util::getAllowedClasses($GLOBALS['DB_WE']);
					foreach($ac as $cid){
						$path = id_to_path($cid, OBJECT_TABLE);
						$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
						$wsQuery[] = 'Path="' . $GLOBALS['DB_WE']->escape($path) . '"';
					}
				}
				break;
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				if(!permissionhandler::hasPerm("CAN_SEE_OBJECTS")){
					return 0;
				}
				break;
			case VFILE_TABLE:
				if(!permissionhandler::hasPerm("CAN_SEE_COLLECTIONS")){
					return 0;
				}
				break;
		}

		$GLOBALS['wsQuery'] = ' ' . ($wsQuery ? ' OR (' . implode(' OR ', $wsQuery) . ')' : '');

		$treeItems = array();

		self::getItems($table, $parentFolder, $treeItems, $openFolders);

		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', we_html_element::jsElement('
if(!' . $this->topFrame . '.treeData) {' .
						we_message_reporting::getShowMessageCall("A fatal error occured", we_message_reporting::WE_MESSAGE_ERROR) . '
}' .
						($parentFolder ? '' :
								$this->topFrame . '.treeData.clear();' .
								$this->topFrame . '.treeData.add(' . $this->topFrame . '.node.prototype.rootEntry(\'' . $parentFolder . '\',\'root\',\'root\'));'
						) .
						$this->getJSLoadTree(!$parentFolder, $treeItems)
				), we_html_element::htmlBody(array("bgcolor" => "#ffffff"))
		);
	}

}
