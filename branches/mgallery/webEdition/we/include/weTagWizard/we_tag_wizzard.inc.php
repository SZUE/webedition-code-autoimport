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
$tagName = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
$openAtCursor = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 2);

we_html_tools::protect();

// include wetag depending on we_cmd[1]
$weTag = weTagData::getTagData($tagName);
if(!$weTag){
	echo sprintf(g_l('taged', '[tag_not_found]'), $tagName);
	exit;
}


// needed javascript for the individual tags
// #1 - all attributes of this we:tag (ids of attributes)
$_attributes = $weTag->getAllAttributes(true);

// #2 all required attributes
$_reqAttributes = $weTag->getRequiredAttributes();
$jsReqAttributes = array();
foreach($_reqAttributes as $_attribName){
	$jsReqAttributes[] = '"' . $_attribName . '": 1';
}

// #3 all neccessary stuff for typeAttribute
if(($typeAttribute = $weTag->getTypeAttribute())){

	// name of the attribute
	$typeAttributeJs = 'var typeAttributeId = "' . $typeAttribute->getIdName() . '";';

	// allowed attributes
	$_typeOptions = $weTag->getTypeAttributeOptions();

	if($_typeOptions){
		$typeAttributeJs .= '
var typeAttributeAllows = {};
var typeAttributeRequires = {};';

		foreach($_typeOptions as $option){
			$_allowedAttribs = $option->getAllowedAttributes();
			if(empty($_allowedAttribs)){
				$typeAttributeJs .= 'typeAttributeAllows["' . $option->getName() . '"] = [];';
			} else {
				$typeAttributeJs .= 'typeAttributeAllows["' . $option->getName() . '"] = ["' .
					implode('","', $_allowedAttribs) .
					'"];';
			}

			$_reqAttribs = $option->getRequiredAttributes($_attributes);
			if($_reqAttribs){
				$typeAttributeJs .= 'typeAttributeRequires["' . $option->getName() . '"] = ["' .
					implode('","', $_reqAttribs) .
					'"];';
			} else {
				$typeAttributeJs .= 'typeAttributeRequires["' . $option->getName() . '"] = [];';
			}
		}

		$typeAttributeJs .= '
weTagWizard.typeAttributeAllows = typeAttributeAllows;
weTagWizard.typeAttributeRequires = typeAttributeRequires;';
	}
	$typeAttributeJs .= "weTagWizard.typeAttributeId = typeAttributeId;";
} else {
	$typeAttributeJs = '';
}
// additional javascript for the individual tags - end
// print html header of page
echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET .
	we_html_element::cssLink(CSS_DIR . 'tagWizard.css') .
	we_html_element::jsScript(JS_DIR . 'tagWizard.js') .
	we_html_element::jsElement('
var allAttributes = [' . ($_attributes ? '"' . implode('", "', $_attributes) . '"' : '') . '];
var reqAttributes = {' . implode(',', $jsReqAttributes) . '};

weTagWizard = new weTagWizard("' . $weTag->getName() . '");
weTagWizard.allAttributes = allAttributes;
weTagWizard.reqAttributes = reqAttributes;
weTagWizard.needsEndTag = ' . ($weTag->needsEndTag() ? 'true' : 'false') . ';

// information about the type-attribute
' . $typeAttributeJs . '
function we_cmd(){
	var args = "";
	var url = WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?";
	for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]){
		case "switch_type":
			weTagWizard.changeType(arguments[1]);
		break;

		case "saveTag":

			if (strWeTag = weTagWizard.getWeTag()) {
				var contentEditor = opener.top.weEditorFrameController.getVisibleEditorFrame();
			' .
		( $openAtCursor ? '
				contentEditor.window.addCursorPosition( strWeTag );
				self.close();;
				' : '
				contentEditor.document.we_form.elements.tag_edit_area.value=strWeTag;
   			contentEditor.document.we_form.elements.tag_edit_area.select();
    			self.close();'
		) . '
			} else {
				if (weTagWizard.missingFields.length) {
					req = "";
					for (i=0;i<weTagWizard.missingFields.length;i++) {
						req += "- " + weTagWizard.missingFields[i] + "\n";
					}
					req = "' . g_l('taged', '[fill_required_fields]') . '\n" + req;
					' . we_message_reporting::getShowMessageCall("req", we_message_reporting::WE_MESSAGE_WARNING, true) . '
				} else {
					' . we_message_reporting::getShowMessageCall(g_l('taged', '[no_type_selected]'), we_message_reporting::WE_MESSAGE_WARNING) . '
				}
			}
		break;

		case "we_selector_directory":
			new jsWindow(url,"we_fileselector",-1,-1,WE().consts.size.windowDirSelect.width,WE().consts.size.windowDirSelect.height,true,true,true,true);
			break;
		case "we_selector_document":
		case "we_selector_image":
			new jsWindow(url,"we_fileselector",-1,-1,WE().consts.size.docSelect.width,WE().consts.size.docSelect.height,true,true,true,true);
			break;
		case "we_selector_file":
			new jsWindow(url,"we_fileselector",-1,-1,WE().consts.size.windowSelect.width,WE().consts.size.windowSelect.height,true,true,true,true);
			break;
		case "we_selector_category":
			new jsWindow(url,"we_catselector",-1,-1,WE().consts.size.catSelect.width,WE().consts.size.catSelect.height,true,true,true,true);
			break;
		case "we_users_selector":
	        new jsWindow(url,"browse_users",-1,-1,500,300,true,false,true);
	        break;
		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			opener.we_cmd.apply(this, args);

			break;
	 }
}')) . '
<body onload="window.focus();" class="defaultfont">
<form name="we_form" onsubmit="we_cmd(\'saveTag\'); return false;">';
// start building the content of the page
// get all attributes
$typeAttribCode = $weTag->getTypeAttributeCodeForTagWizard();
$attributesCode = $weTag->getAttributesCodeForTagWizard();
$defaultValueCode = ($weTag->needsEndTag() ? $weTag->getDefaultValueCodeForTagWizard() : '');

