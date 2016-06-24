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
we_html_tools::protect();

$uniqid = md5(uniqid(__FILE__, true)); // #6590, changed from: uniqid(time())

$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', 0, 1);

// init document
$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : "";
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');


if(!($we_doc instanceof we_imageDocument)){
	exit("ERROR: Couldn't initialize we_imageDocument object");
}

switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case "image_resize":
		$js = we_getImageResizeDialogJS();
		$dialog = we_getImageResizeDialog();
		break;
	case "image_convertJPEG":
		$js = we_getImageConvertDialogJS();
		$dialog = we_getImageConvertDialog();
		break;
	case "image_rotate":
		$js = we_getImageRotateDialogJS();
		$dialog = we_getImageRotateDialog();
		break;
	default:
		$dialog = $js = '';
}
echo we_html_tools::getHtmlTop() .
 we_html_element::jsScript(JS_DIR . 'image_edit.js') .
 we_html_element::jsElement($js) .
 STYLESHEET . '</head>' .
 we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus()'), we_html_element::htmlForm(array("name" => "we_form"), $dialog)) . "</html>";

function we_getImageResizeDialogJS(){
	list($width, $height) = $GLOBALS['we_doc']->getOrigSize();

	return 'var width = ' . $width . ';
var height = ' . $height . ';
var ratio_wh = width / height;
var ratio_hw = height / width;

function doOK(){
	var f = document.we_form;
	var qual = 8;

	if (f.width.value == 0 || f.height.value == 0 || f.width.value == "0%" || f.height.value == "0%") {
		' . we_message_reporting::getShowMessageCall(g_l('weClass', '[image_edit_null_not_allowed]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		return;
	}
	var newWidth = (f.widthSelect.options[f.widthSelect.selectedIndex].value == "pixel") ? f.width.value : Math.round((width/100) * f.width.value);
	var newHeight = (f.heightSelect.options[f.heightSelect.selectedIndex].value == "pixel") ? f.height.value : Math.round((height/100) * f.height.value);
	' . (($GLOBALS['we_doc']->getGDType() === "jpg") ? "\nqual = f.quality.options[f.quality.selectedIndex].value;\n" : '') . '
	top.opener._EditorFrame.setEditorIsHot(true);
	top.opener.we_cmd("resizeImage",newWidth,newHeight,qual);
	top.close();
}

';
}

function we_getImageConvertDialogJS(){
	return 'function doOK(){
	var f = document.we_form;
	var qual = f.quality.options[f.quality.selectedIndex].value;
	top.opener._EditorFrame.setEditorIsHot(true);
	top.opener.top.we_cmd("doImage_convertJPEG", qual);
	top.close();
}
';
}

function we_getImageRotateDialogJS(){

	$imageSize = $GLOBALS['we_doc']->getOrigSize();

	return 'function doOK(){
	var f = document.we_form;
	var qual = 8;
	var degrees = 0;
	var w = "' . $imageSize[0] . '";
	var h= "' . $imageSize[1] . '";

	for(var i=0; i<f.degrees.length;i++){
		if(f.degrees[i].checked){
			degrees = f.degrees[i].value;
			break;
		}
	}
	switch(parseInt(degrees)){
		case 90:
		case 270:
			w = "' . $imageSize[1] . '";
			h= "' . $imageSize[0] . '";
			break;
	}
	' . (($GLOBALS['we_doc']->getGDType() === "jpg") ? "\nqual = f.quality.options[f.quality.selectedIndex].value;\n" : '') . '
	top.opener._EditorFrame.setEditorIsHot(true);
	top.opener.top.we_cmd("rotateImage", w, h, degrees, qual);
	top.close();
}
';
}

