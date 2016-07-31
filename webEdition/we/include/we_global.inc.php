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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function correctUml($in){//FIXME: need charset!!
	//FIXME: can we use this (as in objectfile): preg_replace(array('~&szlig;~','~&(.)(uml|grave|acute|circ|tilde|ring|cedil|slash|caron);|&(..)(lig);|&#.*;~', '~[^0-9a-zA-Z/._-]~'), array('ss','${1}${3}', ''), htmlentities($text, ENT_COMPAT, $this->Charset));
	return strtr($in, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss']);
}

function makeIDsFromPathCVS($paths, $table = FILE_TABLE){
	if(!$paths || !$table){
		return '';
	}
	$foo = is_array($paths) ? $paths : explode(',', $paths);
	$db = new DB_WE();
//cleanup paths
	foreach($foo as &$path){
		$path = '"' . $db->escape('/' . ltrim(trim($path), '/')) . '"';
	}
	$db->query('SELECT ID FROM ' . $db->escape($table) . ' WHERE PATH IN (' . implode(',', $foo) . ')');
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

function getHTTP($server, $url, &$status, $port = '', $username = '', $password = ''){
//FIXME: add code for proxy, see weXMLBrowser
	if(strpos($server, '://') === FALSE){
		$server = 'http' . ($port == 443 ? 's' : '') . '://' . (($username && $password) ? "$username:$password@" : '') . $server . ( $port !== '' ? ':' . $port : '');
	}
	switch(getHttpOption()){
		case 'fopen':
			/* not yet tested
			  if(defined('WE_PROXYHOST')){
			  $proxyhost = defined('WE_PROXYHOST') ? WE_PROXYHOST : "";
			  $proxyport = (defined('WE_PROXYPORT') && WE_PROXYPORT) ? WE_PROXYPORT : "80";
			  $proxy_user = defined('WE_PROXYUSER') ? WE_PROXYUSER : "";
			  $proxy_pass = defined('WE_PROXYPASSWORD') ? WE_PROXYPASSWORD : "";

			  return getHttpThroughProxy(($server . $url), $proxyhost, $proxyport, $proxy_user, $proxy_pass);
			  }
			 */
			$fh = @fopen($server . $url, 'rb');
			$matches = [];
			preg_match('#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches);
			$status = $matches[1];

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
			return ($fh ? $page : 'Server Error: Failed opening URL: ' . $server . $url);
		case 'curl':
			$response = we_base_util::getCurlHttp($server, $url, []);
			$status = $response['status']? : 200;
			return ($response['status'] ? $response['error'] : $response['data']);
		default:
			return 'Server error: Unable to open URL (php configuration directive allow_url_fopen=Off)';
	}
}

function getHttpThroughProxy($url, $proxyhost, $proxyport, $proxy_user, $proxy_pass){
	$errno = $errstr = '';
	$file = fsockopen($proxyhost, $proxyport, $errno, $errstr, 30);

	if(!$file){
		return '';
	}
	$ret = '';
	$realm = base64_encode($proxy_user . ':' . $proxy_pass);

	// send headers
	fputs($file, "GET $url HTTP/1.0\r\n");
	fputs($file, "Proxy-Connection: Keep-Alive\r\n");
	fputs($file, "User-Agent: PHP " . PHP_VERSION . "\r\n");
	fputs($file, "Pragma: no-cache\r\n");
	if($proxy_user != ''){
		fputs($file, "Proxy-authorization: Basic $realm\r\n");
	}
	fputs($file, "\r\n");

	// write comoplete file and cut http header before returning
	while(!feof($file)){
		$data = fread($file, 8192);
		$ret .= $data;
	}
	fclose($file);

	return substr($ret, 0, 5) === 'HTTP/' ? substr($ret, strpos($ret, "\r\n\r\n") + 4) : $ret;
}

/**
 * Strips off the table prefix - this function is save of calling multiple times
 * @param string $table
 * @return string stripped tablename
 */
function stripTblPrefix($table){
	return TBL_PREFIX != '' && (strpos($table, TBL_PREFIX) === 0) ? substr($table, strlen(TBL_PREFIX)) : $table;
}

function addTblPrefix($table){
	return TBL_PREFIX . $table;
}

//FIXME: remove this & decide where to use old version of htmlspecialchars
function oldHtmlspecialchars($string, $flags = -1, $encoding = 'ISO-8859-1', $double_encode = true){
	$flags = ($flags === -1 ? ENT_COMPAT | (defined('ENT_HTML401') ? ENT_HTML401 : 0) : $flags);
	return htmlspecialchars($string, $flags, $encoding, $double_encode);
}

/**
 * filter all bad Xss attacks from var. Arrays can be used.
 * @param mixed $var
 * @deprecated since version 6.3.9
 * @return mixed
 */
function filterXss($var, $type = 'string'){
	//t_e('deprecated', __FUNCTION__);
	if(!is_array($var)){
		return ($type === 'string' ? oldHtmlspecialchars(strip_tags($var)) : intval($var));
	}
	$ret = [];
	foreach($var as $key => $val){
		$ret[oldHtmlspecialchars(strip_tags($key))] = filterXss($val, $type);
	}
	return $ret;
}

/**
 * @deprecated since version 6.3.9
 *
 */
function we_make_attribs($attribs, $doNotUse = ''){
	t_e('deprecated', __FUNCTION__);
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

function we_getParentIDs($table, $id, &$ids, we_database_base $db = null){
	$db = $db ? : new DB_WE();
	while(($pid = f('SELECT ParentID FROM ' . $db->escape($table) . ' WHERE ID=' . intval($id), '', $db)) > 0){
		$id = $pid; // #5836
		$ids[] = $id;
	}
}

/**
 * @deprecated since version 6.4.3
 * @param type $csv
 * @return type
 */
function makeArrayFromCSV($csv){
	$csv = trim($csv, ',');

	if($csv === ''){
		return [];
	}

	return explode(',', $csv);
}

function in_parentID($id, $pid, $table = FILE_TABLE, we_database_base $db = null){
	if(intval($pid) != 0 && intval($id) == 0){
		return false;
	}
	if(intval($pid) == 0 || $id == $pid || ($id == '' && $id != '0')){
		return true;
	}
	$db = $db ? : new DB_WE();

	$found = [];
	$p = intval($id);
	do{
		if($p == $pid){
			return true;
		}
		if(in_array($p, $found)){
			return false;
		}
		$found[] = $p;
	} while(($p = f('SELECT ParentID FROM ' . $db->escape($table) . ' WHERE ID=' . intval($p), '', $db)));
	return false;
}

function in_workspace($IDs, $wsIDs, $table = FILE_TABLE, we_database_base $db = null, $norootcheck = false){
	return we_users_util::in_workspace($IDs, $wsIDs, $table, $db, $norootcheck);
}

function path_to_id($path, $table = FILE_TABLE, we_database_base $db = null, $asArray = false){
	if(empty($path)){
		return $asArray ? [] : '';
	}
	if(!is_array($path)){
		$path = [$path];
	}
	$db = ($db ? : $GLOBALS['DB_WE']);
	$db->query('SELECT ID FROM ' . $db->escape($table) . ' WHERE Path IN ("' . implode('","', array_map('escape_sql_query', array_map('trim', $path))) . '")');
	$ret = (in_array('/', $path) ? [0] : []) + $db->getAll(true);
	return $asArray ? $ret : implode(',', $ret);
}

function id_to_path($IDs, $table = FILE_TABLE, we_database_base $db = null, $asArray = false, $endslash = false, $isPublished = false){
	if(!is_array($IDs) && !$IDs){
		return ($asArray ? [0 => '/'] : '/');
	}

	$db = $db ? : $GLOBALS['DB_WE'];

	if(!is_array($IDs)){
		$IDs = makeArrayFromCSV($IDs);
	}
	switch($table){
		case defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE':
			$select = 'Username';
			break;
		default:
			$select = ($endslash ?
					'IF(IsFolder=1,CONCAT(Path,"/"),Path)' :
					'Path');
	}

	$foo = (in_array(0, $IDs) ? [0 => '/'] : []) +
		($IDs ?
			$db->getAllFirstq('SELECT ID,' . $select . ' FROM ' . $db->escape($table) . ' WHERE ID IN(' . implode(',', array_map('intval', $IDs)) . ')' . ($isPublished ? ' AND Published>0' : ''), false) :
			[]
		);

	return $asArray ? $foo : implode(',', $foo);
}

function getPathsFromTable($table, we_database_base $db, $type = we_base_constants::FILE_ONLY, $wsIDs = '', $order = 'Path', $limitCSV = '', $first = ''){
	$limitCSV = trim($limitCSV, ',');
	$query = [];
	if($wsIDs){
		$idArr = makeArrayFromCSV($wsIDs);
		$wsPaths = makeArrayFromCSV(id_to_path($wsIDs, $table, $db));
		$qfoo = [];
		for($i = 0; $i < count($wsPaths); $i++){
			if((!$limitCSV) || we_users_util::in_workspace($idArr[$i], $limitCSV, FILE_TABLE, $db)){
				$qfoo[] = ' Path LIKE "' . $db->escape($wsPaths[$i]) . '%" ';
			}
		}
		if(!count($qfoo)){
			return [];
		}
		$query[] = ' (' . implode(' OR ', $qfoo) . ' )';
	}
	switch($type){
		case we_base_constants::FILE_ONLY :
			$query[] = ' IsFolder=0 ';
			break;
		case we_base_constants::FOLDER_ONLY :
			$query[] = ' IsFolder=1 ';
			break;
	}
	$out = $first ? [0 => $first] : [];

	$db->query('SELECT ID,Path FROM ' . $db->escape($table) . (count($query) ? ' WHERE ' . implode(' AND ', $query) : '') . ' ORDER BY ' . $order);
	while($db->next_record()){
		$out[$db->f('ID')] = $db->f('Path');
	}
	return $out;
}

function pushChildsFromArr(&$arr, $table = FILE_TABLE, $isFolder = ''){
	$tmpArr = $arr;
	$tmpArr2 = [];
	foreach($arr as $id){
		pushChilds($tmpArr, $id, $table, $isFolder);
	}
	foreach(array_unique($tmpArr) as $id){
		$tmpArr2[] = $id;
	}
	return $tmpArr2;
}

function pushChilds(&$arr, $id, $table = FILE_TABLE, $isFolder = '', we_database_base $db = null){
	$db = $db? : new DB_WE();
	$arr[] = $id;
	$db->query('SELECT ID FROM ' . $db->escape($table) . ' WHERE ParentID=' . intval($id) . (($isFolder != '' || $isFolder === 0) ? (' AND IsFolder=' . intval($isFolder)) : ''));
	$all = $db->getAll(true);
	foreach($all as $id){
		pushChilds($arr, $id, $table, $isFolder, $db);
	}
}

function get_ws($table = FILE_TABLE, $asArray = false){
	if(isset($_SESSION) && isset($_SESSION['perms'])){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return $asArray ? [] : '';
		}
		if($_SESSION['user']['workSpace'] && !empty($_SESSION['user']['workSpace'][$table])){
			return $asArray ? $_SESSION['user']['workSpace'][$table] : implode(',', $_SESSION['user']['workSpace'][$table]);
		}
	}
	return $asArray ? [] : '';
}

function we_readParents($id, &$parentlist, $tab, $match = 'ContentType', $matchvalue = 'folder', we_database_base $db = null){
	$db = $db ? : new DB_WE();
	if(($pid = f('SELECT ParentID FROM ' . $db->escape($tab) . ' WHERE ID=' . intval($id), '', $db)) !== ''){
		if($pid == 0){
			$parentlist[] = $pid;
		} elseif(f('SELECT 1 FROM ' . $db->escape($tab) . ' WHERE ID=' . intval($pid) . ' AND ' . $db->escape($match) . ' = "' . $db->escape($matchvalue) . '" LIMIT 1', '', $db)){
			$parentlist[] = $pid;
			we_readParents($pid, $parentlist, $tab, $match, $matchvalue, $db);
		}
	}
}

function we_readChilds($pid, &$childlist, $tab, $folderOnly = true, $where = '', $match = 'ContentType', $matchvalue = 'folder', we_database_base $db = null){
	if(empty($pid)){
		return;
	}
	$db = $db ? : new DB_WE();
	$db->query('SELECT ID,' . $db->escape($match) . ' FROM ' . $db->escape($tab) . ' WHERE ' . ($folderOnly ? ' IsFolder=1 AND ' : '') . 'ParentID IN (' . (is_array($pid) ? implode(',', $pid) : intval($pid)) . ') ' . $where);
	$todo = [];
	while($db->next_record()){
		if($db->f($match) == $matchvalue){
			$todo[] = $db->f('ID');
		}
		$childlist[] = $db->f('ID');
	}
	if($todo){
		we_readChilds($todo, $childlist, $tab, $folderOnly, $where, $match, $matchvalue, $db);
	}
}

function getWsQueryForSelector($tab, $includingFolders = true){
	if(permissionhandler::hasPerm('ADMINISTRATOR')){
		return '';
	}

	if(!($ws = get_ws($tab, true))){
		return (($tab == NAVIGATION_TABLE || (defined('NEWSLETTER_TABLE') && $tab == NEWSLETTER_TABLE)) ? '' : ' OR RestrictOwners=0 ');
	}
	$paths = id_to_path($ws, $tab, null, true);
	$wsQuery = [];
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

function get_def_ws($table = FILE_TABLE){
	if(!get_ws($table)){ // WORKARROUND
		return '';
	}
	if(permissionhandler::hasPerm('ADMINISTRATOR')){
		return '';
	}

	$foo = f('SELECT workSpaceDef FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION['user']['ID']), '', new DB_WE());
	$ws = implode(',', explode(',', $foo));

	if(!$ws){
		list($wsA) = get_ws($table, true);
		return ($wsA ? $wsA[0] : '');
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
	$data = [];
	$values = func_get_args();
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
			//$inc = true;
			$values[0] = 'Deprecated';
			$type = E_USER_NOTICE; //E_USER_DEPRECATED - seems not to work anymore
			break;
		case 'warning':
			$inc = true;
		default:
			$type = E_USER_WARNING;
	}
	if($inc){
		array_shift($values);
	}
	foreach($values as $value){
		if(is_array($value) || is_object($value)){
			$data[] = @print_r($value, true);
		} else {
			$data[] = (is_bool($value) ? var_export($value, true) : $value);
		}
	}

	if($data){
		trigger_error(implode("\n---------------------------------------------------\n", $data), $type);
	}
}

function removeHTML($val){
	$val = preg_replace(['%<br ?/?>%i', '/<[^><]+>/'], ['###BR###', ''], str_replace(['<?', '?>'], ['###?###', '###/?###'], $val));
	return str_replace(['###BR###', '###?###', '###/?###'], ['<br/>', '<?', '?>'], $val);
}

/**
 * @deprecated since version 6.3.0
 * @param type $val
 * @return type
 */
function removePHP($val){
	t_e('deprecated', 'use of deprecated function');
	return we_base_util::rmPhp($val);
}

function we_mail($recipient, $subject, $txt, $from = '', $replyTo = ''){
	if(runAtWin() && $txt){
		$txt = str_replace("\n", "\r\n", $txt);
	}

	$phpmail = new we_mail_mail($recipient, $subject, $from, $replyTo);
	$phpmail->setCharSet($GLOBALS['WE_BACKENDCHARSET']);
	$txtMail = strip_tags($txt);
	if($txt != $txtMail){
		$phpmail->setTextPartOutOfHTML($txt);
		$phpmail->addHTMLPart($txt);
	} else {
		$phpmail->addTextPart(trim($txt));
	}
	$phpmail->buildMessage();
	return $phpmail->Send();
}

function runAtWin(){
	return stripos(PHP_OS, 'win') !== false && (stripos(PHP_OS, 'darwin') === false);
}

function weMemDebug(){
	echo 'Mem usage ' . round(((memory_get_usage() / 1024) / 1024), 3) . " MiB\n" .
	(microtime(true) - floatval($_SERVER['REQUEST_TIME_FLOAT'])) . ' ';
}

function weGetCookieVariable($name){
	$c = isset($_COOKIE['we' . session_id()]) ? $_COOKIE['we' . session_id()] : '';
	$vals = [];
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
		return we_base_ContentTypes::FOLDER;
	}
	$ext = strtolower(preg_replace('#^.*(\..+)$#', '${1}', $dat));
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
	$min = min($post_max_size, $upload_max_filesize, ($mysql ? $db->getMaxAllowedPacket() : PHP_INT_MAX));

	return (intval(FILE_UPLOAD_MAX_UPLOAD_SIZE) == 0 ?
			$min :
			min(FILE_UPLOAD_MAX_UPLOAD_SIZE * 1024 * 1024, $min));
}

function we_convertIniSizes($in){
	$regs = [];
	if(preg_match('#^([0-9]+)M$#i', $in, $regs)){
		return 1024 * 1024 * intval($regs[1]);
	}
	if(preg_match('#^([0-9]+)K$#i', $in, $regs)){
		return 1024 * intval($regs[1]);
	}
	return intval($in);
}

function we_getDocumentByID($id, $includepath = '', we_database_base $db = null, &$charset = ''){
	$db = $db ? : new DB_WE();
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
	$charset = $GLOBALS['we_doc']->getElement('Charset')? : DEFAULT_CHARSET;

	if(isset($backupdoc)){
		$GLOBALS['we_doc'] = $backupdoc;
	}
	return $content;
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

/**
 * Validates email address
 * @param $email
 * @return bool
 */
function we_check_email($email){
	if(($pos = strpos($email, '<'))){//check format is "xxx xx" <test@test.de> because php validates only the pure address
		++$pos;
		$email = substr($email, $pos, strrpos($email, '>') - $pos);
	}
	list($name, $host) = explode('@', $email);
	$host = (function_exists('idn_to_ascii') ? idn_to_ascii($host) : $host);
	return (filter_var(trim($name . '@' . $host), FILTER_VALIDATE_EMAIL) !== false);
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
function getHtmlTag($element, $attribs = [], $content = '', $forceEndTag = false, $onlyStartTag = false){
	require_once (WE_INCLUDES_PATH . 'we_tag.inc.php');

//	take values given from the tag - later from preferences.
	$xhtml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, we_base_request::BOOL);

//	remove x(ht)ml-attributs
	$removeAttribs = ['xml', 'xmltype', 'to', 'nameto', '_name_orig', null];

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

	$attribs = removeAttribs($attribs, $removeAttribs);

	$tag = '<' . $element;

	foreach($attribs as $k => $v){
		$tag .= ' ' . ($k === 'link_attribute' ? // Bug #3741
				$v :
				str_replace('pass_', '', $k) . '="' . $v . '"');
	}
	return $tag . ($content || $forceEndTag ? //	use endtag
			'>' . $content . '</' . $element . '>' :
//	xml style or not
			( ($xhtml && !$onlyStartTag) ? ' />' : '>'));
}

/**
 * @return array
 * @param array $attribs
 * @param array $remove
 * @desc removes all entries of $attribs, where the key from attribs is in values of $remove
 */
function removeAttribs($attribs, array $remove = []){
	foreach($remove as $r){
		if(isset($attribs[$r])){
			unset($attribs[$r]);
		}
	}
	return $attribs;
}

function getWeFrontendLanguagesForBackend(){
	$la = [];
	if(!isset($GLOBALS['weFrontendLanguages'])){
		return [];
	}
	$targetLang = array_search($GLOBALS['WE_LANGUAGE'], getWELangs());
	foreach($GLOBALS['weFrontendLanguages'] as $Locale){
		$temp = explode('_', $Locale);
		$la[$Locale] = (count($temp) == 1 ?
				CheckAndConvertISObackend(we_base_country::getTranslation($temp[0], we_base_country::LANGUAGE, $targetLang) . ' ' . $Locale) :
				CheckAndConvertISObackend(we_base_country::getTranslation($temp[0], we_base_country::LANGUAGE, $targetLang) . ' (' . we_base_country::getTranslation($temp[1], we_base_country::TERRITORY, $targetLang) . ') ' . $Locale));
	}
	return $la;
}

function we_isHttps(){
	return isset($_SERVER['HTTPS']) && (strtoupper($_SERVER['HTTPS']) === 'ON' || $_SERVER['HTTPS'] == 1);
}

function getVarArray($arr, $string){
	if(!isset($arr)){
		return false;
	}
	$arr_matches = [];
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
	$to = (!empty($GLOBALS['CHARSET']) ? $GLOBALS['CHARSET'] : DEFAULT_CHARSET);
	return ($to === 'UTF-8' ? $utf8data : mb_convert_encoding($utf8data, $to, 'UTF-8'));
}

function CheckAndConvertISObackend($utf8data){
	$to = (isset($GLOBALS['we']['PageCharset']) ? $GLOBALS['we']['PageCharset'] : $GLOBALS['WE_BACKENDCHARSET']);
	return ($to === 'UTF-8' ? $utf8data : mb_convert_encoding($utf8data, $to, 'UTF-8'));
}

function register_g_l_dir($dir){
	if(empty($_SESSION['weS']['gl'])){
		$_SESSION['weS']['gl'] = [];
	}
	if(strpos($dir, $_SERVER['DOCUMENT_ROOT']) !== 0){
		$dir = $_SERVER['DOCUMENT_ROOT'] . $dir;
	}
	$_SESSION['weS']['gl'][] = $dir;
}

/* * internal function - do not call */

function g_l_encodeArray($tmp){
	$charset = (isset($_SESSION['user']) && isset($_SESSION['user']['isWeSession']) ? $GLOBALS['WE_BACKENDCHARSET'] : (isset($GLOBALS['CHARSET']) ? $GLOBALS['CHARSET'] : $GLOBALS['WE_BACKENDCHARSET']));
	return (is_array($tmp) ?
			array_map(__METHOD__, $tmp) :
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
			(!empty($GLOBALS['CHARSET']) ? $GLOBALS['CHARSET'] : DEFAULT_CHARSET) );
//	return $name.$specific;
//cache last accessed lang var
	static $cache = [];
//echo $name.$specific;
	if(isset($cache['l_' . $name])){
		$tmp = getVarArray($cache['l_' . $name], $specific);
		if(!($tmp === false)){
			return ($charset != 'UTF-8' ?
					(is_array($tmp) ?
						array_map('g_l_encodeArray', $tmp) :
						mb_convert_encoding($tmp, $charset, 'UTF-8')
					) :
					$tmp);
		}
	}
	$dirs = (empty($_SESSION['weS']['gl']) ? [] : $_SESSION['weS']['gl']);
	$dirs[] = WE_INCLUDES_PATH . 'we_language/';
	$found = false;
	foreach($dirs as $dir){
		$file = $dir . $GLOBALS['WE_LANGUAGE'] . '/' . str_replace('_', '/', $name) . '.inc.php';
		if(file_exists($file)){
			$found = true;
			break;
		}
	}
	if(!$found){
		if(!$omitErrors){
			t_e('Language file "' . $file . '" not found with entry ' . $specific);
			return '?';
		}
		return false;
	}

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
	}
	if(!$omitErrors){
		t_e('notice', 'Requested lang entry l_' . $name . $specific . ' not found in ' . $file . ' !');
		return '??';
	}
	return false;
}

function we_templateInit(){
	if(isset($GLOBALS['WE_TEMPLATE_INIT'])){
		++$GLOBALS['WE_TEMPLATE_INIT'];
	} else {
		$GLOBALS['WE_TEMPLATE_INIT'] = 1;

		// Activate the autoloader & webEdition error handler
		require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_autoload.inc.php');
		require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tag.inc.php');

		if(!isset($GLOBALS['DB_WE'])){
			$GLOBALS['DB_WE'] = new DB_WE();
		}
//check for Trigger
		if(empty($GLOBALS['we']['Scheduler_active']) && we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && (!$GLOBALS['we_doc']->InWebEdition) &&
			(SCHEDULER_TRIGGER == SCHEDULER_TRIGGER_PREDOC) &&
			(empty($GLOBALS['we']['backVars'])) //on first call this variable is unset, so we're not inside an include
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
			$GLOBALS['WE_MAIN_DOC'] = isset($GLOBALS['we_obj']) ? $GLOBALS['we_obj'] : clone($GLOBALS['we_doc']);
		}
		if(!isset($GLOBALS['WE_MAIN_DOC_REF'])){
			if(isset($GLOBALS['we_obj'])){
				$GLOBALS['WE_MAIN_DOC_REF'] = &$GLOBALS['we_obj'];
			} else {
				$GLOBALS['WE_MAIN_DOC_REF'] = &$GLOBALS['we_doc'];
			}
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
		if((!defined('WE_CONTENT_TYPE_SET'))){
			define('WE_CONTENT_TYPE_SET', 1);
			we_html_tools::headerCtCharset('text/html', $GLOBALS['CHARSET'], true);
		}
	}
}

function we_templateHead($fullHeader = false){
	if(!$GLOBALS['WE_MAIN_DOC']->InWebEdition ||
		((!isset($GLOBALS['we_editmode']) || (!$GLOBALS['we_editmode']) && isset($_SESSION['weS']) && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE))){
		return;
	}
	if($fullHeader && isset($GLOBALS['WE_HTML_HEAD_BODY'])){
		echo we_templatePreContent(); //to increment we_templatePreContent-var
		return;
	}
	echo ($fullHeader ? we_html_element::htmlDocType() . '<html><head><title>WE</title>' . we_html_tools::htmlMetaCtCharset($GLOBALS['CHARSET']) : '') .
	we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();parent.openedWithWE=true;') .
	STYLESHEET_MINIMAL .
	weSuggest::getYuiFiles();
	require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
	if($fullHeader){
		echo '</head><body onload="doScrollTo();" onunload="doUnload()">';
		we_templatePreContent();
		$GLOBALS['WE_HTML_HEAD_BODY'] = true;
	}
}

function we_templatePreContent($force = false){//force is used by templates with a full html/body.
	if(!empty($GLOBALS['we_editmode'])){
		if($force || (!isset($GLOBALS['WE_HTML_HEAD_BODY']) && !isset($GLOBALS['we_templatePreContent']))){
			echo '<form name="we_form" action="" method="post" onsubmit="return false;">' .
			we_class::hiddenTrans();
		}
		$GLOBALS['we_templatePreContent'] = (isset($GLOBALS['we_templatePreContent']) ? $GLOBALS['we_templatePreContent'] + 1 : $GLOBALS['WE_TEMPLATE_INIT']);
	}
}

function we_templatePostContent($force = false, $fullPoster = false){//force on </body tag
	if(!empty($GLOBALS['we_editmode']) && ($force || ( --$GLOBALS['we_templatePreContent']) == 0)){
		if($force){//never do this again
			$GLOBALS['we_templatePreContent'] = -10000;
		}
		$yuiSuggest = &weSuggest::getInstance();
		//FIXME: check this new field to determine if all data has been transmitted
		echo $yuiSuggest->getYuiJs() .
		we_html_element::htmlHidden("we_complete_request", 1) .
		'</form>' .
		($fullPoster ? '</body></html>' : '');
	}
}

function we_templatePost(){
	if(--$GLOBALS['WE_TEMPLATE_INIT'] == 0 && !isWE()){
		if(!empty($_SESSION['webuser']) && isset($_SESSION['webuser']['loginfailed'])){
			unset($_SESSION['webuser']['loginfailed']);
		}
		if(defined('DEBUG_MEM')){
			weMemDebug();
			p_r(get_included_files());
		}

		if(ob_get_level() && count(array_diff(($handler = ob_list_handlers()), ['zlib output compression'])) && (end($handler) == 'default output handler')){//if still document active, we have to do url replacements
			$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE']);
// --> Glossary Replacement
			$useGlossary = ((defined('GLOSSARY_TABLE') && (!isset($GLOBALS['WE_MAIN_DOC']) || $GLOBALS['WE_MAIN_ID'] == $GLOBALS['we_doc']->ID)) && (isset($GLOBALS['we_doc']->InGlossar) && $GLOBALS['we_doc']->InGlossar == 0) && we_glossary_replace::useAutomatic());
			$content = ob_get_clean();
			if($useGlossary){
				$content = we_glossary_replace::replace($content, $GLOBALS['we_doc']->Language);
			}
			if($urlReplace){
				$content = preg_replace($urlReplace, array_keys($urlReplace), $content);
			}
			if(isset($GLOBALS['we']['Scheduler_active'])){
				//restart, since we need the content
				ob_start();
			}
			echo $content;
		}
		//check for Trigger
		if(!isset($GLOBALS['we']['Scheduler_active']) && we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && (!$GLOBALS['WE_MAIN_DOC']->InWebEdition) &&
			(SCHEDULER_TRIGGER == SCHEDULER_TRIGGER_POSTDOC) &&
			(empty($GLOBALS['we']['backVars']))//not inside an included Doc
		){ //is set to Post or not set (new default)
			session_write_close();
			flush();
			if(function_exists('fastcgi_finish_request')){
				fastcgi_finish_request();
			}
			ignore_user_abort(true);
			we_schedpro::trigger_schedule();
		}
	}
}

function show_SeoLinks(){
	return (
		!(SEOINSIDE_HIDEINWEBEDITION && $GLOBALS['WE_MAIN_DOC']->InWebEdition) &&
		!(SEOINSIDE_HIDEINEDITMODE && (!empty($GLOBALS['we_editmode']) || (!empty($GLOBALS['WE_MAIN_EDITMODE']))))
		);
}

function seoIndexHide($basename){
	return show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($basename, array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)));
}

function we_TemplateExit($param = 0){
	if(!empty($GLOBALS['FROM_WE_SHOW_DOC'])){
		exit($param);
	}
//we are inside we, we don't terminate here
	if($param){
		echo $param;
	}
//FIXME: use g_l
	t_e('template forces document to exit, see Backtrace for template name. Message of statement', $param);
}

/**
 *
 * @deprecated since version 6.3.0
 */
function update_time_limit($newLimit){
	if($newLimit == 0 || intval(ini_get('max_execution_time')) < $newLimit){
		@set_time_limit($newLimit);
	}
}

//FIXME: remove this function & all calls to it
/**
 *
 * @deprecated since version 6.3.0
 */
function update_mem_limit($newLimit){
	if(intval(ini_get('memory_limit')) < $newLimit){
		@ini_set('memory_limit', $newLimit . 'M');
	}
}

/**
 *
 * @return bool true if inside WE
 */
function isWE(){
	return !empty($_SESSION['user']['isWeSession']);
}

function getWELangs(){
	return [
		'de' => 'Deutsch',
		'en' => 'English',
		'nl' => 'Dutch',
		'fi' => 'Finnish',
		'ru' => 'Russian',
		'es' => 'Spanish',
		'pl' => 'Polish',
		'fr' => 'French'
	];
}

function getWECountries(){
	return [
		'DE' => 'de',
		'GB' => 'en',
		'NL' => 'nl',
		'FI' => 'fi',
		'RU' => 'ru',
		'ES' => 'es',
		'PL' => 'pl',
		'FR' => 'fr'
	];
}

function getMysqlVer($nodots = true){
	return we_database_base::getMysqlVer($nodots);
}

function we_unserialize($string, $default = [], $quiet = false){
	//already unserialized
	if(is_array($string) || is_object($string)){
		return $string;
	}
	//compressed?
	if($string && $string[0] === 'x'){
		$try = @gzuncompress($string);
		$string = $try? : $string;
	}
	//no content, return default
	if($string === '' || $string === false){
		return $default;
	}
	//std-serialized data by php
	if(preg_match('/^[asO]:\d+:|^b:[01];/', $string)){
		$ret = @unserialize($string);
		//unserialize failed, we try to eliminate \r which seems to be a cause for this
		if(!$ret && strlen($string) > 6){
			$ret = @unserialize(str_replace("\r", '', $string));
		}
		return ($ret === false ? $default : $ret);
	}
	//json data
	if(preg_match('|^[{\[].*[}\]]$|sm', $string)){
		//maybe we should just do it & check if it failed
		if(mb_check_encoding($string, 'UTF-8')){
			return json_decode($string, true);
		}
		//non UTF-8 json decode
		static $json = null;
		$json = $json ? : new Services_JSON(16/* SERVICES_JSON_LOOSE_TYPE */ | Services_JSON::SERVICES_JSON_USE_NO_CHARSET_CONVERSION);
		return (array) $json->decode(str_replace("\n", '\n', $string));
	}
	//data is really not serialized!
	if(preg_match('|^\d+(,\d+)*$|', $string)){
		return explode(',', $string);
	}
	if(!$quiet){
		t_e('unable to decode', $string);
	}
	return $default;
}

/**
 * serializes an array
 * @param array $array array to operate on
 * @param string $target can be of two types "serialize" or "json" - NO default jet (!)
 * @param bool $numeric forces data to be treated as numeric (not assotiative) array - no position data will be used
 * @param bool $ksort sort the array by key (useful when numeric is used)
 * @return string serialized data
 */
function we_serialize($array, $target = SERIALIZE_PHP, $numeric = false, $compression = 0, $ksort = false){
	if(!$array){
		return '';
	}
	if($ksort){
		ksort($array, SORT_NUMERIC);
	}
	$array = ($numeric ? array_values($array) : $array);

	switch($target){
		case SERIALIZE_JSON:
			if(!is_object($array)){
				//we don't encode objects as json!
				$ret = json_encode($array, JSON_UNESCAPED_UNICODE);
				if($ret){
					break;
				}
				static $json = null;
				$json = $json? : new Services_JSON(Services_JSON::SERVICES_JSON_USE_NO_CHARSET_CONVERSION);
				$ret = $json->encode($array, false);
				if($ret){
					break;
				}
				t_e('json encode failed', $array);
			} else {
				t_e('tried to encode object as json', $array);
			}
		default:
		case SERIALIZE_PHP:
			$ret = serialize($array);
			break;
	}
	return $compression ? gzcompress($ret, $compression) : $ret;
}

function updateAvailable(){
	$versionInfo = json_decode((we_base_file::load(WE_CACHE_PATH . 'newwe_version.json')? : ''), true);
	if($versionInfo && (version_compare($versionInfo['dotted'], WE_VERSION) > 0 /* ||
		  //in branched mode, we compare svn revisions
		  ( WE_VERSION_BRANCH != "" && intval(WE_SVNREV) < intval($versionInfo['svnrevision'])
		  ) */
		)){
		return $versionInfo;
	}
	return false;
}
