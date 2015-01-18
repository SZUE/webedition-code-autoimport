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
class weMainTree extends weTree{

	function __construct($frameset = "", $topFrame = "", $treeFrame = "", $cmdFrame = ""){
		parent::__construct($frameset, $topFrame, $treeFrame, $cmdFrame);

		$this->setNodeLayouts(array(
			'item' => 'item',
			'group' => 'group',
			'threedots' => 'changed',
			'item-disabled' => 'disabled',
			'group-disabled' => 'disabled',
			'group-disabled-open' => 'disabled',
			'item-checked' => 'checked_item',
			'group-checked' => 'checked_group',
			'group-open' => 'group',
			'group-checked-open' => 'checked_group',
			'item-notpublished' => 'notpublished',
			'item-checked-notpublished' => 'checked_notpublished',
			'item-changed' => 'changed',
			'item-checked-changed' => 'checked_changed',
			'item-selected' => 'selected_item',
			'item-selected-notpublished' => 'selected_notpublished_item',
			'item-selected-changed' => 'selected_changed_item',
			'group-selected' => 'selected_group',
			'group-selected-open' => 'selected_open_group'
		));

		$this->setStyles(array(
			'.item {cursor: pointer;}',
			'.group {cursor: pointer;}',
			'.selected_item {background-color: #D4DBFA;}',
			'.selected_group {background-color: #D4DBFA;}',
			'.selected_notpublished_item {color: red;}',
			'.selected_open_group {color: black;}',
			)
		);
	}

	function getJSOpenClose(){
		return '
function openClose(id) {
	if(id==""){
		return;
	}
	var eintragsIndex = indexOfEntry(id);
	var openstatus = (treeData[eintragsIndex].open==0 ? 1:0);
	treeData[eintragsIndex].open=openstatus;
	if(openstatus && treeData[eintragsIndex].loaded!=1){
		we_cmd("loadFolder",top.treeData.table,treeData[eintragsIndex].id);
		toggleBusy(1);
	}else{
		we_cmd("closeFolder",top.treeData.table,treeData[eintragsIndex].id);
		drawTree();
	}
	if(openstatus==1){
		treeData[eintragsIndex].loaded=1;
	}
}';
	}

