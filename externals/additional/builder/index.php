<?php
//include we stuff first
define('NO_SESS', 1);
$_SERVER['DOCUMENT_ROOT'] = '/kunden/343047_10825/sites/webedition.org/nightlybuilder';
require('conf/we_conf.inc.php');
require('webEdition/we/include/we.inc.php');
$language_limit = 6266;
$GLOBALS['cli'] = false;

$GLOBALS['targetWEbranch'] = empty($_REQUEST['branch']) ? 'trunk' : $_REQUEST['branch'];
$GLOBALS['basicReleaseType'] = empty($_REQUEST['type']) ? 'nightly' : $_REQUEST['type'];
require('configurations.php');

//cleanup old error logs
$GLOBALS['DB_WE']->query('DELETE FROM ' . ERROR_LOG_TABLE . ' WHERE `Date` < DATE_SUB(NOW(), INTERVAL ' . we_base_constants::ERROR_LOG_HOLDTIME . ' DAY)');

$GLOBALS['targetWeExternalsDir'] = 'externals';
$GLOBALS['externalsDir'] = "externals";
$GLOBALS['destination'] = '/kunden/343047_10825/sites/webedition.org/update/htdocs/files/we/';
$GLOBALS['comparesource'] = '/kunden/343047_10825/build/svn';

//include configuration and get compareVersion from db
if($GLOBALS['compareWeVersion'] && !$GLOBALS['compareVersion']){
	$GLOBALS['compareVersion'] = (f('SELECT revisionTo FROM v6_versions WHERE version=' . intval($GLOBALS['compareWeVersion']) . ' LIMIT 1', '', $GLOBALS['DB_WE'])) - 3;
	if(!$GLOBALS['compareVersion']){
		$GLOBALS['compareVersion'] = (f('SELECT svnrevision FROM v6_versions WHERE version=' . intval($GLOBALS['compareWeVersion']) . ' LIMIT 1', '', $GLOBALS['DB_WE'])) - 3;
	}
	$GLOBALS['step1Version'] = $GLOBALS['compareVersion'] + 1; //nonsens...
}
echo $GLOBALS['compareVersion'];
//define('debug',1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Nightly bulder</title>
	</head>

	<body>

		<?php

