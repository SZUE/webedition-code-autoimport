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
class we_export_wizard{
	private $frameset;
	private $db;
	private $Tree;
	private $topFrame = 'top';
	private $headerFrame = 'top.header';
	private $loadFrame = 'top.load';
	private $bodyFrame = 'top.body';
	private $footerFrame = 'top.footer';
	private $exportVars = array(
		'extype' => '',
		'selection' => 'auto',
		'type' => 'doctype',
		'doctype' => '',
		'classname' => '',
		'dir' => 0,
		'art' => 'docs',
		'categories' => '',
		'selDocs' => '',
		'selTempl' => '',
		'selObjs' => '',
		'selClasses' => '',
		'finalDocs' => array(),
		'finalObjs' => array(),
		'file_name' => '',
		'export_to' => 'local',
		'path' => '',
		'filename' => '',
		'csv_delimiter' => ';',
		'csv_enclose' => '"',
		'csv_lineend' => 'windows',
		'csv_fieldnames' => '',
		'csv_fields' => 0,
		'cdata' => 'true',
		'RefTable' => array(),
		'CurrentRef' => 0,
		'step' => 0,
		'handle_def_templates' => 0,
		'handle_document_includes' => 0,
		'handle_document_linked' => 0,
		'handle_def_classes' => 0,
		'handle_object_includes' => 0,
		'handle_object_linked' => 0,
		'handle_object_embeds' => 0,
		'handle_class_defs' => 0,
		'handle_doctypes' => 0,
		'handle_categorys' => 0,
		'export_depth' => 1
	);
	private $exportVarTypes = array(
		'extype' => we_base_request::STRING,
		'selection' => we_base_request::STRING,
		'type' => we_base_request::STRING,
		'doctype' => we_base_request::INT,
		'classname' => we_base_request::INT,
		'dir' => we_base_request::INT,
		'art' => we_base_request::STRING,
		'categories' => we_base_request::STRING,
		'selDocs' => we_base_request::INTLIST,
		'selTempl' => we_base_request::INTLIST,
		'selObjs' => we_base_request::INTLIST,
		'selClasses' => we_base_request::INTLIST,
		'finalDocs' => we_base_request::RAW,
		'finalObjs' => we_base_request::RAW,
		'file_name' => we_base_request::FILE,
		'export_to' => we_base_request::STRING,
		'path' => we_base_request::FILE,
		'filename' => we_base_request::FILE,
		'csv_delimiter' => we_base_request::RAW_CHECKED,
		'csv_enclose' => we_base_request::RAW_CHECKED,
		'csv_lineend' => we_base_request::STRING,
		'csv_fieldnames' => we_base_request::RAW,
		'csv_fields' => we_base_request::RAW,
		'cdata' => we_base_request::STRING,
		'RefTable' => we_base_request::RAW,
		'CurrentRef' => we_base_request::INT,
		'step' => we_base_request::INT,
		'handle_def_templates' => we_base_request::BOOL,
		'handle_document_includes' => we_base_request::BOOL,
		'handle_document_linked' => we_base_request::BOOL,
		'handle_def_classes' => we_base_request::BOOL,
		'handle_object_includes' => we_base_request::BOOL,
		'handle_object_linked' => we_base_request::BOOL,
		'handle_object_embeds' => we_base_request::BOOL,
		'handle_class_defs' => we_base_request::BOOL,
		'handle_doctypes' => we_base_request::BOOL,
		'handle_categorys' => we_base_request::BOOL,
		'export_depth' => we_base_request::INT
	);

	public function __construct($frameset = ""){
		$this->frameset = $frameset;
		$this->db = new DB_WE();

		if(isset($_SESSION['weS']['exportVars_session'])){
			foreach($this->exportVars as $k => $v){
				if(isset($_SESSION['weS']['exportVars_session'][$k])){
					$this->exportVars[$k] = $_SESSION['weS']['exportVars_session'][$k];
				} else {
					$_SESSION['weS']['exportVars_session'][$k] = $v;
				}
			}
		} else {
			$_SESSION['weS']['exportVars_session'] = $this->exportVars;
		}
	}

	public function getHTML($what, $step){
		switch($what){

			case "frameset" :
				return $this->getHTMLFrameset();
			case "header" :
				return $this->getHTMLHeader($step);
			case "body" :
				return $this->getHTMLStep($step);
			case "footer" :
				return $this->getHTMLFooter($step);
			case "load" :
				return $this->getHTMLCmd();
			default :
				die("Unknown command: " . $what . "\n");
		}
	}

