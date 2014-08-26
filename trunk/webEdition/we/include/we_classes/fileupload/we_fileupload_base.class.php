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
abstract class we_fileupload_base{
	protected $type = '';
	protected $name = 'weFileSelect';
	protected $form = array(
		'name' => '',
		'action' => ''
	);
	protected $fileselectOnclick = '';
	protected $isDragAndDrop = true;
	protected $callback = '';
	protected $maxUploadSizeMBytes = 0;
	protected $maxUploadSizeBytes = 0;
	protected $dimensions = array(
		'width' => 400,
		'dragHeight' => 30,
		'alertBoxWidth' => 390,
		'marginTop' => 0,
		'marginBottom' => 0
	);
	protected $footerName = '';
	protected $contentName = '';
	protected $uploadBtnName = 'upload_btn';
	protected $internalProgress = array(
		'isInternalProgress' => false,
		'width' => 0,
	);
	protected $externalProgress = array(
		'isExternalProgress' => false,
		'parentElemId' => '',
		'create' => false,
		'html' => '',
		'width' => 0,
		'name' => '',
		'additionalParams' => array()
	);
	protected $typeCondition = array(
		'accepted' => array(
			'mime' => array(),
			'extensions' => array(),
			'all' => array()
		),
		'forbidden' => array(
			'mime' => array(),
			'extensions' => array(),
			'all' => array()
		)
	);
	protected $isGdOk = true;
	protected $fileTable = '';
	public $useLegacy = false;

	const CHUNK_SIZE = 128;
	const ON_ERROR_RETURN = true;
	const ON_ERROR_DIE = true;

	abstract protected function getHTML($hiddens = '');

	protected function __construct($name, $width = 400, $maxUploadSize = -1, $isDragAndDrop = true){
		$this->name = $name;
		$this->isDragAndDrop = (we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 11) || we_base_browserDetect::isOpera() ? false : $isDragAndDrop;
		$this->dimensions['width'] = $width;
		$this->maxUploadSizeMBytes = intval($maxUploadSize != -1 ? $maxUploadSize : (defined('FILE_UPLOAD_MAX_UPLOAD_SIZE') ? FILE_UPLOAD_MAX_UPLOAD_SIZE : 0));
		$this->maxUploadSizeBytes = $this->maxUploadSizeMBytes * 1048576;
		$this->maxChunkCount = $this->maxUploadSizeMBytes * 1024 / self::CHUNK_SIZE;
		$this->useLegacy = we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 10 ? true : (defined('FILE_UPLOAD_USE_LEGACY') ? FILE_UPLOAD_USE_LEGACY : false);
	}

	public function setAction($action){
		$this->form['action'] = $action;
	}

	public function setDimensions($args = array()){
		$this->dimensions = array(
			'width' => isset($args['width']) ? $args['width'] : $this->dimensions['width'],
			'dragHeight' => isset($args['dragHeight']) ? $args['dragHeight'] : $this->dimensions['dragHeight'],
			'alertBoxWidth' => isset($args['alertBoxWidth']) ? $args['alertBoxWidth'] : $this->dimensions['alertBoxWidth'],
			'marginTop' => isset($args['marginTop']) ? $args['marginTop'] : $this->dimensions['marginTop'],
			'marginBottom' => isset($args['marginBottom']) ? $args['marginBottom'] : $this->dimensions['marginBottom']
		);
	}

	public function setMaxUploadSize($sizeMB = 0){
		$this->maxUploadSizeMBytes = $sizeMB;
		$this->maxUploadSizeBytes = $this->maxUploadSizeMBytes * 1048576;
	}

	public function setUseLegacy($useLegacy = true){
		$this->useLegacy = $useLegacy;
	}

	public function getName(){
		return $this->name;
	}

	public function getMaxUploadSize(){
		return $this->useLegacy ? getUploadMaxFilesize(false) : $this->maxUploadSizeBytes;
	}

	public function getMaxUploadSizeText(){
		$field = $this->useLegacy ? '[max_possible_size]' : ($this->getMaxUploadSize() ? '[size_limit_set_to]' : '[no_size_limit]');
		return $field == '[no_size_limit]' ? g_l('newFile', $field) :
			sprintf(g_l('newFile', $field), we_base_file::getHumanFileSize($this->getMaxUploadSize(), we_base_file::SZ_MB));
	}

