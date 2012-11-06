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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_sessionStart($attribs){
	$GLOBALS['WE_SESSION_START'] = true;
	$persistentlogins = weTag_getAttribute('persistentlogins', $attribs, false, true);
	$onlinemonitor = weTag_getAttribute('onlinemonitor', $attribs, false, true);

	if(!isset($_SESSION)){
		@session_start();
		//FIXME: remove in 6.4; due to upgrade!
		if(isset($_SESSION['we'])){
			unset($_SESSION['we']);
		}
	}

	if(!defined('CUSTOMER_TABLE')){
		return '';
	}

	$currenttime = time();
	$SessionAutologin = 0;
	if(isset($_REQUEST['we_webUser_logout']) && $_REQUEST['we_webUser_logout']){

		if(isset($_SESSION['webuser']['registered']) && $_SESSION['webuser']['registered'] && isset($_SESSION['webuser']['ID']) && $_SESSION['webuser']['ID'] && ( (isset($_REQUEST['s']['AutoLogin']) && !$_REQUEST['s']['AutoLogin']) || (isset($_SESSION['webuser']['AutoLogin']) && !$_SESSION['webuser']['AutoLogin'])) && isset($_SESSION['webuser']['AutoLoginID'])){
			$GLOBALS['DB_WE']->query('DELETE FROM ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($_SESSION['webuser']['AutoLoginID'])) . '"');
			setcookie('_we_autologin', '', ($currenttime - 3600), '/');
			;
		}
		unset($_SESSION['webuser']);
		unset($_SESSION['s']);
		unset($_REQUEST['s']);
		$_SESSION['webuser'] = array('registered' => false);

		$GLOBALS['WE_LOGOUT'] = true;
	} else{
		if(isset($_REQUEST['we_set_registeredUser']) && $GLOBALS['we_doc']->InWebEdition){
			$_SESSION['weS']['we_set_registered'] = $_REQUEST['we_set_registeredUser'];
		}
		if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
			if(!isset($_SESSION['webuser'])){
				$_SESSION['webuser'] = array(
					'registered' => false
				);
			}
			if(isset($_REQUEST['s']['Username']) && isset($_REQUEST['s']['Password']) && !(isset($_REQUEST['s']['ID']))){
				if($_REQUEST['s']['Username'] != ''){
					$u = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $GLOBALS['DB_WE']->escape(strtolower($_REQUEST['s']['Username'])) . '"', $GLOBALS['DB_WE']);
					if(isset($u['Password']) && $u['LoginDenied'] != 1){
						if(strtolower($_REQUEST['s']['Username']) == strtolower($u['Username']) && $_REQUEST['s']['Password'] == $u['Password']){
							$_SESSION['webuser'] = $u;
							$_SESSION['webuser']['registered'] = true;
							$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET LastLogin=UNIX_TIMESTAMP() WHERE ID=' . intval($_SESSION['webuser']['ID']));

							if($persistentlogins && isset($_REQUEST['s']['AutoLogin']) && $_REQUEST['s']['AutoLogin'] && $_SESSION['webuser']['AutoLoginDenied'] != 1){
								$_SESSION['webuser']['AutoLoginID'] = uniqid(hexdec(substr(session_id(), 0, 8)), true);
								$GLOBALS['DB_WE']->query('INSERT INTO ' . CUSTOMER_AUTOLOGIN_TABLE . ' SET AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($_SESSION['webuser']['AutoLoginID'])) . '", WebUserID=' . intval($_SESSION['webuser']['ID']) . ',LastIp="' . htmlspecialchars((string) $_SERVER['REMOTE_ADDR']) . '",LastLogin=NOW()');
								setcookie('_we_autologin', $_SESSION['webuser']['AutoLoginID'], ($currenttime + CUSTOMER_AUTOLOGIN_LIFETIME), '/');
								$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET AutoLogin="1" WHERE ID=' . intval($_SESSION["webuser"]["ID"]));
								$_SESSION['webuser']['AutoLogin'] = 1;
								$SessionAutologin = 1;
							}
							$GLOBALS['WE_LOGIN'] = true;
						} else{
							$_SESSION['webuser'] = array(
								'registered' => false, 'loginfailed' => true
							);
						}
					} else{
						$_SESSION['webuser'] = array(
							'registered' => false, 'loginfailed' => true
						);
					}
				} else{
					$_SESSION['webuser'] = array(
						'registered' => false, 'loginfailed' => true
					);
				}
			}
			if($persistentlogins && ((isset($_SESSION['webuser']['registered']) && !$_SESSION['webuser']['registered']) || !isset($_SESSION['webuser']['registered']) ) && isset($_COOKIE['_we_autologin'])){
				$autologinSeek = $_COOKIE['_we_autologin'];
				if($autologinSeek != ''){
					$a = getHash('SELECT * from ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($autologinSeek)) . '"', $GLOBALS['DB_WE']);
					if(isset($a['WebUserID']) && $a['WebUserID']){
						$u = getHash('SELECT * from ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($a['WebUserID']), $GLOBALS['DB_WE']);
						if(isset($u['Password']) && $u['LoginDenied'] != 1 && $u['AutoLoginDenied'] != 1){
							$_SESSION['webuser'] = $u;
							$_SESSION['webuser']['registered'] = true;
							$_SESSION['webuser']['AutoLoginID'] = uniqid(hexdec(substr(session_id(), 0, 8)), true);
							$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_AUTOLOGIN_TABLE . ' SET AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($_SESSION['webuser']['AutoLoginID'])) . '",LastIp="' . htmlspecialchars((string) $_SERVER['REMOTE_ADDR']) . '",LastLogin=NOW() WHERE WebUserID=' . intval($_SESSION['webuser']['ID']) . ' AND AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($autologinSeek)) . '"');
							setcookie('_we_autologin', $_SESSION['webuser']['AutoLoginID'], ($currenttime + CUSTOMER_AUTOLOGIN_LIFETIME), '/');
							$GLOBALS['WE_LOGIN'] = true;
						} else{
							$_SESSION['webuser'] = array('registered' => false);
						}
					} else{
						$_SESSION['webuser'] = array('registered' => false);
					}
				} else{
					$_SESSION['webuser'] = array('registered' => false);
				}
			}
			if(isset($_SESSION['webuser']['registered']) && isset($_SESSION['webuser']['ID']) && isset($_SESSION['webuser']['Username']) && $_SESSION['webuser']['registered'] && $_SESSION['webuser']['ID'] && $_SESSION['webuser']['Username'] != ''){
				$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET LastAccess=UNIX_TIMESTAMP() WHERE ID=' . intval($_SESSION['webuser']['ID']));
			}
		}
	}
	if($onlinemonitor && isset($_SESSION['webuser']['registered'])){
		$GLOBALS['DB_WE']->query('DELETE FROM ' . CUSTOMER_SESSION_TABLE . ' WHERE LastAccess < DATE_SUB(NOW(), INTERVAL 1 HOUR)');
		$monitorgroupfield = weTag_getAttribute('monitorgroupfield', $attribs);
		$docAttr = weTag_getAttribute('monitordoc', $attribs);
		$doc = we_getDocForTag($docAttr, false);
		$PageID = $doc->ID;
		$ObjectID = 0;
		$SessionID = session_id();
		$SessionIp = (!empty($_SERVER['REMOTE_ADDR'])) ? htmlspecialchars((string) $_SERVER['REMOTE_ADDR']) : '';

		$Browser = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';
		$Referrer = (!empty($_SERVER['HTTP_REFERER'])) ? htmlspecialchars((string) $_SERVER['HTTP_REFERER']) : '';
		if($_SESSION['webuser']['registered']){
			$WebUserID = $_SESSION['webuser']['ID'];
			if($monitorgroupfield != ''){
				$WebUserGroup = $_SESSION['webuser'][$monitorgroupfield];
			} else{
				$WebUserGroup = 'we_guest';
			}
			$WebUserDescription = '';
		} else{
			$WebUserID = 0;
			$WebUserGroup = 'we_guest';
			$WebUserDescription = '';
		}

		$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_SESSION_TABLE . ' SET PageID="' . $PageID . '",LastAccess=NOW(),WebUserID=' . intval($WebUserID) . ',WebUserGroup="' . $WebUserGroup . '",WebUserDescription="' . $WebUserDescription . '"  WHERE SessionID="' . $SessionID . '"');
		if($GLOBALS['DB_WE']->affected_rows() == 0){
			$q = 'INSERT INTO ' . CUSTOMER_SESSION_TABLE . " (SessionID,SessionIp,WebUserID,WebUserGroup,WebUserDescription,Browser,Referrer,LastLogin,LastAccess,PageID,ObjectID,SessionAutologin) VALUES('$SessionID','$SessionIp','$WebUserID','$WebUserGroup','$WebUserDescription','$Browser','$Referrer',NOW(),NOW(),'$PageID','$ObjectID','$SessionAutologin')";
			$GLOBALS['DB_WE']->query($q);
		}
	}
	return '';
}
