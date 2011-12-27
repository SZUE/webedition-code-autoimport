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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

we_html_tools::htmlTop();
print STYLESHEET;


require_once(WE_SHOP_MODULE_DIR . 'weShopVatRule.class.php');
$weShopVatRule = weShopVatRule::getShopVatRule();

require_once(WE_SHOP_MODULE_DIR . 'weShopStatusMails.class.php');
$weShopStatusMails = weShopStatusMails::getShopStatusMails();

// Get Country and Lanfield Data
$strFelder = f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLanguage"', 'strFelder', $DB_WE);
if($strFelder !== ''){
	$CLFields = unserialize($strFelder);
} else{
	$CLFields['stateField'] = '-';
	$CLFields['stateFieldIsISO'] = 0;
	$CLFields['languageField'] = '-';
	$CLFields['languageFieldIsISO'] = 0;
}

function getFieldFromShoparticle($array, $name, $length=0){

	$val = ( isset($array["we_$name"]) ? $array["we_$name"] : (isset($array[$name]) ? $array[$name] : '' ) );

	if($length && ($length < strlen($val))){

		return substr($val, 0, $length) . '...';
	}
	return $val;
}

function getOrderCustomerData($orderId, $orderData=false, $customerId=false, $strFelder=array()){

	if(!$customerId){

		// get customerID from order
		$query = 'SELECT IntCustomerID, strSerialOrder FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($orderId);

		$GLOBALS['DB_WE']->query($query);

		if($GLOBALS['DB_WE']->next_record()){
			$customerId = $GLOBALS['DB_WE']->f('IntCustomerID');
			$strSerialOrder = $GLOBALS['DB_WE']->f('strSerialOrder');
			$orderData = @unserialize($strSerialOrder);
		}
	}

	// get Customer
	$customerDb = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($customerId), $GLOBALS['DB_WE']);

	$customerOrder = (isset($orderData[WE_SHOP_CART_CUSTOMER_FIELD]) ? $orderData[WE_SHOP_CART_CUSTOMER_FIELD] : array());

	// default values are fields saved with order
	$tmpCustomer = array_merge($customerDb, $customerOrder);

	// only fields explicity set with the order are shown here
	if(isset($strFelder) && isset($strFelder['customerFields'])){

		foreach($strFelder['customerFields'] as $k){

			if(isset($customerDb[$k])){
				$tmpCustomer[$k] = $customerDb[$k];
			}
		}
	}

	$_customer = array();

	foreach($tmpCustomer as $k => $v){

		if(!is_int($k)){
			$_customer[$k] = $v;
		}
	}
	return $_customer;
}

