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
function _getMails($to){
	$we_recipient = array();
	foreach($to as $mail){
		if(strpos($mail, '@') === false){
			if((
				(isset($_SESSION["webuser"]["registered"]) && $_SESSION["webuser"]["registered"]) ||
				(isset($GLOBALS['ERROR']['customerResetPassword']) && $GLOBALS['ERROR']['customerResetPassword'] = we_customer_customer::PWD_ALL_OK)) &&
				isset($_SESSION["webuser"][$mail]) && strpos($_SESSION["webuser"][$mail], '@') !== false){ //wenn man registireten Usern was senden moechte
				if(we_check_email($_SESSION["webuser"][$mail])){
					$we_recipient[] = $_SESSION["webuser"][$mail];
				}
			} else if(isset($_REQUEST[$mail]) && strpos($_REQUEST[$mail], '@') !== false){ //email to friend test
				if(we_check_email($_REQUEST[$mail])){
					$we_recipient[] = $_REQUEST[$mail];
				}
			}
		} else {
			if(we_check_email($mail)){
				$we_recipient[] = $mail;
			}
		}
	}

	return $we_recipient;
}

function we_tag_sendMail($attribs, $content){
	if(($foo = attributFehltError($attribs, array("recipient" => false, 'from' => false), __FUNCTION__))){
		return $foo;
	}

	if($GLOBALS['we_doc']->InWebEdition){
		return;
	}

	$id = weTag_getAttribute("id", $attribs, we_base_request::_(we_base_request::INT, 'ID', 0));
	$from = weTag_getAttribute("from", $attribs);
	$reply = weTag_getAttribute("reply", $attribs);
	$recipient = weTag_getAttribute("recipient", $attribs);
	$recipientCC = weTag_getAttribute("recipientcc", $attribs, weTag_getAttribute("recipientCC", $attribs));
	$recipientBCC = weTag_getAttribute("recipientbcc", $attribs, weTag_getAttribute("recipientBCC", $attribs));

	$mimetype = weTag_getAttribute("mimetype", $attribs);
	$subject = weTag_getAttribute("subject", $attribs);
	$charset = weTag_getAttribute("charset", $attribs, "UTF-8");
	$includeimages = weTag_getAttribute("includeimages", $attribs, false, true);
	$useBaseHref = weTag_getAttribute("usebasehref", $attribs, true, true);
	$useFormmailLog = weTag_getAttribute("useformmaillog", $attribs, weTag_getAttribute("useformmailLog", $attribs, false, true), true);
	$useFormmailBlock = weTag_getAttribute("useformmailblock", $attribs, weTag_getAttribute("useformmailblock", $attribs, false, true), true);
	if($useFormmailBlock){
		$useFormmailLog = true;
	}
	$_blocked = false;

	if($id){
		$we_recipient = _getMails(explode(",", $recipient));
		$we_recipientCC = _getMails(explode(",", $recipientCC));
		$we_recipientBCC = _getMails(explode(",", $recipientBCC));

		if($useFormmailLog){
			// insert into log
			$GLOBALS['DB_WE']->query('INSERT INTO ' . FORMMAIL_LOG_TABLE . ' SET IP="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '"');
			if(FORMMAIL_EMPTYLOG > -1){
				$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_LOG_TABLE . ' WHERE unixTime<(NOW() - INTERVAL ' . intval(FORMMAIL_EMPTYLOG) . ' SECOND)');
			}

			if($useFormmailBlock){
				$_trials = FORMMAIL_TRIALS;
				// first delete all entries from blocktable which are older then now - blocktime
				$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_BLOCK_TABLE . ' WHERE blockedUntil!=-1 AND blockedUntil<UNIX_TIMESTAMP()');

				// check if ip is allready blocked
				if(f('SELECT 1 FROM ' . FORMMAIL_BLOCK_TABLE . " WHERE ip='" . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . "' LIMIT 1")){
					$_blocked = true;
				} else {
					// ip is not blocked, so see if we need to block it
					if(f('SELECT COUNT(1) FROM ' . FORMMAIL_LOG_TABLE . ' WHERE unixTime>(NOW()- INTERVAL ' . intval(FORMMAIL_SPAN) . " SECOND) AND ip='" . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . "'") > $_trials){
						$_blocked = true;
						// insert in block table
						$blockedUntil = (FORMMAIL_BLOCKTIME == -1) ? -1 : '(UNIX_TIMESTAMP()+' . FORMMAIL_BLOCKTIME . ')';
						$GLOBALS['DB_WE']->query('REPLACE INTO ' . FORMMAIL_BLOCK_TABLE . " (ip, blockedUntil) VALUES('" . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . "', " . $blockedUntil . ")");
					}
				}
			}
		}
		if($_blocked){
			$headline = "Fehler / Error";
			$content = g_l('global', "[formmailerror]") . getHtmlTag("br") . "&#8226; " . "Email dispatch blocked / Email Versand blockiert!";
			$css = array('media' => 'screen', 'rel' => 'stylesheet', 'type' => 'text/css', 'href' => WEBEDITION_DIR . 'css/global.php');

			echo we_html_tools::getHtmlTop() .
			getHtmlTag("link", $css) .
			'</head>' .
			getHtmlTag("body", array("class" => "weEditorBody"), we_html_tools::htmlDialogLayout(getHtmlTag("div", array("class" => "defaultgray"), $content), $headline)) .
			'</html>';

			exit;
		} else {
			if(!isset($_SESSION)){
				new we_base_sessionHandler();
			}
			$_SESSION['WE_SendMail'] = true;
			$codes = ($id > 0) && we_base_file::isWeFile($id, FILE_TABLE, $GLOBALS['DB_WE']) ? we_getDocumentByID($id) : '';
			unset($_SESSION['WE_SendMail']);
			if(!$codes){
				t_e('Document to send via we:sendMail is empty ID: ' . $id);
			}
			$phpmail = new we_util_Mailer($we_recipient, $subject, $from, $reply, $includeimages);
			if(isset($includeimages)){
				$phpmail->setIsEmbedImages($includeimages);
			}
			if(!empty($we_recipientCC)){
				$phpmail->setCC($we_recipientCC);
			}
			if(!empty($we_recipientBCC)){
				$phpmail->setBCC($we_recipientBCC);
			}
			if(isset($useBaseHref)){
				$phpmail->setIsUseBaseHref($useBaseHref);
			}
			$phpmail->setCharSet($charset);
			if($mimetype != 'text/html'){
				$phpmail->addTextPart(strip_tags(str_replace("&nbsp;", " ", str_replace("<br />", "\n", str_replace("<br/>", "\n", $codes)))));
			} else {
				$phpmail->addHTMLPart($codes);
			}
			$phpmail->buildMessage();
			$phpmail->Send();
		}
	}
}
