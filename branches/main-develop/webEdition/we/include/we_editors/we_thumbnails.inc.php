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

// Check if we need to create a new thumbnail
if(($name = we_base_request::_(we_base_request::STRING, 'newthumbnail')) && we_base_permission::hasPerm('ADMINISTRATOR')){
	$DB_WE->query('INSERT INTO ' . THUMBNAILS_TABLE . ' SET Name="' . $DB_WE->escape($name) . '"');
	$GLOBALS['id'] = $DB_WE->getInsertId();
} else {
	$GLOBALS['id'] = we_base_request::_(we_base_request::INT, 'id', 0);
}

// Check if we need to delete a thumbnail
if(($delId = we_base_request::_(we_base_request::INT, 'deletethumbnail')) && we_base_permission::hasPerm('ADMINISTRATOR')){
	// Delete thumbnails in filesystem
	we_thumbnail::deleteByThumbID($delId);

	// Delete entry in database
	$DB_WE->query('DELETE FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . $delId);
}

// Check which thumbnail to work with
$GLOBALS['id'] = $GLOBALS['id']? : ( f('SELECT ID FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name LIMIT 1')? : -1);
$GLOBALS['scriptVars'] = [];

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
		($expand != -1 ? we_html_element::jsScript(JS_DIR . 'multiIconBox.js') : '') .
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
				$setArray[$settingname] = we_thumbnail::FORMAT_JPG;
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

	if(we_base_permission::hasPerm('ADMINISTRATOR')){
		$setArray = ['Date' => sql_function('UNIX_TIMESTAMP()')];
		// Update settings
		remember_value($setArray, we_base_request::_(we_base_request::STRING, 'thumbnail_name', null), 'Name');
		remember_value($setArray, we_base_request::_(we_base_request::INT, 'thumbnail_width', null), 'Width');
		remember_value($setArray, we_base_request::_(we_base_request::INT, 'thumbnail_height', null), 'Height');
		remember_value($setArray, we_base_request::_(we_base_request::INT, 'thumbnail_quality', null), 'Quality');
		remember_value($setArray, we_base_request::_(we_base_request::STRING, 'Format', null), 'Format');
		remember_value($setArray, we_base_request::_(we_base_request::STRING, 'description', null), 'description');
		$setArray['Options'] = implode(',', array_filter(we_base_request::_(we_base_request::STRING, 'Options', []), function($v){
				return $v !== we_thumbnail::OPTION_DEFAULT;
			}));
		$DB_WE->query('UPDATE ' . THUMBNAILS_TABLE . ' SET ' . we_database_base::arraySetter($setArray) . ' WHERE ID=' . we_base_request::_(we_base_request::INT, 'edited_id', 0));
	}
}

