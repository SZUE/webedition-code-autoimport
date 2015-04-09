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
class weTree{
	const DefaultWidth = 300;
	const MinWidth = 200;
	const MaxWidth = 1000;
	const StepWidth = 20;
	const DeleteWidth = 420;
	const MoveWidth = 500;
	const HiddenWidth = 24;
	const MinWidthModules = 120;
	const MaxWidthModules = 800;

	protected $db;
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;
	var $treeItems = array();
	var $frameset = '';
	var $styles = array();
	var $tree_states = array(
		'edit' => 0,
		'select' => 1,
		'selectitem' => 2,
		'selectgroup' => 3,
	);
	var $tree_layouts = array(
		0 => 'tree',
		1 => 'tree',
		2 => 'tree',
		3 => 'tree'
	);
	var $node_layouts = array(
		'item' => 'tree',
		'group' => 'group'
	);
	var $tree_image_dir;
	var $tree_icon_dir;
	var $default_segment = 30;

//Initialization

	public function __construct($frameset = '', $topFrame = '', $treeFrame = '', $cmdFrame = ''){
		$this->db = new DB_WE();
		$this->tree_image_dir = TREE_IMAGE_DIR;
		$this->setTreeIconDir(TREE_ICON_DIR);
		if($frameset != '' && $topFrame != '' && $treeFrame != '' && $cmdFrame != ''){
			$this->init($frameset, $topFrame, $treeFrame, $cmdFrame);
		}

		$this->setItemsCount(we_base_preferences::getUserPref('default_tree_count'));
	}

	function init($frameset, $topFrame, $treeFrame, $cmdFrame){
		$this->frameset = $frameset;
		$this->topFrame = $topFrame;
		$this->treeFrame = $treeFrame;
		$this->cmdFrame = $cmdFrame;
	}

	function setTopFrame($topFrame){
		$this->topFrame = $topFrame;
	}

	function setTreeIconDir($dir){
		$this->tree_icon_dir = $dir;
	}

	protected function setNodeLayouts($node_layout){
		$this->node_layouts = $node_layout;
	}

	protected function setStyles($styles){
		$this->styles = $styles;
	}

	function getJSStartTree(){
		return '';
	}

	function getJSReloadGroup(){
		return '';
	}

	function getJSMakeNewEntry(){
		return '';
	}

	/*
	  the functions prints tree javascript
	  should be placed in a frame which doesn't reloads

	 */

	function getJSIncludeFunctions(){
		return $this->getJSDrawTree() .
			$this->getJSUpdateItem() .
			$this->getJSOpenClose() .
			$this->getJSGetLayout() .
			$this->getJSContainer() .
			$this->getJSCheckNode() .
			$this->getJSInfo() .
			$this->getJSShowSegment() .
			$this->getJSStartTree() .
			$this->getJSReloadGroup() .
			$this->getJSMakeNewEntry() .
			$this->getJSAddSortFunction() .
			$this->getJSTreeFunctions();
	}

	function getJSTreeCode(){
		return we_html_element::jsScript(JS_DIR . 'images.js') .
			we_html_element::jsScript(JS_DIR . 'tree.js','self.focus();') .
			we_html_element::jsElement('
var frames={
};
var treeData = new container();
var we_scrollY = [];
' . $this->getJSIncludeFunctions()
			) . $this->customJSFile();
	}

	function customJSFile(){
		return '';
	}

	function getJSAddSortFunction(){
		return '
function addSort(object){
		this.len++;
		for(var i=this.len; i>0; i--){
			if(i > 1 && (this[i-1].text.toLowerCase() > object.text.toLowerCase()' . (!we_base_browserDetect::isMAC() ? ' || (this[i-1].typ>object.typ)' : '' ) . ')){
				this[i] = this[i-1];
			}else{
				this[i] = object;
				break;
			}
		}
}';
	}

