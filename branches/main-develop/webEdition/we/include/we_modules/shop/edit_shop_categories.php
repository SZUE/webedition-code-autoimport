<?php

/**
 * webEdition CMS
 *
 * $Rev: 8384 $
 * $Author: mokraemer $
 * $Date: 2014-10-07 18:49:11 +0200 (Di, 07 Okt 2014) $
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
$protect = we_base_moduleInfo::isActive('shop') && we_users_util::canEditModule('shop') ? null : array(false);
we_html_tools::protect($protect);

//FIXME: mak sowme view class for this editor and use processVariables() and processCommands()?
//process request
$shopCategoriesDir = ($val = we_base_request::_(we_base_request::STRING, 'weShopCatDir', false)) !== false ? $val : (f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_categories_dir"', '', $DB_WE, -1));
$rels = array();//TODO: take from db!




//process cmds
$debug_output = '';
if($shopCategoriesDir !== -1){
	switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
		case 'saveShopCatRels':
			$DB_WE->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET tool="shop",pref_name="shop_categories_dir",pref_value=' . intval($shopCategoriesDir));

			//how to get data for saving relations in db
			if(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 0) === 'saveShopCatRels'){
				$debug_output .= '<br/><strong>Save...</strong><br><br>';
				$rels = we_base_request::_(we_base_request::STRING, 'weShopCatRels');
				foreach($rels as $k => $v){
					$debug_output .= 'cat <strong>' . $k . '</strong> is to be related with vats: ';
					foreach($v as $territory => $id){
						$debug_output .= $id . ' (' . $territory . '), ';
					}
					$debug_output .= '<br/>';
				}
			}
			break;
		case 'load': 
			//nothing yet
	}
}

//make category dirs select
$DB_WE->query('SELECT ID,Path FROM ' . CATEGORY_TABLE . ' WHERE IsFolder = 1 ORDER BY Path');
$allCategoryDirs = array('-1' => 'bitte wählen');//GL
while($DB_WE->next_record()){
	$allCategoryDirs[$DB_WE->f('ID')] = $DB_WE->f('Path');
}
$selCategoryDirs = we_html_tools::htmlSelect('weShopCatDir', $allCategoryDirs, 1, $shopCategoriesDir, false, array('id' => 'weShopCatDir', 'onchange' => 'we_submitForm(\'' . $_SERVER['SCRIPT_NAME'] . '\');'));

//get all shop categories (from inside $shopCategoriesDir)
if(intval($shopCategoriesDir) !== -1){
	$DB_WE->query('SELECT ID, Text FROM ' . CATEGORY_TABLE . ' WHERE Path LIKE "' . $allCategoryDirs[$shopCategoriesDir] . '/%" AND IsFolder = 0');
	$shopCategories = array();
	while($DB_WE->next_record()){
		$shopCategories[] = array("id" => $DB_WE->f('ID'), "text" => $DB_WE->f('Text'));
	}


	//Categories/VATs-Matrix
	$DB_WE->query('SELECT id,text,vat,territory,textProvince FROM ' . WE_SHOP_VAT_TABLE);
	$allVats = array();

	while($DB_WE->next_record()){
		if(!isset($allVats[$DB_WE->f('territory')])){
			$allVats[$DB_WE->f('territory')] = array();
		}

		$vat = new we_shop_vat($DB_WE->f('id'), $DB_WE->f('text'), $DB_WE->f('vat'), 0, $DB_WE->f('territory'), $DB_WE->f('textProvince'));
		$allVats[$DB_WE->f('territory')]['textTerritory'] = $vat->textTerritory;
		$allVats[$DB_WE->f('territory')]['vatObjects'][] = $vat;
		$allVats[$DB_WE->f('territory')]['selOptions'][$vat->id] = $vat->getNaturalizedText() . ': ' . $vat->vat . '%';
	}

	$matrix = new we_html_table(array("border" => 0, "cellpadding" => 2, "cellspacing" => 4), $rows_num = (count($shopCategories) + 1), count($allVats) + 2);

	//generate column titles
	$i = 0;
	$j = 0;
	$matrix->setCol($i, $j++, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 110), '');
	foreach($allVats as $v){
		$matrix->setCol($i, $j++, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 110), $v['textTerritory']);
	}

	//generate columns
	if(count($shopCategories)){
		foreach($shopCategories as $cat){
			$j = 0;
			$matrix->setCol(++$i, $j++, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 110), $cat['text']);
			if(!count($allVats)){
				$matrix->setCol($i, $j, array("class" => "defaultfont", "style" => "font-weight:normal", "nowrap" => "nowrap", "width" => 110), 'no vats defined yet');
			} else {
				foreach($allVats as $k => $v){
					$sel = we_html_tools::htmlSelect('weShopCatRels[' . $cat['id'] . '][' . $k . ']', $v['selOptions'], 1, $rels[$cat['id']][$k]);
					$matrix->setCol($i, $j++, array("class" => "defaultfont", "style" => "font-weight:normal", "nowrap" => "nowrap", "width" => 110), $sel);
				}
			}
		}
		$matrixHtml = $matrix->getHtml();
	} else {
		$matrixHtml = "Das ausgewählte Kategoeir-Verzeichnis enthält keine Einträge.";//GL
	}
} else {
	$matrixHtml = "Sie habe noch kein Kategorie-Verzeichnis gewählt.";//GL
}

echo we_html_tools::getHtmlTop() . STYLESHEET;

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
	}';

$parts = array(
	array(
		'headline' => 'Verzeichnis der Shop-Kategorien',
		'space' => 0,
		'html' => $selCategoryDirs,

	),
);

$parts[] = array(
	'headline' => 'Categories List',
	'space' => 200,
	'html' => '',
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $matrixHtml,
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $debug_output
);

echo we_html_element::jsElement($jsFunction) .
 '</head>
<body class="weDialogBody" onload="window.focus();">
	<form name="we_form" method="post" >
	<input type="hidden" name="we_cmd[0]" value="load" />' .
 we_html_multiIconBox::getHTML(
	'weShopCategories', 700, $parts, 30, we_html_button::position_yes_no_cancel(
		we_html_button::create_button('save', 'javascript:we_cmd(\'save\');'), '', we_html_button::create_button('cancel', 'javascript:we_cmd(\'close\');')
	), -1, '', '', false, 'Define relations between shop categories and vat rates', '', '', 'scroll'
) . '</form>

</body>
</html>';