	private function getJSTop(){
		return we_html_element::jsElement('
 			var table="' . FILE_TABLE . '";
 		');
	}

	private function getExportVars(){
		if(isset($_SESSION['weS']['exportVars_session'])){
			$this->exportVars = $_SESSION['weS']['exportVars_session'];
		}
		foreach($this->exportVarTypes as $key => $type){
			$var = we_base_request::_($type, $key, null);
			if($var !== null){
				$this->exportVars[$key] = $var;
			}
		}
		$_SESSION['weS']['exportVars_session'] = $this->exportVars;
	}

	private function getHTMLFrameset(){
		$args = "";
		$_SESSION['weS']['exportVars_session'] = array();
		if(($cmd1 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1))){
			$args .= "&we_cmd[1]=" . $cmd1;
		}
		$this->Tree = new we_export_tree(WE_EXPORT_MODULE_DIR . "export_frameset.php", $this->topFrame, $this->bodyFrame, $this->loadFrame);

		$js = $this->getJSTop() .
			$this->Tree->getJSTreeCode() .
			we_html_element::jsElement('
var step = 0;

var activetab=0;
var selection="auto";

var extype="' . we_import_functions::TYPE_WE_XML . '";
var type="doctype";
var categories="";
var doctype="";
var classname="";
var dir="";

var file_format="' . we_import_functions::TYPE_GENERIC_XML . '";
var filename="";
var export_to="server";
var path="/";

var SelectedItems= {
"' . FILE_TABLE . '":[],
"' . TEMPLATES_TABLE . '":[],' .
				(defined('OBJECT_FILES_TABLE') ? (
					'"' . OBJECT_FILES_TABLE . '":[],
	"' . OBJECT_TABLE . '":[],
	') : '') . '
};

var openFolders= {
	"' . FILE_TABLE . '":"",
	"' . TEMPLATES_TABLE . '":"",' .
				(defined('OBJECT_FILES_TABLE') ? ('
	"' . OBJECT_FILES_TABLE . '":"",
	"' . OBJECT_TABLE . '":"",
') : '') .
				'};'
		);

		$body = we_html_element::htmlBody(array('id' => 'weMainBody', "onload" => $this->bodyFrame . ".location='" . $this->frameset . "&pnt=body" . $args . "&step=' + step;")
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlIFrame('header', $this->frameset . "&pnt=header", 'position:absolute;top:0px;height:1px;left:0px;right:0px;overflow: hidden', '', '', false) .
					we_html_element::htmlIFrame('body', $this->frameset . "&pnt=body", 'position:absolute;top:1px;bottom:45px;left:0px;right:0px;', 'border:0px;width:100%;height:100%;') .
					we_html_element::htmlIFrame('footer', $this->frameset . "&pnt=footer", 'position:absolute;height:45px;bottom:0px;left:0px;right:0px;overflow: hidden', '', '', false) .
					we_html_element::htmlIFrame('load', $this->frameset . "&pnt=load", 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
		));

		return we_html_tools::getHtmlTop(g_l('export', '[title]'), '', '', STYLESHEET . $js, $body
		);
	}

	private function getHTMLStep($step = 0){
		$this->getExportVars();
		$function = "getHTMLStep" . intval($step);
		return (method_exists($this, $function) ?
				$this->$function() :
				$this->getHTMLStep0());
	}

	private function getHTMLStep0(){
		$wexpotEnabled = (permissionhandler::hasPerm('NEW_EXPORT') || permissionhandler::hasPerm('DELETE_EXPORT') || permissionhandler::hasPerm('EDIT_EXPORT') || permissionhandler::hasPerm('MAKE_EXPORT'));

		$extype = $this->exportVars["extype"];

		if(!$extype){
			$extype = we_import_functions::TYPE_WE_XML;
			if(!$wexpotEnabled){
				$extype = we_import_functions::TYPE_GENERIC_XML;
				if(!permissionhandler::hasPerm("GENERICXML_EXPORT")){
					$extype = "csv";
					if(!permissionhandler::hasPerm("CSV_EXPORT")){
						$extype = "";
					}
				}
			}
		}


		$js = we_html_element::jsElement(
				$this->footerFrame . '.location="' . $this->frameset . '&pnt=footer&step=0";
					' . $this->headerFrame . '.location="' . $this->frameset . '&pnt=header&step=0";
					self.focus();');

		$parts = array(
			/* 		array(
			  "headline"	=> g_l('export',"[we_export]"),
			  "html"		=> we_html_forms::radiobutton(we_import_functions::TYPE_WE_XML,($extype=="wxml" && permissionhandler::hasPerm("WXML_EXPORT")), "extype", g_l('export',"[wxml_export]"),true, "defaultfont", "",  !permissionhandler::hasPerm("WXML_EXPORT"), g_l('export',"[txt_wxml_export]"), 0, 384),
			  "space"		=> 120,
			  "noline"	=> 1)
			 */

			array(
				"html" => we_html_forms::radiobutton(we_import_functions::TYPE_WE_XML, ($extype == we_import_functions::TYPE_WE_XML && $wexpotEnabled), "extype", g_l('export', '[wxml_export]'), true, "defaultfont", "", !$wexpotEnabled, g_l('export', '[txt_wxml_export]'), 0, 500),
				"space" => 0,
				"noline" => 1
			),
			array(
				"html" => we_html_forms::radiobutton(we_import_functions::TYPE_GENERIC_XML, ($extype == we_import_functions::TYPE_GENERIC_XML && permissionhandler::hasPerm("GENERICXML_EXPORT")), "extype", g_l('export', '[gxml_export]'), true, "defaultfont", "", !permissionhandler::hasPerm("GENERICXML_EXPORT"), g_l('export', '[txt_gxml_export]'), 0, 500),
				"space" => 0,
				"noline" => 1)
		);

		if(we_base_moduleInfo::isActive("object")){
			$parts[] = array(
				"html" => we_html_forms::radiobutton("csv", ($extype === "csv" && permissionhandler::hasPerm("CSV_EXPORT")), "extype", g_l('export', '[csv_export]'), true, "defaultfont", "", !permissionhandler::hasPerm("CSV_EXPORT"), g_l('export', '[txt_csv_export]'), 0, 500),
				"space" => 0,
				"noline" => 1
			);
		}

		return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHiddens(array(
							"pnt" => "body",
							"step" => 1)) .
						we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('export', '[title]'))
					)
				)
		);
	}

	private function getHTMLStep1(){
		$extype = $this->exportVars["extype"];

		if($extype == we_import_functions::TYPE_WE_XML){
			return we_html_element::jsElement('
top.opener.top.we_cmd("export_edit_ifthere");
top.close();');
		}


		$js = we_html_element::jsElement(
				$this->footerFrame . '.location="' . $this->frameset . '&pnt=footer&step=1";
' . $this->headerFrame . '.location="' . $this->frameset . '&pnt=header&step=1";
self.focus();

function we_submit(){
	' . ($this->exportVars["extype"] === "csv" ? '
	if(document.we_form.selection[1].checked){
		document.we_form.step.value=3;
	}
	' : '') . '
	document.we_form.submit();
}');

		$selection = $this->exportVars["selection"];

		$parts = array(
			array(
				"html" => we_html_forms::radiobutton("auto", ($selection === "auto" ? true : false), "selection", g_l('export', '[auto_selection]'), true, "defaultfont", "", false, g_l('export', (($this->exportVars['extype'] === 'csv') ? '[txt_auto_selection_csv]' : '[txt_auto_selection]')), 0, 500),
				"space" => 0,
				"noline" => 1),
			array(
				"html" => we_html_forms::radiobutton("manual", ($selection === "manual" ? true : false), "selection", g_l('export', '[manual_selection]'), true, "defaultfont", "", false, g_l('export', (($this->exportVars['extype'] === 'csv') ? '[txt_manual_selection_csv]' : '[txt_manual_selection]')), 0, 500),
				"space" => 0,
				"noline" => 1)
		);

		return we_html_tools::getHtmlTop(g_l('export', '[wizard_title]'), '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHiddens(array(
							'pnt' => "body",
							'step' => 2,
							'art' => ($this->exportVars["extype"] === 'csv' ? 'objects' : 'docs'))) .
						we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('export', '[step1]'))
					)
				)
		);
	}

	private function getHTMLStep2(){
		switch($this->exportVars["selection"]){
			case "auto":
				return $this->getHTMLStep2a();
			case "manual":
				/* if($this->exportVars["extype"]==we_import_functions::TYPE_WE_XML) return $this->getHTMLStep3();
				  else */
				return ($this->exportVars["extype"] === "csv" ?
						$this->getHTMLStep1() :
						$this->getHTMLStep2b());
		}
	}

	private function getHTMLStep2a(){
		$yuiSuggest = & weSuggest::getInstance();

		$_space = 10;

		$js = we_html_element::jsElement('
function we_cmd(){
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]){
		case "we_selector_category":
			new (WE().util.jsWindow)(this, url,"we_catselector",-1,-1,' . we_selector_file::WINDOW_CATSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_CATSELECTOR_HEIGHT . ',true,true,true,true);
		break;
		case "add_cat":
		case "del_cat":
		case "del_all_cats":
			document.we_form.wcmd.value=args[0];
			document.we_form.cat.value=args[1];
			document.we_form.step.value=2;
			document.we_form.submit();
		break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(this, url,"we_selector",-1,-1,' . we_selector_file::WINDOW_SELECTOR_WIDTH . ',' . we_selector_file::WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
		break;
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}');
		$js.=we_html_element::jsElement(
				$this->footerFrame . '.location="' . $this->frameset . '&pnt=footer&step=2";');

		$parts = array();
		$showdocs = false;
		if(!isset($this->exportVars["extype"]) || (isset($this->exportVars["extype"]) && $this->exportVars["extype"] != "csv")){
			$doc_type = $this->getHTMLDocType();
			$showdocs = true;
			$_tmp = array("headline" => "", "html" => $doc_type, "space" => $_space);
			if(defined('OBJECT_FILES_TABLE')){
				$_tmp["noline"] = 1;
			}
			$parts[] = $_tmp;
		}

		if(!$showdocs){
			$js.= we_html_element::jsElement($this->topFrame . ".type='classname';");
		}
		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "body",
				"type" => ($showdocs ? "doctype" : "classname"),
				"step" => 4));
		if(defined('OBJECT_FILES_TABLE')){
			$classname = $this->getHTMLObjectType(350, $showdocs);

			$parts[] = array("headline" => "", "html" => $classname, "space" => $_space);
		}

		$category = $this->getHTMLCategory();
		$parts[] = array("headline" => "", "html" => $category, "space" => $_space, "noline" => 1);


		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET . $js . weSuggest::getYuiFiles(), we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						we_html_multiIconBox::getHTML("weExportWizard", $parts, 30, "", -1, "", "", false, g_l('export', '[step2]'))
					) . $yuiSuggest->getYuiJs()
				)
		);
	}

	private function getHTMLStep2b(){
		$_space = 10;
		$art = $this->exportVars["art"];
		$js = we_html_element::jsElement(
				$this->headerFrame . '.location="' . $this->frameset . '&pnt=header&step=2";' .
				$this->footerFrame . '.location="' . $this->frameset . '&pnt=footer&step=2";');

		$parts = array(
			array("headline" => "", "html" => we_html_forms::radiobutton("docs", ($art === "docs" ? true : ($art != 'objects')), "art", g_l('export', '[documents]'), true, "defaultfont", $this->topFrame . ".art='docs'"), "space" => $_space, "noline" => 1)
		);
		if(defined('OBJECT_FILES_TABLE')){
			$parts[] = array("headline" => "", "html" => we_html_forms::radiobutton("objects", ($art === 'objects' ? true : ($art != 'docs')), "art", g_l('export', '[objects]'), true, "defaultfont", $this->topFrame . ".art='objects'"), "space" => $_space, "noline" => 1);
		}

		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "body",
				"selection" => "manual",
				"step" => 2));

		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
