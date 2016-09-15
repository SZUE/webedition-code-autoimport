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
if(!(defined('CUSTOMER_TABLE') && permissionhandler::hasPerm('CAN_SEE_CUSTOMER'))){
	return;
}
$db = $GLOBALS['DB_WE'];

$failedLoginsTable = new we_html_table(['class' => 'default',], 1, 4);

$queryFailedLogins = ' FROM ' . FAILED_LOGINS_TABLE . ' f LEFT JOIN ' . CUSTOMER_TABLE . ' c ON f.Username=c.Username	WHERE f.UserTable="tblWebUser" AND f.isValid="true" AND f.LoginDate>(NOW() - INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . ' hour) ' .
	(!permissionhandler::hasPerm("ADMINISTRATOR") && $_SESSION['user']['workSpace'][CUSTOMER_TABLE] ? ' AND ' . $_SESSION['user']['workSpace'][CUSTOMER_TABLE] : '');


if(($maxRows = f('SELECT COUNT(DISTINCT f.Username) ' . $queryFailedLogins, '', $db))){
	$failedLoginsTable->addRow();
	$failedLoginsTable->setCol(0, 0, ['style' => 'width:25px;']);
	$failedLoginsTable->setCol(0, 1, ['class' => "middlefont", "style" => "text-align:left"], we_html_element::htmlB(g_l('cockpit', '[kv_failedLogins][username]')));
	$failedLoginsTable->setCol(0, 2, ['class' => "middlefont", "style" => "text-align:left"], we_html_element::htmlB(g_l('cockpit', '[kv_failedLogins][numberLogins]')));

	$cur = 0;
//	while($maxRows > $cur){
	$db->query('SELECT f.Username, COUNT(f.isValid) AS numberFailedLogins,c.ID AS UID' . $queryFailedLogins . ' GROUP BY f.Username LIMIT ' . $cur . ',100');
	$i = 1;
	while($db->next_record()){
		$webUserID = $db->f('UID');
		$prio = (intval($db->f('numberFailedLogins')) >= SECURITY_LIMIT_CUSTOMER_NAME) || !$webUserID ? 'red' : 'green';
		$failedLoginsTable->addRow();
		$failedLoginsTable->setCol($i, 0, ['class' => "middlefont", "style" => "text-align:center"], '<i class="fa fa-dot-circle-o" style="color:' . $prio . '"></i>');
		$failedLoginsTable->setCol($i, 1, ['class' => "middlefont", "style" => "text-align:left"], $db->f('Username'));
		$failedLoginsTable->setCol($i, 2, ['class' => "middlefont", "style" => "text-align:left"], intval($db->f('numberFailedLogins')) . ' / ' . SECURITY_LIMIT_CUSTOMER_NAME . ' ' . sprintf(g_l('cockpit', '[kv_failedLogins][logins]'), SECURITY_LIMIT_CUSTOMER_NAME_HOURS));

		$buttonJSFunction = 'YAHOO.util.Connect.asyncRequest( "GET", WE().consts.dirs.WEBEDITION_DIR+"rpc.php?cmd=ResetFailedCustomerLogins&cns=customer&custid=' . $webUserID . '", ajaxCallbackResetLogins );';
		$failedLoginsTable->setCol($i, 3, ['class' => "middlefont", "style" => "text-align:right"], ((intval($db->f('numberFailedLogins')) >= SECURITY_LIMIT_CUSTOMER_NAME && $webUserID) ? we_html_button::create_button('reset', "javascript:" . $buttonJSFunction) : ''));
		$i++;
	}
	//$cur+=1000;
	//}
} else {
	$maxRows = 0;
	$failedLoginsTable->addRow();
	$failedLoginsTable->setCol(1, 0, ['class' => "middlefont", "colspan" => "4", "style" => "text-align:left;color:green;"], we_html_element::htmlB(g_l("cockpit", "[kv_failedLogins][noFailedLogins]")));
}
if(!isset($aProps)){
	$newSCurrId = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5);
}

$failedLoginHTML = YAHOO_FILES .
	we_html_element::jsElement('var ajaxCallbackResetLogins = {
success: function(o) {
	if(typeof(o.responseText) != undefined && o.responseText != "") {
		try {
			var weResponse =JSON.parse(o.responseText);
			if ( weResponse ) {
				if (weResponse.DataArray.data == "true") {
					' . ( isset($newSCurrId) ? 'rpc("","","","","","' . $newSCurrId . '");' : '' ) .
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

if(!isset($aProps)){//preview requested
	$sJsCode = "
var _sObjId='" . $newSCurrId . "';
var _sType='fdl';
var _sTb='" . g_l('cockpit', '[kv_failedLogins][headline]') . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}";

	echo we_html_tools::getHtmlTop(g_l('cockpit', '[kv_failedLogins][headline]') . ' (' . $maxRows . ')', '', '', we_html_element::jsElement($sJsCode), we_html_element::htmlBody(['style' => 'margin:10px 15px;',
			"onload" => "if(parent!=self){init();}"
			], we_html_element::htmlDiv(["id" => "fdl"
				], we_html_element::htmlDiv(['id' => 'fdl_data'], $failedLoginHTML)
	)));
}