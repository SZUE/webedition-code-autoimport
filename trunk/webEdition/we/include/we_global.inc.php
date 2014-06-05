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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
if(isset($_SERVER['SCRIPT_NAME']) && str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(dirname(__FILE__), '', __FILE__)){
	exit();
}

function weFileExists($id, $table = FILE_TABLE, we_database_base $db = NULL){
	t_e('deprecated', __FUNCTION__);
	return we_base_file::isWeFile($id, $table, $db);
}

function correctUml($in){
	//FIXME: can we use this (as in objectfile): preg_replace(array('~&szlig;~','~&(.)(uml|grave|acute|circ|tilde|ring|cedil|slash|caron);|&(..)(lig);|&#.*;~', '~[^0-9a-zA-Z/._-]~'), array('ss','\1\3', ''), htmlentities($text));
	return strtr($in, array('ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss'));
}

function makePIDTail($pid, $cid, we_database_base $db = null, $table = FILE_TABLE){
	if($table != FILE_TABLE){
		return '1';
	}

	$db = $db ? $db : new DB_WE();
	$parentIDs = array();
	$pid = intval($pid);
	$parentIDs[] = $pid;
	while($pid != 0){
		$pid = f('SELECT ParentID FROM ' . FILE_TABLE . ' WHERE ID=' . $pid, '', $db);
		$parentIDs[] = $pid;
	}
	$cid = intval($cid);
	$foo = f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $cid, '', $db);
	$fooArr = unserialize($foo);
	$flag = (isset($fooArr['WorkspaceFlag']) ? $fooArr['WorkspaceFlag'] : 1);
	$pid_tail = array();
	if($flag){
		$pid_tail[] = OBJECT_X_TABLE . $cid . '.OF_Workspaces=""';
	}
	foreach($parentIDs as $pid){
		$pid_tail[] = OBJECT_X_TABLE . $cid . '.OF_Workspaces LIKE "%,' . $pid . ',%" OR ' . OBJECT_X_TABLE . $cid . '.OF_ExtraWorkspacesSelected LIKE "%,' . $pid . ',%"';
	}
	return ($pid_tail ? ' (' . implode(' OR ', $pid_tail) . ') ' : 1);
}

function makeIDsFromPathCVS($paths, $table = FILE_TABLE){
	if(strlen($paths) == 0 || strlen($table) == 0){
		return '';
	}
	$foo = exlplode(',', $paths);
	$db = new DB_WE();
//cleanup paths
	foreach($foo as &$path){
		$path = '"' . $db->escape('/' . ltrim(trim($path), '/')) . '"';
	}
	$db->query('SELECT ID FROM ' . $table . ' WHERE PATH IN (' . implode(',', $foo) . ')');
	$outArray = $db->getAll(true);

	return implode(',', $outArray);
}

function getHttpOption(){
	if(ini_get('allow_url_fopen') != 1){
		@ini_set('allow_url_fopen', '1');
		return (ini_get('allow_url_fopen') != 1 ?
				(function_exists('curl_init') ? 'curl' : 'none') :
				'fopen');
	}
	return 'fopen';
}

function getHTTP($server, $url, $port = '', $username = '', $password = ''){
//FIXME: add code for proxy, see weXMLBrowser
	$_opt = getHttpOption();
	if(strpos($server, '://') === FALSE){
		$server = 'http' . ($port == 443 ? 's' : '') . '://' . (($username && $password) ? "$username:$password@" : '') . $server . ':' . $port;
	}
	switch($_opt){
		case 'fopen':
			$page = 'Server Error: Failed opening URL: ' . $server . $url;
			$fh = @fopen($server . $url, 'rb');
			if(!$fh){
				$fh = @fopen($_SERVER['DOCUMENT_ROOT'] . $server . $url, 'rb');
			}
			if($fh){
				$page = '';
				while(!feof($fh)){
					$page .= fgets($fh, 8192);
				}
				fclose($fh);
			}
			return $page;
		case 'curl':
			$_response = we_util::getCurlHttp($server, $url, array());
			return ($_response['status'] ? $_response['error'] : $_response['data']);
		default:
			return 'Server error: Unable to open URL (php configuration directive allow_url_fopen=Off)';
	}
}

/**
 *
 * @param type $id
 * @param type $table
 * @return bool true on success, or if not in DB
 */
function deleteContentFromDB($id, $table, we_database_base $DB_WE = null){
	$DB_WE = $DB_WE ? $DB_WE : new DB_WE();

	if(!f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '" LIMIT 1', '', $DB_WE)){
		return true;
	}

	$DB_WE->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID IN (
		SELECT CID FROM ' . LINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '")');
	return $DB_WE->query('DELETE FROM ' . LINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '"');
}

/**
 * Strips off the table prefix - this function is save of calling multiple times
 * @param string $table
 * @return string stripped tablename
 */
function stripTblPrefix($table){
	return TBL_PREFIX != '' && (strpos($table, TBL_PREFIX) !== FALSE) ? substr($table, strlen(TBL_PREFIX)) : $table;
}

function addTblPrefix($table){
	return TBL_PREFIX . $table;
}

//FIXME: remove this & decide where to use old version of htmlspecialchars
function oldHtmlspecialchars($string, $flags = -1, $encoding = 'ISO-8859-1', $double_encode = true){
	$flags = ($flags == -1 ? ENT_COMPAT | (defined('ENT_HTML401') ? ENT_HTML401 : 0) : $flags);
	return htmlspecialchars($string, $flags, $encoding, $double_encode);
}

/**
 * filter all bad Xss attacks from var. Arrays can be used.
 * @param mixed $var
 * @return mixed
 */
function filterXss($var, $type = 'string'){
	if(!is_array($var)){
		return ($type == 'string' ? oldHtmlspecialchars(strip_tags($var)) : intval($var));
	}
	$ret = array();
	foreach($var as $key => $val){
		$ret[oldHtmlspecialchars(strip_tags($key))] = filterXss($val, $type);
	}
	return $ret;
}

/** Helper for Filtering variables (callback of array_walk)
 *
 * @param mixed $var value
 * @param string $key key used by array-walk - unused
 * @param array $data array pair of type & default
 * @return type
 */
function _weRequest(&$var, $key, array $data){
	list($type, $default) = $data;
	switch($type){
		case 'transaction':
			$var = (preg_match('|^([a-f0-9]){32}$|i', $var) ? $var : $default);
			return;
		case 'intList':
			$var = implode(',', array_map('intval', explode(',', trim($var, ','))));
			return;
		case 'unit':
			//FIMXE: check for %d[em,ex,pt,...]?
			return;
		case 'int':
			$var = intval($var);
			return;
		case 'float':
			$var = floatval($var);
			return;
		case 'bool':
			switch($var){
				case false:
				case '0':
				case 'off':
				case 'false':
					$var = false;
					return;
				case true:
				case 'true':
				case 'on':
				case '1':
					$var = true;
					return;
				default:
					$var = (bool) $var;
					return;
			}

		case 'toggle': //FIXME: temporary type => whenever possible use 'bool'
			$var = $var == 'on' || $var == 'off' || $var == '1' || $var == '0' ? $var : (bool) $var;
			return;
		case 'table': //FIXME: this doesn't hold for OBJECT_X_TABLE - make sure we don't use them in requests
			$var = $var && ($k = array_search($var, get_defined_constants(), true)) && (substr($k, -6) == '_TABLE') ? $var : $default;
			return;
		case 'email'://removes mailto:
			$var = filter_var(str_replace(we_base_link::TYPE_MAIL_PREFIX, '', $var), FILTER_SANITIZE_EMAIL);
			return;
		case 'file':
		case 'url':
			$var = filter_var($var, FILTER_SANITIZE_URL);
			return;
		case 'string'://strips tags
			$var = filter_var($var, FILTER_SANITIZE_STRING);
			return;
		case 'html':
			$var = filter_var($var, FILTER_SANITIZE_SPECIAL_CHARS);
			return;
		default:
			t_e('unknown filter type ' . $type);
		case 'js'://for information!
		case 'raw':
			//do nothing - used as placeholder for all types not yet known
			return;
	}
	$var = $default;
}

