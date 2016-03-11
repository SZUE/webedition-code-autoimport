<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nightly bulder</title>
</head>

<body>

<?php
/*
***************************************************************************
*   Copyright (C) 2007-2008 by Sixdegrees                                 *
*   cesar@sixdegrees.com.br                                               *
*   "Working with freedom"                                                *
*   http://www.sixdegrees.com.br                                          *
*                                                                         *
*   Permission is hereby granted, free of charge, to any person obtaining *
*   a copy of this software and associated documentation files (the       *
*   "Software"), to deal in the Software without restriction, including   *
*   without limitation the rights to use, copy, modify, merge, publish,   *
*   distribute, sublicense, and/or sell copies of the Software, and to    *
*   permit persons to whom the Software is furnished to do so, subject to *
*   the following conditions:                                             *
*                                                                         *
*   The above copyright notice and this permission notice shall be        *
*   included in all copies or substantial portions of the Software.       *
*                                                                         *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       *
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     *
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, *
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR *
*   OTHER DEALINGS IN THE SOFTWARE.                                       *
***************************************************************************
*/
error_reporting(E_ALL);


require($_SERVER['DOCUMENT_ROOT']."/lib/phpsvnclient.php");

function getfiledescription($svnpath,$isQuery,$isLang,$isBetalang=false,$areLangsExternal=false){
	//imi
	$externals = array(
		'zend' => array(
			'needle' => '/externals/Zend/Zend-FW1/',
			'replace' => 'webEdition/lib/Zend'
			),
		'jupload' => array(
			'needle' => '/externals/java/jupload/',
			'replace' => 'webEdition/jupload'
			),
	);
	//imi

	$entry=array();
	$entry['svnpath']=$svnpath;
	if($isQuery){
		if( (strpos($svnpath,'/'.$GLOBALS['targetWEbranchDir'].'/webEdition/liveUpdate/sqldumps/tbl') !== false && strpos($svnpath,'/'.$GLOBALS['targetWEbranchDir'].'/webEdition/liveUpdate/sqldumps/tbl') == 0) ){
			//versions >= 6.3.5.1
			$entry['targetpath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/webEdition/liveUpdate/sqldumps/',$GLOBALS['destination'].'version'.$GLOBALS['targetWEVersion'].'/queries/',$svnpath);
			$entry['frompath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/webEdition/liveUpdate/sqldumps/',$GLOBALS['source'].'webEdition/liveUpdate/sqldumps/',$svnpath);
		} else{
			$entry['targetpath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/additional/sqldumps/',$GLOBALS['destination'].'version'.$GLOBALS['targetWEVersion'].'/queries/',$svnpath);
			$entry['frompath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/additional/sqldumps/',$GLOBALS['source'].'additional/sqldumps/',$svnpath);
		}
		$entry['isQuery'] = true;
		$entry['isLang'] = 0;
	} elseif($isLang){// anpassen f�r externals
		if(!$areLangsExternal){
			if( (strpos($svnpath,'/'.$GLOBALS['targetWEbranchDir'].'/additional/lang_iso/') !== false && strpos($svnpath,$GLOBALS['targetWEbranchDir'].'/additional/lang_iso/') == 0) ){
				$entry['targetpath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/additional/lang_iso/',$GLOBALS['destination'].'version'.$GLOBALS['targetWEVersion'].'/files/none/webEdition/we/include/we_language/',$svnpath);
				$entry['wepath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/additional/lang_iso/','/webEdition/we/include/we_language/',$svnpath);
				$entry['frompath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/additional/lang_iso/',$GLOBALS['source'].'additional/lang_iso/',$svnpath);
			} else {
				$entry['targetpath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/webEdition/we/include/we_language/',$GLOBALS['destination'].'version'.$GLOBALS['targetWEVersion'].'/files/none/webEdition/we/include/we_language/',$svnpath);
				$entry['wepath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/webEdition/we/include/we_language/','/webEdition/we/include/we_language/',$svnpath);
				$entry['frompath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/webEdition/we/include/we_language/',$GLOBALS['source'].'webEdition/we/include/we_language/',$svnpath);
			}
		} else{
			$entry['targetpath'] = str_replace('/'.$GLOBALS['externalsDir'].'/language/',$GLOBALS['destination'].'version'.$GLOBALS['targetWEVersion'].'/files/none/webEdition/we/include/we_language/',$svnpath);
			$entry['wepath'] = str_replace('/'.$GLOBALS['externalsDir'].'/language/','/webEdition/we/include/we_language/',$svnpath);
			$entry['frompath'] = str_replace('/'.$GLOBALS['externalsDir'].'/language/',$GLOBALS['source'].'webEdition/we/include/we_language/',$svnpath);
		}
		$entry['isLang'] = true;
		$entry['isQuery'] = 0;
		$entry['$isBetalang'] = $isBetalang;
	} else {
		//imi: find externals, not langfiles
		foreach($externals as $extkey => $extval){
			if(strpos($svnpath, $extval['needle']) !== false && strpos($svnpath, $extval['needle']) === 0){
				$entry['targetpath'] = str_replace($extval['needle'], $GLOBALS['destination'].'version'.$GLOBALS['targetWEVersion'].'/files/none/'.$extval['replace'].'/',$svnpath);
				$entry['frompath'] = str_replace($extval['needle'], $GLOBALS['source'].$extval['replace'].'/',$svnpath);
				$entry['wepath'] = str_replace($extval['needle'], '/'.$extval['replace'].'/',$svnpath);
				$entry['isLang'] = 0;
				$entry['isQuery'] = 0;
				return $entry;
			}
		}
		//imi

		$entry['targetpath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/webEdition/',$GLOBALS['destination'].'version'.$GLOBALS['targetWEVersion'].'/files/none/webEdition/',$svnpath);
		$entry['frompath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/webEdition/',$GLOBALS['source'].'webEdition/',$svnpath);
		$entry['wepath'] = str_replace('/'.$GLOBALS['targetWEbranchDir'].'/webEdition/','/webEdition/',$svnpath);
		$entry['isLang'] = 0;
		$entry['isQuery'] = 0;
	}
	return $entry;

}
function getsouredir($path){
	$files = scandir($path);
	$addfiles = array();
	foreach ($files as $key => &$entry){
		if ($entry=='.' || $entry=='..' || $entry=='.svn'){
			unset($files[$key]);
		} else {
			$entry=$path.'/'.$entry;
			if (is_dir($entry)){
				unset($files[$key]);
				$addfiles[] = getsouredir($entry);
			}
		}

	}
	foreach ($addfiles as $filearray){
		$files = array_merge($files,$filearray);
	}
	return $files;
}


