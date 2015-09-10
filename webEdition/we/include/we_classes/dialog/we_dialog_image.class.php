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

	var $ClassName = __CLASS__;
	var $changeableArgs = array("type",
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
		$this->dialogTitle = g_l('wysiwyg', '[edit_image]');
		$this->noInternals = $noInternals;
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
				$this->args["extSrc"] = "";
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
				$this->args["fileID"] = "";
				$this->args["fileSrc"] = "";
				$this->args["thumbnail"] = 0;
			}
		} else {
			$this->args["type"] = we_base_link::TYPE_EXT;
			$this->args["extSrc"] = we_base_link::EMPTY_EXT;
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
						$alt = $preserveData ? $alt : f('SELECT c.Dat as Dat FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON c.ID=l.CID WHERE l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND l.DID=' . intval($fileID) . ' AND l.Name="alt"', '', $this->db);
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

	/* use parent
	  function getFormHTML(){}
	 */

	function getFormJsOnSubmit(){
		return ' onsubmit="return fsubmit(this)"';
	}

	function getDialogContentHTML(){
		$yuiSuggest = & weSuggest::getInstance();
		if($this->noInternals || (isset($this->args['outsideWE']) && $this->args['outsideWE'] == 1)){
			$extSrc = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[extSrc]", 30, (isset($this->args["extSrc"]) ? $this->args["extSrc"] : ""), "", "", "text", 410), "", "left", "defaultfont", we_html_tools::getPixel(10, 2), "", "", "", "", 0);
			$intSrc = '';
			$thumbnails = '';

			$_longdesc = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('we_dialog_args[longdesc]', 30, str_replace('"', '&quot;', (isset($this->args["longdesc"]) ? $this->args["longdesc"] : "")), "", '', "text", 520), g_l('weClass', '[longdesc_text]'));
		} else {
			$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[extSrc]'].value");
			$wecmdenc4 = we_base_request::encCmd("opener.document.we_form.elements['we_dialog_args[type]'][0].checked=true;opener.imageChanged();");
			$but = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ?
					we_html_button::create_button("select", "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','',document.we_form.elements['we_dialog_args[extSrc]'].value,'" . $wecmdenc4 . "')"
					) : "";

			$radioBut = we_html_forms::radiobutton(we_base_link::TYPE_EXT, (isset($this->args["type"]) && $this->args["type"] == we_base_link::TYPE_EXT), "we_dialog_args[type]", g_l('wysiwyg', '[external_image]'), true, "defaultfont", "imageChanged();"
			);

			$extSrc = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[extSrc]", 30, (isset($this->args["extSrc"]) ? $this->args["extSrc"] : ""), "", ' onfocus="if(this.form.elements[\'we_dialog_args[type]\'][1].checked) { this.form.elements[\'we_dialog_args[type]\'][0].checked=true;imageChanged();}" onchange="this.form.elements[\'we_dialog_args[type]\'][0].checked=true;imageChanged();"', "text", 300), $radioBut, "left", "defaultfont", we_html_tools::getPixel(20, 2), $but, "", "", "", 0
			);
			$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[fileID]'].value");
			$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[fileSrc]'].value");
			$wecmdenc3 = we_base_request::encCmd("opener.document.we_form.elements['we_dialog_args[type]'][1].checked=true;opener.imageChanged();");

			$but = we_html_button::create_button("select", "javascript:we_cmd('openImgselector',document.we_form.elements['we_dialog_args[fileID]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . we_base_ContentTypes::IMAGE . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");"
			);

			$radioBut = we_html_forms::radiobutton(we_base_link::TYPE_INT, (isset($this->args["type"]) && $this->args["type"] == we_base_link::TYPE_INT), "we_dialog_args[type]", g_l('wysiwyg', '[internal_image]'), true, "defaultfont", "imageChanged();");

			$yuiSuggest->setAcId("Image");
			$yuiSuggest->setContentType("folder," . we_base_ContentTypes::IMAGE);
			$yuiSuggest->setInput("we_dialog_args[fileSrc]", str_replace('"', '&quot;', (isset($this->args["fileSrc"]) ? $this->args["fileSrc"] : "")), array("onfocus" => "document.we_form.elements[2].checked=true;", "onchange" => "document.we_form.elements['we_dialog_args[type]'][1].checked=true;"));
			//Bug #3556:
			//$yuiSuggest->setDoOnTextfieldBlur('imageChanged();');
			//#8587: Better solution for #3556:
			$yuiSuggest->setDoOnItemSelect("if(param2[2][1]!=='undefined'&&param2[2][1]){document.we_form.elements['we_dialog_args[fileID]']=param2[2][1];if(param2[2][2]!=='undefined'&&param2[2][2]!=='folder'){imageChanged();}}");
			$yuiSuggest->setLabel($radioBut);
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult("we_dialog_args[fileID]", str_replace('"', '&quot;', (isset($this->args["fileID"]) ? $this->args["fileID"] : "")));
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setWidth(300);
			$yuiSuggest->setSelectButton($but);


			$intSrc = $yuiSuggest->getHTML();

			$thumbdata = (isset($this->args["thumbnail"]) ? $this->args["thumbnail"] : "");
			$thumbnails = '<select name="we_dialog_args[thumbnail]" size="1" onchange="imageChanged(true);">' .
					'<option value="0"' . (($thumbdata == 0) ? (' selected="selected"') : "") . '>' . g_l('wysiwyg', '[nothumb]') . '</option>';
			$this->db->query('SELECT ID,Name FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');
			while($this->db->next_record()){
				$thumbnails .= '<option value="' . $this->db->f("ID") . '"' . (($thumbdata == $this->db->f("ID")) ? (' selected="selected"') : "") . '>' . $this->db->f("Name") . '</option>';
			}
			$thumbnails .= '</select>';
			$thumbnails = '<div id="selectThumbnail" style="display: ' . $this->getDisplayThumbsSel() . '">' . we_html_tools::htmlFormElementTable($thumbnails, g_l('wysiwyg', '[thumbnail]')) . '</div>';

			$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[longdescid]'].value");
			$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[longdescsrc]'].value");

			$but = we_html_button::create_button("select", "javascript:we_cmd('openImgselector',document.we_form.elements['we_dialog_args[longdescid]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','',''," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");
			$but2 = we_html_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['we_dialog_args[longdescid]'].value='';document.we_form.elements['we_dialog_args[longdescsrc]'].value='';");

			$yuiSuggest->setAcId("Longdesc");
			$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT . ',' . we_base_ContentTypes::HTML);
			$yuiSuggest->setInput("we_dialog_args[longdescsrc]", str_replace('"', '&quot;', (isset($this->args["longdescsrc"]) ? $this->args["longdescsrc"] : "")));
			$yuiSuggest->setLabel(g_l('weClass', '[longdesc_text]'));
			$yuiSuggest->setMaxResults(7);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult("we_dialog_args[longdescid]", (isset($this->args["longdescid"]) ? $this->args["longdescid"] : ""));
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setWidth(363);
			$yuiSuggest->setSelectButton($but);
			$yuiSuggest->setTrashButton($but2);

			$_longdesc = $yuiSuggest->getHTML();
		}

		$foo = we_html_tools::htmlTextInput("we_dialog_args[width]", 5, (isset($this->args["width"]) ? $this->args["width"] : ""), "", ' onkeypress="return IsDigitPercent(event);" onkeyup="return checkWidthHeight(this);"', "text", 50);

		$width = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[width]'));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[height]", 5, (isset($this->args["height"]) ? $this->args["height"] : ""), "", ' onkeypress="return IsDigitPercent(event);" onkeyup="return checkWidthHeight(this);"', "text", 50);
		$height = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[height]'));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[hspace]", 5, (isset($this->args["hspace"]) ? $this->args["hspace"] : ""), "", ' onkeypress="return IsDigit(event);"', "text", 50);
		$hspace = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[hspace]'));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[vspace]", 5, (isset($this->args["vspace"]) ? $this->args["vspace"] : ""), "", ' onkeypress="return IsDigit(event);"', "text", 50);
		$vspace = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[vspace]'));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[border]", 5, (isset($this->args["border"]) ? $this->args["border"] : ""), "", ' onkeypress="return IsDigit(event);"', "text", 50);
		$border = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[border]'));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[alt]", 5, (isset($this->args["alt"]) ? $this->args["alt"] : ""), "", "", "text", 200);
		$alt = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[altText]'));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[title]", 5, (isset($this->args["title"]) ? $this->args["title"] : ""), "", "", "text", 200);
		$title = we_html_tools::htmlFormElementTable($foo, g_l('global', '[title]'));


		$foo = '<select class="defaultfont" name="we_dialog_args[align]" size="1">
							<option value="">Default</option>
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
		$align = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[alignment]'));

		$foo = we_html_tools::htmlTextInput("we_dialog_args[name]", 30, (isset($this->args["name"]) ? $this->args["name"] : ""), "", '', "text", 200);
		$name = we_html_tools::htmlFormElementTable($foo, "Name");

		$srctable = '<table cellpadding="0" cellspacing="0" border="0">
	<tr><td class="defaultgray" valign="top">' . g_l('wysiwyg', '[image_url]') . '</td><td>' . $extSrc . '</td></tr>';
		if($intSrc){
			$srctable .= '	<tr><td>' . we_html_tools::getPixel(100, 4) . '</td><td>' . we_html_tools::getPixel(10, 4) . '</td></tr>
	<tr><td></td><td>' . $intSrc . '</td></tr>' .
					($thumbnails ?
							'	<tr><td>' . we_html_tools::getPixel(100, 4) . '</td><td>' . we_html_tools::getPixel(10, 4) . '</td></tr>
	<tr><td></td><td>' . $thumbnails . '</td></tr>' : '');
		}
		$srctable .=
				'<tr><td>' . we_html_tools::getPixel(100, 4) . '</td><td>' . we_html_tools::getPixel(10, 4) . '</td></tr>
	</table>';

		if($this->args["editor"] === 'tinyMce'){
			$classSelect = we_html_tools::htmlFormElementTable($this->getClassSelect(), g_l('wysiwyg', '[css_style]'));
		} else {
			$foo = we_html_element::jsElement('showclasss("we_dialog_args[class]","' . (isset($this->args["class"]) ? $this->args["class"] : "") . '","");');
			$classSelect = $classSelect = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', '[css_style]'));
		}

		$onclick = "checkWidthHeight(document.we_form.elements['we_dialog_args[width]']);";

		$ratio = we_html_forms::checkboxWithHidden((isset($this->args["ratio"]) ? $this->args["ratio"] : false), "we_dialog_args[ratio]", g_l('thumbnails', '[ratio]'), false, "defaultfont", $onclick);

		return array(
			array("html" => $srctable),
			array("html" => '<table cellpadding="0" cellspacing="0" border="0" width="400"><tr><td>' . $width . '</td><td>' . $height . '</td><td>' . $ratio . '</td></tr></table>'),
			array("html" => '<table cellpadding="0" cellspacing="0" border="0" width="560"><tr><td>' . $hspace . '</td><td>' . $vspace . '</td><td>' . $border . '</td><td>' . $align . '</td></tr></table><div></div>'),
			array("html" =>
				'<div style="height:240px"><table cellpadding="0" cellspacing="0" border="0" width="380">
<tr><td colspan="2">' . $name . '</td><td colspan="2">' . $alt . '</td></tr>
<tr><td colspan="4">' . we_html_tools::getPixel(150, 15) . '</td></tr>
<tr><td colspan="2">' . $classSelect . '</td><td colspan="2">' . $title . '</td></tr>
<tr><td>' . we_html_tools::getPixel(160, 15) . '</td><td>' . we_html_tools::getPixel(160, 4) . '</td><td>' . we_html_tools::getPixel(100, 4) . '</td><td>' . we_html_tools::getPixel(100, 4) . '</td></tr>
<tr><td colspan="4">' . $_longdesc . '</td></tr>
<tr><td colspan="4">' . we_html_tools::getPixel(150, 15) . '</td></tr>
</table></div>' .
				we_html_tools::hidden("imgChangedCmd", 0) . we_html_tools::hidden("wasThumbnailChange", 0) . we_html_tools::hidden("isTinyMCEInitialization", 0) .
				we_html_tools::hidden("tinyMCEInitRatioH", 0) . we_html_tools::hidden("tinyMCEInitRatioW", 0) .
				weSuggest::getYuiFiles() .
				$yuiSuggest->getYuiCss() . $yuiSuggest->getYuiJs() . we_html_element::jsScript(TINYMCE_JS_DIR . 'plugins/weimage/js/image_init.js')),
		);
	}

	private function getDisplayThumbsSel(){
		$_p = (isset($this->args["fileSrc"]) ? $this->args["fileSrc"] : "");
		$tmp = $_p ? explode('.', $_p) : array();
		$extension = count($tmp) > 1 ? '.' . $tmp[count($tmp) - 1] : '';
		unset($_p);

		return (we_base_imageEdit::gd_version() > 0 && we_base_imageEdit::is_imagetype_supported(isset(we_base_imageEdit::$GDIMAGE_TYPE[strtolower($extension)]) ? we_base_imageEdit::$GDIMAGE_TYPE[strtolower($extension)] : "") && isset($this->args["type"]) && $this->args["type"] == we_base_link::TYPE_INT) ? "block" : "none";
	}

	function cmdFunction($args){
		if(isset($this->we_cmd[0])){
			switch($this->we_cmd[0]){
				case 'update_editor':
					//fill in all fields
					$js = 'top.document.we_form["we_cmd[0]"].value = "";';
					foreach($args as $k => $v){
						$js .= 'if(typeof top.document.we_form["we_dialog_args[' . $k . ']"] !== "undefined") top.document.we_form["we_dialog_args[' . $k . ']"].value = "' . $v . '";
						';
					}

					$js .= '
						try{
							top.document.getElementById("selectThumbnail").style.display = "' . $this->getDisplayThumbsSel() . '";
						} catch(err){
						//console.log(top.document.getElementById("selectThumbnail"));
						}

						var rh = ' . (intval($args["width"] * $args["height"]) ? ($this->args["width"] / $args["height"]) : 0) . ';
						var rw = ' . (intval($args["width"] * $args["height"]) ? ($this->args["height"] / $args["width"]) : 0) . ';
						if(typeof top.document.we_form["tinyMCEInitRatioH"] !== "undefined") top.document.we_form[["tinyMCEInitRatioH"]].value = rh;
						if(typeof top.document.we_form["tinyMCEInitRatioW"] !== "undefined") top.document.we_form[["tinyMCEInitRatioW"]].value = rw;
					';

					echo we_html_tools::getHtmlTop() . we_html_element::jsElement($js) . "</head></html>";
					break;
				default:
			}
		}
	}

	function getJs(){
		$yuiSuggest = & weSuggest::getInstance();
		return parent::getJs() . we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
function we_cmd(){
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]){
    case "openDocselector":
		case "openImgselector":
		new jsWindow(url,"we_fileselector",-1,-1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ',true,true,true,true);
		break;
	case "browse_server":
		new jsWindow(url,"browse_server",-1,-1,840,400,true,false,true);
		break;
	}
}