/**
 * Filter an Requested variable
 * @param string $type type to filter, see list in _weGetVar
 * @param string $name name of variable in Request array
 * @param mixed $default default value
 * @param mixed $index optional index
 * @return mixed default, if value not set, the filtered value else
 */
function weRequest($type, $name, $default = false, $index = null){
	if(!isset($_REQUEST[$name]) || (isset($_REQUEST[$name]) && $_REQUEST[$name] === '') || ($index !== null && (!isset($_REQUEST[$name][$index]) || ($_REQUEST[$name][$index] === '')))){
		return $default;
	}
	$var = ($index === null ? $_REQUEST[$name] : $_REQUEST[$name][$index]);
	if(is_array($var)){
		array_walk($var, '_weRequest', array($type, $default));
	} else {
		$oldVar = $var;
		_weRequest($var, '', array($type, $default));

		switch($type){
			case 'intList':
				$oldVar = trim($var, ',');
				$cmp = '' . $var;
				break;
			case 'bool'://bool is transfered as 0/1
				$cmp = '' . intval($var);
				break;
			default:
				$cmp = '' . $var;
		}
		if($oldVar != $cmp){
			t_e('changed values', $type, $name, $index, $oldVar, $var);
		}
	}
	return $var;
}

function we_makeHiddenFields($filter = ''){
	$filterArr = explode(',', $filter);
	$hidden = '';
	if($_REQUEST){
		foreach($_REQUEST as $key => $val){
			if(!in_array($key, $filterArr)){
				if(is_array($val)){
					foreach($val as $v){
						$hidden .= '<input type="hidden" name="' . $key . '" value="' . oldHtmlspecialchars($v) . '" />';
					}
				} else {
					$hidden .= '<input type="hidden" name="' . $key . '" value="' . oldHtmlspecialchars($val) . '" />';
				}
			}
		}
	}
	return $hidden;
}

function we_make_attribs($attribs, $doNotUse = ''){
	$attr = '';
	$fil = explode(',', $doNotUse);
	$fil[] = 'user';
	$fil[] = 'removefirstparagraph';
	if(is_array($attribs)){
		reset($attribs);
		foreach($attribs as $k => $v){
			if(!in_array($k, $fil)){
				$attr .= $k . '="' . $v . '" ';
			}
		}
		$attr = trim($attr);
	}
	return $attr;
}

//FIXME: remove in 6.5
function we_hasPerm($perm){
	t_e('deprecated', 'call of ' . __FUNCTION__);
	return permissionhandler::hasPerm($perm);
}

function we_getParentIDs($table, $id, &$ids, we_database_base $db = null){
	$db = $db ? $db : new DB_WE();
	while(($pid = f('SELECT ParentID FROM ' . $table . ' WHERE ID=' . intval($id), '', $db)) > 0){
		$id = $pid; // #5836
		$ids[] = $id;
	}
}

function makeArrayFromCSV($csv){
	$csv = trim(str_replace('\\,', '###komma###', $csv), ',');

	if($csv === ''){
		return array();
	}

	$foo = explode(',', $csv);
	foreach($foo as &$f){
		$f = trim(str_replace('###komma###', ',', $f));
	}
	return $foo;
}

function makeCSVFromArray($arr, $prePostKomma = false, $sep = ','){
	if(!$arr){
		return '';
	}

	$replaceKomma = (count($arr) > 1) || ($prePostKomma == true);

	if($replaceKomma){
		foreach($arr as &$a){
			$a = str_replace($sep, '###komma###', $a);
		}
	}
	$out = implode($sep, $arr);
	if($prePostKomma){
		$out = $sep . $out . $sep;
	}
	return ($replaceKomma ?
			str_replace('###komma###', '\\' . $sep, $out) :
			$out);
}

function in_parentID($id, $pid, $table = FILE_TABLE, we_database_base $db = null){
	if(intval($pid) != 0 && intval($id) == 0){
		return false;
	}
	if(intval($pid) == 0 || $id == $pid || ($id == '' && $id != '0')){
		return true;
	}
	$db = $db ? $db : new DB_WE();

	$found = array();
	$p = intval($id);
	do{
		if($p == $pid){
			return true;
		}
		if(in_array($p, $found)){
			return false;
		}
		$found[] = $p;
	} while(($p = f('SELECT ParentID FROM ' . $table . ' WHERE ID=' . intval($p), '', $db)));
	return false;
}

function in_workspace($IDs, $wsIDs, $table = FILE_TABLE, we_database_base $db = null, $norootcheck = false){
	if(!$wsIDs || $IDs === ''){
		return true;
	}
	$db = ($db ? $db : new DB_WE());

	if(!is_array($IDs)){
		$IDs = makeArrayFromCSV($IDs);
	}
	if(!is_array($wsIDs)){
		$wsIDs = makeArrayFromCSV($wsIDs);
	}
	if(!$wsIDs || (in_array(0, $wsIDs))){
		return true;
	}

	if((!$norootcheck) && in_array(0, $IDs)){
		return false;
	}
	foreach($IDs as $id){
		foreach($wsIDs as $ws){
			if(in_parentID($id, $ws, $table, $db) || ($id == $ws) || ($id == 0)){
				return true;
			}
		}
	}
	return false;
}

function path_to_id($path, $table = FILE_TABLE, we_database_base $db = null){
	//FIXME: make them all use $db at call
	if($path == '/'){
		return 0;
	}
	$db = ($db ? $db : new DB_WE());
	return intval(f('SELECT DISTINCT ID FROM ' . $db->escape($table) . ' WHERE Path="' . $db->escape($path) . '" LIMIT 1', '', $db));
}

function weConvertToIds($paths, $table){
	if(!is_array($paths)){
		return array();
	}
	$paths = array_unique($paths);
	$ids = array();
	foreach($paths as $p){
		$ids[] = path_to_id($p, $table);
	}
	return $ids;
}

function path_to_id_ct($path, $table, &$contentType){
	if($path == '/'){
		return 0;
	}
	$db = new DB_WE();
	$res = getHash('SELECT ID,ContentType FROM ' . $db->escape($table) . ' WHERE Path="' . $db->escape($path) . '"', $db);
	$contentType = isset($res['ContentType']) ? $res['ContentType'] : null;

	return intval(isset($res['ID']) ? $res['ID'] : 0);
}