//error_reporting(E_ALL);

		function getfiledescription($svnpath, $isQuery, $isLang, $isLibAdditional = false, $isBetalang = false, $areLangsExternal = false){
			$externals = array(
				'zend' => array(
					'needle' => '/externals/Zend/Zend-FW1/',
					'replace' => 'webEdition/lib/Zend'
				),
				'jupload' => array(
					'needle' => '/externals/java/jupload/',
					'replace' => 'webEdition/jupload'
				),
				'libraries' => array(
					'needle' => '/externals/libraries/',
					'replace' => 'webEdition/lib'
				),
			);

			$entry = array(
				'svnpath' => $svnpath
			);
			if($isQuery){
				if((strpos($svnpath, '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/liveUpdate/sqldumps/tbl') !== false && strpos($svnpath, '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/liveUpdate/sqldumps/tbl') == 0)){
					//versions >= 6.3.5.1
					$entry['targetpath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/liveUpdate/sqldumps/', $GLOBALS['destination'] . 'version' . $GLOBALS['targetWEVersion'] . '/queries/', $svnpath);
					$entry['frompath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/liveUpdate/sqldumps/', $GLOBALS['source'] . 'webEdition/liveUpdate/sqldumps/', $svnpath);
				} else {
					$entry['targetpath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/additional/sqldumps/', $GLOBALS['destination'] . 'version' . $GLOBALS['targetWEVersion'] . '/queries/', $svnpath);
					$entry['frompath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/additional/sqldumps/', $GLOBALS['source'] . 'additional/sqldumps/', $svnpath);
				}
				$entry['isQuery'] = true;
				$entry['isLang'] = 0;
			} elseif($isLang){
				if(!$areLangsExternal){
					if((strpos($svnpath, '/' . $GLOBALS['targetWEbranchDir'] . '/additional/lang_iso/') !== false && strpos($svnpath, $GLOBALS['targetWEbranchDir'] . '/additional/lang_iso/') == 0)){
						$entry['targetpath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/additional/lang_iso/', $GLOBALS['destination'] . 'version' . $GLOBALS['targetWEVersion'] . '/files/none/webEdition/we/include/we_language/', $svnpath);
						$entry['wepath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/additional/lang_iso/', '/webEdition/we/include/we_language/', $svnpath);
						$entry['frompath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/additional/lang_iso/', $GLOBALS['source'] . 'additional/lang_iso/', $svnpath);
					} else { // used for >6.5 doing snapshot
						$entry['targetpath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/we/include/we_language/', $GLOBALS['destination'] . 'version' . $GLOBALS['targetWEVersion'] . '/files/none/webEdition/we/include/we_language/', $svnpath);
						$entry['wepath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/we/include/we_language/', '/webEdition/we/include/we_language/', $svnpath);
						$entry['frompath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/we/include/we_language/', $GLOBALS['source'] . 'webEdition/we/include/we_language/', $svnpath);
					}
				} else {
					$entry['targetpath'] = str_replace('/' . $GLOBALS['externalsDir'] . '/language/', $GLOBALS['destination'] . 'version' . $GLOBALS['targetWEVersion'] . '/files/none/webEdition/we/include/we_language/', $svnpath);
					$entry['wepath'] = str_replace('/' . $GLOBALS['externalsDir'] . '/language/', '/webEdition/we/include/we_language/', $svnpath);
					$entry['frompath'] = str_replace('/' . $GLOBALS['externalsDir'] . '/language/', $GLOBALS['source'] . 'webEdition/we/include/we_language/', $svnpath);
				}
				$entry['isLang'] = true;
				$entry['isQuery'] = 0;
				$entry['$isBetalang'] = $isBetalang;
			} else {

				// process externals (other than sql dumps and language files)
				//foreach($externals as $extkey => $extval){
				//if(strpos($svnpath, $extval['needle']) !== false && strpos($svnpath, $extval['needle']) === 0){
				//$entry['targetpath'] = str_replace($extval['needle'], $GLOBALS['destination'].'version'.$GLOBALS['targetWEVersion'].'/files/none/'.$extval['replace'].'/',$svnpath);
				//$entry['frompath'] = str_replace($extval['needle'], $GLOBALS['source'].$extval['replace'].'/',$svnpath);
				//$entry['wepath'] = str_replace($extval['needle'], '/'.$extval['replace'].'/',$svnpath);
				//$entry['isLang'] = 0;
				//$entry['isQuery'] = 0;
				//return $entry;
				//}
				//}


				$entry['targetpath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/', $GLOBALS['destination'] . 'version' . $GLOBALS['targetWEVersion'] . '/files/none/webEdition/', $svnpath);
				$entry['frompath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/', $GLOBALS['source'] . 'webEdition/', $svnpath);
				$entry['wepath'] = str_replace('/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/', '/webEdition/', $svnpath);
				$entry['isLang'] = 0;
				$entry['isQuery'] = 0;
			}

			return $entry;
		}

		function correctExternalPaths($svnpaths){
			$tmpFiles = array();
			foreach($svnpaths as $afilepath){
				$tmpFiles[] = correctExternalPath($afilepath); // IMPORTANT: language files and sql dumps are not detected by this
			}
			return array_unique($tmpFiles);
		}

		function correctExternalPath($svnpath){
			$externals = array(
				'zend' => array(
					'needle' => '/externals/Zend/Zend-FW1',
					'replace' => '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/lib/Zend/'
				),
				'jupload' => array(
					'needle' => '/externals/java/jupload',
					'replace' => '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/jupload/'
				),
				'libraries' => array(
					'needle' => '/externals/libraries',
					'replace' => '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/lib/additional'
				),
			);


			foreach($externals as $extval){
				if(strpos($svnpath, $extval['needle']) !== false && strpos($svnpath, $extval['needle']) === 0){
					$c = str_replace($extval['needle'], $extval['replace'], $svnpath);
					echo ' changed: ' . $c . '<br>';
					return $c;
				}
			}

			echo ' orig: ' . $svnpath . '<br>';
			return $svnpath;
		}

		function getsouredir($path){
			$files = scandir($path);
			$addfiles = array();
			foreach($files as $key => &$entry){
				if($entry == '.' || $entry == '..' || $entry == '.svn'){
					unset($files[$key]);
				} else {
					$entry = $path . '/' . $entry;
					if(is_dir($entry)){
						unset($files[$key]);
						$addfiles[] = getsouredir($entry);
					}
				}
			}
			foreach($addfiles as $filearray){
				$files = array_merge($files, $filearray);
			}
			return $files;
		}

		function writeWeVersion(){

		}

		function delCommitsFromSvnArray(&$svnarr, $ids = array()){
			$delList = array(8444, 8447);
			$ids = !empty($ids) ? $ids : $delList;

			for($i = 0; $i < count($svnarr); $i++){
				if(in_array($svnarr[$i]['version'], $ids)){
					$svnarr[$i] = array(
						'version' => $svnarr[$i]['version'],
						'comment' => $svnarr[$i]['comment'],
						'author' => $svnarr[$i]['author'],
						'date' => $svnarr[$i]['date'],
						'files' => array()
					);

					for($j = 0; $j < count($ids); $j++){
						if($ids[$j] == $svnarr[$i]['version']){
							array_splice($ids, $j, 1);
						}
					}
					echo "<br><br>eliminiert: " . $svnarr[$i]['version'] . "<br>ids: " . implode(",", $ids) . "<br>";
					if(count($ids) == 0){
						echo "stopp patch<br><br>";
						break;
					}
				}
			}
		}

		//Creating new phpSVNClient Object.

		require($_SERVER['DOCUMENT_ROOT'] . "/lib/phpsvnclient.php");
		$svn = new phpsvnclient("http://svn.code.sf.net/p/webedition/code/");
		$latestVersion = $svn->getVersion();
		echo "Latest SVN-Revision: " . $latestVersion . '<br/>';

		$GLOBALS['source'] = '/kunden/343047_10825/build/svn/' . $GLOBALS['targetWEbranchDir'] . '/';
		echo "<pre>" . print_r($GLOBALS['source'], true) . "</pre>";

