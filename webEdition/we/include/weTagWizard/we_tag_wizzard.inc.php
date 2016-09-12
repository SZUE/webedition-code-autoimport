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

$tw = [
	'tagName' => $weTag->getName(),
	'needsEndTag' => $weTag->needsEndTag(),
	'openAtCursor' => $openAtCursor,
	'reqAttributes' => [],
	'typeAttributeId' => '',
	'typeAttributeAllows' => [],
	'typeAttributeRequires' => [],
];
// needed javascript for the individual tags
// #1 - all attributes of this we:tag (ids of attributes)
$tw['attributes'] = $weTag->getAllAttributes(true);

// #2 all required attributes
$reqAttributes = $weTag->getRequiredAttributes();
foreach($reqAttributes as $attribName){
	$tw['reqAttributes'][$attribName] = 1;
}

// #3 all neccessary stuff for typeAttribute
if(($typeAttribute = $weTag->getTypeAttribute())){
	// name of the attribute
	$tw['typeAttributeId'] = $typeAttribute->getIdName();

	// allowed attributes
	$typeOptions = $weTag->getTypeAttributeOptions();

	if($typeOptions){
		foreach($typeOptions as $option){
			$allowedAttribs = $option->getAllowedAttributes();
			$tw['typeAttributeAllows'][$option->getName()] = (empty($allowedAttribs) ? [] : $allowedAttribs);

			$reqAttribs = $option->getRequiredAttributes($tw['attributes']);
			$tw['typeAttributeRequires'][$option->getName()] = (empty($reqAttribs) ? [] : $reqAttribs);
		}
	}
}
// additional javascript for the individual tags - end
// print html header of page
echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', we_html_element::cssLink(CSS_DIR . 'tagWizard.css') .
	we_html_element::jsScript(JS_DIR . 'tagWizard.js', '', ['id' => 'loadVarTagWizard', 'data-tw' => setDynamicVar($tw)]));
?>
<body onload="self.focus();" class="defaultfont tagWizzard">
	<form name="we_form" onsubmit="we_cmd('saveTag');
			return false;"><?php
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
		<div class="legend"><strong>' . we_html_element::htmlLabel(['id' => 'label_weTagData_defaultValue', 'for' => 'weTagData_defaultValue'], g_l('taged', '[defaultvalue]') . ':<br />') . "</strong></div>
		$defaultValueCode
	</fieldset>";
					}

					$code = '<fieldset>
		<div class="legend"><strong>' . g_l('taged', '[description]') . '</strong></div>' .
						($weTag->isDeprecated() ? we_html_tools::htmlAlertAttentionBox(g_l('taged', '[deprecated][description]'), we_html_tools::TYPE_ALERT, '98%') : '') . $weTag->getDescription() .
						'</fieldset>' . $typeAttribCode . ' ' . $attributesCode . ' ' .
						$defaultValueCode;
					?>
		<div id="divTagName">
			<h1>&lt;we:<?= $weTag->getName() . '&gt;' . ($weTag->isDeprecated() ? ' (' . g_l('taged', '[deprecated][title]') . ')' : ''); ?></h1>
		</div>
		<div id="divContent">
			<?= $code; ?>
			<br/>
		</div>
		<div id="divButtons">
			<div style="padding-top: 8px;">
				<?=
				we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:we_cmd('saveTag');"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
				);
				?>
			</div>
		</div>
		<input type="submit" style="width:1px; height:1px; padding:0px; margin:0px; color:#fff; background-color:#fff; border:0px;" />
	</form></body></html>
