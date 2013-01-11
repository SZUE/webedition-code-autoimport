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
/* * ***************************************************************************
 * INCLUDES
 * *************************************************************************** */

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once(WE_INCLUDES_PATH . 'we_delete_fn.inc.php');

we_html_tools::protect();

/**
 * This function returns the HTML code of a dialog.
 *
 * @param          string                                  $name
 * @param          string                                  $title
 * @param          array                                   $content
 * @param          int                                     $expand             (optional)
 * @param          string                                  $show_text          (optional)
 * @param          string                                  $hide_text          (optional)
 * @param          bool                                    $cookie             (optional)
 * @param          string                                  $JS                 (optional)
 *
 * @return         string
 */
function create_dialog($name, $title, $content, $expand = -1, $show_text = "", $hide_text = "", $cookie = false, $JS = ""){
	$_output = "";

	// Check, if we need to write some JavaScripts
	if($JS != ""){
		$_output .= $JS;
	}

	if($expand != -1){
		$_output .= we_multiIconBox::getJS();
	}

	// Return HTML code of dialog
	return $_output . we_multiIconBox::getHTML($name, "100%", $content, 30, "", $expand, $show_text, $hide_text, $cookie != false ? ($cookie == "down") : $cookie, $title);
}

/**
 * This functions saves all options.
 *
 * @return         void
 */
function save_all_values(){
	/*	 * ***********************************************************************
	 * SAVE METADATA FIELDS TO DB
	 * *********************************************************************** */
	if(we_hasPerm("ADMINISTRATOR")){
		// save all fields
		$_definedFields = array();
		if(isset($_REQUEST["metadataTag"]) && is_array($_REQUEST["metadataTag"])){
			foreach($_REQUEST["metadataTag"] as $key => $value){
				$_definedFields[] = array(
					"id" => "", // will be genereated by rdbms (autoincrement pk)
					"tag" => $value,
					"type" => (isset($_REQUEST["metadataType"][$key])) ? $_REQUEST["metadataType"][$key] : "",
					"importFrom" => (isset($_REQUEST["metadataImportFrom"][$key])) ? $_REQUEST["metadataImportFrom"][$key] : ""
				);
			}
		}
		$truncateQuery = "truncate table " . METADATA_TABLE . ";";
		$_insertQuery = array();
		foreach($_definedFields as $key => $value){
			$_insertQuery[] = "insert into " . METADATA_TABLE . " 	values('','" . $GLOBALS['DB_WE']->escape($value['tag']) . "','" . $GLOBALS['DB_WE']->escape($value['type']) . "','" . $GLOBALS['DB_WE']->escape($value['importFrom']) . "');";
		}

		$GLOBALS['DB_WE']->query($truncateQuery);
		foreach($_insertQuery as $value){
			$GLOBALS['DB_WE']->query($value);
		}
	}
}