//get version from build/svn/trunk/.svn or from branch
		if(file_exists($GLOBALS['source'] . '.svn/all-wcprops')){
			//echo "<pre>".print_r($GLOBALS['source'].'.svn/all-wcprops',true)."</pre>";
			$vcontent = file($GLOBALS['source'] . '.svn/all-wcprops');
			//echo "<pre>".print_r($vcontent,true)."</pre>";
		}
//$vcontent = file_exists($GLOBALS['source'].'.svn/all-wcprops') ? file( $GLOBALS['source'].'.svn/all-wcprops') : array();
//echo "<pre>".print_r($vcontent,true)."</pre>";
//$targetversion = trim(str_replace('/p/webedition/code/!svn/ver/','',str_replace('/'.$GLOBALS['targetWEbranchDir'],'',$vcontent[3])));
//$targetversion = trim(str_replace('/p/webedition/code/!svn/ver/','',str_replace('/'.$GLOBALS['targetWEbranchDir'],'','/p/webedition/code/!svn/ver/8803/trunk')));
		$targetversion = isset($vcontent[3]) && $vcontent[3] ? trim(str_replace('/p/webedition/code/!svn/ver/', '', str_replace('/' . $GLOBALS['targetWEbranchDir'], '', $vcontent[3]))) : $latestVersion;
		$targetversion = $latestVersion;


		echo "Branch: " . $GLOBALS['targetWEbranch'] . "<br/>";
		echo "Releasetyp (basic): " . $GLOBALS['basicReleaseType'] . "<br/>";
		echo "Releasetyp: " . $GLOBALS['targetWEtype'] . "<br/>";
		echo "Vergleichs SVN-Revision: " . $GLOBALS['compareVersion'] . "<br/>";