/**
 *  Creating new phpSVNClient Object.
 */
//$svn  = new phpsvnclient("https://webedition.svn.sourceforge.net/svnroot/webedition/");
$svn  = new phpsvnclient("https://svn.code.sf.net/p/webedition/code/");

/**
 *  Repository URL
 */
//$svn->setRepository("http://php-ajax.googlecode.com/svn/");
//$svn->setRepository("https://webedition.svn.sourceforge.net/svnroot/webedition/");

$latestVersion = $svn->getVersion();
echo "Latest SVN-Revision: " . $latestVersion.'<br/>';


//$compareVersion = 1790;
//$compareVersion = 2982;
$compareVersion = 2988;
$step1Version = 2989;
//$compareVersion = 2132;
//$compareVersion = 2892;


$compareVersion = 3282;//6.2.3.0
$step1Version = 3293;
$compareVersion = 3361;//6.2.4.0
$step1Version = 3361;
$compareVersion = 3425;//6.2.5.0
$step1Version = 3425;

$compareVersion = 3624;//6.2.6.0
$step1Version = 3624;

$compareVersion = 4334;//6.2.7.0
$step1Version = 4334;

$language_limit = 6300;//Test auf kleiner

$GLOBALS['targetWEVersion']= "6271";
$GLOBALS['targetWEVersionString']= "6.2.7.1";
$GLOBALS['targetWEtype']= "nightly-build";
$GLOBALS['targetWEtypeversion']= "0";
$GLOBALS['targetWEbranch']= "trunk";
$GLOBALS['targetWEbranchDir']= "trunk";
$GLOBALS['takeSnapshot']= false;

//$GLOBALS['targetWEVersion']= "6250";
//$GLOBALS['targetWEVersionString']= "6.2.5.0";
//$GLOBALS['targetWEtype']= "release";
/*
		$GLOBALS['targetWEbranch']= 'trunk';
		$GLOBALS['targetWEbranchDir']= 'trunk';
		$GLOBALS['targetWEtype']= "release";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6310";
		$GLOBALS['targetWEVersionString']= "6.3.1.0";
		$compareVersion = 4465;
		$step1Version = 4466;

		$GLOBALS['targetWEtype']= "release";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6320";
		$GLOBALS['targetWEVersionString']= "6.3.2.0";
		$compareVersion = 4501;
		$step1Version = 4502;
		//$GLOBALS['takeSnapshot']= true;

		$GLOBALS['targetWEtype']= "release";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6330";
		$GLOBALS['targetWEVersionString']= "6.3.3.0";
		$compareVersion = 4563;
		$step1Version = 4564;


		$GLOBALS['targetWEtype']= "nightly-build";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6331";
		$GLOBALS['targetWEVersionString']= "6.3.3.1";
		$compareVersion = 4787;
		$step1Version = 4788;

		$GLOBALS['targetWEtype']= "beta";
		$GLOBALS['targetWEtypeversion']= "1";
		$GLOBALS['targetWEVersion']= "6331";
		$GLOBALS['targetWEVersionString']= "6.3.3.1";
		$compareVersion = 4787;
		$step1Version = 4788;

		$GLOBALS['targetWEtype']= "release";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6340";
		$GLOBALS['targetWEVersionString']= "6.3.4.0";
		$compareVersion = 4787;
		$step1Version = 4788;

		$GLOBALS['targetWEtype']= "nightly-build";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6341";
		$GLOBALS['targetWEVersionString']= "6.3.4.1";
		$compareVersion = 5371;
		$step1Version = 5372;

		$GLOBALS['targetWEtype']= "release";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6350";
		$GLOBALS['targetWEVersionName']= "";
		$GLOBALS['targetWEVersionString']= "6.3.5.0";
		$compareVersion = 5371;
		$step1Version = 5372;

		$GLOBALS['targetWEtype']= "beta";
		$GLOBALS['targetWEtypeversion']= "1";
		$GLOBALS['targetWEVersion']= "6351";
		$GLOBALS['targetWEVersionName']= "6.3.6 Beta 1";
		$GLOBALS['targetWEVersionString']= "6.3.5.1";
		$compareVersion = 5512;
		$step1Version = 5513;
		
		$GLOBALS['targetWEtype']= "nightly-build";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6352";
		$GLOBALS['targetWEVersionName']= "6.3.5.2 Nightly";
		$GLOBALS['targetWEVersionString']= "6.3.5.2";
		$compareVersion = 5512;
		$step1Version = 5513;
		
		$GLOBALS['targetWEtype']= "release";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6360";
		$GLOBALS['targetWEVersionName']= "6.3.6";
		$GLOBALS['targetWEVersionString']= "6.3.6.0";
		$compareVersion = 5512;
		$step1Version = 5513;
		
		$GLOBALS['targetWEtype']= "nightly-build";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6361";
		$GLOBALS['targetWEVersionName']= "6.3.6.1 Nightly";
		$GLOBALS['targetWEVersionString']= "6.3.6.1";
		$compareVersion = 5996;
		$step1Version = 5997;

		$GLOBALS['targetWEtype']= "beta";
		$GLOBALS['targetWEtypeversion']= "1";
		$GLOBALS['targetWEVersion']= "6362";
		$GLOBALS['targetWEVersionName']= "6.3.7 Beta 1";
		$GLOBALS['targetWEVersionString']= "6.3.6.2";
		$compareVersion = 5996;
		$step1Version = 5997;
*/
		
		$GLOBALS['targetWEtype']= "nightly-build";
		$GLOBALS['targetWEtypeversion']= "0";
		$GLOBALS['targetWEVersion']= "6363";
		$GLOBALS['targetWEVersionName']= "6.3.6.3 Nightly";
		$GLOBALS['targetWEVersionString']= "6.3.6.3";
		$compareVersion = 5996;
		$step1Version = 5997;
		

