<?php

/**
 * webEdition CMS
 *
 * $Rev: 10461 $
 * $Author: lukasimhof $
 * $Date: 2015-09-18 15:20:39 +0200 (Fr, 18 Sep 2015) $
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
class we_fileupload_ui_editor extends we_fileupload_ui_preview{
	protected $editorProperties = array(
		'formAttributes' => false,
		'formThumbnails' => false,
		'formImageEdit' => false,
		'isLayoutSmall' => false
	);
	protected $editorJS = array(
		'writebackTarget' => '',
		'editorCallback' => '',
	);
	protected $doImport = true;
	protected $contentType = array();

	public function __construct($contentType = array(), $extensions = '', $doImport = true){
		parent::__construct($contentType, $extensions);

		$this->dimensions['dragWidth'] = 376;
		$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, array(
			array('fu_file_parentID', 'int'),
		));
		$this->doImport = $doImport;

		if($this->doImport){
			$this->responseClass = 'we_fileupload_resp_import';
			if(!$this->contentType || $this->contentType === we_base_ContentTypes::IMAGE){
				$this->editorProperties['formAttributes'] = $this->editorProperties['formThumbnails'] = $this->editorProperties['formImageEdit'] = true;
				$this->moreFieldsToAppend = array_merge($this->moreFieldsToAppend, array(
					array('fu_doc_isSearchable', 'int'),
					array('fu_doc_thumbs[]', 'multi_select'),
					array('fu_doc_alt', 'text'),
					array('fu_doc_title', 'text'),
					array('fu_doc_width', 'int'),
					array('fu_doc_height', 'int'),
					array('fu_doc_widthSelect', 'int'),
					array('fu_doc_heightSelect', 'int'),
					array('fu_doc_keepRatio', 'int'),
					array('fu_doc_degrees', 'int'),
					array('fu_doc_quality', 'int'),
				));
			}
		} else {
			$this->responseClass = 'we_fileupload_resp_base';
		}
	}

	public function getCss() {
		$this->dimensions['dragWidth'] = 376;
		return parent::getCss() . we_html_element::cssElement('
			.paddingTop{
				padding-top: 12px;
			}
		');
	}

	public function getHtml($returnRows = false){
		$progress = new we_progressBar(0, true);
		$progress->setStudLen(200);
		$progress->setName('_fileupload');
		$divProgressbar = we_html_element::htmlDiv(array('id' => 'div_fileupload_progressBar', 'style' => 'display:none;'), $progress->getHTML());

		$divButtons = we_html_element::htmlDiv(array('id' => 'div_fileupload_buttons', 'style' => 'width:400px'),
			$this->getDivBtnInputReset($this->dimensions['inputWidth'] - 4)
		);

		$noImage = '<img style="margin:8px 18px;border-style:none;width:64px;height:64px;" src="/webEdition/images/icons/no_image.gif" alt="no-image" />';
		$formUploader = we_html_element::htmlDiv(array('id' => 'imageUpload', 'style' => 'display:block;width:384px;'),
			$this->getJs() .
			$this->getCss() .
			$this->getHiddens() .

			we_html_element::htmlDiv(array('style' => 'width:200px'),
				$divButtons
			) .
			we_html_element::htmlDiv(array('style' => 'width:200px'),
				we_html_element::htmlDiv(array('id' => 'div_fileupload_right', 'style'=>"position:relative;"),
					$this->getHtmlDropZone('preview', $noImage)
				)
			) .
			$divProgressbar
		);

		$parts = array(
			array('icon' => '', 'headline' => 'Dateiauswahl', 'html' => $formUploader, 'space' => 0, 'noline' => false, 'forceRightHeadline' => $this->editorProperties['isLayoutSmall']),
			array('icon' => '', 'headline' => '', 'html' => $this->getFormSameName(), 'space' => 0, 'noline' => false, 'forceRightHeadline' => $this->editorProperties['isLayoutSmall'])
		);

		if($this->parentID['setField']){
			$parts[] = array('icon' => '', 'headline' => g_l('importFiles', '[destination_dir]'), 'html' => $this->getFormParentID(), "class" => 'paddingTop', 'noline' => true, 'forceRightHeadline' => $this->editorProperties['isLayoutSmall']);
		}

		if($this->doImport){
			$parts[] = array('icon' => '', 'headline' => 'Metadaten', 'html' => $this->getFormImportMeta(), 'space' => 0, 'noline' => false, 'forceRightHeadline' => $this->editorProperties['isLayoutSmall']);
			$parts[] = array('icon' => '', 'headline' => 'Dokument', 'html' => $this->getFormIsSearchable(), 'space' => 0, 'noline' => true, 'forceRightHeadline' => $this->editorProperties['isLayoutSmall']);
			if(!$this->contentType || $this->contentType === we_base_ContentTypes::IMAGE){
				$parts = $this->editorProperties['formAttributes'] ? array_merge($parts, $this->getFormImageAttributes()) : $parts;
				$parts = $this->editorProperties['formThumbnails'] ? array_merge($parts, $this->getFormThumbnails()) : $parts;
				$parts = $this->editorProperties['formImageEdit'] ? array_merge($parts, $this->getFormImageEdit()) : $parts;
			}
		}

	$box = we_html_multiIconBox::getHTML("", $parts, 20, '', 3, g_l('importFiles', '[image_options_open]'), g_l('importFiles', '[image_options_close]'), false);
		$divBtnUpload = we_html_element::htmlDiv(array('style' => 'float:right;padding-top:10px;width:auto;'), $this->getDivBtnUploadCancel(170));

		return $returnRows ? $parts : we_html_multiIconBox::getJS() . $box . ($this->isExternalBtnUpload ? '' : $divBtnUpload);
	}

	public function getHtmlFooter(){
		return we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'width:auto; height:100%;'),
			we_html_element::htmlDiv(array('style' => 'float:right'),
				(we_html_element::htmlDiv(array('style' => 'display:table-cell;'), $this->getDivBtnUploadCancel(170))) .
				we_html_element::htmlDiv(array('style' => 'display:table-cell;'), we_html_button::create_button(we_html_button::CLOSE, 'javascript:top.close()'))
			)
		);
	}

	public function getEditorJS(){ // TODO: move this JS to weFileupload.js!!
		return we_html_element::jsScript(JS_DIR . 'weFileUpload.js') .
			we_html_element::jsElement('
predefinedCallback = "' . $this->editorJS['predefinedCallback'] . '";
customCallback = function(importedDocument){' . $this->editorJS['customCallback'] . '};
writebackTarget = "' . $this->editorJS['writebackTarget'] . '";

doOnImportSuccess = function(importedDocument){
	if(writebackTarget){
		documentWriteback(importedDocument);
	}

	switch(predefinedCallback){
		case "selector":
			opener.top.reloadDir();
			opener.top.unselectAllFiles();
			opener.top.addEntry(importedDocument.id, "noch nichts", false, "importedDocument.path");
			opener.top.doClick(importedDocument, 0);
			setTimeout(function () {
					opener.top.selectFile(importedDocument);
				}, 200);
			reloadMainTree();
			setTimeout(self.close, 250);
			break;
		case "sselector":
			opener.top.fscmd.selectDir();
			opener.top.fscmd.selectFile(importedDocument.text);
			self.close();
		case "weimg":
			/*
			var ed = WE().layout.weEditorFrameController.getVisibleEditorFrame();
			ed.setScrollTo();
			ed._EditorFrame.setEditorIsHot(true);
			//var t = opener.top;
			we_cmd("reload_editpage","jux","change_image");
			//t.hot = 1;
			//setTimeout(self.close, 250);
			*/
			break;
		case "imagedialog":
			alert("import done: " + importedDocument.path + " (id: " + importedDocument.id + ")");
			we_FileUpload.reset();
			document.we_form.elements["radio_type"][0].checked=true;
			document.getElementById("imageInt").style.display="block";
			document.getElementById("imageExt").style.display="none";
			document.getElementById("imageUpload").style.display="none";
			document.getElementById("yuiAcResultImage").value = importedDocument.id;
			document.getElementById("yuiAcInputImage").value = importedDocument.path;
			imageChanged();

			break;
		default:
			// do nothing
	}

	customCallback(importedDocument);
}

