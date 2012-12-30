<?php
$GLOBALS['noSess'] = true;
$GLOBALS['WE_IS_DYN'] = 1;
$GLOBALS['we_transaction'] = '';
$GLOBALS['we_ContentType'] = 'text/webedition';
$_REQUEST['we_cmd'] = array();

if (isset($_REQUEST['pv_id']) && isset($_REQUEST['pv_tid'])) {
	$_REQUEST['we_cmd'][1] = $_REQUEST['pv_id'];
	$_REQUEST['we_cmd'][4] = $_REQUEST['pv_tid'];
} else {
	$_REQUEST['we_cmd'][1] = 12;
}

$FROM_WE_SHOW_DOC = true;

if (!isset($GLOBALS['WE_MAIN_DOC']) && isset($_REQUEST['we_objectID'])) {
	include($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/object/we_object_showDocument.inc.php');
} else {
	include($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_showDocument.inc.php');
}