if( isset($_REQUEST['branch']) ){
	switch($_REQUEST['branch']){
		case 'main-develop':
			$GLOBALS['targetWEbranch']= 'main-develop';
			$GLOBALS['targetWEbranchDir']= 'branches/main-develop';
			$GLOBALS['targetWEtype']= "nightly-build";
			$GLOBALS['targetWEtypeversion']= "0";
			$GLOBALS['targetWEVersion']= "6382";
			$GLOBALS['targetWEVersionName']= "6.3.8.2 Nightly";
			$GLOBALS['targetWEVersionString']= "6.3.8.2";
			$compareVersion = 4799;
			$step1Version = 5513;
			//$GLOBALS['takeSnapshot']= true;
			//exit;
		case 'tag':
			//to build from tag run: http://nightly-builder.webedition.org/index.php?branch=tag&tag=6.3.6.1
			if(isset($_REQUEST['tag']))
			$GLOBALS['targetWEbranch']= 'tag';
			$GLOBALS['targetWEbranchDir']= 'tags/' . $_REQUEST['tag'];
			$GLOBALS['targetWEtype']= "nightly-build";
			$GLOBALS['targetWEtypeversion']= "0";
			$GLOBALS['targetWEVersion']= "6361";
			$GLOBALS['targetWEVersionName']= "6.3.6.1 Nightly";
			$GLOBALS['targetWEVersionString']= "6.3.6.1";
			$compareVersion = 5996;
			$step1Version = 5997;
		}
}

$GLOBALS['destination'] = '/kunden/343047_10825/rp-hosting/1/1000/'.'sites/update.webedition.org/htdocs/files/we/';
$GLOBALS['source'] = '/kunden/343047_10825/rp-hosting/1/1000/'.'build/svn/'.$GLOBALS['targetWEbranchDir'].'/';
$GLOBALS['comparesource'] = '/kunden/343047_10825/rp-hosting/1/1000/'.'build/svn';
//imi
$GLOBALS['externalsDir'] = "externals";
//imi
echo "<pre>".print_r($GLOBALS['source'],true)."</pre>";

//get version from build/svn/trunk/.svn or from branch
if (file_exists($GLOBALS['source'].'.svn/all-wcprops') ){
	//echo "<pre>".print_r($GLOBALS['source'].'.svn/all-wcprops',true)."</pre>";
 	$vcontent=  file( $GLOBALS['source'].'.svn/all-wcprops');
 	//echo "<pre>".print_r($vcontent,true)."</pre>";
}
echo "<pre>".print_r($vcontent,true)."</pre>";
$targetversion = trim(str_replace('/p/webedition/code/!svn/ver/','',str_replace('/'.$GLOBALS['targetWEbranchDir'],'',$vcontent[3])));
//$targetversion = 2631;
echo "Branch: ".$GLOBALS['targetWEbranch']."<br/>";
echo "Vergleichs SVN-Revision: ".$compareVersion."<br/>";
echo "Ziel SVN-Revision, im build/svn enthalten, (latest): ".$targetversion." (".$latestVersion.")<br/>" ;
echo "Ziel WE-Version-Revision: ".$GLOBALS['targetWEVersion']."<br/>";

//die Tabelleneinträge müssen vorhanden sein! es werden nur updates durchgeführt
$dbserver = "mysql5.webedition.org";
$dbuser = "db343047_3";
$dbdb = "db343047_3";
$dbpassword = "WdcAqcd2aq";

