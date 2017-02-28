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


class we_users_view extends we_modules_view{

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		if(isset($_SESSION['user_session_data'])){
			unset($_SESSION['user_session_data']);
		}

		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'users/users_view.js', '', ['id' => 'loadVarUsersView', 'data-users' => setDynamicVar([
					'modTitle' => $title,
					'cgroup' => ($_SESSION['user']['ID'] ? intval(f('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . $_SESSION['user']["ID"])) : 0)
		])]);
	}

	function getJSProperty(array $jsVars = []){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'users/users_property.js');
	}

	private function new_group(we_base_jsCmd $jscmd){
		if(!we_base_permission::hasPerm('NEW_GROUP')){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}

		$user_object = new we_users_user();

		if(($cgroup = we_base_request::_(we_base_request::INT, "cgroup"))){
			$user_group = new we_users_user();
			if($user_group->initFromDB($cgroup)){
				$user_object->ParentID = $cgroup;
			}
		}

		$user_object->initType(we_users_user::TYPE_USER_GROUP);

		$_SESSION["user_session_data"] = $user_object;

		$jscmd->addCmd('loadUsersContent');
	}

	private function new_alias(we_base_jsCmd $jscmd){
		if(!we_base_permission::hasPerm("NEW_USER")){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}

		$user_object = new we_users_user();

		if(($cgroup = we_base_request::_(we_base_request::INT, 'cgroup'))){
			$user_group = new we_users_user();
			if($user_group->initFromDB($cgroup)){
				$user_object->ParentID = $cgroup;
			}
		}

		$user_object->initType(we_users_user::TYPE_ALIAS);

		$_SESSION["user_session_data"] = $user_object;
		$jscmd->addCmd('loadUsersContent');
	}

