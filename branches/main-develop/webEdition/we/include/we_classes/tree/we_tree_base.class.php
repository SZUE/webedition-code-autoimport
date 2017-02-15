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
abstract class we_tree_base{
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
	var $styles = [];
	var $tree_states = ['edit' => 0,
		'select' => 1,
		'selectitem' => 2,
		'selectgroup' => 3,
	];
	var $default_segment = 30;
	protected $autoload = true;
	protected $addSorted = true;
	protected $extraClasses = '';

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
	pid = pid ? pid : 0;
	offset = offset ? offset : 0;
}';
	}

	/*
	  the functions prints tree javascript
	  should be placed in a frame which doesn't reloads

	 */

	public function getJSTreeCode(){
		return we_html_element::jsScript(JS_DIR . 'tree.js') .
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
			) . ($this->autoload ? we_base_jsCmd::singleCmd('loadTree', [
				'clear' => 1,
				'items' => static::getItems(0, 0, $this->Tree->default_segment),
				'sorted' => $this->addSorted
			]) : '');
	}

	abstract protected function customJSFile();

	public function getHTMLConstruct(){
		return
			we_html_element::cssLink(CSS_DIR . 'tree.css') .
			we_html_element::htmlDiv(['id' => 'treetable',
				'class' => 'tree' . ($this->extraClasses ? ' ' . $this->extraClasses : ''),
				], ''
		);
	}

	abstract public static function getItems($ParentID, $offset = 0, $segment = 500, $sort = false);
}
