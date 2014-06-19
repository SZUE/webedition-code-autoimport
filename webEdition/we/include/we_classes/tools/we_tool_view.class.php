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
/* the parent class of storagable webEdition classes */


class we_tool_view{

	var $Model;
	var $toolName;
	var $toolDir;
	var $toolUrl;
	var $db;
	var $frameset;
	var $topFrame;
	var $editorBodyFrame;
	var $editorBodyForm;
	var $editorHeaderFrame;
	var $editorFooterFrame;
	var $icon_pattern = '';
	var $item_pattern = '';
	var $group_pattern = '';
	var $page = 1;

	function __construct($frameset = '', $topframe = 'top'){

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
		$this->editorBodyFrame = $frame . '.resize.right.editor.edbody';
		$this->editorBodyForm = $this->editorBodyFrame . '.document.we_form';
		$this->editorHeaderFrame = $frame . '.resize.right.editor.edheader';
		$this->editorFooterFrame = $frame . '.resize.right.editor.edfooter';
	}

	//------------------------------------------------


	function getCommonHiddens($cmds = array()){
		return $this->htmlHidden('cmd', (isset($cmds['cmd']) ? $cmds['cmd'] : '')) .
			$this->htmlHidden('cmdid', (isset($cmds['cmdid']) ? $cmds['cmdid'] : '')) .
			$this->htmlHidden('pnt', (isset($cmds['pnt']) ? $cmds['pnt'] : '')) .
			$this->htmlHidden('tabnr', (isset($cmds['tabnr']) ? $cmds['tabnr'] : '')) .
			$this->htmlHidden('vernr', (isset($cmds['vernr']) ? $cmds['vernr'] : 0)) .
			$this->htmlHidden('delayCmd', (isset($cmds['delayCmd']) ? $cmds['delayCmd'] : '')) .
			$this->htmlHidden('delayParam', (isset($cmds['delayParam']) ? $cmds['delayParam'] : ''));
	}

