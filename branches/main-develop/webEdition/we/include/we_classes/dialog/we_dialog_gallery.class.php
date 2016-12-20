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
class we_dialog_gallery extends we_dialog_base{

	function __construct($noInternals = false){
		parent::__construct();
		$this->changeableArgs = ['collid',
			'tmpl',
			'templateIDs'
		];
		$this->JsOnly = true;
		$this->dialogTitle = g_l('wysiwyg', '[addGallery]');
		$this->noInternals = $noInternals;
		$this->defaultInit();
	}

	function defaultInit(){
		$this->args['collid'] = 0;
		$this->args['tmpl'] = 0;
		$this->args['templateIDs'] = '';
	}

	protected function getJs(){
		return parent::getJs() .
			we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/wegallery/js/gallery_init.js');
	}

	function getOkJs(){
		return '
WegalleryDialog.insert();
';
	}

	function getDialogContentHTML(){
		$textname = 'we_targetname';
		$idname = 'we_dialog_args[collid]';
		$weSuggest = & weSuggest::getInstance();
		$weSuggest->setAcId('ID');
		$weSuggest->setContentType(we_base_ContentTypes::COLLECTION);
		$weSuggest->setInput($textname, !empty($this->args['collid']) ? id_to_path($this->args['collid'], VFILE_TABLE) : '');
		$weSuggest->setMaxResults(4);
		$weSuggest->setResult($idname, isset($this->args['collid']) ? $this->args['collid'] : 0);
		$weSuggest->setSelector(weSuggest::DocSelector);
		$weSuggest->setTable(VFILE_TABLE);

		$weSuggest->setCheckFieldValue(false);
		$weSuggest->setNoAutoInit(true);

		$weSuggest->setWidth(234);
		$cmd1 = 'top.document.we_form.elements["' . $idname . '"].value';
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . VFILE_TABLE . "','" . $idname . "','" . $textname . "','','',0)"), 4);
		$weSuggest->setOpenButton(we_html_button::create_button(we_html_button::EDIT, "javascript:if(" . $cmd1 . "){WE().layout.weEditorFrameController.openDocument('" . VFILE_TABLE . "'," . $cmd1 . ",'" . we_base_ContentTypes::COLLECTION . "'); return false}"));
		$weSuggest->setAdditionalButton(we_html_button::create_button('fa:btn_add_collection,fa-plus,fa-lg fa-archive', "javascript:top.we_cmd('edit_new_collection','write_back_to_opener," . $idname . "," . $textname . "','',-1,'" . stripTblPrefix(FILE_TABLE) . "', 'wegallery');", '', 0, 0, "", "", false, false), 4);

		$btnTrash = we_html_button::create_button(we_html_button::TRASH, "javascript:" . $cmd1 . "=0;document.we_form.elements['" . $textname . "'].value='';document.we_form.elements['we_dialog_args[tmpl]'].value='0'");

		$collid = we_html_tools::htmlFormElementTable($weSuggest->getHTML(), 'Sammlung');

		if(($tempArr = id_to_path(isset($this->args['templateIDs']) ? $this->args['templateIDs'] : '', TEMPLATES_TABLE, null, true))){
			$templatesArr = ['----'];
			foreach($tempArr as $k => $v){
				$templatesArr[$k] = $v;
			}
			$input = we_html_tools::htmlSelect('we_dialog_args[tmpl]', $templatesArr, 1, (isset($this->args['tmpl']) ? id_to_path($this->args['tmpl'], TEMPLATES_TABLE) : '----'), false, [], '', 430);
		} else {
			$input = we_html_element::htmlHidden('we_dialog_args[tmpl]', 0) .
				we_html_tools::htmlAlertAttentionBox(g_l('wysiwyg', '[gallery_alert_no_template]'), we_html_tools::TYPE_ALERT, 410, false);
		}
		$tmpl = we_html_tools::htmlFormElementTable($input, 'Template');

		/*
		  $trash = '<table class="default">
		  <tbody>
		  <tr><td class="defaultfont" style="text-align:left" colspan="1"></td></tr>
		  <tr style="height:1px"><td colspan="1">&nbsp;</td></tr>
		  <tr style="height:1px"><td colspan="1"></td></tr>
		  <tr><td style="padding-left:12px;">' . $btnTrash . '
		  </td></tr>
		  </tbody>
		  </table>';
		 *
		 */
		$trash = '';

		$html = '<table class="default">
<tr><td style="padding-bottom:10px;">' . $collid . '</td><td>' . $trash . '</td></tr>
<tr><td style="padding-bottom:24px;">' . $tmpl . '</td></tr>
<tr><td>' . $btnTrash . ' Gallerie entfernen</td></tr>
</table>';

		return $html;
	}

}
