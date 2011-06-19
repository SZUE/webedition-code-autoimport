#!/usr/bin/php
<?php
ini_set("display_errors", 1);
error_reporting(E_ALL & ~E_NOTICE);
//define('TESTMODE',1);

define('MASTERLANG','English');
define('ENC','UTF-8');

if(!defined('TESTMODE')){
	define('LANGS','English,Deutsch,French,Russian,Dutch,Finnish,Polish,Spanish');
	define('RECURSE',1);
	define('DIR','../../../webEdition/we/include/we_language/');
	define('QUIET',1);
}else{
	define('LANGS','English,Deutsch');
	define('DIR','test/');
}


define('FILE_TABLE','FILE_TABLE');
define('TEMPLATES_TABLE','TEMPLATES_TABLE');
define('WE_ZFVERSION','WE_ZFVERSION');
define('OBJECT_FILES_TABLE','OBJECT_FILES_TABLE');
define('OBJECT_TABLE','OBJECT_TABLE');

//dummy function - should never be called
function g_l($a,$b){
	if(!defined('QUIET')){
		trigger_error("Called g_l($a,$b) - remove this!",E_USER_WARNING);
	}
	return $a.$b;
}

function getVar($file){
		eval('include($file);');
		$vars=get_defined_vars();
		unset($vars['file']);
		foreach($vars as $name=>$v){
			return $vars[$name];
		}
		return false;
}

function getVars($dir,&$langs,$file){
	foreach($langs as $mylang=>&$val){
		$var=getVar($dir.$mylang.'_'.ENC.$file);
		if($var!==false){
			$val[$file]=$var;
		}
	}
}

function showDiff($langs){
	$notTrans=$all=array();
	foreach($langs as $lang=>&$val){
		if($lang==MASTERLANG){ //this is the reference lang
			continue;
		}
		foreach($val as $file=>$vars){
			if(!is_array($langs[MASTERLANG][$file])){
				continue;
			}
			//TODO: make this recursive
			foreach($langs[MASTERLANG][$file] as $key=>$v){
				if(array_key_exists($key,$vars)){//variable present
					if(is_array($v)){
						foreach($v as $k=>$j){
							if(array_key_exists($k,$vars[$key])){//variable is array
								if(!is_numeric($j) && $j!='' && ($j==$vars[$key][$k])){
									$notTrans[$lang][$file][$key][$k]=$j;
								}
							}else{//variable is array, subkey is missing
								$all[$lang][$file][$key][$k]='type:'.gettype($j);
							}
						}
					}else{
					if(!is_numeric($v) && $v!='' && ($v==$vars[$key])){
						$notTrans[$lang][$file][$key]=$v;
					}
					}
				}else{//variable missing
					$all[$lang][$file][$key]='type:'.gettype($v);
				}
			}
		}
	}
	echo "Missing vars:\n";
	print_r($all);
	echo "Untranslated vars:\n";
	print_r($notTrans);
}

function searchFiles($searchDir,&$langs,&$fileCnt){
	$mydir=DIR.MASTERLANG.'_'.ENC.$searchDir;
	$files=scandir($mydir);
	if(defined('TESTMODE')){
		print_r($files);
	}
	foreach($files as $file){
		if(is_dir($mydir.'/'.$file)){
			if(!defined('RECURSE')||$file=='.'||$file=='..'||$file=='.svn'){
				continue;
			}
			searchFiles($searchDir.'/'.$file,$langs,$fileCnt);
		}
		if(!is_file($mydir.'/'.$file)||substr($file,-3)!='php'){
			continue;
		}
		if(defined('TESTMODE')){
			echo "include $searchDir/$file\n";
		}
		$fileCnt++;
		getVars(DIR,$langs,$searchDir.'/'.$file);
		//temporary:
//		showDiff($langs);
	}
}

$langs=array();
foreach(explode(',',LANGS) as $lang){
	$langs[$lang]=array();
}
$fileCnt=0;
searchFiles('',$langs,$fileCnt);
echo "Included $fileCnt Files each Language (".count($langs)."):\n";
showDiff($langs);
