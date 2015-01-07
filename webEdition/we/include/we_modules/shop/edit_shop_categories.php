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
//TODO: make read, save and process relations-data more concise

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
$protect = we_base_moduleInfo::isActive('shop') && we_users_util::canEditModule('shop') ? null : array(false);
we_html_tools::protect($protect);

//FIXME: mak sowme view class for this editor and use processVariables() and processCommands()?
//process request
$shopCategoriesDir = ($val = we_base_request::_(we_base_request::INT, 'weShopCatDir', false)) !== false ? $val : we_shop_category::getShopCatDir();//(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_dir"', '', $DB_WE, -1));
$relations = array();
$saveSuccess = false;
$onsaveClose = we_base_request::_(we_base_request::BOOL, 'onsaveclose', false);

if($shopCategoriesDir !== -1 && we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'saveShopCatRels'){
	$saveSuccess = we_shop_category::saveShopCatsDir($shopCategoriesDir);

	$destPrincipleIds = array();
	foreach(we_base_request::_(we_base_request::INT, 'weShopCatDestPrinciple', array()) as $k => $v){
		if($v){
			$destPrincipleIds[] = intval($k);
		}
	}
	$saveSuccess &= we_shop_category::saveSettingDestPrinciple(implode(',', $destPrincipleIds));

	//FIXME: get destPrinciple and isActive from db at once
	$isInactiveIds = array();
	foreach(we_base_request::_(we_base_request::INT, 'weShopCatIsActive', array()) as $k => $v){
		if(!$v){
			$isInactiveIds[] = intval($k);
		}
	}
	$saveSuccess &= we_shop_category::saveSettingIsInactive(implode(',', $isInactiveIds));

	$saveCatIds = array();
	$relations = we_base_request::_(we_base_request::STRING, 'weShopCatRels');
	foreach($relations as $k => $v){
		foreach($v as $id){
			if(!isset($saveCatIds[$id])){
				$saveCatIds[$id] = array();
			}
			$saveCatIds[$id][] = intval($k);
		}
	}

	//reset all vat-category relations before saving the new set of relations
	$saveSuccess &= $DB_WE->query('UPDATE ' . WE_SHOP_VAT_TABLE . ' SET categories=""');
	foreach($saveCatIds as $vatId => $catIds){
		$saveSuccess &= $DB_WE->query('UPDATE ' . WE_SHOP_VAT_TABLE . ' SET categories="' . implode(',', $catIds) . '" WHERE id=' . intval($vatId));
	}

	if($saveSuccess){
		$jsMessage = g_l('modules_shop', '[shopcats][save_success]');
		$jsMessageType = we_message_reporting::WE_MESSAGE_NOTICE;
	} else {
		$jsMessage = g_l('modules_shop', '[shopcats][save_error]');
		$jsMessageType = we_message_reporting::WE_MESSAGE_ERROR;
	}
} else {
	//please select category dir...
}

//make category dirs select
$DB_WE->query('SELECT ID,Path FROM ' . CATEGORY_TABLE . ' WHERE IsFolder = 1 ORDER BY Path');
$allCategoryDirs = array('-1' => g_l('modules_shop', '[shopcats][select_shopCatDir]'));
while($DB_WE->next_record()){
	$data = $DB_WE->getRecord();
	$allCategoryDirs[$data['ID']] = $data['Path'];
}
$selCategoryDirs = we_html_tools::htmlSelect('weShopCatDir', $allCategoryDirs, 1, $shopCategoriesDir, false, array('id' => 'weShopCatDir', 'onchange' => 'we_submitForm(\'' . $_SERVER['SCRIPT_NAME'] . '\');'));

