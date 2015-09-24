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
class we_fileupload_ui_image extends we_fileupload_binaryDocument{
	private $layout = 'vertical';

	public function __construct($contentType = '', $extension = '', $layout = 'vertical'){
		$this->layout = $layout === 'horizontal' ? $layout : $this->layout;

		parent::__construct($contentType, $extension, ($layout === 'vertical' ? 'dialog' : 'we_doc'));

		/*
		if($location === 'we_doc'){
			$this->moreFieldsToAppend = array(
				array('we_transaction', 'text'),
				array('import_metadata', 'check'),
				array('we_doc_ct', 'text'),
				array('we_doc_ext', 'text')
			);
		}
		 * 
		 */
	}

	public function getCss(){
		return $this->layout !== 'vertical' ? we_html_element::cssLink(CSS_DIR . 'we_fileupload.css') : parent::getCss();
	}

	public function getDivBtnUpload(){
		$btnUpload = $this->getBtn('upload', true);
		$btnCancel = $this->getBtn('cancel');
		$divBtnUpload = we_html_element::htmlDiv(array('id' => 'div_fileupload_btnUpload', 'style' => 'margin-top: 4px;'), $btnUpload);
		$divBtnCancel = we_html_element::htmlDiv(array('id' => 'div_fileupload_btnCancel', 'style' => 'margin-top: 4px;display:none;'), $btnCancel);

		return $divBtnUpload . $divBtnCancel;
	}

