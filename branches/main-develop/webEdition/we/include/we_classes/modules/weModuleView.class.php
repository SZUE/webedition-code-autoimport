<?php

/**
 * webEdition CMS
 *
 * $Rev: 6119 $
 * $Author: lukasimhof $
 * $Date: 2013-05-17 16:03:12 +0200 (Fr, 17 Mai 2013) $
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
/* the parent class of storagable webEdition classes */


class weModuleView {

	var $db;
	var $frameset;
	var $topFrame;
	var $raw;

	function __construct($frameset = '', $topframe = 'top.content'){
		$this->db = new DB_WE();
		$this->setFramesetName($frameset);
		$this->setTopFrame($topframe);
	}

	//----------- Utility functions ------------------

	function htmlHidden($name, $value = ''){
		return we_html_element::htmlHidden(array('name' => trim($name), 'value' => oldHtmlspecialchars($value)));
	}

	//-----------------Init -------------------------------

	function setFramesetName($frameset){
		$this->frameset = $frameset;
	}

	function setTopFrame($frame){
		$this->topFrame = $frame;
	}

	//------------------------------------------------


	function getCommonHiddens($cmds = array()){
		return $this->htmlHidden('cmd', (isset($cmds['cmd']) ? $cmds['cmd'] : '')) .
			$this->htmlHidden('cmdid', (isset($cmds['cmdid']) ? $cmds['cmdid'] : '')) .
			$this->htmlHidden('pnt', (isset($cmds['pnt']) ? $cmds['pnt'] : '')) .
			$this->htmlHidden('tabnr', (isset($cmds['tabnr']) ? $cmds['tabnr'] : ''));
	}

	function getJSTop_tmp(){//taken from old edit_shop_frameset.php
		return we_html_element::jsScript(JS_DIR . 'images.js') . we_html_element::jsScript(JS_DIR . 'windows.js');
	}

	function getJSTop(){//TODO: is this shop-code or a copy paste from another module?
		return we_html_element::jsScript(JS_DIR . 'windows.js');
	}

	function getJSProperty(){
		return we_html_element::jsScript(JS_DIR . "windows.js");
	}

	function getJSTreeHeader(){
	}

	function getJSSubmitFunction($def_target = "edbody", $def_method = "post"){
		return '
			function submitForm() {
				var f = arguments[3] ? self.document.forms[arguments[3]] : self.document.we_form;
				f.target = arguments[0]?arguments[0]:"' . $def_target . '";
				f.action = arguments[1]?arguments[1]:"' . $this->frameset . '";
				f.method = arguments[2]?arguments[2]:"' . $def_method . '";

				f.submit();
			}';
	}

	function processCommands(){
		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){
				case 'new_raw':
					$this->raw = new weShop();
					print we_html_element::jsElement(
							$this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->raw->Text) . '";' .
							$this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
					);
					break;
				case 'edit_raw':
					$this->raw = new weShop($_REQUEST['cmdid']);
					print we_html_element::jsElement(
							$this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->raw->Text) . '";' .
							$this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
					);
					break;
				case 'save_raw':
					if($this->raw->filenameNotValid()){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_shop', '[we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
						break;
					}

					$newone = ($this->raw->ID ? false : true);

					$this->raw->save();

					//$ttrow = getHash('SELECT * FROM ' . RAW_TABLE . ' WHERE ID=' . intval($this->raw->ID), $this->db);
					$tt = addslashes($tt != '' ? $tt : $this->raw->Text);
					$js = ($newone ?
							'
var attribs = new Array();
attribs["icon"]="' . $this->raw->Icon . '";
attribs["id"]="' . $this->raw->ID . '";
attribs["typ"]="item";
attribs["parentid"]="0";
attribs["text"]="' . $tt . '";
attribs["disable"]=0;
attribs["tooltip"]="";' .
							$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));' .
							$this->topFrame . '.drawTree();' :
							$this->topFrame . '.updateEntry(' . $this->raw->ID . ',"' . $tt . '");'
						);
					print we_html_element::jsElement(
							$js .
							we_message_reporting::getShowMessageCall(g_l('modules_shop', '[raw_saved_ok]'), we_message_reporting::WE_MESSAGE_NOTICE)
					);
					break;
				case 'delete_raw':
					$js = '' . $this->topFrame . '.deleteEntry(' . $this->raw->ID . ');';

					$this->raw->delete();
					$this->raw = new weShop();

					print we_html_element::jsElement(
							$js .
							we_message_reporting::getShowMessageCall(g_l('modules_shop', '[raw_deleted]'), we_message_reporting::WE_MESSAGE_NOTICE)
					);
					break;
				case 'switchPage':
					break;
				default:
			}
		}

		$_SESSION['weS']['raw_session'] = serialize($this->raw);
	}

	function processVariables(){
		if(isset($_SESSION['weS']['raw_session'])){
			$this->raw = unserialize($_SESSION['weS']['raw_session']);
		}

		if(is_array($this->raw->persistent_slots)){
			foreach($this->raw->persistent_slots as $key => $val){
				$varname = $val;
				if(isset($_REQUEST[$varname])){
					$this->raw->{$val} = $_REQUEST[$varname];
				}
			}
		}

		if(isset($_REQUEST['page']))
			if(isset($_REQUEST['page'])){
				$this->page = $_REQUEST['page'];
			}
	}

	function new_array_splice(&$a, $start, $len = 1){
		$ks = array_keys($a);
		$k = array_search($start, $ks);
		if($k !== false){
			$ks = array_splice($ks, $k, $len);
			foreach($ks as $k)
				unset($a[$k]);
		}
	}

}