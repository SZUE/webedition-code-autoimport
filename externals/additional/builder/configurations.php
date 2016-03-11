<?php
$GLOBALS['targetWEbranch'] = empty($GLOBALS['targetWEbranch']) ? '' : $GLOBALS['targetWEbranch'];
$GLOBALS['basicReleaseType'] = empty($GLOBALS['basicReleaseType']) ? '' : $GLOBALS['basicReleaseType'];

$GLOBALS['isValidBranch'] = true;
$GLOBALS['isValidType'] = true;

// default values
$GLOBALS['targetWEbranchDir'] = '';
$GLOBALS['targetWEtype'] = '';
$GLOBALS['targetWEVersion'] = '';
$GLOBALS['targetWEVersionName'] = '';
$GLOBALS['targetWEVersionString'] = '';
$GLOBALS['targetWEtypeversion'] = '0';
$GLOBALS['targetWEZFVersion']= '1.12.13';
$GLOBALS['versionsToDelete'] = array();
$GLOBALS['takeSnapshot'] = false;
$GLOBALS['lockedForCronjob'] = true;
$GLOBALS['compareWeVersion'] = 6000;
$GLOBALS['compareVersion'] = 0;
$GLOBALS['step1Version'] = 0;
$GLOBALS['createTag'] = false;

// set branch/type specific values
switch($GLOBALS['targetWEbranch']) {
	case 'main-develop':
		$GLOBALS['targetWEbranchDir'] = 'branches/main-develop';
		switch($GLOBALS['basicReleaseType']){
			case 'nightly':
			case 'nightly-build':
				/*
				$GLOBALS['targetWEVersion'] = 7001;
				$GLOBALS['targetWEVersionName'] = '7.0.0.1 MAIN-DEVELOP';
				$GLOBALS['targetWEVersionString'] = '7.0.0.1';
				$GLOBALS['targetWEtype'] = 'nightly-build';
				$GLOBALS['takeSnapshot'] = true;
				$GLOBALS['compareWeVersion'] = 6490; // not necessary when taking snapshot
				$GLOBALS['versionsToDelete'] = array();
				 * 
				 */
				$GLOBALS['targetWEbranchDir'] = 'trunk';
				$GLOBALS['targetWEVersion'] = 8001;
				$GLOBALS['targetWEVersionName'] = '8.0.0.1 MAIN-DEVELOP';
				$GLOBALS['targetWEVersionString'] = '8.0.0.1';
				$GLOBALS['targetWEtype'] = 'nightly-build';
				$GLOBALS['takeSnapshot'] = true;
				$GLOBALS['compareWeVersion'] = 6430; // not necessary when taking snapshot
				$GLOBALS['versionsToDelete'] = array();
				break;
			default:
				$GLOBALS['isValidType'] = false;
		}
		break;
	case 'mgallery':
		$GLOBALS['targetWEbranchDir'] = 'branches/mgallery';
		switch($GLOBALS['basicReleaseType']) {
			case 'release':
			case 'beta':
			case 'alpha':
			case 'rc':
				$GLOBALS['targetWEtype'] = 'release';
				$GLOBALS['targetWEVersion'] = '6498';
				$GLOBALS['targetWEVersionName'] = '7.0 RC';
				$GLOBALS['targetWEVersionString'] = '6.4.9.8';
				$GLOBALS['compareWeVersion'] = 6490; // not necessary when taking snapshot
//$GLOBALS['compareVersion'] = 9053;
//$GLOBALS['step1Version'] = 9054;
				$GLOBALS['takeSnapshot'] = true;
				$GLOBALS['versionsToDelete'] = array(6498, 6499); // delete Beta and latest nightly
				break;
			case 'hotfix': // TODO: get all config from versions db to make this more generic
				//
				break;
			case 'nightly':
			case 'nightly-build':
//$GLOBALS['lockedForCronjob'] = false;
				/*
				$GLOBALS['targetWEtype'] = 'rc';
				$GLOBALS['targetWEtypeversion']= "2";
				$GLOBALS['targetWEVersion'] = 6501;
				$GLOBALS['targetWEVersionName'] = '7.0 RC2';
				$GLOBALS['targetWEVersionString'] = '6.5.0.1';
				$GLOBALS['compareWeVersion'] = 6490; // not necessary when taking snapshot
				$GLOBALS['takeSnapshot'] = true;
				 * */
//$GLOBALS['compareVersion'] = 9053;
//$GLOBALS['step1Version'] = 9054;

//$GLOBALS['lockedForCronjob'] = false;
				$GLOBALS['targetWEtype'] = 'nightly-build';
				$GLOBALS['targetWEtypeversion']= "0";
				$GLOBALS['targetWEVersion'] = 6502;
				$GLOBALS['targetWEVersionName'] = '6.5.0.2 mGallery Nightly';
				$GLOBALS['targetWEVersionString'] = '6.5.0.2';
				$GLOBALS['compareWeVersion'] = 6490; // not necessary when taking snapshot
				$GLOBALS['takeSnapshot'] = true;
//$GLOBALS['compareVersion'] = 9053;
//$GLOBALS['step1Version'] = 9054;
				break;
			default:
				$GLOBALS['isValidType'] = false;
		}
		break;
	case 'trunk':
		$GLOBALS['targetWEbranchDir'] = 'trunk';
		switch($GLOBALS['basicReleaseType']) {
			case 'release':
			case 'beta':
			case 'alpha':
			case 'rc':
				$GLOBALS['targetWEtype']= 'release';
				$GLOBALS['targetWEVersion']= 6440;
				$GLOBALS['targetWEVersionName']= '6.4.4';
				$GLOBALS['targetWEVersionString']= '6.4.4.0';
				$GLOBALS['compareWeVersion'] = 6430;
				$GLOBALS['versionsToDelete'] = array(6431);
				$GLOBALS['createTag'] = true;
				$GLOBALS['versionsToDelete'] = array(6498, 6499); // delete Beta and latest nightly
				break;
			case 'hotfix': // TODO: get all config from versions db to make this more generic
				//
				break;
			case 'nightly':
			case 'nightly-build':
				$GLOBALS['lockedForCronjob'] = false;
				$GLOBALS['targetWEtype'] = 'nightly-build';
				$GLOBALS['targetWEVersion'] = 6441;
				$GLOBALS['targetWEVersionName'] = '6.4.4.1 Nightly';
				$GLOBALS['targetWEVersionString'] = '6.4.4.1';
				$GLOBALS['compareWeVersion'] = 6440;
				break;
			default:
				$GLOBALS['isValidType'] = false;
		}
		break;
	case 'tag': // to build snap-shot version from older tags
		/*
		if(isset($_REQUEST['tag'])){
			$GLOBALS['targetWEbranch'] = 'tag';
		}
		$GLOBALS['targetWEbranchDir'] = 'tags/' . $_REQUEST['tag'];
		$GLOBALS['targetWEtype'] = "release";
		$GLOBALS['targetWEVersion'] = "6400";
		$GLOBALS['targetWEVersionName'] = "6.4.0 (built from svn-tag)";
		$GLOBALS['targetWEVersionString'] = "6.4.0.0";
		$GLOBALS['takeSnapshot'] = true;
		break;
		*/
		break;
	default:
		$GLOBALS['isValidBranch'] = false;
}
