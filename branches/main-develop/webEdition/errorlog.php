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

we_html_tools::protect(array('ADMINISTRATOR'));
$trans = array('Error type' => 'Type', 'Error message' => 'Text', 'Script name' => 'File', 'Line number' => 'Line', 'Backtrace' => 'Backtrace',
	'Source code around' => 'posData',
	'Request' => 'Request', 'Server' => 'Server', 'Session' => 'Session', 'Global' => 'Global');

function getInfoTable($infoArr){
	if(!$infoArr){
		return;
	}
	//recode data - this data might be different than the rest
	foreach($infoArr as &$tmp){
		if(!mb_check_encoding($tmp, $GLOBALS['WE_BACKENDCHARSET'])){
			$tmp = mb_convert_encoding($tmp, $GLOBALS['WE_BACKENDCHARSET'], 'UTF-8,ISO-8859-15,ISO-8859-1');
		}
		try{
			@$tmp = htmlentities($tmp, ENT_COMPAT, $GLOBALS['WE_BACKENDCHARSET']);
		} catch (Exception $e){
			//try another encoding since last conversion failed.
			@$tmp = htmlentities($tmp, ENT_COMPAT, $GLOBALS['WE_BACKENDCHARSET'] === 'UTF-8' ? 'ISO-8859-15' : 'UTF-8');
		}
	}

	$ret = '<table class="error" align="center">
  <colgroup>
  <col width="10%"/>
  <col width="90%" />
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


	return '<table style="margin-top: 10px;border-style:none;width:100%;" cellpadding="0" cellspacing="0"><tr><td>' .
		we_html_button::create_button_table(array(
			we_html_button::create_button("first", $_SERVER['SCRIPT_NAME'] . '?function=first', true, 0, 0, '', '', ($pos == 1)),
			we_html_button::getButton("-" . $div, 'btn', "window.location.href='" . $_SERVER['SCRIPT_NAME'] . '?function=prevX&ID=' . $id . '&step=' . $div . "';", 0, '', ($pos - $div < 1)),
			we_html_button::create_button("back", $_SERVER['SCRIPT_NAME'] . '?function=prev&ID=' . $id, true, 0, 0, "", "", ($pos == 1)),
			), 10) .
		'</td><td align="center">' .
		we_html_button::create_button_table(array(
			we_html_button::create_button("export", $_SERVER['SCRIPT_NAME'] . '?function=export&ID=' . $id, true, 0, 0),
			we_html_button::create_button("delete", $_SERVER['SCRIPT_NAME'] . '?function=delete&ID=' . $id, true, 0, 0),
			), 10) . '</td><td align="right">' .
		we_html_button::create_button_table(array(
			we_html_button::create_button("next", $_SERVER['SCRIPT_NAME'] . '?function=next&ID=' . $id, true, 0, 0, "", "", ($pos == $size)),
			we_html_button::getButton("+" . $div, 'btn2', "window.location.href='" . $_SERVER['SCRIPT_NAME'] . '?function=nextX&ID=' . $id . '&step=' . $div . "';", 0, '', ($pos + $div > $size)),
			we_html_button::create_button("last", $_SERVER['SCRIPT_NAME'] . '?function=last', true),
			), 10) .
		'</td></tr><tr><td colspan="3" align="center" class="defaultfont" width="120"><b>' . $pos . "&nbsp;" . g_l('global', '[from]') . ' ' . $size . '</b>' .
		'</td></table>';
}

/* function formatLine(&$val, $key){
  $val = $key . ': ' . $val;
  } */

