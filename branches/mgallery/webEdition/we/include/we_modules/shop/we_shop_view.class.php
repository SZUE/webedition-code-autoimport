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

class we_shop_view{
	var $db;
	var $frameset;
	var $topFrame;
	var $raw;
	private $CLFields = array(); //
	private $classIds = array();

	function __construct($frameset = '', $topframe = 'top.content'){
		$this->db = new DB_WE();
		$this->setFramesetName($frameset);
		$this->setTopFrame($topframe);
		//$this->raw = new weShop();
	}

	//-----------------Init -------------------------------

	function setFramesetName($frameset){
		$this->frameset = $frameset;
	}

	function setTopFrame($frame){
		$this->topFrame = $frame;
	}

	//------------------------------------------------


	function getCommonHiddens($cmds = array()){
		return we_html_element::htmlHiddens(array(
				'cmd' => (isset($cmds['cmd']) ? $cmds['cmd'] : ''),
				'cmdid' => (isset($cmds['cmdid']) ? $cmds['cmdid'] : ''),
				'pnt' => (isset($cmds['pnt']) ? $cmds['pnt'] : ''),
				'tabnr' => (isset($cmds['tabnr']) ? $cmds['tabnr'] : '')
		));
	}

	function getJSTop_tmp(){//taken from old edit_shop_frameset.php
		// grep the last element from the year-set, wich is the current year
		$this->db->query('SELECT DATE_FORMAT(DateOrder,"%Y") AS DateOrd FROM ' . SHOP_TABLE . ' ORDER BY DateOrd');
		while($this->db->next_record()){
			$strs = array($this->db->f("DateOrd"));
			$yearTrans = end($strs);
		}
		// print $yearTrans;
		/// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', 'strFelder', $this->db));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}

		$fe = explode(',', $feldnamen[3]);
		if(empty($classid)){
			$classid = $fe[0];
		}
		//$resultO = count ($fe);
		$resultO = array_shift($fe);

		// whether the resultset is empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name ="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1', '', $this->db);


		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		$out = '
var hot = 0;

parent.document.title = "' . $title . '";

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd(){
	var args = "";

	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]){
		case "new_shop":
			' . $this->topFrame . '.editor.location="<?php echo WE_SHOP_MODULE_DIR; ?>edit_shop_frameset.php?pnt=editor";
			break;
		case "delete_shop":
			if (' . $this->topFrame . '.right && ' . $this->topFrame . '.editor.edbody.hot && ' . $this->topFrame . '.editor.edbody.hot == 1 ) {
				if(confirm("' . g_l('modules_shop', '[del_shop]') . '")){
					' . $this->topFrame . '.editor.edbody.deleteorder();
				}
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
			}
			break;
		case "new_article":
			if (' . $this->topFrame . '.right && ' . $this->topFrame . '.editor.edbody.hot && ' . $this->topFrame . '.editor.edbody.hot == 1 ) {
				top.content.editor.edbody.neuerartikel();
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[no_order_there]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
			break;
		case "revenue_view":
		//FIXME: this is not correct; document doesnt work like this
			' . ($resultD ? $this->topFrame . '.editor.location="' . WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?pnt=editor&top=1&typ=document";' :
				(!empty($resultO) ? $this->topFrame . '.editor.location="' . WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?pnt=editor&top=1&typ=object&ViewClass=' . $classid . '";' :
					$this->topFrame . '.editor.location="' . WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?pnt=editor&top=1&typ=document";')) . '
			break;
		';

		$years = we_shop_shop::getAllOrderYears();
		foreach($years as $cur){
			$out .= '
		case "year' . $cur . '":
			' . $this->topFrame . '.location="' . WE_MODULES_DIR . 'show.php?mod=shop&year=' . $cur . '";
				break;
		';
		}

		$out .= '
		case "pref_shop":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_pref.php","shoppref",-1,-1,470,600,true,true,true,false);
			break;

		case "edit_shop_vats":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_vats.php","edit_shop_vats",-1,-1,500,450,true,false,true,false);
			break;

		case "edit_shop_shipping":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_shipping.php","edit_shop_shipping",-1,-1,700,600,true,false,true,false);
			break;
		case "edit_shop_status":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_status.php","edit_shop_status",-1,-1,700,780,true,true,true,false);
			break;
		case "edit_shop_vat_country":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_vat_country.php","edit_shop_vat_country",-1,-1,700,780,true,true,true,false);
			break;
		case "payment_val":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_payment.inc.php","shoppref",-1,-1,520,720,true,false,true,false);
			break;

		case "edit_shop_categories":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_categories.php","edit_shop_categories",-1,-1,500,450,true,false,true,false);
			break;

		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.opener.top.we_cmd.apply(this, args);

			break;
	}
}
		';

		return we_html_element::jsScript(JS_DIR . 'images.js') . we_html_element::jsScript(JS_DIR . 'windows.js') .
			we_html_element::jsElement($out);
	}

