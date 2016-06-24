<?php
/**
 * webEdition CMS
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect(['ADMINISTRATOR']);
$trans = ['Error type' => 'Type', 'Error message' => 'Text', 'Script name' => 'File', 'Line number' => 'Line', 'Backtrace' => 'Backtrace',
	'Source code around' => 'posData',
	'Request' => 'Request', 'Server' => 'Server', 'Session' => 'Session', 'Global' => 'Global'];

function getInfoTable($infoArr){
	if(!$infoArr){
		return;
	}
	$redTdir = ltrim(TEMPLATES_DIR, '/');
	$realTemplate = realpath(TEMPLATES_PATH);
	$droot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
	$realDroot = realpath($_SERVER['DOCUMENT_ROOT']);
	//recode data - this data might be different than the rest
	foreach($infoArr as $val => &$tmp){
		$extra = '';
		switch($val){
			case 'File':
				//FIXME: check if we can ommit realpath, since logging was changed
				//FIXME2: what about SECURITY_REPL_DOC_ROOT
				if(strpos($tmp, TEMPLATES_PATH) === 0 || strpos($tmp, $realTemplate) === 0 || strpos($tmp, $redTdir) === 0){
					$id = path_to_id(str_replace([TEMPLATES_PATH, $realTemplate, $redTdir, '.php'], ['', '', '', '.tmpl'], $tmp), TEMPLATES_TABLE, $GLOBALS['DB_WE']);
					$extra = $id ? we_html_button::create_button(we_html_button::EDIT, 'javascript:WE().layout.weEditorFrameController.openDocument(WE().consts.tables.TEMPLATES_TABLE, ' . $id . ', WE().consts.contentTypes.TEMPLATE);') : '';
				} elseif(($id = path_to_id(str_replace([$realDroot, $droot], '', $tmp), FILE_TABLE, $GLOBALS['DB_WE']))){
					$extra = $id ? we_html_button::create_button(we_html_button::EDIT, 'javascript:WE().layout.weEditorFrameController.openDocument(WE().consts.tables.FILE_TABLE, ' . $id . ');') : '';
				}
		}


		if(!mb_check_encoding($tmp, $GLOBALS['WE_BACKENDCHARSET'])){
			$tmp = mb_convert_encoding($tmp, $GLOBALS['WE_BACKENDCHARSET'], 'UTF-8,ISO-8859-15,ISO-8859-1');
		}
		try{
			@$tmp = htmlentities($tmp, ENT_COMPAT, $GLOBALS['WE_BACKENDCHARSET']);
		} catch (Exception $e){
			//try another encoding since last conversion failed.
			@$tmp = htmlentities($tmp, ENT_COMPAT, $GLOBALS['WE_BACKENDCHARSET'] === 'UTF-8' ? 'ISO-8859-15' : 'UTF-8');
		}
		$tmp = $extra . $tmp;
	}

	$ret = '<table class="error">
  <colgroup>
  <col style="width:10%"/>
  <col style="width:90%" />
  </colgroup>
  <tr class="first">
  	<td class="left">#' . $infoArr['ID'] . '</td>
    <td class="right">' . $infoArr['Date'] . '</td>
  </tr>';
	foreach($GLOBALS['trans'] as $key => $val){
		if(isset($infoArr[$val])){
			$ret.= '<tr id="' . str_replace(' ', '', $key) . '">
    <td class="left">' . $key . ':</td>
    <td class="right"><pre>' . $infoArr[$val] . '</pre></td>
  </tr>';
		}
	}
	return $ret . '</table>';
}

function getNavButtons($size, $pos, $id){
	if(!$size){
		return;
	}

	$div = max(intval($size / 10), 1);

	$url = WEBEDITION_DIR . basename(__FILE__);
	return '<table style="margin-top: 10px;width:100%;" class="default"><tr><td>' .
		we_html_button::create_button('fa:first,fa-lg fa-fast-backward', $url . '?function=first', true, 0, 0, '', '', ($pos == 1)) .
		we_html_button::getButton("-" . $div, 'btn', "window.location.href='" . $url . '?function=prevX&ID=' . $id . '&step=' . $div . "';", '', ($pos - $div < 1)) .
		we_html_button::create_button(we_html_button::BACK, $url . '?function=prev&ID=' . $id, true, 0, 0, "", "", ($pos == 1))
		.
		'</td><td style="text-align:center">' .
		we_html_button::create_button('export', $url . '?function=export&ID=' . $id, true) .
		we_html_button::create_button(we_html_button::DELETE, $url . '?function=delete&ID=' . $id, true) .
		we_html_button::create_button(we_html_button::DELETE_EQUAL, $url . '?function=deleteEqual&ID=' . $id, true) .
		'</td><td style="text-align:right;">' .
		we_html_button::create_button(we_html_button::NEXT, $url . '?function=next&ID=' . $id, true, 0, 0, "", "", ($pos == $size)) .
		we_html_button::getButton("+" . $div, 'btn2', "window.location.href='" . $url . '?function=nextX&ID=' . $id . '&step=' . $div . "';", '', ($pos + $div > $size)) .
		we_html_button::create_button('fa:last,fa-lg fa-fast-forward', $url . '?function=last', true) .
		'</td></tr><tr><td colspan="3" style="text-align:center;width:120px;" class="defaultfont bold" >' . $pos . "&nbsp;" . g_l('global', '[from]') . ' ' . $size . '</td></table>';
}

/* function formatLine(&$val, $key){
  $val = $key . ': ' . $val;
  } */

