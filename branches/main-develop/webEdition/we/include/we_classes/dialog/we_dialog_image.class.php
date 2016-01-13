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
class we_dialog_image extends we_dialog_base{

	private $weFileupload = null;
	var $ClassName = __CLASS__;
	var $changeableArgs = array(
		"type",
		"extSrc",
		"fileID",
		"src",
		"fileSrc",
		"width",
		"height",
		"hspace",
		"vspace",
		"border",
		"alt",
		"align",
		"name",
		"thumbnail",
		"ratiow",
		"class",
		"title",
		"longdescid",
		"longdescsrc",
		"longdesc"
	);

	function __construct($noInternals = false){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', '[insert_image]');
		$this->noInternals = $noInternals;
		$this->initFileUploader();
	}

	function initBySrc($src, $width = 0, $height = 0, $hspace = 0, $vspace = 0, $border = 0, $alt = '', $align = '', $name = '', $class = '', $title = '', $longdesc = ''){
		if($src){
			$this->args['src'] = $src;
			$tokkens = explode('?', $src);
			$id = false;
			$thumb = 0;

			if(count($tokkens) == 2){
				parse_str($tokkens[1], $foo);
				if(isset($foo['id'])){
					$id = $foo['id'];
					$_fileScr = $tokkens[0];
				} else if(isset($foo['thumb'])){
					$foo = explode(',', $foo['thumb']);
					$id = $foo[0];
					$thumb = $foo[1];
					$_fileScr = id_to_path($id);
				}
			}

			if($id !== false){
				$this->args["type"] = we_base_link::TYPE_INT;
				$this->args["extSrc"] = '';
				$this->args["fileID"] = $id;
				$this->args["fileSrc"] = $id == 0 ? '' : $_fileScr;
				$this->args["thumbnail"] = $thumb;
			} else {
				$this->args["type"] = we_base_link::TYPE_EXT;
				$this->args["extSrc"] = preg_replace(array(
					'|^https?://' . $_SERVER['SERVER_NAME'] . '(/.*)$|i',
					'|^' . WEBEDITION_DIR . 'we_cmd.php[^"\'#]+(#.*)$|',
					'|^' . WEBEDITION_DIR . '|',
						), array('$1', '$1', ''), $this->args["src"]);
				$this->args["fileID"] = '';
				$this->args["fileSrc"] = '';
				$this->args["thumbnail"] = 0;
			}
		} else {
			$this->args["type"] = we_base_link::TYPE_INT;
			$this->args["extSrc"] = '';
			$this->args["fileSrc"] = '';
		}
		$this->initAttributes($width, $height, $hspace, $vspace, $border, $alt, $align, $name, $class, $title, $longdesc);
	}

	function initAttributes($width = 0, $height = 0, $hspace = 0, $vspace = 0, $border = 0, $alt = '', $align = '', $name = '', $class = '', $title = '', $longdesc = ''){
		$tokkens = explode('?', $longdesc);
		$longdescid = '';
		if(count($tokkens) == 2){
			$foo = explode('=', $tokkens[1]);
			if(count($foo) == 2){
				if($foo[0] === 'id'){
					$longdescid = $foo[1];
				}
			}
		}

		$this->args['width'] = $width;
		$this->args['height'] = $height;
		$this->args['hspace'] = $hspace;
		$this->args['vspace'] = $vspace;
		$this->args['border'] = $border;
		$this->args['alt'] = $alt;
		$this->args['align'] = $align;
		$this->args['name'] = $name;
		$this->args['class'] = $class;
		$this->args['title'] = $title;
		$this->args['longdesc'] = $longdesc;
		$this->args['longdescid'] = $longdescid;
		$this->args['longdescsrc'] = ($longdesc ? $tokkens[0] : '');
		$this->args['ratio'] = we_base_request::_(we_base_request::INT, 'we_dialog_args', 1, 'ratio');
	}

