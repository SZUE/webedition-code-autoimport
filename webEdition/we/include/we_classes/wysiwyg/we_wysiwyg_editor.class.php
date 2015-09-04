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
//make sure we know which browser is used

class we_wysiwyg_editor{
	var $name = '';
	private $origName = '';
	private $fieldName = '';
	private $fieldName_clean = '';
	var $width = '';
	var $height = '';
	var $ref = '';
	var $propstring = '';
	var $elements = array();
	var $value = '';
	var $restrictContextmenu = '';
	private $tinyPlugins = array();
	private $wePlugins = array('weadaptunlink', 'weadaptbold', 'weadaptitalic', 'weimage', 'advhr', 'weabbr', 'weacronym', 'welang', 'wevisualaid', 'weinsertbreak', 'wespellchecker', 'welink', 'wefullscreen');
	private $createContextmenu = true;
	private $filteredElements = array();
	private $bgcol = '';
	private $buttonpos = '';
	private $tinyParams = '';
	private $templates = '';
	private $fullscreen = '';
	private $className = '';
	private $outsideWE = false;
	private $xml = false;
	private $removeFirstParagraph = true;
	var $charset = '';
	private $inlineedit = true;
	private $cssClasses = '';
	private $cssClassesJS = '';
	private $tinyCssClasses = '';
	var $Language = '';
	private $_imagePath;
	private $_image_languagePath;
	private $baseHref = '';
	private $showSpell = true;
	private $isFrontendEdit = false;
	private $htmlSpecialchars = true; // in wysiwyg default was "true" (although Tag-Hilfe says "false")
	private $contentCss = '';
	private $isInPopup = false;
	private $formats = '';
	private $fontnames = '';
	private $fontnamesCSV = '';
	private $fontsizes = '1 (8px)=xx-small,2 (10px)=x-small,3 (12px)=small,4 (14px)=medium,5 (18px)=large,6 (24px)=x-large,7 (36px)=xx-large'; // tinyMCE default!
	private static $allFormats = array('p', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'code', 'blockquote', 'samp');
	private static $fontstrings = array(
		'andale mono' => "Andale Mono='andale mono','times new roman',times;",
		'arial' => 'Arial=arial,helvetica,sans-serif;',
		'arial black' => "Arial Black='arial black',arial,'avant garde';",
		'book antiqua' => "Book Antiqua='book antiqua',palatino;",
		'comic sans ms' => "Comic Sans MS='comic sans ms',sans-serif;",
		'courier' => "Courier=courier,'courier new;'",
		'courier new' => "Courier New='courier new',courier;",
		'geneva' => "Geneva=geneva,arial,helvetica,sans-serif;",
		'georgia' => "Georgia=georgia,palatino,'times new roman',times,serif;",
		'helvetica' => "Helvetica=helvetica,arial,sans-serif;",
		'impact' => "Impact=impact,chicago;",
		'symbol' => "Symbol=symbol;",
		'tahoma' => "Tahoma=tahoma,arial,helvetica,sans-serif;",
		'terminal' => "Terminal=terminal,monaco;",
		'times' => "Times=times,'times new roman',serif;",
		'times new roman' => "Times New Roman='times new roman',times,serif;",
		'trebuchet ms' => "Trebuchet MS='trebuchet ms',geneva;",
		'verdana' => "Verdana=verdana,geneva,arial,helvetica,sans-serif;",
		'webdings' => "Webdings=webdings;",
		'wingdings' => "Wingdings=wingdings,'zapf dingbats';"
	);
	private static $allFontSizes = array('0.5em', '0.8em', '1em', '1.2em', '1.5em', '2em', '8px', '10px', '12px', '14px', '18px', '24px', '36px', 'xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large', 'smaller', 'larger', 'inherit');

	const CONDITIONAL = true;

