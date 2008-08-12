<?php

	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_htmlElement.inc.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/alert.inc.php");
	
	
	//	user
	$_isUsedByUser = $we_doc->isLockedByUser();
	$_username = f("SELECT username FROM " . USER_TABLE . " WHERE ID=$_isUsedByUser","username",$DB_WE);
	
	
	$_messageTbl = new we_htmlTable(	array(	"border"      => 0,
												"cellpadding" => 0,
												"cellspacing" => 0),
									2,
									6);
	
	
	$refreshButton = "";
	
	if(!isset($_REQUEST["SEEM_edit_include"]) || $_REQUEST["SEEM_edit_include"]== "false" ){
		
		$we_button = new we_button();
		$refreshButton = $we_button->create_button("refresh", "javascript:top.weNavigationHistory.navigateReload();");
	}
	
	//	spaceholder
	$_messageTbl->setColContent(0,0, getPixel(20,7));
	$_messageTbl->setColContent(1,1, we_htmlElement::htmlImg(array("src" => IMAGE_DIR . "alert.gif")));
	$_messageTbl->setColContent(1,2, getPixel(5,2));
	$_messageTbl->setCol(1,3, array("class" => "defaultfont"), sprintf($l_alert["file_locked_footer"], $_username));
	$_messageTbl->setColContent(1,4, getPixel(5,2));
	$_messageTbl->setColContent(1,5, $refreshButton);
	
	
	$_head = we_htmlElement::htmlHead(we_htmlElement::jsElement("\n<!--\ntop.toggleBusy(0);\n-->\n") . STYLESHEET);
	$_body = we_htmlElement::htmlBody(	array(	"background" => "/webEdition/images/edit/editfooterback.gif",
												"bgcolor"    => "white"),
										$_messageTbl->getHtmlCode());
	
	
	print we_htmlElement::htmlHtml($_head . "\n" . $_body);
?>