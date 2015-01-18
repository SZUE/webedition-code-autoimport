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
class we_glossary_tree extends weMainTree{

	function customJSFile(){
		return we_html_element::jsScript(WE_JS_GLOSSARY_MODULE_DIR . 'glossary_tree.js');
	}

	function getJSOpenClose(){
		return '';
	}

	function getJSUpdateItem(){
		return '';
	}

	function getJSTreeFunctions(){
		return parent::getJSTreeFunctions(true);
	}

	function getJSStartTree(){
		return '
var g_l={
	"save_changed_glossary":"' . g_l('modules_glossary', '[save_changed_glossary]') . '"
};
function startTree(){
			frames={
	"top":' . $this->topFrame . ',
	"cmd":' . $this->cmdFrame . '
};
treeData.frames=frames;
	frames.cmd.location=treeData.frameset+"?pnt=cmd&pid=0";
	drawTree();
}';
	}

	function getJSMakeNewEntry(){
		return '
function makeNewEntry(icon,id,pid,txt,open,ct,tab,pub){
	if(treeData[indexOfEntry(pid)]){
		if(treeData[indexOfEntry(pid)].loaded){
		 ct=(ct=="folder"?"group":"item");
			var attribs={
			"id":id,
			"icon":icon,
			"text":txt,
			"parentid":pid,
			"open":open,
			"tooltip":id,
			"typ":ct,
			"disabled":0,
			"published":(ct=="item"?pub:1),
			"selected":0
			};

			treeData.addSort(new node(attribs));

			drawTree();
	}
	}
}';
	}

	function getJSInfo(){
		return '';
	}

	function getJSShowSegment(){
		return '';
	}

}