	public function getHTML($fs = '', $ft = '', $md = '', $thumbnailSmall = '', $thumbnailBig = ''){

		if($this->layout !== 'vertical'){
			// TODO: use this for we_imageDocuments later to add more image upload functionality
			parent::getHTML($fs, $ft, $md, $thumbnailSmall, $thumbnailBig);
		} else {
			$isIE10 = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11;

			//FIXME: this should be static in css
			$width['input'] = $this->layout === 'vertical' ? ($this->dimensions['inputWidth'] - 4) : ($isIE10 ? 84 : 170);
			//$dropText = g_l('newFile', $this->isDragAndDrop ? '[drop_text_ok]' : '[drop_text_nok]');

			$fileInput = we_html_element::htmlInput(array(
					'class' => 'fileInput fileInputHidden' . ($isIE10 ? ' fileInputIE10' : ''),
					'style' => 'width:' . $width['input'] . 'px;',
					'type' => 'file',
					'name' => $this->name,
					'id' => $this->name,
					'accept' => implode(',', $this->typeCondition['accepted']['mime']))
			);
			$fileInput .= !$isIE10 ? '' :
				we_html_element::htmlInput(array(
					'class' => 'fileInput fileInputHidden fileInputIE10',
					'style' => 'width:' . $width['input'] . 'px; left:' . $width['input'] . 'px;',
					'type' => 'file',
					'name' => $this->name . '_x2',
					'id' => $this->name . '_x2',
					'accept' => implode(',', $this->typeCondition['accepted']['mime']))
			);
			$divFileInput = we_html_element::htmlDiv(array('id' => 'div_we_File_fileInputWrapper', 'class' => 'we_fileInputWrapper', 'style' => 'height:26px;margin-top:18px;width:' . ($width['input'] + 8) . 'px;'), $fileInput . $this->getBtn('browse', false, $width['input']));
			$divBtnReset = we_html_element::htmlDiv(array('id' => 'div_fileupload_btnReset', 'style' => 'height:26px;margin-top:18px;display:none;'), $this->getBtn('reset', false, $width['input']));

			$progress = new we_progressBar(0, true);
			if($this->layout !== 'vertical'){
				$progress->setStudLen(170);
				$progress->setProgressTextPlace(0);
			} else {
				$progress->setStudLen(200);
			}
			$progress->setName('_fileupload');

			$btnUploadLegacy = we_html_button::create_button(we_html_button::UPLOAD, "javascript:we_cmd('editor_uploadFile', 'legacy')", true, 150, 22, "", "", false, false, "_legacy_btn", true);
			$divBtnUploadLegacy = we_html_element::htmlDiv(array('id' => 'div_fileupload_btnUploadLegacy', 'style' => 'margin:0px 0 16px 0;display:' . (self::isFallback() || self::isLegacyMode() ? '' : 'none' ) . ';'), $btnUploadLegacy);

			
			$divProgressbar = we_html_element::htmlDiv(array('id' => 'div_fileupload_progressBar', 'style' => 'display:none;'), $progress->getHTML());
			$divButtons = we_html_element::htmlDiv(array('id' => 'div_fileupload_buttons', 'style' => 'width:400px'), $divFileInput . $divBtnReset);

			$yuiSuggest = &weSuggest::getInstance();
			$cmd1 = "document.we_form.importToID.value";
			$wecmdenc2 = we_base_request::encCmd("document.we_form.importToDir.value");
			$wecmdenc3 = ''; //we_base_request::encCmd();
			$startID = IMAGESTARTID_DEFAULT ? : 0;
			$but = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd1 . ",'" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "',''," . $startID . ",'" . we_base_ContentTypes::FOLDER . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");
			$yuiSuggest->setAcId("importToID");
			$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$yuiSuggest->setInput("importToDir", $startID ? id_to_path($startID, FILE_TABLE) : '/');
			$yuiSuggest->setLabel(g_l('weClass', '[dir]'));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult("importToID", $startID);
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setWidth(320);
			$yuiSuggest->setSelectButton($but);
			$noImage = '<img style="margin:8px 18px;border-style:none;width:64px;height:64px;" src="/webEdition/images/icons/no_image.gif" alt="no-image" />';

			return (self::isFallback() || self::isLegacyMode() ? '' : $this->getJs() . $this->getCss()) .
				we_html_element::htmlDiv(array(),
					we_html_element::htmlDiv(array('id' => 'imageUpload', 'style' => 'display:none;width:384px;'),
						'<table id="table_form_upload" class="default" width="500">
							<tr style="vertical-align:top;">
								<td class="defaultfont" width="200px">' .
									$divBtnUploadLegacy .
									(self::isFallback() || self::isLegacyMode() ? '' :
										$divButtons .
										we_html_element::htmlHiddens(array(
											'we_doc_ct' => $this->contentType,
											'we_doc_ext' => $this->extension,
											'weFileNameTemp' => '',
											'weFileName' => '',
											'weFileCt' => '',
											'weIsFileInLegacy' => 0
										))
									) . '
								</td>
							</tr>
								<td width="300px">' .
									(self::isFallback() || self::isLegacyMode() ? '' :
										we_html_element::htmlDiv(array('id' => 'div_fileupload_right', 'style'=>"position:relative;"),
											$this->getHtmlDropZone('preview', $noImage)
										)
									) .
									we_html_element::htmlDiv(array('id' => 'div_fileupload_right_legacy', 'style' => 'text-align:right;' . (self::isFallback() || self::isLegacyMode() ? '' : 'display:none;' )),
										$noImage
									) . '
								</td>
							</tr>
							<tr style="vertical-align:top;">
								<td width="300px">' .
									$divProgressbar .'
								</td>
							</tr>
						</table>' . '<br/> ' .
						$yuiSuggest->getHTML() .
						we_html_element::htmlDiv(array(),
							we_html_forms::checkboxWithHidden(true, 'imgsSearchable', g_l('weClass', '[IsSearchable]'), false, 'defaultfont', '') .
							we_html_forms::checkboxWithHidden(true, 'importMetadata', g_l('importFiles', '[import_metadata]'), false, 'defaultfont', '') . '<br />'
						) .
						we_html_element::htmlDiv(array('style' => 'margin:10px 0 0 0;'),
							we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[sameName_expl]'), we_html_tools::TYPE_INFO, 380) .
							we_html_element::htmlDiv(array('style' => 'margin-top:10px'), we_html_forms::radiobutton('overwrite', false, "sameName", g_l('importFiles', '[sameName_overwrite]'), false, "defaultfont", 'document.we_form.sameName.value=this.value;') .
								we_html_forms::radiobutton('rename', true, "sameName", g_l('importFiles', '[sameName_rename]'), false, "defaultfont", 'document.we_form.sameName.value=this.value;') .
								we_html_forms::radiobutton('nothing', false, "sameName", g_l('importFiles', '[sameName_nothing]'), false, "defaultfont", 'document.we_form.sameName.value=this.value;')
							) .
							we_html_tools::hidden("sameName", 0)
						) .
						we_html_element::htmlDiv(array('style' => 'float:right;padding-top:10px;'), $this->getDivBtnUpload(true))
					)
				);
		}
	}
}
