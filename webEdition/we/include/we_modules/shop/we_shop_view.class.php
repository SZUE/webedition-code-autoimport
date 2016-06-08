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
/* the parent class of storagable webEdition classes */

class we_shop_view extends we_modules_view{
	var $frameset;
	var $topFrame;
	var $raw;
	private $CLFields = array(); //
	private $classIds = array();

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';


		/* 		$years = we_shop_shop::getAllOrderYears();
		  foreach($years as $cur){
		  }
		 */
		/// config
		$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"', '', $this->db));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}

		$fe = explode(',', $feldnamen[3]);
		$classid = $fe[0];
		//$resultO = count ($fe);
		$resultO = array_shift($fe);

		// whether the resultset is empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1', '', $this->db);


		return we_html_element::jsElement('
WE().consts.g_l.shop={
	no_perms:"' . we_message_reporting::prepareMsgForJS(g_l('modules_shop', '[no_perms]')) . '",
	nothing_to_save:"' . we_message_reporting::prepareMsgForJS(g_l('modules_shop', '[nothing_to_save]')) . '",
	nothing_to_delete:"' . we_message_reporting::prepareMsgForJS(g_l('modules_shop', '[nothing_to_delete]')) . '",
	no_order_there:"' . we_message_reporting::prepareMsgForJS(g_l('modules_shop', '[no_order_there]')) . '",
	delete_alert:"' . g_l('modules_shop', '[delete_alert]') . '",
	del_shop:"' . g_l('modules_shop', '[del_shop]') . '",
};
WE().consts.dirs.WE_SHOP_MODULE_DIR="' . WE_SHOP_MODULE_DIR . '";
var isDocument=' . intval($resultD) . ';
var isObject=' . intval((!empty($resultO))) . ';
var classID=' . intval($classid) . ';
') .
			we_html_element::jsScript(JS_DIR . 'we_modules/shop/we_shop_view.js', 'parent.document.title=\'' . $title . '\';');
	}

	function getJSProperty(){
		return parent::getJSProperty() .
			we_html_element::jsElement('
function submitForm(target,action,method) {
	var f = self.document.we_form;
	f.target =  (target?target:"edbody");
	f.action = (action?action:"' . $this->frameset . '");
	f.method = (method?method:"post");
	f.submit();
}') .
			we_html_element::jsScript(JS_DIR . 'we_modules/shop/we_shop_property.js');
	}

	function getProperties(){
		we_html_tools::protect();
		echo STYLESHEET;

		//$weShopVatRule = weShopVatRule::getShopVatRule();

		$weShopStatusMails = we_shop_statusMails::getShopStatusMails();

		// Get Country and Langfield Data
		$this->CLFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_CountryLanguage"', '', $this->db), array(
			'stateField' => '-',
			'stateFieldIsISO' => 0,
			'languageField' => '-',
			'languageFieldIsISO' => 0
		));

		// config
		$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"', '', $this->db));
		$waehr = '&nbsp;' . oldHtmlspecialchars($feldnamen[0]);
		$numberformat = $feldnamen[2];
		$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
		$this->classIds = makeArrayFromCSV($classid);
		$mwst = ($feldnamen[1] ? : '');

		$da = '%d.%m.%Y';
		$dateform = '00.00.0000';
		$db = '%d.%m.%Y %H:%i';

