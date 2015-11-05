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
	);
	var $default_segment = 30;

//Initialization

	public function __construct($frameset = '', $topFrame = '', $treeFrame = '', $cmdFrame = ''){
		$this->db = new DB_WE();
		if($frameset != '' && $topFrame != '' && $treeFrame != '' && $cmdFrame != ''){
			$this->init($frameset, $topFrame, $treeFrame, $cmdFrame);
		}

		$this->default_segment = intval(we_base_preferences::getUserPref('default_tree_count'));
	}

	function init($frameset, $topFrame, $treeFrame, $cmdFrame){
		$this->frameset = $frameset;
		$this->topFrame = $topFrame;
		$this->treeFrame = $treeFrame;
		$this->cmdFrame = $cmdFrame;
	}

	function getJSStartTree(){
		return '
function startTree(pid,offset){
frames={
};
	pid = pid ? pid : 0;
	offset = offset ? offset : 0;
	frames.cmd.location=treeData.frameset+"?pnt=cmd&pid="+pid+"&offset="+offset;
	drawTree();
}';
	}

	/*
	  the functions prints tree javascript
	  should be placed in a frame which doesn't reloads

	 */

	function getJSTreeCode(){
		return we_html_element::jsScript(JS_DIR . 'tree.js', 'self.focus();') .
			$this->customJSFile() .
			we_html_element::jsElement('
var frames={
	top:' . $this->topFrame . ',
	cmd:' . $this->cmdFrame . '
};
var treeData = new container();
var we_scrollY = [];
' .
				$this->getJSDrawTree() .
				$this->getJSContainer() .
				$this->getJSStartTree()
		);
	}

	function customJSFile(){
		return '';
	}

	private function getJSContainer(){
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
	container.prototype.topFrame="' . $this->topFrame . '";
	container.prototype.treeFrame="' . $this->treeFrame . '";
	container.prototype.frameset="' . $this->frameset . '";
	container.prototype.frames={};

	container.prototype.tree_states={' . implode(',', $ts) . '};
	container.prototype.tree_layouts={' . implode(',', $tl) . '};
	container.prototype.node_layouts={' . implode(',', $nl) . '};

';
	}

	function getHTMLContruct($onresize = ''){
		return
			we_html_element::cssLink(CSS_DIR . 'tree.css') .
			we_html_element::htmlDiv(array(
				'id' => 'treetable',
				'class' => 'tree',
				'onresize' => $onresize
				), ''
		);
	}

	protected function getJSDrawTree(){
		return '
function drawTree(){
	if(' . $this->treeFrame . '==undefined){
		window.setTimeout(drawTree, 500);
		return;
	}

	var out="<div class=\""+treeData.getLayout()+"\"><nobr>"+
		treeData.draw(treeData.startloc,"")+
		"</nobr></div>";' .
			$this->treeFrame . '.document.getElementById("treetable").innerHTML=out;
}';
	}

	function getJSLoadTree($clear, array $treeItems){
		$js = '';
		foreach($treeItems as $item){
			$item['id'] = (is_numeric($item['id'])) ? $item['id'] : '"' . $item['id'] . '"';
			$js.=($clear ? '' : 'if(' . $this->topFrame . '.treeData.indexOfEntry(' . $item['id'] . ')<0){' ) .
				$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node({';
			foreach($item as $k => $v){
				$js.= strtolower($k) . ':' . ($v === 1 || $v === 0 || is_bool($v) || $v === 'true' || $v === 'false' || is_int($v) ?
						intval($v) :
						'\'' . str_replace(array('"', '\'', '\\'), '',$v) . '\'') . ',';
			}
			$js.='}));' . ($clear ? '' : '}');
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

	static function deleteTreeEntries($dontDeleteClassFolders = false){
		return '
var obj = top.treeData;
var cont = new top.container();
for(var i=1;i<=obj.len;i++){
	if(obj[i].checked!=1 ' . ($dontDeleteClassFolders ? ' || obj[i].parentid==0' : '') . '){
		if(obj[i].parentid != 0){
			if(!top.treeData.parentChecked(obj[i].parentid)){
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