function getFieldFromOrder($bid, $field){
	return f('SELECT ' . $DB_WE->escape($field) . ' FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($_REQUEST['bid']), $field, $GLOBALS['DB_WE']);
}

function updateFieldFromOrder($orderId, $fieldname, $value){

	$upQuery = 'UPDATE ' . SHOP_TABLE . ' SET ' . $DB_WE->escape($fieldname) . '="' . $DB_WE->escape($value) . '"WHERE IntOrderID=' . intval($_REQUEST['bid']);

	return ($GLOBALS['DB_WE']->query($upQuery) ? true : false);
}

// config
$feldnamen = explode("|", f("SELECT strFelder from " . ANZEIGE_PREFS_TABLE . " WHERE strDateiname = 'shop_pref'", "strFelder", $DB_WE));

$waehr = "&nbsp;" . htmlspecialchars($feldnamen[0]);
$dbTitlename = "shoptitle";
$dbPreisname = "price";
$numberformat = $feldnamen[2];
$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
$classIds = makeArrayFromCSV($classid);
$mwst = (!empty($feldnamen[1])) ? (($feldnamen[1])) : '';
$notInc = "tblTemplates";

$da = "%d.%m.%Y";
$dateform = "00.00.0000";
$db = "%d.%m.%Y %H:%i";
$datetimeform = "00.00.0000 00:00";

// determine the number format
function numfom($result){
	$result = we_util::std_numberformat($result);
	switch($GLOBALS['numberformat']){
		case 'german':
			return number_format($result, 2, ",", ".");
		case 'french':
			return number_format($result, 2, ",", "&nbsp;");
		case 'english':
			return number_format($result, 2, ".", "");
		case 'swiss':
			return number_format($result, 2, ",", "'");
	}
	return $result;
}

function numfom2($result){
	return rtrim(rtrim($numfom($result), '.00'), ',00');
}

if(isset($_REQUEST['we_cmd'][0])){

	switch($_REQUEST['we_cmd'][0]){

		case 'add_article':

			if($_REQUEST["anzahl"] > 0){

				// add complete article / object here - inclusive request fields
				$_strSerialOrder = getFieldFromOrder($_REQUEST["bid"], 'strSerialOrder');

				$tmp = explode("_", $_REQUEST["add_article"]);
				$isObj = ($tmp[1] == "o");

				$id = $tmp[0];

				// check for variant or customfields
				$customFieldsTmp = array();
				if(strlen($_REQUEST['we_customField'])){

					$fields = explode(';', trim($_REQUEST['we_customField']));

					if(is_array($fields)){
						foreach($fields as $field){

							$fieldData = explode('=', $field);

							if(is_array($fieldData) && sizeof($fieldData) == 2){
								$customFieldsTmp[trim($fieldData[0])] = trim($fieldData[1]);
							}
							unset($fieldData);
						}
					}
					unset($fields);
				}


				if($isObj){
					$serialDoc = Basket::getserial($id, 'o', $_REQUEST['we_variant'], $customFieldsTmp);
				} else{
					$serialDoc = Basket::getserial($id, 'w', $_REQUEST['we_variant'], $customFieldsTmp);
				}

				unset($customFieldsTmp);

				// shop vats must be calculated
				require_once(WE_SHOP_MODULE_DIR . 'weShopVats.class.php');
				$standardVat = weShopVats::getStandardShopVat();

				if(isset($serialDoc[WE_SHOP_VAT_FIELD_NAME])){
					$shopVat = weShopVats::getShopVATById($serialDoc[WE_SHOP_VAT_FIELD_NAME]);
				}

				if(isset($shopVat) && $shopVat){
					$serialDoc[WE_SHOP_VAT_FIELD_NAME] = $shopVat->vat;
				} else{
					if($standardVat){
						$serialDoc[WE_SHOP_VAT_FIELD_NAME] = $standardVat->vat;
					}
				}

				$preis = getFieldFromShoparticle($serialDoc, 'price');

				// now insert article to order:
				$DB_WE->query("SELECT IntOrderID, IntCustomerID, DateOrder, DateShipping, Datepayment,IntPayment_Type FROM " . SHOP_TABLE . " WHERE IntOrderID = " . abs($_REQUEST["bid"]));
				$DB_WE->next_record();

				$sql = 'INSERT INTO ' . SHOP_TABLE . '
						(IntArticleID,IntQuantity,Price,IntOrderID, IntCustomerID, DateOrder, DateShipping, Datepayment,IntPayment_Type,strSerial,strSerialOrder)
					VALUES'.
						"(\"$id\", \"" . $_REQUEST["anzahl"] . "\",\"" . $preis . "\", \"" . $DB_WE->f("IntOrderID") . "\", \"" . $DB_WE->f("IntCustomerID") . "\",\"" . $DB_WE->f("DateOrder") . "\",\"" . $DB_WE->f("DateShipping") . "\",\"" . $DB_WE->f("Datepayment") . "\",\"" . $DB_WE->f("IntPayment_Type") . "\",'" . addslashes(serialize($serialDoc)) . "', '$_strSerialOrder')";
				$DB_WE->query($sql);
			}

			break;

		case 'add_new_article':
			$shopArticles = array();
			$shopArticlesSelect = array();
			$parts = array();


			$saveBut = '';
			$cancelBut = we_button::create_button('cancel', "javascript:window.close();");
			$searchBut = we_button::create_button('search', "javascript:searchArticles();");

			// first get all shop documents
			$query = 'SELECT ' . CONTENT_TABLE . '.dat AS shopTitle, ' . LINK_TABLE . '.DID AS documentId FROM ' . CONTENT_TABLE . ', ' . LINK_TABLE . ', ' . FILE_TABLE .
				' WHERE ' . FILE_TABLE . '.ID = ' . LINK_TABLE . '.DID
					AND ' . LINK_TABLE . '.CID = ' . CONTENT_TABLE . '.ID
					AND ' . LINK_TABLE . '.Name = "shoptitle"
					AND ' . LINK_TABLE . '.DocumentTable != "tblTemplates"
			';

			if(isset($_REQUEST['searchArticle']) && $_REQUEST['searchArticle']){
				$query .= ' AND ' . CONTENT_TABLE . '.Dat LIKE "%' . $DB_WE->escape($_REQUEST['searchArticle']) . '%"';
			}

			$DB_WE->query($query);

			while($DB_WE->next_record()) {
				$shopArticles[$DB_WE->f('documentId') . '_d'] = $DB_WE->f("shopTitle") . ' [' . $DB_WE->f("documentId") . ']' . g_l('modules_shop', '[isDoc]');
			}

			if(defined('OBJECT_TABLE')){
				// now get all shop objects
				foreach($classIds as $_classId){
					$_classId = intval($_classId);
					$query = '
						SELECT  ' . OBJECT_X_TABLE . $_classId . '.input_shoptitle as shopTitle, ' . OBJECT_X_TABLE . $_classId . '.OF_ID as objectId
						FROM ' . OBJECT_X_TABLE . $_classId . ', ' . OBJECT_FILES_TABLE . '
						WHERE ' . OBJECT_X_TABLE . $_classId . '.OF_ID = ' . OBJECT_FILES_TABLE . '.ID
							AND ' . OBJECT_X_TABLE . $_classId . '.ID = ' . OBJECT_FILES_TABLE . '.ObjectID
					';

					if(isset($_REQUEST['searchArticle']) && $_REQUEST['searchArticle']){
						$query .= '
							AND ' . OBJECT_X_TABLE . $_classId . '.input_shoptitle  LIKE "%' . $DB_WE->escape($_REQUEST['searchArticle']) . '%"';
					}

					$DB_WE->query($query);
					while($DB_WE->next_record()) {
						$shopArticles[$DB_WE->f('objectId') . '_o'] = $DB_WE->f('shopTitle') . ' [' . $DB_WE->f('objectId') . ']' . g_l('modules_shop', '[isObj]');
					}
				}
				unset($_classId);
			}

			unset($query);

			// <<< determine which articles should be shown ...

			asort($shopArticles);
			$MAX_PER_PAGE = 15;
			$AMOUNT_ARTICLES = sizeof($shopArticles);



			$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;

			$shopArticlesParts = array_chunk($shopArticles, $MAX_PER_PAGE, true);

			$start_entry = $page * $MAX_PER_PAGE + 1;
			$end_entry = (($page * $MAX_PER_PAGE + $MAX_PER_PAGE < $AMOUNT_ARTICLES) ? ($page * $MAX_PER_PAGE + $MAX_PER_PAGE) : $AMOUNT_ARTICLES );

			if($start_entry - $MAX_PER_PAGE > 0){
				$backBut = we_button::create_button('back', "javascript:switchEntriesPage(" . ($page - 1) . ");");
			} else{
				$backBut = we_button::create_button('back', "#", true, 100, 22, '', '', true);
			}

			if(($end_entry) < $AMOUNT_ARTICLES){
				$nextBut = we_button::create_button('next', "javascript:switchEntriesPage(" . ($page + 1) . ");");
			} else{
				$nextBut = we_button::create_button('next', "#", true, 100, 22, '', '', true);
			}

			$shopArticlesSelect = $shopArticlesParts[$page];
			asort($shopArticlesSelect);

			// determine which articles should be shown >>>


			print we_html_element::jsElement('
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
		}

	') . '
</head>
<body class="weDialogBody">';

			if($AMOUNT_ARTICLES > 0){

				array_push($parts, array(
					'headline' => g_l('modules_shop', '[Artikel]'),
					'space' => 100,
					'html' => '<form name="we_intern_form">' . we_html_tools::hidden('bid', $_REQUEST['bid']) . we_html_tools::hidden("we_cmd[]", 'add_new_article') . '
					<table border="0" cellpadding="0" cellspacing="0">
					<tr><td>' . we_class::htmlSelect("add_article", $shopArticlesSelect, 15, (isset($_REQUEST['add_article']) ? $_REQUEST['add_article'] : ''), false, 'onchange="selectArticle(this.options[this.selectedIndex].value);"', 'value', '380') . '</td>
					<td>' . we_html_tools::getPixel(10, 1) . '</td>
					<td valign="top">' . $backBut . '<div style="margin:5px 0"></div>' . $nextBut . '</td>
					</tr>
					<tr>
						<td class="small">' . sprintf(g_l('modules_shop', '[add_article][entry_x_to_y_from_z]'), $start_entry, $end_entry, $AMOUNT_ARTICLES) . '</td>
					</tr>
					</table>',
					'noline' => 1
					)
				);
			} else{
				array_push($parts, array(
					'headline' => g_l('modules_shop', '[Artikel]'),
					'space' => 100,
					'html' => g_l('modules_shop', '[add_article][empty_articles]')
					)
				);
			}

			if($AMOUNT_ARTICLES > 0 || isset($_REQUEST['searchArticle'])){
				array_push($parts, array(
					'headline' => g_l('global', '[search]'),
					'space' => 100,
					'html' => '<table border="0" cellpadding="0" cellspacing="0">
					<tr><td>' . we_class::htmlTextInput('searchArticle', 24, ( isset($_REQUEST['searchArticle']) ? $_REQUEST['searchArticle'] : ''), '', 'id="searchArticle"', 'text', 380) . '</td>
					<td>' . we_html_tools::getPixel(10, 1) . '</td>
					<td>' . $searchBut . '</td>
					</tr>
					</table>
					</form>'
					)
				);
			}

			if(isset($_REQUEST['add_article']) && $_REQUEST['add_article'] != '0'){

				$saveBut = we_button::create_button('save', "javascript:document.we_form.submit();window.close();");

				require_once(WE_SHOP_MODULE_DIR . 'weShopVariants.inc.php');

				$articleInfo = explode('_', $_REQUEST['add_article']);

				$id = $articleInfo[0];
				$type = $articleInfo[1];

				$variantData = array();

				$variantOptions = array();
				$variantOptions['-'] = '-';

				if($type == 'o'){

					require_once(WE_OBJECT_MODULE_DIR . 'we_objectFile.inc.php');

					$model = new we_objectFile();
					$model->initByID($id, OBJECT_FILES_TABLE);

					$variantData = weShopVariants::getVariantData($model, '-');
				} else{

					$model = new we_webEditionDocument();
					$model->initByID($id);

					$variantData = weShopVariants::getVariantData($model, '-');
				}

				$sizeVariantData = sizeof($variantData);
				reset($variantData);
				if($sizeVariantData > 1){
					for($i = 0; $i < $sizeVariantData; $i++){
						reset($variantData[$i]);
						list($key, $varData) = each($variantData[$i]);
						if($key != '-'){
							$variantOptions[$key] = $key;
						}
					}
				}

				array_push($parts, array(
					'headline' => g_l('modules_shop', '[Artikel]'),
					'space' => 100,
					'html' => '
					<form name="we_form" target="edbody">
					' . we_html_tools::hidden('bid', $_REQUEST['bid']) .
					we_html_tools::hidden("we_cmd[]", 'add_article') .
					we_html_tools::hidden("add_article", $_REQUEST['add_article']) .
					'
					<b>' . $model->elements['shoptitle']['dat'] . '</b>',
					'noline' => 1
					)
				);

				unset($model);

				array_push($parts, array(
					'headline' => g_l('modules_shop', '[Anzahl]'),
					'space' => 100,
					'html' => we_class::htmlTextInput('anzahl', 24, '', '', '', 'text', 380),
					'noline' => 1
					)
				);

				array_push($parts, array(
					'headline' => g_l('modules_shop', '[variant]'),
					'space' => 100,
					'html' => we_class::htmlSelect('we_variant', $variantOptions, 1, '', false, '', 'value', 380),
					'noline' => 1
					)
				);

				array_push($parts, array(
					'headline' => g_l('modules_shop', '[customField]'),
					'space' => 100,
					'html' => we_class::htmlTextInput('we_customField', 24, '', '', '', 'text', 380) .
					'<br /><span class="small">Eingabe in der Form: <i>name1=wert1;name2=wert2</i></span></form>',
					'noline' => 1
					)
				);

				unset($id);
				unset($type);
				unset($variantData);
				unset($model);
			}


			print we_multiIconBox::getHTML("", "100%", $parts, 30, we_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, "", "", false, g_l('modules_shop', '[add_article][title]'));
			print '
</form>
</body>
</html>';
			unset($saveBut);
			unset($cancelBut);
			unset($selectBut);
			unset($parts);
			unset($shopArticles);
			exit;
			break;

		case 'payVat':

			$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

			$serialOrder = @unserialize($strSerialOrder);
			$serialOrder[WE_SHOP_CALC_VAT] = $_REQUEST['pay'] == '1' ? 1 : 0;

			// update all orders with this orderId
			if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
				$alertMessage = g_l('modules_shop', '[edit_order][js_saved_calculateVat_success]');
				$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
			} else{
				$alertMessage = g_l('modules_shop', '[edit_order][js_saved_calculateVat_error]');
				$alertType = we_message_reporting::WE_MESSAGE_ERROR;
			}
			unset($serialOrder);
			unset($strSerialOrder);
			break;

		case 'delete_shop_cart_custom_field':

			if(isset($_REQUEST['cartfieldname']) && $_REQUEST['cartfieldname']){

				$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

				$serialOrder = @unserialize($strSerialOrder);
				unset($serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']]);

				// update all orders with this orderId
				if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
					$alertMessage = sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_success]'), $_REQUEST['cartfieldname']);
					$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
				} else{
					$alertMessage = sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field_error]'), $_REQUEST['cartfieldname']);
					$alertType = we_message_reporting::WE_MESSAGE_ERROR;
				}
			}
			unset($strSerialOrder);
			unset($serialOrder);
			break;

		case 'edit_shop_cart_custom_field':

			print '
			' . we_html_element::jsElement('
	function we_submit() {
		elem = document.getElementById("cartfieldname");

		if (elem && elem.value) {
			document.we_form.submit();
		} else {
			' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[field_empty_js_alert]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		}

	}
			') . '
			</head>
<body class="weDialogBody">
<form name="we_form">
<input type="hidden" name="bid" value="' . $_REQUEST['bid'] . '" />
<input type="hidden" name="we_cmd[0]" value="save_shop_cart_custom_field" />
';
			$saveBut = we_button::create_button('save', "javascript:we_submit();");
			$cancelBut = we_button::create_button('cancel', "javascript:self.close();");

			$parts = array();

			$val = '';

			if(isset($_REQUEST['cartfieldname']) && $_REQUEST['cartfieldname']){

				$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');
				$serialOrder = @unserialize($strSerialOrder);

				$val = $serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] ? $serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] : '';

				$fieldHtml = $_REQUEST['cartfieldname'] . '<input type="hidden" name="cartfieldname" id="cartfieldname" value="' . $_REQUEST['cartfieldname'] . '" />';
			} else{
				$fieldHtml = we_html_tools::htmlTextInput('cartfieldname', 24, '', '', 'id="cartfieldname"');
			}

			// make input field, for name or textfield
			array_push($parts, array(
				'headline' => g_l('modules_shop', '[field_name]'),
				'html' => $fieldHtml,
				'space' => 120,
				'noline' => 1
				)
			);
			array_push($parts, array(
				'headline' => g_l('modules_shop', '[field_value]'),
				'html' => '<textarea name="cartfieldvalue" style="width: 350; height: 150">' . $val . '</textarea>',
				'space' => 120
				)
			);

			print we_multiIconBox::getHTML("", "100%", $parts, 30, we_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, "", "", false, g_l('modules_shop', '[add_shop_field]'));
			unset($saveBut);
			unset($canelBut);
			unset($parts);
			unset($val);
			unset($fieldHtml);
			print '
				</form></body>