//we_html_element::htmlInput(array("type" => "text","name" => "selectedItems")).
						we_html_multiIconBox::getHTML("weExportWizard", $parts, 30, "", -1, "", "", false, g_l('export', '[step2]'))
					)
				)
		);
	}

	private function getHTMLStep3(){
		$art = $this->exportVars["art"];

		$js = ($art === 'objects' && defined('OBJECT_FILES_TABLE') ?
				we_html_element::jsElement($this->topFrame . '.table="' . OBJECT_FILES_TABLE . '";') :
				($art == 'docs' ?
					we_html_element::jsElement($this->topFrame . '.table="' . FILE_TABLE . '";') :
					'')
			);


		$js.=we_html_element::jsElement(
				$this->footerFrame . '.location="' . $this->frameset . '&pnt=footer&step=3";
	setTimeout(' . $this->topFrame . '.startTree,100);

function populate(id,table){
	//if(table=="' . FILE_TABLE . '") document.we_form.selDocs.value+=","+id;
//' . (defined('OBJECT_FILES_TABLE') ? 'else if(table=="' . OBJECT_FILES_TABLE . '") document.we_form.selObjs.value+=","+id;' : "") . '
}

function setHead(tab){
	var c0="#DDDDDD";
	var c1="#DDDDDD";
	var c2="#DDDDDD";
	var c3="#DDDDDD";
	eval("c"+tab+"=\"#DFE9F5\"");
	var fw0="normal";
	var fw1="normal";
	var fw2="normal";
	var fw3="normal";
	eval("fw"+tab+"=\"bold\"");


	switch (tab){
		case 0:
			' . $this->topFrame . '.table="' . FILE_TABLE . '";
		break;
		case 1:
			' . $this->topFrame . '.table="' . TEMPLATES_TABLE . '";
		break;
		' . (defined('OBJECT_FILES_TABLE') ? '
		case 2:
			' . $this->topFrame . '.table="' . OBJECT_FILES_TABLE . '";
		break;
		' : '') .
				(defined('OBJECT_TABLE') ? '
		case 3:
			' . $this->topFrame . '.table="' . OBJECT_TABLE . '";
		break;
		' : '') . '
	}

	setTimeout(' . $this->topFrame . '.startTree,100);
	document.getElementById("' . FILE_TABLE . '").style.backgroundColor=c0;
	document.getElementById("' . TEMPLATES_TABLE . '").style.backgroundColor=c1;' .
				(defined('OBJECT_FILES_TABLE') ? 'document.getElementById("' . OBJECT_FILES_TABLE . '").style.backgroundColor=c2;' : '' ) .
				(defined('OBJECT_TABLE') ? 'document.getElementById("' . OBJECT_TABLE . '").style.backgroundColor=c3;' : '') . '

	document.getElementById("' . FILE_TABLE . '").style.fontWeight=fw0;
	document.getElementById("' . TEMPLATES_TABLE . '").style.fontWeight=fw1;' .
				(defined('OBJECT_FILES_TABLE') ? 'document.getElementById("' . OBJECT_FILES_TABLE . '").style.fontWeight=fw2;' : '' ) .
				(defined('OBJECT_TABLE') ? 'document.getElementById("' . OBJECT_TABLE . '").style.fontWeight=fw3;' : '') . '
}

function we_submit(){
	document.we_form.selDocs.value=' . $this->topFrame . '.SelectedItems["' . FILE_TABLE . '"].join(",");
	document.we_form.selTempl.value=' . $this->topFrame . '.SelectedItems["' . TEMPLATES_TABLE . '"].join(",");' .
				(defined('OBJECT_FILES_TABLE') ? 'document.we_form.selObjs.value=' . $this->topFrame . '.SelectedItems["' . OBJECT_FILES_TABLE . '"].join(",");' : '') .
				(defined('OBJECT_TABLE') ? 'document.we_form.selClasses.value=' . $this->topFrame . '.SelectedItems["' . OBJECT_TABLE . '"].join(",");' : '') . '
	document.we_form.submit();
}');

		$header = new we_html_table(array('class' => 'default'), 2, 9);
		$parts = array(
			array(
				"headline" => "",
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[select_export]'), we_html_tools::TYPE_INFO, 540),
				"space" => 0,
				"noline" => 1
			),
			array(
				"headline" => "",
				"html" => $header->getHtml() . we_html_element::htmlDiv(array("id" => "treetable", "class" => "blockWrapper", "style" => "width: 540px; height: 250px; border:1px #dce6f2 solid;"), ""),
				"space" => 0
			)
		);

		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET .
				we_html_element::cssLink(CSS_DIR . 'tree.css') .
				$js, we_html_element::htmlBody(array(
					"class" => "weDialogBody"
					), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHiddens(array(
							"pnt" => "body",
							"step" => 4,
							"selDocs" => '',
							"selTempl" => '',
							"selObjs" => (isset($_SESSION['weS']['exportVars_session']["selObjs"]) ? $_SESSION['weS']['exportVars_session']["selObjs"] : ""),
							"selClasses" => (isset($_SESSION['weS']['exportVars_session']["selClasses"]) ? $_SESSION['weS']['exportVars_session']["selClasses"] : ""))) .
						we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('export', '[title]'))
					)
				)
		);
	}

	private function getHTMLStep4(){
//	define different parts of the export wizard
		$_space = 100;

		$extype = $this->exportVars["extype"];
		$filename = $this->exportVars["filename"];

		$handle_def_templates = $this->exportVars["handle_def_templates"];
		$handle_document_includes = $this->exportVars["handle_document_includes"];
		$handle_document_linked = $this->exportVars["handle_document_linked"];

		$handle_def_classes = $this->exportVars["handle_def_classes"];
		$handle_object_includes = $this->exportVars["handle_object_includes"];
		//$handle_object_linked = $this->exportVars["handle_object_linked"];

		$handle_doctypes = $this->exportVars["handle_doctypes"];
		$handle_categorys = $this->exportVars["handle_categorys"];


		$handle_object_embeds = $this->exportVars["handle_object_embeds"];
		//$handle_class_defs = $this->exportVars["handle_class_defs"];


		$export_depth = $this->exportVars["export_depth"];

		$filename = $filename ? : "weExport_" . time() . ($extype == we_import_functions::TYPE_GENERIC_XML ? ".xml" : ".csv");


//set variables in top frame
		$js = we_html_element::jsElement('
function setLabelState(l,disable){
	if(disable){
		document.getElementById(l).style.color = "grey";
	}else{
		document.getElementById(l).style.color = "black";
	}
}

function setState(a) {
		_new_state = (document.getElementsByName(a)[0].checked == true?false:true);

		if(a=="_handle_templates"){
			if(_new_state==true){
				document.getElementsByName("handle_document_linked")[0].value = 0;
				document.getElementsByName("handle_object_linked")[0].value = 0;

				document.getElementsByName("_handle_document_linked")[0].checked = false;
				document.getElementsByName("_handle_object_linked")[0].checked = false;
			}

			document.getElementsByName("_handle_document_linked")[0].disabled = _new_state;
			setLabelState("label__handle_document_linked",_new_state);

			document.getElementsByName("_handle_object_linked")[0].disabled = _new_state;
			setLabelState("label__handle_object_linked",_new_state);
		}
		if(a=="_handle_classesfff"){
			if(_new_state==true){
				document.getElementsByName("handle_object_includes")[0].value = 0;
				document.getElementsByName("_handle_object_includes")[0].checked = false;
			}
			document.getElementsByName("_handle_object_includes")[0].disabled = _new_state;
			setLabelState("label__handle_object_includes",_new_state);

			document.getElementsByName("link_object_depth")[0].disabled = _new_state;
			setLabelState("label_link_object_depth",_new_state);
		}
}
' . $this->headerFrame . '.location="' . $this->frameset . '&pnt=header&step=4";
' . $this->footerFrame . '.location="' . $this->frameset . '&pnt=footer&step=4";');



		$formattable = new we_html_table(array(), 4, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden($handle_def_templates, "handle_def_templates", g_l('export', '[handle_def_templates]')));
		$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($handle_document_includes ? true : false), "handle_document_includes", g_l('export', '[handle_document_includes]')));
		$formattable->setCol(2, 0, null, we_html_forms::checkboxWithHidden(($handle_object_includes ? true : false), "handle_object_includes", g_l('export', '[handle_object_includes]')));
		$formattable->setCol(3, 0, null, we_html_forms::checkboxWithHidden(($handle_document_linked ? true : false), "handle_document_linked", g_l('export', '[handle_document_linked]')));

		$formattable2 = new we_html_table(array(), 3, 1);
		$formattable2->setCol(0, 0, array("colspan" => 2), we_html_forms::checkboxWithHidden(($handle_def_classes ? true : false), "handle_def_classes", g_l('export', '[handle_def_classes]')));
		$formattable2->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($handle_object_embeds ? true : false), "handle_object_embeds", g_l('export', '[handle_object_embeds]')));
