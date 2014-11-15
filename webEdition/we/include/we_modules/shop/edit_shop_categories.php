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

//FIXME: Base editor on edit_shop_frameset and we_shop_view (maybe in 6.4.1)

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
$protect = we_base_moduleInfo::isActive('shop') && we_users_util::canEditModule('shop') ? null : array(false);
we_html_tools::protect($protect);

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
				we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
			break;
		}
	}';

// initialise the shopStatusMails Object

$shopCatSelector = new we_html_table(array("border" => 0, "cellpadding" => 2, "cellspacing" => 4), $rows_num = 1, $cols_num = 2);

$parts = array(
	array(
		'headline' => 'Select Shop Categories\' Parent',
		'space' => 100,
		'html' => '',
		'noline' => 1
	),
	array(
		'headline' => '',
		'space' => 0,
		'html' => 'nothing much to see \'till now...'
	),
);

//Categories/VATs-Matrix
$DB_WE->query('SELECT id,text,vat,territory,textProvince FROM ' . WE_SHOP_VAT_TABLE);
$allVats = array();
$allCats = array(
	array('id' => 254, 'text' => 'dummyCat_254'),
	array('id' => 62, 'text' => 'dummyCat_62'),
	array('id' => 75, 'text' => 'dummyCat_75'),
	array('id' => 27, 'text' => 'dummyCat_27'),
	array('id' => 163, 'text' => 'dummyCat_163')
);

while($DB_WE->next_record()){
	if(!isset($allVats[$DB_WE->f('territory')])){
		$allVats[$DB_WE->f('territory')] = array();
	}

	$vat = new we_shop_vat($DB_WE->f('id'), $DB_WE->f('text'), $DB_WE->f('vat'), 0, $DB_WE->f('territory'), $DB_WE->f('textProvince'));

	$allVats[$DB_WE->f('territory')]['textTerritory'] = $vat->textTerritory;
	$allVats[$DB_WE->f('territory')]['vatObjects'][] = $vat;
	$allVats[$DB_WE->f('territory')]['selOptions'][$vat->id] = $vat->getNaturalizedText() . ': ' . $vat->vat . '%';
}

$shopCategories = new we_html_table(array("border" => 0, "cellpadding" => 2, "cellspacing" => 4), $rows_num = (count($allCats) + 1), $cols_num = 17);

$i = 0;

//generate column titles
$j = 0;
$shopCategories->setCol($i, $j++, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 110), '');
foreach($allVats as $v){
	$shopCategories->setCol($i, $j++, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 110), $v['textTerritory']);
}

//generate columns
foreach($allCats as $dummyCat){
	$j = 0;
	$shopCategories->setCol(++$i, $j++, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 110), $dummyCat['text']);
	foreach($allVats as $k => $v){
		$sel = we_html_tools::htmlSelect('weShopCatRelations[' . $dummyCat['id'] . '][' . $k . ']', $v['selOptions']);
		$shopCategories->setCol($i, $j++, array("class" => "defaultfont", "style" => "font-weight:normal", "nowrap" => "nowrap", "width" => 110), $sel);
	}
}

$parts[] = array(
	'headline' => 'Categories List',
	'space' => 100,
	'html' => '',
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => 'list based on new shop_vats with territory field and some dummy cats allready exists :-)',
	'noline' => 1
);

$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $shopCategories->getHtml()
);

//how to get data for saving relations in db
$out = '';
if(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 0) === 'saveShopCatRelations'){
	$out .= '<strong>Save...</strong><br><br>';
	$rels = we_base_request::_(we_base_request::STRING, 'weShopCatRelations');
	foreach($rels as $k => $v){
		$out .= 'cat <strong>' . $k . '</strong> is to be related with vats: ';
		foreach($v as $territory => $id){
			$out .= $id . ' (' . $territory . '), ';
		}
		$out .= '<br/>';
	}
}
$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $out
);


echo we_html_element::jsElement($jsFunction) .
 '</head>
<body class="weDialogBody" onload="window.focus();">
	<form name="we_form" method="post" >
	<input type="hidden" name="we_cmd[0]" value="saveShopCatRelations" />' .
 we_html_multiIconBox::getHTML(
	'weShopCategories', 700, $parts, 30, we_html_button::position_yes_no_cancel(
		we_html_button::create_button('save', 'javascript:we_cmd(\'save\');'), '', we_html_button::create_button('cancel', 'javascript:we_cmd(\'close\');')
	), -1, '', '', false, 'Define relations between shop categories and vat rates', '', '', 'scroll'
) . '</form>

</body>
</html>';
