
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

require_once ($_SERVER['DOCUMENT_ROOT']. '/webEdition/lib/we/core/autoload.inc.php');

include_once('conf/define.conf.php');

class we_<?php echo $TOOLNAME; ?>DirSelector extends we_dirSelector{

var $fields = 'ID,ParentID,Text,Path,IsFolder,ContentType';

function __construct($id,$JSIDName='',$JSTextName='',$JSCommand='',$order='',$we_editDirID='',$FolderText=''){
$JSIDName = stripslashes($JSIDName);
$JSTextName = stripslashes($JSTextName);
parent::__construct($id,<?php echo (!empty($TABLECONSTANT)) ? $TABLECONSTANT : "''"; ?>,$JSIDName,$JSTextName,$JSCommand,$order,'',$we_editDirID,$FolderText);
$this->userCanMakeNewFolder = true;
}

function printHeaderHeadlines(){
return '<table class="default" style="width:550px;">
	<tr>
		<td></td>
		<td class="selector"colspan="2"><b><a href="#" onclick="javascript:top.orderIt(\'Text\');">'.g_l('tools','[name]').'</a></b></td>
	</tr>
	<tr>
		<td width="25"></td>
		<td width="200"></td>
		<td width="300"></td>
	</tr>
</table>
';

}

function printHeaderTableExtraCols(){
echo '<td></td>';
}

protected function getWriteBodyHead(){
return we_html_element::jsElement('
var ctrlpressed=false;
var shiftpressed=false;
var inputklick=false;
var wasdblclick=false;
var tout=null;
function weonclick(e){
if(top.makeNewFolder ||  top.we_editDirID){
if(!inputklick){
top.makeNewFolder =top.we_editDirID=false;
document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);document.we_form.submit();
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
}' . ($this->multiple ? '
if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}' : '
top.unselectAllFiles();') . '
}
}
');
}

function printFramesetJSFunctioWriteBody(){
ob_start();
?><script><!--
			function writeBody(d) {
					var body = (top.we_editDirID?
									'<input type="hidden" name="what" value="' + WE().consts.selectors.DORENAMEFOLDER + '" />' +
									'<input type="hidden" name="we_editDirID" value="' + top.we_editDirID + '" />':
									'<input type="hidden" name="what" value="' + WE().consts.selectors.CREATEFOLDER + '" />'
									) +
									'<input type="hidden" name="order" value="' + top.order + '" />' +
									'<input type="hidden" name="rootDirID" value="' + top.options.rootDirID + '" />' +
									'<input type="hidden" name="table" value="' + top.options.table + '" />' +
									'<input type="hidden" name="id" value="' + top.currentDir + '" />' +
									'<table class="default" width="100%">' +
									(makeNewFolder?
													'<tr style="background-color:#DFE9F5;">' +
													'<td style="text-align:center"><img class="treeIcon" src="<?php echo '<?php echo WE_APPS_DIR;?>' . $TOOLNAME; ?>/ui/themes/default/shared/icons/small/folder.gif" ></td>' +
													'<td><input type="hidden" name="we_FolderText" value="<?php echo g_l('tools', '[newFolder]'); ?>" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="<?php echo g_l('tools', '[newFolder]'); ?>"  class="wetextinput" style="width:100%" /></td>' +
													'</tr>':
													'');
									for (i = 0; i < entries.length; i++){
					var onclick = ' onclick="return selectorOnClick(event,' + entries[i].ID + ');"';
									var ondblclick = ' onDblClick="return selectorOnDblClick(' + entries[i].ID + ');"';
									body += '<tr id="line_' + entries[i].ID + '" style="' + ((entries[i].ID == top.currentID && (!makeNewFolder))  ? 'background-color:#DFE9F5;' : '') + 'cursor:pointer;' + ((we_editDirID != entries[i].ID) ? '' : '') + '"' + ((we_editDirID || makeNewFolder) ? '' : onclick) + (entries[i].isFolder ? ondblclick : '') + ' >' +
									'<td class="selector" width="25" style="text-align:center">' +
									'<img class="treeIcon" src="<?php echo '<?php echo WE_APPS_DIR;?>' . $TOOLNAME; ?>/ui/themes/default/shared/icons/small/' + entries[i].icon + '">' +
									'</td>' +
									(we_editDirID == entries[i].ID?
													'<td class="selector"><input type="hidden" name="we_FolderText" value="' + entries[i].text + '"><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />':
													'<td class="selector">' + entries[i].text
													) +
									'</td></tr>';
					}
					d.innerHTML = '<form name="we_form" target="fscmd" method="post" action="' + options.formtarget + '">' + body + '</table></form>';
									if (makeNewFolder || top.we_editDirID){
					document.we_form.we_FolderText_tmp.focus();
									document.we_form.we_FolderText_tmp.select();
					}
					}
-->
</script>
<?php echo '<?php'; ?>

}

