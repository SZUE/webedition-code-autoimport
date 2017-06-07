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
class we_widget_shp extends we_widget_base{
	private $shopDashboard = '';
	private $newSCurrId = '';
	private $interval = '';
	private $jsConfig = [];

	public function __construct($curID = '', $aProps = []){
		if(!$curID){//preview requested
			$aCols = we_base_request::_(we_base_request::STRING, 'we_cmd');
			$this->newSCurrId = we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 5);
		} else {
			$this->newSCurrId = $curID;
		}
		$isRefresh = true;
		if(!isset($aCols[5])){
			$aCols = explode(';', $aProps[3]);
			$isRefresh = false;
		}

		$sKPIs = $aCols[0] ?: [];
		$bOrders = !empty($sKPIs[0]);
		$bCustomer = !empty($sKPIs[1]);
		$bAverageOrder = !empty($sKPIs[2]);
		$bTarget = !empty($sKPIs[3]);

		$iDate = intval($aCols[1]);
		$sRevenueTarget = intval($aCols[2]);
		$day = date('d');
		$month = date('m');
		$year = (date('Y'));
//note we use between, which means a between b and c is b<=a<=c, in case of date hour is always set to "00:00:00"
		switch($iDate){//FIXME: use cast & between to make this perform better
			default:
			case 0 : //heute
				$queryShopDateCondtion = '(o.DateOrder BETWEEN "' . date('Y-m-d') . '" AND "' . date('Y-m-') . ($day + 1) . '")';
				$timestampCustomer = '(MemberSince >= UNIX_TIMESTAMP(CURDATE()))';
				$this->interval = g_l('cockpit', '[today]');
				break;
			case 1 : //diese woche
				$queryShopDateCondtion = '(YEARWEEK(o.DateOrder,1) = YEARWEEK(CURDATE(),1))';
				$timestampCustomer = '(YEARWEEK(FROM_UNIXTIME(MemberSince),1) = YEARWEEK(CURDATE(),1))';
				$this->interval = g_l('cockpit', '[this_week]');
				break;
			case 2 : //letzte woche
				$queryShopDateCondtion = '(YEARWEEK(o.DateOrder,1) = YEARWEEK(CURDATE(),1)-1)';
				$timestampCustomer = '(YEARWEEK(FROM_UNIXTIME(MemberSince),1) = YEARWEEK(CURDATE(),1)-1)';
				$this->interval = g_l('cockpit', '[last_week]');
				break;
			case 3 : //dieser monat
				$queryShopDateCondtion = '(o.DateOrder BETWEEN "' . $year . '-' . $month . '-01" AND "' . $year . '-' . ($month + 1) . '-01")';
				$timestampCustomer = '(MemberSince BETWEEN UNIX_TIMESTAMP("' . $year . '-' . $month . '-01") AND UNIX_TIMESTAMP("' . $year . '-' . ($month + 1) . '-01"))';
				$this->interval = g_l('cockpit', '[this_month]');
				break;
			case 4 : //letzter monat
				$queryShopDateCondtion = '(o.DateOrder BETWEEN "' . $year . '-' . ($month - 1) . '-01" AND "' . $year . '-' . $month . '-01")';
				$timestampCustomer = '(MemberSince BETWEEN UNIX_TIMESTAMP("' . $year . '-' . ($month - 1) . '-01") AND UNIX_TIMESTAMP("' . $year . '-' . $month . '-01"))';
				$this->interval = g_l('cockpit', '[last_month]');
				break;
			case 5 : //dieses jahr
				$queryShopDateCondtion = '(o.DateOrder BETWEEN "' . $year . '-01-01" AND "' . ($year + 1) . '-01-01")';
				$timestampCustomer = '(MemberSince BETWEEN UNIX_TIMESTAMP("' . $year . '-01-01") AND UNIX_TIMESTAMP("' . ($year + 1) . '-01-01"))';
				$this->interval = g_l('cockpit', '[this_year]');
				break;
			case 6 : //letztes jahr
				$queryShopDateCondtion = '(o.DateOrder BETWEEN "' . ($year - 1) . '-01-01" AND "' . ($year) . '-01-01")';
				$timestampCustomer = '(MemberSince BETWEEN UNIX_TIMESTAMP("' . ($year - 1) . '-01-01") AND UNIX_TIMESTAMP("' . ($year) . '-01-01"))';
				$this->interval = g_l('cockpit', '[last_year]');
				break;
		}

// get some preferences!
		$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"'));
		$currency = oldHtmlspecialchars($feldnamen[0]);
		$numberformat = $feldnamen[2];
		//$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
		$defaultVat = ($feldnamen[1] ?: 0);

