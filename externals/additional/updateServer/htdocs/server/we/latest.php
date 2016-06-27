<?php
/*
 * simple script that checks for the latest webEdition version available
 *
 * parameters:
 * 		beta: 			true/false - check for unpublished beta releases
 * 		language:		check for a specific language, i.e. "English_UTF-8" (default: all)
 * 		version:		check for we5 or we6 (default: we6)
 * 		style:			"dotted" or "plain" (default: plain)
 * 		versiononly:	output version number only without publishing date
 * 		format:			"json" or "xml" (default: json) only used if "versiononly" is not set
 *
 * output:
 * 		version number
 * 		release date
 * 		available languages
 */

ini_set("log_errors", 1);
ini_set("error_reporting", E_ALL);
ini_set("error_log", $_SERVER["DOCUMENT_ROOT"] . "/php_errors.log");

// this script fetches the most recent webEdition version from the database
// for webEdition 6
// uncomment this to disable the cache:
$disableCache = true;
// check which output format to use:
$formats = array("json", "array");

if(!isset($_REQUEST["format"]) || !in_array($_REQUEST["format"], $formats)){
	$format = "json";
} else {
	$format = $_REQUEST["format"];
}
$beta = false;
$verStr = "rel";
if(!empty($_REQUEST["beta"]) && $_REQUEST["beta"] == "true" && !empty($_REQUEST["supp"])){
	$beta = true;
	$branch = !empty($_REQUEST["branch"]) ? $_REQUEST["branch"] : "beta";
} else {
	$branch = 'beta';
}
// check cache:
if($format == "json"){
	$cachefile = dirname(__FILE__) . "/latest.tmp/" . $branch . ".json";
} else {
	$cachefile = dirname(__FILE__) . "/latest.tmp/" . $branch . ".php";
}

$cachelt = 43200; // 43200 seconds = 12 hours
if(!isset($disableCache) && is_readable($cachefile) && filemtime($cachefile) >= (time() - $cachelt)){
	$str = file_get_contents($cachefile);
	if(!empty($str)){
		echo $str;
		exit;
	}
}

include("./conf/conf.inc.php");
include("./conf/define.inc.php");

$db_versioning_down = false;
require_once './database/we_database_base.class.php';
require_once './database/we_database_mysqli.class.php';
require_once './include/we_db_tools.inc.php';

$GLOBALS['DB_WE'] = new DB_WE();

$latest = $GLOBALS['DB_WE']->getHash('SELECT DISTINCT(version), date,(type="release") AS islive,svnrevision FROM ' . VERSION_TABLE . ' WHERE ' . ($beta === true ? 'branch="' . $GLOBALS['DB_WE']->escape($branch) . '" AND type IN ("live","alpha","beta","rc")' : 'type="release"') . ' ORDER BY version DESC LIMIT 1');
$latestVersion = $latest ? $latest["version"] : 0;

// create dotted version
$dotted = "";
for($i = 0; $i < strlen($latestVersion); $i++){
	if($i < (strlen($latestVersion) - 1)){
		$dotted .= $latestVersion[$i] . ".";
	} else {
		$dotted .= $latestVersion[$i];
	}
}
//$latest["dotted"] = substr($dotted,-1);
$latest["dotted"] = $dotted;

// fetch languages:
if(!empty($latestVersion)){
	$GLOBALS['DB_WE']->query("SELECT DISTINCT(language) FROM " . SOFTWARE_LANGUAGE_TABLE . ' WHERE version=' . $latestVersion);
	while($GLOBALS['DB_WE']->next_record()){
		$row = $GLOBALS['DB_WE']->getRecord();
		if(substr($row['language'], -6) == "_UTF-8"){
			$name = substr($row['language'], 0, -6);
			$charset = 'UTF-8';
		} else {
			$name = $row['language'];
			$charset = "ISO-8859-1";
		}
		$latest['lang'][] = array(
			"name" => $name,
			"charset" => $charset,
			"beta" => 0
		);
	}
	//print_r($allLanguages);
}

// create output:
switch($format){
	case "array":
		$out = base64_encode(serialize($latest));
		break;
	case "json":
	default:
		$out = json_encode($latest);
		break;
}

// write to cache
file_put_contents($cachefile, $out);
echo $out;
