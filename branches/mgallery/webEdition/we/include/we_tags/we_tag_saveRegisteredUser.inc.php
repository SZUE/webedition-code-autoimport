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
function we_tag_saveRegisteredUser($attribs){
	if(!(defined('CUSTOMER_TABLE') && isset($_REQUEST['s']))){
		return;
	}

	$userexists = weTag_getAttribute('userexists', $attribs, false, we_base_request::STRING);
	$userempty = weTag_getAttribute('userempty', $attribs, '', we_base_request::STRING);
	$passempty = weTag_getAttribute('passempty', $attribs, '', we_base_request::STRING);
	$changesessiondata = weTag_getAttribute('changesessiondata', $attribs, true, we_base_request::BOOL);
	$default_register = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin"  AND pref_name="default_saveRegisteredUser_register"') === 'true';
	$registerallowed = (isset($attribs['register']) ? weTag_getAttribute('register', $attribs, $default_register, we_base_request::BOOL) : $default_register);
	$protected = weTag_getAttribute('protected', $attribs, '', we_base_request::STRING_LIST);
	$allowed = weTag_getAttribute('allowed', $attribs, '', we_base_request::STRING_LIST);
	$pwdRule = weTag_getAttribute('passwordRule', $attribs, '', we_base_request::RAW);

	if(isset($_REQUEST['s']['Password2'])){
		unset($_REQUEST['s']['Password2']);
	}
	we_base_util::convertDateInRequest($_REQUEST['s'], false);

	$uid = we_base_request::_(we_base_request::INT, 's', false, 'ID');
	$username = trim(we_base_request::_(we_base_request::STRING, 's', '', 'Username'));
	$password = we_base_request::_(we_base_request::RAW, 's', false, 'Password');
	//register new User
	if(($uid === false || $uid <= 0) && (!isset($_SESSION['webuser']['ID'])) && $registerallowed && (!isset($_SESSION['webuser']['registered']) || !$_SESSION['webuser']['registered'])){ // neuer User
		if(!$username){
			we_tag_saveRegisteredUser_keepInput();
			$GLOBALS['ERROR']['saveRegisteredUser'] = we_customer_customer::PWD_USER_EMPTY;
			if($userempty !== ''){
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(($userempty ? : g_l('modules_customer', '[username_empty]')), we_message_reporting::WE_MESSAGE_FRONTEND));
			}
			return;
		}
		if(we_customer_customer::customerNameExist($username, $GLOBALS['DB_WE'])){
			// Eingabe in Session schreiben, damit die eingegebenen Werte erhalten bleiben!
			we_tag_saveRegisteredUser_keepInput();
			$GLOBALS['ERROR']['saveRegisteredUser'] = we_customer_customer::PWD_USER_EXISTS;
			if($userexists !== ''){
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(($userexists ? : g_l('modules_customer', '[username_exists]')), $username), we_message_reporting::WE_MESSAGE_FRONTEND));
			}
			return;
		}
		if(!$password){
			we_tag_saveRegisteredUser_keepInput();
			$GLOBALS['ERROR']['saveRegisteredUser'] = we_customer_customer::PWD_FIELD_NOT_SET;
			if($passempty !== ''){
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(($passempty ? : g_l('modules_customer', '[password_empty]')), we_message_reporting::WE_MESSAGE_FRONTEND));
			}
			return;
		}
		if($pwdRule && !preg_match('/' . preg_quote($pwdRule, '/') . '/', $password)){
			we_tag_saveRegisteredUser_keepInput();
			$GLOBALS['ERROR']['saveRegisteredUser'] = we_customer_customer::PWD_NOT_SUFFICIENT;
			return;
		}

		// username existiert noch nicht!
		$hook = new weHook('customer_preSave', '', array('customer' => &$_REQUEST['s'], 'from' => 'tag', 'type' => 'new', 'tagname' => 'saveRegisteredUser'));
		$ret = $hook->executeHook();

		we_saveCustomerImages();
		$set = we_tag_saveRegisteredUser_processRequest($protected, $allowed);

		if($set){
			// User in DB speichern
			$set['ModifyDate'] = sql_function('UNIX_TIMESTAMP()');
			$set['MemberSince'] = sql_function('UNIX_TIMESTAMP()');
			$set['LastAccess'] = sql_function('UNIX_TIMESTAMP()');
			$set['LastLogin'] = sql_function('UNIX_TIMESTAMP()');
			$set['ModifiedBy'] = 'frontend';

			$GLOBALS['DB_WE']->query('INSERT INTO ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($set));
			$id = $GLOBALS['DB_WE']->getInsertId();
			if($id){
				// User in session speichern
				$_SESSION['webuser'] = array(
					'ID' => $id,
					'registered' => true, //needed for reload
				);
				$GLOBALS['we_customer_write_ID'] = $_SESSION['webuser']['ID'];
				//make sure to always load session data
				$changesessiondata = true;
			}
		}
	} else if($uid == $_SESSION['webuser']['ID'] && $_SESSION['webuser']['registered']){ // existing user
		// existierender User (Daten werden von User geaendert)!!
		$weUsername = $username? : $_SESSION['webuser']['Username'];

		if(f('SELECT 1 FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $GLOBALS['DB_WE']->escape($weUsername) . '" AND ID!=' . intval($_SESSION['webuser']['ID']))){
			$GLOBALS['ERROR']['saveRegisteredUser'] = we_customer_customer::PWD_USER_EXISTS;
			if($userexists !== ''){
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf($userexists ? : g_l('modules_customer', '[username_exists]'), $weUsername), we_message_reporting::WE_MESSAGE_FRONTEND));
			}
			return;
		}
		if(isset($_REQUEST['s'])){// es existiert kein anderer User mit den neuen Username oder username hat sich nicht geaendert
			$hook = new weHook('customer_preSave', '', array('customer' => &$_REQUEST['s'], 'from' => 'tag', 'type' => 'modify', 'tagname' => 'saveRegisteredUser'));
			$ret = $hook->executeHook();

			we_saveCustomerImages();
			$set_a = we_tag_saveRegisteredUser_processRequest($protected, $allowed);
			$password = we_base_request::_(we_base_request::RAW_CHECKED, 's', false, 'Password');
			if($password !== false && $password != we_customer_customer::NOPWD_CHANGE){
				if(!$password){
					we_tag_saveRegisteredUser_keepInput();
					$GLOBALS['ERROR']['saveRegisteredUser'] = we_customer_customer::PWD_FIELD_NOT_SET;
					if($passempty !== ''){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(($passempty ? : g_l('modules_customer', '[password_empty]')), we_message_reporting::WE_MESSAGE_FRONTEND));
					}
					return;
				}
				if($pwdRule && !preg_match('/' . preg_quote($pwdRule, '/') . '/', $password)){
					we_tag_saveRegisteredUser_keepInput();
					$GLOBALS['ERROR']['saveRegisteredUser'] = we_customer_customer::PWD_NOT_SUFFICIENT;
					return;
				}
			}
			if($password != we_customer_customer::NOPWD_CHANGE && !we_customer_customer::comparePassword(f('SELECT Password FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . $_SESSION['webuser']['ID']), $password)){//bei Passwordaenderungen muessen die Autologins des Users geloescht werden
				$GLOBALS['DB_WE']->query('DELETE FROM ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE WebUserID=' . intval($_SESSION['webuser']['ID']));
			}
			if($set_a){
				$set_a['ModifyDate'] = sql_function('UNIX_TIMESTAMP()');
				$set_a['ModifiedBy'] = 'frontend';
				$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($set_a) . ' WHERE ID=' . intval($_SESSION['webuser']['ID']));
			}
		}
	}

	//die neuen daten in die session schreiben
	$oldReg = !empty($_SESSION['webuser']['registered']);
	if($changesessiondata && $oldReg){
		//keep Password if known
		if(SECURITY_SESSION_PASSWORD & we_customer_customer::STORE_PASSWORD){
			//FIXME: on register password is in $_REQUEST['s']['Password']
			$oldPwd = $_SESSION['webuser']['_Password'];
		}
		$_SESSION['webuser'] = array_merge(getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . $_SESSION['webuser']['ID'], null, MYSQL_ASSOC), we_customer_customer::getEncryptedFields());
		if((SECURITY_SESSION_PASSWORD & we_customer_customer::STORE_DBPASSWORD) == 0){
			unset($_SESSION['webuser']['Password']);
		}
		if(SECURITY_SESSION_PASSWORD & we_customer_customer::STORE_PASSWORD){
			$_SESSION['webuser']['_Password'] = $oldPwd;
		}
	}
	//don't set anything that wasn't set before
	$_SESSION['webuser']['registered'] = $oldReg;
}

