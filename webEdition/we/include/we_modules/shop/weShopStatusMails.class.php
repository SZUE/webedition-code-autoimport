<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we_global.inc.php");
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/modules/shop.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf_language.inc.php');

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_button.inc.php");
class weShopStatusMails {
	
	
	var $FieldsHidden; //an array of statusfield names not to be shown in order
	var $FieldsHiddenCOV; //an array of statusfield names not to be shown in order
	var $FieldsText; //an array with keys equal to name of statusfield, and value = text to be shown
	var $FieldsMails; //an array with keys equal to name of statusfield, and value = 0 for no Mail, 1 for Mail by Hand, 2 for automatic mails
	var $EMailData; // an array with the E-Mail data, see getShopStatusMails
	var $LanguageData; // an array with the Language data, see getShopStatusMails
	var $FieldsDocuments; // an array with dfault values and separate Arrays for each Langauge, see getShopStatusMails
	var $StatusFields = array('DateOrder','DateConfirmation','DateCustomA','DateCustomB','DateCustomC','DateShipping','DateCustomD','DateCustomE','DatePayment','DateCustomF','DateCustomG','DateCancellation','DateCustomH','DateCustomI','DateCustomJ','DateFinished');

	
	function weShopStatusMails( $FieldsHidden, $FieldsHiddenCOV, $FieldsText, $FieldsMails,$EMailData,$LanguageData,$FieldsDocuments) {
		
		$this->FieldsHidden = $FieldsHidden;
		$this->FieldsHiddenCOV = $FieldsHiddenCOV;
		$this->FieldsText = $FieldsText; 
		$this->FieldsMails = $FieldsMails; 
		$this->EMailData = $EMailData; 
		$this->LanguageData = $LanguageData; 
		$this->FieldsDocuments = $FieldsDocuments;
	}
	

	
	function initByRequest(&$req) {
		
		return new weShopStatusMails(
			$req['FieldsHidden'],
			$req['FieldsHiddenCOV'],
			$req['FieldsText'],
			$req['FieldsMails'],
			$req['EMailData'],
			$req['LanguageData'],
			$req['FieldsDocuments']
		);
	
		
	}
	
