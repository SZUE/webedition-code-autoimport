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
class we_export_tree extends weMainTree{

	public function __construct($frameset = "", $topFrame = "", $treeFrame = "", $cmdFrame = ""){
		parent::__construct($frameset, $topFrame, $treeFrame, $cmdFrame);
	}

	function getJSInfo(){
		return 'function info(text) {}';
	}

	function getJSOpenClose(){

		return '
function openClose(id){

	if(id=="") return;

	var eintragsIndex = indexOfEntry(id);
	var status;

	if(treeData[eintragsIndex].open==0) openstatus=1;
	else openstatus=0;
	treeData[eintragsIndex].open=openstatus;
	if(openstatus && treeData[eintragsIndex].loaded!=1){
		' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=load&tab="+' . $this->topFrame . '.table+"&cmd=load&pid="+id;
		' . $this->topFrame . '.openFolders[' . $this->topFrame . '.table]+=","+id;
	}else{
		var arr = ' . $this->topFrame . '.openFolders[' . $this->topFrame . '.table].split(",");
		' . $this->topFrame . '.openFolders[' . $this->topFrame . '.table]="";
		for(var t=0;t<arr.length;t++){
			if(arr[t]!="" && arr[t]!=id){
				' . $this->topFrame . '.openFolders[' . $this->topFrame . '.table]+=","+arr[t];
			}
		}
		drawTree();
	}
	if(openstatus==1) treeData[eintragsIndex].loaded=1;
}';
	}