	private function new_user(we_base_jsCmd $jscmd){
		if(!we_base_permission::hasPerm("NEW_USER")){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		$user_object = new we_users_user();

		if(($cgroup = we_base_request::_(we_base_request::INT, "cgroup"))){
			$user_group = new we_users_user();
			if($user_group->initFromDB($cgroup)){
				$user_object->ParentID = $cgroup;
			}
		}
		$user_object->initType(we_users_user::TYPE_USER);

		$_SESSION["user_session_data"] = $user_object;
		$jscmd->addCmd('loadUsersContent', ['oldtab' => 0]);
	}

	private function display_user(we_base_jsCmd $jscmd){
		if(!($uid = we_base_request::_(we_base_request::INT, 'uid'))){
			return;
		}
		$user_object = new we_users_user();
		$user_object->initFromDB($uid);
		if(!we_base_permission::hasPerm("ADMINISTRATOR") && $user_object->checkPermission("ADMINISTRATOR")){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			$user_object = new we_users_user();
			return;
		}

		$_SESSION["user_session_data"] = $user_object;

		$jscmd->addCmd('usetHot');
		if($user_object->Type == 1){
			$jscmd->addCmd('setCgroup', $user_object->ID);
		}
		$jscmd->addCmd('loadUsersContent', ['oldtab' => 0]);
	}

	private function save_user(we_base_jsCmd $jscmd){
		$weAcQuery = new we_selector_query();
		$ob = we_base_request::_(we_base_request::STRING, 'obj_name');
		$uname = we_base_request::_(we_base_request::STRING, $ob . '_username');
		if($uname && !we_users_user::filenameNotValid($uname)){
			$jscmd->addMsg(g_l('global', '[username_wrong_chars]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		if(!isset($_SESSION['user_session_data'])){
			$jscmd->addMsg(g_l('alert', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}

		if(($parent = we_base_request::_(we_base_request::INT, $ob . '_ParentID'))){
			$weAcResult = $weAcQuery->getItemById($parent, USER_TABLE, ['IsFolder'], false);
			if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
				$jscmd->addMsg(g_l('alert', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
				return;
			}
		}

		$alltables = array_filter([
			FILE_TABLE,
			TEMPLATES_TABLE,
			NAVIGATION_TABLE,
			defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : '',
			defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : ''
		]);

		foreach($alltables as $table){
			$i = 0;
			while(($wsp = we_base_request::_(we_base_request::INT, $ob . '_Workspace_' . $table . '_' . $i))){
				$weAcResult = $weAcQuery->getItemById($wsp, $table, ['IsFolder']);
				if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
					$jscmd->addMsg(g_l('modules_users', '[workspaceFieldError]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}
				$i++;
			}
		}

		$user_object = $_SESSION['user_session_data'];
		if(($oldtab = we_base_request::_(we_base_request::INT, 'oldtab')) !== false && ($opb = we_base_request::_(we_base_request::STRING, 'old_perm_branch')) !== false){//FIXME: is latter ever used?
			$user_object->preserveState($oldtab, $opb);
			$_SESSION['user_session_data'] = $user_object;
		}

		if(!we_base_permission::hasPerm('ADMINISTRATOR') && $user_object->checkPermission('ADMINISTRATOR')){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			$user_object = new we_users_user();
			return;
		}
		$oldperm = $user_object->checkPermission('ADMINISTRATOR');
		if(!$user_object){
			return;
		}

		if(!we_base_permission::hasPerm('SAVE_USER') && ($user_object->Type == we_users_user::TYPE_USER || $user_object->Type == we_users_user::TYPE_ALIAS) && $user_object->ID != 0){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		if(!we_base_permission::hasPerm("NEW_USER") && ($user_object->Type == we_users_user::TYPE_USER || $user_object->Type == we_users_user::TYPE_ALIAS) && $user_object->ID == 0){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		if(!we_base_permission::hasPerm("SAVE_GROUP") && $user_object->Type == we_users_user::TYPE_USER_GROUP && $user_object->ID != 0){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		if(!we_base_permission::hasPerm("NEW_GROUP") && $user_object->Type == we_users_user::TYPE_USER_GROUP && $user_object->ID == 0){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		if(($ot = we_base_request::_(we_base_request::INT, 'oldtab'))){
			$user_object->preserveState($ot, we_base_request::_(we_base_request::STRING, 'old_perm_branch'));
		}

		$id = $user_object->ID;
		if(!$user_object->username && $user_object->Type != we_users_user::TYPE_ALIAS){
			$jscmd->addMsg(g_l('modules_users', '[username_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}

		if($user_object->Alias == 0 && $user_object->Type == we_users_user::TYPE_ALIAS){
			$jscmd->addMsg(g_l('modules_users', '[username_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		$exist = (f('SELECT 1 FROM ' . USER_TABLE . ' WHERE ID!=' . intval($user_object->ID) . ' AND username="' . $user_object->username . '" LIMIT 1'));
		if($exist && $user_object->Type != we_users_user::TYPE_ALIAS){
			$jscmd->addMsg(sprintf(g_l('modules_users', '[username_exists]'), $user_object->username), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		if(($oldperm) && (!$user_object->checkPermission("ADMINISTRATOR")) && ($user_object->isLastAdmin())){
			$jscmd->addMsg(g_l('modules_users', '[modify_last_admin]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		$foo = ($user_object->ID ?
			getHash('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($user_object->ID), $user_object->DB_WE) :
			['ParentID' => 0]);

		$ret = $user_object->saveToDB();
		$_SESSION['user_session_data'] = $user_object;

		//	Save seem_startfile to DB when needed.
		if(($sid = we_base_request::_(we_base_request::INT, 'seem_start_file')) !== false){
			$uid = we_base_request::_(we_base_request::INT, 'uid');
			if($sid || (isset($_SESSION['save_user_seem_start_file'][$uid]))){
				$tmp = new DB_WE();

				if($sid !== false){
					//	save seem_start_file from REQUEST
					$seem_start_file = $sid;
					if($user_object->ID == $_SESSION['user']['ID']){ // change preferences if user edits his own startfile
						$_SESSION['prefs']['seem_start_file'] = $seem_start_file;
					}
				} else {
					//	Speichere seem_start_file aus SESSION
					$seem_start_file = $_SESSION['save_user_seem_start_file'][$uid];
				}

				$tmp->query('REPLACE INTO ' . PREFS_TABLE . ' SET userID=' . $uid . ',`key`="seem_start_file",`value`="' . $tmp->escape($seem_start_file) . '"');
				unset($tmp);
				unset($seem_start_file);
				if(isset($_SESSION['save_user_seem_start_file'][$uid])){
					unset($_SESSION['save_user_seem_start_file'][$uid]);
				}
			}
		}

		if($ret == we_users_user::ERR_USER_PATH_NOK){
			$jscmd->addMsg(g_l('modules_users', '[user_path_nok]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		$jscmd->addCmd('usetHot');
		if($id){
			$jscmd->addCmd('updateTreeEntry', ['id' => $user_object->ID, 'parentid' => $user_object->ParentID, 'text' => $user_object->Text, 'class' => ($user_object->checkPermission('ADMINISTRATOR') ? 'bold ' : '') . ($user_object->LoginDenied ? 'red' : '')]);
		} else {
			$jscmd->addCmd('makeTreeEntry', ['id' => $user_object->ID, 'parentid' => $user_object->ParentID, 'text' => $user_object->Text, 'open' => false, 'contenttype' => (($user_object->Type == we_users_user::TYPE_USER_GROUP) ? 'we/userGroup' : (($user_object->Type == we_users_user::TYPE_ALIAS) ? 'we/alias' : 'we/user')),
				'table' => USER_TABLE, 'published' => ($user_object->LoginDenied ? 0 : 1), 'class' => ($user_object->checkPermission('ADMINISTRATOR') ? 'bold ' : '')]);
		}

		switch($user_object->Type){
			case we_users_user::TYPE_ALIAS:
				$jscmd->addMsg(sprintf(g_l('modules_users', '[alias_saved_ok]'), $user_object->Text), we_message_reporting::WE_MESSAGE_NOTICE);
				break;
			case we_users_user::TYPE_USER_GROUP:
				$jscmd->addMsg(sprintf(g_l('modules_users', '[group_saved_ok]'), $user_object->Text), we_message_reporting::WE_MESSAGE_NOTICE);
				break;
			case we_users_user::TYPE_USER:
			default:
				$jscmd->addMsg(sprintf(g_l('modules_users', '[user_saved_ok]'), $user_object->Text), we_message_reporting::WE_MESSAGE_NOTICE);
				break;
		}

		if($user_object->Type == we_users_user::TYPE_USER){
			$jscmd->addCmd('setCgroup', $user_object->ParentID);
		}
		$jscmd->addCmd('updateTitle', $user_object->Path);
		echo $jscmd->getCmds() . we_html_element::jsElement($ret);
	}

	private function delete_user(we_base_jsCmd $jscmd){
		if(empty($_SESSION['user_session_data'])){
			return;
		}

		$user_object = $_SESSION['user_session_data'];

		if($user_object->ID == $_SESSION['user']['ID']){
			$jscmd->addMsg(g_l('modules_users', '[delete_user_same]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}

		if(we_users_util::isUserInGroup($_SESSION['user']['ID'], $user_object->ID)){
			$jscmd->addMsg(g_l('modules_users', '[delete_group_user_same]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}

		if(!we_base_permission::hasPerm('ADMINISTRATOR') && $user_object->checkPermission("ADMINISTRATOR")){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			$user_object = new we_users_user();
			return;
		}
		if(!we_base_permission::hasPerm("DELETE_USER") && $user_object->Type == we_users_user::TYPE_USER){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		if(!we_base_permission::hasPerm("DELETE_GROUP") && $user_object->Type == we_users_user::TYPE_USER_GROUP){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}

		if(isset($GLOBALS["user"]) && $user_object->Text == $GLOBALS["user"]["Username"]){
			$jscmd->addMsg(g_l('alert', '[user_same]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}

		if($user_object->checkPermission("ADMINISTRATOR")){
			if($user_object->isLastAdmin()){
				$jscmd->addMsg(g_l('modules_users', '[modify_last_admin]'), we_message_reporting::WE_MESSAGE_ERROR);
				return;
			}
		}

		switch($user_object->Type){
			case we_users_user::TYPE_USER_GROUP:
				$question = sprintf(g_l('modules_users', '[delete_alert_group]'), $user_object->Text);
				break;
			case we_users_user::TYPE_ALIAS:
				$question = sprintf(g_l('modules_users', '[delete_alert_alias]'), $user_object->Text);
				break;
			case we_users_user::TYPE_USER:
			default:
				$question = sprintf(g_l('modules_users', '[delete_alert_user]'), $user_object->Text);
				break;
		}
		echo we_html_element::jsElement('
		if(window.confirm("' . $question . '")){
			top.content.cmd.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=do_delete";
		}');
	}

	private function do_delete(we_base_jsCmd $jscmd){
		if(empty($_SESSION['user_session_data'])){
			return;
		}
		$user_object = $_SESSION["user_session_data"];
		if(!we_base_permission::hasPerm('DELETE_USER') && $user_object->Type == we_users_user::TYPE_USER){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		if(!we_base_permission::hasPerm('DELETE_GROUP') && $user_object->Type == we_users_user::TYPE_USER_GROUP){
			$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		if($user_object->deleteMe()){
			$cmd = new we_base_jsCmd();
			$cmd->addCmd('deleteTreeEntry', $user_object->ID);
			$cmd->addCmd('loadUsersContent', ['home' => 1]);

			unset($_SESSION["user_session_data"]);
		}
	}

	private function check_user_display(we_base_jsCmd $jscmd){
		if(($uid = we_base_request::_(we_base_request::INT, 'uid'))){
			$mpid = f('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION['user']['ID']), '', $this->db);
			$pid = f('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . $uid, '', $this->db);

			$search = $first = true;
			$found = false;

			if(!we_base_permission::hasPerm('ADMINISTRATOR')){
				while($search){
					if($mpid == $pid){
						$search = false;
						if(!$first){
							$found = true;
						}
					}
					$first = false;
					if($pid == 0){
						$search = false;
					}
					$pid = intval(f('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($pid), '', $this->db));
				}
			}

			if($found || we_base_permission::hasPerm('ADMINISTRATOR')){
				$jscmd->addCmd('display_user', $uid);
			}else{
				$jscmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
			}
		}
	}

	function processCommands(we_base_jsCmd $jscmd){
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			case 'new_group':
				return $this->new_group($jscmd);
			case 'new_alias':
				return $this->new_alias($jscmd);
			case 'new_user':
				return $this->new_user($jscmd);
			case 'display_user':
				return $this->display_user($jscmd);
			case 'save_user':
				return $this->save_user($jscmd);
			case 'delete_user':
				return $this->delete_user($jscmd);
			case 'do_delete':
				return $this->do_delete($jscmd);
			case 'check_user_display':
				return $this->check_user_display($jscmd);
		}
	}

	function processVariables(){
		if(($page = we_base_request::_(we_base_request::INT, 'page')) !== false){
			$this->page = $page;
		}
	}

	public function getHomeScreen(){
		$content = we_html_button::create_button('fat:create_user,fa-lg fa-user-plus', "javascript:top.we_cmd('new_user');", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_USER")) .
			we_html_button::create_button('fat:create_group,fa-lg fa-users,fa-plus', "javascript:top.we_cmd('new_group');", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_GROUP")) .
			we_html_button::create_button('fat:create_alias,alias fa-lg fa-user-plus', "javascript:top.we_cmd('new_alias');", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_ALIAS"));

		return parent::getActualHomeScreen('users', "user.gif", $content);
	}

}
