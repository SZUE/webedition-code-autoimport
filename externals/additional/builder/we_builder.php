#!/usr/local/bin/php5-56LATEST-CLI
<?php
/*
 * later: pack this script to class we_builder_builder and reduce index.php to...:
 *		$configuration = new we_builder_configuration;
 *		$builder new we_builder_builder($configuration);
 *		echo $builder->execute();
 *
 *
 */


//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$_SERVER['DOCUMENT_ROOT'] = '/kunden/343047_10825/sites/webedition.org/nightlybuilder';
define('NO_SESS', 1);
require($_SERVER['DOCUMENT_ROOT'] . '/conf/we_conf.inc.php');
require($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
require($_SERVER['DOCUMENT_ROOT'] . '/we_builder_configurations.class.php');

$GLOBALS['DB_WE']->query('DELETE FROM ' . ERROR_LOG_TABLE . ' WHERE `Date` < DATE_SUB(NOW(), INTERVAL ' . we_base_constants::ERROR_LOG_HOLDTIME . ' DAY)');

$cliArguments = getopt("b:t:v:d:");
if(empty($cliArguments)){
	echo "Builder won't run when called by http!";
	exit();
}

$branch = empty($cliArguments['b']) ? '' : $cliArguments['b'];
$type = empty($cliArguments['t']) ? '' : $cliArguments['t'];
$version = empty($cliArguments['v']) ? 0 : $cliArguments['v'];
$debug = empty($cliArguments['d']) ? 0 : $cliArguments['d'];

echo $debug ? "debug mode: on\n" : 1;

$configurations = new we_builder_configurations($GLOBALS['DB_WE'], $branch, $type, $version);
if(!$configurations->getActiveConfiguration()){
	echo "\nThere is no configuration for the parameters given!\n";
	exit();
}

// array $builder contains props of a future class we_builder_class
$builder = array(
	'debug' => $debug,
	'DB_WE' => $GLOBALS['DB_WE'],
	'externalsDir' => 'externals',
	'source' => '/kunden/343047_10825/build/svn/' . $configurations->get('targetBranchDir') . '/',
	'destination' => '/kunden/343047_10825/sites/webedition.org/update/htdocs/files/we/',
	'comparesource' => '/kunden/343047_10825/build/svn',
);

// move this functions ot some new builder class
function getSvnLatestRevision($branch = 'trunk'){
	$xml_branch = simplexml_load_string(shell_exec('svn info --xml http://svn.code.sf.net/p/webedition/code/' . $branch));
	$xml_externals = simplexml_load_string(shell_exec('svn info --xml http://svn.code.sf.net/p/webedition/code/externals'));

	return max((int) $xml_branch->entry['revision'], (int) $xml_externals->entry['revision']);
}

function getFilelistFromSvn($branch = 'trunk', $startRev = 9000, $endRev = 'HEAD', $origBranch = ''){
	$branchDirs = array('trunk', 'branches/mgallery', 'branches/main-develop'); // FIXME: when making we_builder_builder this will be property
	$xml = simplexml_load_string(shell_exec('svn log --xml -v -r ' . $startRev . ':' . $endRev . ' http://svn.code.sf.net/p/webedition/code/' . $branch));
	$latest = 0;
	$M = array();
	$delList = array(8444, 8447);

	foreach($xml->logentry as $logentry){
		$rev = intval($logentry['revision']);
		if(in_array($rev, $delList)){
			continue;
		}
		$latest = max($rev, $latest);
		$revAR = array();
		$revD = array();
		foreach($logentry->paths[0] as $path){
			$p = (string) $path;

			//Fix some wrong paths (resulting from using log instead of diff):Throw out externals from
			//=> delete externals from branch log and vice versa
			switch($branch){
				case 'externals':
					//=> delete branch paths from externals log
					if(strpos($p, '/externals/') !== 0){
						continue 2;
					}
					break;
				default:
					//=> delete external paths from branch log
					if(strpos($p, '/externals/') === 0){
						continue 2;
					}
					//=> change branch dir of files commited to other branch (before the tag or branch in question was created)
					if(strpos($p, '/' . $branch . '/') !== 0){
						$p = str_replace($branchDirs, $branch, $p);
					}
			}

			switch($path['action']){
				case 'A':
				case 'R':
					$revAR[$p] = true;
					break;
				case 'M':
					$M[$p] = true;
					break;
				case 'D':
					$revD[$p] = true;
					break;
			}
		}

		$M = array_merge(array_diff_key($M, $revD), $revAR);
	}
	$M = array_keys($M);
	sort($M);

	return array('latest' => $latest, 'files' => $M);
}

function getFilelistWithExternals($branch = 'trunk', $startRev = 9000, $endRevBranch = 'HEAD', $endRevExternals = 'HEAD'){
	$branchfiles = getFilelistFromSvn($branch, $startRev, $endRevBranch);
	$externals = getFilelistFromSvn('externals', $startRev, $endRevExternals);

	// Filter some external paths we do not need to have in the filelist
	// TODO: use shell_exec('chdir /kunden/343047_10825/build/svn/trunk; svn propget svn:externals --xml --depth infinity > ../ausgabe.txt'); to find targetdirs of externals
	$externals['files'] = array_filter($externals['files'], function($f){return strpos($f, "/externals/additional/") === false;});

	return array('latest' => (max($branchfiles['latest'], $externals['latest'])) , 'files' => array_merge($branchfiles['files'], $externals['files']));
}

/*
function getFilelistFromSvn_plusD($branch = 'trunk', $startRev = 9000, $endRev = 'HEAD'){
	$xml = simplexml_load_string(shell_exec('svn log --xml -v -r ' . $startRev . ':' . $endRev . ' http://svn.code.sf.net/p/webedition/code/' . $branch));

	$latest = 0;
	$M = array();
	$D = array();

	foreach($xml->logentry as $logentry){
		$latest = max(intval($logentry['revision']), $latest);
		$revAR = array();
		$revD = array();

		foreach($logentry->paths[0] as $path){
			$p = (string) $path;
			switch($path['action']){
				case 'A':
				case 'R':
					$revAR[$p] = true;
					break;
				case 'M':
					$M[$p] = true;
					break;
				case 'D':
					$revD[$p] = true;
					break;
			}
		}
		$M = array_merge(array_diff_key($M, $revD), $revAR);
		$D = array_merge($D, $revD);
	}

	$M = array_keys($M);
	sort($M);
	$D = array_keys(array_diff_key($D, $M));
	sort($D);

	return array('latest' => $latest, 'M' => $M, 'D' => $D);
}
*/

function getfiledescription($configurations, $builder, $svnpath, $isQuery, $isLang, $areLangsExternal = false){
	$entry = array(
		'svnpath' => $svnpath
	);
	if($isQuery){
		$entry['targetpath'] = str_replace('/' . $configurations->get('targetBranchDir') . '/webEdition/liveUpdate/sqldumps/', $builder['destination'] . 'version' . $configurations->get('targetVersion') . '/queries/', $svnpath);
		$entry['frompath'] = str_replace('/' . $configurations->get('targetBranchDir') . '/webEdition/liveUpdate/sqldumps/', $builder['source'] . 'webEdition/liveUpdate/sqldumps/', $svnpath);
		$entry['isQuery'] = true;
		$entry['isLang'] = 0;
	} elseif($isLang){
		if(!$areLangsExternal){
			$entry['targetpath'] = str_replace('/' . $configurations->get('targetBranchDir') . '/webEdition/we/include/we_language/', $builder['destination'] . 'version' . $configurations->get('targetVersion') . '/files/none/webEdition/we/include/we_language/', $svnpath);
			$entry['wepath'] = str_replace('/' . $configurations->get('targetBranchDir') . '/webEdition/we/include/we_language/', '/webEdition/we/include/we_language/', $svnpath);
			$entry['frompath'] = str_replace('/' . $configurations->get('targetBranchDir') . '/webEdition/we/include/we_language/', $builder['source'] . 'webEdition/we/include/we_language/', $svnpath);
		} else {
			$entry['targetpath'] = str_replace('/' . $builder['externalsDir'] . '/language/', $builder['destination'] . 'version' . $configurations->get('targetVersion') . '/files/none/webEdition/we/include/we_language/', $svnpath);
			$entry['wepath'] = str_replace('/' . $builder['externalsDir'] . '/language/', '/webEdition/we/include/we_language/', $svnpath);
			$entry['frompath'] = str_replace('/' . $builder['externalsDir'] . '/language/', $builder['source'] . 'webEdition/we/include/we_language/', $svnpath);
		}
		$entry['isLang'] = true;
		$entry['isQuery'] = 0;
	} else {
		$entry['targetpath'] = str_replace('/' . $configurations->get('targetBranchDir') . '/webEdition/', $builder['destination'] . 'version' . $configurations->get('targetVersion') . '/files/none/webEdition/', $svnpath);
		$entry['frompath'] = str_replace('/' . $configurations->get('targetBranchDir') . '/webEdition/', $builder['source'] . 'webEdition/', $svnpath);
		$entry['wepath'] = str_replace('/' . $configurations->get('targetBranchDir') . '/webEdition/', '/webEdition/', $svnpath);
		$entry['isLang'] = 0;
		$entry['isQuery'] = 0;
	}

	return $entry;
}

function correctExternalPaths($configurations, $svnpaths){
	$tmpFiles = array();
	foreach($svnpaths as $afilepath){
		$tmpFiles[] = correctExternalPath($configurations, $afilepath); // IMPORTANT: language files and svn.code, dumps are not detected by this
	}
	return array_unique($tmpFiles);
}

function correctExternalPath($configurations, $svnpath){
	$externals = array(
		'zend' => array(
			'needle' => '/externals/Zend/Zend-FW1',
			'replace' => '/' . $configurations->get('targetBranchDir') . '/webEdition/lib/Zend/'
		),
		'jupload' => array(
			'needle' => '/externals/java/jupload',
			'replace' => '/' . $configurations->get('targetBranchDir') . '/webEdition/jupload/'
		),
		'libraries' => array(
			'needle' => '/externals/libraries',
			'replace' => '/' . $configurations->get('targetBranchDir') . '/webEdition/lib/additional'
		),
	);

	foreach($externals as $extval){
		if(strpos($svnpath, $extval['needle']) !== false && strpos($svnpath, $extval['needle']) === 0){
			$c = str_replace($extval['needle'], $extval['replace'], $svnpath);

			return $c;
		}
	}

	return $svnpath;
}

// #### COMPUTE FILEDESCRIPTIONS ###
if($configurations->get('targetTakeSnapshot')){
	$directory = $builder['source'];
	$modifiedfiles = array();
	if(is_dir($directory)){
		$DirFileObjectsArray = array();
		$DirFileObjects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

		foreach($DirFileObjects as $name => $object){
			if(substr($name, -2) != '/.' && substr($name, -3) != '/..' && strpos($name, '/.svn/') === false &&
					strpos($name, $builder['comparesource'] . '/' . $configurations->get('targetBranchDir') . '/additional') !== 0){ //throw out /additional
				$modifiedfiles[] = str_replace($builder['comparesource'], '', $name);
			}
		}
		sort($modifiedfiles);
	}
	if(false && $builder['debug']){
		echo "Scanned dir:\n" . implode("\n", $modifiedfiles) . "\n\n";
	}
	$targetRevision = getSvnLatestRevision($configurations->get('targetBranchDir'));
} else { //normale Verarbeitung
	if($configurations->get('targetBranch') === 'tag'){
		// this was intended to make NEW versions from tag, not replacing existing ones
		// => for that purpose we could reintroduce type=tag instead of taking configuration from db
		// => prcondition would be a version number not yet existing in update db
	} else { //release, nightly and hotfix on all branches, NOT snapshot
		if($configurations->getIsHotfix()){
			$log = getFilelistWithExternals($configurations->get('targetBranchDir'), $configurations->get('targetRevisionFrom'), 'HEAD', $configurations->get('targetRevisionTo'));
		} else {
			$log = getFilelistWithExternals($configurations->get('targetBranchDir'), $configurations->get('targetRevisionFrom'));
		}
		$targetRevision = $log['latest'];
		$modifiedfiles = correctExternalPaths($configurations, $log['files']);
		if(!in_array('/' . $configurations->get('targetBranchDir') . '/webEdition/we/include/we_version.php', $modifiedfiles)){
			$modifiedfiles[] = '/' . $configurations->get('targetBranchDir') . '/webEdition/we/include/we_version.php';
		}
	}
}
sort($modifiedfiles);

if($builder['debug']){
	echo "\nConfiguration:\n" ;
	echo "targetBranch: " . $configurations->get('targetBranch') . "\n";
	echo "targetBranchDir: " . $configurations->get('targetBranchDir') . "\n";
	echo "type (basic): " . $configurations->get('targetNormalizedType') . "\n";
	echo "type: " . $configurations->get('targetType') . "\n";
	echo "targetVersion: " . $configurations->get('targetVersion') . "\n";
	echo "targetCompareVersion: " . $configurations->get('targetCompareVersion') . " " . ($configurations->get('targetTakeSnapshot') ? "[not needed when snapshot]" : "") . "\n";
	echo "targetRevisionFrom: " . $configurations->get('targetRevisionFrom') . " " . ($configurations->get('targetTakeSnapshot') ? "[not needed when snapshot]" : "") . "\n";
	echo "targetRevisionTo: " . $targetRevision . "\n";
	echo "targetTakeSnapshot: " . ($configurations->get('targetTakeSnapshot') ? 'ja' : 'nein') . "\n";
	echo "isHotfix: " . ($configurations->getIsHotfix() ? 'ja' : 'nein') . "\n\n";
}

### find and process special files: externals, language files and sql dumps; throw out unused files of /additional ###
// find sql dumps
$sqlfiles = array();
foreach($modifiedfiles as $key => $filepath){
	if((strpos($filepath, '/' . $configurations->get('targetBranchDir') . '/webEdition/liveUpdate/sqldumps/tbl') !== false && strpos($filepath, '/' . $configurations->get('targetBranchDir') . '/webEdition/liveUpdate/sqldumps/tbl') == 0)){
		//versions >= 6.3.5.1
		$sqlfiles[] = $filepath;
	}
}

// find language files
$languages = array('Deutsch' => '', 'Dutch' => '', 'English' => '', 'Finnish' => '', 'French' => '', 'Polish' => '', 'Russian' => '', 'Spanish' => '');
$alllang = $languages;
$languagesExt = array();
foreach($languages as $lkey => $lang){
	$pathstr = '/' . $configurations->get('targetBranchDir') . '/webEdition/we/include/we_language/' . $lkey . '/';
	$extPathStr = '/' . $builder['externalsDir'] . '/language/' . $lkey . '/';

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

// process sql dumps
$allsql = array();
foreach($sqlfiles as $sql){
	$allsql[] = getfiledescription($configurations, $builder, $sql, true, false);
}

// process language files
foreach($languages as $lang => $langfilepath){
	if(is_array($langfilepath)){
		foreach($langfilepath as $lfilepath){
			$alllang[$lang][] = getfiledescription($configurations, $builder, $lfilepath, false, true, $areLangsExternal);
		}
	}
}

// process all other files
$modifiedfiles = array_merge($modifiedfiles); // ? what do we re-index it for?
$allfiles = array();
foreach($modifiedfiles as $afilepath){
	$allfiles[] = getfiledescription($configurations, $builder, $afilepath, false, false);
}


// #### START BUILD HERE ###
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
	}
}

