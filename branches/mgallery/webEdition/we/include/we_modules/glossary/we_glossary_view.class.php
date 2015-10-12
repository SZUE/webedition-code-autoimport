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
class we_glossary_view extends we_modules_view{

	/**
	 * Glossary Instance
	 * @var object
	 */
	var $Glossary;

	/**
	 * Name of the Editor-Body-Frame
	 * @var string
	 */
	var $EditorBodyFrame;

	/**
	 * Name of the Editor-Body-Form
	 * @var string
	 */
	var $EditorBodyForm;

	/**
	 * Name of the Editor-Header-Frame
	 * @var string
	 */
	var $EditorHeaderFrame;

	private $page = 0;

	/**
	 * @param string $frameset
	 * @param string $topframe
	 */
	public function __construct($frameset = "", $topframe = "top.content"){
		parent::__construct($frameset, $topframe);
		$this->Glossary = new we_glossary_glossary();

	}

	//-----------------Init -------------------------------

	/**
	 * set the name of the topframe, editorBodyFrame, editorBodyForm
	 * and the editorHeaderFrame
	 *
	 * @param string $frame
	 */
	function setTopFrame($frame){
		parent::setTopFrame($frame);
		$this->EditorBodyFrame = $frame . '.editor.edbody';
		$this->EditorBodyForm = $this->EditorBodyFrame . '.document.we_form';
		$this->EditorHeaderFrame = $frame . '.editor.edheader';
	}

	//------------------------------------------------

	function getCommonHiddens($cmds = array()){
		return
				parent::getCommonHiddens($cmds) .
				we_html_element::htmlHidden("IsFolder", (isset($this->Glossary->IsFolder) ? $this->Glossary->IsFolder : '0'));
	}

