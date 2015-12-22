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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function checkPwds($pwRegex){
	$pwd = we_base_request::_(we_base_request::RAW, 's', '', 'Password');
	if(!$pwd){
		$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_FIELD_NOT_SET;
		return false;
	}
	if($pwd != we_base_request::_(we_base_request::RAW, 's', '', 'Password2')){
		$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_NOT_MATCH;
		return false;
	}
	if($pwRegex && !preg_match('/' . preg_quote($pwRegex, '/') . '/', $pwd)){
		$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_NOT_SUFFICIENT;
		return false;
	}

	return true;
}

function checkRequired(array $required, array $loadFields, $emailfield = ''){
	if(!$required){
		return false;
	}
	$where = array();
	foreach($required as $cur){
		if(($var = we_base_request::_(we_base_request::STRING, 's', false, $cur))){
			$where[] = '`' . $GLOBALS['DB_WE']->escape($cur) . '`="' . $GLOBALS['DB_WE']->escape($var) . '"';
		} else {
			$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_FIELD_NOT_SET;
			return;
		}
	}
	if(($uid = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE LoginDenied=0 AND ' . implode(' AND ', $where)))){
		array_push($loadFields, 'ID', 'Username');
		if($emailfield){
			$loadFields[] = $emailfield;
		}
		$loadFields = array_unique($loadFields);

		$_SESSION['webuser'] = getHash('SELECT `' . implode('`,`', $loadFields) . '` FROM ' . CUSTOMER_TABLE . ' WHERE LoginDenied=0 AND ID=' . $uid);
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
	$GLOBALS['DB_WE']->query('DELETE FROM ' . PWDRESET_TABLE . ' WHERE expires<NOW()');

	$required = array_unique(weTag_getAttribute('required', $attribs, '', we_base_request::STRING_LIST));
	$loadFields = array_unique(weTag_getAttribute('loadFields', $attribs, '', we_base_request::STRING_LIST));
	$pwdRegex = weTag_getAttribute('passwordRule', $attribs, '', we_base_request::RAW);

//set dates
	we_base_util::convertDateInRequest($_REQUEST['s'], false);

	switch($type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING)){
		default:
			return parseError('no type set');
		case 'direct':
			if(count($required) < 2){
				return parseError('For security reasons: in direct mode, attribute <b>required</b> needs at least two different fields!');
			}
			if(!checkPwds($pwdRegex) || !($uid = checkRequired($required, $loadFields))){
				return;
			}

			$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter(array('Password' => we_customer_customer::cryptPassword(we_base_request::_(we_base_request::STRING, 's', '', 'Password')))) . ' WHERE ID=' . $uid);
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
			$customerEmailField = weTag_getAttribute('customerEmailField', $attribs, '', we_base_request::STRING);
			if(($type === 'emailPassword' && !checkPwds($pwdRegex)) || !($uid = checkRequired($required, $loadFields, $customerEmailField))){
				return;
			}
			$pwd = we_base_request::_(we_base_request::STRING, 's', '', 'Password');
			$_SESSION['webuser']['WE_token'] = substr(md5(uniqid('', true)), 0, 25);
			$GLOBALS['DB_WE']->query('REPLACE INTO ' . PWDRESET_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'ID' => $uid,
					'UserTable' => 'tblWebUser',
					'password' => $pwd ? we_customer_customer::cryptPassword($pwd) : '',
					'expires' => sql_function('NOW()+ INTERVAL ' . intval(weTag_getAttribute('expireToken', $attribs, 3600, we_base_request::INT)) . ' SECOND'),
					'token' => $_SESSION['webuser']['WE_token']
			)));
			$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_ALL_OK;

			break;
		case 'resetFromMail':
			//if optional required field is given, check them
			if(!empty($required) && !checkRequired($required, $loadFields)){
				return;
			}
			$user = we_base_request::_(we_base_request::INT, 'user');
			//check token && if password present; expired logins are already deleted
			$data = getHash('SELECT ID,password FROM ' . PWDRESET_TABLE . ' WHERE UserTable="tblWebUser" AND ID=' . $user . ' AND token="' . $GLOBALS['DB_WE']->escape(we_base_request::_(we_base_request::STRING, 'token')) . '"');

			if(!$data){
				$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_TOKEN_INVALID;
				$GLOBALS['DB_WE']->query('DELETE FROM ' . PWDRESET_TABLE . ' WHERE UserTable="tblWebUser" AND ID=' . $user);
				return;
			}
			//if no pwd is set, check if passwords are given by request
			if(!$data['password']){
				if(!checkPwds($pwdRegex)){
					return;
				}
				//set password from request, pwd in db was empty
				$data['password'] = we_customer_customer::cryptPassword(we_base_request::_(we_base_request::STRING, 's', '', 'Password'));
			}
			//ok, we have a password, all (optional requirements are met) & token was valid
			$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET Password="' . $GLOBALS['DB_WE']->escape($data['password']) . '" WHERE LoginDenied=0 AND ID=' . $data['ID']);
			$GLOBALS['DB_WE']->query('DELETE FROM ' . PWDRESET_TABLE . ' WHERE UserTable="tblWebUser" AND ID=' . $data['ID']);

			$GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_ALL_OK;
			break;
	}
}
