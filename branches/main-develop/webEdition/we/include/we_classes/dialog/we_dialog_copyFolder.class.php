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
abstract class we_dialog_copyFolder{

	private static function formCreateCategoryChooser(we_base_jsCmd $jsCmd){
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',-1,'" . CATEGORY_TABLE . "','','','opener.addCat(top.fileSelect.data.allPaths);')");
		$del_but = we_html_button::create_button(we_html_button::TRASH, 'javascript:#####placeHolder#####;');
		$jsCmd->addCmd('setCategories_edit', $del_but);

		$table = new we_html_table([
			'id' => 'CategoriesBlock',
			'style' => 'display: block;',
			'class' => 'default',
				], 5, 2);

		$table->setCol(1, 0, ['class' => 'defaultfont', 'width' => 100, 'style' => 'padding-top:5px;'], g_l('copyFolder', '[categories]'));
		$table->setCol(1, 1, ['class' => 'defaultfont'], we_html_forms::checkbox(1, 0, 'OverwriteCategories', g_l('copyFolder', '[overwrite_categories]'), false, "defaultfont", "toggleButton();"));
		$table->setCol(2, 0, ['colspan' => 2], we_html_element::htmlDiv(['id' => 'categories',
					'class' => 'blockWrapper',
					'style' => 'width: 488px; height: 60px; border: #AAAAAA solid 1px;'
		]));

		$table->setCol(4, 0, ['colspan' => 2, 'style' => 'text-align:right;padding-top:5px;'], we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:removeAllCats()") . $addbut);

		return $table->getHtml() . $js;
	}

	private static function formCreateTemplateDirChooser(){
		$path = '/';
		$myid = 0;

		$weSuggest = & we_gui_suggest::getInstance();
		$weSuggest->setAcId('Template');
		$weSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$weSuggest->setInput('foo', $path, [], true);
		$weSuggest->setLabel(g_l('copyFolder', '[destdir]'));
		$weSuggest->setMaxResults(10);
		$weSuggest->setRequired(true);
		$weSuggest->setResult('CreateTemplateInFolderID', $myid);
		$weSuggest->setSelector(we_gui_suggest::DirSelector);
		$weSuggest->setTable(TEMPLATES_TABLE);
		$weSuggest->setWidth(370);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.CreateTemplateInFolderID.value,'" . TEMPLATES_TABLE . "','CreateTemplateInFolderID','foo','setCreateTemplate')", '', 0, 0, "", "", true, false));

		return $weSuggest->getHTML();
	}

	public static function getDialog(){
		$weSuggest = & we_gui_suggest::getInstance();
		$jsCmd = new we_base_jsCmd();

		if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 3)){
			$cmd1 = we_base_request::_(we_base_request::INT, 'we_cmd', '', 1);
			$cmd4 = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 4);

			$yes_button = we_html_button::create_button(we_html_button::OK, we_html_button::WE_FORM . ":we_form");
			$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");

			$pb = new we_progressBar(0, 270);

			$pb->addText("&nbsp;", we_progressBar::TOP, "pbar1");

			$buttons = '<table class="default" style="width:100%"><tr><td id="pbTd" style="text-align:left;">' . $pb->getHTML('', 'display:none;') . '</td><td style="text-align:right">' .
					we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button) .
					'</td></tr></table>';

			$hidden = we_html_element::htmlHiddens([
						"we_cmd[0]" => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
						"we_cmd[1]" => $cmd1,
						"we_cmd[2]" => we_base_request::_(we_base_request::INT, 'we_cmd', '', 2),
						($cmd4 ? "we_cmd[4]" : '') => $cmd4]);

			if(defined('OBJECT_FILES_TABLE') && $cmd4 == OBJECT_FILES_TABLE){
				$content = g_l('copyFolder', '[object_copy]') . '<br/>' .
						we_html_forms::checkbox(1, 0, "DoNotCopyFolders", g_l('copyFolder', '[object_copy_no_folders]')) .
						'&nbsp;<br/>' . g_l('copyFolder', '[sameName_headline]') . '<br/>' .
						we_html_tools::htmlAlertAttentionBox(g_l('copyFolder', '[sameName_expl]'), we_html_tools::TYPE_INFO, 380) .
						we_html_element::htmlDiv(['style' => 'margin-top:10px;'], we_html_forms::radiobutton("overwrite", 0, "OverwriteObjects", g_l('copyFolder', '[sameName_overwrite]')) .
								we_html_forms::radiobutton("rename", 0, "OverwriteObjects", g_l('copyFolder', '[sameName_rename]')) .
								we_html_forms::radiobutton("nothing", 1, "OverwriteObjects", g_l('copyFolder', '[sameName_nothing]'))
						) .
						$hidden;
			} else {
				$content = '<table class="default" style="width:500px;"><tr><td>' . we_html_forms::checkbox(1, 0, 'CreateTemplate', g_l('copyFolder', '[create_new_templates]'), false, "defaultfont", "toggleButton(); incTemp(this.checked)") .
						'<div id="imTemp" style="display:block">' .
						we_html_forms::checkbox(1, 0, 'CreateMasterTemplate', g_l('copyFolder', '[create_new_masterTemplates]'), false, "defaultfont", "", 1) .
						we_html_forms::checkbox(1, 0, 'CreateIncludedTemplate', g_l('copyFolder', '[create_new_includedTemplates]'), false, "defaultfont", "", 1) .
						'</div></td><td style="vertical-align:top">' .
						we_html_forms::checkbox(1, 0, 'CreateDoctypes', g_l('copyFolder', '[create_new_doctypes]')) .
						'</td></tr>
					<tr><td colspan="2" style="padding:2px 0px;">' . self::formCreateTemplateDirChooser() . '</td></tr>
					<tr><td colspan="2">' . self::formCreateCategoryChooser($jsCmd) .
						$hidden .
						'</td></tr></table>';
			}

			echo we_html_tools::getHtmlTop('', '', '', we_progressBar::getJSCode() .
					we_html_element::jsScript(JS_DIR . 'copyFolder.js') .
					$jsCmd->getCmds() .
					we_html_element::htmlBody(['class' => "weDialogBody", 'onload' => "self.focus();"], '<form name="we_form" target="pbUpdateFrame" method="get">' .
							we_html_tools::htmlDialogLayout($content, g_l('copyFolder', '[headline]') . ": " . we_base_util::shortenPath(id_to_path($cmd1, $cmd4), 46), $buttons) .
							'</form>' .
							'<iframe src="about:blank" name="pbUpdateFrame" style="width:0px;height:0px" id="pbUpdateFrame"></iframe>'
			));
			return;
		}

		$bodyAttribs = ['style' => "background-color:#FFFFFF;margin:10px;"];
		$fr = (we_base_request::_(we_base_request::BOOL, 'finish') ?
				new we_fragment_copyFolderFinish('we_copyFolderFinish', 5, $bodyAttribs) :
				new we_fragment_copyFolder('we_copyFolder', 5, $bodyAttribs)
				);
	}

}
