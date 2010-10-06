<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/modules/shop.inc.php');
class weShopStatusMails {
	
	
	var $FieldsHidden; //an array of statusfield names not to be shown
	var $FieldsText; //an array with keys equal to name of statusfield, and value = text to be shown
	var $FieldsMails; //an array with keys equal to name of statusfield, and value = 0 for no Mail, 1 for Mail by Hand, 2 for automatic mails
	var $EMailData; // an array with the E-Mail data, see getShopStatusMails
	var $LanguageData; // an array with the Language data, see getShopStatusMails
	var $FieldsDocuments; // an array with dfault values and separate Arrays for each Langauge, see getShopStatusMails
	var $StatusFields = array('DateOrder','DateConfirmation','DateCustomA','DateCustomB','DateCustomC','DateShipping','DateCancellation','DatePayment','DateFinished');

	
	function weShopStatusMails( $FieldsHidden, $FieldsText, $FieldsMails,$EMailData,$LanguageData,$FieldsDocuments) {
		
		$this->FieldsHidden = $FieldsHidden;
		$this->FieldsText = $FieldsText; 
		$this->FieldsMails = $FieldsMails; 
		$this->EMailData = $EMailData; 
		$this->LanguageData = $LanguageData; 
		$this->FieldsDocuments = $FieldsDocuments;
	}
	

	
	function initByRequest(&$req) {
		
		return new weShopStatusMails(
			$req['FieldsHidden'],
			$req['FieldsText'],
			$req['FieldsMails'],
			$req['EMailData'],
			$req['LanguageData'],
			$req['FieldsDocuments']
		);
	
		
	}
	
	function getShopStatusMails() {
		
		global $DB_WE,$l_shop;
		
		$query = 'SELECT * FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="weShopStatusMails"	';
		
		$DB_WE->query($query);
		
		if ($DB_WE->next_record()) {
			
			return unserialize($DB_WE->f('strFelder'));
			
		} else {
			return new weShopStatusMails(
				array(//Fieldshidden
					'DateOrder' => 0,
					'DateConfirmation' => 0,
					'DateCustomA' => 0,
					'DateCustomB' => 0,
					'DateCustomC' => 0,
					'DateShipping' => 0,
					'DateCancellation' => 0,
					'DatePayment' => 0,
					'DateFinished' => 0		
				
				),
				array( //FieldsTexts
					'DateOrder' => $l_shop["bestelldatum"],
					'DateConfirmation' => $l_shop["bestaetigt"],
					'DateCustomA' => $l_shop["customA"],
					'DateCustomB' => $l_shop["customB"],
					'DateCustomC' => $l_shop["customC"],
					'DateShipping' => $l_shop["bearbeitet"],
					'DateCancellation' => $l_shop["storniert"],
					'DatePayment' => $l_shop["bezahlt"],
					'DateFinished' => $l_shop["beendet"]				
				),
				array( //FieldsMails
					'DateOrder' => 2,
					'DateConfirmation' => 1,
					'DateCustomA' => 1,
					'DateCustomB' => 1,
					'DateCustomC' =>1,
					'DateShipping' => 1,
					'DateCancellation' => 1,
					'DatePayment' => 1,
					'DateFinished' => 0
				),
				array(//statusFieldsEMailData
					'address' => '',
					'name' => '',
					'DocumentSubjectField' =>'title'
				),
				array( //statusFieldsLanguageData
					'useLanguages' => 1,
					'languageField' => '',
					'languageFieldIsISO' => 1				
				),
				array(
					'default' => array(
						'DateOrder' => '',
						'DateConfirmation' => '',
						'DateCustomA' => '',
						'DateCustomB' => '',
						'DateCustomC' =>'',
						'DateShipping' => '',
						'DateCancellation' => '',
						'DatePayment' => '',
						'DateFinished' => ''
					)
				)	
			
			);
		}
	}
	
	function makeArrayFromConditionField($req) {
		
	}
	
	function makeArrayFromReq($req) {

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