function id_to_path($IDs, $table = FILE_TABLE, we_database_base $db = null, $prePostKomma = false, $asArray = false, $endslash = false, $isPublished = false){
	if(!is_array($IDs) && !$IDs){
		return '/';
	}

	$db = $db ? $db : new DB_WE();

	if(!is_array($IDs)){
		$IDs = makeArrayFromCSV($IDs);
	}
	$foo = array();
	foreach($IDs as $id){
		if($id == 0){
			$foo[$id] = '/';
		} else {
			$foo2 = getHash('SELECT Path,IsFolder FROM ' . $db->escape($table) . ' WHERE ID=' . intval($id) . ($isPublished ? ' AND Published>0' : ''), $db);
			if(isset($foo2['Path'])){
				if($endslash && $foo2['IsFolder']){
					$foo2['Path'] .= '/';
				}
				$foo[$id] = $foo2['Path'];
			}
		}
	}
	return $asArray ? $foo : makeCSVFromArray($foo, $prePostKomma);
}

function getHashArrayFromCSV($csv, $firstEntry, we_database_base $db = null){
	if(!$csv){
		return array();
	}
	$db = $db ? $db : new DB_WE();
	$IDArr = makeArrayFromCSV($csv);
	$out = $firstEntry ? array(
		0 => $firstEntry
		) : array();
	foreach($IDArr as $id){
		if(strlen($id) && ($path = id_to_path($id, FILE_TABLE, $db))){
			$out[$id] = $path;
		}
	}
	return $out;
}

function getPathsFromTable($table = FILE_TABLE, we_database_base $db = null, $type = FILE_ONLY, $wsIDs = '', $order = 'Path', $limitCSV = '', $first = ''){
	$db = ($db ? $db : new DB_WE());
	$limitCSV = trim($limitCSV, ',');
	$query = array();
	if($wsIDs){
		$idArr = makeArrayFromCSV($wsIDs);
		$wsPaths = makeArrayFromCSV(id_to_path($wsIDs, $table, $db));
		$qfoo = array();
		for($i = 0; $i < count($wsPaths); $i++){
			if((!$limitCSV) || in_workspace($idArr[$i], $limitCSV, FILE_TABLE, $db)){
				$qfoo[] = ' Path LIKE "' . $db->escape($wsPaths[$i]) . '%" ';
			}
		}
		if(!count($qfoo)){
			return array();
		}
		$query[] = ' (' . implode(' OR ', $qfoo) . ' )';
	}
	switch($type){
		case FILE_ONLY :
			$query[] = ' IsFolder=0 ';
			break;
		case FOLDER_ONLY :
			$query[] = ' IsFolder=1 ';
			break;
	}
	$out = $first ? array(0 => $first) : array();

	$db->query('SELECT ID,Path FROM ' . $table . (count($query) ? ' WHERE ' . implode(' AND ', $query) : '') . ' ORDER BY ' . $order);
	while($db->next_record()){
		$out[$db->f('ID')] = $db->f('Path');
	}
	return $out;
}

function pushChildsFromArr(&$arr, $table = FILE_TABLE, $isFolder = ''){
	$tmpArr = $arr;
	$tmpArr2 = array();
	foreach($arr as $id){
		pushChilds($tmpArr, $id, $table, $isFolder);
	}
	foreach(array_unique($tmpArr) as $id){
		$tmpArr2[] = $id;
	}
	return $tmpArr2;
}

function pushChilds(&$arr, $id, $table = FILE_TABLE, $isFolder = ''){
	$db = new DB_WE();
	$arr[] = $id;
	$db->query('SELECT ID FROM ' . $table . ' WHERE ParentID=' . intval($id) . (($isFolder != '' || $isFolder == 0) ? (' AND IsFolder=' . intval($isFolder)) : ''));
	while($db->next_record()){
		pushChilds($arr, $db->f('ID'), $table, $isFolder);
	}
}

function uniqueCSV($csv, $prePost = false){
	$arr = array_unique(makeArrayFromCSV($csv));
	$foo = array();
	foreach($arr as $v){
		$foo[] = $v;
	}
	return makeCSVFromArray($foo, $prePost);
}

function get_ws($table = FILE_TABLE, $prePostKomma = false){
	if(isset($_SESSION) && isset($_SESSION['perms'])){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return '';
		}
		if($_SESSION['user']['workSpace'] && isset($_SESSION['user']['workSpace'][$table]) && $_SESSION['user']['workSpace'][$table] != ''){
			return makeCSVFromArray($_SESSION['user']['workSpace'][$table], $prePostKomma);
		}
	}
	return '';
}

function we_readParents($id, &$parentlist, $tab, $match = 'ContentType', $matchvalue = 'folder', we_database_base $db = null){
	$db = $db ? $db : new DB_WE();
	$pid = f('SELECT ParentID FROM ' . $db->escape($tab) . ' WHERE ID=' . intval($id), '', $db);
	if($pid !== ''){
		if($pid == 0){
			$parentlist[] = $pid;
		} else {
			if(f('SELECT 1 FROM ' . $db->escape($tab) . ' WHERE ID=' . intval($pid) . ' AND ' . $match . ' = "' . $db->escape($matchvalue) . '" LIMIT 1', '', $db)){
				$parentlist[] = $pid;
				we_readParents($pid, $parentlist, $tab, $match, $matchvalue, $db);
			}
		}
	}
}

function we_readChilds($pid, &$childlist, $tab, $folderOnly = true, $where = '', $match = 'ContentType', $matchvalue = 'folder', we_database_base $db = null){
	$db = $db ? $db : new DB_WE();
	$db->query('SELECT ID,' . $db->escape($match) . ' FROM ' . $db->escape($tab) . ' WHERE ' . ($folderOnly ? ' IsFolder=1 AND ' : '') . 'ParentID=' . intval($pid) . ' ' . $where);
	$todo = array();
	while($db->next_record()){
		if($db->f($match) == $matchvalue){
			$todo[] = $db->f('ID');
		}
		$childlist[] = $db->f('ID');
	}
	foreach($todo as $id){
		we_readChilds($id, $childlist, $tab, $folderOnly, $where, $match, $matchvalue, $db);
	}
}

function getWsQueryForSelector($tab, $includingFolders = true){
	if(permissionhandler::hasPerm('ADMINISTRATOR')){
		return '';
	}

	if(!($ws = makeArrayFromCSV(get_ws($tab)))){
		return (($tab == NAVIGATION_TABLE || (defined('NEWSLETTER_TABLE') && $tab == NEWSLETTER_TABLE)) ? '' : ' OR RestrictOwners=0 ');
	}
	$paths = id_to_path($ws, $tab, null, false, true);
	$wsQuery = array();
	foreach($paths as $path){
		$parts = explode('/', $path);
		array_shift($parts);
		$last = array_pop($parts);
		$path = '/';
		foreach($parts as $part){

			$path .= $part;
			if($includingFolders){
				$wsQuery[] = 'Path = "' . $GLOBALS['DB_WE']->escape($path) . '"';
			} else {
				$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
			}
			$path .= '/';
		}
		$path .= $last;
		if($includingFolders){
			$wsQuery[] = 'Path = "' . $GLOBALS['DB_WE']->escape($path) . '"';
			$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
		} else {
			$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
		}
		$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
	}

	return ' AND (' . implode(' OR ', $wsQuery) . ')';
}