	function getJSTop(){
		$js = '
			var activ_tab = "1";
			var hot = 0;

			function we_cmd() {
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				if(' . $this->topFrame . '.hot && (arguments[0]=="tool_' . $this->toolName . '_edit" || arguments[0]=="tool_' . $this->toolName . '_new" || arguments[0]=="tool_' . $this->toolName . '_new_group" || arguments[0]=="tool_' . $this->toolName . '_exit")){
					' . $this->editorBodyFrame . '.document.we_form.delayCmd.value = arguments[0];
					' . $this->editorBodyFrame . '.document.we_form.delayParam.value = arguments[1];
					arguments[0] = "exit_doc_question";
				}
				switch (arguments[0]) {
					case "tool_' . $this->toolName . '_edit":
						if(' . $this->editorBodyFrame . '.loaded) {
							' . $this->editorBodyFrame . '.document.we_form.cmd.value = arguments[0];
							' . $this->editorBodyFrame . '.document.we_form.cmdid.value=arguments[1];
							' . $this->editorBodyFrame . '.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
							' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
							' . $this->editorBodyFrame . '.submitForm();
						} else {
							setTimeout(\'we_cmd("tool_' . $this->toolName . '_edit",\'+arguments[1]+\');\', 10);
						}
					break;
					case "tool_' . $this->toolName . '_new":
					case "tool_' . $this->toolName . '_new_group":
						if(' . $this->editorBodyFrame . '.loaded) {
							' . $this->topFrame . '.hot = 0;
							' . $this->editorBodyFrame . '.document.we_form.cmd.value = arguments[0];
							' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
							' . $this->editorBodyFrame . '.document.we_form.tabnr.value = 1;
							' . $this->editorBodyFrame . '.submitForm();
						} else {
							setTimeout(\'we_cmd("tool_' . $this->toolName . '_new");\', 10);
						}
						if((typeof treeData!="undefined") && treeData){
							treeData.unselectnode();
						}
					break;
					case "tool_' . $this->toolName . '_save":
						if(' . $this->editorBodyFrame . '.document.we_form.cmd.value=="home") return;
						if (' . $this->editorBodyFrame . '.loaded) {
							' . $this->editorBodyFrame . '.document.we_form.cmd.value=arguments[0];
							' . $this->editorBodyFrame . '.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
							' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
							' . $this->editorBodyFrame . '.submitForm();
						} else {
							' . we_message_reporting::getShowMessageCall(g_l('tools', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
						break;
					case "tool_' . $this->toolName . '_delete":
						if(' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=="home"){
							' . we_message_reporting::getShowMessageCall(g_l('tools', '[nothing_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							return;
						}
						if(' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.newone.value==1){
							' . we_message_reporting::getShowMessageCall(g_l('tools', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							return;
						}
						' . (!permissionhandler::hasPerm("DELETE_" . strtoupper($this->toolName)) ?
				(
				we_message_reporting::getShowMessageCall(g_l('tools', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
				) :
				('
								if (' . $this->topFrame . '.resize.right.editor.edbody.loaded) {
									if (confirm("' . g_l('tools', '[delete_alert]') . '")) {
										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
										' . $this->editorHeaderFrame . '.location="' . $this->frameset . '?home=1&pnt=edheader";
										' . $this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?home=1&pnt=edfooter";
										' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
									}
								} else {
									' . we_message_reporting::getShowMessageCall(g_l('tools', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
								}

						')) . '
					break;
					case "tool_' . $this->toolName . '_exit":
						top.close();
					break;
					case "exit_doc_question":
						url = "' . $this->frameset . '?pnt=exit_doc_question&delayCmd="+' . $this->editorBodyFrame . '.document.we_form.delayCmd.value+"&delayParam="+' . $this->editorBodyFrame . '.document.we_form.delayParam.value;
						new jsWindow(url,"we_exit_doc_question",-1,-1,380,130,true,false,true);
					break;
					' . $this->getTopJSAdditional() . '
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("top.opener.top.we_cmd(" + args + ")");
				}
			}

			function mark() {
				hot=1;
				' . $this->editorHeaderFrame . '.mark();
			}

			';

		return we_html_element::jsScript(JS_DIR . "windows.js") . we_html_element::jsElement($js);
	}

	function getTopJSAdditional(){
		return '';
	}

	function getPropertyJSAdditional(){
		return '';
	}

	function getJSProperty(){
		$out = "";
		$out.=we_html_element::jsScript(JS_DIR . "windows.js");

		$js = '
			var loaded=0;
			function we_cmd() {
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				switch (arguments[0]) {
					case "openDocselector":
						new jsWindow(url,"we_docselector",-1,-1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ',true,true,true,true);
						break;
					case "openSelector":
						new jsWindow(url,"we_selector",-1,-1,' . we_selector_file::WINDOW_SELECTOR_WIDTH . ',' . we_selector_file::WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
						break;
					case "openDirselector":
						new jsWindow(url,"we_selector",-1,-1,' . we_selector_file::WINDOW_DIRSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DIRSELECTOR_HEIGHT . ',true,true,true,true);
						break;
					case "openCatselector":
						new jsWindow(url,"we_catselector",-1,-1,' . we_selector_file::WINDOW_CATSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_CATSELECTOR_HEIGHT . ',true,true,true,true);
						break;
					case "open' . $this->toolName . 'Dirselector":
						url = "' . WEBEDITION_DIR . 'apps/' . $this->toolName . '/we_' . $this->toolName . 'DirSelect.php?";
						for(var i = 0; i < arguments.length; i++){
							url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }
						}
						new jsWindow(url,"we_' . $this->toolName . '_dirselector",-1,-1,600,400,true,true,true);
						break;
						' . $this->getPropertyJSAdditional() . '
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("' . $this->topFrame . '.we_cmd("+args+")");
				}
			}


			' . $this->getJSSubmitFunction() . '

		';

		$out.=we_html_element::jsElement($js);
		return $out;
	}

	function getJSTreeHeader(){
		return '

				function we_cmd(){
					var args = "";
					var url = "' . $this->frameset . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
					switch (arguments[0]) {
						default:
							for (var i = 0; i < arguments.length; i++) {
								args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
							}
							eval(\'' . $this->topFrame . '.we_cmd(\'+args+\')\');
					}
				}

		' . $this->getJSSubmitFunction('cmd');
	}

	function getJSSubmitFunction($def_target = "edbody", $def_method = "post"){
		return '

				function submitForm() {

					var f = self.document.we_form;

					if (arguments[0]) {
						f.target = arguments[0];
					} else {
						f.target = "' . $def_target . '";
					}

					if (arguments[1]) {
						f.action = arguments[1];
					} else {
						f.action = "' . $this->frameset . '";
					}

					if (arguments[2]) {
						f.method = arguments[2];
					} else {
						f.method = "' . $def_method . '";
					}

					f.submit();
				}

		';
	}

	function processCommands(){

	}

	function processVariables(){
		if(isset($_SESSION['weS'][$this->toolName . '_session'])){
			$this->Model = unserialize($_SESSION['weS'][$this->toolName . '_session']);
		}

		if(is_array($this->Model->persistent_slots)){
			foreach($this->Model->persistent_slots as $val){
				if(($tmp = we_base_request::_(we_base_request::STRINGC, $val))){
					$this->Model->$val = $tmp;
				}
			}
		}
	}

}