function build_dialog($selected_setting = "ui"){

	switch($selected_setting){
		// save dialog:
		case "save":
			$_settings = array(
				array("headline" => "", "html" => g_l('metadata', "[save]"), "space" => 0)
			);
			return create_dialog("", g_l('metadata', "[save_wait]"), $_settings);

		// SAVED SUCCESSFULLY DIALOG:
		case "saved":
			$_content = array(
				array("headline" => "", "html" => g_l('metadata', "[saved]"), "space" => 0)
			);
			// Build dialog element if user has permission
			return create_dialog("", g_l('metadata', "[saved_successfully]"), $_content);

		// THUMBNAILS
		case "dialog":
			$_headline = we_html_element::htmlDiv(array("class" => "weDialogHeadline", "style" => "padding:10 25 5 25;"), g_l('metadata', "[headline]"));

			// read already defined metadata fields from db:
			$GLOBALS['DB_WE']->query('SELECT * FROM ' . METADATA_TABLE);
			$_defined_fields = $GLOBALS['DB_WE']->getAll();

			$_metadata_types = array(
				"textfield" => "textfield",
				"textarea" => "textarea",
				//"wysiwyg" 	=> "wysiwyg",
				"date" => "date"
			);

			$_metadata_fields = array('' => '-- ' . g_l('metadata', '[add]') . ' --', 'Exif' => we_html_tools::OPTGROUP);
			$_tmp = weMetaData_Exif::getUsedFields();
			foreach($_tmp as $key){
				$_metadata_fields[$key] = $key;
			}
			$_tmp = weMetaData_IPTC::getUsedFields();
			$_metadata_fields['IPTC'] = we_html_tools::OPTGROUP;
			foreach($_tmp as $key){
				$_metadata_fields[$key] = $key;
			}

			$_onChange = '';

			$_i = 0;
			$_adv_row = '';
			$_first = 0;

			foreach($_defined_fields as $key => $value){
				$_adv_row .= '
				<tr id="metadataRow_' . $key . '">
					<td width="210" style="padding-right:5px;">
						' . we_html_tools::htmlTextInput('metadataTag[' . $key . ']', 24, $value['tag'], 255, "", "text", 205) . '
					</td>
					<td width="200">
						' .
					we_html_tools::htmlSelect('metadataType[' . $key . ']', $_metadata_types, 1, $value['type'], false, 'class="defaultfont" ')
					. '
					</td>
					<td align="right" width="30">
						' .
					we_button::create_button("image:btn_function_trash", "javascript:delRow(" . $_i . ")")
					. '
					</td>
				</tr>
				<tr id="metadataRow2_' . $key . '">
					<td style="padding-bottom:10px;padding-right:5px;">
						<div class="small">' . oldHtmlspecialchars(g_l('metadata', "[import_from]")) . '</div>' . we_html_tools::htmlTextInput('metadataImportFrom[' . $key . ']', 24, $value['importFrom'], 255, "", "text", 205) . '
					</td>
					<td colspan="2" style="padding-bottom:10px;">
						<div class="small">' . oldHtmlspecialchars(g_l('metadata', "[fields]")) . '</div>' .
					we_html_tools::htmlSelect('add_' . $key, $_metadata_fields, 1, "", false, 'class="defaultfont" style="width:100%" onchange="addFieldToInput(this,' . $key . ')"')
					. '
					</td>
				</tr>
				';
				$_i++;
			}

			$_metadataTable = '
			<table border="0" cellpadding="0" cellspacing="0" width="440">
				<tbody id="metadataTable">
					<tr>
						<td class="defaultfont" style="width:210px;"><strong>' . g_l('metadata', "[tagname]") . '</strong></td>
						<td class="defaultfont" style="width:110px;" colspan="2"><strong>' . g_l('metadata', "[type]") . '</strong></td>
					</tr>
					' . $_adv_row . '
				</tbody>
			</table>';

			$js = we_html_element::jsElement('

				function addRow() {

					var tagInp = "' . addslashes(we_html_tools::htmlTextInput('metadataTag[__we_new_id__]', 24, "", 255, "", "text", 210)) . '";
					var importInp = "' . addslashes(we_html_tools::htmlTextInput('metadataImportFrom[__we_new_id__]', 24, "", 255, "", "text", 210)) . '";
					var typeSel = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('metadataType[__we_new_id__]', $_metadata_types, 1, "textfield", false, 'class="defaultfont"'))) . '";
					var fieldSel = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('metadataType[__we_new_id__]', $_metadata_fields, 1, "", false, 'class="defaultfont" style="width:100%"  onchange="addFieldToInput(this,__we_new_id__)"'))) . '";

					var elem = document.getElementById("metadataTable");
					newID = (elem.rows.length-1) / 2;
					if(elem){

						var newRow = document.createElement("TR");
	        			newRow.setAttribute("id", "metadataRow_" + newID);

						cell = document.createElement("TD");
	        			cell.innerHTML=tagInp.replace(/__we_new_id__/,newID);
	        			cell.width="210";
	        			newRow.appendChild(cell);

	        			cell = document.createElement("TD");
	        			cell.innerHTML=typeSel.replace(/__we_new_id__/,newID);
	        			cell.width="200";
	        			newRow.appendChild(cell);

	        			cell = document.createElement("TD");
	        			cell.width="30";
	        			cell.align="right"
	        			cell.innerHTML=\'' . we_button::create_button("image:btn_function_trash", "javascript:delRow('+newID+')") . '\';
	        			newRow.appendChild(cell);

	        			elem.appendChild(newRow);

	        			newRow = document.createElement("TR");
	        			newRow.setAttribute("id", "metadataRow2_" + newID);

						cell = document.createElement("TD");
						cell.style.paddingBottom="10px";
	        			cell.innerHTML=\'<div class="small">' . oldHtmlspecialchars(g_l('metadata', "[import_from]")) . '</div>\'+importInp.replace(/__we_new_id__/,newID);
	        			newRow.appendChild(cell);
						cell = document.createElement("TD");
						cell.setAttribute("colspan",2);
						cell.style.paddingBottom="10px";
	        			cell.innerHTML=\'<div class="small">' . oldHtmlspecialchars(g_l('metadata', "[fields]")) . '</div>\'+fieldSel.replace(/__we_new_id__/g,newID);
	        			newRow.appendChild(cell);
	        			elem.appendChild(newRow);
					}
				}

				function delRow(id) {
					var elem = document.getElementById("metadataTable");
					if(elem){
						var trows = elem.rows;
						var rowID = "metadataRow_" + id;
						var rowID2 = "metadataRow2_" + id;

			        	for (i=trows.length-1;i>=0;i--) {
			        		if(rowID == trows[i].id || rowID2 == trows[i].id) {
			        			elem.deleteRow(i);
			        		}
			        	}

					}
				}
				function init() {
					self.focus();
				}

				function addFieldToInput(sel, inpNr) {
					if (sel && sel.selectedIndex >= 0 && sel.options[sel.selectedIndex].parentNode.nodeName.toLowerCase() == "optgroup") {
						var _inpElem = document.forms[0].elements["metadataImportFrom["+inpNr+"]"];
						var _metaType = sel.options[sel.selectedIndex].parentNode.label.toLowerCase();
						var _str = _metaType + "/" + sel.options[sel.selectedIndex].value;
						_inpElem.value = _inpElem.value ? _inpElem.value + (","+_str) : _str;
					}
					sel.selectedIndex = 0;
				}

			');

			$_hint = we_html_tools::htmlAlertAttentionBox(g_l('metadata', '[fields_hint]'), 1, 440, false);

			$_content = $_hint . '<div style="height:20px"></div>' . $_metadataTable . we_button::create_button("image:btn_function_plus", "javascript:addRow()");

			$_contentFinal = array(
				array("headline" => "", "html" => $_content, "space" => 0)
			);
			// Build dialog element if user has permission
			return create_dialog("settings_predefined", g_l('metadata', "[headline]"), $_contentFinal, -1, "", "", false, $js);
	}
	return '';
}

