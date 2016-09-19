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
list($jsFile, $oSelCls) = include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');

we_html_tools::protect();
$widgetData = [];

$yuiSuggest = new weSuggest();
$showAC = false;
$yuiSuggest->setAutocompleteField("yuiAcInputDoc", "yuiAcContainerDoc", FILE_TABLE, "folder," . we_base_ContentTypes::WEDOCUMENT . "," . we_base_ContentTypes::HTML, weSuggest::DocSelector, 14, 1, "yuiAcLayerDoc", ["yuiAcIdDoc"], 1, "296px");

list($sTitle, $selBinary, $sCsv) = explode(";", we_base_request::_(we_base_request::STRING, 'we_cmd', ';;', 1));
$title = base64_decode($sTitle);
$selection = (bool) $selBinary{0};
$selType = (bool) $selBinary{1};

$selTable = FILE_TABLE;

if($selection){
	$selType = ($selType) ? "selObjs" : "selDocs";
	if(defined('OBJECT_FILES_TABLE')){
		$selTable = ($selType) ? OBJECT_FILES_TABLE : FILE_TABLE;
	}

	$_SESSION['weS']['exportVars_session'][$selType] = $sCsv;
}

function getHTMLDirSelector($selType){
	global $showAC;
	$showAC = true;
	$rootDirID = 0;
	$folderID = 0;
	$button_doc = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.FolderID.value,'" . FILE_TABLE . "','FolderID','FolderPath','','','" . $rootDirID . "')");
	$button_obj = defined('OBJECT_TABLE') ? we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.FolderID.value,'" . OBJECT_FILES_TABLE . "','FolderID','FolderPath','','','" . $rootDirID . "')") : '';

	$buttons = '<div id="docFolder" style="display: ' . (!$selType ? "inline" : "none") . '">' . $button_doc . "</div>" . '<div id="objFolder" style="display: ' . ($selType ? "inline" : "none") . '">' . $button_obj . "</div>";
	$path = id_to_path($folderID, (!$selType ? FILE_TABLE : (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : "")));

	return we_html_element::htmlDiv(["style" => "margin-top:10px;"
			], we_html_tools::htmlFormElementTable(
				"<div id=\"yuiAcLayerDoc\" class=\"yuiAcLayer\">" . we_html_tools::htmlTextInput("FolderPath", 58, $path, "", 'onchange="" id="yuiAcInputDoc"', "text", (420 - 120), 0) .
				"<div id=\"yuiAcContainerDoc\"></div></div>", g_l('cockpit', '[dir]'), "left", "defaultfont", we_html_element::htmlHidden("FolderID", $folderID, "yuiAcIdDoc"
				), $buttons));
}

$docTypes = [0 => g_l('cockpit', '[no_entry]')];

$dtq = we_docTypes::getDoctypeQuery($DB_WE);
$DB_WE->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
while($DB_WE->next_record()){
	$docTypes[$DB_WE->f("ID")] = $DB_WE->f("DocType");
}
$doctypeElement = we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect("DocTypeID", $docTypes, 1, 0, false, ['onchange' => "", 'style' => "width:420px; border: #AAAAAA solid 1px;"], 'value'), g_l('cockpit', '[doctype]'));

$cls = new we_html_select(["name" => "classID",
	'class' => 'defaultfont',
	"style" => "width:420px; border: #AAAAAA solid 1px"
	]);
$optid = 0;
$cls->insertOption($optid, 0, g_l('cockpit', '[no_entry]'));
$ac = implode(',', we_users_util::getAllowedClasses($DB_WE));
if($ac){
	$DB_WE->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ' . ($ac ? ' WHERE ID IN(' . $ac . ') ' : '') . 'ORDER BY Text');
	while($DB_WE->next_record()){
		$optid++;
		$cls->insertOption($optid, $DB_WE->f("ID"), $DB_WE->f("Text"));
		if($DB_WE->f("ID") == -1){
			$cls->selectOption($DB_WE->f("ID"));
		}
	}
}