function printFramesetJSFunctionQueryString(){
?>
<script>
<!--
	function queryString(what, id, o, we_editDirID){
	if (!o) o = top.order;
					if (!we_editDirID) we_editDirID = "";
					return options.formtarget + \'what=' + what + '&rootDirID="+options.rootDirID+"&open_doc="+options.open_doc+"&table="+options.table+"&id=' + id + (o ? ("&order=" + o) : "") + (we_editDirID ? ("&we_editDirID=" + we_editDirID) : "");
	}
-->
</script>
<?php echo '<?php'; ?>

}

protected function printFramesetJSFunctionEntry(){
<?php echo '?>'; ?>
<script>
<!--
	function addEntry(id, icon, txt, folder, pth) {
	entries.push({
	ID: id,
					text: txt,
					isFolder: folder,
					path: pth,
					contentType:(folder?'folder':'application/*')
	});
					}

-->
</script>
<?php echo '<?php'; ?>

}


function printCmdAddEntriesHTML(){
$this->query();
while($this->db->next_record()){
$_text = $this->db->f('Text');
$_charset = $this->db->f('Charset');

print 'top.addEntry('.$this->db->f('ID').',"'.we_ui_layout_Image::getIconClass($this->db->f('ContentType')).'.gif","'.$_text.'",'.$this->db->f('IsFolder').',"'.$this->db->f('Path').'");'."\n";
}
}

