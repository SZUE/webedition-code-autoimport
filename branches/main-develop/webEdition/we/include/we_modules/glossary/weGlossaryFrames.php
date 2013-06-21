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

class weGlossaryFrames extends weModuleFrames{

	var $View;
	var $Tree;
	var $_space_size = 150;
	var $_text_size = 75;
	var $_width_size = 535;

	protected $treeDefaultWidth = 280;

	function __construct(){

		parent::__construct(WE_GLOSSARY_MODULE_DIR . "edit_glossary_frameset.php");

		$this->Tree = new weGlossaryTree();
		$this->View = new weGlossaryView(WE_GLOSSARY_MODULE_DIR . "edit_glossary_frameset.php", "top.content");

		$this->setupTree(GLOSSARY_TABLE, "top.content", "top.content.tree", "top.content.cmd");

		$this->module = "glossary";
	}

	function getJSCmdCode(){

		return $this->View->getJSTop() . we_html_element::jsElement($this->Tree->getJSMakeNewEntry());
	}

	function getHTMLFrameset(){
		$extraHead = $this->Tree->getJSTreeCode() . we_html_element::jsElement($this->getJSStart());

		return weModuleFrames::getHTMLFrameset($extraHead);
	}

	function getHTMLEditorHeader(){

		if(isset($_REQUEST['home']) && $_REQUEST["home"]){
			return weGlossaryFrameEditorHome::Header($this);
		}

		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){

				// Folder View
				case 'view_folder':
					return weGlossaryFrameEditorFolder::Header($this);
					break;

				// Type View
				case 'view_type':
					return weGlossaryFrameEditorType::Header($this);
					break;

				// Exception View
				case 'view_exception':
				case 'save_exception':
					return weGlossaryFrameEditorException::Header($this);
					break;

				// Item View
				default:
					return weGlossaryFrameEditorItem::Header($this);
					break;
			}

			if(isset($_REQUEST['cmdid']) && !preg_match('|^[0-9]|', $_REQUEST['cmdid'])){
				$this->View->Glossary->Language = substr($_REQUEST['cmdid'], 0, 5);
			}
		} else{
			return weGlossaryFrameEditorItem::Header($this);
		}
	}

	function getHTMLEditorBody(){

		if(isset($_REQUEST['home']) && $_REQUEST["home"]){
			return weGlossaryFrameEditorHome::Body($this);
		}

		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){

				// Folder View
				case 'view_folder':
					return weGlossaryFrameEditorFolder::Body($this);
					break;

				// Type View
				case 'view_type':
					return weGlossaryFrameEditorType::Body($this);
					break;

				// Exception View
				case 'view_exception':
				case 'save_exception':
					return weGlossaryFrameEditorException::Body($this);
					break;

				// Item View
				default:
					return weGlossaryFrameEditorItem::Body($this);
					break;
			}

			if(isset($_REQUEST['cmdid']) && !preg_match('|^[0-9]|', $_REQUEST['cmdid'])){
				$this->View->Glossary->Language = substr($_REQUEST['cmdid'], 0, 5);
			}
		} else{
			return weGlossaryFrameEditorItem::Body($this);
		}
	}

	function getHTMLEditorFooter(){

		if(isset($_REQUEST["home"])){
			return weGlossaryFrameEditorHome::Footer($this);
		}

		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){

				// Folder View
				case 'view_folder':
					return weGlossaryFrameEditorFolder::Footer($this);
					break;

				// Type View
				case 'view_type':
					return weGlossaryFrameEditorType::Footer($this);
					break;

				// Exception View
				case 'view_exception':
				case 'save_exception':
					return weGlossaryFrameEditorException::Footer($this);
					break;

				// Item View
				default:
					return weGlossaryFrameEditorItem::Footer($this);
					break;
			}

			if(isset($_REQUEST['cmdid']) && !preg_match('|^[0-9]|', $_REQUEST['cmdid'])){
				$this->View->Glossary->Language = substr($_REQUEST['cmdid'], 0, 5);
			}
		} else{
			return weGlossaryFrameEditorItem::Footer($this);
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
		}
		else
			exit;

		if(isset($_REQUEST["offset"])){
			$offset = $_REQUEST["offset"];
		}
		else
			$offset = 0;

		$rootjs = "";
		if(!$pid)
			$rootjs.='
		' . $this->Tree->topFrame . '.treeData.clear();
		' . $this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));
		';

		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "no_cmd"));

		$out.=we_html_element::htmlBody(array("bgcolor" => "white", "marginwidth" => 10, "marginheight" => 10, "leftmargin" => 10, "topmargin" => 10), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
					we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree(weGlossaryTreeLoader::getItems($pid, $offset, $this->Tree->default_segment, "")))
				)
		);

		return $this->getHTMLDocument($out);
	}

}