function getPosData($bt, $file, $lineNo){
	$ret = '';
	$matches = [];

	if(!$bt || $bt == '-' || !preg_match_all('|#\d+ [^\]]*\[([^:\]]*):(\d+)|', $bt, $matches)){
		$matches = [
			1 => [0 => str_replace('SECURITY_REPL_DOC_ROOT/', '', $file)],
			2 => [0 => $lineNo]
		];
	}

	$max = 8;
	foreach($matches[1] as $i => $file){
		if(!--$max){
			break;
		}
		$lineNo = $matches[2][$i];
		$lines = we_base_file::loadLines((strpos($file, $_SERVER['DOCUMENT_ROOT']) === 0 || strpos($file, realpath(WEBEDITION_PATH)) === 0 ? '' : $_SERVER['DOCUMENT_ROOT'] . '/' ) . $file, max(1, $lineNo - 1), $lineNo + 5);
		if($lines){
			array_walk($lines, function(&$val, $key){
				$val = $key . ': ' . $val;
			});
			$ret .=$file . ":\n" . implode('', $lines) . "\n----------------------------------------------------------\n";
		}
	}
	return $ret;
}

$options = '';
foreach(array_keys($GLOBALS['trans']) as $key){
	$options.='<option value="' . str_replace(' ', '', $key) . '">' . $key . '</option>';
}
$url = WEBEDITION_DIR . basename(__FILE__);
$buttons = g_l('searchtool', '[anzeigen]') . ': <select onchange="document.getElementById(this.value).scrollIntoView();">' . $options . '</select>' .
	we_html_button::formatButtons(we_html_button::create_button(we_html_button::DELETE_ALL, $url . '?deleteAll=1') . we_html_button::create_button(we_html_button::REFRESH, $url) . we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close()"));


$db = $GLOBALS['DB_WE'];
if(we_base_request::_(we_base_request::BOOL, 'deleteAll')){
	$db->query('TRUNCATE TABLE `' . ERROR_LOG_TABLE . '`');
}

$size = f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '`');
$id = we_base_request::_(we_base_request::INT, 'ID', 0);
$step = we_base_request::_(we_base_request::INT, 'step', 0);