function printCreateFolderHTML(){
echo we_html_tools::getHtmlTop();
we_html_tools::protect();

print we_html_element('<script>
<!--
	top.clearEntries(); ';
					$this - > FolderText = rawurldecode($this - > FolderText);
					$txt = '';
					if (we_base_request::_(we_base_request::BOOL, 'we_FolderText_tmp')){
	$txt = rawurldecode(we_base_request::_(we_base_request::STRING, 'we_FolderText_tmp'));
	}
	if ($txt == ''){
	echo we_message_reporting::getShowMessageCall(g_l('tools', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
	} else{
	$folder = new we_folder();
					$folder - > we_new($this - > table,$this - > dir,$txt);
					$this - > db - > query("SELECT ID FROM ".$this - > db - > escape($this - > table).' WHERE Path="'.$this - > db - > escape($folder - > Path).'"');
					if ($this - > db - > next_record()){
	echo we_message_reporting::getShowMessageCall(g_l('tools', '[folder_path_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
	} else{
	if (<?php echo $CLASSNAME; ?>::textNotValid($folder - > Text)){
	echo we_message_reporting::getShowMessageCall(g_l('tools', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
	} else{
	$folder - > we_save();
					print 'var ref = top.opener.top.content;
					if (ref.makeNewEntry){
	ref.treeData.makeNewEntry({id:'.$folder->ID.', parentid:"'.$folder->ParentID.'", text:"'.$txt.'", open:1, contenttype:"folder", table:"'.$this->table.'"});
	}
	';
					if ($this - > canSelectDir){
	echo 'top.currentPath = "'.$folder - > Path.'";
					top.currentID = "'.$folder->ID.'";
					top.document.getElementsByName("fname")[0].value = "'.$folder->Text.'";
					';
	}
	}

	}
	}


	$this - > printCmdAddEntriesHTML();
					$this - > printCMDWriteAndFillSelectorHTML();
					print 'top.makeNewFolder = 0;
					top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
}

function query(){
$ws_query = getWsQueryForSelector(<?php echo $TABLECONSTANT; ?>);
$this->db->query("SELECT ".$this->db->escape($this->fields).", abs(text) as Nr, (text REGEXP '^[0-9]') as isNr FROM ".
$this->table.
" WHERE IsFolder=1 AND ParentID='".abs($this->dir)."' ".
$ws_query .
" ORDER BY isNr DESC,Nr,Text;");
}

function printDoRenameFolderHTML(){
echo we_html_tools::getHtmlTop();
we_html_tools::protect();

echo '<script><!--
					top.clearEntries();
					';
					$this - > FolderText = rawurldecode($this - > FolderText);
					$txt = $this - > FolderText;
					if ($txt == ''){
	echo we_message_reporting::getShowMessageCall($GLOBALS['l_<?php echo $TOOLNAME; ?>']['folder_empty'], we_message_reporting::WE_MESSAGE_ERROR);
	} else{
	$folder = new we_folder();
					$folder - > initByID($this - > we_editDirID, $this - > table);
					$folder - > Text = $txt;
					$folder - > Filename = $txt;
					$folder - > Path = $folder - > getPath();
					$this - > db - > query("SELECT ID,Text FROM ".$this - > db - > escape($this - > table)." WHERE Path='".$this - > db - > escape($folder - > Path)."' AND ID != ".intval($this - > we_editDirID));
					if ($this - > db - > next_record()){
	$we_responseText = sprintf($GLOBALS["l_<?php echo $TOOLNAME; ?>"]["folder_exists"], $folder - > Path);
					echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
	} else{
	if (preg_match('/[%/\\"\']/', $folder - > Text)){
	$we_responseText = $GLOBALS["l_<?php echo $TOOLNAME; ?>"]["wrongtext"];
					echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
	} else{
	if (f('SELECT Text FROM '.$this - > db - > escape($this - > table)." WHERE ID=".intval($this - > we_editDirID), "Text", $this - > db) != $txt){
	$folder - > we_save();
					echo 'var ref = top.opener.top.content;
					if (ref.treeData.updateEntry){
	ref.treeData.updateEntry({id:'.$folder->ID.', text:"'.$txt.'", parentid:"'.$folder->ParentID.'"});
	}
	';
					if ($this - > canSelectDir){
	echo 'top.currentPath = "'.$folder - > Path.'";
					top.currentID = "'.$folder->ID.'";
					top.document.getElementsByName("fname")[0].value = "'.$folder->Text.'";
					';
	}
	}
	}

	}
	}

	print
					$this - > printCmdAddEntriesHTML().
					$this - > printCMDWriteAndFillSelectorHTML().
					'top.makeNewFolder = 0;
					top.selectFile(top.currentID);
//-->
</script>
';
echo '</head><body></body></html>';
}



function printFramesetSelectFileHTML(){

?>
<script>
<!--
					function selectFile(id){
					if (id){
					e = getEntry(id);
									if (top.document.getElementsByName("fname")[0].value != e.text &&
													top.document.getElementsByName("fname")[0].value.indexOf(e.text + ",") == - 1 &&
													top.document.getElementsByName("fname")[0].value.indexOf("," + e.text + ",") == - 1 &&
													top.document.getElementsByName("fname")[0].value.indexOf("," + e.text + ",") == - 1){

					top.document.getElementsByName("fname")[0].value = top.document.getElementsByName("fname")[0].value ?
									(top.document.getElementsByName("fname")[0].value + "," + e.text) :
									e.text;
									var show = top.document.getElementById("showDiv");
									if (show){
					show.innerHTML = top.document.getElementsByName("fname")[0].value;
					}

					}
					if (top.fsbody.document.getElementById("line_" + id)) top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
									top.currentPath = e.path;
									top.currentID = id;
									top.we_editDirID = 0;
					} else{
					top.document.getElementsByName("fname")[0].value = "";
									top.currentPath = "";
									top.we_editDirID = 0;
					}
					}
-->
</script>
<?php echo '<?php'; ?>
}


<?php
echo '}';
