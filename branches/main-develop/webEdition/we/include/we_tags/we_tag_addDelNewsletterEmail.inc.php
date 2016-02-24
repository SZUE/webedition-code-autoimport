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
function we_tag_addDelNewsletterEmail($attribs){
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

	$_customerFieldPrefs = we_newsletter_view::getSettings();

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
		$f = getNewsletterFields(we_base_request::_(we_base_request::HTML, 'confirmID', 0), $err, we_base_request::_(we_base_request::EMAIL, 'mail', '')); //FIXME: use data from above
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

		if($doubleoptin && (!isset($_REQUEST['confirmID']))){ // Direkte ANmeldung mit doubleoptin => zuerst confirmmail verschicken.
			$confirmID = md5(uniqid(__FUNCTION__, true));
			$lists = array();
			$emailExistsInOneOfTheLists = false;
			switch($type){
				case 'customer':
					$hash = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ' . $_customerFieldPrefs['customer_email_field'] . '="' . $db->escape($f['subscribe_mail']) . '"', $db);
					// #5589 start
					if($hash){
						foreach($abos as $cAbo){
							$dbAbo = isset($hash[$cAbo]) ? $hash[$cAbo] : false;
							if(!$dbAbo){
								$lists[] = $cAbo;
							}
						}
						if(empty($lists)){// subscriber exists in all lists
							$emailExistsInOneOfTheLists = true;
						}
						// #5589 end
					} else {
						$lists = $abos; //#9002
					}


					break;
				case 'csv':
					foreach($paths as $p){
						$realPath = realpath((substr($p, 0, 1) === '/') ? ($_SERVER['DOCUMENT_ROOT'] . $p) : ($_SERVER['DOCUMENT_ROOT'] . '/' . $p));
						if(file_exists($realPath)){
							$file = we_base_file::load($realPath);
							if($file !== false){// #5135
								if(!preg_match("%[\r\n]" . $f['subscribe_mail'] . ",[^\r\n]+[\r\n]%i", $file) && !preg_match('%^' . $f['subscribe_mail'] . ",[^\r\n]+[\r\n]%i", $file)){
									$lists[] = $p;
								}
							} else {
								t_e('newsletter file not found');
								$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
								$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
								return;
							}
						} else {
							t_e('newsletter file not found');
							$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
							$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
							return;
						}
					}
					if(empty($lists)){// #5135 subscriber exists in all lists
						$emailExistsInOneOfTheLists = true;
					}
					break;
			}
			if($emailExistsInOneOfTheLists){
				$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_EMAIL_EXISTS;
				return;
			}

			$lists = implode(',', $lists);

			$mailid = weTag_getAttribute('mailid', $attribs, '', we_base_request::STRING);

			if($mailid){

				$db->query('REPLACE INTO ' . NEWSLETTER_CONFIRM_TABLE . ' SET ' .
						we_database_base::arraySetter(array(
							'confirmID' => $confirmID,
							'subscribe_mail' => strtolower($f['subscribe_mail']),
							'subscribe_html' => $f['subscribe_html'],
							'subscribe_salutation' => $f['subscribe_salutation'],
							'subscribe_title' => $f['subscribe_title'],
							'subscribe_firstname' => $f['subscribe_firstname'],
							'subscribe_lastname' => $f['subscribe_lastname'],
							'lists' => $lists,
							'expires' => sql_function('UNIX_TIMESTAMP() + ' . weTag_getAttribute('expiredoubleoptin', $attribs, 1440, we_base_request::INT) * 60) // in secs
				)));

				$id = weTag_getAttribute('id', $attribs, 0, we_base_request::INT);
				$subject = weTag_getAttribute('subject', $attribs, 'newsletter', we_base_request::STRING);
				$from = weTag_getAttribute('from', $attribs, 'newsletter@' . $_SERVER['SERVER_NAME'], we_base_request::EMAIL);

				$use_https_refer = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="newsletter" AND pref_name="use_https_refer"', '', $db);
				$protocol = ($use_https_refer ? 'https://' : 'http://');

				$port = $use_https_refer ? 443 : 80;
				$basehref = $protocol . $_SERVER['SERVER_NAME'] . ':' . $port;

				$cnt = 0;
				$confirmLink = ($id ? id_to_path($id, FILE_TABLE) : $_SERVER['SCRIPT_NAME']) . '?confirmID=' . $confirmID . '&mail=' . rawurlencode($f['subscribe_mail']);
				$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE'], true, true);
				if($urlReplace){
					$confirmLink = str_replace('//', $protocol, preg_replace($urlReplace, array_keys($urlReplace), $confirmLink, -1, $cnt));
				}

				$confirmLink = ($cnt == 0 ? $protocol . $_SERVER['SERVER_NAME'] . (($port && ($port != 80)) ? ':' . $port : '') : '') . $confirmLink;
				$GLOBALS['WE_MAIL'] = $f['subscribe_mail'];
				$GLOBALS['WE_TITLE'] = '###TITLE###';
				$GLOBALS['WE_SALUTATION'] = $f['subscribe_salutation'];
				$GLOBALS['WE_FIRSTNAME'] = $f['subscribe_firstname'];
				$GLOBALS['WE_LASTNAME'] = $f['subscribe_lastname'];
				$GLOBALS['WE_CONFIRMLINK'] = $confirmLink;

				if($f['subscribe_html']){
					$GLOBALS['WE_HTMLMAIL'] = true;

					if(isset($GLOBALS['we_doc'])){
						$mywedoc = $GLOBALS['we_doc'];
						unset($GLOBALS['we_doc']);
					}
					$mailtextHTML = ($mailid > 0) && we_base_file::isWeFile($mailid, FILE_TABLE, $GLOBALS['DB_WE']) ? we_getDocumentByID($mailid, '', $GLOBALS['DB_WE']) : '';
					if($f['subscribe_title']){
						$mailtextHTML = preg_replace('%([^ ])###TITLE###%', '${1} ' . $f['subscribe_title'], $mailtextHTML);
					}
					$mailtextHTML = str_replace('###TITLE###', $f['subscribe_title'], $mailtextHTML);
				}

				$GLOBALS['WE_HTMLMAIL'] = false;

				if(isset($GLOBALS['we_doc'])){
					if(!isset($mywedoc)){
						$mywedoc = $GLOBALS['we_doc'];
					}
					unset($GLOBALS['we_doc']);
				}


				$charset = !empty($mywedoc->elements['Charset']['dat']) ? $mywedoc->elements['Charset']['dat'] : $GLOBALS['WE_BACKENDCHARSET'];
				$mailtext = ($mailid > 0) && we_base_file::isWeFile($mailid, FILE_TABLE, $db) ? we_getDocumentByID($mailid, '', $db, $charset) : '';

				if($f['subscribe_title']){
					$mailtext = preg_replace('%([^ ])###TITLE###%', '${1} ' . $f['subscribe_title'], $mailtext);
				}
				$mailtext = str_replace('###TITLE###', $f['subscribe_title'], $mailtext);



				$pattern = '/####PLACEHOLDER:DB::CUSTOMER_TABLE:(.[^#]{1,200})####/';
				$placeholderfieldsmatches = array();
				preg_match_all($pattern, $mailtext, $placeholderfieldsmatches);
				$placeholderfields = $placeholderfieldsmatches[1];
				unset($placeholderfieldsmatches);

				$placeholderReplaceValue = '';
				if($type === 'customer'){
					$customerHash = array_merge(getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ' . $_customerFieldPrefs['customer_email_field'] . '="' . $db->escape($f['subscribe_mail']) . '"', $db), we_customer_customer::getEncryptedFields());
				}
				if(is_array($placeholderfields)){
					foreach($placeholderfields as $phf){
						$placeholderReplaceValue = ($type === 'customer' && isset($customerHash[$phf])) ? $customerHash[$phf] : '';
						$mailtext = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $placeholderReplaceValue, $mailtext);
						$mailtextHTML = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $placeholderReplaceValue, $mailtextHTML);
					}
				}
				$toCC = weTag_getAttribute('recipientCC', $attribs, array(), we_base_request::EMAILLISTA);
				$toBCC = weTag_getAttribute('recipientBCC', $attribs, array(), we_base_request::EMAILLISTA);
				$includeimages = weTag_getAttribute('includeimages', $attribs, false, we_base_request::BOOL);
				$we_recipientCC = array();
				foreach($toCC as $cc){
					if(strpos($cc, '@') === false){
						if(!empty($_SESSION['webuser']['registered']) && isset($_SESSION['webuser'][$cc]) && strpos($_SESSION['webuser'][$cc], '@') !== false){ //wenn man registrierten Usern was senden moechte
							if(we_check_email($_SESSION['webuser'][$cc])){
								$we_recipientCC[] = $_SESSION['webuser'][$cc];
							}
						} else if(isset($_REQUEST[$cc]) && strpos($_REQUEST[$cc], '@') !== false){ //email to friend test
							if(we_check_email(($cc = we_base_request::_(we_base_request::EMAIL, $cc)))){
								$we_recipientCC[] = $cc;
							}
						}
					} elseif(we_check_email($cc)){
						$we_recipientCC[] = $cc;
					}
				}
				$we_recipientBCC = array();
				foreach($toBCC as $bcc){
					if(strpos($bcc, '@') === false){
						if(!empty($_SESSION['webuser']['registered']) && isset($_SESSION['webuser'][$bcc]) && strpos('@', $_SESSION['webuser'][$bcc]) !== false){ //wenn man registrierte Usern was senden moechte
							if(we_check_email($_SESSION['webuser'][$bcc])){
								$we_recipientBCC[] = $_SESSION['webuser'][$bcc];
							}
						} else if(isset($_REQUEST[$bcc]) && strpos('@', $_REQUEST[$bcc]) !== false){ //email to friend test
							if(we_check_email(($bcc = we_base_request::_(we_base_request::EMAIL, $bcc)))){
								$we_recipientBCC[] = $bcc;
							}
						}
					} elseif(we_check_email($bcc)){
						$we_recipientBCC[] = $bcc;
					}
				}
				$phpmail = new we_helpers_mail($f['subscribe_mail'], $subject, $from, $from);
				if(isset($includeimages)){
					$phpmail->setIsEmbedImages($includeimages);
				} else {
					$phpmail->setBaseDir($basehref);
				}
				if(!empty($we_recipientCC)){
					$phpmail->setCC($we_recipientCC);
				}
				if(!empty($we_recipientBCC)){
					$phpmail->setBCC($we_recipientBCC);
				}

				$phpmail->setCharSet($charset);


				if($f['subscribe_html']){
					$phpmail->addHTMLPart($mailtextHTML);
				} else {
					$phpmail->addTextPart(trim($mailtext));
				}
				$phpmail->buildMessage();
				$phpmail->Send();
				$GLOBALS['WE_DOUBLEOPTIN'] = 1;

				if(isset($mywedoc)){
					$GLOBALS['we_doc'] = $mywedoc;
				}
			} else {
				$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR;
				return;
			}
		} else { //confirmID wurde übermittelt, eine Bestätigung liegt also vor
			$emailwritten = 0;
			$__db = new DB_WE();
			switch($type){
				case 'customer':
					$uid = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE ' . $_customerFieldPrefs['customer_email_field'] . '="' . $__db->escape($f['subscribe_mail']) . '"', '', $__db);
					if(!$uid){
						$GLOBALS['WE_NEWSUBSCRIBER_PASSWORD'] = substr(md5(time()), 4, 8);
						$GLOBALS['WE_NEWSUBSCRIBER_USERNAME'] = $f['subscribe_mail'];
					}
					$fields = (!$uid ? array(
								'Username' => $f['subscribe_mail'],
								'Text' => $f['subscribe_mail'],
								'Path' => '/' . $f['subscribe_mail'],
								'Password' => $GLOBALS['WE_NEWSUBSCRIBER_PASSWORD'],
								'MemberSince' => time(),
								'IsFolder' => 0,
								'ParentID' => 0,
								'LoginDenied' => 0,
								'LastLogin' => 0,
								'LastAccess' => 0,
								($_customerFieldPrefs['customer_salutation_field'] != 'ID' ? $_customerFieldPrefs['customer_salutation_field'] : '') => $f['subscribe_salutation'],
								($_customerFieldPrefs['customer_title_field'] != 'ID' ? $_customerFieldPrefs['customer_title_field'] : '') => $f['subscribe_title'],
								($_customerFieldPrefs['customer_firstname_field'] != 'ID' ? $_customerFieldPrefs['customer_firstname_field'] : '') => $f['subscribe_firstname'],
								($_customerFieldPrefs['customer_lastname_field'] != 'ID' ? $_customerFieldPrefs['customer_lastname_field'] : '') => $f['subscribe_lastname'],
								($_customerFieldPrefs['customer_email_field'] != 'ID' ? $_customerFieldPrefs['customer_email_field'] : '') => $f['subscribe_mail'],
								($_customerFieldPrefs['customer_html_field'] != 'ID' ? $_customerFieldPrefs['customer_html_field'] : '') => $f['subscribe_html'],
									) : array(
								'ModifyDate' => time(),
								'ModifiedBy' => 'frontend',
					));
					$hook = new weHook('customer_preSave', '', array('customer' => &$fields, 'from' => 'tag', 'type' => (!$uid ? 'new' : 'modify'), 'tagname' => 'addDelNewsletterEmail', 'isSubscribe' => $isSubscribe, 'isUnsubscribe' => $isUnsubscribe));
					$hook->executeHook();

					$__db->query($uid ?
									'UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($fields) . ' WHERE ID=' . $uid :
									'INSERT INTO ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($fields));



					$set = array();
					$customerFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="FieldAdds"', '', $__db));
					$updateCustomerFields = false;
					foreach($abos as $abo){
						if(isset($customerFields[$abo]['default']) && ($customerFields[$abo]['default'])){
							$setVals = explode(',', $customerFields[$abo]['default']);
						} else if(isset($customerFields['Newsletter_Ok']['default']) && ($customerFields['Newsletter_Ok']['default'])){
							$setVals = explode(',', $customerFields['Newsletter_Ok']['default']);
						} else {
							$setVals = array('', '1');
						}

						switch(true){
							case is_array($setVals) && count($setVals) > 1 :
								$setDefault = $setVals[0];
								$setVal = $setVals[1];
								break;
							case is_array($setVals) && (count($setVals) == 1) :
								$setDefault = '';
								$setVal = $setVals[0];
								break;
							default :
								$setDefault = '';
								$setVal = '1';
								break;
						}

						if(!$__db->isColExist(CUSTOMER_TABLE, $abo)){
							$__db->addCol(CUSTOMER_TABLE, $abo, 'VARCHAR(200) DEFAULT "' . $__db->escape($setDefault) . '"');
							$fieldDefault = array('default' => isset($customerFields['Newsletter_Ok']['default']) && !empty($customerFields['Newsletter_Ok']['default']) ? $customerFields['Newsletter_Ok']['default'] : ',1');
							$customerFields[$abo] = $fieldDefault;
							$updateCustomerFields = true;
						}
						$set[$abo] = $setVal;
					}



					if($updateCustomerFields){
						$__db->query('UPDATE ' . SETTINGS_TABLE . ' SET pref_value="' . $__db->escape(we_serialize($customerFields, SERIALIZE_JSON)) . '" WHERE tool="webadmin" AND pref_name="FieldAdds"');
					}

					if($_customerFieldPrefs['customer_html_field'] != 'ID'){
						$set[$_customerFieldPrefs['customer_html_field']] = $f["subscribe_html"];
					} else {
						t_e('warning', 'missing newsletter customer settings', 'no customer html field found in settings: field "ID" is not allowed');
					}

					$__db->query('UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($set) . ' WHERE ' . $_customerFieldPrefs['customer_email_field'] . '="' . $__db->escape($f["subscribe_mail"]) . '"');
					break;
				case 'emailonly':
					//nicht in eine Liste eintragen sondern adminmail versenden
					$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_SUCCESS;
					_weMailNewSuccessfullNewsletterActiviation($adminmailid, $adminemail, $adminsubject, DEFAULT_CHARSET, $f, weTag_getAttribute('includeimages', $attribs, false, we_base_request::BOOL));
					break;
				case 'csv':
					//in die Liste eintragen
					foreach($paths as $p){
						$realPath = realpath((substr($p, 0, 1) === '/') ? ($_SERVER['DOCUMENT_ROOT'] . $p) : ($_SERVER['DOCUMENT_ROOT'] . '/' . $p));
						if(!file_exists(dirname($realPath)) || strpos(realpath($realPath), realpath($_SERVER['DOCUMENT_ROOT'])) === FALSE){
							$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
							$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
							return;
						}

						$ok = true;

						while(($lock = we_base_file::lock(basename(__FILE__))) == false){
							usleep(500000);
						}
						$file = we_base_file::load($realPath);
						if($file !== false){
							if((preg_match("%[\r\n]" . $f['subscribe_mail'] . ",[^\r\n]+[\r\n]%i", $file) || preg_match('%^' . $f['subscribe_mail'] . ",[^\r\n]+[\r\n]%i", $file))){
								$ok = false; // E-Mail schon vorhanden => Nix tun
							}
						}
						if($ok){
							$row = $f['subscribe_mail'] . ',' . $f['subscribe_html'] . ',' . $f['subscribe_salutation'] . ',' . $f['subscribe_title'] . ',' . $f['subscribe_firstname'] . ',' . $f['subscribe_lastname'] . "\n";
							if(we_base_file::save($realPath, $row, 'ab+')){
								$emailwritten++;
								we_base_file::unlock($lock);
							} else {
								we_base_file::unlock($lock);
								t_e('save of file ' . $p . ' failed');
								$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
								return;
							}
						}
						@chmod($path);
					}
					if($emailwritten == 0){
						$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_EMAIL_EXISTS;
					}


					_weMailNewSuccessfullNewsletterActiviation($adminmailid, $adminemail, $adminsubject, DEFAULT_CHARSET, $f, weTag_getAttribute('includeimages', $attribs, false, we_base_request::BOOL));
			}
			$__db->query('DELETE FROM ' . NEWSLETTER_CONFIRM_TABLE . ' WHERE LOWER(subscribe_mail)="' . $__db->escape(strtolower($f["subscribe_mail"])) . '"');
		}
	}

	//NEWSLETTER UNSUBSCTIPTION
	if($isUnsubscribe){
		if(!we_unsubscribeNL($db, $type === 'customer', $_customerFieldPrefs, $abos, $paths)){
			return;
		}
	}

	unset($_REQUEST['we_unsubscribe_email__'], $_REQUEST['we_subscribe_email__'], $_REQUEST['we_subscribe_html__'], $_REQUEST['we_subscribe_title__'], $_REQUEST['we_subscribe_salutation__'], $_REQUEST['we_subscribe_firstname__'], $_REQUEST['we_subscribe_lastname__'], $_REQUEST['we_subscribe_list__']);
}