switch(we_base_request::_(we_base_request::STRING, 'function', 'last')){
	case 'deleteEqual':
		$db->addTable('del', ['ID' => 'bigint(20) unsigned NOT NULL'], ['PRIMARY KEY (ID)'], 'MEMORY', true);
		$db->query('INSERT INTO del SELECT ID FROM `' . ERROR_LOG_TABLE . '` WHERE (Text,File,Type,Function,Line) IN (SELECT Text,File,Type,Function,Line FROM `' . ERROR_LOG_TABLE . '` WHERE ID=' . $id . ')');
		$db->query('DELETE FROM `' . ERROR_LOG_TABLE . '` WHERE ID IN (SELECT ID FROM del)');
		$db->delTable('del', true);
		$size = f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '`');
	//no break;
	case 'prev':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID<' . $id . ' ORDER BY ID DESC LIMIT 1');
		$pos = f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID<' . $id);
		break;

	default:
	case 'last':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` ORDER BY ID DESC LIMIT 1');
		$pos = $size;
		break;
	case 'first':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` ORDER BY ID ASC LIMIT 1');
		$pos = 1;
		break;
	case 'export':
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename=error' . $id . '.txt');
		$cur = getHash('SELECT ID,Type,Function,File,Line,Text,Backtrace,Date FROM `' . ERROR_LOG_TABLE . '` WHERE ID=' . $id . ' ORDER BY ID ASC LIMIT 1', $db, MYSQL_ASSOC);
		$sep = "\n" . str_repeat('-', 80) . "\n";
		if($cur){
			$cur['Source-Code'] = getPosData($cur['Backtrace'], $cur['File'], $cur['Line']);
		}
		$data = '';
		foreach($cur as $key => $val){
			$data.=$key . ': ' . str_replace($_SERVER['SERVER_NAME'], 'HOST', $val) . $sep;
		}
		$data.='WE-Info:
Version: ' . WE_VERSION . '
SVN: ' . WE_SVNREV . ' ' . WE_VERSION_BRANCH . ' ' . WE_VERSION_SUPP . ' h' . (defined('WE_VERSION_HOTFIX_NR') ? WE_VERSION_HOTFIX_NR : 0) . $sep .
			'System:
PHP: ' . PHP_VERSION . '
max_execution_time: ' . ini_get('max_execution_time') . '
memory_limit: ' . ini_get('memory_limit') . '
short_open_tag: ' . ini_get('short_open_tag') . '
post_max_size: ' . ini_get('post_max_size') . '
max_input_vars: ' . ini_get('max_input_vars') . '
session.auto_start: ' . ini_get('session.auto_start') . $sep .
			'Mysql:
' . $GLOBALS['DB_WE']->getInfo(false);

		echo str_replace($_SERVER['DOCUMENT_ROOT'], 'DOCUMENT_ROOT', $data);
		//`Request` text NOT NULL,
		exit();
	case 'pos':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID=' . $id . ' ORDER BY ID ASC LIMIT 1');
		$pos = $size - f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID>=' . $id) + 1;
		break;
	case 'delete':
		$db->query('DELETE FROM `' . ERROR_LOG_TABLE . '` WHERE ID=' . $id);
		$size = f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '`');
	//no break;
	case 'next':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID>' . $id . ' ORDER BY ID ASC LIMIT 1');
		$pos = $size - f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID>' . $id) + 1;
		break;
	case 'nextX':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID>=' . $id . ' ORDER BY ID ASC LIMIT ' . $step . ',1');
		$pos = $size - f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID>' . $cur['ID']);
		break;
	case 'prevX':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID<=' . $id . ' ORDER BY ID DESC LIMIT ' . $step . ',1');
		$pos = f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID<=' . $cur['ID']);
		break;
}

if($size && !$cur){//nothing found, go to last element
	$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` ORDER BY ID DESC LIMIT 1');
	$pos = $size;
}

if($size && $cur){
	$cur['posData'] = getPosData($cur['Backtrace'], $cur['File'], $cur['Line']);
}

$data = getInfoTable($cur);

$parts = [
	[
		'html' => ($size && $data ? $data : g_l('global', '[no_entries]')),
		'space' => we_html_multiIconBox::SPACE_SMALL,
	]
];

echo we_html_tools::getHtmlTop(g_l('javaMenu_global', '[showerrorlog]'), '', '', we_html_element::jsElement('function closeOnEscape() {
		return true;
	}
') .
	STYLESHEET);
?>
<body class="weDialogBody" style="overflow:hidden;" onload="self.focus();">
	<div id="info" style="display: block;">
		<?php
		echo we_html_multiIconBox::getJS() .
		we_html_element::htmlDiv(['style' => 'position:absolute; top:0px; left:30px;right:30px;height:60px;'], $size && $data ? getNavButtons($size, $pos, isset($cur['ID']) ? $cur['ID'] : 0) : '') .
		we_html_element::htmlDiv(['style' => 'position:absolute;top:60px;bottom:0px;left:0px;right:0px;'], we_html_multiIconBox::getHTML('', $parts, 30, $buttons));
		?>
	</div>
</body>
</html>