	function getJSTreeFunctions(){
		return '
var wasdblclick=false;
var tout=null;

function setScrollY(){
	if(' . $this->topFrame . '){
		if(' . $this->topFrame . '.we_scrollY){
			' . $this->topFrame . '.we_scrollY[treeData.table]=' . (we_base_browserDetect::isIE() ? 'document.body.scrollTop' : 'pageYOffset') . ';
		}
	}
}

function setSegment(id){
	var node=' . $this->topFrame . '.get(id);
	node.showsegment();
}';
	}

	function getJSOpenClose(){
		return '
function openClose(id){
	if(id==""){
		return;
	}

	var eintragsIndex = indexOfEntry(id);
	var status;

	openstatus=(treeData[eintragsIndex].open==0?1:0);

	treeData[eintragsIndex].open=openstatus;
	if(openstatus && treeData[eintragsIndex].loaded!=1){
		' . $this->cmdFrame . '.location=treeData.frameset+"?pnt=cmd&pid="+id;
	}else{
		drawTree();
	}
	if(openstatus==1){
		treeData[eintragsIndex].loaded=1;
	}
}';
	}

	function getJSCheckNode(){
		return '
function checkNode(imgName) {
	var object_name = imgName.substring(4,imgName.length);
	for(i=1;i<=treeData.len;i++) {

		if(treeData[i].id == object_name) {
			if(treeData[i].checked==1) {
				treeData[i].checked=0;
				treeData[i].applylayout();
				if(document.images) {
					try{
						eval("if("+treeData.treeFrame+".document.images[imgName]) "+treeData.treeFrame+".document.images[imgName].src=treeData.tree_image_dir+\"check0.gif\";");
					} catch(e) {
						self.Tree.setCheckNode(imgName);
					}
				}
				break;
			}else {
				treeData[i].checked=1;
				treeData[i].applylayout();
				if(document.images) {
					try{
						eval("if("+treeData.treeFrame+".document.images[imgName]){ "+treeData.treeFrame+".document.images[imgName].src=treeData.tree_image_dir+\"check1.gif\";}");
					} catch(e) {
						self.Tree.setUnCheckNode(imgName);
					}
				}
				break;
			}
		}

	}
	if(!document.images) {
		drawTree();
	}
}';
	}

	function getJSGetLayout(){
		return '
function getLayout(){
		var layout_key=(this.typ=="group" ? "group" : "item");
		return treeData.node_layouts[layout_key];
}';
	}

	function getJSShowSegment(){
		return '
function showSegment(){
	parentnode=frames.top.get(this.parentid);
	parentnode.clear();
	we_cmd("loadFolder",treeData.table,parentnode.id,"","","",this.offset);
	toggleBusy(1);
}';
	}

	protected function getJSContainer(){
		$ts = array();
		foreach($this->tree_states as $k => $v){
			$ts[] = '"' . $k . '":"' . $v . '"';
		}

		$tl = array();
		foreach($this->tree_layouts as $k => $v){
			$tl[] = '"' . $k . '":"' . $v . '"';
		}

		$nl = array();
		foreach($this->node_layouts as $k => $v){
			$nl[] = '"' . $k . '":"' . $v . '"';
		}

		return '
function container(){
	this.len = 0;
	this.state=0;
	this.startloc=0;
	this.clear=containerClear;
	this.add = add;
	this.addSort = addSort;

	this.table="";

	this.selection="";
	this.selection_table="";
	this.selectnode=selectNode;
	this.unselectnode=unselectNode;

	this.setstate=setTreeState;
	this.getlayout=getTreeLayout;

	this.tree_image_dir="' . $this->tree_image_dir . '";
	this.tree_icon_dir="' . $this->tree_icon_dir . '";
	this.topFrame="' . $this->topFrame . '";
	this.treeFrame="' . $this->treeFrame . '";
	this.frameset="' . $this->frameset . '";
	this.frames={};

	this.tree_states={' . implode(',', $ts) . '};
	this.tree_layouts={' . implode(',', $tl) . '};
	this.node_layouts={' . implode(',', $nl) . '};
	return this;
}';
	}