$DBlink = mysql_connect ($dbserver,$dbuser, $dbpassword );
if (!$DBlink) {
    die('keine Verbindung möglich: ' . mysql_error());
}
$DB = mysql_select_db($dbdb, $DBlink);
if (!$DB) {
    die ('Kann '.$dbdb.' nicht benutzen : ' . mysql_error());
}
echo"ja2<br/>";
if($GLOBALS['takeSnapshot']){
	$directoy=$GLOBALS['source'];
	if(is_dir($directoy)) {
				$DirFileObjectsArray= array();
				$DirFileObjects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoy));
				foreach($DirFileObjects as $name => $object){
					if(substr($name,-2) != '/.'  && substr($name,-3) != '/..' && strpos($name,'/.svn/')===false){
						$DirFileObjectsArray[]=str_replace($GLOBALS['comparesource'],'',$name);
					}
				}
				sort($DirFileObjectsArray);

	};
	echo "<pre>Scanned dir: ".print_r($DirFileObjectsArray,true)."</pre>";
	$modifiedfiles= $DirFileObjectsArray;
} else { //normale Verarbeitung
	if(isset($_REQUEST['branch']) && $_REQUEST['branch'] == 'tag'){
		//build list of externals as in default case
		$versionLogsExternals = $svn->getRepositoryLogs($GLOBALS['externalsDir'],$compareVersion,$targetversion);
		$versionLogsExternalsTmp = array();
		foreach($versionLogsExternals as $v){
			$versionLogsExternalsTmp[$v['version']] = $v;
		}
		ksort($versionLogsExternalsTmp);
		$versionLogsExternals = array();
		foreach($versionLogsExternalsTmp as $v){
			$versionLogsExternals[] = $v;
		}

		//we take all files from tag as mod_files: they are unique and complete!
		$versionLogs = array();
		$DirFileObjects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($GLOBALS['source']));
		foreach($DirFileObjects as $name => $object){
			if(substr($name,-2) != '/.'  && substr($name,-3) != '/..' && strpos($name,'/.svn/')===false){
				$versionLogs[] = array('version' => $targetversion, 'files' => array(str_replace($GLOBALS['comparesource'],'',$name)), 'mod_files' => array(str_replace($GLOBALS['comparesource'],'',$name)));
			}
		}

		if(is_array($versionLogsExternals)){
			$versionLogs = array_merge($versionLogs,$versionLogsExternals);
		}
		//exit();
	}else{
		//start 
		echo "++++++++++++++++++++++++++++++<br>";
		echo $svn->getRepositoryLogs($GLOBALS['targetWEbranchDir'],$compareVersion,$targetversion,true);
		exit();
		//end
		$versionLogs1 = $svn->getRepositoryLogs($GLOBALS['targetWEbranchDir'],$compareVersion,$step1Version);
		echo "1: <br/>";
		//echo "<pre>".print_r($versionLogs1,true)."</pre>";
		$versionLogs2 = $svn->getRepositoryLogs($GLOBALS['targetWEbranchDir'],$step1Version,$targetversion);
		echo "2 int: <br/>";
		//echo "<pre>".print_r($versionLogs2,true)."</pre>";
		$versionLogs1externals = $svn->getRepositoryLogs($GLOBALS['externalsDir'],$compareVersion,$step1Version);
		echo "3: <br/>";
		//echo "<pre>".print_r($versionLogs1externals,true)."</pre>";
		$versionLogs2externals = $svn->getRepositoryLogs($GLOBALS['externalsDir'],$step1Version,$targetversion);
		echo "4: <br/>";
		//echo "<pre>".print_r($versionLogs2externals,true)."</pre>";
		if(is_array($versionLogs1) && is_array($versionLogs1externals)){
			$versionLogs1 = array_merge($versionLogs1,$versionLogs1externals);
		}
		if(is_array($versionLogs2) && is_array($versionLogs2externals)){
			$versionLogs2 = array_merge($versionLogs2,$versionLogs2externals);
		}

		//echo "<pre>XXX".print_r($versionLogs2,true)."</pre>";
		echo "Anzahl Elemente versionLogs 1: ".count($versionLogs1)."<br/>";
		echo "Anzahl Elemente versionLogs 2: ".count($versionLogs2)."<br/>";
		if(is_array($versionLogs1) && is_array($versionLogs2)){
			echo "Array OK<br/>";
			$versionLogs = array_merge($versionLogs1,$versionLogs2);
		}

		//imi: unset double entries and sort by svn
		$versionLogsTmp = array();
		foreach($versionLogs as $v){
			$versionLogsTmp[$v['version']] = $v;
		}
		ksort($versionLogsTmp);
		$versionLogs = array();
		foreach($versionLogsTmp as $v){
			$versionLogs[] = $v;
		}
		//imi
		echo "Anzahl Elemente merged ".count($versionLogs)."<br/>";
	}

	//exit();
	//echo"ja4versionLogs<br/>";
	//echo "<pre>".print_r($versionLogs,true)."</pre>";
	//$filesTotal= $svn->getDirectoryTree('/',$targetversion);
	//echo "<pre>XXX".print_r($filesTotal,true)."</pre>";
	//echo"ja5";

	$addfiles = array();
	$delfiles = array();
	$modfiles=  array();
	//echo "<hr>logentries<hr>";
	foreach($versionLogs as $logentry){
	//echo "<pre>".print_r($logentry,true)."</pre>";
		if(isset($logentry['mod_files'])){
			foreach ($logentry['mod_files'] as $filepath){
				//imi
				//$modfiles[] = $filepath;
				if(strpos($filepath, "externals/additional") === false) {
					$modfiles[] = $filepath;
				}
				//imi
			}
		}
		if(isset($logentry['add_files'])){
			foreach ($logentry['add_files'] as $filepath){
				//imi
				//$addfiles[] = $filepath;
				if(strpos($filepath, "externals/additional") === false) {
					$addfiles[] = $filepath;
				}
				//imi
			}
		}
		if(isset($logentry['del_files'])){
			foreach ($logentry['del_files'] as $filepath){
				//imi
				//$delfiles[] = $filepath;
				if(strpos($filepath, "externals/additional") === false) {
					$delfiles[] = $filepath;
				}
				//imi
			}
		}
	}