//echo "Ziel SVN-Revision, im build/svn enthalten, (latest): ".$targetversion." (".$latestVersion.")<br/>" ;
		echo "Ziel WE-Version-Revision: " . $GLOBALS['targetWEVersion'] . "<br/>";
		echo "Snapshot: " . ($GLOBALS['takeSnapshot'] ? 'ja' : 'nein') . "<br/>";

		if($GLOBALS['takeSnapshot']){
			$directory = $GLOBALS['source'];
			if(is_dir($directory)){
				$DirFileObjectsArray = array();
				$DirFileObjects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
				if(defined('debug')){
					echo "<pre>" . print_r($DirFileObjects, true) . "</pre>";
				}
				foreach($DirFileObjects as $name => $object){
					if(substr($name, -2) != '/.' && substr($name, -3) != '/..' && strpos($name, '/.svn/') === false){
						$DirFileObjectsArray[] = str_replace($GLOBALS['comparesource'], '', $name);
					}
				}
				sort($DirFileObjectsArray);
			};
			if(defined('debug')){
				echo "<pre>Scanned dir: " . print_r($DirFileObjectsArray, true) . "</pre>";
			}
			$modifiedfiles = $DirFileObjectsArray;
		} else { //normale Verarbeitung
			if($GLOBALS['targetWEbranch'] === 'tag'){
				//build list of externals as in default case
				$versionLogsExternals = $svn->getRepositoryLogs($GLOBALS['externalsDir'], $GLOBALS['compareVersion'], $targetversion);
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
				$DirFileObjects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($GLOBALS['source'])); // for this we must checkout tag manually to build/svn/branches/tag...
				foreach($DirFileObjects as $name => $object){
					if(substr($name, -2) != '/.' && substr($name, -3) != '/..' && strpos($name, '/.svn/') === false){
						$versionLogs[] = array('version' => $targetversion, 'files' => array(str_replace($GLOBALS['comparesource'], '', $name)), 'mod_files' => array(str_replace($GLOBALS['comparesource'], '', $name)));
					}
				}
				delCommitsFromSvnArray($versionLogs);

				if(is_array($versionLogsExternals)){
					$versionLogs = array_merge($versionLogs, $versionLogsExternals);
				}
			} else { //release, nightly and hotfix on all branches, NOT snapshot
				//$starttime = time();
				$versionLogs1 = $svn->getRepositoryLogs($GLOBALS['targetWEbranchDir'], $GLOBALS['compareVersion'], $GLOBALS['step1Version']);
				$versionLogs2 = $svn->getRepositoryLogs($GLOBALS['targetWEbranchDir'], $GLOBALS['step1Version'], $targetversion);
				$versionLogsExt1 = $svn->getRepositoryLogs($GLOBALS['targetWeExternalsDir'], $GLOBALS['compareVersion'], $GLOBALS['step1Version']);
				$versionLogsExt2 = $svn->getRepositoryLogs($GLOBALS['targetWeExternalsDir'], $GLOBALS['step1Version'], $targetversion);
				//echo '<br>TIME OLD: ' . (time() - $starttime) . '<br>';

				if(defined('debug')){
					echo '<hr>';
					print_r($versionLogs2);
					echo '<hr>';
				}

				$versionLogs1 = is_array($versionLogs1) ? $versionLogs1 : array();
				$versionLogs2 = is_array($versionLogs2) ? $versionLogs2 : array();
				$versionLogsExt1 = is_array($versionLogsExt1) ? $versionLogsExt1 : array();
				$versionLogsExt2 = is_array($versionLogsExt2) ? $versionLogsExt2 : array();

				delCommitsFromSvnArray($versionLogs1);
				delCommitsFromSvnArray($versionLogs2);
				delCommitsFromSvnArray($versionLogsExt1);
				delCommitsFromSvnArray($versionLogsExt2);

				$versionLogsComplete = array_merge($versionLogs1, $versionLogs2, $versionLogsExt1, $versionLogsExt2);
				$checkins = array();
				$highestSvn = 0;
				foreach($versionLogsComplete as $checkin){
					echo $checkin['version'] . '<br>';
					$checkins['c_' . $checkin['version']] = $checkin;
					$highestSvn = $checkin['version'] > $highestSvn ? $checkin['version'] : $highestSvn; //2014-12-23: dirty fix for targetversion bug
				}

				ksort($checkins);
				$versionLogsComplete = $checkins;
				$targetversion = $highestSvn;

				//echo "<hr>COMBINED<hr>1 Ext: <br/>";
				//echo "<pre>".print_r($versionLogsComplete,true)."</pre>";

				$versionLogs = array();
				foreach($versionLogsComplete as $k => $v){// do we need numeric indices?
					$versionLogs[] = $v;
				}
			}

			$addfiles = $delfiles = array();
			//always have we_version.php in array $modfiles
			$modfiles = array('/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/we/include/we_version.php');

			//maybe build files array deleting and (re-) adding paths chronologically
			foreach($versionLogs as $logentry){
				if(isset($logentry['mod_files'])){
					foreach($logentry['mod_files'] as $filepath){
						//imi
						//$modfiles[] = $filepath;
						if(strpos($filepath, "externals/additional") === false){ // we do not want /additional in build!
							$modfiles[] = $filepath;
						}
						//imi
					}
				}

				//replace_files can be treated the same as mod_files
				if(isset($logentry['replace_files'])){
					foreach($logentry['replace_files'] as $filepath){
						//imi
						//$modfiles[] = $filepath;
						if(strpos($filepath, "externals/additional") === false){
							$modfiles[] = $filepath;
						}
						//imi
					}
				}
				if(isset($logentry['add_files'])){
					foreach($logentry['add_files'] as $filepath){
						//imi
						//$addfiles[] = $filepath;
						if(strpos($filepath, "externals/additional") === false){
							$addfiles[] = $filepath;
						}
						//imi
					}
				}
				if(isset($logentry['del_files'])){
					foreach($logentry['del_files'] as $filepath){
						//imi
						//$delfiles[] = $filepath;
						if(strpos($filepath, "externals/additional") === false){
							$delfiles[] = $filepath;
						}
						//imi
					}
				}
			}

			$modfiles = correctExternalPaths(array_unique($modfiles), 'mod');
			$delfiles = correctExternalPaths(array_unique($delfiles), 'del');
			$addfiles = correctExternalPaths(array_unique($addfiles), 'add');

			if(defined('debug')){
				echo "<hr>ADDFILESD<hr>1 Ext: <br/>";
				echo "<pre>" . print_r($addfiles, true) . "</pre><hr>";
			}

			$additionalfiles = array();
			foreach($addfiles as $key => $filepath){
				//echo $GLOBALS['comparesource'].$filepath;
				if(is_dir($GLOBALS['comparesource'] . $filepath)){
					//$additionalfiles = array_merge($additionalfiles, getsouredir($GLOBALS['comparesource'] . $filepath));
				}
			}
			foreach($additionalfiles as &$file){
				$file = str_replace($GLOBALS['comparesource'], '', $file);
			}
			$addfiles = array_unique(array_merge($addfiles, $additionalfiles));

			//only files not (re-)added can remain in delfiles
			$remainDel = array_diff($delfiles, $addfiles);


			$modifiedfiles = array_diff(array_unique(array_merge($modfiles, $addfiles)), $remainDel);
		}
		sort($modifiedfiles);


