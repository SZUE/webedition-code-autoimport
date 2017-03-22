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
class we_fileupload_ui_importer extends we_fileupload_ui_base { // FIXME: extend ui_preview
	protected $dimensions = ['width' => 400,
		'dragHeight' => 90,
		'dragWidth' => 320,
		'progressWidth' => 90,
		'alertBoxWidth' => 500,
		'marginTop' => 0,
		'marginBottom' => 0
	 ];

	public function __construct($name){
		parent::__construct($name);
		$this->responseClass = 'we_fileupload_resp_multiimport';

		$this->type = 'importer';
		$this->doCommitFile = true;
		$this->isGdOk = we_base_imageEdit::gd_version() > 0;
		$this->internalProgress = ['isInternalProgress' => true,
			'width' => 100
			];
		$this->externalProgress = ['isExternalProgress' => true,
			'create' => false
			];
		$this->fileTable = FILE_TABLE;
		$this->footerName = 'wizbusy';
		$this->contentName = 'wizbody';
		$this->cliensideImageEditing = true;
	}

	public function getCss(){
		return we_html_element::cssLink(CSS_DIR . 'we_fileupload.css');
	}

	public function getJs(){
		return parent::getJs() . we_html_multiIconBox::getDynJS('uploadFiles');
	}

	public function getHTML($hiddens = ''){
		$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;
		$alert = we_html_element::htmlHiddens(['we_cmd[0]' => 'import_files',
				'cmd' => 'content',
				]) .
			we_html_element::htmlDiv(['id' => 'desc'], we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[import_expl_js]') . '<br/><br/>' . (!we_fileupload::getMaxUploadSizeMB() ? g_l('importFiles', '[import_expl_js_no_limit]') : sprintf(g_l('importFiles', '[import_expl_js_limit]'), we_fileupload::getMaxUploadSizeMB())), we_html_tools::TYPE_INFO, 542, false, 20));

		$topParts = [["headline" => "",
			"html" => $alert]
		];

		$butBrowse = str_replace(["\n\r", "\r\n", "\r", "\n"], "", $isIE10 ? we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', '', 0, 0, '', '', false, false, '_btn') :
				we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', '', 0, 0, '', '', false, false, '_btn', false, '', 'importerBrowseHarddisk'));
		$butReset = str_replace(["\n\r", "\r\n", "\r", "\n"], "", we_html_button::create_button('reset', 'javascript:weFileUpload_instance.reset()', '', 0, 0, '', '', true, false, '_btn'));
		// TODO: get fileselect from parent!
		$fileselect = '
		<div style="float:left;">
			<div>
				<div class="we_fileInputWrapper" id="div_' . $this->name . '_fileInputWrapper">
					<input class="fileInput fileInputHidden' . ($isIE10 ? ' fileInputIE10' : '') . '" type="file" id="' . $this->name . '" name="fileselect[]" multiple="multiple" />
					' . $butBrowse . '
				</div>
				<div style="vertical-align: top; display: inline-block; height: 22px">
					' . $butReset . '
				</div>
				<div class="we_file_drag" id="div_' . $this->name . '_fileDrag" ' . ($isIE10 || we_base_browserDetect::isOpera() ? 'style="display:none;"' : 'style="display:block;width:280px;height:88px;padding-top:40px"') . '>' . g_l('importFiles', '[dragdrop_text]') . '</div>
			</div>
		</div>' .
		(we_fileupload::EDIT_IMAGES_CLIENTSIDE ? '<div class="editOptsImporter" style="position:absolute; left: 328px; padding-top: 24px">' . we_fileupload_ui_preview::getFormImageEditClientside($this->type) . '</div>' : '');

		$topParts[] = ["html" => $fileselect, 'space' => 0];

		// TODO: finish GUI
		$divMask = we_html_element::htmlDiv(['id' => 'we_fileUploadImporter_mask', 'class' => 'editorMask']);
		$divBusyMessage = we_html_element::htmlDiv(['id' => 'we_fileUploadImporter_busyMessage', 'class' => 'editorMessage'], we_html_element::htmlDiv(['class' => 'we_file_drag_maskSpinner'], '<i class="fa fa-2x fa-spinner fa-pulse"></i>') .
				we_html_element::htmlDiv(['id' => 'we_fileUploadImporter_busyText', 'class' => 'we_file_drag_maskBusyText'])
		);
		$loupe = we_fileupload_ui_preview::getHtmlLoup();

		$content = we_html_element::htmlDiv(['id' => 'forms', 'class' => 'fileuploadImporter', 'style' => 'display:block'], we_html_element::htmlForm(['action' => '',
					'name' => 'we_form',
					'enctype' => 'multipart/form-data'
			], $hiddens .
				'<div style="overflow:hidden; padding-bottom: 10px">' . we_html_multiIconBox::getHTML("selectFiles", $topParts, 30, "", -1, "", "", "", g_l('importFiles', '[step2]'), "", 0, "hidden") . '</div>' .
				'<div id="div_upload_files" style="height:410px; width: 100%; overflow-y:auto; overflow-x: hidden;">' . we_html_multiIconBox::getHTML("uploadFiles", [], 30, "", -1, "", "", "", "") . '</div>'
			) . $divMask . $loupe . $divBusyMessage
		);
		//<div id="imgfocus_point" style="display:none;" draggable="false"></div>

		return we_html_element::htmlBody(['class' => "weDialogBody"], $content);
	}

	//TODO: add param filetype
	public static function getBtnImportFiles($parentID = 0, $nextCmd = '', $text = ''){
		return we_html_button::create_button('fa:' . ($text ? : 'btn_import_files') . ',fa-lg fa-upload', "javascript:we_cmd('import_files','" . $parentID . "', '" . $nextCmd . "')");
	}

	protected function _getHtmlFileRow(){
		$btnTable = new we_html_table(['class' => 'default'], 1, 2);
		$btnTable->addCol(we_html_button::create_button(we_html_button::TRASH, "javascript:weFileUpload_instance.deleteRow(WEFORMNUM,this);"));
		$btnTable->setCol(0, 0, [], we_html_button::create_button(we_html_button::TRASH, "javascript:weFileUpload_instance.deleteRow(WEFORMNUM,this);"));
		$divBtnSelect = we_html_element::htmlDiv(
				['class' => 'fileInputWrapper', 'style' => 'overflow:hidden;vertical-align: bottom; display: inline-block;'], we_html_element::htmlInput(['type' => 'file', 'id' => 'fileInput_uploadFiles_WEFORMNUM',
					'class' => 'fileInput fileInputList fileInputHidden elemFileinput']) .
				we_html_button::create_button('fa:, fa-lg fa-hdd-o', 'javascript:void(0)')
		);
		$btnTable->setCol(0, 1, [], $divBtnSelect);

		// TODO: replace by we_progressBar
		$progressbar = '<table class="default"><tbody><tr>
			<td style="vertical-align:middle"><div class="progress_image" style="width:0px;height:10px;" id="' . $this->name . '_progress_image_WEFORMNUM" style="vertical-align:top"></div><div class="progress_image_bg" style="width:100px;height:10px;" id="' . $this->name . '_progress_image_bg_WEFORMNUM" style="vertical-align:top"></div></td>
			<td class="small bold" style="width:3em;color:#006699;padding:6px 0 0 8px;" id="span_' . $this->name . '_progress_text_WEFORMNUM">0%</td>
			<td><span id="alert_img_WEFORMNUM" style="visibility:hidden;" class="fa-stack fa-lg" style="color:#F2F200;" title=""><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span></td>
		</tr></tbody></table>';

		/*
		$progress = new we_progressBar(0,100,'_WEFORMNUM');
		$progressbar =  $progress->getHTML('', 'font-size:11px;');
		 *
		 */

		$scaleHelp = we_html_element::htmlDiv(['data-index' => 'WEFORMNUM', 'class' => 'optsRowScaleHelp'], '<span class="fa-stack alertIcon" style="color:black;"><i class="fa fa-question-circle" ></i></span>' . we_html_element::htmlDiv([
					'class' => 'optsRowScaleHelpText', 'style' => 'display:none;']));

		$btnRotateLeft = we_html_button::create_button('fa:, fa-lg fa-rotate-left', 'javascript:void(0)', 'fuOpts_addRotationLeft', 0, 0, '', '', false, false, '', false, g_l('importFiles', '[correct_orientation_left]'), 'fuOpts_addRotationLeft');
		$btnRotateRight = we_html_button::create_button('fa:, fa-lg fa-rotate-right', 'javascript:void(0)', 'fuOpts_addRotationRight', 0, 0, '', '', false, false, '', false, g_l('importFiles', '[correct_orientation_right]'), 'fuOpts_addRotationRight');
		$fiddenFuOpts_rotate = we_html_element::htmlHidden('fuOpts_rotate', -1, 'fuOpts_rotate_WEFORMNUM');

		$divInfoTop = we_html_element::htmlDiv(['class' => 'infoTop'], we_html_element::htmlDiv(['class' => 'leftDiv bold'], g_l('importFiles', '[edit_pixel_width]') . ' x ' . g_l('importFiles', '[edit_pixel_height]') . ':') . we_html_element::htmlDiv(['class' => 'rowDimensionsProcessed rightDiv'], '--') . $scaleHelp);
		$divInfoMiddle = we_html_element::htmlDiv(['class' => 'infoMiddle'], we_html_element::htmlDiv(['class' => 'leftDiv bold'], g_l('importFiles', '[rotation]') . ':') . we_html_element::htmlDiv(['class' => 'rowRotation rightDiv'], ''));
		$divInfoBottom = we_html_element::htmlDiv(['class' => 'infoBottom'], we_html_element::htmlDiv(['class' => 'leftDiv'], g_l('importFiles', '[original_dimensions]') . ':') . we_html_element::htmlDiv(['class' => 'rowDimensionsOriginal rightDiv'], '--'));
		$divInfo = we_html_element::htmlDiv(['class' => 'elemInfo'], $divInfoTop . $divInfoMiddle . $divInfoBottom);

		$divBtnRefresh = we_html_element::htmlDiv(['class' => 'btnRefresh', 'id' => 'customOpts_WEFORMNUM', 'style' => 'top:6px; left: 332px;'], '<div>' . $fiddenFuOpts_rotate . $btnRotateLeft . $btnRotateRight . '</div><div style="margin-top: 4px;">' . we_html_button::create_button(we_html_button::MAKE_PREVIEW, "javascript:", '', 0, 0, '', '', false, true, '', false, $title = 'Bearbeitungsvorschau erstellen', 'weFileupload_btnImgEditRefresh rowBtnProcess') . '<div class="fa fa-asterisk rowEditHot "></div></div>');

		return str_replace(["\r", "\n"], "", we_html_element::htmlDiv(['class' => 'importerElem'], we_html_element::htmlDiv(['class' => 'weMultiIconBoxHeadline elemNum', 'style' => 'display:none;'], 'Nr. WE_FORM_NUM') .
		we_html_element::htmlDiv(['class' => 'elemContainer'],
			we_html_element::htmlDiv(['id' => 'preview_uploadFiles_WEFORMNUM', 'class' => 'weFileUploadEntry_preview elemPreview'],
				we_html_element::htmlDiv(['class' => 'elemPreviewPreview'])
			) .
			we_html_element::htmlDiv(['id' => 'icon_uploadFiles_WEFORMNUM', 'class' => 'weFileUploadEntry_icon elemIcon']) .

			we_html_element::htmlDiv(['class' => 'elemContent'],
				we_html_element::htmlForm(['id' => 'form_editOpts_WEFORMNUM', 'name' => 'form_editOpts', 'data-type' => 'importer_rowForm', 'data-index' => 'WEFORMNUM'],
					we_html_element::htmlDiv(['class' => 'elemContentTop'],
						we_html_element::htmlDiv(['id' => 'name_uploadFiles_WEFORMNUM', 'class' => 'elemFilename'], 
							we_html_element::htmlDiv(['id' => 'editName_uploadFiles_WEFORMNUM', 'data-name' => 'editName_uploadFiles', 'style' => 'display:none;'], 
								we_html_element::htmlInput(['type' => 'text', 'data-name' => 'fuOpts_filenameInput', 'data-index' => 'WEFORMNUM', 'name' => 'fuOpts_filenameInput', 'id' => 'fuOpts_filenameInput_WEFORMNUM', 'value' => 'FNAME', 'class' => 'inputElemFilename']) . ' .FENDING' .
								we_html_element::htmlHidden('fuOpts_index', 'WEFORMNUM')
							) .
							we_html_element::htmlDiv(['id' => 'showName_uploadFiles_WEFORMNUM', 'class' => 'elemShowName', 'data-name' => 'showName_uploadFiles', 'data-index' => 'WEFORMNUM', 'data-ext' => 'FENDING'], 'FILENAME')
						) .
						we_html_element::htmlDiv(['id' => 'div_rowButtons_WEFORMNUM', 'class' => 'elemContentTopRight'],
							we_html_element::htmlDiv(['id' => 'size_uploadFiles_WEFORMNUM', 'class' => 'weFileUploadEntry_size elemSize'], 'FILESIZE') .
							we_html_element::htmlDiv(['class' => 'elemButtons'], $btnTable->getHtml())
						) .
						we_html_element::htmlDiv(['id' => 'div_rowProgress_WEFORMNUM', 'class' => 'elemProgress'], $progressbar)
					) .
					we_html_element::htmlDiv(['id' => 'editoptions_uploadFiles_WEFORMNUM', 'class' => 'weFileUploadEntry_editoption elemContentBottom'],
						$divInfo . $divBtnRefresh
					)
				) .
				we_html_element::htmlDiv(['class' => 'elemContentMask'],
					we_html_element::htmlDiv(['class' => 'we_file_drag_maskSpinner'], '<i class="fa fa-2x fa-spinner fa-pulse"></i></span>') .
					we_html_element::htmlDiv(['id' => 'image_edit_mask_text', 'class' => 'we_file_drag_maskBusyText'])
				) . we_html_element::htmlDiv(['id' => 'image_edit_done_WEFORMNUM', 'class' => 'elemContentDone']))
		)));

	}


	protected function _getHtmlFileRow_legacy(){
		return str_replace(["\r", "\n"], "", '<table class="default importer_files"><tbody><tr height="28">
			<td class="weMultiIconBoxHeadline" style="width:55px;padding-left:45px;padding-right:20px;" >' . g_l('importFiles', '[file]') . '&nbsp;<span id="headline_uploadFiles_WEFORMNUM">WE_FORM_NUM</span></td>
			<td><input id="name_uploadFiles_WEFORMNUM" style="width:17.4em;" type="text" readonly="readonly" value="FILENAME" /></td>
			<td>
				<div id="div_rowButtons_WEFORMNUM">
					<table class="default"><tbody><tr>
							<td class="weFileUploadEntry_size" style="width:6em;text-align:right;margin-left:2px" id="size_uploadFiles_WEFORMNUM">FILESIZE</td>
							<td style="text-align:middle"><span id="alert_img_WEFORMNUM" style="visibility:hidden;" class="fa-stack fa-lg" style="color:#F2F200;" title=""><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span></td>
							<td>
								' . we_html_button::create_button(we_html_button::TRASH, "javascript:weFileUpload_instance.deleteRow(WEFORMNUM,this);") . '
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