function we_getImageResizeDialog(){
	list($width, $height) = $GLOBALS['we_doc']->getOrigSize();

	$content = [];

	$okbut = we_html_button::create_button(we_html_button::OK, "javascript:doOK();");
	$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");

	$buttons = we_html_button::position_yes_no_cancel($okbut, null, $cancelbut);

	$widthInput = we_html_tools::htmlTextInput("width", 10, $width, "", 'onkeypress="return WE().util.IsDigit(event,this);" onkeyup="we_keep_ratio(this,this.form.widthSelect);"', "text", 60);
	$heightInput = we_html_tools::htmlTextInput("height", 10, $height, "", 'onkeypress="return WE().util.IsDigit(event,this);" onkeyup="we_keep_ratio(this,this.form.heightSelect);"', "text", 60);

	$widthSelect = '<select class="weSelect" name="widthSelect" onchange="we_switchPixelPercent(this.form.width,this);"><option value="pixel">' . g_l('weClass', '[pixel]') . '</option><option value="percent">' . g_l('weClass', '[percent]') . '</option></select>';
	$heightSelect = '<select class="weSelect" name="heightSelect" onchange="we_switchPixelPercent(this.form.height,this);"><option value="pixel">' . g_l('weClass', '[pixel]') . '</option><option value="percent">' . g_l('weClass', '[percent]') . '</option></select>';

	$ratio_checkbox = we_html_forms::checkbox(1, true, "ratio", g_l('thumbnails', '[ratio]'), false, "defaultfont", "if(this.checked){we_keep_ratio(this.form.width,this.form.widthSelect);}");

	$table = '<table>
	<tr>
		<td class="defaultfont">' . g_l('weClass', '[width]') . ':</td>
		<td>' . $widthInput . '</td>
		<td>' . $widthSelect . '</td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('weClass', '[height]') . ':</td>
		<td>' . $heightInput . '</td>
		<td>' . $heightSelect . '</td>
	</tr>
	<tr>
		<td colspan="3">' . $ratio_checkbox . '</td>
	</tr>
</table>' .
		(($GLOBALS['we_doc']->getGDType() === "jpg") ?
			'<br/><div class="defaultfont">' . g_l('weClass', '[quality]') . '</div>' . we_base_imageEdit::qualitySelect("quality") :
			'');
	$content[] = array("headline" => "", "html" => $table);
	return we_html_multiIconBox::getHTML("", $content, 30, $buttons, -1, "", "", false, g_l('weClass', '[resize]'));
}

function we_getImageConvertDialog(){
	$content = [];

	$okbut = we_html_button::create_button(we_html_button::OK, "javascript:doOK();");
	$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");
	$buttons = we_html_button::position_yes_no_cancel($okbut, null, $cancelbut);
	$dialog = '<div class="defaultfont">' . g_l('weClass', '[quality]') . '</div>' . we_base_imageEdit::qualitySelect("quality");
	$content[] = array("headline" => "", "html" => $dialog);


	return we_html_multiIconBox::getHTML("", $content, 30, $buttons, -1, "", "", false, g_l('weClass', '[convert]'));
}

function we_getImageRotateDialog(){
	$content = [];

	$okbut = we_html_button::create_button(we_html_button::OK, "javascript:doOK();");
	$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");

	$buttons = we_html_button::position_yes_no_cancel($okbut, null, $cancelbut);

	$radio180 = we_html_forms::radiobutton(180, true, "degrees", g_l('weClass', '[rotate180]'));
	$radio90l = we_html_forms::radiobutton(90, false, "degrees", g_l('weClass', '[rotate90l]'));
	$radio90r = we_html_forms::radiobutton(270, false, "degrees", g_l('weClass', '[rotate90r]'));

	$dialog = $radio180 . $radio90l . $radio90r .
		(($GLOBALS['we_doc']->getGDType() === "jpg") ?
			'<br/><div class="defaultfont">' . g_l('weClass', '[quality]') . '</div>' . we_base_imageEdit::qualitySelect("quality") :
			'');

	$content[] = array("headline" => "", "html" => $dialog);


	return we_html_multiIconBox::getHTML("", $content, 30, $buttons, -1, "", "", false, g_l('weClass', '[rotate]'));
}