### find and process special files: externals, language files and sql dumps; throw out unused files of /additional ###
// find sql dumps
		$sqlfiles = array();
		foreach($modifiedfiles as $key => $filepath){
			if((strpos($filepath, '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/liveUpdate/sqldumps/tbl') !== false && strpos($filepath, '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/liveUpdate/sqldumps/tbl') == 0)){
				//versions >= 6.3.5.1
				$sqlfiles[] = $filepath;
			} else if((strpos($filepath, '/' . $GLOBALS['targetWEbranchDir'] . '/additional/sqldumps/tbl') !== false && strpos($filepath, '/' . $GLOBALS['targetWEbranchDir'] . '/additional/sqldumps/tbl') == 0)){
				$sqlfiles[] = $filepath;
				unset($modifiedfiles[$key]);
			}
		}

// find language files
		if($GLOBALS['targetWEVersion'] < $language_limit){
			$languages = array('Deutsch' => '', 'Deutsch_UTF-8' => '', 'Dutch' => '', 'Dutch_UTF-8' => '', 'English' => '', 'English_UTF-8' => '', 'Finnish' => '', 'Finnish_UTF-8' => '', 'French_UTF-8' => '', 'Polish_UTF-8' => '', 'Russian_UTF-8' => '', 'Spanish_UTF-8' => '');
		} else {
			$languages = array('Deutsch' => '', 'Dutch' => '', 'English' => '', 'Finnish' => '', 'French' => '', 'Polish' => '', 'Russian' => '', 'Spanish' => '');
		}

		$alllang = $languages;
		$languagesExt = array();
		foreach($languages as $lkey => $lang){
			if($GLOBALS['targetWEVersion'] < $language_limit){
				if(strpos($lkey, 'UTF-8') === false){//iso
					$pathstr = '/' . $GLOBALS['targetWEbranchDir'] . '/additional/lang_iso/' . $lkey . '/';
				} else {
					$pathstr = '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/we/include/we_language/' . $lkey . '/';
				}
			} else {
				$pathstr = '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/we/include/we_language/' . $lkey . '/';
				$extPathStr = '/' . $GLOBALS['externalsDir'] . '/language/' . $lkey . '/';
			}
			foreach($modifiedfiles as $key => $filepath){
				if((strpos($filepath, $pathstr) !== false && strpos($filepath, $pathstr) === 0)){
					$languages[$lkey][] = $filepath;
					unset($modifiedfiles[$key]);
				}
				if((strpos($filepath, $extPathStr) !== false && strpos($filepath, $extPathStr) === 0)){
					$languagesExt[$lkey][] = $filepath;
					unset($modifiedfiles[$key]);
				}
			}
		}

		$areLangsExternal = false;
		if(count($languagesExt) > 0){
			$languages = $languagesExt;
			$areLangsExternal = true;
		}

// eliminate /additional
		foreach($modifiedfiles as $key => &$filepath){
			if(strpos($filepath, '/' . $GLOBALS['targetWEbranchDir'] . '/additional/') !== false && strpos($filepath, '/' . $GLOBALS['targetWEbranchDir'] . '/additional/') === 0){
				unset($modifiedfiles[$key]);
			}
			if(strpos($filepath, '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/we/include/conf/we_conf') !== false &&
				strpos($filepath, '/' . $GLOBALS['targetWEbranchDir'] . '/webEdition/we/include/conf/we_conf') === 0 &&
				strpos($filepath, '.php.default') === false){
				unset($modifiedfiles[$key]);
			}
		}