//echo "<hr>ende logentries<hr>";
//exit();
echo "modfiles:<br>";
echo "<pre>".var_dump($modfiles,true)."</pre>";
echo "end modfiles:<br>";
	$modfiles= array_unique($modfiles);
	$delfiles= array_unique($delfiles);
	$addfiles= array_unique($addfiles);


	//Zend filtern
	/*
	foreach ($modfiles as $key => &$filepath){
		if( strpos($filepath,'webEdition/lib/Zend/') !== false ){unset($modfiles[$key]);}
	}
	foreach ($delfiles as $key => &$filepath){
		if( strpos($filepath,'webEdition/lib/Zend/') !== false ){unset($delfiles[$key]);}
	}
	foreach ($addfiles as $key => &$filepath){
		if( strpos($filepath,'webEdition/lib/Zend/') !== false ){unset($addfiles[$key]);}
	}
	*/


	//echo "Mod<pre>XXX".print_r($modfiles,true)."</pre>";
	//echo "Del<pre>XXX".print_r($delfiles,true)."</pre>";
	//echo "Add<pre>XXX".print_r($addfiles,true)."</pre>";

	$additionalfiles = array(); //directories to create


	foreach ($addfiles as $key =>  &$filepath){
		//echo $GLOBALS['comparesource'].$filepath;
		if (is_dir($GLOBALS['comparesource'].$filepath) ) {
			$additionalfiles = array_merge($additionalfiles,getsouredir($GLOBALS['comparesource'].$filepath));
		}
	}
	foreach ($additionalfiles as &$file){
		$file=str_replace($GLOBALS['comparesource'],'',$file);
	}
	$additionalfiles = array_unique($additionalfiles);
	//echo "<pre>additional: ".print_r($additionalfiles,true)."</pre>";
	$addfiles = array_merge($addfiles,$additionalfiles);
	$remainDel=array_diff($delfiles,$addfiles);
	//echo "<pre>to be deleted: ".print_r($remainDel,true)."</pre>";

	//echo "ADD<pre>XXX".print_r($addfiles,true)."</pre>";
	$addfiles = array_unique($addfiles);
	//merge add und mod
	$modifiedfiles = array_merge($modfiles,$addfiles);
	//kille die die gelöscht werden sollen
	$remainingModfiles=array_diff($modifiedfiles,$remainDel);
	$modifiedfiles = array_unique($remainingModfiles);
} // Ende normale Verarbeitung (im Gegensatz zum Snapshot)
sort($modifiedfiles);

echo "<br/>Mod<pre>".print_r($modifiedfiles,true)."</pre>";
//find sql-files

$sqlfiles=array();
foreach ($modifiedfiles as $key =>  &$filepath){
	if( (strpos($filepath,'/'.$GLOBALS['targetWEbranchDir'].'/webEdition/liveUpdate/sqldumps/tbl') !== false && strpos($filepath,'/'.$GLOBALS['targetWEbranchDir'].'/webEdition/liveUpdate/sqldumps/tbl') == 0) ){
		//versions >= 6.3.5.1
		$sqlfiles[] = $filepath;
	}
	else if( (strpos($filepath,'/'.$GLOBALS['targetWEbranchDir'].'/additional/sqldumps/tbl') !== false && strpos($filepath,'/'.$GLOBALS['targetWEbranchDir'].'/additional/sqldumps/tbl') ==0) ){
		$sqlfiles[] = $filepath;
		unset($modifiedfiles[$key]);
	}
}
//echo "<pre>".print_r($sqlfiles,true)."</pre>";
//echo "Mod<pre>XXX".print_r($modifiedfiles,true)."</pre>";
//Sprachfiles herausfiltern
if ($GLOBALS['targetWEVersion'] < $language_limit){
	$languages=array('Deutsch'=>'', 'Deutsch_UTF-8'=>'','Dutch'=>'', 'Dutch_UTF-8'=>'','English'=>'', 'English_UTF-8'=>'','Finnish'=>'', 'Finnish_UTF-8'=>'','French_UTF-8'=>'','Polish_UTF-8'=>'','Russian_UTF-8'=>'','Spanish_UTF-8'=>'');
} else {
	$languages=array('Deutsch'=>'', 'Dutch'=>'','English'=>'','Finnish'=>'','French'=>'','Polish'=>'','Russian'=>'','Spanish'=>'');
}