function imageChanged(wasThumbnailChange){
	if(wasThumbnailChange != null && wasThumbnailChange){
		document.we_form.wasThumbnailChange.value="1";
	}
	if(top.opener.tinyMCECallRegisterDialog) {
		top.opener.tinyMCECallRegisterDialog(null,"block");
	}
	//document.we_form.target = "we_weImageDialog_edit_area";
	document.we_form.target = "we_we_dialog_image_cmd_frame";//TODO: send form to iFrame cmd for and for not reloading whole editor
	document.we_form.we_what.value = "cmd";
	document.we_form["we_cmd[0]"].value = "update_editor";
	document.we_form.imgChangedCmd.value = "1";
	document.we_form.submit();
}

function checkWidthHeight(field){
	var ratioCheckBox = document.getElementById("check_we_dialog_args[ratio]");
	if(ratioCheckBox.checked){
		if(field.value.indexOf("%") == -1){
			ratiow = ratiow ? ratiow :
				(field.form.elements["tinyMCEInitRatioW"].value ? field.form.elements["tinyMCEInitRatioW"].value : 0);
			ratioh = ratioh ? ratioh :
				(field.form.elements["tinyMCEInitRatioH"].value ? field.form.elements["tinyMCEInitRatioH"].value : 0);
			if(ratiow && ratioh){
				if(field.name=="we_dialog_args[height]"){
					field.form.elements["we_dialog_args[width]"].value = Math.round(field.value * ratioh);
				}else{
					field.form.elements["we_dialog_args[height]"].value = Math.round(field.value * ratiow);
				}
			}
		}else{
			ratioCheckBox.checked=false;
		}
	}
	return true;
}

				function showclasss(name, val, onCh) {' .
						(isset($this->args["cssClasses"]) && $this->args["cssClasses"] ?
								'					var classCSV = "' . $this->args["cssClasses"] . '";
									classNames = classCSV.split(/,/);' : ($this->args["editor"] === "tinyMce" ? 'classNames = top.opener.weclassNames_tinyMce;' :
										'					classNames = top.opener.we_classNames;')) . '
					document.writeln(\'<select class="defaultfont" style="width:200px" name="\'+name+\'" id="\'+name+\'" size="1"\'+(onCh ? \' onchange="\'+onCh+\'"\' : \'\')+\'>\');
					document.writeln(\'<option value="">' . g_l('wysiwyg', '[none]') . '\');
					if(typeof(classNames) != "undefined"){
						for (var i = 0; i < classNames.length; i++) {
							var foo = classNames[i].substring(0,1) == "." ?
								classNames[i].substring(1,classNames[i].length) :
								classNames[i];
							document.writeln(\'<option value="\'+foo+\'"\'+((val==foo) ? \' selected\' : \'\')+\'>.\'+foo);
						}
					}
					document.writeln(\'</select>\');
				}

var ratioh = ' . (intval($this->args["width"] * $this->args["height"]) ? ($this->args["width"] / $this->args["height"]) : 0) . ';
var ratiow = ' . (intval($this->args["width"] * $this->args["height"]) ? ($this->args["height"] / $this->args["width"]) : 0) . ';

function fsubmit(e) {
	return false;
}') .
				weSuggest::getYuiFiles();
	}

}
