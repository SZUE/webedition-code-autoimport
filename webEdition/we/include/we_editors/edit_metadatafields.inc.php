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
	if(permissionhandler::hasPerm('EDIT_METADATAFIELD')){
		$GLOBALS['DB_WE']->query('TRUNCATE TABLE ' . METADATA_TABLE);
		$GLOBALS['DB_WE']->query('TRUNCATE TABLE ' . METAVALUES_TABLE);

		if(isset($_REQUEST['metadataTag']) && is_array($_REQUEST['metadataTag'])){
			foreach(we_base_request::_(we_base_request::STRING, 'metadataTag', '') as $key => $value){
				$GLOBALS['DB_WE']->query('INSERT INTO ' . METADATA_TABLE . ' SET ' . we_database_base::arraySetter(['tag' => $value,
						'type' => ($type = we_base_request::_(we_base_request::STRING, 'metadataType', '', $key) ? : 'textfield'),
						'importFrom' => we_base_request::_(we_base_request::RAW, 'metadataImportFrom', '', $key),
						'mode' => we_base_request::_(we_base_request::STRING, 'metadataMode', '', $key),
						'csv' => we_base_request::_(we_base_request::INT, 'metadataCsv', '', $key),
						'closed' => we_base_request::_(we_base_request::INT, 'metadataClosed', '', $key)
						]));
			}

			foreach(we_base_request::_(we_base_request::STRING, 'metadataProposal', '') as $key => $proposals){
				foreach($proposals as $proposal){
					if($proposal){
						$GLOBALS['DB_WE']->query('INSERT INTO ' . METAVALUES_TABLE . ' SET ' . we_database_base::arraySetter(['tag' => we_base_request::_(we_base_request::STRING, 'metadataTag', '', $key),
								'value' => $proposal
								]));
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
			$settings = [
				['headline' => '', 'html' => g_l('metadata', '[save]'),]
			];
			return create_dialog('', g_l('metadata', '[save_wait]'), $settings);
		// SAVED SUCCESSFULLY DIALOG:
		case 'saved':
			$content = [
				['headline' => '', 'html' => g_l('metadata', '[saved]'),]
			];
			// Build dialog element if user has permission
			return create_dialog('', g_l('metadata', '[saved_successfully]'), $content);
		// THUMBNAILS
		case 'dialog':
			$headline = we_html_element::htmlDiv(['class' => 'weDialogHeadline'], g_l('metadata', '[headline]'));

			$defined_fields = we_metadata_metaData::getDefinedMetaDataFields(we_metadata_metaData::ALL_BUT_STANDARD_FIELDS);
			$defined_values = we_metadata_metaData::getDefinedMetaValues();

			$metadata_types = [
				'textfield' => 'textfield',
				'textarea' => 'textarea',
				//'wysiwyg' 	=> 'wysiwyg',
				'date' => 'date'
			];

			$metadata_modes = [
				'none' => g_l('metadata', '[mode_none]'),
				'manual' => g_l('metadata', '[mode_manually]'),
				'auto' => g_l('metadata', '[mode_auto]')
			];

			$metadata_fields = ['' => '-- ' . g_l('metadata', '[add]') . ' --', 'Exif' => we_html_tools::OPTGROUP];
			$tmp = we_metadata_Exif::getUsedFields();
			foreach($tmp as $key){
				$metadata_fields[$key] = $key;
			}
			$tmp = we_metadata_IPTC::getUsedFields();
			$metadata_fields['IPTC'] = we_html_tools::OPTGROUP;
			foreach($tmp as $key){
				$metadata_fields[$key] = $key;
			}

			$i = 0;
			$adv_row = '';

			// add standard meta field Keywords
			$standardFields = [];
			$standardFieldNames = explode(',', we_metadata_metaData::STANDARD_FIELDS);
			foreach($standardFieldNames as $name){
				$field = we_metadata_metaData::getMetaDataField($name) ? :
					['tag' => $name,
					'type' => 'textfield',
					'importFrom' => '',
					'mode' => 'none',
					'csv' => ($name === 'Keywords' ? 1 : 0),
					'closed' => 0,
				];
				$field['tagname'] = g_l('weClass', '[' . $name . ']');
				$field['isStandard'] = true;
				$standardFields[] = $field;
			}

			$defined_fields = array_merge($standardFields, $defined_fields);

			//FIXME: clean this html and use we_html_element
			foreach($defined_fields as $key => $value){
				$value['mode'] = $value['mode'] ? : 'none';

				if(empty($value['isStandard'])){
					$row0 = '<td class="defaultfont" style="width:210px;"><strong>' . g_l('metadata', '[customfield]') . '</strong></td>
<td class="defaultfont" style="width:110px;" colspan="2"><strong>' . g_l('metadata', '[type]') . '</strong></td>';
					$row1 = '<td style="width:210px;padding-right:5px;">' . we_html_tools::htmlTextInput('metadataTag[' . $key . ']', 24, $value['tag'], 255, "", "text", 205) . '</td>
<td style="width:200px;">' . we_html_tools::htmlSelect('metadataType[' . $key . ']', $metadata_types, 1, $value['type'], false, ['class' => "defaultfont", "onchange" => "toggleType(this, " . $key . ")"]) . '</td>
<td style="text-align:right;width:30px;">' . we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(" . $i . ")") . '</td>';
					$row2 = '<td style="padding-bottom:6px;padding-right:5px;">
	<div class="small">' . oldHtmlspecialchars(g_l('metadata', '[import_from]')) . '</div>' . we_html_tools::htmlTextInput('metadataImportFrom[' . $key . ']', 24, $value['importFrom'], 255, "", "text", 205) . '
</td>
<td colspan="2" style="padding-bottom:6px;">
	<div class="small">' . oldHtmlspecialchars(g_l('metadata', '[fields]')) . '</div>' .
						we_html_tools::htmlSelect('add_' . $key, $metadata_fields, 1, "", false, ['class' => "defaultfont", 'style' => "width:98%", 'onchange' => "addFieldToInput(this,' . $key . ')"]) . '
</td>';
				} else {
					$row0 = '<td class="defaultfont" style="width:210px;"><strong>' . g_l('metadata', '[standardfield]') . '</strong></td>
<td class="defaultfont" style="width:110px;" colspan="2"><strong>' . g_l('metadata', '[type]') . '</strong></td>';
					$row1 = '<td style="width:210px;padding-right:5px;">' . we_html_tools::htmlTextInput('tag[' . $key . ']', 24, $value['tagname'], 255, "", "text", 205, 0, '', true) . '</td>
<td style="width:200px;">' . we_html_tools::htmlSelect('type[' . $key . ']', $metadata_types, 1, $value['type'], false, ['class' => "defaultfont", "disabled" => "disabled"]) . '</td>
<td style="text-align:right;width:30px;">' . we_html_element::htmlHiddens(['metadataTag[' . $key . ']' => $value['tag'], 'metadataType[' . $key . ']' => $value['type']]) . we_html_button::create_button(we_html_button::TRASH, '', '', 0, 0, '', '', true) . '</td>';
					$row2 = '<td colspan="3" style="padding-bottom:6px;">' . we_html_element::htmlHidden('metadataImportFrom[' . $key . ']', '') . '</td>';
				}

				$adv_row .= '
<tr id="row_' . $key . '">
	<td>
		<table style="background-color:#f5f5f5; margin-bottom:10px" id="elem_' . $key . '">
			<tr id="metadataRow0_' . $key . '">' .
					$row0 . '
			</tr>
			<tr id="metadataRow1_' . $key . '">' .
					$row1 . '
			</tr>
			<tr id="metadataRow2_' . $key . '">' .
					$row2 . '
			</tr>
			<tr id="metadataRow3_' . $key . '">
				<td style="padding-bottom:1px;padding-right:5px;">
					<div class="small" id="metadataModeDiv0_' . $key . '">' . g_l('metadata', '[proposals]') . '</div><div id="metadataModeDiv1_' . $key . '">' . we_html_tools::htmlSelect('metadataMode[' . $key . ']', $metadata_modes, 1, $value['mode'], false, [
						($value['type'] === 'textfield' ? '' : 'disabled') => ($value['type'] === 'textfield' ? '' : '1'), 'class' => "defaultfont", 'style' => "width:98%", 'onchange' => "togglePropositionTable(this, " . $key . ");"]) . '</div>
				</td>
				<td colspan="2" style="padding-bottom:1px;">
					<div class="small" id="metadataProposalChecks0_' . $key . '">&nbsp;</div>
					<div id="metadataProposalChecks1_' . $key . '">' .
					we_html_forms::checkboxWithHidden($value['csv'], 'metadataCsv[' . $key . ']', 'CSV', false, 'defaultfont', '', false, '', we_html_tools::TYPE_NONE, 0, '', '', $title = g_l('metadata', '[csv_desc]')) .
					we_html_forms::checkboxWithHidden($value['closed'], 'metadataClosed[' . $key . ']', g_l('metadata', '[closedList]'), false, 'defaultfont', '', false, '', we_html_tools::TYPE_NONE, 0, '', '', $title = g_l('metadata', '[closedList_desc]')) .
					'</div>
				</td>
			</tr>
			<tr id="metadataRow4_' . $key . '">
				<td colspan="3" style="padding-bottom:16px;padding-right:5px;">
					<table id="proposalTable_' . $key . '" style="width:100%;border:1px solid gray;display:' . ($value['mode'] === 'none' ? 'none' : 'block') . ';padding-top:8px;">';
				if(isset($defined_values[$value['tag']])){
					$i = 0;
					foreach($defined_values[$value['tag']] as $proposal){
						$adv_row .= '<tr>
									<td style="width:15%"></td>
									<td style="text-align:left">' . we_html_tools::htmlTextInput('metadataProposal[' . $key . '][' . $i++ . ']', 24, $proposal, 255, ($value['mode'] === 'auto' ? 'disabled="1"' : ''), "text", 310) . '</td>
									<td style="width:25px;">' . we_html_button::create_button(we_html_button::TRASH, "javascript:delProposition(this)") . '</td>
								</tr>';
					}
				} else {
					$adv_row .= '<tr>
								<td style="width:15%"></td>
								<td style="text-align:left">' . we_html_tools::htmlTextInput('metadataProposal[' . $key . '][0]', 24, '', 255, ($value['mode'] === 'auto' ? 'disabled="1"' : ''), "text", 310) . '</td>
								<td style="width:25px;">' . we_html_button::create_button(we_html_button::TRASH, "javascript:delProposition(this)") . '</td>
							</tr>';
				}
				$adv_row .= '<tr>
							<td style="text-align:right;width:15%"></td>
							<td style="text-align:left">' . we_html_button::create_button(we_html_button::PLUS, 'javascript:addProposition(this, ' . $key . ')') . '</td>
							<td style="width:25px;"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>';
				$i++;
			}

			$metadataTable = '
<table class="default" style="width:440px;">
	<tbody id="metadataTable">' .
				$adv_row . '
	</tbody>
</table>';

			$js = we_html_element::jsScript(JS_DIR . 'edit_metadatafields.js', '', ['id' => 'loadVarEdit_metadatafields', 'data-phpdata' => setDynamicVar([
						'tagInp' => we_html_tools::htmlTextInput('metadataTag[__we_new_id__]', 24, "", 255, "", "text", 210),
						'importInp' => we_html_tools::htmlTextInput('metadataImportFrom[__we_new_id__]', 24, "", 255, "", "text", 210),
						'typeSel' => we_html_tools::htmlSelect('metadataType[__we_new_id__]', $metadata_types, 1, 'textfield', false, ['class' => 'defaultfont', 'onchange' => 'toggleType(this, __we_new_id__)']),
						'fieldSel' => we_html_tools::htmlSelect('metadataType[__we_new_id__]', $metadata_fields, 1, '', false, ['class' => 'defaultfont', 'style' => 'width:100%', 'onchange' => 'addFieldToInput(this,__we_new_id__)']),
						'modeSel' => we_html_tools::htmlSelect('metadataMode[__we_new_id__]', $metadata_modes, 1, 'none', false, ['class' => "defaultfont", 'style' => 'width:100%', 'onchange' => 'togglePropositionTable(this, __we_new_id__)']),
						'csvCheck' => we_html_forms::checkboxWithHidden(0, 'metadataCsv[__we_new_id__]', 'CSV'),
						'closedCheck' => we_html_forms::checkboxWithHidden(0, 'metadataClosed[__we_new_id__]', g_l('export', '[finish_progress]')),
						'addPropositionBtn' => we_html_button::create_button(we_html_button::PLUS, 'javascript:addProposition(this, __we_new_id__)'),
						'trashButton' => we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(__we_new_id__)"),
						'proposalInp' => we_html_tools::htmlTextInput('metadataProposal[__we_meta_id__][__we_prop_id__]', 24, "", 255, "", "text", 310),
						'delPropositionBtn' => we_html_button::create_button(we_html_button::TRASH, 'javascript:delProposition(this)'),
			])]);

			$hint = we_html_tools::htmlAlertAttentionBox(g_l('metadata', '[fields_hint]'), we_html_tools::TYPE_INFO, 440, false, 50);
			$hint2 = we_html_tools::htmlAlertAttentionBox(g_l('metadata', '[proposals_hint]'), we_html_tools::TYPE_INFO, 440, false);

			$content = $hint . '<div>&nbsp;</div>' . $hint2 . '<div style="height:20px"></div>' . $metadataTable . we_html_button::create_button(we_html_button::PLUS, 'javascript:addRow()');

			$contentFinal = [
				['headline' => '', 'html' => $content,]
			];
			// Build dialog element if user has permission
			return create_dialog('settings_predefined', g_l('metadata', '[headline]'), $contentFinal, -1, '', '', false, $js);
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
	return we_html_element::htmlDiv(['id' => 'metadatafields_dialog'], build_dialog('dialog')) .
		// Render save screen
		we_html_element::htmlDiv(['id' => 'metadatafields_save', 'style' => 'display: none;'], build_dialog('save'));
}

function getMainDialog(){
	// Check if we need to save settings
	if(we_base_request::_(we_base_request::BOOL, 'save_metadatafields')){
		$save_javascript = new we_base_jsCmd();
		$name = we_base_request::_(we_base_request::STRING, 'metadatafields_name');
		if((strpos($name, "'") !== false || strpos($name, ',') !== false)){
			$save_javascript->addCmd('msg', ['msg' => g_l('alert', '[metadatafields_hochkomma]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
			$save_javascript->addCmd('history.back');
		} else {
			save_all_values();

			$save_javascript->addCmd('msg', ['msg' => g_l('metadata', '[saved]'), 'prio' => we_message_reporting::WE_MESSAGE_NOTICE]);
			$save_javascript->addCmd('close');
		}

		return
			$save_javascript->getCmds() .
			we_html_element::htmlDiv(['class' => 'weDialogBody', 'style' => 'height:100%;width:100%'], build_dialog('saved'));
	}
	return
		we_html_element::htmlForm(['name' => 'we_form', 'method' => 'post', 'action' => $_SERVER['REQUEST_URI']], we_html_element::htmlHidden('save_metadatafields', 'false') . render_dialog());
}

echo
 '</head>' .
 we_html_element::htmlBody(['class' => 'weDialogBody', 'onload' => 'self.focus();']
	, we_html_element::htmlExIFrame('we_metadatafields', getMainDialog(), 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow:auto;', 'weDialogBody') .
	we_html_element::htmlDiv(['class' => 'weDialogButtonsBody', 'style' => 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;'], we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:we_save();"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:" . "top.close()"), 10, '', '', 0))
) . '</html>';
