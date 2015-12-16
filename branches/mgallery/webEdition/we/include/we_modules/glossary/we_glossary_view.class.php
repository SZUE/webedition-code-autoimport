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
	private $page = 0;

	/**
	 * @param string $frameset
	 * @param string $topframe
	 */
	public function __construct($frameset){
		$topframe = "top.content";
		parent::__construct($frameset, $topframe);

		$this->Glossary = new we_glossary_glossary();
	}

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
WE().consts.g_l.glossary.view={
	save_changed_glossary:"' . g_l('modules_glossary', '[save_changed_glossary]') . '",
	delete_alert:	"' . g_l('modules_glossary', '[delete_alert]') . '",
	nothing_to_delete:"' . we_message_reporting::prepareMsgForJS(g_l('modules_glossary', '[nothing_to_delete]')) . '",
	no_perms:"' . we_message_reporting::prepareMsgForJS(g_l('modules_glossary', '[no_perms]')) . '",
	nothing_to_save:"' . we_message_reporting::prepareMsgForJS(g_l('modules_glossary', '[nothing_to_save]')) . '",
	no_workspace:"' . we_message_reporting::prepareMsgForJS(g_l('modules_glossary', '[no_workspace]')) . '",
};
var data={
	frameset:"' . $this->frameset . '",
};
parent.document.title = "' . $title . '";
') .
				we_html_element::jsScript(WE_JS_MODULES_DIR . 'glossary/glossary_view.js');
	}

	function getJSProperty(){
		return we_html_element::jsElement('
var data={
	frameset:"' . $this->frameset . '"
};
') . we_html_element::jsScript(WE_JS_MODULES_DIR . 'glossary/glossary_view_prop.js');
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
top.content.editor.edheader.location="' . $this->frameset . '&pnt=edheader&text=' . urlencode($this->Glossary->Text) . '";
top.content.editor.edfooter.location="' . $this->frameset . '&pnt=edfooter";
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
						'top.content.editor.edheader.location="' . $this->frameset . '&pnt=edheader&text=' . urlencode($this->Glossary->Text) . '";' .
						'top.content.editor.edfooter.location="' . $this->frameset . '&pnt=edfooter";');
				break;

			case 'populateWorkspaces':
				$objectLinkID = we_base_request::_(we_base_request::INT, 'link', 0, 'Attributes', 'ObjectLinkID');
				$_values = we_navigation_dynList::getWorkspacesForObject($objectLinkID);

				if($_values){
					$_js = '';

					foreach($_values as $_id => $_path){
						$_js .= 'top.content.editor.edbody.document.we_form.elements[\'link[Attributes][ObjectWorkspaceID]\'].options[top.content.editor.edbody.document.we_form.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length] = new Option("' . $_path . '",' . $_id . ');
							';
					}
					echo we_html_element::jsElement('top.content.populateWorkspaces("values");' . $_js);
				} elseif(we_navigation_dynList::getWorkspaceFlag($objectLinkID)){
					echo we_html_element::jsElement('top.content.populateWorkspaces("workspace");');
				} else {
					echo we_html_element::jsElement('top.content.populateWorkspaces("noWorkspace");');
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
								f('SELECT Published FROM ' . GLOSSARY_TABLE . " WHERE ID = " . intval($this->Glossary->ID)) :
								0);

				$isNew = $this->Glossary->ID == 0;

				if($this->Glossary->save()){
					$this->Glossary->Text = htmlentities($this->Glossary->Text, ENT_QUOTES);
					$this->Glossary->Title = htmlentities($this->Glossary->Title, ENT_QUOTES);

					if($isNew){
						$js = 'top.content.treeData.makeNewEntry(id:\'' . $this->Glossary->ID . '\',parentid:\'' . $this->Glossary->Language . '_' . $this->Glossary->Type . '\',text:\'' . $this->Glossary->Text . '\',open:0,contenttype:\'' . ($this->Glossary->IsFolder ? 'folder' : 'we/glossary') . '\',table:\'' . GLOSSARY_TABLE . '\',published:' . ($this->Glossary->Published > 0 ? 1 : 0) . '});
								top.content.drawTree();';
					} else {
						$js = 'top.content.treeData.updateEntry({id:' . $this->Glossary->ID . ',text:"' . $this->Glossary->Text . '",parentid:"' . $this->Glossary->Language . '_' . $this->Glossary->Type . '",published:' . ($this->Glossary->Published > 0 ? 1 : 0) . '});';
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
	top.content.we_cmd("new_glossary_' . $this->Glossary->Type . '", "' . $this->Glossary->Language . '");
} else {
	top.content.editor.edheader.location.reload();
}
top.content.hot=0;
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
top.content.treeData.deleteEntry(' . $this->Glossary->ID . ');
setTimeout(function(){' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', ($this->Glossary->IsFolder == 1 ? '[group_deleted]' : '[item_deleted]')), we_message_reporting::WE_MESSAGE_NOTICE) . '},500);
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

	public function getHomeScreen(){

		$_hidden = array(
			'cmd' => 'home',
			'pnt' => 'edbody',
			'name' => 'home',
			'value' => 0,
		);

		$_form = array(
			'name' => 'we_form',
		);

		$GLOBALS["we_head_insert"] = $this->getJSProperty();
		$GLOBALS["we_body_insert"] = we_html_element::htmlForm($_form, $this->getCommonHiddens($_hidden));
		$createAbbreviation = we_html_button::create_button("new_glossary_abbreviation", "javascript:top.opener.top.we_cmd('new_glossary_abbreviation');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_GLOSSARY"));
		$createAcronym = we_html_button::create_button("new_glossary_acronym", "javascript:top.opener.top.we_cmd('new_glossary_acronym');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_GLOSSARY"));
		$createForeignWord = we_html_button::create_button("new_glossary_foreignword", "javascript:top.opener.top.we_cmd('new_glossary_foreignword');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_GLOSSARY"));
		$createLink = we_html_button::create_button("new_glossary_link", "javascript:top.opener.top.we_cmd('new_glossary_link');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_GLOSSARY"));
		$createTextReplacement = we_html_button::create_button("new_glossary_textreplacement", "javascript:top.opener.top.we_cmd('new_glossary_textreplacement');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_GLOSSARY"));

		$content = $createAbbreviation . '<br/>' .
				$createAcronym . '<br/>' .
				$createForeignWord . '<br/>' .
				$createLink . '<br/>' .
				$createTextReplacement;

		return parent::getHomeScreen("glossary", "glossary.gif", $content);
	}

}
