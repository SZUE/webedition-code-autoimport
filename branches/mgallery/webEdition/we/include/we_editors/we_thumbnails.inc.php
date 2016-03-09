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

// Check if we need to create a new thumbnail
if(($name = we_base_request::_(we_base_request::STRING, 'newthumbnail')) &&
		permissionhandler::hasPerm('ADMINISTRATOR')){
	$DB_WE->query('INSERT INTO ' . THUMBNAILS_TABLE . ' SET Name="' . $DB_WE->escape($name) . '"');
	$GLOBALS['id'] = $DB_WE->getInsertId();
} else {
	$GLOBALS['id'] = we_base_request::_(we_base_request::INT, 'id', 0);
}

// Check if we need to delete a thumbnail
if(($delId = we_base_request::_(we_base_request::INT, 'deletethumbnail')) && permissionhandler::hasPerm('ADMINISTRATOR')){
	// Delete thumbnails in filesystem
	we_thumbnail::deleteByThumbID($delId);

	// Delete entry in database
	$DB_WE->query('DELETE FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . $delId);
}

// Check which thumbnail to work with
$GLOBALS['id'] = $GLOBALS['id']? : ( f('SELECT ID FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name LIMIT 1')? : -1);

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
			we_html_multiIconBox::getHTML($name, $content, 30, '', $expand, $show_text, $hide_text, $cookie != false ? ($cookie === 'down') : $cookie, $title);
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
			case 'description':
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
		remember_value($setArray, we_base_request::_(we_base_request::STRING, 'Format', null), 'Format');
		remember_value($setArray, we_base_request::_(we_base_request::STRING, 'description', null), 'description');
		$setArray['Options'] = implode(',', array_filter(we_base_request::_(we_base_request::STRING, 'Options', array()), function($v) {return $v !== we_thumbnail::OPTION_DEFAULT;}));
		$DB_WE->query('UPDATE ' . THUMBNAILS_TABLE . ' SET ' . we_database_base::arraySetter($setArray) . ' WHERE ID=' . we_base_request::_(we_base_request::INT, 'edited_id', 0));
	}
}

