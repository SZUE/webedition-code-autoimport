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
include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');

we_html_tools::protect();
$yuiSuggest = new weSuggest();
$showAC = false;
$yuiSuggest->setAutocompleteField(
	"yuiAcInputDoc", "yuiAcContainerDoc", FILE_TABLE, "folder," . we_base_ContentTypes::WEDOCUMENT . "," . we_base_ContentTypes::HTML, weSuggest::DocSelector, 14, 1, "yuiAcLayerDoc", array(
	"yuiAcIdDoc"
	), 1, "296px");

list($sTitle, $selBinary, $sCsv) = explode(";", we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
$_title = base64_decode($sTitle);
$_selection = (bool) $selBinary{0};
$_selType = (bool) $selBinary{1};

$_selTable = FILE_TABLE;

if($_selection){
	$selType = ($_selType) ? "selObjs" : "selDocs";
	if(defined('OBJECT_FILES_TABLE')){
		$_selTable = ($_selType) ? OBJECT_FILES_TABLE : FILE_TABLE;
	}

	$_SESSION['weS']['exportVars_session'][$selType] = $sCsv;
}
$jsTree = "
function startInit() { // this function is called onload!
	startTree();
	var _sCsv='" . $sCsv . "';
	var aCsv=_sCsv.split(',');
	var aCsvLen=aCsv.length;
	for(var i=0;i<aCsvLen;i++){
		SelectedItems['" . $_selTable . "'][i]=aCsv[i];
	}
}";

function getHTMLDirSelector($_selType){
	global $showAC, $yuiSuggest;
	$showAC = true;
	$rootDirID = 0;
	$folderID = 0;
	$wecmdenc1 = we_base_request::encCmd("document.we_form.elements.FolderID.value");
	$wecmdenc2 = we_base_request::encCmd("document.we_form.elements.FolderPath.value");
	$_button_doc = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.FolderID.value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $rootDirID . "')");
	$_button_obj = defined('OBJECT_TABLE') ? we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.FolderID.value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $rootDirID . "')") : '';

	$_buttons = '<div id="docFolder" style="display: ' . (!$_selType ? "inline" : "none") . '">' . $_button_doc . "</div>" . '<div id="objFolder" style="display: ' . ($_selType ? "inline" : "none") . '">' . $_button_obj . "</div>";
	$_path = id_to_path($folderID, (!$_selType ? FILE_TABLE : (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : "")));

	return we_html_element::htmlDiv(
			array(
			"style" => "margin-top:10px;"
			), we_html_tools::htmlFormElementTable(
				"<div id=\"yuiAcLayerDoc\" class=\"yuiAcLayer\">" . $yuiSuggest->getErrorMarkPlaceHolder(
					"yuiAcErrorMarkDoc") . we_html_tools::htmlTextInput(
					"FolderPath", 58, $_path, "", 'onchange="" id="yuiAcInputDoc"', "text", (420 - 120), 0) . "<div id=\"yuiAcContainerDoc\"></div></div>", g_l('cockpit', '[dir]'), "left", "defaultfont", we_html_element::htmlHidden("FolderID", $folderID, "yuiAcIdDoc"
				), we_html_tools::getPixel(20, 4), $_buttons));
}

$docTypes = array(0 => g_l('cockpit', '[no_entry]'));

$DB_WE->query('SELECT ID,DocType FROM ' . DOC_TYPES_TABLE . ' ' . we_docTypes::getDoctypeQuery($DB_WE));
while($DB_WE->next_record()){
	$docTypes[$DB_WE->f("ID")] = $DB_WE->f("DocType");
}
$doctypeElement = we_html_tools::htmlFormElementTable(
		we_html_tools::htmlSelect(
			"DocTypeID", $docTypes, 1, 0, false, array('onchange' => "", 'style' => "width:420px; border: #AAAAAA solid 1px;"), 'value'), g_l('cockpit', '[doctype]'));

$cls = new we_html_select(
	array(
	"name" => "classID",
	"size" => 1,
	"class" => "defaultfont",
	"style" => "width:420px; border: #AAAAAA solid 1px"
	));
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

