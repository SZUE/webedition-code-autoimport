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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$tagName = weRequest('string', 'we_cmd', '', 1);
$openAtCursor = weRequest('bool', 'we_cmd', false, 2);

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
$jsAllAttributes = 'var allAttributes = new Array(' . ($_attributes ? '"' . implode('", "', $_attributes) . '"' : '') . ');';

// #2 all required attributes
$_reqAttributes = $weTag->getRequiredAttributes();
$jsReqAttributes = "var reqAttributes = new Object();";
foreach($_reqAttributes as $_attribName){
	$jsReqAttributes .= 'reqAttributes["' . $_attribName . '"] = 1;';
}

// #3 all neccessary stuff for typeAttribute
if(($typeAttribute = $weTag->getTypeAttribute())){

	// name of the attribute
	$typeAttributeJs = 'var typeAttributeId = "' . $typeAttribute->getIdName() . '";';

	// allowed attributes
	$_typeOptions = $weTag->getTypeAttributeOptions();

	if($_typeOptions){
		$typeAttributeJs .= '
var typeAttributeAllows = new Object();
var typeAttributeRequires = new Object();';

		foreach($_typeOptions as $option){
			$_allowedAttribs = $option->getAllowedAttributes();
			if(empty($_allowedAttribs)){
				$typeAttributeJs .= 'typeAttributeAllows["' . $option->getName() . '"] = new Array();';
			} else {
				$typeAttributeJs .= 'typeAttributeAllows["' . $option->getName() . '"] = new Array("' .
					implode('","', $_allowedAttribs) .
					'");';
			}

			$_reqAttribs = $option->getRequiredAttributes($_attributes);
			if(empty($_reqAttribs)){
				$typeAttributeJs .= "typeAttributeRequires[\"" . $option->getName() . "\"] = new Array();";
			} else {
				$typeAttributeJs .= "typeAttributeRequires[\"" . $option->getName() . "\"] = new Array(\"" .
					implode('","', $_reqAttribs) .
					"\");";
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
echo we_html_tools::getHtmlTop() .
 STYLESHEET .
 we_html_element::cssLink(CSS_DIR . 'tagWizard.css') .
 we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'tagWizard.js') .
 we_html_element::jsScript(JS_DIR . 'keyListener.js') .
 we_html_element::jsScript(JS_DIR . 'attachKeyListener.js') . we_html_element::jsElement('


function closeOnEscape() {
	return true;
}

function applyOnEnter(evt) {
	_elemName = "target";
	if ( typeof(evt["srcElement"]) != "undefined" ) { // IE
		_elemName = "srcElement";
	}

	if (	!( evt[_elemName].tagName == "SELECT")) {
		we_cmd("saveTag");
		return true;
	}


}

' . $jsAllAttributes .
	$jsReqAttributes . '

weTagWizard = new weTagWizard("' . $weTag->getName() . '");
weTagWizard.allAttributes = allAttributes;
weTagWizard.reqAttributes = reqAttributes;
' . ($weTag->needsEndTag() ? 'weTagWizard.needsEndTag = true;' : '') . '

// information about the type-attribute
' . $typeAttributeJs . '
function we_cmd(){
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]){

		case "switch_type":
			weTagWizard.changeType(arguments[1]);
		break;

		case "saveTag":

			if (strWeTag = weTagWizard.getWeTag()) {' .
	( $openAtCursor ? '
				var contentEditor = opener.top.weEditorFrameController.getVisibleEditorFrame();
				contentEditor.window.addCursorPosition( strWeTag );
				self.close();;
				' : '
				var contentEditor = opener.top.weEditorFrameController.getVisibleEditorFrame();
				contentEditor.document.we_form.elements["tag_edit_area"].value=strWeTag;
    			contentEditor.document.we_form.elements["tag_edit_area"].select();
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

		case "openDirselector":
			new jsWindow(url,"we_fileselector",-1,-1,' . we_selector_file::WINDOW_DIRSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DIRSELECTOR_HEIGHT . ',true,true,true,true);
			break;
		case "openDocselector":
			new jsWindow(url,"we_fileselector",-1,-1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ',true,true,true,true);
			break;
		case "openSelector":
			new jsWindow(url,"we_fileselector",-1,-1,' . we_selector_file::WINDOW_SELECTOR_WIDTH . ',' . we_selector_file::WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
			break;
		case "openCatselector":
			new jsWindow(url,"we_catselector",-1,-1,' . we_selector_file::WINDOW_CATSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_CATSELECTOR_HEIGHT . ',true,true,true,true);
			break;
		case "browse_users":
	        new jsWindow(url,"browse_users",-1,-1,500,300,true,false,true);
	        break;
		default:
			for(var i = 0; i < arguments.length; i++){
				args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
			}
			eval("opener.top.we_cmd("+args+")");
			break;
	 }
}') . '
</head>
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
		we_html_button::create_button('ok', "javascript:we_cmd('saveTag');"), null, we_html_button::create_button('cancel', "javascript:self.close();")
);
?>
<div id="divTagName">
	<h1>&lt;we:<?php print $weTag->getName() . '&gt;' . ($weTag->isDeprecated() ? ' (' . g_l('taged', '[deprecated][title]') . ')' : ''); ?></h1>
</div>
<div id="divContent">
	<?php print $code; ?>
	<br>
</div>
<div id="divButtons">
	<div style="padding-top: 8px;">
		<?php print $_buttons; ?>
	</div>
</div>
<input type="submit" style="width:1px; height:1px; padding:0px; margin:0px; color:#fff; background-color:#fff; border:0px;" />
</form></body></html>