//$formattable2->setCol(2,0,null,we_html_forms::checkboxWithHidden(($handle_class_defs ? true : false),"handle_class_defs",g_l('export',"[handle_class_defs]")));

		$formattable3 = new we_html_table(array(), 2, 1);
		$formattable3->setCol(0, 0, null, we_html_forms::checkboxWithHidden(($handle_doctypes ? true : false), "handle_doctypes", g_l('export', '[handle_doctypes]')));
		$formattable3->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($handle_categorys ? true : false), "handle_categorys", g_l('export', '[handle_categorys]')));

		$parts = array(
			array("headline" => g_l('export', '[handle_document_options]') . we_html_element::htmlBr() . g_l('export', '[handle_template_options]'), "html" => $formattable->getHtml(), "space" => $_space),
			array("headline" => g_l('export', '[handle_object_options]') . we_html_element::htmlBr() . g_l('export', '[handle_classes_options]'), "html" => $formattable2->getHtml(), "space" => $_space),
			array("headline" => g_l('export', '[handle_doctype_options]'), "html" => $formattable3->getHtml(), "space" => $_space),
			array("headline" => g_l('export', '[export_depth]'), "html" => we_html_element::htmlLabel(array('style' => 'padding-right:5px;'), g_l('export', '[to_level]')) . we_html_tools::htmlTextInput("export_depth", 10, $export_depth, "", "", "text", 50), "space" => $_space)
		);

		return we_html_tools::getHtmlTop('', '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHiddens(array(
							"pnt" => "body",
							"step" => 7)) .
						we_html_multiIconBox::getHTML("weExportWizard", $parts, 30, "", -1, "", "", false, g_l('export', '[options]'))
					)
				)
		);
	}

	private function getHTMLStep7(){
//	define different parts of the export wizard
		$_space = 130;
		$_input_size = 42;

		$extype = $this->exportVars["extype"];
		$filename = $this->exportVars["filename"];
		$export_to = $this->exportVars["export_to"];
		$cdata = $this->exportVars["cdata"];
		$path = $this->exportVars["path"];
		$csv_delimiter = $this->exportVars["csv_delimiter"];
		$csv_enclose = $this->exportVars["csv_enclose"];
		$csv_lineend = $this->exportVars["csv_lineend"];

		if(!$filename){
			$filename = "weExport_" . date('d_m_Y_H_i') . ($extype == we_import_functions::TYPE_GENERIC_XML ? ".xml" : ".csv");
		}

//set variables in top frame
		$js = we_html_element::jsElement(
				$this->headerFrame . '.location="' . $this->frameset . '&pnt=header&step=7";' .
				$this->footerFrame . '.location="' . $this->frameset . '&pnt=footer&step=7";');

		$parts = array(
			array("headline" => g_l('export', '[filename]'), "html" => we_html_tools::htmlTextInput("filename", $_input_size, $filename, "", "", "text", 260), "space" => $_space)
		);

//	Filetype
		switch($extype){
			case "csv":
//$csv_input_size = 3;

				$fileformattable = new we_html_table(array(), 4, 1);

				$_file_encoding = new we_html_select(array("name" => "csv_lineend", "size" => 1, "class" => "weSelect", "style" => "width: 254px"));
				$_file_encoding->addOption("windows", g_l('export', '[windows]'));
				$_file_encoding->addOption("unix", g_l('export', '[unix]'));
				$_file_encoding->addOption("mac", g_l('export', '[mac]'));
				$_file_encoding->selectOption($csv_lineend);

				$fileformattable->setCol(0, 0, array("class" => "defaultfont"), g_l('export', '[csv_lineend]') . "<br/>" . $_file_encoding->getHtml());
				$fileformattable->setColContent(1, 0, $this->getHTMLChooser("csv_delimiter", $csv_delimiter, array(";" => g_l('export', '[semicolon]'), "," => g_l('export', '[comma]'), ":" => g_l('export', '[colon]'), "\\t" => g_l('export', '[tab]'), " " => g_l('export', '[space]')), g_l('export', '[csv_delimiter]')));
				$fileformattable->setColContent(2, 0, $this->getHTMLChooser("csv_enclose", $csv_enclose, array("\"" => g_l('export', '[double_quote]'), "'" => g_l('export', '[single_quote]')), g_l('export', '[csv_enclose]')));

				$fileformattable->setColContent(3, 0, we_html_forms::checkbox(1, true, "csv_fieldnames", g_l('export', '[csv_fieldnames]')));

				$parts[] = array("headline" => g_l('export', '[csv_params]'), "html" => $fileformattable->getHtml(), "space" => $_space);
				break;

			case we_import_functions::TYPE_GENERIC_XML:
				$table = new we_html_table(array('class' => 'default withSpace'), 2, 1);

				$table->setColContent(0, 0, we_html_forms::radiobutton("true", ($cdata === "true"), "cdata", g_l('export', '[export_xml_cdata]'), true, "defaultfont", $this->topFrame . ".cdata='true'"));
				$table->setColContent(1, 0, we_html_forms::radiobutton("false", ($cdata === "false"), "cdata", g_l('export', '[export_xml_entities]'), true, "defaultfont", $this->topFrame . ".cdata='false'"));

				$parts[] = array("headline" => g_l('export', '[cdata]'), "html" => $table->getHtml(), "space" => $_space);
				break;
		}

		$table = new we_html_table(array('class' => 'default'), 2, 1);

		$table->setColContent(0, 0, we_html_forms::radiobutton("local", ($export_to === "local" ? true : false), "export_to", g_l('export', '[export_to_local]'), true, "defaultfont", $this->topFrame . ".export_to='local'"));
		$table->setCol(1, 0, array('style' => 'padding-top:20px;'), we_html_tools::htmlFormElementTable($this->formFileChooser(260, "path", $path, "", "folder"), we_html_forms::radiobutton("server", ($export_to === "server" ? true : false), "export_to", g_l('export', '[export_to_server]'), true, "defaultfont", $this->topFrame . ".export_to='server'")));

		$parts[] = array("headline" => g_l('export', '[export_to]'), "html" => $table->getHtml(), "space" => $_space);

		return we_html_tools::getHtmlTop('', '', '', STYLESHEET . $js, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHiddens(array(
							"pnt" => "load",
							"cmd" => "export",
							"step" => 7)) .
						we_html_multiIconBox::getHTML("weExportWizard", $parts, 30, "", -1, "", "", false, g_l('export', '[step3]'))
					)
				)
		);
	}

	private function getHTMLStep10(){
		$filename = urldecode(we_base_request::_(we_base_request::FILE, "file_name"));

		$message = we_html_element::htmlSpan(array("class" => "defaultfont"), g_l('export', '[backup_finished]') . "<br/><br/>" .
				g_l('export', '[download_starting]') .
				we_html_element::htmlA(array("href" => $this->frameset . "&pnt=body&step=50&exportfile=" . $filename, 'download' => basename($filename)), g_l('export', '[download]')));

		unset($_SESSION['weS']['exportVars_session']);

		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET .
				we_html_element::htmlMeta(array("http-equiv" => "refresh", "content" => "2; url=" . $this->frameset . "&pnt=body&step=50&exportfile=" . $filename)), we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_tools::htmlDialogLayout($message, g_l('export', '[step10]'))
				)
		);
	}

	private function getHTMLStep50(){
		if(we_base_request::_(we_base_request::BOOL, "exportfile")){
			$_filename = basename(urldecode(we_base_request::_(we_base_request::RAW, "exportfile")));

			if(file_exists(TEMP_PATH . $_filename) // Does file exist?
				&& !preg_match('%p?html?%i', $_filename) && stripos($_filename, "inc") === false && !preg_match('%php3?%i', $_filename)){ // Security check
				session_write_close();
				$_size = filesize(TEMP_PATH . $_filename);

				header("Pragma: public");
				header("Expires: 0");
				header("Cache-control: private, max-age=0, must-revalidate");
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . trim(htmlentities($_filename)) . '"');
				header("Content-Description: " . trim(htmlentities($_filename)));
				header("Content-Length: " . $_size);

				readfile(TEMP_PATH . $_filename);

				exit;
			} else {
				header("Location: " . $this->frameset . "&pnt=body&step=99&error=download_failed");
				exit;
			}
		} else {
			header("Location: " . $this->frameset . "&pnt=body&step=99&error=download_failed");
			exit;
		}
	}

	private function getHTMLStep99(){
		$errortype = we_base_request::_(we_base_request::STRING, "error", "unknown");

		switch($errortype){
			case "no_object_module":
				$returned_message = array(g_l('export', '[error_object_module]'), false);
				break;

			case "nothing_selected_docs":
				$returned_message = array(g_l('export', '[error_nothing_selected_docs]'), false);
				break;

			case "nothing_selected_objs":
				$returned_message = array(g_l('export', '[error_nothing_selected_objs]'), false);
				break;

			case "download_failed":
				$returned_message = array(g_l('export', '[error_download_failed]'), true);
				break;

			case "unknown":
			default:
				$returned_message = array(g_l('export', '[error_unknown]'), true);
				break;
		}

		$message = we_html_element::htmlSpan(array("class" => "defaultfont"), ($returned_message[1] ? (g_l('export', '[error]') . "<br/><br/>") : "") . $returned_message[0]);

		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_tools::htmlDialogLayout($message, g_l('export', ($returned_message[1] ? '[step99]' : '[step99_notice]')))
				)
		);
	}

	private function getHTMLHeader($step = 0){
		$art = $this->exportVars["art"];
//$selection = $this->exportVars["selection"];

		$table = new we_html_table(array("width" => '100%', 'class' => 'default'), 3, 1);
//print $step;

		switch($step){
			case 3:
				//FIXME: is this ever called???
				/* $js = we_html_element::jsElement('
				  function addOpenFolder(id){
				  if (top.openFolders[top.table]=="") top.openFolders[top.table]+=id;
				  else top.openFolders[top.table]+=","+id;
				  }

				  function delOpenFolder(id){
				  var of=top.openFolders[top.table];
				  var arr=[];
				  var arr1=[];
				  arr=of.split(",");
				  for(i=0;i<arr.length;i++){
				  if (arr[i]!=id) arr1.push(arr[i]);
				  }
				  top.openFolders[top.table]=arr1.join(",");
				  }


				  function populateVars(){
				  ' . $this->bodyFrame . '.document.we_form.selDocs.value="' . (isset($_SESSION['weS']['exportVars_session']["selDocs"]) ? $_SESSION['weS']['exportVars_session']["selDocs"] : "") . '";
				  ' . $this->bodyFrame . '.document.we_form.selObjs.value="' . (isset($_SESSION['weS']['exportVars_session']["selObjs"]) ? $_SESSION['weS']['exportVars_session']["selObjs"] : "") . '";
				  }

				  function setTab(tab) {
				  ' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.treeData.table]=[];
				  for(i=1;i<' . $this->topFrame . '.treeData.len;i++) {
				  if (' . $this->topFrame . '.treeData[i].checked==1) {
				  ' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.treeData.table].push(' . $this->topFrame . '.treeData[i].id);
				  }
				  }

				  switch (tab) {
				  case 0:
				  top.table="' . FILE_TABLE . '";
				  break;' .
				  (defined('OBJECT_FILES_TABLE') ? ('
				  case 1:
				  top.table="' . OBJECT_FILES_TABLE . '";
				  break;') : '') . '
				  }

				  document.we_form.openFolders.value=' . $this->topFrame . '.openFolders[top.table];
				  document.we_form.tab.value=' . $this->topFrame . '.table;
				  ' . $this->topFrame . '.activetab=tab;
				  document.we_form.submit();

				  }

				  var js_path  = "' . JS_DIR . '";
				  var img_path = "' . IMAGE_DIR . "tabs/" . '";
				  var suffix   = "";
				  var layerPosYOffset = 22;') .

				  $js2 = we_html_element::jsElement('
				  var winWidth  = getWindowWidth(window);
				  var winHeight = getWindowHeight(window);

				  var we_tabs = [];
				  ' . ($art === "docs" ? ('we_tabs.push(new we_tab("' . g_l('export', '[documents]') . '",(' . $this->topFrame . '.table=="' . FILE_TABLE . '" ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . '),"self.setTab(0);"));') : '') . '
				  ' . ($art === "objects" && defined('OBJECT_FILES_TABLE') ? ('we_tabs.push(new we_tab("' . g_l('export', '[objects]') . '",(' . $this->topFrame . '.table=="' . OBJECT_FILES_TABLE . '" ? ' . we_tab::ACTIVE . ': ' . we_tab::NORMAL . '),"self.setTab(1);"));') : ''));


				  $table->setCol(0, 0, array("class" => "header_small"), we_html_element::htmlB(g_l('export', '[step2]')));
				  $table->setCol(2, 0, array("nowrap" => "nowrap"), we_html_element::jsElement('setTimeout(we_tabInit,500);')
				  ); */
				break;

			case 1:
			case 2:
			case 4:
				$js = $js2 = '';

				break;
			default:
				$js = $js2 = '';
		}

		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET . $js, we_html_element::htmlBody(array("id" => "eHeaderBody"), $js2 .
					$table->getHtml() .
					we_html_element::htmlForm(array("name" => "we_form", "target" => "load", "action" => $this->frameset), we_html_element::htmlHiddens(array(
							"pnt" => "load",
							"cmd" => "load",
							"tab" => "",
							"pid" => 0,
							"openFolders" => ""))
					)
				)
		);
	}

	private function getHTMLFooter($step = 0){
		$this->getExportVars();
		$errortype = we_base_request::_(we_base_request::STRING, "error", "no_error");
//$selection = we_base_request::_(we_base_request::RAW, "selection", "auto");
		$show_controls = false;
		switch($errortype){
			case "no_object_module":
			default:
				$show_controls = true;
				break;
		}
		switch($step){
			case 0:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true) .
						we_html_button::create_button(we_html_button::NEXT, "javascript:" . $this->bodyFrame . ".document.we_form.submit();"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				break;
			case 1:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:" . $this->bodyFrame . ".document.we_form.step.value=0;" . $this->bodyFrame . ".document.we_form.submit();") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:" . $this->bodyFrame . ".we_submit();"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				break;
			case 2:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:" . $this->bodyFrame . ".document.we_form.step.value=1;" . $this->bodyFrame . ".document.we_form.submit();") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:" . $this->bodyFrame . ".document.we_form.step.value=" . ($this->exportVars["selection"] == "auto" ? 7 : 3) . ";" . $this->bodyFrame . ".document.we_form.submit();"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				break;
			case 3:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:" . $this->bodyFrame . ".document.we_form.step.value=2;" . $this->bodyFrame . ".we_submit();") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:" . $this->bodyFrame . ".document.we_form.step.value=7;" . $this->bodyFrame . ".we_submit();"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				break;
			case 4:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:" . $this->bodyFrame . ".document.we_form.target='body';" . $this->bodyFrame . ".document.we_form.pnt.value='body';" . $this->bodyFrame . ".document.we_form.step.value=" . ($this->exportVars["selection"] == "auto" ? 2 : 3) . ";" . $this->bodyFrame . ".document.we_form.submit();") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:" . $this->bodyFrame . ".document.we_form.submit();"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				break;
			case 7:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:" . $this->bodyFrame . ".document.we_form.target='body';" . $this->bodyFrame . ".document.we_form.pnt.value='body';" . $this->bodyFrame . ".document.we_form.step.value=" . ($this->exportVars["selection"] == "auto" ? 2 : 3) . ";" . $this->bodyFrame . ".document.we_form.submit();") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:" . $this->bodyFrame . ".document.we_form.target='load';;" . $this->bodyFrame . ".document.we_form.pnt.value='load';" . $this->bodyFrame . ".document.we_form.submit();"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
				break;
			case 10:
			case 99:
				if($step == 10 || ($step == 99 && !$show_controls)){
					$buttons = we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();");
				} else if($step == 99 && $show_controls){
					$buttons = we_html_button::position_yes_no_cancel(
							we_html_button::create_button(we_html_button::BACK, "javascript:" . $this->bodyFrame . ".location='" . $this->frameset . "&pnt=body&step=0';" . $this->footerFrame . ".location='" . $this->frameset . "&pnt=footer&step=0';") .
							we_html_button::create_button(we_html_button::NEXT, "", false, 100, 22, "", "", true), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
					);
				}
				break;
			default:
				$buttons = we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::BACK, "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "&pnt=load&cmd=back&step=" . $step . "';") .
						we_html_button::create_button(we_html_button::NEXT, "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "&pnt=load&cmd=next&step=" . $step . "';"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();")
				);
		}

		if(we_base_request::_(we_base_request::STRING, "mode") === "progress"){
			$text = we_base_request::_(we_base_request::STRING, "current_description", g_l('backup', '[working]'));
			$progress = we_base_request::_(we_base_request::INT, 'percent', 0);

			$progressbar = new we_progressBar($progress);
			$progressbar->setStudLen(200);
			$progressbar->addText($text, 0, "current_description");
		}

		$content = new we_html_table(array('class' => 'default', "width" => "100%"), 1, 2);
		$content->setCol(0, 0, null, (isset($progressbar) ? $progressbar->getHtml() : ""));
		$content->setCol(0, 1, array("style" => "text-align:right"), $buttons);

		return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET . (isset($progressbar) ? $progressbar->getJSCode() : ""), we_html_element::htmlBody(array("class" => "weDialogButtonsBody", 'style' => 'overflow:hidden;'), we_html_element::htmlForm(array(
						"name" => "we_form",
						"method" => "post",
						"target" => "load",
						"action" => $this->frameset
						), $content->getHtml()
					)
				)
		);
	}

	private function getHTMLCmd(){
		$out = "";
		$this->getExportVars();
		switch(we_base_request::_(we_base_request::STRING, "cmd")){
			case "load":
				if(($pid = we_base_request::_(we_base_request::INT, "pid")) !== false){
					return we_html_element::jsElement("self.location='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=loadTree&we_cmd[1]=" . we_base_request::_(we_base_request::TABLE, "tab") . "&we_cmd[2]=" . $pid . "&we_cmd[3]=" . we_base_request::_(we_base_request::INTLIST, "openFolders", "") . "'");
				}
				break;
			case "export":
				$xmlExIm = new we_exim_XMLExIm();

				$file_format = we_base_request::_(we_base_request::STRING, "file_format", "");
//$export_to = we_base_request::_(we_base_request::RAW, "export_to", "");
				$path = we_base_request::_(we_base_request::FILE, "path", '') . '/';
//$csv_delimiter = we_base_request::_(we_base_request::RAW, "csv_delimiter", "");
//$csv_enclose = we_base_request::_(we_base_request::RAW, "csv_enclose", "");
//$csv_lineend = we_base_request::_(we_base_request::RAW, "csv_lineend", "");
//$csv_fieldnames = we_base_request::_(we_base_request::RAW, "csv_fieldnames", "");
//$cdata = we_base_request::_(we_base_request::BOOL, "cdata");

				$extype = $this->exportVars["extype"];
				$filename = $this->exportVars["filename"];
//$export_to = $this->exportVars["export_to"];
				$path = $this->exportVars["path"];
				$csv_delimiter = $this->exportVars["csv_delimiter"];
				$csv_enclose = $this->exportVars["csv_enclose"];
				$csv_lineend = $this->exportVars["csv_lineend"];
				$csv_fieldnames = $this->exportVars["csv_fieldnames"];
				$cdata = $this->exportVars["cdata"];

				$finalDocs = makeArrayFromCSV($this->exportVars["selDocs"]);
				$finalTempl = makeArrayFromCSV($this->exportVars["selTempl"]);
				$finalObjs = makeArrayFromCSV($this->exportVars["selObjs"]);
				$finalClasses = makeArrayFromCSV($this->exportVars["selClasses"]);

				$xmlExIm->getSelectedItems($this->exportVars["selection"], $extype, $this->exportVars["art"], $this->exportVars["type"], $this->exportVars["doctype"], $this->exportVars["classname"], $this->exportVars["categories"], $this->exportVars["dir"], $finalDocs, $finalTempl, $finalObjs, $finalClasses);

				$_SESSION['weS']['exportVars_session']["finalDocs"] = $finalDocs;
				$_SESSION['weS']['exportVars_session']["finalTempl"] = $finalTempl;
				$_SESSION['weS']['exportVars_session']["finalObjs"] = $finalObjs;
				$_SESSION['weS']['exportVars_session']["finalClasses"] = $finalClasses;

// Description of the variables:
//  $finalDocs - contains documents IDs that need to be exported
//  $finalObjs - contains objects IDs that need to be exported
//  $file_format - export format; possible values are "xml","csv"
//  $file_name - name of the file that contains exported docs and objects
//  $export_to - where the file should be stored; possible values are "server","local"
//  $path - if the file will be stored on server then this variable contains the server path
//  $csv_delimiter - non-empty if csv file has been specified
//  $csv_enclose - non-empty if csv file has been specified
//  $csv_lineend - non-empty if csv file has been specified
//  $csv_fieldnames - non-empty if first row conains field names
//  $cdata - non-empty if xml file has been specified - coding of file

				$start_export = false;

				$hiddens = we_html_element::htmlHidden("pnt", "load");

				if(!empty($finalDocs)){
					$start_export = true;
					$hiddens .= we_html_element::htmlHidden("all", count($finalDocs));
				} else if(!empty($finalObjs)){
					$start_export = true;
					$hiddens .= we_html_element::htmlHidden("all", count($finalObjs));

					/* } else if ((count($finalTempl) > 0 && $extype==we_import_functions::TYPE_WE_XML) || (count($finalClasses) > 0  && $extype==we_import_functions::TYPE_WE_XML)) {
					  $start_export = true; */
				} else {
					$export_error = (defined('OBJECT_TABLE') ? "nothing_selected_objs" : "nothing_selected_docs");
				}

				if($start_export){
					$hiddens .= we_html_element::htmlHidden("cmd", "do_export");

					$out = we_html_element::jsElement('
if (top.footer.setProgressText){
	top.footer.setProgressText("current_description","Exportiere ...");
}
if (top.footer.setProgress){
	top.footer.setProgress(0);
}
							');
				}

				return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => ($start_export ? ($this->footerFrame . ".location='" . $this->frameset . "&pnt=footer&mode=progress&step=4';document.we_form.submit()") : ($this->bodyFrame . ".location='" . $this->frameset . "&pnt=body&step=99&error=" . $export_error . "';" . $this->footerFrame . ".location='" . $this->frameset . "&pnt=footer&step=99';"))), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens)
						)
				);

			case "do_export":
				$this->getExportVars();

				$file_format = $this->exportVars["extype"];
				$filename = $this->exportVars["filename"];
				$path = $this->exportVars["path"] . "/";

				$remaining_docs = $this->exportVars["finalDocs"];
				$remaining_objs = $this->exportVars["finalObjs"];
				$export_local = $this->exportVars["export_to"] === "local";

				$csv_delimiter = $this->exportVars["csv_delimiter"];
				$csv_enclose = $this->exportVars["csv_enclose"];
				$csv_lineend = $this->exportVars["csv_lineend"];
				$csv_fieldnames = $this->exportVars["csv_fieldnames"];

				$cdata = $this->exportVars["cdata"] === "true";

				$all = abs(we_base_request::_(we_base_request::INT, "all", 0));
				$exports = 0;

				if(isset($remaining_docs) && !empty($remaining_docs)){
					$exports = count($remaining_docs);
					$file_create = ($exports == $all);
					$file_complete = ($exports == 1);

					we_export_functions::exportDocument($remaining_docs[0], $file_format, $filename, ($export_local ? "###temp###" : $path), $file_create, $file_complete, $cdata);
				} else if(isset($remaining_objs) && !empty($remaining_objs)){
					if(defined('OBJECT_FILES_TABLE')){
						$exports = count($remaining_objs);
						we_export_functions::exportObject($remaining_objs[0], $file_format, $filename, ($export_local ? "###temp###" : $path), ($exports == $all), $exports == 1, $cdata, $csv_delimiter, $csv_enclose, $csv_lineend, ($csv_fieldnames == 1) && ($all == $exports));
					}
				}

				$percent = min(100, max(0, (int) ((($all - $exports + 2) / $all) * 100)));

				$_progress_update = we_html_element::jsElement('
							if (top.footer.setProgress) top.footer.setProgress(' . $percent . ');
						');

				if($remaining_docs){
					array_shift($remaining_docs);
					$_SESSION['weS']['exportVars_session']["finalDocs"] = $remaining_docs;
				} else if($remaining_objs){
					array_shift($remaining_objs);
					$_SESSION['weS']['exportVars_session']["finalObjs"] = $remaining_objs;
				}

				$hiddens = we_html_element::htmlHiddens(array(
						"pnt" => "load",
						"all" => $all,
						"cmd" => "do_export"));
				if(($remaining_docs) || ($remaining_objs)){
					return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens) . $_progress_update
							)
					);
				}
				if(!$export_local){
					unset($_SESSION['weS']['exportVars_session']);
				}
				return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET, we_html_element::htmlBody(
							array(
								"bgcolor" => "#ffffff",
								"marginwidth" => 5,
								"marginheight" => 5,
								"leftmargin" => 5,
								"topmargin" => 5,
								"onload" => oldHtmlspecialchars($export_local ? ($this->bodyFrame . ".location='" . $this->frameset . "&pnt=body&step=10&file_name=" . urlencode($filename) . "';" . $this->footerFrame . ".location='" . $this->frameset . "&pnt=footer&step=10';") : (we_message_reporting::getShowMessageCall(g_l('export', '[server_finished]'), we_message_reporting::WE_MESSAGE_NOTICE) . "top.close();")))), null
				);

			case 'do_wexport':
				$this->getExportVars();

				$file_format = $this->exportVars["extype"];
				$filename = $this->exportVars["filename"];
				$path = $this->exportVars["path"] . "/";

				$remaining_docs = $this->exportVars["finalDocs"];
				$remaining_objs = $this->exportVars["finalObjs"];
				$export_local = $this->exportVars["export_to"] === "local";

				$csv_delimiter = $this->exportVars["csv_delimiter"];
				$csv_enclose = $this->exportVars["csv_enclose"];
				$csv_lineend = $this->exportVars["csv_lineend"];
				$csv_fieldnames = $this->exportVars["csv_fieldnames"];

				$cdata = $this->exportVars["cdata"] === "true";

				$xmlExIm = new we_exim_XMLExIm();

				if(empty($this->exportVars["RefTable"])){
					$finalDocs = $this->exportVars["finalDocs"];
					$finalTempl = $this->exportVars["finalTempl"];
					$finalObjs = $this->exportVars["finalObjs"];
					$finalClasses = $this->exportVars["finalClasses"];

					$ids = array();
					foreach($finalDocs as $k => $v){
						$ct = f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . $v, '', $this->db);
						$ids[] = array(
							"ID" => $v,
							"ContentType" => $ct,
							"level" => 0
						);
					}
					foreach($finalTempl as $k => $v){
						$ids[] = array(
							"ID" => $v,
							"ContentType" => we_base_ContentTypes::TEMPLATE,
							"level" => 0
						);
					}
					foreach($finalObjs as $k => $v){
						$ids[] = array(
							"ID" => $v,
							"ContentType" => we_base_ContentTypes::OBJECT_FILE,
							"level" => 0
						);
					}
					foreach($finalClasses as $k => $v){
						$ids[] = array(
							"ID" => $v,
							"ContentType" => "object",
							"level" => 0
						);
					}
					$xmlExIm->setOptions($this->exportVars);
					$xmlExIm->prepareExport($ids);
					$_SESSION['weS']['exportVars_session']['RefTable'] = $xmlExIm->RefTable;
					$all = count($xmlExIm->RefTable);
					$exports = 0;
					$_SESSION['weS']['exportVars_session']['filename'] = ($export_local ? TEMP_PATH . $filename : $_SERVER['DOCUMENT_ROOT'] . $path . $filename);
