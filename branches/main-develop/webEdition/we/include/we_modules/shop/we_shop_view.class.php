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
	private $CLFields = []; //
	private $classIds = [];

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

		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'shop/we_shop_view.js', '', ['id' => 'loadVarShop_view', 'data-viewData' => setDynamicVar([
					'isDocument' => intval($resultD),
					'isObject' => ((!empty($resultO))),
					'classID' => intval($classid),
					'title' => $title,
		])]);
	}

	function getJSProperty(){
		return parent::getJSProperty() .
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'shop/we_shop_property.js');
	}

	function getProperties(){
		we_html_tools::protect();

		//$weShopVatRule = weShopVatRule::getShopVatRule();

		$weShopStatusMails = we_shop_statusMails::getShopStatusMails();
		$hiddenStatusFields = array_keys(array_filter($weShopStatusMails->FieldsHidden, function ($v){
				return $v == 1;
			}));

		// Get Country and Langfield Data
		$this->CLFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_CountryLanguage"', '', $this->db), [
			'stateField' => '-',
			'stateFieldIsISO' => 0,
			'languageField' => '-',
			'languageFieldIsISO' => 0
		]);

		// config
		$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"', '', $this->db));
		$waehr = '&nbsp;' . oldHtmlspecialchars($feldnamen[0]);
		//$numberformat = $feldnamen[2];
		$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
		$this->classIds = makeArrayFromCSV($classid);
		$mwst = floatval($feldnamen[1] ?: 0);

		$da = '%d.%m.%Y';
		$dateform = '00.00.0000';

		$ret = $this->processCommands(); //imi

		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
		if(we_base_request::_(we_base_request::BOOL, 'deletethisorder')){
			$this->db->query('DELETE FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $bid);
			$this->db->query('DELETE FROM ' . SHOP_ORDER_ITEM_TABLE . ' WHERE orderID=' . $bid);
			$this->db->query('DELETE FROM ' . SHOP_ORDER_DATES_TABLE . ' WHERE ID=' . $bid);
			return we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement('top.content.treeData.deleteEntry(' . $bid . ')'), we_html_element::htmlBody(['class' => "weEditorBody",
						'onunload' => "doUnload()"], '<table style="width:300px">
			  <tr>
				<td colspan="2" class="defaultfont">' . we_html_tools::htmlDialogLayout('<span class="defaultfont">' . g_l('modules_shop', '[geloscht]') . '</span>', g_l('modules_shop', '[loscht]')) . '</td>
			  </tr>
			  </table>'));
		}

		if(($id = we_base_request::_(we_base_request::INT, 'deleteaarticle'))){
			$this->db->query('DELETE FROM ' . SHOP_ORDER_ITEM_TABLE . ' WHERE orderID=' . $bid . ' AND orderDocID=' . $id);
			if(f('SELECT COUNT(1) FROM ' . SHOP_ORDER_ITEM_TABLE . ' WHERE orderID=' . $bid, '', $this->db) < 1){
				//last item deleted, delete the order itself
				$this->db->query('DELETE FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $bid);
				$this->db->query('DELETE FROM ' . SHOP_ORDER_DATES_TABLE . ' WHERE ID=' . $bid);

				echo we_html_element::jsElement('
				top.content.editor.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&deletethisorder=1&bid=' . $_REQUEST["bid"] . '";
				top.content.treeData.deleteEntry(' . $_REQUEST['bid'] . ');
			') . '
		</head>
		<body bgcolor="#ffffff"></body></html>';
				return;
			}
		}
		echo we_html_tools::getHtmlTop() . $ret;

		// Get Customer data
		$_REQUEST['cid'] = f('SELECT customerID FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $bid, '', $this->db);

		if(($fields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="edit_shop_properties"', '', $this->db)))){
			// we have an array with following syntax:
			// array ( 'customerFields' => array('fieldname ...',...)
			//         'orderCustomerFields' => array('fieldname', ...) )
		} else {
			//unsupported
			t_e('unsupported Shop-Settings found. Please open settings and readjust the settings of the shop module.');
			$fields = [];
		}

		$customer = $this->getOrderCustomerData($bid, $fields ? $fields['orderCustomerFields'] : []);

		if(isset($_REQUEST['SendMail'])){
			$weShopStatusMails->sendEMail($_REQUEST['SendMail'], $_REQUEST['bid'], $customer);
		}
		foreach(we_shop_statusMails::$StatusFields as $field){
			if(isset($_REQUEST[$field])){
				list($day, $month, $year) = explode('.', $_REQUEST[$field]);
				$DateOrder = $year . '-' . $month . '-' . $day;
				if(in_array($field, we_shop_statusMails::$BaseDateFields)){
					$this->db->query('UPDATE ' . SHOP_ORDER_TABLE . ' SET ' . $field . '="' . $this->db->escape($DateOrder) . '" WHERE ID=' . $bid);
				} else {
					$this->db->query('REPACE INTO ' . SHOP_ORDER_DATES_TABLE . ' SET ID=' . $bid . ',type="' . $field . '",date="' . $this->db->escape($DateOrder));
				}
				$weShopStatusMails->checkAutoMailAndSend(substr($field, 4), $bid, $customer);
			}
		}

		if(($article = we_base_request::_(we_base_request::INT, 'article'))){
			if(($preis = we_base_request::_(we_base_request::FLOAT, 'preis')) !== false){
				$this->db->query('UPDATE ' . SHOP_ORDER_ITEM_TABLE . ' SET Price=' . abs($preis) . ' WHERE orderID=' . $bid . ' AND orderDocID=' . $article);
			} else if(($anz = we_base_request::_(we_base_request::FLOAT, 'anzahl')) !== false){
				$this->db->query('UPDATE ' . SHOP_ORDER_ITEM_TABLE . ' SET quantity=' . abs($anz) . ' WHERE orderID=' . $bid . ' AND orderDocID=' . $article);
			} else if(($vat = we_base_request::_(we_base_request::FLOAT, 'vat')) !== false){
				$this->db->query('UPDATE ' . SHOP_ORDER_ITEM_TABLE . ' SET Vat="' . abs($vat) . '" WHERE orderID=' . $bid . ' AND orderDocID=' . $article);
			}
		}

		// order has still articles - get them all
		// ********************************************************************************
		// first get all information about orders, we need this for the rest of the page
		//
		$showBaseFields = array_diff(we_shop_statusMails::$BaseDateFields, $hiddenStatusFields);
		$showDateFields = array_diff(we_shop_statusMails::$StatusFields, $hiddenStatusFields);
		$showMailFields = array_diff(we_shop_statusMails::$MailFields, $hiddenStatusFields);
		$showAdvanced = array_merge(array_diff($showDateFields, $showBaseFields), $showMailFields);

		$format = [];
		foreach($showDateFields as $field){
			$format[] = 'DATE_FORMAT(' . $field . ',"' . $da . '") AS ' . $field;
		}

		if($showAdvanced){
			$advanced = $GLOBALS['DB_WE']->getAllFirstq('SELECT odt.type,DATE_FORMAT(odt.date,"' . $da . '") FROM ' . SHOP_ORDER_TABLE . ' o JOIN ' . SHOP_ORDER_DATES_TABLE . ' odt ON odt.ID=o.ID WHERE odt.type IN ("' . implode('","', $showAdvanced) . '") AND o.ID=' . $bid, false);
		}

		$orderData = getHash('SELECT
customerID,
customOrderNo,
pricesNet,
calcVat,
shippingCost,
shippingNet,
shippingVat,
customFields' .
			($format ? ',' . implode(',', $format) : '') .
			' FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $bid);

		if(empty($orderData)){
			//FIXME: JS is broken
			echo we_html_element::jsElement('top.opener.document.getElementById("iconbar").location.reload();') . '
</head>
<body class="weEditorBody" onunload="doUnload()">
<table style="width:300px">
	<tr>
		<td colspan="2" class="defaultfont">' . we_html_tools::htmlDialogLayout("<span class='defaultfont'>" . g_l('modules_shop', '[orderDoesNotExist]') . '</span>', g_l('modules_shop', '[loscht]')) . '</td>
	</tr>
</table></body></html>';
			exit;
		}

		// get all needed information for order-data
		$_REQUEST['cid'] = $orderData['customerID'];
		//have all dates in $advanced
		foreach($showBaseFields as $field){
			$advanced[$field] = $orderData[$field];
		}
		// prices are net?
		$pricesAreNet = $orderData['pricesNet'];
		// must calculate vat?
		$calcVat = $orderData['calcVat'];
		$customCartFields = we_unserialize($orderData['customFields']);
		// ********************************************************************************
		// Building table with customer and order data fields - start
		//

		$orderDataTable = '
		<table style="width:99%" class="default defaultfont">';

		foreach($showDateFields as $field){
			$EMailhandler = $weShopStatusMails->getEMailHandlerCode(substr($field, 4), $advanced[$field]);
			$orderDataTable .= '
			<tr height="25">
				<td class="defaultfont" style="width:86px;vertical-align:top" height="25">' . ($field === 'DateOrder' ? g_l('modules_shop', '[bestellnr]') : '') . '</td>
				<td class="defaultfont" style="vertical-align:top;width:40px;height:25px"><b>' . ($field === 'DateOrder' ? ($orderData['customOrderNo'] ?: $bid) : '') . '</b></td>
				<td style="width:20px;height:25px;"></td>
				<td style="width:98px;height:25px;" class="defaultfont">' . $weShopStatusMails->FieldsText[$field] . '</td>
				<td style="height:25px;width:14px"></td>
				<td class="defaultfont" style="width:14px;text-align:right;height:25px;">
					<div id="div_Calendar_' . $field . '">' . ((empty($advanced[$field]) || $advanced[$field] == $dateform) ? '-' : $advanced[$field]) . '</div>
					<input type="hidden" name="' . $field . '" id="hidden_Calendar_' . $field . '" value="' . (empty($advanced[$field]) || ($advanced[$field] == $dateform) ? '-' : $advanced[$field]) . '" />
				</td>
				<td style="height:25px;width:10px"></td>
				<td style="width:102px;vertical-align:top;height:25px">' . we_html_button::create_button(we_html_button::CALENDAR, "javascript:", null, null, null, null, null, null, false, 'button_Calendar_' . $field) . '</td>
				<td style="width:300px;height:25px"  class="defaultfont">' . $EMailhandler . '</td>
			</tr>';
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
			</tr>' . $this->getCustomerFieldTable($customer, $fields ? $fields['orderCustomerFields'] : []) . '
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

		$this->db->query('SELECT
	oi.orderDocID,
	oi.quantity,
	oi.Price,
	oi.customFields,
	IF(o.pricesNet||o.calcVat||IFNULL(oi.Vat,' . $mwst . ')=0,oi.Price, (oi.Price/(1+(IFNULL(oi.Vat,' . $mwst . ')/100))) )AS NetPrice,
	IFNULL(oi.Vat,' . $mwst . ') AS Vat,
	od.DocID,
	od.title,
	od.description,
	od.variant
FROM ' . SHOP_ORDER_TABLE . ' o JOIN ' . SHOP_ORDER_ITEM_TABLE . ' oi ON o.ID=oi.orderID JOIN ' . SHOP_ORDER_DOCUMENT_TABLE . ' od ON oi.orderDocID=od.ID
WHERE o.ID=' . $bid);


		// loop through all articles
		$totalPrice = 0;
		$articleVatArray = [];
		while($this->db->next_record()){
			// all information for article
			$ArticleId = $this->db->f('DocID'); // id of article (object or document) in shopping cart
			$tblOrdersId = $this->db->f('orderDocID');
			// now determine VAT
			$articleVat = $this->db->f('Vat');

			// determine taxes - correct price, etc.
			$Price = floatval($this->db->f('NetPrice'));
			$Quantity = floatval($this->db->f('quantity'));
			$articlePrice = $Price * $Quantity;
			$totalPrice += $articlePrice;

			// calculate individual vat for each article
			if($calcVat && $articleVat){
				if(!isset($articleVatArray[$articleVat])){ // avoid notices
					$articleVatArray[$articleVat] = 0;
				}
				$articleVatArray[$articleVat] += $articlePrice * ($articleVat / 100);
			}

// table row of one article
			$orderTable .= '
		<tr><td height="1" colspan="11"><hr style="color: black" noshade /></td></tr>
		<tr>
			<td class="shopContentfontR">' . "<a href=\"javascript:var anzahl=prompt('" . g_l('modules_shop', '[jsanz]') . "','" . $Quantity . "'); if(anzahl != null){if(anzahl.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&bid=" . $bid . "&article=$tblOrdersId&anzahl='+anzahl;}}\">" . $Quantity . "</a>" . '</td>
			<td>' . self::cutText($this->db->f('title'), 35) . '</td>
			<td>' . self::cutText($this->db->f('description'), 35) . '</td>
			<td class="shopContentfontR">' . "<a href=\"javascript:var preis = prompt('" . g_l('modules_shop', '[jsbetrag]') . "','" . $Price . "'); if(preis != null ){if(preis.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . "}else{document.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&bid=" . $bid . "&article=$tblOrdersId&preis=' + preis; } }\">" . we_base_util::formatNumber($Price) . "</a>" . $waehr . '</td>
			<td class="shopContentfontR">' . we_base_util::formatNumber($articlePrice) . $waehr . '</td>' .
				($calcVat ? '<td class="shopContentfontR small">(' . "<a href=\"javascript:var vat = prompt('" . g_l('modules_shop', '[keinezahl]') . "','" . $articleVat . "'); if(vat != null ){if(vat.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&bid=" . $bid . "&article=$tblOrdersId&vat=' + vat; } }\">" . we_base_util::formatNumber($articleVat) . "</a>" . '%)</td>' : '') . '
			<td>' . we_html_button::create_button(we_html_button::TRASH, "javascript:check=confirm('" . g_l('modules_shop', '[jsloeschen]') . "'); if (check){document.location.href=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&bid=" . $bid . "&deleteaarticle=" . $tblOrdersId . "';}", '', 0, 0, "", "", !permissionhandler::hasPerm("DELETE_SHOP_ARTICLE")) . '</td>
		</tr>';
			// if this article has custom fields or is a variant - we show them in a extra rows
			// add variant.
			if($this->db->f('variant')){

				$orderTable .= '
		<tr>
			<td colspan="4"></td>
			<td class="small" colspan="6">' . g_l('modules_shop', '[variant]') . ': ' . $this->db->f('variant') . '</td>
		</tr>';
			}
			// add custom fields
			if(($cf = $this->db->f('customFields'))){
				$cf = we_unserialize($cf);
				$caField = '';
				foreach($cf as $key => $value){
					$caField .= $key . ': ' . $value . '; ';
				}

				$orderTable .= '
		<tr>
			<td colspan="4"></td>
			<td class="small" colspan="6">' . $caField . '</td>
		</tr>';
			}
		}

		// "Sum of order"
		// add shipping to costs
		// just calculate netPrice, gros, and taxes

		if(!isset($articleVatArray[$orderData['shippingVat']])){
			$articleVatArray[$orderData['shippingVat']] = 0;
		}

		if($orderData['shippingNet']){ // all correct here
			$shippingCostsNet = $orderData['shippingCost'];
			$shippingCostsVat = $orderData['shippingCost'] * $orderData['shippingVat'] / 100;
			$shippingCostsGros = $shippingCostsNet + $shippingCostsVat;
		} else {
			$shippingCostsGros = $orderData['shippingCost'];
			$shippingCostsVat = $orderData['shippingCost'] / ($orderData['shippingVat'] + 100) * $orderData['shippingVat'];
			$shippingCostsNet = $orderData['shippingCost'] / ($orderData['shippingVat'] + 100) * 100;
		}
		$articleVatArray[$orderData['shippingVat']] += $shippingCostsVat;

		$orderTable .= '
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="3" class="shopContentfontR">' . g_l('modules_shop', '[Preis]') . ':</td>
			<td colspan="2" class="shopContentfontR"><strong>' . we_base_util::formatNumber($totalPrice) . $waehr . '</strong></td>
		</tr>';

		if($calcVat){ // add Vat to price
			$totalPriceAndVat = $totalPrice;

			if($pricesAreNet){ // prices are net
				$orderTable .= '<tr><td height="1" colspan="11"><hr style="color: black" noshade /></td></tr>';

				$totalPriceAndVat += $shippingCostsNet;
				$orderTable .= '
		<tr>
			<td colspan="3" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
			<td colspan="2" class="shopContentfontR"><strong><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . we_base_util::formatNumber($shippingCostsNet) . $waehr . '</a></strong></td>
			<td class="shopContentfontR small">(' . we_base_util::formatNumber($orderData['shippingVat']) . '%)</td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="3" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[plusVat]') . '</label>:</td>
			<td colspan="2"></td>
			<td><input id="checkBoxCalcVat" onclick="document.location=WE().consts.dirs.WEBEDITION_DIR+\'we_showMod.php?mod=shop&pnt=edbody&bid=' . $bid . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
		</tr>';
				foreach($articleVatArray as $vatRate => $sum){
					if($vatRate){
						$totalPriceAndVat += $sum;
						$orderTable .= '
		<tr>
			<td colspan="3" class="shopContentfontR">' . $vatRate . ' %:</td>
			<td colspan="2" class="shopContentfontR">' . we_base_util::formatNumber($sum) . $waehr . '</td>
		</tr>';
					}
				}
				$orderTable .= '
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="3" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
			<td colspan="2" class="shopContentfontR"><strong>' . we_base_util::formatNumber($totalPriceAndVat) . $waehr . '</strong></td>
		</tr>';
			} else { // prices are gros
				$orderTable .= '<tr><td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td></tr>';


				$totalPrice += $shippingCostsGros;
				$orderTable .= '
		<tr>
			<td colspan="3" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
			<td colspan="2" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . we_base_util::formatNumber($shippingCostsGros) . $waehr . '</a></td>
			<td class="shopContentfontR small">(' . we_base_util::formatNumber($orderData['shippingVat']) . '%)</td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="3" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
			<td colspan="2" class="shopContentfontR"><strong>' . we_base_util::formatNumber($totalPrice) . $waehr . '</strong></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="3" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[includedVat]') . '</label>:</td>
			<td colspan="2"></td>
			<td><input id="checkBoxCalcVat" onclick="document.location=WE().consts.dirs.WEBEDITION_DIR+\'we_showMod.php?mod=shop&pnt=edbody&bid=' . $bid . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
		</tr>';
				foreach($articleVatArray as $vatRate => $sum){
					if($vatRate){
						$orderTable .= '
		<tr>
			<td colspan="3" class="shopContentfontR">' . $vatRate . ' %:</td>
			<td colspan="2" class="shopContentfontR">' . we_base_util::formatNumber($sum) . $waehr . '</td>
		</tr>';
					}
				}
			}
		} else {
			$totalPrice += $shippingCostsNet;

			$orderTable .= '
		<tr>
			<td height="1" colspan="11"><hr style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="3" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
			<td colspan="2" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\')">' . we_base_util::formatNumber($shippingCostsNet) . $waehr . '</a></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="3" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[edit_order][calculate_vat]') . '</label></td>
			<td colspan="2"></td>
			<td><input id="checkBoxCalcVat" onclick="document.location=WE().consts.dirs.WEBEDITION_DIR+\'we_showMod.php?mod=shop&pnt=edbody&bid=' . $bid . '&we_cmd[0]=payVat&pay=1\';" type="checkbox" name="calculateVat" value="1" /></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="3" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
			<td colspan="2" class="shopContentfontR"><strong>' . we_base_util::formatNumber($totalPrice) . $waehr . '</strong></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>';
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
		//
		// "Additional fields in shopping basket"
		// ********************************************************************************
		//
			// "Building the order infos"
		// ********************************************************************************
		// ********************************************************************************
		// "Html output for order with articles"
		//
			$js = '
bid =' . $bid . ';
cid =' . $orderData['customerID'] . ';';
		echo we_html_tools::getCalendarFiles() .
		we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
		we_html_element::jsScript(WE_JS_MODULES_DIR . 'shop/we_shop_view2.js', $js);
		?>

		</head>
				<body class="weEditorBody" onload="hot = true" onunload="doUnload()"><?php
					$parts = [['html' => $orderDataTable,
				],
					['html' => $orderTable,
				]
			];


			$parts[] = ['html' => $this->getCustomCartFieldsTable($bid, $customCartFields),
			];


			echo we_html_multiIconBox::getHTML('', $parts, 30);

			//
			// "Html output for order with articles"
			// ********************************************************************************

			$js = '
// init the used calendars

function CalendarChanged(calObject) {
	// field:
	_field = calObject.params.inputField;
	document.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&bid=' . $bid . '&" + _field.name + "=" + _field.value;
}
';

			foreach(we_shop_statusMails::$StatusFields as $cur){
				if(!$weShopStatusMails->FieldsHidden[$cur]){
					$js .= '		Calendar.setup({
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
		$bid = we_base_request::_(we_base_request::INT, 'bid');
		switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
			case 'add_article':
				if(we_base_request::_(we_base_request::FLOAT, 'anzahl') == 0){
					return we_message_reporting::jsMessagePush("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true);
				}

				$tmp = explode('_', we_base_request::_(we_base_request::STRING, 'add_article', ''));
				$isObj = ($tmp[1] == we_shop_shop::OBJECT);
				$id = intval($tmp[0]);

				// check for variant or customfields
				$customFieldsTmp = [];
				$customField = we_base_request::_(we_base_request::STRING, 'we_customField');
				if($customField){
					$fields = explode(';', trim($customField));
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
				$orderArray = getHash('SELECT customerData,priceName FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $bid);
				$customer = we_unserialize($orderArray['customerData']);

				$standardVat = we_shop_vats::getStandardShopVat();

				if(we_shop_category::isCategoryMode()){
					$wedocCategory = $serialDoc[we_listview_base::PROPPREFIX . 'CATEGORY'];
					$stateField = we_shop_vatRule::getStateField();
					$billingCountry = !empty($customer[$stateField]) ? $customer[$stateField] : we_shop_category::getDefaultCountry();
					$catId = !empty($serialDoc[WE_SHOP_CATEGORY_FIELD_NAME]) ? $serialDoc[WE_SHOP_CATEGORY_FIELD_NAME] : 0;

					$shopVat = we_shop_category::getShopVatByIdAndCountry($catId, $wedocCategory, $billingCountry);
				} elseif(isset($serialDoc[WE_SHOP_VAT_FIELD_NAME])){
					$shopVat = we_shop_vats::getShopVATById($serialDoc[WE_SHOP_VAT_FIELD_NAME]);
				}

				$type = ($isObj ? 'object' : 'document');
				$pub = intval(empty($serialDoc['we_wedoc_Published']) ? $serialDoc['WE_Published'] : $serialDoc['we_wedoc_Published']);

				$orderDocID = f('SELECT ID FROM ' . SHOP_ORDER_DOCUMENT_TABLE . ' WHERE DocID=' . $id . ' AND type="' . $type . '" AND variant="' . $this->db->escape($variant) . '" AND Published=FROM_UNIXTIME(' . $pub . ')');
				if(!$orderDocID){
					$data = $serialDoc;
					unset($data['we_shoptitle'], $data['we_shopdescription'], $data['we_sacf'], $data['shopvat'], $data['shopcategory'], $data['WE_VARIANT']);
					//add document first
					$this->db->query('INSERT INTO ' . SHOP_ORDER_DOCUMENT_TABLE . ' SET ' . we_database_base::arraySetter([
							'DocID' => $id,
							'type' => $type,
							'variant' => $variant,
							'Published' => sql_function('FROM_UNIXTIME(' . $pub . ')'),
							'title' => strip_tags($serialDoc['we_shoptitle']),
							'description' => strip_tags($serialDoc['we_shopdescription']),
							'CategoryID' => $catId,
							'SerializedData' => we_serialize($data, SERIALIZE_JSON, false, 5, true)
					]));
					$orderDocID = $this->db->getInsertId();
				}

				//need pricefield:
				$pricename = ($orderArray['priceName'] ?: 'shopprice');
				// now insert article to order:

				$this->db->query('INSERT INTO ' . SHOP_ORDER_ITEM_TABLE . ' SET ' .
					we_database_base::arraySetter(([
						'orderID' => $bid,
						'orderDocID' => $orderDocID,
						'quantity' => we_base_request::_(we_base_request::FLOAT, 'anzahl', 0),
						'Price' => $serialDoc[$pricename],
						'Vat' => ($shopVat !== false ? $shopVat : ($standardVat ?: sql_function('NULL'))),
						'customFields' => $serialDoc[WE_SHOP_ARTICLE_CUSTOM_FIELD] ? we_serialize($serialDoc[WE_SHOP_ARTICLE_CUSTOM_FIELD], SERIALIZE_JSON, false, 0, true) : sql_function('NULL'),
				])));

				break;

			case 'add_new_article':
				$shopArticles = [];

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
					$searchArticle = we_base_request::_(we_base_request::STRING, 'searchArticle');
					// now get all shop objects
					foreach($this->classIds as $classId){
						$classId = intval($classId);
						$this->db->query('SELECT obx.input_' . WE_SHOP_TITLE_FIELD_NAME . ' AS shopTitle,of.ID as objectId FROM ' . OBJECT_X_TABLE . $classId . ' obx JOIN ' . OBJECT_FILES_TABLE . ' of ON obx.OF_ID=of.ID ' .
							($searchArticle ?
								' WHERE obx.input_' . WE_SHOP_TITLE_FIELD_NAME . '  LIKE "%' . $this->db->escape($searchArticle) . '%"' :
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
					we_html_button::create_button(we_html_button::BACK, '#', '', 0, 0, '', '', true));

				$nextBut = (($end_entry) < $AMOUNT_ARTICLES ?
					we_html_button::create_button(we_html_button::NEXT, 'javascript:switchEntriesPage(' . ($page + 1) . ');') :
					we_html_button::create_button(we_html_button::NEXT, '#', '', 0, 0, '', '', true));


				$shopArticlesSelect = $shopArticlesParts[$page];
				asort($shopArticlesSelect);

				// determine which articles should be shown >>>

				$parts = [($AMOUNT_ARTICLES > 0 ?
					['headline' => g_l('modules_shop', '[Artikel]'),
					'space' => we_html_multiIconBox::SPACE_MED,
					'html' => '
		<form name="we_intern_form">' . we_html_element::htmlHiddens(['bid' => $_REQUEST['bid'],
						'we_cmd[]' => 'add_new_article'
						]) . '
			<table class="default">
			<tr>
			<td>' . we_html_tools::htmlSelect("add_article", $shopArticlesSelect, 15, we_base_request::_(we_base_request::RAW, 'add_article', ''), false, ['onchange' => "selectArticle(this.options[this.selectedIndex].value)"], 'value', '380') . '</td>
			<td width="10"></td>
			<td style="vertical-align:top">' . $backBut . '<div style="margin:5px 0"></div>' . $nextBut . '</td>
			</tr>
			<tr>
				<td class="small">' . sprintf(g_l('modules_shop', '[add_article][entry_x_to_y_from_z]'), $start_entry, $end_entry, $AMOUNT_ARTICLES) . '</td>
			</tr>
			</table>',
					'noline' => 1
					] :
					['headline' => g_l('modules_shop', '[Artikel]'),
					'space' => we_html_multiIconBox::SPACE_MED,
					'html' => g_l('modules_shop', '[add_article][empty_articles]')
					]
					)
				];

				if($AMOUNT_ARTICLES > 0 || isset($_REQUEST['searchArticle'])){
					$parts[] = ['headline' => g_l('global', '[search]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => '
			<table class="default">
				<tr><td>' . we_html_tools::htmlTextInput('searchArticle', 24, we_base_request::_(we_base_request::RAW, 'searchArticle', ''), '', 'id="searchArticle"', 'text', 380) . '</td>
					<td></td>
					<td>' . $searchBut . '</td>
				</tr>
			</table>
		</form>'
					];
				}

				if(isset($_REQUEST['add_article']) && $_REQUEST['add_article'] != '0'){
					$saveBut = we_html_button::create_button(we_html_button::SAVE, "javascript:document.we_form.submit();window.close();");
					list($id, $type) = explode('_', $_REQUEST['add_article']);

					$variantOptions = ['-' => '-'];

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

					$parts[] = ['headline' => g_l('modules_shop', '[Artikel]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => '
							<form name="we_form" target="edbody">' .
						we_html_element::htmlHiddens(['bid' => $_REQUEST['bid'],
							'we_cmd[]' => 'add_article',
							'add_article' => $_REQUEST['add_article']
						]) .
						'<b>' . $model->elements[WE_SHOP_TITLE_FIELD_NAME]['dat'] . '</b>',
						'noline' => 1
					];

					$parts[] = ['headline' => g_l('modules_shop', '[anzahl]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => we_html_tools::htmlTextInput('anzahl', 24, '', '', 'min="1"', 'number', 380),
						'noline' => 1
					];

					$parts[] = ['headline' => g_l('modules_shop', '[variant]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => we_html_tools::htmlSelect(we_base_constants::WE_VARIANT_REQUEST, $variantOptions, 1, '', false, [], 'value', 380),
						'noline' => 1
					];

					$parts[] = ['headline' => g_l('modules_shop', '[customField]'),
						'space' => we_html_multiIconBox::SPACE_MED,
						'html' => we_html_tools::htmlTextInput('we_customField', 24, '', '', '', 'text', 380) .
						'<br /><span class="small">Eingabe in der Form: <i>name1=wert1;name2=wert2</i></span></form>',
						'noline' => 1
					];
				}

				echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement('
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
		}'), '<body class="weDialogBody">' .
					we_html_multiIconBox::getHTML('', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[add_article][title]')) .
					'</form>');
				exit;

			case 'payVat':
				$calcVat = we_base_request::_(we_base_request::INT, 'pay', 0);

				// update all orders with this orderId
				if($this->db->query('UPDATE ' . SHOP_ORDER_TABLE . ' SET calcVat=' . $calcVat . ' WHERE ID=' . $bid)){
					return we_message_reporting::jsMessagePush(g_l('modules_shop', '[edit_order][js_saved_calculateVat_success]'), we_message_reporting::WE_MESSAGE_NOTICE);
				}
				return we_message_reporting::jsMessagePush(g_l('modules_shop', '[edit_order][js_saved_calculateVat_error]'), we_message_reporting::WE_MESSAGE_ERROR);

			case 'delete_shop_cart_custom_field':
				$cartfield = we_base_request::_(we_base_request::STRING, 'cartfieldname');
				if($cartfield){
					$customFields = we_unserialize(f('SELECT customFields FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $bid));
					unset($customFields[$cartfield]);

					// update all orders with this orderId
					if($this->db->query('UPDATE ' . SHOP_ORDER_TABLE . ' SET ' . we_database_base::arraySetter([
								'customFields' => $customFields ? we_serialize($customFields, SERIALIZE_JSON, false, 0, true) : sql_function('NULL'),
							]) . ' WHERE ID=' . $bid)
					){
						return we_message_reporting::jsMessagePush(sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_success]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_NOTICE);
					}
					return we_message_reporting::jsMessagePush(sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_error]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_ERROR);
				}
				break;

			case 'edit_shop_cart_custom_field':
				$saveBut = we_html_button::create_button(we_html_button::SAVE, 'javascript:we_submit();');
				$cancelBut = we_html_button::create_button(we_html_button::CANCEL, 'javascript:self.close();');


				$val = '';
				$cartfield = we_base_request::_(we_base_request::STRING, 'cartfieldname');
				if($cartfield){
					$customFields = we_unserialize(f('SELECT customFields FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $bid));
					$val = $customFields[$cartfield] ?: '';
					$fieldHtml = $cartfield . '<input type="hidden" name="cartfieldname" id="cartfieldname" value="' . $cartfield . '" />';
				} else {
					$fieldHtml = we_html_tools::htmlTextInput('cartfieldname', 24, '', '', 'id="cartfieldname"');
					$val = '';
				}

				// make input field, for name or textfield
				$parts = [
						['headline' => g_l('modules_shop', '[field_name]'),
						'html' => $fieldHtml,
						'space' => we_html_multiIconBox::SPACE_MED,
						'noline' => 1
					],
						['headline' => g_l('modules_shop', '[field_value]'),
						'html' => '<textarea name="cartfieldvalue" style="width: 350; height: 150">' . $val . '</textarea>',
						'space' => we_html_multiIconBox::SPACE_MED
					]
				];

				echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement('function we_submit() {
	var elem = document.getElementById("cartfieldname");

	if (elem && elem.value) {
		document.we_form.submit();
	} else {
		top.we_showMessage(WE().consts.g_l.shop.field_empty_js_alert, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}'), '
		<body class="weDialogBody">
		<form name="we_form">
		<input type="hidden" name="bid" value="' . $_REQUEST['bid'] . '" />
		<input type="hidden" name="we_cmd[0]" value="save_shop_cart_custom_field" />' .
					we_html_multiIconBox::getHTML('', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[add_shop_field]')) .
					'</form>');
				exit;

			case 'save_shop_cart_custom_field':
				$cartfield = we_base_request::_(we_base_request::STRING, 'cartfieldname');
				if($cartfield){
					$customFields = we_unserialize(f('SELECT customFields FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $bid));
					$customFields[$cartfield] = we_base_request::_(we_base_request::STRING, 'cartfieldvalue', '');

					// update all orders with this orderId
					if($this->db->query('UPDATE ' . SHOP_ORDER_TABLE . ' SET ' . we_database_base::arraySetter([
								'customFields' => $customFields ? we_serialize($customFields, SERIALIZE_JSON, false, 0, true) : sql_function('NULL'),
							]) . ' WHERE ID=' . $bid)
					){
						$jsCmd = 'top.opener.top.content.doClick(' . $_REQUEST['bid'] . ',"shop","' . SHOP_ORDER_TABLE . '");
top.opener.' . we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_success]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_NOTICE);
					} else {
						$jsCmd = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_error]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_ERROR);
					}
				} else {
					$jsCmd = 'top.opener.' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[field_empty_js_alert]'), we_message_reporting::WE_MESSAGE_ERROR);
				}

				echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement($jsCmd . 'window.close();'), we_html_element::htmlBody());
				exit;

			case 'edit_shipping_cost':
				$shopVats = we_shop_vats::getAllShopVATs();
				$shippingVats = [];

				foreach($shopVats as $k => $shopVat){
					$shippingVats[$shopVat->vat] = $shopVat->vat . ' - ' . $shopVat->getNaturalizedText() . ' (' . $shopVat->territory . ')';
				}

				$saveBut = we_html_button::create_button(we_html_button::SAVE, 'javascript:document.we_form.submit();self.close();');
				$cancelBut = we_html_button::create_button(we_html_button::CANCEL, 'javascript:self.close();');

				$shipping = getHash('SELECT shippingCost,shippingNet,shippingVat FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $bid);

				$parts = [
						['headline' => g_l('modules_shop', '[edit_order][shipping_costs]'),
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_html_tools::htmlTextInput('weShipping_costs', 24, $shipping['shippingCost']),
						'noline' => 1
					],
						['headline' => g_l('modules_shop', '[edit_shipping_cost][isNet]'),
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_html_tools::htmlSelect('weShipping_isNet', [1 => g_l('global', '[yes]'), 0 => g_l('global', '[no]')], 1, $shipping['shippingNet']),
						'noline' => 1
					],
						['headline' => g_l('modules_shop', '[edit_shipping_cost][vatRate]'),
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_html_tools::htmlInputChoiceField('weShipping_vatRate', $shipping['shippingVat'], $shippingVats, [], '', true),
						'noline' => 1
					]
				];

				echo we_html_tools::getHtmlTop('', '', '', '', '
						<body class="weDialogBody">
						<form name="we_form" target="edbody">' .
					we_html_element::htmlHiddens([
						'bid' => $_REQUEST['bid'],
						"we_cmd[]" => 'save_shipping_cost'
					]) .
					we_html_multiIconBox::getHTML('', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[edit_shipping_cost][title]')) .
					'</form>');
				exit;

			case 'save_shipping_cost':
				if($this->db->query('UPDATE ' . SHOP_ORDER_TABLE . ' SET ' . we_database_base::arraySetter([
							'shippingCost' => we_base_request::_(we_base_request::FLOAT, 'weShipping_costs'),
							'shippingNet' => we_base_request::_(we_base_request::INT, 'weShipping_isNet'),
							'shippingVat' => we_base_request::_(we_base_request::FLOAT, 'weShipping_vatRate'),
						]) . ' WHERE ID=' . $bid)
				){
					return we_message_reporting::jsMessagePush(g_l('modules_shop', '[edit_order][js_saved_shipping_success]'), we_message_reporting::WE_MESSAGE_NOTICE);
				}
				return we_message_reporting::jsMessagePush(g_l('modules_shop', '[edit_order][js_saved_shipping_error]'), we_message_reporting::WE_MESSAGE_ERROR);

			case 'edit_order_customer'; // edit data of the saved customer.
				$saveBut = we_html_button::create_button(we_html_button::SAVE, 'javascript:document.we_form.submit();self.close();');
				$cancelBut = we_html_button::create_button(we_html_button::CANCEL, 'javascript:self.close();');
				// 1st get the customer for this order
				$customer = $this->getOrderCustomerData($_REQUEST['bid']);
				ksort($customer);

				$dontEdit = explode(',', we_shop_shop::ignoredEditFields);

				$parts = [
						['html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[preferences][explanation_customer_odercustomer]'), we_html_tools::TYPE_INFO, 470),
					],
						['headline' => g_l('modules_customer', '[Forname]') . ': ',
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_html_tools::htmlTextInput('weCustomerOrder[Forename]', 44, $customer['Forename']),
						'noline' => 1
					],
						['headline' => g_l('modules_customer', '[Surname]') . ': ',
						'space' => we_html_multiIconBox::SPACE_MED2,
						'html' => we_html_tools::htmlTextInput('weCustomerOrder[Surname]', 44, $customer['Surname']),
						'noline' => 1
					]
				];
				$editFields = ['Forename', 'Surname'];

				foreach($customer as $k => $v){
					if(!in_array($k, $dontEdit) && !is_numeric($k)){
						if(isset($this->CLFields['stateField']) && !empty($this->CLFields['stateFieldIsISO']) && $k == $this->CLFields['stateField']){
							$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
							$langcode = array_search($lang[0], getWELangs());
							$countrycode = array_search($langcode, getWECountries());
							$countryselect = new we_html_select(['name' => 'weCustomerOrder[' . $k . ']', 'style' => 'width:280px;', 'class' => 'wetextinput']);

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
							$countryselect->addOption('-', '----', ['disabled' => 'disabled']);
							foreach($shownCountries as $countrykey => &$countryvalue){
								$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
							}
							unset($countryvalue);
							$countryselect->selectOption($v);

							$parts[] = ['headline' => $k . ': ',
								'space' => we_html_multiIconBox::SPACE_MED2,
								'html' => $countryselect->getHtml(),
								'noline' => 1
							];
						} elseif((isset($this->CLFields['languageField']) && !empty($this->CLFields['languageFieldIsISO']) && $k == $this->CLFields['languageField'])){
							$frontendL = $GLOBALS['weFrontendLanguages'];
							foreach($frontendL as &$lcvalue){
								list($lcvalue) = explode('_', $lcvalue);
							}
							unset($countryvalue);
							$languageselect = new we_html_select(['name' => 'weCustomerOrder[' . $k . ']', 'style' => 'width:280px;', 'class' => 'wetextinput']);
							foreach(g_l('languages', '') as $languagekey => $languagevalue){
								if(in_array($languagekey, $frontendL)){
									$languageselect->addOption($languagekey, $languagevalue);
								}
							}
							$languageselect->selectOption($v);

							$parts[] = ['headline' => $k . ': ',
								'space' => we_html_multiIconBox::SPACE_MED2,
								'html' => $languageselect->getHtml(),
								'noline' => 1
							];
						} else {
							$parts[] = ['headline' => $k . ': ',
								'space' => we_html_multiIconBox::SPACE_MED2,
								'html' => we_html_tools::htmlTextInput('weCustomerOrder[' . $k . ']', 44, $v),
								'noline' => 1
							];
						}
						$editFields[] = $k;
					}
				}

				echo we_html_tools::getHtmlTop('', '', '', '', '
						<body class="weDialogBody">
						<form name="we_form" target="edbody">' .
					we_html_element::htmlHiddens([
						'bid' => $_REQUEST['bid'],
						'we_cmd[]' => 'save_order_customer'
					]) .
					we_html_multiIconBox::getHTML('', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[preferences][customerdata]')) .
					'</form>
						</body>');
				exit;

			case 'save_order_customer':
				// just get this order and save this userdata in there.
				$customer = we_base_request::_(we_base_request::STRING, 'weCustomerOrder');

				if($this->db->query('UPDATE ' . SHOP_ORDER_TABLE . ' SET ' . we_database_base::arraySetter([
							'customerData' => we_serialize($customer, SERIALIZE_JSON, false, 5, true),
						]) . ' WHERE ID=' . $bid)){
					return we_message_reporting::jsMessagePush(g_l('modules_shop', '[edit_order][js_saved_customer_success]'), we_message_reporting::WE_MESSAGE_NOTICE);
				}
				return we_message_reporting::jsMessagePush(g_l('modules_shop', '[edit_order][js_saved_customer_error]'), we_message_reporting::WE_MESSAGE_ERROR);
		}
	}

	function processVariables(){
		if(isset($_SESSION['weS']['raw_session'])){
			$this->raw = we_unserialize($_SESSION['weS']['raw_session']);
		}

		if(is_array($this->raw->persistent_slots)){
			foreach($this->raw->persistent_slots as $val){
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

	private static function cutText($val, $length = 0){
		return $length && strlen($val) > $length ?
			'<span ' . ($length ? 'class="cutText" title="' . $val . '" style="max-width: ' . $length . 'em;"' : '') . '>' . $val . '</span>' :
			$val;
	}

	private function getOrderCustomerData($orderId, array $felder = []){
		// get Customer
		$customerDb = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=(SELECT customerID FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $orderId . ')', $this->db, MYSQL_ASSOC);

		$customerOrder = we_unserialize(f('SELECT customerData FROM ' . SHOP_ORDER_TABLE . ' WHERE ID=' . $orderId));

		//used only if edit customer data is selected!!!
		//only data from order - return all fields, fill in unknown fields from customer-db
		// default values are fields saved with order
		$ret = array_merge($customerDb, $customerOrder);
		return ($felder ? //return only selected fields
			array_intersect_key($ret, array_flip($felder)) :
			$ret);
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


		$content = we_html_button::create_button('pref_shop', "javascript:top.we_cmd('pref_shop');", '', 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")) . '<br/>' .
			we_html_button::create_button('payment_val', "javascript:top.we_cmd('payment_val');", '', 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")) . '<br/>';
		if(($resultD) && $resultO){ //docs and objects
			$content .= we_html_button::create_button('quick_rev', "javascript:top.content.editor.location='" . $this->frameset . "&pnt=editor&top=1&typ=document '") . '<br/>';
		} elseif((!$resultD) && $resultO){ // no docs but objects
			$content .= we_html_button::create_button('quick_rev', "javascript:top.content.editor.location='" . $this->frameset . "&pnt=editor&top=1&typ=object&ViewClass=$classid '") . '<br/>';
		} elseif(($resultD) && !$resultO){ // docs but no objects
			$content .= we_html_button::create_button('quick_rev', "javascript:top.content.editor.location='" . $this->frameset . "&pnt=editor&top=1&typ=document '") . '<br/>';
		}

		return parent::getActualHomeScreen('shop', "shop.gif", $content);
	}

	private function getCustomCartFieldsTable($bid, $customCartFields){
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
						<td style="vertical-align:top">' . we_html_button::create_button(we_html_button::TRASH, "javascript:check=confirm('" . sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field]'), $key) . "'); if (check) { document.location.href=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=shop&pnt=edbody&we_cmd[0]=delete_shop_cart_custom_field&bid=" . $bid . "&cartfieldname=" . $key . "'; }") . '</td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>';
		}
		$customCartFieldsTable .= '<tr>
						<td>' . we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('edit_shop_cart_custom_field');") . '</td>
					</tr>
					</table>';
		return $customCartFieldsTable;
	}

	private function getCustomerFieldTable(array $customer, array $customerFields){
		$customerFieldTable = // first show fields Forename and surname
			(empty($customer['Forename']) ? '' : '
		<tr style="height:25px">
			<td class="defaultfont" style="width:86px;vertical-align:top;height:25px">' . g_l('modules_customer', '[Forname]') . ':</td>
			<td class="defaultfont" style="vertical-align:top;width:40px;height:25px"></td>
			<td style="width:20px;height:25px;"></td>
			<td class="defaultfont" style="vertical-align:top" colspan="6" height="25">' . $customer['Forename'] . '</td>
		</tr>') .
			(empty($customer['Surname']) ? '' : '
		<tr style="height:25px">
			<td class="defaultfont" style="width:86px;vertical-align:top;height:25px;">' . g_l('modules_customer', '[Surname]') . ':</td>
			<td class="defaultfont" style="vertical-align:top;width:40px;height:25px"></td>
			<td style="width:20px;height:25px;"></td>
			<td class="defaultfont" style="vertical-align:top;height:25px;" colspan="6">' . $customer['Surname'] . '</td>
		</tr>');

		$customer = array_intersect_key($customer, array_flip($customerFields));
		unset($customer['Surname'], $customer['Forename']);
		foreach($customer as $key => $value){
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
			$customerFieldTable .= '
		<tr height="25">
			<td class="defaultfont" style="width:86px;vertical-align:top;height:25px;">' . $key . ':</td>
			<td class="defaultfont" style="vertical-align:top;width:40px;height:25px;"></td>
			<td style="width:20px;height:25px;"></td>
			<td class="defaultfont" style="vertical-align:top;height:25px;" colspan="6">' . $value . '</td>
		</tr>';
		}
		return $customerFieldTable;
	}

}