if($typeAttribCode){

	$typeAttribCode = '<hr /><fieldset>
		<div class="legend"><strong>' . g_l('taged', '[type_attribute]') . "</strong></div>
		$typeAttribCode
	</fieldset>";
}
if($attributesCode){

	$attributesCode = '<hr/><fieldset>
		<div class="legend"><strong>' . g_l('taged', '[attributes]') . "</strong></div>
		" . ($typeAttribCode ? '<ul id="no_type_selected_attributes"><li>' . g_l('taged', '[no_type_selected]') . '</li></ul>' : '' ) . "
		" . ($typeAttribCode ? '<ul id="no_attributes_for_type" style="display: none;"><li>' . g_l('taged', '[no_attributes_for_type]') . '</li></ul>' : '' ) . "
		$attributesCode
	</fieldset>";
}
if($defaultValueCode){

	$defaultValueCode = '<hr/><fieldset>
		<div class="legend"><strong>' . we_html_element::htmlLabel(array('id' => 'label_weTagData_defaultValue', 'for' => 'weTagData_defaultValue'), g_l('taged', '[defaultvalue]') . ':<br />') . "</strong></div>
		$defaultValueCode
	</fieldset>";
}

$code = '<fieldset>
		<div class="legend"><strong>' . g_l('taged', '[description]') . '</strong></div>' .
	($weTag->isDeprecated() ? we_html_tools::htmlAlertAttentionBox(g_l('taged', '[deprecated][description]'), we_html_tools::TYPE_ALERT, '98%') : '') . $weTag->getDescription() .
	'</fieldset>' . $typeAttribCode . ' ' . $attributesCode . ' ' .
	$defaultValueCode;

$_buttons = we_html_button::position_yes_no_cancel(
		we_html_button::create_button(we_html_button::OK, "javascript:we_cmd('saveTag');"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
);
?>
<div id="divTagName">
	<h1>&lt;we:<?php echo $weTag->getName() . '&gt;' . ($weTag->isDeprecated() ? ' (' . g_l('taged', '[deprecated][title]') . ')' : ''); ?></h1>
</div>
<div id="divContent">
	<?php echo $code; ?>
	<br/>
</div>
<div id="divButtons">
	<div style="padding-top: 8px;">
		<?php echo $_buttons; ?>
	</div>
</div>
<input type="submit" style="width:1px; height:1px; padding:0px; margin:0px; color:#fff; background-color:#fff; border:0px;" />
</form></body></html>