	function getJSLoadTree($treeItems){
		$js = '
function in_array(arr,item){
	for(i=0;i<arr.length;i++){
		if(arr[i]==item) return true;
	}
	return false;
}
var attribs=new Array();' .
			$this->topFrame . '.treeData.table=' . $this->topFrame . '.table;';

		foreach($treeItems as $item){
			//if(strpos($item["contenttype"], "text") !== false || strpos($item["contenttype"], "folder") !== false || strpos($item["contenttype"], "object") !== false){

			$js.="if(" . $this->topFrame . ".indexOfEntry('" . $item["id"] . "')<0){ \n";
			foreach($item as $k => $v){
				if(strtolower($k) === "checked"){
					$js.='
if(in_array(' . $this->topFrame . '.SelectedItems[attribs["table"]],"' . $item["id"] . '")){
	attribs["' . strtolower($k) . '"]=\'1\';
}else{
	attribs["' . strtolower($k) . '"]=\'' . $v . '\';
}';
				} else {
					$js.='attribs["' . strtolower($k) . '"]=\'' . $v . '\';';
				}
			}
			$js.=$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));
					}';
			//}
		}
		$js.=$this->topFrame . '.treeData.setstate(' . $this->topFrame . '.treeData.tree_states["select"]);' .
			$this->topFrame . '.drawTree();';

		return $js;
	}

	function getJSStartTree(){
		return 'function startTree(){
				' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=load&cmd=load&tab="+' . $this->topFrame . '.table+"&pid=0&openFolders="+' . $this->topFrame . '.openFolders[' . $this->topFrame . '.table];
			}';
	}

	function getJSTreeCode(){
		return weMainTree::getJSTreeCode() .
			we_html_element::jsElement($this->getJSStartTree());
	}

	function getJSDrawTree(){

		return '
function drawTree(){
	var out=\'<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>' . we_html_tools::getPixel(5, 7) . '</td></tr><tr><td class="\'+treeData.getlayout()+\'">\n<nobr>\n\';
	out+=draw(treeData.startloc,"");
	out+="</nobr>\n</td></tr></table>\n";
	' . $this->treeFrame . '.document.getElementById("treetable").innerHTML=out;
	/*nurl="treeMain.php";
	win=window.open(nurl);
	win.document.open();
	win.document.write(top.treeHTML.innerHTML);
	win.document.close();*/
	}' . $this->getJSDraw();
	}

	function getJSCheckNode(){
		return '
function checkNode(imgName) {
	var object_name = imgName.substring(4,imgName.length);
	for(i=1;i<=treeData.len;i++) {
		if(treeData[i].id == object_name) {
			' . $this->treeFrame . '.populate(treeData[i].id,treeData.table);
			if(treeData[i].checked==1) {
				if(document.images) {
					eval("if("+treeData.treeFrame+".document.images[imgName]) "+treeData.treeFrame+".document.images[imgName].src=treeData.check0_img.src;");
				}
				treeData[i].checked=0;
				if(' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table].length>1){
					found=false;
					' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table].length=' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table].length+1;
					for(z=0;z<' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table].length;z++){
						if(' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table][z]==treeData[i].id) found=true;
						if(found){
							' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table][z]=' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table][z+1];
					}}
					' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table].length=' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table].length-2;
				}
				else ' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table]=new Array();

				treeData[i].applylayout();
				break;
			}
			else {
				if(document.images) {
					eval("if("+treeData.treeFrame+".document.images[imgName]) "+treeData.treeFrame+".document.images[imgName].src=treeData.check1_img.src;");
				}
				treeData[i].checked=1;
				' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.table].push(treeData[i].id);
				treeData[i].applylayout();
				break;
			}
		}

	}
	if(top.content) {
		if(typeof(top.content.hot) != "undefined") {
			top.content.hot=1;
		}
	}
	if(!document.images){
	 drawTree();
	}
}';
	}

	function getHTMLMultiExplorer($width = 500, $height = 250, $useSelector = true){
		$js = $this->getJSTreeCode() . we_html_element::jsElement('
function populate(id,table){

}

function setHead(tab){
	' . $this->topFrame . '.table=tab;
	' . $this->topFrame . '.document.we_form.table.value=tab;
	setTimeout("' . $this->topFrame . '.startTree()",100);
}

var SelectedItems= new Array();
SelectedItems["' . FILE_TABLE . '"]=new Array();' .
				(defined('OBJECT_FILES_TABLE') ? (
					'SelectedItems["' . OBJECT_FILES_TABLE . '"]=new Array();
	SelectedItems["' . OBJECT_TABLE . '"]=new Array();
	') : '') . '

SelectedItems["' . TEMPLATES_TABLE . '"]=new Array();

var openFolders= new Array();
openFolders["' . FILE_TABLE . '"]="";' .
				(defined('OBJECT_FILES_TABLE') ? ('
openFolders["' . OBJECT_FILES_TABLE . '"]="";
openFolders["' . OBJECT_TABLE . '"]="";
') : '') . '
openFolders["' . TEMPLATES_TABLE . '"]="";


' . $this->getJSStartTree());

		$style_code = "";
		if(isset($this->SelectionTree->styles)){
			foreach($this->SelectionTree->styles as $st){
				$style_code.=$st . "\n";
			}
		}

		if($useSelector){
			$header = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 1);

			$header->setCol(0, 0, array("bgcolor" => "white"), we_html_tools::getPixel(5, 5));
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
		$where = ' WHERE  ParentID=' . intval($ParentID) . ' AND((1' . we_users_util::makeOwnersSql() . ')' . $GLOBALS['wsQuery'] . ')';
		//if($GLOBALS['table']==FILE_TABLE) $where .= " AND (ClassName='we_webEditionDocument' OR ClassName='we_folder')";
		$elem = 'ID,ParentID,Path,Text,Icon,IsFolder,ModDate' . (($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE)) ? ",Published" : "") . ((defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE) ? ",IsClassFolder" : "");

		switch($table){
			case FILE_TABLE :
			case TEMPLATES_TABLE:
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$elem .= ',ContentType';
		}

		$DB_WE->query('SELECT ' . $elem . ', ABS(text) as Nr, (text REGEXP "^[0-9]") AS isNr FROM ' . $DB_WE->escape($table) . ' ' . $where . ' ORDER BY isNr DESC,Nr,Text');

		while($DB_WE->next_record()){
			$ID = $DB_WE->f("ID");
			$ParentID = $DB_WE->f("ParentID");
			$Text = $DB_WE->f("Text");
			$Path = $DB_WE->f("Path");
			$IsFolder = $DB_WE->f("IsFolder");
			$ContentType = $DB_WE->f("ContentType");
			$Icon = $DB_WE->f("Icon");
			$IsClassFolder = $DB_WE->f("IsClassFolder");

			$checked = 0;
			switch($table){
				case FILE_TABLE:
					$published = (($DB_WE->f("Published") != 0) && ($DB_WE->f("Published") < $DB_WE->f("ModDate"))) ? -1 : $DB_WE->f("Published");
					if(isset($_SESSION['weS']['exportVars_session']["selDocs"]) && in_array($ID, makeArrayFromCSV($_SESSION['weS']['exportVars_session']["selDocs"]))){
						$checked = 1;
					}
					break;
				case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					$published = (($DB_WE->f("Published") != 0) && ($DB_WE->f("Published") < $DB_WE->f("ModDate"))) ? -1 : $DB_WE->f("Published");
					if(isset($_SESSION['weS']['exportVars_session']["selObjs"]) && in_array($ID, makeArrayFromCSV($_SESSION['weS']['exportVars_session']["selObjs"]))){
						$checked = 1;
					}
					break;
				default:
					$published = 1;
					break;
			}

			$OpenCloseStatus = (in_array($ID, $openFolders) ? 1 : 0);
			$disabled = in_array($Path, $GLOBALS['parentpaths']) ? 1 : 0;

			$typ = $IsFolder ? "group" : "item";

			$treeItems[] = array(
				"icon" => $Icon,
				"id" => $ID,
				"parentid" => $ParentID,
				"text" => $Text,
				"contenttype" => $ContentType,
				"isclassfolder" => $IsClassFolder,
				"table" => $table,
				"checked" => $checked,
				"typ" => $typ,
				"open" => $OpenCloseStatus,
				"published" => $published,
				"disabled" => $disabled,
				"tooltip" => $ID
			);

			if($typ === "group" && $OpenCloseStatus == 1){
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