	function getShopStatusMails() {		
		global $DB_WE;
		include($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/modules/shop.inc.php');
		$docarray = array(
						'DateOrder' => '',
						'DateConfirmation' => '',
						'DateCustomA' => '',
						'DateCustomB' => '',
						'DateCustomC' =>'',
						'DateShipping' => '',
						'DateCustomD' =>'',
						'DateCustomE' =>'',
						'DateCancellation' => '',
						'DateCustomF' =>'',
						'DateCustomG' =>'',
						'DatePayment' => '',
						'DateCustomH' =>'',
						'DateCustomI' =>'',
						'DateCustomJ' =>'',
						'DateFinished' => ''
					);
		$documentsarray['default']=$docarray;
		$frontendL = array_keys($GLOBALS["weFrontendLanguages"]);
		foreach ($frontendL as $lc => &$lcvalue){
			$lccode = explode('_', $lcvalue);
			$lcvalue= $lccode[0];
		}
		foreach ($frontendL as $langkey){
			$documentsarray[$langkey]=$docarray;
		} 
		$zw= new weShopStatusMails(
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
				
				),
				array(//FieldshiddenCOV
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
				
				),
				array( //FieldsTexts
					'DateOrder' => $l_shop['bestelldatum'],
					'DateConfirmation' => $l_shop['bestaetigt'],
					'DateCustomA' => $l_shop['customA'],
					'DateCustomB' => $l_shop['customB'],
					'DateCustomC' => $l_shop['customC'],
					'DateShipping' => $l_shop['bearbeitet'],
					'DateCustomD' => $l_shop['customD'],
					'DateCustomE' => $l_shop['customE'],
					'DatePayment' => $l_shop['bezahlt'],
					'DateCustomF' => $l_shop['customF'],
					'DateCustomG' => $l_shop['customG'],
					'DateCancellation' => $l_shop['storniert'],
					'DateCustomH' => $l_shop['customH'],
					'DateCustomI' => $l_shop['customI'],
					'DateCustomJ' => $l_shop['customJ'],
					'DateFinished' => $l_shop['beendet']				
				),
				array( //FieldsMails
					'DateOrder' => 1,
					'DateConfirmation' => 1,
					'DateCustomA' => 1,
					'DateCustomB' => 1,
					'DateCustomC' =>1,
					'DateShipping' => 1,
					'DateCustomD' =>1,
					'DateCustomE' =>1,
					'DateCancellation' => 1,
					'DateCustomF' =>1,
					'DateCustomG' =>1,
					'DatePayment' => 1,
					'DateCustomH' =>1,
					'DateCustomI' =>1,
					'DateCustomJ' =>1,
					'DateFinished' => 0
				),
				array(//EMailData
					'address' => '',
					'name' => '',
					'bcc' => '',
					'DocumentSubjectField' =>'Title',
					'emailField' => '',
					'titleField' => ''
				),
				array( //LanguageData
					'useLanguages' => 1,
					'languageField' => '',
					'languageFieldIsISO' => 0
									
				),
				$documentsarray				
			);
				
		$query = 'SELECT * FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="weShopStatusMails"	';	
		$DB_WE->query($query);
		
		if ($DB_WE->next_record()) {			
			$zw2 = unserialize($DB_WE->f('strFelder'));
			foreach($zw->FieldsHidden as $key => &$value){
				if( isset($zw2->FieldsHidden[$key])){
					$zw->FieldsHidden[$key]=$zw2->FieldsHidden[$key];
				}
			}
			foreach($zw->FieldsHiddenCOV as $key => &$value){
				if( isset($zw2->FieldsHiddenCOV[$key]) ){
					$zw->FieldsHiddenCOV[$key]=$zw2->FieldsHiddenCOV[$key];
				}
			}
			foreach($zw->FieldsText as $key => &$value){
				if( isset($zw2->FieldsText[$key]) ){
					$zw->FieldsText[$key]=$zw2->FieldsText[$key];
				}
			}
			foreach($zw->FieldsMails as $key => &$value){
				if( isset($zw2->FieldsMails[$key]) ){
					$zw->FieldsMails[$key]=$zw2->FieldsMails[$key];
				}
			}
			foreach($zw->EMailData as $key => &$value){
				if( isset($zw2->EMailData[$key]) ){
					$zw->EMailData[$key]=$zw2->EMailData[$key];
				}
			}
			foreach($zw->LanguageData as $key => &$value){
				if( isset($zw2->LanguageData[$key]) ){
					$zw->LanguageData[$key]=$zw2->LanguageData[$key];
				}
			}
			foreach($zw->FieldsDocuments as $key => &$value){
				if( isset($zw2->FieldsDocuments[$key]) ){
					$zw->FieldsDocuments[$key]=$zw2->FieldsDocuments[$key];
				}
			}
			return $zw;
		} else {
			return $zw;
		}
	}
	function sendEMail($was,$order,$cdata){
	global $DB_WE;
		if (isset($this->EMailData['emailField']) && $this->EMailData['emailField'] !='' && isset($cdata[$this->EMailData['emailField']]) &&  we_check_email($cdata[$this->EMailData['emailField']]) ){
			$recipientOK = true;
		} else $recipientOK = false;
		$docID=0;
		$UserLang='';
		if (isset($this->LanguageData['useLanguages']) && $this->LanguageData['useLanguages'] && isset($this->LanguageData['languageField']) && $this->LanguageData['languageField'] != '' && isset($cdata[$this->LanguageData['languageField']]) && $cdata[$this->LanguageData['languageField']]!='' ){
			if (isset($this->FieldsDocuments[$cdata[$this->LanguageData['languageField']]]) && isset($this->FieldsDocuments[$cdata[$this->LanguageData['languageField']]]['Date'.$was]) ){
				$docID= $this->FieldsDocuments[$cdata[$this->LanguageData['languageField']]]['Date'.$was];
			} else {
				$docID = $this->FieldsDocuments['default']['Date'.$was];
			}
			if (isset($this->LanguageData['languageField']) && $this->LanguageData['languageField'] != '' && isset($cdata[$this->LanguageData['languageField']]) && $cdata[$this->LanguageData['languageField']]!=''){
				$UserLang= $cdata[$this->LanguageData['languageField']];
			}
		} else {
			$docID = $this->FieldsDocuments['default']['Date'.$was];
			if (isset($this->LanguageData['languageField']) && $this->LanguageData['languageField'] != '' && isset($cdata[$this->LanguageData['languageField']]) && $cdata[$this->LanguageData['languageField']]!=''){
				$UserLang= $cdata[$this->LanguageData['languageField']];
			}
		} 
		
		if ($docID && $docID!=''){
			$_SESSION['WE_SendMail']=true;
			$_REQUEST['we_orderid']= $order;
			$_REQUEST['we_userlanguage']= $UserLang;
			$_REQUEST['we_shopstatus']= $was;	
			$codes = we_getDocumentByID($docID);
			$maildoc= new we_webEditionDocument();
			$maildoc->initByID($docID);
			unset($_REQUEST['we_orderid']);
			unset($_SESSION['WE_SendMail']);
		} else $docID=0;
		
		
		if ($docID){
			$phpmail = new we_util_Mailer();
			
			$subject = $maildoc->getElement($this->EMailData['DocumentSubjectField']);
			if ($recipientOK  && $subject!='' && $this->EMailData['address']!='' && we_check_email($this->EMailData['address']) ){
				$phpmail->setSubject($subject);
				$phpmail->setIsEmbedImages(true);
				$phpmail->setFrom($this->EMailData['address'],$this->EMailData['name']);
				$phpmail->addHTMLPart($codes);
				$phpmail->addTextPart(strip_tags(str_replace("&nbsp;"," ",str_replace("<br />","\n",str_replace("<br>","\n",$codes)))));
				$phpmail->addTo($cdata[$this->EMailData['emailField']], ( (isset($this->EMailData['titleField']) && $this->EMailData['titleField']!='' && isset( $cdata[$this->EMailData['titleField']]) &&  $cdata[$this->EMailData['titleField']] !='' ) ? $cdata[$this->EMailData['titleField']].' ': '').  $cdata['Forename'].' '.$cdata['Surname'] );
				if (isset($this->EMailData['bcc']) && we_check_email($this->EMailData['bcc'])){
					$phpmail->setBCC($this->EMailData['bcc']);
				}
				$phpmail->buildMessage();
				if ($phpmail->Send()){
					$dasDatum = date('Y-m-d H:i:s');
					$DB_WE->query("UPDATE ".SHOP_TABLE." SET Mail".mysql_real_escape_string($was)."='". mysql_real_escape_string($dasDatum) . "' WHERE IntOrderID = ".abs($order));
	
					return true;
				}
				
			} 
		}
		return false;
	}
	