		$this->processCommands(); //imi

		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
		if(we_base_request::_(we_base_request::BOOL, 'deletethisorder')){
			$this->db->query('DELETE FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid);
			echo we_html_element::jsElement('top.content.treeData.deleteEntry(' . $bid . ')') .
			'</head>
			<body class="weEditorBody" onunload="doUnload()">
			<table style="width:300px">
			  <tr>
				<td colspan="2" class="defaultfont">' . we_html_tools::htmlDialogLayout('<span class="defaultfont">' . g_l('modules_shop', '[geloscht]') . '</span>', g_l('modules_shop', '[loscht]')) . '</td>
			  </tr>
			  </table></html>';
			exit;
		}

		if(($id = we_base_request::_(we_base_request::INT, 'deleteaarticle'))){
			$this->db->query('DELETE FROM ' . SHOP_TABLE . ' WHERE IntID=' . $id);
			if(f('SELECT COUNT(1) FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid, '', $this->db) < 1){
				$letzerartikel = 1;
			}
		}

		// Get Customer data
		$_REQUEST['cid'] = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid . ' LIMIT 1', '', $this->db);

		if(($fields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="edit_shop_properties"', '', $this->db)))){
			// we have an array with following syntax:
			// array ( 'customerFields' => array('fieldname ...',...)
			//         'orderCustomerFields' => array('fieldname', ...) )
		} else {
			//unsupported
			t_e('unsupported Shop-Settings found. Please open settings and readjust the settings of the shop module.');
			$fields = array();
		}

		$_customer = $this->getOrderCustomerData(we_base_request::_(we_base_request::INT, 'bid', 0), $fields);

		if(isset($_REQUEST['SendMail'])){
			$weShopStatusMails->sendEMail($_REQUEST['SendMail'], $_REQUEST['bid'], $_customer);
		}
		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
		foreach(we_shop_statusMails::$StatusFields as $field){
			if(isset($_REQUEST[$field])){
				list($day, $month, $year) = explode('.', $_REQUEST[$field]);
				$DateOrder = $year . '-' . $month . '-' . $day . ' 00:00:00';
				$this->db->query('UPDATE ' . SHOP_TABLE . ' SET ' . $field . '="' . $this->db->escape($DateOrder) . '" WHERE IntOrderID=' . $bid);
				$weShopStatusMails->checkAutoMailAndSend(substr($field, 4), $_REQUEST['bid'], $_customer);
			}
		}

		if(($article = we_base_request::_(we_base_request::INT, 'article'))){
			if(($preis = we_base_request::_(we_base_request::FLOAT, 'preis')) !== false){
				$this->db->query('UPDATE ' . SHOP_TABLE . ' SET Price=' . abs($preis) . ' WHERE IntID=' . $article);
			} else if(($anz = we_base_request::_(we_base_request::FLOAT, 'anzahl')) !== false){
				$this->db->query('UPDATE ' . SHOP_TABLE . ' SET IntQuantity=' . abs($anz) . ' WHERE IntID=' . $article);
			} else if(isset($_REQUEST['vat'])){
				$tmpDoc = we_unserialize(f('SELECT strSerial FROM ' . SHOP_TABLE . ' WHERE IntID=' . $article, '', $this->db));
				if($tmpDoc){
					$tmpDoc[WE_SHOP_VAT_FIELD_NAME] = $_REQUEST['vat'];
					$this->db->query('UPDATE ' . SHOP_TABLE . ' SET strSerial="' . $this->db->escape(we_serialize($tmpDoc, SERIALIZE_JSON)) . '" WHERE IntID=' . $article);
				}
			}
		}

		if(!isset($letzerartikel)){ // order has still articles - get them all
			// ********************************************************************************
			// first get all information about orders, we need this for the rest of the page
			//

			$format = array();
			foreach(we_shop_statusMails::$StatusFields as $field){
				$format[] = 'DATE_FORMAT(' . $field . ',"' . $da . '") AS ' . $field;
			}
			foreach(we_shop_statusMails::$MailFields as $field){
				$format[] = 'DATE_FORMAT(' . $field . ',"' . $db . '") AS ' . $field;
			}

			$this->db->query('SELECT IntID,IntCustomerID,IntArticleID,strSerial,strSerialOrder,IntQuantity,Price, ' . implode(',', $format) . '	FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . we_base_request::_(we_base_request::INT, 'bid', 0));

			// loop through all articles
			while($this->db->next_record()){

				// get all needed information for order-data
				$_REQUEST['cid'] = $this->db->f('IntCustomerID');
				$SerialOrder[] = $this->db->f('strSerialOrder');
				foreach(we_shop_statusMails::$StatusFields as $field){
					$_REQUEST[$field] = $this->db->f($field);
				}
				foreach(we_shop_statusMails::$MailFields as $field){
					$_REQUEST[$field] = $this->db->f($field);
				}

				// all information for article
				$ArticleId[] = $this->db->f('IntArticleID'); // id of article (object or document) in shopping cart
				$tblOrdersId[] = $this->db->f('IntID');
				$Quantity[] = $this->db->f('IntQuantity');
				$Serial[] = $this->db->f('strSerial'); // the serialised doc
				$Price[] = str_replace(',', '.', $this->db->f('Price')); // replace , by . for float values
			}
			if(!isset($ArticleId)){
				echo we_html_element::jsElement('parent.parent.document.getElementById("iconbar").location.reload();') . '
</head>
<body class="weEditorBody" onunload="doUnload()">
<table style="width:300px">
	<tr>
		<td colspan="2" class="defaultfont">' . we_html_tools::htmlDialogLayout("<span class='defaultfont'>" . g_l('modules_shop', '[orderDoesNotExist]') . '</span>', g_l('modules_shop', '[loscht]')) . '</td>
	</tr>
</table></body></html>';
				exit;
			}
			//
			// first get all information about orders, we need this for the rest of the page
			// ********************************************************************************
			// ********************************************************************************
			// no get information about complete order
			// - pay VAT?
			// - prices are net?
			if($ArticleId){
				// first unserialize order-data
				if($SerialOrder[0]){
					$orderData = we_unserialize($SerialOrder[0]);
					$customCartFields = isset($orderData[WE_SHOP_CART_CUSTOM_FIELD]) ? $orderData[WE_SHOP_CART_CUSTOM_FIELD] : array();
				} else {
					$orderData = array();
					$customCartFields = array();
				}

				// prices are net?
				$pricesAreNet = (isset($orderData[WE_SHOP_PRICE_IS_NET_NAME]) ? $orderData[WE_SHOP_PRICE_IS_NET_NAME] : true);

				// must calculate vat?
				$calcVat = (isset($orderData[WE_SHOP_CALC_VAT]) ? $orderData[WE_SHOP_CALC_VAT] : true);
			}
			//
			// no get information about complete order
			// ********************************************************************************
			// ********************************************************************************
			// Building table with customer and order data fields - start
			//
			$customerFieldTable = '';

			// first show fields Forename and surname
			if(isset($_customer['Forename'])){
				$customerFieldTable .='
		<tr style="height:25px">
			<td class="defaultfont" style="width:86px;vertical-align:top;height:25px">' . g_l('modules_customer', '[Forname]') . ':</td>
			<td class="defaultfont" style="vertical-align:top;width:40px;height:25px"></td>
			<td style="width:20px;height:25px;"></td>
			<td class="defaultfont" style="vertical-align:top" colspan="6" height="25">' . $_customer['Forename'] . '</td>
		</tr>';
			}
			if(isset($_customer['Surname'])){
				$customerFieldTable .='
		<tr style="height:25px">
			<td class="defaultfont" style="width:86px;vertical-align:top;height:25px;">' . g_l('modules_customer', '[Surname]') . ':</td>
			<td class="defaultfont" style="vertical-align:top;width:40px;height:25px"></td>
			<td style="width:20px;height:25px;"></td>
			<td class="defaultfont" style="vertical-align:top;height:25px;" colspan="6">' . $_customer['Surname'] . '</td>
		</tr>';
			}

			foreach($_customer as $key => $value){
				if(in_array($key, $fields['customerFields'])){
					switch($key){
						case $this->CLFields['stateField']:
							if($this->CLFields['stateFieldIsISO']){
								$value = g_l('countries', '[' . strtoupper($value) . ']');
							}
							break;
						case $this->CLFields['languageField']:
							if($this->CLFields['languageFieldIsISO']){
								$value = g_l('languages', '[' . strtolower($value) . ']');
							}
							break;
					}
					$customerFieldTable .='
		<tr height="25">
			<td class="defaultfont" style="width:86px;vertical-align:top;height:25px;">' . $key . ':</td>
			<td class="defaultfont" style="vertical-align:top;width:40px;height:25px;"></td>
			<td style="width:20px;height:25px;"></td>
			<td class="defaultfont" style="vertical-align:top;height:25px;" colspan="6">' . $value . '</td>
		</tr>';
				}
			}

			$orderDataTable = '
		<table style="width:99%" class="default defaultfont">';
			foreach(we_shop_statusMails::$StatusFields as $field){
				if(!$weShopStatusMails->FieldsHidden[$field]){
					$EMailhandler = $weShopStatusMails->getEMailHandlerCode(substr($field, 4), $_REQUEST[$field]);
					$orderDataTable .= '
			<tr height="25">
				<td class="defaultfont" style="width:86px;vertical-align:top" height="25">' . ($field === 'DateOrder' ? g_l('modules_shop', '[bestellnr]') : '') . '</td>
				<td class="defaultfont" style="vertical-align:top;width:40px;height:25px"><b>' . ($field === 'DateOrder' ? $_REQUEST['bid'] : '') . '</b></td>
				<td style="width:20px;height:25px;"></td>
				<td style="width:98px;height:25px;" class="defaultfont">' . $weShopStatusMails->FieldsText[$field] . '</td>
				<td style="height:25px;width:14px"></td>
				<td class="defaultfont" style="width:14px;text-align:right;height:25px;">
					<div id="div_Calendar_' . $field . '">' . (($_REQUEST[$field] == $dateform) ? '-' : $_REQUEST[$field]) . '</div>
					<input type="hidden" name="' . $field . '" id="hidden_Calendar_' . $field . '" value="' . (($_REQUEST[$field] == $dateform) ? '-' : $_REQUEST[$field]) . '" />
				</td>
				<td style="height:25px;width:10px"></td>
				<td style="width:102px;vertical-align:top;height:25px">' . we_html_button::create_button(we_html_button::CALENDAR, "javascript:", null, null, null, null, null, null, false, 'button_Calendar_' . $field) . '</td>
				<td style="width:300px;height:25px"  class="defaultfont">' . $EMailhandler . '</td>
			</tr>';
				}
			}
			$orderDataTable .= '
			<tr height="5">
				<td class="defaultfont" style="width:86px;vertical-align:top;height:5px"></td>
				<td class="defaultfont" style="vertical-align:top" height="5" width="40"></td>
				<td height="5" width="20"></td>
				<td width="98" class="defaultfont" style="vertical-align:top" height="5"></td>
				<td height="5"></td>
				<td width="14" class="defaultfont" style="text-align:right;vertical-align:top" height="5"></td>
				<td height="5"></td>
				<td width="102" style="vertical-align:top" height="5"></td>
				<td width="30" height="5"></td>
			</tr>
			<tr height="1">
				<td class="defaultfont" style="vertical-align:top" colspan="9" bgcolor="grey" height="1"></td>
			</tr>
			<tr>
				<td class="defaultfont" width="86" style="vertical-align:top"></td>
				<td class="defaultfont" style="vertical-align:top" width="40"></td>
				<td width="20"></td>
				<td width="98" class="defaultfont" style="vertical-align:top"></td>
				<td></td>
				<td width="14" class="defaultfont" style="text-align:right;vertical-align:top"></td>
				<td></td>
				<td width="102" style="vertical-align:top"></td>
				<td width="30"></td>
			</tr>' . $customerFieldTable . '
			<tr>
				<td colspan="9"><a href="javascript:we_cmd(\'edit_order_customer\');">' . g_l('modules_shop', '[order][edit_order_customer]') . '</a></td>
			</tr>
			<tr>
				<td colspan="9">' . (permissionhandler::hasPerm('EDIT_CUSTOMER') ? '<a href="javascript:we_cmd(\'customer_edit\');">' . g_l('modules_shop', '[order][open_customer]') . '</a>' : '') . ' </td>
			</tr>
		</table>';
			//
			// end of "Building table with customer fields"
			// ********************************************************************************
			// ********************************************************************************
			// "Building the order infos"
			//

			// headline here - these fields are fix.
			$orderTable = '
		<table width="99%" class="default defaultfont">
			<tr class="defaultfont lowContrast">
				<th style="height:25px;padding-right:15px;">' . g_l('modules_shop', '[anzahl]') . '</th>
				<th style="height:25px;padding-right:15px;">' . g_l('modules_shop', '[Titel]') . '</th>
				<th style="height:25px;padding-right:15px;">' . g_l('modules_shop', '[Beschreibung]') . '</th>
				<th style="height:25px;padding-right:15px;">' . g_l('modules_shop', '[Preis]') . '</th>
				<th style="height:25px;padding-right:15px;">' . g_l('modules_shop', '[Gesamt]') . '</th>' .
				($calcVat ? '<th height="25">' . g_l('modules_shop', '[mwst]') . '</th>' : '' ) . '
			</tr>';


			$articlePrice = $totalPrice = 0;
			$articleVatArray = array();
			// now loop through all articles in this order
			foreach($ArticleId as $i => $currentArticle){

				// now init each article
				$shopArticleObject = (empty($Serial[$i]) ? // output 'document-articles' if $Serial[$d] is empty. This is when an order has been extended
						// this should not happen any more
						we_shop_Basket::getserial($currentArticle, we_shop_shop::DOCUMENT) :
						// output if $Serial[$i] is not empty. This is when a user ordered an article online
						we_unserialize($Serial[$i]));

				if($shopArticleObject === false){
					t_e('Error in DB-data', $currentArticle, $Serial[$i]);
					continue;
				}

				// now determine VAT
				$articleVat = (isset($shopArticleObject[WE_SHOP_VAT_FIELD_NAME]) ?
						$shopArticleObject[WE_SHOP_VAT_FIELD_NAME] :
						((isset($mwst)) ?
							$mwst :
							0));

				// determine taxes - correct price, etc.
				$Price[$i]/=($pricesAreNet || $calcVat ? 1 : (100 + $articleVat) / 100);
				$articlePrice = $Price[$i] * $Quantity[$i];
				$totalPrice += $articlePrice;

				// calculate individual vat for each article
				if($calcVat){

					if($articleVat > 0){
						if(!isset($articleVatArray[$articleVat])){ // avoid notices
							$articleVatArray[$articleVat] = 0;
						}
						$articleVatArray[$articleVat] += ($articlePrice * $articleVat / (100 + ($pricesAreNet ? 0 : $articleVat)));
					}
				}

// table row of one article
				$orderTable .= '
		<tr><td height="1" colspan="11"><hr style="color: black" noshade /></td></tr>
		<tr>
			<td class="shopContentfontR">' . "<a href=\"javascript:var anzahl=prompt('" . g_l('modules_shop', '[jsanz]') . "','" . $Quantity[$i] . "'); if(anzahl != null){if(anzahl.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&anzahl='+anzahl;}}\">" . $Quantity[$i] . "</a>" . '</td>
			<td></td>
			<td>' . self::getFieldFromShoparticle($shopArticleObject, WE_SHOP_TITLE_FIELD_NAME, 35) . '</td>
			<td></td>
			<td>' . self::getFieldFromShoparticle($shopArticleObject, WE_SHOP_DESCRIPTION_FIELD_NAME, 45) . '</td>
			<td></td>
			<td class="shopContentfontR">' . "<a href=\"javascript:var preis = prompt('" . g_l('modules_shop', '[jsbetrag]') . "','" . $Price[$i] . "'); if(preis != null ){if(preis.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . "}else{document.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&preis=' + preis; } }\">" . we_base_util::formatNumber($Price[$i]) . "</a>" . $waehr . '</td>
			<td></td>
			<td class="shopContentfontR">' . we_base_util::formatNumber($articlePrice) . $waehr . '</td>' .
					($calcVat ? '<td></td><td class="shopContentfontR small">(' . "<a href=\"javascript:var vat = prompt('" . g_l('modules_shop', '[keinezahl]') . "','" . $articleVat . "'); if(vat != null ){if(vat.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&vat=' + vat; } }\">" . we_base_util::formatNumber($articleVat) . "</a>" . '%)</td>' : '') . '
			<td>' . we_html_button::create_button(we_html_button::TRASH, "javascript:check=confirm('" . g_l('modules_shop', '[jsloeschen]') . "'); if (check){document.location.href=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&bid=" . $_REQUEST["bid"] . "&deleteaarticle=" . $tblOrdersId[$i] . "';}", true, 100, 22, "", "", !permissionhandler::hasPerm("DELETE_SHOP_ARTICLE")) . '</td>
		</tr>';
				// if this article has custom fields or is a variant - we show them in a extra rows
				// add variant.
				if(!empty($shopArticleObject['WE_VARIANT'])){

					$orderTable .='
		<tr>
			<td colspan="4"></td>
			<td class="small" colspan="6">' . g_l('modules_shop', '[variant]') . ': ' . $shopArticleObject['WE_VARIANT'] . '</td>
		</tr>';
				}
				// add custom fields
				if(isset($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD]) && is_array($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD]) && count($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD])){

					$caField = '';
					foreach($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD] as $key => $value){
						$caField .= "$key: $value; ";
					}

					$orderTable .='
		<tr>
			<td colspan="4"></td>
			<td class="small" colspan="6">' . $caField . '</td>
		</tr>';
				}
			}

			// "Sum of order"
			// add shipping to costs
			if(isset($orderData[WE_SHOP_SHIPPING])){

				// just calculate netPrice, gros, and taxes

				if(!isset($articleVatArray[$orderData[WE_SHOP_SHIPPING]['vatRate']])){
					$articleVatArray[$orderData[WE_SHOP_SHIPPING]['vatRate']] = 0;
				}

				if($orderData[WE_SHOP_SHIPPING]['isNet']){ // all correct here
					$shippingCostsNet = $orderData[WE_SHOP_SHIPPING]['costs'];
					$shippingCostsVat = $orderData[WE_SHOP_SHIPPING]['costs'] * $orderData[WE_SHOP_SHIPPING]['vatRate'] / 100;
					$shippingCostsGros = $shippingCostsNet + $shippingCostsVat;
				} else {
					$shippingCostsGros = $orderData[WE_SHOP_SHIPPING]['costs'];
					$shippingCostsVat = $orderData[WE_SHOP_SHIPPING]['costs'] / ($orderData[WE_SHOP_SHIPPING]['vatRate'] + 100) * $orderData[WE_SHOP_SHIPPING]['vatRate'];
					$shippingCostsNet = $orderData[WE_SHOP_SHIPPING]['costs'] / ($orderData[WE_SHOP_SHIPPING]['vatRate'] + 100) * 100;
				}
				$articleVatArray[$orderData[WE_SHOP_SHIPPING]['vatRate']] += $shippingCostsVat;
			}

			$orderTable .= '
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[Preis]') . ':</td>
			<td colspan="4" class="shopContentfontR"><strong>' . we_base_util::formatNumber($totalPrice) . $waehr . '</strong></td>
		</tr>';

			if($calcVat){ // add Vat to price
				$totalPriceAndVat = $totalPrice;

				if($pricesAreNet){ // prices are net
					$orderTable .= '<tr><td height="1" colspan="11"><hr style="color: black" noshade /></td></tr>';

					if(isset($orderData[WE_SHOP_SHIPPING]) && isset($shippingCostsNet)){

						$totalPriceAndVat += $shippingCostsNet;
						$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
			<td colspan="4" class="shopContentfontR"><strong><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . we_base_util::formatNumber($shippingCostsNet) . $waehr . '</a></strong></td>
			<td></td>
			<td class="shopContentfontR small">(' . we_base_util::formatNumber($orderData[WE_SHOP_SHIPPING]['vatRate']) . '%)</td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr style="color: black" noshade /></td>
		</tr>';
					}
					$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[plusVat]') . '</label>:</td>
			<td colspan="7"></td>
			<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edbody&bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
		</tr>';
					foreach($articleVatArray as $vatRate => $sum){
						if($vatRate){
							$totalPriceAndVat += $sum;
							$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR">' . $vatRate . ' %:</td>
			<td colspan="4" class="shopContentfontR">' . we_base_util::formatNumber($sum) . $waehr . '</td>
		</tr>';
						}
					}
					$orderTable .= '
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
			<td colspan="4" class="shopContentfontR"><strong>' . we_base_util::formatNumber($totalPriceAndVat) . $waehr . '</strong></td>
		</tr>';
				} else { // prices are gros
					$orderTable .= '<tr><td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td></tr>';

					if(isset($orderData[WE_SHOP_SHIPPING]) && isset($shippingCostsGros)){
						$totalPrice += $shippingCostsGros;
						$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
			<td colspan="4" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . we_base_util::formatNumber($shippingCostsGros) . $waehr . '</a></td>
			<td></td>
			<td class="shopContentfontR small">(' . we_base_util::formatNumber($orderData[WE_SHOP_SHIPPING]['vatRate']) . '%)</td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
			<td colspan="4" class="shopContentfontR"><strong>' . we_base_util::formatNumber($totalPrice) . $waehr . '</strong></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>';
					}

					$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[includedVat]') . '</label>:</td>
			<td colspan="7"></td>
			<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edbody&bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
		</tr>';
					foreach($articleVatArray as $vatRate => $sum){
						if($vatRate){
							$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR">' . $vatRate . ' %:</td>
			<td colspan="4" class="shopContentfontR">' . we_base_util::formatNumber($sum) . $waehr . '</td>
		</tr>';
						}
					}
				}
			} else {

				if(isset($shippingCostsNet)){
					$totalPrice += $shippingCostsNet;

					$orderTable .= '
		<tr>
			<td height="1" colspan="11"><hr style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
			<td colspan="4" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\')">' . we_base_util::formatNumber($shippingCostsNet) . $waehr . '</a></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[edit_order][calculate_vat]') . '</label></td>
			<td colspan="7"></td>
			<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edbody&bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=1\';" type="checkbox" name="calculateVat" value="1" /></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
			<td colspan="4" class="shopContentfontR"><strong>' . we_base_util::formatNumber($totalPrice) . $waehr . '</strong></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>';
				} else {

					$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[edit_order][calculate_vat]') . '</label></td>
			<td colspan="7"></td>
			<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edbody&bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=1\';" type="checkbox" name="calculateVat" value="1" /></td>
		</tr>';
				}
			}
			$orderTable .= '</table>';
			//
			// "Sum of order"
			// ********************************************************************************
			// ********************************************************************************
			// "Additional fields in shopping basket"
			//

			// at last add custom shopping fields to order
			// table with orders ends here

			$customCartFieldsTable = '<table class="default" width="99%">
					<tr>
						<th colspan="3" class="defaultfont lowContrast" height="30">' . g_l('modules_shop', '[order_comments]') . '</th>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>';

			foreach($customCartFields as $key => $value){
				$customCartFieldsTable .= '<tr>
						<td class="defaultfont" style="padding-right:15px;vertical-align:top"><b>' . $key . ':</b></td>
						<td class="defaultfont" style="padding-right:15px;vertical-align:top">' . nl2br($value) . '</td>
						<td style="padding-right:15px;vertical-align:top;">' . we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('edit_shop_cart_custom_field','" . $key . "');") . '</td>
						<td style="vertical-align:top">' . we_html_button::create_button(we_html_button::TRASH, "javascript:check=confirm('" . sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field]'), $key) . "'); if (check) { document.location.href=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&we_cmd[0]=delete_shop_cart_custom_field&bid=" . $_REQUEST["bid"] . "&cartfieldname=" . $key . "'; }") . '</td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>';
			}
			$customCartFieldsTable .= '<tr>
						<td>' . we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('edit_shop_cart_custom_field');") . '</td>
					</tr>
					</table>';


			//
			// "Additional fields in shopping basket"
			// ********************************************************************************
			//
			// "Building the order infos"
			// ********************************************************************************
			// ********************************************************************************
			// "Html output for order with articles"
			//
		echo we_html_tools::getCalendarFiles() .
			we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
			we_html_element::jsElement('
var SCRIPT_NAME= "' . $_SERVER['SCRIPT_NAME'] . '";
var bid =' . we_base_request::_(we_base_request::INT, 'bid', 0) . ';
var cid =' . we_base_request::_(we_base_request::INT, 'cid', 0) . ';

' . (isset($alertMessage) ?
					we_message_reporting::getShowMessageCall($alertMessage, $alertType) : '')
			) .
			we_html_element::jsScript(JS_DIR . 'we_modules/shop/we_shop_view2.js');
			?>

			</head>
			<body class="weEditorBody" onload="hot = 1" onunload="doUnload()">

				<?php
				$parts = array(array(
						'html' => $orderDataTable,
					),
					array(
						'html' => $orderTable,
					)
				);
				if($customCartFieldsTable){

					$parts[] = array(
						'html' => $customCartFieldsTable,
					);
				}

				echo we_html_multiIconBox::getHTML('', $parts, 30);

				//
				// "Html output for order with articles"
				// ********************************************************************************
			} else { // This order has no more entries
				echo we_html_element::jsElement('
				top.content.editor.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&deletethisorder=1&bid=' . $_REQUEST["bid"] . '";
				top.content.treeData.deleteEntry(' . $_REQUEST['bid'] . ');
			') . '
		</head>
		<body bgcolor="#ffffff">';
			}

			$js = '
// init the used calendars

function CalendarChanged(calObject) {
	// field:
	_field = calObject.params.inputField;
	document.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&bid=' . $_REQUEST['bid'] . '&" + _field.name + "=" + _field.value;
}
';

			foreach(we_shop_statusMails::$StatusFields as $cur){
				if(!$weShopStatusMails->FieldsHidden[$cur]){
					$js.='		Calendar.setup({
				"inputField" : "hidden_Calendar_' . $cur . '",
				"displayArea" : "div_Calendar_' . $cur . '",
				"button" : "date_pickerbutton_Calendar_' . $cur . '",
				"ifFormat" : "' . $da . '",
				"daFormat" : "' . $da . '",
				"onUpdate" : CalendarChanged
				});';
				}
			}
			echo we_html_element::jsElement($js);
			?>
		</body>
		</html>
		<?php
	}

	function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
			case 'add_article':
				if(we_base_request::_(we_base_request::FLOAT, 'anzahl') > 0){

					// add complete article / object here - inclusive request fields
					$_strSerialOrder = $this->getFieldFromOrder(we_base_request::_(we_base_request::INT, 'bid'), 'strSerialOrder');

					$tmp = explode('_', $_REQUEST['add_article']);
					$isObj = ($tmp[1] == we_shop_shop::OBJECT);

					$id = intval($tmp[0]);

					// check for variant or customfields
					$customFieldsTmp = array();
					if(strlen($_REQUEST['we_customField'])){

						$fields = explode(';', trim($_REQUEST['we_customField']));

						if(is_array($fields)){
							foreach($fields as $field){
								$fieldData = explode('=', $field);
								if(is_array($fieldData) && count($fieldData) == 2){
									$customFieldsTmp[trim($fieldData[0])] = trim($fieldData[1]);
								}
							}
						}
					}

					$variant = strip_tags($_REQUEST[we_base_constants::WE_VARIANT_REQUEST]);
					$serialDoc = we_shop_Basket::getserial($id, ($isObj ? we_shop_shop::OBJECT : we_shop_shop::DOCUMENT), $variant, $customFieldsTmp);

					// shop vats must be calculated
					$orderArray = we_unserialize($_strSerialOrder);
					$standardVat = we_shop_vats::getStandardShopVat();

					if(we_shop_category::isCategoryMode()){
						$wedocCategory = ((isset($serialDoc['we_wedoc_Category'])) ? $serialDoc['we_wedoc_Category'] : $serialDoc['wedoc_Category']);
						$stateField = we_shop_vatRule::getStateField();
						$billingCountry = !empty($orderArray[WE_SHOP_CART_CUSTOMER_FIELD][$stateField]) ?
							$orderArray[WE_SHOP_CART_CUSTOMER_FIELD][$stateField] : we_shop_category::getDefaultCountry();

						$shopVat = we_shop_category::getShopVatByIdAndCountry((!empty($serialDoc[WE_SHOP_CATEGORY_FIELD_NAME]) ? $serialDoc[WE_SHOP_CATEGORY_FIELD_NAME] : 0), $wedocCategory, $billingCountry);
					} elseif(isset($serialDoc[WE_SHOP_VAT_FIELD_NAME])){
						$shopVat = we_shop_vats::getShopVATById($serialDoc[WE_SHOP_VAT_FIELD_NAME]);
					}

					if(!empty($shopVat)){
						$serialDoc[WE_SHOP_VAT_FIELD_NAME] = $shopVat->vat;
					} elseif($standardVat){
						$serialDoc[WE_SHOP_VAT_FIELD_NAME] = $standardVat->vat;
					}

					//need pricefield:
					$pricename = (isset($orderArray[WE_SHOP_PRICENAME]) ? $orderArray[WE_SHOP_PRICENAME] : 'shopprice');
					// now insert article to order:
					$row = getHash('SELECT IntOrderID, IntCustomerID, DateOrder, DateShipping, Datepayment, IntPayment_Type FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . we_base_request::_(we_base_request::INT, 'bid'), $this->db);
					$this->db->query('INSERT INTO ' . SHOP_TABLE . ' SET ' .
						we_database_base::arraySetter((array(
							'IntArticleID' => $id,
							'IntQuantity' => we_base_request::_(we_base_request::FLOAT, 'anzahl', 0),
							'Price' => we_base_util::std_numberformat(self::getFieldFromShoparticle($serialDoc, $pricename)),
							'IntOrderID' => $row['IntOrderID'],
							'IntCustomerID' => $row['IntCustomerID'],
							'DateOrder' => $row['DateOrder'],
							'DateShipping' => $row['DateShipping'],
							'Datepayment' => $row['Datepayment'],
							'IntPayment_Type' => $row['IntPayment_Type'],
							'strSerial' => we_serialize($serialDoc, SERIALIZE_JSON),
							'strSerialOrder' => $_strSerialOrder
					))));
				} else {
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true));
				}

				break;

			case 'add_new_article':
				$shopArticles = array();

				$saveBut = '';
				$cancelBut = we_html_button::create_button(we_html_button::CANCEL, 'javascript:window.close();');
				$searchBut = we_html_button::create_button(we_html_button::SEARCH, 'javascript:searchArticles();');

				// first get all shop documents
				$this->db->query('SELECT c.dat AS shopTitle, l.DID AS documentId FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON l.CID=c.ID JOIN ' . FILE_TABLE . ' f ON f.ID=l.DID WHERE l.nHash=x\'' . md5(WE_SHOP_TITLE_FIELD_NAME) . '\' AND l.DocumentTable!="tblTemplates" ' .
					(we_base_request::_(we_base_request::BOOL, 'searchArticle') ?
						' AND c.Dat LIKE "%' . $this->db->escape($_REQUEST['searchArticle']) . '%"' :
						'')
				);

				while($this->db->next_record()){
					$shopArticles[$this->db->f('documentId') . '_' . we_shop_shop::DOCUMENT] = $this->db->f('shopTitle') . ' [' . g_l('modules_shop', '[isDoc]') . ': ' . $this->db->f('documentId') . ']';
				}

				if(defined('OBJECT_TABLE')){
					// now get all shop objects
					foreach($this->classIds as $_classId){
						$_classId = intval($_classId);
						$this->db->query('SELECT o.input_' . WE_SHOP_TITLE_FIELD_NAME . ' AS shopTitle, o.OF_ID as objectId FROM ' . OBJECT_X_TABLE . $_classId . ' o JOIN ' . OBJECT_FILES_TABLE . ' of ON o.OF_ID=of.ID ' .
							(we_base_request::_(we_base_request::BOOL, 'searchArticle') ?
								' WHERE ' . OBJECT_X_TABLE . $_classId . '.input_' . WE_SHOP_TITLE_FIELD_NAME . '  LIKE "%' . $this->db->escape($searchArticle) . '%"' :
								'')
						);

						while($this->db->next_record()){
							$shopArticles[$this->db->f('objectId') . '_' . we_shop_shop::OBJECT] = $this->db->f('shopTitle') . ' [' . g_l('modules_shop', '[isObj]') . ': ' . $this->db->f('objectId') . ']';
						}
					}
				}

				// <<< determine which articles should be shown ...

				asort($shopArticles);
				$MAX_PER_PAGE = 10;
				$AMOUNT_ARTICLES = count($shopArticles);

				$page = we_base_request::_(we_base_request::INT, 'page', 0);

				$shopArticlesParts = array_chunk($shopArticles, $MAX_PER_PAGE, true);

				$start_entry = $page * $MAX_PER_PAGE + 1;
				$end_entry = (($page * $MAX_PER_PAGE + $MAX_PER_PAGE < $AMOUNT_ARTICLES) ? ($page * $MAX_PER_PAGE + $MAX_PER_PAGE) : $AMOUNT_ARTICLES );

				$backBut = ($start_entry - $MAX_PER_PAGE > 0 ?
						we_html_button::create_button(we_html_button::BACK, 'javascript:switchEntriesPage(' . ($page - 1) . ');') :
						we_html_button::create_button(we_html_button::BACK, '#', true, 100, 22, '', '', true));

				$nextBut = (($end_entry) < $AMOUNT_ARTICLES ?
						we_html_button::create_button(we_html_button::NEXT, 'javascript:switchEntriesPage(' . ($page + 1) . ');') :
						we_html_button::create_button(we_html_button::NEXT, '#', true, 100, 22, '', '', true));


				$shopArticlesSelect = $shopArticlesParts[$page];
				asort($shopArticlesSelect);

				// determine which articles should be shown >>>


				echo we_html_element::jsElement('
		self.focus();

		function selectArticle(articleInfo) {
			document.location = "?we_cmd[0]=' . $_REQUEST['we_cmd'][0] . '&bid=' . $_REQUEST['bid'] . '&page=' . $page . (isset($_REQUEST['searchArticle']) ? '&searchArticle=' . $_REQUEST['searchArticle'] : '') . '&add_article=" + articleInfo;
		}

		function switchEntriesPage(pageNum) {
			document.location = "?we_cmd[0]=' . $_REQUEST['we_cmd'][0] . '&bid=' . $_REQUEST['bid'] . (isset($_REQUEST['searchArticle']) ? '&searchArticle=' . $_REQUEST['searchArticle'] : '') . '&page=" + pageNum;
		}

		function searchArticles() {
			field = document.getElementById("searchArticle");
			document.location = "?we_cmd[0]=' . $_REQUEST['we_cmd'][0] . '&bid=' . $_REQUEST['bid'] . '&searchArticle=" + field.value;
		}') . '
		</head>
		<body class="weDialogBody">';

				$parts = array(
					($AMOUNT_ARTICLES > 0 ?
						array(
						'headline' => g_l('modules_shop', '[Artikel]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => '
		<form name="we_intern_form">' . we_html_element::htmlHiddens(array(
							'bid' => $_REQUEST['bid'],
							'we_cmd[]' => 'add_new_article'
						)) . '
			<table class="default">
			<tr>
			<td>' . we_class::htmlSelect("add_article", $shopArticlesSelect, 15, we_base_request::_(we_base_request::RAW, 'add_article', ''), false, array("onchange" => "selectArticle(this.options[this.selectedIndex].value)"), 'value', '380') . '</td>
			<td width="10"></td>
			<td style="vertical-align:top">' . $backBut . '<div style="margin:5px 0"></div>' . $nextBut . '</td>
			</tr>
			<tr>
				<td class="small">' . sprintf(g_l('modules_shop', '[add_article][entry_x_to_y_from_z]'), $start_entry, $end_entry, $AMOUNT_ARTICLES) . '</td>
			</tr>
			</table>',
						'noline' => 1
						) :
						array(
						'headline' => g_l('modules_shop', '[Artikel]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => g_l('modules_shop', '[add_article][empty_articles]')
						)
					)
				);

				if($AMOUNT_ARTICLES > 0 || isset($_REQUEST['searchArticle'])){
					$parts[] = array(
						'headline' => g_l('global', '[search]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => '
			<table class="default">
				<tr><td>' . we_class::htmlTextInput('searchArticle', 24, we_base_request::_(we_base_request::RAW, 'searchArticle', ''), '', 'id="searchArticle"', 'text', 380) . '</td>
					<td></td>
					<td>' . $searchBut . '</td>
				</tr>
			</table>
		</form>'
					);
				}

				if(isset($_REQUEST['add_article']) && $_REQUEST['add_article'] != '0'){
					$saveBut = we_html_button::create_button(we_html_button::SAVE, "javascript:document.we_form.submit();window.close();");
					list($id, $type) = explode('_', $_REQUEST['add_article']);

					$variantOptions = array(
						'-' => '-'
					);

					$model = ($type == we_shop_shop::OBJECT ? new we_objectFile() : new we_webEditionDocument());

					$model->initByID($id);
					$variantData = we_base_variants::getVariantData($model, '-');

					if(count($variantData) > 1){
						foreach($variantData as $cur){
							list($key) = each($cur);
							if($key != '-'){
								$variantOptions[$key] = $key;
							}
						}
					}

					$parts[] = array(
						'headline' => g_l('modules_shop', '[Artikel]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => '
							<form name="we_form" target="edbody">' .
						we_html_element::htmlHiddens(array(
							'bid' => $_REQUEST['bid'],
							'we_cmd[]' => 'add_article',
							'add_article' => $_REQUEST['add_article']
						)) .
						'<b>' . $model->elements[WE_SHOP_TITLE_FIELD_NAME]['dat'] . '</b>',
						'noline' => 1
					);

					$parts[] = array(
						'headline' => g_l('modules_shop', '[anzahl]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => we_class::htmlTextInput('anzahl', 24, '', '', 'min="1"', 'number', 380),
						'noline' => 1
					);

					$parts[] = array(
						'headline' => g_l('modules_shop', '[variant]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => we_class::htmlSelect(we_base_constants::WE_VARIANT_REQUEST, $variantOptions, 1, '', false, array(), 'value', 380),
						'noline' => 1
					);

					$parts[] = array(
						'headline' => g_l('modules_shop', '[customField]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => we_class::htmlTextInput('we_customField', 24, '', '', '', 'text', 380) .
						'<br /><span class="small">Eingabe in der Form: <i>name1=wert1;name2=wert2</i></span></form>',
						'noline' => 1
					);
				}

				echo we_html_multiIconBox::getHTML('', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[add_article][title]')) .
				'</form>
		</body>
		</html>';
				exit;
				break;

			case 'payVat':
				$serialOrder = we_unserialize($this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder'));
				$serialOrder[WE_SHOP_CALC_VAT] = we_base_request::_(we_base_request::INT, 'pay', 0);

				// update all orders with this orderId
				if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', we_serialize($serialOrder))){
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_calculateVat_success]');
					$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
				} else {
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_calculateVat_error]');
					$alertType = we_message_reporting::WE_MESSAGE_ERROR;
				}
				break;

			case 'delete_shop_cart_custom_field':
				if(!empty($_REQUEST['cartfieldname'])){
					$serialOrder = we_unserialize($this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder'));
					unset($serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']]);

					// update all orders with this orderId
					if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', we_serialize($serialOrder))){
						$alertMessage = sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_success]'), $_REQUEST['cartfieldname']);
						$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
					} else {
						$alertMessage = sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_error]'), $_REQUEST['cartfieldname']);
						$alertType = we_message_reporting::WE_MESSAGE_ERROR;
					}
				}
				break;

			case 'edit_shop_cart_custom_field':

				echo we_html_element::jsElement('function we_submit() {
				elem = document.getElementById("cartfieldname");

				if (elem && elem.value) {
					document.we_form.submit();
				} else {
					' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[field_empty_js_alert]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				}

			}') . '
					</head>
		<body class="weDialogBody">
		<form name="we_form">
		<input type="hidden" name="bid" value="' . $_REQUEST['bid'] . '" />
		<input type="hidden" name="we_cmd[0]" value="save_shop_cart_custom_field" />';
				$saveBut = we_html_button::create_button(we_html_button::SAVE, 'javascript:we_submit();');
				$cancelBut = we_html_button::create_button(we_html_button::CANCEL, 'javascript:self.close();');


				$val = '';

				if(!empty($_REQUEST['cartfieldname'])){
					$serialOrder = we_unserialize($this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder'));
					$val = $serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] ? : '';
					$fieldHtml = $_REQUEST['cartfieldname'] . '<input type="hidden" name="cartfieldname" id="cartfieldname" value="' . $_REQUEST['cartfieldname'] . '" />';
				} else {
					$fieldHtml = we_html_tools::htmlTextInput('cartfieldname', 24, '', '', 'id="cartfieldname"');
				}

				// make input field, for name or textfield
				$parts = array(
					array(
						'headline' => g_l('modules_shop', '[field_name]'),
						'html' => $fieldHtml,
						'space' => we_html_multiIconBox::SPACE_MED,
						'noline' => 1
					),
					array(
						'headline' => g_l('modules_shop', '[field_value]'),
						'html' => '<textarea name="cartfieldvalue" style="width: 350; height: 150">' . $val . '</textarea>',
						'space' => we_html_multiIconBox::SPACE_MED
					)
				);

				echo we_html_multiIconBox::getHTML('', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[add_shop_field]')) .
				'</form></body></html>';
				exit;

			case 'save_shop_cart_custom_field':
				if(!empty($_REQUEST['cartfieldname'])){
					$serialOrder = we_unserialize($this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder'));
					$serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] = htmlentities($_REQUEST['cartfieldvalue']);
					$serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] = $_REQUEST['cartfieldvalue'];

					// update all orders with this orderId
					if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', we_serialize($serialOrder))){
						$jsCmd = 'top.opener.top.content.doClick(' . $_REQUEST['bid'] . ',"shop","' . SHOP_TABLE . '");top.opener.' .
							we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_success]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_NOTICE);
					} else {
						$jsCmd = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_error]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_ERROR);
					}
				} else {
					$jsCmd = 'top.opener.' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[field_empty_js_alert]'), we_message_reporting::WE_MESSAGE_ERROR);
				}

				echo we_html_element::jsElement($jsCmd . 'window.close();') .
				'</head><body></body></html>';
				exit;

			case 'edit_shipping_cost':
				$shopVats = we_shop_vats::getAllShopVATs();
				$shippingVats = array();

				foreach($shopVats as $k => $shopVat){
					$shippingVats[$shopVat->vat] = $shopVat->vat . ' - ' . $shopVat->getNaturalizedText() . ' (' . $shopVat->territory . ')';
				}

				$saveBut = we_html_button::create_button(we_html_button::SAVE, 'javascript:document.we_form.submit();self.close();');
				$cancelBut = we_html_button::create_button(we_html_button::CANCEL, 'javascript:self.close();');

				$serialOrder = we_unserialize($this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder'));

				if($serialOrder){
					$shippingCost = $serialOrder[WE_SHOP_SHIPPING]['costs'];
					$shippingIsNet = $serialOrder[WE_SHOP_SHIPPING]['isNet'];
					$shippingVat = $serialOrder[WE_SHOP_SHIPPING]['vatRate'];
				} else {
					$shippingCost = '0';
					$shippingIsNet = '1';
					$shippingVat = '19';
				}

				$parts = array(
					array(
						'headline' => g_l('modules_shop', '[edit_order][shipping_costs]'),
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_class::htmlTextInput('weShipping_costs', 24, $shippingCost),
						'noline' => 1
					),
					array(
						'headline' => g_l('modules_shop', '[edit_shipping_cost][isNet]'),
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_class::htmlSelect('weShipping_isNet', array(1 => g_l('global', '[yes]'), 0 => g_l('global', '[no]')), 1, $shippingIsNet),
						'noline' => 1
					),
					array(
						'headline' => g_l('modules_shop', '[edit_shipping_cost][vatRate]'),
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_html_tools::htmlInputChoiceField('weShipping_vatRate', $shippingVat, $shippingVats, array(), '', true),
						'noline' => 1
					)
				);


				echo '</head>
						<body class="weDialogBody">
						<form name="we_form" target="edbody">' .
				we_html_element::htmlHiddens(array(
					'bid' => $_REQUEST['bid'],
					"we_cmd[]" => 'save_shipping_cost'
				)) .
				we_html_multiIconBox::getHTML('', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[edit_shipping_cost][title]')) .
				'</form></body></html>';
				exit;
				break;

			case 'save_shipping_cost':
				$serialOrder = we_unserialize($this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder'));

				if($serialOrder){
					$weShippingCosts = str_replace(',', '.', $_REQUEST['weShipping_costs']);
					$serialOrder[WE_SHOP_SHIPPING]['costs'] = $weShippingCosts;
					$serialOrder[WE_SHOP_SHIPPING]['isNet'] = $_REQUEST['weShipping_isNet'];
					$serialOrder[WE_SHOP_SHIPPING]['vatRate'] = $_REQUEST['weShipping_vatRate'];

					// update all orders with this orderId
					if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', we_serialize($serialOrder))){
						$alertMessage = g_l('modules_shop', '[edit_order][js_saved_shipping_success]');
						$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
					} else {
						$alertMessage = g_l('modules_shop', '[edit_order][js_saved_shipping_error]');
						$alertType = we_message_reporting::WE_MESSAGE_ERROR;
					}
				}

				break;

			case 'edit_order_customer'; // edit data of the saved customer.
				$saveBut = we_html_button::create_button(we_html_button::SAVE, 'javascript:document.we_form.submit();self.close();');
				$cancelBut = we_html_button::create_button(we_html_button::CANCEL, 'javascript:self.close();');
				// 1st get the customer for this order
				$_customer = $this->getOrderCustomerData($_REQUEST['bid']);
				ksort($_customer);

				$dontEdit = explode(',', we_shop_shop::ignoredEditFields);

				$parts = array(
					array(
						'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[preferences][explanation_customer_odercustomer]'), we_html_tools::TYPE_INFO, 470),
					),
					array(
						'headline' => g_l('modules_customer', '[Forname]') . ': ',
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_class::htmlTextInput('weCustomerOrder[Forename]', 44, $_customer['Forename']),
						'noline' => 1
					),
					array(
						'headline' => g_l('modules_customer', '[Surname]') . ': ',
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_class::htmlTextInput('weCustomerOrder[Surname]', 44, $_customer['Surname']),
						'noline' => 1
					)
				);
				$editFields = array('Forename', 'Surname');

				foreach($_customer as $k => $v){
					if(!in_array($k, $dontEdit) && !is_numeric($k)){
						if(isset($this->CLFields['stateField']) && !empty($this->CLFields['stateFieldIsISO']) && $k == $this->CLFields['stateField']){
							$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
							$langcode = array_search($lang[0], getWELangs());
							$countrycode = array_search($langcode, getWECountries());
							$countryselect = new we_html_select(array('name' => 'weCustomerOrder[' . $k . ']', 'style' => 'width:280px;', 'class' => 'wetextinput'));

							$topCountries = array_flip(explode(',', WE_COUNTRIES_TOP));
							foreach($topCountries as $countrykey => &$countryvalue){
								$countryvalue = we_base_country::getTranslation($countrykey, we_base_country::TERRITORY, $langcode);
							}
							unset($countryvalue);
							$shownCountries = array_flip(explode(',', WE_COUNTRIES_SHOWN));
							foreach($shownCountries as $countrykey => &$countryvalue){
								$countryvalue = we_base_country::getTranslation($countrykey, we_base_country::TERRITORY, $langcode);
							}
							unset($countryvalue);
							$oldLocale = setlocale(LC_ALL, NULL);
							setlocale(LC_ALL, $langcode . '_' . $countrycode . '.UTF-8');
							asort($topCountries, SORT_LOCALE_STRING);
							asort($shownCountries, SORT_LOCALE_STRING);
							setlocale(LC_ALL, $oldLocale);

							if(WE_COUNTRIES_DEFAULT != ''){
								$countryselect->addOption('--', CheckAndConvertISObackend(WE_COUNTRIES_DEFAULT));
							}
							foreach($topCountries as $countrykey => &$countryvalue){
								$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
							}
							unset($countryvalue);
							$countryselect->addOption('-', '----', array('disabled' => 'disabled'));
							foreach($shownCountries as $countrykey => &$countryvalue){
								$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
							}
							unset($countryvalue);
							$countryselect->selectOption($v);

							$parts[] = array(
								'headline' => $k . ': ',
								'space' => we_html_multiIconBox::SPACE_MED2,
								'html' => $countryselect->getHtml(),
								'noline' => 1
							);
						} elseif((isset($this->CLFields['languageField']) && !empty($this->CLFields['languageFieldIsISO']) && $k == $this->CLFields['languageField'])){
							$frontendL = $GLOBALS['weFrontendLanguages'];
							foreach($frontendL as &$lcvalue){
								list($lcvalue) = explode('_', $lcvalue);
							}
							unset($countryvalue);
							$languageselect = new we_html_select(array('name' => 'weCustomerOrder[' . $k . ']', 'style' => 'width:280px;', 'class' => 'wetextinput'));
							foreach(g_l('languages', '') as $languagekey => $languagevalue){
								if(in_array($languagekey, $frontendL)){
									$languageselect->addOption($languagekey, $languagevalue);
								}
							}
							$languageselect->selectOption($v);

							$parts[] = array(
								'headline' => $k . ': ',
								'space' => we_html_multiIconBox::SPACE_MED2,
								'html' => $languageselect->getHtml(),
								'noline' => 1
							);
						} else {
							$parts[] = array(
								'headline' => $k . ': ',
								'space' => we_html_multiIconBox::SPACE_MED2,
								'html' => we_class::htmlTextInput('weCustomerOrder[' . $k . ']', 44, $v),
								'noline' => 1
							);
						}
						$editFields[] = $k;
					}
				}

				echo '</head>
						<body class="weDialogBody">
						<form name="we_form" target="edbody">' .
				we_html_element::htmlHiddens(array(
					'bid' => $_REQUEST['bid'],
					'we_cmd[]' => 'save_order_customer'
				)) .
				we_html_multiIconBox::getHTML('', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[preferences][customerdata]')) .
				'</form>
						</body>
						</html>';
				exit;

			case 'save_order_customer':
				// just get this order and save this userdata in there.
				$_orderData = we_unserialize($this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder'));
				$_orderData[WE_SHOP_CART_CUSTOMER_FIELD] = $_REQUEST['weCustomerOrder'];

				if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', we_serialize($_orderData))){
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_customer_success]');
					$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
				} else {
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_customer_error]');
					$alertType = we_message_reporting::WE_MESSAGE_ERROR;
				}
				break;
		}
	}

	function processVariables(){
		if(isset($_SESSION['weS']['raw_session'])){
			$this->raw = we_unserialize($_SESSION['weS']['raw_session']);
		}

		if(is_array($this->raw->persistent_slots)){
			foreach($this->raw->persistent_slots as $key => $val){
				$varname = $val;
				if(isset($_REQUEST[$varname])){
					$this->raw->{$val} = $_REQUEST[$varname];
				}
			}
		}

		if(isset($_REQUEST['page'])){
			if(isset($_REQUEST['page'])){
				$this->page = $_REQUEST['page'];
			}
		}
	}

	//some functions from edit_shop_properties

	private static function getFieldFromShoparticle(array $array, $name, $length = 0){
		$val = ( isset($array['we_' . $name]) ? $array['we_' . $name] : (isset($array[$name]) ? $array[$name] : '' ) );
		return $length && strlen($val) > $length ?
			'<span ' . ($length ? 'class="cutText" title="' . $val . '" style="max-width: ' . $length . 'em;"' : '') . '>' . $val . '</span>' :
			$val;
	}

	private function getOrderCustomerData($orderId, array $strFelder = array()){
		$hash = getHash('SELECT IntCustomerID,strSerialOrder FROM ' . SHOP_TABLE . '	WHERE IntOrderID=' . intval($orderId) . ' LIMIT 1', $this->db);
		$customerId = $hash['IntCustomerID'];
		$tmp = $hash['strSerialOrder'];
		// get Customer
		$customerDb = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($customerId), $this->db, MYSQL_ASSOC);

		$orderData = we_unserialize($tmp);
		$customerOrder = (isset($orderData[WE_SHOP_CART_CUSTOMER_FIELD]) ? $orderData[WE_SHOP_CART_CUSTOMER_FIELD] : array());

		if(empty($strFelder)){ //used only if edit customer data is selected!!!
			//only data from order - return all fields, fill in unknown fields from customer-db
			// default values are fields saved with order
			return array_merge($customerDb, $customerOrder);
		}


		$customer = $customerDb;
		foreach($strFelder['orderCustomerFields'] as $field){
			if(isset($customerOrder[$field])){
				$customer[$field] = $customerOrder[$field];
			}
		}

		return $customer;
	}

	private function getFieldFromOrder($bid, $field){
		return f('SELECT ' . $this->db->escape($field) . ' FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($bid) . ' LIMIT 1', '', $this->db);
	}

	private function updateFieldFromOrder($orderId, $fieldname, $value){
		return (bool) $this->db->query('UPDATE ' . SHOP_TABLE . ' SET ' . $this->db->escape($fieldname) . '="' . $this->db->escape($value) . '" WHERE IntOrderID=' . intval($orderId));
	}

	public function getHomeScreen(){

		$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"'));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}
		$fe = explode(',', $feldnamen[3]);
		$classid = $fe[0];

		$resultO = array_shift($fe);

		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1');


		$content = we_html_button::create_button('pref_shop', "javascript:top.we_cmd('pref_shop');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")) . '<br/>' .
			we_html_button::create_button('payment_val', "javascript:top.we_cmd('payment_val');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")) . '<br/>';
		if(($resultD) && $resultO){ //docs and objects
			$content.= we_html_button::create_button('quick_rev', "javascript:top.content.editor.location='" . $this->frameset . "&pnt=editor&top=1&typ=document '", true) . '<br/>';
		} elseif((!$resultD) && $resultO){ // no docs but objects
			$content.= we_html_button::create_button('quick_rev', "javascript:top.content.editor.location='" . $this->frameset . "&pnt=editor&top=1&typ=object&ViewClass=$classid '", true) . '<br/>';
		} elseif(($resultD) && !$resultO){ // docs but no objects
			$content.= we_html_button::create_button('quick_rev', "javascript:top.content.editor.location='" . $this->frameset . "&pnt=editor&top=1&typ=document '", true) . '<br/>';
		}

		return parent::getActualHomeScreen('shop', "shop.gif", $content);
	}

}
