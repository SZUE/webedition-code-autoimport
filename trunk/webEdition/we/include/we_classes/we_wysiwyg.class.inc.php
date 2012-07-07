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
 * @package    webEdition_wysiwyg
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//make sure we know which browser is used

class we_wysiwyg{

	var $name = "";
	var $width = "";
	var $height = "";
	var $ref = "";
	var $propstring = "";
	var $elements = array();
	var $value = "";
	var $filteredElements = array();
	var $bgcol = "white";
	var $fullscreen = "";
	var $className = "";
	var $fontnames = array();
	var $maxGroupWidth = 0;
	var $outsideWE = false;
	var $xml = false;
	var $removeFirstParagraph = true;
	var $charset = "";
	var $inlineedit = true;
	var $cssClasses = "";
	var $Language = "";
	var $_imagePath;
	var $_image_languagePath;
	var $baseHref = "";
	var $showSpell = true;
	var $isFrontendEdit = false;

	function __construct($name, $width, $height, $value = "", $propstring = "", $bgcol = "", $fullscreen = "", $className = "", $fontnames = "", $outsideWE = false, $xml = false, $removeFirstParagraph = true, $inlineedit = true, $baseHref = "", $charset = "", $cssClasses = "", $Language = "", $test = "", $spell = true, $isFrontendEdit = false){

		$this->propstring = $propstring ? "," . $propstring . "," : "";
		$this->name = $name;
		$this->bgcol = $bgcol;
		$this->xml = $xml;
		$this->removeFirstParagraph = $removeFirstParagraph;
		$this->inlineedit = $inlineedit;
		$this->fullscreen = $fullscreen;
		$this->className = $className;
		$this->outsideWE = $outsideWE;
		$fn = $fontnames ? explode(",", $fontnames) : array("Arial, Helvetica, sans-serif", "Courier New, Courier, mono", "Geneva, Arial, Helvetica, sans-serif", "Georgia, Times New Roman, Times, serif", "Tahoma", "Times New Roman, Times, serif", "Verdana, Arial, Helvetica, sans-serif", "Wingdings");
		$this->cssClasses = $cssClasses;
		$this->Language = $Language;
		$this->showSpell = $spell;
		$this->isFrontendEdit = $isFrontendEdit;
		foreach($fn as $i => $font){
			$fn[$i] = strtolower(str_replace(";", ",", $font));
		}

		$this->_imagePath = IMAGE_DIR . "wysiwyg/";
		$this->_image_languagePath = WEBEDITION_DIR . "we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/wysiwyg/";

		$this->fontnames = array();


		$this->baseHref = $baseHref ? $baseHref : we_util::getGlobalPath();
		$this->charset = $charset;


		foreach($fn as $font){
			$this->fontnames[$font] = $font;
		}

		$this->width = $width;
		$this->height = $height;
		$this->ref = preg_replace('%[^0-9a-zA-Z_]%', '', $this->name);
		$this->hiddenValue = $value;

		if($inlineedit){
			if($value){
				$value = str_replace("\\", "\\\\", $value);
				$value = str_replace("\n", "\\n", $value);
				$value = str_replace("\r", "\\r", $value);
				$value = str_replace('script', '##scr#ipt##', $value);
				$value = str_replace('Script', '##Scr#ipt##', $value);
				$value = str_replace('SCRIPT', '##SCR#IPT##', $value);
				$value = preg_replace('%<\?xml[^>]*>%i', '', $value);
				$value = str_replace('<?', '||##?##||', $value);
				$value = str_replace('?>', '##||?||##', $value);
			}
		}

		$this->setToolbarElements();
		$this->setFilteredElements();
		$this->getMaxGroupWidth();
		$this->value = $value;
	}

	function getMaxGroupWidth(){
		$w = 0;
		foreach($this->filteredElements as $i => $v){
			if($v->classname == "we_wysiwygToolbarSeparator"){
				$this->maxGroupWidth = max($w, $this->maxGroupWidth);
				$w = 0;
			} else{
				$w += $v->width;
			}
		}
		$this->maxGroupWidth = max($w, $this->maxGroupWidth);
	}

