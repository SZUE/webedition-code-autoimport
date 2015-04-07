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

	public function __construct($id, $table = '', $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $sessionID = '', $we_editDirID = '', $FolderText = '', $rootDirID = 0, $open_doc = false, $multiple = false, $canSelectDir = false, $startID = 0){
		$filter = 'image/*';
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $we_editDirID, $FolderText, $filter, $rootDirID, $open_doc, $multiple, $canSelectDir, $startID);
	}

	protected function getFrameset(){
		return
				STYLESHEET .
				we_html_element::cssLink(CSS_DIR . 'selectors.css') .
				'<body class="selector">' .
				we_html_element::htmlIFrame('fsheader', $this->getFsQueryString(we_selector_file::HEADER), '', '', '', false) .
				we_html_element::htmlIFrame('fsbody', $this->getFsQueryString(we_selector_file::BODY), '', '', '', true, 'preview') .
				we_html_element::htmlIFrame('fspreview', $this->getFsQueryString(we_selector_file::PREVIEW), '', '', '', false) .
				we_html_element::htmlIFrame('fsfooter', $this->getFsQueryString(we_selector_file::FOOTER), '', '', '', false, 'path') .
				we_html_element::htmlIFrame('fspath', HTML_DIR . 'gray2.html', '', '', '', false) .
				we_html_element::htmlIFrame('fscmd', 'about:blank', '', '', '', false) .
				'</body>
</html>';
	}

	protected function printFooterTable(){
		//IE doesn't support slider correctly inside tables, disable this
		return parent::printFooterTable(we_base_browserDetect::inst()->isIE() ? '<input name="zoom" type="hidden"/>' : '<input type="range" style="width:120px;height:20px;" name="zoom" min="50" step="25" max="250" value="100" onchange="top.fsbody.document.body.style.fontSize=this.value+\'%\';"/>');
	}

	protected function printCMDWriteAndFillSelectorHTML(){
		return parent::printCMDWriteAndFillSelectorHTML() .
				'top.fsbody.document.body.style.fontSize=top.fsfooter.document.getElementsByName("zoom")[0].value+"%";';
	}

	//FIXME: printFramesetSelectFileHTML should only set a class "selected", not the background itself
	protected function printFramesetJSFunctioWriteBody(){
		$ret = parent::printFramesetJSFunctioWriteBody();
		ob_start();
		?><script type="text/javascript"><!--
					function writeBody(d) {
						if (top.options.view == "<?php echo we_search_view::VIEW_LIST; ?>") {
							writeBodyDocument(d);
							return;
						}
						var body = (we_editDirID ?
										'<input type="hidden" name="what" value="' + top.consts.DORENAMEFOLDER + '" />' +
										'<input type="hidden" name="we_editDirID" value="' + top.we_editDirID + '" />' :
										'<input type="hidden" name="what" value="' + top.consts.CREATEFOLDER + '" />'
										) +
										'<input type="hidden" name="order" value="' + top.order + '" />' +
										'<input type="hidden" name="rootDirID" value="' + top.options.rootDirID + '" />' +
										'<input type="hidden" name="table" value="' + top.options.table + '" />' +
										'<input type="hidden" name="id" value="' + top.currentDir + '" />' +
										(makeNewFolder ?
														'<div class="imgDiv"><img class="icon" src="' + top.dirs.ICON_DIR + 'doclist/' + top.consts.FOLDER_ICON + '"/><br/>' +
														'<input type="hidden" name="we_FolderText" value="<?php echo g_l('fileselector', "[new_folder_name]") ?>" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="<?php echo g_l('fileselector', "[new_folder_name]") ?>" class="wetextinput" style="width:100%" />' +
														'</div>' :
														'');
						for (i = 0; i < entries.length; i++) {
							var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true"';
							var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
							body += '<div class="imgDiv ' + ((entries[i].ID == top.currentID) ? "selected" : "") + '" id="line_' + entries[i].ID + '" title="' + entries[i].text + '" ' + ((we_editDirID || makeNewFolder) ? "" : onclick) + (entries[i].isFolder ? ondblclick : "") + '>' +
											'<img src="' + ((entries[i].isFolder) ? top.dirs.ICON_DIR + 'doclist/' + top.consts.FOLDER_ICON : top.dirs.WEBEDITION_DIR + "thumbnail.php?id=" + entries[i].ID + "&amp;size=150&amp;path=" + entries[i].path + "&amp;extension=.jpg&amp;size2=200") + '" class="icon"/>' +
											'<br/><div class="imgText selector">' +
											(we_editDirID == entries[i].ID ?
															'<input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onmousedown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
															entries[i].text) +
											'</div></div>';
						}
						d.innerHTML = '<form name="we_form" target="fscmd" action="' + top.options.formtarget + '" onsubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">' + body + '</form>';
						if (makeNewFolder || top.we_editDirID) {
							top.fsbody.document.we_form.we_FolderText_tmp.focus();
							top.fsbody.document.we_form.we_FolderText_tmp.select();
						}
					}
					//-->
		</script>
		<?php
		return $ret . ob_get_clean();
	}

	//FIXME: get/set view using tblFile.viewType
	protected function printHeaderTableExtraCols(){
		$newFileState = $this->userCanMakeNewFile ? 1 : 0;

		return parent::printHeaderTableExtraCols() .
				'<td id="' . we_search_view::VIEW_ICONS . '" style="display:none">' . we_html_button::create_button("image:iconview", "javascript:setview('" . we_search_view::VIEW_ICONS . "');", true, 40, "", "", "", false) . '</td>
		<td id="' . we_search_view::VIEW_LIST . '">' . we_html_button::create_button("image:listview", "javascript:setview('" . we_search_view::VIEW_LIST . "');", true, 40, "", "", "", false) . '</td>' .
				'<td>' .
				we_html_element::jsElement('newFileState=' . $newFileState . ';') .
				($this->filter && isset($this->ctb[$this->filter]) ?
						we_html_button::create_button("image:" . $this->ctb[$this->filter], "javascript:top.newFile();", true, 0, 0, "", "", !$newFileState, false) :
						we_html_button::create_button("image:btn_add_file", "javascript:top.newFile();", true, 0, 0, "", "", !$newFileState, false)) .
				'</td>';
	}

	function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('options.view="' . we_search_view::VIEW_ICONS . '";');
	}

}
