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
class we_fileupload_ui_importer extends we_fileupload_ui_base{
	protected $dimensions = array(
		'width' => 400,
		'dragHeight' => 80,
		'dragWidth' => 395,
		'progressWidth' => 90,
		'alertBoxWidth' => 390,
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
			'width' => 130
		);
		$this->externalProgress = array(
			'isExternalProgress' => true,
			'create' => false
		);
		$this->fileTable = FILE_TABLE;
		$this->footerName = 'imgimportbuttons';
		$this->contentName = 'imgimportcontent';
	}

	public function getHTML($hiddens = ''){
		$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;
		$alert = we_html_tools::hidden('we_cmd[0]', 'import_files') .
			we_html_tools::hidden('cmd', 'content') . we_html_tools::hidden('step', 2) .
			we_html_element::htmlDiv(array('id' => 'desc'), we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[import_expl_js]') . '<br/><br/>' . ($this->maxUploadSizeMBytes == 0 ? g_l('importFiles', '[import_expl_js_no_limit]') : sprintf(g_l('importFiles', '[import_expl_js_limit]'), $this->maxUploadSizeMBytes)), we_html_tools::TYPE_INFO, 520, false, 20));

		$topParts = array(
			array("headline" => "",
				"html" => $alert)
		);

		$butBrowse = str_replace(array("\n\r", "\r\n", "\r", "\n"), "", $isIE10 ? we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', true, 80, we_html_button::HEIGHT, '', '', false, false, '_btn') :
				we_html_button::create_button('fat:browse_harddisk,fa-lg fa-hdd-o', 'javascript:void(0)', true, 281, we_html_button::HEIGHT, '', '', false, false, '_btn'));
		$butReset = str_replace(array("\n\r", "\r\n", "\r", "\n"), "", we_html_button::create_button('reset', 'javascript:we_FileUpload.reset()', true, ($isIE10 ? 84 : 100), we_html_button::HEIGHT, '', '', true, false, '_btn'));
		$fileselect = '
		<div style="float:left;">
		<form id="filechooser" action="" method="" enctype="multipart/form-data">
			<div>
				<div class="we_fileInputWrapper" id="div_' . $this->name . '_fileInputWrapper">
					<input class="fileInput fileInputHidden' . ($isIE10 ? ' fileInputIE10' : '') . '" type="file" id="' . $this->name . '" name="fileselect[]" multiple="multiple" />
					' . $butBrowse . '
				</div>
				<div style="vertical-align: top; display: inline-block; height: 22px">
					' . $butReset . '
				</div>
				<div class="we_file_drag" id="div_' . $this->name . '_fileDrag" ' . ($isIE10 || we_base_browserDetect::isOpera() ? 'style="display:none;"' : 'style="display:block;"') . '>' . g_l('importFiles', '[dragdrop_text]') . '</div>
			</div>
		</form>
		</div>
		';

		$topParts[] = array("headline" => g_l('importFiles', '[select_files]'), "html" => $fileselect, 'space' => 119);

		// TODO: throw out inline css and use we_html_element
		$messageLayer = '
			<div id="we_fileUpload_messageBg" style="display:none;position:absolute;top:0; left:0; right:0; bottom:0; background-color:gray;opacity: 0.8;"></div>
			<div id="we_fileUpload_message" style="display:none;padding: 20px; background-color:white; width: 400px; height: 180px;position:absolute; top:70px;left:100px;opacity:1.0">
			<p>Please wait: <span id="we_fileUpload_messageNr"></span> images left to process</p><p>(add progressbar here)</p></div>
		';

		$content = we_html_element::htmlDiv(array("id" => "forms", "style" => "display:block"), we_html_element::htmlForm(array(
					"action" => WEBEDITION_DIR . "we_cmd.php",
					"name" => "we_startform",
					"method" => "post"
					), $hiddens) .
				'<div style="overflow:hidden; padding-bottom: 10px">' . we_html_multiIconBox::getHTML("selectFiles", $topParts, 30, "", -1, "", "", "", g_l('importFiles', '[step2]'), "", 0, "hidden") . '</div>' .
				'<div id="div_upload_files" style="height:310px; width: 100%; overflow:auto">' . we_html_multiIconBox::getHTML("uploadFiles", array(), 30, "", -1, "", "", "", "") . '</div>' .
				$messageLayer
		);

		return we_html_element::htmlBody(array("class" => "weDialogBody"), $content);
	}

	//TODO: add param filetype
	public static function getBtnImportFiles($parentID = 0, $callback = '', $text = ''){
		return we_html_button::create_button('fa:' . ($text ? : 'btn_import_files') . ',fa-lg fa-upload', "javascript:top.we_cmd('import_files','" . $parentID . "', '" . $callback . "')", true, 62);
	}

	protected function _getHtmlFileRow(){
		return str_replace(array("\r", "\n"), "", '<table class="default"><tbody><tr height="28">
			<td class="weMultiIconBoxHeadline" style="width:55px;padding-left:45px;padding-right:20px;" >' . g_l('importFiles', '[file]') . '&nbsp;<span id="headline_uploadFiles_WEFORMNUM">WE_FORM_NUM</span></td>
			<td><input id="name_uploadFiles_WEFORMNUM" style="width:17.4em;" type="text" readonly="readonly" value="FILENAME" /></td>
			<td>
				<div id="div_rowButtons_WEFORMNUM">
					<table class="default"><tbody><tr>
							<td class="weFileUploadEntry_size" style="width:6em;text-align:right;margin-left:2px" id="size_uploadFiles_WEFORMNUM">FILESIZE</td>
							<td style="text-align:middle"><span id="alert_img_WEFORMNUM" style="visibility:hidden;" class="fa-stack fa-lg" style="color:#F2F200;" title=""><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span></td>
							<td>
								<div class="fileInputWrapper" style="vertical-align: bottom; display: inline-block;">
									<input class="fileInput fileInputList fileInputHidden" type="file" id="fileInput_uploadFiles_WEFORMNUM" name="" />
									' . we_html_button::create_button(we_html_button::EDIT, 'javascript:void(0)') . '
								</div>
							</td>
							<td>
								' . we_html_button::create_button(we_html_button::TRASH, "javascript:we_FileUpload.deleteRow(WEFORMNUM,this);") . '
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
