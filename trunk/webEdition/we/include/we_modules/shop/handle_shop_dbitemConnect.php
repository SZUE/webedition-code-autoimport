<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | PHP version 4.1.0 or higher                                          |
// +----------------------------------------------------------------------+
// | Copyright (c) 2000 - 2005 living-e AG                   |
// +----------------------------------------------------------------------+
// +----------------------------------------------------------------------+
// | shopModule by Jan Gorba                                              |
// +----------------------------------------------------------------------+
//
// $Id: handle_shop_dbitemConnect.php,v 1.5 2007/04/25 15:23:01 holger.meyer Exp $


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_html_tools.inc.php");

if(defined("SHOP_TABLE")){
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/modules/shop.inc.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/modules/shop_month.inc.php");
}

protect();

// grep the last element from the year-set, wich is the current year
	$DB_WE->query("SELECT DATE_FORMAT(DateOrder,'%Y') AS DateOrd FROM ".SHOP_TABLE . " ORDER BY DateOrd");
	while ($DB_WE->next_record()) {
		if(isset($strs)){
	$strs = array($DB_WE->f("DateOrd"));
	$yearTrans = end($strs);
		}
    }
    // print $yearTrans;

/// config
$DB_WE->query("SELECT strFelder from ".ANZEIGE_PREFS_TABLE." where strDateiname = 'shop_pref'");
	$DB_WE->next_record();
	$feldnamen = explode("|",$DB_WE->f("strFelder"));
	for ($i=0;$i<=3;$i++) {
		$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
	}
	 $fe = explode(",",$feldnamen[3]);
	  if(empty($classid)){
	  	$classid = $fe[0];
	  }
     
      //$resultO = count($fe);
      $resultO = array_shift ($fe);
      
  
     $dbTitlename="shoptitle";
   	// wether the resultset ist empty?
	$DB_WE->query("SELECT count(Name) as Anzahl FROM ".LINK_TABLE." WHERE Name ='$dbTitlename'");
	$DB_WE->next_record();
	$resultD = $DB_WE->f("Anzahl");

?>