// process sql dumps
		$allsql = array();
		foreach($sqlfiles as $sql){
			$allsql[] = getfiledescription($sql, true, false);
		}

// process language files
		foreach($languages as $lang => $langfilepath){
			if($GLOBALS['targetWEVersion'] < $language_limit){
				if($lang == 'French_UTF-8' || $lang == 'Polish_UTF-8' || $lang == 'Russian_UTF-8' || $lang == 'Spanish_UTF-8'){
					$betaLang = 1;
				} else {
					$betaLang = 0;
				}
			} else {
				switch($lang){
					case 'Polish':
					case 'Russian':
					case 'Spanish':
						$betaLang = 1;
						break;
					default:
						$betaLang = 0;
				}
			}
			if(is_array($langfilepath)){
				foreach($langfilepath as $lfilepath){
					$alllang[$lang][] = getfiledescription($lfilepath, false, true, false, $betaLang, $areLangsExternal);
				}
			}
		}

// process all other files
		$modifiedfiles = array_merge($modifiedfiles); // ? what do we re-index it for?
		$allfiles = array();
		foreach($modifiedfiles as $afilepath){
			$allfiles[] = getfiledescription($afilepath, false, false);
		}

		if(defined('debug')){
//echo "<pre>".print_r($allsql,true)."</pre>";
			echo "<hr>Allfiles<pre>" . print_r($allfiles, true) . "</pre>";
		}

### BUILD VERSION ###

		$changessql = [];
		foreach($allsql as $data){
			if(file_exists($data['frompath'])){
				if(filesize($data['frompath'])){
					$path_parts = pathinfo($data['targetpath']);
					$dirname = $path_parts['dirname'];
					if(!file_exists($dirname)){
						mkdir($dirname, 0777, true);
					}
					if(copy($data['frompath'], $data['targetpath'])){
						$changessql[] = '/' . $path_parts['basename'];
					}
				}
			} else {
				//echo "<pre> nicht gefunden sql: ".print_r($data['frompath'],true)."</pre>";
			}
		}
//echo "<pre>".print_r($changessql,true)."</pre>";
//echo "<pre>".print_r($qsql,true)."</pre>";

		$changesfiles = [];
//
		foreach($allfiles as $data){
			if(!file_exists($data['frompath'])){
				continue;
			}

			if(filesize($data['frompath'])){
				$path_parts = pathinfo($data['targetpath']);
				$dirname = $path_parts['dirname'];
				if(!file_exists($dirname)){
					mkdir($dirname, 0777, true);
				}

				$pinfo = pathinfo($data['frompath']);
				switch(isset($pinfo['extension']) ? $pinfo['extension'] : ''){
					case 'scss':
						$scss = new we_helpers_scss();
						we_helpers_scss::$includedFiles = array(); //due to rebuild!
						$scss->setImportPaths(array_unique(array('', $GLOBALS['source'], $pinfo['dirname'])));
						try{
							$doc = $scss->compile(file_get_contents($data['frompath']));
							$data['frompath'] = $pinfo['dirname'] . '/' . $pinfo['filename'] . '.css';
							file_put_contents($data['frompath'], $doc);
							$data['wepath'] = str_replace('.scss', '.css', $data['wepath']);
						} catch (exception $e){
							echo 'ERROR with scss file ' . $data['frompath'];
							p_r(str_replace(array('\n', "\n"), ' ', $e->getMessage()));
							return false;
						}
						break;
				}

				if(copy($data['frompath'], $data['targetpath'])){
					$changesfiles[] = $data['wepath'];
				}
				//TODO: check up to what version the installer-function replacecode() was active and then throw the following code out!
				if($path_parts['basename'] == 'we_version.php' && intval($GLOBALS['targetWEVersion']) < 6381){
					$versionsfile = intval($GLOBALS['targetWEVersion']) > 6380 || file($data['targetpath']) === false ? array("<?php") : file($data['targetpath']);

					$versionsfile[1] = 'define("WE_VERSION","' . trim($GLOBALS['targetWEVersionString']) . '");';
					$versionsfile[2] = 'define("WE_VERSION_SUPP","' . trim($GLOBALS['targetWEtype']) . '");';
					$versionsfile[3] = intval($GLOBALS['targetWEVersion']) > 6380 ? 'define("WE_ZFVERSION","' . trim($GLOBALS['targetWEZFVersion']) . '"); // test. recommended version of the Zend Framework (bundled with webEdition)' :
						(file($data['targetpath']) !== false ? $versionsfile[3] : 'define("WE_VERSION_NAME","");');
					$versionsfile[4] = 'define("WE_SVNREV","' . trim($targetversion) . '");';
					$versionsfile[5] = 'define("WE_VERSION_SUPP_VERSION","' . trim($GLOBALS['targetWEtypeversion']) . '");';
					$versionsfile[6] = 'define("WE_VERSION_BRANCH","' . trim($GLOBALS['targetWEbranch']) . '");';
					$versionsfile[7] = 'define("WE_VERSION_NAME","' . trim($GLOBALS['targetWEVersionName']) . '");';
					//echo "<br>we_version.php<br>";
					//echo "<pre>".print_r($versionsfile,true)."</pre>";
					$versionsfiletext = implode("\n", $versionsfile);
					file_put_contents($data['targetpath'], $versionsfiletext);
				}
			}
		}

