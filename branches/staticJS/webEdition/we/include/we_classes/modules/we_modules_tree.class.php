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
//This is a copy of weToolTree:
//If this is appropriate for other modules too, make a generic combination of both classes!

class we_modules_tree extends weMainTree{

	function __construct($frameset = '', $topFrame = '', $treeFrame = '', $cmdFrame = ''){

		parent::__construct($frameset, $topFrame, $treeFrame, $cmdFrame);

		$this->setStyles(array(
			'.selected_item {background-color: #D4DBFA;}',
			'.selected_group {background-color: #D4DBFA;}',
		));
	}

	function getJSOpenClose(){
		return '
function openClose(id){
	var sort="";
	if(id=="") return;
	var eintragsIndex = indexOfEntry(id);
	var openstatus;

	openstatus=(treeData[eintragsIndex].open==0?1:0);
	treeData[eintragsIndex].open=openstatus;

	if(openstatus && treeData[eintragsIndex].loaded!=1){
		if(sort!=""){
			' . $this->cmdFrame . '.location=treeData.frameset+"?pnt=cmd&pid="+id+"&sort="+sort;
		}else{
			' . $this->cmdFrame . '.location=treeData.frameset+"?pnt=cmd&pid="+id;
		}
	}else{
		drawTree();
	}
	if(openstatus==1){
		treeData[eintragsIndex].loaded=1;
	}
}';
	}

	function getJSUpdateItem(){
		return '
function updateEntry(id,text,pid,pub,order){
	var ai = 1;
	while (ai <= treeData.len) {
			if (treeData[ai].id==id) {
					treeData[ai].text=text;
					treeData[ai].parentid=pid;
					treeData[ai].order=order;
					treeData[ai].tooltip=id;
			}
			ai++;
	}
	drawTree();
}';
	}

	function getJSStartTree(){
		return '
function startTree(){
frames={
	"top":' . $this->topFrame . ',
	"cmd":' . $this->cmdFrame . '
};
	pid = arguments[0] ? arguments[0] : 0;
	offset = arguments[1] ? arguments[1] : 0;
	frames.cmd.location=treeData.frameset+"?pnt=cmd&pid="+pid+"&offset="+offset;
	drawTree();
}';
	}

	function getJSReloadGroup(){
		return '
function reloadGroup(pid){
	var ai = 1;
	var it = get(pid);
	offset = arguments[1] ? arguments[1] : 0;
	if(it){
		it.clear();
		startTree(pid,offset);
	}
}';
	}

	function getJSMakeNewEntry(){
		return '
function makeNewEntry(icon,id,pid,txt,open,ct,tab,pub,order){
	if(treeData[indexOfEntry(pid)] && treeData[indexOfEntry(pid)].loaded){

		ct=(ct=="folder"?"group":"item");

		var attribs={
			"id":id,
			"icon":icon,
			"text":txt,
			"parentid":pid,
			"open":open,
			"order":order,
			"tooltip":id,
			"typ":ct,
			"disabled":0,
			"published":(pub==0 ? 1 : 0),
			"depended":pub,
			"selected":0
};
		treeData.addSort(new node(attribs));

		drawTree();
	}
}';
	}

	function getJSInfo(){
		return '
function info(text) {
	t=' . $this->topFrame . '.document.getElementById("infoField");
	if(text!=" "){
		t.style.display="block";
		t.innerHTML = text;
	} else {
		t.innerHTML = text;
		t.style.display="none";
	}
}';
	}

	function getJSAddSortFunction(){
		return '
function addSort(object){
	this.len++;
	for(var i=this.len; i>0; i--){
		if(i > 1 && (this[i-1].order > object.order)){
			this[i] = this[i-1];
		}else{
			for(var j=i; j>0; j--){
				if(j > 1  && (this[j-1].order == object.order) && (this[j-1].text.toLowerCase() > object.text.toLowerCase()' . (!we_base_browserDetect::isMAC() ? " || (this[j-1].typ>object.typ)" : "" ) . ')){
					this[j] = this[j-1];
				}else{
					this[j] = object;
					break;
				}
			}
			break;
		}
	}
}';
	}

	function getHTMLContruct(){
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
						we_html_element::htmlHead(//FIXME: missing title
								we_html_tools::getHtmlInnerHead() .
								STYLESHEET .
								$this->getStyles()
						) .
						we_html_element::htmlBody(array(), '<div id="treetable"></div>'
						)
		);
	}

	function getJSShowSegment(){
		return '
function showSegment(){
	' . $this->topFrame . '.reloadGroup(this.parentid,this.offset);
}';
	}

	function getJSTreeFunctions($overriden = false){
		// must override
		return parent::getJSTreeFunctions(true) . ($overriden ? '' : '
function doClick(id,typ){
}' ) .
				$this->topFrame . '.loaded=1;';
	}

	function getJSTreeCode(){
		// must override
		return parent::getJSTreeCode() .
				we_html_element::jsElement('drawTree.selection_table="";');
	}

}