reloadMainTree = function (table) {
	try {
		top.opener.top.we_cmd(\'load\', \'tblFile\');
	} catch (e) {
		//
	}
};

documentWriteback = function(importedDocument){
	' . ($this->editorJS['writebackTarget'] ? 'WE().layout.weEditorFrameController.getVisibleEditorFrame().' . $this->editorJS['writebackTarget'] . ' = importedDocument.id;' : '//no writeback') . '
}
');
	}

	protected function getFormIsSearchable(){
		return we_html_element::htmlDiv(array(), we_html_forms::checkboxWithHidden(true, 'fu_doc_isSearchable', g_l('weClass', '[IsSearchable]'), false, 'defaultfont', ''));
	}

	protected function getFormSameName(){
		return we_html_element::htmlDiv(array('style' => 'margin:10px 0 0 0;'),
			we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[sameName_expl]'), we_html_tools::TYPE_INFO, 380) .
			we_html_element::htmlDiv(array('style' => 'margin-top:10px'), //g_l('newFile', '[caseFileExists]') . '<br/>' .
				we_html_forms::radiobutton('overwrite', false, "sameName", g_l('importFiles', '[sameName_overwrite]'), false, "defaultfont", 'document.we_form.fu_file_sameName.value=this.value;') .
				we_html_forms::radiobutton('rename', true, "sameName", g_l('importFiles', '[sameName_rename]'), false, "defaultfont", 'document.we_form.fu_file_sameName.value=this.value;') .
				we_html_forms::radiobutton('nothing', false, "sameName", g_l('importFiles', '[sameName_nothing]'), false, "defaultfont", 'document.we_form.fu_file_sameName.value=this.value;')
			) .
			we_html_tools::hidden('fu_file_sameName', 'rename')
		);
	}

	protected function getFormParentID(){
			if(!$this->parentID['setFixed'] && is_numeric($this->parentID['preset'])){
				$yuiSuggest = &weSuggest::getInstance();
				$cmd1 = "document.we_form.fu_file_parentID.value";
				$wecmdenc2 = we_base_request::encCmd("document.we_form.fu_file_parentID.value");
				$wecmdenc3 = ''; //we_base_request::encCmd();
				$startID = $this->parentID['preset'] !== false ? $this->parentID['preset'] : (IMAGESTARTID_DEFAULT ? : 0);
				$but = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd1 . ",'" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "',''," . $startID . ",'" . we_base_ContentTypes::FOLDER . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");
				$yuiSuggest->setAcId("fu_file_parentID");
				$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
				$yuiSuggest->setInput("fu_file_parentID", $startID ? id_to_path($startID, FILE_TABLE) : '/', '', false);
				$yuiSuggest->setMaxResults(10);
				$yuiSuggest->setMayBeEmpty(true);
				$yuiSuggest->setResult("fu_file_parentID", $startID);
				$yuiSuggest->setSelector(weSuggest::DirSelector);
				$yuiSuggest->setWidth(320);
				$yuiSuggest->setSelectButton($but);

				return $yuiSuggest->getHTML();
			} else {
				if(is_numeric($this->parentID['preset'])){
					$id = $this->parentID['preset'];
					$path = id_to_path($this->parentID['preset']);
				} else {
					$id = path_to_id($this->parentID['preset']);
					$path = $this->parentID['preset'];
				}

				return we_html_element::htmlInput(array('value' => $path, 'disabled' => 'disabled')) .
					we_html_button::create_button(we_html_button::SELECT, '', '', '', '', '', '', true) .
					we_html_element::htmlHiddens(array(
						'fu_file_parentID' => $id,
					));
			}
	}

	protected function getFormThumbnails(){
		$_thumbnails = new we_html_select(array('multiple' => 'multiple', 'name' => 'fu_doc_thumbs[]', 'id' => 'fu_doc_thumbs', 'class' => 'defaultfont', 'size' => 6, 'style' => 'width: 330px;'));
		$DB_WE = new DB_WE();
		$DB_WE->query('SELECT ID,Name,description FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');

		$_enabled_buttons = false;
		while($DB_WE->next_record()){
			$_enabled_buttons = true;
			$_thumbnails->addOption($DB_WE->f('ID'), $DB_WE->f('Name'), array('title' => $DB_WE->f('description')));
		}

		return array(array('icon' => '', 'headline' => g_l('thumbnails', '[create_thumbnails]'), 'html' => $_thumbnails->getHtml(), "class" => 'paddingTop', 'noline' => $this->editorProperties['isLayoutSmall'], 'forceRightHeadline' => $this->editorProperties['isLayoutSmall']));
	}

	protected function getFormImageAttributes(){
		$html = we_html_element::htmlDiv(array(), we_html_element::htmlLabel(array(), 'Alternativ Text') . '<br>' . we_html_tools::htmlTextInput('fu_doc_alt', 24, '', '', '', 'text', 330)) .
				we_html_element::htmlDiv(array(), we_html_element::htmlLabel(array(), 'Titel') . '<br>' . we_html_tools::htmlTextInput('fu_doc_title', 24, '', '', '', 'text', 330));

		return array(array('icon' => '', 'headline' => 'Attribute', 'html' => $html, "class" => 'paddingTop', 'noline' => $this->editorProperties['isLayoutSmall'], 'forceRightHeadline' => $this->editorProperties['isLayoutSmall']));
	}

	protected function getFormImageEdit(){
		$parts = array();
		$widthInput = we_html_tools::htmlTextInput("fu_doc_width", 10, '', "", '', "text", 60);
		$heightInput = we_html_tools::htmlTextInput("fu_doc_height", 10, '', "", '', "text", 60);

		$widthSelect = '<select size="1" class="weSelect" name="fu_doc_widthSelect"><option value="pixel" selected="selected">' . g_l('weClass', '[pixel]') . '</option><option value="percent">' . g_l('weClass', '[percent]') . '</option></select>';
		$heightSelect = '<select size="1" class="weSelect" name="fu_doc_heightSelect"><option value="pixel" selected="selected">' . g_l('weClass', '[pixel]') . '</option><option value="percent">' . g_l('weClass', '[percent]') . '</option></select>';

		$ratio_checkbox = we_html_forms::checkboxWithHidden(false, 'fu_doc_keepRatio', g_l('thumbnails', '[ratio]'), false, 'defaultfont', '');

		$_resize = '<table>
<tr>
<td class="defaultfont">' . g_l('weClass', '[width]') . ':</td>
<td>' . $widthInput . '</td>
<td>' . $widthSelect . '</td>
</tr>
<tr>
<td class="defaultfont">' . g_l('weClass', '[height]') . ':</td>
<td>' . $heightInput . '</td>
<td>' . $heightSelect . '</td>
</tr>
<tr>
<td colspan="3">' . $ratio_checkbox . '</td>
</tr>
</table>';
		$parts[] = array("headline" => g_l('weClass', '[resize]'), "html" => $_resize, "class" => 'paddingTop', 'noline' => $this->editorProperties['isLayoutSmall'], 'forceRightHeadline' => $this->editorProperties['isLayoutSmall']);

		$_radio0 = we_html_forms::radiobutton(0, true, "fu_doc_degrees", g_l('weClass', '[rotate0]'));
		$_radio180 = we_html_forms::radiobutton(180, false, "fu_doc_degrees", g_l('weClass', '[rotate180]'));
		$_radio90l = we_html_forms::radiobutton(90, false == 90, "fu_doc_degrees", g_l('weClass', '[rotate90l]'));
		$_radio90r = we_html_forms::radiobutton(270, false == 270, "fu_doc_degrees", g_l('weClass', '[rotate90r]'));

		$parts[] = array(
			"headline" => g_l('weClass', '[rotate]'),
			"html" => $_radio0 . $_radio180 . $_radio90l . $_radio90r,
			"class" => 'paddingTop',
			'noline' => $this->editorProperties['isLayoutSmall'],
			'forceRightHeadline' => $this->editorProperties['isLayoutSmall']
		);

		$parts[] = array(
			"headline" => g_l('weClass', '[quality]'),
			"html" => we_base_imageEdit::qualitySelect("fu_doc_quality", 8),
			"class" => 'paddingTop',
			'noline' => $this->editorProperties['isLayoutSmall'],
			'forceRightHeadline' => $this->editorProperties['isLayoutSmall']
		);

		return $parts;
	}

	public function setEditorJS($editorJS = array()){
		$this->editorJS = array_merge($this->editorJS, $editorJS);
	}

	public function setEditorProperties($editorProperties = array()){
		$this->editorProperties = array_merge($this->editorProperties, $editorProperties);
	}

	public function setDoImport($doImport = true){
		$this->doImport = $doImport;
		$this->responseClass = $this->doImport ? 'we_fileupload_resp_import' : 'we_fileupload_resp_base';

	}


}

