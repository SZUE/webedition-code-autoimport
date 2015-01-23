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

	public function __construct($id, $table = '', $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $sessionID = '', $we_editDirID = '', $FolderText = '', $rootDirID = 0, $open_doc = false, $multiple = false, $canSelectDir = false){
		$filter = 'image/*';
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $we_editDirID, $FolderText, $filter, $rootDirID, $open_doc, $multiple, $canSelectDir);
	}

	protected function getFrameset(){
		return '
<frameset rows="67,*,65,20,0" border="0"  onunload="if(top.opener && top.opener.top && top.opener.top.toggleBusy){top.opener.top.toggleBusy();}">
	<frame src="' . $this->getFsQueryString(we_selector_file::HEADER) . '" name="fsheader" noresize scrolling="no">
	<frameset cols="*,200" border="1">
		<frame src="' . $this->getFsQueryString(we_selector_file::BODY) . '" name="fsbody" noresize scrolling="auto">
		<frame src="' . $this->getFsQueryString(self::PREVIEW) . '" name="fspreview" noresize scrolling="no"' . ((!we_base_browserDetect::isGecko()) ? ' style="border-left:1px solid black"' : '') . '>
	</frameset>
	<frame src="' . $this->getFsQueryString(we_selector_file::FOOTER) . '"  name="fsfooter" noresize scrolling="no">
	<frame src="' . HTML_DIR . 'gray2.html"  name="fspath" noresize scrolling="no">
	<frame src="about:blank"  name="fscmd" noresize scrolling="no">
</frameset>
<body>
</body>
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
			var ctrlpressed=false
var shiftpressed=false
var inputklick=false
var wasdblclick=false
document.onclick = weonclick;
function weonclick(e){
	if(top.makeNewFolder ||  top.we_editDirID){
		if(!inputklick){
			document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);
			document.we_form.submit();
		}else{
			inputklick=false;
		}
	}else{
		inputklick=false;
		if(document.all){
			if(event.ctrlKey || event.altKey){ ctrlpressed=true;}
			if(event.shiftKey){ shiftpressed=true;}
		}else{
			if(e.altKey || e.metaKey || e.ctrlKey){ ctrlpressed=true;}
			if(e.shiftKey){ shiftpressed=true;}
		}' . ($this->multiple ? '
		if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}' : '
		top.unselectAllFiles();') . '
	}
}
') . we_html_element::cssElement('
div.imgDiv{
	float: left;
	width: 4em;
	height:4em;
	margin: 1px;
	text-align: center;
	cursor: pointer;
	position: relative;
}
img.icon{
	max-width:4em;
	max-height:3em;
}
div.imgText{
	/*font-size: 16px;*/
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
	width:100%;
	text-align:center;
	position: absolute;
	bottom: 0px;
}
div.selected{
	background-color:#DFE9F5;
	border: 1px solid grey;
}
body{
background-color:white;
margin:0px;
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
									(we_editDirID?
													'<input type="hidden" name="what" value="<?php echo self::DORENAMEFOLDER ?>" />' +
													'<input type="hidden" name="we_editDirID" value="' + top.we_editDirID + '" />':
													'<input type="hidden" name="what" value="<?php echo self::CREATEFOLDER ?>" />'
													) +
									'<input type="hidden" name="order" value="' + top.order + '" />' +
									'<input type="hidden" name="rootDirID" value="<?php echo $this->rootDirID ?>" />' +
									'<input type="hidden" name="table" value="<?php echo $this->table ?>" />' +
									'<input type="hidden" name="id" value="' + top.currentDir + '" />' +
									(makeNewFolder?
													'<div class="imgDiv " id="line_'+entries[i].ID+'"><img class="icon" src="<?php echo ICON_DIR . 'doclist/' . we_base_ContentTypes::FOLDER_ICON ?>"/><br/>' +
													'<input type="hidden" name="we_FolderText" value="<?php echo g_l('fileselector', "[new_folder_name]") ?>" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="<?php echo g_l('fileselector', "[new_folder_name]") ?>" class="wetextinput" style="width:100%" />' +
													'</div>':
													'');
									for (i = 0; i < entries.length; i++){
					var onclick = ' onclick="weonclick('.(we_base_browserDetect::isIE() ? "this" : "event").');tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true"';
									var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
									body += '<div class="imgDiv ' + ((entries[i].ID == top.currentID)  ? "selected" : "") + '" id="line_'+entries[i].ID+'" title="' + entries[i].text + '" ' + ((we_editDirID || makeNewFolder) ? "" : onclick) + (entries[i].isFolder ? ondblclick : "") + '>' +
									'<img src="' + ((entries[i].isFolder)  ? "<?php echo ICON_DIR . 'doclist/' . we_base_ContentTypes::FOLDER_ICON ?>" : "<?php echo WEBEDITION_DIR ?>thumbnail.php?id=" + entries[i].ID + "&amp;size=150&amp;path=" + entries[i].path + "&amp;extension=.jpg&amp;size2=200") + '" class="icon"/>' +
									'<br/><div class="imgText">' +
									(we_editDirID == entries[i].ID?
													'<input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />':
													entries[i].text
													) +
									'</div></div>';
									}
					body += '</form>';
									d.innerHTML = body;
									if (makeNewFolder || top.we_editDirID){
					document.we_form.we_FolderText_tmp.focus();
									document.we_form.we_FolderText_tmp.select();
									}
					}
					//-->
		</script>
		<?php
		return ob_get_clean();
	}

}
