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
echo we_html_tools::getHtmlTop(g_l('metadata', '[headline]'));

function getFooter(){
	// Define needed JS
	$_meta_field_empty_messsage = addslashes(g_l('metadata', '[error_meta_field_empty_msg]'));
	$_meta_field_wrong_chars_messsage = addslashes(g_l('metadata', '[meta_field_wrong_chars_messsage]'));
	$_meta_field_wrong_name_messsage = addslashes(g_l('metadata', '[meta_field_wrong_name_messsage]'));

	$_javascript = <<< END_OF_SCRIPT
function we_save() {
	var _doc = document;


	var _z = 0;
	var _field = _doc.forms[0].elements['metadataTag[' + _z + ']'] !== undefined ? _doc.forms[0].elements['metadataTag[' + _z + ']'] : null;

	while (_field != null) {
		if (!checkMetaFieldName(_field, _z)) {
			return;
		}
		_z++;
		_field = _doc.forms[0].elements['metadataTag[' + _z + ']'] !== undefined ? _doc.forms[0].elements['metadataTag[' + _z + ']'] : null;
	}

	_doc.getElementById('metadatafields_dialog').style.display = 'none';

	_doc.getElementById('metadatafields_save').style.display = '';

	_doc.we_form.save_metadatafields.value = 'true';
	_doc.we_form.submit();
}

function checkMetaFieldName(inpElem, nr) {
	var _val = inpElem.value;
	var _forbiddenNames = ",data,width,height,border,align,hspace,vspace,alt,name,title,longdescid,useMetaTitle,scale,play,autoplay,quality,attrib,salign,loop,controller,volume,hidden,";
	var _errtxt = "";
	if (_val === "") {
		_errtxt = "$_meta_field_empty_messsage";
		_errtxt = _errtxt.replace(/%s1/,nr+1);
	} else if (_val.search(/[^a-zA-z0-9_]/) != -1) {
		_errtxt = "$_meta_field_wrong_chars_messsage";
		_errtxt = _errtxt.replace(/%s1/,_val);
	} else if (_forbiddenNames.indexOf(","+_val+",") >= 0) {
		_errtxt = "$_meta_field_wrong_name_messsage";
		_errtxt = _errtxt.replace(/%s1/,_val);
		_errtxt = _errtxt.replace(/%s2/,"\\n" + _forbiddenNames.substring(1,_forbiddenNames.length-1).replace(/,/g,", "));
	}


	if (_errtxt !== "") {
		inpElem.focus();
		inpElem.select();
		top.opener.top.showMessage(_errtxt, 4, top);
		return false;
	}
	return true;
}

END_OF_SCRIPT;

	return we_html_element::jsElement($_javascript) .
		we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'height:100%;'), we_html_button::position_yes_no_cancel(we_html_button::create_button("ok", "javascript:we_save();"), "", we_html_button::create_button("cancel", "javascript:" . "top.close()"), 10, '', '', 0));
}

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
function create_dialog($name, $title, $content, $expand = -1, $show_text = '', $hide_text = '', $cookie = false, $JS = ''){

	// Check, if we need to write some JavaScripts
	return
		($JS === '' ? '' : $JS ) .
		($expand != -1 ? we_html_multiIconBox::getJS() : '') .
		// Return HTML code of dialog
		we_html_multiIconBox::getHTML($name, '100%', $content, 30, '', $expand, $show_text, $hide_text, $cookie != false ? ($cookie === 'down') : $cookie, $title);
}

/**
 * This functions saves all options.
 *
 * @return         void
 */
function save_all_values(){
	//SAVE METADATA FIELDS TO DB
	if(permissionhandler::hasPerm('ADMINISTRATOR')){
		t_e("props", $_REQUEST['metadataProposal'], we_base_request::_(we_base_request::STRING, 'metadataProposal', ''));
		// save all fields
		$GLOBALS['DB_WE']->query('TRUNCATE TABLE ' . METADATA_TABLE);
		if(isset($_REQUEST['metadataTag']) && is_array($_REQUEST['metadataTag'])){
			foreach($_REQUEST['metadataTag'] as $key => $value){
				$GLOBALS['DB_WE']->query('INSERT INTO ' . METADATA_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'tag' => $value,
						'type' => we_base_request::_(we_base_request::STRING, 'metadataType', '', $key),
						'importFrom' => we_base_request::_(we_base_request::RAW, 'metadataImportFrom', '', $key),
				)));
			}
		}
	}
}

