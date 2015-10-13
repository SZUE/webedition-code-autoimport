<?php
/**
 * webEdition CMS
 *
 * $Rev: 10493 $
 * $Author: lukasimhof $
 * $Date: 2015-09-24 11:15:22 +0200 (Thu, 24 Sep 2015) $
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

/* THIS IS TO BE THE ONE AND ONLY FU-EDITOR!! use we_rpc for legacy-mode and let some js collect all formfields to rpc! */
/* => EDITOR PARAMS: FU-UI-CLASS (+ EVT. EXTRAPARAM), FU-RESP-CLASS, 2 ENCCOMMANDS FOR CALLBACK */

we_html_tools::protect();

$writebackTarget = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 1));
$customCallback = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2));
$importToID = we_base_request::_(we_base_request::CMD, 'we_cmd', 0, 3);
$importToPath = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '/', 4));
$setDisabledImportToID = we_base_request::_(we_base_request::CMD, 'we_cmd', 0, 5);
$predefinedCallback = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 6);

// for legacy only
if(we_base_request::_(we_base_request::BOOL, 'we_fu_isSubmitLegacy', false)){
	//make some inst()-bla to have this in one line!
	$respClass = we_base_request::_(we_base_request::STRING, 'weResponseClass', 'we_fileupload_resp_base');
	$fileupload = new $respClass();
	$fileUpload->setCallback('top.doOnImportSuccess(scope.weDoc);');
	$resp = $fileupload->processRequest();
}

$fileUpload = new we_fileupload_ui_editor(we_base_ContentTypes::IMAGE, '', 'dialog');
//$fileUpload->setCallback('top.reloadOpener();top.close()');
$fileUpload->setCallback('top.doOnImportSuccess(scope.weDoc);');
$fileUpload->setDimensions(array('dragWidth' => 374, 'inputWidth' => 378));
$fileUpload->setIsPreset(true);
$fileUpload->setIsExternalBtnUpload(true);
$fileUpload->setFieldImportToID(array('setField' => true, 'presetID' => $importToID, 'presetPath' => $importToPath, 'setDisabled' => $setDisabledImportToID));
$fileUpload->setMoreFieldsToAppend(array(
	array('imgsSearchable', 'text'),
	array('importMetadata', 'text'),
	array('sameName', 'text'),
	array('importToID', 'text')
));
$fileUpload->setEditorJS(array(
	'writebackTarget' => $writebackTarget,
	'customCallback' => $customCallback,
	'predefinedCallback' => $predefinedCallback
));

echo we_html_tools::getHtmlTop('fileupload') . 
	STYLESHEET . $fileUpload->getEditorJS() .
	we_html_element::jsScript(JS_DIR . 'global.js') .
	we_html_element::jsScript(JS_DIR . 'keyListener.js');

echo we_html_element::htmlBody(array('style' => 'position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;', 'onload' => ''),
	we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;'),
		'<form name="we_form" method="post" enctype="multipart/form-data">
		<input type="hidden" name="we_cmd[0]" value="we_fileupload_image">
		<input type="hidden" name="we_fu_isSubmitLegacy" value="1">
		<input type="hidden" name="weResponseClass" value="we_fileupload_resp_import">
		<input type="hidden" name="weFormCount" value="1">
		<input type="hidden" name="weFormNum" value="1">' .
		we_html_element::htmlDiv(array('id' => 'we_fileupload', 'class' => 'weDialogBody', 'style' => 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: auto;'), $fileUpload->getHtml()) .
		we_html_element::htmlDiv(array('id' => 'we_fileupload_footer', 'class' => '', 'style' => 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;'), $fileUpload->getHtmlFooter()) .
		'</form>'
	)
) . '</html>';
