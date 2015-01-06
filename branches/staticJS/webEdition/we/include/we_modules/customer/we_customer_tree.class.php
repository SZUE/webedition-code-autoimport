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
class we_customer_tree extends weTree{

	function getJSCustomDraw(){
		return array_merge(parent::getJSCustomDraw(), array(
			"sort" => '
var newAst = zweigEintrag;
var oc_js=treeData.topFrame+".openClose(\'" + nf[ai].id + "\')\"";

row+="&nbsp;&nbsp;<a href=\"javascript:"+oc_js+" border=0><img src=\""+treeData.tree_image_dir+(nf[ai].open == 0?"auf":"zu")+(ai == nf.laenge ? "end" : "")+".gif\" class=\"treeKreuz\" alt=\"\"></a>"+
	"<a name=\'_"+nf[ai].id+"\' href=\"javascript://\" onclick=\""+oc_js+";return true;\" border=0>"+
	"<img src=\""+treeData.tree_image_dir+"icons/"+nf[ai].icon+"\" alt=\"\">"+
	"</a>"+
	"<a name=\'_"+nf[ai].id+"\' href=\"javascript://\" onclick=\""+oc_js+";return true;\">"+
	"<label id=\"lab_"+nf[ai].id+"\" class=\""+treeData.node_layout[nf[ai].state]+"\">&nbsp;" + nf[ai].text+"</label>"+
	"</a>"+
	"&nbsp;&nbsp;<br/>\n";

if (nf[ai].open){
	newAst = newAst + "<img src=\""+treeData.tree_image_dir+(ai == nf.laenge?"leer.gif":"strich2.gif")+"\" class=\"treeKreuz\" />";
	row+=draw(nf[ai].id,newAst);
}',
			"group" => '
var newAst = zweigEintrag;
var oc_js=treeData.topFrame+".setScrollY();"+treeData.topFrame+".openClose(\'" + nf[ai].id + "\')\"";
row+="&nbsp;&nbsp;<a href=\"javascript:"+oc_js+" border=0><img src=\""+treeData.tree_image_dir+(nf[ai].open == 1?"zu":"auf")+(ai == nf.len ? "end" : "")+".gif\" class=\"treeKreuz\" alt=\"\"></a>";

nf[ai].icon="folder"+(nf[ai].open==1 ? "open" : "")+(nf[ai].disabled==1 ? "_disabled" : "")+".gif";

row+=(nf[ai].disabled!=1?
		"<a name=\'_"+nf[ai].id+"\' href=\"javascript:"+oc_js+"\">":
		"")+
	"<img src=\""+treeData.tree_image_dir+"icons/"+nf[ai].icon+"\" alt=\"\">"+
	(nf[ai].disabled!=1?
		"</a><a name=\'_"+nf[ai].id+"\' href=\"javascript:"+oc_js+"\">":
		"")+
	"<label id=\"lab_"+nf[ai].id+"\" class=\""+nf[ai].getlayout()+"\">&nbsp;" + nf[ai].text+"</label>"+
	(nf[ai].disabled!=1?
		"</a>":
		"")+
	"&nbsp;&nbsp;<br/>";
if (nf[ai].open==1){
	newAst += "<img src=\""+treeData.tree_image_dir+(ai == nf.len?"leer.gif":"strich2.gif")+"\" class=\"treeKreuz\"/>";
	row+=draw(nf[ai].id,newAst);
}'
		));
	}

	function getJSOpenClose(){
		return '
function openClose(id){
	var sort="";
	if(id==""){
		return;
	}
	var eintragsIndex = indexOfEntry(id);

	if(treeData[eintragsIndex].typ=="group"){
		sort=' . $this->topFrame . '.document.we_form_treeheader.sort.value;
	}

	var openstatus=(treeData[eintragsIndex].open==0?1:0);

	treeData[eintragsIndex].open=openstatus;

	if(openstatus && treeData[eintragsIndex].loaded!=1){
		id = encodeURI(id);
		sort = encodeURI(sort);
		id = id.replace(/\+/g,"%2B");
		sort = sort.replace(/\+/g,"%2B");
		' . $this->cmdFrame . '.location=treeData.frameset+"?pnt=cmd&pid="+id+(sort!=""?"&sort="+sort:"");
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
function updateEntry(id,text){
	var ai = 1;
	while (ai <= treeData.len) {
			if (treeData[ai].id==id) {
				text = text.replace(/</g,"&lt;");
				text = text.replace(/>/g,"&gt;");
				treeData[ai].text=text;
			}
			ai++;
	}
	drawTree();
}';
	}

	function getJSTreeFunctions(){
		return parent::getJSTreeFunctions() . '
function doClick(id,typ){
	var node=' . $this->topFrame . '.get(id);
		if(node.typ=="item"){
		' . $this->topFrame . '.we_cmd(\'customer_edit\',node.id,node.typ,node.table);
		}
}
' . $this->topFrame . '.loaded=1;';
	}

	function getJSStartTree(){
		return '
function startTree(){
	' . $this->cmdFrame . '.location=treeData.frameset+"?pnt=cmd&pid=0";
	drawTree();
}';
	}

	function getJSIncludeFunctions(){
		return parent::getJSIncludeFunctions() .
			$this->getJSStartTree();
	}

	function getJSLoadTree($treeItems){
		$days = array(
			'Sunday' => 0,
			'Monday' => 1,
			'Tuesday' => 2,
			'Wednesday' => 3,
			'Thursday' => 4,
			'Friday' => 5,
			'Saturday' => 6
		);

		$months = array(
			'January' => 0,
			'February' => 1,
			'March' => 2,
			'April' => 3,
			'May' => 4,
			'June' => 5,
			'July' => 6,
			'August' => 7,
			'September' => 8,
			'October' => 9,
			'November' => 10,
			'December' => 11
		);

		$js = 'var attribs=new Array();';
		foreach($treeItems as $item){
			$js.='if(' . $this->topFrame . '.indexOfEntry(\'' . str_replace(array("\n", "\r", '\''), '', $item["id"]) . '\')<0){';
			foreach($item as $k => $v){
				if($k === 'text'){
					if(in_array($v, array_keys($days))){
						$v = g_l('date', '[day][long][' . $days[$v] . ']');
					}
					if(in_array($v, array_keys($months))){
						$v = g_l('date', '[month][long][' . $months[$v] . ']');
					}
				}
				$js.='attribs["' . strtolower($k) . '"]=\'' . addslashes(stripslashes(str_replace(array("\n", "\r", '\''), '', $v))) . '\';';
			}
			$js.=$this->topFrame . '.treeData.add(new ' . $this->topFrame . '.node(attribs));
				}';
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

	function getJSShowSegment(){
		return '
function showSegment(){
	var sort="";
	parentnode=' . $this->topFrame . '.get(this.parentid);
	parentnode.clear();
	sort=' . $this->topFrame . '.document.we_form_treheader.sort.value;
	we_cmd("load",parentnode.id,this.offset,sort);
}';
	}

	function getJSGetLayout(){
		return '
function getLayout(){
		if(this.typ=="threedots"){
			return treeData.node_layouts["threedots"];
		}
		var layout_key=(this.typ=="group" ? "group" : "item");

		return treeData.node_layouts[layout_key]+(this.typ=="item" && this.published==1 ? " loginDenied" : "");
}';
	}

}
