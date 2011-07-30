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

define('WE_NEWSLETTER_STATUS_ERROR',-1);
define('WE_NEWSLETTER_STATUS_SUCCESS',0);
define('WE_NEWSLETTER_STATUS_EMAIL_EXISTS',1);
define('WE_NEWSLETTER_STATUS_EMAIL_INVALID',2);
define('WE_NEWSLETTER_STATUS_CONFIR_FAILED',3);

include_once $_SERVER['DOCUMENT_ROOT'].'/webEdition/lib/we/core/autoload.php';

function we_tag_addDelNewsletterEmail($attribs, $content) {
	$useListsArray = isset($_REQUEST["we_use_lists__"]);

	$isSubscribe = isset($_REQUEST["we_subscribe_email__"]) || isset($_REQUEST["confirmID"]);
	$isUnsubscribe = isset($_REQUEST["we_unsubscribe_email__"]);
	$doubleoptin = we_getTagAttribute("doubleoptin",$attribs,"",true);
	$forcedoubleoptin = we_getTagAttribute("forcedoubleoptin",$attribs,"",true);
	if ($forcedoubleoptin) $doubleoptin = 1;
	$customer = we_getTagAttribute("type",$attribs) == "customer" ? true : false;
	$emailonly = we_getTagAttribute("type",$attribs) == "emailonly" ? true : false;
	$fieldGroup = we_getTagAttribute("fieldGroup",$attribs);
	$fieldGroup = empty($fieldGroup) ? "Newsletter" : $fieldGroup;
	$abos = array();
	$paths = array();
	$db=new DB_WE();

	$db->query("SELECT * FROM " . NEWSLETTER_PREFS_TABLE);
	if($db->num_rows()){
		while($db->next_record()) {
			$_customerFieldPrefs[$db->f('pref_name')] = $db->f('pref_value');
		}
	} else {
		$_domainName = str_replace("www.","",SERVER_NAME);
		$_customerFieldPrefs = array(
			'black_list' => '',
			'customer_email_field' => 'Kontakt_Email',
			'customer_firstname_field' => 'Forename',
			'customer_html_field' => 'htmlMailYesNo',
			'customer_lastname_field' => 'Surname',
			'customer_salutation_field' => 'Anrede_Salutation',
			'customer_title_field' => 'Anrede_Title',
			'default_htmlmail' => '0',
			'isEmbedImages' => '0',
			'default_reply' => 'reply@'.$_domainName,
			'default_sender' => 'mailer@'.$_domainName,
			'female_salutation' => g_l('modules_newsletter','[default][female]'),
			'global_mailing_list' => '',
			'log_sending' => '1',
			'male_salutation' => g_l('modules_newsletter','[default][male]'),
			'reject_malformed' => '1',
			'reject_not_verified' => '1',
			'send_step' => '20',
			'send_wait' => '0',
			'test_account' => 'test@'.$_domainName,
			'title_or_salutation' => '0',
			'use_port' => '0',
			'use_https_refer' => '0',
			'additional_clp' => '0'
		);
		foreach ($_customerFieldPrefs as $name=>$value) {
			$db->query("INSERT INTO ".NEWSLETTER_PREFS_TABLE."(pref_name,pref_value) VALUES('".$db->escape($name)."','".$db->escape($value)."');");
		}
	}

	if (!$useListsArray) {
		if ($customer) {
			$abos = makeArrayFromCSV(we_getTagAttribute("mailingList",$attribs));
			if (!sizeof($abos) || (strlen($abos[0]) == 0)) {
				$abos[0] = "Newsletter_Ok";
			}
		} else {
			if ($emailonly) {

			} else {
				$paths = makeArrayFromCSV(we_getTagAttribute("path",$attribs));
				if (!sizeof($paths) || (strlen($paths[0]) == 0)) {
					$paths[0] = "newsletter.txt";
				}
			}
		}
	} else {
		if (isset($_REQUEST["we_subscribe_list__"]) && is_array($_REQUEST["we_subscribe_list__"])) {
			if ($customer) {
				$tmpAbos = makeArrayFromCSV(we_getTagAttribute("mailingList",$attribs));
				foreach($_REQUEST["we_subscribe_list__"] as $nr){
					array_push($abos,$fieldGroup."_".$tmpAbos[$nr]);
				}
			} else {
				$tmpPaths = makeArrayFromCSV(we_getTagAttribute("path",$attribs));
				foreach($_REQUEST["we_subscribe_list__"] as $nr){
					array_push($paths,$tmpPaths[$nr]);
				}
			}
			if(sizeof($abos) == 0 && sizeof($paths) == 0){
				$GLOBALS["WE_MAILING_LIST_EMPTY"] = 1;
				if($isSubscribe){
					$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR;
				}
				if($isUnsubscribe){
					$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR;
				}
				return;
			}
		} else {
			$GLOBALS["WE_MAILING_LIST_EMPTY"] = 1;
			if($isSubscribe){
				$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR;
			}
			if($isUnsubscribe){
				$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR;
			}
			return;
		}
	}

	$db->query("DELETE FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE expires<'".time()."'");

	/**********************************************************************************/
	/***                          NEWSLETTER SUBSCTIPTION                           ***/
	/**********************************************************************************/
	if ($isSubscribe) {
		$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_SUCCESS;
		$err = 0;
		$f = getNewsletterFields($_REQUEST,isset($_REQUEST["confirmID"]) ? $_REQUEST["confirmID"] : "",$err,isset($_REQUEST["mail"]) ? $_REQUEST["mail"] : "");
		// Setting Globals FOR WE-Tags
		$GLOBALS["WE_NEWSLETTER_EMAIL"] = isset($f["subscribe_mail"]) ? $f["subscribe_mail"] : "";
		$GLOBALS["WE_SALUTATION"] = isset($f["subscribe_salutation"]) ? $f["subscribe_salutation"] : "";
		$GLOBALS["WE_TITLE"] = isset($f["subscribe_title"]) ? $f["subscribe_title"] : "";
		$GLOBALS["WE_FIRSTNAME"] = isset($f["subscribe_firstname"]) ? $f["subscribe_firstname"] : "";
		$GLOBALS["WE_LASTNAME"] = isset($f["subscribe_lastname"]) ? $f["subscribe_lastname"] : "";
		if(isset($f["lists"]) && $f["lists"]){
			if (strpos($f["lists"],".")) {
				$paths = makeArrayFromCSV($f["lists"]);
			} else {
				$abos = makeArrayFromCSV($f["lists"]);
				$customer = 1;
			}

		}

		if($err!=WE_NEWSLETTER_STATUS_SUCCESS){
			$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = $err;
			return;
		}
		if(sizeof($f) == 0){
			$GLOBALS["WE_WRITENEWSLETTER_STATUS"]= WE_NEWSLETTER_STATUS_ERROR;
			return;
		}

		if ($doubleoptin && (!isset($_REQUEST["confirmID"]))) { // Direkte ANmeldung mit doubleoptin => zuerst confirmmail verschicken.
			$confirmID = md5 (uniqid (rand()));
			$lists = "";
			$emailExistsInOneOfTheLists = false;
			if($customer) {
				$db = new DB_WE();
				$__query = "SELECT * FROM " . CUSTOMER_TABLE . " WHERE " . $_customerFieldPrefs['customer_email_field'] . "='" . $db->escape($f["subscribe_mail"]) . "'";
				$db->query($__query);
				if($db->next_record()) {
					$emailExistsInOneOfTheLists = true;
				}
				foreach ($abos as $cAbo) {
					$dbAbo = $db->f($cAbo);
					if(!empty($dbAbo)) $emailExistsInOneOfTheLists = true;
					$lists .= $cAbo . ",";
				}
			} else {
				if(!$emailonly){
					foreach($paths as $p){
						if(!$emailExistsInOneOfTheLists){
							$realPath = (substr($p,0,1) == "/") ? ($_SERVER['DOCUMENT_ROOT'] . $p) : ($_SERVER['DOCUMENT_ROOT'] . "/" . $p);
							if(@file_exists($realPath)){
								$fh=@fopen($realPath,"rb");
								if($fh) {
									$file="";
									if(filesize($realPath)){
										while(!feof($fh)) $file.=fread($fh,filesize($realPath));
									}
									fclose($fh);
									if(eregi("[\r\n]".$f["subscribe_mail"].",[^\r\n]+[\r\n]",$file) || eregi("^".$f["subscribe_mail"].",[^\r\n]+[\r\n]",$file)){
										$emailExistsInOneOfTheLists = true; // E-Mail does not exists in one of the lists
									}
								} else {
									$GLOBALS["WE_WRITENEWSLETTER_STATUS"]= WE_NEWSLETTER_STATUS_ERROR;  // FATAL ERROR
									$GLOBALS["WE_REMOVENEWSLETTER_STATUS"]= WE_NEWSLETTER_STATUS_ERROR; // FATAL ERROR
									return;
								}
							}else{
								$emailExistsInOneOfTheLists = false; // List does not exists, so email can't also exists
							}
						}
						$lists .= $p.",";
					}
				}
			}
			if($emailExistsInOneOfTheLists){
				$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_EMAIL_EXISTS;
				return;
			}

			$lists = ereg_replace('^(.*),$','\1',$lists);

			$db = new DB_WE();
			$db->query("DELETE FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE LOWER(subscribe_mail) = LOWER('".$db->escape($f["subscribe_mail"])."')");

			$mailid = we_getTagAttribute("mailid",$attribs);
			$expiredoubleoptin = we_getTagAttribute("expiredoubleoptin",$attribs,1440) * 60;  // in secs

			if($mailid){

				$q = "INSERT INTO " . NEWSLETTER_CONFIRM_TABLE . " (confirmID,subscribe_mail,subscribe_html,subscribe_salutation,subscribe_title,subscribe_firstname,subscribe_lastname,lists,expires)
							VALUES ('".$db->escape($confirmID)."','".$db->escape($f["subscribe_mail"])."','".$db->escape($f["subscribe_html"])."','".$db->escape($f["subscribe_salutation"])."','".$db->escape($f["subscribe_title"])."','".$db->escape($f["subscribe_firstname"])."','".$db->escape($f["subscribe_lastname"])."','".$db->escape($lists)."','".($expiredoubleoptin+time())."')";

				$db->query($q);

				$id = we_getTagAttribute("id",$attribs);
				$subject = we_getTagAttribute("subject",$attribs,"newsletter");
				$from = we_getTagAttribute("from",$attribs,"newsletter@".SERVER_NAME);

				$use_https_refer=false;
				$db->query("SELECT pref_value FROM ".NEWSLETTER_PREFS_TABLE." WHERE pref_name='use_https_refer';");
				if(!$db->next_record()) $use_https_refer=$db->f("use_https_refer");
				if($use_https_refer) $protocol="https://";
				else $protocol="http://";

				$port = defined("HTTP_PORT") ? HTTP_PORT : 80;
				$basehref=$protocol.SERVER_NAME.":".$port;

				$confirmLink = $id ? id_to_path($id, FILE_TABLE) : $_SERVER["SCRIPT_NAME"];

				$confirmLink .= "?confirmID=".$confirmID."&mail=".rawurlencode($f["subscribe_mail"]);

				$confirmLink = $protocol.SERVER_NAME.(($port && ($port != 80)) ? ":$port" : "").$confirmLink;
				$GLOBALS["WE_MAIL"]=$f["subscribe_mail"];
				$GLOBALS["WE_TITLE"]="###TITLE###";
				$GLOBALS["WE_SALUTATION"]=$f["subscribe_salutation"];
				$GLOBALS["WE_FIRSTNAME"]=$f["subscribe_firstname"];
				$GLOBALS["WE_LASTNAME"]=$f["subscribe_lastname"];
				$GLOBALS["WE_CONFIRMLINK"]=$confirmLink;

				if($f["subscribe_html"]){
					$GLOBALS["WE_HTMLMAIL"]=1;

					if(isset($GLOBALS["we_doc"])){
						$mywedoc=$GLOBALS["we_doc"];
						unset($GLOBALS["we_doc"]);
					}
					$mailtextHTML = we_getDocumentByID($mailid);
					if($f["subscribe_title"]){
						$mailtextHTML = eregi_replace('([^ ])###TITLE###','\1 '.$f["subscribe_title"],$mailtextHTML);
					}
					$mailtextHTML = str_replace('###TITLE###',$f["subscribe_title"],$mailtextHTML);
				}

				$GLOBALS["WE_HTMLMAIL"]=0;

				if(isset($GLOBALS["we_doc"])){
					if(!isset($mywedoc)){
						$mywedoc=$GLOBALS["we_doc"];
					}
					unset($GLOBALS["we_doc"]);
				}


				$charset = isset($mywedoc->elements["Charset"]["dat"]) && $mywedoc->elements["Charset"]["dat"]!="" ? $mywedoc->elements["Charset"]["dat"] : $GLOBALS['WE_BACKENDCHARSET'];
				$mailtext = we_getDocumentByID($mailid,"","",$charset);

				if($f["subscribe_title"]){
					$mailtext = eregi_replace('([^ ])###TITLE###','\1 '.$f["subscribe_title"],$mailtext);
				}
				$mailtext = str_replace('###TITLE###',$f["subscribe_title"],$mailtext);



				$pattern = "/####PLACEHOLDER:DB::CUSTOMER_TABLE:(.[^#]{1,200})####/";
				preg_match_all($pattern,$mailtext,$placeholderfieldsmatches);
				$placeholderfields = $placeholderfieldsmatches[1];
				unset($placeholderfieldsmatches);

				$placeholderReplaceValue = "";
				if ($customer) {
					$db->query("SELECT * FROM ".CUSTOMER_TABLE." WHERE " . $_customerFieldPrefs['customer_email_field'] . "='".$db->escape($f["subscribe_mail"])."'");
					$db->next_record();
				}
				if (is_array($placeholderfields)) {

					foreach ($placeholderfields as $phf) {
						$placeholderReplaceValue = $customer ? $db->f($phf) : "";
						$mailtext = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:'.$phf.'####',$placeholderReplaceValue,$mailtext);
						$mailtextHTML = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:'.$phf.'####',$placeholderReplaceValue,$mailtextHTML);
					}
				}
				$recipientCC = we_getTagAttribute("recipientCC",$attribs);
				$recipientBCC = we_getTagAttribute("recipientBCC",$attribs);
				$includeimages = we_getTagAttribute("includeimages",$attribs,false,true);
				$useBaseHref = we_getTagAttribute("usebasehref",$attribs,true,true,true);
				$toCC = explode(",",$recipientCC);
				$we_recipientCC = array();
				for ($l=0;$l < sizeof($toCC);$l++) {
					if (strpos($toCC[$l],'@')===false) {
						if (isset($_SESSION["webuser"]["registered"]) && $_SESSION["webuser"]["registered"] && isset($_SESSION["webuser"][$toCC[$l]]) && strpos($_SESSION["webuser"][$toCC[$l]],'@')!==false) { //wenn man registrierten Usern was senden moechte
							$we_recipientCC[] = $_SESSION["webuser"][$toCC[$l]];
						} else if(isset($_REQUEST[$toCC[$l]]) && strpos($_REQUEST[$toCC[$l]],'@')!==false) {	//email to friend test
							$we_recipientCC[] = $_REQUEST[$toCC[$l]];
						}
					} else {
						$we_recipientCC[] = $toCC[$l];
					}
				}
				$toBCC = explode(",",$recipientBCC);
				$we_recipientBCC = array();
				for ($l=0;$l < sizeof($toBCC);$l++) {
					if (strpos("@",$toBCC[$l])===false) {
						if (isset($_SESSION["webuser"]["registered"]) && $_SESSION["webuser"]["registered"] && isset($_SESSION["webuser"][$toBCC[$l]]) && strpos("@",$_SESSION["webuser"][$toBCC[$l]])!==false) { //wenn man registrierte Usern was senden moechte
							$we_recipientBCC[] = $_SESSION["webuser"][$toBCC[$l]];
						} else if(isset($_REQUEST[$toBCC[$l]]) && strpos("@",$_REQUEST[$toBCC[$l]])!==false) {	//email to friend test
							$we_recipientBCC[] = $_REQUEST[$toBCC[$l]];
						}
					} else {
						$we_recipientBCC[] = $toBCC[$l];
					}
				}
				$phpmail = new we_util_Mailer($f["subscribe_mail"],$subject,$from,$from);
				if(isset($includeimages)) {
					$phpmail->setIsEmbedImages($includeimages);
				} else {
					$phpmail->setBaseDir($basehref);
				}
				if(!empty($we_recipientCC)){$phpmail->setCC($we_recipientCC);}
				if(!empty($we_recipientBCC)){$phpmail->setBCC($we_recipientBCC);}

				$phpmail->setCharSet($charset);


				if($f["subscribe_html"]){
					$phpmail->addHTMLPart($mailtextHTML);
				}else{
					$phpmail->addTextPart(trim($mailtext));
				}
				$phpmail->buildMessage();
				$phpmail->Send();
				$GLOBALS["WE_DOUBLEOPTIN"]=1;

				if(isset($mywedoc)) $GLOBALS["we_doc"]=$mywedoc;

			}else{
				$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR;
				return;
			}

		} else { //confirmID wurde �bermittelt, eine Best�tigung liegt also vor
			$emailwritten = 0;
			if($customer) {
				include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_exim/backup/weBackupUpdater.class.php');
				$__db = new DB_WE();
				$__id=f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE ' . $_customerFieldPrefs['customer_email_field'] . '="' . $__db->escape($f["subscribe_mail"]) . '"','ID',$__db);
				if($__id=='') {
					$GLOBALS["WE_NEWSUBSCRIBER_PASSWORD"] = substr(md5(time()),4,8);
					$GLOBALS["WE_NEWSUBSCRIBER_USERNAME"] = $f["subscribe_mail"];
					$fields = array(
							'`Username`' => '"'.escape_sql_query($f["subscribe_mail"]).'"',
							'`Text`' => '"'.escape_sql_query($f["subscribe_mail"]).'"',
							'`Password`'=> '"'.escape_sql_query($GLOBALS["WE_NEWSUBSCRIBER_PASSWORD"]).'"',
							'`MemberSince`'=>time(),
							'`IsFolder`'=>0,
							'`Icon`'=> '\'customer.gif\'',
							'`'.$_customerFieldPrefs['customer_salutation_field'].'`'=>'"'.$__db->escape($f["subscribe_salutation"]).'"',
							'`'.$_customerFieldPrefs['customer_title_field'].'`'=>'"'.$__db->escape($f["subscribe_title"]).'"',
							'`'.$_customerFieldPrefs['customer_firstname_field'].'`'=>'"'.$__db->escape($f["subscribe_firstname"]).'"',
							'`'.$_customerFieldPrefs['customer_lastname_field'].'`'=>'"'.$__db->escape($f["subscribe_lastname"]).'"',
							'`'.$_customerFieldPrefs['customer_email_field'].'`'=>'"'.$__db->escape($f["subscribe_mail"]) .'"',
							'`'.$_customerFieldPrefs['customer_html_field'].'`'=>'"'.$__db->escape($f["subscribe_html"]).'"',
							);

					$__db->query('INSERT INTO ' . CUSTOMER_TABLE . ' ('.implode(',',array_keys($fields)).') VALUES('.implode(',',$fields).')');

				}

				$__set = "";
				$__customerFields=f('SELECT Value FROM ' . CUSTOMER_ADMIN_TABLE . ' WHERE Name="FieldAdds"','Value',$__db);
				$__customerFields = $__customerFields ? unserialize($__customerFields) : '';
				$updateCustomerFields = false;
				foreach ($abos as $abo) {
					if (isset($__customerFields[$abo]["default"]) && !empty($__customerFields[$abo]["default"])) {
						$__setVals = explode(",", $__customerFields[$abo]["default"]);
					} else if (isset($__customerFields["Newsletter_Ok"]["default"]) && !empty($__customerFields[$abo]["default"])) {
						$__setVals = explode(",", $__customerFields["Newsletter_Ok"]["default"]);
					} else {
						$__setVals = array("","1");
					}

					switch (true) {
						case is_array($__setVals) && count($__setVals) > 1 :
							$__setDefault =  $__setVals[0];
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

					$__db->query("SHOW COLUMNS FROM ".CUSTOMER_TABLE." LIKE '".$__db->escape($abo)."'");
					if($__db->num_rows()<1) {
						$__db->query("ALTER TABLE " . CUSTOMER_TABLE . " ADD ".$__db->escape($abo)." VARCHAR(200) DEFAULT '".$__db->escape($__setDefault)."'");
						$fieldDefault = array("default" => isset($__customerFields['Newsletter_Ok']['default']) && !empty($__customerFields['Newsletter_Ok']['default']) ? $__customerFields['Newsletter_Ok']['default'] : ",1");
						$__customerFields[$abo] = $fieldDefault;
						$updateCustomerFields = true;
					}
					$__set .=  "$abo='". $__db->escape($__setVal) . "', ";
				}

				if ($updateCustomerFields) {
					$__db->query("UPDATE " . CUSTOMER_ADMIN_TABLE . " SET Value='" . $__db->escape(serialize($__customerFields)) . "' WHERE Name='FieldAdds'");
				}

				$__set .= $_customerFieldPrefs['customer_html_field'].'= \'' . $__db->escape($f["subscribe_html"]).'\'';
				$__db->query("UPDATE " . CUSTOMER_TABLE . " SET ".$__set." WHERE " . $_customerFieldPrefs['customer_email_field'] . "='".$__db->escape($f["subscribe_mail"])."'");
				$__db->query("DELETE FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE subscribe_mail ='".$__db->escape($f["subscribe_mail"])."'");
			} else {
				if(!$emailonly){ //in die Liste eintragen
					foreach($paths as $path){

						$path = (substr($path,0,1) == "/") ? ($_SERVER['DOCUMENT_ROOT'] . $path) : ($_SERVER['DOCUMENT_ROOT'] . "/" . $path);

						if(!@file_exists(dirname($path))){
							$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR;  // FATAL ERROR
							$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR; // FATAL ERROR
							return;
						}


						$ok = true;

						$fh=@fopen($path,"rb");
						if($fh){
							$file="";
							if(filesize($path)){
								while(!feof($fh)) $file.=fread($fh,filesize($path));
							}
							fclose($fh);
							if((eregi("[\r\n]".$f["subscribe_mail"].",[^\r\n]+[\r\n]",$file) || eregi("^".$f["subscribe_mail"].",[^\r\n]+[\r\n]",$file))){
								$ok = false; // E-Mail schon vorhanden => Nix tun
							}
						}
						if($ok){
							$fh=@fopen($path,"ab+");
							if($fh){
								$row=$f["subscribe_mail"].",".$f["subscribe_html"].",".$f["subscribe_salutation"].",".$f["subscribe_title"].",".$f["subscribe_firstname"].",".$f["subscribe_lastname"]."\n";
								if(!@fwrite($fh,$row)){
									fclose($fh);
									$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR; // FATAL ERROR
									return;
								}
								fclose($fh);
								$emailwritten++;
							}else{
								$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR; // FATAL ERROR
								return;
							}
						}
						@chmod($path);
					}
					if($emailwritten==0){
						$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_EMAIL_EXISTS;
					}
					$db->query("DELETE FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE subscribe_mail ='".$db->escape($f["subscribe_mail"])."'");

				} else { //nicht in eine Liste eintragen sondern adminmail versenden

					$adminmailid = we_getTagAttribute("adminmailid",$attribs);
					$adminsubject = we_getTagAttribute("adminsubject",$attribs);
					$adminemail = we_getTagAttribute("adminemail",$attribs);
					$db->query("DELETE FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE subscribe_mail ='".$db->escape($f["subscribe_mail"])."'");
					$phpmail = new we_util_Mailer($adminemail,$adminsubject,$f["subscribe_mail"],$f["subscribe_mail"]);
					$phpmail->setCharSet($charset);

					$adminmailtextHTML = we_getDocumentByID($adminmailid);
					$adminmailtextHTML = str_replace('###MAIL###',$f["subscribe_mail"],$adminmailtextHTML);
					$adminmailtextHTML = str_replace('###SALUTATION###',$f["subscribe_salutation"],$adminmailtextHTML);
					$adminmailtextHTML = str_replace('###TITLE###',$f["subscribe_title"],$adminmailtextHTML);
					$adminmailtextHTML = str_replace('###FIRSTNAME###',$f["subscribe_firstname"],$adminmailtextHTML);
					$adminmailtextHTML = str_replace('###LASTNAME###',$f["subscribe_lastname"],$adminmailtextHTML);
					$adminmailtextHTML = str_replace('###HTML###',$f["subscribe_html"],$adminmailtextHTML);
					$includeimages = we_getTagAttribute("includeimages",$attribs,false,true);
					$phpmail->addHTMLPart($adminmailtextHTML);
					if(isset($includeimages)) {$phpmail->setIsEmbedImages($includeimages);}
					$phpmail->buildMessage();
					$phpmail->Send();
					$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_SUCCESS;

				}
			}

		}
	}

	/**********************************************************************************/
	/***                         NEWSLETTER UNSUBSCTIPTION                          ***/
	/**********************************************************************************/
	if($isUnsubscribe){
		if(!we_unsubscribeNL($db,$customer,$_customerFieldPrefs,$abos,$paths)){
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

function we_unsubscribeNL($db,$customer,$_customerFieldPrefs,$abos,$paths){
		$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_SUCCESS;
		$unsubscribe_mail = preg_replace("|[\r\n,]|","",trim($_REQUEST["we_unsubscribe_email__"]));
		$GLOBALS["WE_NEWSLETTER_EMAIL"] = $unsubscribe_mail;
		if(!we_check_email($unsubscribe_mail)){
			$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_EMAIL_INVALID; // E-Mail ungueltig
			return false;
		}

		$emailExists = false;

		$db->query("DELETE FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE subscribe_mail ='".$db->escape($unsubscribe_mail)."'");

		if ($customer) {
			$__db = new DB_WE();

			$__db->query("SELECT Value FROM " . CUSTOMER_ADMIN_TABLE . " WHERE Name='FieldAdds'");
			$__customerFields = $__db->next_record() ? unserialize($__db->f('Value')) : "";

			$__where = " WHERE " .$_customerFieldPrefs['customer_email_field'] . "='" . $__db->escape($unsubscribe_mail) . "'";
			$__db->query("SELECT * FROM " . CUSTOMER_TABLE . $__where);
			$__update = "";
			if ($__db->next_record()) {
				foreach($abos as $abo) {
					$fieldDefault = (isset($__customerFields[$abo]["default"]) ? $__customerFields[$abo]["default"] : "");
					$fieldDefaults = explode(",", $fieldDefault);
					$aboNeg = is_array($fieldDefaults) && count($fieldDefaults)>1 ? $fieldDefaults[0] : "";

					$dbAbo = $__db->f($abo);
					if (!empty($dbAbo) || $dbAbo != $aboNeg) {
						$__update .= (empty($__update)?"":", ") . "$abo='$aboNeg'";
						$emailExists = true;
					}
				}
				if($emailExists) $__db->query("UPDATE " . CUSTOMER_TABLE . " SET $__update $__where");
			}
		} else {

			foreach($paths as $path){

				$path = (substr($path,0,1) == "/") ? ($_SERVER['DOCUMENT_ROOT'] . $path) : ($_SERVER['DOCUMENT_ROOT'] . "/" . $path);

				if(!@file_exists(dirname($path))){
					$GLOBALS["WE_WRITENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR;  // FATAL ERROR
					$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR; // FATAL ERROR
					return false;
				}

				// #4158
				$file = @file($path);
				if (!$file) continue;

				$fileChanged = false;
				foreach ($file as $i => $line) {
					if (mb_substr($line, 0, mb_strlen($unsubscribe_mail) + 1) == "$unsubscribe_mail,") {
						$emailExists = true;
						unset($file[$i]);
						$fileChanged = true;
					}
				}

				if ($fileChanged) {
					$success = file_put_contents($path, implode("\n", array_map('trim', $file)) . "\n");
					if (!$success) {
						$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_ERROR; // FATAL ERROR
					}
				}
				//
			}
		}

		if(!$emailExists){
			$GLOBALS["WE_REMOVENEWSLETTER_STATUS"] = WE_NEWSLETTER_STATUS_EMAIL_EXISTS;
			return false;
		}
		return true;
}

function getNewsletterFields($request,$confirmid,&$errorcode,$mail=""){

	$errorcode = 0;
	if($confirmid){
		$_h = getHash("SELECT * FROM " . NEWSLETTER_CONFIRM_TABLE . " WHERE confirmID = '".escape_sql_query($confirmid)."' AND subscribe_mail='".escape_sql_query($mail)."'", new DB_WE());
		if(empty($_h)) {
			$errorcode = WE_NEWSLETTER_STATUS_CONFIR_FAILED;
		}
		return $_h;
	}else{
		$subscribe_mail=preg_replace("|[\r\n,]|","",trim($request["we_subscribe_email__"]));
		if(strlen($subscribe_mail) == 0){
			$errorcode=2;
			return array();
		}

		if(!we_check_email($subscribe_mail)){
			$errorcode=2; // E-Mail ungueltig
			return array();
		}

		$subscribe_html=(isset($request["we_subscribe_html__"])?$request["we_subscribe_html__"]:0);

		$subscribe_salutation=(isset($request["we_subscribe_salutation__"])?preg_replace("|[\r\n,]|","",$request["we_subscribe_salutation__"]):'');

		$subscribe_title=(isset($request["we_subscribe_title__"])?preg_replace("|[\r\n,]|","",$request["we_subscribe_title__"]):'');

		$subscribe_firstname=(isset($request["we_subscribe_firstname__"])?preg_replace("|[\r\n,]|","",$request["we_subscribe_firstname__"]):'');

		$subscribe_lastname=(isset($request["we_subscribe_lastname__"])?preg_replace("|[\r\n,]|","",$request["we_subscribe_lastname__"]):'');
	}

	return array(	"subscribe_mail"=>trim($subscribe_mail),
					"subscribe_html"=>trim($subscribe_html),
					"subscribe_salutation"=>trim($subscribe_salutation),
					"subscribe_title"=>trim($subscribe_title),
					"subscribe_firstname"=>trim($subscribe_firstname),
					"subscribe_lastname"=>trim($subscribe_lastname)
				);
}
