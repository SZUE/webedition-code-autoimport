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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

$out = '';

switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case '':
		exit();
	case "editSource" :
		$_we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', 0, 2);

		$_filename = (isset($_SESSION['weS']['we_data'][$_we_transaction][0]['Path']) && $_SESSION['weS']['we_data'][$_we_transaction][0]['Path'] ?
				$_SESSION['weS']['we_data'][$_we_transaction][0]['Path'] :
				we_base_request::_(we_base_request::FILE, 'we_cmd', '', 1)
			);


		$_ct = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);
		$_source = we_base_request::_(we_base_request::RAW_CHECKED, 'we_cmd', '###EDITORPLUGIN:EMPTYSTRING###', 4);

		if($_source == '###EDITORPLUGIN:EMPTYSTRING###'){
			$_source = $_SESSION['weS']['we_data'][$_we_transaction][0]['elements']['data']['dat'];
		}

		// charset is necessary when encoding=true
		$charset = (isset($_SESSION['weS']['we_data'][$_we_transaction][0]['elements']['Charset']['dat']) && !empty($_SESSION['weS']['we_data'][$_we_transaction][0]['elements']['Charset']['dat']) ?
				$_SESSION['weS']['we_data'][$_we_transaction][0]['elements']['Charset']['dat'] :
				$GLOBALS['WE_BACKENDCHARSET']);


		$out = we_html_element::jsElement('
session = "' . session_id() . '";
transaction = "' . str_replace('"', '', $_we_transaction) . '";
filename = "' . addslashes($_filename) . '";
ct = "' . $_ct . '";
source = "' . base64_encode($_source) . '";
if (top.plugin.isLoaded && (typeof top.plugin.document.WePlugin.editSource == "function") ) {
	top.plugin.document.WePlugin.editSource(session,transaction,filename,source,ct,"true","' . $charset . '");
}');

		break;
	case "editFile":
		$_we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 1);

		$we_dt = isset($_SESSION['weS']['we_data'][$_we_transaction]) ? $_SESSION['weS']['we_data'][$_we_transaction] : "";
		include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

		$we_doc->we_initSessDat($we_dt);

		$_filename = $we_doc->Path;
		$we_ContentType = $we_doc->ContentType;


		$_tmp_file = TEMP_DIR . basename($_filename);

		if(file_exists($we_doc->getElement('data'))){
			copy($we_doc->getElement('data'), $_SERVER['DOCUMENT_ROOT'] . $_tmp_file);
		} else {
			t_e("$_tmp_file not exists in " . __FILE__ . " on line " . __LINE__);
		}

		$out = we_html_element::jsElement(//FIXME: sessionname
				'top.plugin.document.WePlugin.editFile("' . session_id() . '","' . $_we_transaction . '","' . addslashes($_filename) . '","' . getServerUrl(true) . WEBEDITION_DIR . 'showTempFile.php?file=' . $_tmp_file . '","' . $we_ContentType . '");');

		break;
	case "setSource":
		$transactionID = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 1);
		if(isset($_SESSION['weS']['we_data'][$transactionID][0]["elements"]["data"]["dat"])){
			$_SESSION['weS']['we_data'][$transactionID][0]["elements"]["data"]["dat"] = we_base_request::_(we_base_request::RAW_CHECKED, 'we_cmd', '', 2);
			$_SESSION['weS']['we_data'][$transactionID][1]["data"]["dat"] = $_SESSION['weS']['we_data'][$transactionID][0]["elements"]["data"]["dat"];

			$out = we_html_element::jsElement(
					'var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction("' . $transactionID . '");
_EditorFrame.getContentFrame().reloadContent = true;');
		}

		break;
	case "reloadContentFrame":

		$_we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 1);

		$out = we_html_element::jsElement(
				'var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction("' . $_we_transaction . '");
_EditorFrame.setEditorIsHot(true);
if (
	_EditorFrame.getEditorEditPageNr() == ' . we_base_constants::WE_EDITPAGE_CONTENT . ' ||
	_EditorFrame.getEditorEditPageNr() == ' . we_base_constants::WE_EDITPAGE_PREVIEW . ' ||
	_EditorFrame.getEditorEditPageNr() == ' . we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE . '
) {
	if ( _EditorFrame.getEditorIsActive() ) { // reload active editor
		_EditorFrame.setEditorReloadNeeded(true);
		_EditorFrame.setEditorIsActive(true);
	} else {
		_EditorFrame.setEditorReloadNeeded(true);
	}
}');
		break;

	case "setBinary":
		if(isset($_FILES['uploadfile']) && ($_we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0))){
			$we_ContentType = we_base_request::_(we_base_request::STRING, 'contenttype');

			$we_dt = isset($_SESSION['weS']['we_data'][$_we_transaction]) ? $_SESSION['weS']['we_data'][$_we_transaction] : "";
			include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

			$tempName = TEMP_PATH . we_base_file::getUniqueId();
			move_uploaded_file($_FILES['uploadfile']["tmp_name"], $tempName);


			$we_doc->we_initSessDat($we_dt);

			if($we_ContentType == we_base_ContentTypes::IMAGE){
				$we_doc->setElement('data', $tempName, 'image');
				$_dim = we_thumbnail::getimagesize($tempName);
				if(is_array($_dim) && count($_dim) > 0){
					$we_doc->setElement('width', $_dim[0], 'dat');
					$we_doc->setElement('height', $_dim[1], 'dat');
				}
			} else {
				$we_doc->setElement('data', $tempName, 'dat');
			}

			$we_doc->saveInSession($_SESSION['weS']['we_data'][$_we_transaction]);
		}
		break;

	default:
		exit("command '" . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) . "' not known!");
}


$charset = '';

if(isset($_we_transaction)){
	if(isset($_SESSION['weS']['we_data'][$_we_transaction][0]['elements']['Charset']['dat'])){
		$charset = $_SESSION['weS']['we_data'][$_we_transaction][0]['elements']['Charset']['dat'];
		we_html_tools::headerCtCharset('text/html', $charset);
	}
}

echo we_html_element::htmlDocType() . we_html_element::htmlHtml(
	we_html_element::htmlHead(
		we_html_tools::htmlMetaCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET'])
	) .
	we_html_element::htmlBody(array('bgcolor' => 'white', 'marginwidth' => 0, 'marginheight' => 0, 'leftmargin' => 0, 'topmargin' => 0), $out
	)
);
