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

if($shopCategoriesDir !== -1 && we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'saveShopCatRels'){
		$success = we_shop_category::saveShopCatsDir($shopCategoriesDir);

		$destPrincipleIds = array();
		foreach(we_base_request::_(we_base_request::INT, 'weShopCatDestPrinciple', array()) as $k => $v){
			if($v){
				$destPrincipleIds[] = intval($k);
			}
		}
		$success &= we_shop_category::saveSettingDestPrinciple(implode(',', $destPrincipleIds));

		//FIXME: get destPrinciple and isActive from db at once
		$isInactiveIds = array();
		foreach(we_base_request::_(we_base_request::INT, 'weShopCatIsActive', array()) as $k => $v){
			if(!$v){
				$isInactiveIds[] = intval($k);
			}
		}
		$success &= we_shop_category::saveSettingIsInactive(implode(',', $isInactiveIds));

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
		$success &= $DB_WE->query('UPDATE ' . WE_SHOP_VAT_TABLE . ' SET categories=""');
		foreach($saveCatIds as $vatId => $catIds){
			$success &= $DB_WE->query('UPDATE ' . WE_SHOP_VAT_TABLE . ' SET categories="' . implode(',', $catIds) . '" WHERE id=' . intval($vatId));
		}

		if($success){
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
$allCategoryDirs = array('-1' => 'bitte wählen');//GL
while($DB_WE->next_record()){
	$data = $DB_WE->getRecord();
	$allCategoryDirs[$data['ID']] = $data['Path'];
}
$selCategoryDirs = we_html_tools::htmlSelect('weShopCatDir', $allCategoryDirs, 1, $shopCategoriesDir, false, array('id' => 'weShopCatDir', 'onchange' => 'we_submitForm(\'' . $_SERVER['SCRIPT_NAME'] . '\');'));

if(intval($shopCategoriesDir) !== -1){
	$shopCategories = we_shop_category::getShopCatFieldsFromDir('', true, $shopCategoriesDir, true, true, true, '', 'Path');

	//Categories/VATs-Matrix
	$DB_WE->query('SELECT id, text, vat, territory, textProvince, categories FROM ' . WE_SHOP_VAT_TABLE);
	$allVats = array();
	$doWriteRelations = !$relations ? true : false;

	while($DB_WE->next_record()){
		$data = $DB_WE->getRecord();

		if(!isset($allVats[$data['territory']])){
			$allVats[$data['territory']] = array();
			$allVats[$data['territory']]['selOptions'][0] = ' ';//GL
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

	$catsMatrix = new we_html_table(array("border" => 0, "cellpadding" => 2, "cellspacing" => 4), (count($shopCategories) * (count($allVats) + 4)), 5);
	$catsDirMatrix = new we_html_table(array("border" => 0, "cellpadding" => 2, "cellspacing" => 4), ((count($allVats) + 5)), 5);
	if(count($shopCategories)){
		$i = $iTmp = 0;

		foreach($shopCategories as $k => $cat){
			$matrix = $catsMatrix;
			if($cat['ID'] == $shopCategoriesDir){
				$matrix = $catsDirMatrix;
				$iTmp = $i;
				$i = 0;
			}

			$j = 0;
			$matrix->setCol($i, 1, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 20), $cat['ID'] . ': ');
			$matrix->setCol($i, 2, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 140), $cat['Category']);
			$matrix->setCol($i++, 3, array("class" => "defaultfont", "style" => "font-weight:bold", "colspan" => "2","nowrap" => "nowrap", "width" => 140), $cat['Path']);//GL
			if($cat['ID'] != $shopCategoriesDir){
				$matrix->setCol($i, 3, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 140), 'Aktive Shop-Kategorie');//GL
				$matrix->setCol($i++, 4, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 240), we_html_forms::checkboxWithHidden(($cat['IsInactive'] == 0), 'weShopCatIsActive[' . $cat['ID'] . ']', '', false, '', 'we_switch_active_by_id(' . $cat['ID'] . ')'));
			}
			$matrix->setCol($i, 3, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 140, "style" => "padding-bottom: 10px"), 'Destination Principle');//GL
			$matrix->setCol($i++, 4, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 240, "style" => "padding-bottom: 10px"), we_html_forms::checkboxWithHidden(($cat['DestPrinciple'] == 1), 'weShopCatDestPrinciple[' . $cat['ID'] . ']', ''));

			if(!count($allVats)){
				$matrix->setCol($i, 3, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 140), 'no vats defined yet');//GL
			} else {
				foreach($allVats as $k => $v){
					$isDefCountry = we_shop_category::getDefaultCountry() == $k;
					$value = isset($relations[$cat['ID']][$k]) && $relations[$cat['ID']][$k] ? $relations[$cat['ID']][$k] : 0;
					$selAttribs = array("id" => 'weShopCatRels[' . $cat['ID'] . '][' . $k . ']');
					if($cat['IsInactive']){
						$selAttribs = array_merge($selAttribs, array('disabled' => 'disabled'));
					}
					$sel = we_html_tools::htmlSelect('weShopCatRels[' . $cat['ID'] . '][' . $k . ']', $v['selOptions'], 1, $value, false, $selAttribs, 'value', 240);
					$matrix->setCol($i, 3, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 140, "style" => ($isDefCountry ? "font-weight: normal;" : "")), $v['textTerritory']);
					$matrix->setCol($i++, 4, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 240), $sel);
				}
			}
			$matrix->setCol($i, 1, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 20), '');
			$matrix->setCol($i++, 2, array("style" => "padding-bottom: 20px", "class" => "defaultfont", "nowrap" => "nowrap", "width" => 140), '');

			$i = $cat['ID'] == $shopCategoriesDir ? $iTmp : $i;
		}
	} else {
		$catsMatrix = "Das ausgewählte Kategoeir-Verzeichnis enthält keine Einträge.";//GL
		$catsDirMatrix = "Das ausgewählte Kategoeir-Verzeichnis enthält keine Einträge.";//GL
	}
	$catsMatrixHtml = $catsMatrix->getHtml();
	$catsDirMatrixHtml = $catsDirMatrix->getHtml();
} else {
	$catsMatrixHtml = $catsDirMatrixHtml = "Sie habe noch kein Kategorie-Verzeichnis gewählt.";//GL
}