//if WE_VERSION > 6380 or if file we_version.php does not exist: write it!
//new code 2014-03-13
		$weVersionFile = $GLOBALS['destination'] . 'version' . $GLOBALS['targetWEVersion'] . '/files/none/webEdition/we/include/we_version.php';
		if(intval($GLOBALS['targetWEVersion']) > 6380 || !file_exists($weVersionFile)){
			$versionsfile = array("<?php",
				'define("WE_VERSION","' . trim($GLOBALS['targetWEVersionString']) . '");',
				'define("WE_VERSION_SUPP","' . trim($GLOBALS['targetWEtype']) . '");',
				'define("WE_ZFVERSION","' . trim($GLOBALS['targetWEZFVersion']) . '"); // recommended version of the Zend Framework (bundled with webEdition)',
				'define("WE_SVNREV","' . trim($targetversion) . '");',
				'define("WE_VERSION_SUPP_VERSION","' . trim($GLOBALS['targetWEtypeversion']) . '");',
				'define("WE_VERSION_BRANCH","' . trim($GLOBALS['targetWEbranch']) . '");',
				isset($GLOBALS['targetWEVersionName']) ? 'define("WE_VERSION_NAME","' . trim($GLOBALS['targetWEVersionName']) . '");' . "\n" : 'define("WE_VERSION_NAME","");'
			);
			p_r($versionsfile);
			$versionsfiletext = implode("\n", $versionsfile);
			file_put_contents($weVersionFile, $versionsfiletext);
		}