//FIXME set export type in getHeader
					we_base_file::save($_SESSION['weS']['exportVars_session']["filename"], we_exim_XMLExIm::getHeader(), "wb");
				} else {
					$xmlExIm->RefTable = $this->exportVars["RefTable"];
					$xmlExIm->RefTable->current = $this->exportVars["CurrentRef"];
					$all = $xmlExIm->RefTable->getCount();
					$ref = $xmlExIm->RefTable->getNext();
					if($ref->ID && $ref->ContentType){
						$xmlExIm->exportChunk($ref->ID, $ref->ContentType, $filename);
					}
					$exports = $xmlExIm->RefTable->current;
				}

				$percent = round(min(100, max(0, ($all != 0 ? (int) (($exports / $all) * 100) : 0))), 2);
				$_SESSION['weS']['exportVars_session']["CurrentRef"] = $xmlExIm->RefTable->current;

				$hiddens = we_html_element::htmlHiddens(array(
						"pnt" => "load",
						"all" => $all,
						"cmd" => "do_wexport"));

				if($all > $exports){
					return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens) .
								we_html_element::jsElement('if (top.footer.setProgress) top.footer.setProgress(' . $percent . ');')
							)
					);
				}
				if(is_writable($filename)){
					we_base_file::save($filename, we_exim_XMLExIm::getFooter(), "ab");
				}

				return we_html_tools::getHtmlTop(g_l('import', '[title]'), '', '', STYLESHEET . we_html_element::jsElement('if (top.footer.setProgress) top.footer.setProgress(100);'), we_html_element::htmlBody(
							array(
								"bgcolor" => "#ffffff",
								"marginwidth" => 5,
								"marginheight" => 5,
								"leftmargin" => 5,
								"topmargin" => 5,
								"onload" => oldHtmlspecialchars($export_local ? ($this->bodyFrame . ".location='" . $this->frameset . "&pnt=body&step=10&file_name=" . urlencode($filename) . "';" . $this->footerFrame . ".location='" . $this->frameset . "&pnt=footer&step=10';") : ( we_message_reporting::getShowMessageCall(g_l('export', '[server_finished]'), we_message_reporting::WE_MESSAGE_NOTICE) . ";top.close();")))), null
				);
		}
		return $out;
	}

	/* creates the FileChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	private function formFileChooser($width = "", $IDName = "ParentID", $IDValue = "/", $cmd = "", $filter = ""){
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:formFileChooser('browse_server','" . $wecmdenc1 . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value);");

		return we_html_element::jsElement('
function formFileChooser() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

switch (args[0]) {
		case "browse_server":
			new (WE().util.jsWindow)(window, url,"server_selector",-1,-1,500,300,true,false,true);
		break;
	}
}') .
			we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 42, $IDValue, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", "", permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	private function formDirChooser($width = "", $rootDirID = 0, $table = FILE_TABLE, $Pathname = "ParentPath", $Pathvalue = "", $IDName = "ParentID", $IDValue = "", $cmd = ""){
		$table = FILE_TABLE;

		$js = we_html_element::jsElement('
				function formDirChooser() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

switch (args[0]) {
						case "we_selector_directory":
							new (WE().util.jsWindow)(window, url,"dir_selector",-1,-1,WE().consts.size.windowDirSelect.width,WE().consts.size.windowDirSelect.height,true,false,true true);
						break;
					}
				}
		');

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:formDirChooser('we_selector_directory',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");
		return $js . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden($IDName, $IDValue), $button);
	}

	private function getHTMLDocType($width = 350){
		$dtq = we_docTypes::getDoctypeQuery($this->db);
		$this->db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		$select = new we_html_select(array("name" => "doctype", "size" => 1, "class" => "weSelect", "style" => "{width: $width;}", "onchange" => ""));
		$first = "";
		while($this->db->next_record()){
			if(!$first){
				$first = $this->db->f("ID");
			}
			$select->addOption($this->db->f("ID"), $this->db->f("DocType"));
		}

		$doctype = $this->exportVars["doctype"];
		$type = $this->exportVars["type"];
		$dir = $this->exportVars["dir"];

		$select->selectOption($doctype);

		$path = $dir ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($dir), '', $this->db) : '/';
		$dir = we_html_tools::htmlFormElementTable($this->formWeChooser(FILE_TABLE, $width, 0, "dir", $dir, "Path", $path), g_l('export', '[dir]'));

		$table = new we_html_table(array('class' => 'default'), 3, 2);
		$table->setCol(0, 0, array('style' => 'width:' . (defined('OBJECT_FILES_TABLE') ? 25 : 0) . 'px'));
		$table->setColContent(0, 1, $select->getHtml());
		$table->setColContent(2, 1, $dir);

		$headline = defined('OBJECT_FILES_TABLE') ?
			we_html_forms::radiobutton("doctype", ($type === "doctype" ? true : ($type != "classname" ? true : false)), "type", g_l('export', '[doctypename]'), true, "defaultfont", $this->topFrame . ".type='doctype'") :
			we_html_element::htmlSpan(array("class" => "defaultfont"), g_l('export', '[doctypename]'));

		return we_html_tools::htmlFormElementTable(
				$table->getHtml(), $headline
		);
	}

	private function getHTMLObjectType($width = 350, $showdocs = false){
		if(defined('OBJECT_FILES_TABLE')){
			$this->db->query("SELECT ID,Text FROM " . OBJECT_TABLE);
			$select = new we_html_select(array("name" => "classname", "class" => "weSelect", "size" => 1, "style" => "{width: $width}", "onchange" => $this->topFrame . ".classname=document.we_form.classname.options[document.we_form.classname.selectedIndex].value;"));
			$first = "";
			while($this->db->next_record()){
				if(!$first){
					$first = $this->db->f("ID");
				}
				$select->addOption($this->db->f("ID"), $this->db->f("Text"));
			}

			$classname = $this->exportVars["classname"];


			$js = we_html_element::jsElement($this->topFrame . '.classname="' . $classname . '";');
			$select->selectOption($classname);

			$type = we_base_request::_(we_base_request::STRING, "type", '');

			$radio = $showdocs ? we_html_forms::radiobutton("classname", ($type === "classname" ? true : false), "type", g_l('export', '[classname]'), true, "defaultfont", $this->topFrame . ".type='classname'") : g_l('export', '[classname]');
			return $js . we_html_tools::htmlFormElementTable($select->getHtml(), $radio);
		}
		return null;
	}

	private function getHTMLCategory(){
		switch(we_base_request::_(we_base_request::STRING, "wcmd")){
			case "add_cat":
				$arr = makeArrayFromCSV($this->exportVars["categories"]);
				if(($cat = we_base_request::_(we_base_request::INTLISTA, "cat", array()))){
					foreach($cat as $id){
						if(strlen($id) && (!in_array($id, $arr))){
							$arr[] = $id;
						}
					}
					$this->exportVars["categories"] = implode(',', $arr);
				}
				break;
			case "del_cat":
				$arr = makeArrayFromCSV($this->exportVars["categories"]);
				if(($cat = we_base_request::_(we_base_request::INT, "cat"))){
					foreach($arr as $k => $v){
						if($v == $cat){
							unset($arr[$k]);
						}
					}
					$this->exportVars["categories"] = implode(',', $arr);
				}
				break;
			case "del_all_cats":
				$this->exportVars["categories"] = "";
				break;
			default:
		}


		$hiddens = we_html_element::htmlHiddens(array(
				"wcmd" => "",
				"categories" => $this->exportVars["categories"],
				"cat" => we_base_request::_(we_base_request::RAW, "cat", "")));


		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_cats')", true, 0, 0, "", "", (isset($this->exportVars["categories"]) ? false : true));
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','fillIDs();opener." . $this->bodyFrame . ".we_cmd(\\'add_cat\\',top.allIDs);')");
		$cats = new we_chooser_multiDir(350, $this->exportVars["categories"], "del_cat", $delallbut . $addbut, "", '"we/category"', CATEGORY_TABLE);

		if(!permissionhandler::hasPerm("EDIT_KATEGORIE")){
			$cats->isEditable = false;
		}
		return '<table class="default"><tr><td></td><td>' .
			$hiddens . we_html_tools::htmlFormElementTable($cats->get(), g_l('export', '[categories]'), "left", "defaultfont") .
			'</td></tr></table>';
	}

	private function formWeChooser($table = FILE_TABLE, $width = "", $rootDirID = 0, $IDName = "ID", $IDValue = 0, $Pathname = "Path", $Pathvalue = "/", $cmd = ""){
		$yuiSuggest = & weSuggest::getInstance();
		if(!$Pathvalue){
			$Pathvalue = f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), "", $this->db);
		}

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");

		$yuiSuggest->setAcId("Dir");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($Pathname, $Pathvalue);
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($IDName, $IDValue);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button, 10);

		return $yuiSuggest->getHTML();
	}

	private function getHTMLChooser($name, $value, $values, $title){
		$input_size = 5;

		$select = new we_html_select(array('name' => $name . '_select', 'class' => 'weSelect', 'onchange' => 'document.we_form.' . $name . '.value=this.options[this.selectedIndex].value;this.selectedIndex=0', 'style' => 'width:200;'));
		$select->addOption("", "");
		foreach($values as $k => $v){
			$select->addOption(oldHtmlspecialchars($k), oldHtmlspecialchars($v));
		}

		$table = new we_html_table(array('class' => 'default', "width" => 250), 1, 2);

		$table->setColContent(0, 0, we_html_tools::htmlTextInput($name, $input_size, $value) . '  ');
		$table->setColContent(0, 1, $select->getHtml());

		return we_html_tools::htmlFormElementTable($table->getHtml(), $title);
	}

}
