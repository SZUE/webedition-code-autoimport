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
class we_navigation_tree extends weTree{

	function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'navigation_tree.js');
	}

	function getJSTreeCode(){
		return parent::getJSTreeCode() .
			we_html_element::jsElement('drawTree.selection_table="' . NAVIGATION_TABLE . '";');
	}

	function getJSStartTree(){
		return '
function startTree(pid,offset){
frames={
	"top":' . $this->topFrame . ',
	"cmd":' . $this->cmdFrame . '
};
	pid = pid ? pid : 0;
	offset = offset ? offset : 0;
	frames.cmd.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=cmd&pid="+pid+"&offset="+offset;
	drawTree();
}';
	}

}
