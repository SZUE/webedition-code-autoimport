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
class we_selector_image extends we_selector_document{

	public function __construct($id, $table = '', $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $sessionID = '', $we_editDirID = '', $FolderText = '', $rootDirID = 0, $open_doc = false, $multiple = false, $canSelectDir = false, $startID = 0, $lang = ''){
		$filter = 'image/*';
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $we_editDirID, $FolderText, $filter, $rootDirID, $open_doc, $multiple, $canSelectDir, $startID, '');
	}

	protected function getFrameset($withPreview = false){
		return '<body class="selector" onload="top.document.getElementById(\'fspath\').innerHTML=(top.fileSelect.data.startPath === \'\' ? \'/\' : top.fileSelect.data.startPath);startFrameset();">' .
			we_html_element::htmlDiv(['id' => 'fsheader'], $this->printHeaderHTML()) .
			we_html_element::htmlIFrame('fsbody', $this->getFsQueryString(we_selector_file::BODY), '', '', '', true, 'preview') .
			we_html_element::htmlIFrame('fspreview', $this->getFsQueryString(we_selector_file::PREVIEW), '', '', '', false) .
			we_html_element::htmlDiv(['id' => 'fsfooter'], $this->printFooterTable()) .
			we_html_element::htmlDiv(['id' => 'fspath', 'class' => 'radient']) .
			we_html_element::htmlIFrame('fscmd', 'about:blank', '', '', '', false) .
			'</body>';
	}

	protected function printFooterTable($more = null){
		//IE doesn't support slider correctly inside tables, disable this
		return parent::printFooterTable(we_base_browserDetect::inst()->isIE() ? '<input name="zoom" type="hidden"/>' : '<input type="range" style="width:120px;height:20px;" name="zoom" min="50" step="25" max="250" value="100" onchange="top.fsbody.document.body.style.fontSize=this.value+\'%\';"/>');
	}

	protected function printBodyHTML(){//FIXME: move this somewhere more appropriate
		return parent::printBodyHTML() . we_html_element::jsElement('top.fsbody.document.body.style.fontSize=top.document.getElementsByName("zoom")[0].value+"%";');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() . we_html_element::jsScript(JS_DIR . 'selectors/image_selector.js');
	}

	//FIXME: get/set view using tblFile.viewType
	protected function printHeaderTable($extra = '', $append = true){
		return parent::printHeaderTable(
				'<td id="' . we_search_view::VIEW_ICONS . '" style="display:none">' . we_html_button::create_button('fa:iconview,fa-lg fa-th', "javascript:setview('" . we_search_view::VIEW_ICONS . "');", true, 40, "", "", "", false) . '</td>
		<td id="' . we_search_view::VIEW_LIST . '">' . we_html_button::create_button('fa:listview,fa-lg fa-align-justify-lg fa-align-justify', "javascript:setview('" . we_search_view::VIEW_LIST . "');", true, 40, "", "", "", false) . '</td>', true);
	}

	protected function setFramesetJavaScriptOptions(){
		parent::setFramesetJavaScriptOptions();
		$this->jsoptions['options']['view'] = we_search_view::VIEW_ICONS;
	}

}
