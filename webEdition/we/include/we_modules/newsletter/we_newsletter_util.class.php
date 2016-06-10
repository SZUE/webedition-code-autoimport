<?php

/**
 * webEdition CMS
 *
 * $Rev: 11901 $
 * $Author: mokraemer $
 * $Date: 2016-04-12 22:59:36 +0200 (Di, 12. Apr 2016) $
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
abstract class we_newsletter_util{

	static function unsubscribeNewsletter(we_database_base $db, $customer, $emailField, $abos, $paths, $unsubscribe_mail){
		$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_SUCCESS;
		$unsubscribe_mail = strtolower(preg_replace("|[\r\n,]|", '', $unsubscribe_mail));
		$GLOBALS['WE_NEWSLETTER_EMAIL'] = $unsubscribe_mail;
		if(!we_check_email($unsubscribe_mail)){
			$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_EMAIL_INVALID; // E-Mail ungueltig
			return false;
		}


		$db->query('DELETE FROM ' . NEWSLETTER_CONFIRM_TABLE . ' WHERE subscribe_mail ="' . $db->escape($unsubscribe_mail) . '"');

		$emailExists = ($customer ?
				self::removeFromDB($db, $emailField, $unsubscribe_mail, $abos) :
				self::removeFromFile($paths, $unsubscribe_mail));

		if($emailExists){
			return true;
		}
		$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = $GLOBALS['WE_REMOVENEWSLETTER_STATUS']? : we_newsletter_base::STATUS_EMAIL_EXISTS;
		return false;
	}

	private static function removeFromDB(we_database_base $db, $emailField, $unsubscribe_mail, array $abos){
		$customerFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="FieldAdds"', '', $db));

		$tmp = array();
		foreach($abos as $abo){
			$tmp[] = '"' . $db->escape($abo) . '"';
		}
		$where = ' WHERE ' . $emailField . '="' . $db->escape($unsubscribe_mail) . '"';
		$hash = getHash('SELECT ' . implode(',', $tmp) . ' FROM ' . CUSTOMER_TABLE . $where, $db);
		unset($tmp);
		$update = array();
		if(!$hash){
			return false;
		}
		$emailExists = false;
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
			$hook = new weHook('customer_preSave', '', array('customer' => &$fields, 'from' => 'tag', 'type' => 'modify', 'tagname' => 'addDelNewsletterEmail', 'isSubscribe' => false, 'isUnsubscribe' => true));
			$hook->executeHook();
			$db->query('UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter(array_merge($update, $fields)) . ' ' . $where);
		}
		return $emailExists;
	}

	private static function removeFromFile(array $paths, $unsubscribe_mail){
		$emailExists = false;
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

			if($fileChanged && !file_put_contents($path, implode("\n", array_map('trim', $file)) . "\n")){
				$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
				$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
				return false;
			}
			we_base_file::unlock($lock);
		}
		return $emailExists;
	}

	static function getNewsletterFields($confirmid, &$errorcode, $mail, $subscribe_mail){
		$errorcode = we_newsletter_base::STATUS_SUCCESS;
		if($confirmid){
			$h = getHash('SELECT * FROM ' . NEWSLETTER_CONFIRM_TABLE . ' WHERE confirmID="' . $GLOBALS['DB_WE']->escape($confirmid) . '" AND LOWER(subscribe_mail)="' . $GLOBALS['DB_WE']->escape(strtolower($mail)) . '"');
			if(!$h){
				$errorcode = we_newsletter_base::STATUS_CONFIRM_FAILED;
			}
			return $h;
		}

		$subscribe_mail = trim(preg_replace("|[\r\n,]|", '', $subscribe_mail));
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

	static function mailNewSuccessfullNewsletterActiviation($adminmailid, $adminemail, $adminsubject, $charset, array $f, $includeimages){
		if(!($adminmailid && $adminemail)){//inform admin of the new account
			return;
		}
		$db = $GLOBALS['DB_WE'];
		$phpmail = new we_mail_mail($adminemail, $adminsubject, $f['subscribe_mail'], $f['subscribe_mail']);

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

	static function addDoubleOptIn(we_database_base $db, $type, $customer_email_field, $f, array $abos, array $paths, $mailid, $attribs){
		$confirmID = md5(uniqid(__FUNCTION__, true));
		$lists = array();
		$emailExistsInOneOfTheLists = false;
		switch($type){
			case 'customer':
				$hash = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ' . $customer_email_field . '="' . $db->escape($f['subscribe_mail']) . '"', $db);
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
			case 'emailonly':
				$lists = $abos;
				break;
		}
		if($emailExistsInOneOfTheLists){
			$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_EMAIL_EXISTS;
			return;
		}

		$lists = implode(',', $lists);

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
			if(($urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE'], true, true))){
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
				$mailtextHTML = $mailid && we_base_file::isWeFile($mailid, FILE_TABLE, $GLOBALS['DB_WE']) ? we_getDocumentByID($mailid, '', $GLOBALS['DB_WE']) : '';
				$mailtextHTML = str_replace('###TITLE###', $f['subscribe_title'], ($f['subscribe_title'] ? preg_replace('%([^ ])###TITLE###%', '${1} ' . $f['subscribe_title'], $mailtextHTML) : $mailtextHTML));
			}

			$GLOBALS['WE_HTMLMAIL'] = false;

			if(isset($GLOBALS['we_doc'])){
				if(!isset($mywedoc)){
					$mywedoc = $GLOBALS['we_doc'];
				}
				unset($GLOBALS['we_doc']);
			}

			$mailtext = $mailid && we_base_file::isWeFile($mailid, FILE_TABLE, $db) ? we_getDocumentByID($mailid, '', $db, $charset) : '';
			$mailtext = str_replace('###TITLE###', $f['subscribe_title'], ($f['subscribe_title'] ? preg_replace('%([^ ])###TITLE###%', '${1} ' . $f['subscribe_title'], $mailtext) : $mailtext));

			$placeholderfieldsmatches = array();
			if(preg_match_all('/####PLACEHOLDER:DB::CUSTOMER_TABLE:(.[^#]{1,200})####/', $mailtext, $placeholderfieldsmatches)){
				$placeholderfields = $placeholderfieldsmatches[1];
				unset($placeholderfieldsmatches);
			}
			$placeholderReplaceValue = '';
			if($type === 'customer'){
				$customerHash = array_merge(getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ' . $customer_email_field . '="' . $db->escape($f['subscribe_mail']) . '"', $db), we_customer_customer::getEncryptedFields());
			}
			if(!empty($placeholderfields)){
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
			$phpmail = new we_mail_mail($f['subscribe_mail'], $subject, $from, $from);
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
	}

	static function addToFile(array $paths, $f){
		$emailwritten = false;
		foreach($paths as $p){
			$realPath = realpath($_SERVER['DOCUMENT_ROOT'] . ltrim($p, '/') . '/' . $p);
			if(!file_exists(dirname($realPath)) || strpos(realpath($realPath), realpath($_SERVER['DOCUMENT_ROOT'])) === FALSE){
				$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
				$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
				return;
			}

			while(($lock = we_base_file::lock(basename(__FILE__))) == false){
				usleep(500000);
			}

			if(($file = we_base_file::load($realPath)) && (
				preg_match("%[\r\n]" . $f['subscribe_mail'] . ",[^\r\n]+[\r\n]%i", $file) ||
				preg_match('%^' . $f['subscribe_mail'] . ",[^\r\n]+[\r\n]%i", $file))){
				// E-Mail schon vorhanden => Nix tun
			} else {
				$row = $f['subscribe_mail'] . ',' . $f['subscribe_html'] . ',' . $f['subscribe_salutation'] . ',' . $f['subscribe_title'] . ',' . $f['subscribe_firstname'] . ',' . $f['subscribe_lastname'] . "\n";
				if(!we_base_file::save($realPath, $row, 'ab+')){
					we_base_file::unlock($lock);
					t_e('save of file ' . $p . ' failed');
					$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_ERROR; // FATAL ERROR
					return;
				}
				$emailwritten = true;
				we_base_file::unlock($lock);
			}
		}
		if(!$emailwritten){
			$GLOBALS['WE_WRITENEWSLETTER_STATUS'] = we_newsletter_base::STATUS_EMAIL_EXISTS;
		}
	}

	static function addToDB(we_database_base $db, array $customerFieldPrefs, array $f, array $abos){
		$uid = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE ' . $customerFieldPrefs['customer_email_field'] . '="' . $db->escape($f['subscribe_mail']) . '"', '', $db);
		if(!$uid){
			$GLOBALS['WE_NEWSUBSCRIBER_PASSWORD'] = substr(md5(time()), 4, 8);
			$GLOBALS['WE_NEWSUBSCRIBER_USERNAME'] = $f['subscribe_mail'];
		}
		$fields = (!$uid ? array(
				'Username' => $f['subscribe_mail'],
				'Path' => '/' . $f['subscribe_mail'],
				'Password' => $GLOBALS['WE_NEWSUBSCRIBER_PASSWORD'],
				'MemberSince' => time(),
				'IsFolder' => 0,
				'ParentID' => 0,
				'LoginDenied' => 0,
				'LastLogin' => 0,
				'LastAccess' => 0,
				($customerFieldPrefs['customer_salutation_field'] != 'ID' ? $customerFieldPrefs['customer_salutation_field'] : '') => $f['subscribe_salutation'],
				($customerFieldPrefs['customer_title_field'] != 'ID' ? $customerFieldPrefs['customer_title_field'] : '') => $f['subscribe_title'],
				($customerFieldPrefs['customer_firstname_field'] != 'ID' ? $customerFieldPrefs['customer_firstname_field'] : '') => $f['subscribe_firstname'],
				($customerFieldPrefs['customer_lastname_field'] != 'ID' ? $customerFieldPrefs['customer_lastname_field'] : '') => $f['subscribe_lastname'],
				($customerFieldPrefs['customer_email_field'] != 'ID' ? $customerFieldPrefs['customer_email_field'] : '') => $f['subscribe_mail'],
				($customerFieldPrefs['customer_html_field'] != 'ID' ? $customerFieldPrefs['customer_html_field'] : '') => $f['subscribe_html'],
				) : array(
				'ModifyDate' => time(),
				'ModifiedBy' => 'frontend',
		));
		$hook = new weHook('customer_preSave', '', array('customer' => &$fields, 'from' => 'tag', 'type' => (!$uid ? 'new' : 'modify'), 'tagname' => 'addDelNewsletterEmail', 'isSubscribe' => true, 'isUnsubscribe' => false));
		$hook->executeHook();

		$db->query($uid ?
				'UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($fields) . ' WHERE ID=' . $uid :
				'INSERT INTO ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($fields));

		$set = array();
		$customerFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="FieldAdds"', '', $db));
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

			if(!$db->isColExist(CUSTOMER_TABLE, $abo)){
				$db->addCol(CUSTOMER_TABLE, $abo, 'VARCHAR(200) DEFAULT "' . $db->escape($setDefault) . '"');
				$fieldDefault = array('default' => isset($customerFields['Newsletter_Ok']['default']) && !empty($customerFields['Newsletter_Ok']['default']) ? $customerFields['Newsletter_Ok']['default'] : ',1');
				$customerFields[$abo] = $fieldDefault;
				$updateCustomerFields = true;
			}
			$set[$abo] = $setVal;
		}

		if($updateCustomerFields){
			$db->query('UPDATE ' . SETTINGS_TABLE . ' SET pref_value="' . $db->escape(we_serialize($customerFields, SERIALIZE_JSON)) . '" WHERE tool="webadmin" AND pref_name="FieldAdds"');
		}

		if($customerFieldPrefs['customer_html_field'] === 'ID'){
			t_e('warning', 'missing newsletter customer settings', 'no customer html field found in settings: field "ID" is not allowed');
		} else {
			$set[$customerFieldPrefs['customer_html_field']] = $f['subscribe_html'];
		}

		$db->query('UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($set) . ' WHERE ' . $customerFieldPrefs['customer_email_field'] . '="' . $db->escape($f["subscribe_mail"]) . '"');
	}

}
