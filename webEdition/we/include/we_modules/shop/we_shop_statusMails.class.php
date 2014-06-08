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
class we_shop_statusMails{

	var $FieldsHidden; //an array of statusfield names not to be shown in order
	var $FieldsHiddenCOV; //an array of statusfield names not to be shown in order
	var $FieldsText; //an array with keys equal to name of statusfield, and value = text to be shown
	var $FieldsMails; //an array with keys equal to name of statusfield, and value = 0 for no Mail, 1 for Mail by Hand, 2 for automatic mails
	var $EMailData; // an array with the E-Mail data, see getShopStatusMails
	var $LanguageData; // an array with the Language data, see getShopStatusMails
	var $FieldsDocuments; // an array with dfault values and separate Arrays for each Langauge, see getShopStatusMails
	public static $StatusFields = array('DateOrder', 'DateConfirmation', 'DateCustomA', 'DateCustomB', 'DateCustomC', 'DateShipping', 'DateCustomD', 'DateCustomE', 'DatePayment', 'DateCustomF', 'DateCustomG', 'DateCancellation', 'DateCustomH', 'DateCustomI', 'DateCustomJ', 'DateFinished');
	public static $MailFields = array('MailShipping', 'MailPayment', 'MailOrder', 'MailConfirmation', 'MailCustomA', 'MailCustomB', 'MailCustomC', 'MailCustomD', 'MailCustomE', 'MailCustomF', 'MailCustomG', 'MailCustomH', 'MailCustomI', 'MailCustomJ', 'MailCancellation', 'MailFinished');

	function __construct($FieldsHidden, $FieldsHiddenCOV, $FieldsText, $FieldsMails, $EMailData, $LanguageData, $FieldsDocuments){

		$this->FieldsHidden = $FieldsHidden;
		$this->FieldsHiddenCOV = $FieldsHiddenCOV;
		$this->FieldsText = $FieldsText;
		$this->FieldsMails = $FieldsMails;
		$this->EMailData = $EMailData;
		$this->LanguageData = $LanguageData;
		$this->FieldsDocuments = $FieldsDocuments;
	}

	function initByRequest(&$req){

		return new we_shop_statusMails(
				$req['FieldsHidden'], $req['FieldsHiddenCOV'], $req['FieldsText'], $req['FieldsMails'], $req['EMailData'], $req['LanguageData'], $req['FieldsDocuments']
		);
	}