		$amountOrders = $amountArticles = $amountCanceledOrders = $canceled = 0;

		if(defined('WE_SHOP_VAT_TABLE') && (we_base_permission::hasPerm(['NEW_SHOP_ARTICLE', 'DELETE_SHOP_ARTICLE', 'EDIT_SHOP_ORDER', 'DELETE_SHOP_ORDER', 'EDIT_SHOP_PREFS']))){

			$total = $payed = $unpayed = $timestampDatePayment = 0;
			if(f('SELECT 1 FROM ' . SHOP_ORDER_TABLE . ' o JOIN ' . SHOP_ORDER_ITEM_TABLE . ' oi ON o.ID=oi.orderID WHERE ' . $queryShopDateCondtion)){

				$amountOrders = f('SELECT COUNT(1) FROM ' . SHOP_ORDER_TABLE . ' o WHERE ' . $queryShopDateCondtion);
				$amountCanceledOrders = f('SELECT COUNT(1) FROM ' . SHOP_ORDER_TABLE . ' o WHERE ' . $queryShopDateCondtion . ' AND o.DateCancellation IS NOT NULL');
				$amountArticles = f('SELECT COUNT(1) FROM ' . SHOP_ORDER_TABLE . ' o JOIN ' . SHOP_ORDER_ITEM_TABLE . ' oi ON o.ID=oi.orderID WHERE ' . $queryShopDateCondtion);
				$query = 'SELECT
SUM((oi.Price*oi.quantity*IF(o.pricesNet&&o.calcVat&&IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . '),(1+IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . ')/100),1)))
FROM ' . SHOP_ORDER_TABLE . ' o JOIN ' . SHOP_ORDER_ITEM_TABLE . ' oi ON o.ID=oi.orderID WHERE ' . $queryShopDateCondtion;