	function __construct($name, $width, $height, $value = '', $propstring = '', $bgcol = '', $fullscreen = '', $className = '', $fontnames = '', $outsideWE = false, $xml = false, $removeFirstParagraph = true, $inlineedit = true, $baseHref = '', $charset = '', $cssClasses = '', $Language = '', $test = '', $spell = true, $isFrontendEdit = false, $buttonpos = 'top', $oldHtmlspecialchars = true, $contentCss = '', $origName = '', $tinyParams = '', $contextmenu = '', $isInPopup = false, $templates = '', $formats = '', $fontsizes = ''){
		$this->propstring = $propstring ? ',' . $propstring . ',' : '';
		$this->restrictContextmenu = $contextmenu ? ',' . $contextmenu . ',' : '';
		$this->createContextmenu = trim($contextmenu, " ,'") === 'none' || trim($contextmenu, " ,'") === 'false' ? false : true;
		$this->name = $name;
		if(preg_match('|^.+\[.+\]$|i', $this->name)){
			$this->fieldName = preg_replace('/^.+\[(.+)\]$/', '$1', $this->name);
			$this->fieldName_clean = str_replace(array('-', '.', '#', ' '), array('_minus_', '_dot_', '_sharp_', '_blank_'), $this->fieldName);
		};
		$this->origName = $origName;
		$this->bgcol = $bgcol;
		if(preg_match('/^#[a-f0-9]{6}$/i', $this->bgcol)){
			$this->bgcol = substr($this->bgcol, 1);
		} else if(!preg_match('/^[a-f0-9]{6}$/i', $this->bgcol) && !preg_match('/^[a-z]*$/i', $this->bgcol)){
			$this->bgcol = '';
		}
		$this->tinyParams = str_replace(array('\'', '&#34;', '&#8216;', '&#8217;'), '"', trim($tinyParams, ' ,'));
		$this->templates = trim($templates, ',');
		$this->xml = $xml ? "xhtml" : "html";
		$this->removeFirstParagraph = $removeFirstParagraph;
		$this->inlineedit = $inlineedit;
		$this->fullscreen = $fullscreen;
		$this->className = $className;
		$this->buttonpos = $buttonpos;
		$this->statuspos = $this->buttonpos != 'external' ? $this->buttonpos : 'bottom';
		$this->outsideWE = $outsideWE;

		$this->fontnamesCSV = $fontnames ? : self::getAttributeOptions('fontnames', false, false, false);
		$fontsArr = explode(',', $this->fontnamesCSV);
		natsort($fontsArr);
		foreach($fontsArr as $font){
			$f = trim($font, ', ');
			$this->fontnames .= (array_key_exists($f, self::$fontstrings)) ? self::$fontstrings[$f] : ucfirst($f) . '=' . $f . ';';
		}

		$this->fontsizes = $fontsizes ? : $this->fontsizes;

		if($formats){
			$tmp = '';
			foreach(explode(',', $formats) as $f){
				if(in_array(trim($f, ', '), self::$allFormats)){
					$tmp .= trim($f, ', ') . ',';
				}
			}
			$this->formats = trim($tmp, ',');
		} else {
			$this->formats = self::getAttributeOptions('formats', false, false, false);
		}

		if($cssClasses){
			$cc = explode(',', $cssClasses);
			$tf = '';
			$jsCl = '';
			$csvCl = '';
			foreach($cc as $val){
				$val = trim($val);
				$tf .= $val . '=' . $val . ';';
				$jsCl .= '"' . $val . '"' . ',';
				$csvCl .= $val . ',';
			}
			$this->cssClasses = rtrim($csvCl, ',');
			$this->cssClassesJS = rtrim($jsCl, ',');
			$this->tinyCssClasses = rtrim($tf, ';');
		}

		$this->contentCss = $contentCss;

		$this->Language = $Language;
		$this->showSpell = $spell;
		$this->htmlSpecialchars = $oldHtmlspecialchars;
		$this->isFrontendEdit = $isFrontendEdit;

		$this->_imagePath = IMAGE_DIR . 'wysiwyg/';
		$this->_image_languagePath = WE_INCLUDES_DIR . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/wysiwyg/';

		$this->baseHref = $baseHref ? : we_base_util::getGlobalPath();
		$this->charset = $charset;

		$this->width = $width;
		$this->height = $height;
		$this->ref = preg_replace('%[^0-9a-zA-Z_]%', '', $this->name);
		$this->hiddenValue = $value;
		$this->isInPopup = $isInPopup;

		//FIXME: what to do with scripts??
		/*
		  if($inlineedit && $value){
		  //old editor's code
		  $value = strtr($value, array("\\" => "\\\\", "\n" => '\n', "\r" => '\r'));
		  $value = str_replace(array('script', 'Script', 'SCRIPT',), array('##scr#ipt##', '##Scr#ipt##', '##SCR#IPT##',), $value);
		  $value = preg_replace('%<\?xml[^>]*>%i', '', $value);
		  $value = str_replace(array('<?', '?>',), array('||##?##||', '##||?||##'), $value);
		  }
		 *
		 */

		$this->setToolbarElements();
		$this->setFilteredElements();
		$this->value = $value;
	}

	public function getIsFrontendEdit(){
		return $this->isFrontendEdit;
	}

	public static function getEditorCommands($isTag){
		$commands = array(
			'font' => array('fontname', 'fontsize'),
			'prop' => array('formatblock', 'applystyle', 'bold', 'italic', 'underline', 'subscript', 'superscript', 'strikethrough', 'styleprops', 'removeformat', 'removetags'),
			'xhtmlxtras' => array('cite', 'acronym', 'abbr', 'lang', 'del', 'ins', 'ltr', 'rtl'),
			'color' => array('forecolor', 'backcolor'),
			'justify' => array('justifyleft', 'justifycenter', 'justifyright', 'justifyfull'),
			'list' => array('insertunorderedlist', 'insertorderedlist', 'indent', 'outdent', 'blockquote'),
			'link' => array('createlink', 'unlink', 'anchor'),
			'table' => array('inserttable', 'deletetable', 'editcell', 'editrow', 'insertcolumnleft', 'insertcolumnright', 'deletecol', 'insertrowabove', 'insertrowbelow', 'deleterow', 'increasecolspan', 'decreasecolspan'),
			'insert' => array('insertimage', 'hr', 'inserthorizontalrule', 'insertspecialchar', 'insertbreak', 'insertdate', 'inserttime'),
			'copypaste' => array(/* 'cut', 'copy', 'paste', */'pastetext', 'pasteword'),
			'layer' => array('insertlayer', 'movebackward', 'moveforward', 'absolute'),
			'essential' => array('undo', 'redo', 'spellcheck', 'selectall', 'search', 'replace', 'fullscreen', 'visibleborders'),
			'advanced' => array('editsource', 'template')
		);

		$tmp = array_keys($commands);
		unset($tmp[0]); //unsorted
		if($isTag){
			$ret = array(new weTagDataOption(g_l('wysiwyg', '[groups]'), we_html_tools::OPTGROUP));

			foreach($tmp as $command){
				$ret[] = new weTagDataOption($command);
			}
			foreach($commands as $key => $values){
				$ret[] = new weTagDataOption($key, we_html_tools::OPTGROUP);
				foreach($values as $value){
					$ret[] = new weTagDataOption($value);
				}
			}

			return $ret;
		}

		$ret = array_merge(array(
			'',
			g_l('wysiwyg', '[groups]') => we_html_tools::OPTGROUP
			), $tmp);
		foreach($commands as $key => $values){
			$ret = array_merge($ret, array($key => we_html_tools::OPTGROUP), $values);
		}
		return $ret;
	}

	public static function getAttributeOptions($name = '', $isTag = false, $asArray = true, $leadingEmpty = true){
		switch($name){
			case 'formats':
				$options = self::$allFormats;
				break;
			case 'fontnames':
				$options = array_keys(self::$fontstrings);
				break;
			case 'fontsizes':
				$options = self::$allFontSizes;
				break;
			default:
				return;
		}

		if($isTag){
			foreach($options as &$opt){
				$opt = new weTagDataOption($opt);
			}
			return $options;
		}

		$options = $leadingEmpty ? array('---') + $options : $options;
		return $asArray ? $options : implode(',', $options);
	}