	function getJSUpdateItem(){
		return '
function updateEntry(attribs){
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id==attribs["id"]) {
			for(aname in attribs){
				treeData[ai][aname] = attribs[aname];
			}
		}
		ai++;
	}
}';
	}

	function getStyles(){
		return we_html_element::cssLink(CSS_DIR . 'tree.css') .
			($this->styles ? we_html_element::cssElement(implode("\n", $this->styles)) : '');
	}

	// Function which control how tree contenet will be displayed
	function getHTMLContruct($onresize = ''){

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_tools::getHtmlInnerHead() .
					STYLESHEET .
					$this->getStyles() .
					we_html_element::jsScript(JS_DIR . 'tree.js','self.focus();')
				) .
				we_html_element::htmlBody(array(
					'id' => 'treetable',
					'onresize' => $onresize
					), ''
				)
		);
	}

	function getHTMLContructX($onresize = ''){
		return
			$this->getStyles() .
			we_html_element::htmlDiv(array(
				'id' => 'treetable',
				'onresize' => $onresize
				), ''
		);
	}

	function getJSDrawTree(){
		return '
function drawTree(){
	try{
		var type=typeof(' . $this->treeFrame . ');
	}catch(e){
		window.setTimeout(drawTree, 500);
		return;
	}

	var out="<div class=\""+treeData.getlayout()+"\"><nobr>"+draw(treeData.startloc,"")+"</nobr></div>";' .
			$this->treeFrame . '.document.getElementById("treetable").innerHTML=out;
}' .
			$this->getJSDraw();
	}

	function getJSDraw(){
		$custom_draw = $this->getJSCustomDraw();
		$draw_code = '';
		foreach($custom_draw as $ck => $cv){
			$draw_code.=' case "' . $ck . '":' . $cv . ' break;';
		}
		return
			'
function draw(startEntry,zweigEintrag){
	var nf = search(startEntry);
	var ai = 1;
	var row="";
	while (ai <= nf.len) {
		row+=zweigEintrag;
		var pind=indexOfEntry(nf[ai].parentid);
		if(pind!=-1){
			if(treeData[pind].open==1){
				switch(nf[ai].typ){
					case "item":
						row+=drawItem(nf,ai);
						break;
					case "threedots":
						row+=drawThreeDots(nf,ai);
						break;
					' . $draw_code . '
				}
			}
		}
		ai++;
	}
	return row;
}

function zeichne(startEntry,zweigEintrag){
		draw(startEntry,zweigEintrag);
}';
	}

	function getJSCustomDraw(){
		return array(
			"group" => 'row+=drawGroup(nf, ai, zweigEintrag);',
		);
	}

	function getJSLoadTree($treeItems){
		$js = 'var attribs={};';
		foreach($treeItems as $item){
			$js.='if(' . $this->topFrame . ".indexOfEntry('" . $item["id"] . "')<0){"
				. "attribs={";
			foreach($item as $k => $v){
				$js.='"' . strtolower($k) . '":' . ($v === 0 ? 0 : '\'' . addslashes($v) . '\'') . ',';
			}
			$js.='};' . $this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));
			}';
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

	function getJSInfo(){
		return 'function info(text){}';
	}

	function setItemsCount($count){
		$this->default_segment = $count = intval($count);
	}

	static function deleteTreeEntries($dontDeleteClassFolders = false){
		return '
var obj = top.treeData;
var cont = new top.container();
for(var i=1;i<=obj.len;i++){
	if(obj[i].checked!=1 ' . ($dontDeleteClassFolders ? ' || obj[i].parentid==0' : '') . '){
		if(obj[i].parentid != 0){
			if(!top.parentChecked(obj[i].parentid)){
				cont.add(obj[i]);
			}
		}else{
			cont.add(obj[i]);
		}
	}
}
top.treeData = cont;
top.drawTree();
';
	}

}