$changesfiles = [];
foreach($allfiles as $data){
	if(!file_exists($data['frompath'])){
		if($builder['debug']){
			echo 'not exists in repo: ' . $data['frompath'] . "\n";
		}
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
				$scss->setImportPaths(array_unique(array('', $builder['source'], $pinfo['dirname'])));
				try{
					$doc = $scss->compile(file_get_contents($data['frompath']));
					$data['frompath'] = $pinfo['dirname'] . '/' . $pinfo['filename'] . '.css';
					file_put_contents($data['frompath'], $doc);
					$data['wepath'] = str_replace('.scss', '.css', $data['wepath']);
				}catch(exception $e){
					if($builder['debug']){
						echo 'ERROR with scss file ' . $data['frompath'] . "\n";
						//p_r(str_replace(array('\n', "\n"), ' ', $e->getMessage()));
					}
					return false;
				}
				break;
		}
		if(!is_dir($data['frompath']) && copy($data['frompath'], $data['targetpath'])){
			$changesfiles[] = $data['wepath'];
		}
	}
}
//exit();
$weVersionFile = $builder['destination'] . 'version' . $configurations->get('targetVersion') . '/files/none/webEdition/we/include/we_version.php';
$versionsfile = array("<?php",
	'define("WE_VERSION","' . trim($configurations->get('targetVersionstring')) . '");',
	'define("WE_VERSION_SUPP","' . trim($configurations->get('targetType')) . '");',
	'define("WE_ZFVERSION","' . trim($configurations->get('targetZFVersion')) . '"); // recommended version of the Zend Framework (bundled with webEdition)',
	'define("WE_SVNREV","' . trim($targetRevision) . '");',
	'define("WE_VERSION_SUPP_VERSION","' . trim($configurations->get('targetTypeversion')) . '");',
	'define("WE_VERSION_BRANCH","' . trim($configurations->get('targetBranch')) . '");',
	$configurations->get('targetName') ? 'define("WE_VERSION_NAME","' . trim($configurations->get('targetName')) . '");' . "\n" : 'define("WE_VERSION_NAME","");'
);
$versionsfiletext = implode("\n", $versionsfile);
file_put_contents($weVersionFile, $versionsfiletext);
if($builder['debug']){
	echo "\nwe_version.php\n" . $versionsfiletext;
}

