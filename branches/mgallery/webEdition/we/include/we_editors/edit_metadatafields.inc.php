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
	var inputs = _doc.getElementsByTagName('INPUT');
	for(var i = 0; i < inputs.length; i++){
		inputs[i].disabled = false;
	}

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
		WE().util.showMessage(_errtxt, 4, top);
		return false;
	}
	return true;
}

END_OF_SCRIPT;

	return we_html_element::jsElement($_javascript) .
		we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'height:100%;'), we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:we_save();"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:" . "top.close()"), 10, '', '', 0));
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
		we_html_multiIconBox::getHTML($name, $content, 30, '', $expand, $show_text, $hide_text, $cookie != false ? ($cookie === 'down') : $cookie, $title);
}

/**
 * This functions saves all options.
 *
 * @return         void
 */
function save_all_values(){
	//SAVE METADATA FIELDS TO DB
	if(permissionhandler::hasPerm('ADMINISTRATOR')){
		$GLOBALS['DB_WE']->query('TRUNCATE TABLE ' . METADATA_TABLE);
		$GLOBALS['DB_WE']->query('TRUNCATE TABLE ' . METAVALUES_TABLE);

		if(isset($_REQUEST['metadataTag']) && is_array($_REQUEST['metadataTag'])){
			foreach(we_base_request::_(we_base_request::STRING, 'metadataTag', '') as $key => $value){
				$GLOBALS['DB_WE']->query('INSERT INTO ' . METADATA_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'tag' => $value,
						'type' => ($type = we_base_request::_(we_base_request::STRING, 'metadataType', '', $key) ? : 'textfield'),
						'importFrom' => we_base_request::_(we_base_request::RAW, 'metadataImportFrom', '', $key),
						'mode' => we_base_request::_(we_base_request::STRING, 'metadataMode', '', $key),
						'csv' => we_base_request::_(we_base_request::INT, 'metadataCsv', '', $key),
						'closed' => we_base_request::_(we_base_request::INT, 'metadataClosed', '', $key)
				)));
			}

			foreach(we_base_request::_(we_base_request::STRING, 'metadataProposal', '') as $key => $proposals){
				foreach($proposals as $proposal){
					if($proposal){
						$GLOBALS['DB_WE']->query('INSERT INTO ' . METAVALUES_TABLE . ' SET ' . we_database_base::arraySetter(array(
								'tag' => we_base_request::_(we_base_request::STRING, 'metadataTag', '', $key),
								'value' => $proposal
						)));
					}
				}
			}
		}
	}
}

