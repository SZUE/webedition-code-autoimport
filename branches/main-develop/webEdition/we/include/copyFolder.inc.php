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
$weSuggest = & weSuggest::getInstance();

if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 3)){
	$cmd1 = we_base_request::_(we_base_request::INT, 'we_cmd', '', 1);
	$cmd4 = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 4);

	$yes_button = we_html_button::create_button(we_html_button::OK, we_html_button::WE_FORM . ":we_form");
	$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");

	$pb = new we_progressBar(0, 270);

	$pb->addText("&nbsp;", we_progressBar::TOP, "pbar1");

	$buttons = '<table class="default" style="width:100%"><tr><td id="pbTd" style="text-align:left;display:none;">' . $pb->getHTML() . '</td><td style="text-align:right">' .
		we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button) .
		'</td></tr></table>';

	$hidden = we_html_element::htmlHiddens([
			"we_cmd[0]" => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 0),
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
					<tr><td colspan="2" style="padding:2px 0px;">' . we_fragment_copyFolder::formCreateTemplateDirChooser() . '</td></tr>
					<tr><td colspan="2">' . we_fragment_copyFolder::formCreateCategoryChooser() .
			$hidden .
			'</td></tr></table>';
	}
	we_fragment_copyFolder::printHeader();
	echo
	we_progressBar::getJSCode() .
	we_html_element::jsScript(JS_DIR . 'copyFolder.js') .
	'<body class="weDialogBody" onload="self.focus();">' .
	'<form name="we_form" target="pbUpdateFrame" method="get">' .
	we_html_tools::htmlDialogLayout($content, g_l('copyFolder', '[headline]') . ": " . we_base_util::shortenPath(id_to_path($cmd1, $cmd4), 46), $buttons) .
	'</form>' .
	'<iframe src="about:blank" name="pbUpdateFrame" style="width:0px;height:0px" id="pbUpdateFrame"></iframe></body></html>';
	return;
}

$bodyAttribs = ['style' => "background-color:#FFFFFF;margin:10px;"];
$fr = (we_base_request::_(we_base_request::BOOL, 'finish') ?
	new we_fragment_copyFolderFinish('we_copyFolderFinish', 1, 0, $bodyAttribs) :
	new we_fragment_copyFolder('we_copyFolder', 1, 0, $bodyAttribs)
	);