function build_dialog($selected_setting = 'ui'){
	$DB_WE = $GLOBALS['DB_WE'];
	$id = $GLOBALS['id'];

	switch($selected_setting){
		case 'save':
			//SAVE DIALOG
			return create_dialog('', g_l('thumbnails', '[save_wait]'), array(
				array('headline' => '', 'html' => g_l('thumbnails', '[save]'),)
			));

		case 'saved':
			// SAVED SUCCESSFULLY DIALOG
			return create_dialog('', g_l('thumbnails', '[saved_successfully]'), array(
				array('headline' => '', 'html' => g_l('thumbnails', '[saved]'),)
			));

		case 'dialog':
			// Detect thumbnail names
			$DB_WE->query('SELECT Name FROM ' . THUMBNAILS_TABLE);
			$_thumbnail_names = $DB_WE->getAll(true);

			$_thumbnail_names = $_thumbnail_names ? '\'' . implode('\',\'', $_thumbnail_names) . '\'' : '';

			// Generate needed JS
			$_needed_JavaScript_Source = "
var thumbnail_names = [" . $_thumbnail_names . "];
function delete_thumbnail() {" .
					(permissionhandler::hasPerm('ADMINISTRATOR') ?
							"var deletion = confirm('" . sprintf(g_l('thumbnails', '[delete_prompt]'), f('SELECT Name FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . intval($id))) . "');

		if (deletion == true) {
			self.location = consts.reloadUrl+'&deletethumbnail=" . $id . "';
		}" :
							"") . "
}";

			$_needed_JavaScript = we_html_element::jsElement($_needed_JavaScript_Source);

			$_enabled_buttons = false;

			// Build language select box
			$_thumbnails = new we_html_select(array('name' => 'Thumbnails', 'class' => 'weSelect', 'size' => 8, 'style' => 'width: 440px;', 'onchange' => "if(this.selectedIndex > -1){change_thumbnail(this.options[this.selectedIndex].value);}"));

			$DB_WE->query('SELECT ID,Name FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');

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
			$_thumbnails_table = new we_html_table(array('class' => 'default'), 1, 2);

			$_thumbnails_table->setCol(0, 0, array('style' => "padding-right:10px;"), we_html_element::htmlHidden('edited_id', $id) . $_thumbnails->getHtml());
			$_thumbnails_table->setCol(0, 1, array('style' => 'vertical-align:top'), we_html_button::create_button(we_html_button::ADD, 'javascript:add_thumbnail();') . '<br/>' . we_html_button::create_button(we_html_button::DELETE, 'javascript:delete_thumbnail();', true, 100, 22, '', '', !$_enabled_buttons, false));

			// Build dialog
			$_thumbs[] = array('headline' => '', 'html' => $_thumbnails_table->getHtml(),);

			$allData = (getHash('SELECT Name,Width,Height,Quality,Format,Options,description FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . $id)? :
							array(
						'Name' => '',
						'Width' => '',
						'Height' => '',
						'Quality' => '',
						'Format' => '',
						'Options' => '',
						'description' => ''
			));

			$allData['Options'] = explode(',', $allData['Options']);

			$_thumbnail_name_input = we_html_tools::htmlTextInput('thumbnail_name', 22, ($id != -1 ? $allData['Name'] : ''), 255, ($id == -1 ? 'disabled="true"' : ''), 'text', 340);
			$_thumbnail_description_input = we_html_tools::htmlTextInput('description', 22, ($id != -1 ? $allData['description'] : ''), 255, ($id == -1 ? 'disabled="true"' : ''), 'text', 340);
			/*			 * ***************************************************************
			 * PROPERTIES
			 * *************************************************************** */

			// Create specify thumbnail dimension input
			$_thumbnail_quality = ($id != -1) ? $allData['Quality'] : -1;
			$_thumbnail_specify_table = new we_html_table(array('class' => 'default inputs'), 3, 2);

			$_thumbnail_specify_table->setCol(0, 0, array('class' => 'defaultfont', 'style' => 'padding-right:10px;'), g_l('thumbnails', '[width]') . ':');
			$_thumbnail_specify_table->setCol(0, 1, null, we_html_tools::htmlTextInput('thumbnail_width', 6, ($id != -1 ? $allData['Width'] : ''), 4, ($id == -1 ? 'disabled="disabled"' : ''), 'text'));
			$_thumbnail_specify_table->setCol(1, 0, array('class' => 'defaultfont'), g_l('thumbnails', '[height]') . ':');
			$_thumbnail_specify_table->setCol(1, 1, null, we_html_tools::htmlTextInput('thumbnail_height', 6, ($id != -1 ? $allData['Height'] : ''), 4, ($id == -1 ? 'disabled="disabled"' : ''), 'text'));
			$_thumbnail_specify_table->setCol(2, 0, array('class' => 'defaultfont', 'id' => 'thumbnail_quality_text_cell'), g_l('thumbnails', '[quality]') . ':');
			$_thumbnail_specify_table->setCol(2, 1, array('class' => 'defaultfont', 'id' => 'thumbnail_quality_value_cell'), we_base_imageEdit::qualitySelect('thumbnail_quality', $_thumbnail_quality));

			// Create checkboxes for options for thumbnails
			$options['opts'] = array(2, 1, array(
					we_thumbnail::OPTION_MAXSIZE => array(($id != -1) ? intval(in_array(we_thumbnail::OPTION_MAXSIZE, $allData['Options'])) : -1, g_l('thumbnails', '[maximize]'), g_l('thumbnails', '[maximize_desc]')),
					we_thumbnail::OPTION_INTERLACE => array(($id != -1) ? intval(in_array(we_thumbnail::OPTION_INTERLACE, $allData['Options'])) : -1, g_l('thumbnails', '[interlace]'), g_l('thumbnails', '[interlace_desc]')),
				)
			);
			$options['cutting'] = array(4, 1, array(
					we_thumbnail::OPTION_DEFAULT => array(($id == -1) ? true : false, g_l('thumbnails', '[cutting_none]'), g_l('thumbnails', '[cutting_none_desc]')),
					we_thumbnail::OPTION_RATIO => array(($id != -1) ? in_array(we_thumbnail::OPTION_RATIO, $allData['Options']) : false, g_l('thumbnails', '[ratio]'), g_l('thumbnails', '[ratio_desc]')),
					we_thumbnail::OPTION_FITINSIDE => array(($id != -1) ? in_array(we_thumbnail::OPTION_FITINSIDE, $allData['Options']) : false, g_l('thumbnails', '[fitinside]'), g_l('thumbnails', '[fitinside_desc]')),
					we_thumbnail::OPTION_CROP => array(($id != -1) ? in_array(we_thumbnail::OPTION_CROP, $allData['Options']) : false, g_l('thumbnails', '[crop]'), g_l('thumbnails', '[crop_desc]')),
				)
			);
			$options['filter'] = array(2, 3, array(
					we_thumbnail::OPTION_DEFAULT => array(($id == -1) ? true : false, g_l('thumbnails', '[filter_none]'), g_l('thumbnails', '[filter_none_desc]')),
					we_thumbnail::OPTION_UNSHARP => array(($id != -1) ? in_array(we_thumbnail::OPTION_UNSHARP, $allData['Options']) : false, g_l('thumbnails', '[unsharp]')),
					we_thumbnail::OPTION_GAUSSBLUR => array(($id != -1) ? in_array(we_thumbnail::OPTION_GAUSSBLUR, $allData['Options']) : false, g_l('thumbnails', '[gauss]'), g_l('thumbnails', '[gauss_desc]')),
					we_thumbnail::OPTION_GRAY => array(($id != -1) ? in_array(we_thumbnail::OPTION_GRAY, $allData['Options']) : false, g_l('thumbnails', '[gray]')),
					we_thumbnail::OPTION_NEGATE => array(($id != -1) ? in_array(we_thumbnail::OPTION_NEGATE, $allData['Options']) : false, g_l('thumbnails', '[negate]'), g_l('thumbnails', '[negate_desc]')),
					we_thumbnail::OPTION_SEPIA => array(($id != -1) ? in_array(we_thumbnail::OPTION_SEPIA, $allData['Options']) : false, g_l('thumbnails', '[sepia]')),
				)
			);
			foreach($options as $k => $v){
				if(isset($v[2][we_thumbnail::OPTION_DEFAULT])){
					$v[2][we_thumbnail::OPTION_DEFAULT][0] = ($id == -1) || ((count(array_intersect(array_keys($v[2]), $allData['Options']))) === 0);
				}

				$_thumbnail_option_table[$k] = new we_html_table(array('class' => 'editorThumbnailsOptions'), $v[0], $v[1]);
				$i = 0;
				foreach($v[2] as $key => $val){
					switch($k){
						case 'opts':
							$_thumbnail_option_table[$k]->setCol(($i % $v[0]), intval($i++ / $v[0]), null, we_html_forms::checkbox($key , (($val[0] <= 0) ? false : true), 'Options[' . $key . ']', $val[1], false, 'defaultfont', '', ($val[0] == -1), '', we_html_tools::TYPE_NONE, 0, '', '', (isset($val[2]) ? $val[2] : '')));
							break;
						default:
							$_thumbnail_option_table[$k]->setCol(($i % $v[0]), intval($i++ / $v[0]), null, we_html_forms::radiobutton($key, $val[0], 'Options[' . $k . ']', $val[1], true, "defaultfont", '', false, '', we_html_tools::TYPE_NONE, 0, '', '', (isset($val[2]) ? $val[2] : '')));
					}
					
				}
			}

			$_window_html = new we_html_table(array('class' => 'editorThumbnailsOptions'), 1, 4);
			$_window_html->setCol(0, 0, null, $_thumbnail_specify_table->getHtml());
			$_window_html->setCol(0, 1, null, we_html_element::htmlDiv(array(), g_l('thumbnails', '[output_options]') . ':') . we_html_element::htmlDiv(array(), $_thumbnail_option_table['opts']->getHtml()));
			$_window_html->setCol(0, 2, null, we_html_element::htmlDiv(array(), g_l('thumbnails', '[cutting]') . ':') . we_html_element::htmlDiv(array(), $_thumbnail_option_table['cutting']->getHtml()));

			// OUTPUT FORMAT

			$_thumbnail_format = ($id != -1) ? $allData['Format'] : -1;

			// Define available formats
			$_thumbnails_formats = array('none' => g_l('thumbnails', '[format_original]'), 'gif' => g_l('thumbnails', '[format_gif]'), 'jpg' => g_l('thumbnails', '[format_jpg]'), 'png' => g_l('thumbnails', '[format_png]'));
			$_thumbnail_format_select_attribs = array('name' => 'Format', 'id' => 'Format', 'class' => 'weSelect', 'style' => 'width: 225px;', 'onchange' => 'changeFormat()');

			if($id == -1){
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
			return create_dialog('settings_predefined', g_l('thumbnails', '[thumbnails]'), array(
				array('html' => $_thumbnails_table->getHtml(),),
				array('headline' => g_l('thumbnails', '[name]'), 'html' => $_thumbnail_name_input, 'space' => 100),
				array('headline' => g_l('thumbnails', '[description]'), 'html' => $_thumbnail_description_input, 'space' => 100),
				array('headline' => g_l('thumbnails', '[properties]'), 'html' => $_window_html->getHtml(), 'space' => 10),
				array('headline' => 'Filter', 'html' => we_html_element::htmlDiv(array('class' => 'editorThumbnailsFilter'), $_thumbnail_option_table['filter']->getHtml()), 'space' => 0),
				array('headline' => g_l('thumbnails', '[format]'), 'html' => $_thumbnail_format_select->getHtml(), 'space' => 100),
					), -1, '', '', false, $_needed_JavaScript);
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
	$close = we_base_request::_(we_base_request::JS, "closecmd");

	return we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'height:100%'), we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, 'javascript:we_save();'), '', we_html_button::create_button(we_html_button::CLOSE, "javascript:" . ($close ? $close . ';' : '') . 'top.close()'), 10, '', '', 0));
}