	function getJSTreeFunctions($overridden){

		return parent::getJSTreeFunctions() . ($overridden ? '' : '
function doClick(id){
	var node=' . $this->topFrame . '.get(id);
	var ct=node.contenttype;
	var table=node.table;
	setScrollY();
	if(' . $this->topFrame . '.wasdblclick && ct != \'folder\' && table!=\'' . TEMPLATES_TABLE . '\'' . (defined('OBJECT_TABLE') ? ' && table!=\'' . OBJECT_TABLE . '\' && table!=\'' . OBJECT_FILES_TABLE . '\'' : '' ) . '){
		top.openBrowser(id);
		setTimeout(\'wasdblclick=0;\',400);
	} else {
		top.weEditorFrameController.openDocument(table,id,ct);
	}
}');
	}

	function getJSUpdateTreeScript($doc, $select = true){

		$published = ((($doc->Published != 0) && ($doc->Published < $doc->ModDate) && ($doc->ContentType == we_base_ContentTypes::HTML || $doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $doc->ContentType === we_base_ContentTypes::OBJECT_FILE)) ? -1 : $doc->Published);

//	This is needed in SeeMode
		$s = '
isEditInclude = false;
weWindow = top;
while(1){
	if(!weWindow.top.opener || weWindow.top.opener.top.win){
			break;
	} else {
		 isEditInclude = true;
		 weWindow = weWindow.opener.top;
	}
}';
		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
			return $s;
		}

		$s .= '
if(weWindow.treeData){
	var obj = weWindow.treeData;
	var isIn = false;' .
			($select ? '
	weWindow.treeData.selection_table="' . $doc->Table . '";
	weWindow.treeData.selection="' . $doc->ID . '";' :
				'weWindow.treeData.unselectnode();') . '
	if(weWindow.treeData.table == "' . $doc->Table . '"){
		if(weWindow.treeData[top.indexOfEntry(' . $doc->ParentID . ')]){
				var attribs={
				"id":\'' . $doc->ID . '\',
				"parentid":\'' . $doc->ParentID . '\',
				"text":\'' . $doc->Text . '\',
				"published":\'' . $published . '\',
				"table":\'' . $doc->Table . '\'
				};

				var visible=(' . $this->topFrame . '.indexOfEntry(' . $doc->ParentID . ')!=-1?
					' . $this->topFrame . '.treeData[' . $this->topFrame . '.indexOfEntry(' . $doc->ParentID . ')].open:
						0);

				if(' . $this->topFrame . '.indexOfEntry(' . $doc->ID . ')!=-1){
						isIn=true;
						var ai = 1;
						while (ai <= ' . $this->topFrame . '.treeData.len) {
							if (' . $this->topFrame . '.treeData[ai].id==attribs["id"]){
								' . $this->topFrame . '.treeData[ai].text=attribs["text"];
								' . $this->topFrame . '.treeData[ai].parentid=attribs["parentid"];
								' . $this->topFrame . '.treeData[ai].table=attribs["table"];
								' . $this->topFrame . '.treeData[ai].published=attribs["published"];
							}
							++ai;
						}
			}else{
				attribs["icon"]=\'' . $doc->Icon . '\';
				attribs["contenttype"]=\'' . $doc->ContentType . '\';
				attribs["isclassfolder"]=\'' . (isset($doc->IsClassFolder) ? $doc->IsClassFolder : false) . '\';
				attribs["checked"]=\'0\';
				attribs["typ"]=\'' . ($doc->IsFolder ? "group" : "item") . '\';
				attribs["open"]=\'0\';
				attribs["disabled"]=\'0\';
				attribs["tooltip"]=\'' . $doc->ID . '\';
				' . $this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));
		}
		weWindow.drawTree();
	}else if(' . $this->topFrame . '.indexOfEntry(' . $doc->ID . ')!=-1){
		' . $this->topFrame . '.deleteEntry(' . $doc->ID . ');
	}
}
}';


		return $s;
	}

	function getJSGetLayout(){
		return '
function getLayout(){
		if(this.typ=="threedots") return treeData.node_layouts["threedots"];
		var layout_key=(this.typ=="group" ? "group" : "item")+
			(this.selected==1 ? "-selected" : "")+
			(this.disabled==1 ? "-disabled" : "")+
			(this.checked==1 ? "-checked" : "")+
			(this.open==1 ? "-open" : "")+
			(this.typ=="item" && this.published==0 ? "-notpublished" : "")+
			(this.typ=="item" && this.published==-1 ? "-changed" : "") ;

		return treeData.node_layouts[layout_key];
}';
	}

	function getJSInfo(){
		return '
function info(text) {
	t=TreeInfo.window.document.getElementById("infoField");
	s=TreeInfo.window.document.getElementById("search");
	if(text!=" "){
		s.style.display="none";
		t.style.display="block";
		t.innerHTML = text;
	} else {
		s.style.display="block";
		t.innerHTML = text;
		t.style.display="none";
	}
}';
	}

	function getJSUpdateItem(){
		return '
function updateEntry(id,text,pid,tab){
	//if((treeData.table == tab)&&(treeData[indexOfEntry(pid)])&&(treeData[indexOfEntry(pid)].loaded)){
	if((treeData.table == tab)&&(treeData[indexOfEntry(pid)])){
		var ai = 1;
		while (ai <= treeData.len) {
			if (treeData[ai].id==id){
				if(text){
				treeData[ai].text=text;
				}
				if(pid){
				treeData[ai].parentid=pid;
				}
				if(tab){
				treeData[ai].table=tab;
				}
			}
			ai++;
		}
		drawTree();
	}
}';
	}

	function getJSMakeNewEntry(){
		return '
function makeNewEntry(icon,id,pid,txt,open,ct,tab){
	if(treeData.table == tab){
		if(treeData[indexOfEntry(pid)]){
			if(treeData[indexOfEntry(pid)].loaded){

				var attribs={
					"id":id,
					"icon":icon,
					"text":txt,
					"parentid":pid,
					"open":open,
					"typ":(ct=="folder" ? "group" : "item"),
					"table":tab,
					"tooltip":id,
					"contenttype":ct,
					"disabled":0,
					"selected":0
				};
				if(attribs["typ"]=="item"){
					attribs["published"]=0;
				}

				treeData.addSort(new node(attribs));

				drawTree();
			}
		}
	}
}';
	}

	function getJSIncludeFunctions(){
		return parent::getJSIncludeFunctions() . '
we_scrollY["' . FILE_TABLE . '"] = 0;
we_scrollY["' . TEMPLATES_TABLE . '"] = 0;' .
			(defined('OBJECT_TABLE') ? '
we_scrollY["' . OBJECT_TABLE . '"] = 0;
we_scrollY["' . OBJECT_FILES_TABLE . '"] = 0;' :
				'') . '
treeData.table="' . FILE_TABLE . '";';
	}

	function getJSLoadTree($treeItems){
		$js = 'var attribs={};';

		if(is_array($treeItems)){
			foreach($treeItems as $item){
				$js.= 'if(' . $this->topFrame . ".indexOfEntry('" . $item["id"] . "')<0){"
					. "attribs={";
				foreach($item as $k => $v){
					$js.='"' . strtolower($k) . '":\'' . addslashes($v) . '\',';
				}

				$js.='};' . $this->topFrame . '.treeData.add(new ' . $this->topFrame . '.node(attribs));
					}';
			}
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

}
