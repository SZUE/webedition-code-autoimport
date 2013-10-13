<?php

/**
 * webEdition CMS
 *
 * $Rev: 6489 $
 * $Author: mokraemer $
 * $Date: 2013-08-19 15:19:40 +0200 (Mon, 19 Aug 2013) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

if(defined("CUSTOMER_TABLE") && we_hasPerm("CAN_SEE_CUSTOMER")){
	
	$failedLoginHTML = "";
	
	$failedLoginsTable = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 1, 4);
	
	$queryFailedLogins = ' FROM ' . FAILED_LOGINS_TABLE . '	WHERE UserTable="tblWebUser" AND isValid="true" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . ' hour)';
	
	if(($maxRows = f('SELECT COUNT(1) AS a ' . $queryFailedLogins, 'a', $DB_WE))){		$failedLoginsTable->addRow();
		$failedLoginsTable->setCol(0, 0, array(), we_html_tools::getPixel(25, 1));
		$failedLoginsTable->setCol(0, 1, array("class" => "middlefont","align"=>"left"), we_html_element::htmlB(g_l('cockpit', '[kv_failedLogins][username]')).we_html_tools::getPixel(15, 1));
		$failedLoginsTable->setCol(0, 2, array("class" => "middlefont","align"=>"left"), we_html_element::htmlB(g_l('cockpit', '[kv_failedLogins][numberLogins]')).we_html_tools::getPixel(10, 1));
		$failedLoginsTable->setCol(0, 3, array(), we_html_tools::getPixel(5, 1));
				$cur = 0;
		while($maxRows > $cur) {			$DB_WE->query('SELECT Username, count(isValid) AS numberFailedLogins' . $queryFailedLogins . ' GROUP BY Username LIMIT '. $cur . ',1000');
			$i = 1;
			while($DB_WE->next_record()) {
				intval($DB_WE->f('numberFailedLogins')) < SECURITY_LIMIT_CUSTOMER_NAME ? $prio = "prio_low.gif" : $prio = "prio_high.gif";
				$failedLoginsTable->addRow();
				$failedLoginsTable->setCol($i, 0, array("class" => "middlefont","align"=>"center"), we_html_element::htmlImg(array("src" => IMAGE_DIR . "pd/".$prio."", "width" => 13, "height" => 14)));
				$failedLoginsTable->setCol($i, 1, array("class" => "middlefont","align"=>"left"), $DB_WE->f('Username').we_html_tools::getPixel(10, 1));
				$failedLoginsTable->setCol($i, 2, array("class" => "middlefont","align"=>"left"), intval($DB_WE->f('numberFailedLogins')).' / '.SECURITY_LIMIT_CUSTOMER_NAME.' '.sprintf(g_l('cockpit', '[kv_failedLogins][logins]'), SECURITY_LIMIT_CUSTOMER_NAME_HOURS).we_html_tools::getPixel(10, 1));
				
				$DB_WE_UserID = new DB_WE();
				$webUserID = 0;
				$webUserID = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $DB_WE->f('Username').'"', 'ID', $DB_WE_UserID);
				$buttonJSFunction = 'YAHOO.util.Connect.asyncRequest( "GET", "' . WEBEDITION_DIR . 'rpc/rpc.php?cmd=ResetFailedCustomerLogins&cns=customer&custid=' . $webUserID . '", ajaxCallbackResetLogins );';
				
				$failedLoginsTable->setCol($i, 3, array("class" => "middlefont","align"=>"right"), ((intval($DB_WE->f('numberFailedLogins')) == SECURITY_LIMIT_CUSTOMER_NAME AND !empty($webUserID)) ? we_button::create_button("reset", "javascript:".$buttonJSFunction) : we_html_tools::getPixel(10, 1) ));
				$i++;
			}
			$cur+=1000;
		}	}else{
		$failedLoginsTable->addRow();
		$failedLoginsTable->setCol(1, 0, array("class" => "middlefont","colspan" => "4","align"=>"left","style"=>"color:green;"), we_html_element::htmlB("Keine fehlgeschlagenen Loginversuche vorhanden"));
	}

	$failedLoginHTML .= we_html_element::jsScript(JS_DIR . "libs/yui/yahoo-min.js");
	$failedLoginHTML .= we_html_element::jsScript(JS_DIR . "libs/yui/event-min.js");
	$failedLoginHTML .= we_html_element::jsScript(JS_DIR . "libs/yui/connection-min.js");
	
	$failedLoginHTML .= we_html_element::jsElement('var ajaxCallbackResetLogins = {
													success: function(o) {
														if(typeof(o.responseText) != undefined && o.responseText != "") {
															var weResponse = false;
															try {
																eval( "var weResponse = "+o.responseText );
																if ( weResponse ) {
																	if (weResponse["DataArray"]["data"] == "true") {
																		alert("'.g_l('cockpit', '[kv_failedLogins][deleted]').'");
																		self.setTheme(_sObjId,_oSctCls[_oSctCls.selectedIndex].value);
																	}
																}
															} catch (exc){}
														}
													},
													failure: function(o) {
									
													}}');

	$failedLoginHTML .= $failedLoginsTable->getHtml();
	//$msg_cmd = "javascript:top.we_cmd('messaging_start','message');";
	//$todo_cmd = "javascript:top.we_cmd('messaging_start','todo');";
	//$msg_button = we_html_element::htmlA(array("href" => $msg_cmd), we_html_element::htmlImg(array("src" => IMAGE_DIR . 'pd/msg/message.gif', "width" => 34, "height" => 34, "border" => 0)));
	//$todo_button = we_html_element::htmlA(array("href" => $todo_cmd), we_html_element::htmlImg(array("src" => IMAGE_DIR . 'pd/msg/todo.gif', "width" => 34, "height" => 34, "border" => 0)));
}
