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

function we_tag_write($attribs, $content){
	$type = we_getTagAttribute("type", $attribs, "document");

	if ($type == "object") {
		$foo = attributFehltError($attribs, "classid", "write");
		if ($foo)
			return $foo;
	} else {
		$foo = attributFehltError($attribs, "doctype", "write");
		if ($foo)
			return $foo;
	}

	$name = we_getTagAttribute(
			"formname",
			$attribs,
			((isset($GLOBALS["WE_FORM"]) && $GLOBALS["WE_FORM"]) ? $GLOBALS["WE_FORM"] : "we_global_form"));

	$publish = we_getTagAttribute("publish", $attribs, "", true);
	$triggerid = we_getTagAttribute("triggerid", $attribs, 0);
	$charset = we_getTagAttribute("charset", $attribs, "iso-8859-1");
	$doctype = we_getTagAttribute("doctype", $attribs);
	$tid = we_getTagAttribute("tid", $attribs);
	$categories = we_getTagAttribute("categories", $attribs);
	$classid = we_getTagAttribute("classid", $attribs);
	$parentid = we_getTagAttribute("parentid", $attribs);
	$userid = we_getTagAttribute("userid", $attribs); // deprecated  use protected=true instead
	$protected = we_getTagAttribute("protected", $attribs, "", true);
	$admin = we_getTagAttribute("admin", $attribs);
	$mail = we_getTagAttribute("mail", $attribs);
	$mailfrom = we_getTagAttribute("mailfrom", $attribs);
	$forceedit = we_getTagAttribute("forceedit", $attribs, "", true);
	$workspaces = we_getTagAttribute("workspaces", $attribs);
	$objname = preg_replace('/[^a-z0-9_-]/i','',we_getTagAttribute("name", $attribs));
	$onduplicate = we_getTagAttribute("onduplicate", $attribs,"increment");
	if($objname==''){$onduplicate="overwrite";}
	$onpredefinedname = we_getTagAttribute("onpredefinedname", $attribs,"appendto");
	$workflowname = we_getTagAttribute("workflowname", $attribs,"");
	$workflowuserid = we_getTagAttribute("workflowuserid", $attribs,0);
	if ($workflowname!='' && $workflowuserid!=0){
		$doworkflow = true;
	} else {
		$doworkflow = false;
	}

	if (isset($_REQUEST["edit_$type"]) && $_REQUEST["edit_$type"]) {

		if ($type == "document") {
			$ok = initDocument($name, $tid, $doctype, $categories);
		} else {
			$ok = initObject($classid, $name, $categories, $parentid);
		}

		if ($ok) {
			$isOwner = false;
			if ($protected && isset($_SESSION["webuser"]["ID"])) {
				$isOwner = ($_SESSION["webuser"]["ID"] == $GLOBALS["we_$type"][$name]->WebUserID);
			} else
				if ($userid) {
					$isOwner = ($_SESSION["webuser"]["ID"] == $GLOBALS["we_$type"][$name]->getElement($userid));
				}
			$isAdmin = false;
			if ($admin) {
				$isAdmin = isset($_SESSION["webuser"][$admin]) && $_SESSION["webuser"][$admin];
			}

			if ($isAdmin || ($GLOBALS["we_$type"][$name]->ID == 0) || $isOwner || $forceedit) {
				$doWrite = true;
				$GLOBALS["we_" . $type . "_write_ok"] = true;
				$newObject = ($GLOBALS["we_$type"][$name]->ID) ? false : true;
				if ($protected) {
					if (!isset($_SESSION["webuser"]["ID"]))
						return;
					if (!$GLOBALS["we_$type"][$name]->WebUserID) {
						$GLOBALS["we_$type"][$name]->WebUserID = $_SESSION["webuser"]["ID"];
					}
				} else
					if ($userid) {
						if (!isset($_SESSION["webuser"]["ID"]))
							return;
						if (!$GLOBALS["we_$type"][$name]->getElement($userid)) {
							$GLOBALS["we_$type"][$name]->setElement($userid, $_SESSION["webuser"]["ID"]);
						}
					}

				checkAndCreateImage($name, ($type == "document") ? "we_document" : "we_object");
				checkAndCreateFlashmovie($name, ($type == "document") ? "we_document" : "we_object");
				checkAndCreateQuicktime($name, ($type == "document") ? "we_document" : "we_object");
				checkAndCreateBinary($name, ($type == "document") ? "we_document" : "we_object");

				$GLOBALS["we_$type"][$name]->i_checkPathDiffAndCreate();
				if ($objname=='') {
					$GLOBALS["we_$type"][$name]->i_correctDoublePath();
				}
				$_WE_DOC_SAVE = $GLOBALS["we_doc"];
				$GLOBALS["we_doc"] = &$GLOBALS["we_$type"][$name];
				if (strlen($workspaces) > 0 && $type == "object") {
					$wsArr = makeArrayFromCSV($workspaces);
					$tmplArray = array();
					foreach ($wsArr as $wsId) {
						array_push($tmplArray, $GLOBALS["we_$type"][$name]->getTemplateFromWs($wsId));
					}
					$GLOBALS["we_$type"][$name]->Workspaces = makeCSVFromArray($wsArr, true);
					$GLOBALS["we_$type"][$name]->Templates = makeCSVFromArray($tmplArray, true);
				}

				$GLOBALS["we_$type"][$name]->Path = $GLOBALS["we_$type"][$name]->getPath();

				if (defined("OBJECT_FILES_TABLE") && $type == "object" ) {
					$db = new DB_WE();
					if ($GLOBALS["we_$type"][$name]->Text == ""){
						if ($objname=='') {
							$objname = 1 + abs(f("SELECT max(ID) as ID FROM " . OBJECT_FILES_TABLE, "ID", $db));
						}
					} else {
						if ($onpredefinedname=='appendto') {
							if ($objname!='') {
								$objname = $GLOBALS["we_$type"][$name]->Text . '_'.$objname;
							} else {
								$objname = $GLOBALS["we_$type"][$name]->Text;
							}
						} elseif($onpredefinedname=='infrontof'){
							if ($objname!='') {
								$objname .= '_'.$GLOBALS["we_$type"][$name]->Text;
							} else {
								$objname = $GLOBALS["we_$type"][$name]->Text;
							}
						} elseif($onpredefinedname=='overwrite')  {
							if ($objname=='') {
								$objname = $GLOBALS["we_$type"][$name]->Text;
							}
						}
					}
					$objexists = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='".escape_sql_query(str_replace('//','/',$GLOBALS["we_$type"][$name]->Path."/".$objname))."'", "ID", $db);
					if($objexists==''){
						$GLOBALS["we_$type"][$name]->Text = $objname;
						$GLOBALS["we_$type"][$name]->Path = str_replace('//','/',$GLOBALS["we_$type"][$name]->Path . '/' . $objname);
					} else {
						if($onduplicate == 'abort') {
							$GLOBALS["we_object_write_ok"] = false;
							$doWrite = false;
						}
						if($onduplicate == 'overwrite') {
							$GLOBALS["we_$type"][$name]->ID = $objexists;
							$GLOBALS["we_$type"][$name]->Path = str_replace('//','/',$GLOBALS["we_$type"][$name]->Path . '/' . $objname);
							$GLOBALS["we_$type"][$name]->Text = $objname;
						}
						if($onduplicate == 'increment') {
							$z=0;
							$footext = $objname."_".$z;
							while(f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='".escape_sql_query(str_replace('//','/',$GLOBALS["we_$type"][$name]->Path."/".$footext))."'", "ID", $db)){
								$z++;
								$footext = $objname."_".$z;
							}
							$GLOBALS["we_$type"][$name]->Path = str_replace('//','/',$GLOBALS["we_$type"][$name]->Path . '/' . $footext);
							$GLOBALS["we_$type"][$name]->Text = $footext;
						}
					}
				}
				if ($doWrite){
					$GLOBALS["we_$type"][$name]->we_save();
					if ($publish) {
						if ($type == "document" && (!$GLOBALS["we_$type"][$name]->IsDynamic) && isset(
								$GLOBALS["we_doc"])) { // on static HTML Documents we have to do it different
							$GLOBALS["we_doc"]->we_publish();
						} else {
							$GLOBALS["we_$type"][$name]->we_publish();
						}
					}
				}
				if ($doWrite && $doworkflow) {
					include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_modules/workflow/weWorkflowUtility.php");
					$workflowID = weWorkflowUtility::getWorkflowID($workflowname);
					$wf_text = "we:write ".$workflowname."  " ;
					if($GLOBALS["we_doc"]->Table==FILE_TABLE) {
						$wf_text .= "we_object ID: " . $GLOBALS["we_doc"]->ID;
					} else {
						$wf_text .= "we_document ID: ".$GLOBALS["we_doc"]->ID ;
					}
					if(weWorkflowUtility::insertDocInWorkflow($GLOBALS["we_doc"]->ID,$GLOBALS["we_doc"]->Table,$workflowID,$workflowuserid,$wf_text)){
					}

				}

				unset($GLOBALS["we_doc"]);
				$GLOBALS["we_doc"] = $_WE_DOC_SAVE;
				unset($_WE_DOC_SAVE);
				$_REQUEST["we_returnpage"] = $GLOBALS["we_$type"][$name]->getElement("we_returnpage");

				if ($doWrite && $mail) {
					if (!$mailfrom) {
						$mailfrom = "dontReply@" . $GLOBALS["SERVER_NAME"];
					}
					$path = $GLOBALS["we_$type"][$name]->Path;
					if ($type == "object") {
						$classname = f(
								"SELECT Text FROM " . OBJECT_TABLE . " WHERE ID='" . abs($classid) . "'",
								"Text",
								$GLOBALS["DB_WE"]);
						if ($triggerid) {
							$port = (defined("HTTP_PORT")) ? (":" . HTTP_PORT) : "";
							$mailtext = sprintf(g_l('global',"[std_mailtext_newObj]"), $path, $classname) . "\n" . "http://" . $GLOBALS["SERVER_NAME"] . $port . id_to_path(
									$triggerid) . "?we_objectID=" . $GLOBALS["we_object"][$name]->ID;
						} else {
							$mailtext = sprintf(g_l('global',"[std_mailtext_newObj]"), $path, $classname) . "\n" . "ObjectID: " . $GLOBALS["we_object"][$name]->ID;
						}
						$subject = g_l('global',"[std_subject_newObj]");
					} else {
						$mailtext = sprintf(g_l('global',"[std_mailtext_newDoc]"), $path) . "\n" . $GLOBALS["we_$type"][$name]->getHttpPath();
						$subject = g_l('global',"[std_subject_newDoc]");
					}
					$phpmail = new we_util_Mailer($mail, $subject, $mailfrom);
					$phpmail->setCharSet($charset);
					$phpmail->addTextPart($mailtext);
					$phpmail->buildMessage();
					$phpmail->Send();
				}
			} else {
				$GLOBALS["we_object_write_ok"] = false;
			}
		}
	}
	if (isset($GLOBALS["WE_SESSION_START"]) && $GLOBALS["WE_SESSION_START"]) {

		unset($_SESSION['we_' . $type . '_session_' . $name]);
		$GLOBALS['we_' . $type . '_session_' . $name] = array();
	}
}
