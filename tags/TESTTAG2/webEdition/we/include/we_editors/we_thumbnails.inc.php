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
echo we_html_tools::getHtmlTop(g_l('thumbnails', '[thumbnails]'));

$reloadUrl = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=editThumbs';

// Check if we need to create a new thumbnail
if(($name = we_base_request::_(we_base_request::STRING, 'newthumbnail')) &&
	permissionhandler::hasPerm('ADMINISTRATOR')){
	$DB_WE->query('INSERT INTO ' . THUMBNAILS_TABLE . ' SET Name="' . $DB_WE->escape($name) . '"');
	$GLOBALS['id'] = $DB_WE->getInsertId();
} else {
	$GLOBALS['id'] = we_base_request::_(we_base_request::INT, 'id', 0);
}

// Check if we need to delete a thumbnail
if(($delId = we_base_request::_(we_base_request::INT, 'deletethumbnail'))){
	if(permissionhandler::hasPerm('ADMINISTRATOR')){
		// Delete thumbnails in filesystem
		we_thumbnail::deleteByThumbID($delId);

		// Delete entry in database
		$DB_WE->query('DELETE FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . $delId);
	}
}

// Check which thumbnail to work with
if(!$id){
	$tmpid = f('SELECT ID FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name LIMIT 1');

	$id = $tmpid ? : -1;
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
	return
		// Check, if we need to write some JavaScripts
		($JS ? : '') .
		($expand != -1 ? we_html_multiIconBox::getJS() : '') .
		// Return HTML code of dialog
		we_html_multiIconBox::getHTML($name, '100%', $content, 30, '', $expand, $show_text, $hide_text, $cookie != false ? ($cookie === 'down') : $cookie, $title);
}

/**
 * This functions saves an option in the current session.
 *
 * @param          string                                  $settingvalue
 * @param          string                                  $settingname
 *
 * @see            save_all_values
 *
 * @return         bool
 */
function remember_value(array &$setArray, $settingvalue, $settingname){
	if(isset($settingvalue) && ($settingvalue != null)){
		switch($settingname){
			case null:
				break;
			case 'Name':
				$setArray[$settingname] = $settingvalue;
				break;
			case 'Format':
				$setArray[$settingname] = (($settingvalue === 'none') ? '' : $settingvalue);
				break;
			default:
				$setArray[$settingname] = abs($settingvalue);
		}
	} else {
		switch($settingname){
			case 'Format':
				$setArray[$settingname] = 'jpg';
				break;
			default:
				$setArray[$settingname] = 0;
				break;
		}
	}
}

/**
 * This functions saves all options.
 *
 * @see            remember_value()
 *
 * @return         void
 */
function save_all_values(){
	global $DB_WE;

	if(permissionhandler::hasPerm('ADMINISTRATOR')){
		$setArray = array('Date' => sql_function('UNIX_TIMESTAMP()'));
		// Update settings
		remember_value($setArray, we_base_request::_(we_base_request::STRING, 'thumbnail_name', null), 'Name');
		remember_value($setArray, we_base_request::_(we_base_request::INT, 'thumbnail_width', null), 'Width');
		remember_value($setArray, we_base_request::_(we_base_request::INT, 'thumbnail_height', null), 'Height');
		remember_value($setArray, we_base_request::_(we_base_request::INT, 'thumbnail_quality', null), 'Quality');
		remember_value($setArray, we_base_request::_(we_base_request::BOOL, 'Ratio', null), 'Ratio');
		remember_value($setArray, we_base_request::_(we_base_request::BOOL, 'Maxsize', null), 'Maxsize');
		remember_value($setArray, we_base_request::_(we_base_request::BOOL, 'Interlace', null), 'Interlace');
		remember_value($setArray, we_base_request::_(we_base_request::BOOL, 'Fitinside', null), 'Fitinside');
		remember_value($setArray, we_base_request::_(we_base_request::STRING, 'Format', null), 'Format');

		$DB_WE->query('UPDATE ' . THUMBNAILS_TABLE . ' SET ' . we_database_base::arraySetter($setArray) . ' WHERE ID=' . we_base_request::_(we_base_request::INT, 'edited_id', 0));
	}
}

function build_dialog($selected_setting = 'ui'){
	$DB_WE = $GLOBALS['DB_WE'];
	$id = $GLOBALS['id'];

	switch($selected_setting){
		case 'save':
			//SAVE DIALOG

			$_settings = array(
				array('headline' => '', 'html' => g_l('thumbnails', '[save]'), 'space' => 0)
			);
			return create_dialog('', g_l('thumbnails', '[save_wait]'), $_settings);

		case 'saved':
			// SAVED SUCCESSFULLY DIALOG

			$_thumbs = array(
				array('headline' => '', 'html' => g_l('thumbnails', '[saved]'), 'space' => 0)
			);

			return create_dialog('', g_l('thumbnails', '[saved_successfully]'), $_thumbs);

		case 'dialog':
			//THUMBNAILS

			$_thumbs = array();

			// Generate needed JS
			$_needed_JavaScript_Source = '
function in_array(haystack, needle) {
	for (var i = 0; i < haystack.length; i++) {
		if (haystack[i] == needle) {
			return true;
		}
	}

	return false;
}

function add_thumbnail() {';

			// Detect thumbnail names
			$_thumbnail_names = '';

			$DB_WE->query('SELECT Name FROM ' . THUMBNAILS_TABLE);

			while($DB_WE->next_record()){
				$_thumbnail_names .= "'" . str_replace("'", "\'", $DB_WE->f("Name")) . "',";
			}

			$_thumbnail_names = rtrim($_thumbnail_names, ',');

			$_needed_JavaScript_Source .= "
	var thumbnail_names = new Array(" . $_thumbnail_names . ");
	var name = prompt('" . g_l('thumbnails', '[new]') . "', '');

	if (name != null) {
		if((name.indexOf('<') != -1) || (name.indexOf('>') != -1)) {
			" . we_message_reporting::getShowMessageCall(g_l('alert', '[name_nok]'), we_message_reporting::WE_MESSAGE_ERROR) . "
			return;
		}

		if (name.indexOf(\"'\") != -1 || name.indexOf(\",\") != -1) {
			" . we_message_reporting::getShowMessageCall(g_l('alert', '[thumbnail_hochkomma]'), we_message_reporting::WE_MESSAGE_ERROR) . "
		} else if (name == '') {
			" . we_message_reporting::getShowMessageCall(g_l('alert', '[thumbnail_empty]'), we_message_reporting::WE_MESSAGE_ERROR) . "
		} else if (in_array(thumbnail_names, name)) {
			" . we_message_reporting::getShowMessageCall(g_l('alert', '[thumbnail_exists]'), we_message_reporting::WE_MESSAGE_ERROR) . "
		} else {
			self.location = '" . $GLOBALS['reloadUrl'] . "&newthumbnail=' +encodeURI(name);
		}
	}
}

function delete_thumbnail() {" .
				((permissionhandler::hasPerm('ADMINISTRATOR')) ?
					"var deletion = confirm('" . sprintf(g_l('thumbnails', '[delete_prompt]'), f('SELECT Name FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . intval($id))) . "');

		if (deletion == true) {
			self.location = '" . $GLOBALS['reloadUrl'] . "&deletethumbnail=" . $id . "';
		}" :
					"") . "
}

function change_thumbnail() {
	var url = '" . $GLOBALS['reloadUrl'] . "&id=' + arguments[0];
	self.location = url;
}

function changeFormat() {
	if(document.getElementById('Format').value == 'jpg' || document.getElementById('Format').value == 'none') {
		document.getElementById('thumbnail_quality_text_cell').style.display='';
		document.getElementById('thumbnail_quality_value_cell').style.display='';
	} else {
		document.getElementById('thumbnail_quality_text_cell').style.display='none';
		document.getElementById('thumbnail_quality_value_cell').style.display='none';
	}
}

function init() {
	changeFormat();
}";

			$_needed_JavaScript = we_html_element::jsElement($_needed_JavaScript_Source) .
				we_html_element::jsScript(JS_DIR . 'keyListener.js');

			$_enabled_buttons = false;

			// Build language select box
			$_thumbnails = new we_html_select(array('name' => 'Thumbnails', 'class' => 'weSelect', 'size' => 10, 'style' => 'width: 314px;', 'onchange' => "if(this.selectedIndex > -1){change_thumbnail(this.options[this.selectedIndex].value);}"));

			$DB_WE->query('SELECT ID, Name FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');

			$_thumbnail_counter_firsttime = true;
			while($DB_WE->next_record()){
				$_enabled_buttons = true;

				$_thumbnails->addOption($DB_WE->f('ID'), $DB_WE->f('Name'));

				if($_thumbnail_counter_firsttime && !$id){
					$id = $DB_WE->f('ID');

					$_thumbnails->selectOption($DB_WE->f('ID'));
				} else if($id == $DB_WE->f('ID')){
					$_thumbnails->selectOption($DB_WE->f('ID'));
				}

				$_thumbnail_counter_firsttime = false;
			}

			// Create thumbnails list
			$_thumbnails_table = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 2, 3);

			$_thumbnails_table->setCol(0, 0, null, we_html_element::htmlHidden(array('name' => 'edited_id', 'value' => $id)) . $_thumbnails->getHtml());
			$_thumbnails_table->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
			$_thumbnails_table->setCol(0, 2, array('valign' => 'top'), we_html_button::create_button('add', 'javascript:add_thumbnail();') . we_html_tools::getPixel(1, 10) . we_html_button::create_button('delete', 'javascript:delete_thumbnail();', true, 100, 22, '', '', !$_enabled_buttons, false));

			// Build dialog
			$_thumbs[] = array('headline' => '', 'html' => $_thumbnails_table->getHtml(), 'space' => 0);

			$allData = getHash('SELECT Name,Width,Height,Quality,Ratio,Maxsize,Interlace,Fitinside,Format FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . $id);
			if(!$allData){
				$allData = array('Name' => '', 'Width' => '', 'Height' => '', 'Quality' => '', 'Ratio' => '', 'Maxsize' => '', 'Interlace' => '', 'Fitinside' => '', 'Format' => '');
			} 

			$_thumbnail_name = ($id != -1) ? $allData['Name'] : -1;

			$_thumbnail_name_input = we_html_tools::htmlTextInput('thumbnail_name', 22, ($_thumbnail_name != -1 ? $_thumbnail_name : ''), 255, ($_thumbnail_name == -1 ? 'disabled="true"' : ''), 'text', 225);

			// Build dialog
			$_thumbs[] = array('headline' => g_l('thumbnails', '[name]'), 'html' => $_thumbnail_name_input, 'space' => 200);

			/*			 * ***************************************************************
			 * PROPERTIES
			 * *************************************************************** */

			// Create specify thumbnail dimension input
			$_thumbnail_width = ($id != -1) ? $allData['Width'] : -1;
			$_thumbnail_height = ($id != -1) ? $allData['Height'] : -1;
			$_thumbnail_quality = ($id != -1) ? $allData['Quality'] : -1;

			$_thumbnail_specify_table = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 5, 3);

			$_thumbnail_specify_table->setCol(1, 0, array('width' => 60), we_html_tools::getPixel(1, 5));
			$_thumbnail_specify_table->setCol(3, 0, array('colspan' => 3), we_html_tools::getPixel(1, 5));

			$_thumbnail_specify_table->setCol(0, 0, array('class' => 'defaultfont'), g_l('thumbnails', '[width]') . ':');
			$_thumbnail_specify_table->setCol(2, 0, array('class' => 'defaultfont'), g_l('thumbnails', '[height]') . ':');
			$_thumbnail_specify_table->setCol(4, 0, array('class' => 'defaultfont', 'id' => 'thumbnail_quality_text_cell'), g_l('thumbnails', '[quality]') . ':');

			$_thumbnail_specify_table->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
			$_thumbnail_specify_table->setCol(2, 1, null, we_html_tools::getPixel(10, 1));
			$_thumbnail_specify_table->setCol(4, 1, null, we_html_tools::getPixel(10, 22));

			$_thumbnail_specify_table->setCol(0, 2, null, we_html_tools::htmlTextInput('thumbnail_width', 6, ($_thumbnail_width != -1 ? $_thumbnail_width : ''), 4, ($_thumbnail_width == -1 ? 'disabled="disabled"' : ''), 'text', 60));
			$_thumbnail_specify_table->setCol(2, 2, null, we_html_tools::htmlTextInput('thumbnail_height', 6, ($_thumbnail_height != -1 ? $_thumbnail_height : ''), 4, ($_thumbnail_height == -1 ? 'disabled="disabled"' : ''), 'text', 60));
			$_thumbnail_specify_table->setCol(4, 2, array('class' => 'defaultfont', 'id' => 'thumbnail_quality_value_cell'), we_base_imageEdit::qualitySelect('thumbnail_quality', $_thumbnail_quality));

			// Create checkboxes for options for thumbnails
			$_thumbnail_ratio = ($id != -1) ? $allData['Ratio'] : -1;
			$_thumbnail_maximize = ($id != -1) ? $allData['Maxsize'] : -1;
			$_thumbnail_interlace = ($id != -1) ? $allData['Interlace'] : -1;
			$_thumbnail_fitinside = ($id != -1) ? $allData['Fitinside'] : -1;

			$_thumbnail_option_table = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 7, 1);

			$_thumbnail_option_table->setCol(0, 0, null, we_html_forms::checkbox(1, (($_thumbnail_ratio == -1 || $_thumbnail_ratio == 0) ? false : true), 'Ratio', g_l('thumbnails', '[ratio]'), false, 'defaultfont', '', ($_thumbnail_ratio == -1)));
			$_thumbnail_option_table->setCol(1, 0, null, we_html_tools::getPixel(1, 5));
			$_thumbnail_option_table->setCol(2, 0, null, we_html_forms::checkbox(1, (($_thumbnail_maximize == -1 || $_thumbnail_maximize == 0) ? false : true), 'Maxsize', g_l('thumbnails', '[maximize]'), false, 'defaultfont', '', ($_thumbnail_maximize == -1)));
			$_thumbnail_option_table->setCol(3, 0, null, we_html_tools::getPixel(1, 5));
			$_thumbnail_option_table->setCol(4, 0, null, we_html_forms::checkbox(1, (($_thumbnail_interlace == -1 || $_thumbnail_interlace == 0) ? false : true), 'Interlace', g_l('thumbnails', '[interlace]'), false, 'defaultfont', '', ($_thumbnail_interlace == -1)));
			$_thumbnail_option_table->setCol(5, 0, null, we_html_tools::getPixel(1, 5));
			$_thumbnail_option_table->setCol(6, 0, null, we_html_forms::checkbox(1, (($_thumbnail_fitinside == -1 || $_thumbnail_fitinside == 0) ? false : true), 'Fitinside', 'Fit inside', false, 'defaultfont', '', ($_thumbnail_fitinside == -1)));

			// Build final HTML code
			$_window_html = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 3, 1);
			$_window_html->setCol(0, 0, null, $_thumbnail_specify_table->getHtml());
			$_window_html->setCol(1, 0, null, we_html_tools::getPixel(1, 10));
			$_window_html->setCol(2, 0, null, $_thumbnail_option_table->getHtml());

			// Build dialog
			$_thumbs[] = array('headline' => g_l('thumbnails', '[properties]'), 'html' => $_window_html->getHtml(), 'space' => 200);

			// OUTPUT FORMAT

			$_thumbnail_format = ($id != -1) ? $allData['Format'] : -1;

			// Define available formats
			$_thumbnails_formats = array('none' => g_l('thumbnails', '[format_original]'), 'gif' => g_l('thumbnails', '[format_gif]'), 'jpg' => g_l('thumbnails', '[format_jpg]'), 'png' => g_l('thumbnails', '[format_png]'));

			$_thumbnail_format_select_attribs = array('name' => 'Format', 'id' => 'Format', 'class' => 'weSelect', 'style' => 'width: 225px;', 'onchange' => 'changeFormat()');

			if($_thumbnail_format == -1){
				$_thumbnail_format_select_attribs['disabled'] = 'true'; //#6027
			}

			$_thumbnail_format_select = new we_html_select($_thumbnail_format_select_attribs);

			foreach($_thumbnails_formats as $_k => $_v){
				if(in_array($_k, we_base_imageEdit::supported_image_types()) || $_k === 'none'){
					$_thumbnail_format_select->addOption($_k, $_v);

					// Check if added option is selected
					if($_thumbnail_format == $_k || (!$_thumbnail_format && ($_k === 'none'))){
						$_thumbnail_format_select->selectOption($_k);
					}
				}
			}

			// Build dialog
			$_thumbs[] = array('headline' => g_l('thumbnails', '[format]'), 'html' => $_thumbnail_format_select->getHtml(), 'space' => 200);

			return create_dialog('settings_predefined', g_l('thumbnails', '[thumbnails]'), $_thumbs, -1, '', '', false, $_needed_JavaScript);
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
	return we_html_element::htmlDiv(array('id' => 'thumbnails_dialog'), build_dialog('dialog')) .
		// Render save screen
		we_html_element::htmlDiv(array('id' => 'thumbnails_save', 'style' => 'display: none;'), build_dialog('save'));
}

function getFooter(){
	$_javascript = we_html_element::jsElement('
function we_save() {
	top.document.getElementById("thumbnails_dialog").style.display = "none";
	top.document.getElementById("thumbnails_save").style.display = "";
	top.document.we_form.save_thumbnails.value = "1";
	top.document.we_form.submit();
}');

	$close = we_base_request::_(we_base_request::JS, "closecmd");

	return $_javascript .
		we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'height:100%'), we_html_button::position_yes_no_cancel(we_html_button::create_button('save', 'javascript:we_save();'), '', we_html_button::create_button("close", "javascript:" . ($close ? $close . ';' : '') . 'top.close()'), 10, '', '', 0));
}