function getHTMLCategory(){
	$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','fillIDs();opener.addCat(top.allPaths);')", false, 100, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));
	$del_but = addslashes(we_html_button::create_button(we_html_button::TRASH,'javascript:#####placeHolder#####;top.mark();'));

	$variant_js = '
		var categories_edit=new multi_edit("categories",document.we_form,0,"' . $del_but . '",390,false);
		categories_edit.addVariant();
		document.we_form.CategoriesControl.value=categories_edit.name;
	';
	$Categories = '';
	if(is_array($Categories)){
		foreach($Categories as $cat){
			$variant_js .= '
				categories_edit.addItem();
				categories_edit.setItem(0,(categories_edit.itemCount-1),"' . $cat . '");
			';
		}
	}

	$variant_js .= 'categories_edit.showVariant(0);';

	$table = new we_html_table(
		array(
		'id' => 'CategoriesBlock',
		'style' => 'display: block;',
		'cellpadding' => 0,
		'cellspacing' => 0,
		'border' => 0
		), 5, 1);

	$table->setColContent(0, 0, we_html_tools::getPixel(5, 5));
	$table->setCol(1, 0, array(
		'class' => 'defaultfont'
		), "Kategorien");
	$table->setColContent(
		2, 0, we_html_element::htmlDiv(
			array(
				'id' => 'categories',
				'class' => 'blockWrapper',
				'style' => 'width:420px;height:60px;border:#AAAAAA solid 1px;'
	)));

	$table->setColContent(3, 0, we_html_tools::getPixel(5, 5));

	$table->setCol(
		4, 0, array(
		'colspan' => 2, 'align' => 'right'
		), we_html_button::create_button_table(
			array(
				we_html_button::create_button(we_html_button::DELETE_ALL, 'javascript:removeAllCats()'), $addbut
	)));

	return $table->getHtml() . we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js') .
		we_html_element::jsElement($variant_js);
}

$jsCode = "
	var dirs={
	'WE_INCLUDES_DIR':'" . WE_INCLUDES_DIR . "',
	'WEBEDITION_DIR':'" . WEBEDITION_DIR . "'
	};
var _oCsv_;
var _sCsv;
var _sInitCsv_;
var _sInitTitle_='" . $sTitle . "';
var _sMdcInc='mdc/mdc';

var table='" . $_selTable . "';
var g_l={
	prefs_saved_successfully: '" . we_message_reporting::prepareMsgForJS(g_l('cockpit', '[prefs_saved_successfully]')) . "'
};

function we_cmd(){
	var args='';
	var url=dirs.WEBEDITION_DIR +'we_cmd.php?';
	for(var i=0;i<arguments.length;i++){
		url+='we_cmd['+i+']='+encodeURI(arguments[i]);
		if(i<(arguments.length-1)){
			url+='&';
		}
	}
	switch(arguments[0]){
		case 'we_selector_directory':
			new jsWindow(url,'we_fileselector',-1,-1," . we_selector_file::WINDOW_DIRSELECTOR_WIDTH . "," . we_selector_file::WINDOW_DIRSELECTOR_HEIGHT . ",true,true,true,true);
			break;
		case 'we_selector_category':
			new jsWindow(url,'we_catselector',-1,-1," . we_selector_file::WINDOW_CATSELECTOR_WIDTH . "," . we_selector_file::WINDOW_CATSELECTOR_HEIGHT . ",true,true,true,true);
			break;
		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			parent.we_cmd.apply(this, args);

	}
}

function init(){
	_fo=document.forms[0];
	initPrefs();
	_oCsv_=opener.gel(_sObjId+'_csv');
	_sInitCsv_=_oCsv_.value;
	_sInitTitle_=opener.gel(_sObjId+'_prefix').value;
	_fo.elements.title.value=_sInitTitle_;
	var aInitCsv=_sInitCsv_.split(';');
	var dir=aInitCsv[2].split(',');
	var sBinary='" . $selBinary . "';
	var _sCsv='" . $sCsv . "';
	if(parseInt(sBinary.substr(0,1))==parseInt(aInitCsv[1].substr(0,1))){
		if(parseInt(sBinary.substr(1))==parseInt(aInitCsv[1].substr(1))){
			_fo.FolderID.value=dir[0];
			_fo.FolderPath.value=dir[1];
			if(aInitCsv[3]!=0){
				var obj=parseInt(sBinary.substr(1))?_fo.classID:_fo.DocTypeID;
				obj.value=aInitCsv[3];
			}
			if(aInitCsv[4]!==undefined&&aInitCsv[4]!='')addCat(opener.base64_decode(aInitCsv[4]));
		}
	}
}
";

