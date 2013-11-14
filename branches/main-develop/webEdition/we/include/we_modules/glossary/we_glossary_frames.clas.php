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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//TEST: was it ok to abandon treefooter?

class we_glossary_frames extends weModuleFrames{

	var $View;
	var $Tree;
	var $_space_size = 150;
	var $_text_size = 75;
	var $_width_size = 535;
	public $module = "glossary";
	protected $treeDefaultWidth = 280;

	function __construct(){
		parent::__construct(WE_GLOSSARY_MODULE_DIR . "edit_glossary_frameset.php");
		$this->Tree = new we_glossary_tree();
		$this->View = new we_glossary_view(WE_GLOSSARY_MODULE_DIR . "edit_glossary_frameset.php", "top.content");
		$this->setupTree(GLOSSARY_TABLE, "top.content", "top.content", "top.content.cmd");
	}

	function getJSCmdCode(){

		return $this->View->getJSTop() . we_html_element::jsElement($this->Tree->getJSMakeNewEntry());
	}

	function getHTMLFrameset(){
		$extraHead = $this->Tree->getJSTreeCode() . we_html_element::jsElement($this->getJSStart());

		return parent::getHTMLFrameset($extraHead);
	}

	function getHTMLEditorHeader(){

		if(isset($_REQUEST['home']) && $_REQUEST["home"]){
			return we_glossary_frameEditorHome::Header($this);
		}

		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){

				// Folder View
				case 'view_folder':
					return we_glossary_frameEditorFolder::Header($this);
					break;

				// Type View
				case 'view_type':
					return we_glossary_frameEditorType::Header($this);
					break;

				// Exception View
				case 'view_exception':
				case 'save_exception':
					return we_glossary_frameEditorException::Header($this);
					break;

				// Item View
				default:
					return we_glossary_frameEditorItem::Header($this);
					break;
			}

			if(isset($_REQUEST['cmdid']) && !preg_match('|^[0-9]|', $_REQUEST['cmdid'])){
				$this->View->Glossary->Language = substr($_REQUEST['cmdid'], 0, 5);
			}
		} else {
			return we_glossary_frameEditorItem::Header($this);
		}
	}

	function getHTMLEditorBody(){

		if(isset($_REQUEST['home']) && $_REQUEST["home"]){
			return we_glossary_frameEditorHome::Body($this);
		}

		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){

				// Folder View
				case 'view_folder':
					return we_glossary_frameEditorFolder::Body($this);
					break;

				// Type View
				case 'view_type':
					return we_glossary_frameEditorType::Body($this);
					break;

				// Exception View
				case 'view_exception':
				case 'save_exception':
					return we_glossary_frameEditorException::Body($this);
					break;

				// Item View
				default:
					return we_glossary_frameEditorItem::Body($this);
					break;
			}

			if(isset($_REQUEST['cmdid']) && !preg_match('|^[0-9]|', $_REQUEST['cmdid'])){
				$this->View->Glossary->Language = substr($_REQUEST['cmdid'], 0, 5);
			}
		} else {
			return we_glossary_frameEditorItem::Body($this);
		}
	}

	function getHTMLEditorFooter(){

		if(isset($_REQUEST["home"])){
			return we_glossary_frameEditorHome::Footer($this);
		}

		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){

				// Folder View
				case 'view_folder':
					return we_glossary_frameEditorFolder::Footer($this);
					break;

				// Type View
				case 'view_type':
					return we_glossary_frameEditorType::Footer($this);
					break;

				// Exception View
				case 'view_exception':
				case 'save_exception':
					return we_glossary_frameEditorException::Footer($this);
					break;

				// Item View
				default:
					return we_glossary_frameEditorItem::Footer($this);
					break;
			}

			if(isset($_REQUEST['cmdid']) && !preg_match('|^[0-9]|', $_REQUEST['cmdid'])){
				$this->View->Glossary->Language = substr($_REQUEST['cmdid'], 0, 5);
			}
		} else {
			return we_glossary_frameEditorItem::Footer($this);
		}
	}

	function getHTMLTreeHeader(){
		return "";
	}

	function getHTMLTreeFooter(){

		$body = we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "edit/editfooterback.gif", "marginwidth" => 5, "marginheight" => 0, "leftmargin" => 5, "topmargin" => 0), ""
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLCmd(){
		$out = "";

		if(isset($_REQUEST["pid"])){
			$pid = $_REQUEST["pid"];
		} else {
			exit;
		}

		$offset = (isset($_REQUEST["offset"]) ? $_REQUEST["offset"] : 0);

		$rootjs = "";
		if(!$pid)
			$rootjs.='
		' . $this->Tree->topFrame . '.treeData.clear();
		' . $this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));
		';

		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "no_cmd"));

		$out.=we_html_element::htmlBody(array("bgcolor" => "white", "marginwidth" => 10, "marginheight" => 10, "leftmargin" => 10, "topmargin" => 10), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
					we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree(we_glossary_treeLoader::getItems($pid, $offset, $this->Tree->default_segment, "")))
				)
		);

		return $this->getHTMLDocument($out);
	}

}