// #### WRITE VERSION TO DB ###
if($changesfiles){
	$strChangesfiles = implode(",\n", array_unique($changesfiles));
	if($builder['debug']){
		//echo $strChangesfiles;
	}

	if(!f("SELECT 1 FROM `v6_changes` WHERE version= " . $configurations->get('targetVersion') . " AND detail = 'files' LIMIT 1")){
		if(!$builder['DB_WE']->query("INSERT INTO `v6_changes` (version,detail) VALUES ('" . $configurations->get('targetVersion') . "','files')")){
			exit();
		}
	}
	if(!$builder['DB_WE']->query("UPDATE `v6_changes` SET changes = '" . $strChangesfiles . "', isSnapshot='" . intval($configurations->get('targetTakeSnapshot')) . "' WHERE version= " . $configurations->get('targetVersion') . " AND detail = 'files'")){
		exit();
	}

	if($changessql){
		$changessql = implode(",\n", $changessql);
		if($builder['debug']){
			//echo "\nChanges SQL: " . $changessql . "\n";
		}

		if(!f("SELECT 1 FROM `v6_changes` WHERE version= " . $configurations->get('targetVersion') . " AND detail = 'queries' LIMIT 1")){
			if(!$builder['DB_WE']->query("INSERT INTO `v6_changes` (version,detail) VALUES ('" . $configurations->get('targetVersion') . "','queries')")){
				exit();
			}
		}
		if(!$builder['DB_WE']->query("UPDATE `v6_changes` SET changes = '" . $changessql . "', isSnapshot='" . intval($configurations->get('targetTakeSnapshot')) . "' WHERE version= " . $configurations->get('targetVersion') . " AND detail = 'queries'")){
			exit();
		}
	}

	//file_put_contents ('changes_new.txt', $strChangesfiles . "\n\n". $changessql);

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
					if($builder['debug']){
						echo 'no such file: ' . $data['frompath'] . "\n";
					}
				}
			}
		}

		if($changeslang){
			$changeslang = implode(",\n", $changeslang);
			if(!f("SELECT 1 FROM `v6_changes_language` WHERE version= " . $configurations->get('targetVersion') . " AND detail='files' AND language='" . $langkey . "' LIMIT 1")){
				if(!$builder['DB_WE']->query("INSERT INTO `v6_changes_language` (version,detail,language) VALUES ('" . $configurations->get('targetVersion') . "','files','" . $langkey . "')")){
					exit();
				}
			}
			if(!$builder['DB_WE']->query("UPDATE `v6_changes_language` SET changes='" . $changeslang . "' WHERE version= " . $configurations->get('targetVersion') . " AND detail = 'files' AND language = '" . $langkey . "'")){
				exit();
			}
		}

	}

	// in new builds svnrevision and revisionTo are the same. when making hotfix revisionTo stays untouched whereas svnrevision is the real latest revision
	$revisionTo = ($revisionTo = f('SELECT revisionTo FROM `v6_versions` WHERE version=' . $configurations->get('targetVersion') . ' LIMIT 1')) &&
		$configurations->get('targetNormalizedType') === 'hotfix' ? $revisionTo : $targetRevision;

	if(!$builder['DB_WE']->query("REPLACE INTO `v6_versions` SET version=" . $configurations->get('targetVersion') . ",versname='" . $configurations->get('targetName') . "', svnrevision = '" . $targetRevision . "', revisionFrom = '" . ($configurations->get('targetTakeSnapshot') ? 0 : $configurations->get('targetRevisionFrom')) . "', revisionTo = '" . $revisionTo . "', type='" . $configurations->get('targetType') . "', typeversion='" . $configurations->get('targetTypeversion') . "', branch='" . $configurations->get('targetBranch') . "', isSnapshot='" . intval($configurations->get('targetTakeSnapshot')) . "', date='" . date('Y-m-d H:i:s') . "', zfversion='" . trim($configurations->get('targetZFVersion')) . "'")){
			exit();
	}

	echo $builder['debug'] ? "\nFERTIG" : 1;
}
