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

	private $editorType = 1;
	private $readyConfig = [];

	var $name = '';
	private $origName = '';
	private $fieldName = '';
	private $fieldName_clean = '';
	var $width = 600;
	var $height = 400;
	private $origWidth = 0;
	private $origHeight = 0;
	var $ref = '';
	var $propString = '';
	var $elements = [];
	var $value = '';
	private $contextmenu = '';
	var $restrictContextmenu = '';
	private $tinyPlugins = [];
	private $wePlugins = ['wetable', 'weadaptunlink', 'weadaptbold', 'weadaptitalic', 'weimage', 'advhr', 'weabbr', 'weacronym', 'welang', 'wevisualaid', 'weinsertbreak',
		'wespellchecker', 'welink', 'wefullscreen', 'wegallery'];
	private $createContextmenu = true;
	private $filteredElements = [];
	private $bgcolor = '';
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
	private $language = '';
	private $imagePath;
	private $image_languagePath;
	private $baseHref = '';
	private $showSpell = true;
	private $isFrontendEdit = false;
	private $htmlSpecialchars = true; // in wysiwyg default was "true" (although Tag-Hilfe says "false")
	private $contentCss = '';
	private $isInPopup = false;
	private $imageStartID = 0;
	private $galleryTemplates = '';
	private $formats = '';
	private $fontnames = '';
	private $fontnamesCSV = '';
	private $toolbarRows = [];
	private $usedCommands = [];
	private $fontsizes = '1 (8px)=xx-small,2 (10px)=x-small,3 (12px)=small,4 (14px)=medium,5 (18px)=large,6 (24px)=x-large,7 (36px)=xx-large'; // tinyMCE default!

	private static $allFormats = ['p', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'code', 'blockquote', 'samp'];
	private static $fontstrings = ['andale mono' => "Andale Mono='andale mono','times new roman',times;",
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
	 ];
	private static $allFontSizes = ['0.5em', '0.8em', '1em', '1.2em', '1.5em', '2em', '8px', '10px', '12px', '14px', '18px', '24px', '36px', 'xx-small', 'x-small',
		'small', 'medium', 'large', 'x-large', 'xx-large', 'smaller', 'larger', 'inherit'];

	const CONDITIONAL = true;
	const MIN_WIDTH_INLINE = 100;
	const MIN_HEIGHT_INLINE = 100;
	const MIN_WIDTH_POPUP = 100;
	const MIN_HEIGHT_POPUP = 100;

	const TYPE_INLINE_TRUE = 'inlineTrue';
	const TYPE_INLINE_FALSE = 'inlineFalse';
	const TYPE_FULLSCREEN = 'fullscreen';
	const TYPE_EDITBUTTON = 'editButton';

	function __construct(array $readyConfig = [], $editorType = '', $name = '', $width = 600, $height = 400, $value = '', $propString = '', $bgcolor = '', $fullscreen = '', $className = '', $fontnamesCsv = '', $outsideWE = false, $xml = false, $removeFirstParagraph = true, $inlineedit = true, $baseHref = '', $charset = '', $cssClasses = '', $language = '', $test = '', $spell = true, $isFrontendEdit = false, $buttonpos = 'top', $oldHtmlspecialchars = true, $contentCss = '', $origName = '', $tinyParams = '', $contextmenu = '', $isInPopup = false, $templates = '', $formats = '', $imageStartID = 0, $galleryTemplates = '', $fontsizes = ''){
		$this->editorType = $editorType ? : self::TYPE_INLINE_TRUE;
		$this->readyConfig = $readyConfig;

		if(in_array($this->editorType, [self::TYPE_INLINE_FALSE, self::TYPE_FULLSCREEN])){
			if(empty($this->readyConfig)){
				t_e('attempt to initialize wysiwyg editor type "' . $this->editorType . '" without readyConfig');
				exit();
			}

			// we only init props that we need in function getInlineHTML
			$this->width = '100%';
			$this->height = '100%';
			$this->name = $this->readyConfig['name'];
			$this->buttonpos = $this->readyConfig['buttonpos'];
			$this->readyConfig['editorType'] = $this->editorType;

			//$this->name = $this->readyConfig['name'] = $this->readyConfig['weName'] = $this->readyConfig['elements'] = ($this->editorType === self::TYPE_FULLSCREEN ? 'wysiwygFullscreen_textarea' : $this->name);

		} else {
			$this->name = $name;
			$this->width = $width;
			$this->height = $height;
			$this->value = $value;
			$this->propString = $propString;
			$this->bgcolor = $bgcolor;
			$this->fullscreen = $fullscreen; // replace by editorType
			$this->className = $className;
			$this->fontnamesCsv = $fontnamesCsv;
			$this->outsideWE = $outsideWE; // != $isFrontendEdit
			$this->xml = $xml;
			$this->removeFirstParagraph = $removeFirstParagraph;
			$this->inlineedit = $inlineedit; // replace by editorType
			$this->baseHref = $baseHref;
			$this->charset = $charset;
			$this->cssClasses = $cssClasses;
			$this->language = $language;
			$this->showSpell = $spell;
			$this->isFrontendEdit = $isFrontendEdit;
			$this->buttonpos = $buttonpos;
			$this->htmlSpecialchars = $oldHtmlspecialchars;
			$this->contentCss = $contentCss;
			$this->origName = $origName; // obsolete?
			$this->tinyParams = $tinyParams;
			$this->contextmenu = $contextmenu;
			$this->isInPopup = $isInPopup; // replace by editorType
			$this->templates = $templates;
			$this->formats = $formats;
			$this->imageStartID = $imageStartID;
			$this->galleryTemplates = $galleryTemplates;
			$this->fontsizes = $fontsizes;

			$this->preprocessProps();
			$this->setToolbarElements();
			$this->setFilteredElements();
			$this->setToolbarRows();
		}
	}

	private function preprocessProps(){
		if(preg_match('|^.+\[.+\]$|i', $this->name)){
			$this->fieldName_clean = str_replace(['-', '.', '#', ' '], ['_minus_', '_dot_', '_sharp_', '_blank_'], $this->fieldName);
		}

		if(preg_match('%^.+_te?xt\[.+\]$%i', $this->name)){
			$this->fieldName = preg_replace('/^.+_te?xt\[(.+)\]$/', '${1}', $this->name);
		} else if(preg_match('|^.+_input\[.+\]$|i', $this->name)){
			$this->fieldName = preg_replace('/^.+_input\[(.+)\]$/', '${1}', $this->name);
		} else if(preg_match('|^we_ui.+\[.+\]$|i', $this->name)){//we_user_input
			$this->fieldName = preg_replace('/^we_ui.+\[(.+)\]$/', '${1}', $this->name);
		}
		$this->fieldName_clean = str_replace(['-', '.', '#', ' '], ['_minus_', '_dot_', '_sharp_', '_blank_'], $this->fieldName);

		$this->origWidth = $this->width0;
		$this->origHeight = $this->height;

		$this->hiddenValue = $this->value;
		$this->propString = $this->propString ? ',' . $this->propString . ',' : '';

		if(preg_match('/^#[a-f0-9]{6}$/i', $this->bgcolor)){
			$this->bgcolor = substr($this->bgcolor, 1);
		} else if(!preg_match('/^[a-f0-9]{6}$/i', $this->bgcolor) && !preg_match('/^[a-z]*$/i', $this->bgcolor)){
			$this->bgcolor = '';
		}

		$this->fontnamesCSV = $this->fontnamesCSV ? : self::getAttributeOptions('fontnames', false, false, false);
		$fontsArr = explode(',', $this->fontnamesCSV);
		natsort($fontsArr);
		foreach($fontsArr as $font){
			$f = trim($font, ', ');
			$this->fontnames .= (array_key_exists($f, self::$fontstrings)) ? self::$fontstrings[$f] : ucfirst($f) . '=' . $f . ';';
		}

		$this->xml = $this->xml ? "xhtml" : "html";

		$this->charset ? : DEFAULT_CHARSET;
		$charsets = we_base_charsetHandler::inst()->getAll();
		if($this->charset !== DEFAULT_CHARSET && $charsets && is_array($charsets)){
			$found = false;
			$tmp = strtolower($this->charset);
			foreach($charsets as $v){
				if(!empty($v['charset'])){
					if(strtolower($v['charset']) === $tmp){
						$found = true;
						break;
					}
				}
			}
			if(!$found){
				$this->charset = '';
			}
		}

		if($this->cssClasses){
			$cc = explode(',', $this->cssClasses);
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

		$this->statuspos = $this->buttonpos != 'external' ? $this->buttonpos : 'bottom';
		$this->contentCss = ($this->contentCss === '/' ? '' : $this->contentCss);
		$this->restrictContextmenu = $this->contextmenu ? ',' . urldecode($this->contextmenu) . ',' : '';
		$this->createContextmenu = trim($this->contextmenu, " ,'") === 'none' || trim($this->contextmenu, " ,'") === 'false' ? false : true;
		$this->templates = trim($this->templates, ',');

		if($this->formats){
			$tmp = '';
			foreach(explode(',', $this->formats) as $f){
				if(in_array(trim($f, ', '), self::$allFormats)){
					$tmp .= trim($f, ', ') . ',';
				}
			}
			$this->formats = trim($tmp, ',');
		} else {
			$this->formats = self::getAttributeOptions('formats', false, false, false);
		}

		$this->imageStartID = intval($this->imageStartID);

		foreach(explode(',', trim($this->galleryTemplates, ',')) as $id){
			if($id && is_numeric(trim($id))){
				$this->galleryTemplates .= $id . ',';
			}
		}

		$this->fontsizes = $this->fontsizes ? : $this->fontsizesDefault;
		$this->imagePath = IMAGE_DIR . 'wysiwyg/';
		$this->image_languagePath = WE_INCLUDES_DIR . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/wysiwyg/';
		$this->ref = preg_replace('%[^0-9a-zA-Z_]%', '', $this->name);

		//FIXME: what to do with scripts??
	}

	public function getIsFrontendEdit(){
		return $this->isFrontendEdit;
	}

	public static function getEditorCommands($isTag){
		$commands = [
			'font' => ['fontname', 'fontsize'],
			'prop' => ['formatblock', 'applystyle', 'bold', 'italic', 'underline', 'subscript', 'superscript', 'strikethrough', 'styleprops', 'removeformat', 'removetags'],
			'xhtmlxtras' => ['cite', 'acronym', 'abbr', 'lang', 'del', 'ins', 'ltr', 'rtl'],
			'color' => ['forecolor', 'backcolor'],
			'justify' => ['justifyleft', 'justifycenter', 'justifyright', 'justifyfull'],
			'list' => ['insertunorderedlist', 'insertorderedlist', 'indent', 'outdent', 'blockquote'],
			'link' => ['createlink', 'unlink', 'anchor'],
			'table' => ['inserttable', 'deletetable', 'editcell', 'editrow', 'insertcolumnleft', 'insertcolumnright', 'deletecol', 'insertrowabove', 'insertrowbelow', 'deleterow',
				'increasecolspan', 'decreasecolspan'],
			'insert' => ['insertimage', 'insertgallery', 'hr', 'inserthorizontalrule', 'insertspecialchar', 'insertbreak', 'insertdate', 'inserttime'],
			'copypaste' => ['pastetext', 'pasteword'],
			'layer' => ['insertlayer', 'movebackward', 'moveforward', 'absolute'],
			'essential' => ['undo', 'redo', 'spellcheck', 'selectall', 'search', 'replace', 'fullscreen', 'visibleborders'],
			'advanced' => ['editsource', 'template']
		];

		$tmp = array_keys($commands);
		unset($tmp[0]); //unsorted
		if($isTag){
			$ret = [new weTagDataOption(g_l('wysiwyg', '[groups]'), we_html_tools::OPTGROUP)];

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

		$ret = array_merge(['',
			g_l('wysiwyg', '[groups]') => we_html_tools::OPTGROUP
			], $tmp);
		foreach($commands as $key => $values){
			$ret = array_merge($ret, [$key => we_html_tools::OPTGROUP], $values);
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

		if($leadingEmpty){
			array_unshift($options, '---');
		}

		return $asArray ? $options : implode(',', $options);
	}

	static function getHeaderHTML($loadDialogRegistry = false, $frontendEdit = false){
		if(defined('WE_WYSIWG_HEADER')){
			if($loadDialogRegistry && !defined('WE_WYSIWG_HEADER_REG')){
				define('WE_WYSIWG_HEADER_REG', 1);
				return we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_registerDialogs.js');
			}
			return '';
		}

		if($frontendEdit){
			$frontendHeader = '';
			if(!defined('WE_FRONTEND_EDIT_HEADER')){
				define('WE_FRONTEND_EDIT_HEADER', 1);
				$frontendHeader .= we_html_element::jsScript(JS_DIR . 'weFrontendEdit_header.js');
			}
			$frontendHeader .= we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_config.js', '', ['id' => 'loadVarWeTinyMce_config', 'data-consts' => setDynamicVar(self::getFrontendHeaderConsts())]) .
				we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_functionsTop.js');
		}

		define('WE_WYSIWG_HEADER', 1);
		if($loadDialogRegistry){
			define('WE_WYSIWG_HEADER_REG', 1);
		}
		return we_html_element::cssLink(CSS_DIR . 'wysiwyg/tinymce/toolbar.css') .
				we_html_element::jsScript(TINYMCE_SRC_DIR . 'tiny_mce.js') .
				($frontendEdit ? $frontendHeader  : '') .
				($loadDialogRegistry ? we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_registerDialogs.js') : '') .
				we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_functions.js');
	}

	function getAllCmds(){
		$arr = [
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
			'insertgallery',
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
			];

		if(defined('SPELLCHECKER')){
			$arr[] = "spellcheck";
		}
		return $arr;
	}

	private function setToolbarElements(){// TODO: declare setToolbarElements
		$sep = new we_wysiwyg_ToolbarSeparator($this);
		$sepCon = new we_wysiwyg_ToolbarSeparator($this, self::CONDITIONAL);

		//group: font
		$this->elements = array_filter([
			new we_wysiwyg_ToolbarButton($this, "fontname", 92, 20),
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
			new we_wysiwyg_ToolbarButton($this, "insertgallery"),
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
			($this->fullscreen ? // FIXME: disable fullscreen btn in editor when fullscreen
					false :
					new we_wysiwyg_ToolbarButton($this, "fullscreen")
			),
			new we_wysiwyg_ToolbarButton($this, "visibleborders"),
			$sep,
			//group: advanced
			new we_wysiwyg_ToolbarButton($this, "editsource"),
			new we_wysiwyg_ToolbarButton($this, "template"),
			]);
	}

	private function setFilteredElements(){
		$lastSep = true;
		foreach($this->elements as $elem){
			if(is_object($elem) && $elem->showMe){
				if((!$lastSep) || !($elem->isSeparator)){
					$this->filteredElements[] = $elem;
				}
				$lastSep = ($elem->isSeparator);
			}
		}
		if($this->filteredElements){
			if($this->filteredElements[count($this->filteredElements) - 1]->isSeparator){
				array_pop($this->filteredElements);
			}
		}
	}

	function getHTML(){
		return ($this->inlineedit ? $this->getInlineHTML() : $this->getEditButtonHTML());
	}

	private function getEditButtonHTML(){
		$js_function = $this->isFrontendEdit ? 'open_wysiwyg_win' : 'we_cmd';

		$param4 = !$this->isFrontendEdit ? '' : 'frontend';
		$width = we_base_util::convertUnits($this->width);
		$width = is_numeric($width) ? max($width, self::MIN_WIDTH_POPUP) : '(' . intval($width) . '/100*screen.availWidth)';
		$height = we_base_util::convertUnits($this->height);
		$height = is_numeric($height) ? max($height, self::MIN_HEIGHT_POPUP) : '(' . intval($height) . '/100*screen.availHeight)';

		$readyConfig = urlencode(json_encode($this->getDynamicVars()));
		return we_html_button::create_button(we_html_button::EDIT, "javascript:" . $js_function . "('open_wysiwyg_window', '" . $readyConfig . "', " . $width . ", " . $height . ");");

		//return we_html_button::create_button(we_html_button::EDIT, "javascript:" . $js_function . "('open_wysiwyg_window', '" . $this->name . "', " . $width . ", " . $height . ",'" . $param4 . "','" . $this->propString . "','" . $this->className . "','" . rtrim($this->fontnamesCSV, ',') . "','" . $this->outsideWE . "'," . $width . "," . $height . ",'" . $this->xml . "','" . $this->removeFirstParagraph . "','" . $this->bgcolor . "','" . urlencode($this->baseHref) . "','" . $this->charset . "','" . $this->cssClasses . "','" . $this->language . "','" . $this->contentCss . "'+WE().layout.we_tinyMCE.functions.getDocumentCss(window," . ($this->contentCss ? 'true' : 'false') . "),'" . $this->origName . "','" . urlencode($this->tinyParams) . "','" . urlencode($this->restrictContextmenu) . "', 'true', '" . $this->isFrontendEdit . "','" . $this->templates . "','" . $this->formats . "','" . $this->imageStartID . "','" . $this->galleryTemplates . "','" . $this->fontsizes . "');");
	}

	static function parseInternalImageSrc($value){
		static $t = 0;
		$t = ($t ? : time());
		$regs = [];

		// IMPORTANT: we process tiny content both from db and session: the latter uses paths?id=xy instead of document:xy
		if(preg_match_all('/<img [^>]*(src="(' . we_base_link::TYPE_INT_PREFIX . '|[^" >]*\?id=)(\d+)[^"]*")[^>]*>/i', $value, $regs, PREG_SET_ORDER)){
			$ids = [];
			foreach($regs as $reg){
				$ids[] = intval($reg[3]);
			}
			$ids = array_filter($ids);
			if($ids){
				$GLOBALS['DB_WE']->query('SELECT ID,Path FROM ' . FILE_TABLE . ' WHERE ID IN (' . implode(',', $ids) . ')');
				$lookup = $GLOBALS['DB_WE']->getAllFirst(false);
			} else {
				$lookup = [];
			}

			foreach($regs as $reg){
				$path = empty($lookup[intval($reg[3])]) ? '' : $lookup[intval($reg[3])];
				$value = $path ? str_ireplace($reg[1], 'src="' . $path . '?id=' . $reg[3] . '&time=' . $t . '"', $value) :
						str_ireplace($reg[0], '<img src="' . ICON_DIR . 'no_image.gif?id=0">', $value);
			}
		}

		if(preg_match_all('/<img [^>]*(src="(' . we_base_link::TYPE_THUMB_PREFIX . '|[^" >]*\?thumb=)(\d+,\d+)[^"]*")[^>]*>/i', $value, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				list($imgID, $thumbID) = explode(',', $reg[3]);
				$thumbObj = new we_thumbnail();
				$imageExists = $thumbObj->initByImageIDAndThumbID($imgID, $thumbID);
				$value = $imageExists ? str_ireplace($reg[1], 'src="' . $thumbObj->getOutputPath() . "?thumb=" . $reg[3] . '&time=' . $t . '"', $value) :
						str_ireplace($reg[0], '<img src="' . ICON_DIR . 'no_image.gif?id=0">', $value);
				unset($thumbObj);
			}
		}

		return $value;
	}

	/*
	 * this function is used to prepare textaea content for db
	 * it returns an array of img/href-ids (for use in registerMediaLinks)
	 */

	public static function reparseInternalLinks(&$origContent, $replace = false, $name = ''){// FIXME: move to we_document?
		$regs = $internalIDs = [];
		$content = $origContent;

		// replace real links by internals when necessery (in documents links are parsed already, in modules not)
		if(preg_match_all('{src="/[^">]+\\?id=(\\d+)(&amp;|&)?("|[^"]+")}i', $content, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				$content = str_replace($reg[0], 'src="' . we_base_link::TYPE_INT_PREFIX . $reg[1] . '"', $content);
			}
		}
		if(preg_match_all('{src="/[^">]+\\?thumb=(\\d+,\\d+)(&amp;|&)?("|[^"]+")}i', $content, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				$content = str_replace($reg[0], 'src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1] . '"', $content);
			}
		}

		// replace original content if necessary
		$origContent = $replace ? $content : $origContent;

		// FIXME: the obove stuff has to be done before parsing medialinks!
		// FIXME: make we_wysiwyg_editor::registerMedisLinks() just looking for IDs and writing the correct MediaLinks (after making internal link elsewhere)!
		// parse internal IDs in one step to preserve order!
		$content = str_replace(we_base_link::TYPE_THUMB_PREFIX, we_base_link::TYPE_INT_PREFIX, $content);
		if(preg_match_all('/(src|href)="' . we_base_link::TYPE_INT_PREFIX . '(\\d+),?(\\d*)["|?]/i', $content, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				$internalIDs[] = intval($reg[2]);
			}
		}

		$ret = [];
		$c = 0;
		foreach($internalIDs as $id){
			$ret['textarea[name=' . $name . '] [src/href ' . ++$c . ']'] = $id;
		}

		return $ret;
	}

	private function setToolbarRows(){
		$tmpElements = $this->filteredElements;
		$rownr = 0;
		$rows = [$rownr => []
		];

		foreach($tmpElements as $elem){
			if($elem->classname === "we_wysiwyg_ToolbarSeparator"){
				$rownr++;
				$rows[$rownr] = [];
				continue;
			}
			$rows[$rownr][] = $elem;
		}

		$toolbarRows = [];
		$allCommands = [];
		$k = 1;
		foreach($rows as $outer){
			$singleRowName = 'theme_advanced_buttons' . $k;
			$singleRowVal = '';
			foreach($outer as $inner){
				if(!$inner->cmd){
					$singleRowVal .= $inner->conditional ? '' : 'separator,';
				} else if(($cmd = self::wysiwygCmdToTiny($inner->cmd))){
					$singleRowVal .= $cmd . ',';
					$allCommands[] = $cmd;
				}
			}
			$toolbarRows[] = ['name' => $singleRowName, 'value' => rtrim($singleRowVal, ',')];
			$k++;
		}
		$toolbarRows[] = ['name' => 'theme_advanced_buttons' . $k, 'value' => ''];

		$this->toolbarRows = $toolbarRows;
		$this->usedCommands = $allCommands;
	}

	function getContextmenuCommands(){
		if(!$this->filteredElements){
			return '{}';
		}
		$ret = '';
		foreach($this->filteredElements as $elem){
			$ret .= $elem->showMeInContextmenu && self::wysiwygCmdToTiny($elem->cmd) ? '"' . self::wysiwygCmdToTiny($elem->cmd) . '":true,' : '';
		}
		return trim($ret, ',') !== '' ? '{' . trim($ret, ',') . '}' : 'false';
	}

	private static function wysiwygCmdToTiny($cmd){
		$cmdMapping = ['abbr' => 'weabbr',
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
			'insertcolumnleft' => 'col_before',
			'insertcolumnright' => 'col_after',
			'insertgallery' => 'wegallery',
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
			];
		return $cmdMapping[$cmd] != '--' ? $cmdMapping[$cmd] : '';
	}

	function setPlugin($name, $doSet){
		if($doSet){
			$this->tinyPlugins[] = $name;
		}
		return $doSet;
	}

	private function getTemplates(){
		if(!$this->templates){
			return '';
		}

		//FIXME: the ParentID query will only hold for depth 1 folders
		$templates = array_filter(array_map('intval', explode(',', $this->templates)));
		if(!$templates){
			return [];
			//return 'template_templates : [],';
		}
		$templates = implode(',', $templates);
		$GLOBALS['DB_WE']->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE (ID IN (' . $templates . ') OR ParentID IN (' . $templates . ') ) AND Published!=0 AND isFolder=0');
		$tmplArr = $GLOBALS['DB_WE']->getAll(true);

		$templates = [];
		$templates_new = [];
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
					$templates_new[] = ['title' => $tmplDoc->getElement('Title', 'dat', 'no title ' . ($i + 1), true),
							'src' => $tmplDoc->Path . '?pub=' . $tmplDoc->Published,
							'description' => $tmplDoc->getElement('Description', 'dat', 'no description ' . ($i + 1), true)
						];
			}
		}

		return $templates_new;
		//return 'template_templates : [' . implode(',', $templates) . '],';
	}

	private function getDynamicVars(){
		if(in_array($this->editorType, [self::TYPE_INLINE_FALSE, self::TYPE_FULLSCREEN])){
			return $this->readyConfig;
		}

		list($lang) = explode('_', $GLOBALS["weDefaultFrontendLanguage"]);
		$editorLangSuffix = (array_search($GLOBALS['WE_LANGUAGE'], getWELangs())) === 'de' ? 'de_' : '';

		// TODO: move to some function
		$this->tinyPlugins = implode(',', array_unique($this->tinyPlugins));
		$this->wePlugins = implode(',', array_intersect($this->wePlugins, $this->usedCommands));
		$plugins = ($this->createContextmenu ? 'wecontextmenu,' : '') .
				($this->tinyPlugins ? $this->tinyPlugins . ',' : '') .
				($this->wePlugins ? $this->wePlugins . ',' : '') .
				(in_array('wevisualaid', $this->usedCommands) ? 'visualblocks,' : '') .
				(in_array('table', $this->usedCommands) ? 'wetable,' : '') .
				'weutil,wepaste,autolink,template,wewordcount'; //TODO: load "templates" on demand as we do it with other plugins


		return [
			'editorType' => $this->editorType,
			'weCharset' => $this->charset,
			'weOrigWidth' => $this->origWidth,
			'weOrigHeight' =>$this->origHeight,
			'weLanguage' => array_search($GLOBALS['WE_LANGUAGE'], getWELangs()),
			'weIsFullscreen' => $this->fullscreen,
			'weIsInPopup' => true,//$this->isInPopup,
			'weIsFrontendEdit' => $this->isFrontendEdit,
			'weName' => $this->name,
			'name' => $this->name,
			'weFieldName' => $this->fieldName,
			'weFieldNameClean' => $this->fieldName_clean,
			'editorLangSuffix' => $editorLangSuffix,
			'removedInlinePictures' => g_l('wysiwyg', '[removedInlinePictures]'), // // move to consts.g_l
			'fullscreen_readyConfig' => '',
			'weImageStartID' => intval($this->imageStartID),
			'weGalleryTemplates' => $this->galleryTemplates,
			'weCssClasses' => urlencode($this->cssClasses),
			'weRemoveFirstParagraph' => $this->removeFirstParagraph ? 1 : 0,
			'wePopupGl' => [ // move to consts.g_l
					'btnOk' => ['text' => g_l('button', '[ok][value]'), 'alt' => g_l('button', '[ok][alt]')],
					'btnCancel' => ['text' => g_l('button', '[cancel][value]'), 'alt' => g_l('button', '[cancel][alt]')],
					'btnDelete' => ['text' => g_l('button', '[delete][value]'), 'alt' => g_l('button', '[delete][alt]')],
					'btnSearchNext' => ['text' => g_l('buttons_global', '[searchContinue][value]'), 'alt' => g_l('buttons_global', '[searchContinue][value]')],
					'btnReplace' => ['text' => g_l('buttons_global', '[replace][value]'), 'alt' => g_l('buttons_global', '[replace][value]')],
					'btnReplaceAll' => ['text' => g_l('buttons_global', '[replaceAll][value]'), 'alt' => g_l('buttons_global', '[replaceAll][value]')],
				],
			'cssClassesJS' => explode(',', $this->cssClasses),
			'language' => $lang,
			'elements' => $this->name,
			'toolbarRows' => $this->toolbarRows,
			'xml' => $this->xml,
			'className' => $this->className,
 			'origName' => $this->origName,
			'plugins' => $plugins,
			'contextmenuCommands' => $this->getContextmenuCommands(),
			'buttonpos' => $this->buttonpos,
			'fontnames' => $this->fontnames,
			'fontsizes' => $this->fontsizes,
			'tinyCssClasses' => $this->tinyCssClasses,
			'formats' => $this->formats,
			'statuspos' => $this->statuspos,
			'editorCss' => we_html_element::getUnCache(CSS_DIR . 'wysiwyg/tinymce/editorCss.css'),
			'fontawsomeCss' => we_html_element::getUnCache(LIB_DIR . 'additional/fontawesome/css/font-awesome.min.css'),
			'contentCssFirst' => we_html_element::getUnCache(CSS_DIR . 'wysiwyg/tinymce/contentCssFirst.php') . '&tinyMceBackgroundColor=' . $this->bgcolor,
			'contentCssLast' => ($this->contentCss ? $this->contentCss : ''),
			'popupCssAdd' => we_html_element::getUnCache(WEBEDITION_DIR . 'lib/additional/fontLiberation/stylesheet.css') . ',' . we_html_element::getUnCache(WEBEDITION_DIR . 'lib/additional/fontawesome/css/font-awesome.min.css') . ',' . we_html_element::getUnCache(CSS_DIR . 'wysiwyg/tinymce/tinyDialogCss.css'),
			'templates' => (in_array('template', $this->usedCommands) && $this->templates ? $this->getTemplates() : '')
		];
	}

	private static function getFrontendHeaderConsts(){
		return ['g_l' => ['tinyMceTranslationObject' => self::getTinyMceTranslationObject()],
				'tables' => ['FILE_TABLE' => FILE_TABLE, 'OBJECT_FILES_TABLE' => OBJECT_FILES_TABLE],
				'dirs' => ['WE_JS_TINYMCE_DIR' => WE_JS_TINYMCE_DIR],
				'linkPrefix' => ['TYPE_INT_PREFIX' => we_base_link::TYPE_INT_PREFIX,
					'TYPE_OBJ_PREFIX' => we_base_link::TYPE_OBJ_PREFIX]
			];
	}

	public static function getTinyMceTranslationObject(){
		return [array_search($GLOBALS['WE_LANGUAGE'], getWELangs()) => ['we' => [
					'group_link' => g_l('wysiwyg', '[links]'),
					'group_copypaste' => g_l('wysiwyg', '[import_text]'),
					'group_advanced' => g_l('wysiwyg', '[advanced]'),
					'group_insert' => g_l('wysiwyg', '[insert]'),
					'group_indent' => g_l('wysiwyg', '[indent]'),
					'group_view' => g_l('wysiwyg', '[view]'),
					'group_table' => g_l('wysiwyg', '[table]'),
					'group_edit' => g_l('wysiwyg', '[edit]'),
					'group_layer' => g_l('wysiwyg', '[layer]'),
					'group_xhtml' => g_l('wysiwyg', '[xhtml_extras]'),
					'tt_weinsertbreak' => g_l('wysiwyg', '[insert_br]'),
					'tt_welink' => g_l('wysiwyg', '[hyperlink]'),
					'tt_weimage' => g_l('wysiwyg', '[insert_edit_image]'),
					'tt_wefullscreen_set' => g_l('wysiwyg', '[maxsize_set]'),
					'tt_wefullscreen_reset' => g_l('wysiwyg', '[maxsize_reset]'),
					'tt_welang' => g_l('wysiwyg', '[language]'),
					'tt_wespellchecker' => g_l('wysiwyg', '[spellcheck]'),
					'tt_wevisualaid' => g_l('wysiwyg', '[visualaid]'),
					'tt_wegallery' => g_l('wysiwyg', '[addGallery]'),
					'cm_inserttable' => g_l('wysiwyg', '[insert_table]'),
					'cm_table_props' => g_l('wysiwyg', '[edit_table]')
				]
			]
		];
	}

	private function getInlineHTML(){
		$editValue = self::parseInternalImageSrc($this->value);
		$height = we_base_util::convertUnits($this->height);
		$width = we_base_util::convertUnits($this->width);
		if(is_numeric($height) && is_numeric($width) && $width){
			//only a simple fix
			$this->height = $height = $height - ($this->buttonpos === 'external' ? 0 : round((($k) / ($width / (5 * 22))) * 26));
		}
		$width = (is_numeric($width) ? round(max($width, self::MIN_WIDTH_INLINE) / 96, 3) . 'in' : $width);
		$height = (is_numeric($height) ? round(max($height, self::MIN_HEIGHT_INLINE) / 96, 3) . 'in' : $height);

		$config = $this->getDynamicVars();
		$config['fullscreen_readyConfig'] = $this->editorType === self::TYPE_INLINE_TRUE ? urlencode(json_encode($config)) : '';

		return we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_init.js', '', ['id' => 'loadVarWeTinyMce_init', 'data-dynvars' => setDynamicVar($config)]) .
				getHtmlTag('textarea', ['wrap' => "off",
				'style' => 'color:#eeeeee; background-color:#eeeeee;  width:' . $width . '; height:' . $height . ';',
					'id' => $this->name,
					'name' => $this->name,
					'class' => 'wetextarea'
					], strtr($editValue, ['\n' => '', '&' => '&amp;']), true);
	}

}