				$payed = f($query . ' AND o.DatePayment IS NOT NULL AND o.DateCancellation IS NULL');
				$canceled = f($query . ' AND o.DatePayment IS NULL AND o.DateCancellation IS NOT NULL');
				$unpayed = f($query . ' AND o.DatePayment IS NULL AND o.DateCancellation IS NULL');
				$total = $payed + $canceled + $unpayed;
			}
		}

		$amountCustomers = (defined('CUSTOMER_TABLE') && we_base_permission::hasPerm('CAN_SEE_CUSTOMER') ?
			f('SELECT COUNT(1) FROM ' . CUSTOMER_TABLE . '	WHERE ' . $timestampCustomer) :
			'');

		$shopDashboardTable = new we_html_table(['class' => 'default'], 1, 2);
		$i = 0;
		if($bOrders){
			//1. row
			$shopDashboardTable->setCol($i, 0, ['class' => "middlefont"], we_html_element::htmlB(g_l('cockpit', '[shop_dashboard][cnt_order]')));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right"], we_html_element::htmlB(($amountOrders > 0 ? $amountOrders : 0)));

			//2. row
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont", 'style' => "color:red;"], g_l('cockpit', '[shop_dashboard][canceled_order]'));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right;color:red;"], ($amountCanceledOrders > 0 ? $amountCanceledOrders : 0));

			//3. row
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont"], g_l('cockpit', '[shop_dashboard][cnt_articles]'));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right"], ($amountArticles > 0 ? $amountArticles : 0));

			//4. row
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont"], g_l('cockpit', '[shop_dashboard][articles_order]'));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => 'text-align:right;padding-bottom:2ex;'], we_base_util::formatNumber(($amountArticles > 0 ? ($amountArticles / $amountOrders) : 0), $numberformat));
		}

		if($bAverageOrder){
			//order volume
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont"], we_html_element::htmlB(g_l('cockpit', '[shop_dashboard][order_volume]')));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right"], we_html_element::htmlB(we_base_util::formatNumber($total, $numberformat) . '&nbsp;' . $currency));

			//canceled volume
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont", 'style' => "color:red;"], g_l('cockpit', '[shop_dashboard][canceled]'));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right;color:red;"], we_base_util::formatNumber($canceled, $numberformat) . '&nbsp;' . $currency);

			//revenue
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont"], we_html_element::htmlB(g_l('cockpit', '[shop_dashboard][revenue]')));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right"], we_html_element::htmlB(we_base_util::formatNumber(($total - $canceled), $numberformat) . '&nbsp;' . $currency));

			//payed volume
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont", 'style' => "color:green;"], g_l('cockpit', '[shop_dashboard][payed]'));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right;color:green;"], we_base_util::formatNumber($payed, $numberformat) . '&nbsp;' . $currency);

			//unpayed volume
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont", 'style' => "color:red;"], g_l('cockpit', '[shop_dashboard][unpayed]'));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right;color:red;"], we_base_util::formatNumber($unpayed, $numberformat) . '&nbsp;' . $currency);

			//volume per order
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont"], g_l('cockpit', '[shop_dashboard][order_value_order]'));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right"], we_base_util::formatNumber(($amountOrders > 0 ? ($total / $amountOrders) : 0), $numberformat) . '&nbsp;' . $currency);

			//need some space
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont"], "&nbsp;");
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont"], "&nbsp;");
		}

		if($bCustomer){
			//new customer
			$shopDashboardTable->addRow();
			$shopDashboardTable->setCol(++$i, 0, ['class' => "middlefont"], we_html_element::htmlB(g_l('cockpit', '[shop_dashboard][cnt_new_customer]')));
			$shopDashboardTable->setCol($i, 1, ['class' => "middlefont", 'style' => "text-align:right"], we_html_element::htmlB(($amountCustomers > 0 ? $amountCustomers : 0)));
		}

		$this->shopDashboard = '<div style="width:60%;float:left;">' .
			$shopDashboardTable->getHtml() .
			'</div>'
			. '<div style="width:40%;float:right;">' . ($bTarget ? '<b>' . g_l('cockpit', '[shop_dashboard][revenue_target]') . '&nbsp;' . we_base_util::formatNumber($sRevenueTarget, $numberformat) . '&nbsp;' . $currency . '</b><br/>' : '') .
			//note: canvas doesn't support CSS width/height....
			'<canvas id="' . $this->newSCurrId . '_chart_div" width="160" height="160"></canvas>' .
			'</div><br style="clear:both;"/>' .
			($bTarget ? we_html_element::jsScript(LIB_DIR . 'additional/gauge/gauge.min.js') : '' );
		$this->jsConfig = [
			'value' => we_base_util::formatNumber(($total - $canceled)),
			'label' => 'Ziel in ' . $currency,
			'unitsLabel' => ' ' . $currency,
			'min' => 0,
			'max' => ($sRevenueTarget * 2),
			'minorTicks' => 5, // small ticks inside each major tick
			'greenFrom' => ($sRevenueTarget * 1.1),
			'greenTo' => ($sRevenueTarget * 2),
			'yellowFrom' => ($sRevenueTarget * 0.9),
			'yellowTo' => ($sRevenueTarget * 1.1),
			'redFrom' => 0,
			'redTo' => ($sRevenueTarget * 0.9)
		];
	}

	public function getInsertDiv($iCurrId, $aProps, we_base_jsCmd $jsCmd){
		$cfg = self::getDefaultConfig();
		$oTblDiv = we_html_element::htmlDiv(
				['id' => 'm_' . $iCurrId . '_inline',
				'style' => "height:" . ($cfg["height"] - 25) . "px;overflow:auto;"
				], we_html_element::htmlDiv(['id' => 'shp_data'], $this->shopDashboard)
		);
		$aLang = [g_l('cockpit', '[shop_dashboard][headline]') . '&nbsp;' . $this->interval, ""];
		$jsCmd->addCmd('initGauge', $this->newSCurrId . '_chart_div',$this->jsConfig);
		return [$oTblDiv, $aLang];
	}

	public static function getDefaultConfig(){
		return [
			'width' => self::WIDTH_LARGE,
			'expanded' => 1,
			'height' => 212,
			'res' => 1,
			'cls' => 'lightCyan',
			'csv' => '1111;3;10000;',
			'dlgHeight' => 435,
			'isResizable' => 0
		];
	}

	public static function showDialog(){
		list($jsFile, $oSelCls) = self::getDialogPrefs();

		list($sType, $iDate, $sRevenueTarget) = explode(";", we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));