	function checkAutoMailAndSend($was,$order,$cdata){
		if($this->FieldsMails['Date'.$was]==2){
			$this->sendEMail($was,$order,$cdata);
		}
	}
	
	function getEMailHandlerCode($was,$dateSet){
		global $l_shop;
		$datetimeform = "00.00.0000 00:00";
		$dateform = "00.00.0000"; 
		$we_button = new we_button();
		if ($this->FieldsMails['Date'.$was]){
			$EMailhandler = '<table cellpadding="0" cellspacing="0" border="0" width="99%" class="defaultfont"><tr><td class="defaultfont">'.$l_shop['statusmails']['EMail'].': </td>';
			if ($_REQUEST["Mail".$was] != $datetimeform && $_REQUEST["Mail".$was]!='') {
				$EMailhandler .= '<td class="defaultfont" width="150">'.$_REQUEST["Mail".$was].'</td>';
				$but =  $we_button->create_button("image:/mail_resend","javascript:check=confirm('".$l_shop['statusmails']['resent']."'); if (check){SendMail('".$was."');}");
			} else {
				$EMailhandler .= '<td class="defaultfont" width="150">&nbsp;</td>';
				$but =  $we_button->create_button("image:/mail_send","javascript:SendMail('".$was."')");
			}
			if ($dateSet!= $dateform){
				$EMailhandler .= '<td class="defaultfont">'.$but.'</td>';
			} else {
				$EMailhandler .= '<td class="defaultfont">'.getPixel(30,15).'</td>';
			}
			
			$EMailhandler .='</tr></table>';
			
		} else {
			$EMailhandler = getPixel(30,15);
		}

		return $EMailhandler;
	
	}
	
	function save() {
		
		global $DB_WE;
		// check if already inserted
		$query = 'SELECT * FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="weShopStatusMails"';
		
		$DB_WE->query($query);
		
		if ($DB_WE->num_rows() > 0) {
			
			$query = 'UPDATE ' . ANZEIGE_PREFS_TABLE . ' set strFelder="' . mysql_real_escape_string(serialize($this)) . '" WHERE strDateiname="weShopStatusMails"';
			
		} else {
			$query = 'INSERT INTO ' . ANZEIGE_PREFS_TABLE . ' (strDateiname, strFelder) VALUES ("weShopStatusMails", "' . mysql_real_escape_string(serialize($this)) . '")';
		}
		
		if ($DB_WE->query($query)) {
			$q = 'SELECT * FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLangauge"';
			$DB_WE->query($q);
			if ( $DB_WE->num_rows() > 0) {
				$DB_WE->next_record();
				$CLFields = unserialize($DB_WE->f("strFelder"));
				$CLFields['languageField'] =  $this->LanguageData['languageField'];
				$CLFields['languageFieldIsISO'] =  $this->LanguageData['languageFieldIsISO'];
				$DB_WE->query("UPDATE " . ANZEIGE_PREFS_TABLE . " SET strFelder = '" . mysql_real_escape_string(serialize($CLFields)) . "' WHERE strDateiname ='shop_CountryLangauge'");
			}
			return true;
		} else {
			return false;
		}
	}
	
}
?>