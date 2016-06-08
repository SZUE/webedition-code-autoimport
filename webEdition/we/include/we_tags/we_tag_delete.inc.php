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
function we_tag_delete(array $attribs){
	$id = weTag_getAttribute('id', $attribs, 0, we_base_request::INT);
	$type = weTag_getAttribute('type', $attribs, 'document', we_base_request::STRING);
	$userid = weTag_getAttribute('userid', $attribs, 0, we_base_request::STRING); // deprecated  use protected=true instead
	$protected = weTag_getAttribute('protected', $attribs, false, we_base_request::BOOL);
	$admin = weTag_getAttribute('admin', $attribs, '', we_base_request::STRING);
	$mail = weTag_getAttribute('mail', $attribs, '', we_base_request::EMAIL);
	$mailfrom = weTag_getAttribute('mailfrom', $attribs, '', we_base_request::EMAIL);
	$charset = weTag_getAttribute('charset', $attribs, "iso-8859-1", we_base_request::STRING);
	$doctype = weTag_getAttribute('doctype', $attribs, '', we_base_request::STRING);
	$classid = weTag_getAttribute('classid', $attribs, '', we_base_request::INT);
	$pid = weTag_getAttribute('pid', $attribs, 0, we_base_request::INT);
	$forceedit = weTag_getAttribute('forceedit', $attribs, false, we_base_request::BOOL);

	switch($type){
		case 'document':
			$docID = $id ? : we_base_request::_(we_base_request::INT, 'we_delDocument_ID');
			if(!$docID){
				return '';
			}
			$doc = new we_webEditionDocument();
			$doc->initByID($docID);
			$table = FILE_TABLE;
			if($doctype){
				$doctypeID = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType LIKE "' . $GLOBALS['DB_WE']->escape($doctype) . '"');
				if($doc->DocType != $doctypeID){
					$GLOBALS['we_' . $type . '_delete_ok'] = false;
					return '';
				}
			}
			if($mail){
				$mailtext = sprintf(g_l('global', '[std_mailtext_delDoc]'), $doc->Path);
				$subject = g_l('global', '[std_subject_delDoc]');
			}
			break;
		case 'object':
			$docID = $id ? : we_base_request::_(we_base_request::INT, 'we_delObject_ID', $id);
			if(!$docID){
				return '';
			}
			$doc = new we_objectFile();
			$doc->initByID($docID, OBJECT_FILES_TABLE);
			$table = OBJECT_FILES_TABLE;
			if($classid && $doc->TableID != $classid){//FIXME: IsClassFolder
				$GLOBALS['we_' . $type . '_delete_ok'] = false;
				return '';
			}
			if($mail){
				$mailtext = sprintf(g_l('global', '[std_mailtext_delObj]'), $doc->Path);
				$subject = g_l('global', '[std_subject_delObj]');
			}
			break;
		default:
			return;
	}

	if($pid && $doc->ParentID != $pid){
		$GLOBALS['we_' . $type . '_delete_ok'] = false;
		return '';
	}

	$isOwner = !empty($_SESSION['webuser']['registered']) && isset($_SESSION['webuser']['ID']) && (
		($protected && $_SESSION['webuser']['ID'] == $doc->WebUserID) ||
		($userid && $_SESSION['webuser']['ID'] == $doc->getElement($userid))
		);


	$isAdmin = !empty($_SESSION['webuser']['registered']) && $admin && !empty($_SESSION['webuser'][$admin]);

	if($isAdmin || $isOwner || $forceedit){
		we_base_delete::deleteEntry($docID, $table);
		$GLOBALS['we_' . $type . '_delete_ok'] = true;
		if($mail){
			if(!$mailfrom){
				$mailfrom = 'dontReply@' . $_SERVER['SERVER_NAME'];
			}
			$phpmail = new we_mail_mail($mail, $subject, $mailfrom);
			$phpmail->setCharSet($charset);
			$phpmail->addTextPart(trim($mailtext));
			$phpmail->buildMessage();
			$phpmail->Send();
		}
	} else {
		$GLOBALS['we_' . $type . '_delete_ok'] = false;
	}
	return '';
}