$alllang=$languages;
$languagesExt = array();
foreach($languages as $lkey => $lang){
	if ($GLOBALS['targetWEVersion'] < $language_limit){
		if(strpos($lkey,'UTF-8') === false){//iso
			$pathstr = '/'.$GLOBALS['targetWEbranchDir'].'/additional/lang_iso/'.$lkey.'/';
		} else {
			$pathstr = '/'.$GLOBALS['targetWEbranchDir'].'/webEdition/we/include/we_language/'.$lkey.'/';
		}
	} else {
		$pathstr = '/'.$GLOBALS['targetWEbranchDir'].'/webEdition/we/include/we_language/'.$lkey.'/';
		//imi
		$extPathStr = '/'.$GLOBALS['externalsDir'].'/language/'.$lkey.'/';
		//imi

	}
	foreach($modifiedfiles as $key =>  &$filepath){
		if( (strpos($filepath,$pathstr) !== false && strpos($filepath,$pathstr) ==0) ){
			$languages[$lkey][] = $filepath;
			unset($modifiedfiles[$key]);
		}
	}

	//imi
	$extPathStr = '/'.$GLOBALS['externalsDir'].'/language/'.$lkey.'/';
	foreach($modifiedfiles as $key => &$filepath){
		if( (strpos($filepath,$extPathStr) !== false && strpos($filepath,$extPathStr) === 0) ){
			$languagesExt[$lkey][] = $filepath;
			unset($modifiedfiles[$key]);
		}
	}
	//imi
}
//imi
$areLangsExternal = false;
if(count($languagesExt) > 0){
	$languages = $languagesExt;
	$areLangsExternal = true;
}
//imi

//echo "Lang<pre>XXX".print_r($languages,true)."</pre>";
// rest von additional kann weg und das we_conf muss noch raus
foreach ($modifiedfiles as $key =>  &$filepath){
	if( (strpos($filepath,'/'.$GLOBALS['targetWEbranchDir'].'/additional/') !== false && strpos($filepath,'/'.$GLOBALS['targetWEbranchDir'].'/additional/') ==0) || (strpos($filepath,'/'.$GLOBALS['targetWEbranchDir'].'/webEdition/we/include/conf/we_conf') !== false && strpos($filepath,'/'.$GLOBALS['targetWEbranchDir'].'/webEdition/we/include/conf/we_conf') ==0)){
		unset($modifiedfiles[$key]);
	}
}
$modifiedfiles = array_merge($modifiedfiles);

echo "<pre>".print_r($modifiedfiles,true)."</pre>";
//echo "<pre>".print_r($languages,true)."</pre>";

$allfiles = array();
$allsql = array();

foreach ($sqlfiles as $sql){
	$allsql[] = getfiledescription($sql,true,false);
}
//echo'<pre>'; print_r( $allsql);echo'</pre>';
//echo'<pre>'; print_r( $languages);echo'</pre>';
foreach ($languages as $lang => $langfilepath){
	if ($GLOBALS['targetWEVersion'] < $language_limit){
		if($lang=='French_UTF-8' || $lang=='Polish_UTF-8' || $lang=='Russian_UTF-8' || $lang=='Spanish_UTF-8'){
			$betaLang=1;
		} else {
			$betaLang=0;
		}
	} else {
		if($lang=='French' || $lang=='Polish' || $lang=='Russian' || $lang=='Spanish'){
			$betaLang=1;
		} else {
			$betaLang=0;
		}

	}
	if(is_array($langfilepath)){
		foreach ($langfilepath as $lfilepath){
			$alllang[$lang][] = getfiledescription($lfilepath,false,true,$betaLang,$areLangsExternal);
		}
	}
}

//imi: here we have still extarnals (only langfiles are fikltered out
foreach ($modifiedfiles as $afilepath){
	$allfiles[] = getfiledescription($afilepath,false,false);
}

//echo "<pre>".print_r($allsql,true)."</pre>";
//echo "Allfiles<pre>".print_r($allfiles,true)."</pre>";
//echo "<pre>".print_r($alllang,true)."</pre>";
$changessql = "";
foreach ($allsql as $data) {
	if (file_exists($data['frompath'])){
		$fcontent=  file_get_contents ( $data['frompath']);
		if ($fcontent) {
			$path_parts = pathinfo($data['targetpath']);
			$dirname= $path_parts['dirname'];
			if (!file_exists($dirname)){
				mkdir($dirname, 0777,true);
			}
			if (file_put_contents ( $data['targetpath'],$fcontent ) ){
				$changessql .= '/'.$path_parts['basename'].','."\n";
			}
		}
	} else {
		//echo "<pre> nicht gefunden sql: ".print_r($data['frompath'],true)."</pre>";
	}
}
//echo "<pre>".print_r($changessql,true)."</pre>";
$changessql=rtrim($changessql);
$changessql=rtrim($changessql,',');
echo "<pre>Änderungen SQL: ".print_r($changessql,true)."</pre>";

//echo "<pre>".print_r($qsql,true)."</pre>";

$changesfiles = "";

