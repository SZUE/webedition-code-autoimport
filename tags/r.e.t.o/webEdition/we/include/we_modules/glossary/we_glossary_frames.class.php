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
//TEST: was it ok to abandon treefooter?

class we_glossary_frames extends we_modules_frame{
	var $_space_size = 150;
	var $_text_size = 75;
	var $_width_size = 535;
	protected $treeDefaultWidth = 280;

	function __construct(){
		$this->module = "glossary";
		parent::__construct(WE_GLOSSARY_MODULE_DIR . "edit_glossary_frameset.php");
		$this->Tree = new we_glossary_tree();
		$this->View = new we_glossary_view(WE_GLOSSARY_MODULE_DIR . "edit_glossary_frameset.php", "top.content");
		$this->setupTree(GLOSSARY_TABLE, "top.content", "top.content", "top.content.cmd");
	}

	function getJSCmdCode(){
		return $this->View->getJSTop() . we_html_element::jsElement($this->Tree->getJSMakeNewEntry());
	}

	function getHTMLFrameset(){
		return parent::getHTMLFrameset(
				$this->Tree->getJSTreeCode() . we_html_element::jsElement($this->getJSStart())
		);
	}

	protected function getHTMLEditorHeader(){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return we_glossary_frameEditorHome::Header($this);
		}
		$cmdid = we_base_request::_(we_base_request::STRING, 'cmdid');
		if($cmdid && !is_numeric($cmdid)){
			$this->View->Glossary->Language = substr($cmdid, 0, 5);
		}
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			// Folder View
			case 'glossary_view_folder':
				return we_glossary_frameEditorFolder::Header($this);
			// Type View
			case 'glossary_view_type':
				return we_glossary_frameEditorType::Header($this);
			// Exception View
			case 'glossary_view_exception':
			case 'save_exception':
				return we_glossary_frameEditorException::Header($this);
			// Item View
			default:
				return we_glossary_frameEditorItem::Header($this);
		}
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return we_glossary_frameEditorHome::Body($this);
		}
		$cmdid = we_base_request::_(we_base_request::STRING, 'cmdid');
		if($cmdid && !is_numeric($cmdid)){
			$this->View->Glossary->Language = substr($cmdid, 0, 5);
		}
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			// Folder View
			case 'glossary_view_folder':
				return we_glossary_frameEditorFolder::Body($this);
			// Type View
			case 'glossary_view_type':
				return we_glossary_frameEditorType::Body($this);
			// Exception View
			case 'glossary_view_exception':
			case 'save_exception':
				return we_glossary_frameEditorException::Body($this);
			// Item View
			default:
				return we_glossary_frameEditorItem::Body($this);
		}
	}

	protected function getHTMLEditorFooter(){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return we_glossary_frameEditorHome::Footer($this);
		}
		$cmdid = we_base_request::_(we_base_request::STRING, 'cmdid');
		if($cmdid && !is_numeric($cmdid)){
			$this->View->Glossary->Language = substr($cmdid, 0, 5);
		}
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			// Folder View
			case 'glossary_view_folder':
				return we_glossary_frameEditorFolder::Footer($this);
			// Type View
			case 'glossary_view_type':
				return we_glossary_frameEditorType::Footer($this);
			// Exception View
			case 'glossary_view_exception':
			case 'save_exception':
				return we_glossary_frameEditorException::Footer($this);
			// Item View
			default:
				return we_glossary_frameEditorItem::Footer($this);
		}
	}

	protected function getHTMLTreeHeader(){
		return "";
	}

	protected function getHTMLTreeFooter(){
		return $this->getHTMLDocument(
				we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "edit/editfooterback.gif", "marginwidth" => 5, "marginheight" => 0, "leftmargin" => 5, "topmargin" => 0), ""
				)
		);
	}

	function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::RAW, "pid")) === false){
			exit;
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);

		$rootjs = "";
		if(!$pid){
			$rootjs.=
				$this->Tree->topFrame . '.treeData.clear();' .
				$this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));';
		}
		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "no_cmd"));

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array("bgcolor" => "white", "marginwidth" => 10, "marginheight" => 10, "leftmargin" => 10, "topmargin" => 10), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree(we_glossary_treeLoader::getItems($pid, $offset, $this->Tree->default_segment, "")))
					)
				)
		);
	}

}
