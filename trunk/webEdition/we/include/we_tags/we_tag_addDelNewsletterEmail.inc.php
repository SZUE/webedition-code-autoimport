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
 * @param       string  $type           Anmeldeverfahren, moegliche Werte sind: customer, csv, emailonly
 * @param       string  $fieldGroup     Erwartet eine Feldgruppe (Bereich) aus der webEdition KV; Default: "Newsletter"; Nur bei $type == customer
 * @param       string  $mailingList    Erwartet den Namen der Mailing-Liste OHNE Feldgruppe (Bereich) aus der webEdition KV; Default: "Ok"; Nur bei $type == customer
 */
function we_tag_addDelNewsletterEmail(array $attribs){
	if(($foo = attributFehltError($attribs, 'type', __FUNCTION__))){
		return $foo;
	}
	$useListsArray = we_base_request::_(we_base_request::BOOL, 'we_use_lists__');
	$confirmID = we_base_request::_(we_base_request::STRING, 'confirmID', 0);
	$isSubscribe = isset($_REQUEST['we_subscribe_email__']) || $confirmID;
	$isUnsubscribe = isset($_REQUEST['we_unsubscribe_email__']);
	$doubleoptin = weTag_getAttribute('doubleoptin', $attribs, false, we_base_request::BOOL);

	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	$adminmailid = intval(weTag_getAttribute('adminmailid', $attribs, 0, we_base_request::INT));
	$adminsubject = weTag_getAttribute('adminsubject', $attribs, '', we_base_request::STRING);
	$adminemail = weTag_getAttribute('adminemail', $attribs, '', we_base_request::STRING);
	$fieldGroup = weTag_getAttribute('fieldGroup', $attribs, 'Newsletter', we_base_request::STRING);
	$abos = $paths = array();
	$db = new DB_WE();

	$customerFieldPrefs = we_newsletter_view::getSettings();

	if(!$useListsArray){
		switch($type){
			case 'customer':
				$tmpAbos = weTag_getAttribute('mailingList', $attribs, '', we_base_request::STRING_LIST);
				if(!$tmpAbos || $tmpAbos[0] == ''){
					$abos[0] = $fieldGroup . '_Ok';
				} else {// #6100
					foreach($tmpAbos as $abo){
						$abos[] = $fieldGroup . '_' . $abo;
					}
				}
				break;
			case 'csv':
				$paths = weTag_getAttribute('path', $attribs, array('newsletter.txt'), we_base_request::FILELISTA)? : array('newsletter.txt');
				break;
		}
	} elseif(isset($_REQUEST['we_subscribe_list__']) && is_array(($subList = we_base_request::_(we_base_request::HTML, 'we_subscribe_list__')))){
		switch($type){
			case 'customer':
				$tmpAbos = weTag_getAttribute('mailingList', $attribs, '', we_base_request::STRING_LIST);
				foreach($subList as $nr){
					$abos[] = $fieldGroup . '_' . $tmpAbos[intval($nr)];
				}
				break;
			default:
				$tmpPaths = weTag_getAttribute('path', $attribs, array(), we_base_request::FILELISTA);
				foreach($subList as $nr){
					$paths[] = $tmpPaths[intval($nr)];
				}
				break;
		}
		if(empty($abos) && empty($paths)){
			$GLOBALS['WE_MAILING_LIST_EMPTY'] = 1;
			$GLOBALS[($isSubscribe ? 'WE_WRITENEWSLETTER_STATUS' : 'WE_REMOVENEWSLETTER_STATUS')] = we_newsletter_base::STATUS_ERROR;
			return;
		}
	} else {
		$GLOBALS['WE_MAILING_LIST_EMPTY'] = 1;
		$GLOBALS[($isSubscribe ? 'WE_WRITENEWSLETTER_STATUS' : 'WE_REMOVENEWSLETTER_STATUS')] = we_newsletter_base::STATUS_ERROR;
		return;
	}

	$db->query('DELETE FROM ' . NEWSLETTER_CONFIRM_TABLE . ' WHERE expires<UNIX_TIMESTAMP()');

	//NEWSLETTER SUBSCTIPTION
	if($isSubscribe){
		$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_SUCCESS;
		$err = we_newsletter_base::STATUS_SUCCESS;
		$f = we_newsletter_util::getNewsletterFields(we_base_request::_(we_base_request::HTML, 'confirmID', 0), $err, we_base_request::_(we_base_request::EMAIL, 'mail', ''), we_base_request::_(we_base_request::EMAIL, 'we_subscribe_email__')); //FIXME: use data from above
		// Setting Globals FOR WE-Tags
		$GLOBALS['WE_NEWSLETTER_EMAIL'] = isset($f['subscribe_mail']) ? $f['subscribe_mail'] : '';
		$GLOBALS['WE_SALUTATION'] = isset($f['subscribe_salutation']) ? $f['subscribe_salutation'] : '';
		$GLOBALS['WE_TITLE'] = isset($f['subscribe_title']) ? $f['subscribe_title'] : '';
		$GLOBALS['WE_FIRSTNAME'] = isset($f['subscribe_firstname']) ? $f['subscribe_firstname'] : '';
		$GLOBALS['WE_LASTNAME'] = isset($f['subscribe_lastname']) ? $f['subscribe_lastname'] : '';
		if(!empty($f['lists'])){
			if(strpos($f['lists'], '.')){
				$paths = makeArrayFromCSV($f['lists']);
			} else {
				$abos = makeArrayFromCSV($f['lists']);
				$type = 'customer';
			}
		}

		if($err != we_newsletter_base::STATUS_SUCCESS){
			$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = $err;
			return;
		}
		if(empty($f)){
			$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR;
			return;
		}

		if($doubleoptin && (empty($_REQUEST['confirmID']))){ // Direkte Anmeldung mit doubleoptin => zuerst confirmmail verschicken.
			we_newsletter_util::addDoubleOptIn($db, $type, $customerFieldPrefs['customer_email_field'], $f, $abos, $paths, weTag_getAttribute('mailid', $attribs, '', we_base_request::INT), $attribs);
			return;
		}
		//confirmID wurde übermittelt, eine Bestätigung liegt also vor
		switch($type){
			case 'customer':
				we_newsletter_util::addToDB($db, $customerFieldPrefs, $f, $abos);
				break;
			case 'emailonly':
				//nicht in eine Liste eintragen sondern adminmail versenden
				$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_SUCCESS;
				we_newsletter_util::mailNewSuccessfullNewsletterActiviation($adminmailid, $adminemail, $adminsubject, DEFAULT_CHARSET, $f, weTag_getAttribute('includeimages', $attribs, false, we_base_request::BOOL));
				break;
			case 'csv':
				//in die Liste eintragen
				we_newsletter_util::addToFile($paths, $f);
                if($GLOBALS['WE_WRITENEWSLETTER_STATUS'] == we_newsletter_base::STATUS_SUCCESS){
                    we_newsletter_util::mailNewSuccessfullNewsletterActiviation($adminmailid, $adminemail, $adminsubject, DEFAULT_CHARSET, $f, weTag_getAttribute('includeimages', $attribs, false, we_base_request::BOOL));
                }
		}
		$db->query('DELETE FROM ' . NEWSLETTER_CONFIRM_TABLE . ' WHERE LOWER(subscribe_mail)="' . $db->escape(strtolower($f['subscribe_mail'])) . '"');
	}

	//NEWSLETTER UNSUBSCTIPTION
	if($isUnsubscribe){
		if(!we_newsletter_util::unsubscribeNewsletter($db, $type === 'customer', $customerFieldPrefs['customer_email_field'], $abos, $paths, we_base_request::_(we_base_request::EMAIL, 'we_unsubscribe_email__'))){
			return;
		}
        we_newsletter_util::mailNewSuccessfullNewsletterActiviation($adminmailid, $adminemail, $adminsubject, DEFAULT_CHARSET, $f, weTag_getAttribute('includeimages', $attribs, false, we_base_request::BOOL));
	}

	unset($_REQUEST['we_unsubscribe_email__'], $_REQUEST['we_subscribe_email__'], $_REQUEST['we_subscribe_html__'], $_REQUEST['we_subscribe_title__'], $_REQUEST['we_subscribe_salutation__'], $_REQUEST['we_subscribe_firstname__'], $_REQUEST['we_subscribe_lastname__'], $_REQUEST['we_subscribe_list__']);
}