function build_dialog($selected_setting = 'ui'){

	switch($selected_setting){
		// save dialog:
		case 'save':
			$_settings = array(
				array('headline' => '', 'html' => g_l('metadata', '[save]'), 'space' => 0)
			);
			return create_dialog('', g_l('metadata', '[save_wait]'), $_settings);

		// SAVED SUCCESSFULLY DIALOG:
		case 'saved':
			$_content = array(
				array('headline' => '', 'html' => g_l('metadata', '[saved]'), 'space' => 0)
			);
			// Build dialog element if user has permission
			return create_dialog('', g_l('metadata', '[saved_successfully]'), $_content);

		// THUMBNAILS
		case 'dialog':
			$_headline = we_html_element::htmlDiv(array('class' => 'weDialogHeadline', 'style' => 'padding:10px 25px 5px 25px;'), g_l('metadata', '[headline]'));

			// read already defined metadata fields from db:
			$GLOBALS['DB_WE']->query('SELECT * FROM ' . METADATA_TABLE);
			$_defined_fields = $GLOBALS['DB_WE']->getAll();

			$_metadata_types = array(
				'textfield' => 'textfield',
				'textarea' => 'textarea',
				//'wysiwyg' 	=> 'wysiwyg',
				'date' => 'date'
			);
			
			$_proposal_types = array(
				'none' => 'keine',
				'manually' => 'manuell',

				'auto' => 'automatisch'
			);

			$_metadata_fields = array('' => '-- ' . g_l('metadata', '[add]') . ' --', 'Exif' => we_html_tools::OPTGROUP);
			$_tmp = we_metadata_Exif::getUsedFields();
			foreach($_tmp as $key){
				$_metadata_fields[$key] = $key;
			}
			$_tmp = we_metadata_IPTC::getUsedFields();
			$_metadata_fields['IPTC'] = we_html_tools::OPTGROUP;
			foreach($_tmp as $key){
				$_metadata_fields[$key] = $key;
			}

			$_i = 0;
			$_adv_row = '';

			foreach($_defined_fields as $key => $value){
				$_adv_row .= '
<tr>
	<td class="defaultfont" style="width:210px;"><strong>' . g_l('metadata', '[tagname]') . '</strong></td>
	<td class="defaultfont" style="width:110px;" colspan="2"><strong>' . g_l('metadata', '[type]') . '</strong></td>
</tr>
<tr id="metadataRow_' . $key . '">
	<td width="210" style="padding-right:5px;">' . we_html_tools::htmlTextInput('metadataTag[' . $key . ']', 24, $value['tag'], 255, "", "text", 205) . '</td>
	<td width="200">' . we_html_tools::htmlSelect('metadataType[' . $key . ']', $_metadata_types, 1, $value['type'], false, array('class' => "defaultfont")) . '</td>
	<td align="right" width="30">' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow(" . $_i . ")") . '</td>
</tr>
<tr id="metadataRow2_' . $key . '">
	<td style="padding-bottom:6px;padding-right:5px;">
		<div class="small">' . oldHtmlspecialchars(g_l('metadata', '[import_from]')) . '</div>' . we_html_tools::htmlTextInput('metadataImportFrom[' . $key . ']', 24, $value['importFrom'], 255, "", "text", 205) . '
	</td>
	<td colspan="2" style="padding-bottom:6px;">
		<div class="small">' . oldHtmlspecialchars(g_l('metadata', '[fields]')) . '</div>' .
					we_html_tools::htmlSelect('add_' . $key, $_metadata_fields, 1, "", false, array('class' => "defaultfont", 'style' => "width:98%", 'onchange' => "addFieldToInput(this,' . $key . ')")) . '
	</td>
</tr>
<tr id="metadataRow3_' . $key . '">
	<td style="padding-bottom:4px;padding-right:5px;">
		<div class="small">Vorschlagsliste</div>' . we_html_tools::htmlSelect('metadataProposalType_' . $key, $_proposal_types, 1, "", false, array('class' => "defaultfont", 'style' => "width:98%", 'onchange' => "togglePropositionTable(this, " . $key . ");")) . '
	</td>
	<td colspan="2" style="padding-bottom:4px;">
	</td>
</tr>
<tr id="metadataRow4_' . $key . '">
	<td colspan="3" style="padding-bottom:16px;padding-right:5px;">
		<table id="propositionTable_' . $key . '" style="width:100%;border:1px solid gray;display:none;padding-top:8px;">
			<tr>
				<td width="15%"></td>
				<td align="left" style="">' . we_html_tools::htmlTextInput('metadataProposal[' . $key . '][0]', 24, '', 255, "", "text", 310) . '</td>
				<td width="25">' . we_html_button::create_button("image:btn_function_trash", "javascript:delProposition(this)") . '</td>
			</tr>
			<tr>
				<td align="right" width="15%"></td>
				<td align="left" style="">' . we_html_button::create_button('image:btn_function_plus', 'javascript:addProposition(this, ' . $key . ')') . '</td>
				<td width="25"></td>
			</tr>
		</table>
	</td>
</tr>';
				$_i++;
			}

			$_metadataTable = '
<table border="0" cellpadding="0" cellspacing="0" width="440">
	<tbody id="metadataTable">
		' . $_adv_row . '
	</tbody>
</table>';

			$js = we_html_element::jsElement('
	function togglePropositionTable(sel, index){
		row = document.getElementById("propositionTable_" + index);
		row.style.display = sel.value === "manually" ? "block" : "none";
	}

	function addRow() {
		var tagInp = "' . addslashes(we_html_tools::htmlTextInput('metadataTag[__we_new_id__]', 24, "", 255, "", "text", 210)) . '";
		var importInp = "' . addslashes(we_html_tools::htmlTextInput('metadataImportFrom[__we_new_id__]', 24, "", 255, "", "text", 210)) . '";
		var typeSel = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('metadataType[__we_new_id__]', $_metadata_types, 1, "textfield", false, array('class' => "defaultfont")))) . '";
		var fieldSel = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('metadataType[__we_new_id__]', $_metadata_fields, 1, "", false, array('class' => "defaultfont", 'style' => "width:100%", 'onchange' => "addFieldToInput(this,__we_new_id__)")))) . '";
		var propositionTypeSel = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('metadataProposalType_[__we_new_id__]', $_proposal_types, 1, "none", false, array('class' => "defaultfont", 'style' => "width:100%", 'onchange' => 'togglePropositionTable(this, __we_new_id__)')))) . '";
		var addPropositionBtn = "' . str_replace("\n", "\\n", addslashes(we_html_button::create_button('image:btn_function_plus', 'javascript:addProposition(this, __we_new_id__)'))) . '";

		var elem = document.getElementById("metadataTable");
		newID = (elem.rows.length) / 5;
		if(elem){
			var newRow = document.createElement("TR");
				cell = document.createElement("TD");
				cell.innerHTML = "<strong>' . g_l('metadata', '[tagname]') . '</strong>";
				cell.width="210";
				cell.style.paddingTop="12px";
			newRow.appendChild(cell);
				cell = document.createElement("TD");
				cell.innerHTML = "<strong>' . g_l('metadata', '[type]') . '</strong>";
				cell.width="110";
				cell.style.paddingTop="12px";
				cell.colspan="2";
			newRow.appendChild(cell);
			elem.appendChild(newRow);

			newRow = document.createElement("TR");
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
				cell.innerHTML=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow('+newID+')") . '\';
			newRow.appendChild(cell);
			elem.appendChild(newRow);

			newRow = document.createElement("TR");
			newRow.setAttribute("id", "metadataRow2_" + newID);
				cell = document.createElement("TD");
				cell.style.paddingBottom="6px";
				cell.innerHTML=\'<div class="small">' . oldHtmlspecialchars(g_l('metadata', '[import_from]')) . '</div>\'+importInp.replace(/__we_new_id__/,newID);
			newRow.appendChild(cell);
				cell = document.createElement("TD");
				cell.setAttribute("colspan",2);
				cell.style.paddingBottom="6px";
				cell.innerHTML=\'<div class="small">' . oldHtmlspecialchars(g_l('metadata', '[fields]')) . '</div>\'+fieldSel.replace(/__we_new_id__/g,newID);
			newRow.appendChild(cell);
			elem.appendChild(newRow);

			newRow = document.createElement("TR");
			newRow.setAttribute("id", "metadataRow3_" + newID);
				cell = document.createElement("TD");
				cell.style.paddingBottom="4px";
				cell.innerHTML=\'<div class="small">Vorschlagsliste</div>\'+propositionTypeSel.replace(/__we_new_id__/g,newID);
			newRow.appendChild(cell);
				cell = document.createElement("TD");
				cell.setAttribute("colspan",2);
			newRow.appendChild(cell);
			elem.appendChild(newRow);

			newRow = document.createElement("TR");
			newRow.setAttribute("id", "metadataRow4_" + newID);
				cell = document.createElement("TD");
				cell.colSpan = "3";
				cell.style.paddingBottom = "16px";
				cell.paddingRight = "5px";
					var nestedTable = document.createElement("TABLE");
					nestedTable.setAttribute("id", "propositionTable_" + newID);
					nestedTable.style.width = "100%";
					nestedTable.style.display = "none";
					//nestedTable.style.backgroundColor = "white";
					nestedTable.style.border = "1px solid gray";
					nestedTable.style.paddingTop = "8px";
					nestedTable.appendChild(getPropositionRow(newID, 0));
						nestedRow = document.createElement("TR");
							nestedCell = document.createElement("TD");
							nestedCell.width = "15%";
						nestedRow.appendChild(nestedCell);
							nestedCell = document.createElement("TD");
							nestedCell.innerHTML = addPropositionBtn.replace(/__we_new_id__/,newID);
						nestedRow.appendChild(nestedCell);
							nestedCell = document.createElement("TD");
							nestedCell.width = "25";
						nestedRow.appendChild(nestedCell);
					nestedTable.appendChild(nestedRow);
				cell.appendChild(nestedTable);
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

	function addProposition(btn, index){
		var plusRow = btn.parentNode.parentNode;
		var newProp = getPropositionRow(index, (plusRow.parentNode.rows.length - 1));
		plusRow.parentNode.insertBefore(newProp,plusRow);
	}

	function delProposition(btn){
		var prop = btn.parentNode.parentNode;
		prop.parentNode.removeChild(prop);
	}

	function getPropositionRow(indexMeta, indexProp){
		var propositionInp = "' . addslashes(we_html_tools::htmlTextInput('metadataProposal[__we_meta_id__][__we_prop_id__]', 24, "", 255, "", "text", 310)) . '";
		var delPropositionBtn = "' . str_replace("\n", "\\n", addslashes(we_html_button::create_button('image:btn_function_trash', 'javascript:delProposition(this)'))) . '";

		var row = document.createElement("TR");
		var cell = document.createElement("TD");
		cell.width = "15%";
		row.appendChild(cell);

		cell = document.createElement("TD");
		cell.innerHTML = propositionInp.replace(/__we_meta_id__/,indexMeta).replace(/__we_prop_id__/,indexProp);
		row.appendChild(cell);

		cell = document.createElement("TD");
		cell.width = "25";
		cell.innerHTML = delPropositionBtn;
		row.appendChild(cell);

		return row;
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
	}');

			$_hint = we_html_tools::htmlAlertAttentionBox(g_l('metadata', '[fields_hint]'), we_html_tools::TYPE_ALERT, 440, false);

			$_metadata = new we_html_table(array('border' => 1, 'cellpadding' => 0, 'cellspacing' => 2, 'width' => 440, 'height' => 50), 4, 3);

			$_content = $_hint . '<div style="height:20px"></div>' . $_metadataTable . we_html_button::create_button('image:btn_function_plus', 'javascript:addRow()');

			$_contentFinal = array(
				array('headline' => '', 'html' => $_content, 'space' => 0)
			);
			// Build dialog element if user has permission
			return create_dialog('settings_predefined', g_l('metadata', '[headline]'), $_contentFinal, -1, '', '', false, $js);
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
	return we_html_element::htmlDiv(array('id' => 'metadatafields_dialog'), build_dialog('dialog')) .
		// Render save screen
		we_html_element::htmlDiv(array('id' => 'metadatafields_save', 'style' => 'display: none;'), build_dialog('save'));
}

