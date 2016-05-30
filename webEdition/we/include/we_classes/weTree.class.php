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
abstract class weTree{

	const DefaultWidth = 300;
	const MinWidth = 200;
	const MaxWidth = 1000;
	const StepWidth = 20;
	const DeleteWidth = 420;
	const MoveWidth = 500;
	const HiddenWidth = 40;
	const MinWidthModules = 120;
	const MaxWidthModules = 800;

	protected $db;
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;
	var $frameset = '';
	var $styles = array();
	var $tree_states = array(
		'edit' => 0,
		'select' => 1,
		'selectitem' => 2,
		'selectgroup' => 3,
	);
	var $default_segment = 30;

//Initialization

	public function __construct($frameset = '', $topFrame = '', $treeFrame = '', $cmdFrame = ''){
		$this->db = new DB_WE();
		if($frameset && $topFrame && $treeFrame && $cmdFrame){
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
	treeData.frames={
		top:' . $this->topFrame . ',
		cmd:' . $this->cmdFrame . ',
		tree:' . $this->treeFrame . '
	};
	pid = pid ? pid :
	offset = offset ? offset : 0;
	treeData.frames.cmd.location=treeData.frameset+"&pnt=cmd&pid="+pid+"&offset="+offset;
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
var treeData = new container();
var we_scrollY = {};
container.prototype.topFrame="' . $this->topFrame . '";
container.prototype.treeFrame="' . $this->treeFrame . '";
container.prototype.frameset="' . $this->frameset . '";
container.prototype.frames={
	top:' . $this->topFrame . ',
	tree:' . $this->treeFrame . '
};
' . $this->getJSStartTree()
		);
	}

	abstract protected function customJSFile();

	function getHTMLContruct($classes = ''){
		return
				we_html_element::cssLink(CSS_DIR . 'tree.css') .
				we_html_element::htmlDiv(array(
					'id' => 'treetable',
					'class' => 'tree' . ($classes ? ' ' . $classes : ''),
						), ''
		);
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
								'\'' . str_replace(array('"', '\'', '\\'), '', $v) . '\'') . ',';
			}
			$js.='}));' . ($clear ? '' : '}');
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

	public static function deleteTreeEntries($dontDeleteClassFolders = false){
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
top.drawTree();';
	}

}