/**
 * This functions renders the complete dialog.
 *
 * @return         string
 */
function render_dialog(){
	// Render setting groups
	return we_html_element::htmlDiv(array("id" => "metadatafields_dialog"), build_dialog("dialog")) .
		// Render save screen
		we_html_element::htmlDiv(array("id" => "metadatafields_save", "style" => "display: none;"), build_dialog("save"));
}

/* * ***************************************************************************
 * RENDER FILE
 * *************************************************************************** */

we_html_tools::htmlTop();
// Check if we need to save settings
if(isset($_REQUEST["save_metadatafields"]) && $_REQUEST["save_metadatafields"] == "true"){
	$save_javascript = '';

	if(isset($_REQUEST["metadatafields_name"]) && (strpos($_REQUEST["metadatafields_name"], "'") !== false || strpos($_REQUEST["metadatafields_name"], ",") !== false)){
		$save_javascript = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[metadatafields_hochkomma]"), we_message_reporting::WE_MESSAGE_ERROR) .
				'history.back()');
	} else{
		save_all_values();

		$save_javascript = we_html_element::jsElement($save_javascript .
				we_message_reporting::getShowMessageCall(g_l('metadata', "[saved]"), we_message_reporting::WE_MESSAGE_NOTICE) .
				'top.close();');
	}

	print STYLESHEET . $save_javascript . '</head>' .
		we_html_element::htmlBody(array("class" => "weDialogBody"), build_dialog("saved")) . '</html>';
} else{
	$_form = we_html_element::htmlForm(array("name" => "we_form", "method" => "get", "action" => $_SERVER["SCRIPT_NAME"]), we_html_element::htmlHidden(array("name" => "save_metadatafields", "value" => "false")) . render_dialog());

	print STYLESHEET . '</head>' .
		we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "init()"), $_form) . "</html>";
}
