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

$contentType = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 1));
$doImport = boolval(we_base_request::_(we_base_request::CMD, 'we_cmd', true, 2));
$predefinedConfig = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
$writebackTarget = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4));
$customCallback = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5));
$importToID = we_base_request::_(we_base_request::CMD, 'we_cmd', 0, 6);
$setFixedImportTo = we_base_request::_(we_base_request::CMD, 'we_cmd', 0, 7);
$predefinedCallback = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 8);
$isPreset = boolval(we_base_request::_(we_base_request::CMD, 'we_cmd', false, 9));

$fileUpload = new we_fileupload_ui_editor($contentType, '', $doImport);
$fileUpload->setpredefinedConfig($predefinedConfig);
$fileUpload->setCallback('top.doOnImportSuccess(scope.weDoc);');
$fileUpload->setDimensions(array('dragWidth' => 374, 'inputWidth' => 378));
$fileUpload->setIsPreset($isPreset);
$fileUpload->setIsExternalBtnUpload(true);
$fileUpload->setFieldImportToID(array('setField' => true, 'preset' => $importToID, 'setFixed' => $setFixedImportTo));
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
		we_html_element::htmlForm(array(),
			we_html_element::htmlDiv(array('id' => 'we_fileupload', 'class' => 'weDialogBody', 'style' => 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: auto;'), $fileUpload->getHtml()) .
			we_html_element::htmlDiv(array('id' => 'we_fileupload_footer', 'class' => '', 'style' => 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;'), $fileUpload->getHtmlFooter())
		)
	)
) . '</html>';
