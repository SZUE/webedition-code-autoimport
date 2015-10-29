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
function we_tag_sessionStart($attribs){
	$GLOBALS['WE_SESSION_START'] = true;

	if(!isset($_SESSION)){
		new we_base_sessionHandler();
	}

	if(!defined('CUSTOMER_TABLE')){
		return '';
	}

	if(!empty($_REQUEST['we_webUser_logout'])){
		if(!empty($_SESSION['webuser']['registered']) && !empty($_SESSION['webuser']['ID'])){
			if(( (isset($_REQUEST['s']['AutoLogin']) && !$_REQUEST['s']['AutoLogin']) || (isset($_SESSION['webuser']['AutoLogin']) && !$_SESSION['webuser']['AutoLogin'])) && isset($_SESSION['webuser']['AutoLoginID'])){
				$GLOBALS['DB_WE']->query('DELETE FROM ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($_SESSION['webuser']['AutoLoginID'])) . '"');
				setcookie('_we_autologin', '', (time() - 3600), '/');
			}
			$GLOBALS['WE_LOGOUT'] = true;
			unset($_SESSION['s'], $_REQUEST['s']);
			if(SECURITY_DELETE_SESSION){
				we_base_sessionHandler::makeNewID(true);
			}
			$_SESSION['webuser'] = array('registered' => false);
		}
		return '';
	}

	if(isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->InWebEdition && we_base_request::_(we_base_request::BOOL, 'we_set_registeredUser')){
		$_SESSION['weS']['we_set_registered'] = $_REQUEST['we_set_registeredUser'];
	}

	$SessionAutologin = 0;

	if((empty($GLOBALS['we_editmode']))){
		if(!isset($_SESSION['webuser'])){
			$_SESSION['webuser'] = array(
				'registered' => false
			);
		}
		$persistentlogins = weTag_getAttribute('persistentlogins', $attribs, false, we_base_request::BOOL);
		if(!$_SESSION['webuser']['registered'] && isset($_REQUEST['s']['Username']) && isset($_REQUEST['s']['Password']) && !(isset($_REQUEST['s']['ID'])) && !isset($_REQUEST['s']['Password2'])//if set, we assume it is a password reset or use of an forgotten password routine, so we don't try to do an login
		){
			$GLOBALS['DB_WE']->query('DELETE FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblWebUser" AND LoginDate < DATE_SUB(NOW(), INTERVAL ' . we_base_constants::LOGIN_FAILED_HOLDTIME . ' DAY)');
			$hook = new weHook('customer_preLogin', '', array('customer' => &$_REQUEST['s'], 'type' => 'normal', 'tagname' => 'sessionStart'));
			$hook->executeHook();

			if(!wetagsessionStartdoLogin($persistentlogins, $SessionAutologin)){
				wetagsessionHandleFailedLogin();
			} else {
				$GLOBALS['DB_WE']->query('UPDATE ' . FAILED_LOGINS_TABLE . ' SET isValid="false" WHERE UserTable="tblWebUser" AND Username="' . $GLOBALS['DB_WE']->escape($_REQUEST['s']['Username']) . '"');
				//change session ID to prevent session
				we_base_sessionHandler::makeNewID();
				$hook = new weHook('customer_Login', '', array('customer' => &$_SESSION['webuser'], 'type' => 'normal', 'tagname' => 'sessionStart'));
				$hook->executeHook();
			}
			unset($_REQUEST['s']['Password']);
		}
		if($persistentlogins && (!$_SESSION['webuser']['registered']) && isset($_COOKIE['_we_autologin'])){
			if(!wetagsessionStartdoAutoLogin()){
				wetagsessionHandleFailedLogin();
			} else {
				we_base_sessionHandler::makeNewID();
			}
		}

		if(!empty($_SESSION['webuser']['registered']) && !empty($_SESSION['webuser']['ID']) && !empty($_SESSION['webuser']['Username'])){
			if($_SESSION['webuser']['LastAccess'] + 60 < time()){
				$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET LastAccess=UNIX_TIMESTAMP() WHERE ID=' . intval($_SESSION['webuser']['ID']));
				$_SESSION['webuser']['LastAccess'] = time();
			}
		}
	}

	if(!empty($_SESSION['webuser']['registered']) && weTag_getAttribute('onlinemonitor', $attribs, false, we_base_request::BOOL)){
		$GLOBALS['DB_WE']->query('DELETE FROM ' . CUSTOMER_SESSION_TABLE . ' WHERE LastAccess<DATE_SUB(NOW(), INTERVAL 1 HOUR)');
		$monitorgroupfield = weTag_getAttribute('monitorgroupfield', $attribs, '', we_base_request::STRING);
		$doc = we_getDocForTag(weTag_getAttribute('monitordoc', $attribs, '', we_base_request::STRING), false);

		$WebUserID = ($_SESSION['webuser']['registered'] ? $_SESSION['webuser']['ID'] : 0);
		$WebUserGroup = ($_SESSION['webuser']['registered'] && $monitorgroupfield ? $_SESSION['webuser'][$monitorgroupfield] : 'we_guest');

		$GLOBALS['DB_WE']->query('INSERT INTO ' . CUSTOMER_SESSION_TABLE . ' SET ' .
			we_database_base::arraySetter(array(
				'SessionID' => session_id(),
				'SessionIp' => $_SERVER['REMOTE_ADDR'] ? : '',
				'WebUserID' => $WebUserID,
				'WebUserGroup' => $WebUserGroup,
				'WebUserDescription' => '',
				'Browser' => isset($_SERVER['HTTP_USER_AGENT']) ? : '',
				'Referrer' => isset($_SERVER['HTTP_REFERER']) ? oldHtmlspecialchars((string) $_SERVER['HTTP_REFERER']) : '',
				'LastLogin' => sql_function('NOW()'),
				'PageID' => $doc->ID,
				'SessionAutologin' => $SessionAutologin
			)) . ' ON DUPLICATE KEY UPDATE ' . we_database_base::arraySetter(array(
				'PageID' => $doc->ID,
				'WebUserID' => $WebUserID,
				'WebUserGroup' => $WebUserGroup,
				'WebUserDescription' => '',
		)));
	}
	//remove sessions consisting only of webuser[registered]
	if(!empty($_SESSION['webuser']) && count($_SESSION['webuser']) == 1){
		unset($_SESSION['webuser']);
	}
	return '';
}

function wetagsessionHandleFailedLogin(){
	$_SESSION['webuser'] = array(
		'registered' => false, 'loginfailed' => we_users_user::INVALID_CREDENTIALS
	);
	if(!isset($GLOBALS['WE_LOGIN_DENIED'])){
		we_users_user::logLoginFailed('tblWebUser', $_REQUEST['s']['Username']);
	}
	sleep(SECURITY_DELAY_FAILED_LOGIN);
	//make it harder to guess salt/password
	usleep(rand(0, 1000000));


	if(
		intval(f('SELECT COUNT(1)  FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblWebUser" AND Username="' . $GLOBALS['DB_WE']->escape($_REQUEST['s']['Username']) . '" AND isValid="true" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . ' hour)')) >= intval(SECURITY_LIMIT_CUSTOMER_NAME) ||
		intval(f('SELECT COUNT(1) FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblWebUser" AND IP="' . $_SERVER['REMOTE_ADDR'] . '" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_IP_HOURS) . ' hour)')) >= intval(SECURITY_LIMIT_CUSTOMER_IP)
	){
		//don't serve user
		if(SECURITY_LIMIT_CUSTOMER_REDIRECT){
			$_SESSION['webuser']['loginfailed'] = we_users_user::MAX_LOGIN_COUNT_REACHED;
			unset($_REQUEST['s']);
			if(($path = id_to_path(SECURITY_LIMIT_CUSTOMER_REDIRECT, FILE_TABLE))){
				include(WEBEDITION_PATH . '../' . $path);
				exit();
			}
		}
		echo CheckAndConvertISOfrontend('Dear customer, our service is currently not available. Please try again later. Thank you.<br/>' .
			'Sehr geehrter Kunde, aus Sicherheitsgründen ist ein Login derzeit nicht möglich! Bitte probieren Sie es später noch ein mal. Vielen Dank');

		exit();
	}
}

/* Function made available to check externally if userlogin is denied */

function wetagsessionStartCheckDenied(){
	if(
		intval(f('SELECT COUNT(1) FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblWebUser" AND Username="' . $GLOBALS['DB_WE']->escape($_REQUEST['s']['Username']) . '" AND isValid="true" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . ' hour)')) >= intval(SECURITY_LIMIT_CUSTOMER_NAME) ||
		intval(f('SELECT COUNT(1) FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblWebUser" AND IP="' . $_SERVER['REMOTE_ADDR'] . '" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_IP_HOURS) . ' hour)')) >= intval(SECURITY_LIMIT_CUSTOMER_IP)
	){
		$GLOBALS['WE_LOGIN_DENIED'] = true;
		return true;
	}
	return false;
}

function wetagsessionStartdoLogin($persistentlogins, &$SessionAutologin, $externalPasswordCheck = false){ //FIXME: check for last time =>(cuncurrent logins)
	if($_REQUEST['s']['Username'] && $_REQUEST['s']['Password']){
		if(wetagsessionStartCheckDenied()){
			return false;
		}
		$wasRegistered = empty($_SESSION['webuser']['registered']) ? false : $_SESSION['webuser']['registered'];
		$u = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE Password!="" AND LoginDenied=0 AND Username="' . $GLOBALS['DB_WE']->escape($_REQUEST['s']['Username']) . '"', null, MYSQL_ASSOC);
		if($u && ($externalPasswordCheck || we_customer_customer::comparePassword($u['Password'], $_REQUEST['s']['Password']))){
			if((SECURITY_SESSION_PASSWORD & we_customer_customer::STORE_DBPASSWORD) == 0){
				unset($u['Password']);
			}

			$_SESSION['webuser'] = array_merge($u, we_customer_customer::getEncryptedFields());
			//keep Password if known
			if(SECURITY_SESSION_PASSWORD & we_customer_customer::STORE_PASSWORD){
				$_SESSION['webuser']['_Password'] = $_REQUEST['s']['Password'];
			}
			$_SESSION['webuser']['registered'] = true;
			$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET LastLogin=UNIX_TIMESTAMP() WHERE ID=' . intval($_SESSION['webuser']['ID']));

			if($persistentlogins && !empty($_REQUEST['s']['AutoLogin']) && $_SESSION['webuser']['AutoLoginDenied'] != 1){
				$_SESSION['webuser']['AutoLoginID'] = uniqid(hexdec(substr(session_id(), 0, 8)), true);
				$GLOBALS['DB_WE']->query('INSERT INTO ' . CUSTOMER_AUTOLOGIN_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'AutoLoginID' => sha1($_SESSION['webuser']['AutoLoginID']),
						'WebUserID' => $_SESSION['webuser']['ID'],
						'LastIp' => $_SERVER['REMOTE_ADDR']
				)));

				setcookie('_we_autologin', $_SESSION['webuser']['AutoLoginID'], (time() + CUSTOMER_AUTOLOGIN_LIFETIME), '/');
				$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET AutoLogin=1 WHERE ID=' . intval($_SESSION['webuser']['ID']));
				$_SESSION['webuser']['AutoLogin'] = 1;
				$SessionAutologin = 1;
			}
			$GLOBALS['WE_LOGIN'] = !$wasRegistered;
			return true;
		}
	}
	return false;
}

function wetagsessionStartdoAutoLogin(){
	$autologinSeek = $_COOKIE['_we_autologin'];
	if(!empty($autologinSeek)){
		$hook = new weHook('customer_preLogin', '', array('customer' => &$_REQUEST['s'], 'type' => 'autoLogin', 'tagname' => 'sessionStart'));
		$hook->executeHook();

		$wasRegistered = $_SESSION['webuser']['registered'];
		$u = getHash('SELECT u.* FROM ' . CUSTOMER_TABLE . ' u JOIN ' . CUSTOMER_AUTOLOGIN_TABLE . ' c ON u.ID=c.WebUserID WHERE u.LoginDenied=0 AND u.AutoLoginDenied=0 AND u.Password!="" AND c.AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($autologinSeek)) . '"', null, MYSQL_ASSOC);
		if($u){
			if((SECURITY_SESSION_PASSWORD & we_customer_customer::STORE_DBPASSWORD) == 0){
				unset($u['Password']);
			}

			$_SESSION['webuser'] = array_merge($u, we_customer_customer::getEncryptedFields());
			//try to decrypt password if possible
			if(SECURITY_SESSION_PASSWORD & we_customer_customer::STORE_PASSWORD){
				$_SESSION['webuser']['_Password'] = we_customer_customer::decryptData($_SESSION['webuser']['Password']);
			}
			$_SESSION['webuser']['registered'] = true;
			$_SESSION['webuser']['AutoLoginID'] = uniqid(hexdec(substr(session_id(), 0, 8)), true);
			$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_AUTOLOGIN_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'AutoLoginID' => sha1($_SESSION['webuser']['AutoLoginID']),
					'LastIp' => $_SERVER['REMOTE_ADDR'],
				)) . ' WHERE WebUserID=' . intval($_SESSION['webuser']['ID']) . ' AND AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($autologinSeek)) . '"'
			);

			setcookie('_we_autologin', $_SESSION['webuser']['AutoLoginID'], (time() + CUSTOMER_AUTOLOGIN_LIFETIME), '/');
			$GLOBALS['WE_LOGIN'] = $wasRegistered;
			$hook = new weHook('customer_Login', '', array('customer' => &$_SESSION['webuser'], 'type' => 'autoLogin', 'tagname' => 'sessionStart'));
			$hook->executeHook();
			return true;
		}
	}

	return false;
}