function getPosData($bt, $file, $lineNo){
	$ret = '';
	$matches = array();
	
	if(!$bt || $bt == '-' || !preg_match_all('|#\d+ [^\]]*\[([^:\]]*):(\d+)|', $bt, $matches)){
		$matches = array(
			1 => array(0=>str_replace('SECURITY_REPL_DOC_ROOT/','', $file)),
			2 => array(0=>$lineNo)
		);
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
$buttons = g_l('searchtool', '[anzeigen]') . ': <select onchange="document.getElementById(this.value).scrollIntoView();">' . $options . '</select>' .
	we_html_button::position_yes_no_cancel(
		we_html_button::create_button("delete_all", $_SERVER['SCRIPT_NAME'] . '?deleteAll=1'), we_html_button::create_button('refresh', $_SERVER['SCRIPT_NAME']), we_html_button::create_button("close", "javascript:self.close()")
);


$db = $GLOBALS['DB_WE'];
if(we_base_request::_(we_base_request::BOOL, 'deleteAll')){
	$db->query('TRUNCATE TABLE `' . ERROR_LOG_TABLE . '`');
}

$size = f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '`');
$id = we_base_request::_(we_base_request::INT, 'ID', 0);
$step = we_base_request::_(we_base_request::INT, 'step', 0);

switch(we_base_request::_(we_base_request::STRING, 'function', 'last')){
	default:
	case 'last':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` ORDER By ID DESC LIMIT 1');
		$pos = $size;
		break;
	case 'first':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` ORDER By ID ASC LIMIT 1');
		$pos = 1;
		break;
	case 'export':
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename=error' . $id . '.txt');
		$cur = getHash('SELECT ID,Type,Function,File,Line,Text,Backtrace,Date FROM `' . ERROR_LOG_TABLE . '` WHERE ID=' . $id . ' ORDER By ID ASC LIMIT 1', $db, MYSQL_ASSOC);
		$sep = "\n" . str_repeat('-', 80) . "\n";
		if($cur){
			$cur['Source-Code'] = getPosData($cur['Backtrace'], $cur['File'], $cur['Line']);
		}
		$data = '';
		foreach($cur as $key => $val){
			$data.=$key . ': ' . $val . $sep;
		}
		$data.='WE-Info:
Version: ' . WE_VERSION . '
SVN: ' . WE_SVNREV . ' ' . WE_VERSION_BRANCH . ' ' . WE_VERSION_SUPP . $sep .
			'System:
PHP: ' . phpversion() . '
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
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID=' . $id . ' ORDER By ID ASC LIMIT 1');
		$pos = $size - f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID>=' . $id) + 1;
		break;
	case 'delete':
		$db->query('DELETE FROM `' . ERROR_LOG_TABLE . '` WHERE ID=' . $id);
		$size = f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '`');
	case 'next':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID>' . $id . ' ORDER By ID ASC LIMIT 1');
		$pos = $size - f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID>' . $id) + 1;
		break;
	case 'prev':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID<' . $id . ' ORDER By ID DESC LIMIT 1');
		$pos = f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID<' . $id);
		break;
	case 'nextX':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID>=' . $id . ' ORDER By ID ASC LIMIT ' . $step . ',1');
		$pos = $size - f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID>' . $cur['ID']);
		break;
	case 'prevX':
		$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` WHERE ID<=' . $id . ' ORDER By ID DESC LIMIT ' . $step . ',1');
		$pos = f('SELECT COUNT(1) FROM `' . ERROR_LOG_TABLE . '` WHERE ID<=' . $cur['ID']);
		break;
}

if($size && !$cur){//nothing found, go to last element
	$cur = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` ORDER By ID DESC LIMIT 1');
	$pos = $size;
}

if($size && $cur){
	$cur['posData'] = getPosData($cur['Backtrace'], $cur['File'], $cur['Line']);
}

$data = getInfoTable($cur);

$_parts = array(
	array(
		'html' => ($size && $data ? $data : g_l('global', '[no_entries]')),
		'space' => 10,
	)
);

echo we_html_tools::getHtmlTop(g_l('javaMenu_global', '[showerrorlog]')) .
 we_html_element::jsScript(JS_DIR . 'keyListener.js') .
 we_html_element::jsElement('function closeOnEscape() {
		return true;
	}
') .
 STYLESHEET . we_html_element::cssElement('
table.error{
	 background-color:#FFFFFF;
	 border: 1px solid #265da6;
	 width:610px;
}
table.error tr.first{
	background-color:#f7f7f7;
}
table.error tr{
	vertical-align:top;
}
table.error td{
	padding:4px;
	border-bottom: 1px solid #265da6;
	font-family:' . g_l('css', '[font_family]') . ';
}
table.error td.left{
	white-space:nowrap;
	border-right: 1px solid #265da6;
	font-weight: bold;
}
table.error td.right{
	font-style:italic;
}
table.error td pre{
	font-style:normal;
	tab-size:2;
	-o-tab-size:2;
	-moz-tab-size:2;
}');
?>
</head>

<body class="weDialogBody" style="overflow:hidden;" onload="self.focus();">
	<div id="info" style="display: block;">
		<?php
		echo we_html_multiIconBox::getJS() .
		we_html_element::htmlDiv(array('style' => 'position:absolute; top:0px; left:30px;right:30px;height:60px;'), $size && $data ? getNavButtons($size, $pos, isset($cur['ID']) ? $cur['ID'] : 0) : '') .
		we_html_element::htmlDiv(array('style' => 'position:absolute;top:60px;bottom:0px;left:0px;right:0px;'), we_html_multiIconBox::getHTML('', 700, $_parts, 30, $buttons, -1, '', '', false, "", "", "", "auto"));
		?>
	</div>
</body>
</html>