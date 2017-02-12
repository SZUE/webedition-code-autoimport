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
//FIXME: handle to start different session if name doesn't match!

abstract class we_main_session{

	public static function initSession(){
		if(!isset($_SESSION)){
			session_name(SESSION_NAME);
			new we_base_sessionHandler();
		}

		if(!isset($_SESSION['weS'])){
			$_SESSION['weS'] = [];
			$_SESSION['user'] = ['ID' => '', 'Username' => '', 'workSpace' => '', 'isWeSession' => false];
		}

		if(!isset($_SESSION['user'])){
			$_SESSION['user'] = ['ID' => '', 'Username' => '', 'workSpace' => '', 'isWeSession' => false];
		}

		//FIXME: if this is not set, nothing work in WE!!!
		$GLOBALS['we_transaction'] = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', md5(uniqID('', true)));

		if(!isset($_SESSION['weS']['we_data'])){
			$_SESSION['weS']['we_data'] = [$GLOBALS['we_transaction'] => ''];
		}

		$_SESSION['weS']['EditPageNr'] = (isset($_SESSION['weS']['EditPageNr']) && (($_SESSION['weS']['EditPageNr'] != '') || ($_SESSION['weS']['EditPageNr'] == 0))) ? $_SESSION['weS']['EditPageNr'] : 1;

		//if this var is not given, no login dialog was shown, we don't need to call the hook
		if(empty($_POST['WE_LOGIN_do'])){
			return;
		}
		$DB_WE = $GLOBALS['DB_WE'];

		$checkUserPassword = true;
		$hook = new we_hook_base('user_preLogin', '', [
			'user' => [
				'Username' => &$_POST['WE_LOGIN_username'],
				'password' => &$_POST['WE_LOGIN_password'],
			],
			'checkPassword' => &$checkUserPassword,
			'type' => 'login'
		]);
		$hookRet = $hook->executeHook();

		if(!$hookRet || !(isset($_POST['WE_LOGIN_username']) && isset($_POST['WE_LOGIN_password']))){
			return;
		}

// only if username exists !!
		if(
			!($userdata = getHash('SELECT passwd,username,LoginDenied,ID FROM ' . USER_TABLE . ' WHERE IsFolder=0 AND username="' . $DB_WE->escape($_POST['WE_LOGIN_username']) . '"')) ||
			($checkUserPassword && !we_users_user::comparePasswords($_POST['WE_LOGIN_username'], $userdata['passwd'], $_POST['WE_LOGIN_password']))
		){
			we_base_sessionHandler::makeNewID(true);
			return;
		}

		if($userdata['LoginDenied']){ // userlogin is denied
			$GLOBALS['userLoginDenied'] = true;
			we_base_sessionHandler::makeNewID(true);
			return;
		}

		if($checkUserPassword && !preg_match('|^\$([^$]{2,4})\$([^$]+)\$(.+)$|', $userdata['passwd'])){ //will cause update on old php-versions every time. since md5 doesn't cost much, ignore this.
			$salted = we_users_user::makeSaltedPassword($_POST['WE_LOGIN_password']);
			// UPDATE Password with SALT
			$DB_WE->query('UPDATE ' . USER_TABLE . ' SET passwd="' . $DB_WE->escape($salted) . '" WHERE IsFolder=0 AND username="' . $DB_WE->escape($_POST["WE_LOGIN_username"]) . '" AND ID=' . $userdata['ID']);
		}

		if(!preg_match('/' . addcslashes(SECURITY_USER_PASS_REGEX, '/') . '/', $_POST['WE_LOGIN_password'])){
			$_SESSION['WE_USER_PASSWORD_NOT_SUFFICIENT'] = 1;
		}

		if(!(isset($_SESSION['user']) && is_array($_SESSION['user']))){
			$_SESSION['user'] = [];
		}

		$_SESSION['user']['Username'] = $userdata['username'];
		$_SESSION['user']['ID'] = $userdata['ID'];

		if($_SESSION['user']['Username'] && $_SESSION['user']['ID']){
			$_SESSION['prefs'] = we_users_user::readPrefs($userdata['ID'], $GLOBALS['DB_WE'], true);
			$_SESSION['perms'] = we_users_user::getAllPermissions($_SESSION['user']['ID']);
			we_users_user::setEffectiveWorkspaces($_SESSION['user']['ID'], $GLOBALS['DB_WE']);
		}

		$_SESSION['user']['isWeSession'] = true;

		we_base_sessionHandler::makeNewID();

		$hook = new we_hook_base('user_Login', '', [
			'user' => &$_SESSION['user'],
			'perms' => &$_SESSION['perms'],
			'prefs' => &$_SESSION['prefs'],
			'type' => 'login'
		]);

		$hookRet = $hook->executeHook();
	}

	public static function logout(){
		$db = $GLOBALS['DB_WE'];
		$db->query('UPDATE ' . LOCK_TABLE . ' SET sessionID=x\'00\',UserID=releaseRequestID WHERE releaseRequestID IS NOT NULL AND releaseRequestID!=UserID AND UserID=' . intval($_SESSION['user']['ID']) . ' AND sessionID=x\'' . session_id() . '\'');

		$db->query('DELETE FROM ' . LOCK_TABLE . ' WHERE UserID=' . intval($_SESSION['user']['ID']) . ' AND sessionID=x\'' . session_id() . '\'');
//FIXME: table is set to false value, if 2 sessions are open; but this is updated shortly - so ignore it now
//TODO: update to time if still locked files open
		$db->query('UPDATE ' . USER_TABLE . ' SET Ping=NULL WHERE ID=' . intval($_SESSION['user']['ID']));

		we_base_file::cleanTempFiles(true);

//	getJSCommand
		/* $path = (isset($_SESSION['weS']['SEEM']['startId']) ? // logout from webEdition opened with tag:linkToSuperEasyEditMode
		  $_SESSION['weS']['SEEM']['startPath'] :
		  WEBEDITION_DIR);
		 */

		we_base_sessionHandler::makeNewID(true);

		if(!isset($GLOBALS['isIncluded']) || !$GLOBALS['isIncluded']){
			echo we_html_tools::getHtmlTop() . we_html_element::jsScript(JS_DIR . 'logout.js');
		}
	}

	public static function loggingOut(){
		$GLOBALS['isIncluded'] = true;
		self::logout();

		if(we_base_request::_(we_base_request::BOOL, 'isopener')){
			header('location: ' . WEBEDITION_DIR . 'index.php');
		}

		echo we_html_tools::getHtmlTop('', '', '', '', '
	<body onload="self.setTimeout(self.close, 1000);" style="background-color:#386AAB;color:white">
		' . g_l('global', '[irregular_logout]') . '
	</body>');
	}

}