	function getJSTop(){//TODO: is this shop-code or a copy paste from another module?
		return we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
var get_focus = 1;
var activ_tab = 1;
var hot= 0;
var scrollToVal=0;

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "new_raw":
			if(' . $this->topFrame . '.editor.edbody.loaded) {
				' . $this->topFrame . '.hot = 1;
				' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value = arguments[0];
				' . $this->topFrame . '.editor.edbody.document.we_form.cmdid.value = arguments[1];
				' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value = 1;
				' . $this->topFrame . '.editor.edbody.submitForm();
			} else {
				setTimeout(\'we_cmd("new_raw");\', 10);
			}
			break;

		case "delete_raw":
			if(top.content.editor.edbody.document.we_form.cmd.value=="home") return;
			' . (!permissionhandler::hasPerm("DELETE_RAW") ?
					( we_message_reporting::getShowMessageCall(g_l('modules_shop', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)) :
					('
					if (' . $this->topFrame . '.editor.edbody.loaded) {
						if (confirm("' . g_l('modules_shop', '[delete_alert]') . '")) {
							' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=arguments[0];
							' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
							' . $this->topFrame . '.editor.edbody.submitForm();
						}
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}

			')) . '
			break;

		case "save_raw":
			if(top.content.editor.edbody.document.we_form.cmd.value=="home") return;


					if (' . $this->topFrame . '.editor.edbody.loaded) {
							' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=arguments[0];
							' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;

							' . $this->topFrame . '.editor.edbody.submitForm();
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}

			break;

		case "edit_raw":
			' . $this->topFrame . '.hot=0;
			' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=arguments[0];
			' . $this->topFrame . '.editor.edbody.document.we_form.cmdid.value=arguments[1];
			' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
			' . $this->topFrame . '.editor.edbody.submitForm();
		break;
		case "load":
			' . $this->topFrame . '.cmd.location="' . $this->frameset . '?pnt=cmd&pid="+arguments[1]+"&offset="+arguments[2]+"&sort="+arguments[3];
		break;
		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.opener.top.we_cmd.apply(this, args);
	}
}');
	}

	function getJSProperty(){
		return we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
var loaded=0;

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "switchPage":
			document.we_form.cmd.value=arguments[0];
			document.we_form.tabnr.value=arguments[1];
			submitForm();
			break;
		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.content.we_cmd.apply(this, args);
	}
}
function submitForm() {
	var f = self.document.we_form;
	f.target =  (arguments[0]?arguments[0]:"edbody");
	f.action = (arguments[1]?arguments[1]:"' . $this->frameset . '");
	f.method = (arguments[2]?arguments[2]:"post");
	f.submit();
}');
	}

	function getProperties(){
		we_html_tools::protect();
		echo STYLESHEET;

		//$weShopVatRule = weShopVatRule::getShopVatRule();

		$weShopStatusMails = we_shop_statusMails::getShopStatusMails();

		// Get Country and Lanfield Data
		$strFelder = f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLanguage"', 'strFelder', $this->db);
		$this->CLFields = (we_unserialize($strFelder)? : array(
				'stateField' => '-',
				'stateFieldIsISO' => 0,
				'languageField' => '-',
				'languageFieldIsISO' => 0
		));

		// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', 'strFelder', $this->db));
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
			echo we_html_element::jsElement('top.content.deleteEntry(' . $bid . ')') .
			'</head>
			<body class="weEditorBody" onunload="doUnload()">
			<table border="0" cellpadding="0" cellspacing="2" width="300">
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
		$_REQUEST['cid'] = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid, '', $this->db);

		if(($fields = we_unserialize(f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="edit_shop_properties"', '', $this->db)))){
			// we have an array with following syntax:
			// array ( 'customerFields' => array('fieldname ...',...)
			//         'orderCustomerFields' => array('fieldname', ...) )
		} else {
			//unsupported
			t_e('unsupported Shop-Settings found');
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

				$this->db->query('SELECT strSerial FROM ' . SHOP_TABLE . ' WHERE IntID=' . $article);

				if($this->db->num_rows() == 1){
					$this->db->next_record();

					$tmpDoc = we_unserialize($this->db->f('strSerial'));
					$tmpDoc[WE_SHOP_VAT_FIELD_NAME] = $_REQUEST['vat'];

					$this->db->query('UPDATE ' . SHOP_TABLE . ' SET strSerial="' . $this->db->escape(serialize($tmpDoc)) . '" WHERE IntID=' . $article);
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

			$this->db->query('SELECT IntID, IntCustomerID, IntArticleID, strSerial, strSerialOrder, IntQuantity, Price, ' . implode(',', $format) . '	FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . we_base_request::_(we_base_request::INT, 'bid', 0));

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
				echo we_html_element::jsElement('parent.parent.getElementById(iconbar).location.reload();') . '
			</head>
			<body class="weEditorBody" onunload="doUnload()">
			<table border="0" cellpadding="0" cellspacing="2" width="300">
		      <tr>
			<td colspan="2" class="defaultfont">' . we_html_tools::htmlDialogLayout("<span class='defaultfont'>" . g_l('modules_shop', '[orderDoesNotExist]') . "</span>", g_l('modules_shop', '[loscht]')) . '</td>
		      </tr>
		      </table></html>';
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
		<tr height="25">
			<td class="defaultfont" width="86" valign="top" height="25">' . g_l('modules_customer', '[Forname]') . ':</td>
			<td class="defaultfont" valign="top" width="40" height="25"></td>
			<td width="20" height="25"></td>
			<td class="defaultfont" valign="top" colspan="6" height="25">' . $_customer['Forename'] . '</td>
		</tr>';
			}
			if(isset($_customer['Surname'])){
				$customerFieldTable .='
		<tr height="25">
			<td class="defaultfont" width="86" valign="top" height="25">' . g_l('modules_customer', '[Surname]') . ':</td>
			<td class="defaultfont" valign="top" width="40" height="25"></td>
			<td width="20" height="25"></td>
			<td class="defaultfont" valign="top" colspan="6" height="25">' . $_customer['Surname'] . '</td>
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
			<td class="defaultfont" width="86" valign="top" height="25">' . $key . ':</td>
			<td class="defaultfont" valign="top" width="40" height="25"></td>
			<td width="20" height="25"></td>
			<td class="defaultfont" valign="top" colspan="6" height="25">' . $value . '</td>
		</tr>';
				}
			}

			$orderDataTable = '
		<table cellpadding="0" cellspacing="0" border="0" width="99%" class="defaultfont">';
			foreach(we_shop_statusMails::$StatusFields as $field){
				if(!$weShopStatusMails->FieldsHidden[$field]){
					$EMailhandler = $weShopStatusMails->getEMailHandlerCode(substr($field, 4), $_REQUEST[$field]);
					$orderDataTable .= '
			<tr height="25">
				<td class="defaultfont" width="86" valign="top" height="25">' . ($field === 'DateOrder' ? g_l('modules_shop', '[bestellnr]') : '') . '</td>
				<td class="defaultfont" valign="top" width="40" height="25"><b>' . ($field === 'DateOrder' ? $_REQUEST['bid'] : '') . '</b></td>
				<td width="20" height="25">' . we_html_tools::getPixel(34, 15) . '</td>
				<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText[$field] . '</td>
				<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
				<td width="14" class="defaultfont" align="right" height="25">
					<div id="div_Calendar_' . $field . '">' . (($_REQUEST[$field] == $dateform) ? '-' : $_REQUEST[$field]) . '</div>
					<input type="hidden" name="' . $field . '" id="hidden_Calendar_' . $field . '" value="' . (($_REQUEST[$field] == $dateform) ? '-' : $_REQUEST[$field]) . '" />
				</td>
				<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
				<td width="102" valign="top" height="25">' . we_html_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, 'button_Calendar_' . $field) . '</td>
				<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
			</tr>';
				}
			}
			$orderDataTable .= '
			<tr height="5">
				<td class="defaultfont" width="86" valign="top" height="5"></td>
				<td class="defaultfont" valign="top" height="5" width="40"></td>
				<td height="5" width="20"></td>
				<td width="98" class="defaultfont" valign="top" height="5"></td>
				<td height="5"></td>
				<td width="14" class="defaultfont" align="right" valign="top" height="5"></td>
				<td height="5"></td>
				<td width="102" valign="top" height="5"></td>
				<td width="30" height="5">' . we_html_tools::getPixel(30, 5) . '</td>
			</tr>
			<tr height="1">
				<td class="defaultfont" valign="top" colspan="9" bgcolor="grey" height="1">' . we_html_tools::getPixel(14, 1) . '</td>
			</tr>
			<tr>
				<td class="defaultfont" width="86" valign="top"></td>
				<td class="defaultfont" valign="top" width="40"></td>
				<td width="20"></td>
				<td width="98" class="defaultfont" valign="top"></td>
				<td></td>
				<td width="14" class="defaultfont" align="right" valign="top"></td>
				<td></td>
				<td width="102" valign="top"></td>
				<td width="30">' . we_html_tools::getPixel(30, 5) . '</td>
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
			$pixelImg = we_html_tools::getPixel(14, 15);
			$orderTable = '
		<table border="0" cellpadding="0" cellspacing="0" width="99%" class="defaultfont">
			<tr>
				<th class="defaultgray" height="25">' . g_l('modules_shop', '[anzahl]') . '</th>
				<td>' . $pixelImg . '</td>
				<th class="defaultgray" height="25">' . g_l('modules_shop', '[Titel]') . '</th>
				<td>' . $pixelImg . '</td>
				<th class="defaultgray" height="25">' . g_l('modules_shop', '[Beschreibung]') . '</th>
				<td>' . $pixelImg . '</td>
				<th class="defaultgray" height="25">' . g_l('modules_shop', '[Preis]') . '</th>
				<td>' . $pixelImg . '</td>
				<th class="defaultgray" height="25">' . g_l('modules_shop', '[Gesamt]') . '</th>' .
				($calcVat ? '<td>' . $pixelImg . '</td><th class="defaultgray" height="25">' . g_l('modules_shop', '[mwst]') . '</th>' : '' ) . '
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
		<tr><td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td></tr>
		<tr>
			<td class="shopContentfontR">' . "<a href=\"javascript:var anzahl=prompt('" . g_l('modules_shop', '[jsanz]') . "','" . $Quantity[$i] . "'); if(anzahl != null){if(anzahl.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location='" . $_SERVER['SCRIPT_NAME'] . "?pnt=edbody&bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&anzahl='+anzahl;}}\">" . $Quantity[$i] . "</a>" . '</td>
			<td></td>
			<td>' . self::getFieldFromShoparticle($shopArticleObject, WE_SHOP_TITLE_FIELD_NAME, 35) . '</td>
			<td></td>
			<td>' . self::getFieldFromShoparticle($shopArticleObject, WE_SHOP_DESCRIPTION_FIELD_NAME, 45) . '</td>
			<td></td>
			<td class="shopContentfontR">' . "<a href=\"javascript:var preis = prompt('" . g_l('modules_shop', '[jsbetrag]') . "','" . $Price[$i] . "'); if(preis != null ){if(preis.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . "}else{document.location='" . $_SERVER['SCRIPT_NAME'] . "?pnt=edbody&bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&preis=' + preis; } }\">" . we_util_Strings::formatNumber($Price[$i]) . "</a>" . $waehr . '</td>
			<td></td>
			<td class="shopContentfontR">' . we_util_Strings::formatNumber($articlePrice) . $waehr . '</td>' .
					($calcVat ? '<td></td><td class="shopContentfontR small">(' . "<a href=\"javascript:var vat = prompt('" . g_l('modules_shop', '[keinezahl]') . "','" . $articleVat . "'); if(vat != null ){if(vat.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location='" . $_SERVER['SCRIPT_NAME'] . "?pnt=edbody&bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&vat=' + vat; } }\">" . we_util_Strings::formatNumber($articleVat) . "</a>" . '%)</td>' : '') . '
			<td>' . $pixelImg . '</td>
			<td>' . we_html_button::create_button('fa:btn_function_trash,fa-lg fa-trash-o', "javascript:check=confirm('" . g_l('modules_shop', '[jsloeschen]') . "'); if (check){document.location.href='" . $_SERVER['SCRIPT_NAME'] . "?pnt=edbody&bid=" . $_REQUEST["bid"] . "&deleteaarticle=" . $tblOrdersId[$i] . "';}", true, 100, 22, "", "", !permissionhandler::hasPerm("DELETE_SHOP_ARTICLE")) . '</td>
		</tr>';
				// if this article has custom fields or is a variant - we show them in a extra rows
				// add variant.
				if(isset($shopArticleObject['WE_VARIANT']) && $shopArticleObject['WE_VARIANT']){

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
			<td colspan="4" class="shopContentfontR"><strong>' . we_util_Strings::formatNumber($totalPrice) . $waehr . '</strong></td>
		</tr>';

			if($calcVat){ // add Vat to price
				$totalPriceAndVat = $totalPrice;

				if($pricesAreNet){ // prices are net
					$orderTable .= '<tr><td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td></tr>';

					if(isset($orderData[WE_SHOP_SHIPPING]) && isset($shippingCostsNet)){

						$totalPriceAndVat += $shippingCostsNet;
						$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
			<td colspan="4" class="shopContentfontR"><strong><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . we_util_Strings::formatNumber($shippingCostsNet) . $waehr . '</a></strong></td>
			<td></td>
			<td class="shopContentfontR small">(' . we_util_Strings::formatNumber($orderData[WE_SHOP_SHIPPING]['vatRate']) . '%)</td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
		</tr>';
					}
					$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[plusVat]') . '</label>:</td>
			<td colspan="7"></td>
			<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?pnt=edbody&bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
		</tr>';
					foreach($articleVatArray as $vatRate => $sum){
						if($vatRate){
							$totalPriceAndVat += $sum;
							$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR">' . $vatRate . ' %:</td>
			<td colspan="4" class="shopContentfontR">' . we_util_Strings::formatNumber($sum) . $waehr . '</td>
		</tr>';
						}
					}
					$orderTable .= '
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
			<td colspan="4" class="shopContentfontR"><strong>' . we_util_Strings::formatNumber($totalPriceAndVat) . $waehr . '</strong></td>
		</tr>';
				} else { // prices are gros
					$orderTable .= '<tr><td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td></tr>';

					if(isset($orderData[WE_SHOP_SHIPPING]) && isset($shippingCostsGros)){
						$totalPrice += $shippingCostsGros;
						$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
			<td colspan="4" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . we_util_Strings::formatNumber($shippingCostsGros) . $waehr . '</a></td>
			<td></td>
			<td class="shopContentfontR small">(' . we_util_Strings::formatNumber($orderData[WE_SHOP_SHIPPING]['vatRate']) . '%)</td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
			<td colspan="4" class="shopContentfontR"><strong>' . we_util_Strings::formatNumber($totalPrice) . $waehr . '</strong></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>';
					}

					$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[includedVat]') . '</label>:</td>
			<td colspan="7"></td>
			<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?pnt=edbody&bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
		</tr>';
					foreach($articleVatArray as $vatRate => $sum){
						if($vatRate){
							$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR">' . $vatRate . ' %:</td>
			<td colspan="4" class="shopContentfontR">' . we_util_Strings::formatNumber($sum) . $waehr . '</td>
		</tr>';
						}
					}
				}
			} else {

				if(isset($shippingCostsNet)){
					$totalPrice += $shippingCostsNet;

					$orderTable .= '
		<tr>
			<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
			<td colspan="4" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\')">' . we_util_Strings::formatNumber($shippingCostsNet) . $waehr . '</a></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[edit_order][calculate_vat]') . '</label></td>
			<td colspan="7"></td>
			<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?pnt=edbody&bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=1\';" type="checkbox" name="calculateVat" value="1" /></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>
		<tr>
			<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
			<td colspan="4" class="shopContentfontR"><strong>' . we_util_Strings::formatNumber($totalPrice) . $waehr . '</strong></td>
		</tr>
		<tr>
			<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
		</tr>';
				} else {

					$orderTable .= '
		<tr>
			<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[edit_order][calculate_vat]') . '</label></td>
			<td colspan="7"></td>
			<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?pnt=edbody&bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=1\';" type="checkbox" name="calculateVat" value="1" /></td>
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

			$customCartFieldsTable = '<table cellpadding="0" cellspacing="0" border="0" width="99%">
					<tr>
						<th colspan="3" class="defaultgray" height="30">' . g_l('modules_shop', '[order_comments]') . '</th>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>';

			foreach($customCartFields as $key => $value){
				$customCartFieldsTable .= '<tr>
						<td class="defaultfont" valign="top"><b>' . $key . ':</b></td>
						<td>' . $pixelImg . '</td>
						<td class="defaultfont" valign="top">' . nl2br($value) . '</td>
						<td>' . $pixelImg . '</td>
						<td valign="top">' . we_html_button::create_button('image:btn_edit_edit', "javascript:we_cmd('edit_shop_cart_custom_field','" . $key . "');") . '</td>
						<td>' . $pixelImg . '</td>
						<td valign="top">' . we_html_button::create_button('fa:btn_function_trash,fa-lg fa-trash-o', "javascript:check=confirm('" . sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field]'), $key) . "'); if (check) { document.location.href='" . $_SERVER['SCRIPT_NAME'] . "?pnt=edbody&we_cmd[0]=delete_shop_cart_custom_field&bid=" . $_REQUEST["bid"] . "&cartfieldname=" . $key . "'; }") . '</td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>';
			}
			$customCartFieldsTable .= '<tr>
						<td>' . we_html_button::create_button('image:btn_function_plus', "javascript:we_cmd('edit_shop_cart_custom_field');") . '</td>
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
		echo we_html_element::jsScript(LIB_DIR . 'additional/jscalendar/calendar.js') .
			we_html_element::jsScript(LIB_DIR . 'additional/jscalendar/calendar-setup.js') .
			we_html_element::jsScript(WE_INCLUDES_DIR . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/calendar.js') .
			we_html_element::jsScript(JS_DIR . 'images.js') .
			we_html_element::jsScript(JS_DIR . 'windows.js') .
			we_html_element::cssLink(LIB_DIR . 'additional/jscalendar/skins/aqua/theme.css') .
			we_html_element::jsElement('
var dirs = {
		"WE_SHOP_MODULE_DIR": "' . WE_SHOP_MODULE_DIR . '",
		"WE_MODULES_DIR": "' . WE_MODULES_DIR . '",
		"SCRIPT_NAME": "' . $_SERVER['SCRIPT_NAME'] . '"
};
var bid =' . we_base_request::_(we_base_request::INT, 'bid', 0) . ';
var cid =' . we_base_request::_(we_base_request::INT, 'cid', 0) . ';

' . (isset($alertMessage) ?
					we_message_reporting::getShowMessageCall($alertMessage, $alertType) : '')
			) .
			we_html_element::jsScript(JS_DIR . 'we_modules/shop/we_shop_view.js');
			?>

			</head>
			<body class="weEditorBody" onload="hot = 1" onunload="doUnload()">

				<?php
				$parts = array(array(
						'html' => $orderDataTable,
						'space' => 0
					),
					array(
						'html' => $orderTable,
						'space' => 0
					)
				);
				if($customCartFieldsTable){

					$parts[] = array(
						'html' => $customCartFieldsTable,
						'space' => 0
					);
				}

				echo we_html_multiIconBox::getHTML('', '100%', $parts, 30);

				//
				// "Html output for order with articles"
				// ********************************************************************************
			} else { // This order has no more entries
				echo we_html_element::jsElement('
				top.content.editor.location="' . WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?pnt=edbody&deletethisorder=1&bid=' . $_REQUEST["bid"] . '";
				top.content.deleteEntry(' . $_REQUEST['bid'] . ');
			') . '
		</head>
		<body bgcolor="#ffffff">';
			}

			$js = '
// init the used calendars

function CalendarChanged(calObject) {
	// field:
	_field = calObject.params.inputField;
	document.location = "' . $_SERVER['SCRIPT_NAME'] . '?pnt=edbody&bid=' . $_REQUEST['bid'] . '&" + _field.name + "=" + _field.value;
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

	function getJSSubmitFunction(){
		return '';
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
								unset($fieldData);
							}
						}
						unset($fields);
					}

					$variant = strip_tags($_REQUEST[we_base_constants::WE_VARIANT_REQUEST]);
					$serialDoc = we_shop_Basket::getserial($id, ($isObj ? we_shop_shop::OBJECT : we_shop_shop::DOCUMENT), $variant, $customFieldsTmp);

					unset($customFieldsTmp);

					// shop vats must be calculated
					$orderArray = we_unserialize($_strSerialOrder);
					$standardVat = we_shop_vats::getStandardShopVat();

					if(we_shop_category::isCategoryMode()){
						$wedocCategory = ((isset($serialDoc['we_wedoc_Category'])) ? $serialDoc['we_wedoc_Category'] : $serialDoc['wedoc_Category']);
						$stateField = we_shop_vatRule::getStateField();
						$billingCountry = isset($orderArray[WE_SHOP_CART_CUSTOMER_FIELD][$stateField]) && $orderArray[WE_SHOP_CART_CUSTOMER_FIELD][$stateField] ?
							$orderArray[WE_SHOP_CART_CUSTOMER_FIELD][$stateField] : we_shop_category::getDefaultCountry();

						$shopVat = we_shop_category::getShopVatByIdAndCountry((isset($serialDoc[WE_SHOP_CATEGORY_FIELD_NAME]) && $serialDoc[WE_SHOP_CATEGORY_FIELD_NAME] ? $serialDoc[WE_SHOP_CATEGORY_FIELD_NAME] : 0), $wedocCategory, $billingCountry);
					} elseif(isset($serialDoc[WE_SHOP_VAT_FIELD_NAME])){
						$shopVat = we_shop_vats::getShopVATById($serialDoc[WE_SHOP_VAT_FIELD_NAME]);
					}

					if(isset($shopVat) && $shopVat){
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
							'strSerial' => serialize($serialDoc),
							'strSerialOrder' => $_strSerialOrder
					))));
				} else {
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true));
				}

				break;

			case 'add_new_article':
				$shopArticles = array();

				$saveBut = '';
				$cancelBut = we_html_button::create_button('cancel', 'javascript:window.close();');
				$searchBut = we_html_button::create_button('search', 'javascript:searchArticles();');

				// first get all shop documents
				$this->db->query('SELECT c.dat AS shopTitle, l.DID AS documentId FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON l.CID=c.ID JOIN ' . FILE_TABLE . ' f ON f.ID=l.DID' .
					' WHERE l.Name = "' . WE_SHOP_TITLE_FIELD_NAME . '"
							AND l.DocumentTable!="tblTemplates" ' .
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
						$this->db->query('SELECT o.input_' . WE_SHOP_TITLE_FIELD_NAME . ' AS shopTitle, o.OF_ID as objectId
							FROM ' . OBJECT_X_TABLE . $_classId . ' o JOIN ' . OBJECT_FILES_TABLE . ' of ON o.OF_ID=of.ID ' .
							(we_base_request::_(we_base_request::BOOL, 'searchArticle') ?
								' WHERE ' . OBJECT_X_TABLE . $_classId . '.input_' . WE_SHOP_TITLE_FIELD_NAME . '  LIKE "%' . $this->db->escape($searchArticle) . '%"' :
								'')
						);

						while($this->db->next_record()){
							$shopArticles[$this->db->f('objectId') . '_' . we_shop_shop::OBJECT] = $this->db->f('shopTitle') . ' [' . g_l('modules_shop', '[isObj]') . ': ' . $this->db->f('objectId') . ']';
						}
					}
					unset($_classId);
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
						we_html_button::create_button('back', 'javascript:switchEntriesPage(' . ($page - 1) . ');') :
						we_html_button::create_button('back', '#', true, 100, 22, '', '', true));

				$nextBut = (($end_entry) < $AMOUNT_ARTICLES ?
						we_html_button::create_button('next', 'javascript:switchEntriesPage(' . ($page + 1) . ');') :
						we_html_button::create_button('next', '#', true, 100, 22, '', '', true));


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
						'space' => 100,
						'html' => '
		<form name="we_intern_form">' . we_html_tools::hidden('bid', $_REQUEST['bid']) . we_html_tools::hidden('we_cmd[]', 'add_new_article') . '
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td>' . we_class::htmlSelect("add_article", $shopArticlesSelect, 15, we_base_request::_(we_base_request::RAW, 'add_article', ''), false, array("onchange" => "selectArticle(this.options[this.selectedIndex].value)"), 'value', '380') . '</td>
			<td>' . we_html_tools::getPixel(10, 1) . '</td>
			<td valign="top">' . $backBut . '<div style="margin:5px 0"></div>' . $nextBut . '</td>
			</tr>
			<tr>
				<td class="small">' . sprintf(g_l('modules_shop', '[add_article][entry_x_to_y_from_z]'), $start_entry, $end_entry, $AMOUNT_ARTICLES) . '</td>
			</tr>
			</table>',
						'noline' => 1
						) :
						array(
						'headline' => g_l('modules_shop', '[Artikel]'),
						'space' => 100,
						'html' => g_l('modules_shop', '[add_article][empty_articles]')
						)
					)
				);

				if($AMOUNT_ARTICLES > 0 || isset($_REQUEST['searchArticle'])){
					$parts[] = array(
						'headline' => g_l('global', '[search]'),
						'space' => 100,
						'html' => '
			<table border="0" cellpadding="0" cellspacing="0">
				<tr><td>' . we_class::htmlTextInput('searchArticle', 24, we_base_request::_(we_base_request::RAW, 'searchArticle', ''), '', 'id="searchArticle"', 'text', 380) . '</td>
					<td>' . we_html_tools::getPixel(10, 1) . '</td>
					<td>' . $searchBut . '</td>
				</tr>
			</table>
		</form>'
					);
				}

				if(isset($_REQUEST['add_article']) && $_REQUEST['add_article'] != '0'){
					$saveBut = we_html_button::create_button('save', "javascript:document.we_form.submit();window.close();");
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
						'space' => 100,
						'html' => '
							<form name="we_form" target="edbody">' .
						we_html_tools::hidden('bid', $_REQUEST['bid']) .
						we_html_tools::hidden('we_cmd[]', 'add_article') .
						we_html_tools::hidden('add_article', $_REQUEST['add_article']) .
						'<b>' . $model->elements[WE_SHOP_TITLE_FIELD_NAME]['dat'] . '</b>',
						'noline' => 1
					);

					unset($model);

					$parts[] = array(
						'headline' => g_l('modules_shop', '[anzahl]'),
						'space' => 100,
						'html' => we_class::htmlTextInput('anzahl', 24, '', '', 'min="1"', 'number', 380),
						'noline' => 1
					);

					$parts[] = array(
						'headline' => g_l('modules_shop', '[variant]'),
						'space' => 100,
						'html' => we_class::htmlSelect(we_base_constants::WE_VARIANT_REQUEST, $variantOptions, 1, '', false, array(), 'value', 380),
						'noline' => 1
					);

					$parts[] = array(
						'headline' => g_l('modules_shop', '[customField]'),
						'space' => 100,
						'html' => we_class::htmlTextInput('we_customField', 24, '', '', '', 'text', 380) .
						'<br /><span class="small">Eingabe in der Form: <i>name1=wert1;name2=wert2</i></span></form>',
						'noline' => 1
					);

					unset($id);
					unset($type);
					unset($variantData);
					unset($model);
				}


				echo we_html_multiIconBox::getHTML('', '100%', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[add_article][title]')) .
				'</form>
		</body>
		</html>';
				exit;
				break;

			case 'payVat':

				$strSerialOrder = $this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

				$serialOrder = we_unserialize($strSerialOrder);
				$serialOrder[WE_SHOP_CALC_VAT] = we_base_request::_(we_base_request::INT, 'pay', 0);

				// update all orders with this orderId
				if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_calculateVat_success]');
					$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
				} else {
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_calculateVat_error]');
					$alertType = we_message_reporting::WE_MESSAGE_ERROR;
				}
				unset($serialOrder);
				unset($strSerialOrder);
				break;

			case 'delete_shop_cart_custom_field':

				if(isset($_REQUEST['cartfieldname']) && $_REQUEST['cartfieldname']){

					$strSerialOrder = $this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

					$serialOrder = we_unserialize($strSerialOrder);
					unset($serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']]);

					// update all orders with this orderId
					if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
						$alertMessage = sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_success]'), $_REQUEST['cartfieldname']);
						$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
					} else {
						$alertMessage = sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_error]'), $_REQUEST['cartfieldname']);
						$alertType = we_message_reporting::WE_MESSAGE_ERROR;
					}
				}
				unset($strSerialOrder);
				unset($serialOrder);
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
				$saveBut = we_html_button::create_button('save', 'javascript:we_submit();');
				$cancelBut = we_html_button::create_button('cancel', 'javascript:self.close();');


				$val = '';

				if(isset($_REQUEST['cartfieldname']) && $_REQUEST['cartfieldname']){

					$strSerialOrder = $this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');
					$serialOrder = we_unserialize($strSerialOrder);

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
						'space' => 120,
						'noline' => 1
					),
					array(
						'headline' => g_l('modules_shop', '[field_value]'),
						'html' => '<textarea name="cartfieldvalue" style="width: 350; height: 150">' . $val . '</textarea>',
						'space' => 120
					)
				);

				echo we_html_multiIconBox::getHTML('', '100%', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[add_shop_field]'));
				unset($saveBut);
				unset($cancelBut);
				unset($parts);
				unset($val);
				unset($fieldHtml);
				echo '</form></body></html>';
				exit;

			case 'save_shop_cart_custom_field':

				if(isset($_REQUEST['cartfieldname']) && $_REQUEST['cartfieldname']){

					$strSerialOrder = $this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');
					$serialOrder = we_unserialize($strSerialOrder);
					$serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] = htmlentities($_REQUEST['cartfieldvalue']);
					$serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] = $_REQUEST['cartfieldvalue'];

					// update all orders with this orderId
					if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
						//TODO: check JS-adress!!
						$jsCmd = 'top.opener.top.content.tree.doClick(' . $_REQUEST['bid'] . ',"shop","' . SHOP_TABLE . '");' .
							we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_success]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_NOTICE);
					} else {
						$jsCmd = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_error]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_ERROR);
					}
				} else {
					$jsCmd = we_message_reporting::getShowMessageCall(g_l('modules_shop', '[field_empty_js_alert]'), we_message_reporting::WE_MESSAGE_ERROR);
				}

				echo we_html_element::jsElement($jsCmd . 'window.close();') .
				'</head><body></body></html>';
				unset($serialOrder);
				unset($strSerialOrder);
				exit;

			case 'edit_shipping_cost':
				$shopVats = we_shop_vats::getAllShopVATs();
				$shippingVats = array();

				foreach($shopVats as $k => $shopVat){
					$shippingVats[$shopVat->vat] = $shopVat->vat . ' - ' . $shopVat->getNaturalizedText() . ' (' . $shopVat->territory . ')';
				}

				unset($shopVat);
				unset($shopVats);
				$saveBut = we_html_button::create_button('save', 'javascript:document.we_form.submit();self.close();');
				$cancelBut = we_html_button::create_button('cancel', 'javascript:self.close();');

				$strSerialOrder = $this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

				if($strSerialOrder){

					$serialOrder = we_unserialize($strSerialOrder);

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
						'space' => 150,
						'html' => we_class::htmlTextInput('weShipping_costs', 24, $shippingCost),
						'noline' => 1
					),
					array(
						'headline' => g_l('modules_shop', '[edit_shipping_cost][isNet]'),
						'space' => 150,
						'html' => we_class::htmlSelect('weShipping_isNet', array(1 => g_l('global', '[yes]'), 0 => g_l('global', '[no]')), 1, $shippingIsNet),
						'noline' => 1
					),
					array(
						'headline' => g_l('modules_shop', '[edit_shipping_cost][vatRate]'),
						'space' => 150,
						'html' => we_html_tools::htmlInputChoiceField('weShipping_vatRate', $shippingVat, $shippingVats, array(), '', true),
						'noline' => 1
					)
				);


				echo '</head>
						<body class="weDialogBody">
						<form name="we_form" target="edbody">' .
				we_html_tools::hidden('bid', $_REQUEST['bid']) .
				we_html_tools::hidden("we_cmd[]", 'save_shipping_cost') .
				we_html_multiIconBox::getHTML('', '100%', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[edit_shipping_cost][title]')) .
				'</form></body></html>';
				exit;
				break;

			case 'save_shipping_cost':

				$strSerialOrder = $this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');
				$serialOrder = we_unserialize($strSerialOrder);

				if($serialOrder){

					$weShippingCosts = str_replace(',', '.', $_REQUEST['weShipping_costs']);
					$serialOrder[WE_SHOP_SHIPPING]['costs'] = $weShippingCosts;
					$serialOrder[WE_SHOP_SHIPPING]['isNet'] = $_REQUEST['weShipping_isNet'];
					$serialOrder[WE_SHOP_SHIPPING]['vatRate'] = $_REQUEST['weShipping_vatRate'];

					// update all orders with this orderId
					if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
						$alertMessage = g_l('modules_shop', '[edit_order][js_saved_shipping_success]');
						$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
					} else {
						$alertMessage = g_l('modules_shop', '[edit_order][js_saved_shipping_error]');
						$alertType = we_message_reporting::WE_MESSAGE_ERROR;
					}
				}

				unset($strSerialOrder);
				unset($serialOrder);
				break;

			case 'edit_order_customer'; // edit data of the saved customer.
				$saveBut = we_html_button::create_button('save', 'javascript:document.we_form.submit();self.close();');
				$cancelBut = we_html_button::create_button('cancel', 'javascript:self.close();');
				if(!Zend_Locale::hasCache()){
					Zend_Locale::setCache(getWEZendCache());
				}
				// 1st get the customer for this order
				$_customer = $this->getOrderCustomerData($_REQUEST['bid']);
				ksort($_customer);

				$dontEdit = explode(',', we_shop_shop::ignoredEditFields);

				$parts = array(
					array(
						'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[preferences][explanation_customer_odercustomer]'), we_html_tools::TYPE_INFO, 470),
						'space' => 0
					),
					array(
						'headline' => g_l('modules_customer', '[Forname]') . ': ',
						'space' => 150,
						'html' => we_class::htmlTextInput('weCustomerOrder[Forename]', 44, $_customer['Forename']),
						'noline' => 1
					),
					array(
						'headline' => g_l('modules_customer', '[Surname]') . ': ',
						'space' => 150,
						'html' => we_class::htmlTextInput('weCustomerOrder[Surname]', 44, $_customer['Surname']),
						'noline' => 1
					)
				);
				$editFields = array('Forename', 'Surname');

				foreach($_customer as $k => $v){
					if(!in_array($k, $dontEdit) && !is_numeric($k)){
						if(isset($this->CLFields['stateField']) && isset($this->CLFields['stateFieldIsISO']) && $k == $this->CLFields['stateField'] && $this->CLFields['stateFieldIsISO']){
							$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
							$langcode = array_search($lang[0], getWELangs());
							$countrycode = array_search($langcode, getWECountries());
							$countryselect = new we_html_select(array('name' => "weCustomerOrder[$k]", 'size' => 1, 'style' => '{width:280;}', 'class' => 'wetextinput'));

							$topCountries = array_flip(explode(',', WE_COUNTRIES_TOP));
							if(!Zend_Locale::hasCache()){
								Zend_Locale::setCache(getWEZendCache());
							}
							foreach($topCountries as $countrykey => &$countryvalue){
								$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
							}
							unset($countryvalue);
							$shownCountries = array_flip(explode(',', WE_COUNTRIES_SHOWN));
							foreach($shownCountries as $countrykey => &$countryvalue){
								$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
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
								'space' => 150,
								'html' => $countryselect->getHtml(),
								'noline' => 1
							);
						} elseif((isset($this->CLFields['languageField']) && isset($this->CLFields['languageFieldIsISO']) && $k == $this->CLFields['languageField'] && $this->CLFields['languageFieldIsISO'])){
							$frontendL = $GLOBALS['weFrontendLanguages'];
							foreach($frontendL as $lc => &$lcvalue){
								$lccode = explode('_', $lcvalue);
								$lcvalue = $lccode[0];
							}
							unset($countryvalue);
							$languageselect = new we_html_select(array('name' => "weCustomerOrder[$k]", 'size' => 1, 'style' => '{width:280;}', 'class' => 'wetextinput'));
							foreach(g_l('languages', '') as $languagekey => $languagevalue){
								if(in_array($languagekey, $frontendL)){
									$languageselect->addOption($languagekey, $languagevalue);
								}
							}
							$languageselect->selectOption($v);

							$parts[] = array(
								'headline' => $k . ': ',
								'space' => 150,
								'html' => $languageselect->getHtml(),
								'noline' => 1
							);
						} else {
							$parts[] = array(
								'headline' => $k . ': ',
								'space' => 150,
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
				we_html_tools::hidden('bid', $_REQUEST['bid']) .
				we_html_tools::hidden('we_cmd[]', 'save_order_customer') .
				we_html_multiIconBox::getHTML('', '100%', $parts, 30, we_html_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, '', '', false, g_l('modules_shop', '[preferences][customerdata]'), '', 560) .
				'</form>
						</body>
						</html>';
				exit;

			case 'save_order_customer':
				// just get this order and save this userdata in there.
				$_strSerialOrder = $this->getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

				$_orderData = we_unserialize($_strSerialOrder);
				$_customer = $_REQUEST['weCustomerOrder'];
				$_orderData[WE_SHOP_CART_CUSTOMER_FIELD] = $_customer;


				if($this->updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($_orderData))){
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_customer_success]');
					$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
				} else {
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_customer_error]');
					$alertType = we_message_reporting::WE_MESSAGE_ERROR;
				}

				unset($upQuery);
				unset($_customer);
				unset($_orderData);
				unset($_strSerialOrder);
				break;
		}
	}

	function processCommands_back(){
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			case 'new_raw':
				$this->raw = new weShop();
				echo we_html_element::jsElement(
					$this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->raw->Text) . '";' .
					$this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
				);
				break;
			case 'edit_raw':
				$this->raw = new weShop($_REQUEST['cmdid']);
				echo we_html_element::jsElement(
					$this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->raw->Text) . '";' .
					$this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
				);
				break;
			case 'save_raw':
				if($this->raw->filenameNotValid()){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_shop', '[we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}

				$newone = ($this->raw->ID ? false : true);

				$this->raw->save();

				//$ttrow = getHash('SELECT * FROM ' . RAW_TABLE . ' WHERE ID=' . intval($this->raw->ID), $this->db);
				$tt = addslashes($tt ? : $this->raw->Text);
				$js = ($newone ?
						'
var attribs = [];
attribs["icon"]="' . $this->raw->Icon . '";
attribs["id"]="' . $this->raw->ID . '";
attribs["typ"]="item";
attribs["parentid"]="0";
attribs["text"]="' . $tt . '";
attribs["disable"]=0;
attribs["tooltip"]="";' .
						$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));' .
						$this->topFrame . '.drawTree();' :
						$this->topFrame . '.updateEntry(' . $this->raw->ID . ',"' . $tt . '");'
					);
				echo we_html_element::jsElement(
					$js .
					we_message_reporting::getShowMessageCall(g_l('modules_shop', '[raw_saved_ok]'), we_message_reporting::WE_MESSAGE_NOTICE)
				);
				break;
			case 'delete_raw':
				$js = '' . $this->topFrame . '.deleteEntry(' . $this->raw->ID . ');';

				$this->raw->delete();
				$this->raw = new weShop();

				echo we_html_element::jsElement(
					$js .
					we_message_reporting::getShowMessageCall(g_l('modules_shop', '[raw_deleted]'), we_message_reporting::WE_MESSAGE_NOTICE)
				);
				break;
			case 'switchPage':
				break;
			default:
		}

		$_SESSION['weS']['raw_session'] = serialize($this->raw);
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

		if(isset($_REQUEST['page']))
			if(isset($_REQUEST['page'])){
				$this->page = $_REQUEST['page'];
			}
	}

	//some functions from edit_shop_properties

	private static function getFieldFromShoparticle(array $array, $name, $length = 0){
		$val = ( isset($array['we_' . $name]) ? $array['we_' . $name] : (isset($array[$name]) ? $array[$name] : '' ) );

		return ($length && ($length < strlen($val)) ?
				substr($val, 0, $length) . '...' :
				$val);
	}

	private function getOrderCustomerData($orderId, array $strFelder = array()){
		$hash = getHash('SELECT IntCustomerID,strSerialOrder FROM ' . SHOP_TABLE . '	WHERE IntOrderID=' . intval($orderId), $this->db);
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
		return f('SELECT ' . $this->db->escape($field) . ' FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($bid), $field, $this->db);
	}

	private function updateFieldFromOrder($orderId, $fieldname, $value){
		return (bool) $this->db->query('UPDATE ' . SHOP_TABLE . ' SET ' . $this->db->escape($fieldname) . '="' . $this->db->escape($value) . '" WHERE IntOrderID=' . intval($orderId));
	}

}
