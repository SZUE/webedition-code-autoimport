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
define("WE_EDIT_IMAGE", true);


echo we_html_tools::getHtmlTop() .
 we_html_element::jsElement(
	(substr(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0), 0, 15) === 'doImage_convert' ?
		'parent.frames.editHeader.we_setPath("' . $we_doc->Path . '","' . $we_doc->Text . '", ' . intval($we_doc->ID) . ',"published");' :
		'') .
	'function changeOption(elem){
	var cmnd = elem.options[elem.selectedIndex].value;
	if(cmnd){
		switch(cmnd){
			case "doImage_convertPNG":
			case "doImage_convertGIF":
				_EditorFrame.setEditorIsHot(true);
		}
		we_cmd(cmnd,"' . $we_transaction . '");
	}
	elem.selectedIndex=0;
}') .
 STYLESHEET .
 we_html_element::jsScript(JS_DIR . 'windows.js');
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
?>
</head>
<body class="weEditorBody" style="padding:20px;">
	<form name="we_form" method="post" onsubmit="return false;">
		<?php
		echo we_class::hiddenTrans();
		$_headline = g_l('weClass', '[image]');

		$_gdtype = $we_doc->getGDType();

		$supported = we_base_imageEdit::supported_image_types();
		$focus = we_unserialize($GLOBALS['we_doc']->getElement('focus', 'dat'), array(0, 0));
		echo '<table class="default">
' . ($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_IMAGEEDIT ?
			'<tr><td style="padding-bottom:20px;"><select name="editmenue" size="1" onchange="changeOption(this);"' . (($we_doc->getElement("data") && we_base_imageEdit::is_imagetype_read_supported($_gdtype) && we_base_imageEdit::gd_version() > 0) ? "" : ' disabled="disabled"') . '>
<option value="" selected="selected" disabled="disabled" style="color:grey"></option>
<optgroup label="' . g_l('weClass', '[edit]') . '">
<option value="image_resize">' . g_l('weClass', '[resize]') . '&hellip;</option>
<option value="image_rotate">' . g_l('weClass', '[rotate]') . '&hellip;</option>
<option value="image_crop">' . g_l('weClass', '[crop]') . '&hellip;</option>
<option value="image_focus">' . g_l('weClass', '[image_focus]') . '&hellip;</option>
</optgroup>
<optgroup label="' . g_l('weClass', '[convert]') . '">' .
			((in_array('jpg', $supported)) ? '<option value="image_convertJPEG">' . g_l('weClass', '[convert_jpg]') . '...</option>' : '') .
			(($_gdtype != "gif" && in_array('gif', $supported)) ? '<option value="doImage_convertGIF">' . g_l('weClass', '[convert_gif]') . '</option>' : '') .
			(($_gdtype != "png" && in_array('png', $supported)) ? '<option value="doImage_convertPNG">' . g_l('weClass', '[convert_png]') . '</option>' : '') .
			'</optgroup>
</select></td></tr>' :
			''
		) . '<tr><td>' . $we_doc->getHtml(true) . '
<div id="cursorVal" style="display:none">
<input type="number" id="x_focus" value="' . round(floatval($focus[0]), 2) . '" step="0.01" min="-1" max="1" onchange="setFocusPositionByValue();" />
<input type="number" id="y_focus" value="' . round(floatval($focus[1]), 2) . '" step="0.01" min="-1" max="1" onchange="setFocusPositionByValue();" />
<input type="hidden" name="we_' . $GLOBALS['we_doc']->Name . '_input[focus]" id="focus" value="[' . implode(',', $focus) . ']"/>
</div>
' .
		'</td></tr>'
		. '</table>' .
		we_html_element::htmlHidden('we_complete_request', 1);
		?>
	</form>
</body>
</html>