function getMainDialog(){
	// Check if we need to save settings
	if(we_base_request::_(we_base_request::BOOL, 'save_metadatafields')){
		$save_javascript = '';
		$name=we_base_request::_(we_base_request::STRING,'metadatafields_name');
		if((strpos($name, "'") !== false || strpos($name, ',') !== false)){
			$save_javascript = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', '[metadatafields_hochkomma]'), we_message_reporting::WE_MESSAGE_ERROR) .
					'history.back()');
		} else {
			save_all_values();

			$save_javascript = we_html_element::jsElement($save_javascript .
					we_message_reporting::getShowMessageCall(g_l('metadata', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE) .
					'top.close();');
		}

		return
			$save_javascript .
			we_html_element::htmlDiv(array('class' => 'weDialogBody', 'style' => 'height:100%;width:100%'), build_dialog('saved'));
	} else {
		return
			we_html_element::htmlForm(
				array('name' => 'we_form', 'method' => 'post', 'action' => $_SERVER['REQUEST_URI']), we_html_element::htmlHidden('save_metadatafields', 'false') . render_dialog())
			. we_html_element::jsElement('init();');
	}
}

echo
we_html_element::jsScript(JS_DIR . 'keyListener.js') .
 we_html_element::jsElement('
function closeOnEscape() {
	return true;

}

function saveOnKeyBoard() {
	window.frames[1].we_save();
	return true;

}'
) . STYLESHEET .
 '</head>' .
 we_html_element::htmlBody(array('style' => 'position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;','onload'=>'self.focus();')
	, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
		, we_html_element::htmlExIFrame('we_metadatafields', getMainDialog(), 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow:auto;', 'weDialogBody') .
		we_html_element::htmlExIFrame('we_metadatafields_footer', getFooter(), 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;')
)) . '</html>';