function we_unsubscribeNL($db, $customer, $_customerFieldPrefs, $abos, $paths){
	$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_SUCCESS;
	$unsubscribe_mail = strtolower(preg_replace("|[\r\n,]|", '', we_base_request::_(we_base_request::EMAIL, 'we_unsubscribe_email__')));
	$GLOBALS['WE_NEWSLETTER_EMAIL'] = $unsubscribe_mail;
	if(!we_check_email($unsubscribe_mail)){
		$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_EMAIL_INVALID; // E-Mail ungueltig
		return false;
	}

	$emailExists = false;

	$db->query('DELETE FROM ' . NEWSLETTER_CONFIRM_TABLE . ' WHERE subscribe_mail ="' . $db->escape($unsubscribe_mail) . '"');

	if($customer){
		$customerFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="FieldAdds"', '', $db));

		$tmp = array();
		foreach($abos as $abo){
			$tmp[] = '"' . $db->escape($abo) . '"';
		}
		$where = ' WHERE ' . $_customerFieldPrefs['customer_email_field'] . '="' . $db->escape($unsubscribe_mail) . '"';
		$hash = getHash('SELECT ' . implode(',', $tmp) . ' FROM ' . CUSTOMER_TABLE . $where, $db);
		unset($tmp);
		$update = array();
		if($hash){
			foreach($abos as $abo){
				$fieldDefault = (isset($customerFields[$abo]['default']) ? $customerFields[$abo]['default'] : '');
				$fieldDefaults = explode(',', $fieldDefault);
				$aboNeg = is_array($fieldDefaults) && count($fieldDefaults) > 1 ? $fieldDefaults[0] : '';

				$dbAbo = $hash[$abo];
				if(!empty($dbAbo) || $dbAbo != $aboNeg){
					$update[$abo] = $aboNeg;
					$emailExists = true;
				}
			}
			if($emailExists){
				$fields = array(
					'ModifyDate' => sql_function('UNIX_TIMESTAMP()'),
					'ModifiedBy' => 'frontend',
				);
				$hook = new weHook('customer_preSave', '', array('customer' => &$fields, 'from' => 'tag', 'type' => 'modify', 'tagname' => 'addDelNewsletterEmail', 'isSubscribe' => 0, 'isUnsubscribe' => 1));
				$hook->executeHook();
				$db->query('UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter(array_merge($update, $fields)) . ' ' . $where);
			}
		}
	} else {

		foreach($paths as $path){
			$path = ($_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($path, '/'));

			if(!file_exists(dirname($path))){
				t_e('file ' . $path . ' doesn\'t exist');
				$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
				$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
				return false;
			}

			// #4158
			while(($lock = we_base_file::lock(basename(__FILE__))) == false){
				usleep(500000);
			}

			$file = file($path);
			if(!$file){
				we_base_file::unlock($lock);
				continue;
			}

			$fileChanged = false;
			$regex = '|' . preg_quote($unsubscribe_mail, '|') . ',|i';
			foreach($file as $i => $line){
				if(preg_match($regex, $line)){
					$emailExists = true;
					unset($file[$i]);
					$fileChanged = true;
				}
			}

			if($fileChanged){
				$success = file_put_contents($path, implode("\n", array_map('trim', $file)) . "\n");
				if(!$success){
					$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
				}
			}
			we_base_file::unlock($lock);
			//
		}
	}

	if(!$emailExists){
		$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_EMAIL_EXISTS;
		return false;
	}
	return true;
}

function getNewsletterFields($confirmid, &$errorcode, $mail = ''){
	$errorcode = we_newsletter_base::STATUS_SUCCESS;
	if($confirmid){
		$_h = getHash('SELECT * FROM ' . NEWSLETTER_CONFIRM_TABLE . ' WHERE confirmID="' . $GLOBALS['DB_WE']->escape($confirmid) . '" AND LOWER(subscribe_mail)="' . $GLOBALS['DB_WE']->escape(strtolower($mail)) . '"');
		if(!$_h){
			$errorcode = we_newsletter_base::STATUS_CONFIRM_FAILED;
		}
		return $_h;
	}

	$subscribe_mail = trim(preg_replace("|[\r\n,]|", '', we_base_request::_(we_base_request::EMAIL, 'we_subscribe_email__')));
	if(!$subscribe_mail){
		$errorcode = we_newsletter_base::STATUS_EMAIL_INVALID;
		return array();
	}

	if(!we_check_email($subscribe_mail)){
		$errorcode = we_newsletter_base::STATUS_EMAIL_INVALID; // E-Mail ungueltig
		return array();
	}

	return array(
		'subscribe_mail' => $subscribe_mail,
		'subscribe_html' => we_base_request::_(we_base_request::BOOL, 'we_subscribe_html__'),
		'subscribe_salutation' => trim(preg_replace("|[\r\n,]|", '', we_base_request::_(we_base_request::HTML, 'we_subscribe_salutation__', ''))),
		'subscribe_title' => trim(preg_replace("|[\r\n,]|", '', we_base_request::_(we_base_request::HTML, 'we_subscribe_title__', ''))),
		'subscribe_firstname' => trim(preg_replace("|[\r\n,]|", '', we_base_request::_(we_base_request::HTML, 'we_subscribe_firstname__', ''))),
		'subscribe_lastname' => trim(preg_replace("|[\r\n,]|", '', we_base_request::_(we_base_request::HTML, 'we_subscribe_lastname__', '')))
	);
}

function _weMailNewSuccessfullNewsletterActiviation($adminmailid, $adminemail, $adminsubject, $charset, $f, $includeimages){
	if(!($adminmailid && $adminemail)){//inform admin of the new account
		return;
	}
	$db = $GLOBALS['DB_WE'];
	$phpmail = new we_helpers_mail($adminemail, $adminsubject, $f['subscribe_mail'], $f['subscribe_mail']);

	$phpmail->setCharSet($charset);

	$adminmailtextHTML = strtr(
			(($adminmailid > 0) && we_base_file::isWeFile($adminmailid, FILE_TABLE, $db) ? we_getDocumentByID($adminmailid, '', $db, $charset) : '')
			, array(
		'###MAIL###' => $f['subscribe_mail'],
		'###SALUTATION###' => $f['subscribe_salutation'],
		'###TITLE###' => $f['subscribe_title'],
		'###FIRSTNAME###' => $f['subscribe_firstname'],
		'###LASTNAME###' => $f['subscribe_lastname'],
		'###HTML###' => $f['subscribe_html'],
	));
	$phpmail->addHTMLPart($adminmailtextHTML);
	if(isset($includeimages)){
		$phpmail->setIsEmbedImages($includeimages);
	}
	$phpmail->buildMessage();
	$phpmail->Send();
}