	static function getHeaderHTML($loadDialogRegistry = false){
		if(defined('WE_WYSIWG_HEADER')){
			if($loadDialogRegistry && !defined('WE_WYSIWG_HEADER_REG')){
				define('WE_WYSIWG_HEADER_REG', 1);
				return we_html_element::jsScript(JS_DIR . 'weTinyMceDialogs.js');
			}
			return '';
		}
		define('WE_WYSIWG_HEADER', 1);
		if($loadDialogRegistry){
			define('WE_WYSIWG_HEADER_REG', 1);
		}

		return we_html_element::cssElement('
table.mceToolbar{
	float:left;
}

td.mceToolbar{
	padding-right:3px;
}

.tbButtonWysiwygBorder {
	border: 1px solid #006DB8;
	background-image: url(' . IMAGE_DIR . 'pixel.gif);
	margin: 0px;
	padding:4px;
	text-align: left;
	text-decoration: none;
	position: relative;
	overflow: auto;
	height: auto;
	width: auto;
}') .
			we_html_element::jsScript(WEBEDITION_DIR . 'editors/content/tinymce/jscripts/tiny_mce/tiny_mce.js') .
			($loadDialogRegistry ? we_html_element::jsScript(JS_DIR . 'weTinyMceDialogs.js') : '') .
			we_html_element::jsScript(JS_DIR . 'weTinyMceFunctions.js');
	}

	function getAllCmds(){
		$arr = array(
			'formatblock',
			'fontname',
			'fontsize',
			'applystyle',
			'bold',
			'italic',
			'underline',
			'subscript',
			'superscript',
			'strikethrough',
			'removeformat',
			'removetags',
			'forecolor',
			'backcolor',
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'insertunorderedlist',
			'insertorderedlist',
			'indent',
			'outdent',
			'createlink',
			'unlink',
			'anchor',
			'insertimage',
			'inserthorizontalrule',
			'insertspecialchar',
			'inserttable',
			'editcell',
			'insertcolumnright',
			'insertcolumnleft',
			'insertrowabove',
			'insertrowbelow',
			'deletecol',
			'deleterow',
			'increasecolspan',
			'decreasecolspan',
			'fullscreen',
			'undo',
			'redo',
			'visibleborders',
			'editsource',
			'insertbreak',
			'acronym',
			'abbr',
			'lang',
			'absolute',
			'blockquote',
			'cite',
			'del',
			'emotions',
			'hr',
			'ins',
			'insertdate',
			'insertlayer',
			'inserttime',
			'ltr',
			'movebackward',
			'moveforward',
			'nonbreaking',
			'pastetext',
			'pasteword',
			'replace',
			'rtl',
			'search',
			'styleprops',
			'template',
			'editrow',
			'deletetable',
			'selectall'
		);

		if(defined('SPELLCHECKER')){
			$arr[] = "spellcheck";
		}
		return $arr;
	}

	private function setToolbarElements(){// TODO: declare setToolbarElements
		$sep = new we_wysiwyg_ToolbarSeparator($this);
		$sepCon = new we_wysiwyg_ToolbarSeparator($this, self::CONDITIONAL);

		//group: font
		$this->elements = array_filter(
			array(new we_wysiwyg_ToolbarButton($this, "fontname", 92, 20),
				new we_wysiwyg_ToolbarButton($this, 'fontsize', 92, 20),
				$sep,
				//group: prop
				new we_wysiwyg_ToolbarButton($this, "formatblock", 92, 20),
				new we_wysiwyg_ToolbarButton($this, "applystyle", 92, 20),
				$sep,
				new we_wysiwyg_ToolbarButton($this, "bold"),
				new we_wysiwyg_ToolbarButton($this, "italic"),
				new we_wysiwyg_ToolbarButton($this, "underline"),
				new we_wysiwyg_ToolbarButton($this, "subscript"),
				new we_wysiwyg_ToolbarButton($this, "superscript"),
				new we_wysiwyg_ToolbarButton($this, "strikethrough"),
				new we_wysiwyg_ToolbarButton($this, "styleprops"),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "removeformat"),
				new we_wysiwyg_ToolbarButton($this, "removetags"),
				$sep,
				//group: xhtmlxtras
				new we_wysiwyg_ToolbarButton($this, "cite"),
				new we_wysiwyg_ToolbarButton($this, "acronym"),
				new we_wysiwyg_ToolbarButton($this, "abbr"),
				new we_wysiwyg_ToolbarButton($this, "lang"),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "del"),
				new we_wysiwyg_ToolbarButton($this, "ins"),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "ltr"),
				new we_wysiwyg_ToolbarButton($this, "rtl"),
				$sep,
				//group: color
				new we_wysiwyg_ToolbarButton($this, "forecolor", 32),
				new we_wysiwyg_ToolbarButton($this, "backcolor", 32),
				$sep,
				//group: justify
				new we_wysiwyg_ToolbarButton($this, "justifyleft"),
				new we_wysiwyg_ToolbarButton($this, "justifycenter"),
				new we_wysiwyg_ToolbarButton($this, "justifyright"),
				new we_wysiwyg_ToolbarButton($this, "justifyfull"),
				$sep,
				//group: list
				new we_wysiwyg_ToolbarButton($this, "insertunorderedlist", 32),
				new we_wysiwyg_ToolbarButton($this, "insertorderedlist", 32),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "indent"),
				new we_wysiwyg_ToolbarButton($this, "outdent"),
				new we_wysiwyg_ToolbarButton($this, "blockquote"),
				$sep,
				//group: link
				new we_wysiwyg_ToolbarButton($this, "createlink"),
				new we_wysiwyg_ToolbarButton($this, "unlink"),
				new we_wysiwyg_ToolbarButton($this, "anchor"),
				$sep,
				//group: table
				new we_wysiwyg_ToolbarButton($this, "inserttable"),
				new we_wysiwyg_ToolbarButton($this, "edittable"),
				new we_wysiwyg_ToolbarButton($this, "deletetable"),
				new we_wysiwyg_ToolbarButton($this, "editcell"),
				new we_wysiwyg_ToolbarButton($this, "editrow"),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "insertcolumnleft"),
				new we_wysiwyg_ToolbarButton($this, "insertcolumnright"),
				new we_wysiwyg_ToolbarButton($this, "deletecol"),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "insertrowabove"),
				new we_wysiwyg_ToolbarButton($this, "insertrowbelow"),
				new we_wysiwyg_ToolbarButton($this, "deleterow"),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "increasecolspan"),
				new we_wysiwyg_ToolbarButton($this, "decreasecolspan"),
				new we_wysiwyg_ToolbarButton($this, "caption"),
				new we_wysiwyg_ToolbarButton($this, "removecaption"),
				$sep,
				//group: insert
				new we_wysiwyg_ToolbarButton($this, "insertimage"),
				new we_wysiwyg_ToolbarButton($this, "hr"),
				new we_wysiwyg_ToolbarButton($this, "inserthorizontalrule"),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "insertspecialchar"),
				new we_wysiwyg_ToolbarButton($this, "nonbreaking"),
				new we_wysiwyg_ToolbarButton($this, "insertbreak"),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "insertdate"),
				new we_wysiwyg_ToolbarButton($this, "inserttime"),
				$sep,
				//group: copypaste
				new we_wysiwyg_ToolbarButton($this, "pastetext"),
				new we_wysiwyg_ToolbarButton($this, "pasteword"),
				$sep,
				//group: layer
				new we_wysiwyg_ToolbarButton($this, "insertlayer"),
				new we_wysiwyg_ToolbarButton($this, "movebackward"),
				new we_wysiwyg_ToolbarButton($this, "moveforward"),
				new we_wysiwyg_ToolbarButton($this, "absolute"),
				$sep,
				//group: essential
				new we_wysiwyg_ToolbarButton($this, "undo"),
				new we_wysiwyg_ToolbarButton($this, "redo"),
				$sepCon,
				(defined('SPELLCHECKER') && $this->showSpell ?
					new we_wysiwyg_ToolbarButton($this, 'spellcheck') :
					false),
				new we_wysiwyg_ToolbarButton($this, "selectall"),
				$sepCon,
				new we_wysiwyg_ToolbarButton($this, "search"),
				new we_wysiwyg_ToolbarButton($this, "replace"),
				$sepCon,
				($this->fullscreen ?
					false :
					new we_wysiwyg_ToolbarButton($this, "fullscreen")
				),
				new we_wysiwyg_ToolbarButton($this, "visibleborders"),
				$sep,
				//group: advanced
				new we_wysiwyg_ToolbarButton($this, "editsource"),
				new we_wysiwyg_ToolbarButton($this, "template"),
		));
	}

	private function setFilteredElements(){
		$lastSep = true;
		foreach($this->elements as $elem){
			if(is_object($elem) && $elem->showMe){
				if((!$lastSep) || ($elem->classname != "we_wysiwyg_ToolbarSeparator")){
					$this->filteredElements[] = $elem;
				}
				$lastSep = ($elem->classname === "we_wysiwyg_ToolbarSeparator");
			}
		}
		if($this->filteredElements){
			if($this->filteredElements[count($this->filteredElements) - 1]->classname === 'we_wysiwyg_ToolbarSeparator'){
				array_pop($this->filteredElements);
			}
		}
	}

	function getHTML(){
		return ($this->inlineedit ? $this->getInlineHTML() : $this->getEditButtonHTML());
	}

	private function getEditButtonHTML(){
		$js_function = $this->isFrontendEdit ? 'open_wysiwyg_win' : 'we_cmd';
		$param4 = !$this->isFrontendEdit ? '' : we_base_request::encCmd('frontend');
		$width = we_base_util::convertUnits($this->width);
		$width = (is_numeric($width) ? "'" . $width . "'" : intval($width) . '/100*screen.availWidth');
		//even if height in % doesn't make sense...
		$height = we_base_util::convertUnits($this->height);
		$height = (is_numeric($height) ? "'" . $height . "'" : intval($height) . '/100*screen.availHeight');

		return
			we_html_button::create_button("image:btn_edit_edit", "javascript:" . $js_function . "('open_wysiwyg_window', '" . $this->name . "'," . $width . ", " . $height . ",'" . $param4 . "','" . $this->propstring . "','" . $this->className . "','" . rtrim($this->fontnamesCSV, ',') . "',
			'" . $this->outsideWE . "'," . $width . "," . $height . ",'" . $this->xml . "','" . $this->removeFirstParagraph . "','" . $this->bgcol . "','" . urlencode($this->baseHref) . "','" . $this->charset . "','" . $this->cssClasses . "','" . $this->Language . "','" . we_base_request::encCmd($this->contentCss) . "',
			'" . $this->origName . "','" . we_base_request::encCmd($this->tinyParams) . "','" . we_base_request::encCmd($this->restrictContextmenu) . "', 'true', '" . $this->isFrontendEdit . "','" . $this->templates . "','" . $this->formats . "','" . $this->fontsizes . "');", true, 25);
	}

	function parseInternalImageSrc($value){
		static $t = 0;
		$t = ($t ? : time());
		$editValue = $value;
		$regs = array();
		if(preg_match_all('/src="(' . we_base_link::TYPE_INT_PREFIX . '|\?id=)(\\d+)(&time=\\d*)?/i', $editValue, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($reg[2]));
				$editValue = str_ireplace('src="' . $reg[1] . $reg[2] . (isset($reg[3]) ? $reg[3] : ''), 'src="' . ($path ? ($path . '?id=' . $reg[2] . '&time=' . $t) : (ICON_DIR . 'no_image.gif')), $editValue);
			}
		}
		if(preg_match_all('/src="' . we_base_link::TYPE_THUMB_PREFIX . '([^" ]+)/i', $editValue, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				list($imgID, $thumbID) = explode(',', $reg[1]);
				$thumbObj = new we_thumbnail();
				$thumbObj->initByImageIDAndThumbID($imgID, $thumbID);
				$editValue = str_ireplace('src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1], 'src="' . $thumbObj->getOutputPath() . "?thumb=" . $reg[1] . '&time=' . $t, $editValue);
				unset($thumbObj);
			}
		}

		return $editValue;
	}

	function getToolbarRows(){
		$tmpElements = $this->filteredElements;
		$rownr = 0;
		$rows = array(
			$rownr => array()
		);

		foreach($tmpElements as $elem){
			if($elem->classname === "we_wysiwyg_ToolbarSeparator"){
				$rownr++;
				$rows[$rownr] = array();
				continue;
			}
			$rows[$rownr][] = $elem;
		}
		return $rows;
	}

	function getContextmenuCommands(){
		if(!$this->filteredElements){
			return '{}';
		}
		$ret = '';
		foreach($this->filteredElements as $elem){
			$ret .= $elem->classname === 'we_wysiwyg_ToolbarButton' && $elem->showMeInContextmenu && self::wysiwygCmdToTiny($elem->cmd) ? '"' . self::wysiwygCmdToTiny($elem->cmd) . '":true,' : '';
		}
		return trim($ret, ',') !== '' ? '{' . trim($ret, ',') . '}' : 'false';
	}

	private static function wysiwygCmdToTiny($cmd){
		$cmdMapping = array(
			'abbr' => 'weabbr',
			'acronym' => 'weacronym',
			'anchor' => 'anchor',
			'applystyle' => 'styleselect',
			'backcolor' => 'backcolor',
			'bold' => 'weadaptbold',
			//'copy' => 'copy',
			'createlink' => 'welink',
			//'cut' => 'cut',
			'decreasecolspan' => 'split_cells',
			'deletecol' => 'delete_col',
			'deleterow' => 'delete_row',
			'editcell' => 'cell_props',
			'editsource' => 'code',
			'fontname' => 'fontselect',
			'fontsize' => 'fontsizeselect',
			'forecolor' => 'forecolor',
			'formatblock' => 'formatselect',
			'fullscreen' => 'wefullscreen',
			'increasecolspan' => 'merge_cells',
			'indent' => 'indent',
			'insertbreak' => 'weinsertbreak',
			'insertcolumnleft' => 'col_before ',
			'insertcolumnright' => 'col_after',
			'inserthorizontalrule' => 'advhr',
			'insertimage' => 'weimage',
			'insertorderedlist' => 'numlist',
			'insertrowabove' => 'row_before',
			'insertrowbelow' => 'row_after',
			'insertspecialchar' => 'charmap',
			'inserttable' => 'table',
			'insertunorderedlist' => 'bullist',
			'italic' => 'weadaptitalic',
			'justifycenter' => 'justifycenter',
			'justifyfull' => 'justifyfull',
			'justifyleft' => 'justifyleft',
			'justifyright' => 'justifyright',
			'lang' => 'welang',
			'outdent' => 'outdent',
			//'paste' => 'paste',
			'redo' => 'redo',
			'removeformat' => 'removeformat',
			'removetags' => 'cleanup',
			'spellcheck' => 'wespellchecker',
			'strikethrough' => 'strikethrough',
			'subscript' => 'sub',
			'superscript' => 'sup',
			'underline' => 'underline',
			'undo' => 'undo',
			'unlink' => 'weadaptunlink',
			'visibleborders' => 'wevisualaid',
			// the following commands exist only in tinyMCE
			'absolute' => 'absolute',
			'blockquote' => 'blockquote',
			'cite' => 'cite',
			'del' => 'del',
			'deletetable' => 'delete_table',
			'editrow' => 'row_props',
			'emotions' => 'emotions',
			'hr' => 'hr',
			'ins' => 'ins',
			'insertdate' => 'insertdate',
			'insertlayer' => 'insertlayer',
			'inserttime' => 'inserttime',
			'ltr' => 'ltr',
			'movebackward' => 'movebackward',
			'moveforward' => 'moveforward',
			'nonbreaking' => 'nonbreaking',
			'pastetext' => 'pastetext',
			'pasteword' => 'pasteword',
			'replace' => 'replace',
			'rtl' => 'rtl',
			'search' => 'search',
			'selectall' => 'selectall',
			'styleprops' => 'styleprops',
			'template' => 'template',
			'editrow' => 'row_props',
			'deletetable' => 'delete_table'
		);
		return $cmdMapping[$cmd] != '--' ? $cmdMapping[$cmd] : '';
	}

	function setPlugin($name, $doSet){
		if($doSet){
			$this->tinyPlugins[] = $name;
		}
		return $doSet;
	}

	function getTemplates(){
		if(!$this->templates){
			return '';
		}

		//FIXME: the ParentID query will only hold for depth 1 folders
		$GLOBALS['DB_WE']->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE (ID IN (' . implode(',', array_map('intval', explode(',', $this->templates))) . ') OR ParentID IN (' . implode(',', array_map('intval', explode(',', $this->templates))) . ') ) AND Published!=0 AND isFolder=0');
		$tmplArr = $GLOBALS['DB_WE']->getAll(true);

		$templates = array();
		foreach($tmplArr as $i => $cur){
			$tmplDoc = new we_document();
			$tmplDoc->initByID(intval($cur));
			switch($tmplDoc->ContentType){
				case we_base_ContentTypes::APPLICATION:
					if(!($tmplDoc->Extension === '.html' || $tmplDoc->Extension === '.htm')){
						continue;
					}
				//no break
				case we_base_ContentTypes::WEDOCUMENT:
					$templates[] = '{title: "' . $tmplDoc->getElement('Title', 'dat', 'no title ' . ($i + 1), true) . '", src : "' . $tmplDoc->Path . '?pub=' . $tmplDoc->Published . '", description: "' . $tmplDoc->getElement('Description', 'dat', 'no description ' . ($i + 1), true) . '"}';
			}
		}

		return 'template_templates : [' . implode(',', $templates) . '],';
	}

	private function getInlineHTML(){
		$rows = $this->getToolbarRows();
		$editValue = $this->parseInternalImageSrc($this->value);

		list($lang) = explode('_', $GLOBALS["weDefaultFrontendLanguage"]);

		//write theme_advanced_buttons_X
		$tinyRows = '';
		$allCommands = array();
		$k = 1;
		foreach($rows as $outer){
			$tinyRows .= 'theme_advanced_buttons' . $k . ' : "';
			foreach($outer as $inner){
				if(!$inner->cmd){
					$tinyRows .= $inner->conditional ? '' : 'separator,';
				} else if(self::wysiwygCmdToTiny($inner->cmd)){
					$tinyRows .= self::wysiwygCmdToTiny($inner->cmd) . ',';
					$allCommands[] .= self::wysiwygCmdToTiny($inner->cmd);
				}
			}
			$tinyRows = rtrim($tinyRows, ',') . '",';

			$k++;
		}
		$tinyRows .= 'theme_advanced_buttons' . $k . ' : "",';

		$this->tinyPlugins = implode(',', array_unique($this->tinyPlugins));
		$this->wePlugins = implode(',', array_intersect($this->wePlugins, $allCommands));
		$plugins = ($this->createContextmenu ? 'wecontextmenu,' : '') .
			($this->tinyPlugins ? $this->tinyPlugins . ',' : '') .
			($this->wePlugins ? $this->wePlugins . ',' : '') .
			'weutil,autolink,template,wewordcount'; //TODO: load "templates" on demand as we do it with other plugins

		$height = we_base_util::convertUnits($this->height);
		$width = we_base_util::convertUnits($this->width);
		if(is_numeric($height) && is_numeric($width)){
			//only a simple fix
			$this->height = $height - ($this->buttonpos === 'external' ? 0 : round((($k) / ($width / (5 * 22))) * 26));
		}

		$wefullscreenVars = array(
			'outsideWE' => $this->outsideWE ? "1" : "",
			'xml' => $this->xml ? "1" : "",
			'removeFirstParagraph' => $this->removeFirstParagraph ? "1" : "0",
		);

		$contentCss = $this->contentCss ? $this->contentCss . ',' : '';

		$editorLang = array_search($GLOBALS['WE_LANGUAGE'], getWELangs());
		$editorLangSuffix = $editorLang === 'de' ? 'de_' : '';

		$width = we_base_util::convertUnits($this->width);
		$width = (is_numeric($width) ? round($width / 96, 3) . 'in' : $width);
		$height = we_base_util::convertUnits($this->height);
		$height = (is_numeric($height) ? round($height / 96, 3) . 'in' : $height);

		return we_html_element::jsElement(($this->fieldName ? '
/* -- tinyMCE -- */

/*
To adress an instance of tinyMCE by JavaScript from anywhere on this document use:
TinyWrapper("SOME_WE_FIELDNAME").getEditor();

To adress the div container of an editor inlineedit=false use:
TinyWrapper("SOME_WE_FIELDNAME").getDiv();

WE_FIELDNAME of THIS instance is: "' . $this->fieldName . '"
*/

/*
//if you want to add additional event listeners to THIS instance of tinyMCE
//copy the following function to your webEdition template and edit its content
' . ($this->fieldName_clean == $this->fieldName ? '' : '//ATTENTION: the field name in the following function name was changed due to javasript restrictions!') . '

function we_tinyMCE_' . $this->fieldName_clean . '_init(ed){
	//you can adress this instance of tinyMCE using variable ed:
	//var this_editor = ed;
	//or:
	var this_editor = TinyWrapper("' . $this->fieldName . '").getEditor();

	//to adress other instances of tinyMCE on this same page use:
	TinyWrapper("OTHER_WE_FIELDNAME").getEditor();

	//example of adding event listener
	var this_editor = TinyWrapper("' . $this->fieldName . '");
	this_editor.on("KeyPress", function(ed, event){
			//console.log(ed.editorId);
			//console.log(event.charCode);
	});
}
*/

/*
read more about event listeners of the tiny editor object in the tinyMCE API,
and have a look at /webEdition/js/weTinyMceFunctions to see what TinyWrapper can do for you
*/

' : '') . '

var weclassNames_tinyMce = [' . $this->cssClassesJS . '];

var tinyMceTranslationObject = {' . $editorLang . ':{
	we:{
		group_link:"' . g_l('wysiwyg', '[links]') . '",//(insert_hyperlink)
		group_copypaste:"' . g_l('wysiwyg', '[import_text]') . '",
		group_advanced:"' . g_l('wysiwyg', '[advanced]') . '",
		group_insert:"' . g_l('wysiwyg', '[insert]') . '",
		group_indent:"' . g_l('wysiwyg', '[indent]') . '",
		//"group_view":"' . g_l('wysiwyg', '[view]') . '",
		group_table:"' . g_l('wysiwyg', '[table]') . '",
		group_edit:"' . g_l('wysiwyg', '[edit]') . '",
		group_layer:"' . g_l('wysiwyg', '[layer]') . '",
		group_xhtml:"' . g_l('wysiwyg', '[xhtml_extras]') . '",
		tt_weinsertbreak:"' . g_l('wysiwyg', '[insert_br]') . '",
		tt_welink:"' . g_l('wysiwyg', '[hyperlink]') . '",
		tt_weimage:"' . g_l('wysiwyg', '[insert_edit_image]') . '",
		tt_wefullscreen:"' . g_l('wysiwyg', '[fullscreen]') . '",
		tt_welang:"' . g_l('wysiwyg', '[language]') . '",
		tt_wespellchecker:"' . g_l('wysiwyg', '[spellcheck]') . '",
		tt_wevisualaid:"' . g_l('wysiwyg', '[visualaid]') . '",
		cm_inserttable:"' . g_l('wysiwyg', '[insert_table]') . '",
		cm_table_props:"' . g_l('wysiwyg', '[edit_table]') . '"
	}}};


var tinyMceConfObject__' . $this->fieldName_clean . ' = {
	wePluginClasses : {
		weadaptbold : "' . $editorLangSuffix . 'weadaptbold",
		weadaptitalic : "' . $editorLangSuffix . 'weadaptitalic",
		weabbr : "' . $editorLangSuffix . 'weabbr",
		weacronym : "' . $editorLangSuffix . 'weacronym"
	},

	weFullscrenParams : {
		outsideWE : "' . $wefullscreenVars['outsideWE'] . '",
		xml : "' . $wefullscreenVars['xml'] . '",
		removeFirstParagraph : "' . $wefullscreenVars['removeFirstParagraph'] . '",
		baseHref : "' . urlencode($this->baseHref) . '",
		charset : "' . $this->charset . '",
		cssClasses : "' . urlencode($this->cssClasses) . '",
		fontnames : "' . urlencode($this->fontnamesCSV) . '",
		bgcolor : "' . $this->bgcol . '",
		language : "' . $this->Language . '",
		screenWidth : screen.availWidth-10,
		screenHeight : screen.availHeight - 70,
		className : "' . $this->className . '",
		propString : "' . urlencode($this->propstring) . '",
		contentCss : "' . urlencode($this->contentCss) . '",
		origName : "' . urlencode($this->origName) . '",
		tinyParams : "' . urlencode($this->tinyParams) . '",
		contextmenu : "' . urlencode(trim($this->restrictContextmenu, ',')) . '",
		templates : "' . $this->templates . '",
		formats : "' . urlencode($this->formats) . '",
		fontsizes : "' . urlencode($this->fontsizes) . '",
	},
	weClassNames_urlEncoded : "' . urlencode($this->cssClasses) . '",
	weIsFrontend : "' . ($this->isFrontendEdit ? 1 : 0) . '",
	weWordCounter : 0,
	weRemoveFirstParagraph : "' . ($this->removeFirstParagraph ? 1 : 0) . '",

	language : "' . $lang . '",
	mode : "exact",
	elements : "' . $this->name . '",
	theme : "advanced",
	//dialog_type : "modal",

	accessibility_warnings : false,
	relative_urls : false, //important!
	convert_urls : false, //important!
	//force_br_newlines : true,
	force_p_newlines : 0, // value 0 instead of true (!) prevents adding additional lines with <p>&nbsp</p> when inlineedit="true"
	//forced_root_block : "",

	entity_encoding : "named",
	entities : "160,nbsp",
	element_format: "' . $this->xml . '",
	body_class : "' . ($this->className ? $this->className . " " : "") . 'wetextarea tiny-wetextarea wetextarea-' . $this->origName . '",

	//CallBacks
	//file_browser_callback : "openWeFileBrowser",
	//onchange_callback : "tinyMCEchanged",

	plugins : "' . $plugins . '",
	we_restrict_contextmenu: ' . $this->getContextmenuCommands() . ',

	// Theme options
	' . $tinyRows . '
	theme_advanced_toolbar_location : "' . $this->buttonpos . '", //external: toolbar floating on top of textarea
	theme_advanced_fonts: "' . $this->fontnames . '",
	theme_advanced_font_sizes: "' . $this->fontsizes . '",
	theme_advanced_styles: "' . $this->tinyCssClasses . '",
	theme_advanced_blockformats : "' . $this->formats . '",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "' . $this->statuspos . '",
	theme_advanced_resizing : false,
	//theme_advanced_source_editor_height : "500",
	//theme_advanced_source_editor_width : "700",
	theme_advanced_default_foreground_color : "#FF0000",
	theme_advanced_default_background_color : "#FFFF99",
	plugin_preview_height : "300",
	plugin_preview_width : "500",
	theme_advanced_disable : "",
	//paste_text_use_dialog: true,
	//fullscreen_new_window: true,
	content_css : "' . WEBEDITION_DIR . 'editors/content/tinymce/we_tinymce/contentCssFirst.php?' . time() . '=,' . $contentCss . WEBEDITION_DIR . 'editors/content/tinymce/we_tinymce/contentCssLast.php?' . time() . '=&tinyMceBackgroundColor=' . $this->bgcol . '",
	popup_css_add : "' . WEBEDITION_DIR . 'editors/content/tinymce/we_tinymce/tinyDialogCss.php",
	' . (in_array('template', $allCommands) && $this->templates ? $this->getTemplates() : '') . '

	// Skin options
	skin : "o2k7",
	skin_variant : "silver",

	' . ($this->tinyParams ? '//params from attribute tinyparams
	' . $this->tinyParams . ',' : '') . '

	//Fix: ad attribute id to anchor
	init_instance_callback: function(ed) {
		ed.serializer.addNodeFilter("a", function(nodes) {
			tinymce.each(nodes, function(node) {
				if(!node.attr("href") && !node.attr("id")){
					node.attr("id", node.attr("name"));
				}
			});
		});
	},

	paste_text_sticky : true,

	/* <br/><br/> => </p><p> on paste: restore default behaviour */
	/*
	paste_preprocess : function(pl, o){
		if(!pl.editor.pasteAsPlainText){
			o.content = o.content.replace(/<br\s?\/?\>s*<br\s?\/?>/g, "<p>");
		}
	},
	*/

	setup : function(ed){
		ed.settings.language = "' . array_search($GLOBALS['WE_LANGUAGE'], getWELangs()) . '";

		ed.onKeyDown.add(function(ed, e){
			if(e.ctrlKey || e.metaKey){
				switch(e.keyCode){
					' . ($this->fullscreen || $this->isInPopup ? "case 87:" : "") . '
					case 68:
					case 79:
					case 82:
						//set keyCode = -1 to just let WE-keyListener cancel event
						e.keyCode = -1;
					case 83:
					' . ($this->fullscreen || $this->isInPopup ? "" : "case 87:") . '
						e.stopPropagation();
						e.preventDefault();
						top.dealWithKeyboardShortCut(e);
						return false;
					default:
						//let tiny do it\'s job
				}
			}
		});

		ed.onInit.add(function(ed, o){
			//TODO: clean up the mess in here!
			ed.pasteAsPlainText = 0;
			ed.controlManager.setActive("pastetext", 0);
			var openerDocument = ' . (!$this->isInPopup ? '""' : ($this->isFrontendEdit ? 'top.opener.document' : 'top.opener.top.weEditorFrameController.getVisibleEditorFrame().document')) . ';
			' . ($this->isInPopup ? '
			try{
				ed.setContent(openerDocument.getElementById("' . $this->name . '").value)
			}catch(e){
				//console.log("failed getting content from main window");
			}
			' : '') . '
			' . ($this->fieldName ? '
			tinyEditors["' . $this->fieldName . '"] = ed;

			var hasOpener = false;
			try{
				hasOpener = opener ? true : false;
			} catch(e){}

			if(typeof we_tinyMCE_' . $this->fieldName_clean . '_init != "undefined"){
				try{
					we_tinyMCE_' . $this->fieldName_clean . '_init(ed);
				} catch(e){
					//nothing
				}
			} else if(hasOpener){
				if(opener.top.weEditorFrameController){
					//we are in backend
					var editor = opener.top.weEditorFrameController.ActiveEditorFrameId;
					var wedoc = null;
					try{
						wedoc = opener.top.rframe.bm_content_frame.frames[editor].frames["contenteditor_" + editor];
						wedoc.tinyEditorsInPopup["' . $this->fieldName . '"] = ed;
						wedoc.we_tinyMCE_' . $this->fieldName_clean . '_init(ed);
					}catch(e){
						//opener.console.log("no external init function for ' . $this->fieldName . ' found");
					}
					try{
						wedoc = opener.top.rframe.bm_content_frame.frames[editor].frames["editor_" + editor];
						wedoc.tinyEditorsInPopup["' . $this->fieldName . '"] = ed;
						wedoc.we_tinyMCE_' . $this->fieldName_clean . '_init(ed);
					}catch(e){
						//opener.console.log("no external init function for ' . $this->fieldName . ' found");
					}
				} else{
					//we are in frontend
					try{
						opener.tinyEditorsInPopup["' . $this->fieldName . '"] = ed;
						opener.we_tinyMCE_' . $this->fieldName_clean . '_init(ed);
					}catch(e){
						//opener.console.log("no external init function for ' . $this->fieldName . ' defined");
					}
				}
			} else{
				//console.log("no external init function for ' . $this->fieldName . ' defined");
			}
			' : '') . '
		});

		ed.onPostProcess.add(function(ed, o) {
			var c = document.createElement("div");
			c.innerHTML = o.content;
			var first = c.firstChild;

			if(first){
				if(first.innerHTML == "&nbsp;" && first == c.lastChild){
				c.innerHTML = "";
			}
			else if(ed.settings.weRemoveFirstParagraph === "1" && first.nodeName == "P"){
				var useDiv = false, div = document.createElement("div"), attribs = ["style", "class", "dir"];
				div.innerHTML = first.innerHTML;

				for(var i=0;i<attribs.length;i++){
					if(first.hasAttribute(attribs[i])){
						div.setAttribute(attribs[i], first.getAttribute(attribs[i]));
						useDiv = true;
					}
				}
				if(useDiv){
					c.replaceChild(div, first);
				} else{
					c.removeChild(first);
					c.innerHTML = first.innerHTML + c.innerHTML;
					}
				}
			}
			o.content = c.innerHTML;
		});' . ($this->isFrontendEdit ? '' : '

		/* set EditorFrame.setEditorIsHot(true) */

		// we look for editorLevel and weEditorFrameController just once at editor init
		var editorLevel = "";
		var weEditorFrame = null;

		if(typeof(_EditorFrame) != "undefined"){
			editorLevel = "inline";
			weEditorFrame = _EditorFrame;
		} else {
			if(top.opener != null && typeof(top.opener.top.weEditorFrameController) != "undefined" && typeof(top.isWeDialog) == "undefined"){
				editorLevel = "popup";
				weEditorFrame = top.opener.top.weEditorFrameController;
			} else {
				editorLevel = "fullscreen";
				weEditorFrame = null;
			}
		}

		// if editorLevel = "inline" we use a local copy of weEditorFrame.EditorIsHot
		var weEditorFrameIsHot = false;
		try{
			weEditorFrameIsHot = editorLevel == "inline" ? weEditorFrame.EditorIsHot : false;
		}catch(e){}

		// listeners for editorLevel = "inline"
		//could be rather CPU-intensive. But weEditorFrameIsHot is nearly allways true, so we could try
		ed.onKeyDown.add(function(ed) {
			if(!weEditorFrameIsHot && editorLevel == "inline" && ed.isDirty()){
				try{
					weEditorFrame.setEditorIsHot(true);
				} catch(e) {}
				weEditorFrameIsHot = true;
			}
		});

		/*
		ed.onChange.add(function(ed) {
			if(!weEditorFrameIsHot && editorLevel == "inline" && ed.isDirty()){
				try{
					weEditorFrame.setEditorIsHot(true);
				} catch(e) {}
				weEditorFrameIsHot = true;
			}
		});
		*/

		ed.onNodeChange.add(function(ed, cm, n) {
			var pc, tmp, td = ed.dom.getParent(n, "td");

			if(typeof td === "object" && td && td.getElementsByTagName("p").length === 1){
				pc = td.getElementsByTagName("p")[0].cloneNode(true);
				tmp = document.createElement("div");
				tmp.appendChild(pc);

				if(tmp.innerHTML === td.innerHTML){
					td.innerHTML = "";
					ed.selection.setContent(pc.innerHTML);
				}
			}
		});

		/*
		ed.onClick.add(function(ed) {
			if(!weEditorFrameIsHot && editorLevel == "inline" && ed.isDirty()){
				try{
					weEditorFrame.setEditorIsHot(true);
				} catch(e) {}
				weEditorFrameIsHot = true;
			}
		});
		*/

		ed.onPaste.add(function(ed) {
			if(!weEditorFrameIsHot && editorLevel == "inline" && ed.isDirty()){
				try{
					weEditorFrame.setEditorIsHot(true);
				} catch(e) {}
				weEditorFrameIsHot = true;
			}
		});

		// onSave (= we_save and we_publish) we reset the (tiny-internal) flag weEditorFrameIsHot to false
		ed.onSaveContent.add(function(ed) {
			weEditorFrameIsHot = false;
			// if is popup and we click on ok
			if(editorLevel == "popup" && ed.isDirty()){
				try{
					weEditorFrame.setEditorIsHot(true);
				} catch(e) {}
			}
		});
		') . '
	}
}
tinyMCE.addI18n(tinyMceTranslationObject);
tinyMCE.init(tinyMceConfObject__' . $this->fieldName_clean . ');
') . getHtmlTag('textarea', array(
				'wrap' => "off",
				'style' => 'color:#eeeeee; background-color:#eeeeee;  width:' . $width . '; height:' . $height . ';',
				'id' => $this->name,
				'name' => $this->name,
				'class' => 'wetextarea'
				), strtr($editValue, array('\n' => '', '&' => '&amp;')), true);
	}

}