</html>';
			exit;
			break;

		case 'save_shop_cart_custom_field':

			if(isset($_REQUEST['cartfieldname']) && $_REQUEST['cartfieldname']){

				$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

				$serialOrder = @unserialize($strSerialOrder);
				$serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] = htmlentities($_REQUEST['cartfieldvalue']);
				$serialOrder[WE_SHOP_CART_CUSTOM_FIELD][$_REQUEST['cartfieldname']] = $_REQUEST['cartfieldvalue'];

				// update all orders with this orderId
				if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
					$jsCmd = '
					top.opener.top.content.shop_tree.doClick(' . $_REQUEST['bid'] . ',"shop","' . SHOP_TABLE . '");
					' . we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_success]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_NOTICE) . '
					window.close();
					';
				} else{
					$jsCmd = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_shop', '[edit_order][js_saved_cart_field_error]'), $_REQUEST['cartfieldname']), we_message_reporting::WE_MESSAGE_ERROR) . '
					window.close();
					';
				}
			} else{

				$jsCmd = we_message_reporting::getShowMessageCall(g_l('modules_shop', '[field_empty_js_alert]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					window.close();
					';
			}



			print '
			' . we_html_element::jsElement($jsCmd) . '
			</head>
<body></body>
</html>';
			unset($serialOrder);
			unset($strSerialOrder);
			exit;
			break;

		case 'edit_shipping_cost':
			require_once(WE_SHOP_MODULE_DIR . 'weShopVats.class.php');

			$shopVats = weShopVats::getAllShopVATs();
			$shippingVats = array();

			foreach($shopVats as $k => $shopVat){
				if(strlen($shopVat->vat . ' - ' . $shopVat->text) > 20){
					$shippingVats[$shopVat->vat] = substr($shopVat->vat . ' - ' . $shopVat->text, 0, 16) . ' ...';
				} else{
					$shippingVats[$shopVat->vat] = $shopVat->vat . ' - ' . $shopVat->text;
				}
			}

			unset($shopVat);
			unset($shopVats);
			$saveBut = we_button::create_button('save', "javascript:document.we_form.submit();self.close();");
			$cancelBut = we_button::create_button('cancel', "javascript:self.close();");

			$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

			if($strSerialOrder){

				$serialOrder = @unserialize($strSerialOrder);

				$shippingCost = $serialOrder[WE_SHOP_SHIPPING]['costs'];
				$shippingIsNet = $serialOrder[WE_SHOP_SHIPPING]['isNet'];
				$shippingVat = $serialOrder[WE_SHOP_SHIPPING]['vatRate'];
			} else{

				$shippingCost = '0';
				$shippingIsNet = '1';
				$shippingVat = '19';
			}

			$parts = array();
			array_push($parts, array(
				'headline' => g_l('modules_shop', '[edit_order][shipping_costs]'),
				'space' => 150,
				'html' => we_class::htmlTextInput("weShipping_costs", 24, $shippingCost),
				'noline' => 1
				)
			);

			array_push($parts, array(
				'headline' => g_l('modules_shop', '[edit_shipping_cost][isNet]'),
				'space' => 150,
				'html' => we_class::htmlSelect("weShipping_isNet", array('1' => g_l('global', '[yes]'), '0' => g_l('global', '[no]')), 1, $shippingIsNet),
				'noline' => 1
				)
			);

			array_push($parts, array(
				'headline' => g_l('modules_shop', '[edit_shipping_cost][vatRate]'),
				'space' => 150,
				'html' => we_getInputChoiceField("weShipping_vatRate", $shippingVat, $shippingVats, array(), '', true),
				'noline' => 1
				)
			);


			print '
				</head>
				<body class="weDialogBody">
				<form name="we_form" target="edbody">
				' . we_html_tools::hidden('bid', $_REQUEST['bid']) .
				we_html_tools::hidden("we_cmd[]", 'save_shipping_cost');
			print we_multiIconBox::getHTML("", "100%", $parts, 30, we_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, "", "", false, g_l('modules_shop', '[edit_shipping_cost][title]'));
			print '
				</form>
				</body>
				</html>';
			exit;
			break;

		case 'save_shipping_cost':

			$strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');
			$serialOrder = @unserialize($strSerialOrder);

			if($serialOrder){

				$weShippingCosts = str_replace(",", ".", $_REQUEST['weShipping_costs']);
				$serialOrder[WE_SHOP_SHIPPING]['costs'] = $weShippingCosts;
				$serialOrder[WE_SHOP_SHIPPING]['isNet'] = $_REQUEST['weShipping_isNet'];
				$serialOrder[WE_SHOP_SHIPPING]['vatRate'] = $_REQUEST['weShipping_vatRate'];

				// update all orders with this orderId
				if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($serialOrder))){
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_shipping_success]');
					$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
				} else{
					$alertMessage = g_l('modules_shop', '[edit_order][js_saved_shipping_error]');
					$alertType = we_message_reporting::WE_MESSAGE_ERROR;
				}
			}

			unset($strSerialOrder);
			unset($serialOrder);
			break;

		case 'edit_order_customer'; // edit data of the saved customer.
			$saveBut = we_button::create_button('save', "javascript:document.we_form.submit();self.close();");
			$cancelBut = we_button::create_button('cancel', "javascript:self.close();");

			// 1st get the customer for this order
			$_customer = getOrderCustomerData($_REQUEST['bid']);
			ksort($_customer);

			$dontEdit = array('ID', 'Username', 'Password', 'MemberSince', 'LastLogin', 'LastAccess', 'ParentID', 'Path', 'IsFolder', 'Icon', 'Text', 'Forename', 'Surname');

			$parts = array();
			array_push($parts, array(
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[preferences][explanation_customer_odercustomer]'), 2, 470),
				'space' => 0
				)
			);
			$editFields = array();


			array_push($parts, array(
				'headline' => g_l('modules_customer', '[Forname]') . ": ",
				'space' => 150,
				'html' => we_class::htmlTextInput("weCustomerOrder[Forename]", 44, $_customer['Forename']),
				'noline' => 1
				)
			);
			$editFields[] = 'Forename';

			array_push($parts, array(
				'headline' => g_l('modules_customer', '[Surname]') . ": ",
				'space' => 150,
				'html' => we_class::htmlTextInput("weCustomerOrder[Surname]", 44, $_customer['Surname']),
				'noline' => 1
				)
			);
			$editFields[] = 'Surname';

			foreach($_customer as $k => $v){
				if(!in_array($k, $dontEdit)){
					if(isset($CLFields['stateField']) && isset($CLFields['stateFieldIsISO']) && $k == $CLFields['stateField'] && $CLFields['stateFieldIsISO']){
						$lang = explode('_', $GLOBALS["WE_LANGUAGE"]);
						$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
						$countrycode = array_search($langcode, $GLOBALS['WE_LANGS_COUNTRIES']);
						$countryselect = new we_html_select(array("name" => "weCustomerOrder[$k]", "size" => "1", "style" => "{width:280;}", "class" => "wetextinput"));

						if(defined("WE_COUNTRIES_TOP")){
							$topCountries = explode(',', WE_COUNTRIES_TOP);
						} else{
							$topCountries = explode(',', "DE,AT,CH");
						}
						$topCountries = array_flip($topCountries);
						foreach($topCountries as $countrykey => &$countryvalue){
							$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
						}
						if(defined("WE_COUNTRIES_SHOWN")){
							$shownCountries = explode(',', WE_COUNTRIES_SHOWN);
						} else{
							$shownCountries = explode(',', "BE,DK,FI,FR,GR,IE,IT,LU,NL,PT,SE,ES,GB,EE,LT,MT,PL,SK,SI,CZ,HU,CY");
						}
						$shownCountries = array_flip($shownCountries);
						foreach($shownCountries as $countrykey => &$countryvalue){
							$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
						}
						$oldLocale = setlocale(LC_ALL, NULL);
						setlocale(LC_ALL, $langcode . '_' . $countrycode . '.UTF-8');
						asort($topCountries, SORT_LOCALE_STRING);
						asort($shownCountries, SORT_LOCALE_STRING);
						setlocale(LC_ALL, $oldLocale);

						$content = '';
						if(defined('WE_COUNTRIES_DEFAULT') && WE_COUNTRIES_DEFAULT != ''){
							$countryselect->addOption('--', CheckAndConvertISObackend(WE_COUNTRIES_DEFAULT));
						}
						foreach($topCountries as $countrykey => &$countryvalue){
							$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
						}
						$countryselect->addOption('-', '----', array("disabled" => "disabled"));
						//$content.='<option value="-" disabled="disabled">----</option>'."\n";
						foreach($shownCountries as $countrykey => &$countryvalue){
							$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
						}

						$countryselect->selectOption($v);

						array_push($parts, array(
							'headline' => "$k: ",
							'space' => 150,
							'html' => $countryselect->getHtml(),
							'noline' => 1
							)
						);
					} elseif((isset($CLFields['languageField']) && isset($CLFields['languageFieldIsISO']) && $k == $CLFields['languageField'] && $CLFields['languageFieldIsISO'])){
						$frontendL = $GLOBALS["weFrontendLanguages"];
						foreach($frontendL as $lc => &$lcvalue){
							$lccode = explode('_', $lcvalue);
							$lcvalue = $lccode[0];
						}
						$languageselect = new we_html_select(array("name" => "weCustomerOrder[$k]", "size" => "1", "style" => "{width:280;}", "class" => "wetextinput"));
						foreach(g_l('languages', '') as $languagekey => $languagevalue){
							if(in_array($languagekey, $frontendL)){
								$languageselect->addOption($languagekey, $languagevalue);
							}
						}
						$languageselect->selectOption($v);

						array_push($parts, array(
							'headline' => "$k: ",
							'space' => 150,
							'html' => $languageselect->getHtml(),
							'noline' => 1
							)
						);
					} else{
						array_push($parts, array(
							'headline' => "$k: ",
							'space' => 150,
							'html' => we_class::htmlTextInput("weCustomerOrder[$k]", 44, $v),
							'noline' => 1
							)
						);
					}
					$editFields[] = $k;
				}
			}

			print '
				</head>
				<body class="weDialogBody">
				<form name="we_form" target="edbody">
				' . we_html_tools::hidden('bid', $_REQUEST['bid']) .
				we_html_tools::hidden("we_cmd[]", 'save_order_customer');
			print we_multiIconBox::getHTML("", "100%", $parts, 30, we_button::position_yes_no_cancel($saveBut, '', $cancelBut), -1, "", "", false, g_l('modules_shop', '[preferences][customerdata]'), "", 560);
			print '
				</form>
				</body>
				</html>';
			exit;
			break;

		case 'save_order_customer':

			// just get this order and save this userdata in there.

			$_strSerialOrder = getFieldFromOrder($_REQUEST['bid'], 'strSerialOrder');

			$_orderData = @unserialize($_strSerialOrder);
			$_customer = $_REQUEST['weCustomerOrder'];

			$_orderData[WE_SHOP_CART_CUSTOMER_FIELD] = $_customer;


			if(updateFieldFromOrder($_REQUEST['bid'], 'strSerialOrder', serialize($_orderData))){
				$alertMessage = g_l('modules_shop', '[edit_order][js_saved_customer_success]');
				$alertType = we_message_reporting::WE_MESSAGE_NOTICE;
			} else{
				$alertMessage = g_l('modules_shop', '[edit_order][js_saved_customer_error]');
				$alertType = we_message_reporting::WE_MESSAGE_ERROR;
			}

			unset($query);
			unset($upQuery);
			unset($_customer);
			unset($_orderData);
			unset($_strSerialOrder);
			break;
	}
}


