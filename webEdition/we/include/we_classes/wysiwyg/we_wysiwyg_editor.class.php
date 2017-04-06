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
	private $dialogProperties = [];
	private $name = '';
	private $origName = '';
	private $fieldName = '';
	private $fieldName_clean = '';
	private $width = 600;
	private $height = 400;
	private $ref = '';
	private $propString = '';
	private $elements = [];
	private $value = '';
	private $contextmenu = '';
	private $restrictContextmenu = '';
	private $tinyPlugins = [];
	private $wePlugins = ['wetable', 'weadaptunlink', 'weadaptbold', 'weadaptitalic', 'weimage', 'advhr', 'weabbr', 'weacronym', 'welang', 'wevisualaid', 'weinsertbreak',
		'wespellchecker', 'welink', 'wefullscreen', 'wegallery'];
	private $externalPlugins = ['weabbr', 'weacronym', 'weadaptunlink', /*'wecontextmenu',*/ 'wefullscreen', 'wegallery', 'weimage',
		'weinsertbreak', 'welang', 'welink', 'wetable', 'weutil', 'wevisualaid',/*'wewordcount',*/ 'xhtmlxtras']; //TINY4
	private $internalPlugins = ['colorpicker', 'compat3x', 'lists', 'paste', 'wordcount', 'importcss', 'advlist', 'textcolor', 'anchor', 'charmap', 'code', 
		'hr', 'insertdatetime', 'nonbreaking', 'searchreplace', 'template', /*'bbcode', */'codesample', 'contextmenu', 'fullscreen'/*, 'visualchars', 'imagetools'*/];
	private $createContextmenu = true;
	private $filteredElements = [];
	private $filteredMenuElements = []; //TINY4
	private $bgcolor = '';
	private $buttonpos = '';
	private $tinyParams = '';
	private $templates = '';
	private $className = '';
	private $xml = false;
	private $removeFirstParagraph = true;
	private $charset = '';
	private $inlineedit = true;
	private $cssClasses = '';
	private $tinyCssClasses = '';
	private $styleFormats = []; //TINY4
	private $blockImportCss = false; //TINY4
	private $language = '';
	private $editorLanguage = '';
	private $imagePath;
	private $image_languagePath;
	private $baseHref = '';
	private $showSpell = true;
	private $isFrontendEdit = false;
	private $htmlSpecialchars = true; // in wysiwyg default was "true" (although Tag-Hilfe says "false")
	private $contentCss = '';
	private $imageStartID = 0;
	private $galleryTemplates = '';
	private $formats = '';
	private $fontnames = '';
	private $fontnamesCSV = '';
	private $toolbarRows = [];
	private $usedCommands = [];
	private $selectorClass = ''; //TINY4
	private $fontsizes = '';
	private $fontsizesDefault = '8pt,10pt,12pt,14pt,18pt,24pt,36pt';
	private $menu; //TINY4
	private $showMenu = false; //TINY4
	private $menuCommands = ''; //TINY4
	private $usedCommandsMenu = [];
	private $submenuAlignments = []; //TINY4
	private $tableMenuCommands = []; //TINY4
	private $tableCommands = []; //TINY4
	private $formatselects = [];

	private static $allFormats = ['p', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'code', 'blockquote', 'samp'];
	private static $allFormatsTiny4 = [ //TINY4
			['title' => 'Blocks', 'items' =>[
				'p' => ['title' => 'Paragraph', 'block' => 'p'],
				'div' => ['title' => 'Div', 'block' => 'div'],
				'pre' => ['title' => 'Pre', 'block' => 'pre'],
				'code' => ['title' => 'Code ', 'block' => 'code'],
				'blockquote' => ['title' => 'Blockquote', 'block' => 'blockquote'],
				'samp' => ['title' => 'Samp', 'block' => 'samp']
			]],['title' => 'Headers', 'items' => [
				'h1' => ['title' => 'Header 1', 'block' => 'h1'],
				'h2' => ['title' => 'Header 2', 'block' => 'h2'],
				'h3' => ['title' => 'Header 3', 'block' => 'h3'],
				'h4' => ['title' => 'Header 4', 'block' => 'h4'],
				'h5' => ['title' => 'Header 5', 'block' => 'h5'],
				'h6' => ['title' => 'Header 6', 'block' => 'h6'],
			]]
		];


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

	private static $dataConfigurations = [];
	private static $dataDialogProperties = ['isDialog' => false];
	private static $countInstances = 0;

	const CONDITIONAL = true;
	const MIN_WIDTH_INLINE = 100;
	const MIN_HEIGHT_INLINE = 100;
	const MIN_WIDTH_POPUP = 100;
	const MIN_HEIGHT_POPUP = 100;

	const TYPE_INLINE_TRUE = 'inlineTrue';
	const TYPE_INLINE_FALSE = 'inlineFalse';
	const TYPE_FULLSCREEN = 'fullscreen';
	const TYPE_EDITBUTTON = 'editButton';

	function __construct(array $dialogProperties = [], $editorType = '', $name = '', $width = 600, $height = 400, $value = '', $propString = '', $bgcolor = '', $className = '', $fontnamesCsv = '', $xml = false, $removeFirstParagraph = true, $inlineedit = true, $baseHref = '', $charset = '', $cssClasses = '', $language = '', $spell = true, $isFrontendEdit = false, $buttonpos = 'top', $oldHtmlspecialchars = true, $contentCss = '', $origName = '', $tinyParams = '', $contextmenu = '', $templates = '', $formats = '', $imageStartID = 0, $galleryTemplates = '', $fontsizes = '', $menuCommands = ''){
		$this->editorType = $editorType ? : self::TYPE_INLINE_TRUE;
		$this->dialogProperties = $dialogProperties;

		if(in_array($this->editorType, [self::TYPE_INLINE_FALSE, self::TYPE_FULLSCREEN])){
			if(empty($this->dialogProperties)){
				t_e('attempt to initialize wysiwyg editor type "' . $this->editorType . '" without dialogProperties');
				//exit();
			}

			// we only init props that we need in function getInlineHTML
			$this->width = '100%';
			$this->height = '100%';
			$this->name = $this->dialogProperties['weName'];
			$this->buttonpos = $this->dialogProperties['theme_advanced_toolbar_location'];
			$this->charset = $this->dialogProperties['weCharset'];
			$this->selectorClass = $this->dialogProperties['weSelectorClass'];

			self::$dataDialogProperties = ['isDialog' => true, 'weEditorType' => $this->editorType, 'weFieldname' => $this->dialogProperties['weFieldName']];
		} else {
			$this->name = $name;
			$this->width = $width;
			$this->height = $height;
			$this->value = $value;
			$this->propString = $propString;
			$this->menuCommands = $menuCommands; //TINY4
			$this->bgcolor = $bgcolor;
			$this->className = $className;
			$this->fontnamesCSV = $fontnamesCsv;
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
			$this->templates = $templates;
			$this->formats = $formats;
			$this->imageStartID = $imageStartID;
			$this->galleryTemplates = $galleryTemplates;
			$this->fontsizes = $fontsizes;

			$this->preprocessProps();
			$this->initializeCommands();
			$this->applyFiltersToCommands();
			$this->processToolbar();

			if(IS_TINYMCE_4){
				$this->processMenu();
				$this->processTableToolbar();
				$this->processFormatsMenu();
				$this->processFormatSelects();
			}
		}
	}

	private function preprocessProps(){
		if(IS_TINYMCE_4){ //TINY4
			$this->menuCommands = array_map('trim', explode(',', $this->menuCommands));

			$this->externalPlugins = [
				'welink' => WE_JS_TINYMCE_DIR . 'plugins/welink/editor_plugin.js',
				'weimage' => WE_JS_TINYMCE_DIR . 'plugins/weimage/editor_plugin.js',
				'weabbr' => WE_JS_TINYMCE_DIR . 'plugins/weabbr/editor_plugin.js',
				'weacronym' => WE_JS_TINYMCE_DIR . 'plugins/weacronym/editor_plugin.js',
				'wegallery' => WE_JS_TINYMCE_DIR . 'plugins/wegallery/editor_plugin.js',
				//'wecontextmenu' => WE_JS_TINYMCE_DIR . 'plugins/wecontextmenu/editor_plugin.js',
				'weutil' => WE_JS_TINYMCE_DIR . 'weutil/editor_plugin.js',
				'weadaptunlink' => WE_JS_TINYMCE_DIR . 'plugins/weadaptunlink/editor_plugin.js',
				'weinsertbreak' => WE_JS_TINYMCE_DIR . 'editor_plugin.js',
				'wefullscreen' => WE_JS_TINYMCE_DIR . 'wefullscreen/editor_plugin.js',
				'wevisualaid' => WE_JS_TINYMCE_DIR . 'plugins/wevisualaid/editor_plugin.js',
				'welang' => WE_JS_TINYMCE_DIR . 'welang/editor_plugin.js',
				'table' =>  WE_JS_TINYMCE_DIR . 'plugins/wetable/plugin.js',
				xhtmlxtras  => WE_JS_TINYMCE_DIR . 'plugins/xhtmlxtras/editor_plugin.js'
			];
		}

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
		$this->hiddenValue = $this->value;
		$this->propString = $this->propString ? ',' . $this->propString . ',' : '';
		if(IS_TINYMCE_4){ //TINY4
			$this->selectorClass = str_replace(['[', ']'], ['_', ''], $this->name);
		}

		if(preg_match('/^#[a-f0-9]{6}$/i', $this->bgcolor)){
			$this->bgcolor = substr($this->bgcolor, 1);
		} else if(!preg_match('/^[a-f0-9]{6}$/i', $this->bgcolor) && !preg_match('/^[a-z]*$/i', $this->bgcolor)){
			$this->bgcolor = '';
		}

		if(!IS_TINYMCE_4){
			$this->fontnamesCSV = $this->fontnamesCSV ? : self::getAttributeOptions('fontnames', false, false, false);
			$fontsArr = explode(',', $this->fontnamesCSV);
			natsort($fontsArr);
			foreach($fontsArr as $font){
				$f = trim($font, ', ');
				$this->fontnames .= (array_key_exists($f, self::$fontstrings)) ? self::$fontstrings[$f] : ucfirst($f) . '=' . $f . ';';
			}
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

		if(IS_TINYMCE_4){ //TINY4	

		} else {
			if($this->cssClasses){
				$cc = explode(',', $this->cssClasses);
				$tf = '';
				$csvCl = '';
				foreach($cc as $val){
					$val = trim($val);
					$tf .= $val . '=' . $val . ';';
					$csvCl .= $val . ',';
				}
				$this->cssClasses = rtrim($csvCl, ',');
				$this->tinyCssClasses = rtrim($tf, ';');
			}
		}

		$this->statuspos = $this->buttonpos != 'external' ? $this->buttonpos : 'bottom';
		$this->contentCss = ($this->contentCss === '/' ? '' : $this->contentCss);
		$this->tinyParams = array_map(function($el){list($k, $v) = explode(':', $el, 2); return ['name' => $k, 'value' => trim($v, "'")];}, explode("',", trim(str_replace(' ', '', $this->tinyParams), ' ,')));
		$this->restrictContextmenu = $this->contextmenu ? ',' . urldecode($this->contextmenu) . ',' : '';
		$this->createContextmenu = trim($this->contextmenu, " ,'") === 'none' || trim($this->contextmenu, " ,'") === 'false' ? false : true;
		$this->templates = trim($this->templates, ',');

		if(IS_TINYMCE_4){ //TINY4

		} else {
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
		}

		$this->imageStartID = intval($this->imageStartID);

		foreach(explode(',', trim($this->galleryTemplates, ',')) as $id){
			if($id && is_numeric(trim($id))){
				$this->galleryTemplates .= $id . ',';
			}
		}

		if(!IS_TINYMCE_4){
			$this->fontsizes = $this->fontsizes ? : $this->fontsizesDefault;
		}

		$this->imagePath = IMAGE_DIR . 'wysiwyg/';
		$this->image_languagePath = WE_INCLUDES_DIR . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/wysiwyg/';
		$this->ref = preg_replace('%[^0-9a-zA-Z_]%', '', $this->name);

		// editorLanguage: in backend we allways use backend language. in frontend we take doc language if defined, if not we use weDefaultFrontendLanguage
		list($docLang) = explode('_', $this->language);
		list($defaultFrontendLang) = explode('_', $GLOBALS["weDefaultFrontendLanguage"]);
		$this->editorLanguage = $this->isFrontendEdit ? ($docLang ? : $defaultFrontendLang) : (($lang = (array_search($GLOBALS['WE_LANGUAGE'], getWELangs()))) ? $lang : 'de');

		//FIXME: what to do with scripts??
	}

	public function getIsFrontend(){
		return $this->isFrontendEdit;
	}

	public function getPropString(){
		return $this->propString;
	}

	public function getMenuCommands(){ //TINY4
		return $this->menuCommands;
	}

	public function getRestrictContextmenu(){
		return $this->restrictContextmenu;
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
			'essential' => array_filter(['undo', 'redo', 'spellcheck', 'selectall', 'search', 'replace', 'fullscreen', (IS_TINYMCE_4 ? 'maximize' : false), 'visibleborders']),
			'advanced' => array_filter(['editsource', 'template', (IS_TINYMCE_4 ? 'codesample' : false)])
		];

		if(IS_TINYMCE_4){ //TINY4
			array_push($commands['table'], 'edittable', 'copypasterow');
		}

		$tmp = array_keys($commands);
		unset($tmp[0]); //unsorted
		if($isTag){
			$ret = [new we_tagData_option(g_l('wysiwyg', '[groups]'), we_html_tools::OPTGROUP)];

			foreach($tmp as $command){
				$ret[] = new we_tagData_option($command);
			}
			foreach($commands as $key => $values){
				$ret[] = new we_tagData_option($key, we_html_tools::OPTGROUP);
				foreach($values as $value){
					$ret[] = new we_tagData_option($value);
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
				$opt = new we_tagData_option($opt);
			}
			return $options;
		}

		if($leadingEmpty){
			array_unshift($options, '---');
		}

		return $asArray ? $options : implode(',', $options);
	}

	static function getHTMLHeader($frontendEdit = false, $loadConfigs = false){
		if(defined('WE_WYSIWG_HEADER')){
			return '';
		}
		define('WE_WYSIWG_HEADER', 1);

		if($frontendEdit){
			$frontendHeader = '';
			if(!defined('WE_FRONTEND_EDIT_HEADER')){
				define('WE_FRONTEND_EDIT_HEADER', 1);
				$frontendHeader .= we_html_element::jsScript(JS_DIR . 'weFrontendEdit_header.js');
			}
			$frontendHeader .= we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_config.js', '', ['id' => 'loadVarWeTinyMce_config', 'data-consts' => setDynamicVar(self::getFrontendHeaderConsts())]) .
				we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_functionsTop.js');
		}

		return we_html_element::cssLink(CSS_DIR . 'wysiwyg/tinymce/toolbar.css') .
				we_html_element::jsScript(TINYMCE_SRC_DIR . 'tiny_mce.js') .
				(IS_TINYMCE_4 ? we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_tiny_mce_overwrites.js') . we_html_element::jsScript(TINYMCE_SRC_DIR . 'plugins/compat3x/plugin.js') : '') . //TINY4
				($frontendEdit ? $frontendHeader  : '') .
				we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'weTinyMce_init.js', '', ($loadConfigs ? ['id' => 'loadVar_tinyConfigs',
						'data-dialogProperties' => setDynamicVar(self::$dataDialogProperties),
						'data-configurations' => setDynamicVar(self::$dataConfigurations),
					] : []));
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

		if(IS_TINYMCE_4){ //TINY4
			array_push($arr, 'edittable', 'copypasterow', 'maximize', 'codesample');
		}

		return $arr;
	}

	private function initializeCommands(){// TODO: declare setToolbarElements
		$sep = new we_wysiwyg_ToolbarSeparator($this);
		$sepCon = new we_wysiwyg_ToolbarSeparator($this, self::CONDITIONAL);

		$this->elements = array_filter([
			//group: font
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
			(IS_TINYMCE_4 ? new we_wysiwyg_ToolbarButton($this, "copypasterow") : false), //TINY4
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
			(IS_TINYMCE_4 ? new we_wysiwyg_ToolbarButton($this, "table_placeholder") : false), //TINY4
			(IS_TINYMCE_4 ? $sep : false), //TINY4
			(IS_TINYMCE_4 ? new we_wysiwyg_ToolbarButton($this, "tablemenucell") : false), //TINY4
			(IS_TINYMCE_4 ? new we_wysiwyg_ToolbarButton($this, "tablemenucolumn") : false), //TINY4
			(IS_TINYMCE_4 ? new we_wysiwyg_ToolbarButton($this, "tablemenurow") : false), //TINY4
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
			(IS_TINYMCE_4 ? false : new we_wysiwyg_ToolbarButton($this, "insertlayer")),
			(IS_TINYMCE_4 ? false : new we_wysiwyg_ToolbarButton($this, "movebackward")),
			(IS_TINYMCE_4 ? false : new we_wysiwyg_ToolbarButton($this, "moveforward")),
			(IS_TINYMCE_4 ? false : new we_wysiwyg_ToolbarButton($this, "absolute")),
			(IS_TINYMCE_4 ? false : $sep),
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
			(IS_TINYMCE_4 ? false : new we_wysiwyg_ToolbarButton($this, "replace")),
			$sepCon,
			new we_wysiwyg_ToolbarButton($this, "fullscreen"),
			(IS_TINYMCE_4 ? new we_wysiwyg_ToolbarButton($this, "maximize") : false), //TINY4
			new we_wysiwyg_ToolbarButton($this, "visibleborders"),
			$sep,
			//group: advanced
			new we_wysiwyg_ToolbarButton($this, "editsource"),
			new we_wysiwyg_ToolbarButton($this, "template"),
			(IS_TINYMCE_4 ? new we_wysiwyg_ToolbarButton($this, "codesample") : false), //TINY4
			]);
	}

	private function applyFiltersToCommands(){
		$lastSep = true;
		foreach($this->elements as $elem){
			if(IS_TINYMCE_4){ //TINY4
				if($elem->cmd === 'inserttable' && !empty($this->tableCommands)){
					$elem->showInToolbar = true; // FIXME: check this!
					$elem->showInMenu = true;
				}
			}

			if(is_object($elem) && $elem->isShowInToolbar()){
				if((!$lastSep) || !($elem->isSeparator())){
					$this->filteredElements[] = $elem;
				}
				$lastSep = ($elem->isSeparator());
			}

			if(IS_TINYMCE_4 && is_object($elem) && $elem->isShowInMenu() && !$elem->isSeparator()){  //TINY4
				$this->filteredMenuElements[] = $elem;
			}
		}
		if($this->filteredElements){
			if($this->filteredElements[count($this->filteredElements) - 1]->isSeparator()){
				array_pop($this->filteredElements);
			}
		}
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
	
	public function addTableCommand($cmd){
		$this->tableCommands[$this->wysiwygCmdToTiny($cmd)] = true;
	}

	public function isTableCommands(){
		return !empty($this->tableCommands);
	}
	
	public function isTableMenuCommand($cmd){
		return isset($this->tableMenuCommands[$cmd]);
	}

	public function addTableMenuCommand($cmd){
		return isset($this->tableMenuCommands[$cmd]);
	}

	private function processToolbar(){
		$tmpElements = $this->filteredElements;
		$rownr = 0;
		$rows = [$rownr => []
		];

		if(IS_TINYMCE_4){  //TINY4
			$toolbarStr = '';
			$allCmds = [];
			foreach($tmpElements as $elem){
				if(!$elem->cmd){
					$toolbarStr .= '| ';
				} else {
					if(($cmd = self::wysiwygCmdToTiny($elem->cmd))){
						$toolbarStr .= $cmd . ' ';
						$allCmds[] = $cmd;
					}
				}
			}
			$this->toolbarRows = str_replace('insertdatetime insertdatetime', 'insertdatetime', trim($toolbarStr));
			$this->usedCommands = $allCmds;

			return;
		}

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

	private function processFormatsMenu(){
		$this->styleFormats = array_merge((in_array('formatselect', $this->usedCommandsMenu) ? $this->formats : []), 
				(in_array('styleselect', $this->usedCommandsMenu) ? $this->styleFormats : []));
		//$this->blockImportCss = !in_array('styleselect', $this->usedCommandsMenu) ? true : $this->blockImportCss;
	}

	private function processFormatSelects(){
		$formatselects = [
			'menus' => [],
			'menuSettings' => [
				'fontselect' => [],
				'fontsizeselect' => [],
				'headers' => [],
				'blocks' => [],
				'styles' => []
			],
			'toolbar' => [],
			'toolbarSettings' => [
				'fonts' => [],
				'fontsizes' => [],
				'headers' => [],
				'blocks' => [],
				'styles' => []
			],
		];

		$this->fontnamesCSV = $this->fontnamesCSV ? : self::getAttributeOptions('fontnames', false, false, false);
		$fontsArr = explode(',', $this->fontnamesCSV);
		natsort($fontsArr);
		if(in_array('fontselect', $this->usedCommands)){
			$formatselects['toolbar'][] = 'fontselect';
			$fontnames = '';
			foreach($fontsArr as $font){
				$f = trim($font, ', ');
				$fontnames .= (array_key_exists($f, self::$fontstrings)) ? self::$fontstrings[$f] : ucfirst($f) . '=' . $f . ';';
			}
			$formatselects['toolbarSettings']['fonts'] = str_replace("'", '', $fontnames);
		}
		if(in_array('fontselect', $this->usedCommandsMenu)){
			$formatselects['menus'][] = 'fontselect';
			$items = [];
			foreach($fontsArr as $font){
				$font = trim($font, ', ');
				$f = (array_key_exists($font, self::$fontstrings)) ? self::$fontstrings[$font] : ucfirst($font) . '=' . $font;
				$parts = explode('=', trim($f, ','));
				$items[] = ['title' => $parts[0], 'inline' => 'span', 'styles' => ['font-family' => str_replace("'", '', trim($parts[1], ' ;'))]];
			}
			$formatselects['menuSettings']['fontselect'] = [['title' => g_l('prefs', '[editor_fontname]'), 'items' => $items]];
		}

		$this->fontsizes = $this->fontsizes ? : $this->fontsizesDefault;
		if(in_array('fontsizeselect', $this->usedCommands)){
			$fontSizes = str_replace(',', ' ', $this->fontsizes);
			$formatselects['toolbar'][] = 'fontsizes';
			$formatselects['toolbarSettings']['fontsizes'] = str_replace(',', ' ', $fontSizes);
		}
		if(in_array('fontsizeselect', $this->usedCommandsMenu)){
			$formatselects['menus'][] = 'fontsizeselect';
			$items = [];
			foreach(explode(',', $this->fontsizes) as $size){
				$size = trim($size);
				$items[] = ['title' => $size, 'inline' => 'span', 'styles' => ['font-size' => $size]];
			}
			$formatselects['menuSettings']['fontsizeselect'] = [['title' => g_l('wysiwyg', '[fontsize]'), 'items' => $items]];
		}

		//$this->fontsizes = $this->fontsizes ? : $this->fontsizesDefault;
		if(in_array('formatselect', $this->usedCommands)){
			/*
			$fontSizes = str_replace(',', ' ', $this->fontsizes);
			$formatselects['toolbar'][] = 'fontsizes';
			$formatselects['toolbarSettings']['fontsizes'] = str_replace(',', ' ', $fontSizes);
			 * 
			 */
		}
		if(in_array('formatselect', $this->usedCommandsMenu)){
			if($this->formats){
				$tmp = [];
				foreach(explode(',', $this->formats) as $f){
					foreach(self::$allFormatsTiny4 as $sub){
						if(isset($sub['items'][trim($f, ', ')])){
							$tmp[$sub['title']] = $tmp[$sub['title']] ? $tmp[$sub['title']] : [];
							$tmp[$sub['title']][] = $sub['items'][trim($f, ', ')];
							break;
						}
					}
				}
				$formats = [];
				foreach($tmp as $k => $v){
					$formatselects['menus'][] = strtolower($k);
				$formatselects['menuSettings'][strtolower($k)] = [['title' => $k, 'items' => $v]];
				}
			} else {
				$formats = [];
				foreach(self::$allFormatsTiny4 as $sub){
					$formats = ['title' => $sub['title'], 'items' => array_values($sub['items'])];
				}
			}
			$formatselects['menuSettings']['fontsizeselect'] = [['title' => g_l('wysiwyg', '[fontsize]'), 'items' => $items]];
		}

		if(in_array('styleselect', $this->usedCommands)){
			//
		}
		if(in_array('styleselect', $this->usedCommandsMenu)){
			$formatselects['menus'][] = 'styles';
			$items = [/*['title' => '', 'classes' => ' ']*/];
			if($this->cssClasses){
				if(strpos($this->cssClasses, '[') === 0){
					$items = json_decode($this->cssClasses);
				} else {
					$classes = explode(',', $this->cssClasses);
					foreach($classes as $class){
						$class = trim($class);
						if(strpos($class, '{') === 0){
							$items[] = (array) json_decode($class);
						} else {
								$items[] = ['title' => $class, 'inline' => 'span', 'classes' => $class];
						}
					}
				}
				$this->blockImportCss = !empty($items);
			}
			$formatselects['menuSettings']['styles'] = ['title' => 'CSS-Styles und Klassen', 'items' => $items];
		}

		$this->formatselects = $formatselects;
	}

	function getContextmenuCommands(){ // FIXME: return PHP, no handmade json!
		if(!$this->filteredElements){
			return '{}';
		}
		$ret = '';
		foreach($this->filteredElements as $elem){
			$ret .= $elem->isShowInContextmenu() && self::wysiwygCmdToTiny($elem->cmd) ? '"' . self::wysiwygCmdToTiny($elem->cmd) . '":true,' : '';
		}
		return trim($ret, ',') !== '' ? '{' . trim($ret, ',') . '}' : 'false';
	}

	function processTableToolbar(){ //TINY4
		if(!IS_TINYMCE_4){
			return;
		}
	
		$fullToolbar = ['tableprops', 'tabledelete', '|', 'tableinsertrowbefore', 'tableinsertrowafter', 'tabledeleterow', '|', 'tableinsertcolbefore', 'tableinsertcolafter', 'tabledeletecol'];
		$toolbar = '';
		$isLastSep = false;

		foreach($fullToolbar as $cmd){
				$toolbar .= isset($this->tableCommands[$cmd]) || ($cmd === '|' && !$isLastSep) ? $cmd . ' ' : '';
				$isLastSep = $cmd === '|';
				unset($this->tableCommands[$cmd]);
		}

		foreach($this->tableCommands as $cmd => $v){ // add commands that are not in fullToolbar
			$toolbar .= $cmd .' ';
		}

		$this->tableCommands = trim($toolbar);
	}

	function processMenu(){  //TINY4
		if(!$this->filteredMenuElements || !IS_TINYMCE_4){
			return [];
		}

		$fullMenu = [
			'edit' => ['title' => 'Edit', 'items' => ['undo', 'redo', '|', 'pastetext', '|', 'selectall', '|', 'searchreplace']],
			'view' => ['title' => 'View', 'items' => ['wevisualaid', 'visualchars', 'wefullscreen', 'fullscreen']],
			'format' => ['title' => 'Format', 'items' => ['fontselect', 'fontsizeselect', 'headers', 'blocks', 'formats', '|', 'bold', 'italic', 'underline', 'subscript', 'superscript', 'strikethrough', '|', 'alignment', '|', 'removeformat']],
			'insert' => ['title' => 'Insert', 'items' => ['welink', 'weadaptunlink', 'anchor', '|', 'weimage', 'wegallery', '|', 'numlist', 'bullist', '|', 'charmap', 'hr', 'weinsertbreak', 'nonbreaking', '|', 'insertdatetime', '|', 'template', 'codesample']],
			'table' => ['title' => 'Table', 'items' => ['inserttable', 'deletetable', 'tableprops', 'cell', 'row', 'column']],
			'xhtml' => ['title' => 'XHTML', 'items' => ['cite', 'weacronym', 'weabbr', 'welang', 'del', 'ins', 'ltr', 'rtl']],
			'tools' => ['title' => 'Tools', 'items' => ['code']]
		];

		$elems = [];
		$usedCommandsMenu = [];

		foreach($this->filteredMenuElements as $elem){
			if($elem->isShowInMenu() && ($mapped = self::wysiwygCmdToTiny($elem->cmd))){
				$elems[$mapped] = $mapped;
			}
		}

		if($elems['formatselect'] || $elems['styleselect']){
			$elems['headers'] = 'headers';
			$elems['blocks'] = 'blocks';
			$elems['formats'] = 'formats';
			$usedCommandsMenu = array_filter([($elems['formatselect'] ? : false), ($elems['styleselect'] ? : false)]);
		}

		//$elems['visualchars'] = 'visualchars';

		if(!empty($alignments = array_intersect($elems, ['alignleft', 'alignright', 'aligncenter', 'alignjustify']))){
			$elems['alignment'] = 'alignment';
			$this->submenuAlignments = $alignments;
		}

		$processedMenu = [];

		foreach ($fullMenu as $name => $menu) {
			$items = '';
			$isLastSep = false;
			$set = false;
			foreach($menu['items'] as $item){
				$set = $set || in_array($item, $elems);

				if(in_array($item, $elems)){
					$items .= $item . ' ';
					$usedCommandsMenu[] = $item;
					unset($elems[$item]);
				} else if($item === '|' && !$isLastSep){
					$items .= '| ';
					$isLastSep = true;
				}
			}
			if($set){
				$processedMenu[$name] = ['title' => $menu['title'], 'items' => trim($items, ' |')];
			}
		}

		$this->usedCommandsMenu = $usedCommandsMenu;
		$this->menu = $processedMenu;
		$this->showMenu = !empty($this->menu);
	}

	private static function wysiwygCmdToTiny($cmd){
		if(!IS_TINYMCE_4){ //TINY4
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
		} else { //TINY4
			$cmdMapping = ['abbr' => 'weabbr',
				'acronym' => 'weacronym',
				'anchor' => 'anchor',
				'applystyle' => 'styleselect',
				'backcolor' => 'backcolor',
				'bold' => 'bold',
				//'copy' => 'copy',
				'createlink' => 'welink',
				//'cut' => 'cut',
				'decreasecolspan' => 'tablesplitcells ',
				'deletecol' => 'tabledeletecol',
				'deleterow' => 'tabledeleterow',
				'editcell' => 'tablecellprops',
				'editsource' => 'code',
				'fontname' => 'fontselect',
				'fontsize' => 'fontsizeselect',
				'forecolor' => 'forecolor',
				'formatblock' => 'formatselect',
				'fullscreen' => 'wefullscreen',
				'maximize' => 'fullscreen',
				'increasecolspan' => 'tablemergecells',
				'indent' => 'indent',
				'insertbreak' => 'weinsertbreak',
				'insertcolumnleft' => 'tableinsertcolbefore',
				'insertcolumnright' => 'tableinsertcolafter',
				'insertgallery' => 'wegallery',
				'inserthorizontalrule' => 'hr',
				'insertimage' => 'weimage',
				'insertorderedlist' => 'numlist',
				'insertrowabove' => 'tableinsertrowbefore',
				'insertrowbelow' => 'tableinsertrowafter',
				'insertspecialchar' => 'charmap',
				'table_placeholder' => 'table',
				'inserttable' => 'inserttable',
				'insertunorderedlist' => 'bullist',
				'tablemenucell' => 'tablemenucell',
				'tablemenucolumn' => 'tablemenucolumn',
				'tablemenurow' => 'tablemenurow',
				'italic' => 'italic',
				'justifycenter' => 'aligncenter',
				'justifyfull' => 'alignjustify',
				'justifyleft' => 'alignleft',
				'justifyright' => 'alignright',
				'lang' => 'welang',
				'outdent' => 'outdent',
				//'paste' => 'paste',
				'redo' => 'redo',
				'removeformat' => 'removeformat',
				'removetags' => 'cleanup',
				'spellcheck' => '',
				'strikethrough' => 'strikethrough',
				'subscript' => 'subscript',
				'superscript' => 'superscript',
				'underline' => 'underline',
				'undo' => 'undo',
				'unlink' => 'weadaptunlink',
				'visibleborders' => 'wevisualaid',
				// the following commands exist only in tinyMCE
				'absolute' => 'absolute',
				'blockquote' => 'blockquote',
				'cite' => 'cite',
				'del' => 'del',
				'deletetable' => 'tabledelete',
				'emotions' => 'emotions',
				'hr' => '',
				'ins' => 'ins',
				'insertdate' => 'insertdatetime',
				'insertlayer' => '',
				'inserttime' => 'insertdatetime',
				'ltr' => 'ltr',
				'movebackward' => '',
				'moveforward' => '',
				'nonbreaking' => 'nonbreaking',
				'pastetext' => 'pastetext',
				'pasteword' => 'pasteword',
				'replace' => 'searchreplace',
				'rtl' => 'rtl',
				'search' => 'searchreplace',
				'searchreplace' => 'searchreplace',
				'selectall' => 'selectall',
				'styleprops' => 'styleprops',
				'template' => 'template',
				'editrow' => 'tablerowprops',
				'deletetable' => 'tabledelete',
				'edittable' => 'tableprops',
				'copypasterow' => 'tablerowcopypaste',
				'codesample' => 'codesample',
			];

			return isset($cmdMapping[$cmd]) ? $cmdMapping[$cmd] : '';
		}
	}

	function setPlugin($name, $doSet){
		if($doSet){
			$this->tinyPlugins[] = $name;
		}
		return $doSet;
	}

	private function getPlugins(){
		if(IS_TINYMCE_4){
			$usedCommands = array_unique(array_merge($this->usedCommandsMenu, $this->usedCommands));
			$allPlugins = array_merge($this->externalPlugins, $this->internalPlugins);
			$usedPlugins = array_merge(array_unique(array_merge(array_intersect($usedCommands, $allPlugins), $this->tinyPlugins)),
					['compat3x', 'colorpicker', 'paste', 'wordcount', 'weutil', 'contextmenu']);
			$usedPlugins = array_combine($usedPlugins, $usedPlugins);

			return implode(' ', $usedPlugins);
		}
		
		$this->tinyPlugins = implode(',', array_unique($this->tinyPlugins));
		$this->wePlugins = implode(',', array_intersect($this->wePlugins, $this->usedCommands));

		return ($this->createContextmenu ? 'wecontextmenu,' : '') .
			($this->tinyPlugins ? $this->tinyPlugins . ',' : '') .
			($this->wePlugins ? $this->wePlugins . ',' : '') .
			(in_array('wevisualaid', $this->usedCommands) ? 'visualblocks,' : '') .
			(in_array('table', $this->usedCommands) ? 'wetable,' : '') .
			'weutil,wepaste,autolink,template,wewordcount'; //TODO: load "templates" on demand as we do it with other plugins
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

	private function getPropertiesDialog($forEditorType = ''){
		return [
			'weEditorType' => $forEditorType,
			'weCharset' => $this->charset,
			'weName' => $this->name,
			'weFieldName' => $this->fieldName,
			'theme_advanced_toolbar_location' => $this->buttonpos,
			'weSelectorClass' => (IS_TINYMCE_4 ? $this->selectorClass : '') //TINY4
		];
	}

	private function getPropertiesEditor(){
		$edProps = [
			'weEditorType' => $this->editorType,
			'weCharset' => $this->charset,
			'weIsFrontend' => $this->isFrontendEdit,
			'weName' => $this->name,
			'weOrigName' => $this->origName,
			'weFieldName' => $this->fieldName,
			'weFieldNameClean' => $this->fieldName_clean,
			'weDialogProperties' => '',
			'weImageStartID' => intval($this->imageStartID),
			'weGalleryTemplates' => $this->galleryTemplates,
			'weCssClasses' => urlencode($this->cssClasses),
			'weRemoveFirstParagraph' => $this->removeFirstParagraph ? 1 : 0,
			'weTinyParams' => $this->tinyParams,
			'weContentCssParts' => [
				'start' => we_html_element::getUnCache(LIB_DIR . 'additional/fontawesome/css/font-awesome.min.css') . ',' . we_html_element::getUnCache(CSS_DIR . 'wysiwyg/tinymce/contentCssFirst.php') . '&tinyMceBackgroundColor=' . $this->bgcolor,
				'end' => ($this->contentCss ? $this->contentCss : '')
			],
			'weToolbarRows' => $this->toolbarRows,
			'weContextmenuCommands' => $this->getContextmenuCommands(),
			'weClassName' => $this->className,
			'wePluginClasses' => [
				'weadaptbold' => ($suffix = $this->editorLanguage === 'de' ? 'de_' : '') . 'weadaptbold',
				'weadaptitalic' => $suffix . 'weadaptitalic',
				'weabbr' => $suffix . 'weabbr',
				'weacronym' => $suffix . 'weacronym'
			],
			'weFormatselects' => $this->formatselects,

			'language' => $this->editorLanguage,
			'elements' => $this->name,
			'element_format' => $this->xml,
			'plugins' => $this->getPlugins(),
			'theme_advanced_toolbar_location' => $this->buttonpos,
			'theme_advanced_fonts' => $this->fontnames,
			'theme_advanced_font_sizes' => $this->fontsizes,
			'theme_advanced_styles' => $this->tinyCssClasses,
			'theme_advanced_blockformats' => $this->formats,
			'theme_advanced_statusbar_location' => $this->statuspos,
			'editor_css' => we_html_element::getUnCache(CSS_DIR . 'wysiwyg/tinymce/editorCss.css'),
			'popup_css_add' => we_html_element::getUnCache(WEBEDITION_DIR . 'lib/additional/fontLiberation/stylesheet.css') . ',' . we_html_element::getUnCache(WEBEDITION_DIR . 'lib/additional/fontawesome/css/font-awesome.min.css') . ',' . we_html_element::getUnCache(CSS_DIR . 'wysiwyg/tinymce/tinyDialogCss.css'),
			'template_templates' => (in_array('template', $this->usedCommands) && $this->templates ? $this->getTemplates() : '')
		];

		if(IS_TINYMCE_4){ //TINY4
			$edProps = array_merge($edProps, [
					'weSelectorClass' => $this->selectorClass,
					'weSubmenuAlignments' => implode(',', $this->submenuAlignments),
					'table_toolbar' => $this->tableCommands,
					'style_formats' => $this->styleFormats,
					'weBlockImportCss' => $this->blockImportCss,
					'menu' => $this->menu
				]);
		}

		return $edProps;
	}

	private static function getFrontendHeaderConsts(){
		return ['g_l' => ['tinyMceTranslationObject' => self::getTranslationObject()],
				'tables' => ['FILE_TABLE' => FILE_TABLE, 'OBJECT_FILES_TABLE' => OBJECT_FILES_TABLE],
				'dirs' => ['WE_JS_TINYMCE_DIR' => WE_JS_TINYMCE_DIR],
				'linkPrefix' => ['TYPE_INT_PREFIX' => we_base_link::TYPE_INT_PREFIX,
					'TYPE_OBJ_PREFIX' => we_base_link::TYPE_OBJ_PREFIX]
			];
	}

	public static function getTranslationObject(){
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
					'tt_weabbr' => g_l('wysiwyg', '[abbr]'), //TINY4
					'tt_weacronym' => g_l('wysiwyg', '[acronym]'), //TINY4
					'tt_weadaptunlink' => g_l('wysiwyg', '[unlink]'), //TINY4
					'tt_wefullscreen' => g_l('wysiwyg', '[fullscreen]'), //TINY4
					'cm_inserttable' => g_l('wysiwyg', '[insert_table]'),
					'cm_table_props' => g_l('wysiwyg', '[edit_table]'),
					'ed_removedInlinePictures' => g_l('wysiwyg', '[removedInlinePictures]'),
					'dialog_btns' => [
						'btnOk' => ['text' => g_l('button', '[ok][value]'), 'alt' => g_l('button', '[ok][alt]')],
						'btnCancel' => ['text' => g_l('button', '[cancel][value]'), 'alt' => g_l('button', '[cancel][alt]')],
						'btnDelete' => ['text' => g_l('button', '[delete][value]'), 'alt' => g_l('button', '[delete][alt]')],
						'btnSearchNext' => ['text' => g_l('buttons_global', '[searchContinue][value]'), 'alt' => g_l('buttons_global', '[searchContinue][value]')],
						'btnReplace' => ['text' => g_l('buttons_global', '[replace][value]'), 'alt' => g_l('buttons_global', '[replace][value]')],
						'btnReplaceAll' => ['text' => g_l('buttons_global', '[replaceAll][value]'), 'alt' => g_l('buttons_global', '[replaceAll][value]')],
					]
				]
			]
		];
	}

	function getHTML(){
		switch($this->editorType){
			case self::TYPE_EDITBUTTON:
				self::$dataConfigurations[] = $this->getPropertiesEditor();
				self::$countInstances++;
				return $this->getHTMLEditButton();
			case self::TYPE_INLINE_TRUE:
				$configuration = $this->getPropertiesEditor();
				$configuration['weDialogProperties'] = urlencode(json_encode($this->getPropertiesDialog(self::TYPE_FULLSCREEN)));
				self::$dataConfigurations[] = $configuration;
				self::$countInstances++;
				/* fall through */
			case self::TYPE_INLINE_FALSE:
			case self::TYPE_FULLSCREEN:
				return $this->getHTMLEditor();
		}
	}

	public static function getHTMLConfigurationsTag(){
		return we_html_baseElement::getHtmlCode(new we_html_baseElement('we-dataTiny', false, ['id' => 'loadVar_tinyConfigs',
				'data-dialogProperties' => setDynamicVar(self::$dataDialogProperties),
				'data-configurations' => setDynamicVar(self::$dataConfigurations),
			]));
	}

	public static function isWysiwygInstances(){
		return self::$countInstances !== 0;
	}

	private function getHTMLEditButton(){
		$js_function = $this->isFrontendEdit ? 'open_wysiwyg_win' : 'we_cmd';

		$param4 = !$this->isFrontendEdit ? '' : 'frontend';
		$width = we_base_util::convertUnits($this->width);
		$width = is_numeric($width) ? max($width, self::MIN_WIDTH_POPUP) : '(' . intval($width) . '/100*screen.availWidth)';
		$height = we_base_util::convertUnits($this->height);
		$height = is_numeric($height) ? max($height, self::MIN_HEIGHT_POPUP) : '(' . intval($height) . '/100*screen.availHeight)';

		$dialogProperties = urlencode(json_encode($this->getPropertiesDialog(self::TYPE_INLINE_FALSE)));

		return we_html_button::create_button(we_html_button::EDIT, "javascript:" . $js_function . "('open_wysiwyg_window', '" . $dialogProperties . "', " . $width . ", " . $height . ");");
	}

	private function getHTMLEditor(){
		$editValue = self::parseInternalImageSrc($this->value);
		$height = we_base_util::convertUnits($this->height);
		$width = we_base_util::convertUnits($this->width);
		if(is_numeric($height) && is_numeric($width) && $width){
			//only a simple fix
			$this->height = $height = $height - ($this->buttonpos === 'external' ? 0 : round((($k) / ($width / (5 * 22))) * 26));
		}
		$width = (is_numeric($width) ? round(max($width, self::MIN_WIDTH_INLINE) / 96, 3) . 'in' : $width);
		$height = (is_numeric($height) ? round(max($height, self::MIN_HEIGHT_INLINE) / 96, 3) . 'in' : $height);

		return /*$this->getHTMLSupportText() .*/ getHtmlTag('textarea', ['wrap' => "off",
				'style' => 'color:#eeeeee; background-color:#eeeeee;  width:' . $width . '; height:' . $height . ';',
					'id' => $this->name,
					'name' => $this->name,
					'class' => (!IS_TINYMCE_4 ? 'wetextarea' : 'wetextarea ' . $this->selectorClass) //TINY4
				], strtr($editValue, ['\n' => '', '&' => '&amp;']), true);
	}

	/* TODO: add tutorial for TinyWrapper and adding additional event listeners to webEdition documentation */
	/*
	private function getHTMLSupportText(){
		return '
<!--
--- tinyMCE ---
* To adress this instance of tinyMCE by your template JavaScript use the webedition wrapper object: "TinyWrapper(\'' . $this->fieldName . '\')"
* Place "function we_tinyMCE_' . $this->fieldName_clean . '_init(ed){// custom code}" to your JavaScript to add additional event listeners to this editor.
* Learn more about manipulating tiny instances from the webEdition Documentation.
-->
';
	}
	 *
	 */


}
