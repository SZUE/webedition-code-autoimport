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
class we_selector_multiple extends we_selector_file{

	var $multiple = true;

	function __construct($id, $table = FILE_TABLE, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $rootDirID = 0, $multiple = true, $filter = "", $startID = 0){
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $rootDirID, $filter, $startID);
		if(defined('CUSTOMER_TABLE') && $table == CUSTOMER_TABLE){
			$this->fields = str_replace('Text', 'CONCAT(Text," (",Forename," ", Surname,")") AS Text', $this->fields);
		}

		$this->multiple = $multiple;
	}

	protected function printFramesetJSFunctions(){
		return parent::printFramesetJSFunctions() . we_html_element::jsElement('
var allIDs ="";
var allPaths ="";
var allTexts ="";
var allIsFolder ="";

function fillIDs() {
	allIDs =",";
	allPaths =",";
	allTexts =",";
	allIsFolder =",";

	for	(var i=0;i < entries.length; i++) {
		if (isFileSelected(entries[i].ID)) {
			allIDs += (entries[i].ID + ",");
			allPaths += (entries[i].path + ",");
			allTexts += (entries[i].text + ",");
			allIsFolder += (entries[i].isFolder + ",");
		}
	}
	if(currentID != ""){
		if(allIDs.indexOf(","+currentID+",") == -1){
			allIDs += (currentID + ",");
		}
	}
	if(currentPath != ""){
		if(allPaths.indexOf(","+currentPath+",") == -1){
			allPaths += (currentPath + ",");
			allTexts += (we_makeTextFromPath(currentPath) + ",");
		}
	}

	if (allIDs == ",") {
		allIDs = "";
	}
	if (allPaths == ",") {
		allPaths = "";
	}
	if (allTexts == ",") {
		allTexts = "";
	}

	if (allIsFolder == ",") {
		allIsFolder = "";
	}
}

function we_makeTextFromPath(path){
	position =  path.lastIndexOf("/");
	if(position > -1 &&  position < path.length){
		return path.substring(position+1);
	}else{
		return "";
	}
}');
	}

	protected function getWriteBodyHead(){
		return we_html_element::jsElement('
var ctrlpressed=false;
var shiftpressed=false;
var wasdblclick=false;
var inputklick=false;
var tout=null;
function weonclick(e){
		if(document.all){
			if(e.ctrlKey || e.altKey){
				ctrlpressed=true;
			}
			if(e.shiftKey){
				shiftpressed=true;
			}
		}else{
			if(e.altKey || e.metaKey || e.ctrlKey){
				ctrlpressed=true;
			}
			if(e.shiftKey){
				shiftpressed=true;
			}
		}
		if(top.options.multiple){
		if((self.shiftpressed==false) && (self.ctrlpressed==false)){
			top.unselectAllFiles();
		}
		}else{
		top.unselectAllFiles();
		}
}');
	}

	protected function printFramesetJSFunctioWriteBody(){
		ob_start();
		?><script type="text/javascript"><!--
					function writeBody(d) {
						var body = '<table>';
						for (i = 0; i < entries.length; i++) {
							var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true;"';
							var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
							body += '<tr' + ((entries[i].ID == top.currentID) ? ' style="background-color:#DFE9F5;cursor:pointer;"' : '') + ' id="line_' + entries[i].ID + '" style="cursor:pointer;"' + onclick + (entries[i].isFolder ? ondblclick : '') + ' >' +
											'<td class="selector" width="25" align="center">' +
											'<img src="<?php echo TREE_ICON_DIR; ?>' + entries[i].icon + '" width="16" height="18" border="0" />' +
											'</td>' +
											'<td class="selector filename"  title="' + entries[i].text + '"><div class="cutText">' + entries[i].text + '</div></td>' +
											'</tr>'
						}
						body += '<tr><td width="25"><?php echo we_html_tools::getPixel(25, 2) ?></td>' +
										'<td><?php echo we_html_tools::getPixel(150, 2) ?></td>' +
										'</tr></table>';
						d.innerHTML = body;
					}
					//->
		</script>
		<?php
		return ob_get_clean();
	}

	function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() .
				we_html_element::jsElement('
options.multiple=' . intval($this->multiple) . ';
			');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .we_html_element::jsScript(JS_DIR . 'selectors/multiple_selector.js');
	}

}