// Typ block
		while(strlen($sType) < 4){
			$sType .= '0';
		}
		if($sType === "0000"){
			$sType = "1111";
		}

		$oChbxCustomer = (defined('CUSTOMER_TABLE') && we_base_permission::hasPerm("CAN_SEE_CUSTOMER") ?
			we_html_forms::checkbox(0, $sType{1}, "chbx_type", g_l('cockpit', '[shop_dashboard][cnt_new_customer]'), true, "defaultfont", "", !(defined('CUSTOMER_TABLE') && we_base_permission::hasPerm('CAN_SEE_CUSTOMER')), "", 0, 0) :
			'');

		if(defined('WE_SHOP_VAT_TABLE') && (we_base_permission::hasPerm(["NEW_SHOP_ARTICLE", "DELETE_SHOP_ARTICLE", "EDIT_SHOP_ORDER", "DELETE_SHOP_ORDER", "EDIT_SHOP_PREFS"]))){
			$oChbxOrders = we_html_forms::checkbox(0, $sType{0}, "chbx_type", g_l('cockpit', '[shop_dashboard][cnt_order]'), true, "defaultfont", "", !(defined('WE_SHOP_VAT_TABLE') && we_base_permission::hasPerm("CAN_SEE_SHOP")), "", 0, 0);
			$oChbxAverageOrder = we_html_forms::checkbox(0, $sType{2}, "chbx_type", g_l('cockpit', '[shop_dashboard][revenue_order]'), true, "defaultfont", "", !(defined('WE_SHOP_VAT_TABLE') && we_base_permission::hasPerm('CAN_SEE_SHOP')), "", 0, 0);
			$oChbxTarget = we_html_forms::checkbox(0, $sType{3}, "chbx_type", g_l('cockpit', '[shop_dashboard][revenue_target]'), true, "defaultfont", "", !(defined('WE_SHOP_VAT_TABLE') && we_base_permission::hasPerm('CAN_SEE_SHOP')), "", 0, 0);

			//$revenueTarget = we_html_forms::textinput($value = "",$name = "input_revenueTarget", $text = "Umsatzziel", $uniqid = true, $class = "defaultfont",$onClick = "", $disabled = !(defined('WE_SHOP_VAT_TABLE') && we_base_permission::hasPerm('CAN_SEE_SHOP'), $description = "", $type = 0, $width = 255);
		} else {
			$oChbxOrders = $oChbxAverageOrder = $oChbxTarget = "";
		}

		$oDbTableType = $oChbxOrders . $oChbxCustomer . $oChbxAverageOrder . $oChbxTarget;
//$oDbTableType->setCol(0, 3, null, $revenueTarget);

		$oSctDate = new we_html_select(['name' => "sct_date", 'class' => 'defaultfont', "onchange" => ""]);
		$aLangDate = [g_l('cockpit', '[today]'),
			g_l('cockpit', '[this_week]'),
			g_l('cockpit', '[last_week]'),
			g_l('cockpit', '[this_month]'),
			g_l('cockpit', '[last_month]'),
			g_l('cockpit', '[this_year]'),
			g_l('cockpit', '[last_year]')
		];
		foreach($aLangDate as $k => $v){
			$oSctDate->insertOption($k, $k, $v);
		}
		$oSctDate->selectOption($iDate);

		$parts = [["headline" => g_l('cockpit', '[shop_dashboard][kpi]'),
			"html" => $oDbTableType,
			'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('cockpit', '[shop_dashboard][revenue_target]'),
				"html" => we_html_element::htmlDiv(['style' => "display:block;"], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("revenueTarget", 55, $sRevenueTarget, 255, "", "text", 100, 0) . "&nbsp;&euro;", '', "left", "defaultfont")),
				'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('cockpit', '[date]'),
				"html" => $oSctDate->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('cockpit', '[display]'),
				"html" => $oSelCls->getHTML(),
			]
		];

		$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, "javascript:save();"), we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();"), we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();"));

		$sTblWidget = we_html_multiIconBox::getHTML("shpProps", $parts, 30, $buttons, -1, "", "", "", "Shop");

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[shop_dashboard][headline]'), '', '', $jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/shop.js', '', ['id' => 'loadVarWidget', 'data-widget' => setDynamicVar([
					'sInitNum' => $sRevenueTarget,
			])]), we_html_element::htmlBody(
				["class" => "weDialogBody", "onload" => "init();"
				], we_html_element::htmlForm("", $sTblWidget)));
	}

	public function showPreview(){
		$jsCmd = new we_base_jsCmd();
		$jsCmd->addCmd('initPreview', [
			'id' => $this->newSCurrId,
			'type' => 'shp',
			'tb' => g_l('cockpit', '[shop_dashboard][headline]') . ':&nbsp;' . $this->interval
		]);
		$jsCmd->addCmd('initGauge', $this->newSCurrId . '_chart_div',$this->jsConfig);
		echo we_html_tools::getHtmlTop(g_l('cockpit', '[shop_dashboard][headline]') . '&nbsp;' . $this->interval, '', '', $jsCmd->getCmds(), we_html_element::htmlBody([
				'style' => 'margin:10px 15px;',
				], we_html_element::htmlDiv(["id" => "shp"
					], we_html_element::htmlDiv(['id' => 'shp_data'], $this->shopDashboard)
		)));
	}

}
