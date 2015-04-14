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

	function customJSFile(){
		return parent::customJSFile() . we_html_element::jsScript(WE_JS_EXPORT_MODULE_DIR . 'export_tree.js');
	}

	function getJSLoadTree(array $treeItems){
		$js = '
function in_array(arr,item){
	for(i=0;i<arr.length;i++){
		if(arr[i]==item) return true;
	}
	return false;
}
var attribs;' .
			$this->topFrame . '.treeData.table=' . $this->topFrame . '.table;';

		foreach($treeItems as $item){

			$js.='if(' . $this->topFrame . ".indexOfEntry('" . $item["id"] . "')<0){"
				. 'attribs={';
			$elems = '';
			foreach($item as $k => $v){
				$elems.='"' . strtolower($k) . '":' .
					(strtolower($k) === "checked" ?
						'(in_array(' . $this->topFrame . '.SelectedItems.' . $item['table'] . ',"' . $item["id"] . '")?
	\'1\':
	\'' . $v . '\'),
' :
						'\'' . $v . '\',');
			}
			$js.=rtrim($elems, ',') . '};' .
				$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));
					}';
		}
		$js.=$this->topFrame . '.treeData.setstate(' . $this->topFrame . '.treeData.tree_states["select"]);' .
			$this->topFrame . '.drawTree();';

		return $js;
	}

	function getJSStartTree(){
		return 'function startTree(){
frames={
	"top":' . $this->topFrame . ',
	"cmd":' . $this->cmdFrame . ',
	"tree":' . $this->treeFrame . '
};
treeData.frames=frames;
				frames.cmd.location=treeData.frameset+"?pnt=load&cmd=load&tab="+frames.top.table+"&pid=0&openFolders="+frames.top.openFolders[frames.top.table];
			}';
	}

	function getJSDrawTree(){

		return '
function drawTree(){
	var out=\'<div class="treetable \'+treeData.getlayout()+\'"><nobr>\'+
		draw(treeData.startloc,"")+
		"</nobr></div>";
	frames.tree.document.getElementById("treetable").innerHTML=out;
	}' . $this->getJSDraw();
	}

	function getHTMLMultiExplorer($width = 500, $height = 250, $useSelector = true){
		$js = $this->getJSTreeCode() . we_html_element::jsElement('
var SelectedItems= [];
SelectedItems["' . FILE_TABLE . '"]=[];' .
				(defined('OBJECT_FILES_TABLE') ? (
					'SelectedItems["' . OBJECT_FILES_TABLE . '"]=[];
	SelectedItems["' . OBJECT_TABLE . '"]=[];
	') : '') . '

SelectedItems["' . TEMPLATES_TABLE . '"]=[];

var openFolders= {
"' . FILE_TABLE . '":"",' .
				(defined('OBJECT_FILES_TABLE') ? ('
"' . OBJECT_FILES_TABLE . '":"",
"' . OBJECT_TABLE . '":"",
') : '') . '
"' . TEMPLATES_TABLE . '":""
};' . $this->getJSStartTree()) . $this->getStyles();

		if($useSelector){
			$captions = array();
			if(permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")){
				$captions[FILE_TABLE] = g_l('export', '[documents]');
			}
			if(permissionhandler::hasPerm("CAN_SEE_TEMPLATES")){
				$captions[TEMPLATES_TABLE] = g_l('export', '[templates]');
			}
			if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
				$captions[OBJECT_FILES_TABLE] = g_l('export', '[objects]');
			}
			if(defined('OBJECT_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTS")){
				$captions[OBJECT_TABLE] = g_l('export', '[classes]');
			}

			$header = new we_html_table(array(), 3, 1);
			$header->setCol(0, 0, array("bgcolor" => "white"), we_html_tools::getPixel(5, 5));
			$header->setColContent(1, 0, we_html_tools::htmlSelect('headerSwitch', $captions, 1, we_base_request::_(we_base_request::TABLE, 'headerSwitch', 0), false, array('onchange' => "setHead(this.value);"), 'value', $width));
			$header->setColContent(2, 0, we_html_tools::getPixel(5, 5));
			$header = $header->getHtml();
		} else {
			$header = '';
		}
		return $js . $header . we_html_element::htmlDiv(array('id' => 'treetable', 'class' => 'blockWrapper', 'style' => 'width: ' . $width . 'px; height: ' . $height . 'px; border:1px #dce6f2 solid;'), '');
	}

	private static function getQueryParents($path){
		$out = array();
		while($path != '/' && $path){
			$out[] = 'Path="' . $path . '"';
			$path = dirname($path);
		}
		return $out ? implode(' OR ', $out) : '';
	}

	private static function getItems($table, $ParentID, array &$treeItems){
		static $openFolders = array();

		$DB_WE = new DB_WE();
		$elem = 'ID,ParentID,Path,Text,Icon,IsFolder,ModDate,ContentType';

		switch($table){
			case FILE_TABLE :
				$selDocs = explode(',', $_SESSION['weS']['exportVars_session']["selDocs"]);
				$elem.=',Published';
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$elem.=',Published,IsClassFolder';
				$selObjs = explode(',', $_SESSION['weS']['exportVars_session']["selObjs"]);
		}

		$DB_WE->query('SELECT ' . $elem . ' FROM ' . $DB_WE->escape($table) . ' WHERE  ParentID=' . intval($ParentID) . ' AND((1' . we_users_util::makeOwnersSql() . ')' . $GLOBALS['wsQuery'] . ') ORDER BY IsFolder DESC,(text REGEXP "^[0-9]") DESC,ABS(text),Text');

		while($DB_WE->next_record()){
			$ID = $DB_WE->f("ID");
			$ParentID = $DB_WE->f("ParentID");
			$Text = $DB_WE->f("Text");
			$Path = $DB_WE->f("Path");
			$IsFolder = $DB_WE->f("IsFolder");
			$ContentType = $DB_WE->f("ContentType");
			$Icon = $DB_WE->f("Icon");
			$IsClassFolder = $DB_WE->f("IsClassFolder");
			$published = (($DB_WE->f("Published") != 0) && ($DB_WE->f("Published") < $DB_WE->f("ModDate"))) ? -1 : $DB_WE->f("Published");

			switch($table){
				case FILE_TABLE:
					$checked = (isset($_SESSION['weS']['exportVars_session']["selDocs"]) && in_array($ID, $selDocs));
					break;
				case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					$checked = (isset($_SESSION['weS']['exportVars_session']["selObjs"]) && in_array($ID, $selObjs));
					break;
				default:
					$published = 1;
					$checked = 0;
					break;
			}

			$OpenCloseStatus = in_array($ID, $openFolders);


			$treeItems[] = array(
				"icon" => $Icon,
				"id" => $ID,
				"parentid" => $ParentID,
				"text" => $Text,
				"contenttype" => $ContentType,
				"isclassfolder" => $IsClassFolder,
				"table" => $table,
				"checked" => $checked,
				"typ" => $IsFolder ? "group" : "item",
				"open" => $OpenCloseStatus,
				"published" => $published,
				"disabled" =>  in_array($Path, $GLOBALS['parentpaths']),
				"tooltip" => $ID
			);

			if($IsFolder && $OpenCloseStatus){
				self::getItems($table, $ID, $treeItems);
			}
		}
	}

	public function loadHTML($table, $parentFolder){
		$GLOBALS["OBJECT_FILES_TREE_COUNT"] = 20;
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
		} else if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!permissionhandler::hasPerm("ADMINISTRATOR"))){
			$ac = we_users_util::getAllowedClasses($GLOBALS['DB_WE']);
			foreach($ac as $cid){
				$path = id_to_path($cid, OBJECT_TABLE);
				$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
				$wsQuery[] = 'Path="' . $GLOBALS['DB_WE']->escape($path) . '"';
			}
		}

		$GLOBALS['wsQuery'] = ' ' . ($wsQuery ? ' OR (' . implode(' OR ', $wsQuery) . ')' : '');

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
				break;
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				if(!permissionhandler::hasPerm("CAN_SEE_OBJECTS")){
					return 0;
				}
				break;
		}

		$treeItems = array();

		self::getItems($table, $parentFolder, $treeItems);

		echo we_html_element::htmlDocType() . we_html_element::htmlHtml(
			we_html_element::htmlHead(we_html_tools::getHtmlInnerHead() .
				we_html_element::jsElement('
if(!' . $this->topFrame . '.treeData) {' .
					we_message_reporting::getShowMessageCall("A fatal error occured", we_message_reporting::WE_MESSAGE_ERROR) . '
}' .
					($parentFolder ? '' :
						$this->topFrame . '.treeData.clear();' .
						$this->topFrame . '.treeData.add(new ' . $this->topFrame . '.rootEntry(\'' . $parentFolder . '\',\'root\',\'root\'));'
					) .
					$this->getJSLoadTree($treeItems)
				)
			) .
			we_html_element::htmlBody(array("bgcolor" => "#ffffff"))
		);
	}

}