function get_def_ws($table = FILE_TABLE, $prePostKomma = false){
	if(!get_ws($table, $prePostKomma)){ // WORKARROUND
		return '';
	}
	if(permissionhandler::hasPerm('ADMINISTRATOR')){
		return '';
	}
	$ws = '';

	$foo = f('SELECT workSpaceDef FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION['user']['ID']), '', new DB_WE());
	$ws = makeCSVFromArray(makeArrayFromCSV($foo), $prePostKomma);

	if($ws == ''){
		$wsA = makeArrayFromCSV(get_ws($table, $prePostKomma));
		return (!empty($wsA) ? $wsA[0] : '');
	}
	return $ws;
}

/**
 * This function is equivalent to print_r, except that it adds addtional "pre"-headers
 * @param * $val the variable to print
 * @param bool html (default: true) whether to apply oldHtmlspecialchars
 * @param bool useTA (default: false) whether output is formated as textarea
 */
function p_r($val, $html = true, $useTA = false){
	$val = print_r($val, true);
	echo ($useTA ? '<textarea style="width:100%" rows="20">' : '<pre>') .
	($html ? oldHtmlspecialchars($val) : $val) .
	($useTA ? '</textarea>' : '</pre>');
}

/**
 * This function triggers an error, which is logged to systemlog, and if enabled to we-log. This function can take any number of variables!
 * @param string $type (optional) define the type of the log; possible values are: warning (default), error, notice, deprecated
 * Note: type error causes we to stop execution, cause this is considered a major bug; but value is still logged.
 */
function t_e($type = 'warning'){
	$inc = false;
	$data = array();
	switch(is_string($type) ? strtolower($type) : -1){
		case 'error':
			$inc = true;
			$type = E_USER_ERROR;
			break;
		case 'notice':
			$inc = true;
			$type = E_USER_NOTICE;
			break;
		case 'deprecated':
			$inc = true;
			if(defined('E_USER_DEPRECATED')){ //not defined in php <5.3; write warning instead
				$type = E_USER_DEPRECATED;
			} else {
				$data[] = 'DEPRECATED';
				$type = E_USER_NOTICE;
			}
			break;
		case 'warning':
			$inc = true;
		default:
			$type = E_USER_WARNING;
	}
	foreach(func_get_args() as $value){
		if($inc){
			$inc = false;
			continue;
		}
		if(is_array($value) || is_object($value)){
			$data[] = @print_r($value, true);
		} else {
			$data[] = (is_bool($value) ? var_export($value, true) : $value);
		}
	}

	if(count($data) > 0){
		trigger_error(implode("\n---------------------------------------------------\n", $data), $type);
	}
}

function parseInternalLinks(&$text, $pid, $path = '', $doBaseReplace = true){
	$doBaseReplace&=we_isHttps();
	$DB_WE = new DB_WE();
	$regs = array();
	if(preg_match_all('/(href|src)="' . we_base_link::TYPE_INT_PREFIX . '(\\d+)(&amp;|&)?("|[^"]+")/i', $text, $regs, PREG_SET_ORDER)){
		foreach($regs as $reg){

			$foo = getHash('SELECT Path,(ContentType="' . we_base_ContentTypes::IMAGE . '") AS isImage  FROM ' . FILE_TABLE . ' WHERE ID=' . intval($reg[2]) . (isset($GLOBALS['we_doc']->InWebEdition) && $GLOBALS['we_doc']->InWebEdition ? '' : ' AND Published>0'), $DB_WE);

			if($foo && $foo['Path']){
				$path_parts = pathinfo($foo['Path']);
				if(show_SeoLinks() && WYSIWYGLINKS_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
					$foo['Path'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
				}
				$text = str_replace($reg[1] . '="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . $reg[3] . $reg[4], $reg[1] . '="' . ($doBaseReplace && $foo['isImage'] ? BASE_IMG : '') . $foo['Path'] . ($reg[3] ? '?' : '') . $reg[4], $text);
			} else {
				$text = preg_replace(array(
					'|<a [^>]*href="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . '"[^>]*>(.*)</a>|Ui', '|<a [^>]*href="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . '"[^>]*>|Ui', '|<img [^>]*src="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . '"[^>]*>|Ui'), array(
					'\1', '', ''), $text);
			}
		}
	}
	if(preg_match_all('/src="' . we_base_link::TYPE_THUMB_PREFIX . '([^" ]+)"/i', $text, $regs, PREG_SET_ORDER)){
		foreach($regs as $reg){
			list($imgID, $thumbID) = explode(',', $reg[1]);
			$thumbObj = new we_thumbnail();
			if($thumbObj->initByImageIDAndThumbID($imgID, $thumbID)){
				$text = str_replace('src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1] . '"', 'src="' . ($doBaseReplace ? BASE_IMG : '') . $thumbObj->getOutputPath(false, true) . '"', $text);
			} else {
				$text = preg_replace('|<img[^>]+src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1] . '[^>]+>|Ui', '', $text);
			}
		}
	}
	if(defined('OBJECT_TABLE')){
		if(preg_match_all('/href="' . we_base_link::TYPE_OBJ_PREFIX . '(\d+)(\??)("|[^"]+")/i', $text, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				$href = we_objectFile::getObjectHref($reg[1], $pid, $path, null, WYSIWYGLINKS_DIRECTORYINDEX_HIDE, WYSIWYGLINKS_OBJECTSEOURLS);
				if(isset($GLOBALS['we_link_not_published'])){
					unset($GLOBALS['we_link_not_published']);
				}
				if($href){
					$text = ($reg[2] == '?' ?
							str_replace('href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '?', 'href="' . $href . '&amp;', $text) :
							str_replace('href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . $reg[2] . $reg[3], 'href="' . $href . $reg[2] . $reg[3], $text));
				} else {
					$text = preg_replace(array('|<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '"[^>]*>(.*)</a>|Ui',
						'|<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '"[^>]*>|Ui',), array('\1'), $text);
				}
			}
		}
	}

	return preg_replace('/\<a>(.*)\<\/a>/siU', '\1', $text);
}

function removeHTML($val){
	$val = preg_replace(array('%<br ?/?>%i', '/<[^><]+>/'), array('###BR###', ''), str_replace(array('<?', '?>'), array('###?###', '###/?###'), $val));
	return str_replace(array('###BR###', '###?###', '###/?###'), array('<br/>', '<?', '?>'), $val);
}

function removePHP($val){
	return we_util::rmPhp($val);
}

function getMysqlVer($nodots = true){
	$DB_WE = new DB_WE();
	$res = f('SELECT VERSION()', '', $DB_WE);

	if($res){
		$res = explode('-', $res);
	} else {
		$res = f('SHOW VARIABLES LIKE "version"', 'Value', $DB_WE);
		if($res){
			$res = explode('-', $res);
		}
	}
	if(isset($res)){
		if($nodots){
			$strver = substr(str_replace('.', '', $res[0]), 0, 4);
			$ver = (int) $strver;
			if(strlen($ver) < 4){
				$ver = sprintf('%04d', $ver);
				if(substr($ver, 0, 1) == '0'){
					$ver = (int) (substr($ver, 1) . '0');
				}
			}

			return $ver;
		}
		return $res[0];
	}
	return '';
}

function we_mail($recipient, $subject, $txt, $from = ''){
	if(runAtWin() && $txt){
		$txt = str_replace("\n", "\r\n", $txt);
	}

	$phpmail = new we_util_Mailer($recipient, $subject, $from);
	$phpmail->setCharSet($GLOBALS['WE_BACKENDCHARSET']);
	$phpmail->addTextPart(trim($txt));
	$phpmail->buildMessage();
	$phpmail->Send();
}

function runAtWin(){
	return stripos(PHP_OS, 'win') !== false && (stripos(PHP_OS, 'darwin') === false);
}

function weMemDebug(){
	print("Mem usage " . round(((memory_get_usage() / 1024) / 1024), 3) . ' MiB');
}

function weGetCookieVariable($name){
	$c = isset($_COOKIE['we' . session_id()]) ? $_COOKIE['we' . session_id()] : '';
	$vals = array();
	if($c){
		$parts = explode('&', $c);
		foreach($parts as $p){
			$foo = explode('=', $p);
			$vals[rawurldecode($foo[0])] = rawurldecode($foo[1]);
		}
		return (isset($vals[$name]) ? $vals[$name] : '');
	}
	return '';
}

function getContentTypeFromFile($dat){
	if(is_link($dat)){
		return 'link';
	}
	if(is_dir($dat)){
		return 'folder';
	}
	$ext = strtolower(preg_replace('#^.*(\..+)$#', '\1', $dat));
	if($ext){
		$type = we_base_ContentTypes::inst()->getTypeForExtension($ext);
		if($type){
			return $type;
		}
	}

	return we_base_ContentTypes::APPLICATION;
}

function getUploadMaxFilesize($mysql = false, we_database_base $db = null){
	$post_max_size = we_convertIniSizes(ini_get('post_max_size'));
	$upload_max_filesize = we_convertIniSizes(ini_get('upload_max_filesize'));
	$min = min($post_max_size, $upload_max_filesize, ($mysql ? getMaxAllowedPacket($db) : PHP_INT_MAX));

	if(intval(WE_MAX_UPLOAD_SIZE) == 0){
		return $min;
	} else {
		return min(WE_MAX_UPLOAD_SIZE * 1024 * 1024, $min);
	}
}

function getMaxAllowedPacket(we_database_base $db = null){
	return f('SHOW VARIABLES LIKE "max_allowed_packet"', 'Value', ($db ? $db : new DB_WE()));
}

function we_convertIniSizes($in){
	$regs = array();
	if(preg_match('#^([0-9]+)M$#i', $in, $regs)){
		return 1024 * 1024 * intval($regs[1]);
	}
	if(preg_match('#^([0-9]+)K$#i', $in, $regs)){
		return 1024 * intval($regs[1]);
	}
	return intval($in);
}

function we_getDocumentByID($id, $includepath = '', we_database_base $db = null, &$charset = ''){
	$db = $db ? $db : new DB_WE();
// look what document it is and get the className
	$clNm = f('SELECT ClassName FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), '', $db);

// init Document
	if(isset($GLOBALS['we_doc'])){
		$backupdoc = $GLOBALS['we_doc'];
	}

	if(!$clNm){
		t_e('Document with ID' . $id . ' missing, or ClassName not set.', $includepath);
		t_e('error', 'Classname/ID missing');
	}
	$GLOBALS['we_doc'] = new $clNm();

	$GLOBALS['we_doc']->initByID($id, FILE_TABLE, we_class::LOAD_MAID_DB);
	$content = $GLOBALS['we_doc']->i_getDocument($includepath);
	$charset = $GLOBALS['we_doc']->getElement('Charset');
	if(!$charset){
		$charset = DEFAULT_CHARSET;
	}

	if(isset($backupdoc)){
		$GLOBALS['we_doc'] = $backupdoc;
	}
	return $content;
}

function we_getObjectFileByID($id, $includepath = ''){
	$mydoc = new we_objectFile();
	$mydoc->initByID($id, OBJECT_FILES_TABLE, we_class::LOAD_MAID_DB);
	return $mydoc->i_getDocument($includepath);
}

/**
 * @return str
 * @param bool $slash
 * @desc returns the protocol, the webServer is running, http or https, when slash is true - :// is added to protocol
 */
function getServerProtocol($slash = false){
	return (we_isHttps() ? 'https' : 'http') . ($slash ? '://' : '');
}

function getServerAuth(){
	$pwd = rawurlencode(defined('HTTP_USERNAME') ? HTTP_USERNAME : (isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '')) . ':' .
		rawurlencode(defined('HTTP_PASSWORD') ? HTTP_PASSWORD : (isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '')) . '@';
	return (strlen($pwd) > 3) ? $pwd : '';
}

function getServerUrl($useUserPwd = false){
	$port = '';
	if(isset($_SERVER['SERVER_PORT'])){
		if((we_isHttps() && $_SERVER['SERVER_PORT'] != 443) || ($_SERVER['SERVER_PORT'] != 80)){
			$port = ':' . $_SERVER['SERVER_PORT'];
		}
	}
	if($useUserPwd){
		$pwd = getServerAuth();
	}
	return getServerProtocol(true) . ($useUserPwd && strlen($pwd) > 3 ? $pwd : '') . $_SERVER['SERVER_NAME'] . $port;
}

function we_check_email($email){ // Zend validates only the pure address
	if(($pos = strpos($email, '<'))){// format "xxx xx" <test@test.de>
		++$pos;
		$email = substr($email, $pos, strrpos($email, '>') - $pos);
	}
	return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
}

/* function getRequestVar($name, $default, $yescode = '', $nocode = ''){
  if(isset($_REQUEST[$name])){
  if($yescode){
  eval($yescode);
  }
  return $_REQUEST[$name];
  } else {
  if($nocode){
  eval($nocode);
  }
  return $default;
  }
  } */

/**
 * This function returns preference for given name; Checks first the users preferences and then global
 *
 * @param          string                                  $name
 *
 * @see            getAllGlobalPrefs()
 *
 * @return         string
 */
function getPref($name){
	return (isset($_SESSION['prefs'][$name]) ?
			$_SESSION['prefs'][$name] :
			(defined($name) ? constant($name) : ''));
}

/**
 * The function saves the user pref in the session and the database; The function works with user preferences only
 *
 * @param          string                                  $name
 * @param          string                                  $value
 *
 * @see            setUserPref()
 *
 * @return         boolean
 */
function setUserPref($name, $value){
	if(isset($_SESSION['prefs'][$name]) && isset($_SESSION['prefs']['userID']) && $_SESSION['prefs']['userID']){
		$_SESSION['prefs'][$name] = $value;
		we_users_user::writePrefs($_SESSION['prefs']['userID'], new DB_WE());
		return true;
	}
	return false;
}

/**
 * This function creates the given path in the repository and returns the id of the last created folder
 *
 * @param          string				$path
 * @param          string				$table
 * @param          array				$pathids
 *
 * @return         string
 */
function makePath($path, $table, &$pathids, $owner = 0){
	$path = str_replace('\\', '/', $path);
	$patharr = explode('/', $path);
	$mkpath = '';
	$pid = 0;
	foreach($patharr as $elem){
		if($elem != '' && $elem != '/'){
			$mkpath .= '/' . $elem;
			$id = path_to_id($mkpath, $table);
			if(!$id){
				$new = new we_folder();
				$new->Text = $elem;
				$new->Filename = $elem;
				$new->ParentID = $pid;
				$new->Path = $mkpath;
				$new->Table = $table;
				$new->CreatorID = $owner;
				$new->ModifierID = $owner;
				$new->Owners = ',' . $owner . ',';
				$new->OwnersReadOnly = serialize(array(
					$owner => 0
				));
				$new->we_save();
				$id = $new->ID;
				$pathids[] = $id;
			}
			$pid = $id;
		}
	}

	return $pid;
}

/**
 * This function clears path from double slashes and back slashes
 *
 * @param          string                                  $path
 *
 *
 * @return         string
 */
function clearPath($path){
	return preg_replace('#/+#', '/', str_replace('\\', '/', $path));
}

/** This function should be used ONLY in generating code for the FRONTEND
 * @return	string
 * @param	string $element
 * @param	[opt]array $attribs
 * @param	[opt]string $content
 * @param	[opt]boolean $forceEndTag=false
 * @param [opt]boolean onlyStartTag=false
 * @desc	returns the html element with the given attribs.attr[pass_*] is replaced by "*" to loop some
 *          attribs through the tagParser.
 */
function getHtmlTag($element, $attribs = array(), $content = '', $forceEndTag = false, $onlyStartTag = false){
	require_once (WE_INCLUDES_PATH . 'we_tag.inc.php');

//	take values given from the tag - later from preferences.
	$xhtml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, true);

//	remove x(ht)ml-attributs
	$removeAttribs = array('xml', 'xmltype', 'to', 'nameto', '_name_orig', null);

	switch($element){
		case 'img':
			if(defined('HIDENAMEATTRIBINWEIMG_DEFAULT') && HIDENAMEATTRIBINWEIMG_DEFAULT && (!isset($GLOBALS['WE_MAIN_DOC']) || !$GLOBALS['WE_MAIN_DOC']->InWebEdition)){
				$removeAttribs[] = 'name';
			}
			break;
		case 'a':
			if(defined('HIDENAMEATTRIBINWEIMG_DEFAULT') && HIDENAMEATTRIBINWEIMG_DEFAULT && (!isset($GLOBALS['WE_MAIN_DOC']) || !$GLOBALS['WE_MAIN_DOC']->InWebEdition)){
				$removeAttribs[] = 'name';
			}
			break;
		case 'form':
			if(defined('HIDENAMEATTRIBINWEFORM_DEFAULT') && HIDENAMEATTRIBINWEFORM_DEFAULT && (!isset($GLOBALS['WE_MAIN_DOC']) || !$GLOBALS['WE_MAIN_DOC']->InWebEdition)){
				$removeAttribs[] = 'name';
			}
			break;
	}
	if($xhtml){ //	xhtml, check if and what we shall debug
		$_xmlClose = true;

		if(XHTML_DEBUG){ //  check if XHTML_DEBUG is activated - system pref
			require_once (WE_INCLUDES_PATH . 'validation/xhtml.inc.php');

			$showWrong = (isset($_SESSION['prefs']['xhtml_show_wrong']) && $_SESSION['prefs']['xhtml_show_wrong'] && isset(
					$GLOBALS['we_doc']) && $GLOBALS['we_doc']->InWebEdition); //  check if XML_SHOW_WRONG is true (user) - only in webEdition
// at the moment only transitional is supported
			$xhtmlType = weTag_getAttribute('xmltype', $attribs, 'transitional');
			$attribs = removeAttribs($attribs, $removeAttribs);

			validateXhtmlAttribs($element, $attribs, $xhtmlType, $showWrong, XHTML_REMOVE_WRONG);
		} else {
			$attribs = removeAttribs($attribs, $removeAttribs);
		}
	} else {
//	default at the moment is xhtml-style
		$_xmlClose = false;
		$attribs = removeAttribs($attribs, $removeAttribs);
	}

	$tag = '<' . $element;

	foreach($attribs as $k => $v){
		$tag .= ' ' . ($k == 'link_attribute' ? // Bug #3741
				$v :
				str_replace('pass_', '', $k) . '="' . $v . '"');
	}
	return $tag . ($content || $forceEndTag ? //	use endtag
			'>' . $content . '</' . $element . '>' :
//	xml style or not
			( ($_xmlClose && !$onlyStartTag) ? ' />' : '>'));
}

/**
 * @return array
 * @param array $attribs
 * @param array $remove
 * @desc removes all entries of $attribs, where the key from attribs is in values of $remove
 */
function removeAttribs($attribs, array $remove = array()){
	foreach($remove as $r){
		if(isset($attribs[$r])){
			unset($attribs[$r]);
		}
	}
	return $attribs;
}

/**
 * This function works in very same way as the standard array_splice function
 * except the second parametar is the array index and not just offset
 * The functions modifies the array that has been passed by reference as the first function parametar
 *
 * @param          array                                  $a
 * @param          interger                                $start
 * @param          integer                                 $len
 *
 *
 * @return         none
 */
function new_array_splice(&$a, $start, $len = 1){
	$ks = array_keys($a);
	$k = array_search($start, $ks);
	if($k !== false){
		$ks = array_splice($ks, $k, $len);
		foreach($ks as $k){
			unset($a[$k]);
		}
	}
}

function we_loadLanguageConfig(){
	$file = WE_INCLUDES_PATH . 'conf/we_conf_language.inc.php';
	if(!file_exists($file) || !is_file($file)){
		we_writeLanguageConfig((WE_LANGUAGE == 'Deutsch' || WE_LANGUAGE == 'Deutsch_UTF-8' ? 'de_DE' : 'en_GB'), array('de_DE', 'en_GB'));
	}
	include_once ($file);
}

function getWeFrontendLanguagesForBackend(){
	$la = array();
	$targetLang = we_core_Local::weLangToLocale($GLOBALS['WE_LANGUAGE']);
	if(!Zend_Locale::hasCache()){
		Zend_Locale::setCache(getWEZendCache());
	}
	foreach($GLOBALS['weFrontendLanguages'] as $Locale){
		$temp = explode('_', $Locale);
		$la[$Locale] = (count($temp) == 1 ?
				CheckAndConvertISObackend(Zend_Locale::getTranslation($temp[0], 'language', $targetLang) . ' ' . $Locale) :
				CheckAndConvertISObackend(Zend_Locale::getTranslation($temp[0], 'language', $targetLang) . ' (' . Zend_Locale::getTranslation($temp[1], 'territory', $targetLang) . ') ' . $Locale));
	}
	return $la;
}

function we_writeLanguageConfig($default, $available = array()){

	$locales = '';
	sort($available);
	foreach($available as $Locale){
		$locales .= "	'" . $Locale . "',\n";
	}

	return we_base_file::save(WE_INCLUDES_PATH . 'conf/we_conf_language.inc.php', '<?php
$GLOBALS[\'weFrontendLanguages\'] = array(
' . $locales . '
);

$GLOBALS[\'weDefaultFrontendLanguage\'] = \'' . $default . '\';'
			, 'w+'
	);
}

function we_filenameNotValid($filename, $isIso = false){
	return (substr($filename, 0, 2) === '..') || preg_match('![<>?":|\\/*' . ($isIso ? '\x00-\x20\x7F-\xFF' : '') . ']!', $filename);
}

function we_isHttps(){
	return isset($_SERVER['HTTPS']) && (strtoupper($_SERVER['HTTPS']) == 'ON' || $_SERVER['HTTPS'] == 1);
}

function getVarArray($arr, $string){
	if(!isset($arr)){
		return false;
	}
	$arr_matches = array();
	preg_match_all('/\[([^\]]*)\]/', $string, $arr_matches, PREG_PATTERN_ORDER);
	$return = $arr;
	foreach($arr_matches[1] as $dimension){
		if(isset($return[$dimension])){
			$return = $return[$dimension];
		} else {
			return false;
		}
	}
	return $return;
}

function CheckAndConvertISOfrontend($utf8data){
	$to = (isset($GLOBALS['CHARSET']) && $GLOBALS['CHARSET'] ? $GLOBALS['CHARSET'] : DEFAULT_CHARSET);
	return ($to == 'UTF-8' ? $utf8data : mb_convert_encoding($utf8data, $to, 'UTF-8'));
}

function CheckAndConvertISObackend($utf8data){
	$to = (isset($GLOBALS['we']['PageCharset']) ? $GLOBALS['we']['PageCharset'] : $GLOBALS['WE_BACKENDCHARSET']);
	return ($to == 'UTF-8' ? $utf8data : mb_convert_encoding($utf8data, $to, 'UTF-8'));
}

/* * internal function - do not call */

function g_l_encodeArray($tmp){
	$charset = (isset($_SESSION['user']) && isset($_SESSION['user']['isWeSession']) ? $GLOBALS['WE_BACKENDCHARSET'] : (isset($GLOBALS['CHARSET']) ? $GLOBALS['CHARSET'] : $GLOBALS['WE_BACKENDCHARSET']));
	return (is_array($tmp) ?
			array_map('g_l_encodeArray', $tmp) :
			mb_convert_encoding($tmp, $charset, 'UTF-8'));
}

/**
 * getLanguage property
 *  Note: underscores in name are used as directories - modules_workflow is searched in subdir modules
 * usage example: echo g_l('modules_workflow','[test][new]');
 *
 * @param $name string name of the variable, without 'l_', this name is also used for inclusion
 * @param $specific array the array element to access
 * @param $omitErrors boolean don't throw an error on non-existent entry
 */
function g_l($name, $specific, $omitErrors = false){
	//t_e($name,$specific,$GLOBALS['we']['PageCharset'] , $GLOBALS['WE_BACKENDCHARSET']);
	$charset = (isset($_SESSION['user']) && isset($_SESSION['user']['isWeSession']) ?
//inside we
			(isset($GLOBALS['we']['PageCharset']) ? $GLOBALS['we']['PageCharset'] : $GLOBALS['WE_BACKENDCHARSET']) :
//front-end
			(isset($GLOBALS['CHARSET']) && $GLOBALS['CHARSET'] ? $GLOBALS['CHARSET'] : DEFAULT_CHARSET) );
//	return $name.$specific;
//cache last accessed lang var
	static $cache = array();
//echo $name.$specific;
	if(isset($cache["l_$name"])){
		$tmp = getVarArray($cache["l_$name"], $specific);
		if(!($tmp === false)){
			return ($charset != 'UTF-8' ?
					(is_array($tmp) ?
						array_map('g_l_encodeArray', $tmp) :
						mb_convert_encoding($tmp, $charset, 'UTF-8')
					) :
					$tmp);
		}
	}
	$file = WE_INCLUDES_PATH . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/' . str_replace('_', '/', $name) . '.inc.php';
	if(file_exists($file)){
		include($file);
		$tmp = (isset(${'l_' . $name}) ? getVarArray(${'l_' . $name}, $specific) : false);
//get local variable
		if($tmp !== false){
			$cache['l_' . $name] = ${'l_' . $name};
			return ($charset != 'UTF-8' ?
					(is_array($tmp) ?
						array_map('g_l_encodeArray', $tmp) :
						mb_convert_encoding($tmp, $charset, 'UTF-8')
					) :
					$tmp);
		} else {
			if(!$omitErrors){
				t_e('notice', 'Requested lang entry l_' . $name . $specific . ' not found in ' . $file . ' !');
				return '??';
			}
			return false;
		}
	}
	if(!$omitErrors){
		t_e('Language file "' . $file . '" not found with entry ' . $specific);
		return '?';
	}
	return false;
}

function we_templateInit(){
	if(isset($GLOBALS['WE_TEMPLATE_INIT'])){
		++$GLOBALS['WE_TEMPLATE_INIT'];
	} else {
		$GLOBALS['WE_TEMPLATE_INIT'] = 1;

		// Activate the autoloader & webEdition error handler
		require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/core/autoload.inc.php');
		require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tag.inc.php');

		if(!isset($GLOBALS['DB_WE'])){
			$GLOBALS['DB_WE'] = new DB_WE();
		}
//check for Trigger
		if(defined('SCHEDULE_TABLE') && (!$GLOBALS['we_doc']->InWebEdition) &&
			(SCHEDULER_TRIGGER == SCHEDULER_TRIGGER_PREDOC) &&
			(!isset($GLOBALS['we']['backVars']) || (isset($GLOBALS['we']['backVars']) && count($GLOBALS['we']['backVars']) == 0)) //on first call this variable is unset, so we're not inside an include
		){
			we_schedpro::trigger_schedule();
		}
	}

	if($GLOBALS['we_doc'] && (!isset($GLOBALS['WE_DOC_ID']) || $GLOBALS['WE_DOC_ID'] != $GLOBALS['we_doc']->ID)){
		$GLOBALS['WE_DOC_ID'] = $GLOBALS['we_doc']->ID;
		if(!isset($GLOBALS['WE_MAIN_ID'])){
			$GLOBALS['WE_MAIN_ID'] = $GLOBALS['we_doc']->ID;
		}
		if(!isset($GLOBALS['WE_MAIN_DOC'])){
			//FIXME: remove? does this really have ever worked?
			$GLOBALS['WE_MAIN_DOC'] = clone($GLOBALS['we_doc']);
		}
		if(!isset($GLOBALS['WE_MAIN_DOC_REF'])){
			$GLOBALS['WE_MAIN_DOC_REF'] = &$GLOBALS['we_doc'];
		}
		if(!isset($GLOBALS['WE_MAIN_EDITMODE'])){
			$GLOBALS['WE_MAIN_EDITMODE'] = isset($GLOBALS['we_editmode']) ? $GLOBALS['we_editmode'] : false;
		}
		$GLOBALS['WE_DOC_ParentID'] = $GLOBALS['we_doc']->ParentID;
		$GLOBALS['WE_DOC_Path'] = $GLOBALS['we_doc']->Path;
		$GLOBALS['WE_DOC_IsDynamic'] = $GLOBALS['we_doc']->IsDynamic;
		$GLOBALS['WE_DOC_FILENAME'] = $GLOBALS['we_doc']->Filename;
		$GLOBALS['WE_DOC_Category'] = isset($GLOBALS['we_doc']->Category) ? $GLOBALS['we_doc']->Category : '';
		$GLOBALS['WE_DOC_EXTENSION'] = $GLOBALS['we_doc']->Extension;
		$GLOBALS['TITLE'] = $GLOBALS['we_doc']->getElement('Title');
		$GLOBALS['KEYWORDS'] = $GLOBALS['we_doc']->getElement('Keywords');
		$GLOBALS['DESCRIPTION'] = $GLOBALS['we_doc']->getElement('Description');
//check if CHARSET is valid
		$charset = $GLOBALS['we_doc']->getElement('Charset');
		$GLOBALS['CHARSET'] = (!in_array($charset, we_base_charsetHandler::getAvailCharsets()) ? DEFAULT_CHARSET : $charset);
	}
}

function we_templateHead($fullHeader = false){
	if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
		return;
	}
	if($fullHeader){
		if(isset($GLOBALS['WE_HTML_HEAD_BODY'])){
			echo we_templatePreContent(); //to increment we_templatePreContent-var
			return;
		}
	}
	echo ($fullHeader ? we_html_element::htmlDocType() . '<html><head><title>WE</title>' : '') . STYLESHEET_BUTTONS_ONLY . SCRIPT_BUTTONS_ONLY .
	we_html_element::jsScript(JS_DIR . 'windows.js') . weSuggest::getYuiFiles() .
	we_html_element::jsScript(JS_DIR . 'attachKeyListener.js') .
	we_html_element::jsElement('parent.openedWithWE = 1;');
	require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
	if($fullHeader){
		echo '</head><body onunload="doUnload()">';
		we_templatePreContent();
		$GLOBALS['WE_HTML_HEAD_BODY'] = true;
	}
}

function we_templatePreContent($force = false){//force is used by templates with a full html/body.
	if(isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']){
		if($force || (!isset($GLOBALS['WE_HTML_HEAD_BODY']) && !isset($GLOBALS['we_templatePreContent']))){
			echo '<form name="we_form" action="" method="post" onsubmit="return false;">' .
			we_class::hiddenTrans();
		}
		$GLOBALS['we_templatePreContent'] = (isset($GLOBALS['we_templatePreContent']) ? $GLOBALS['we_templatePreContent'] + 1 : $GLOBALS['WE_TEMPLATE_INIT']);
	}
}

function we_templatePostContent($force = false, $fullPoster = false){//force on </body tag
	if(isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode'] && ($force || ( --$GLOBALS['we_templatePreContent']) == 0)){
		if($force){//never do this again
			$GLOBALS['we_templatePreContent'] = -10000;
		}
		$yuiSuggest = &weSuggest::getInstance();
		//FIXME: check this new field to determine if all data has been transmitted
		echo $yuiSuggest->getYuiCode() . '<input type="hidden" name="we_complete_request" value="1"/></form>' .
		we_html_element::jsElement('setTimeout("doScrollTo();",100);') .
		($fullPoster ? '</body></html>' : '');
	}
}

function we_templatePost(){
	if(--$GLOBALS['WE_TEMPLATE_INIT'] == 0){
		if(isset($_SESSION) && isset($_SESSION['webuser']) && isset($_SESSION['webuser']['loginfailed'])){
			unset($_SESSION['webuser']['loginfailed']);
		}
		if(defined('DEBUG_MEM')){
			weMemDebug();
		}
		flush();
//check for Trigger
		if(defined('SCHEDULE_TABLE') && (!$GLOBALS['WE_MAIN_DOC']->InWebEdition) &&
			(SCHEDULER_TRIGGER == SCHEDULER_TRIGGER_POSTDOC) &&
			(!isset($GLOBALS['we']['backVars']) || (isset($GLOBALS['we']['backVars']) && count($GLOBALS['we']['backVars']) == 0))//not inside an included Doc
		){ //is set to Post or not set (new default)
			session_write_close();
			we_schedpro::trigger_schedule();
		}
	}
}

function show_SeoLinks(){
	return (
		!(SEOINSIDE_HIDEINWEBEDITION && $GLOBALS['WE_MAIN_DOC']->InWebEdition) &&
		!(SEOINSIDE_HIDEINEDITMODE && (isset($GLOBALS['we_editmode']) && ($GLOBALS['we_editmode']) || (isset($GLOBALS['WE_MAIN_EDITMODE']) && $GLOBALS['WE_MAIN_EDITMODE'])))
		);
}

function we_TemplateExit($param = 0){
	if(isset($GLOBALS['FROM_WE_SHOW_DOC']) && $GLOBALS['FROM_WE_SHOW_DOC']){
		exit($param);
	} else {
//we are inside we, we don't terminate here
		if($param){
			echo $param;
		}
//FIXME: use g_l
		t_e('template forces document to exit, see Backtrace for template name. Message of statement', $param);
	}
}

function we_cmd_enc($str){
	return ($str ? 'WECMDENC_' . urlencode(base64_encode($str)) : '');
}

function we_cmd_dec($no, $default = ''){
	if(isset($_REQUEST['we_cmd'][$no])){
		if(strpos($_REQUEST['we_cmd'][$no], 'WECMDENC_') !== false){
			$_REQUEST['we_cmd'][$no] = base64_decode(urldecode(substr($_REQUEST['we_cmd'][$no], 9)));
		}
		return $_REQUEST['we_cmd'][$no];
	}
	return $default;
}

function getWEZendCache($lifetime = 1800){
	return Zend_Cache::factory('Core', 'File', array('lifetime' => $lifetime, 'automatic_serialization' => true), array('cache_dir' => ZENDCACHE_PATH));
}

function cleanWEZendCache(){
	if(file_exists(ZENDCACHE_PATH . 'clean')){
		$cache = getWEZendCache();
		$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
//remove file
		unlink(ZENDCACHE_PATH . 'clean');
	}
}

function we_log_loginFailed($table, $user){
	$db = $GLOBALS['DB_WE'];
	$db->query('INSERT INTO ' . FAILED_LOGINS_TABLE . ' SET ' . we_database_base::arraySetter(array(
			'UserTable' => $table,
			'Username' => $user,
			'IP' => $_SERVER['REMOTE_ADDR'],
			'Servername' => $_SERVER['SERVER_NAME'],
			'Port' => $_SERVER['SERVER_PORT'],
			'Script' => $_SERVER['SCRIPT_NAME']
	)));
}

/**
 * removes unneded js-open/close tags
 * @param string $js
 * @return string given param without duplicate js-open/close tags
 */
function implodeJS($js){
	list($pre, $post) = explode(';', we_html_element::jsElement(';'));
	return preg_replace('|' . preg_quote($post, '|') . '[\n\t ]*' . preg_quote($pre, '|') . '|', "\n", $js);
}

//FIXME: remove this function and all calls to it
function update_time_limit($newLimit){
	if($newLimit == 0 || intval(ini_get('memory_limit')) < $newLimit){
		@set_time_limit($newLimit);
	}
}

//FIXME: remove this function & all calls to it
function update_mem_limit($newLimit){
	if(intval(ini_get('memory_limit')) < $newLimit){
		@ini_set('memory_limit', $newLimit . 'M');
	}
}