echo we_html_tools::getHtmlTop() . STYLESHEET;


$jsStr = '';
foreach($allVats as $k -> $v){
	$jsStr .= '
		
	';
}

$jsFunction = '
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
				window.close();
			break;

			case "save":
				document.forms["we_form"]["we_cmd[0]"].value = "saveShopCatRels";
				we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
			break;
		}
	}

	function we_switch_active_by_id(id){
		try{
			countries = ["' . implode('","', array_keys($allVats)) . '"];
			var active = document.getElementById("check_weShopCatIsActive[" + id + "]").checked;
			document.getElementById("check_weShopCatDestPrinciple[" + id + "]").disabled = !active;
			for(var i = 0; i < countries.length; i++){
				document.getElementById("weShopCatRels[" + id + "][" + countries[i] + "]").disabled = !active;
			}
		} catch(e){}
	}

	' . (isset($jsMessage) ? we_message_reporting::getShowMessageCall($jsMessage, $jsMessageType) : '');

$parts = array(
	array(
		'headline' => 'Shop-Kategorien-Verzeichnis',//GL
		'space' => 200,
		'html' => $selCategoryDirs,

	),
);

$parts[] = array(
	'headline' => 'Shop-Kategorie-Verzeichnis bearbeiten',
	'space' => 400,
	'html' => '',
	'noline' => 1
);

$info_1 = 'Wählen Sie hier für das Shop-Kategorie-Verzeichnis die passenden Mehrwertsteuersätze aus: Zur Auswahl stehen alle im Editor "Mehrwertsteuersätze" für das jeweilige Land definierten Steuersätze. Wählen Sie für ein Land keinen Steuersatz aus, so gilt für die betreffende Kombination von Land und Kategorie der Standard-Steuersatz. Mit der Checkbox "Destination Principle" legen Sie fest, ob für die Besteuerung dieser Kategorie das Bestimmungsland-Prinzip gilt.'; //GL
	$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => we_html_tools::htmlAlertAttentionBox($info_1, we_html_tools::TYPE_INFO, "580", false, 100),
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $catsDirMatrixHtml,
);

$parts[] = array(
	'headline' => 'Kategorien bearbeiten',
	'space' => 200,
	'html' => '',
	'noline' => 1
);

$info_2 = 'Hier können Sie für die weiteren Shop-Kategorien pro Land einen Mehrwertsteuersatz auswählen. Auch hier wird als Fallback der Standard-Steuersatz verwendet. Wird das Häckchen bei "Aktive Shop-Kategorie" entfernt, greift webEdition so lange auf die jeweilige Parent-Kategorie zurück, bis eine aktive Shop-Kategorie gefunden wird. Das Shop-Kategorie-Verzeichnis ist immer aktiv.'; //GL
$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => we_html_tools::htmlAlertAttentionBox($info_2, we_html_tools::TYPE_INFO, "580", false, 101),
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $catsMatrixHtml,
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	//'html' => $debug_output
);

echo we_html_element::jsElement($jsFunction) .
 '</head>
<body class="weDialogBody" onload="window.focus();">
	<form name="we_form" method="post" >
	<input type="hidden" name="we_cmd[0]" value="load" />' .
 we_html_multiIconBox::getHTML(
	'weShopCategories', 700, $parts, 30, we_html_button::position_yes_no_cancel(
		we_html_button::create_button('save', 'javascript:we_cmd(\'save\');'), '', we_html_button::create_button('cancel', 'javascript:we_cmd(\'close\');')
	), -1, '', '', false, 'Shop-Kategorien', '', '', 'scroll'
) . '</form>

</body>
</html>';
