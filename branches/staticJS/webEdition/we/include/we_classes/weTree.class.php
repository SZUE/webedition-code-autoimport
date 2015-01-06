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
	var $initialized = 0;
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

	function __construct($frameset = '', $topFrame = '', $treeFrame = '', $cmdFrame = ''){
		$this->db = new DB_WE();
		$this->setTreeImageDir(TREE_IMAGE_DIR);
		$this->setTreeIconDir(TREE_ICON_DIR);
		if($frameset != '' && $topFrame != '' && $treeFrame != '' && $cmdFrame != ''){
			$this->init($frameset, $topFrame, $treeFrame, $cmdFrame);
		}

		$this->setStyles(array(
			'.selected_item {background-color: #6070B6;}',
			'.selected_group {background-color: #6070B6;}',
		));

		$this->setItemsCount(we_base_preferences::getUserPref('default_tree_count'));
	}

	function init($frameset, $topFrame, $treeFrame, $cmdFrame){
		$this->frameset = $frameset;
		$this->setTopFrame($topFrame);
		$this->setTreeFrame($treeFrame);
		$this->setCmdFrame($cmdFrame);
		$this->initialized = 1;
	}

	function setTreeFrame($treeFrame){
		$this->treeFrame = $treeFrame;
	}

	function setTopFrame($topFrame){
		$this->topFrame = $topFrame;
	}

	function setCmdFrame($cmdFrame){
		$this->cmdFrame = $cmdFrame;
	}

	function setTreeImageDir($dir){
		$this->tree_image_dir = $dir;
	}

	function setTreeIconDir($dir){
		$this->tree_icon_dir = $dir;
	}

	function setTreeStates($tree_states){
		$this->tree_states = $tree_states;
	}

	function setTreeLayouts($tree_layout){
		$this->tree_layouts = $tree_layout;
	}

	function setNodeLayouts($node_layout){
		$this->node_layouts = $node_layout;
	}

	function setStyles($styles){
		$this->styles = $styles;
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
			$this->getJSClearItems();
	}

	function getJSTreeCode(){
		return we_html_element::jsScript(JS_DIR . 'images.js') .
			we_html_element::jsScript(JS_DIR . 'tree.js') .
			we_html_element::jsElement('
var treeData = new container();

var we_scrollY = new Array();
//var 	setScrollY;

' . $this->getJSIncludeFunctions() . '

function indexOfEntry(id){
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id == id){
			return ai;
		}
		ai++;
	}
	return -1;
}

function get(eintrag){
	var nf = new container();
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id == eintrag){
			nf=treeData[ai];
		}
		ai++;
	}
	return nf;
}

function search(eintrag){
	var nf = new container();
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].parentid == eintrag){
			nf.add(treeData[ai]);
		}
		ai++;
	}
	return nf;
}

function add(object){
	this[++this.len] = object;
}

function containerClear(){
	this.len =0;
}

' . $this->getJSAddSortFunction() . '
' . $this->getJSTreeFunctions() . '