//echo "<pre>".print_r($changesfiles,true)."</pre>";
		$isSnapshot = ($GLOBALS['takeSnapshot'] ? 1 : 0);
		if($changesfiles){
			$changesfiles = array_unique($changesfiles);
			if(true || defined('debug')){
				echo "<pre>ChangeFiles: " . print_r($changesfiles, true) . "</pre>";
			} else {
				echo 'ChangeFiles: ' . count($changesfiles);
			}
			$changesfiles = implode(",\n", $changesfiles);
			
if($GLOBALS['targetWEbranch'] === 'main-develop'){
		file_put_contents ('changes_old.txt', $changesfiles);
	exit();
}
//echo $qfiles;
			if(!f("SELECT 1 FROM `v6_changes` WHERE version= " . $GLOBALS['targetWEVersion'] . " AND detail = 'files' LIMIT 1")){
				if(!$DB_WE->query("INSERT INTO `v6_changes` (version,detail) VALUES ('" . $GLOBALS['targetWEVersion'] . "','files')")){
					exit();
				}
			}
			//echo $qfiles;
			if(!$DB_WE->query("UPDATE `v6_changes` SET changes = '" . $changesfiles . "', isSnapshot='" . $isSnapshot . "' WHERE version= " . $GLOBALS['targetWEVersion'] . " AND detail = 'files'")){
				exit();
			}
			//echo "<pre>".print_r($qfiles,true)."</pre>";

			if($changessql){
				if(defined('debug')){
					echo "<pre>Änderungen SQL: " . print_r($changessql, true) . "</pre>";
				} else {
					echo "Änderungen SQL: " . count($changessql);
				}
				$changessql = implode(",\n", $changessql);
				if(!f("SELECT 1 FROM `v6_changes` WHERE version= " . $GLOBALS['targetWEVersion'] . " AND detail = 'queries' LIMIT 1")){
					if(!$DB_WE->query("INSERT INTO `v6_changes` (version,detail) VALUES ('" . $GLOBALS['targetWEVersion'] . "','queries')")){
						exit();
					}
				}
				if(!$DB_WE->query("UPDATE `v6_changes` SET changes = '" . $changessql . "', isSnapshot='" . $isSnapshot . "' WHERE version= " . $GLOBALS['targetWEVersion'] . " AND detail = 'queries'")){
					exit();
				}
			}


			foreach($alllang as $langkey => $langdata){
				$changeslang = [];
				if(is_array($langdata)){
					foreach($langdata as $data){
						if(file_exists($data['frompath'])){
							if(filesize($data['frompath'])){
								$path_parts = pathinfo($data['targetpath']);
								$dirname = $path_parts['dirname'];
								if(!file_exists($dirname)){
									mkdir($dirname, 0777, true);
								}
								if(copy($data['frompath'], $data['targetpath'])){
									$changeslang[] = $data['wepath'];
								}
							}
						} else {
							echo 'no such file';
							p_r($data['frompath']);
						}
					}
				}

				if($changeslang){
					$changeslang = implode(",\n", $changeslang);
					if(!f("SELECT 1 FROM `v6_changes_language` WHERE version= " . $GLOBALS['targetWEVersion'] . " AND detail = 'files' AND language = '" . $langkey . "' LIMIT 1")){
						if(!$DB_WE->query("INSERT INTO `v6_changes_language` (version,detail,language) VALUES ('" . $GLOBALS['targetWEVersion'] . "','files','" . $langkey . "')")){
							exit();
						}
					}
					if(!$DB_WE->query("UPDATE `v6_changes_language` SET changes = '" . $changeslang . "', isSnapshot='" . $isSnapshot . "' WHERE version= " . $GLOBALS['targetWEVersion'] . " AND detail='files' AND language = '" . $langkey . "'")){
						exit();
					}
				}

				//	echo "<pre>".print_r($lsql,true)."</pre>";
				//	echo "<pre>".print_r($vsql,true)."</pre>";
			}

			$isSnapshot = ($GLOBALS['takeSnapshot'] ? 1 : 0);
			if(!$DB_WE->query("REPLACE INTO `v6_versions` SET version= " . $GLOBALS['targetWEVersion'] . ",versname='" . $GLOBALS['targetWEVersionName'] . "', svnrevision = '" . $targetversion . "', revisionFrom = '" . ($isSnapshot ? 0 : $GLOBALS['compareVersion']) . "', revisionTo = '" . $targetversion . "', type='" . ($GLOBALS['targetWEtype'] === 'nightly-build' ? 'nightly' : $GLOBALS['targetWEtype']) . "', typeversion='" . $GLOBALS['targetWEtypeversion'] . "', branch='" . $GLOBALS['targetWEbranch'] . "', isSnapshot='" . $isSnapshot . "', date='" . date('Y-m-d H:i:s') . "'")){
				exit();
			}

			// fuer Versionen >=6266
			/*if($GLOBALS['targetWEVersion'] >= $language_limit){
				$utflang = array('Deutsch_UTF-8', 'English_UTF-8', 'Dutch_UTF-8', 'Finnish_UTF-8', 'French_UTF-8', 'Polish_UTF-8', 'Russian_UTF-8', 'Spanish_UTF-8');
				foreach($utflang as $langkkey => $langkey){
					if(!f("SELECT 1 FROM `v6_versions` WHERE version=" . $GLOBALS['targetWEVersion'] . " AND language = '" . $langkey . "' LIMIT 1")){
						if(!$DB_WE->query("INSERT INTO `v6_versions` (version,language) VALUES ('" . $GLOBALS['targetWEVersion'] . "','" . $langkey . "')")){
							exit();
						}
					}
					$isSnapshot = ($GLOBALS['takeSnapshot'] ? 1 : 0);
					if(!$DB_WE->query("UPDATE `v6_versions` SET versname = '" . $GLOBALS['targetWEVersionName'] . "', svnrevision = '" . $targetversion . "', revisionFrom = '" . ($isSnapshot ? 0 : $GLOBALS['compareVersion']) . "', revisionTo = '" . $targetversion . "', type='" . $GLOBALS['targetWEtype'] . "', typeversion='" . $GLOBALS['targetWEtypeversion'] . "', branch='" . $GLOBALS['targetWEbranch'] . "', isSnapshot='" . $isSnapshot . "', date='" . date('Y-m-d H:i:s') . "' WHERE version= " . $GLOBALS['targetWEVersion'] . " AND language = '" . $langkey . "'")){
						exit();
					}
				}
			}*/
		}

		p_r("FERTIG");
		?>
	</body>
</html>