	function getJSTop(){
		$modData = we_base_moduleInfo::getModuleData(we_base_request::_(we_base_request::STRING, 'mod', ''));
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';
		return
				parent::getJSTop() .
				we_html_element::jsElement('
var get_focus = 1;
var activ_tab = 1;
var scrollToVal = 0;


function doUnload() {
	jsWindow.prototype.closeAll(window);
}

parent.document.title = "' . $title . '";

function we_cmd() {
	var args = [];
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?";
		for(var i = 0; i < arguments.length; i++){
						args.push(arguments[i]);
		url += "we_cmd["+i+"]="+encodeURI(arguments[i]);
		if(i < (arguments.length - 1)){ url += "&"; }
		}

	if(hot == "1" && args[0] != "save_glossary") {
		if(confirm("' . g_l('modules_glossary', '[save_changed_glossary]') . '")) {
			args[0] = "save_glossary";
		} else {
			top.content.usetHot();
		}
	}
	switch (args[0]) {
		case "exit_glossary":
			if(hot != "1") {
				top.opener.top.we_cmd("exit_modules");
			}
			break;
		case "new_glossary_acronym":
		case "new_glossary_abbreviation":
		case "new_glossary_foreignword":
		case "new_glossary_link":
		case "new_glossary_textreplacement":
			if(' . $this->topFrame . '.editor.edbody.loaded) {
				' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value = args[0];
				if(args[1] != undefined) {
					' . $this->topFrame . '.editor.edbody.document.we_form.cmdid.value = args[1];
				}
				' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value = 1;
				' . $this->topFrame . '.editor.edbody.submitForm();
			} else {
				if(args[1] != undefined) {
					str = \'we_cmd("\' + args[0] + \'", "\' + args[1] + \'");\';
					setTimeout(str, 10);
				} else {
					str = \'we_cmd(\' + args[0] + \');\';
					setTimeout(str, 10);
				}
			}
			break;

		case "delete_glossary":
			var exc = ' . $this->topFrame . '.editor.edbody.document.we_form.cmdid.value;
			if (exc.substring(exc.length-10, exc.length)=="_exception") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				break;
			}
			if(top.content.editor.edbody.document.we_form.cmd.value=="home") return;
			if(top.content.editor.edbody.document.we_form.cmd.value=="glossary_view_folder") return;
			if(top.content.editor.edbody.document.we_form.cmd.value=="glossary_view_type") return;
			if(top.content.editor.edbody.document.we_form.cmd.value=="glossary_view_exception") return;
			if(top.content.editor.edbody.document.we_form.newone.value==1){
				' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return;
			}
			' . (!permissionhandler::hasPerm("DELETE_GLOSSARY") ?
								(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
								) :
								('
				if (' . $this->topFrame . '.editor.edbody.loaded) {
					if (confirm("' . g_l('modules_glossary', '[delete_alert]') . '")) {
						' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=args[0];
						' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
						' . $this->EditorHeaderFrame . '.location="' . $this->frameset . '?home=1&pnt=edheader";
						' . $this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?home=1&pnt=edfooter";
						' . $this->topFrame . '.editor.edbody.submitForm();
					}
				} else {
					' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				}
			')) . '
			break;

		case "save_exception":
		case "save_glossary":
			var exc = ' . $this->topFrame . '.editor.edbody.document.we_form.cmdid.value;
			if (exc.substring(exc.length-10, exc.length)=="_exception") {
				args[0] = "save_exception";
			}
			if(top.content.editor.edbody.document.we_form.cmd.value=="home") return;
			if(top.content.editor.edbody.document.we_form.cmd.value=="glossary_view_folder") return;
			if(top.content.editor.edbody.document.we_form.cmd.value=="glossary_view_type") return;
			if (' . $this->topFrame . '.editor.edbody.loaded) {
				' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=args[0];
				' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
				if(top.makeNewEntryCheck==1) {
					' . $this->topFrame . '.editor.edbody.submitForm("cmd");
				} else {
					' . $this->topFrame . '.editor.edbody.submitForm();
				}
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
			top.content.usetHot();
			break;

		case "glossary_edit_acronym":
		case "glossary_edit_abbreviation":
		case "glossary_edit_foreignword":
		case "glossary_edit_link":
		case "glossary_edit_textreplacement":
			' . (!permissionhandler::hasPerm("EDIT_GLOSSARY") ? we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR) . 'return;' : '') . '
			' . $this->topFrame . '.hot=0;
			' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=args[0];
			' . $this->topFrame . '.editor.edbody.document.we_form.cmdid.value=args[1];
			' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
			' . $this->topFrame . '.editor.edbody.submitForm();
			break;

		case "load":
			' . $this->topFrame . '.cmd.location="' . $this->frameset . '?pnt=cmd&pid="+args[1]+"&offset="+args[2]+"&sort="+args[3];
			break;

		case "home":
			' . $this->EditorBodyFrame . '.parent.location="' . $this->frameset . '?pnt=editor";
			break;

		default:
			top.opener.top.we_cmd.apply(this, args);

	}
}');
	}

	function getJSProperty(){
		return parent::getJSProperty() .
				we_html_element::jsElement('
var loaded=0;

function doUnload() {
	jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "switchPage":
			document.we_form.cmd.value=arguments[0];
			document.we_form.tabnr.value=arguments[1];
			submitForm();
			break;
		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.content.we_cmd.apply(this, args);
	}
}
function submitForm() {
	var f = self.document.we_form;
	f.target =  (arguments[0]?arguments[0]:"edbody");
	f.action = (arguments[1]?arguments[1]:"' . $this->frameset . '");
	f.method = (arguments[2]?arguments[2]:"post");
	f.submit();
}');
	}

		function getJSSubmitFunction(){
		return '';
	}

	public function processCommands(){
		$cmdid = we_base_request::_(we_base_request::STRING, "cmdid");
		switch(($cmd = we_base_request::_(we_base_request::STRING, "cmd"))){

			case "new_glossary_acronym":
			case "new_glossary_abbreviation":
			case "new_glossary_foreignword":
			case "new_glossary_link":
			case "new_glossary_textreplacement":
				if(!permissionhandler::hasPerm("NEW_GLOSSARY")){
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}
				$this->Glossary = new we_glossary_glossary();
				$this->Glossary->Type = array_pop(explode('_', $cmd, 4));

				echo we_html_element::jsElement('
							' . $this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->Glossary->Text) . '";
							' . $this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
					');
				break;

			case "glossary_edit_acronym":
			case "glossary_edit_abbreviation":
			case "glossary_edit_foreignword":
			case "glossary_edit_link":
			case "glossary_edit_textreplacement":
				if(!permissionhandler::hasPerm("EDIT_GLOSSARY")){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					$_REQUEST['home'] = 1;
					$_REQUEST['pnt'] = 'edbody';
					break;
				}
				$this->Glossary = new we_glossary_glossary($cmdid);

				echo we_html_element::jsElement(
						$this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->Glossary->Text) . '";' .
						$this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";');
				break;

			case 'populateWorkspaces':
				$objectLinkID = we_base_request::_(we_base_request::INT, 'link', 0, 'Attributes', 'ObjectLinkID');
				$_values = we_navigation_dynList::getWorkspacesForObject($objectLinkID);
				$_js = '';

				if($_values){

					foreach($_values as $_id => $_path){
						$_js .= $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options[' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length] = new Option("' . $_path . '",' . $_id . ');
							';
					}
					echo we_html_element::jsElement(
							$this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length = 0;
							' . $_js . '
							' . $this->EditorBodyFrame . '.setDisplay("ObjectWorkspaceID","block");
						');
				} elseif(we_navigation_dynList::getWorkspaceFlag($objectLinkID)){
					echo we_html_element::jsElement(
							$this->EditorBodyFrame . '.setDisplay("ObjectWorkspaceID","block");
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length = 0;
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options[' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length] = new Option("/",0);
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].selectedIndex = 0;'
					);
				} else {
					echo we_html_element::jsElement(
							$this->EditorBodyFrame . '.setDisplay("ObjectWorkspaceID","none");
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length = 0;
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options[' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length] = new Option("-1",-1);
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectLinkID]\'].value = "";
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectLinkPath]\'].value = "";
								' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[no_workspace]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							');
				}
				break;

			case 'save_exception':
				if(!$cmdid || !($exception = we_base_request::_(we_base_request::STRING, 'Exception'))){
					break;
				}

				$language = substr($cmdid, 0, 5);

				we_glossary_glossary::editException($language, $exception);

				echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[save_ok]'), we_message_reporting::WE_MESSAGE_NOTICE)
				);

				break;

			case "save_glossary":
				if(($exception = we_base_request::_(we_base_request::STRING, 'Exception'))){
					$language = substr($cmdid, 0, 5);

					we_glossary_glossary::editException($language, $exception);
					break;
				}
				$type = we_base_request::_(we_base_request::STRING, 'Type');
				$this->Glossary->Text = we_base_request::_(we_base_request::STRING, $type, '', 'Text');
				if($this->Glossary->Type != we_glossary_glossary::TYPE_FOREIGNWORD){
					$this->Glossary->Title = we_base_request::_(we_base_request::STRING, $type, '', 'Title');
				}
				$this->Glossary->Attributes = we_base_request::_(we_base_request::STRING, $type, '', 'Attributes');

				if(!permissionhandler::hasPerm("NEW_GLOSSARY") && !permissionhandler::hasPerm("EDIT_GLOSSARY")){
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}

				if(!trim($this->Glossary->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->Glossary->checkFieldText($this->Glossary->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[text_notValid]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->Glossary->checkFieldText($this->Glossary->Title)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[title_notValid]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				//$oldpath = $this->Glossary->Path;
				// set the path and check it
				$this->Glossary->setPath();

				if($this->Glossary->pathExists($this->Glossary->Path)){
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}

				if($this->Glossary->isSelf()){
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}


				$StateBefore = ($this->Glossary->ID ?
								f("SELECT Published FROM " . GLOSSARY_TABLE . " WHERE ID = " . intval($this->Glossary->ID), "", new DB_WE()) :
								0);

				$isNew = $this->Glossary->ID == 0;

				if($this->Glossary->save()){
					$this->Glossary->Text = htmlentities($this->Glossary->Text, ENT_QUOTES);
					$this->Glossary->Title = htmlentities($this->Glossary->Title, ENT_QUOTES);

					if($isNew){
						$js = $this->topFrame . '.makeNewEntry(id:\'' . $this->Glossary->ID . '\',parentid:\'' . $this->Glossary->Language . '_' . $this->Glossary->Type . '\',text:\'' . $this->Glossary->Text . '\',open:0,contenttype:\'' . ($this->Glossary->IsFolder ? 'folder' : 'we/glossary') . '\',table:\'' . GLOSSARY_TABLE . '\',published:' . ($this->Glossary->Published > 0 ? 1 : 0) . '});
								' . $this->topFrame . '.drawTree();';
					} else {
						$js = $this->topFrame . '.updateEntry({id:' . $this->Glossary->ID . ',text:"' . $this->Glossary->Text . '",parentid:"' . $this->Glossary->Language . '_' . $this->Glossary->Type . '",published:' . ($this->Glossary->Published > 0 ? 1 : 0) . '});';
					}

					$this->Glossary->Text = html_entity_decode($this->Glossary->Text, ENT_QUOTES);
					$this->Glossary->Title = html_entity_decode($this->Glossary->Title, ENT_QUOTES);

					$message = "";
					$pub = we_base_request::_(we_base_request::BOOL, 'Published');
					// Replacment of item is activated
					if($StateBefore == 0 && $pub){
						$message .= sprintf(g_l('modules_glossary', '[replace_activated]'), $this->Glossary->Text) . "\\n";

						// Replacement of item is deactivated
					} else if($StateBefore > 0 && !$pub){
						$message .= sprintf(g_l('modules_glossary', '[replace_deactivated]'), $this->Glossary->Text) . "\\n";
					}
					$message .= sprintf(g_l('modules_glossary', '[item_saved]'), $this->Glossary->Text);

					echo we_html_element::jsElement(
							$js .
							we_message_reporting::getShowMessageCall($message, we_message_reporting::WE_MESSAGE_NOTICE) . '
							if(top.makeNewEntryCheck==1) {
								' . $this->topFrame . '.we_cmd("new_glossary_' . $this->Glossary->Type . '", "' . $this->Glossary->Language . '");
							} else {
								' . $this->EditorHeaderFrame . '.location.reload();
							}
							' . $this->topFrame . '.hot=0;
						');

					// --> Save to Cache

					$Cache = new we_glossary_cache($this->Glossary->Language);
					$Cache->write();
					unset($Cache);

					// --> Save to Cache End
				}
				break;

			case "delete_glossary":

				if(!permissionhandler::hasPerm("DELETE_GLOSSARY")){
					echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return;
				}
				if($this->Glossary->delete()){
					echo we_html_element::jsElement('
								' . $this->topFrame . '.deleteEntry(' . $this->Glossary->ID . ');
								setTimeout(\'' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', ($this->Glossary->IsFolder == 1 ? '[group_deleted]' : '[item_deleted]')), we_message_reporting::WE_MESSAGE_NOTICE) . '\',500);
							');

					// --> Save to Cache

					$Cache = new we_glossary_cache($this->Glossary->Language);
					$Cache->write();
					unset($Cache);

					// --> Save to Cache End

					$this->Glossary = new we_glossary_glossary();
					$_REQUEST['home'] = 1;
					$_REQUEST['pnt'] = 'edbody';
				} else {
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR));
				}
				break;

			case "switchPage":
			default:
				break;
		}

		$_SESSION['weS']['glossary_session'] = $this->Glossary;
	}

	function processVariables(){
		if(isset($_SESSION['weS']['glossary_session'])){
			$this->Glossary = $_SESSION['weS']['glossary_session'];
		}
		$isPublished = we_base_request::_(we_base_request::BOOL, 'Published');
		if(is_array($this->Glossary->persistent_slots)){
			foreach($this->Glossary->persistent_slots as $val){
				if(isset($_REQUEST[$val])){
					if($val === 'Published'){
						if($this->Glossary->Published == 0 && $isPublished){
							$this->Glossary->Published = time();
						} elseif(!$isPublished){
							$this->Glossary->Published = 0;
						}
					} else {
						$this->Glossary->$val = $_REQUEST[$val];
					}
				}
			}
		}
		$this->page = we_base_request::_(we_base_request::INT, 'page', $this->page);
	}

}