	function getHeaderHTML(){
		if(defined("WE_WYSIWG_HEADER")){
			return '';
		}

		define("WE_WYSIWG_HEADER", 1);
		if(!defined('WYSIWYG_TYPE'))
			define('WYSIWYG_TYPE', 'default');
		switch(WYSIWYG_TYPE){
			case 'tinyMCE':
				//FIXME: remove onchange - bad practise
				return we_html_element::jsScript(WEBEDITION_DIR . 'editors/content/tinymce/jscripts/tiny_mce/tiny_mce.js') . we_html_element::jsElement('
function tinyMCEchanged(inst){
	if(inst.isDirty()){
		_EditorFrame.setEditorIsHot(true);
	}
}');
			case 'default':
				return '<iframe id="we_wysiwyg_lng_frame" src="/webEdition/editors/content/wysiwyg/weWysiwygLang.php" style="display:none;"></iframe>
				<style type="text/css">
					.tbButton {
						border: 1px solid #F4F4F4;
						padding: 0px;
						margin: 0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}

					.tbButtonMouseOverUp {
    					border-bottom: 1px solid #000000;
    					border-left: 1px solid #CCCCCC;
    					border-right: 1px solid #000000;
    					border-top: 1px solid #CCCCCC;
    					cursor:pointer;
						margin: 0px;
						padding:0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonMouseOverDown {
    					border-bottom: 1px solid #CCCCCC;
    					border-left: 1px solid #000000;
    					border-right: 1px solid #CCCCCC;
    					border-top: 1px solid #000000;
    					cursor: pointer;
						margin: 0px;
						padding: 0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonDown {
    					background-image: url(' . IMAGE_DIR . 'java_menu/background_dark.gif);
    					border-bottom: #CCCCCC solid 1px;
    					border-left: #000000 solid 1px;
    					border-right: #CCCCCC solid 1px;
    					border-top:  #000000 solid 1px;
						margin: 0px;
						padding:0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonsHR {
    					border-top:  #000000 solid 1px;
    					border-bottom:  #CCCCCC solid 1px;
						margin: 0px;
						padding:0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonWysiwygBorder
					{
						border: 1px solid #006DB8;
					    background-image: url(/webEdition/images/pixel.gif);
						margin: 0px;
						padding:4px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonWysiwygBackground{
						background-image: url(/webEdition/images/backgrounds/aquaBackground.gif) ! important;
					}
					.tbButtonWysiwygDefaultStyle{
						background: transparent;
						background-color: transparent;
						background-image: url(/webEdition/images/pixel.gif);
						border: 0px;
						color: #000000;
						cursor: default;
						font-size: ' . ((we_base_browserDetect::isMAC()) ? "11px" : ((we_base_browserDetect::isUNIX()) ? "13px" : "12px")) . ';
						font-family: ' . g_l('css', '[font_family]') . ';
						font-weight: normal;
						margin: 0px;
						padding:0px;
						text-align: left;
						text-decoration: none;
						left: auto ! important;
						right: auto ! important;
						width: auto ! important;
						height: auto ! important;
					}

				</style>' . we_html_element::jsElement('
					var we_wysiwygs = new Array();
					var we_wysiwyg_lng = new Array();
					//FIXME: recognize in browser_check an set according
					var isGecko = ' . (we_base_browserDetect::isGecko() ? 'true' : 'false') . ';
					var isOpera = ' . (we_base_browserDetect::isOpera() ? 'true' : 'false') . ';
					var isIE = ' . (we_base_browserDetect::isIE() ? 'true' : 'false') . ';
					var ieVersion = ' . we_base_browserDetect::getIEVersion() . ';
					var isIE9 = ' . ((we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() == 9) ? 'true' : 'false') . ';
					var weWysiwygLoaded = false;
					var weNodeList = new Array();
					var weWysiwygFolderPath = "/webEdition/editors/content/wysiwyg/";
					var weWysiwygImagesFolderPath = "/webEdition/images/wysiwyg/";
					var weWysiwygBgGifPath = "' . IMAGE_DIR . 'backgrounds/aquaBackground.gif";
					var weWysiwygIsIntialized = false;

					var wePopupMenuArray = new Array();

                    //  Bugfix do not overwrite body.onload !!!
                    function weEvent(){}
                    weEvent.addEvent = function(e, name, f) {
                        if (e.addEventListener) {
                            e.addEventListener(
                                name,
                                f,
                                true);
                        }
                        if(e.attachEvent){
                            e.attachEvent("on" + name, f);
                        }
                    }

					//window.onerror = weNothing;
					//  Bugfix do not overwrite body.onload !!!
					weEvent.addEvent(window,"load", weWysiwygInitializeIt);
					//window.onload = weWysiwygInitializeIt + window.onload;

					function weNothing() {
						return true;
					}


					function weWysiwygInitializeIt() {
						for (var i=0;i<we_wysiwygs.length;i++) {
							we_wysiwygs[i].start();
						}
						for (var i=0;i<we_wysiwygs.length;i++) {
							we_wysiwygs[i].finalize();
 							we_wysiwygs[i].windowFocus();
							we_wysiwygs[i].setButtonsState();
						}
						self.focus();
						weWysiwygIsIntialized = true;
						weWysiwygSetHiddenTextSync();
					}

					function weWysiwygSetHiddenTextSync(){
						weWysiwygSetHiddenText(1);
						setTimeout(weWysiwygSetHiddenTextSync,500);
					}

					function weWysiwygSetHiddenText(arg) {
    					try {
    						if (weWysiwygIsIntialized) {
    							for (var i = 0; i < we_wysiwygs.length; i++) {
    								we_wysiwygs[i].setHiddenText(arg);
    							}
    						}else{
								}
    					} catch(e) {
							// Nothing
    					}
					}') .
					we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
					(we_base_browserDetect::isSafari() ? we_html_element::jsScript(WEBEDITION_DIR . 'editors/content/wysiwyg/weWysiwygSafari.js') .
						we_html_element::jsScript(JS_DIR . 'weDOM_Safari.js') : we_html_element::jsScript(WEBEDITION_DIR . 'editors/content/wysiwyg/weWysiwyg.js'));
		}
	}

	function getAllCmds(){
		$arr = array('formatblock',
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
			'edittable',
			'editcell',
			'insertcolumnright',
			'insertcolumnleft',
			'insertrowabove',
			'insertrowbelow',
			'deletecol',
			'deleterow',
			'increasecolspan',
			'decreasecolspan',
			'caption',
			'removecaption',
			'importrtf',
			'fullscreen',
			'cut',
			'copy',
			'paste',
			'undo',
			'redo',
			'visibleborders',
			'editsource',
			'insertbreak',
			'acronym',
			'abbr',
			'lang'
		);
		if(defined('SPELLCHECKER')){
			$arr[] = "spellcheck";
		}
		return $arr;
	}

	function setToolbarElements(){
		$this->elements = array(
			new we_wysiwygToolbarSelect(
				$this,
				"formatblock",
				g_l('wysiwyg', "[format]"),
				we_base_browserDetect::isIE() ? array(
					"normal" => g_l('wysiwyg', "[normal]"),
					"p" => g_l('wysiwyg', "[paragraph]"),
					"h1" => g_l('wysiwyg', "[h1]"),
					"h2" => g_l('wysiwyg', "[h2]"),
					"h3" => g_l('wysiwyg', "[h3]"),
					"h4" => g_l('wysiwyg', "[h4]"),
					"h5" => g_l('wysiwyg', "[h5]"),
					"h6" => g_l('wysiwyg', "[h6]"),
					"pre" => g_l('wysiwyg', "[pre]"),
					"address" => g_l('wysiwyg', "[address]")
					) : (we_base_browserDetect::isSafari() ? array(
						"div" => g_l('wysiwyg', "[normal]"),
						"p" => g_l('wysiwyg', "[paragraph]"),
						"h1" => g_l('wysiwyg', "[h1]"),
						"h2" => g_l('wysiwyg', "[h2]"),
						"h3" => g_l('wysiwyg', "[h3]"),
						"h4" => g_l('wysiwyg', "[h4]"),
						"h5" => g_l('wysiwyg', "[h5]"),
						"h6" => g_l('wysiwyg', "[h6]"),
						"pre" => g_l('wysiwyg', "[pre]"),
						"address" => g_l('wysiwyg', "[address]"),
						"blockquote" => "blockquote"
						) : array(
						"normal" => g_l('wysiwyg', "[normal]"),
						"p" => g_l('wysiwyg', "[paragraph]"),
						"h1" => g_l('wysiwyg', "[h1]"),
						"h2" => g_l('wysiwyg', "[h2]"),
						"h3" => g_l('wysiwyg', "[h3]"),
						"h4" => g_l('wysiwyg', "[h4]"),
						"h5" => g_l('wysiwyg', "[h5]"),
						"h6" => g_l('wysiwyg', "[h6]"),
						"pre" => g_l('wysiwyg', "[pre]"),
						"address" => g_l('wysiwyg', "[address]"),
						"code" => "Code",
						"cite" => "Cite",
						"q" => "q",
						"blockquote" => "blockquote"
						)),
				120
			),
			new we_wysiwygToolbarSelect(
				$this,
				"fontname",
				g_l('wysiwyg', "[fontname]"),
				$this->fontnames,
				120
			),
			new we_wysiwygToolbarSelect(
				$this,
				"fontsize",
				g_l('wysiwyg', "[fontsize]"),
				we_base_browserDetect::isSafari() ? array(
					"8px" => "8px",
					"9px" => "9px",
					"10px" => "10px",
					"11px" => "11px",
					"12px" => "12px",
					"13px" => "13px",
					"14px" => "14px",
					"15px" => "15px",
					"16px" => "16px",
					"17px" => "17px",
					"18px" => "18px",
					"19px" => "19px",
					"20px" => "20px",
					"21px" => "21px",
					"22px" => "22px",
					"24px" => "24px",
					"26px" => "26px",
					"28px" => "28px",
					"30px" => "30px",
					"36px" => "36px"
					) : array(
					"1" => "1",
					"2" => "2",
					"3" => "3",
					"4" => "4",
					"5" => "5",
					"6" => "6",
					"7" => "7"
					),
				120
			),
			new we_wysiwygToolbarSelect(
				$this,
				"applystyle",
				g_l('wysiwyg', "[css_style]"),
				array(),
				120
			),
			new we_wysiwygToolbarSeparator($this),
			new we_wysiwygToolbarButton(
				$this,
				"bold",
				$this->_image_languagePath . "bold.gif",
				g_l('wysiwyg', "[bold]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"italic",
				$this->_image_languagePath . "italic.gif",
				g_l('wysiwyg', "[italic]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"underline",
				$this->_image_languagePath . "underline.gif",
				g_l('wysiwyg', "[underline]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"subscript",
				$this->_imagePath . "subscript.gif",
				g_l('wysiwyg', "[subscript]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"superscript",
				$this->_imagePath . "superscript.gif",
				g_l('wysiwyg', "[superscript]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"strikethrough",
				$this->_imagePath . "strikethrough.gif",
				g_l('wysiwyg', "[strikethrough]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"removeformat",
				$this->_imagePath . "removeformat.gif",
				g_l('wysiwyg', "[removeformat]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"removetags",
				$this->_imagePath . "removetags.gif",
				g_l('wysiwyg', "[removetags]")
			),
			new we_wysiwygToolbarSeparator($this),
			new we_wysiwygToolbarButton(
				$this,
				"acronym",
				$this->_image_languagePath . "acronym.gif",
				g_l('wysiwyg', "[acronym]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"abbr",
				$this->_image_languagePath . "abbr.gif",
				g_l('wysiwyg', "[abbr]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"lang",
				$this->_imagePath . "lang.gif",
				g_l('wysiwyg', "[language]")
			),
			new we_wysiwygToolbarSeparator($this),
			new we_wysiwygToolbarButton(
				$this,
				"forecolor",
				$this->_imagePath . "setforecolor.gif",
				g_l('wysiwyg', "[fore_color]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"backcolor",
				$this->_imagePath . "setbackcolor.gif",
				g_l('wysiwyg', "[back_color]")
			),
			new we_wysiwygToolbarSeparator($this),
			new we_wysiwygToolbarButton(
				$this,
				"justifyleft",
				$this->_imagePath . "justifyleft.gif",
				g_l('wysiwyg', "[justify_left]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"justifycenter",
				$this->_imagePath . "justifycenter.gif",
				g_l('wysiwyg', "[justify_center]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"justifyright",
				$this->_imagePath . "justifyright.gif",
				g_l('wysiwyg', "[justify_right]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"justifyfull",
				$this->_imagePath . "justifyfull.gif",
				g_l('wysiwyg', "[justify_full]")
			),
			new we_wysiwygToolbarSeparator($this),
			new we_wysiwygToolbarButton(
				$this,
				"insertunorderedlist",
				$this->_imagePath . "unorderlist.gif",
				g_l('wysiwyg', "[unordered_list]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"insertorderedlist",
				$this->_imagePath . "orderlist.gif",
				g_l('wysiwyg', "[ordered_list]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"indent",
				$this->_imagePath . "indent.gif",
				g_l('wysiwyg', "[indent]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"outdent",
				$this->_imagePath . "outdent.gif",
				g_l('wysiwyg', "[outdent]")
			),
			new we_wysiwygToolbarSeparator($this),
			new we_wysiwygToolbarButton(
				$this,
				"createlink",
				$this->_imagePath . "hyperlink.gif",
				g_l('wysiwyg', "[hyperlink]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"unlink",
				$this->_imagePath . "unlink.gif",
				g_l('wysiwyg', "[unlink]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"anchor",
				$this->_imagePath . "anchor.gif",
				g_l('wysiwyg', "[insert_edit_anchor]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"insertimage",
				$this->_imagePath . "image.gif",
				g_l('wysiwyg', "[insert_edit_image]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"inserthorizontalrule",
				$this->_imagePath . "rule.gif",
				g_l('wysiwyg', "[inserthorizontalrule]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"insertspecialchar",
				$this->_imagePath . "specialchar.gif",
				g_l('wysiwyg', "[insertspecialchar]")
			),
			new we_wysiwygToolbarSeparator($this),
			new we_wysiwygToolbarButton(
				$this,
				"inserttable",
				$this->_imagePath . "inserttable.gif",
				g_l('wysiwyg', "[inserttable]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"edittable",
				$this->_imagePath . "edittable.gif",
				g_l('wysiwyg', "[edittable]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"editcell",
				$this->_imagePath . "editcell.gif",
				g_l('wysiwyg', "[editcell]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"insertcolumnleft",
				$this->_imagePath . "insertcol_left.gif",
				g_l('wysiwyg', "[insertcolumnleft]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"insertcolumnright",
				$this->_imagePath . "insertcol_right.gif",
				g_l('wysiwyg', "[insertcolumnright]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"insertrowabove",
				$this->_imagePath . "insertrow_above.gif",
				g_l('wysiwyg', "[insertrowabove]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"insertrowbelow",
				$this->_imagePath . "insertrow_below.gif",
				g_l('wysiwyg', "[insertrowbelow]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"deletecol",
				$this->_imagePath . "deletecols.gif",
				g_l('wysiwyg', "[deletecol]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"deleterow",
				$this->_imagePath . "deleterows.gif",
				g_l('wysiwyg', "[deleterow]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"increasecolspan",
				$this->_imagePath . "inc_col.gif",
				g_l('wysiwyg', "[increasecolspan]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"decreasecolspan",
				$this->_imagePath . "dec_col.gif",
				g_l('wysiwyg', "[decreasecolspan]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"caption",
				$this->_imagePath . "caption.gif",
				g_l('wysiwyg', "[addcaption]")
			),
			new we_wysiwygToolbarButton(
				$this,
				"removecaption",
				$this->_imagePath . "removecaption.gif",
				g_l('wysiwyg', "[removecaption]")
			),
			new we_wysiwygToolbarSeparator($this),
			new we_wysiwygToolbarButton(
				$this,
				"insertbreak",
				$this->_imagePath . "br.gif",
				g_l('wysiwyg', "[insert_br]")
			),
			new we_wysiwygToolbarSeparator($this),
			new we_wysiwygToolbarButton(
				$this,
				"importrtf",
				$this->_imagePath . "rtf.gif",
				g_l('wysiwyg', "[rtf_import]")
			),
		);
		if(!$this->fullscreen){
			$this->elements[] = new we_wysiwygToolbarButton(
					$this,
					"fullscreen",
					$this->_imagePath . "fullscreen.gif",
					g_l('wysiwyg', "[fullscreen]")
			);
		}
		array_push(
			$this->elements, new we_wysiwygToolbarSeparator($this), new we_wysiwygToolbarButton(
				$this,
				"cut",
				$this->_imagePath . "cut.gif",
				g_l('wysiwyg', "[cut]")
			), new we_wysiwygToolbarButton(
				$this,
				"copy",
				$this->_imagePath . "copy.gif",
				g_l('wysiwyg', "[copy]")
			), new we_wysiwygToolbarButton(
				$this,
				"paste",
				$this->_imagePath . "paste.gif",
				g_l('wysiwyg', "[paste]")
			), new we_wysiwygToolbarSeparator($this), new we_wysiwygToolbarButton(
				$this,
				"undo",
				$this->_imagePath . "undo.gif",
				g_l('wysiwyg', "[undo]")
			), new we_wysiwygToolbarButton(
				$this,
				"redo",
				$this->_imagePath . "redo.gif",
				g_l('wysiwyg', "[redo]")
			), new we_wysiwygToolbarSeparator($this), new we_wysiwygToolbarButton(
				$this,
				"visibleborders",
				$this->_imagePath . "visibleborders.gif",
				g_l('wysiwyg', "[visible_borders]")
			), new we_wysiwygToolbarButton(
				$this,
				"editsource",
				$this->_imagePath . "editsourcecode.gif",
				g_l('wysiwyg', "[edit_sourcecode]")
			)
		);
		if(defined('SPELLCHECKER') && $this->showSpell){
			$this->elements[] = new we_wysiwygToolbarButton(
					$this,
					'spellcheck',
					$this->_imagePath . 'spellcheck.gif',
					g_l('wysiwyg', '[spellcheck]')
			);
		}
	}

	function getWidthOfElem($startPos, $end){
		$w = 0;
		for($i = $startPos; $i <= $end; $i++){
			$w += $this->filteredElements[$i]->width;
		}
		return $w;
	}

	function setFilteredElements(){
		$lastSep = true;
		foreach($this->elements as $i => $elem){
			if($elem->showMe){
				if((!$lastSep) || ($elem->classname != "we_wysiwygToolbarSeparator")){
					array_push($this->filteredElements, $elem);
				}
				$lastSep = ($elem->classname == "we_wysiwygToolbarSeparator");
			}
		}
		if(sizeof($this->filteredElements)){
			if($this->filteredElements[sizeof($this->filteredElements) - 1]->classname == "we_wysiwygToolbarSeparator"){
				array_pop($this->filteredElements);
			}
		}
	}

	function hasSep($rowArr){
		foreach($rowArr as $i => $elem){
			if($elem->classname == "we_wysiwygToolbarSeparator")
				return true;
		}
		return false;
	}

	function getEditButtonHTML(){

		list($tbwidth, $tbheight) = $this->getToolbarWidthAndHeight();

		$fns = "";
		foreach($this->fontnames as $fn){
			$fns .= str_replace(",", ";", $fn) . ",";
		}
		$fns = rtrim($fns, ',');
		return we_button::create_button("image:btn_edit_edit", "javascript:we_cmd('open_wysiwyg_window', '" . $this->name . "', '" . max(220, $this->width) . "', '" . $this->height . "','" . $GLOBALS["we_transaction"] . "','" . $this->propstring . "','" . $this->className . "','" . $fns . "','" . $this->outsideWE . "','" . $tbwidth . "','" . $tbheight . "','" . $this->xml . "','" . $this->removeFirstParagraph . "','" . $this->bgcol . "','" . $this->baseHref . "','" . $this->charset . "','" . $this->cssClasses . "','" . $this->Language . "');", true, 25);
	}

	function getHTML(){
		return ($this->inlineedit ? $this->getInlineHTML() : $this->getEditButtonHTML());
	}

	function getToolbarRows(){
		$tmpElements = $this->filteredElements;
		$rows = array();
		$rownr = 0;
		$rows[$rownr] = array();
		$rowwidth = 0;
		while(sizeof($tmpElements)) {
			if(!$this->hasSep($rows[$rownr]) || $rowwidth <= max($this->width, $this->maxGroupWidth)){
				array_push($rows[$rownr], array_shift($tmpElements));
				$rowwidth += $rows[$rownr][sizeof($rows[$rownr]) - 1]->width;
			} else{
				if(sizeof($rows[$rownr])){
					if($rows[$rownr][sizeof($rows[$rownr]) - 1]->classname == "we_wysiwygToolbarSeparator"){
						array_pop($rows[$rownr]);
						$rownr++;
						$rowwidth = 0;
						$rows[$rownr] = array();
					} else{
						while($tmpElements[0]->classname != "we_wysiwygToolbarSeparator") {
							array_unshift($tmpElements, array_pop($rows[$rownr]));
						}
						array_shift($tmpElements);
						$rownr++;
						$rowwidth = 0;
						$rows[$rownr] = array();
					}
				}
			}
		}
		return $rows;
	}

	function getToolbarWidthAndHeight(){

		$rows = $this->getToolbarRows();
		$toolbarheight = 0;
		$min_w = 0;
		$row_w = 0;
		for($r = 0; $r < sizeof($rows); $r++){
			$rowheight = 0;
			for($s = 0; $s < sizeof($rows[$r]); $s++){
				$rowheight = max($rowheight, $rows[$r][$s]->height);
				$row_w += $rows[$r][$s]->width;
			}
			$toolbarheight += ($rowheight + 2);
			$min_w = max($min_w, $row_w);
			$row_w = 0;
		}

		$realWidth = max($min_w, $this->width);
		return array($realWidth, $toolbarheight);
	}

	function getInlineHTML(){
		$rows = $this->getToolbarRows();
		$editValue = $this->value;
		$regs = array();
		if(preg_match_all('/src="document:(\\d+)/i', $editValue, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($reg[1]), 'Path', $GLOBALS['DB_WE']);
				$editValue = str_ireplace('src="document:' . $reg[1], 'src="' . $path . "?id=" . $reg[1], $editValue);
			}
		}
		if(preg_match_all('/src="thumbnail:([^" ]+)/i', $editValue, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				list($imgID, $thumbID) = explode(",", $reg[1]);
				$thumbObj = new we_thumbnail();
				$thumbObj->initByImageIDAndThumbID($imgID, $thumbID);
				$editValue = str_ireplace('src="thumbnail:' . $reg[1], 'src="' . $thumbObj->getOutputPath() . "?thumb=" . $reg[1], $editValue);
				unset($thumbObj);
			}
		}

		switch(WYSIWYG_TYPE){
			case 'tinyMCE':
				list($lang, $code) = explode('_', $GLOBALS["weDefaultFrontendLanguage"]);
				//deactivated: template,save,layer
				return we_html_element::jsElement('
tinyMCE.init({
	language : "' . $lang . '",
	mode : "exact",
	elements : "' . $this->name . '",
	theme : "advanced",

	//CallBacks
	//file_browser_callback : "",
	onchange_callback : "tinyMCEchanged",
	plugins : "spellchecker,style,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",

	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,sub,sup,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "insertdate,inserttime,pastetext,pasteword,removeformat,cleanup,|,search,replace,|,bullist,numlist,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,help,code,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,visualaid,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,fullscreen,preview,print",
	theme_advanced_buttons4 : "styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,blockquote",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_source_editor_height : "300",
	theme_advanced_source_editor_width : "500",
	plugin_preview_height : "300",
	plugin_preview_width : "500",
	theme_advanced_disable : "",
	// Skin options
	skin : "o2k7",
	skin_variant : "silver",
});') . '
<textarea wrap="off" style="color:black;  width:' . $this->width . 'px; height:' . $this->height . 'px;" id="' . $this->name . '" name="' . $this->name . '">' . str_replace('\n', '', $this->value) . '</textarea>';

			case 'default':

//parseInternalLinks($editValue,0);

				$min_w = 0;
				$row_w = 0;
				$pixelrow = '<tr><td background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" class="tbButtonWysiwygDefaultStyle tbButtonWysiwygBackground">' . we_html_tools::getPixel($this->width, 2) . '</td></tr>';
				$linerow = '<tr><td ><div class="tbButtonsHR" class="tbButtonWysiwygDefaultStyle"></div></td></tr>';
				$out = we_html_element::jsElement('var weLastPopupMenu = null; var wefoo = "' . $this->ref . 'edit"; wePopupMenuArray[wefoo] = new Array();') . '<table id="' . $this->ref . 'edit_table" border="0" cellpadding="0" cellspacing="0" width="' . $this->width . '" class="tbButtonWysiwygDefaultStyle"><tr><td  background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" class="tbButtonWysiwygDefaultStyle tbButtonWysiwygBackground">';
				for($r = 0; $r < sizeof($rows); $r++){
					$out .= '<table border="0" cellpadding="0" cellspacing="0" class="tbButtonWysiwygDefaultStyle"><tr>';
					for($s = 0; $s < sizeof($rows[$r]); $s++){
						$out .= '<td class="tbButtonWysiwygDefaultStyle">' . $rows[$r][$s]->getHTML() . '</td>';
						$row_w += $rows[$r][$s]->width;
					}
					$min_w = max($min_w, $row_w);
					$row_w = 0;
					$out .= '</tr></table></td></tr>' . (($r < sizeof($rows) - 1) ? $linerow : $pixelrow) . '<tr><td ' . (($r < (sizeof($rows) - 1)) ? (' bgcolor="white"  background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif"') : '') . ' class="tbButtonWysiwygDefaultStyle' . (($r < (sizeof($rows) - 1)) ? ' tbButtonWysiwygBackground' : '') . '">';
				}

				$realWidth = max($min_w, $this->width);
				$out .= '<table border="0" cellpadding="0" cellspacing="0"  class="tbButtonWysiwygDefaultStyle"><tr><td class="tbButtonWysiwygDefaultStyle"><textarea wrap="off" style="color:black; display: none;font-family: courier; font-size: 10pt; width:' . $realWidth . 'px; height:' . $this->height . 'px;" id="' . $this->ref . 'edit_src" name="' . $this->ref . 'edit_src"></textarea><iframe contenteditable  width="' . $realWidth . '" height="' . $this->height . '" name="' . $this->ref . 'edit" id="' . $this->ref . 'edit" allowTransparency="true" ';
				if(we_base_browserDetect::isSafari()){
					$out.='style="display: block;color: black;border: 1px solid #A5ACB2;-khtml-user-select:none;"  src="/webEdition/editors/content/wysiwyg/empty.html"';
				} else{
					$out.='style="display: block;color: black;border: 1px solid #A5ACB2;"';
				}
				$out .='></iframe></td></tr>
</table></td></tr></table><input type="hidden" id="' . $this->name . '" name="' . $this->name . '" value="' . htmlspecialchars($this->hiddenValue) . '" /><div id="' . $this->ref . 'edit_buffer" style="display: none;"></div>
' . we_html_element::jsElement('
var ' . $this->ref . 'Obj = null;
' . $this->ref . 'Obj = new weWysiwyg("' . $this->ref . 'edit","' . $this->name . '","' . str_replace("\"", "\\\"", $this->value) . '","' . str_replace("\"", "\\\"", $editValue) . '",\'' . $this->fullscreen . '\',\'' . $this->className . '\',\'' . $this->propstring . '\',\'' . $this->bgcol . '\',' . ($this->outsideWE ? "true" : "false") . ',"' . $this->baseHref . '","' . $this->xml . '","' . $this->removeFirstParagraph . '","' . $this->charset . '","' . $this->cssClasses . '","' . $this->Language . '", "' . ($this->isFrontendEdit ? 1 : 0) . '");
we_wysiwygs[we_wysiwygs.length] = ' . $this->ref . 'Obj;

function ' . $this->ref . 'editShowContextMenu(event){
	return ' . $this->ref . 'Obj.showContextMenu(event);
}
function ' . $this->ref . 'editonkeydown(){
	return we_on_key_down(' . $this->ref . 'Obj);
}
function ' . $this->ref . 'editonkeyup(){
	return we_on_key_up(' . $this->ref . 'Obj);
}
function ' . $this->ref . 'editonmouseup(){
	return we_on_mouse_up(' . $this->ref . 'Obj);
}
function ' . $this->ref . 'editonfocus(){
	return we_on_focus(' . $this->ref . 'Obj);
}
function ' . $this->ref . 'editonblur(){
	return we_on_blur(' . $this->ref . 'Obj);
}');
				return $out;
		}
	}

}

class we_wysiwygToolbarElement{

	var $width;
	var $height;
	var $cmd;
	var $editor;
	var $classname = "we_wysiwygToolbarElement";
	var $showMe = false;

	function we_wysiwygToolbarElement($editor, $cmd, $width, $height = ""){
		$this->editor = $editor;
		$this->width = $width;
		$this->height = $height;
		$this->cmd = $cmd;
		$this->showMe = $this->hasProp();
	}

	function getHTML(){
		return '';
	}

	function hasProp(){
		return preg_match('%,' . $this->cmd . ',%i', $this->editor->propstring) || ($this->editor->propstring == '');
	}

}

class we_wysiwygToolbarSeparator extends we_wysiwygToolbarElement{

	var $classname = "we_wysiwygToolbarSeparator";

	function we_wysiwygToolbarSeparator($editor, $width = 3, $height = 22){
		$this->we_wysiwygToolbarElement($editor, "", $width, $height);
	}

	function getHTML(){
		return '<div style="border-right: #999999 solid 1px; font-size: 0px; height: ' . $this->height . 'px ! important; width: ' . ($this->width - 1) . 'px;position: relative;" class="tbButtonWysiwygDefaultStyle"></div>';
	}

	function hasProp(){
		return true;
	}

}

class we_wysiwygToolbarButton extends we_wysiwygToolbarElement{

	var $classname = "we_wysiwygToolbarButton";
	var $tooltiptext = "";
	var $imgSrc = "";

	function __construct($editor, $cmd, $imgSrc, $tooltiptext = "", $width = 25, $height = 22){
		$this->we_wysiwygToolbarElement($editor, $cmd, $width, $height);
		$this->tooltiptext = $tooltiptext;
		$this->imgSrc = $imgSrc;
	}

	function getHTML(){
		if(we_base_browserDetect::isSafari()){
			return '<div id="' . $this->editor->ref . 'edit_' . $this->cmd . 'Div" class="tbButton">
<img  width="' . ($this->width - 2) . '" height="' . $this->height . '" id="' . $this->editor->ref . 'edit_' . $this->cmd . '" src="' . $this->imgSrc . '" alt="' . $this->tooltiptext . '" title="' . $this->tooltiptext . '"
onmouseover="' . $this->editor->ref . 'Obj.over(\'' . $this->cmd . '\');"
onmouseout="' . $this->editor->ref . 'Obj.out(\'' . $this->cmd . '\');"
onmousedown="' . $this->editor->ref . 'Obj.click(event,\'' . $this->cmd . '\');" /></div>';
		} else{

			return '<div id="' . $this->editor->ref . 'edit_' . $this->cmd . 'Div" class="tbButton">
<img  width="' . ($this->width - 2) . '" height="' . $this->height . '" id="' . $this->editor->ref . 'edit_' . $this->cmd . '" src="' . $this->imgSrc . '" alt="' . $this->tooltiptext . '" title="' . $this->tooltiptext . '"
onmouseover="' . $this->editor->ref . 'Obj.over(\'' . $this->cmd . '\');"
onmouseout="' . $this->editor->ref . 'Obj.out(\'' . $this->cmd . '\');"
onmousedown="' . $this->editor->ref . 'Obj.check(\'' . $this->cmd . '\');"
onmouseup="' . $this->editor->ref . 'Obj.uncheck(\'' . $this->cmd . '\');"
onclick="' . $this->editor->ref . 'Obj.click(\'' . $this->cmd . '\');" /></div>';
		}
	}

	function hasProp(){
		switch($this->cmd){
			case "inserttable":
			case "edittable":
			case "editcell":
			case "insertcolumnright":
			case "insertcolumnleft":
			case "insertrowabove":
			case "insertrowbelow":
			case "deleterow":
			case "deletecol":
			case "increasecolspan":
			case "decreasecolspan":
			case "caption":
			case "removecaption":
				return stripos($this->editor->propstring, ",table,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "cut":
			case "copy":
			case "paste":
				return stripos($this->editor->propstring, ",copypaste,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "forecolor":
			case "backcolor":
				return stripos($this->editor->propstring, ",color,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "createlink":
			case "unlink":
				return stripos($this->editor->propstring, ",link,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "insertunorderedlist":
			case "insertorderedlist":
			case "indent":
			case "outdent":
				return stripos($this->editor->propstring, ",list,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "justifyleft":
			case "justifycenter":
			case "justifyright":
			case "justifyfull":
				return stripos($this->editor->propstring, ",justify,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "bold":
			case "italic":
			case "underline":
			case "subscript":
			case "superscript":
			case "strikethrough":
			case "removetags":
			case "removeformat":
				return stripos($this->editor->propstring, ",prop,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			default:
				return stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
		}
	}

}

class we_wysiwygToolbarSelect extends we_wysiwygToolbarElement{

	var $classname = "we_wysiwygToolbarSelect";
	var $title = "";
	var $vals = array();

	function __construct($editor, $cmd, $title, $vals, $width = 0, $height = 20){
		$this->we_wysiwygToolbarElement($editor, $cmd, $width, $height);
		$this->title = $title;
		$this->vals = $vals;
	}

	function hasProp(){
		switch($this->cmd){
			case "fontname":
			case "fontsize":
			case "formatblock":
				return stripos($this->editor->propstring, ",font,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			default:
				return stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
		}
	}

	function getHTML(){
		if(we_base_browserDetect::isSafari()){
			$out = '<select id="' . $this->editor->ref . '_sel_' . $this->cmd . '" style="width:' . $this->width . 'px;margin-right:3px;" size="1" onmousedown="' . $this->editor->ref . 'Obj.saveSelection();" onmouseup="' . $this->editor->ref . 'Obj.restoreSelection();" onchange="' . $this->editor->ref . 'Obj.restoreSelection();' . $this->editor->ref . 'Obj.selectChanged(\'' . $this->cmd . '\',this.value);this.selectedIndex=0">';
			$out .= '<option value="">' . htmlspecialchars($this->title) . '</option>' . "\n";
			foreach($this->vals as $val => $txt){
				$out .= '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($txt) . '</option>' . "\n";
			}
			$out .= '</select>';
		} else{
			$out = '<table id="' . $this->editor->ref . '_sel_' . $this->cmd . '"  onclick="if(' . $this->editor->ref . 'Obj.menus[\'' . $this->cmd . '\'].disabled==false){' . $this->editor->ref . 'Obj.showPopupmenu(\'' . $this->cmd . '\');}" class="tbButtonWysiwygDefaultStyle" width="' . $this->width . '" height="' . $this->height . '" cellpadding="0" cellspacing="0" border="0" title="' . ($this->title) . '" style="cursor:pointer;position: relative;">
	<tr>
		<td width="' . ($this->width - 20) . '" style="padding-left:10px;background-image: url(' . IMAGE_DIR . 'wysiwyg/menuback.gif);"  class="tbButtonWysiwygDefaultStyle"><input value="' . htmlspecialchars($this->title) . '" type="text" name="' . $this->editor->ref . '_seli_' . $this->cmd . '" id="' . $this->editor->ref . '_seli_' . $this->cmd . '" readonly="readonly" style="cursor:pointer;height:16px;width:' . ($this->width - 30) . 'px;border:0px;background-color:transparent;color:black;font: 10px Verdana, Arial, Helvetica, sans-serif;" /></td>
		<td width="20" class="tbButtonWysiwygDefaultStyle"><img src="' . IMAGE_DIR . 'wysiwyg/menudown.gif" width="20" height="20" alt="" /></td>
	</tr>
</table><iframe src="' . HTML_DIR . 'white.html" width="280" height="160" id="' . $this->editor->ref . 'edit_' . $this->cmd . '" style=" z-index: 100000;position: absolute; display:none;"></iframe>';

			$js = 'wePopupMenuArray[wefoo]["' . $this->cmd . '"] = new Array();';
			foreach($this->vals as $val => $txt){
				$js .= 'wePopupMenuArray[wefoo]["' . $this->cmd . '"]["' . $val . '"]="' . $txt . '";	';
			}
			$out .= we_html_element::jsElement($js);
		}
		return $out;
	}

}
