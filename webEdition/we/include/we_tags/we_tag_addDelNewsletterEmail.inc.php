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
function we_tag_addDelNewsletterEmail($attribs){
	$useListsArray = isset($_REQUEST["we_use_lists__"]);

	$isSubscribe = isset($_REQUEST["we_subscribe_email__"]) || isset($_REQUEST["confirmID"]);
	$isUnsubscribe = isset($_REQUEST["we_unsubscribe_email__"]);
	$doubleoptin = weTag_getAttribute("doubleoptin", $attribs, false, true);
	$forcedoubleoptin = weTag_getAttribute("forcedoubleoptin", $attribs, false, true);
	if($forcedoubleoptin){
		$doubleoptin = 1;
	}
	$customer = weTag_getAttribute("type", $attribs) == "customer";
	$emailonly = weTag_getAttribute("type", $attribs) == "emailonly";
	$adminmailid = intval(weTag_getAttribute("adminmailid", $attribs, 0));
	$adminsubject = weTag_getAttribute("adminsubject", $attribs);
	$adminemail = weTag_getAttribute("adminemail", $attribs);
	$fieldGroup = weTag_getAttribute("fieldGroup", $attribs);
	$fieldGroup = empty($fieldGroup) ? "Newsletter" : $fieldGroup;
	$abos = array();
	$paths = array();
	$db = new DB_WE();

	$_customerFieldPrefs = weNewsletterView::getSettings();

	if(!$useListsArray){
		if($customer){
			$abos = makeArrayFromCSV(weTag_getAttribute("mailingList", $attribs));
			if(!sizeof($abos) || (strlen($abos[0]) == 0)){
				$abos[0] = "Newsletter_Ok";
			}
		} else{
			if(!$emailonly){
				$paths = makeArrayFromCSV(weTag_getAttribute("path", $attribs));
				if(!sizeof($paths) || (strlen($paths[0]) == 0)){
					$paths[0] = "newsletter.txt";
				}
			}
		}
	} else{
		if(isset($_REQUEST["we_subscribe_list__"]) && is_array($_REQUEST["we_subscribe_list__"])){
			if($customer){
				$tmpAbos = makeArrayFromCSV(weTag_getAttribute("mailingList", $attribs));
				foreach($_REQUEST["we_subscribe_list__"] as $nr){
					array_push($abos, $fieldGroup . "_" . $tmpAbos[$nr]);
				}
			} else{
				$tmpPaths = makeArrayFromCSV(weTag_getAttribute("path", $attribs));
				foreach($_REQUEST["we_subscribe_list__"] as $nr){
					array_push($paths, $tmpPaths[$nr]);
				}
			}
			if(sizeof($abos) == 0 && sizeof($paths) == 0){
				$GLOBALS["WE_MAILING_LIST_EMPTY"] = 1;
				if($isSubscribe){
					$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR;
				}
				if($isUnsubscribe){
					$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR;
				}
				return;
			}
		} else{
			$GLOBALS["WE_MAILING_LIST_EMPTY"] = 1;
			if($isSubscribe){
				$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR;
			}
			if($isUnsubscribe){
				$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR;
			}
			return;
		}
	}

	$db->query("DELETE FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE expires<UNIX_TIMESTAMP()");

	/*	 * ******************************************************************************* */
	/*	 * *                          NEWSLETTER SUBSCTIPTION                           ** */
	/*	 * ******************************************************************************* */
	if($isSubscribe){
		$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_SUCCESS;
		$err = weNewsletterBase::STATUS_SUCCESS;
		$f = getNewsletterFields($_REQUEST, isset($_REQUEST["confirmID"]) ? $_REQUEST["confirmID"] : "", $err, isset($_REQUEST["mail"]) ? $_REQUEST["mail"] : "");
		// Setting Globals FOR WE-Tags
		$GLOBALS["WE_NEWSLETTER_EMAIL"] = isset($f["subscribe_mail"]) ? $f["subscribe_mail"] : '';
		$GLOBALS["WE_SALUTATION"] = isset($f["subscribe_salutation"]) ? $f["subscribe_salutation"] : "";
		$GLOBALS["WE_TITLE"] = isset($f["subscribe_title"]) ? $f["subscribe_title"] : "";
		$GLOBALS["WE_FIRSTNAME"] = isset($f["subscribe_firstname"]) ? $f["subscribe_firstname"] : "";
		$GLOBALS["WE_LASTNAME"] = isset($f["subscribe_lastname"]) ? $f["subscribe_lastname"] : "";
		if(isset($f["lists"]) && $f["lists"]){
			if(strpos($f["lists"], ".")){
				$paths = makeArrayFromCSV($f['lists']);
			} else{
				$abos = makeArrayFromCSV($f['lists']);
				$customer = 1;
			}
		}

		if($err != weNewsletterBase::STATUS_SUCCESS){
			$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = $err;
			return;
		}
		if(sizeof($f) == 0){
			$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR;
			return;
		}

		if($doubleoptin && (!isset($_REQUEST["confirmID"]))){ // Direkte ANmeldung mit doubleoptin => zuerst confirmmail verschicken.
			$confirmID = md5(uniqid(rand()));
			$lists = "";
			$emailExistsInOneOfTheLists = false;
			if($customer){
				$__query = getHash("SELECT * FROM " . CUSTOMER_TABLE . " WHERE " . $_customerFieldPrefs['customer_email_field'] . "='" . $db->escape($f["subscribe_mail"]) . "'", $db);
				if(count($__query)){
					$emailExistsInOneOfTheLists = true;
				}
				foreach($abos as $cAbo){
					$dbAbo = isset($__query[$cAbo]) ? $__query[$cAbo] : '';
					if(!empty($dbAbo)){
						$emailExistsInOneOfTheLists = true;
					}
					$lists .= $cAbo . ",";
				}
			} else{
				if(!$emailonly){
					foreach($paths as $p){
						if(!$emailExistsInOneOfTheLists){
							$realPath = (substr($p, 0, 1) == "/") ? ($_SERVER['DOCUMENT_ROOT'] . $p) : ($_SERVER['DOCUMENT_ROOT'] . "/" . $p);
							if(@file_exists($realPath)){
								$fh = @fopen($realPath, "rb");
								if($fh){
									$file = "";
									if(filesize($realPath)){
										while(!feof($fh))
											$file.=fread($fh, filesize($realPath));
									}
									fclose($fh);
									if(preg_match("%[\r\n]" . $f["subscribe_mail"] . ",[^\r\n]+[\r\n]%i", $file) || preg_match('%^' . $f["subscribe_mail"] . ",[^\r\n]+[\r\n]%i", $file)){
										$emailExistsInOneOfTheLists = true; // E-Mail does not exists in one of the lists
									}
								} else{
									$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR; // FATAL ERROR
									$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR; // FATAL ERROR
									return;
								}
							} else{
								$emailExistsInOneOfTheLists = false; // List does not exists, so email can't also exists
							}
						}
						$lists .= $p . ",";
					}
				}
			}
			if($emailExistsInOneOfTheLists){
				$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_EMAIL_EXISTS;
				return;
			}

			$lists = rtrim($lists, ',');

			$db->query('DELETE FROM ' . NEWSLETTER_CONFIRM_TABLE . " WHERE LOWER(subscribe_mail) = LOWER('" . $db->escape($f["subscribe_mail"]) . "')");

			$mailid = weTag_getAttribute("mailid", $attribs);
			$expiredoubleoptin = weTag_getAttribute("expiredoubleoptin", $attribs, 1440) * 60; // in secs

			if($mailid){

				$q = "INSERT INTO " . NEWSLETTER_CONFIRM_TABLE . " (confirmID,subscribe_mail,subscribe_html,subscribe_salutation,subscribe_title,subscribe_firstname,subscribe_lastname,lists,expires)
							VALUES ('" . $db->escape($confirmID) . "','" . $db->escape($f["subscribe_mail"]) . "','" . $db->escape($f["subscribe_html"]) . "','" . $db->escape($f["subscribe_salutation"]) . "','" . $db->escape($f["subscribe_title"]) . "','" . $db->escape($f["subscribe_firstname"]) . "','" . $db->escape($f["subscribe_lastname"]) . "','" . $db->escape($lists) . "','" . ($expiredoubleoptin + time()) . "')";

				$db->query($q);

				$id = weTag_getAttribute("id", $attribs);
				$subject = weTag_getAttribute("subject", $attribs, 'newsletter');
				$from = weTag_getAttribute("from", $attribs, "newsletter@" . $_SERVER['SERVER_NAME']);

				$use_https_refer = f("SELECT pref_value FROM " . NEWSLETTER_PREFS_TABLE . " WHERE pref_name='use_https_refer'", 'pref_value', $db);
				$protocol = ($use_https_refer ? "https://" : "http://");

				$port = defined("HTTP_PORT") ? HTTP_PORT : ($use_https_refer ? 443 : 80);
				$basehref = $protocol . $_SERVER['SERVER_NAME'] . ":" . $port;

				$confirmLink = $id ? id_to_path($id, FILE_TABLE) : $_SERVER["SCRIPT_NAME"];

				$confirmLink .= "?confirmID=" . $confirmID . "&mail=" . rawurlencode($f["subscribe_mail"]);

				$confirmLink = $protocol . $_SERVER['SERVER_NAME'] . (($port && ($port != 80)) ? ":$port" : "") . $confirmLink;
				$GLOBALS["WE_MAIL"] = $f["subscribe_mail"];
				$GLOBALS["WE_TITLE"] = "###TITLE###";
				$GLOBALS["WE_SALUTATION"] = $f["subscribe_salutation"];
				$GLOBALS["WE_FIRSTNAME"] = $f["subscribe_firstname"];
				$GLOBALS["WE_LASTNAME"] = $f["subscribe_lastname"];
				$GLOBALS["WE_CONFIRMLINK"] = $confirmLink;

				if($f["subscribe_html"]){
					$GLOBALS["WE_HTMLMAIL"] = 1;

					if(isset($GLOBALS['we_doc'])){
						$mywedoc = $GLOBALS['we_doc'];
						unset($GLOBALS['we_doc']);
					}
					$mailtextHTML = ($mailid > 0) && weFileExists($mailid, FILE_TABLE, $GLOBALS['DB_WE']) ? we_getDocumentByID($mailid) : '';
					if($f["subscribe_title"]){
						$mailtextHTML = preg_replace('%([^ ])###TITLE###%', '\1 ' . $f["subscribe_title"], $mailtextHTML);
					}
					$mailtextHTML = str_replace('###TITLE###', $f["subscribe_title"], $mailtextHTML);
				}

				$GLOBALS["WE_HTMLMAIL"] = 0;

				if(isset($GLOBALS['we_doc'])){
					if(!isset($mywedoc)){
						$mywedoc = $GLOBALS['we_doc'];
					}
					unset($GLOBALS['we_doc']);
				}


				$charset = isset($mywedoc->elements["Charset"]["dat"]) && $mywedoc->elements["Charset"]["dat"] != "" ? $mywedoc->elements["Charset"]["dat"] : $GLOBALS['WE_BACKENDCHARSET'];
				$mailtext = ($mailid > 0) && weFileExists($mailid, FILE_TABLE, $db) ? we_getDocumentByID($mailid, "", $db, $charset) : '';

				if($f["subscribe_title"]){
					$mailtext = preg_replace('%([^ ])###TITLE###%', '\1 ' . $f["subscribe_title"], $mailtext);
				}
				$mailtext = str_replace('###TITLE###', $f["subscribe_title"], $mailtext);



				$pattern = "/####PLACEHOLDER:DB::CUSTOMER_TABLE:(.[^#]{1,200})####/";
				$placeholderfieldsmatches = array();
				preg_match_all($pattern, $mailtext, $placeholderfieldsmatches);
				$placeholderfields = $placeholderfieldsmatches[1];
				unset($placeholderfieldsmatches);

				$placeholderReplaceValue = "";
				if($customer){
					$db->query("SELECT * FROM " . CUSTOMER_TABLE . " WHERE " . $_customerFieldPrefs['customer_email_field'] . "='" . $db->escape($f["subscribe_mail"]) . "'");
					$db->next_record();
				}
				if(is_array($placeholderfields)){

					foreach($placeholderfields as $phf){
						$placeholderReplaceValue = $customer ? $db->f($phf) : "";
						$mailtext = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $placeholderReplaceValue, $mailtext);
						$mailtextHTML = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $placeholderReplaceValue, $mailtextHTML);
					}
				}
				$recipientCC = weTag_getAttribute("recipientCC", $attribs);
				$recipientBCC = weTag_getAttribute("recipientBCC", $attribs);
				$includeimages = weTag_getAttribute("includeimages", $attribs, false, true);
				//FIXME: this is unused???
				//$useBaseHref = weTag_getAttribute("usebasehref", $attribs, true, true, true);
				$toCC = explode(',', $recipientCC);
				$we_recipientCC = array();
				foreach($toCC as $cc){
					if(strpos($cc, '@') === false){
						if(isset($_SESSION["webuser"]["registered"]) && $_SESSION["webuser"]["registered"] && isset($_SESSION["webuser"][$cc]) && strpos($_SESSION["webuser"][$cc], '@') !== false){ //wenn man registrierten Usern was senden moechte
							$we_recipientCC[] = $_SESSION["webuser"][$cc];
						} else if(isset($_REQUEST[$cc]) && strpos($_REQUEST[$cc], '@') !== false){ //email to friend test
							$we_recipientCC[] = $_REQUEST[$cc];
						}
					} else{
						$we_recipientCC[] = $cc;
					}
				}
				$toBCC = explode(',', $recipientBCC);
				$we_recipientBCC = array();
				foreach($toBCC as $bcc){
					if(strpos($bcc, '@') === false){
						if(isset($_SESSION["webuser"]["registered"]) && $_SESSION["webuser"]["registered"] && isset($_SESSION["webuser"][$bcc]) && strpos("@", $_SESSION["webuser"][$bcc]) !== false){ //wenn man registrierte Usern was senden moechte
							$we_recipientBCC[] = $_SESSION["webuser"][$bcc];
						} else if(isset($_REQUEST[$bcc]) && strpos("@", $_REQUEST[$bcc]) !== false){ //email to friend test
							$we_recipientBCC[] = $_REQUEST[$bcc];
						}
					} else{
						$we_recipientBCC[] = $bcc;
					}
				}
				$phpmail = new we_util_Mailer($f["subscribe_mail"], $subject, $from, $from);
				if(isset($includeimages)){
					$phpmail->setIsEmbedImages($includeimages);
				} else{
					$phpmail->setBaseDir($basehref);
				}
				if(!empty($we_recipientCC)){
					$phpmail->setCC($we_recipientCC);
				}
				if(!empty($we_recipientBCC)){
					$phpmail->setBCC($we_recipientBCC);
				}

				$phpmail->setCharSet($charset);


				if($f["subscribe_html"]){
					$phpmail->addHTMLPart($mailtextHTML);
				} else{
					$phpmail->addTextPart(trim($mailtext));
				}
				$phpmail->buildMessage();
				$phpmail->Send();
				$GLOBALS["WE_DOUBLEOPTIN"] = 1;

				if(isset($mywedoc))
					$GLOBALS['we_doc'] = $mywedoc;
			}else{
				$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR;
				return;
			}
		} else{ //confirmID wurde �bermittelt, eine Best�tigung liegt also vor
			$emailwritten = 0;
			if($customer){
				$__db = new DB_WE();
				$__id = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE ' . $_customerFieldPrefs['customer_email_field'] . '="' . $__db->escape($f["subscribe_mail"]) . '"', 'ID', $__db);
				if($__id == ''){
					$GLOBALS["WE_NEWSUBSCRIBER_PASSWORD"] = substr(md5(time()), 4, 8);
					$GLOBALS["WE_NEWSUBSCRIBER_USERNAME"] = $f["subscribe_mail"];
				}
				$fields = ($__id == '' ? array(
						'Username' => $f["subscribe_mail"],
						'Text' => $f["subscribe_mail"],
						'Password' => $GLOBALS["WE_NEWSUBSCRIBER_PASSWORD"],
						'MemberSince' => time(),
						'IsFolder' => 0,
						'Icon' => 'customer.gif',
						'ParentID' => 0,
						'LoginDenied' => 0,
						'LastLogin' => 0,
						'LastAccess' => 0,
						$_customerFieldPrefs['customer_salutation_field'] => $$f["subscribe_salutation"],
						$_customerFieldPrefs['customer_title_field'] => $f["subscribe_title"],
						$_customerFieldPrefs['customer_firstname_field'] => $f["subscribe_firstname"],
						$_customerFieldPrefs['customer_lastname_field'] => $f["subscribe_lastname"],
						$_customerFieldPrefs['customer_email_field'] => $f["subscribe_mail"],
						$_customerFieldPrefs['customer_html_field'] => $f["subscribe_html"],
						) : array(
						'ModifyDate' => time(),
						'ModifiedBy' => 'frontend',
						));
				$hook = new weHook('customer_preSave', '', array('customer' => &$fields, 'from' => 'tag', 'type' => ($__id == '' ? 'new' : 'modify'), 'tagname' => 'addDelNewsletterEmail', 'isSubscribe' => $isSubscribe, 'isUnsubscribe' => $isUnsubscribe));
				$ret = $hook->executeHook();

				if($__id == ''){
					$__db->query('INSERT INTO ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($fields));
				} else{
					$__db->query('UPDATE ' . CUSTOMER_TABLE . " SET " . we_database_base::arraySetter($fields) . " WHERE ID=" . $__id);
				}


				$__set = "";
				$__customerFields = f('SELECT Value FROM ' . CUSTOMER_ADMIN_TABLE . ' WHERE Name="FieldAdds"', 'Value', $__db);
				$__customerFields = $__customerFields ? unserialize($__customerFields) : '';
				$updateCustomerFields = false;
				foreach($abos as $abo){
					if(isset($__customerFields[$abo]["default"]) && !empty($__customerFields[$abo]["default"])){
						$__setVals = explode(",", $__customerFields[$abo]["default"]);
					} else if(isset($__customerFields["Newsletter_Ok"]["default"]) && !empty($__customerFields[$abo]["default"])){
						$__setVals = explode(",", $__customerFields["Newsletter_Ok"]["default"]);
					} else{
						$__setVals = array("", "1");
					}

					switch(true){
						case is_array($__setVals) && count($__setVals) > 1 :
							$__setDefault = $__setVals[0];
							$__setVal = $__setVals[1];
							break;
						case is_array($__setVals) && count($__setVals) == 1 :
							$__setDefault = "";
							$__setVal = $__setVals[0];
							break;
						default :
							$__setDefault = "";
							$__setVal = "1";
							break;
					}

					$__db->query("SHOW COLUMNS FROM " . CUSTOMER_TABLE . " LIKE '" . $__db->escape($abo) . "'");
					if($__db->num_rows() < 1){
						$__db->query("ALTER TABLE " . CUSTOMER_TABLE . " ADD " . $__db->escape($abo) . " VARCHAR(200) DEFAULT '" . $__db->escape($__setDefault) . "'");
						$fieldDefault = array("default" => isset($__customerFields['Newsletter_Ok']['default']) && !empty($__customerFields['Newsletter_Ok']['default']) ? $__customerFields['Newsletter_Ok']['default'] : ",1");
						$__customerFields[$abo] = $fieldDefault;
						$updateCustomerFields = true;
					}
					$__set .= "$abo='" . $__db->escape($__setVal) . "', ";
				}

				if($updateCustomerFields){
					$__db->query("UPDATE " . CUSTOMER_ADMIN_TABLE . " SET Value='" . $__db->escape(serialize($__customerFields)) . "' WHERE Name='FieldAdds'");
				}

				$__set .= $_customerFieldPrefs['customer_html_field'] . '= \'' . $__db->escape($f["subscribe_html"]) . '\'';
				$__db->query("UPDATE " . CUSTOMER_TABLE . " SET " . $__set . " WHERE " . $_customerFieldPrefs['customer_email_field'] . "='" . $__db->escape($f["subscribe_mail"]) . "'");
				$__db->query("DELETE FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE subscribe_mail ='" . $__db->escape($f["subscribe_mail"]) . "'");
			} else{
				if(!$emailonly){ //in die Liste eintragen
					foreach($paths as $path){

						$path = (substr($path, 0, 1) == "/") ? ($_SERVER['DOCUMENT_ROOT'] . $path) : ($_SERVER['DOCUMENT_ROOT'] . "/" . $path);

						if(!@file_exists(dirname($path))){
							$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR; // FATAL ERROR
							$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR; // FATAL ERROR
							return;
						}


						$ok = true;

						$fh = @fopen($path, "rb");
						if($fh){
							$file = "";
							if(filesize($path)){
								while(!feof($fh))
									$file.=fread($fh, filesize($path));
							}
							fclose($fh);
							if((preg_match("%[\r\n]" . $f["subscribe_mail"] . ",[^\r\n]+[\r\n]%i", $file) || preg_match('%^' . $f["subscribe_mail"] . ",[^\r\n]+[\r\n]%i", $file))){
								$ok = false; // E-Mail schon vorhanden => Nix tun
							}
						}
						if($ok){
							$fh = @fopen($path, "ab+");
							if($fh){
								$row = $f["subscribe_mail"] . "," . $f["subscribe_html"] . "," . $f["subscribe_salutation"] . "," . $f["subscribe_title"] . "," . $f["subscribe_firstname"] . "," . $f["subscribe_lastname"] . "\n";
								if(!@fwrite($fh, $row)){
									fclose($fh);
									$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR; // FATAL ERROR
									return;
								}
								fclose($fh);
								$emailwritten++;
							} else{
								$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR; // FATAL ERROR
								return;
							}
						}
						@chmod($path);
					}
					if($emailwritten == 0){
						$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_EMAIL_EXISTS;
					}
				} else{ //nicht in eine Liste eintragen sondern adminmail versenden
					$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_SUCCESS;
				}

				if($adminmailid && $adminemail){//inform admin of the new account
					$phpmail = new we_util_Mailer($adminemail, $adminsubject, $f["subscribe_mail"], $f["subscribe_mail"]);

					$adminmailtextHTML = ($adminmailid > 0) && weFileExists($adminmailid, FILE_TABLE, $db) ? we_getDocumentByID($adminmailid, '', $db, $charset) : '';
					$phpmail->setCharSet($charset);

					$adminmailtextHTML = str_replace('###MAIL###', $f["subscribe_mail"], $adminmailtextHTML);
					$adminmailtextHTML = str_replace('###SALUTATION###', $f["subscribe_salutation"], $adminmailtextHTML);
					$adminmailtextHTML = str_replace('###TITLE###', $f["subscribe_title"], $adminmailtextHTML);
					$adminmailtextHTML = str_replace('###FIRSTNAME###', $f["subscribe_firstname"], $adminmailtextHTML);
					$adminmailtextHTML = str_replace('###LASTNAME###', $f["subscribe_lastname"], $adminmailtextHTML);
					$adminmailtextHTML = str_replace('###HTML###', $f["subscribe_html"], $adminmailtextHTML);
					$includeimages = weTag_getAttribute("includeimages", $attribs, false, true);
					$phpmail->addHTMLPart($adminmailtextHTML);
					if(isset($includeimages)){
						$phpmail->setIsEmbedImages($includeimages);
					}
					$phpmail->buildMessage();
					$phpmail->Send();
				}

				$db->query('DELETE FROM ' . NEWSLETTER_CONFIRM_TABLE . " WHERE subscribe_mail ='" . $db->escape($f["subscribe_mail"]) . "'");
			}
		}
	}

	/*	 * ******************************************************************************* */
	/*	 * *                         NEWSLETTER UNSUBSCTIPTION                          ** */
	/*	 * ******************************************************************************* */
	if($isUnsubscribe){
		if(!we_unsubscribeNL($db, $customer, $_customerFieldPrefs, $abos, $paths)){
			return;
		}
	}

	unset($_REQUEST["we_unsubscribe_email__"]);
	unset($_REQUEST["we_subscribe_email__"]);
	unset($_REQUEST["we_subscribe_html__"]);
	unset($_REQUEST["we_subscribe_title__"]);
	unset($_REQUEST["we_subscribe_salutation__"]);
	unset($_REQUEST["we_subscribe_firstname__"]);
	unset($_REQUEST["we_subscribe_lastname__"]);
	unset($_REQUEST["we_subscribe_list__"]);
}