function getMainDialog(){
	// Check if we need to save settings
	if(we_base_request::_(we_base_request::BOOL, 'save_thumbnails')){
		$tn = we_base_request::_(we_base_request::STRING, 'thumbnail_name');
		if((strpos($tn, "'") !== false || strpos($tn, ',') !== false)){
			$save_javascript = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', '[thumbnail_hochkomma]'), we_message_reporting::WE_MESSAGE_ERROR) .
					'history.back()');
		} else {
			save_all_values();
			$save_javascript = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('thumbnails', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE) .
					"self.location = '" . $GLOBALS['reloadUrl'] . "&id=" . we_base_request::_(we_base_request::INT, "edited_id", 0) . "';");
		}

		return $save_javascript . build_dialog('saved');
	} else {

		return we_html_element::htmlForm(array('name' => 'we_form', 'method' => 'get', 'action' => $_SERVER['SCRIPT_NAME']), we_html_element::htmlHidden(array('name' => 'we_cmd[0]', 'value' => 'editThumbs')) . we_html_element::htmlHidden(array('name' => 'save_thumbnails', 'value' => 0)) . render_dialog()) .
			we_html_element::jsElement('init();');
	}
}

//  check if gd_lib is installed ...
if(we_base_imageEdit::gd_version() > 0){

	echo
	we_html_element::jsElement('self.focus();') .
	we_html_element::jsScript(JS_DIR . 'keyListener.js') .
	we_html_element::jsElement('
function closeOnEscape() {
	return true;
}

function saveOnKeyBoard() {
	window.frames[1].we_save();
	return true;
}') . STYLESHEET . '</head>' .
	we_html_element::htmlBody(array('style' => 'margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;text-align:center;')
		, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
			, we_html_element::htmlExIFrame('we_thumbnails', getMainDialog(), 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: hidden;', 'weDialogBody') .
			we_html_element::htmlExIFrame('we_thumbnails_footer', getFooter(), 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;')
	)) . '</html>';
} else { //  gd_lib is not installed - show error
	echo STYLESHEET . '</head><body class="weDialogBody">';


	$parts = array(
		array(
			'headline' => '',
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[add_description_nogdlib]'), we_html_tools::TYPE_INFO, 440),
			'space' => 0
		)
	);
	echo we_html_multiIconBox::getHTML('thumbnails', '100%', $parts, 30, '', -1, '', '', false, g_l('thumbnails', '[thumbnails]'));
}