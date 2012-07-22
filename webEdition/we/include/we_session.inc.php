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
if(isset($_SERVER['SCRIPT_NAME']) && str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(dirname(__FILE__), '', __FILE__)){
	exit();
}

if(!isset($_SESSION)){
//	session_name(SESSION_NAME);
	@session_start();
	//FIXME: remove in 6.4; due to upgrade!
	if(isset($_SESSION['we'])){
		unset($_SESSION['we']);
	}
}
if(!isset($_SESSION['weS'])){
	$_SESSION['weS'] = array();
}

if(!isset($_SESSION["user"])){
	$_SESSION["user"] = array(
		"ID" => "", "Username" => "", "workSpace" => "", "isWeSession" => false
	);
}

if(isset($_POST["username"]) && isset($_POST["password"])){
	$DB_WE->query("SELECT UseSalt, passwd, username, LoginDenied, ID FROM " . USER_TABLE . " WHERE username='" . $DB_WE->escape($_POST["username"]) . "'");

	// only if username exists !!
	if($DB_WE->next_record()){
		$useSalt = $DB_WE->f("UseSalt");
		$salted = md5($_POST["password"] . md5($_POST["username"]));

		$passwd = $useSalt ? $salted : md5($_POST["password"]);

		if($DB_WE->f('passwd') == $passwd){

			$_userdata = $DB_WE->Record;


			if($_userdata["LoginDenied"]){ // userlogin is denied
				$GLOBALS["userLoginDenied"] = true;
			} else{
				if(!$useSalt){
					// UPDATE Password with SALT
					$DB_WE->query("UPDATE " . USER_TABLE . " SET passwd='" . $salted . "',UseSalt=1 WHERE username='" . $DB_WE->escape(
							$_POST["username"]) . "' AND passwd='" . $DB_WE->escape($passwd) . "'");
				}

				if(!(isset($_SESSION["user"]) && is_array($_SESSION["user"]))){
					$_SESSION["user"] = array();
				}
				$_SESSION["user"]["Username"] = $_userdata["username"];
				$_SESSION["user"]["ID"] = $_userdata["ID"];

				$a = array();
				$f = array();
				$t = array();
				$o = array();
				$n = array();
				$nl = array();
				$_userGroups = array(); //	Get Groups user belongs to.
				$db_tmp = new DB_WE();
				$get_ws = 0;
				$get_wst = 0;
				$get_wso = 0;
				$get_wsn = 0;
				$get_wsnl = 0;

				$DB_WE->query("SELECT ParentID,workSpace,workSpaceTmp,workSpaceNav,workSpaceObj,workSpaceNwl,ParentWs,ParentWst,ParentWsn,ParentWso,ParentWsnl FROM " . USER_TABLE . " WHERE ID=" . intval($_SESSION["user"]["ID"]) . " OR Alias=" . intval($_SESSION["user"]["ID"]));
				while($DB_WE->next_record()) {
					// get workspaces
					$a = makeArrayFromCSV($DB_WE->f("workSpace"));
					foreach($a as $k => $v)
						if(!in_array($v, $f))
							array_push($f, $v);

					$a = makeArrayFromCSV($DB_WE->f("workSpaceTmp"));
					foreach($a as $k => $v){
						if(!in_array($v, $t)){
							array_push($t, $v);
						}
					}

					$a = makeArrayFromCSV($DB_WE->f("workSpaceNav"));
					foreach($a as $k => $v){
						if(!in_array($v, $n)){
							array_push($n, $v);
						}
					}

					$a = makeArrayFromCSV($DB_WE->f("workSpaceObj"));
					foreach($a as $k => $v){
						if(!in_array($v, $o)){
							array_push($o, $v);
						}
					}

					$a = makeArrayFromCSV($DB_WE->f("workSpaceNwl"));
					foreach($a as $k => $v){
						if(!in_array($v, $nl)){
							$nl[]= $v;
						}
					}

					// get parent workspaces
					$pid = $DB_WE->f("ParentID");
					$get_ws = $DB_WE->f("ParentWs");
					$get_wst = $DB_WE->f("ParentWst");
					$get_wso = $DB_WE->f("ParentWso");
					$get_wsn = $DB_WE->f("ParentWsn");
					$get_wsnl = $DB_WE->f("ParentWsnl");

					while($pid) { //	For each group
						$_userGroups[] = $pid;

						$db_tmp->query("SELECT ParentID,workSpace,workSpaceTmp,workSpaceNav,workSpaceObj,workSpaceNwl,ParentWs,ParentWst,ParentWsn,ParentWso,ParentWsnl FROM " . USER_TABLE . " WHERE ID=" . intval($pid));
						if($db_tmp->next_record()){
							if($get_ws){
								$a = makeArrayFromCSV($db_tmp->f("workSpace"));
								foreach($a as $k => $v)
									if(!in_array($v, $f))
										$f[] = $v;
							}
							if($get_wst){
								$a = makeArrayFromCSV($db_tmp->f("workSpaceTmp"));
								foreach($a as $k => $v)
									if(!in_array($v, $t))
										$t[] = $v;
							}
							if($get_wso){
								$a = makeArrayFromCSV($db_tmp->f("workSpaceObj"));
								foreach($a as $k => $v)
									if(!in_array($v, $o))
										$o[] = $v;
							}
							if($get_wsn){
								$a = makeArrayFromCSV($db_tmp->f("workSpaceNav"));
								foreach($a as $k => $v)
									if(!in_array($v, $n))
										$n[] = $v;
							}
							if($get_wsnl){
								$a = makeArrayFromCSV($db_tmp->f("workSpaceNwl"));
								foreach($a as $k => $v)
									if(!in_array($v, $nl))
										$nl[] = $v;
							}
							$pid = $db_tmp->f("ParentID");
							$get_ws = $db_tmp->f("ParentWs");
							$get_wst = $db_tmp->f("ParentWst");
							$get_wso = $db_tmp->f("ParentWso");
							$get_wsn = $db_tmp->f("ParentWsn");
							$get_wsnl = $db_tmp->f("ParentWsnl");
						} else{
							$pid = 0;
						}
					}
				}
				$_SESSION["user"]["groups"] = $_userGroups; //	order: first is folder with user himself (deepest in tree)
				$_SESSION["user"]["workSpace"] = array(
					FILE_TABLE => $f,
					TEMPLATES_TABLE => $t,
					NAVIGATION_TABLE => $n,
				);
				if(defined('OBJECT_FILES_TABLE')){
					$_SESSION["user"]["workSpace"][OBJECT_FILES_TABLE] = $o;
				}
				if(defined('NEWSLETTER_TABLE')){
					$_SESSION["user"]["workSpace"][NEWSLETTER_TABLE] = $nl;
				}

				$exprefs = getHash("SELECT * FROM " . PREFS_TABLE . " WHERE userID=" . intval($_userdata["ID"]), $DB_WE);
				if(is_array($exprefs) && (isset($exprefs["userID"]) && $exprefs["userID"] != 0) && sizeof($exprefs) > 0){
					$_SESSION["prefs"] = $exprefs;
				} else{
					$_SESSION["prefs"]["userID"] = $_userdata["ID"];
					doInsertQuery($DB_WE, PREFS_TABLE, $_SESSION["prefs"]);
				}

				if(isset($_SESSION["user"]["Username"]) && isset($_SESSION["user"]["ID"]) && $_SESSION["user"]["Username"] && $_SESSION["user"]["ID"]){
					$foo = new we_user();
					$foo->initFromDB($_SESSION["user"]["ID"]);
					$_SESSION["perms"] = $foo->getAllPermissions();
				} else{
					$_SESSION["perms"]["ADMINISTRATOR"] = 1;
				}
				$_SESSION["user"]["isWeSession"] = true; // for pageLogger, to know that it is really a webEdition session


				$_SESSION["user"]["groups"] = $_userGroups; //	order: first is folder with user himself (deepest in tree)
				$_SESSION["user"]["workSpace"] = array(
					FILE_TABLE => $f,
					TEMPLATES_TABLE => $t,
					NAVIGATION_TABLE => $n,
				);
				if(defined('OBJECT_FILES_TABLE')){
					$_SESSION["user"]["workSpace"][OBJECT_FILES_TABLE] = $o;
				}
				if(defined('NEWSLETTER_TABLE')){
					$_SESSION["user"]["workSpace"][NEWSLETTER_TABLE] = $nl;
				}

				if(isset($_SESSION["user"]["Username"]) && isset($_SESSION["user"]["ID"]) && $_SESSION["user"]["Username"] && $_SESSION["user"]["ID"]){
					$foo = new we_user();
					$foo->initFromDB($_SESSION["user"]["ID"]);
					$_SESSION["perms"] = $foo->getAllPermissions();
				} else{
					$_SESSION["perms"]["ADMINISTRATOR"] = 1;
				}
				$_SESSION["user"]["isWeSession"] = true; // for pageLogger, to know that it is really a webEdition session
			}
		} else{
			$_SESSION["user"]["Username"] = "";
			foreach(array_keys($_SESSION) as $name){
				unset($_SESSION[$name]);
			}
		}
	} else{
		$_SESSION["user"]["Username"] = "";
		foreach(array_keys($_SESSION) as $name){
			unset($_SESSION[$name]);
		}
	}
}
$we_transaction = isset($_REQUEST["we_transaction"]) ? $_REQUEST["we_transaction"] : md5(uniqID(rand()));
$we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $we_transaction) ? $we_transaction : md5(uniqID(rand())));

if(!isset($_SESSION["we_data"])){
	$_SESSION["we_data"] = array($we_transaction => '');
}

$_SESSION["EditPageNr"] = (isset($_SESSION["EditPageNr"]) && (($_SESSION["EditPageNr"] != "") || ($_SESSION["EditPageNr"] == "0"))) ? $_SESSION["EditPageNr"] : 1;