foreach ($allfiles as $data) {
	if (file_exists($data['frompath'])){
		$fcontent=  file_get_contents ( $data['frompath']);
		if ($fcontent) {
			$path_parts = pathinfo($data['targetpath']);
			$dirname= $path_parts['dirname'];
			if (!file_exists($dirname)){
				mkdir($dirname, 0777,true);
			}
			if (file_put_contents ( $data['targetpath'],$fcontent ) ){
				$changesfiles .= $data['wepath'].','."\n";
			}
			if($path_parts['basename'] =='we_version.php'){
				$versionsfile = file($data['targetpath']);

				$versionsfile[1] = 'define("WE_VERSION","'.trim($GLOBALS['targetWEVersionString']).'");'."\n";
				$versionsfile[2] = 'define("WE_VERSION_SUPP","'.trim($GLOBALS['targetWEtype']).'");'."\n";
				$versionsfile[4] = 'define("WE_SVNREV","'.trim($targetversion).'");'."\n";
				$versionsfile[5] = 'define("WE_VERSION_SUPP_VERSION","'.trim($GLOBALS['targetWEtypeversion']).'");'."\n";
				$versionsfile[6] = 'define("WE_VERSION_BRANCH","'.trim($GLOBALS['targetWEbranch']).'");'."\n";
				$versionsfile[7] = 'define("WE_VERSION_NAME","'.trim($GLOBALS['targetWEVersionName']).'");'."\n";
				echo "<pre>".print_r($versionsfile,true)."</pre>";
				$versionsfiletext= implode("",$versionsfile);
				file_put_contents ( $data['targetpath'],$versionsfiletext );
			}
		}

	} else {
			//echo "<pre> Nicht gefunden allfiles: ".print_r($data['frompath'],true)."</pre>";
	}

}
//echo "<pre>".print_r($changesfiles,true)."</pre>";
$changesfiles=rtrim($changesfiles);
$changesfiles=rtrim($changesfiles,',');
echo "<pre>ChangeFiles: ".print_r($changesfiles,true)."</pre>";
if($GLOBALS['takeSnapshot']){$isSnapshot=1;} else {$isSnapshot=0;}
if ($changesfiles!='') {
	$qfiles= "SELECT * from `v6_changes` WHERE version= ".$GLOBALS['targetWEVersion']." AND `TYPE` = 'system' AND detail = 'files'";
	//echo $qfiles;
	$result = mysql_query($qfiles,$DBlink);
		if(mysql_num_rows ( $result )==0) {
			$qfiles = "INSERT INTO `v6_changes` (version,TYPE,detail) VALUES ('".$GLOBALS['targetWEVersion']."','system','files')";
			$result = mysql_query($qfiles,$DBlink);
			if (!$result) {
				die('Ungültige Abfrage: ' .$qfiles. ' FehlerCode'.mysql_error());
			}
		}
	$qfiles= "UPDATE `v6_changes` SET changes = '".$changesfiles."', isSnapshot='".$isSnapshot."' WHERE version= ".$GLOBALS['targetWEVersion']." AND `TYPE` = 'system' AND detail = 'files'";
	//echo $qfiles;
	$result = mysql_query($qfiles,$DBlink);
		if (!$result) {
			die('Ungültige Abfrage/Fehlender Tabelleneintrag: ' .$qfiles.' Fehlercode: '. mysql_error());
		}
	//echo "<pre>".print_r($qfiles,true)."</pre>";

	if ($changessql!='') {
		$qsql = "SELECT * FROM `v6_changes` WHERE version= ".$GLOBALS['targetWEVersion']." AND `TYPE` = 'system' AND detail = 'queries'";
		$result = mysql_query($qsql,$DBlink);
		if(mysql_num_rows ( $result )==0) {
			$qsql = "INSERT INTO `v6_changes` (version,TYPE,detail) VALUES ('".$GLOBALS['targetWEVersion']."','system','queries')";
			$result = mysql_query($qsql,$DBlink);
			if (!$result) {
				die('Ungültige Abfrage: ' .$qsql. ' FehlerCode'.mysql_error());
			}
		}
		$qsql= "UPDATE `v6_changes` SET changes = '".$changessql."', isSnapshot='".$isSnapshot."' WHERE version= ".$GLOBALS['targetWEVersion']." AND `TYPE` = 'system' AND detail = 'queries'";
		$result = mysql_query($qsql,$DBlink);
			if (!$result) {
				die('Ungültige Abfrage: ' .$qsql. ' FehlerCode'.mysql_error());
			}
	}


	foreach ($alllang as $langkey => $langdata) {
		$changeslang = '';
		if(is_array($langdata)){
			foreach ($langdata as $data){
				if (file_exists($data['frompath'])){
					$fcontent=  file_get_contents ( $data['frompath']);
					if ($fcontent) {
						$path_parts = pathinfo($data['targetpath']);
						$dirname= $path_parts['dirname'];
						if (!file_exists($dirname)){
							mkdir($dirname, 0777,true);
						}
						if (file_put_contents ( $data['targetpath'],$fcontent ) ){
							$changeslang .= $data['wepath'].','."\n";
						}
					}
				} else {
					echo "<pre>".print_r($data['frompath'],true)."</pre>";
				}
			}
		}
	//	echo "<pre>".print_r($changeslang,true)."</pre>";
		$changeslang=rtrim($changeslang);
		$changeslang=rtrim($changeslang,',');
	//	echo "<pre>".print_r($changeslang,true)."</pre>";

		if ($changeslang!=''){
			$lsql= "SELECT * FROM `v6_changes_language` WHERE version= ".$GLOBALS['targetWEVersion']." AND `type` = 'system' AND detail = 'files' AND language = '".	$langkey."'";
			$result = mysql_query($lsql,$DBlink);
			if(mysql_num_rows ( $result )==0) {
				$lsql = "INSERT INTO `v6_changes_language` (version,type,detail,language) VALUES ('".$GLOBALS['targetWEVersion']."','system','files','".$langkey."')";
				$result = mysql_query($lsql,$DBlink);
				if (!$result) {
					die('Ungültige Abfrage: ' .$lsql. ' FehlerCode'.mysql_error());
				}
			}
			$lsql= "UPDATE `v6_changes_language` SET changes = '".$changeslang."', isSnapshot='".$isSnapshot."' WHERE version= ".$GLOBALS['targetWEVersion']." AND `type` = 'system' AND detail = 'files' AND language = '".	$langkey."'";
			$result = mysql_query($lsql,$DBlink);
			if (!$result) {
				die('Ungültige Abfrage/Fehlender Tabelleneintrag: ' .$lsql.' FehlerCode:'. mysql_error());
			}
		}

	//	echo "<pre>".print_r($lsql,true)."</pre>";





		$vsql= "SELECT * FROM `v6_versions` WHERE version= ".$GLOBALS['targetWEVersion']." AND language = '".	$langkey."'";
		$result = mysql_query($vsql,$DBlink);
		if(mysql_num_rows ( $result )==0) {
			$vsql = "INSERT INTO `v6_versions` (version,language) VALUES ('".$GLOBALS['targetWEVersion']."','".$langkey."')";
			$result = mysql_query($vsql,$DBlink);
			if (!$result) {
				die('Ungültige Abfrage: ' .$vsql. ' FehlerCode'.mysql_error());
			}
		}
		if($GLOBALS['takeSnapshot']){$isSnapshot=1;} else {$isSnapshot=0;}
		$vsql ="UPDATE `v6_versions` SET versname = '".$GLOBALS['targetWEVersionName']."', svnrevision = '".$targetversion."', type='".$GLOBALS['targetWEtype']."', typeversion='".$GLOBALS['targetWEtypeversion']."', branch='".$GLOBALS['targetWEbranch']."', isSnapshot='".$isSnapshot."', date='".date('Y-m-d H:i:s')."' WHERE version= ".$GLOBALS['targetWEVersion']." AND language = '".	$langkey."'";
		$result = mysql_query($vsql,$DBlink);
		if (!$result) {
			die('Ungültige Abfrage: ' .$vsql.'Fehlercode: '. mysql_error());
		}



	//	echo "<pre>".print_r($vsql,true)."</pre>";

	}
	// für Versionen >=6266
	if ($GLOBALS['targetWEVersion'] >= $language_limit){
		$utflang= array('Deutsch_UTF-8','English_UTF-8','Dutch_UTF-8','Finnish_UTF-8','French_UTF-8','Polish_UTF-8','Russian_UTF-8','Spanish_UTF-8');
		foreach ($utflang as $langkkey => $langkey) {
			$vsql= "SELECT * FROM `v6_versions` WHERE version= ".$GLOBALS['targetWEVersion']." AND language = '".	$langkey."'";
			$result = mysql_query($vsql,$DBlink);
			if(mysql_num_rows ( $result )==0) {
				$vsql = "INSERT INTO `v6_versions` (version,language) VALUES ('".$GLOBALS['targetWEVersion']."','".$langkey."')";
				$result = mysql_query($vsql,$DBlink);
				if (!$result) {
					die('Ungültige Abfrage: ' .$vsql. ' FehlerCode'.mysql_error());
				}
			}
			if($GLOBALS['takeSnapshot']){$isSnapshot=1;} else {$isSnapshot=0;}
			$vsql ="UPDATE `v6_versions` SET versname = '".$GLOBALS['targetWEVersionName']."', svnrevision = '".$targetversion."', type='".$GLOBALS['targetWEtype']."', typeversion='".$GLOBALS['targetWEtypeversion']."', branch='".$GLOBALS['targetWEbranch']."', isSnapshot='".$isSnapshot."', date='".date('Y-m-d H:i:s')."' WHERE version= ".$GLOBALS['targetWEVersion']." AND language = '".	$langkey."'";
			$result = mysql_query($vsql,$DBlink);
			if (!$result) {
				die('Ungültige Abfrage: ' .$vsql.'Fehlercode: '. mysql_error());
			}
		}
	}
}
echo "<pre>".print_r("FERTIG",true)."</pre>";

/**
 *  Get Files from "/trunk/phpajax/" directory from the last repository version
 */
//$files_now = $svn->getDirectoryFiles("/trunk/");


//echo "<pre>".print_r($files_now,true)."</pre>";


/**
 *  Get Files from "/trunk/phpajax/"  directory from version 7
 */
//$files_7   = $svn->getDirectoryFiles("/trunk/phpajax/",7);

/**
 *  Get "/trunk/phpajax/phpajax.php"  contents from the last repository version
 */
//$phpajax_now = $svn->getFile("/trunk/phpajax/phpajax.php");

/**
 *  Get "/trunk/phpajax/phpajax.php"  contents from version 7
 */
//$phpajax_7   = $svn->getFile("/trunk/phpajax/phpajax.php",7);


/**
 *  Get all logs of /trunk/phpajax/phpajax.org from  between 2 version until the last
 */
//$logs = $svn->getRepositoryLogs(2);

/**
 *  Get all logs of /trunk/phpajax/phpajax.org from  between 2 version until 5 version.
 */
//$logs = $svn->getRepositoryLogs(2,5);


?>
</body>
</html>