function we_saveCustomerImages(){
	if(isset($_FILES['WE_SF_IMG_DATA']['name']) && is_array($_FILES['WE_SF_IMG_DATA']['name'])){
		$webuserId = isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : 0;
		foreach($_FILES['WE_SF_IMG_DATA']['name'] as $imgName => $filename){
			$imgId = isset($_SESSION['webuser'][$imgName]) ? $_SESSION['webuser'][$imgName] : 0;
			if(!id_to_path($imgId)){
				$imgId = 0;
			}

			if(we_base_request::_(we_base_request::BOOL, 'WE_SF_DEL_CHECKBOX_' . $imgName)){
				if($imgId){
					$imgDocument = new we_imageDocument();
					$imgDocument->initByID($imgId);
					if($imgDocument->WebUserID == $webuserId){
						//everything ok, now delete

						we_base_delete::deleteEntry($imgId, FILE_TABLE);
						// reset image field
						$_SESSION['webuser'][$imgName] = 0;
						$_REQUEST['s'][$imgName] = 0;
					}
				}
			} else if($filename){
				// file is selected, check to see if it is an image
				$ct = getContentTypeFromFile($filename);
				if($ct == we_base_ContentTypes::IMAGE){

					$_serverPath = TEMP_PATH . we_base_file::getUniqueId();
					move_uploaded_file($_FILES['WE_SF_IMG_DATA']['tmp_name'][$imgName], $_serverPath);

					$we_size = we_thumbnail::getimagesize($_serverPath);

					if(!empty($we_size)){

						$tmp_Filename = $imgName . '_' . we_base_file::getUniqueId() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['WE_SF_IMG_DATA']['name'][$imgName]);
						$tmp = explode('.', $tmp_Filename);
						$_extension = '.' . $tmp[count($tmp) - 1];
						unset($tmp[count($tmp) - 1]);
						$_fileName = implode('.', $tmp);
						$_text = $_fileName . $_extension;

						//image needs to be scaled
						if((!empty($_SESSION['webuser']['imgtmp'][$imgName]['width'])) ||
							(!empty($_SESSION['webuser']['imgtmp'][$imgName]['height']))){
							$imageData = we_base_file::load($_serverPath);
							$thumb = new we_thumbnail();
							$thumb->init('dummy', $_SESSION['webuser']['imgtmp'][$imgName]['width'], $_SESSION['webuser']['imgtmp'][$imgName]['height'], $_SESSION['webuser']['imgtmp'][$imgName]['keepratio'], $_SESSION['webuser']['imgtmp'][$imgName]['maximize'], false, false, '', 'dummy', 0, '', '', $_extension, $we_size[0], $we_size[1], $imageData, '', $_SESSION['webuser']['imgtmp'][$imgName]['quality'], true);

							$imgData = '';
							$thumb->getThumb($imgData);

							we_base_file::save($_serverPath, $imgData);
							$we_size = we_thumbnail::getimagesize($_serverPath);
						}

						$_imgwidth = $we_size[0];
						$_imgheight = $we_size[1];
						//$_type = $_FILES['WE_SF_IMG_DATA']['type'][$imgName];
						$_size = $_FILES['WE_SF_IMG_DATA']['size'][$imgName];

						$imgDocument = new we_imageDocument();

						if($imgId){
							// document has already an image
							// so change binary data
							$imgDocument->initByID($imgId);
						}

						$imgDocument->Filename = $_fileName;
						$imgDocument->Extension = $_extension;
						$imgDocument->Text = $_text;

						if(!$imgId){
							$imgDocument->setParentID($_SESSION['webuser']['imgtmp'][$imgName]['parentid']);
						}

						$imgDocument->Path = $imgDocument->getParentPath() . (($imgDocument->getParentPath() != '/') ? '/' : '') . $imgDocument->Text;

						$imgDocument->setElement('width', $_imgwidth, 'attrib');
						$imgDocument->setElement('height', $_imgheight, 'attrib');
						$imgDocument->setElement('origwidth', $_imgwidth, 'attrib');
						$imgDocument->setElement('origheight', $_imgheight, 'attrib');
						$imgDocument->setElement('type', we_base_ContentTypes::IMAGE, 'attrib');

						$imgDocument->setElement('data', $_serverPath, 'image');

						$imgDocument->setElement('filesize', $_size, 'attrib');

						$imgDocument->Table = FILE_TABLE;
						$imgDocument->Published = time();
						$imgDocument->WebUserID = $webuserId;
						$imgDocument->we_save();
						$newId = $imgDocument->ID;

						$_SESSION['webuser'][$imgName] = $newId;
						$_REQUEST['s'][$imgName] = $newId;
					}
				}
			}
		}
	}
}

