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
class we_fileupload_ui_preview extends we_fileupload_ui_base{
	protected $layout = 'vertical';
	protected $isExternalBtnUpload = false;
	protected $importToID = array(
		'setField' => false,
		'preset' => 0,
		'setFixed' => false
	);
	protected $transaction;
	protected $contentType;
	protected $extension;

	public function __construct($contentType = '', $extension = ''){
		parent::__construct('we_File');

		$this->doCommitFile = true;
		$this->contentType = $contentType;
		$this->responseClass = 'we_fileupload_resp_import';
		$type = 'binDoc';
		$this->callback = '';
		$this->type = 'binDoc';
		$this->extension = $extension;
		$this->setInternalProgress(array('isInternalProgress' => true));
		$this->internalProgress['width'] = 170;
		$this->setTypeCondition('accepted', array($contentType));
		$this->setDimensions(array('width' => 200, 'dragHeight' => 116, 'alertBoxWidth' => 507));
		//$this->binDocProperties = $this->getDocProperties();
	}

	public function getCss(){
		return we_html_element::cssLink(CSS_DIR . 'we_fileupload.css') . we_html_element::cssElement('
			div.we_file_drag{
				width: ' . $this->dimensions['dragWidth'] . 'px;
			}
			div.filedrag_content_left{
				padding-left: ' . ($this->dimensions['dragWidth'] * 0.04) . 'px;
				width: ' . ($this->dimensions['dragWidth'] * 0.56) . 'px;
			}
			div.filedrag_content_right{
				width: ' . ($this->dimensions['dragWidth'] * 0.4) . 'px;
			}
			div.filedrag_preview_left{
				padding-left: ' . ($this->dimensions['dragWidth'] * 0.04) . 'px;
				width: ' . ($this->dimensions['dragWidth'] * 0.56) . 'px;
			}
			div.filedrag_preview_right{
				width: ' . ($this->dimensions['dragWidth'] * 0.4) . 'px;
			}');
	}

	public function getDivBtnUploadCancel($width = 170){
		return we_html_element::htmlDiv(array(), $this->getButtonWrapped('upload', true, $width) . $this->getButtonWrapped('cancel'), false, $width);
	}

	protected function getDivBtnInputReset($width){
		return we_html_element::htmlDiv(array('style' => 'margin-top:18px;'), $this->getButtonWrapped('reset', false, $width) . $this->getButtonWrapped('browse', false, $width));
	}

	protected function getHtmlDropZone($type = 'preview', $thumbnailSmall = ''){
			$dropText = g_l('newFile', $this->isDragAndDrop ? '[drop_text_ok]' : '[drop_text_nok]');

			return we_html_element::htmlDiv(array('id' => 'div_fileupload_fileDrag_state_0', 'class' => 'we_file_drag we_file_drag_content', 'style' => (!$this->isDragAndDrop ? 'border-color:white;' : ''), 'ondragenter' => "alert('wrong div')"),
					we_html_element::htmlDiv(array('class' => 'filedrag_content_left', 'style' => (!$this->isDragAndDrop ? 'font-size:14px' : '')), $dropText) .
					we_html_element::htmlDiv(array('class' => 'filedrag_content_right'), ($thumbnailSmall ? : we_html_element::jsElement('document.write(getTreeIcon("' . $this->contentType . '"));')))
				) .
				we_html_element::htmlDiv(array('id' => 'div_fileupload_fileDrag_state_1', 'class' => 'we_file_drag we_file_drag_preview', 'style' => (!$this->isDragAndDrop ? 'border-color:rgb(243, 247, 255);' : '')),
					we_html_element::htmlDiv(array('id' => 'div_upload_fileDrag_innerLeft', 'class' => 'filedrag_preview_left'),
						we_html_element::htmlSpan(array('id' => 'span_fileDrag_inner_filename')) . we_html_element::htmlBr() .
						we_html_element::htmlSpan(array('id' => 'span_fileDrag_inner_size')) . we_html_element::htmlBr() .
						we_html_element::htmlSpan(array('id' => 'span_fileDrag_inner_type'))
					) .
					we_html_element::htmlDiv(array('id' => 'div_upload_fileDrag_innerRight', 'class' => 'filedrag_preview_right'), '')
				) .
				($this->isDragAndDrop ? we_html_element::htmlDiv(array('id' => 'div_we_File_fileDrag', 'class' => 'we_file_drag we_file_drag_mask'), '') : '');
	}

	protected function getHiddens(){
		return $hiddens =  parent::getHiddens() . we_html_element::htmlHiddens(array(
			'we_doc_ct' => $this->contentType,
			'we_doc_ext' => $this->extension,
		));
	}

	public function getJsBtnCmd($btn = 'upload'){
		$call = 'window.we_FileUpload.' . ($btn === 'upload' ? 'startUpload()' : 'cancelUpload()');

		return 'if(window.we_FileUpload === undefined){alert("what\'s wrong?");}else{' . $call . ';}';
	}

	public static function getJsOnLeave($callback, $type = 'switch_tab'){
		if($type === 'switch_tab'){
			$parentObj = 'WE().layout.weEditorFrameController';
			$frame = 'WE().layout.weEditorFrameController.getVisibleEditorFrame()';
		} else {
			$parentObj = 'top._EditorFrame';
			$frame = '_EditorFrame.getContentEditor()';
		}

		return "var fileupload; if(" . $parentObj . " !== undefined && (fileUpload = " . $frame . ".we_FileUpload) !== undefined && fileUpload.getType() === 'binDoc'){fileUpload.doUploadIfReady(function(){" . $callback . "})}else{" . $callback . "}";
	}

	public function setIsExternalBtnUpload($isExternal){
		$this->isExternalBtnUpload = $isExternal;
	}

	public function setFieldImportToID($importToID = array()){
		$this->importToID = array_merge($this->importToID, $importToID);
	}
}