	public function getHtmlMaxUploadSizeAlert($width = -1){
		$width = $width == -1 ? $this->dimensions['alertBoxWidth'] : $width;
		$type = $this->useLegacy ? we_html_tools::TYPE_ALERT : we_html_tools::TYPE_INFO;

		return '
			<div id="div_' . $this->name . '_alert_legacy" style="display:none">' .
			we_html_tools::htmlAlertAttentionBox(sprintf(g_l('newFile', '[max_possible_size]'), we_base_file::getHumanFileSize(getUploadMaxFilesize(false), we_base_file::SZ_MB)), we_html_tools::TYPE_ALERT, $width) .
			'<div style="margin-top: 4px"></div>' .
			we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[fallback_text]'), we_html_tools::TYPE_ALERT, $width, false, 9) .
			'</div>
			<div id="div_' . $this->name . '_alert">' .
			we_html_tools::htmlAlertAttentionBox($this->getMaxUploadSizeText(), $type, $width) .
			'</div>';
	}

	protected function getHtmlFileRow(){
		return '';
	}

	public function getCss(){
		return $this->useLegacy ? '' : we_html_element::cssLink(CSS_DIR . 'we_fileupload.css') . we_html_element::cssElement('
			div.we_file_drag{
				padding-top: ' . (($this->dimensions['dragHeight'] - 10) / 2) . 'px;
				height: ' . $this->dimensions['dragHeight'] . 'px;
			}');
	}

	public function getJs(){
		$this->externalProgress['create'] = $this->externalProgress['create'] && $this->externalProgress['isExternalProgress'] && $this->externalProgress['parentElemId'];
		if($this->externalProgress['create']){
			$progressbar = new we_progressBar();
			$progressbar->setName($this->externalProgress['name']);
			$progressbar->setStudLen($this->externalProgress['width']);
			$this->externalProgress['html'] = str_replace(array("\r", "\n"), " ", $progressbar->getHTML());
		}

		return we_html_element::jsScript('/webEdition/js/weFileUpload.js') .
			we_html_element::jsElement('
			we_FileUpload = new weFileUpload("' . $this->type . '");
			we_FileUpload.init({
				fieldName : "' . $this->name . '",
				form : ' . json_encode($this->form) . ',
				footerName : "' . $this->footerName . '",
				uploadBtnName : "' . $this->uploadBtnName . '",
				maxUploadSize : ' . $this->maxUploadSizeBytes . ',
				typeCondition : ' . str_replace(array("\r", "\n"), " ", json_encode($this->typeCondition)) . ',
				isDragAndDrop : ' . ($this->isDragAndDrop ? 'true' : 'false') . ',
				isLegacyMode : ' . ($this->useLegacy ? 'true' : 'false') . ',
				callback : function(){' . $this->callback . '},
				fileselectOnclick : function(){' . $this->fileselectOnclick . '},
				chunkSize : ' . self::CHUNK_SIZE . ',
				intProgress : ' . json_encode($this->internalProgress) . ',
				extProgress : ' . json_encode($this->externalProgress) . ',
				gl: ' . $this->_getJsGl() . ',
				isGdOk : ' . ($this->isGdOk ? 'true' : 'false') . ',
				htmlFileRow : \'' . $this->getHtmlFileRow() . '\',
				fileTable : "' . $this->fileTable . '",
			});
		') . ($this->externalProgress['create'] ? implodeJS($progressbar->getJS('', true)) : '');
	}

	protected function _getJsGl(){
		return '{
					dropText : "' . g_l('importFiles', "[dragdrop_text]") . '",
					sizeTextOk : "' . g_l('newFile', '[file_size]') . ': ",
					sizeTextNok : "' . g_l('newFile', '[file_size]') . ': &gt; ' . $this->maxUploadSizeMBytes . ' MB, ",
					typeTextOk : "' . g_l('newFile', '[file_type]') . ': ",
					typeTextNok : "' . g_l('newFile', '[file_type_forbidden]') . ': ",
					errorNoFileSelected : "' . g_l('newFile', '[error_no_file]') . '",
					errorFileSize : "' . g_l('newFile', '[error_file_size]') . '",
					errorFileType : "' . g_l('newFile', '[error_file_type]') . '",
					errorFileSizeType : "' . g_l('newFile', '[error_size_type]') . '",
					uploadCancelled : "' . g_l('importFiles', '[upload_cancelled]') . '",
					cancelled : "' . g_l('importFiles', "[cancelled]") . '",
					doImport : "' . g_l('importFiles', "[do_import]") . '",
					file : "' . g_l('importFiles', "[file]") . '",
					btnClose : "' . g_l('button', '[close][value]') . '",
					btnCancel : "' . g_l('button', '[cancel][value]') . '",
					btnUpload : "' . g_l('button', "[upload][value]") . '"
				}';
	}
}
