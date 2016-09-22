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
class we_import_files{
	var $parentID = 0;
	var $step = 0;
	var $sameName = "overwrite";
	var $importMetadata = true;
	private $imgsSearchable = false;
	var $cmd = '';
	var $thumbs = '';
	var $width = '';
	var $height = '';
	var $widthSelect = 'pixel';
	var $heightSelect = 'pixel';
	var $keepRatio = 1;
	var $quality = 8;
	var $degrees = 0;
	var $categories = '';
	public $callBack = '';
	private $maxUploadSizeMB = 8;
	private $maxUploadSizeB = 0;
	private $fileNameTemp = '';
	private $partNum = 0;
	private $partCount = 0;
	private $isPreset = false;

	const CHUNK_SIZE = 256;

	function __construct(){
		if(($catarray = we_base_request::_(we_base_request::STRING_LIST, 'fu_doc_categories'))){
			$cats = [];
			foreach($catarray as $cat){
				// bugfix Workarround #700
				$cats[] = (is_numeric($cat) ?
						$cat :
						path_to_id($cat, CATEGORY_TABLE, $GLOBALS['DB_WE']));
			}
			$this->categories = implode(',', $cats);
		} else {
			$this->categories = we_base_request::_(we_base_request::INTLIST, 'fu_doc_categories', $this->categories);
		}
		$this->isPreset = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1) || we_base_request::_(we_base_request::RAW, 'we_cmd', false, 2);
		$this->parentID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1) ? : we_base_request::_(we_base_request::INT, "fu_file_parentID", $this->parentID);
		$this->callBack = we_base_request::_(we_base_request::RAW, 'we_cmd', '', 3) ? : (we_base_request::_(we_base_request::RAW, 'callBack', '') ? : '');
		$this->sameName = we_base_request::_(we_base_request::STRING, "fu_file_sameName", $this->sameName);
		$this->importMetadata = we_base_request::_(we_base_request::INT, "fu_doc_importMetadata", $this->importMetadata);
		$this->imgsSearchable = we_base_request::_(we_base_request::INT, "fu_doc_isSearchable", $this->imgsSearchable);
		$this->step = we_base_request::_(we_base_request::INT, "step", $this->step);
		$this->cmd = we_base_request::_(we_base_request::RAW, "cmd", $this->cmd);
		$this->thumbs = we_base_request::_(we_base_request::INTLIST, 'fu_doc_thumbs', $this->thumbs);
		if(!we_fileupload::EDIT_IMAGES_CLIENTSIDE){
			$this->width = we_base_request::_(we_base_request::INT, "fu_doc_width", $this->width);
			$this->height = we_base_request::_(we_base_request::INT, "fu_doc_height", $this->height);
			$this->widthSelect = we_base_request::_(we_base_request::STRING, "fu_doc_widthSelect", $this->widthSelect);
			$this->heightSelect = we_base_request::_(we_base_request::STRING, "fu_doc_heightSelect", $this->heightSelect);
			$this->keepRatio = we_base_request::_(we_base_request::BOOL, "fu_doc_keepRatio", $this->keepRatio);
			$this->quality = we_base_request::_(we_base_request::INT, "fu_doc_quality", $this->quality);
			$this->degrees = we_base_request::_(we_base_request::INT, "fu_doc_degrees", $this->degrees);
		}
		$this->partNum = we_base_request::_(we_base_request::INT, "wePartNum", 0);
		$this->partCount = we_base_request::_(we_base_request::INT, "wePartCount", 0);
		$this->fileNameTemp = we_base_request::_(we_base_request::FILE, "weFileNameTemp", '');
		$this->maxUploadSizeMB = defined('FILE_UPLOAD_MAX_UPLOAD_SIZE') ? FILE_UPLOAD_MAX_UPLOAD_SIZE : 8; //FIMXE: 8???
		$this->maxUploadSizeB = $this->maxUploadSizeMB * 1048576;
	}

	function getHTML(){
		switch($this->cmd){
			case "content" :
				return $this->_getContent();
			case "buttons" :
				return $this->_getButtons();
			default :
				return $this->_getFrameset();
		}
	}

	function _getJS($fileinput){
		return we_html_element::jsElement('
var we_fileinput = \'<form name="we_upload_form_WEFORMNUM" method="post" action="' . WEBEDITION_DIR . 'we_cmd.php" enctype="multipart/form-data" target="imgimportbuttons">' . str_replace(["\n", "\r"], " ", $this->_getHiddens("buttons", $this->step + 1) . $fileinput) . '</form>\';
') .
			we_html_element::jsScript(JS_DIR . 'import_files.js');
	}

	function _getContent(){
		$funct = 'getStep' . we_base_request::_(we_base_request::INT, 'step', 1);

		return $this->$funct();
	}

	function getStep1(){
		unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);

		$fileupload = new we_fileupload_ui_editor();

		$moreFields = we_fileupload::EDIT_IMAGES_CLIENTSIDE ? [] : [
			'imageResize' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED2, 'rightHeadline' => false, 'noline' => true],
			'imageRotate' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED2, 'rightHeadline' => false, 'noline' => true],
			'imageQuality' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED2, 'rightHeadline' => false, 'noline' => true],
			];
		$fileupload->setFormElements(array_merge($moreFields, [
			'uploader' => ['set' => false],
			'parentId' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED2, 'rightHeadline' => false, 'noline' => true],
			'sameName' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED2, 'rightHeadline' => false],
			'importMeta' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED2, 'rightHeadline' => false, 'noline' => true],
			'categories' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED2, 'rightHeadline' => false],
			'isSearchable' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED2, 'rightHeadline' => false],
			'attributes' => ['set' => true, 'multiIconBox' => true, 'rightHeadline' => true],
			'thumbnails' => ['set' => true, 'multiIconBox' => true, 'space' => we_html_multiIconBox::SPACE_MED2, 'rightHeadline' => false],
			]));

		$cb = $this->callBack;
		$pid = $this->parentID;
		$ips = $this->isPreset;
		$fileupload->loadImageEditPropsFromSession();
		if($ips){
			$this->callBack = $cb;
			$this->parentID = $pid;
		}

		$fileupload->setFieldParentID(array(
			'setField' => true,
			'preset' => ($this->parentID ? : 0),
			'setFixed' => false,
		));

		// create Start Screen ##############################################################################
		$parts = [];
		$parts = is_array($form = $fileupload->getFormParentID('we_startform')) ? array_merge($parts, array($form)) : $parts;
		$parts = is_array($form = $fileupload->getFormSameName()) ? array_merge($parts, array($form)) : $parts;
		$parts = is_array($form = $fileupload->getFormCategories()) ? array_merge($parts, array($form)) : $parts;

		if(permissionhandler::hasPerm("NEW_GRAFIK")){
			$parts = is_array($form = $fileupload->getFormImportMeta()) ? array_merge($parts, array($form)) : $parts;
			$parts = is_array($form = $fileupload->getFormIsSearchable()) ? array_merge($parts, array($form)) : $parts;

			if(we_base_imageEdit::gd_version() > 0){
				$parts = is_array($form = $fileupload->getFormThumbnails()) ? array_merge($parts, array($form)) : $parts;
				$parts = is_array($form = $fileupload->getFormImageResize()) ? array_merge($parts, array($form)) : $parts;
				$parts = is_array($form = $fileupload->getFormImageRotate()) ? array_merge($parts, array($form)) : $parts;
				$parts = is_array($form = $fileupload->getFormImageQuality()) ? array_merge($parts, array($form)) : $parts;
			} else {
				$parts[] = array(
					"headline" => "",
					"html" => we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[add_description_nogdlib]'), we_html_tools::TYPE_INFO, ""),
				);
			}
			$foldAt = we_fileupload::EDIT_IMAGES_CLIENTSIDE ? -1 : 3;
		} else {
			$foldAt = -1;
		}

		$wepos = weGetCookieVariable("but_weimportfiles");
		$content = we_html_element::htmlHiddens(array(
				'we_cmd[0]' => 'import_files',
				'callBack' => $this->callBack,
				'cmd' => 'content',
				'step' => '2'
			)) .
			we_html_multiIconBox::getJS() .
			we_html_multiIconBox::getHTML("weimportfiles", $parts, 30, "", $foldAt, g_l('importFiles', '[image_options_open]'), g_l('importFiles', '[image_options_close]'), ($wepos === "down"), g_l('importFiles', '[step1]'));

		$startsrceen = we_html_element::htmlDiv(
				array(
				"id" => "start"
				), we_html_element::htmlForm(
					array(
					"action" => WEBEDITION_DIR . "we_cmd.php",
					"name" => "we_startform",
					"method" => "post"
					), $content));

		$body = we_html_element::htmlBody(array(
				"class" => "weDialogBody"
				), $startsrceen);

		return $this->_getHtmlPage($body, $this->_getJS(''));
	}

	function getStep2(){
		$uploader = new we_fileupload_ui_importer('we_File');
		$uploader->setImageEditProps(array(
			'parentID' => $this->parentID,
			'sameName' => $this->sameName,
			'importMetadata' => $this->importMetadata,
			'isSearchable' => $this->imgsSearchable,
			'thumbnails' => $this->thumbs,
			'imageWidth' => $this->width,
			'imageHeight' => $this->height,
			'widthSelect' => $this->widthSelect,
			'heightSelect' => $this->heightSelect,
			'keepRatio' => $this->keepRatio,
			'quality' => $this->quality,
			'degrees' => $this->degrees,
			'categories' => $this->categories
		));
		$uploader->saveImageEditPropsInSession();
		$uploader->setCallback($this->callBack);
		$body = $uploader->getHTML($this->_getHiddens(true));

		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', $uploader->getCss() . $uploader->getJs() . we_html_multiIconBox::getDynJS("uploadFiles", 30), $body);
	}

	function getStep3(){
		// create Second Screen ##############################################################################
		$parts = [];

		if(isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'])){

			$filelist = "";
			foreach($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] as $err){
				$filelist .= '- ' . $err["filename"] . ' => ' . $err['error'] . we_html_element::htmlBr();
			}
			unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);

			$parts[] = array(
				'html' => we_html_tools::htmlAlertAttentionBox(sprintf(str_replace('\n', '<br/>', g_l('importFiles', '[error]')), $filelist), we_html_tools::TYPE_ALERT, 520, false));
		} else {

			$parts[] = array(
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[finished]'), we_html_tools::TYPE_INFO, 520, false)
			);
		}

		$content = we_html_element::htmlForm(array(
				"action" => WEBEDITION_DIR . "we_cmd.php",
				"name" => "we_startform", "method" => "post"
				), we_html_element::htmlHidden('step', 3) .
				we_html_multiIconBox::getHTML("uploadFiles", $parts, 30, "", -1, "", "", "", g_l('importFiles', '[step3]'))); // bugfix 1001

		$body = we_html_element::htmlBody(array(
				"class" => "weDialogBody"
				), $content);
		return $this->_getHtmlPage($body);
	}

	function _getButtons(){
		$bodyAttribs = array('class' => "weDialogButtonsBody");
		$cancelButton = we_html_button::create_button(we_html_button::CANCEL, "javascript:cancel()", true, 0, 0, '', '', false, false);
		$closeButton = we_html_button::create_button(we_html_button::CLOSE, "javascript:cancel()");

		$js = we_html_element::jsElement('
function back() {
	if(top.imgimportcontent.document.we_startform.step.value=="2") {
		top.location.href=WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=import&we_cmd[1]=' . we_import_functions::TYPE_LOCAL_FILES . '";
	} else {
		top.location.href=WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=import_files";
	}
}

function weCheckAC(j){
	if(top.imgimportcontent.YAHOO.autocoml){
		feld = top.imgimportcontent.YAHOO.autocoml.checkACFields();
		if(j<30){
			if(feld.running) {
				setTimeout(weCheckAC,100,j++);
			} else {
				return feld.valid;
			}
		} else {
			return false;
		}
	} else {
		return true;
	}
}

// TODO: let we_fileupload deliver fn
function cancel() {
	var cf = top.imgimportcontent;
	if(cf.weFileUpload !== undefined){
		cf.we_FileUpload.cancelUpload();
	} else {
		top.close();
	}
}

function next() {
	var cf = top.imgimportcontent;

	if (cf.document.getElementById("start") && cf.document.getElementById("start") && cf.document.getElementById("start").style.display != "none") {
		' . (permissionhandler::hasPerm('EDIT_KATEGORIE') ? 'top.imgimportcontent.selectCategories();' : '') . '
		cf.document.we_startform.submit();
	} else {
		if(cf.we_FileUpload !== undefined){
			cf.we_FileUpload.startUpload();
		} else {
			alert("what\'s wrong?");
		}
	}

}');
		$prevButton = we_html_button::create_button(we_html_button::BACK, "javascript:back();", true, 0, 0, "", "", false);
		$prevButton2 = we_html_button::create_button(we_html_button::BACK, "javascript:back();", true, 0, 0, "", "", false, false);
		$nextButton = we_html_button::create_button(we_html_button::NEXT, "javascript:next();", true, 0, 0, "", "", $this->step > 0, false);

		// TODO: let we_fileupload set pb
		$pb = new we_progressBar();
		$pb->setStudLen(200);
		$pb->addText(sprintf(g_l('importFiles', '[import_file]'), 1), 0, "progress_title");
		$progressbar = '<div id="progressbar" style="margin:0 0 6px 12px;' . (($this->step == 0) ? 'display:none;' : '') . '">' . $pb->getHTML() . '</div>';
		$js .= $pb->getJSCode();

		$prevNextButtons = $prevButton ? $prevButton . $nextButton : null;

		$table = new we_html_table(array('class' => 'default', "width" => "100%"), 1, 2);
		$table->setCol(0, 0, null, $progressbar);
		$table->setCol(0, 1, array("styke" => "text-align:right"), we_html_element::htmlDiv(array(
				'id' => 'normButton'
				), we_html_button::position_yes_no_cancel($prevNextButtons, null, $cancelButton, 10, '', [], 10)));

		if($this->step == 3){
			$table->setCol(0, 0, null, '');
			$table->setCol(0, 1, array("style" => "text-align:right"), we_html_element::htmlDiv(array(
					'id' => 'normButton'
					), we_html_button::position_yes_no_cancel($prevButton2, null, $closeButton, 10, '', [], 10)));
		}

		$content = $table->getHtml();
		$body = we_html_element::htmlBody($bodyAttribs, $content);

		return $this->_getHtmlPage($body, $js);
	}

	function _getHiddens($noCmd = false){
		$moreHiddens = we_fileupload::EDIT_IMAGES_CLIENTSIDE ? [] : ['fu_doc_width' => $this->width,
			'fu_doc_height' => $this->height,
			'fu_doc_widthSelect' => $this->widthSelect,
			'fu_doc_heightSelect' => $this->heightSelect,
			'fu_doc_keepRatio' => $this->keepRatio,
			'fu_doc_degrees' => $this->degrees,
			'fu_doc_quality' => $this->quality,
			];

		return ($noCmd ? '' : we_html_element::htmlHidden('cmd', 'buttons')) . we_html_element::htmlHiddens(array_merge(array(
				'step' => 1,
				// these are used by we_fileupload to grasp these values AND by editor to have when going back one step
				'fu_file_parentID' => $this->parentID,
				'fu_file_sameName' => $this->sameName,
				'fu_doc_thumbs' => $this->thumbs,
				'fu_doc_categories' => $this->categories,
				'fu_doc_isSearchable' => $this->imgsSearchable,
				'fu_doc_importMetadata' => $this->importMetadata,
		), $moreHiddens));
	}

	function _getFrameset(){
		$step = we_base_request::_(we_base_request::INT, 'step', -1);

		$body = we_html_element::htmlBody(array('id' => 'weMainBody')
				, we_html_element::htmlIFrame('imgimportcontent', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import_files&we_cmd[1]=" . $this->parentID . "&cmd=content" . ($step > -1 ? '&step=' . $step : '') . '&we_cmd[3]=' . $this->callBack, 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;') .
				we_html_element::htmlIFrame('imgimportbuttons', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import_files&cmd=buttons" . ($step > -1 ? '&step=' . $step : '') . '&we_cmd[3]=' . $this->callBack, 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;', '', '', false)
		);

		return $this->_getHtmlPage($body);
	}

	function _getHtmlPage($body, $js = ""){
		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', weSuggest::getYuiFiles() . $js, $body);
	}

}