	function getShopStatusMails(){
		global $DB_WE;
		$documentsarray = array(
			'default' => array(
				'DateOrder' => 0,
				'DateConfirmation' => 0,
				'DateCustomA' => 0,
				'DateCustomB' => 0,
				'DateCustomC' => 0,
				'DateShipping' => 0,
				'DateCustomD' => 0,
				'DateCustomE' => 0,
				'DateCancellation' => 0,
				'DateCustomF' => 0,
				'DateCustomG' => 0,
				'DatePayment' => 0,
				'DateCustomH' => 0,
				'DateCustomI' => 0,
				'DateCustomJ' => 0,
				'DateFinished' => 0,
			)
		);
		$frontendL = $GLOBALS["weFrontendLanguages"];
		foreach($frontendL as $lc => &$lcvalue){
			$lccode = explode('_', $lcvalue);
			$lcvalue = $lccode[0];
		}
		unset($lcvalue);
		foreach($frontendL as $langkey){
			$documentsarray[$langkey] = $documentsarray['default'];
		}
		$zw = new we_shop_statusMails(
				array(//Fieldshidden
			'DateOrder' => 0,
			'DateConfirmation' => 1,
			'DateCustomA' => 1,
			'DateCustomB' => 1,
			'DateCustomC' => 1,
			'DateShipping' => 0,
			'DateCustomD' => 1,
			'DateCustomE' => 1,
			'DateCancellation' => 1,
			'DateCustomF' => 1,
			'DateCustomG' => 1,
			'DatePayment' => 0,
			'DateCustomH' => 1,
			'DateCustomI' => 1,
			'DateCustomJ' => 1,
			'DateFinished' => 1
				), array(//FieldshiddenCOV
			'DateOrder' => 0,
			'DateConfirmation' => 1,
			'DateCustomA' => 1,
			'DateCustomB' => 1,
			'DateCustomC' => 1,
			'DateShipping' => 0,
			'DateCustomD' => 1,
			'DateCustomE' => 1,
			'DateCancellation' => 1,
			'DateCustomF' => 1,
			'DateCustomG' => 1,
			'DatePayment' => 0,
			'DateCustomH' => 1,
			'DateCustomI' => 1,
			'DateCustomJ' => 1,
			'DateFinished' => 1
				), array(//FieldsTexts
			'DateOrder' => g_l('modules_shop', '[bestelldatum]'),
			'DateConfirmation' => g_l('modules_shop', '[bestaetigt]'),
			'DateCustomA' => g_l('modules_shop', '[customA]'),
			'DateCustomB' => g_l('modules_shop', '[customB]'),
			'DateCustomC' => g_l('modules_shop', '[customC]'),
			'DateShipping' => g_l('modules_shop', '[bearbeitet]'),
			'DateCustomD' => g_l('modules_shop', '[customD]'),
			'DateCustomE' => g_l('modules_shop', '[customE]'),
			'DatePayment' => g_l('modules_shop', '[bezahlt]'),
			'DateCustomF' => g_l('modules_shop', '[customF]'),
			'DateCustomG' => g_l('modules_shop', '[customG]'),
			'DateCancellation' => g_l('modules_shop', '[storniert]'),
			'DateCustomH' => g_l('modules_shop', '[customH]'),
			'DateCustomI' => g_l('modules_shop', '[customI]'),
			'DateCustomJ' => g_l('modules_shop', '[customJ]'),
			'DateFinished' => g_l('modules_shop', '[beendet]')
				), array(//FieldsMails
			'DateOrder' => 1,
			'DateConfirmation' => 1,
			'DateCustomA' => 1,
			'DateCustomB' => 1,
			'DateCustomC' => 1,
			'DateShipping' => 1,
			'DateCustomD' => 1,
			'DateCustomE' => 1,
			'DateCancellation' => 1,
			'DateCustomF' => 1,
			'DateCustomG' => 1,
			'DatePayment' => 1,
			'DateCustomH' => 1,
			'DateCustomI' => 1,
			'DateCustomJ' => 1,
			'DateFinished' => 0
				), array(//EMailData
			'address' => '',
			'name' => '',
			'bcc' => '',
			'DocumentSubjectField' => 'Title',
			'DocumentAttachmentFieldA' => '',
			'DocumentAttachmentFieldB' => '',
			'emailField' => '',
			'titleField' => ''
				), array(//LanguageData
			'useLanguages' => 1,
			'languageField' => '',
			'languageFieldIsISO' => 0
				), $documentsarray
		);
		$zw2 = strtr(f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="weShopStatusMails"', '', $DB_WE), array('O:17:"weShopStatusMails":' => 'O:19:"we_shop_statusMails":'));

		if($zw2){
			$zw2 = unserialize($zw2);
			foreach($zw->FieldsHidden as $key => &$value){
				if(isset($zw2->FieldsHidden[$key])){
					$value = $zw2->FieldsHidden[$key];
				}
			}
			unset($value);
			foreach($zw->FieldsHiddenCOV as $key => &$value){
				if(isset($zw2->FieldsHiddenCOV[$key])){
					$value = $zw2->FieldsHiddenCOV[$key];
				}
			}
			unset($value);
			foreach($zw->FieldsText as $key => &$value){
				if(isset($zw2->FieldsText[$key])){
					$value = $zw2->FieldsText[$key];
				}
			}
			foreach($zw->FieldsMails as $key => &$value){
				if(isset($zw2->FieldsMails[$key])){
					$value = $zw2->FieldsMails[$key];
				}
			}
			unset($value);
			foreach($zw->EMailData as $key => &$value){
				if(isset($zw2->EMailData[$key])){
					$value = $zw2->EMailData[$key];
				}
			}
			unset($value);
			foreach($zw->LanguageData as $key => &$value){
				if(isset($zw2->LanguageData[$key])){
					$value = $zw2->LanguageData[$key];
				}
			}
			unset($value);
			foreach($zw->FieldsDocuments as $key => &$value){
				if(isset($zw2->FieldsDocuments[$key])){
					$value = $zw2->FieldsDocuments[$key];
				}
			}
			unset($value);
		}
		return $zw;
	}

	function sendEMail($was, $order, $cdata, $pagelang = ''){
		global $DB_WE;
		$recipientOK = (isset($this->EMailData['emailField']) && $this->EMailData['emailField'] != '' && isset($cdata[$this->EMailData['emailField']]) && we_check_email($cdata[$this->EMailData['emailField']]));
		$UserLang = '';
		$field = 0;
		if(isset($this->LanguageData['useLanguages']) && $this->LanguageData['useLanguages'] && isset($this->LanguageData['languageField']) && $this->LanguageData['languageField'] != '' && isset($cdata[$this->LanguageData['languageField']]) && $cdata[$this->LanguageData['languageField']] != ''){
			if($pagelang != '' && isset($this->FieldsDocuments[$pagelang]) && isset($this->FieldsDocuments[$pagelang]['Date' . $was])){
				$docID = $this->FieldsDocuments[$pagelang]['Date' . $was];
				$field = 1;
			} else {
				$docID = (isset($this->FieldsDocuments[$cdata[$this->LanguageData['languageField']]]) && isset($this->FieldsDocuments[$cdata[$this->LanguageData['languageField']]]['Date' . $was]) ?
								$this->FieldsDocuments[$cdata[$this->LanguageData['languageField']]]['Date' . $was] :
								$this->FieldsDocuments['default']['Date' . $was]);
				$field = '2' . $this->FieldsDocuments . ' ' . $cdata[$this->LanguageData['languageField']];
			}
			if(isset($this->LanguageData['languageField']) && $this->LanguageData['languageField'] != '' && isset($cdata[$this->LanguageData['languageField']]) && $cdata[$this->LanguageData['languageField']] != ''){
				$UserLang = $cdata[$this->LanguageData['languageField']];
			}
		} else {
			$docID = $this->FieldsDocuments['default']['Date' . $was];
			$field = 3;
			if(isset($this->LanguageData['languageField']) && $this->LanguageData['languageField'] != '' && isset($cdata[$this->LanguageData['languageField']]) && $cdata[$this->LanguageData['languageField']] != ''){
				$UserLang = $cdata[$this->LanguageData['languageField']];
			}
		}

		$docID = intval($docID);

		if($docID && we_base_file::isWeFile($docID)){
			$_SESSION['WE_SendMail'] = true;
			$_REQUEST['we_orderid'] = $order;
			$_REQUEST['we_userlanguage'] = $UserLang;
			$_REQUEST['we_shopstatus'] = $was;

			$codes = we_getDocumentByID($docID);
			$maildoc = new we_webEditionDocument();
			$maildoc->initByID($docID);

			if(isset($this->EMailData['DocumentAttachmentFieldA']) && $this->EMailData['DocumentAttachmentFieldA'] != ''){
				$attachmentA = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldA']);
				$codes = $codes . $attachmentA;
			}
			unset($_REQUEST['we_orderid']);
			unset($_SESSION['WE_SendMail']);
		} else {
			t_e('Document to send as status mail is empty! ID: ' . $docID, $field);
			return false;
		}


		$subject = $maildoc->getElement($this->EMailData['DocumentSubjectField']);
		if($subject == ''){
			$subject = 'no subject given';
		}
		if($recipientOK && $this->EMailData['address'] != '' && we_check_email($this->EMailData['address'])){
			$from = (!isset($this->EMailData['name']) || $this->EMailData['name'] === '' || $this->EMailData['name'] === null || $this->EMailData['name'] === $this->EMailData['address'] ?
							$this->EMailData['address'] :
							array('email' => $this->EMailData['address'], 'name' => $this->EMailData['name'])
					);

			$phpmail = new we_util_Mailer('', $subject, $from);
			$phpmail->setIsEmbedImages(true);

			$phpmail->addHTMLPart($codes);
			$phpmail->addTextPart(strip_tags(strtr($codes, array('&nbsp;' => ' ', '<br />' => "\n", '<br>' => "\n"))));
			$phpmail->addTo($cdata[$this->EMailData['emailField']], ( (isset($this->EMailData['titleField']) && $this->EMailData['titleField'] != '' && isset($cdata[$this->EMailData['titleField']]) && $cdata[$this->EMailData['titleField']] != '' ) ? $cdata[$this->EMailData['titleField']] . ' ' : '') . $cdata['Forename'] . ' ' . $cdata['Surname']);
			if(isset($this->EMailData['bcc']) && $this->EMailData['bcc'] != ''){
				$bccArray = explode(',', $this->EMailData['bcc']);
				$phpmail->setBCC($bccArray);
			}
			if(isset($this->EMailData['DocumentAttachmentFieldA']) && $this->EMailData['DocumentAttachmentFieldA'] != ''){
				$attachmentAinternal = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldA'] . we_base_link::MAGIC_INT_LINK);
				$attachmentA = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldA'] . ($attachmentAinternal ? we_base_link::MAGIC_INT_LINK_PATH : ''));
				if($attachmentA){
					$phpmail->doaddAttachment($_SERVER['DOCUMENT_ROOT'] . $attachmentA);
				}
			}
			if(isset($this->EMailData['DocumentAttachmentFieldB']) && $this->EMailData['DocumentAttachmentFieldB'] != ''){
				$attachmentBinternal = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldB'] . we_base_link::MAGIC_INT_LINK);
				$attachmentB = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldB'] . ($attachmentBinternal ? we_base_link::MAGIC_INT_LINK_PATH : ''));
				if($attachmentB){
					$phpmail->doaddAttachment($_SERVER['DOCUMENT_ROOT'] . $attachmentB);
				}
			}
			$phpmail->buildMessage();
			if($phpmail->Send()){
				$dasDatum = date('Y-m-d H:i:s');
				$DB_WE->query('UPDATE ' . SHOP_TABLE . ' SET Mail' . $DB_WE->escape($was) . '="' . $DB_WE->escape($dasDatum) . '" WHERE IntOrderID = ' . intval($order));

				return true;
			}
		}
		return false;
	}

	function checkAutoMailAndSend($was, $order, $cdata){
		if($this->FieldsMails['Date' . $was] == 2){
			$this->sendEMail($was, $order, $cdata);
		}
	}

	function getEMailHandlerCode($was, $dateSet){
		if(!$this->FieldsMails['Date' . $was]){
			return we_html_tools::getPixel(30, 15);
		}
		$datetimeform = "00.00.0000 00:00";
		$dateform = "00.00.0000";
		$EMailhandler = '<table cellpadding="0" cellspacing="0" border="0" width="99%" class="defaultfont"><tr><td class="defaultfont">' . g_l('modules_shop', '[statusmails][EMail]') . ': </td>';
		if($_REQUEST["Mail" . $was] != $datetimeform && $_REQUEST["Mail" . $was] != ''){
			$EMailhandler .= '<td class="defaultfont" width="150">' . $_REQUEST["Mail" . $was] . '</td>';
			$but = we_html_button::create_button("image:/mail_resend", "javascript:check=confirm('" . g_l('modules_shop', '[statusmails][resent]') . "'); if (check){SendMail('" . $was . "');}");
		} else {
			$EMailhandler .= '<td class="defaultfont" width="150">&nbsp;</td>';
			$but = we_html_button::create_button("image:/mail_send", "javascript:SendMail('" . $was . "')");
		}
		$EMailhandler .= '<td class="defaultfont">' . ($dateSet != $dateform ? $but : we_html_tools::getPixel(30, 15)) . '</td></tr></table>';

		return $EMailhandler;
	}

	function save(){
		$DB_WE = $GLOBALS['DB_WE'];

		if($DB_WE->query('REPLACE ' . WE_SHOP_PREFS_TABLE . ' SET strFelder="' . $DB_WE->escape(serialize($this)) . '",strDateiname="weShopStatusMails"')){
			$strFelder = f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLanguage"', 'strFelder', $DB_WE);
			if($strFelder !== ''){
				$CLFields = unserialize($strFelder);
				$CLFields['languageField'] = $this->LanguageData['languageField'];
				$CLFields['languageFieldIsISO'] = $this->LanguageData['languageFieldIsISO'];
				$DB_WE->query('REPLACE ' . WE_SHOP_PREFS_TABLE . ' SET strFelder="' . $DB_WE->escape(serialize($CLFields)) . '", strDateiname ="shop_CountryLanguage"');
			}
			return true;
		}
		return false;
	}

}
