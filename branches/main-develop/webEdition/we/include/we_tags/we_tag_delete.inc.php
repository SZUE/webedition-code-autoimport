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

function we_tag_delete($attribs, $content){
	$type = weTag_getAttribute("type", $attribs, "document");
	$userid = weTag_getAttribute("userid", $attribs); // deprecated  use protected=true instead
	$protected = weTag_getAttribute("protected", $attribs, false, true);
	$admin = weTag_getAttribute("admin", $attribs);
	$mail = weTag_getAttribute("mail", $attribs);
	$mailfrom = weTag_getAttribute("mailfrom", $attribs);
	$charset = weTag_getAttribute("charset", $attribs, "iso-8859-1");
	$doctype = weTag_getAttribute("doctype", $attribs);
	$classid = weTag_getAttribute("classid", $attribs);
	$pid = weTag_getAttribute("pid", $attribs);
	$forceedit = weTag_getAttribute("forceedit", $attribs, false, true);

	if ($type == "document") {
		if (!isset($_REQUEST["we_delDocument_ID"])) {
			return "";
		}
		include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_classes/we_webEditionDocument.inc.php");
		$docID = $_REQUEST["we_delDocument_ID"];
		$doc = new we_webEditionDocument();
		$doc->initByID($docID);
		$table = FILE_TABLE;
		if ($doctype) {
			$doctypeID = f("SELECT ID FROM " . DOC_TYPES_TABLE . " WHERE DocType like '".escape_sql_query($doctype)."'", "ID", new DB_WE());
			if ($doc->DocType != $doctypeID) {
				$GLOBALS["we_" . $type . "_delete_ok"] = false;
				return "";
			}
		}
	} else {
		if (!isset($_REQUEST["we_delObject_ID"])) {
			return "";
		}
		include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_modules/object/we_objectFile.inc.php");
		$docID = $_REQUEST["we_delObject_ID"];
		$doc = new we_objectFile();
		$doc->initByID($docID, OBJECT_FILES_TABLE);
		$table = OBJECT_FILES_TABLE;
		if ($classid) {
			if ($doc->TableID != $classid) {
				$GLOBALS["we_" . $type . "_delete_ok"] = false;
				return "";
			}
		}
	}

	if ($pid) {
		if ($doc->ParentID != $pid) {
			$GLOBALS["we_" . $type . "_delete_ok"] = false;
			return "";
		}
	}

	$isOwner = false;
	if ($protected) {
		$isOwner = ($_SESSION["webuser"]["ID"] == $doc->WebUserID);
	} else
		if ($userid) {
			$isOwner = ($_SESSION["webuser"]["ID"] == $doc->getElement($userid));
		}
	$isAdmin = false;
	if ($admin) {
		$isAdmin = isset($_SESSION["webuser"][$admin]) && $_SESSION["webuser"][$admin];
	}

	if ($isAdmin || $isOwner || $forceedit) {
		$GLOBALS["NOT_PROTECT"] = true;
		include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_delete_fn.inc.php");
		deleteEntry($docID, $table);
		$GLOBALS["we_" . $type . "_delete_ok"] = true;
		if ($mail) {
			if (!$mailfrom) {
				$mailfrom = "dontReply@" . $GLOBALS["SERVER_NAME"];
			}
			if ($type == "object") {
				$mailtext = sprintf(g_l('global',"[std_mailtext_delObj]"), $doc->Path) . "\n";
				$subject = g_l('global',"[std_subject_delObj]");
			} else {
				$mailtext = sprintf(g_l('global',"[std_mailtext_delDoc]"), $doc->Path) . "\n";
				$subject = g_l('global',"[std_subject_delDoc]");
			}
			$phpmail = new we_util_Mailer($mail, $subject, $mailfrom);
			$phpmail->setCharSet($charset);
			$phpmail->addTextPart(trim($mailtext));
			$phpmail->buildMessage();
			$phpmail->Send();
		}
	} else {
		$GLOBALS["we_" . $type . "_delete_ok"] = false;
	}
	return "";
}
