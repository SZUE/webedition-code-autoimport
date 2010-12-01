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

function we_tag_sessionStart($attribs, $content)
{
	$GLOBALS["WE_SESSION_START"] = true;
	$persistentlogins = we_getTagAttribute("persistentlogins",$attribs,false,true);
	$onlinemonitor = we_getTagAttribute("onlinemonitor",$attribs,false,true);
	
	if (defined("CUSTOMER_TABLE")) {
		$currenttime=time();
		$SessionAutologin=0;
		if (isset($_REQUEST["we_webUser_logout"]) && $_REQUEST["we_webUser_logout"]) {
			
			if (!isset($_SESSION)) 
				@session_start();
			
			
			if (isset($_SESSION["webuser"]["registered"]) && $_SESSION["webuser"]["registered"] && isset($_SESSION["webuser"]["ID"]) && $_SESSION["webuser"]["ID"] && ( (isset($_REQUEST["s"]["AutoLogin"]) && !$_REQUEST["s"]["AutoLogin"]) || (isset($_SESSION["webuser"]["AutoLogin"]) && !$_SESSION["webuser"]["AutoLogin"])) ){
				$GLOBALS["DB_WE"]->query("DELETE FROM " . CUSTOMER_AUTOLOGIN_TABLE . " WHERE AutoLoginID='" . mysql_real_escape_string($_SESSION["webuser"]["AutoLoginID"]) . "'");
				setcookie("_we_autologin", '',($currenttime-3600),'/');;
			}	
			unset($_SESSION["webuser"]);
			unset($_SESSION["s"]);
			unset($_REQUEST["s"]);
			$_SESSION["webuser"] = array(
				"registered" => false
			);
		
		} else {
			if (!isset($_SESSION))
				@session_start();
			if (isset($_REQUEST["we_set_registeredUser"]) && $GLOBALS["we_doc"]->InWebEdition) {
				$_SESSION["we_set_registered"] = $_REQUEST["we_set_registeredUser"];
			}
			if (!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) {
				if (!isset($_SESSION["webuser"])) {
					$_SESSION["webuser"] = array(
						"registered" => false
					);
				}
				if (isset($_REQUEST["s"]["Username"]) && isset($_REQUEST["s"]["Password"]) && !(isset($_REQUEST["s"]["ID"]))) {
					if($_REQUEST["s"]["Username"] != ''){
						$u = getHash('SELECT * from ' . CUSTOMER_TABLE . ' WHERE Username="' . mysql_real_escape_string($_REQUEST['s']["Username"]) . '"',$GLOBALS["DB_WE"]);
						if (isset($u["Password"]) && $u["LoginDenied"] != 1) {
							if ($_REQUEST['s']["Username"] == $u["Username"] && $_REQUEST['s']["Password"] == $u["Password"]) {
								$_SESSION["webuser"] = $u;
								$_SESSION["webuser"]["registered"] = true;
								$GLOBALS["DB_WE"]->query("UPDATE " . CUSTOMER_TABLE . " SET LastLogin='" . $currenttime . "' WHERE ID='" . abs($_SESSION["webuser"]["ID"]) . "'");
								
								if ($persistentlogins && isset($_REQUEST["s"]["AutoLogin"]) && $_REQUEST["s"]["AutoLogin"] && $_SESSION["webuser"]["AutoLoginDenied"] !=1 ){
									$_SESSION["webuser"]["AutoLoginID"] = uniqid(hexdec(substr(session_id(), 0, 8)),true);
									$q= "INSERT INTO " . CUSTOMER_AUTOLOGIN_TABLE . " (AutoLoginID,WebUserID,LastIp,LastLogin) VALUES('".mysql_real_escape_string(sha1($_SESSION["webuser"]["AutoLoginID"]))."','".abs($_SESSION["webuser"]["ID"])."','".htmlspecialchars((string) $_SERVER['REMOTE_ADDR'])."','".$currenttime."')";
									$GLOBALS["DB_WE"]->query($q);
									setcookie("_we_autologin", $_SESSION["webuser"]["AutoLoginID"],($currenttime+CUSTOMER_AUTOLOGIN_LIFETIME),'/');
									$GLOBALS["DB_WE"]->query("UPDATE " . CUSTOMER_TABLE . " SET AutoLogin='1' WHERE ID='" . abs($_SESSION["webuser"]["ID"]) . "'");
									$_SESSION["webuser"]["AutoLogin"]=1;
									$SessionAutologin=1;
								} 
							} else {
								$_SESSION["webuser"] = array(
									"registered" => false, "loginfailed" => true
								);
							}
						
						} else {
							$_SESSION["webuser"] = array(
								"registered" => false, "loginfailed" => true
							);
						}
					} else {
						$_SESSION["webuser"] = array(
							"registered" => false, "loginfailed" => true
						);					
					}
				}
				if ($persistentlogins && ((isset($_SESSION["webuser"]["registered"]) && !$_SESSION["webuser"]["registered"]) || !isset($_SESSION["webuser"]["registered"]) ) && isset($_COOKIE['_we_autologin']) ){
					$autologinSeek = $_COOKIE['_we_autologin'];
					if ($autologinSeek!=''){
						$a = getHash('SELECT * from ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE AutoLoginID="' . mysql_real_escape_string(sha1($autologinSeek)) . '"',$GLOBALS["DB_WE"]);
						if (isset($a["WebUserID"]) && $a["WebUserID"]){
							$u = getHash('SELECT * from ' . CUSTOMER_TABLE . ' WHERE ID="' . mysql_real_escape_string($a["WebUserID"]) . '"',$GLOBALS["DB_WE"]);
							if(isset($u["Password"]) && $u["LoginDenied"] != 1 && $u["AutoLoginDenied"] != 1){
								$_SESSION["webuser"] = $u;
								$_SESSION["webuser"]["registered"] = true;
								$_SESSION["webuser"]["AutoLoginID"] = uniqid(hexdec(substr(session_id(), 0, 8)),true);
								$q = "UPDATE ".CUSTOMER_AUTOLOGIN_TABLE." SET AutoLoginID=".mysql_real_escape_string(sha1($_SESSION["webuser"]["AutoLoginID"])).",LastIp=".htmlspecialchars((string) $_SERVER['REMOTE_ADDR']).",LastLogin=".$currenttime." WHERE WebUserID=".abs($_SESSION["webuser"]["ID"])." AND AutoLoginID=".mysql_real_escape_string(sha1($autologinSeek));
								$GLOBALS["DB_WE"]->query($q);
								setcookie("_we_autologin", $_SESSION["webuser"]["AutoLoginID"],($currenttime+CUSTOMER_AUTOLOGIN_LIFETIME),'/');
							} else {
								$_SESSION["webuser"] = array("registered" => false);
							}
						} else {
							$_SESSION["webuser"] = array("registered" => false);
						}			
					} else {
						$_SESSION["webuser"] = array("registered" => false);
					}
					
				} 
				if (isset($_SESSION["webuser"]["registered"]) && isset($_SESSION["webuser"]["ID"]) && isset($_SESSION["webuser"]["Username"]) && $_SESSION["webuser"]["registered"] && $_SESSION["webuser"]["ID"] && $_SESSION["webuser"]["Username"]!='') {
					$GLOBALS["DB_WE"]->query("UPDATE " . CUSTOMER_TABLE . " SET LastAccess='" . $currenttime . "' WHERE ID='" . mysql_real_escape_string($_SESSION["webuser"]["ID"]) . "'");
				}
			}
		}
		if($onlinemonitor && isset($_SESSION["webuser"]["registered"])){
			$q = "DELETE FROM ".CUSTOMER_SESSION_TABLE." WHERE LastAccess < '".$currenttime - 3600 ."'";
			$monitorgroupfield = we_getTagAttribute("monitorgroupfield",$attribs);
			$docAttr = we_getTagAttribute("monitordoc", $attribs);
			$doc = we_getDocForTag($docAttr, false);
			$PageID = $doc->ID;
			$SessionID=session_id();
			$SessionIp = (!empty($_SERVER['REMOTE_ADDR'])) ? htmlspecialchars((string) $_SERVER['REMOTE_ADDR']) : '';
			
			$Browser = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';
			$Referer = (!empty($_SERVER['HTTP_REFERER'])) ? htmlspecialchars((string) $_SERVER['HTTP_REFERER']) : '';
			$q = "UPDATE ".CUSTOMER_SESSION_TABLE." SET PageID='".$PageID."', LastAccess='".$currenttime."' WHERE SessionID='".$SessionID."'";
			p_r($q);
			$GLOBALS["DB_WE"]->query($q);
			if ($GLOBALS["DB_WE"]->affected_rows()==0){
				if ($_SESSION["webuser"]["registered"]){
					$WebUserID = $_SESSION["webuser"]["ID"];
					if ($monitorgroupfield!=''){ $WebUserGroup = $_SESSION["webuser"][$monitorgroupfield];} else {$WebUserGroup='';}
					$WebUserDescription = '';
				} else {
					$WebUserID = 0;
					$WebUserGroup = 'guest';
					$WebUserDescription = '';
				}
				$q = "INSERT INTO ".CUSTOMER_SESSION_TABLE." (SessionID,SessionIp,WebUserID,WebUserGroup,WebUserDescription,Browser,LastLogin,LastAccess,PageID,SessionAutologin) VALUES('$SessionID','$SessionIp','$WebUserID','$WebUserGroup','$WebUserDescription','$Browser','$currenttime','$currenttime','$PageID','$SessionAutologin')";
				p_r($q);
				$GLOBALS["DB_WE"]->query($q);
			}
		}
		return "";
	
	} else {
		if (!isset($_SESSION))
			@session_start();
	}
	return "";
}