function getHTMLCategory(&$widgetData){
	$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','fillIDs();opener.addCat(top.allPaths);')", false, 100, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));
	$del_but = we_html_button::create_button(we_html_button::TRASH, 'javascript:#####placeHolder#####;top.mark();');
	$widgetData['cats'] = [
		'del' => $del_but,
		'items' => []
	];

	$table = new we_html_table(['id' => 'CategoriesBlock',
		'style' => 'display: block;margin-top: 5px;',
		'class' => 'default'
		], 5, 1);

	$table->setCol(1, 0, ['class' => 'defaultfont'], "Kategorien");
	$table->setColContent(2, 0, we_html_element::htmlDiv(['id' => 'categories',
			'class' => 'blockWrapper',
			'style' => 'width:420px;height:60px;border:#AAAAAA solid 1px;margin-bottom:5px;'
	]));

	$table->setCol(4, 0, ['colspan' => 2, 'style' => 'text-align:right'], we_html_button::create_button(we_html_button::DELETE_ALL, 'javascript:removeAllCats()') . $addbut);

	return $table->getHtml();
}

$seltype = ['doctype' => g_l('cockpit', '[documents]')];
if(defined('OBJECT_TABLE')){
	$seltype['classname'] = g_l('cockpit', '[objects]');
}

$tree = new we_export_tree('treeCmd.php', 'top', 'top', 'cmd');

$captions = [];
if(permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")){
	$captions[FILE_TABLE] = g_l('export', '[documents]');
}
if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
	$captions[OBJECT_FILES_TABLE] = g_l('export', '[objects]');
}


$divContent = we_html_element::htmlDiv(["style" => "display:block;"
		], we_html_tools::htmlSelect("Selection", ["dynamic" => g_l('cockpit', '[dyn_selection]'), "static" => g_l('cockpit', '[stat_selection]')
			], 1, ($selection ? "static" : "dynamic"), false, ['style' => "width:420px;border:#AAAAAA solid 1px;", 'onchange' => "closeAllSelection();we_submit();"], 'value') .
		we_html_element::htmlBr() .
		we_html_tools::htmlSelect("headerSwitch", $captions, 1, (!$selType ? FILE_TABLE : OBJECT_FILES_TABLE), false, ['style' => "width:420px;border:#AAAAAA solid 1px;margin-top:10px;", 'onchange' => "setHead(this.value);we_submit();"], 'value', 420) .
		we_html_element::htmlDiv(["id" => "static", "style" => ($selection ? "display:block;" : "display:none;")], we_html_element::htmlDiv(["id" => "treeContainer"], $tree->getHTMLMultiExplorer(420, 180, false)) . '<iframe name="cmd" src="about:blank" style="visibility:hidden; width: 0px; height: 0px;"></iframe>') .
		we_html_element::htmlDiv(["id" => "dynamic", "style" => (!$selection ? 'display:block;' : 'display:none;')
			], getHTMLDirSelector($selType) . we_html_element::htmlBr() . ((!$selType) ? $doctypeElement : we_html_tools::htmlFormElementTable(
					$cls->getHTML(), g_l('cockpit', '[class]'))) . we_html_element::htmlBr() . getHTMLCategory($widgetData)) .
		we_html_element::htmlBr() .
		we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("title", 55, $title, 255, "", "text", 420, 0), g_l('cockpit', '[title]'), "left", "defaultfont"));

$parts = [["headline" => "",
	"html" => $divContent,
	],
	["headline" => "",
		"html" => $oSelCls->getHTML(),
	]
];

$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();", false, 0, 0);
$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();", false, 0, 0);
$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getHTML("mdcProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[my_documents]'));

echo we_html_tools::getHtmlTop(g_l('cockpit', '[my_documents]'), '', '', weSuggest::getYuiFiles() .
	$jsFile .
	we_html_element::jsScript(JS_DIR . 'widgets/mdc.js', '', ['id' => 'loadVarWidget', 'data-widget' => setDynamicVar($widgetData)]), we_html_element::htmlBody(
		["class" => "weDialogBody", "onload" => "init('" . $selTable . "','" . $sTitle . "','" . $selBinary . "','" . $sCsv . "');"
		], we_html_element::htmlForm(
			"", we_html_element::htmlHiddens(["table" => "",
				"FolderID" => 0,
				"CategoriesControl" => we_base_request::_(we_base_request::INT, 'CategoriesCount', 0)
			]) . $sTblWidget)));
if($showAC){
	echo $yuiSuggest->getYuiJs();
}
