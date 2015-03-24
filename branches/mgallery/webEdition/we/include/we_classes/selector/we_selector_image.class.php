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
		return parent::printFooterTable(we_base_browserDetect::inst()->isIE() ? '' : '<input type="range" style="width:120px;height:20px;" name="zoom" min="50" step="25" max="250" onchange="parent.frames.fsbody.document.body.style.fontSize=this.value+\'%\';"/>');
	}

	protected function printCMDWriteAndFillSelectorHTML(){
		return parent::printCMDWriteAndFillSelectorHTML() .
				'parent.frames.fsbody.document.body.style.fontSize=parent.frames.fsfooter.document.getElementsByName("zoom")[0].value+"%";';
	}

	protected function getWriteBodyHead(){
		return we_html_element::jsElement('
			var ctrlpressed=false;
var shiftpressed=false;
var inputklick=false;
var wasdblclick=false;
var tout=false;
function weonclick(e){
	if(top.makeNewFolder ||  top.we_editDirID){
		if(!inputklick){
		top.makeNewFolder =  top.we_editDirID=false;
			document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);
			document.we_form.submit();
		}else{
			inputklick=false;
		}
	}else{
		inputklick=false;
		if(document.all){
			if(e.ctrlKey || e.altKey){ ctrlpressed=true;}
			if(e.shiftKey){ shiftpressed=true;}
		}else{
			if(e.altKey || e.metaKey || e.ctrlKey){ ctrlpressed=true;}
			if(e.shiftKey){ shiftpressed=true;}
		}
		if(top.options.multiple){
		if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}
		}else{
		top.unselectAllFiles();
		}
	}
}
');
	}


	//FIXME: printFramesetSelectFileHTML should only set a class "selected", not the background itself
	protected function printFramesetJSFunctioWriteBody(){
		ob_start();
		?><script type="text/javascript"><!--
					function writeBody(d) {
						var body =
										'<form name="we_form" target="fscmd" action="<?php echo $_SERVER["SCRIPT_NAME"] ?>" onsubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">' +
										(we_editDirID ?
														'<input type="hidden" name="what" value="<?php echo self::DORENAMEFOLDER ?>" />' +
														'<input type="hidden" name="we_editDirID" value="' + top.we_editDirID + '" />' :
														'<input type="hidden" name="what" value="<?php echo self::CREATEFOLDER ?>" />'
														) +
										'<input type="hidden" name="order" value="' + top.order + '" />' +
										'<input type="hidden" name="rootDirID" value="<?php echo $this->rootDirID ?>" />' +
										'<input type="hidden" name="table" value="<?php echo $this->table ?>" />' +
										'<input type="hidden" name="id" value="' + top.currentDir + '" />' +
										(makeNewFolder ?
														'<div class="imgDiv " id="line_' + entries[i].ID + '"><img class="icon" src="<?php echo ICON_DIR . 'doclist/' . we_base_ContentTypes::FOLDER_ICON ?>"/><br/>' +
														'<input type="hidden" name="we_FolderText" value="<?php echo g_l('fileselector', "[new_folder_name]") ?>" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="<?php echo g_l('fileselector', "[new_folder_name]") ?>" class="wetextinput" style="width:100%" />' +
														'</div>' :
														'');
						for (i = 0; i < entries.length; i++) {
							var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true"';
							var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
							body += '<div class="imgDiv ' + ((entries[i].ID == top.currentID) ? "selected" : "") + '" id="line_' + entries[i].ID + '" title="' + entries[i].text + '" ' + ((we_editDirID || makeNewFolder) ? "" : onclick) + (entries[i].isFolder ? ondblclick : "") + '>' +
											'<img src="' + ((entries[i].isFolder) ? "<?php echo ICON_DIR . 'doclist/' . we_base_ContentTypes::FOLDER_ICON ?>" : "<?php echo WEBEDITION_DIR ?>thumbnail.php?id=" + entries[i].ID + "&amp;size=150&amp;path=" + entries[i].path + "&amp;extension=.jpg&amp;size2=200") + '" class="icon"/>' +
											'<br/><div class="imgText selector">' +
											(we_editDirID == entries[i].ID ?
															'<input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
															entries[i].text
															) +
											'</div></div>';
						}
						body += '</form>';
						d.innerHTML = body;
						if (makeNewFolder || top.we_editDirID) {
							top.fsbody.document.we_form.we_FolderText_tmp.focus();
							top.fsbody.document.we_form.we_FolderText_tmp.select();
						}
					}
					//-->
		</script>
		<?php
		return ob_get_clean();
	}

}