function getMainDialog(){
	// Check if we need to save settings
	if(!we_base_request::_(we_base_request::BOOL, 'save_thumbnails')){
		return we_html_element::htmlForm(array('name' => 'we_form', 'method' => 'get', 'action' => $_SERVER['SCRIPT_NAME']), we_html_element::htmlHiddens(array('we_cmd[0]' => 'editThumbs', 'save_thumbnails' => 0)) . render_dialog());
	}

	$tn = we_base_request::_(we_base_request::STRING, 'thumbnail_name');
	if((strpos($tn, "'") !== false || strpos($tn, ',') !== false)){
		$save_javascript = we_message_reporting::getShowMessageCall(g_l('alert', '[thumbnail_hochkomma]'), we_message_reporting::WE_MESSAGE_ERROR) .
				'history.back()';
	} else {
		save_all_values();
		$save_javascript = we_message_reporting::getShowMessageCall(g_l('thumbnails', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE) .
				"self.location = consts.reloadUrl+'&id=" . we_base_request::_(we_base_request::INT, "edited_id", 0) . "';";
	}

	return we_html_element::jsElement($save_javascript) . build_dialog('saved');
}

echo
we_html_element::jsElement('
var consts={
	reloadUrl:WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=editThumbs",
};
var g_l={
	thumbnail_hochkomma: "' . we_message_reporting::prepareMsgForJS(g_l('alert', '[thumbnail_hochkomma]')) . '",
	thumbnail_empty: "' . we_message_reporting::prepareMsgForJS(g_l('alert', '[thumbnail_empty]')) . '",
	thumbnail_exists: "' . we_message_reporting::prepareMsgForJS(g_l('alert', '[thumbnail_exists]')) . '",
	thumbnail_new: "' . g_l('thumbnails', '[new]') . '"
};
	') .
 we_html_element::jsScript(JS_DIR . 'we_thumbnails.js') .
 STYLESHEET . '</head>';
//  check if gd_lib is installed ...
if(we_base_imageEdit::gd_version() > 0){
	echo we_html_element::htmlBody(array('class' => 'weDialogBody', 'onload' => 'init();')
			, we_html_element::htmlExIFrame('we_thumbnails', getMainDialog(), 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: hidden;') .
			we_html_element::htmlExIFrame('we_thumbnails_footer', getFooter(), 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;')
	) . '</html>';
	return;
}//  gd_lib is not installed - show error

echo we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_multiIconBox::getHTML('thumbnails', array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[add_description_nogdlib]'), we_html_tools::TYPE_INFO, 440),
			)
				), 30, '', -1, '', '', false, g_l('thumbnails', '[thumbnails]'))
) . '</html>';