	function initByFileID($fileID, $width = 0, $height = 0, $hspace = 0, $vspace = 0, $border = 0, $alt = '', $align = '', $name = '', $thumb = '', $class = '', $title = '', $longdesc = ''){
		if($fileID){
			$this->args['type'] = we_base_link::TYPE_INT;
			$this->args['extSrc'] = '';
			$this->args['fileID'] = $fileID;
			if($thumb){
				$thumbObj = new we_thumbnail();
				$thumbObj->initByImageIDAndThumbID($fileID, $thumb);
				$thumbpath = $thumbObj->getOutputPath();
				$this->args['thumbnail'] = $thumb;
				$this->args['fileSrc'] = id_to_path($fileID);
				$this->args['src'] = $thumbpath . '?thumb=' . $fileID . ',' . $thumb;
				$width = $thumbObj->getOutputWidth();
				$height = $thumbObj->getOutputHeight();
				unset($thumbObj);
			} else {
				$this->args['thumbnail'] = '';
				$this->args['fileSrc'] = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->args['fileID']), '', $this->db);
				$this->args['src'] = $this->args['fileSrc'] . '?id=' . $fileID;
			}
			$this->args['ratio'] = 1;
		}
		$this->initAttributes($width, $height, $hspace, $vspace, $border, $alt, $align, $name, $class, $title, $longdesc);
	}

	function initByHttp(){
		we_dialog_base::initByHttp();
		$src = $this->getHttpVar(we_base_request::URL, 'src');
		$width = $this->getHttpVar(we_base_request::UNIT, 'width');
		$height = $this->getHttpVar(we_base_request::UNIT, 'height');
		$hspace = $this->getHttpVar(we_base_request::INT, 'hspace');
		$vspace = $this->getHttpVar(we_base_request::INT, 'vspace');
		$class = $this->getHttpVar(we_base_request::STRING, 'class');
		$title = $this->getHttpVar(we_base_request::STRING, 'title');
		$longdescsrc = $this->getHttpVar(we_base_request::URL, 'longdescsrc');
		$longdescid = $this->getHttpVar(we_base_request::INT, 'longdescid');
		if($longdescsrc && $longdescid){
			$longdesc = $longdescsrc . '?id=' . $longdescid;
		} else {
			$longdesc = $this->getHttpVar(we_base_request::STRING, 'longdesc');
		}
		$border = $this->getHttpVar(we_base_request::UNIT, 'border');
		$alt = $this->getHttpVar(we_base_request::STRING, 'alt');
		$align = $this->getHttpVar(we_base_request::STRING, 'align');
		$name = $this->getHttpVar(we_base_request::STRING, 'name');
		$type = $this->getHttpVar(we_base_request::STRING, 'type');
		$thumbnail = $this->getHttpVar(we_base_request::INT, 'thumbnail');

		$type = ($type ? : we_base_link::TYPE_EXT);
		if($src && !$thumbnail){
			$this->initBySrc($src, $width, $height, $hspace, $vspace, $border, $alt, $align, $name, $class, $title, $longdesc);
		} else if($type){
			$fileID = $this->getHttpVar(we_base_request::INT, 'fileID');

			switch($type){
				case we_base_link::TYPE_EXT:
					$extSrc = $this->getHttpVar(we_base_request::URL, 'extSrc', '');
					$this->initBySrc($extSrc, $width, $height, $hspace, $vspace, $border, $alt, $align, $name, $class, $title, $longdesc);
					break;
				case we_base_link::TYPE_INT:
					if(we_base_request::_(we_base_request::BOOL, 'imgChangedCmd') && $fileID){
						$imgpath = $_SERVER['DOCUMENT_ROOT'] . id_to_path($fileID);
						$imgObj = new we_imageDocument();
						$imgObj->initByID($fileID);

						$preserveData = (we_base_request::_(we_base_request::BOOL, 'wasThumbnailChange') || we_base_request::_(we_base_request::BOOL, 'isTinyMCEInitialization'));
						$width = $imgObj->getElement('width');
						$height = $imgObj->getElement('height');
						$alt = $preserveData ? $alt : $imgObj->getElement('alt');
						$hspace = $preserveData ? $hspace : $imgObj->getElement('hspace');
						$vspace = $preserveData ? $vspace : $imgObj->getElement('vspace');
						$title = $preserveData ? $title : $imgObj->getElement('title');
						$name = $preserveData ? $name : $imgObj->getElement('name');
						$align = $preserveData ? $align : $imgObj->getElement('align');
						$border = $preserveData ? $border : $imgObj->getElement('border');
						$longdesc = $preserveData ? $longdesc : ($imgObj->getElement('longdescid') ? (id_to_path($imgObj->getElement('longdescid')) . '?id=' . $imgObj->getElement('longdescid')) : $longdesc);
						$alt = $preserveData ? $alt : f('SELECT c.Dat as Dat FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON c.ID=l.CID WHERE l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND l.DID=' . intval($fileID) . ' AND l.nHash=x\'' . md5("alt") . '\'', '', $this->db);
					}
					$this->initByFileID($fileID, $width, $height, $hspace, $vspace, $border, $alt, $align, $name, $thumbnail, $class, $title, $longdesc);
					break;
			}
		} else {
			$this->defaultInit();
		}
	}

	function defaultInit(){
		$this->args['src'] = '';
		$this->args['width'] = 0;
		$this->args['height'] = 0;
		$this->args['hspace'] = 0;
		$this->args['vspace'] = 0;
		$this->args['class'] = '';
		$this->args['title'] = '';
		$this->args['longdesc'] = '';
		$this->args['border'] = 0;
		$this->args['alt'] = '';
		$this->args['align'] = '';
		$this->args['name'] = '';
		$this->args['type'] = we_base_link::TYPE_EXT;
		$this->args['ratio'] = 1;
	}

	private function initFileUploader(){
		$this->weFileupload = new we_fileupload_ui_editor(we_base_ContentTypes::IMAGE, '', 'dialog');
		$this->weFileupload->setCallback('top.doOnImportSuccess(scope.weDoc);');
		//$this->weFileupload->setIsInternalBtnUpload(true);
		$this->weFileupload->setDimensions(array('dragWidth' => 374, 'inputWidth' => 378));
		$this->weFileupload->setFormElements(array(
			'parentId' => array('set' => true, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true),
			'sameName' => array('set' => true, 'multiIconBox' => true, 'rightHeadline' => true,),
			'importMeta' => array('set' => true, 'multiIconBox' => true, 'rightHeadline' => true, 'noline' => true),
			'categories' => array('set' => true, 'multiIconBox' => true, 'rightHeadline' => true,),
			'isSearchable' => array('set' => true, 'multiIconBox' => true, 'space' => 130, 'rightHeadline' => false,),
			'attributes' => array('set' => true, 'multiIconBox' => true, 'rightHeadline' => true,),
			'thumbnails' => array('set' => true, 'multiIconBox' => true, 'rightHeadline' => true,),
			'imageResize' => array('set' => true, 'multiIconBox' => true, 'space' => 130, 'rightHeadline' => false, 'noline' => true),
			'imageRotate' => array('set' => true, 'multiIconBox' => true, 'space' => 130, 'rightHeadline' => false, 'noline' => true),
			'imageQuality' => array('set' => true, 'multiIconBox' => true, 'space' => 130, 'rightHeadline' => false, 'noline' => true),
			'tableProperties' => array('foldAtNr' => 3, 'foldAtOpen' => g_l('importFiles', '[image_options_open]'), 'foldAtClose' => g_l('importFiles', '[image_options_close]'))
		));
		$this->weFileupload->setEditorJS(array(
			'writebackTarget' => '',
			'customCallback' => '',
			'predefinedCallback' => 'imagedialog'
		));
		$this->weFileupload->setFieldParentID(array(
			'setField' => true,
			'preset' => IMAGESTARTID_DEFAULT,
		));
	}

	/* use parent
	  function getFormHTML(){}
	 */

	function getFormJsOnSubmit(){
		return ' onsubmit="return fsubmit(this)"';
	}

	function getHeaderHTML($printJS_Style = false){
		return parent::getHeaderHTML($printJS_Style, $this->weFileupload->getJs() . $this->weFileupload->getEditorJs() . $this->weFileupload->getCss());
	}

	function getDialogContentHTML(){
		$yuiSuggest = & weSuggest::getInstance();
		if($this->noInternals || (isset($this->args['outsideWE']) && $this->args['outsideWE'] == 1)){
			$extSrc = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[extSrc]", 30, (isset($this->args["extSrc"]) ? $this->args["extSrc"] : ""), "", "", "text", 410), "", "left", "defaultfont", '', "", "", "", "", 0);
			$intSrc = '';
			$thumbnails = '';

			$_longdesc = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('we_dialog_args[longdesc]', 30, str_replace('"', '&quot;', (isset($this->args["longdesc"]) ? $this->args["longdesc"] : "")), "", '', "text", 520), g_l('weClass', '[longdesc_text]'));
		} else {
			/**
			 * input for external image files
			 */
			$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[extSrc]'].value");
			$wecmdenc4 = we_base_request::encCmd("opener.document.we_form.elements['radio_type'][0].checked=true;top.we_form.elements['we_dialog_args[type]'].value='" . we_base_link::TYPE_INT . "';opener.imageChanged();");
			$but = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ?
					we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','',document.we_form.elements['we_dialog_args[extSrc]'].value,'" . $wecmdenc4 . "')"
					) : "";
			$openbutton = we_html_button::create_button(we_html_button::EDIT, "javascript:var f=top.document.we_form.elements['we_dialog_args[extSrc]']; if(f.value && f.value !== '" . we_base_link::EMPTY_EXT . "'){new (WE().util.jsWindow)(window, f.value, '_blank', -1, -1, 500, 550, true, true, true);}", true, 0, 0, '', '', true, false, '_ext', false, g_l('wysiwyg', '[openNewWindow]'));

			$radioButtonExt = we_html_forms::radiobutton(we_base_link::TYPE_EXT, (isset($this->args["type"]) && $this->args["type"] == we_base_link::TYPE_EXT), "radio_type", g_l('wysiwyg', '[external_image]'), true, "defaultfont", "if(this.form.elements['radio_type'][2].checked){this.form.elements['we_dialog_args[type]'].value='" . we_base_link::TYPE_EXT . "';top.document.getElementById('imageExt').style.display='block';top.document.getElementById('imageInt').style.display='none';top.document.getElementById('imageUpload').style.display='none';}imageChanged();");
			$textInput = we_html_tools::htmlTextInput("we_dialog_args[extSrc]", 30, (isset($this->args["extSrc"]) ? $this->args["extSrc"] : ""), "", ' onfocus="if(this.form.elements[\'radio_type\'][2].checked){imageChanged();}" onchange="imageChanged();if(this.value !== \'\' && this.value !== \'' . we_base_link::EMPTY_EXT . '\'){weButton[\'enable\'](\'btn_edit_ext\')}else{weButton[\'disable\'](\'btn_edit_ext\')}" ', "text", 315);
			$extSrc = we_html_tools::htmlFormElementTable($textInput, '', "left", "defaultfont", $but, $openbutton, '', '', '', 0);

			/**
			 * input for webedition internal image files
			 */
			$cmd1 = "document.we_form.elements['we_dialog_args[fileID]'].value";
			$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[fileSrc]'].value");
			$wecmdenc3 = we_base_request::encCmd("opener.document.we_form.elements['radio_type'][0].checked=true;opener.document.we_form.elements['we_dialog_args[type]'].value='" . we_base_link::TYPE_INT . "';opener.imageChanged();");
			$startID = $this->args['selectorStartID'] ? : (IMAGESTARTID_DEFAULT ? : 0);

			$but = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_image'," . $cmd1 . ",'" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "'," . $startID . ",'','" . we_base_ContentTypes::IMAGE . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");
			$radioButtonInt = we_html_forms::radiobutton(we_base_link::TYPE_INT, (isset($this->args["type"]) && $this->args["type"] == we_base_link::TYPE_INT), "radio_type", g_l('wysiwyg', '[internal_image]'), true, "defaultfont", "if(this.form.elements['radio_type'][0].checked){this.form.elements['we_dialog_args[type]'].value='" . we_base_link::TYPE_INT . "';top.document.getElementById('imageInt').style.display='block';top.document.getElementById('imageExt').style.display='none';top.document.getElementById('imageUpload').style.display='none';}imageChanged();");

			$yuiSuggest->setAcId("Image");
			$yuiSuggest->setContentType("folder," . we_base_ContentTypes::IMAGE);
			$yuiSuggest->setInput("we_dialog_args[fileSrc]", str_replace('"', '&quot;', (isset($this->args["fileSrc"]) ? $this->args["fileSrc"] : "")));
			//Bug #3556:
			//$yuiSuggest->setDoOnTextfieldBlur('imageChanged();');
			//#8587: Better solution for #3556:
			$yuiSuggest->setDoOnItemSelect("if(param2[2][1]!=='undefined'&&param2[2][1]){document.we_form.elements['we_dialog_args[fileID]']=param2[2][1];if(param2[2][2]!=='undefined'&&param2[2][2]!=='folder'){imageChanged();}}");
			$yuiSuggest->setLabel('');
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult("we_dialog_args[fileID]", str_replace('"', '&quot;', (isset($this->args["fileID"]) ? $this->args["fileID"] : "")));
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setWidth(315);
			$yuiSuggest->setSelectButton($but);
			$yuiSuggest->setOpenButton(we_html_button::create_button(we_html_button::EDIT, "javascript:if(top.document.we_form.elements['yuiAcResultImage'].value){if(opener.top.doClickDirect!==undefined){var p=opener.top;}else if(opener.top.opener.top.doClickDirect!==undefined){var p=opener.top.opener.top;}else{return;}p.doClickDirect(document.we_form.elements['yuiAcResultImage'].value,'" . we_base_ContentTypes::IMAGE . "','" . FILE_TABLE . "'); }"));
			$intSrc = $yuiSuggest->getHTML();

			/**
			 * input for image upload
			 */
			$radioButtonUpload = we_html_forms::radiobutton(we_base_link::TYPE_INT, false, "radio_type", g_l('buttons_global', '[upload][value]'), true, "defaultfont", "if(this.form.elements['radio_type'][1].checked){this.form.elements['we_dialog_args[type]'].value='" . we_base_link::TYPE_INT . "';top.document.getElementById('imageInt').style.display='none';top.document.getElementById('imageExt').style.display='none';top.document.getElementById('imageUpload').style.display='block';}imageChanged();");

			/**
			 * thumbnail select list
			 */
			$thumbdata = (isset($this->args["thumbnail"]) ? $this->args["thumbnail"] : "");
			$thumbnails = '<select id="selectThumbnail" name="we_dialog_args[thumbnail]" size="1" onchange="imageChanged(true);"' . ($this->getDisplayThumbsSel() === 'none' ? ' disabled="disabled"' : '') . '>';
			$thumbnails .= '<option value="0"' . (($thumbdata == 0) ? ' selected="selected"' : '') . '>' . g_l('wysiwyg', '[nothumb]') . '</option>';

			$this->db->query('SELECT ID,Name,description FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');
			while($this->db->next_record()){
				$thumbnails .= '<option title="' . $this->db->f('description') . '" value="' . $this->db->f("ID") . '"' . (($thumbdata == $this->db->f("ID")) ? (' selected="selected"') : "") . '>' . $this->db->f("Name") . '</option>';
			}
			$thumbnails .= '</select>';

			/**
			 * longdec file chooser
			 */
			$cmd1 = "document.we_form.elements['we_dialog_args[longdescid]'].value";

			$but = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("document.we_form.elements['we_dialog_args[longdescsrc]'].value") . "','','','',''," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");
			$but2 = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['we_dialog_args[longdescid]'].value='';document.we_form.elements['we_dialog_args[longdescsrc]'].value='';");

			$yuiSuggest->setAcId("Longdesc");
			$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT . ',' . we_base_ContentTypes::HTML);
			$yuiSuggest->setInput("we_dialog_args[longdescsrc]", str_replace('"', '&quot;', (isset($this->args["longdescsrc"]) ? $this->args["longdescsrc"] : "")));
			$yuiSuggest->setLabel(g_l('weClass', '[longdesc_text]'));
			$yuiSuggest->setMaxResults(7);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult("we_dialog_args[longdescid]", (isset($this->args["longdescid"]) ? $this->args["longdescid"] : ""));
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setWidth(315);
			$yuiSuggest->setSelectButton($but);
			$yuiSuggest->setTrashButton($but2);

			$_longdesc = $yuiSuggest->getHTML();
		}


		$height = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[height]", 5, (isset($this->args["height"]) ? $this->args["height"] : ""), "", ' onkeypress="return WE().util.IsDigitPercent(event);" onkeyup="return checkWidthHeight(this);"', "text", 140), g_l('wysiwyg', '[height]'));
		$width = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[width]", 5, (isset($this->args["width"]) ? $this->args["width"] : ""), "", ' onkeypress="return WE().util.IsDigitPercent(event);" onkeyup="return checkWidthHeight(this);"', "text", 140), g_l('wysiwyg', '[width]'));
		$onclick = "checkWidthHeight(document.we_form.elements['we_dialog_args[width]']);";
		$ratio = we_html_forms::checkboxWithHidden((isset($this->args["ratio"]) ? $this->args["ratio"] : false), "we_dialog_args[ratio]", g_l('thumbnails', '[ratio]'), false, "defaultfont", $onclick);
		$hspace = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[hspace]", 5, (isset($this->args["hspace"]) ? $this->args["hspace"] : ""), "", ' onkeypress="return WE().util.IsDigit(event);"', "text", 140), g_l('wysiwyg', '[hspace]'));
		$vspace = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[vspace]", 5, (isset($this->args["vspace"]) ? $this->args["vspace"] : ""), "", ' onkeypress="return WE().util.IsDigit(event);"', "text", 140), g_l('wysiwyg', '[vspace]'));
		$border = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[border]", 5, (isset($this->args["border"]) ? $this->args["border"] : ""), "", ' onkeypress="return WE().util.IsDigit(event);"', "text", 140), g_l('wysiwyg', '[border]'));
		$name = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[name]", 30, (isset($this->args["name"]) ? $this->args["name"] : ""), "", '', "text", 315), "Name");
		$alt = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[alt]", 5, (isset($this->args["alt"]) ? $this->args["alt"] : ""), "", "", "text", 315), g_l('wysiwyg', '[altText]'));
		$title = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[title]", 5, (isset($this->args["title"]) ? $this->args["title"] : ""), "", "", "text", 315), g_l('global', '[title]'));

		$foo = '
			<select class="defaultfont" name="we_dialog_args[align]" size="1" style="width:140px;">
				<option value="">' . g_l('global', '[default]') . '</option>
				<option value="top"' . (($this->args["align"] === "top") ? "selected" : "") . '>Top</option>
				<option value="middle"' . (($this->args["align"] === "middle") ? "selected" : "") . '>Middle</option>
				<option value="bottom"' . (($this->args["align"] === "bottom") ? "selected" : "") . '>Bottom</option>
				<option value="left"' . (($this->args["align"] === "left") ? "selected" : "") . '>Left</option>
				<option value="right"' . (($this->args["align"] === "right") ? "selected" : "") . '>Right</option>
				<option value="texttop"' . (($this->args["align"] === "texttop") ? "selected" : "") . '>Text Top</option>
				<option value="absmiddle"' . (($this->args["align"] === "absmiddle") ? "selected" : "") . '>Abs Middle</option>
				<option value="baseline"' . (($this->args["align"] === "baseline") ? "selected" : "") . '>Baseline</option>
				<option value="absbottom"' . (($this->args["align"] === "absbottom") ? "selected" : "") . '>Abs Bottom</option>
			</select>';
		$align = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[alignment]'), 'left', 'defaultfont', '', '', '', '', '', '', 0);

		$classSelect = we_html_tools::htmlFormElementTable($this->getClassSelect('width: 140px;'), g_l('wysiwyg', '[css_style]'), 'left', 'defaultfont', '', '', '', '', '', '', 0);

		return array(
			array(
				'html' => '<table class="default" style="margin-bottom:4px;">
<tr><td style="width:100%"><div style="display:inline;float:left">' . ($intSrc ? $radioButtonInt : '') . '</div><div style="display:inline;float:right">' . $radioButtonUpload . '</div></tr>
<tr><td>' . $radioButtonExt . '</td><td>&nbsp;</td></tr>
</table
<table class="default" style="margin-bottom:4px;">
<tr><td><div id="imageExt" style="margin-top:4px;' . (isset($this->args["type"]) && $this->args["type"] === we_base_link::TYPE_EXT ? '' : 'display:none;') . '">' . $extSrc . '</div></td></tr>' .
				($intSrc ?
						'<tr><td><div id="imageInt" style="margin:2px 0 2px 0;' . (isset($this->args["type"]) && $this->args["type"] === we_base_link::TYPE_INT ? '' : 'display:none;') . '">' . $intSrc . '</div></td></tr>
<tr><td><div id="imageUpload" style="margin:2px 0 2px 20px;display:none;background-color:#fafafa;">' . $this->weFileupload->getHTML() . '</div></td></tr>' :
						'') .
				'</table>'
			),
			//$parts = array_merge($parts, array(array('html' => '<div id="imageUpload">' . $this->weFileupload->getHTML() . '</div>')));
			array('headline' => g_l('wysiwyg', '[image][formatting]'),
				'html' => '<table class="default" style="width:560px">
					<tr>
						<td>' . we_html_tools::htmlFormElementTable($thumbnails, g_l('wysiwyg', '[thumbnail]'), 'left', 'defaultfont', '', '', '', '', '', '', 0) . '</td>
						<td>' . $classSelect . '</td>
						<td>' . $align . '</td>
					</tr>
				</table>'
			),
			array(
				'headline' => g_l('global', '[attributes]'),
				'html' => '<table class="default" style="width:560px">
					<tr><td style="padding-bottom:15px;">' . $width . '</td><td style="padding-bottom:15px;">' . $height . '</td><td style="padding-bottom:15px;">' . $ratio . '</td></tr>
					<tr><td style="padding-bottom:15px;">' . $border . '</td><td style="padding-bottom:15px;">' . $hspace . '</td><td style="padding-bottom:15px;">' . $vspace . '</td></tr>
					<tr><td colspan="3" style="padding-bottom:15px;">' . $alt . '</td></tr>
					<tr><td colspan="3" style="padding-bottom:15px;">' . $title . '</td></tr>
					<tr><td colspan="3" style="padding-bottom:15px;">' . $_longdesc . '</td></tr>
				</table>
				<div></div>' .
				we_html_element::htmlHiddens(array(
					'we_dialog_args[name]' => (isset($this->args["name"]) ? $this->args["name"] : ''),
					'we_dialog_args[type]' => (isset($this->args["type"]) ? $this->args["type"] : we_base_link::TYPE_INT),
					'we_dialog_args[rendered_width]' => 0,
					'we_dialog_args[rendered_height]' => 0,
					'imgChangedCmd' => 0,
					'wasThumbnailChange' => 0,
					'isTinyMCEInitialization' => 0,
					'tinyMCEInitRatioH' => 0,
					'tinyMCEInitRatioW' => 0
				)) .
				weSuggest::getYuiFiles() .
				$yuiSuggest->getYuiJs() .
				we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/weimage/js/image_init.js')
			)
		);
	}

	private function getDisplayThumbsSel(){
		$_p = (isset($this->args["fileSrc"]) ? $this->args["fileSrc"] : "");
		$tmp = $_p ? explode('.', $_p) : array();
		$extension = count($tmp) > 1 ? '.' . $tmp[count($tmp) - 1] : '';
		unset($_p);

		return (we_base_imageEdit::gd_version() > 0 && we_base_imageEdit::is_imagetype_supported(isset(we_base_imageEdit::$GDIMAGE_TYPE[strtolower($extension)]) ? we_base_imageEdit::$GDIMAGE_TYPE[strtolower($extension)] : "") && isset($this->args["type"]) && $this->args["type"] == we_base_link::TYPE_INT) ? "block" : "none";
	}

	function cmdFunction(array $args){
		switch(isset($this->we_cmd[0]) ? $this->we_cmd[0] : ''){
			case 'update_editor':
				//fill in all fields
				$js = '
					top.document.we_form["we_cmd[0]"].value = "";
					var inputElem;';
				foreach($args as $k => $v){
					$js .= $k !== 'cssclass' ? '
						if(inputElem = top.document.we_form.elements["we_dialog_args[' . $k . ']"]){
							inputElem.value = "' . $v . '";
						}' : '';
				}
				$js .= '

						try{' .
						($this->getDisplayThumbsSel() === 'none' ? 'top.document.getElementById("selectThumbnail").setAttribute("disabled", "disabled");' : 'top.document.getElementById("selectThumbnail").removeAttribute("disabled");') . '
						} catch(err){}

						var rh = ' . (intval($args["width"] * $args["height"]) ? ($this->args["width"] / $args["height"]) : 0) . ';
						var rw = ' . (intval($args["width"] * $args["height"]) ? ($this->args["height"] / $args["width"]) : 0) . ';
						if(top.document.we_form.tinyMCEInitRatioH !== undefined) top.document.we_form.tinyMCEInitRatioH.value = rh;
						if(top.document.we_form.tinyMCEInitRatioW !== undefined) top.document.we_form.tinyMCEInitRatioW.value = rw;
					';

				echo we_html_tools::getHtmlTop($this->dialogTitle, '', '', we_html_element::jsElement($js), we_html_element::htmlBody());
				break;
			default:
				if(isset($args["thumbnail"]) && $args["thumbnail"] && $args["fileID"]){
					$thumbObj = new we_thumbnail();
					$thumbObj->initByImageIDAndThumbID($args["fileID"], $args["thumbnail"]);
					if(!file_exists($thumbObj->getOutputPath(true))){
						$thumbObj->createThumb();
					}
				}

				$attribs = we_base_request::_(we_base_request::BOOL, 'imgChangedCmd') && !we_base_request::_(we_base_request::BOOL, 'wasThumbnailChange') ? we_base_request::_(we_base_request::STRING, 'we_dialog_args') : $args;
				return we_dialog_base::getTinyMceJS() .
						we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/weimage/js/image_insert.js') .
						'<form name="tiny_form">' .
						we_html_element::htmlHiddens(array(
							"src" => (isset($args["src"]) ? $args["src"] : ''),
							"width" => (intval($attribs["width"]) === 0 ? '' : $attribs["width"]),
							"height" => (intval($attribs["height"]) === 0 ? '' : $attribs["height"]),
							"hspace" => $attribs["hspace"],
							"vspace" => $attribs["vspace"],
							"border" => $attribs["border"],
							"alt" => $attribs["alt"],
							"align" => $attribs["align"],
							"name" => $attribs["name"],
							"class" => $attribs["cssclass"],
							"title" => $attribs["title"],
							"longdesc" => (intval($attribs["longdescid"]) ? $attribs["longdescsrc"] . '?id=' . intval($attribs["longdescid"]) : '')
						)) . '</form>';
		}
	}

	function getJs(){
		$yuiSuggest = & weSuggest::getInstance();
		$css = !empty($this->args["cssClasses"]) ? explode(',', $this->args["cssClasses"]) : array();
		return parent::getJs() . we_html_element::jsElement('
var classNames=' . ($css ? '["' . implode('","', $css) . '"]' : 'top.opener.weclassNames_tinyMce') . ' ;
var ratioh = ' . (intval($this->args["width"] * $this->args["height"]) ? ($this->args["width"] / $this->args["height"]) : 0) . ';
var ratiow = ' . (intval($this->args["width"] * $this->args["height"]) ? ($this->args["height"] / $this->args["width"]) : 0) . ';
') .
				we_html_element::jsScript(JS_DIR . 'dialogs/we_dialog_image.js') .
				weSuggest::getYuiFiles();
	}

}
