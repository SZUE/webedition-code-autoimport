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
class we_fileupload_ui_importer extends we_fileupload_ui_base {
	protected $dimensions = array(
		'width' => 400,
		'dragHeight' => 90,
		'dragWidth' => 320,
		'progressWidth' => 90,
		'alertBoxWidth' => 500,
		'marginTop' => 0,
		'marginBottom' => 0
	);

	public function __construct($name){
		parent::__construct($name);
		$this->responseClass = 'we_fileupload_resp_multiimport';

		$this->type = 'importer';
		$this->doCommitFile = true;
		$this->isGdOk = we_base_imageEdit::gd_version() > 0;
		$this->internalProgress = array(
			'isInternalProgress' => true,
			'width' => 100
		);
		$this->externalProgress = array(
			'isExternalProgress' => true,
			'create' => false
		);
		$this->fileTable = FILE_TABLE;
		$this->footerName = 'imgimportbuttons';
		$this->contentName = 'imgimportcontent';
	}

	public function getCss(){
		return we_html_element::cssLink(CSS_DIR . 'we_fileupload.css');
	}

	public function getHTML($hiddens = ''){
		$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;
		$alert = we_html_element::htmlHiddens(array(
				'we_cmd[0]' => 'import_files',
				'cmd' => 'content',
				'step' => 2
			)) .
			we_html_element::htmlDiv(array('id' => 'desc'), we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[import_expl_js]') . '<br/><br/>' . ($this->maxUploadSizeMBytes == 0 ? g_l('importFiles', '[import_expl_js_no_limit]') : sprintf(g_l('importFiles', '[import_expl_js_limit]'), $this->maxUploadSizeMBytes)), we_html_tools::TYPE_INFO, 568, false, 20));

		$topParts = array(
			array("headline" => "",
				"html" => $alert)
		);

		$butBrowse = str_replace(array("\n\r", "\r\n", "\r", "\n"), "", $isIE10 ? we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', true, 80, we_html_button::HEIGHT, '', '', false, false, '_btn') :
				we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', true, 281, we_html_button::HEIGHT, '', '', false, false, '_btn', false, '', 'importerBrowseHarddisk'));
		$butReset = str_replace(array("\n\r", "\r\n", "\r", "\n"), "", we_html_button::create_button('reset', 'javascript:we_FileUpload.reset()', true, ($isIE10 ? 84 : 100), we_html_button::HEIGHT, '', '', true, false, '_btn'));
		// TODO: get fileselect from parent!
		$fileselect = '
		<form id="filechooser" action="" method="" enctype="multipart/form-data">
		<div style="float:left;">
			<div>
				<div class="we_fileInputWrapper" id="div_' . $this->name . '_fileInputWrapper">
					<input class="fileInput fileInputHidden' . ($isIE10 ? ' fileInputIE10' : '') . '" type="file" id="' . $this->name . '" name="fileselect[]" multiple="multiple" />
					' . $butBrowse . '
				</div>
				<div style="vertical-align: top; display: inline-block; height: 22px">
					' . $butReset . '
				</div>
				<div class="we_file_drag" id="div_' . $this->name . '_fileDrag" ' . ($isIE10 || we_base_browserDetect::isOpera() ? 'style="display:none;"' : 'style="display:block;width:316px;height:88px;padding-top:40px"') . '>' . g_l('importFiles', '[dragdrop_text]') . '</div>
			</div>
		</div>
		<div style="position:absolute; left: 370px; padding-top: 10px">' . we_fileupload_ui_preview::getFormImageEditClientside(true, false, 'we_FileUpload.reeditImage(null, 0, true);') . '</div>' .
		'</form>';

		$topParts[] = array("html" => $fileselect, 'space' => 0);

		// TODO: finish GUI of message layer, throw out inline css and use we_html_element
		$messageLayer = '
			<div id="we_fileUpload_messageBg" style="display:none;position:absolute;top:0; left:0; right:0; bottom:0; background-color:gray;opacity: 0.8;"></div>
			<div id="we_fileUpload_message" style="display:none;padding: 20px; background-color:white; width: 400px; height: 180px;position:absolute; top:70px;left:100px;opacity:1.0">
			<p>Please wait: <span id="we_fileUpload_messageNr"></span> images left to process</p><p>(add progressbar here)</p>
			<p><i class="fa fa-2x fa-spinner fa-pulse"></i></p></div>
		';

		$content = we_html_element::htmlDiv(array("id" => "forms", "style" => "display:block"), we_html_element::htmlForm(array(
					"action" => WEBEDITION_DIR . "we_cmd.php",
					"name" => "we_startform",
					"method" => "post"
					), $hiddens) .
				'<div style="overflow:hidden; padding-bottom: 10px">' . we_html_multiIconBox::getHTML("selectFiles", $topParts, 30, "", -1, "", "", "", g_l('importFiles', '[step2]'), "", 0, "hidden") . '</div>' .
				'<div id="div_upload_files" style="height:410px; width: 100%; overflow:auto">' . we_html_multiIconBox::getHTML("uploadFiles", array(), 30, "", -1, "", "", "", "") . '</div>' .
				$messageLayer
		);

		return we_html_element::htmlBody(array("class" => "weDialogBody"), $content);
	}

	//TODO: add param filetype
	public static function getBtnImportFiles($parentID = 0, $callback = '', $text = ''){
		return we_html_button::create_button('fa:' . ($text ? : 'btn_import_files') . ',fa-lg fa-upload', "javascript:top.we_cmd('import_files','" . $parentID . "', '" . $callback . "')", true, 62);
	}

	protected function _getHtmlFileRow(){
		$btnTable = new we_html_table(array('class' => 'default') , 1, 2);
		$btnTable->addCol(we_html_button::create_button(we_html_button::TRASH, "javascript:we_FileUpload.deleteRow(WEFORMNUM,this);"));
		$btnTable->setCol(0, 0, array(), we_html_button::create_button(we_html_button::TRASH, "javascript:we_FileUpload.deleteRow(WEFORMNUM,this);"));
		$divBtnSelect = we_html_element::htmlDiv(
			array('class' => 'fileInputWrapper', 'style' => 'overflow:hidden;vertical-align: bottom; display: inline-block;'),
			we_html_element::htmlInput(array('type' => 'file', 'id' => 'fileInput_uploadFiles_WEFORMNUM', 'class' => 'fileInput fileInputList fileInputHidden elemFileinput')) .
			we_html_button::create_button('fa:, fa-lg fa-hdd-o', 'javascript:void(0)')
		);
		$btnTable->setCol(0, 1, array(), $divBtnSelect);

		// TODO: replace by we_progressBar
		$progressbar = '<table class="default"><tbody><tr>
			<td style="vertical-align:middle"><div class="progress_image" style="width:0px;height:10px;" id="' . $this->name . '_progress_image_WEFORMNUM" style="vertical-align:top"></div><div class="progress_image_bg" style="width:100px;height:10px;" id="' . $this->name . '_progress_image_bg_WEFORMNUM" style="vertical-align:top"></div></td>
			<td class="small bold" style="width:3em;color:#006699;padding:6px 0 0 8px;" id="span_' . $this->name . '_progress_text_WEFORMNUM">0%</td>
			<td><span id="alert_img_WEFORMNUM" style="visibility:hidden;" class="fa-stack fa-lg" style="color:#F2F200;" title=""><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span></td>
		</tr></tbody></table>';

		/*
		$progress = new we_progressBar(0, true);
		$progress->setStudLen(100);
		$progress->setProgressTextPlace(0);
		$progress->setName('_WEFORMNUM');
		$progressbar =  $progress->getHTML('', 'font-size:11px;');
		 * 
		 */

		$btnPreview = we_html_element::htmlDiv(array('class' => 'btnRefresh'), we_html_button::create_button(we_html_button::VIEW, "javascript:we_FileUpload.openImageEditor(WEFORMNUM);", true, 0, 0, '', '', false, true, '', false, $title = 'Vollansicht'));

		$divWhatOptions = we_html_element::htmlDiv(array('class' => 'elemWhatOpts'),
			we_html_element::htmlDiv(array(), we_html_forms::checkbox(0, true, 'useGeneralOpts', g_l('importFiles', '[edit_useGlobalOpts]'), true, 'defaultfont', 'we_FileUpload.setUseGeneralOpts(this);')) .
			we_html_element::htmlDiv(array(), we_html_forms::radiobutton('custom', true, 'editOpts', g_l('importFiles', '[edit_useCustom]'), true, 'defaultfont', 'we_FileUpload.setCustomEditOpts(this.form);', true)) .
			we_html_element::htmlDiv(array(), we_html_forms::radiobutton('expert', false, 'editOpts', g_l('importFiles', '[edit_useExpert]'), true, 'defaultfont', 'we_FileUpload.setCustomEditOpts(this.form);', true))
		);
		$valueInput = we_html_tools::htmlTextInput('resizeValue', 11, '', '', '', "text", 57, 20, '', true);
		$unitSelect = we_html_tools::htmlSelect('unitSelect', array(
				'percent' => g_l('weClass', '[percent]'),
				'pixel_w' => g_l('importFiles', '[edit_pixel_width]'),
				'pixel_h' => g_l('importFiles', '[edit_pixel_height]')
			), 1, 0, false, array('disabled' => 'disabled'), '', 0, 'weSelect optsUnitSelect');
		$rotateSelect = we_html_tools::htmlSelect('rotateSelect', array(
				0 => g_l('weClass', '[rotate0]'),
				180 => g_l('weClass', '[rotate180]'),
				270 => g_l('weClass', '[rotate90l]'),
				90 => g_l('weClass', '[rotate90r]'),
			), 1, 0, false, array('disabled' => 'disabled'), '', 0, 'weSelect optsRotateSelect');
		$quality = we_html_element::htmlInput(array('class' => 'optsQuality', 'type' => 'range', 'title' => 'test', 'value' => 90, 'min' => 0, 'max' => 100, 'step' => 5, 'oninput' => 'this.form.qualityOutput.value = this.value', 'name' => 'quality'));
		$qualityOutput = we_html_baseElement::getHtmlCode(new we_html_baseElement('output', true, array('name' => 'qualityOutput', 'for' => "fu_doc_quality"), 90));
		$inputs = we_html_element::htmlDiv(array('class' => 'optsRowTop'), $valueInput . ' ' .  $unitSelect) . we_html_element::htmlDiv(array('class' => 'optsRowMiddle'), $rotateSelect) . we_html_element::htmlDiv(array('class' => 'optsRowBottom'), $quality . $qualityOutput);
		$divEditCustom = we_html_element::htmlDiv(array('class' => 'elemOpts'), $inputs);//$btnRefresh
		$btnRefresh = we_html_element::htmlDiv(array('class' => 'btnRefresh'), we_html_button::create_button(we_html_button::REFRESH_NOTEXT, "javascript:if(!this.form.useGeneralOpts.checked && this.form.editOpts){we_FileUpload.reeditImage(null, WEFORMNUM);}", true, 0, 0, '', '', true, true, '', false, $title = 'Ausf�hren'));

		return str_replace(array("\r", "\n"), "", we_html_element::htmlDiv(array('class' => 'importerElem'), we_html_element::htmlDiv(array('class' => 'weMultiIconBoxHeadline elemNum'), 'Nr. WE_FORM_NUM') .
		we_html_element::htmlDiv(array('class' => 'elemContainer'),
			we_html_element::htmlDiv(array('id' => 'preview_uploadFiles_WEFORMNUM', 'class' => 'weFileUploadEntry_preview elemPreview'), 
				we_html_element::htmlDiv(array('class' => 'elemPreviewPreview')) .
				we_html_element::htmlDiv(array('class' => 'elemPreviewBtn'), $btnPreview)
			) .
			we_html_element::htmlDiv(array('id' => 'icon_uploadFiles_WEFORMNUM', 'class' => 'weFileUploadEntry_icon elemIcon')) .

			we_html_element::htmlDiv(array('class' => 'elemContent'), 
				we_html_element::htmlDiv(array('class' => 'elemContentTop'), 
					we_html_element::htmlDiv(array('id' => 'name_uploadFiles_WEFORMNUM', 'class' => 'elemFilename'), 'FILENAME') .
					we_html_element::htmlDiv(array('id' => 'div_rowButtons_WEFORMNUM', 'class' => 'elemContentTopRight'),
						we_html_element::htmlDiv(array('id' => 'size_uploadFiles_WEFORMNUM', 'class' => 'weFileUploadEntry_size elemSize'), 'FILESIZE') .
						we_html_element::htmlDiv(array('class' => 'elemButtons'), $btnTable->getHtml())
					) .
					we_html_element::htmlDiv(array('id' => 'div_rowProgress_WEFORMNUM', 'class' => 'elemProgress'), $progressbar)
				) .
				we_html_element::htmlDiv(array('id' => 'editoptions_uploadFiles_WEFORMNUM', 'class' => 'weFileUploadEntry_editoption elemContentBottom'), we_html_element::htmlForm(array('id' => 'form_editOpts_WEFORMNUM', 'data-index' => 'WEFORMNUM'), $divWhatOptions . $divEditCustom . $btnRefresh))
			)
		)));
		
	}

}
