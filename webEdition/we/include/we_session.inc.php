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

if(!isset($_SESSION)){
	session_name(SESSION_NAME);
	new we_base_sessionHandler();
}

if(!isset($_SESSION['weS'])){
	$_SESSION['weS'] = [];
	$_SESSION['user'] = array(
		'ID' => '', 'Username' => '', 'workSpace' => '', 'isWeSession' => false
	);
}

if(!isset($_SESSION['user'])){
	$_SESSION['user'] = array(
		'ID' => '', 'Username' => '', 'workSpace' => '', 'isWeSession' => false
	);
}

$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', md5(uniqID('', true)));

if(!isset($_SESSION['weS']['we_data'])){
	$_SESSION['weS']['we_data'] = array($we_transaction => '');
}

$_SESSION['weS']['EditPageNr'] = (isset($_SESSION['weS']['EditPageNr']) && (($_SESSION['weS']['EditPageNr'] != '') || ($_SESSION['weS']['EditPageNr'] == 0))) ? $_SESSION['weS']['EditPageNr'] : 1;

if(!(isset($_POST['WE_LOGIN_username']) && isset($_POST['WE_LOGIN_password']))){
	return;
}

//check if we have utf-8 -> Login-Screen is always utf-8
/* if($GLOBALS['WE_BACKENDCHARSET'] !== 'UTF-8'){
  $_POST['WE_LOGIN_username'] = utf8_decode($_POST['WE_LOGIN_username']);
  $_POST['WE_LOGIN_password'] = utf8_decode($_POST['WE_LOGIN_password']);
  } */

$userdata = getHash('SELECT passwd,username,LoginDenied,ID FROM ' . USER_TABLE . ' WHERE IsFolder=0 AND username="' . $DB_WE->escape($_POST['WE_LOGIN_username']) . '"');

// only if username exists !!
if(!$userdata || (!we_users_user::comparePasswords($_POST['WE_LOGIN_username'], $userdata['passwd'], $_POST['WE_LOGIN_password']))){
	we_base_sessionHandler::makeNewID(true);
	return;
}

if($userdata['LoginDenied']){ // userlogin is denied
	$GLOBALS['userLoginDenied'] = true;
	we_base_sessionHandler::makeNewID(true);
	return;
}

if(!preg_match('|^\$([^$]{2,4})\$([^$]+)\$(.+)$|', $userdata['passwd'])){ //will cause update on old php-versions every time. since md5 doesn't cost much, ignore this.
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
unset($userdata);
we_base_sessionHandler::makeNewID();