function build_dialog($selected_setting = 'ui'){
	switch($selected_setting){
		// save dialog:
		case 'save':
			$_settings = array(
				array('headline' => '', 'html' => g_l('metadata', '[save]'),)
			);
			return create_dialog('', g_l('metadata', '[save_wait]'), $_settings);
		// SAVED SUCCESSFULLY DIALOG:
		case 'saved':
			$_content = array(
				array('headline' => '', 'html' => g_l('metadata', '[saved]'),)
			);
			// Build dialog element if user has permission
			return create_dialog('', g_l('metadata', '[saved_successfully]'), $_content);
		// THUMBNAILS
		case 'dialog':
			$_headline = we_html_element::htmlDiv(array('class' => 'weDialogHeadline', 'style' => 'padding:10px 25px 5px 25px;'), g_l('metadata', '[headline]'));

			$_defined_fields = we_metadata_metaData::getDefinedMetaDataFields(we_metadata_metaData::ALL_BUT_STANDARD_FIELDS);
			$_defined_values = we_metadata_metaData::getDefinedMetaValues();

			$_metadata_types = array(
				'textfield' => 'textfield',
				'textarea' => 'textarea',
				//'wysiwyg' 	=> 'wysiwyg',
				'date' => 'date'
			);

			$_metadata_modes = array(
				'none' => g_l('metadata', '[mode_none]'),
				'manual' => g_l('metadata', '[mode_manually]'),
				'auto' => g_l('metadata', '[mode_auto]')
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

			// add standard meta field Keywords
			$standardFields = array();
			$standardFieldNames = explode(',', we_metadata_metaData::STANDARD_FIELDS);
			foreach($standardFieldNames as $name){
				$field = we_metadata_metaData::getMetaDataField($name) ? :
					array(
						'tag' => $name, 
						'type' => 'textfield',
						'importFrom' => '',
						'mode' => 'none',
						'csv' => ($name === 'Keywords' ? 1 : 0),
						'closed' => 0,
					);
				$field['tagname'] = g_l('weClass', '[' . $name . ']');
				$field['isStandard'] = true;
				$standardFields[] = $field;
			}

			$_defined_fields = array_merge($standardFields, $_defined_fields);

			//FIXME: clean this html and use we_html_element
			foreach($_defined_fields as $key => $value){
				$value['mode'] = $value['mode'] ? : 'none';

				if(empty($value['isStandard'])){
					$row0 = '<td class="defaultfont" style="width:210px;"><strong>' . g_l('metadata', '[customfield]') . '</strong></td>
<td class="defaultfont" style="width:110px;" colspan="2"><strong>' . g_l('metadata', '[type]') . '</strong></td>';
					$row1 = '<td width="210" style="padding-right:5px;">' . we_html_tools::htmlTextInput('metadataTag[' . $key . ']', 24, $value['tag'], 255, "", "text", 205) . '</td>
<td width="200">' . we_html_tools::htmlSelect('metadataType[' . $key . ']', $_metadata_types, 1, $value['type'], false, array('class' => "defaultfont", "onchange" => "toggleType(this, " . $key . ")")) . '</td>
<td style="text-align:right" width="30">' . we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(" . $_i . ")") . '</td>';
					$row2 = 	'<td style="padding-bottom:6px;padding-right:5px;">
	<div class="small">' . oldHtmlspecialchars(g_l('metadata', '[import_from]')) . '</div>' . we_html_tools::htmlTextInput('metadataImportFrom[' . $key . ']', 24, $value['importFrom'], 255, "", "text", 205) . '
</td>
<td colspan="2" style="padding-bottom:6px;">
	<div class="small">' . oldHtmlspecialchars(g_l('metadata', '[fields]')) . '</div>' .
				we_html_tools::htmlSelect('add_' . $key, $_metadata_fields, 1, "", false, array('class' => "defaultfont", 'style' => "width:98%", 'onchange' => "addFieldToInput(this,' . $key . ')")) . '
</td>';
				} else {
					$row0 = '<td class="defaultfont" style="width:210px;"><strong>' . g_l('metadata', '[standardfield]') . '</strong></td>
<td class="defaultfont" style="width:110px;" colspan="2"><strong>' . g_l('metadata', '[type]') . '</strong></td>';
					$row1 = '<td width="210" style="padding-right:5px;">' . we_html_tools::htmlTextInput('tag[' . $key . ']', 24, $value['tagname'], 255, "", "text", 205, 0, '', true) . '</td>
<td width="200">' . we_html_tools::htmlSelect('type[' . $key . ']', $_metadata_types, 1, $value['type'], false, array('class' => "defaultfont", "disabled" => "disabled")) . '</td>
<td style="text-align:right" width="30">' . we_html_element::htmlHiddens(array('metadataTag[' . $key . ']' => $value['tag'], 'metadataType[' . $key . ']' => $value['type'])) . we_html_button::create_button(we_html_button::TRASH, '', true, 0, 0, '', '', true) . '</td>';
					$row2 = '<td colspan="3" style="padding-bottom:6px;">' . we_html_element::htmlHidden('metadataImportFrom[' . $key . ']', '') . '</td>';
				}

				$_adv_row .= '
<tr id="row_' . $key . '">
	<td>
		<table style="background-color:#f5f5f5; margin-bottom:10px" id="elem_' . $key . '">
			<tr id="metadataRow0_' . $key . '">' .
				$row0 . '
			</tr>
			<tr id="metadataRow1_' . $key . '">'.
				$row1 . '
			</tr>
			<tr id="metadataRow2_' . $key . '">' .
				$row2 . '
			</tr>
			<tr id="metadataRow3_' . $key . '">
				<td style="padding-bottom:1px;padding-right:5px;">
					<div class="small" id="metadataModeDiv0_' . $key . '">' . g_l('metadata', '[proposals]') . '</div><div id="metadataModeDiv1_' . $key . '">' . we_html_tools::htmlSelect('metadataMode[' . $key . ']', $_metadata_modes, 1, $value['mode'], false, array(($value['type'] === 'textfield' ? '' : 'disabled') => ($value['type'] === 'textfield' ? '' : '1'), 'class' => "defaultfont", 'style' => "width:98%", 'onchange' => "togglePropositionTable(this, " . $key . ");")) . '</div>
				</td>
				<td colspan="2" style="padding-bottom:1px;">
					<div class="small" id="metadataProposalChecks0_' . $key . '">&nbsp;</div>
					<div id="metadataProposalChecks1_' . $key . '">' . we_html_forms::checkboxWithHidden($value['csv'], 'metadataCsv[' . $key . ']', 'CSV') . we_html_forms::checkboxWithHidden($value['closed'], 'metadataClosed[' . $key . ']', g_l('metadata', '[closedList]')) . '</div>
				</td>
			</tr>
			<tr id="metadataRow4_' . $key . '">
				<td colspan="3" style="padding-bottom:16px;padding-right:5px;">
					<table id="proposalTable_' . $key . '" style="width:100%;border:1px solid gray;display:' . ($value['mode'] === 'none' ? 'none' : 'block') . ';padding-top:8px;">';
							if(isset($_defined_values[$value['tag']])){
								$i = 0;
								foreach($_defined_values[$value['tag']] as $proposal){
									$_adv_row .= '<tr>
									<td width="15%"></td>
									<td style="text-align:left">' . we_html_tools::htmlTextInput('metadataProposal[' . $key . '][' . $i++ . ']', 24, $proposal, 255, ($value['mode'] === 'auto' ? 'disabled="1"' : ''), "text", 310) . '</td>
									<td width="25">' . we_html_button::create_button(we_html_button::TRASH, "javascript:delProposition(this)") . '</td>
								</tr>';
								}
							} else {
								$_adv_row .= '<tr>
								<td width="15%"></td>
								<td style="text-align:left">' . we_html_tools::htmlTextInput('metadataProposal[' . $key . '][0]', 24, '', 255, ($value['mode'] === 'auto' ? 'disabled="1"' : ''), "text", 310) . '</td>
								<td width="25">' . we_html_button::create_button(we_html_button::TRASH, "javascript:delProposition(this)") . '</td>
							</tr>';
							}
							$_adv_row .= '<tr>
							<td style="text-align:right" width="15%"></td>
							<td style="text-align:left">' . we_html_button::create_button(we_html_button::PLUS, 'javascript:addProposition(this, ' . $key . ')') . '</td>
							<td width="25"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>';
				$_i++;
			}

			$_metadataTable = '
<table class="default" width="440">
	<tbody id="metadataTable">' .
		$_adv_row . '
	</tbody>
</table>';

			$js = we_html_element::jsElement('
var g_l={
	tagname:"' . g_l('metadata', '[tagname]') . '",
	type:"' . g_l('metadata', '[type]') . '",
	import_from:"' . oldHtmlspecialchars(g_l('metadata', '[import_from]')) . '",
	fields:"' . oldHtmlspecialchars(g_l('metadata', '[fields]')) . '",
	proposals:"' . oldHtmlspecialchars('Vorschlagsliste') . '",
};
var phpdata={
	tagInp:"' . addslashes(we_html_tools::htmlTextInput('metadataTag[__we_new_id__]', 24, "", 255, "", "text", 210)) . '",
	importInp:"' . addslashes(we_html_tools::htmlTextInput('metadataImportFrom[__we_new_id__]', 24, "", 255, "", "text", 210)) . '",
	typeSel:"' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('metadataType[__we_new_id__]', $_metadata_types, 1, 'textfield', false, array('class' => 'defaultfont', 'onchange' => 'toggleType(this, __we_new_id__)')))) . '",
	fieldSel:"' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('metadataType[__we_new_id__]', $_metadata_fields, 1, '', false, array('class' => 'defaultfont', 'style' => 'width:100%', 'onchange' => 'addFieldToInput(this,__we_new_id__)')))) . '",
	modeSel:"' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('metadataMode[__we_new_id__]', $_metadata_modes, 1, 'none', false, array('class' => "defaultfont", 'style' => 'width:100%', 'onchange' => 'togglePropositionTable(this, __we_new_id__)')))) . '",
	csvCheck:"' . str_replace("\n", "\\n", addslashes(we_html_forms::checkboxWithHidden(0, 'metadataCsv[__we_new_id__]', 'CSV'))) . '",
	closedCheck:"' . str_replace("\n", "\\n", addslashes(we_html_forms::checkboxWithHidden(0, 'metadataClosed[__we_new_id__]', 'abgeschlossen'))) . '",
	addPropositionBtn:"' . str_replace("\n", "\\n", addslashes(we_html_button::create_button(we_html_button::PLUS, 'javascript:addProposition(this, __we_new_id__)'))) . '",
	trashButton:\'' . we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(__we_new_id__)") . '\',
	proposalInp:"' . addslashes(we_html_tools::htmlTextInput('metadataProposal[__we_meta_id__][__we_prop_id__]', 24, "", 255, "", "text", 310)) . '",
	delPropositionBtn:"' . str_replace("\n", "\\n", addslashes(we_html_button::create_button(we_html_button::TRASH, 'javascript:delProposition(this)'))) . '"
		
};') .
				we_html_element::jsScript(JS_DIR . 'edit_metadatafields.js');

			$_hint = we_html_tools::htmlAlertAttentionBox(g_l('metadata', '[fields_hint]'), we_html_tools::TYPE_ALERT, 440, false);

			$_metadata = new we_html_table(array('style' => 'border:1px solid black', 'width' => 440, 'height' => 50), 4, 3);

			$_content = $_hint . '<div style="height:20px"></div>' . $_metadataTable . we_html_button::create_button(we_html_button::PLUS, 'javascript:addRow()');

			$_contentFinal = array(
				array('headline' => '', 'html' => $_content,)
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
		$name = we_base_request::_(we_base_request::STRING, 'metadatafields_name');
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
			we_html_element::htmlForm(array('name' => 'we_form', 'method' => 'post', 'action' => $_SERVER['REQUEST_URI']), we_html_element::htmlHidden('save_metadatafields', 'false') . render_dialog())
			. we_html_element::jsElement('init();');
	}
}

echo
we_html_element::jsElement('
function closeOnEscape() {
	return true;
}

function saveOnKeyBoard() {
	window.we_save();
	return true;

}'
) . STYLESHEET .
 '</head>' .
 we_html_element::htmlBody(array('style' => 'position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;', 'onload' => 'self.focus();')
	, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
		, we_html_element::htmlExIFrame('we_metadatafields', getMainDialog(), 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow:auto;', 'weDialogBody') .
		we_html_element::htmlExIFrame('we_metadatafields_footer', getFooter(), 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;')
)) . '</html>';