we_html_tools::htmlTop();

print STYLESHEET;


if(isset($_REQUEST["deletethisorder"])){

	$DB_WE->query("DELETE FROM " . SHOP_TABLE . " WHERE IntOrderID = " . $_REQUEST["bid"]);
	echo we_html_element::jsElement('
	top.content.deleteEntry(' . $_REQUEST["bid"] . ')') . '
	</head>
	<body class="weEditorBody" onunload="doUnload()">
	<table border="0" cellpadding="0" cellspacing="2" width="300">
      <tr>
        <td colspan="2" class="defaultfont">' . we_html_tools::htmlDialogLayout("<span class='defaultfont'>" . g_l('modules_shop', '[geloscht]') . "</span>", g_l('modules_shop', '[loscht]')) . '</td>
      </tr>
      </table></html>';
	exit;
}

if(isset($_REQUEST["deleteaartikle"])){

	$DB_WE->query("DELETE FROM " . SHOP_TABLE . " WHERE IntID = " . $_REQUEST["deleteaartikle"]);
	$DB_WE->query("SELECT IntID from " . SHOP_TABLE . " WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$l = $DB_WE->num_rows();
	if($l < 1){
		$letzerartikel = 1;
	}
}
// Get Customer data
$query = "SELECT IntID, IntCustomerID	FROM " . SHOP_TABLE . "	WHERE IntOrderID = " . intval($_REQUEST["bid"]);

$DB_WE->query($query);
$DB_WE->next_record();

// get all needed information for order-data
$_REQUEST["cid"] = $DB_WE->f("IntCustomerID");

$DB_WE->query(
	'SELECT strFelder
		FROM ' . ANZEIGE_PREFS_TABLE . '
		WHERE strDateiname = "edit_shop_properties"'
);

$DB_WE->next_record();

$strFelder = $DB_WE->f("strFelder");

if($fields = @unserialize($strFelder)){
	// we have an array with following syntax:
	// array ( 'customerFields' => array('fieldname ...',...)
	//         'orderCustomerFields' => array('fieldname', ...) )
} else{

	$fields['customerFields'] = array();
	$fields['orderCustomerFields'] = array();

	// the save format used to be ...
	// Vorname:tblWebUser||Forename,Nachname:tblWebUser||Surname,Contact/Address1:tblWebUser||Contact_Address1,Contact/Address1:tblWebUser||Contact_Address1,...
	$_fieldInfos = explode(",", $strFelder);

	foreach($_fieldInfos as $_fieldInfo){

		$tmp1 = explode('||', $_fieldInfo);
		$tmp2 = explode(':', $tmp1[0]);

		$_fieldname = $tmp1[1];
		$_titel = $tmp2[0];
		$_tbl = $tmp2[1];

		if($_tbl != 'webE'){
			$fields['customerFields'][] = $_fieldname;
		}
	}
	$fields['customerFields'] = array_unique($fields['customerFields']);

	unset($_tmpEntries);
}

// >>>> Getting customer data
//$_customer = getOrderCustomerData(0, $orderData, $_REQUEST['cid'], $fields);
$_customer = getOrderCustomerData(0, 0, $_REQUEST['cid'], $fields);
// <<<< End of getting customer data




if(isset($_REQUEST["SendMail"])){
	$weShopStatusMails->sendEMail($_REQUEST["SendMail"], $_REQUEST["bid"], $_customer);
}

if(isset($_REQUEST["DatePayment"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DatePayment"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DatePayment='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('Payment', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateConfirmation"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateConfirmation"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateConfirmation='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('Confirmation', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomA"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomA"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomA='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomA', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomB"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomB"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomB='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomB', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomC"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomC"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomC='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomC', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomD"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomD"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomD='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomD', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomE"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomE"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomE='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomE', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomF"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomF"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomF='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomF', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomG"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomG"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomG='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomG', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomH"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomH"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomH='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomH', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomI"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomI"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomI='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomI', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateCustomJ"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCustomJ"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCustomJ='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('CustomJ', $_REQUEST["bid"], $_customer);
}

if(isset($_REQUEST["DateCancellation"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateCancellation"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateCancellation='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('Cancellation', $_REQUEST["bid"], $_customer);
}
if(isset($_REQUEST["DateFinished"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateFinished"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";
	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateFinished='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('Finished', $_REQUEST["bid"], $_customer);
}



if(isset($_REQUEST["DateOrder"])){

	$DateOrder_ARR = explode(".", $_REQUEST["DateOrder"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";

	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateOrder='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('Order', $_REQUEST["bid"], $_customer);

	$DB_WE->query("SELECT IntOrderID,DateShipping, DATE_FORMAT(DateOrder,'" . $da . "') as orddate FROM " . SHOP_TABLE . " GROUP BY IntOrderID ORDER BY intID DESC");
	$DB_WE->next_record();
}

if(isset($_REQUEST["DateShipping"])){ // ist bearbeitet
	$DateOrder_ARR = explode(".", $_REQUEST["DateShipping"]);
	$DateOrder1 = $DateOrder_ARR[2] . "-" . $DateOrder_ARR[1] . "-" . $DateOrder_ARR[0] . " 00:00:00";

	$DB_WE->query("UPDATE " . SHOP_TABLE . " SET DateShipping='" . $DB_WE->escape($DateOrder1) . "' WHERE IntOrderID = " . intval($_REQUEST["bid"]));
	$weShopStatusMails->checkAutoMailAndSend('Shipping', $_REQUEST["bid"], $_customer);

	$DB_WE->query("SELECT IntOrderID, DATE_FORMAT(DateOrder,'" . $da . "') as orddate FROM " . SHOP_TABLE . " GROUP BY IntOrderID ORDER BY intID DESC");
	$DB_WE->next_record();
}
if(isset($_REQUEST["article"])){
	if(isset($_REQUEST["preis"])){
		$DB_WE->query("UPDATE " . SHOP_TABLE . " SET Price='" . abs($_REQUEST["preis"]) . "' WHERE IntID = " . intval($_REQUEST["article"]));
	} else if(isset($_REQUEST["anzahl"])){
		$DB_WE->query("UPDATE " . SHOP_TABLE . " SET IntQuantity='" . abs($_REQUEST["anzahl"]) . "' WHERE IntID = " . intval($_REQUEST["article"]));
	} else if(isset($_REQUEST['vat'])){

		$DB_WE->query('SELECT strSerial FROM ' . SHOP_TABLE . ' WHERE IntID = ' . $DB_WE->escape($_REQUEST["article"]));

		if($DB_WE->num_rows() == 1){

			$DB_WE->next_record();

			$strSerial = $DB_WE->f('strSerial');
			$tmpDoc = @unserialize($strSerial);
			$tmpDoc[WE_SHOP_VAT_FIELD_NAME] = $_REQUEST['vat'];

			$DB_WE->query("UPDATE " . SHOP_TABLE . " SET strSerial='" . $DB_WE->escape(serialize($tmpDoc)) . "' WHERE IntID = " . intval($_REQUEST["article"]));
			unset($strSerial);
			unset($tmpDoc);
		}
	}
}

if(!isset($letzerartikel)){ // order has still articles - get them all
	// ********************************************************************************
	// first get all information about orders, we need this for the rest of the page
	//
	$query = "
		SELECT IntID, IntCustomerID, IntArticleID, strSerial, strSerialOrder, IntQuantity, Price, DATE_FORMAT(DateShipping,'" . $da . "') as DateShipping, DATE_FORMAT(DatePayment,'" . $da . "') as DatePayment, DATE_FORMAT(DateOrder,'" . $da . "') as DateOrder, DATE_FORMAT(DateConfirmation,'" . $da . "') as DateConfirmation, DATE_FORMAT(DateCustomA,'" . $da . "') as DateCustomA, DATE_FORMAT(DateCustomB,'" . $da . "') as DateCustomB, DATE_FORMAT(DateCustomC,'" . $da . "') as DateCustomC, DATE_FORMAT(DateCustomD,'" . $da . "') as DateCustomD, DATE_FORMAT(DateCustomE,'" . $da . "') as DateCustomE, DATE_FORMAT(DateCustomF,'" . $da . "') as DateCustomF, DATE_FORMAT(DateCustomG,'" . $da . "') as DateCustomG, DATE_FORMAT(DateCustomH,'" . $da . "') as DateCustomH, DATE_FORMAT(DateCustomI,'" . $da . "') as DateCustomI, DATE_FORMAT(DateCustomJ,'" . $da . "') as DateCustomJ, DATE_FORMAT(DateCancellation,'" . $da . "') as DateCancellation, DATE_FORMAT(DateFinished,'" . $da . "') as DateFinished,
		DATE_FORMAT(MailShipping,'" . $db . "') as MailShipping, DATE_FORMAT(MailPayment,'" . $db . "') as MailPayment, DATE_FORMAT(MailOrder,'" . $db . "') as MailOrder, DATE_FORMAT(MailConfirmation,'" . $db . "') as MailConfirmation, DATE_FORMAT(MailCustomA,'" . $db . "') as MailCustomA, DATE_FORMAT(MailCustomB,'" . $db . "') as MailCustomB, DATE_FORMAT(MailCustomC,'" . $db . "') as MailCustomC, DATE_FORMAT(MailCustomD,'" . $db . "') as MailCustomD, DATE_FORMAT(MailCustomE,'" . $db . "') as MailCustomE, DATE_FORMAT(MailCustomF,'" . $db . "') as MailCustomF, DATE_FORMAT(MailCustomG,'" . $db . "') as MailCustomG, DATE_FORMAT(MailCustomH,'" . $db . "') as MailCustomH, DATE_FORMAT(MailCustomI,'" . $db . "') as MailCustomI, DATE_FORMAT(MailCustomJ,'" . $db . "') as MailCustomJ, DATE_FORMAT(MailCancellation,'" . $db . "') as MailCancellation, DATE_FORMAT(MailFinished,'" . $db . "') as MailFinished
		FROM " . SHOP_TABLE . "
		WHERE IntOrderID = " .intval($_REQUEST["bid"]);

	$DB_WE->query($query);

	// loop through all articles
	while($DB_WE->next_record()) {

		// get all needed information for order-data
		$_REQUEST["cid"] = $DB_WE->f("IntCustomerID");
		$SerialOrder[] = $DB_WE->f("strSerialOrder");
		$_REQUEST["DateOrder"] = $DB_WE->f("DateOrder");
		$_REQUEST["DateConfirmation"] = $DB_WE->f("DateConfirmation");
		$_REQUEST["DateCustomA"] = $DB_WE->f("DateCustomA");
		$_REQUEST["DateCustomB"] = $DB_WE->f("DateCustomB");
		$_REQUEST["DateCustomC"] = $DB_WE->f("DateCustomC");
		$_REQUEST["DateCustomD"] = $DB_WE->f("DateCustomD");
		$_REQUEST["DateCustomE"] = $DB_WE->f("DateCustomE");
		$_REQUEST["DateCustomF"] = $DB_WE->f("DateCustomF");
		$_REQUEST["DateCustomG"] = $DB_WE->f("DateCustomG");
		$_REQUEST["DateCustomH"] = $DB_WE->f("DateCustomH");
		$_REQUEST["DateCustomI"] = $DB_WE->f("DateCustomI");
		$_REQUEST["DateCustomJ"] = $DB_WE->f("DateCustomJ");
		$_REQUEST["DatePayment"] = $DB_WE->f("DatePayment");
		$_REQUEST["DateShipping"] = $DB_WE->f("DateShipping");
		$_REQUEST["DateCancellation"] = $DB_WE->f("DateCancellation");
		$_REQUEST["DateFinished"] = $DB_WE->f("DateFinished");
		$_REQUEST["MailOrder"] = $DB_WE->f("MailOrder");
		$_REQUEST["MailConfirmation"] = $DB_WE->f("MailConfirmation");
		$_REQUEST["MailCustomA"] = $DB_WE->f("MailCustomA");
		$_REQUEST["MailCustomB"] = $DB_WE->f("MailCustomB");
		$_REQUEST["MailCustomC"] = $DB_WE->f("MailCustomC");
		$_REQUEST["MailCustomD"] = $DB_WE->f("MailCustomD");
		$_REQUEST["MailCustomE"] = $DB_WE->f("MailCustomE");
		$_REQUEST["MailCustomF"] = $DB_WE->f("MailCustomF");
		$_REQUEST["MailCustomG"] = $DB_WE->f("MailCustomG");
		$_REQUEST["MailCustomH"] = $DB_WE->f("MailCustomH");
		$_REQUEST["MailCustomI"] = $DB_WE->f("MailCustomI");
		$_REQUEST["MailCustomJ"] = $DB_WE->f("MailCustomJ");
		$_REQUEST["MailPayment"] = $DB_WE->f("MailPayment");
		$_REQUEST["MailShipping"] = $DB_WE->f("MailShipping");
		$_REQUEST["MailCancellation"] = $DB_WE->f("MailCancellation");
		$_REQUEST["MailFinished"] = $DB_WE->f("MailFinished");

		// all information for article
		$ArticleId[] = $DB_WE->f("IntArticleID"); // id of article (object or document) in shopping cart
		$tblOrdersId[] = $DB_WE->f("IntID");
		$Quantity[] = $DB_WE->f("IntQuantity");
		$Serial[] = $DB_WE->f("strSerial"); // the serialised doc
		$Price[] = str_replace(',', '.', $DB_WE->f("Price")); // replace , by . for float values
	}
	if(!isset($ArticleId)){

		echo we_html_element::jsElement('
		parent.parent.frames.shop_header_icons.location.reload();
	') . '
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
	if(sizeof($ArticleId)){

		// first unserialize order-data
		if(!empty($SerialOrder[0])){

			$orderData = @unserialize($SerialOrder[0]);
			$customCartFields = isset($orderData[WE_SHOP_CART_CUSTOM_FIELD]) ? $orderData[WE_SHOP_CART_CUSTOM_FIELD] : array();
		} else{
			$orderData = array();
			$customCartFields = array();
		}

		// prices are net?
		$pricesAreNet = true;
		if(isset($orderData[WE_SHOP_PRICE_IS_NET_NAME])){
			$pricesAreNet = $orderData[WE_SHOP_PRICE_IS_NET_NAME];
		}

		// must calculate vat?
		$calcVat = true;
		if(isset($orderData[WE_SHOP_CALC_VAT])){
			$calcVat = $orderData[WE_SHOP_CALC_VAT];
		}
	}
	//
	// no get information about complete order
	// ********************************************************************************
	// ********************************************************************************
	// Building table with customer and order data fields - start
	//
	$customerFieldTable = '';

	// determine all fields for order head


	$fl = 0;


	// first show fields Forename and surname
	if(isset($_customer['Forename'])){
		$customerFieldTable .=
			'	<tr height="25">
											<td class="defaultfont" width="86" valign="top" height="25">' . g_l('modules_customer', '[Forname]') . ':</td>
											<td class="defaultfont" valign="top" width="40" height="25"></td>
											<td width="20" height="25"></td>
											<td class="defaultfont" valign="top" colspan="6" height="25">' . $_customer['Forename'] . '</td>
				</tr>';
	}
	if(isset($_customer['Surname'])){
		$customerFieldTable .=
			'	<tr height="25">
											<td class="defaultfont" width="86" valign="top" height="25">' . g_l('modules_customer', '[Surname]') . ':</td>
											<td class="defaultfont" valign="top" width="40" height="25"></td>
											<td width="20" height="25"></td>
											<td class="defaultfont" valign="top" colspan="6" height="25">' . $_customer['Surname'] . '</td>
				</tr>';
	}

	foreach($_customer as $key => $value){

		if(in_array($key, $fields['customerFields']) || in_array($key, $fields['orderCustomerFields'])){
			if($key == $CLFields['stateField'] && $CLFields['stateFieldIsISO']){
				$value = g_l('countries', '[' . $value . ']');
			}
			if($key == $CLFields['languageField'] && $CLFields['languageFieldIsISO']){
				$value = g_l('countries', '[' . $value . ']');
			}
			$customerFieldTable .=
				'	<tr height="25">
											<td class="defaultfont" width="86" valign="top" height="25">' . $key . ':</td>
											<td class="defaultfont" valign="top" width="40" height="25"></td>
											<td width="20" height="25"></td>
											<td class="defaultfont" valign="top" colspan="6" height="25">' . $value . '</td>
				</tr>
											';
		}
	}



	$orderDataTable = '
	<table cellpadding="0" cellspacing="0" border="0" width="99%" class="defaultfont">';
	if(!$weShopStatusMails->FieldsHidden['DateOrder']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('Order', $_REQUEST["DateOrder"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86" valign="top" height="25">' . g_l('modules_shop', '[bestellnr]') . '</td>
												<td class="defaultfont" valign="top" width="40" height="25"><b>' . $_REQUEST["bid"] . '</b></td>
												<td width="20" height="25">' . we_html_tools::getPixel(34, 15) . '</td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateOrder'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateOrder">' . (($_REQUEST["DateOrder"] == $dateform) ? "-" : $_REQUEST["DateOrder"]) . '</div>
													<input type="hidden" name="DateOrder" id="hidden_Calendar_DateOrder" value="' . (($_REQUEST["DateOrder"] == $dateform) ? "-" : $_REQUEST["DateOrder"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateOrder") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateConfirmation']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('Confirmation', $_REQUEST["DateConfirmation"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateConfirmation'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateConfirmation">' . (($_REQUEST["DateConfirmation"] == $dateform) ? "-" : $_REQUEST["DateConfirmation"]) . '</div>
													<input type="hidden" name="DateConfirmation" id="hidden_Calendar_DateConfirmation" value="' . (($_REQUEST["DateConfirmation"] == $dateform) ? "-" : $_REQUEST["DateConfirmation"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateConfirmation") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomA']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomA', $_REQUEST["DateCustomA"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomA'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomA">' . (($_REQUEST["DateCustomA"] == $dateform) ? "-" : $_REQUEST["DateCustomA"]) . '</div>
													<input type="hidden" name="DateCustomA" id="hidden_Calendar_DateCustomA" value="' . (($_REQUEST["DateCustomA"] == $dateform) ? "-" : $_REQUEST["DateCustomA"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomA") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomB']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomB', $_REQUEST["DateCustomB"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomB'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomB">' . (($_REQUEST["DateCustomB"] == $dateform) ? "-" : $_REQUEST["DateCustomB"]) . '</div>
													<input type="hidden" name="DateCustomB" id="hidden_Calendar_DateCustomB" value="' . (($_REQUEST["DateCustomB"] == $dateform) ? "-" : $_REQUEST["DateCustomB"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomB") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomC']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomC', $_REQUEST["DateCustomC"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomC'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomC">' . (($_REQUEST["DateCustomC"] == $dateform) ? "-" : $_REQUEST["DateCustomC"]) . '</div>
													<input type="hidden" name="DateCustomC" id="hidden_Calendar_DateCustomC" value="' . (($_REQUEST["DateCustomC"] == $dateform) ? "-" : $_REQUEST["DateCustomC"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomC") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateShipping']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('Shipping', $_REQUEST["DateShipping"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateShipping'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateShipping">' . (($_REQUEST["DateShipping"] == $dateform) ? "-" : $_REQUEST["DateShipping"]) . '</div>
													<input type="hidden" name="DateShipping" id="hidden_Calendar_DateShipping" value="' . (($_REQUEST["DateShipping"] == $dateform) ? "-" : $_REQUEST["DateShipping"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateShipping") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomD']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomD', $_REQUEST["DateCustomD"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomD'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomD">' . (($_REQUEST["DateCustomD"] == $dateform) ? "-" : $_REQUEST["DateCustomD"]) . '</div>
													<input type="hidden" name="DateCustomD" id="hidden_Calendar_DateCustomD" value="' . (($_REQUEST["DateCustomD"] == $dateform) ? "-" : $_REQUEST["DateCustomD"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomD") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomE']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomE', $_REQUEST["DateCustomE"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomE'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomE">' . (($_REQUEST["DateCustomE"] == $dateform) ? "-" : $_REQUEST["DateCustomE"]) . '</div>
													<input type="hidden" name="DateCustomE" id="hidden_Calendar_DateCustomE" value="' . (($_REQUEST["DateCustomE"] == $dateform) ? "-" : $_REQUEST["DateCustomE"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomE") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DatePayment']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('Payment', $_REQUEST["DatePayment"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86" valign="top" height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DatePayment'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DatePayment">' . (($_REQUEST["DatePayment"] == $dateform) ? "-" : $_REQUEST["DatePayment"]) . '</div>
													<input type="hidden" name="DatePayment" id="hidden_Calendar_DatePayment" value="' . (($_REQUEST["DatePayment"] == $dateform) ? "-" : $_REQUEST["DatePayment"]) . '" />
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DatePayment") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomF']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomF', $_REQUEST["DateCustomF"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomF'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomF">' . (($_REQUEST["DateCustomF"] == $dateform) ? "-" : $_REQUEST["DateCustomF"]) . '</div>
													<input type="hidden" name="DateCustomF" id="hidden_Calendar_DateCustomF" value="' . (($_REQUEST["DateCustomF"] == $dateform) ? "-" : $_REQUEST["DateCustomF"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomF") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomG']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomG', $_REQUEST["DateCustomG"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomG'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomG">' . (($_REQUEST["DateCustomG"] == $dateform) ? "-" : $_REQUEST["DateCustomG"]) . '</div>
													<input type="hidden" name="DateCustomG" id="hidden_Calendar_DateCustomG" value="' . (($_REQUEST["DateCustomG"] == $dateform) ? "-" : $_REQUEST["DateCustomG"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomG") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCancellation']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('Cancellation', $_REQUEST["DateCancellation"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCancellation'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCancellation">' . (($_REQUEST["DateCancellation"] == $dateform) ? "-" : $_REQUEST["DateCancellation"]) . '</div>
													<input type="hidden" name="DateCancellation" id="hidden_Calendar_DateCancellation" value="' . (($_REQUEST["DateCancellation"] == $dateform) ? "-" : $_REQUEST["DateCancellation"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCancellation") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomH']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomH', $_REQUEST["DateCustomH"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomH'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomH">' . (($_REQUEST["DateCustomH"] == $dateform) ? "-" : $_REQUEST["DateCustomH"]) . '</div>
													<input type="hidden" name="DateCustomH" id="hidden_Calendar_DateCustomH" value="' . (($_REQUEST["DateCustomH"] == $dateform) ? "-" : $_REQUEST["DateCustomH"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomH") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomI']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomI', $_REQUEST["DateCustomI"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomI'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomI">' . (($_REQUEST["DateCustomI"] == $dateform) ? "-" : $_REQUEST["DateCustomI"]) . '</div>
													<input type="hidden" name="DateCustomI" id="hidden_Calendar_DateCustomI" value="' . (($_REQUEST["DateCustomI"] == $dateform) ? "-" : $_REQUEST["DateCustomI"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomI") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomJ']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('CustomJ', $_REQUEST["DateCustomJ"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateCustomJ'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateCustomJ">' . (($_REQUEST["DateCustomJ"] == $dateform) ? "-" : $_REQUEST["DateCustomJ"]) . '</div>
													<input type="hidden" name="DateCustomJ" id="hidden_Calendar_DateCustomJ" value="' . (($_REQUEST["DateCustomJ"] == $dateform) ? "-" : $_REQUEST["DateCustomJ"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateCustomJ") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
	}
	if(!$weShopStatusMails->FieldsHidden['DateFinished']){
		$EMailhandler = $weShopStatusMails->getEMailHandlerCode('Finished', $_REQUEST["DateFinished"]);
		$orderDataTable .= '
											<tr height="25">

												<td class="defaultfont" width="86"  height="25"></td>
												<td class="defaultfont" valign="top" width="40" height="25"></td>
												<td width="20" height="25"></td>
												<td width="98" class="defaultfont" height="25">' . $weShopStatusMails->FieldsText['DateFinished'] . '</td>
												<td height="25">' . we_html_tools::getPixel(14, 15) . '</td>
												<td width="14" class="defaultfont" align="right" height="25">
													<div id="div_Calendar_DateFinished">' . (($_REQUEST["DateFinished"] == $dateform) ? "-" : $_REQUEST["DateFinished"]) . '</div>
													<input type="hidden" name="DateFinished" id="hidden_Calendar_DateFinished" value="' . (($_REQUEST["DateFinished"] == $dateform) ? "-" : $_REQUEST["DateFinished"]) . '" />
												</td>
												<td height="25">' . we_html_tools::getPixel(10, 15) . '</td>
												<td width="102" valign="top" height="25">
													' . we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, "button_Calendar_DateFinished") . '
												</td>
												<td width="300" height="25"  class="defaultfont">' . $EMailhandler . '</td>
											</tr>';
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
											</tr>
' . $customerFieldTable . '
                                            <tr>
                                            	<td colspan="9"><a href="javascript:we_cmd(\'edit_order_customer\');">' . g_l('modules_shop', '[order][edit_order_customer]') . '</a></td>
                                            </tr>
                                            <tr>
                                            	<td colspan="9">' . (we_hasPerm("EDIT_CUSTOMER") ? '<a href="javascript:we_cmd(\'edit_customer\');">' . g_l('modules_shop', '[order][open_customer]') . '</a>' : '') . ' </td>
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
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[Anzahl]') . '</th>
		<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[Titel]') . '</th>
		<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[Beschreibung]') . '</th>
		<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[Preis]') . '</th>
		<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[Gesamt]') . '</th>
		' . ($calcVat ? '<td>' . $pixelImg . '</td>
		<th class="defaultgray" height="25">' . g_l('modules_shop', '[MwSt]') . '</th>' : '' ) . '
	</tr>';


	$articlePrice = 0;
	$totalPrice = 0;
	$articleVatArray = array();
	// now loop through all articles in this order
	for($i = 0; $i < sizeof($ArticleId); $i++){

		// now init each article
		if(empty($Serial[$i])){ // output 'document-articles' if $Serial[$d] is empty. This is when an order has been extended
			// this should not happen any more
			$shopArticleObject = Basket::getserial($ArticleId[$i], 'w');
		} else{	 // output if $Serial[$i] is not empty. This is when a user ordered an article online
			$shopArticleObject = @unserialize($Serial[$i]);
		}

		// determine taxes - correct price, etc.
		$articlePrice = $Price[$i] * $Quantity[$i];
		$totalPrice += $articlePrice;

		// calculate individual vat for each article
		if($calcVat){

			// now determine VAT
			if(isset($shopArticleObject[WE_SHOP_VAT_FIELD_NAME])){
				$articleVat = $shopArticleObject[WE_SHOP_VAT_FIELD_NAME];
			} else if(isset($mwst)){
				$articleVat = $mwst;
			} else{
				$articleVat = 0;
			}

			if($articleVat > 0){

				if(!isset($articleVatArray[$articleVat])){ // avoid notices
					$articleVatArray[$articleVat] = 0;
				}

				if($pricesAreNet){
					$articleVatArray[$articleVat] += ($articlePrice * $articleVat / 100);
				} else{
					$articleVatArray[$articleVat] += ($articlePrice * $articleVat / (100 + $articleVat));
				}
			}
		}

		// table row of one article
		$orderTable .= '
	<tr>
		<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
	</tr>
	<tr>
		<td class="shopContentfontR">' . "<a href=\"javascript:var anzahl=prompt('" . g_l('modules_shop', '[jsanz]') . "','" . $Quantity[$i] . "'); if(anzahl != null){if(anzahl.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location='" . $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&anzahl='+anzahl;}}\">" . numfom2($Quantity[$i]) . "</a>" . '</td>
		<td></td>
		<td>' . getFieldFromShoparticle($shopArticleObject, 'shoptitle', 35) . '</td>
		<td></td>
		<td>' . getFieldFromShoparticle($shopArticleObject, 'shopdescription', 45) . '</td>
		<td></td>
		<td class="shopContentfontR">' . "<a href=\"javascript:var preis = prompt('" . g_l('modules_shop', '[jsbetrag]') . "','" . $Price[$i] . "'); if(preis != null ){if(preis.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . "}else{document.location='" . $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&preis=' + preis; } }\">" . numfom($Price[$i]) . "</a>" . $waehr . '</td>
		<td></td>
		<td class="shopContentfontR">' . numfom($articlePrice) . $waehr . '</td>
		' . ($calcVat ? '
			<td></td>
			<td class="shopContentfontR small">(' . "<a href=\"javascript:var vat = prompt('" . g_l('modules_shop', '[keinezahl]') . "','" . $articleVat . "'); if(vat != null ){if(vat.search(/\d.*/)==-1){" . we_message_reporting::getShowMessageCall("'" . g_l('modules_shop', '[keinezahl]') . "'", we_message_reporting::WE_MESSAGE_ERROR, true) . ";}else{document.location='" . $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"] . "&article=$tblOrdersId[$i]&vat=' + vat; } }\">" . numfom($articleVat) . "</a>" . '%)</td>' : '') . '
		<td>' . $pixelImg . '</td>
		<td>' . we_button::create_button("image:btn_function_trash", "javascript:check=confirm('" . g_l('modules_shop', '[jsloeschen]') . "'); if (check){document.location.href='" . $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"] . "&deleteaartikle=" . $tblOrdersId[$i] . "';}", true, 100, 22, "", "", !we_hasPerm("DELETE_SHOP_ARTICLE")) . '</td>
	</tr>
		';
		// if this article has custom fields or is a variant - we show them in a extra rows
		// add variant.
		if(isset($shopArticleObject['WE_VARIANT']) && $shopArticleObject['WE_VARIANT']){

			$orderTable .='
	<tr>
		<td colspan="4"></td>
		<td class="small" colspan="6">' . g_l('modules_shop', '[variant]')
				. ': ' . $shopArticleObject['WE_VARIANT'] . '</td>
	</tr>
			';
		}
		// add custom fields
		if(isset($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD]) && is_array($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD]) && sizeof($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD])){

			$caField = '';
			foreach($shopArticleObject[WE_SHOP_ARTICLE_CUSTOM_FIELD] as $key => $value){
				$caField .= "$key: $value; ";
			}

			$orderTable .='
	<tr>
		<td colspan="4"></td>
		<td class="small" colspan="6">' . $caField . '</td>
	</tr>
			';
		}
	}

	// ********************************************************************************
	// "Sum of order"
	//

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

			$articleVatArray[$orderData[WE_SHOP_SHIPPING]['vatRate']] += $shippingCostsVat;
		} else{

			$shippingCostsGros = $orderData[WE_SHOP_SHIPPING]['costs'];
			$shippingCostsVat = $orderData[WE_SHOP_SHIPPING]['costs'] / ($orderData[WE_SHOP_SHIPPING]['vatRate'] + 100) * $orderData[WE_SHOP_SHIPPING]['vatRate'];
			$shippingCostsNet = $orderData[WE_SHOP_SHIPPING]['costs'] / ($orderData[WE_SHOP_SHIPPING]['vatRate'] + 100) * 100;

			$articleVatArray[$orderData[WE_SHOP_SHIPPING]['vatRate']] += $shippingCostsVat;
		}
	}

	$orderTable .= '
	<tr>
		<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
	</tr>
	<tr>
		<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[Preis]') . ':</td>
		<td colspan="4" class="shopContentfontR"><strong>' . numfom($totalPrice) . $waehr . '</strong></td>
	</tr>
	';

	if($calcVat){ // add Vat to price
		$totalPriceAndVat = $totalPrice;

		if($pricesAreNet){ // prices are net
			$orderTable .= '
				<tr>
					<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
				</tr>';

			if(isset($orderData[WE_SHOP_SHIPPING]) && isset($shippingCostsNet)){

				$totalPriceAndVat += $shippingCostsNet;
				$orderTable .= '
					<tr>
						<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
						<td colspan="4" class="shopContentfontR"><strong><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . numfom($shippingCostsNet) . $waehr . '</a></strong></td>
					</tr>
					<tr>
						<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
					</tr>';
			}
			$orderTable .='
	<tr>
		<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[plusVat]') . '</label>:</td>
		<td colspan="7"></td>
		<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
	</tr>
	';
			foreach($articleVatArray as $vatRate => $sum){

				if($vatRate){

					$totalPriceAndVat += $sum;
					$orderTable .= '
	<tr>
		<td colspan="5" class="shopContentfontR">' . $vatRate . ' %:</td>
		<td colspan="4" class="shopContentfontR">' . numfom($sum) . $waehr . '</td>
	</tr>
					';
				}
			}
			$orderTable .= '
	<tr>
		<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
	</tr>
	<tr>
		<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
		<td colspan="4" class="shopContentfontR"><strong>' . numfom($totalPriceAndVat) . $waehr . '</strong></td>
	</tr>
			';
		} else{ // prices are gros
			$orderTable .= '
	<tr>
		<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
	</tr>';

			if(isset($orderData[WE_SHOP_SHIPPING]) && isset($shippingCostsGros)){

				$totalPrice += $shippingCostsGros;
				$orderTable .= '
					<tr>
						<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
						<td colspan="4" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\');">' . numfom($shippingCostsGros) . $waehr . '</a></td>
					</tr>
					<tr>
						<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
					</tr>
					<tr>
						<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
						<td colspan="4" class="shopContentfontR"><strong>' . numfom($totalPrice) . $waehr . '</strong></td>
					</tr>
					<tr>
						<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
					</tr>';
			}

			$orderTable .= '

	<tr>
		<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[includedVat]') . '</label>:</td>
		<td colspan="7"></td>
		<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=0\';" type="checkbox" name="calculateVat" value="1" checked="checked" /></td>
	</tr>
			';
			foreach($articleVatArray as $vatRate => $sum){
				if($vatRate){
					$orderTable .= '
	<tr>
		<td colspan="5" class="shopContentfontR">' . $vatRate . ' %:</td>
		<td colspan="4" class="shopContentfontR">' . numfom($sum) . $waehr . '</td>
	</tr>
					';
				}
			}
		}
	} else{

		if(isset($shippingCostsNet)){
			$totalPrice += $shippingCostsNet;

			$orderTable .= '
	<tr>
		<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
	</tr>
	<tr>
		<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[shipping][shipping_package]') . ':</td>
		<td colspan="4" class="shopContentfontR"><a href="javascript:we_cmd(\'edit_shipping_cost\')">' . numfom($shippingCostsNet) . $waehr . '</a></td>
	</tr>
	<tr>
		<td height="1" colspan="11"><hr size="1" style="color: black" noshade /></td>
	</tr>
	<tr>
		<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[edit_order][calculate_vat]') . '</label></td>
		<td colspan="7"></td>
		<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=1\';" type="checkbox" name="calculateVat" value="1" /></td>
	</tr>
	<tr>
		<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
	</tr>
	<tr>
		<td colspan="5" class="shopContentfontR">' . g_l('modules_shop', '[gesamtpreis]') . ':</td>
		<td colspan="4" class="shopContentfontR"><strong>' . numfom($totalPrice) . $waehr . '</strong></td>
	</tr>
	<tr>
		<td height="1" colspan="11"><hr size="2" style="color: black" noshade /></td>
	</tr>
				';
		} else{

			$orderTable .= '
	<tr>
		<td colspan="5" class="shopContentfontR"><label style="cursor: pointer" for="checkBoxCalcVat">' . g_l('modules_shop', '[edit_order][calculate_vat]') . '</label></td>
		<td colspan="7"></td>
		<td colspan="1"><input id="checkBoxCalcVat" onclick="document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $_REQUEST['bid'] . '&we_cmd[0]=payVat&pay=1\';" type="checkbox" name="calculateVat" value="1" /></td>
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

	if(sizeof($customCartFields)){

		foreach($customCartFields as $key => $value){
			$customCartFieldsTable .= '
			<tr>
				<td class="defaultfont" valign="top"><b>' . $key . ':</b></td>
				<td>' . $pixelImg . '</td>
				<td class="defaultfont" valign="top">' . nl2br($value) . '</td>
				<td>' . $pixelImg . '</td>
				<td valign="top">' . we_button::create_button('image:btn_edit_edit', "javascript:we_cmd('edit_shop_cart_custom_field','" . $key . "');") . '</td>
				<td>' . $pixelImg . '</td>
				<td valign="top">' . we_button::create_button('image:btn_function_trash', "javascript:check=confirm('" . sprintf(g_l('modules_shop', '[edit_order][js_delete_cart_field]'), $key) . "'); if (check) { document.location.href='" . $_SERVER['SCRIPT_NAME'] . "?we_cmd[0]=delete_shop_cart_custom_field&bid=" . $_REQUEST["bid"] . "&cartfieldname=" . $key . "'; }") . '</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>';
		}
	}
	$customCartFieldsTable .= '
			<tr>
				<td>' . we_button::create_button('image:btn_function_plus', "javascript:we_cmd('edit_shop_cart_custom_field');") . '</td>
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
echo we_html_element::jsScript(JS_DIR . "jscalendar/calendar.js") .
	we_html_element::jsScript(JS_DIR . "jscalendar/calendar-setup.js") .
	we_html_element::jsScript(JS_DIR . WEBEDITION_DIR . "we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/calendar.js") .
	we_html_element::jsScript(JS_DIR . 'images.js') .
	we_html_element::jsScript(JS_DIR . 'windows.js');
	?>
	<link type="text/css" rel="stylesheet" href="<?php print JS_DIR . "jscalendar/skins/aqua/theme.css"; ?>" title="Aqua" />

	<script language="JavaScript" type="text/javascript">
		function SendMail(was){
			document.location = "<?php print $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"]; ?>&SendMail=" + was ;
		}
		function doUnload() {
			if (!!jsWindow_count) {
				for (i = 0; i < jsWindow_count; i++) {
					eval("jsWindow" + i + "Object.close()");
				}
			}
		}

		function we_cmd(){

			var args = "";
			var url = "<?php print $_SERVER['SCRIPT_NAME']; ?>?";

			for(var i = 0; i < arguments.length; i++){
				url += "we_cmd["+i+"]="+escape(arguments[i]);
				if(i < (arguments.length - 1)){
					url += "&";
				}
			}

			switch (arguments[0]) {

				case "edit_shipping_cost":
					var wind = new jsWindow(url + "&bid=<?php echo $_REQUEST["bid"]; ?>","edit_shipping_cost",-1,-1,545,205,true,true,true,false);
					break;

				case "edit_shop_cart_custom_field":
					var wind = new jsWindow(url + "&bid=<?php echo $_REQUEST["bid"]; ?>&cartfieldname="+ (arguments[1] ? arguments[1] : ''),"edit_shop_cart_custom_field",-1,-1,545,300,true,true,true,false);
					break;

				case "edit_order_customer":
					var wind = new jsWindow(url + "&bid=<?php echo $_REQUEST["bid"]; ?>","edit_order_customer",-1,-1,545,600,true,true,true,false);
					break;
				case "edit_customer":
					top.document.location = '/webEdition/we/include/we_modules/show_frameset.php?mod=customer&sid=<?php print $_REQUEST["cid"]; ?>';
					break;
				case "add_new_article":
					var wind = new jsWindow(url + "&bid=<?php echo $_REQUEST["bid"]; ?>","add_new_article",-1,-1,650,600,true,false,true,false);
					break;
				}
			}

			function neuerartikel(){
				we_cmd("add_new_article");
			}

			function deleteorder(){
				top.content.shop_properties.location="<?php print WE_SHOP_MODULE_PATH; ?>edit_shop_properties.php?deletethisorder=1&bid=<?php echo $_REQUEST["bid"]; ?>";
				top.content.deleteEntry(<?php echo $_REQUEST["bid"]; ?>);
			}

			hot = 1;
	<?php
	if(isset($alertMessage)){

		print we_message_reporting::getShowMessageCall($alertMessage, $alertType);
	}
	?>
	</script>

	</head>
	<body class="weEditorBody" onUnload="doUnload()">

	<?php
	$parts = array();

	array_push($parts, array(
		"html" => $orderDataTable,
		"space" => 0
		)
	);

	array_push($parts, array(
		"html" => $orderTable,
		"space" => 0
		)
	);
	if($customCartFieldsTable){

		array_push($parts, array(
			"html" => $customCartFieldsTable,
			"space" => 0
			)
		);
	}

	print we_multiIconBox::getHTML("", "100%", $parts, 30);

	//
	// "Html output for order with articles"
	// ********************************************************************************
} else{ // This order has no more entries
	echo we_html_element::jsElement('
		top.content.shop_properties.location="' . WE_SHOP_MODULE_PATH . 'edit_shop_properties.php?deletethisorder=1&bid=' . $_REQUEST["bid"] . '";
		top.content.deleteEntry(' . $_REQUEST["bid"] . ');
	') . '
</head>
<body bgcolor="#ffffff">';
}
?>
	<script type="text/javascript">we_html_element::jsElement(
			// init the used calendars

			function CalendarChanged(calObject) {
				// field:
				_field = calObject.params.inputField;
				document.location = "<?php print $_SERVER['SCRIPT_NAME'] . "?bid=" . $_REQUEST["bid"]; ?>&" + _field.name + "=" + _field.value;

			}
	<?php if(!$weShopStatusMails->FieldsHidden['DateOrder']){ ?>
			// Calender for order date
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateOrder",
				"displayArea" : "div_Calendar_DateOrder",
				"button" : "date_pickerbutton_Calendar_DateOrder",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
		<?php
	}
	if(!$weShopStatusMails->FieldsHidden['DateConfirmation']){
		?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateConfirmation",
				"displayArea" : "div_Calendar_DateConfirmation",
				"button" : "date_pickerbutton_Calendar_DateConfirmation",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
		<?php
	}
	if(!$weShopStatusMails->FieldsHidden['DateCustomA']){
		?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomA",
				"displayArea" : "div_Calendar_DateCustomA",
				"button" : "date_pickerbutton_Calendar_DateCustomA",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCustomB']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomB",
				"displayArea" : "div_Calendar_DateCustomB",
				"button" : "date_pickerbutton_Calendar_DateCustomB",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCustomC']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomC",
				"displayArea" : "div_Calendar_DateCustomC",
				"button" : "date_pickerbutton_Calendar_DateCustomC",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateShipping']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateShipping",
				"displayArea" : "div_Calendar_DateShipping",
				"button" : "date_pickerbutton_Calendar_DateShipping",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCustomD']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomD",
				"displayArea" : "div_Calendar_DateCustomD",
				"button" : "date_pickerbutton_Calendar_DateCustomD",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCustomE']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomE",
				"displayArea" : "div_Calendar_DateCustomE",
				"button" : "date_pickerbutton_Calendar_DateCustomE",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DatePayment']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DatePayment",
				"displayArea" : "div_Calendar_DatePayment",
				"button" : "date_pickerbutton_Calendar_DatePayment",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCustomF']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomF",
				"displayArea" : "div_Calendar_DateCustomF",
				"button" : "date_pickerbutton_Calendar_DateCustomF",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCustomG']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomG",
				"displayArea" : "div_Calendar_DateCustomG",
				"button" : "date_pickerbutton_Calendar_DateCustomG",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCancellation']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCancellation",
				"displayArea" : "div_Calendar_DateCancellation",
				"button" : "date_pickerbutton_Calendar_DateCancellation",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCustomH']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomH",
				"displayArea" : "div_Calendar_DateCustomH",
				"button" : "date_pickerbutton_Calendar_DateCustomH",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCustomI']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomI",
				"displayArea" : "div_Calendar_DateCustomI",
				"button" : "date_pickerbutton_Calendar_DateCustomI",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateCustomJ']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateCustomJ",
				"displayArea" : "div_Calendar_DateCustomJ",
				"button" : "date_pickerbutton_Calendar_DateCustomJ",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
	<?php
}
if(!$weShopStatusMails->FieldsHidden['DateFinished']){
	?>
			Calendar.setup(
			{
				"inputField" : "hidden_Calendar_DateFinished",
				"displayArea" : "div_Calendar_DateFinished",
				"button" : "date_pickerbutton_Calendar_DateFinished",
				"ifFormat" : "<?php print $da; ?>",
				"daFormat" : "<?php print $da; ?>",
				"onUpdate" : CalendarChanged
			}
		);
<?php } ?>
		//-->
	</script>
</body>
</html>