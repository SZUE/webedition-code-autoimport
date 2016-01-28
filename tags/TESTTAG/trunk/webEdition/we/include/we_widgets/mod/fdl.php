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
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

if(!(defined('CUSTOMER_TABLE') && permissionhandler::hasPerm('CAN_SEE_CUSTOMER'))){
	return;
}
$db = $GLOBALS['DB_WE'];

$failedLoginsTable = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 1, 4);

$queryFailedLogins = ' FROM ' . FAILED_LOGINS_TABLE . ' f LEFT JOIN ' . CUSTOMER_TABLE . ' c ON f.Username=c.Username	WHERE f.UserTable="tblWebUser" AND f.isValid="true" AND f.LoginDate>DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . ' hour) ' .
	(!permissionhandler::hasPerm("ADMINISTRATOR") && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? ' AND ' . $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '');


if(($maxRows = f('SELECT COUNT(DISTINCT f.Username) ' . $queryFailedLogins, '', $db))){
	$failedLoginsTable->addRow();
	$failedLoginsTable->setCol(0, 0, array(), we_html_tools::getPixel(25, 1));
	$failedLoginsTable->setCol(0, 1, array("class" => "middlefont", "align" => "left"), we_html_element::htmlB(g_l('cockpit', '[kv_failedLogins][username]')) . we_html_tools::getPixel(15, 1));
	$failedLoginsTable->setCol(0, 2, array("class" => "middlefont", "align" => "left"), we_html_element::htmlB(g_l('cockpit', '[kv_failedLogins][numberLogins]')) . we_html_tools::getPixel(10, 1));
	$failedLoginsTable->setCol(0, 3, array(), we_html_tools::getPixel(5, 1));

	$cur = 0;
//	while($maxRows > $cur){
	$db->query('SELECT f.Username, count(f.isValid) AS numberFailedLogins,c.ID AS UID' . $queryFailedLogins . ' GROUP BY f.Username LIMIT ' . $cur . ',100');
	$i = 1;
	while($db->next_record()){
		$webUserID = $db->f('UID');
		$prio = (intval($db->f('numberFailedLogins')) >= SECURITY_LIMIT_CUSTOMER_NAME) || !$webUserID ? 'prio_high.gif' : 'prio_low.gif';
		$failedLoginsTable->addRow();
		$failedLoginsTable->setCol($i, 0, array("class" => "middlefont", "align" => "center"), we_html_element::htmlImg(array("src" => IMAGE_DIR . "pd/" . $prio . "", "width" => 13, "height" => 14)));
		$failedLoginsTable->setCol($i, 1, array("class" => "middlefont", "align" => "left"), $db->f('Username') . we_html_tools::getPixel(10, 1));
		$failedLoginsTable->setCol($i, 2, array("class" => "middlefont", "align" => "left"), intval($db->f('numberFailedLogins')) . ' / ' . SECURITY_LIMIT_CUSTOMER_NAME . ' ' . sprintf(g_l('cockpit', '[kv_failedLogins][logins]'), SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . we_html_tools::getPixel(10, 1));

		$buttonJSFunction = 'YAHOO.util.Connect.asyncRequest( "GET", "' . WEBEDITION_DIR . 'rpc/rpc.php?cmd=ResetFailedCustomerLogins&cns=customer&custid=' . $webUserID . '", ajaxCallbackResetLogins );';
		$failedLoginsTable->setCol($i, 3, array("class" => "middlefont", "align" => "right"), ((intval($db->f('numberFailedLogins')) >= SECURITY_LIMIT_CUSTOMER_NAME && $webUserID) ? we_html_button::create_button("reset", "javascript:" . $buttonJSFunction) : we_html_tools::getPixel(10, 1)));
		$i++;
	}
	//$cur+=1000;
	//}
} else {
	$maxRows = 0;
	$failedLoginsTable->addRow();
	$failedLoginsTable->setCol(1, 0, array("class" => "middlefont", "colspan" => "4", "align" => "left", "style" => "color:green;"), we_html_element::htmlB(g_l("cockpit", "[kv_failedLogins][noFailedLogins]")));
}

$failedLoginHTML = we_html_element::jsScript(JS_DIR . "libs/yui/yahoo-min.js") .
	we_html_element::jsScript(JS_DIR . "libs/yui/event-min.js") .
	we_html_element::jsScript(JS_DIR . "libs/yui/connection-min.js") .
	we_html_element::jsElement('var ajaxCallbackResetLogins = {
													success: function(o) {
														if(typeof(o.responseText) != undefined && o.responseText != "") {
															var weResponse = false;
															try {
																eval( "var weResponse = "+o.responseText );
																if ( weResponse ) {
																	if (weResponse["DataArray"]["data"] == "true") {
																		' . ( isset($newSCurrId) ? 'rpc("","","","","","' . $newSCurrId . '","fdl/fdl");' : '' ) .
		we_message_reporting::getShowMessageCall(g_l('cockpit', '[kv_failedLogins][deleted]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
																		self.setTheme(_sObjId,_oSctCls[_oSctCls.selectedIndex].value);
																	}
																}
															} catch (exc){}
														}
													},
													failure: function(o) {

													}}') .
	$failedLoginsTable->getHtml();
