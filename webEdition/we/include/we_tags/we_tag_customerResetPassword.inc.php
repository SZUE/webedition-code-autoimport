<?php

/**
 * webEdition CMS
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
function checkPwds(){
	$pwd = weRequest('string', 's', '', 'Password');
	if(!$pwd){
		$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_FIELD_NOT_SET;
		return false;
	}
	if($pwd != weRequest('string', 's', '', 'Password2')){
		$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_NOT_MATCH;
		return false;
	}
	return true;
}

function checkRequired($required, $emailfield = ''){
	if(!$required){
		return false;
	}
	$where = array();
	foreach($required as $cur){
		if(($var = weRequest('string', 's', false, $cur))){
			$where[] = '`' . $GLOBALS['DB_WE']->escape($cur) . '`="' . $GLOBALS['DB_WE']->escape($var) . '"';
		} else {
			$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_FIELD_NOT_SET;
			return;
		}
	}
	if(($uid = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE ' . implode(' AND ', $where)))){
		$_SESSION['webuser'] = getHash('SELECT ID,Username,Anrede_Anrede,Anrede_Titel,Forename,Surname' . ($emailfield ? ',' . $GLOBALS['DB_WE']->escape($emailfield) : '') . ' FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . $uid);
		return $uid;
	}

	$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_NO_SUCH_USER;
	return false;
}

function we_tag_customerResetPassword(array $attribs){
	if(($foo = attributFehltError($attribs, 'type', __FUNCTION__))){
		return $foo;
	}
	if(!(defined('CUSTOMER_TABLE') && isset($_REQUEST['s']))){
		return;
	}
//cleanup table
	$GLOBALS['DB_WE']->query('DELETE FROM ' . PWDRESET_TABLE . ' WHERE expires<NOW');

	$required = array_unique(explode(',', weTag_getAttribute('required', $attribs)));

//set dates
	we_util::convertDateInRequest($_REQUEST['s'], false);

	switch($type = weTag_getAttribute('type', $attribs)){
		default:
			return parseError('no type set');
		case 'direct':
			if(count($required) < 2){
				return parseError('For security reasons: in direct mode, attribute <b>required</b> needs at least two different fields!');
			}
			if(!checkPwds() || !($uid = checkRequired($required))){
				return;
			}

			$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter(array('Password' => we_customer_customer::cryptPassword(weRequest('string', 's', '', 'Password')))) . ' WHERE ID=' . $uid);
			$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_ALL_OK;
//finally remove any entries of failed logins so the user can login
			$GLOBALS['DB_WE']->query('UPDATE ' . FAILED_LOGINS_TABLE . ' SET isValid="false" WHERE UserTable="tblWebUser" AND Username="' . $GLOBALS['DB_WE']->escape($_SESSION['webuser']['Username']) . '"');
//what about IP blocks?!
			break;
		case 'email'://no preset password
		case 'emailPassword'://preset Password
			if(count($required) < 1){
				return parseError('For security reasons: in email mode, attribute <b>required</b> needs at least one field!');
			}
			$customerEmailField = weTag_getAttribute('customerEmailField', $attribs);
			if(($type == 'emailPassword' && !checkPwds()) || !($uid = checkRequired($required, $customerEmailField))){
				return;
			}
			$pwd = weRequest('string', 's', '', 'Password');
			$_SESSION['webuser']['token'] = substr(md5(uniqid('', true)), 0, 25);
			$GLOBALS['DB_WE']->query('REPLACE INTO ' . PWDRESET_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'ID' => $uid,
					'UserTable' => 'tblWebUser',
					'password' => $pwd ? we_customer_customer::cryptPassword($pwd) : '',
					'expires' => sql_function('NOW()+ INTERVAL ' . intval(weTag_getAttribute('expireToken', $attribs, 3600)) . ' SECOND'),
					'token' => $_SESSION['webuser']['token']
			)));
			$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_ALL_OK;

			break;
		case 'resetFromMail':
			//if optional required field is given, check them
			if($required && !checkRequired($required)){
				return;
			}
			$user = weRequest('int', 'user');
			//check token && if password present; expired logins are already deleted
			$data = getHash('SELECT ID,password FROM ' . PWDRESET_TABLE . ' WHERE UserTable="tblWebUser" AND ID=' . $user . ' AND token="' . $GLOBALS['DB_WE']->escape(weRequest('string', 'token')) . '"');

			if(!$data){
				$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_TOKEN_INVALID;
			}
			//if no pwd is set, check if passwords are given by request
			if(!$data['password']){
				if(!checkPwds()){
					return;
				}
				//set password from request, pwd in db was empty
				$data['password'] = we_customer_customer::cryptPassword(weRequest('string', 's', '', 'Password'));
			}
			//ok, we have a password, all (optional requirements are met) & token was valid
			$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET Password="' . $GLOBALS['DB_WE']->escape($data['password']) . '" WHERE ID=' . $user);
			$GLOBALS['DB_WE']->query('DELETE FROM ' . PWDRESET_TABLE . ' WHERE UserTable="tblWebUser" AND ID=' . $user);

			$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_ALL_OK;
			break;
	}
}
