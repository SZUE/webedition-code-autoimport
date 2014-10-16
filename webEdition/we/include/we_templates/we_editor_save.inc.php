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
echo we_html_tools::getHtmlTop();
?>

<script  type="text/javascript"><!--
	var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction("<?php print $GLOBALS['we_transaction']; ?>");
	var _EditorFrameDocumentRef = _EditorFrame.getDocumentReference();

<?php
if($we_responseText && $we_responseTextType == we_message_reporting::WE_MESSAGE_ERROR){
	echo "_EditorFrame.setEditorIsHot(true);";
}

if(isset($wasSaved) && $wasSaved){
	// DOC was saved, mark open tabs to reload if necessary
	// outsource this, if code gets too big
	// was saved - not hot anymore
	?>
		_EditorFrame.setEditorIsHot(false);
	<?php
	$reload = array();
	switch($GLOBALS['we_doc']->ContentType){
		case we_base_ContentTypes::FOLDER:
			if($GLOBALS['we_doc']->wasMoved()){
				$GLOBALS['DB_WE']->query('SELECT f.ID FROM ' . $GLOBALS['we_doc']->Table . ' f INNER JOIN ' . LOCK_TABLE . ' l ON f.ID=l.ID AND l.tbl="' . stripTblPrefix($GLOBALS['we_doc']->Table) . '" WHERE f.Path LIKE "' . $GLOBALS['we_doc']->Path . '/%"');
				$reload[$GLOBALS['we_doc']->Table] = implode(',', $GLOBALS['DB_WE']->getAll(true));
			}
			break;

		case we_base_ContentTypes::TEMPLATE: // #538 reload documents based on this template

			$reloadDocsTempls = we_rebuild_base::getTemplAndDocIDsOfTemplate($GLOBALS['we_doc']->ID, false, false, true, true);

			$reload[FILE_TABLE] = implode(',', $reloadDocsTempls['documentIDs']);
			$reload[TEMPLATES_TABLE] = implode(',', $reloadDocsTempls['templateIDs']);
			// reload all documents based on this template

			break;
		case 'object':
			$GLOBALS['DB_WE']->query('SELECT f.ID FROM ' . OBJECT_FILES_TABLE . ' f INNER JOIN ' . LOCK_TABLE . ' l ON f.ID=l.ID AND l.tbl="' . stripTblPrefix(OBJECT_FILES_TABLE) . '" WHERE f.IsFolder=0 AND f.TableID=' . intval($GLOBALS['we_doc']->ID));
			$reload[OBJECT_FILES_TABLE] = implode(',', $GLOBALS['DB_WE']->getAll(true));
	}
	$reload = array_filter($reload);

	if($reload){
		echo "var _reloadTabs = new Object();";
		foreach($reload as $table => $vals){
			echo "_reloadTabs['" . $table . "'] = '," . $vals . ",';";
		}
		echo "
var _usedEditors = top.weEditorFrameController.getEditorsInUse();

for (frameId in _usedEditors) {
	if ( _reloadTabs[_usedEditors[frameId].getEditorEditorTable()] && (_reloadTabs[_usedEditors[frameId].getEditorEditorTable()]).indexOf(',' + _usedEditors[frameId].getEditorDocumentId() + ',') != -1 ) {
		_usedEditors[frameId].setEditorReloadNeeded(true);
 	}
}";
	}

	if($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
		//	JS, when not in seem
		$isTmpl = $we_doc->ContentType == we_base_ContentTypes::TEMPLATE && (permissionhandler::hasPerm("NEW_WEBEDITIONSITE") || permissionhandler::hasPerm("ADMINISTRATOR"));
		echo ($isTmpl ?
				'if( _EditorFrame.getEditorMakeNewDoc() == true ) {' .
				(isset($saveTemplate) && $saveTemplate ?
						"	top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','','" . $we_doc->ID . "');" :
						''
				) .
				'} else {' :
				(($isObject = $we_doc->ContentType === 'object' && (permissionhandler::hasPerm('NEW_OBJECTFILE') || permissionhandler::hasPerm("ADMINISTRATOR"))) ?
						"if( _EditorFrame.getEditorMakeNewDoc() == true ) {
				top.we_cmd('new','" . OBJECT_FILES_TABLE . "','','objectFile','" . $we_doc->ID . "');
			} else {" : '')) .
		(!isset($isClose) || !$isClose ? //if we close the document, we should not reload any header etc.
				'if ( _EditorFrame.getEditorIsInUse() ) {_EditorFrameDocumentRef.frames[0].location.reload();}' : '') .
		($isTmpl || $isObject ?
				'}' : '');
	}
}

echo (isset($we_JavaScript) ? $we_JavaScript : "");

if($we_responseText){
	$_jsCommand = "";
	echo 'self.focus();
		top.toggleBusy(0);
		showAlert = 0;
		var contentEditor = top.weEditorFrameController.getVisibleEditorFrame();';

	// enable navigation box if doc has been published
	if(isset($GLOBALS['we_doc']->Published) && $GLOBALS['we_doc']->Published){
		echo "try{ if( _EditorFrame && _EditorFrame.getEditorIsInUse() && contentEditor && contentEditor.switch_button_state) contentEditor.switch_button_state('add', 'add_enabled', 'enabled'); } catch(e) {}";
	}

	if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE && (!isset($_showAlert) || !$_showAlert)){ //	Confirm Box or alert in seeMode
		if(isset($GLOBALS["publish_doc"]) && $GLOBALS["publish_doc"] == true){ //	edit include and pulish then close window and reload
			$_jsCommand .='
			if(isEditInclude){
				showAlert = 1;
			}';
		}

		if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $GLOBALS['we_doc']->EditPageNrs) && $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_PREVIEW){ //	alert or confirm
			$_jsCommand .= "
			if(!showAlert){
				if(confirm(\"" . $we_responseText . "\\n\\n" . g_l('SEEM', "[confirm][change_to_preview]") . "\")){
					_EditorFrameDocumentRef.frames[0].we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $GLOBALS['we_transaction'] . "');
				} else {
					_EditorFrameDocumentRef.frames[0].we_cmd('switch_edit_page'," . $GLOBALS['we_doc']->EditPageNr . ",'" . $GLOBALS['we_transaction'] . "');
				}
			} else {
				" . we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) . "
			}
			";
		} else { //	alert when in preview mode
			$_jsCommand .= we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) .
					"_EditorFrameDocumentRef.frames[0].we_cmd('switch_edit_page'," . $GLOBALS['we_doc']->EditPageNr . ",'" . $GLOBALS['we_transaction'] . "');" .
					//	JavaScript: generated in we_editor.inc.php
					we_base_request::_(we_base_request::RAW, 'we_cmd', '', 5);
		}

		if(isset($GLOBALS["publish_doc"]) && $GLOBALS["publish_doc"] == true){

			$_jsCommand .="
			if(isEditInclude){
				" . we_message_reporting::getShowMessageCall(g_l('SEEM', "[alert][changed_include]"), we_message_reporting::WE_MESSAGE_NOTICE) . '
						weWindow.top.we_cmd("reload_editpage");
						weWindow.edit_include.close();
						top.close();
			}';
		}
	} else { //	alert in normal mode
		$_jsCommand .= we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) .
				//	JavaScript: generated in we_editor.inc.php
				(isset($GLOBALS['we_responseJS']) ? $GLOBALS['we_responseJS'] : '') . //fixme: isset only because of workflow_finish as command
				we_base_request::_(we_base_request::RAW, 'we_cmd', '', 5); //should be empty
	}
	echo $_jsCommand;
}
?>
//-->
</script></head><body></body></html>