if($shopCategoriesDir && intval($shopCategoriesDir) !== -1){
	$shopCategories = we_shop_category::getShopCatFieldsFromDir('', false, true, $shopCategoriesDir, true, true, true, '', 'Path');

	//Categories/VATs-Table
	$DB_WE->query('SELECT id, text, vat, territory, textProvince, categories FROM ' . WE_SHOP_VAT_TABLE);
	$allVats = array();
	$doWriteRelations = !$relations ? true : false;

	while($DB_WE->next_record()){
		$data = $DB_WE->getRecord();

		if(!isset($allVats[$data['territory']])){
			$allVats[$data['territory']] = array();
			$allVats[$data['territory']]['selOptions'][0] = ' ';
		}

		$vat = new we_shop_vat($data['id'], $data['text'], $data['vat'], 0, $data['territory'], $data['textProvince']);
		$allVats[$data['territory']]['textTerritory'] = $vat->textTerritory;
		$allVats[$data['territory']]['selOptions'][$vat->id] = $vat->getNaturalizedText() . ': ' . $vat->vat . '%';

		if($doWriteRelations){
			$catArr = explode(',', $data['categories']);
			foreach($catArr = explode(',', $data['categories']) as $cat){
				if(!isset($relations[$cat])){
					$relations[$cat] = array();
				}
				$relations[$cat][$data['territory']] = $data['id'];
			}
		}
	}

	$catsTable = new we_html_table(array('border' => 0, 'cellpadding' => 2, 'cellspacing' => 4), (count($shopCategories) * 6), 5);
	$catsDirTable = new we_html_table(array('border' => 0, 'cellpadding' => 2, 'cellspacing' => 4), 7, 5);
	if(is_array($shopCategories) && count($shopCategories) > 1){
		$i = $iTmp = 0;

		foreach($shopCategories as $k => $cat){
			$table = $catsTable;
			$isShopCatsDir = false;
			if($cat['ID'] == $shopCategoriesDir){
				$isShopCatsDir = true;
				$table = $catsDirTable;
				$iTmp = $i;
				$i = 0;
			}

			$j = 0;
			$table->setCol($i, 1, array('class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 20), $cat['ID'] . ': ');
			$table->setCol($i, 2, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'nowrap' => 'nowrap', 'width' => 140), $cat['Category']);
			$table->setCol($i++, 3, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'colspan' => 2, 'nowrap' => 'nowrap', 'width' => 174), $cat['Path']);
			if($cat['ID'] != $shopCategoriesDir){
				$table->setCol($i, 3, array('class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 174), g_l('modules_shop', '[shopcats][active_shopCat]'));
				$table->setCol($i++, 4, array('class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 240), we_html_forms::checkboxWithHidden(($cat['IsInactive'] == 0), 'weShopCatIsActive[' . $cat['ID'] . ']', '', false, '', 'we_switch_active_by_id(' . $cat['ID'] . ')'));
			}

			$taxPrinciple = we_html_forms::radioButton(0, ($cat['DestPrinciple'] == 0 ? '1' : '0'), 'weShopCatDestPrinciple[' . $cat['ID'] . ']', g_l('modules_shop', '[shopcats][text_originPrinciple]'), false, 'defaultfont', 'we_switch_principle_by_id(' . $cat['ID'] . ', this, ' . ($isShopCatsDir ? 'true' : 'false') . ')') .
				we_html_forms::radioButton(1, ($cat['DestPrinciple'] == 1 ? '1' : '0'), 'weShopCatDestPrinciple[' . $cat['ID'] . ']', g_l('modules_shop', '[shopcats][text_destPrinciple]'), false, 'defaultfont', 'we_switch_principle_by_id(' . $cat['ID'] . ', this, ' . ($isShopCatsDir ? 'true' : 'false') . ')') .
				we_html_element::htmlHidden(array('id' => 'taxPrinciple_tmp[' . $cat['ID'] . ']', 'value' => $cat['DestPrinciple']));

			$table->setRow($i, array('id' => 'destPrincipleRow_' . $cat['ID'], 'style' => ($cat['IsInactive'] == 1 ? 'display: none;' : '')));
			$table->setCol($i, 3, array('class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 174, 'style' => 'padding-bottom: 10px'), g_l('modules_shop', '[shopcats][title_taxationMode]'));
			$table->setCol($i++, 4, array('class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 240, 'style' => 'padding-bottom: 10px'), $taxPrinciple);

			if(!count($allVats)){
				$table->setCol($i, 3, array('class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 140), g_l('modules_shop', '[shopcats][warning_noVatsDefined]'));
			} else {
				$defCountry = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 1, 2);
				$countries = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), max((count($allVats) - 1), 1), 2);

				$c = -1;
				ksort($allVats);
				foreach($allVats as $k => $v){
					if(we_shop_category::getDefaultCountry() == $k){
						$innerTable = $defCountry;
						$num = 0;
						$isDefCountry = true;
					} else {
						$innerTable = $countries;
						$c++;
						$num = $c;
						$isDefCountry = false;
					}

					$value = isset($relations[$cat['ID']][$k]) && $relations[$cat['ID']][$k] ? $relations[$cat['ID']][$k] : 0;
					$selAttribs = array('id' => 'weShopCatRels[' . $cat['ID'] . '][' . $k . ']');
					$sel = we_html_tools::htmlSelect('weShopCatRels[' . $cat['ID'] . '][' . $k . ']', $v['selOptions'], 1, $value, false, $selAttribs, 'value', 220);

					$innerTable->setCol($num, 0, array('class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 184, 'style' => ($isDefCountry ? 'font-weight: normal;' : 'padding-bottom: 8px;')), ($v['textTerritory'] ? : 'N.N.'));
					$innerTable->setCol($num, 1, array('class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 220), $sel);
				}
			}
			$table->setRow($i, array('id' => 'defCountryRow_' . $cat['ID'], 'style' => ($cat['IsInactive'] == 0 ? '' : 'display: none;')));
			$table->setCol($i++, 3, array('class' => 'defaultfont', 'colspan' => 2, 'nowrap' => 'nowrap', 'width' => 424), $defCountry->getHtml());
			$table->setRow($i, array('id' => 'countriesRow_' . $cat['ID'], 'style' => ($cat['IsInactive'] == 1 || $cat['DestPrinciple'] == 0 ? 'display: none;' : '')));
			$table->setCol($i++, 3, array('class' => 'defaultfont', 'colspan' => 2, 'nowrap' => 'nowrap', 'width' => 424), $countries->getHtml());

			$table->setCol($i, 1, array('class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 20), '');
			$table->setCol($i++, 2, array('style' => 'padding-bottom: 20px', 'class' => 'defaultfont', 'nowrap' => 'nowrap', 'width' => 140), '');

			$i = $cat['ID'] == $shopCategoriesDir ? $iTmp : $i;
		}
		$catsTableHtml = $catsTable->getHtml();
		$catsDirTableHtml = $catsDirTable->getHtml();
	} else {
		$catsTableHtml = g_l('modules_shop', '[shopcats][warning_shopCatDirEmpty]');
		$catsDirTableHtml = g_l('modules_shop', '[shopcats][warning_shopCatDirEmpty]');
	}
} else {
	$catsTableHtml = $catsDirTableHtml = g_l('modules_shop', '[shopcats][warning_noShopCatDir]');
}

echo we_html_tools::getHtmlTop() . STYLESHEET;

$jsFunction = '
	var hot = 0;

	function addListeners(){
		for(var i = 1; i < document.we_form.elements.length; i++){
			document.we_form.elements[i].onchange = function(){hot = 1};
		}
	}

	function we_submitForm(url){
		var f = self.document.we_form;
		f.action = url;
		f.method = "post";
		f.submit();
	}

	function doUnload() {
		if (!!jsWindow_count) {
			for (i = 0; i < jsWindow_count; i++) {
				eval("jsWindow" + i + "Object.close()");
			}
		}
	}

	function we_cmd(){
		switch (arguments[0]) {
			case "close":
				if(hot){
					new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_exitQuestion.php","we_exit_doc_question",-1,-1,380,130,true,false,true);
				} else {
					window.close();
				}
			break;

			case "save":
				document.forms["we_form"]["we_cmd[0]"].value = "saveShopCatRels";
				document.we_form.onsaveclose.value = 1;
				we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
			break;

			case "save_notclose":
				document.forms["we_form"]["we_cmd[0]"].value = "saveShopCatRels";
				we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
			break;
		}
	}

	function we_switch_active_by_id(id){
		try{
			document.getElementById("destPrincipleRow_" + id).style.display = 
				document.getElementById("defCountryRow_" + id).style.display = 
				(document.getElementById("check_weShopCatIsActive[" + id + "]").checked) ? "" : "none";

			document.getElementById("countriesRow_" + id).style.display = 
				document.getElementById("check_weShopCatIsActive[" + id + "]").checked && 
				(document.getElementById("taxPrinciple_tmp[" + id + "]").value == 1) ? "" : "none";
		} catch(e){}
	}

	function we_switch_principle_by_id(id, obj, isShopCatsDir){
		try{
			var active = isShopCatsDir ? true : document.getElementById("check_weShopCatIsActive[" + id + "]").checked;

			document.getElementById("taxPrinciple_tmp[" + id + "]").value = obj.value;
			document.getElementById("countriesRow_" + id).style.display = 
				(active && obj.value == 1) ? "" : "none";
		} catch(e){}
	}

	' . (isset($jsMessage) ? we_message_reporting::getShowMessageCall($jsMessage, $jsMessageType) . ($saveSuccess && $onsaveClose ? 'window.close()' : '') : '');

$parts = array(
	array(
		'headline' => g_l('modules_shop', '[shopcats][text_shopCatDir]'),
		'space' => 200,
		'html' => $selCategoryDirs,

	),
);

$parts[] = array(
	'headline' => g_l('modules_shop', '[shopcats][text_editShopCatDir]'),
	'space' => 400,
	'html' => '',
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[shopcats][info_edit_shopCatDir]'), we_html_tools::TYPE_INFO, '614', false, 100),
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $catsDirTableHtml,
);

$parts[] = array(
	'headline' => g_l('modules_shop', '[shopcats][text_editShopCats]'),
	'space' => 200,
	'html' => '',
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[shopcats][info_editShopCats]'), we_html_tools::TYPE_INFO, "614", false, 101),
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $catsTableHtml,
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	//'html' => $debug_output
);

echo we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement($jsFunction) .
 '</head>
<body class="weDialogBody" onload="window.focus(); addListeners();">
	<form name="we_form" method="post" >
	<input type="hidden" name="we_cmd[0]" value="load" /><input type="hidden" name="onsaveclose" value="0" />' .
 we_html_multiIconBox::getHTML(
	'weShopCategories', 700, $parts, 30, we_html_button::position_yes_no_cancel(
		we_html_button::create_button('save', 'javascript:we_cmd(\'save_notclose\');'), '', we_html_button::create_button('close', 'javascript:we_cmd(\'close\');')
	), -1, '', '', false, g_l('modules_shop', '[shopcats][title_editorShopCats]'), '', '', 'scroll'
) . '</form>

</body>
</html>';