function we_unsubscribeNL($db, $customer, $_customerFieldPrefs, $abos, $paths){
	$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_SUCCESS;
	$unsubscribe_mail = preg_replace("|[\r\n,]|", "", trim($_REQUEST["we_unsubscribe_email__"]));
	$GLOBALS["WE_NEWSLETTER_EMAIL"] = $unsubscribe_mail;
	if(!we_check_email($unsubscribe_mail)){
		$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_EMAIL_INVALID; // E-Mail ungueltig
		return false;
	}

	$emailExists = false;

	$db->query('DELETE FROM ' . NEWSLETTER_CONFIRM_TABLE . " WHERE subscribe_mail ='" . $db->escape($unsubscribe_mail) . "'");

	if($customer){
		$__db = new DB_WE();

		$__customerFields = f('SELECT Value FROM ' . CUSTOMER_ADMIN_TABLE . ' WHERE Name="FieldAdds"', 'Value', $__db);
		$__customerFields = $__customerFields ? unserialize($__customerFields) : '';

		$__where = ' WHERE ' . $_customerFieldPrefs['customer_email_field'] . '="' . $__db->escape($unsubscribe_mail) . '"';
		$tmp = array();
		foreach($abos as $abo){
			$tmp[] = '"' . $__db->escape($abo) . '"';
		}
		$__db->query('SELECT ' . implode(',', $tmp) . ' FROM ' . CUSTOMER_TABLE . $__where);
		unset($tmp);
		$__update = array();
		if($__db->next_record()){
			foreach($abos as $abo){
				$fieldDefault = (isset($__customerFields[$abo]["default"]) ? $__customerFields[$abo]["default"] : "");
				$fieldDefaults = explode(',', $fieldDefault);
				$aboNeg = is_array($fieldDefaults) && count($fieldDefaults) > 1 ? $fieldDefaults[0] : "";

				$dbAbo = $__db->f($abo);
				if(!empty($dbAbo) || $dbAbo != $aboNeg){
					$__update[$abo] = $aboNeg;
					$emailExists = true;
				}
			}
			if($emailExists){
				$fields = array(
					'ModifyDate' => time(),
					'ModifiedBy' => 'frontend',
				);
				$hook = new weHook('customer_preSave', '', array('customer' => &$fields, 'from' => 'tag', 'type' => 'modify', 'tagname' => 'addDelNewsletterEmail', 'isSubscribe' => 0, 'isUnsubscribe' => 1));
				$ret = $hook->executeHook();
				$__db->query("UPDATE " . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter(array_merge($__update, $fields)) . ' ' . $__where);
			}
		}
	} else{

		foreach($paths as $path){

			$path = (substr($path, 0, 1) == "/") ? ($_SERVER['DOCUMENT_ROOT'] . $path) : ($_SERVER['DOCUMENT_ROOT'] . "/" . $path);

			if(!@file_exists(dirname($path))){
				$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR; // FATAL ERROR
				$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR; // FATAL ERROR
				return false;
			}

			// #4158
			$file = @file($path);
			if(!$file)
				continue;

			$fileChanged = false;
			foreach($file as $i => $line){
				if(mb_substr($line, 0, mb_strlen($unsubscribe_mail) + 1) == "$unsubscribe_mail,"){
					$emailExists = true;
					unset($file[$i]);
					$fileChanged = true;
				}
			}

			if($fileChanged){
				$success = file_put_contents($path, implode("\n", array_map('trim', $file)) . "\n");
				if(!$success){
					$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = weNewsletterBase::STATUS_ERROR; // FATAL ERROR
				}
			}
			//
		}
	}

	if(!$emailExists){
		$GLOBALS['WE_REMOVENEWSLETTER_STATUS'] = weNewsletterBase::STATUS_EMAIL_EXISTS;
		return false;
	}
	return true;
}

