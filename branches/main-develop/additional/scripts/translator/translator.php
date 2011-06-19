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
	foreach($langs as $lang=>&$val){
		if($lang==MASTERLANG){ //this is the reference lang
			continue;
		}
		foreach($val as $file=>$vars){
			if(!is_array($langs[MASTERLANG][$file])){
				continue;
			}
			foreach($langs[MASTERLANG][$file] as $key=>$v){
				if(!array_key_exists($key,$vars)){
					$all[$lang][$file][$key]='type:'.gettype($v);
				}else if(is_array($v)){
					foreach($v as $k=>$j){
						if(!array_key_exists($k,$vars[$key])){
							$all[$lang][$file][$key][$k]='type:'.gettype($j);
						}
					}
				}
			}
		}
	}
	if(count($all)==0){
		echo "no missing vars in files:\n";
		foreach($langs[MASTERLANG] as $file=>$f){
			echo $file."\n";
		}
	}else{
		echo "Missing vars:\n";
		print_r($all);
	}
}

function searchFiles($searchDir,&$langs){
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
			searchFiles($searchDir.'/'.$file,$langs);
		}
		if(!is_file($mydir.'/'.$file)||substr($file,-3)!='php'){
			continue;
		}
		echo "include $searchDir/$file\n";
		getVars(DIR,$langs,$searchDir.'/'.$file);
		//temporary:
		showDiff($langs);
	}
}

$langs=array();
foreach(explode(',',LANGS) as $lang){
	$langs[$lang]=array();
}

searchFiles('',$langs);
//temporary disabled
//showDiff($langs);