var startloc=0;
var treeHTML;
self.focus();'
		);
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
//var clickCount=0;
var wasdblclick=0;
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
		' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=cmd&pid="+id;
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
						eval("if("+treeData.treeFrame+".document.images[imgName]) "+treeData.treeFrame+".document.images[imgName].src=treeData.check0_img.src;");
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
						eval("if("+treeData.treeFrame+".document.images[imgName]) "+treeData.treeFrame+".document.images[imgName].src=treeData.check1_img.src;");
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
	parentnode=' . $this->topFrame . '.get(this.parentid);
	parentnode.clear();
	we_cmd("loadFolder",treeData.table,parentnode.id,"","","",this.offset);
	toggleBusy(1);
}';
	}

	function getJSClearItems(){
		return '
function clearItems(){
	var ai = 1;
	var delid = 1;
	var deleted = 0;

	while (ai <= treeData.len) {
		if (treeData[ai].parentid == this.id){
			if(treeData[ai].contenttype=="group"){
				deleted+=treeData[ai].clear();
			}else{
				ind=ai;
				while (ind <= treeData.len-1) {
					treeData[ind]=treeData[ind+1];
					ind++;
				}
				treeData.len[treeData.len]=null;
				treeData.len--;
			}
			deleted++;
		}else{
			ai++;
		}
	}
	drawTree();
	return deleted;
}';
	}

	function getJSContainer(){
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

	this.tree_states={' . implode(',', $ts) . '};
	this.tree_layouts={' . implode(',', $tl) . '};
	this.node_layouts={' . implode(',', $nl) . '};

	this.check0_img=new Image();
	this.check0_img.src=this.tree_image_dir+"check0.gif";

	this.check1_img=new Image();
	this.check1_img.src=this.tree_image_dir+"check1.gif";

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
				we_html_element::htmlHead(//FIXME: missing title
					we_html_tools::getHtmlInnerHead() .
					STYLESHEET .
					$this->getStyles() .
					we_html_element::jsScript(JS_DIR . 'tree.js')
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
		//console.log("Frame not found ' . $this->treeFrame . '");
		window.setTimeout("drawTree()", 500);
		return;
	}

	var out="<table><tr><td class=\""+treeData.getlayout()+"\"><nobr>"+draw(treeData.startloc,"")+"</nobr></td></tr></table>";' .
			$this->treeFrame . '.document.getElementById("treetable").innerHTML=out;
}' .
			$this->getJSDraw();
	}

	function getJSDraw(){
		$custom_draw = $this->getJSCustomDraw();
		$draw_code = empty($custom_draw) ? '' : 'switch(nf[ai].typ){';
		foreach($custom_draw as $ck => $cv){
			$draw_code.=' case "' . $ck . '":' . $cv . ' break;';
		}
		$draw_code .= empty($custom_draw) ? '' : '}';
		return'
function draw(startEntry,zweigEintrag){
	var nf = search(startEntry);
	var ai = 1;
	var row="";
	while (ai <= nf.len) {
		row+=zweigEintrag;
		var pind=indexOfEntry(nf[ai].parentid);
		if(pind!=-1){
			if(treeData[pind].open==1){
				' . $draw_code . '
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

	function getJSCustomDraw($click_handler = ''){
		if(!$click_handler){
			$click_handler = '
if(treeData.selection_table==treeData.table && nf[ai].id==treeData.selection) nf[ai].selected=1;

if(treeData.state==treeData.tree_states["select"] && nf[ai].disabled!=1) {
	row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">";
} else if(treeData.state==treeData.tree_states["selectitem"] && nf[ai].disabled!=1 && nf[ai].typ == "item") {
	row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">";
} else if(treeData.state==treeData.tree_states["selectgroup"] && nf[ai].disabled!=1 && nf[ai].typ == "group") {
	row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">";
} else {
	if(nf[ai].disabled!=1) {
		row+="<a name=\'_"+nf[ai].id+"\' href=\"javascript://\"  ondblclick=\"' . $this->topFrame . '.wasdblclick=1;clearTimeout(' . $this->topFrame . '.tout);' . $this->topFrame . '.doClick(\'"+nf[ai].id+"\');return true;\" onclick=\"' . $this->topFrame . '.tout=setTimeout(\'if(' . $this->topFrame . '.wasdblclick==0) ' . $this->topFrame . '.doClick(\\\\\'"+nf[ai].id+"\\\\\'); else ' . $this->topFrame . '.wasdblclick=0;\',300);return true;\" onmouseover=\"' . $this->topFrame . '.info(\'ID:"+nf[ai].id+"\')\" onmouseout=\"' . $this->topFrame . '.info(\' \');\">";
	}
}

row+="<img src="+treeData.tree_icon_dir+nf[ai].icon+" alt=\"\">"+
(nf[ai].disabled!=1?
	"</a>":
	""
);

if(treeData.state==treeData.tree_states["selectitem"] && (nf[ai].disabled!=1)) {
	row+= (nf[ai].typ == "group"?
		"<label id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\">&nbsp;" + nf[ai].text +"</label>":
		"<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\"><img src=\""+treeData.tree_image_dir+(nf[ai].checked==1?"check1.gif":"check0.gif")+"\" alt=\"\" name=\"img_"+nf[ai].id+"\"></a>"+
			"<label id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\" onclick=\""+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">&nbsp;" + nf[ai].text +"</label>"
		);
}else if(treeData.state==treeData.tree_states["selectgroup"] && (nf[ai].disabled!=1)) {
	row+=(nf[ai].typ == "item"?
		"<label id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\">&nbsp;" + nf[ai].text +"</label>":
		"<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\"><img src=\""+treeData.tree_image_dir+(nf[ai].checked==1?"check1.gif":"check0.gif")+"\" alt=\"\" name=\"img_"+nf[ai].id+"\"></a>"+
			"<label id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\" onclick=\""+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">&nbsp;" + nf[ai].text +"</label>"
		);
}else if(treeData.state==treeData.tree_states["select"] && (nf[ai].disabled!=1)) {
	row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\"><img src=\""+treeData.tree_image_dir+(nf[ai].checked==1?"check1.gif":"check0.gif")+"\" alt=\"\" name=\"img_"+nf[ai].id+"\"></a>"+
	"<label id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\" onclick=\""+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">&nbsp;" + nf[ai].text +"</label>";

}else{
	row+=(nf[ai].disabled!=1?
			"<a name=\'_"+nf[ai].id+"\' href=\"javascript://\"  onDblClick=\"' . $this->topFrame . '.wasdblclick=1;clearTimeout(' . $this->topFrame . '.tout);' . $this->topFrame . '.doClick(\'"+nf[ai].id+"\');return true;\" onclick=\"' . $this->topFrame . '.tout=setTimeout(\'if(' . $this->topFrame . '.wasdblclick==0) ' . $this->topFrame . '.doClick(\\\\\'"+nf[ai].id+"\\\\\'); else ' . $this->topFrame . '.wasdblclick=0;\',300);return true;\" onMouseOver=\"' . $this->topFrame . '.info(\'ID:"+nf[ai].id+"\')\" onMouseOut=\"' . $this->topFrame . '.info(\' \');\">":
				""
	)+
	"<label id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\">&nbsp;" + nf[ai].text +"</label>"+
	(nf[ai].disabled!=1?
		"</a>":
		""
	);
}
row+="&nbsp;&nbsp;<br/>";';
		}

		return array(
			"item" => 'row+="&nbsp;&nbsp;<img src=\""+treeData.tree_image_dir+(ai == nf.len?"kreuzungend.gif":"kreuzung.gif")+"\" class=\"treeKreuz\" >";' . $click_handler,
			"group" => '
var newAst = zweigEintrag;

row+="&nbsp;&nbsp;<a href=\"javascript:"+treeData.topFrame+".setScrollY();"+treeData.topFrame+".openClose(\'" + nf[ai].id + "\')\"><img src="+treeData.tree_image_dir+(nf[ai].open == 0?"auf":"zu")+(ai == nf.len ? "end" : "")+".gif class=\"treeKreuz\" alt=\"\"></a>";

nf[ai].icon="folder"+(nf[ai].open==1 ? "open" : "")+(nf[ai].disabled==1 ? "_disabled" : "")+".gif";

' . $click_handler . '

if (nf[ai].open==1){
	newAst += "<img src=\""+treeData.tree_image_dir+(ai == nf.len?"leer.gif":"strich2.gif")+"\" class=\"treeKreuz\"/>";
	row+=draw(nf[ai].id,newAst);
		}',
			"threedots" => '
row+="&nbsp;&nbsp;<img src=\""+treeData.tree_image_dir+(ai == nf.len?"kreuzungend.gif":"kreuzung.gif")+"\" class=\"treeKreuz\" >"+
"<a name=\'_"+nf[ai].id+"\' href=\"javascript://\"  onclick=\"' . $this->topFrame . '.setSegment(\'"+nf[ai].id+"\');return true;\">"+
"<img src=\""+treeData.tree_image_dir+nf[ai].icon+"\" style=\"width:100px;height:7px\" alt=\"\">"+
"</a>"+
"&nbsp;&nbsp;<br/>";'
		);
	}

	function getJSLoadTree($treeItems){
		$js = 'var attribs=new Array();';
		foreach($treeItems as $item){
			$js.='if(' . $this->topFrame . ".indexOfEntry('" . $item["id"] . "')<0){";
			foreach($item as $k => $v){
				$js.='attribs["' . strtolower($k) . '"]=\'' . addslashes($v) . '\';';
			}
			$js.=$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));
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
			if(!parentChecked(obj[i].parentid)){
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
