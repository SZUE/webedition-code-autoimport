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

$sc1 = $sc2 = [];

// define shortcuts
$shortCuts_left = [];
$shortCuts_right = [];

if(defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS')){
	$shortCuts_left[] = 'open_document';
	$shortCuts_left[] = 'new_document';
	$shortCutsDocs = '1';
} else {
	$shortCutsDocs = '0';
}

if(defined('TEMPLATES_TABLE')){
	$shortCutsTemplates = (we_base_permission::hasPerm('CAN_SEE_TEMPLATES')) ? '1' : '0';
	if(we_base_permission::hasPerm('NEW_TEMPLATE')){
		$shortCuts_left[] = 'new_template';
	}
} else {
	$shortCutsTemplates = '0';
}
$shortCuts_left[] = 'new_directory';
if(defined('FILE_TABLE') && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS')){
	$shortCuts_left[] = 'unpublished_pages';
}
if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES')){
	$shortCuts_right[] = 'unpublished_objects';
	$shortCutsObjects = '1';
} else {
	$shortCutsObjects = '0';
}
if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm('NEW_OBJECTFILE')){
	$shortCuts_right[] = 'new_object';
}

if(defined('OBJECT_TABLE')){
	$shortCutsClasses = (we_base_permission::hasPerm('CAN_SEE_OBJECTS')) ? '1' : '0';
	if(we_base_permission::hasPerm('NEW_OBJECT')){
		$shortCuts_right[] = 'new_class';
	}
} else {
	$shortCutsClasses = '0';
}
if(we_base_permission::hasPerm('EDIT_SETTINGS')){
	$shortCuts_right[] = 'preferences';
}

$aPrefs = [
	'sct' => ['width' => $small,
		'height' => 210,
		'res' => 0,
		'cls' => 'red',
		'csv' => implode(',', $sc1) . ';' . implode(',', $sc2),
		'dlgHeight' => 435,
		'isResizable' => 1
	],
	'rss' => ['width' => $small,
		'height' => 307,
		'res' => 0,
		'cls' => 'yellow',
		'csv' => base64_encode('http://www.webedition.org/de/feeds/aktuelles.xml') . ',111000,0,110000,1',
		'dlgHeight' => 480,
		'isResizable' => 1
	],
	'mfd' => ['width' => $large,
		'height' => 210,
		'res' => 1,
		'cls' => 'lightCyan',
		'csv' => $shortCutsDocs . $shortCutsTemplates . $shortCutsObjects . $shortCutsClasses . ';0;5;00;',
		'dlgHeight' => 435,
		'isResizable' => 0
	],
	'shp' => ['width' => $large,
		'height' => 212,
		'res' => 1,
		'cls' => 'lightCyan',
		'csv' => '1111;3;10000;',
		'dlgHeight' => 435,
		'isResizable' => 0
	],
	'msg' => ['width' => $small,
		'height' => 100,
		'res' => 0,
		'cls' => 'lightCyan',
		'csv' => '',
		'dlgHeight' => 140,
		'isResizable' => 1
	],
	'fdl' => ['width' => $large,
		'height' => 210,
		'res' => 1,
		'cls' => 'orange',
		'csv' => '',
		'dlgHeight' => 435,
		'isResizable' => 0
	],
	'usr' => ['width' => $small,
		'height' => 210,
		'res' => 0,
		'cls' => 'lightCyan',
		'csv' => '',
		'dlgHeight' => 140,
		'isResizable' => 1
	],
	'upb' => ['width' => $small,
		'height' => 210,
		'res' => 0,
		'cls' => 'lightCyan',
		'csv' => $shortCutsDocs . $shortCutsObjects,
		'dlgHeight' => 190,
		'isResizable' => 1
	],
	'mdc' => ['width' => $small,
		'height' => 307,
		'res' => 0,
		'cls' => 'white',
		'csv' => ';10;',
		'dlgHeight' => 450,
		'isResizable' => 1
	],
	'pad' => ['width' => $large,
		'height' => 307,
		'res' => 1,
		'cls' => 'blue',
		'csv' => base64_encode(g_l('cockpit', '[notepad_defaultTitle_DO_NOT_TOUCH]')) . ',30020',
		'dlgHeight' => 560,
		'isResizable' => 0
	],
];

$aCfgProps = [
	[
		["pad", "blue", 1, base64_encode(g_l('cockpit', '[notepad_defaultTitle_DO_NOT_TOUCH]')) . ',30020'],
		["mfd", "green", 1, $shortCutsDocs . $shortCutsTemplates . $shortCutsObjects . $shortCutsClasses . ';0;5;00;']
	],
	[
		["rss", "yellow", 1, base64_encode('http://www.webedition.org/de/feeds/aktuelles.xml') . ',111000,0,110000,1'],
		["sct", "red", 1, implode(',', $shortCuts_left) . ';' . implode(',', $shortCuts_right)]
	]
];

$aTopRssFeeds = g_l('topFeeds', '');
for($i = 0; $i < count($aTopRssFeeds); $i++){
	foreach($aTopRssFeeds[$i] as $k => $v){
		$aTopRssFeeds[$i][$k] = base64_encode($v);
	}
}
//$aCfgProps[]= $aTopRssFeeds;

$jsoCfg = [
	'iDlgWidth' => intval($iDlgWidth),
	'_noResizeTypes' => ['pad'],
];

foreach($aPrefs as $type => $prefs){
	$jsoCfg[$type . '_props_'] = [
		'width' => intval($prefs["width"]),
		'height' => intval($prefs["height"]),
		'res' => $prefs["res"],
		'cls' => $prefs["cls"],
		'iDlgHeight' => intval($prefs["dlgHeight"]),
	];
}