function getNewsletterFields($request, $confirmid, &$errorcode, $mail = ""){

	$errorcode = weNewsletterBase::STATUS_SUCCESS;
	if($confirmid){
		$_h = getHash("SELECT * FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE confirmID = '" . escape_sql_query($confirmid) . "' AND subscribe_mail='" . escape_sql_query($mail) . "'", new DB_WE());
		if(empty($_h)){
			$errorcode = weNewsletterBase::STATUS_CONFIRM_FAILED;
		}
		return $_h;
	}

	$subscribe_mail = preg_replace("|[\r\n,]|", "", trim($request["we_subscribe_email__"]));
	if(strlen($subscribe_mail) == 0){
		$errorcode = weNewsletterBase::STATUS_EMAIL_INVALID;
		return array();
	}

	if(!we_check_email($subscribe_mail)){
		$errorcode = weNewsletterBase::STATUS_EMAIL_INVALID; // E-Mail ungueltig
		return array();
	}

	$subscribe_html = (isset($request["we_subscribe_html__"]) ? $request["we_subscribe_html__"] : 0);
	$subscribe_salutation = (isset($request["we_subscribe_salutation__"]) ? preg_replace("|[\r\n,]|", "", $request["we_subscribe_salutation__"]) : '');
	$subscribe_title = (isset($request["we_subscribe_title__"]) ? preg_replace("|[\r\n,]|", "", $request["we_subscribe_title__"]) : '');
	$subscribe_firstname = (isset($request["we_subscribe_firstname__"]) ? preg_replace("|[\r\n,]|", "", $request["we_subscribe_firstname__"]) : '');
	$subscribe_lastname = (isset($request["we_subscribe_lastname__"]) ? preg_replace("|[\r\n,]|", "", $request["we_subscribe_lastname__"]) : '');

	return array("subscribe_mail" => trim($subscribe_mail),
		"subscribe_html" => trim($subscribe_html),
		"subscribe_salutation" => trim($subscribe_salutation),
		"subscribe_title" => trim($subscribe_title),
		"subscribe_firstname" => trim($subscribe_firstname),
		"subscribe_lastname" => trim($subscribe_lastname)
	);
}