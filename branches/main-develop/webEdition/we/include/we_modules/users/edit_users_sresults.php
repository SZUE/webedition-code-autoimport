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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
$protect = we_base_moduleInfo::isActive(we_base_moduleInfo::USERS) && we_users_util::canEditModule(we_base_moduleInfo::USERS) ? null : array(false);
we_html_tools::protect($protect);

echo we_html_tools::getHtmlTop(g_l('modules_users', '[search_result]'), $GLOBALS['WE_BACKENDCHARSET']) . STYLESHEET;

$kwd = we_base_request::_(we_base_request::RAW, "kwd", "");
$arr = explode(" ", strToLower($kwd));
$sWhere = "";
$ranking = "0";

$first = "";
$array_and = [];
$array_or = [];
$array_not = [];
$array_and[0] = $arr[0];

for($i = 1; $i < count($arr); $i++){
	switch($arr[$i]){
		case 'not':
			$i++;
			$array_not[count($array_not)] = $arr[$i];
			break;
		case 'and':
			$i++;
			$array_and[count($array_and)] = $arr[$i];
			break;
		case 'or':
			$i++;
			$array_or[count($array_or)] = $arr[$i];
			break;
		default:
			$array_and[count($array_and)] = $arr[$i];
			break;
	}
}
$condition = "";
foreach($array_and as $k => $value){
	$value = $DB_WE->escape($value);
	$condition.=($condition ?
			" AND (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')" :
			" (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')");
}
foreach($array_or as $k => $value){
	$value = $DB_WE->escape($value);
	$condition.=($condition ?
			" OR (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')" :
			" (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')");
}
foreach($array_not as $k => $value){
	$value = $DB_WE->escape($value);
	$condition.=($condition ?
			" AND NOT (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')" :
			" (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')");
}

$DB_WE->query('SELECT ID,Text FROM ' . USER_TABLE . ($condition ? ' WHERE ' . $condition . ' ORDER BY Text' : ''));

$select = '<div style="background-color:white;width:520px;height:220px;"/>';
if($DB_WE->num_rows()){
	$select = '<select name="search_results" size="20" style="width:520px;height:220px;" ondblclick="top.opener.top.we_cmd(\'check_user_display\',document.we_form.search_results.value); top.close();">';
	while($DB_WE->next_record()){
		$select.='<option value="' . $DB_WE->f("ID") . '">' . $DB_WE->f("Text") . '</option>';
	}
	$select.='</select>';
}

$buttons = we_html_button::position_yes_no_cancel(
		we_html_button::create_button(we_html_button::EDIT, "javascript:top.opener.top.we_cmd('check_user_display',document.we_form.search_results.value); if(document.we_form.search_results.value){top.close()}"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
);

$content = we_html_tools::htmlFormElementTable(
		we_html_tools::htmlTextInput('kwd', 24, $kwd, "", "", "text", 485), g_l('modules_users', '[search_for]'), "left", "defaultfont", we_html_button::create_button(we_html_button::SEARCH, "javascript:document.we_form.submit();")
	) . '<div style="height:20px;"></div>' .
	we_html_tools::htmlFormElementTable($select, g_l('modules_users', '[search_result]'));
?>
</head>
<body class="weEditorBody" style="margin:10px 20px;">
	<form name="we_form" method="post">
		<?= we_html_tools::htmlDialogLayout($content, g_l('modules_users', '[search]'), $buttons); ?>
	</form>
</body>
</html>