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
include_once (WE_INCLUDES_PATH . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/topFeeds.inc.php');

$iDefCols = 2;
$small = 202;
$large = 432;
$iDlgWidth = 480;

$sc1 = $sc2 = array();

// define shortcuts
$shortCuts_left = array();
$shortCuts_right = array();

if(defined('FILE_TABLE') && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
	$shortCuts_left[] = 'open_document';
	$shortCuts_left[] = 'new_document';
	$shortCutsDocs = '1';
} else {
	$shortCutsDocs = '0';
}

if(defined('TEMPLATES_TABLE')){
	$shortCutsTemplates = (permissionhandler::hasPerm('CAN_SEE_TEMPLATES')) ? '1' : '0';
	if(permissionhandler::hasPerm('NEW_TEMPLATE')){
		$shortCuts_left[] = 'new_template';
	}
} else {
	$shortCutsTemplates = '0';
}
$shortCuts_left[] = 'new_directory';
if(defined('FILE_TABLE') && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
	$shortCuts_left[] = 'unpublished_pages';
}
if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
	$shortCuts_right[] = 'unpublished_objects';
	$shortCutsObjects = '1';
} else {
	$shortCutsObjects = '0';
}
if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('NEW_OBJECTFILE')){
	$shortCuts_right[] = 'new_object';
}

if(defined('OBJECT_TABLE')){
	$shortCutsClasses = (permissionhandler::hasPerm('CAN_SEE_OBJECTS')) ? '1' : '0';
	if(permissionhandler::hasPerm('NEW_OBJECT')){
		$shortCuts_right[] = 'new_class';
	}
} else {
	$shortCutsClasses = '0';
}
if(permissionhandler::hasPerm('EDIT_SETTINGS')){
	$shortCuts_right[] = 'preferences';
}

$aPrefs = array(
	'sct' => array(
		'width' => $small,
		'height' => 210,
		'res' => 0,
		'cls' => 'red',
		'csv' => implode(',', $sc1) . ';' . implode(',', $sc2),
		'dlgHeight' => 435,
		'isResizable' => 1
	),
	'rss' => array(
		'width' => $small,
		'height' => 307,
		'res' => 0,
		'cls' => 'yellow',
		'csv' => base64_encode('http://www.webedition.org/de/feeds/aktuelles.xml') . ',111000,0,110000,1',
		'dlgHeight' => 480,
		'isResizable' => 1
	),
	'mfd' => array(
		'width' => $large,
		'height' => 210,
		'res' => 1,
		'cls' => 'lightCyan',
		'csv' => $shortCutsDocs . $shortCutsTemplates . $shortCutsObjects . $shortCutsClasses . ';0;5;00;',
		'dlgHeight' => 435,
		'isResizable' => 0
	),
	'shp' => array(
		'width' => $large,
		'height' => 212,
		'res' => 1,
		'cls' => 'lightCyan',
		'csv' => '1111;3;10000;',
		'dlgHeight' => 435,
		'isResizable' => 0
	),
	'msg' => array(
		'width' => $small,
		'height' => 100,
		'res' => 0,
		'cls' => 'lightCyan',
		'csv' => '',
		'dlgHeight' => 140,
		'isResizable' => 1
	),
	'fdl' => array(
		'width' => $large,
		'height' => 210,
		'res' => 1,
		'cls' => 'orange',
		'csv' => '',
		'dlgHeight' => 435,
		'isResizable' => 0
	),
	'usr' => array(
		'width' => $small,
		'height' => 210,
		'res' => 0,
		'cls' => 'lightCyan',
		'csv' => '',
		'dlgHeight' => 140,
		'isResizable' => 1
	),
	'upb' => array(
		'width' => $small,
		'height' => 210,
		'res' => 0,
		'cls' => 'lightCyan',
		'csv' => $shortCutsDocs . $shortCutsObjects,
		'dlgHeight' => 190,
		'isResizable' => 1
	),
	'mdc' => array(
		'width' => $small,
		'height' => 307,
		'res' => 0,
		'cls' => 'white',
		'csv' => ';10;',
		'dlgHeight' => 450,
		'isResizable' => 1
	),
	'pad' => array(
		'width' => $large,
		'height' => 307,
		'res' => 1,
		'cls' => 'blue',
		'csv' => base64_encode(g_l('cockpit', '[notepad_defaultTitle_DO_NOT_TOUCH]')) . ',30020',
		'dlgHeight' => 560,
		'isResizable' => 0
	),
);

$aCfgProps = array(
	array(
		array(
			"pad", "blue", 1, base64_encode(g_l('cockpit', '[notepad_defaultTitle_DO_NOT_TOUCH]')) . ',30020'
		),
		array(
			"mfd",
			"green",
			1,
			$shortCutsDocs . $shortCutsTemplates . $shortCutsObjects . $shortCutsClasses . ';0;5;00;'
		)
	),
	array(
		array(
			"rss",
			"yellow",
			1,
			base64_encode('http://www.webedition.org/de/feeds/aktuelles.xml') . ',111000,0,110000,1'
		),
		array(
			"sct", "red", 1, implode(',', $shortCuts_left) . ';' . implode(',', $shortCuts_right)
		)
	)
);

$aTopRssFeeds = g_l('topFeeds', '');
for($i = 0; $i < count($aTopRssFeeds); $i++){
	foreach($aTopRssFeeds[$i] as $k => $v){
		$aTopRssFeeds[$i][$k] = base64_encode($v);
	}
}
//$aCfgProps[]= $aTopRssFeeds;

$jsPrefs = "
var oCfg={
	iDlgWidth:" . intval($iDlgWidth) . ",
	_noResizeTypes:['pad']
};";

foreach($aPrefs as $type => $_prefs){
	$jsPrefs .= "oCfg." . $type . "_props_={
		width:" . intval($_prefs["width"]) . ",
		height:" . intval($_prefs["height"]) . ",
		res:" . $_prefs["res"] . ",
		cls:'" . $_prefs["cls"] . "',
		iDlgHeight:" . intval($_prefs["dlgHeight"]) . "
};";
}

