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

/**
 *  view class for document customer filters
 *
 */
class we_customer_documentFilterView extends we_customer_filterView{

	/**
	 * Gets the HTML and Javascript for the filter
	 *
	 * @return string
	 */
	function getFilterHTML($ShowModeNone = false){
		return parent::getFilterHTML() . '<div style="height: 20px;"></div>' .
			$this->getAccessControlHTML() .
			(($GLOBALS['we_doc']->ContentType === "folder") ? ('<div style="height: 20px;"></div>' . $this->getFolderApplyHTML()) : "");
	}

	/**
	 * Gets the HTML and Javascript for the access control ui
	 *
	 * @return string
	 */
	function getAccessControlHTML(){
		$filter = $this->getFilter();

		$yuiSuggest = & weSuggest::getInstance();

		/*		 * ** AUTOSELECTOR FOR ErrorDocument, Customer is not logged in *** */
		$id_selectorNoLoginId = $filter->getErrorDocNoLogin();
		$path_selectorNoLoginId = $id_selectorNoLoginId ? id_to_path($id_selectorNoLoginId) : "";
		if(!$path_selectorNoLoginId){
			$id_selectorNoLoginId = "";
		}

		$selectorNoLoginId = "wecf_noLoginId";
		$selectorNoLoginText = "wecf_InputNoLoginText";
		//$selectorNoLoginError = "wecf_ErrorMarkNoLoginText";
		$selectorNoLoginButton = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $selectorNoLoginId . "'].value,'" . FILE_TABLE . "','" . $selectorNoLoginId . "','" . $selectorNoLoginText . "','setHot','','','" . we_base_ContentTypes::WEDOCUMENT . "',1)") . "<div id=\"wecf_container_noLoginId\"></div>";

		$yuiSuggest->setAcId("NoLogin");
		$yuiSuggest->setContentType("folder," . we_base_ContentTypes::WEDOCUMENT);
		$yuiSuggest->setInput($selectorNoLoginText, $path_selectorNoLoginId);
		$yuiSuggest->setLabel(g_l('modules_customerFilter', '[documentNoLogin]'));
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($selectorNoLoginId, $id_selectorNoLoginId);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setWidth(409);
		$yuiSuggest->setSelectButton($selectorNoLoginButton);

		$weAcSelector = $yuiSuggest->getHTML();

		/*		 * ** AUTOSELECTOR FOR ErrorDocument, Customer might be logged in, but has no access *** */
		$id_selectorNoAccessId = $filter->getErrorDocNoAccess();
		$path_selectorNoAccessId = $id_selectorNoAccessId ? id_to_path($id_selectorNoAccessId) : "";
		if(!$path_selectorNoAccessId){
			$id_selectorNoAccessId = "";
		}

		$selectorNoAccessId = "wecf_noAccessId";
		$selectorNoAccessText = "wecf_InputNoAccessText";
		//$selectorNoAccessError = "wecf_ErrorMarkNoAccessText";
		$selectorNoAccessButton = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $selectorNoAccessId . "'].value,'" . FILE_TABLE . "','" . $selectorNoAccessId . "','" . $selectorNoAccessText . "','setHot','','','" . we_base_ContentTypes::WEDOCUMENT . "',1)");

		$yuiSuggest->setAcId("NoAccess");
		$yuiSuggest->setContentType("folder," . we_base_ContentTypes::WEDOCUMENT);
		$yuiSuggest->setInput($selectorNoAccessText, $path_selectorNoAccessId);
		$yuiSuggest->setLabel(g_l('modules_customerFilter', '[documentNoAccess]'));
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($selectorNoAccessId, $id_selectorNoAccessId);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setWidth(409);
		$yuiSuggest->setSelectButton($selectorNoAccessButton);

		$weAcSelector2 = $yuiSuggest->getHTML();

		$accesControl = '<div class="weMultiIconBoxHeadline">' .
			g_l('modules_customerFilter', '[accessControl]') . '</div>' .
			we_html_forms::radiobutton(
				"onTemplate", $filter->getAccessControlOnTemplate(), "wecf_accessControlOnTemplate", g_l('modules_customerFilter', '[accessControlOnTemplate]'), true, "defaultfont", "updateView();top.content.setHot();") .
			we_html_forms::radiobutton(
				"errorDoc", !$filter->getAccessControlOnTemplate(), "wecf_accessControlOnTemplate", g_l('modules_customerFilter', '[accessControlOnErrorDoc]'), true, "defaultfont", "updateView();top.content.setHot();") .
			we_customer_documentFilterView::getDiv($weAcSelector . $weAcSelector2, 'accessControlSelectorDiv', (!$filter->getAccessControlOnTemplate()), 25);



		return weSuggest::getYuiFiles() .
			$this->getDiv($accesControl, 'accessControlDiv', $filter->getMode() !== we_customer_abstractFilter::OFF, 0);
	}

	/**
	 * Gets the HTML and Javascript for the folder apply ui (copy filter)
	 *
	 * @return string
	 */
	function getFolderApplyHTML(){
		$ok_button = we_html_button::create_button(we_html_button::OK, "javascript:if (_EditorFrame.getEditorIsHot()) { " . we_message_reporting::getShowMessageCall(g_l('modules_customerFilter', '[apply_filter_isHot]'), we_message_reporting::WE_MESSAGE_INFO) . " } else { we_cmd('copyWeDocumentCustomerFilter', '" . $GLOBALS['we_doc']->ID . "', '" . $GLOBALS['we_doc']->Table . "');}");

		return '
<div class="weMultiIconBoxHeadline paddingVertical" style="padding-top: 10px;padding-bottom: 10px;">' . g_l('modules_customerFilter', '[apply_filter]') . '</div>
<table>
	<tr>
		<td>' . we_html_tools::htmlAlertAttentionBox(g_l('modules_customerFilter', '[apply_filter_info]'), we_html_tools::TYPE_INFO, 432, false) . '</td>
		<td style="padding-left:17px;">' . $ok_button . '</td>
	</tr>
</table>';
	}

	/**
	 * Creates the content for the JavaScript updateView() function
	 *
	 * @return string
	 */
	function createUpdateViewScript(){
		return parent::createUpdateViewScript() . <<<EOF
	var r2 = f.wecf_accessControlOnTemplate;
	var wecf_onTemplateRadio 	= r2[0];
	var wecf_errorDocRadio 		= r2[1];

	getById('accessControlSelectorDiv').style.display = wecf_errorDocRadio.checked ? "block" : "none";
	getById('accessControlDiv').style.display = modeRadioOff.checked ? "none" : "block";

EOF;
	}

}
