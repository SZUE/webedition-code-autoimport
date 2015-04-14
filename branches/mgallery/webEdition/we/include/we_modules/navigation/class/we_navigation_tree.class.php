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

	function __construct($frameset = '', $topFrame = '', $treeFrame = '', $cmdFrame = ''){
		parent::__construct($frameset, $topFrame, $treeFrame, $cmdFrame);

		$this->styles = array(
			'.selected_item {background-color: #D4DBFA;}',
			'.selected_group {background-color: #D4DBFA;}',
		);
	}

	function customJSFile(){
		return parent::customJSFile() . we_html_element::jsScript(JS_DIR . 'navigation_tree.js');
	}

	function getJSTreeCode(){
		return parent::getJSTreeCode() .
			we_html_element::jsElement('drawTree.selection_table="' . NAVIGATION_TABLE . '";');
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

	function getHTMLContruct(){
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(//FIXME: missing title
					we_html_tools::getHtmlInnerHead() .
					STYLESHEET .
					$this->getStyles()
				) .
				we_html_element::htmlBody(array('id' => 'treetable',), '<div id="treetable"></div>'
				)
		);
	}

}