function we_tag_saveRegisteredUser_keepInput(){
	if(isset($_REQUEST['s'])){
		$registered = $_SESSION['webuser']['registered'];
		$_SESSION['webuser'] = we_base_request::_(we_base_request::HTML, 's');
		//never set ID + Password
		if(isset($_SESSION['webuser']['ID'])){
			unset($_SESSION['webuser']['ID']);
		}
		if(isset($_SESSION['webuser']['Password'])){
			unset($_SESSION['webuser']['Password']);
		}
		//make sure we stay unregistered!
		$_SESSION['webuser']['registered'] = $registered;
	}
}

function we_tag_saveRegisteredUser_processRequest(array $protected, array $allowed){
	$set = array();
	$allEncryptedFields = we_customer_customer::getEncryptedFields();

	foreach($_REQUEST['s'] as $name => $val){
		switch($name){
			case 'Username': ### QUICKFIX !!!
				$set['Username'] = we_base_util::rmPhp($val);
				$set['Path'] = '/' . we_base_util::rmPhp($val);
				$set['Text'] = we_base_util::rmPhp($val);
				break;
			case 'Text':
			case 'Path':
			case 'ID':
				break;
			case 'Password':
				if($val == we_customer_customer::NOPWD_CHANGE || !$val){
					continue;
				}
				$val = we_customer_customer::cryptPassword($val);
			default:
				if(($protected && in_array($name, $protected)) ||
					($allowed && !in_array($name, $allowed))){
					continue;
				}
				$set[$name] = (isset($allEncryptedFields[$name]) && $val != we_customer_customer::ENCRYPTED_DATA ?
						we_customer_customer::cryptData(we_base_util::rmPhp($val), SECURITY_ENCRYPTION_KEY, false) :
						we_base_util::rmPhp($val)
					);
				break;
		}
	}
	return $set;
}