$_seltype = array(
	'doctype' => g_l('cockpit', '[documents]')
);
if(defined('OBJECT_TABLE')){
	$_seltype['classname'] = g_l('cockpit', '[objects]');
}

$tree = new we_export_tree('treeCmd.php', 'top', 'top', 'cmd');

$divStatic = we_html_element::htmlDiv(
		array(
		"id" => "static", "style" => ($_selection ? "display:block;" : "display:none;")
		), we_html_element::htmlDiv(array(
			"id" => "treeContainer"
			), $tree->getHTMLMultiExplorer(420, 180, false)) . "<iframe name=\"cmd\" src=\"about:blank\" style=\"visibility:hidden; width: 0px; height: 0px;\"></iframe>");

$captions = array();
if(permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")){
	$captions[FILE_TABLE] = g_l('export', '[documents]');
}
if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")){
	$captions[OBJECT_FILES_TABLE] = g_l('export', '[objects]');
}

$divDynamic = we_html_element::htmlDiv(
		array(
		"id" => "dynamic", "style" => (!$_selection ? 'display:block;' : 'display:none;')
		), getHTMLDirSelector($_selType) . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . ((!$_selType) ? $doctypeElement : we_html_tools::htmlFormElementTable(
				$cls->getHTML(), g_l('cockpit', '[class]'))) . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . getHTMLCategory());

$divContent = we_html_element::htmlDiv(
		array(
		"style" => "display:block;"
		), we_html_tools::htmlSelect(
			"Selection", array(
			"dynamic" => g_l('cockpit', '[dyn_selection]'), "static" => g_l('cockpit', '[stat_selection]')
			), 1, ($_selection ? "static" : "dynamic"), false, array('style' => "width:420px;border:#AAAAAA solid 1px;", 'onchange' => "closeAllSelection();we_submit();"), 'value') . we_html_element::htmlBr() . we_html_tools::htmlSelect(
			"headerSwitch", $captions, 1, (!$_selType ? FILE_TABLE : OBJECT_FILES_TABLE), false, array('style' => "width:420px;border:#AAAAAA solid 1px;margin-top:10px;", 'onchange' => "setHead(this.value);we_submit();"), 'value', 420) . $divStatic . $divDynamic . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . we_html_tools::htmlFormElementTable(
			we_html_tools::htmlTextInput(
				$name = "title", $size = 55, $value = $_title, $maxlength = 255, $attribs = "", $type = "text", $width = 420, $height = 0), g_l('cockpit', '[title]'), "left", "defaultfont"));

$parts = array(
	array(
		"headline" => "", "html" => $divContent, "space" => 0
	),
	array(
		"headline" => "", "html" => $oSelCls->getHTML(), "space" => 0
	)
);

$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();", false, 0, 0);
$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();", false, 0, 0);
$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getHTML("mdcProps", "100%", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[my_documents]'));

echo we_html_element::htmlDocType() . we_html_element::htmlHtml(
	we_html_element::htmlHead(
		we_html_tools::getHtmlInnerHead(g_l('cockpit', '[my_documents]')) . weSuggest::getYuiFiles() . STYLESHEET .
		we_html_element::jsScript(JS_DIR . "we_showMessage.js") .
		we_html_element::jsScript(JS_DIR . "windows.js") .
		we_html_element::jsElement($jsPrefs . $jsCode . $jsTree)) .
	we_html_element::jsScript(JS_DIR . 'widgets/mdc.js') .
	we_html_element::htmlBody(
		array(
		"class" => "weDialogBody", "onload" => "init();startInit();"
		), we_html_element::htmlForm(
			"", we_html_element::htmlHiddens(array(
				"table" => "",
				"FolderID" => 0,
				"CategoriesControl" => we_base_request::_(we_base_request::INT, 'CategoriesCount', 0)
			)) . $sTblWidget)));
if($showAC){
	echo $yuiSuggest->getYuiJs();
}