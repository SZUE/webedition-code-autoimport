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
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 1);
?>

<script><!--
	var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction("<?= $we_transaction; ?>");
	var _EditorFrameDocumentRef = _EditorFrame.getDocumentReference();

<?php
if($we_responseText && $we_responseTextType == we_message_reporting::WE_MESSAGE_ERROR){
	echo '_EditorFrame.setEditorIsHot(true);';
}

if(!empty($wasSaved)){
	// DOC was saved, mark open tabs to reload if necessary
	// was saved - not hot anymore
	?>
		_EditorFrame.setEditorIsHot(false);
		if (_EditorFrame.getContentFrame().refreshContentCompare !== undefined) {
			_EditorFrame.getContentFrame().refreshContentCompare();
		}
	<?php
	$reload = [];
	switch($GLOBALS['we_doc']->ContentType){
		case we_base_ContentTypes::FOLDER:
			if($GLOBALS['we_doc']->wasMoved()){
				$reload[$GLOBALS['we_doc']->Table] = implode(',', $GLOBALS['DB_WE']->getAllq('SELECT f.ID FROM ' . $GLOBALS['we_doc']->Table . ' f INNER JOIN ' . LOCK_TABLE . ' l ON f.ID=l.ID AND l.tbl="' . stripTblPrefix($GLOBALS['we_doc']->Table) . '" WHERE f.Path LIKE "' . $GLOBALS['we_doc']->Path . '/%"', true));
			}
			break;

		case we_base_ContentTypes::TEMPLATE: // #538 reload documents based on this template
			$reloadDocsTempls = we_rebuild_base::getTemplAndDocIDsOfTemplate($GLOBALS['we_doc']->ID, false, false, true, true);

			// reload all documents based on this template
			$reload[FILE_TABLE] = implode(',', $reloadDocsTempls['documentIDs']);
			//no need to reload the edit tab, since this is not changed & Preview is always regenerated
//			$reload[TEMPLATES_TABLE] = implode(',', $reloadDocsTempls['templateIDs']);

			break;
		case we_base_ContentTypes::OBJECT:
			$GLOBALS['DB_WE']->query('SELECT of.ID FROM ' . OBJECT_FILES_TABLE . ' of INNER JOIN ' . LOCK_TABLE . ' l ON of.ID=l.ID AND l.tbl="' . stripTblPrefix(OBJECT_FILES_TABLE) . '" WHERE of.IsFolder=0 AND of.TableID=' . intval($GLOBALS['we_doc']->ID));
			$reload[OBJECT_FILES_TABLE] = implode(',', $GLOBALS['DB_WE']->getAll(true));
	}
	$reload = array_filter($reload);

	if($reload){
		echo 'WE().layout.reloadUsedEditors({';
		foreach($reload as $table => $vals){
			echo $table . " : '," . $vals . ",',";
		}
		echo "});";
	}

	if($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
		echo 'WE().layout.makeNewDoc(_EditorFrame,"' . $we_doc->ContentType . '",' . $we_doc->ID . ',' . intval(!empty($saveTemplate)) . ',' . intval(!isset($isClose) || !$isClose) . ');';
	}
}

echo (isset($we_JavaScript) ? $we_JavaScript : "");

if($we_responseText){
	$jsCommand = "";
	echo 'self.focus();
var showAlert = false;
var contentEditor = WE().layout.weEditorFrameController.getVisibleEditorFrame();';

	// enable navigation box if doc has been published
	if(!empty($GLOBALS['we_doc']->Published)){
		echo 'try{
	if( _EditorFrame && _EditorFrame.getEditorIsInUse() && contentEditor){
		WE().layout.button.switch_button_state(contentEditor.document, "add", "enabled");
	}
} catch(e) {}';
	}

	if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE && (!isset($showAlert) || !$showAlert)){ //	Confirm Box or alert in seeMode
		if(!empty($GLOBALS["publish_doc"])){ //	edit include and pulish then close window and reload
			$jsCommand .='
if(isEditInclude){
	showAlert = true;
}';
		}
		$jsCommand .=
			(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $GLOBALS['we_doc']->EditPageNrs) && $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_PREVIEW ? //	alert or confirm
				"
if(!showAlert){
	if(confirm(\"" . $we_responseText . "\\n\\n" . g_l('SEEM', '[confirm][change_to_preview]') . "\")){
		_EditorFrameDocumentRef.frames.editHeader.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $GLOBALS['we_transaction'] . "');
	} else {
		_EditorFrameDocumentRef.frames.editHeader.we_cmd('switch_edit_page'," . $GLOBALS['we_doc']->EditPageNr . ",'" . $GLOBALS['we_transaction'] . "');
	}
} else {
	" . we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) . "
}" :
				//	alert when in preview mode
				we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) .
				"_EditorFrameDocumentRef.frames.editHeader.we_cmd('switch_edit_page'," . $GLOBALS['we_doc']->EditPageNr . ",'" . $GLOBALS['we_transaction'] . "');" .
				//	JavaScript: generated in we_editor.inc.php
				we_base_request::_(we_base_request::RAW, 'we_cmd', '', 5)
			) .
			(!empty($GLOBALS["publish_doc"]) ?
				"
if(isEditInclude){
	" . we_message_reporting::getShowMessageCall(g_l('SEEM', '[alert][changed_include]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
	weWindow.top.we_cmd("reload_editpage");
	weWindow.edit_include.close();
	top.close();
}' :
				''
			);
	} else { //	alert in normal mode
		$jsCommand .= we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) .
			//	JavaScript: generated in we_editor.inc.php
			(isset($GLOBALS['we_responseJS']) ? $GLOBALS['we_responseJS'] : '') . //fixme: isset only because of workflow_finish as command
			we_base_request::_(we_base_request::RAW, 'we_cmd', '', 5); //should be empty
	}
	echo $jsCommand;
}
?>
//-->
</script></head><body></body></html>