function build_dialog($selected_setting = 'ui'){
	$DB_WE = $GLOBALS['DB_WE'];
	$id = $GLOBALS['id'];

	switch($selected_setting){
		case 'save':
			//SAVE DIALOG
			return create_dialog('', g_l('thumbnails', '[save_wait]'), [['headline' => '', 'html' => g_l('thumbnails', '[save]'),]
			]);

		case 'saved':
			// SAVED SUCCESSFULLY DIALOG
			return create_dialog('', g_l('thumbnails', '[saved_successfully]'), [['headline' => '', 'html' => g_l('thumbnails', '[saved]'),]
			]);

		case 'dialog':
			// Detect thumbnail names
			$DB_WE->query('SELECT Name FROM ' . THUMBNAILS_TABLE);
			$GLOBALS['scriptVars']['thumbnail_names'] = $DB_WE->getAll(true);
			$GLOBALS['scriptVars']['selectedID'] = $id;
			$GLOBALS['scriptVars']['selectedName'] = f('SELECT Name FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . intval($id));

			$enabled_buttons = false;

			// Build language select box
			$thumbnails = new we_html_select(['name' => 'Thumbnails', 'class' => 'weSelect', 'size' => 8, 'style' => 'width: 440px;', 'onchange' => "if(this.selectedIndex > -1){change_thumbnail(this.options[this.selectedIndex].value);}"]);

			$DB_WE->query('SELECT ID,Name FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');

			$thumbnail_counter_firsttime = true;
			while($DB_WE->next_record()){
				$enabled_buttons = true;

				$thumbnails->addOption($DB_WE->f('ID'), $DB_WE->f('Name'));

				if($thumbnail_counter_firsttime && !$id){
					$id = $DB_WE->f('ID');

					$thumbnails->selectOption($DB_WE->f('ID'));
				} else if($id == $DB_WE->f('ID')){
					$thumbnails->selectOption($DB_WE->f('ID'));
				}

				$thumbnail_counter_firsttime = false;
			}

			// Create thumbnails list
			$thumbnails_table = new we_html_table(['class' => 'default'], 1, 2);
			$thumbnails_table->setCol(0, 0, ['style' => "padding-right:10px;"], we_html_element::htmlHidden('edited_id', $id) . $thumbnails->getHtml());
			$thumbnails_table->setCol(0, 1, ['style' => 'vertical-align:top'], we_html_button::create_button(we_html_button::ADD, 'javascript:add_thumbnail();') . '<br/>' . we_html_button::create_button(we_html_button::DELETE, 'javascript:delete_thumbnail();', '', 0, 0, '', '', !$enabled_buttons, false));

			$allData = (getHash('SELECT Name,Width,Height,Quality,Format,Options,description FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . $id)? :
					['Name' => '',
					'Width' => '',
					'Height' => '',
					'Quality' => '',
					'Format' => '',
					'Options' => '',
					'description' => ''
			]);

			$allData['Options'] = explode(',', $allData['Options']);

			$thumbnail_name_input = we_html_tools::htmlTextInput('thumbnail_name', 22, ($id != -1 ? $allData['Name'] : ''), 255, ($id == -1 ? 'disabled="true"' : ''), 'text', 340);
			$thumbnail_description_input = we_html_tools::htmlTextInput('description', 22, ($id != -1 ? $allData['description'] : ''), 255, ($id == -1 ? 'disabled="true"' : ''), 'text', 340);
			/*			 * ***************************************************************
			 * PROPERTIES
			 * *************************************************************** */

			// Create specify thumbnail dimension input
			$thumbnail_quality = ($id != -1) ? $allData['Quality'] : -1;
			$thumbnail_specify_table = new we_html_table(['class' => 'default inputs'], 3, 2);

			$thumbnail_specify_table->setCol(0, 0, ['class' => 'defaultfont', 'style' => 'padding-right:10px;'], g_l('thumbnails', '[width]') . ':');
			$thumbnail_specify_table->setCol(0, 1, null, we_html_tools::htmlTextInput('thumbnail_width', 6, ($id != -1 ? $allData['Width'] : ''), 4, ($id == -1 ? 'disabled="disabled"' : ''), 'text'));
			$thumbnail_specify_table->setCol(1, 0, ['class' => 'defaultfont'], g_l('thumbnails', '[height]') . ':');
			$thumbnail_specify_table->setCol(1, 1, null, we_html_tools::htmlTextInput('thumbnail_height', 6, ($id != -1 ? $allData['Height'] : ''), 4, ($id == -1 ? 'disabled="disabled"' : ''), 'text'));
			$thumbnail_specify_table->setCol(2, 0, ['class' => 'defaultfont', 'id' => 'thumbnail_quality_text_cell'], g_l('thumbnails', '[quality]') . ':');
			$thumbnail_specify_table->setCol(2, 1, ['class' => 'defaultfont', 'id' => 'thumbnail_quality_value_cell'], we_base_imageEdit::qualitySelect('thumbnail_quality', $thumbnail_quality));

			// Create checkboxes for options for thumbnails

			$options = [
				'opts' => [2, 1, [
						we_thumbnail::OPTION_MAXSIZE => [($id != -1) ? intval(in_array(we_thumbnail::OPTION_MAXSIZE, $allData['Options'])) : -1, g_l('thumbnails', '[maximize]'), g_l('thumbnails', '[maximize_desc]')],
						we_thumbnail::OPTION_INTERLACE => [($id != -1) ? intval(in_array(we_thumbnail::OPTION_INTERLACE, $allData['Options'])) : -1, g_l('thumbnails', '[interlace]'), g_l('thumbnails', '[interlace_desc]')],
					]
				],
				'cutting' => [4, 1, [
						we_thumbnail::OPTION_DEFAULT => [($id == -1) ? true : false, g_l('thumbnails', '[cutting_none]'), g_l('thumbnails', '[cutting_none_desc]')],
						we_thumbnail::OPTION_RATIO => [($id != -1) ? in_array(we_thumbnail::OPTION_RATIO, $allData['Options']) : false, g_l('thumbnails', '[ratio]'), g_l('thumbnails', '[ratio_desc]')],
						we_thumbnail::OPTION_FITINSIDE => [($id != -1) ? in_array(we_thumbnail::OPTION_FITINSIDE, $allData['Options']) : false, g_l('thumbnails', '[fitinside]'), g_l('thumbnails', '[fitinside_desc]')],
						we_thumbnail::OPTION_CROP => [($id != -1) ? in_array(we_thumbnail::OPTION_CROP, $allData['Options']) : false, g_l('thumbnails', '[crop]'), g_l('thumbnails', '[crop_desc]')],
					]
				],
				'filter' => [2, 3, [
						we_thumbnail::OPTION_UNSHARP => [($id != -1) ? in_array(we_thumbnail::OPTION_UNSHARP, $allData['Options']) : false, g_l('thumbnails', '[unsharp]'), g_l('thumbnails', '[unsharp_desc]')],
						we_thumbnail::OPTION_GAUSSBLUR => [($id != -1) ? in_array(we_thumbnail::OPTION_GAUSSBLUR, $allData['Options']) : false, g_l('thumbnails', '[gauss]'), g_l('thumbnails', '[gauss_desc]')],
						we_thumbnail::OPTION_GRAY => [($id != -1) ? in_array(we_thumbnail::OPTION_GRAY, $allData['Options']) : false, g_l('thumbnails', '[gray]')],
						we_thumbnail::OPTION_NEGATE => [($id != -1) ? in_array(we_thumbnail::OPTION_NEGATE, $allData['Options']) : false, g_l('thumbnails', '[negate]'), g_l('thumbnails', '[negate_desc]')],
						we_thumbnail::OPTION_SEPIA => [($id != -1) ? in_array(we_thumbnail::OPTION_SEPIA, $allData['Options']) : false, g_l('thumbnails', '[sepia]')],
					]
				]
			];
			foreach($options as $k => $v){
				if(isset($v[2][we_thumbnail::OPTION_DEFAULT])){
					$v[2][we_thumbnail::OPTION_DEFAULT][0] = ($id == -1) || ((count(array_intersect(array_keys($v[2]), $allData['Options']))) === 0);
				}

				$thumbnail_option_table[$k] = new we_html_table(['class' => 'editorThumbnailsOptions'], $v[0], $v[1]);
				$i = 0;
				foreach($v[2] as $key => $val){
					switch($k){
						case 'opts':
						case 'filter':
							$thumbnail_option_table[$k]->setCol(($i % $v[0]), intval($i++ / $v[0]), null, we_html_forms::checkbox($key, (intval($val[0]) > 0), 'Options[' . $key . ']', $val[1], false, 'defaultfont', '', (intval($val[0]) === -1), '', we_html_tools::TYPE_NONE, 0, '', '', (isset($val[2]) ? $val[2] : '')));
							break;
						default:
							$thumbnail_option_table[$k]->setCol(($i % $v[0]), intval($i++ / $v[0]), null, we_html_forms::radiobutton($key, $val[0], 'Options[' . $k . ']', $val[1], true, "defaultfont", '', false, '', we_html_tools::TYPE_NONE, 0, '', '', (isset($val[2]) ? $val[2] : '')));
					}
				}
			}

			$window_html = new we_html_table(['class' => 'editorThumbnailsOptions'], 1, 4);
			$window_html->setCol(0, 0, null, $thumbnail_specify_table->getHtml());
			$window_html->setCol(0, 1, null, we_html_element::htmlDiv([], g_l('thumbnails', '[output_options]') . ':') . we_html_element::htmlDiv([], $thumbnail_option_table['opts']->getHtml()));
			$window_html->setCol(0, 2, null, we_html_element::htmlDiv([], g_l('thumbnails', '[cutting]') . ':') . we_html_element::htmlDiv([], $thumbnail_option_table['cutting']->getHtml()));

			// OUTPUT FORMAT

			$thumbnail_format = ($id != -1) ? $allData['Format'] : -1;

			// Define available formats
			$thumbnails_formats = ['none' => g_l('thumbnails', '[format_original]'), we_thumbnail::FORMAT_GIF => g_l('thumbnails', '[format_gif]'), we_thumbnail::FORMAT_JPG => g_l('thumbnails', '[format_jpg]'), we_thumbnail::FORMAT_PNG => g_l('thumbnails', '[format_png]')];
			$thumbnail_format_select_attribs = ['name' => 'Format', 'id' => 'Format', 'class' => 'weSelect', 'style' => 'width: 225px;', 'onchange' => 'changeFormat()'];

			if($id == -1){
				$thumbnail_format_select_attribs['disabled'] = 'true'; //#6027
			}
			$thumbnail_format_select = new we_html_select($thumbnail_format_select_attribs);

			foreach($thumbnails_formats as $k => $v){
				if(in_array($k, we_base_imageEdit::supported_image_types()) || $k === 'none'){
					$thumbnail_format_select->addOption($k, $v);

					// Check if added option is selected
					if($thumbnail_format == $k || (!$thumbnail_format && ($k === 'none'))){
						$thumbnail_format_select->selectOption($k);
					}
				}
			}

			// Build dialog
			return create_dialog('settings_predefined', g_l('thumbnails', '[thumbnails]'), [
				['html' => $thumbnails_table->getHtml(),],
				['headline' => g_l('thumbnails', '[name]'), 'html' => $thumbnail_name_input, 'space' => we_html_multiIconBox::SPACE_MED],
				['headline' => g_l('thumbnails', '[description]'), 'html' => $thumbnail_description_input, 'space' => we_html_multiIconBox::SPACE_MED],
				['headline' => g_l('thumbnails', '[properties]'), 'html' => $window_html->getHtml(), 'space' => we_html_multiIconBox::SPACE_SMALL],
				['headline' => 'Filter', 'html' => we_html_element::htmlDiv(['class' => 'editorThumbnailsFilter'], $thumbnail_option_table['filter']->getHtml())],
				['headline' => g_l('thumbnails', '[format]'), 'html' => $thumbnail_format_select->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED],
				]);
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
	return we_html_element::htmlDiv(['id' => 'thumbnails_dialog'], build_dialog('dialog')) .
		// Render save screen
		we_html_element::htmlDiv(['id' => 'thumbnails_save', 'style' => 'display: none;'], build_dialog('save'));
}

function getFooter(){
	return we_html_element::htmlDiv(['class' => 'weDialogButtonsBody', 'style' => 'height:100%'], we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, 'javascript:we_save();'), '', we_html_button::create_button(we_html_button::CLOSE, "javascript:" . 'top.close()'), 10, '', '', 0));
}

function getMainDialog(){
	// Check if we need to save settings
	if(!we_base_request::_(we_base_request::BOOL, 'save_thumbnails')){
		return we_html_element::htmlForm(['name' => 'we_form', 'method' => 'get', 'action' => $_SERVER['SCRIPT_NAME']], we_html_element::htmlHiddens(['we_cmd[0]' => 'editThumbs', 'save_thumbnails' => 0]) . render_dialog());
	}
	$cmd = new we_base_jsCmd();
	$tn = we_base_request::_(we_base_request::STRING, 'thumbnail_name');
	if((strpos($tn, "'") !== false || strpos($tn, ',') !== false)){
		$cmd->addMsg(g_l('alert', '[thumbnail_hochkomma]'), we_base_util::WE_MESSAGE_ERROR);
		$cmd->addCmd('history.back');
	} else {
		save_all_values();
		$cmd->addMsg(g_l('thumbnails', '[saved]'), we_base_util::WE_MESSAGE_NOTICE);
		$cmd->addCmd('location', ['doc' => 'document', 'loc' => WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=editThumbs&id=' . we_base_request::_(we_base_request::INT, "edited_id", 0)]);
	}

	return $cmd->getCmds() . build_dialog('saved');
}

//  check if gd_lib is installed ...
if(we_base_imageEdit::gd_version() > 0){
	//some var's are set
	$dialog = getMainDialog();
	echo we_html_tools::getHtmlTop(g_l('thumbnails', '[thumbnails]'), '', '', we_html_element::jsScript(JS_DIR . 'we_thumbnails.js', '', ['id' => 'loadVarThumbnails', 'data-thumbnails' => setDynamicVar($GLOBALS['scriptVars'])]), we_html_element::htmlBody(['class' => 'weDialogBody', 'onload' => 'init();']
			, we_html_element::htmlExIFrame('we_thumbnails', $dialog, 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: hidden;') .
			we_html_element::htmlExIFrame('we_thumbnails_footer', getFooter(), 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;')
	));
	return;
}//  gd_lib is not installed - show error

echo we_html_tools::getHtmlTop(g_l('thumbnails', '[thumbnails]'), '', '', '', we_html_element::htmlBody(['class' => 'weDialogBody'], we_html_multiIconBox::getHTML('thumbnails', [
			[
				'headline' => '',
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[add_description_nogdlib]'), we_html_tools::TYPE_INFO, 440),
			]
			], 30, '', -1, '', '', false, g_l('thumbnails', '[thumbnails]'))
));
