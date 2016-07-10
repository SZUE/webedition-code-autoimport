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
		$this->cliensideImageEditing = true;
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

		$butBrowse = str_replace(array("\n\r", "\r\n", "\r", "\n"), "", $isIE10 ? we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', true, 0, 0, '', '', false, false, '_btn') :
				we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', true, 0, 0, '', '', false, false, '_btn', false, '', 'importerBrowseHarddisk'));
		$butReset = str_replace(array("\n\r", "\r\n", "\r", "\n"), "", we_html_button::create_button('reset', 'javascript:we_FileUpload.reset()', true, 0, 0, '', '', true, false, '_btn'));
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
		</div>' .
		(we_fileupload::EDIT_IMAGES_CLIENTSIDE ? '<div style="position:absolute; left: 370px; padding-top: 10px">' . we_fileupload_ui_preview::getFormImageEditClientside(true, false) . '</div>' : '') .
		'</form>';

		$topParts[] = array("html" => $fileselect, 'space' => 0);

		// TODO: finish GUI
		$divMask = we_html_element::htmlDiv(array('id' => 'we_fileUploadImporter_mask', 'class' => 'editorMask'));
		$divBusyMessage = we_html_element::htmlDiv(array('id' => 'we_fileUploadImporter_busyMessage', 'class' => 'editorMessage'),
			we_html_element::htmlDiv(array('class' => 'we_file_drag_maskSpinner'), '<i class="fa fa-2x fa-spinner fa-pulse"></i>') .
			we_html_element::htmlDiv(array('id' => 'we_fileUploadImporter_busyText', 'class' => 'we_file_drag_maskBusyText'))
		);
		$loupe = we_fileupload_ui_preview::getHtmlLoup();

		$content = we_html_element::htmlDiv(array('id' => 'forms', 'class' => 'fileuploadImporter', 'style' => 'display:block'), we_html_element::htmlForm(array(
					'action' => WEBEDITION_DIR . 'we_cmd.php',
					'name' => 'we_startform',
					'method' => 'post'
					), $hiddens) .
				'<div style="overflow:hidden; padding-bottom: 10px">' . we_html_multiIconBox::getHTML("selectFiles", $topParts, 30, "", -1, "", "", "", g_l('importFiles', '[step2]'), "", 0, "hidden") . '</div>' .
				'<div id="div_upload_files" style="height:410px; width: 100%; overflow:auto">' . we_html_multiIconBox::getHTML("uploadFiles", array(), 30, "", -1, "", "", "", "") . '</div>' .
				$divMask .
				$loupe .
				$divBusyMessage
		);
//<div id="imgfocus_point" style="display:none;" draggable="false"></div>
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

		$quality = we_html_element::htmlDiv(array('class' => 'qualityInputContainer'),
				we_html_element::htmlInput(array('disabled' => true, 'class' => 'optsQuality', 'type' => 'range', 'title' => 'test', 'value' => 100, 'min' => 10, 'max' => 100, 'step' => 5, 'oninput' => 'this.parentNode.nextSibling.innerHTML = this.value', 'name' => 'fuOpts_quality')) .
				we_html_element::htmlDiv(array('class' => 'qualityValueContainer'), 100)
			);

		$optsCheckbox = we_html_forms::checkbox(0, false, 'fuOpts_useCustomOpts', 'Eigene Einstellungen'/* . g_l('importFiles', '[edit_useGlobalOpts]')*/, true, 'defaultfont');

		$scaleValue = we_html_tools::htmlTextInput('fuOpts_scale', 11, '', '', 'class="optsScaleInput optsScaleInput_row"', "text", 0, 0, '', true);
		$scaleWhatSelect = we_html_tools::htmlSelect('fuOpts_scaleWhat', array(
				'pixel_l' => g_l('importFiles', '[edit_pixel_longest]'),
				'pixel_w' => g_l('importFiles', '[edit_pixel_width]'),
				'pixel_h' => g_l('importFiles', '[edit_pixel_height]'),
			), 1, 0, false, array('disabled' => 'disabled'), '', 0, 'weSelect optsUnitSelect');
		$scalePropositions = we_html_tools::htmlSelect('fuOpts_scaleProps', 
				array('' => '', 320 => 320, 640 => 640, 1280 => 1280, 1440 => 1440, 1600 => 1600, 1920 => 1920, 2560 => 2560),
					1, 0, false, array('disabled' => 'disabled'), '', '', 'weSelect optsScalePropositions');
		$scaleHelp = we_html_element::htmlDiv(array('data-index' => 'WEFORMNUM', 'class' => 'optsRowScaleHelp'), '<span class="fa-stack alertIcon" style="color:black;"><i class="fa fa-question-circle" ></i></span>' . we_html_element::htmlDiv(array('class' => 'optsRowScaleHelpText')));
		$divOptsScale = we_html_element::htmlDiv(array('class' => 'optsRowScale'), $scaleWhatSelect . ' ' . $scalePropositions . $scaleValue) . $scaleHelp;

		$qualitySlide = we_html_element::htmlInput(array('disabled' => true, 'class' => 'optsQualitySlide', 'type' => 'range', 'title' => 'test', 'value' => 100, 'min' => 10, 'max' => 100, 'step' => 5, 'name' => 'fuOpts_quality'));
		$qualityValue = we_html_element::htmlDiv(array('class' => 'optsQualityValue qualityValueContainer'), 100);
		$qualityBox = we_html_element::htmlDiv(array('class' => 'optsQualityBox'), $qualitySlide . $qualityValue);
		$divOptsQuality = we_html_element::htmlDiv(array('class' => 'optsRowQuality'), $qualityBox);

		$rotateSelect = we_html_tools::htmlSelect('fuOpts_rotate', array(
				0 => g_l('weClass', '[rotate0]'),
				180 => g_l('weClass', '[rotate180]'),
				270 => g_l('weClass', '[rotate90l]'),
				90 => g_l('weClass', '[rotate90r]'),
			), 1, 0, false, array('disabled' => 'disabled'), '', 0, 'weSelect optsRotateSelect');
		$divOptsRotation = we_html_element::htmlDiv(array('class' => 'optsRowRotate'), $rotateSelect);

		$divOptionsLeft = we_html_element::htmlDiv(array('class' => 'optsLeft'),
			we_html_element::htmlDiv(array('class' => 'optsLeftTop'), $optsCheckbox) .
			we_html_element::htmlDiv(array('class' => 'optsLeftBottom'), $divOptsRotation)
		);
		$divOptopnsRight = we_html_element::htmlDiv(array('class' => 'optsRight' . (we_base_browserDetect::isIE() ? ' optsRightIE' : '')),
			we_html_element::htmlDiv(array('class' => 'optsRightTop'), $divOptsScale) .
			we_html_element::htmlDiv(array('class' => 'optsRightBottom'), $divOptsQuality)
		);
		$divBtnRefresh = we_html_element::htmlDiv(array('class' => 'btnRefresh'), we_html_button::create_button(we_html_button::MAKE_PREVIEW, "javascript:", true, 0, 0, '', '', false, true, '', false, $title = 'Bearbeitungsvorschau erstellen', 'weFileupload_btnImgEditRefresh rowBtnProcess'));

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
				we_html_element::htmlDiv(array('id' => 'editoptions_uploadFiles_WEFORMNUM', 'class' => 'weFileUploadEntry_editoption elemContentBottom'), 
					we_html_element::htmlForm(array('id' => 'form_editOpts_WEFORMNUM', 'data-type' => 'importer_rowForm', 'data-index' => 'WEFORMNUM'),
							$divOptionsLeft . $divOptopnsRight . $divBtnRefresh
					)
				) .
				we_html_element::htmlDiv(array('class' => 'elemContentMask'),
					we_html_element::htmlDiv(array('class' => 'we_file_drag_maskSpinner'), '<i class="fa fa-2x fa-spinner fa-pulse"></i></span>') .
					we_html_element::htmlDiv(array('id' => 'image_edit_mask_text', 'class' => 'we_file_drag_maskBusyText'))
				) . we_html_element::htmlDiv(array('id' => 'image_edit_done_WEFORMNUM', 'class' => 'elemContentDone')))
		)));

	}

	protected function _getHtmlFileRow_legacy(){
		return str_replace(array("\r", "\n"), "", '<table class="default importer_files"><tbody><tr height="28">
			<td class="weMultiIconBoxHeadline" style="width:55px;padding-left:45px;padding-right:20px;" >' . g_l('importFiles', '[file]') . '&nbsp;<span id="headline_uploadFiles_WEFORMNUM">WE_FORM_NUM</span></td>
			<td><input id="name_uploadFiles_WEFORMNUM" style="width:17.4em;" type="text" readonly="readonly" value="FILENAME" /></td>
			<td>
				<div id="div_rowButtons_WEFORMNUM">
					<table class="default"><tbody><tr>
							<td class="weFileUploadEntry_size" style="width:6em;text-align:right;margin-left:2px" id="size_uploadFiles_WEFORMNUM">FILESIZE</td>
							<td style="text-align:middle"><span id="alert_img_WEFORMNUM" style="visibility:hidden;" class="fa-stack fa-lg" style="color:#F2F200;" title=""><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span></td>
							<td>
								' . we_html_button::create_button(we_html_button::TRASH, "javascript:we_FileUpload.deleteRow(WEFORMNUM,this);") . '
							</td>
							<td>
								<div class="fileInputWrapper" style="overflow:hidde;vertical-align: bottom; display: inline-block;">
									<input style="width:40px; height:26px;" class="fileInput fileInputList fileInputHidden" type="file" id="fileInput_uploadFiles_WEFORMNUM" name="" />
									' . we_html_button::create_button('fa:, fa-lg fa-hdd-o', 'javascript:void(0)') . '
								</div>
							</td>
					</tr></tbody></table>
				</div>
				<div style="display: none; margin-left: 12px;" id="div_rowProgress_WEFORMNUM">
					<table class="default"><tbody><tr>
						<td style="vertical-align:middle"><div class="progress_image" style="width:0px;height:10px;" id="' . $this->name . '_progress_image_WEFORMNUM" style="vertical-align:top"></div><div class="progress_image_bg" style="width:130px;height:10px;" id="' . $this->name . '_progress_image_bg_WEFORMNUM" style="vertical-align:top"></div></td>
						<td class="small bold" style="width:3em;color:#006699;padding-left:8px;" id="span_' . $this->name . '_progress_text_WEFORMNUM">0%</td>
						<td><span id="alert_img_WEFORMNUM" style="visibility:hidden;" class="fa-stack fa-lg" style="color:#F2F200;" title=""><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span></td>
					</tr></tbody></table>
				</div>
			<td>
		</tr></tbody></table>');
	}

}
