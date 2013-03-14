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


// this script fetches the most recent webEdition version from the database
// for webEdition 6

//ini_set("display_errors","Off");
// uncomment this to disable the cache:
//$disableCache = true;
// check which output format to use:
$formats = array("json","array");

if(!isset($_REQUEST["format"]) || !in_array($_REQUEST["format"],$formats)) {
	$format = "json";
} else {
	$format = $_REQUEST["format"];
}
$beta = false;
$verStr = "rel";
if(isset($_REQUEST["beta"]) && $_REQUEST["beta"] == "true") {
	$beta = true;
	$verStr = "beta";
}
// check cache:
if($format == "json") {
	$cachefile = dirname(__FILE__)."/latest.tmp/".$verStr.".json";
} else {
	$cachefile = dirname(__FILE__)."/latest.tmp/".$verStr.".php";
}

$cachelt = 43200; // 43200 seconds = 12 hours
if(!isset($disableCache) && is_readable($cachefile) && filemtime($cachefile) >= (time() - $cachelt)) {
	$str = file_get_contents($cachefile);
	if(!empty($str)) {
		echo $str;
		exit;
	}
}

@include("./conf/conf.inc.php");
@include("./conf/define.inc.php");

@include("PEAR.php");
// include the PEAR Db class
require_once('DB.php');
// include the PEAR i18n class
require_once('I18Nv2.php');

$db_versioning_down = false;

$DB_Versioning = & DB::connect($DSN_versioning, $OPTIONS_versioning);

if (PEAR::isError($DB_Versioning)) {
    $db_versioning_down = true;
	exit;
} else {
	$DB_Versioning->setFetchMode(DB_FETCHMODE_ASSOC);
}
if($beta === true) {
	$query = "SELECT DISTINCT(version), date, islive FROM " . VERSION_TABLE . " ORDER BY version DESC limit 1";
} else {
	$query = "SELECT DISTINCT(version), date, islive FROM " . VERSION_TABLE . " WHERE islive = 1 ORDER BY version DESC limit 1";
}
$res =& $DB_Versioning->query($query);
$latest = $res->fetchRow();
$latestVersion = $latest["version"];

// create dotted version
$dotted = "";
for($i=0;$i<strlen($latest["version"]); $i++) {
	if($i < (strlen($latest["version"]) - 1)) {
		$dotted .= $latest["version"][$i].".";
	} else {
		$dotted .= $latest["version"][$i];
	}
}
//$latest["dotted"] = substr($dotted,-1);
$latest["dotted"] = $dotted;

// fetch languages:
if(!empty($latestVersion)) {
	$query = "SELECT DISTINCT(language), isbeta FROM " . VERSION_TABLE . " WHERE version = '" . $latestVersion . "'";
	$res =& $DB_Versioning->query($query);
	while ( $row = $res->fetchRow() ) {
		if(substr($row['language'],-6) == "_UTF-8") {
			$name = substr($row['language'],0,-6);
			$charset = "UTF-8";
		} else {
			$name = $row['language'];
			$charset = "ISO-8859-1";
		}
		$latest['lang'][] = array(
			"name" => $name,
			"charset" => $charset,
			"beta" => $row['isbeta']			
		);
	}
	//print_r($allLanguages);
	
}

// create output:
switch($format) {
	case "array":
		$out = base64_encode(serialize($latest));
		break;
	case "json":
	default:
		$out = json_encode($latest);
		break;
}

// write to cache
@file_put_contents($cachefile,$out);
echo $out;
?>