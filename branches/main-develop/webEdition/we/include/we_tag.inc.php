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
function we_include_tag_file($name){
	$fn = 'we_tag_' . $name;

	// as default: all tag_functions are in this file.
	if(function_exists($fn)){
		// do noting
		return true;
	}
	if(file_exists(WE_INCLUDES_PATH . 'we_tags/' . $fn . '.inc.php')){
		require_once (WE_INCLUDES_PATH . 'we_tags/' . $fn . '.inc.php');
		return true;
	}
	//error check is only required for custom tags
	if(file_exists(WE_INCLUDES_PATH . 'we_tags/custom_tags/' . $fn . '.inc.php')){
		require_once (WE_INCLUDES_PATH . 'we_tags/custom_tags/' . $fn . '.inc.php');
		return function_exists($fn) ? true : parseError(sprintf(g_l('parser', '[tag_not_known]'), trim($name)));
	}

	$toolinc = '';
	if(we_tool_lookup::getToolTag($name, $toolinc, true)){
		require_once ($toolinc);
		return function_exists($fn) ? true : parseError(sprintf(g_l('parser', '[tag_not_known]'), trim($name)));
	}
	if(strpos(trim($name), 'if') === 0){ // this ifTag does not exist
		echo parseError(sprintf(g_l('parser', '[tag_not_known]'), trim($name)));
		return false;
	}
	return parseError(sprintf(g_l('parser', '[tag_not_known]'), trim($name)));
}

/**
 * get the full name of an Attribute with applied postTagName if set
 * @param type $var
 * @return type
 */
function we_tag_getPostName($var){
	if($var && isset($GLOBALS['postTagName'])){
		return $var . $GLOBALS['postTagName'];
	}
	return $var;
}

function we_profiler($start = true){
	if($start){
		define('WE_PROFILER', microtime(true));
		$GLOBALS['we_profile'] = [];
	} else {
		echo 'tag,line,file,time,mem<br/>';
		foreach($GLOBALS['we_profile'] as $line){
			echo implode(',', $line) . '<br/>';
		}
	}
}

function we_tag($name, $attribs = [], $content = '', $internal = false){
	//keep track of editmode
	$edMerk = isset($GLOBALS['we_editmode']) ? $GLOBALS['we_editmode'] : '';
	//FIXME: do we support this????
	$user = weTag_getAttribute('user', $attribs, [], we_base_request::STRING_LIST);
	if(defined('WE_PROFILER')){
		$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		$prof = array('tag' => $name, 'line' => $bt[0]['line'], 'file' => $bt[0]['file'], 'time' => round((microtime(true) - WE_PROFILER), 5) . ' s', 'mem' => round(((memory_get_usage() / 1024))) . ' kB');
		$GLOBALS['we_profile'][] = $prof;
		//annotate tag, if possible
		$attribs['data-time'] = $prof['time'];
		$attribs['data-mem'] = $prof['mem'];
		$attribs['data-line'] = $prof['line'];
		$attribs['data-file'] = $prof['file'];
	}
	//make sure comment attribute is never shown
	switch($name){
		case 'setVar':
		case 'xmlnode'://special handling inside tag setVar and xmlnode
			$attribs = removeAttribs($attribs, array('cachelifetime', 'comment', 'user'));
			$nameTo = '';
			$to = 'screen';
			break;
		default:
			$to = weTag_getAttribute('to', $attribs, 'screen');
			$nameTo = weTag_getAttribute('nameto', $attribs, isset($attribs['name']) ? $attribs['name'] : '');
			$attribs = removeAttribs($attribs, array('cachelifetime', 'comment', 'to', 'nameto', 'user'));

			/* if to attribute is set, output of the tag is redirected to a variable
			 * this makes only sense if tag output is equal to non-editmode */
			if($to != 'screen'){
				$GLOBALS['we_editmode'] = false;
			}
	}

	//make a copy of the name - this copy is never touched even not inside blocks/listviews etc.
	if(isset($attribs['name'])){
		$attribs['_name_orig'] = $attribs['name'];
		$attribs['name'] = we_tag_getPostName($attribs['name']);
		if(!empty($GLOBALS['we_editmode']) && ($GLOBALS['we_doc'] instanceof we_webEditionDocument)){
			$GLOBALS['we_doc']->addUsedElement($name, $attribs['name']);
		}
	}

	if($edMerk && $user && (!permissionhandler::hasPerm('ADMINISTRATOR'))){
		if(!in_array($_SESSION['user']['Username'], $user)){
			$GLOBALS['we_editmode'] = false;
		}
	}

	if(($foo = we_include_tag_file($name)) !== true){
		return $foo;
	}

	$fn = 'we_tag_' . $name;
	switch($fn){
		case 'we_tag_setVar':
			$fn($attribs, $content, $internal);
			//nothing more to do don't waste time
			return;
		default:
			$foo = $fn($attribs, $content, $internal);
			$GLOBALS['we_editmode'] = $edMerk;
			return we_redirect_tagoutput($foo, $nameTo, $to);
	}
}

### tag utility functions ###

function we_setVarArray(&$arr, $string, $value){
	if(strpos($string, '[') === false){
		$arr[$string] = $value;
		return;
	}
	$current = &$arr;

	/* 	$arr_matches = [];
	  preg_match('/[^\[\]]+/', $string, $arr_matches);
	  $first = $arr_matches[0];
	  preg_match_all('/\[([^\]]*)\]/', $string, $arr_matches, PREG_PATTERN_ORDER);
	  $arr_matches = $arr_matches[1];
	  array_unshift($arr_matches, $first); */
	$arr_matches = preg_split('/\]\[|\[|\]/', $string);
	$last = count($arr_matches) - 1;
	unset($arr_matches[$last--]); //preg split has an empty element at the end
	foreach($arr_matches as $pos => $dimension){
		if(empty($dimension)){
			$dimension = count($current);
		}
		if($pos == $last){
			$current[$dimension] = $value;
			return;
		}
		if(!isset($current[$dimension])){
			$current[$dimension] = [];
		}
		$current = &$current[$dimension];
	}
}

function we_redirect_tagoutput($returnvalue, $nameTo, $to = 'screen'){
	switch(isset($GLOBALS['calculate']) ? 'calculate' : $to){
		case 'request':
			we_setVarArray($_REQUEST, $nameTo, $returnvalue);
			return null;
		case 'post':
			we_setVarArray($_POST, $nameTo, $returnvalue);
			return null;
		case 'get':
			we_setVarArray($_GET, $nameTo, $returnvalue);
			return null;
		case 'global':
			we_setVarArray($GLOBALS, $nameTo, $returnvalue);
			return null;
		case 'session':
			we_setVarArray($_SESSION, $nameTo, $returnvalue);
			return null;
		case 'top':
			$GLOBALS['WE_MAIN_DOC_REF']->setElement($nameTo, $returnvalue);
			return null;
		case 'block' :
			$nameTo = we_tag_getPostName($nameTo);
		case 'self' :
			$GLOBALS['we_doc']->setElement($nameTo, $returnvalue);
			return null;
		case 'sessionfield' :
			if(isset($_SESSION['webuser'][$nameTo])){
				$_SESSION['webuser'][$nameTo] = $returnvalue;
			}
			return null;
		case 'calculate':
			return we_base_util::std_numberformat($returnvalue);
		case 'screen':
		default:
			return $returnvalue;
	}
	return null;
}

function mta($hash, $key){
	return (isset($hash[$key]) && ($hash[$key] != '' || $key === 'alt')) ? (' ' . $key . '="' . $hash[$key] . '"') : '';
}

function printElement($code){
	if($code === ''){//tag calculate can return 0, we need to write this.
		return;
	}
	if(strpos($code, '<?') === FALSE){
		echo $code;
		return;
	}
	//t_e('deprecated', 'we-tag contained php code which needs evaluation, this is deprecated, use parseTag instead', $code);
	//this is used e.g. in <we:a>$var</we> or in <we:a><we:ifBack....
	//FIXME: bad eval????
	eval('?>' . str_replace(array('<?php', '<?=', '?>'), array('<?php ', '<?= ', ' ?>'), $code));
}

function getArrayValue($var, $name, $arrayIndex, $isset = false){
	$arr_matches = preg_split('/\]\[|\[|\]/', $arrayIndex);
	if(count($arr_matches) > 1){
		unset($arr_matches[count($arr_matches) - 1]);
	}
	if($name !== null){
		$arr_matches[0] = $name;
	}
	foreach($arr_matches as $cur){
		if(!isset($var[$cur])){
			return ($isset ? false : '');
		}
		$var = $var[$cur];
	}
	return ($isset ? true : $var);
}

/**
 * get an attribute from $attribs, and return its value according to default
 * @param string $name attributes name
 * @param array $attribs array containg the attributes
 * @param mixed $default default value
 * @return mixed returns the attributes value or default if not set
 */
function weTag_getParserAttribute($name, $attribs, $default = '', $type = we_base_request::RAW){
	return weTag_getAttribute($name, $attribs, $default, $type, false);
}

/**
 * get an attribute from $attribs, and return its value according to default
 * @param string $name attributes name
 * @param array $attribs array containg the attributes
 * @param mixed $default default value
 * @param bool $useGlobal check if attribute value is a php-variable and is found in $GLOBALS
 * @return mixed returns the attributes value or default if not set
 */
function weTag_getAttribute($name, $attribs, $default = '', $type = we_base_request::RAW, $useGlobal = true){
	//FIXME: add an array holding attributes accessed for removal!
	$value = isset($attribs[$name]) ? $attribs[$name] : $default;
	$regs = [];
	if($useGlobal && !is_array($value) && preg_match('|^\\\\?\$([^\[]+)(\[.*\])?|', $value, $regs)){
		$value = (isset($regs[2]) ?
				getArrayValue($GLOBALS, $regs[1], $regs[2]) :
				(isset($GLOBALS[$regs[1]]) ? $GLOBALS[$regs[1]] : ''));
	}

	$value = we_base_request::filterVar($value, (is_bool($type) ? we_base_request::BOOL : $type), $default);

	return is_array($value) || is_bool($value) ? $value : htmlspecialchars_decode($value);
}

function we_tag_path_hasIndex($path, $indexArray){
	foreach($indexArray as $index){
		if(file_exists($path . $index)){
			return true;
		}
	}
	return false;
}

function cutSimpleText($text, $len){
	if($len >= strlen($text)){
		return $text;
	}
	$text = substr($text, 0, $len);
	//cut to last whitespace, if any.
	return substr($text, 0, max(array(
			strrpos($text, ' '),
			strrpos($text, '.'),
			strrpos($text, ','),
			strrpos($text, "\n"),
			strrpos($text, "\t"),
		))? : $len
	);
}

function cutText($text, $max = 0, $striphtml = false){
	$text = $striphtml ? strip_tags($text) : $text;
	if((!$max) || (strlen($text) <= $max)){
		return $text;
	}
	//no tags, simple cut off
	if($striphtml || strstr($text, '<') === FALSE){
		return cutSimpleText($text, $max) . ($striphtml ? ' ...' : ' &hellip;');
	}

	$ret = '';
	$tags = $foo = [];
	//split text on tags, entities and "rest"
	preg_match_all('%(&#?[[:alnum:]]+;)|([^<&]*)|<(/?)([[:alnum:]]+)([ \t\r\n]+[[:alnum:]]+[ \t\r\n]*=[ \t\r\n]*"[^"]*")*[ \t\r\n]*(/?)>%sm', $text, $foo, PREG_SET_ORDER);

	foreach($foo as $cur){
		switch(count($cur)){
			case 2://entity
				if($max > 0){
					$ret.=$cur[0];
					$max-=1;
				}
				break;
			case 3://text
				if($max > 0){
					$len = strlen($cur[0]);
					$ret.=($len > $max ? cutSimpleText($cur[0], $max) : $cur[0]);
					$max-=$len;
					if($max <= 0){
						$ret.=($striphtml ? ' ...' : ' &hellip;');
					}
				}
				break;
			case 7://tags
				if($max > 0){
					$ret.=$cur[0];
					if(!$cur[6]){//!selfclosing
						if($cur[3]){//close
							array_pop($tags);
						} else {
							$tags[] = $cur[4];
						}
					}
				}
				break;
		}
	}

//close open tags
	while($tags){
		$ret.='</' . array_pop($tags) . '>';
	}

	return $ret;
}

function we_getDocForTag($docAttr, $maindefault = false){
	switch($docAttr){
		case 'self' :
			return $GLOBALS['we_doc'];
		case 'top' :
			return $GLOBALS['WE_MAIN_DOC'];
		default :
			return ($maindefault ?
					$GLOBALS['WE_MAIN_DOC'] :
					$GLOBALS['we_doc']);
	}
}

function modulFehltError($modul, $tag){
	return parseError(sprintf(g_l('parser', '[module_missing]'), $modul, str_replace(array('we_tag_', 'we_parse_tag_'), '', $tag)));
}

function parseError($text, $extra = ''){
	t_e('warning', html_entity_decode($text, ENT_QUOTES, $GLOBALS['WE_BACKENDCHARSET']), g_l('weClass', '[template]') . ': ' . we_tag_tagParser::$curFile, $extra);
	return '<b>' . g_l('parser', '[error_in_template]') . ':</b>' . $text . "<br/>\n" . g_l('weClass', '[template]') . ': ' . we_tag_tagParser::$curFile;
}

function attributFehltError($attribs, $attrs, $tag, $canBeEmpty = false){
	$tag = str_replace(array('we_tag_', 'we_parse_tag_'), '', $tag);
	if(!is_array($attrs)){
		$attrs = array($attrs => $canBeEmpty);
	}
	foreach($attrs as $attr => $canBeEmpty){
		if($canBeEmpty){
			if(!isset($attribs[$attr])){
				return parseError(sprintf(g_l('parser', '[attrib_missing2]'), $attr, $tag));
			}
		} elseif(!isset($attribs[$attr]) || $attribs[$attr] === ''){
			return parseError(sprintf(g_l('parser', '[attrib_missing]'), $attr, $tag));
		}
	}
	return '';
}

/**
 * @return array
 * @param array $atts
 * @param array $ignore
 * @desc Removes all empty values from assoc array without the in $ignore given
 */
function removeEmptyAttribs($atts, $ignore = []){
	foreach($atts as $k => $v){
		if($v === '' && !in_array($k, $ignore)){
			unset($atts[$k]);
		}
	}
	return $atts;
}

/**
 * @return array
 * @param array $atts
 * @param array $ignore
 * @desc only uses the attribs given in the array use
 */
function useAttribs(array $atts, array $use){
	return array_intersect_key($atts, array_flip($use));
}

function we_getInputRadioField($name, $value, $itsValue, $atts){
	//  This function replaced fnc: we_getRadioField
	$atts['type'] = 'radio';
	$atts['name'] = $name;
	$atts['value'] = oldHtmlspecialchars($itsValue, -1, 'ISO-8859-1', false);
	if($value == $itsValue){
		$atts['checked'] = 'checked';
	}
	return getHtmlTag('input', $atts);
}

function we_getTextareaField($name, $value, array $atts){
	$atts['name'] = $name;
	$atts['rows'] = isset($atts['rows']) ? $atts['rows'] : 5;
	$atts['cols'] = isset($atts['cols']) ? $atts['cols'] : 20;

	return getHtmlTag('textarea', $atts, oldHtmlspecialchars($value), true);
}

function we_getInputTextInputField($name, $value, array $atts){
	$atts['type'] = 'text';
	$atts['name'] = $name;
	$atts['value'] = oldHtmlspecialchars($value);

	return getHtmlTag('input', $atts);
}

//function we_getInputChoiceField($name, $value, $values, $atts, $mode, $valuesIsHash = false){}
//=> moved as statical function htmlInputChoiceField() to we_html_tools

function we_getInputCheckboxField($name, $value, array $attr){
	//  returns a checkbox with associated hidden-field

	$tmpname = md5(uniqid(__FUNCTION__, true)); // #6590, changed from: uniqid(time())
	if($value){
		$attr['checked'] = 'checked';
	}
	$attr['type'] = 'checkbox';
	$attr['value'] = 1;
	$attr['name'] = $tmpname;
	$attr['onclick'] = 'this.form.elements[\'' . $name . '\'].value=(this.checked) ? 1 : 0';
	$attsHidden = [];

	// hiddenField
	if(isset($attr['xml'])){
		$attsHidden['xml'] = $attr['xml'];
	}
	$attsHidden['type'] = 'hidden';
	$attsHidden['name'] = $name;
	$attsHidden['value'] = oldHtmlspecialchars($value);

	return getHtmlTag('input', $attr) . getHtmlTag('input', $attsHidden);
}

function we_getSelectField($name, $value, $values, array $attribs = [], $addMissing = true){
	$options = makeArrayFromCSV($values);
	$attribs['name'] = $name;
	$content = '';
	$isin = 0;
	foreach($options as $option){
		$opt = oldHtmlspecialchars($option, -1, 'ISO-8859-1', false);
		if($option == $value){
			$content .= getHtmlTag('option', array('value' => $opt, 'selected' => 'selected'), $opt, true);
			$isin = 1;
		} else {
			$content .= getHtmlTag('option', array('value' => $opt), $opt, true);
		}
	}
	if((!$isin) && $addMissing && $value != ''){
		$content .= getHtmlTag('option', array(
			'value' => oldHtmlspecialchars($value), 'selected' => 'selected'
			), oldHtmlspecialchars($value), true);
	}
	return getHtmlTag('select', $attribs, $content, true);
}

function we_pre_tag_listview(){
	//prevent error if $GLOBALS["we_lv_array"] is no array
	if(!isset($GLOBALS['we_lv_array']) || !is_array($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'] = [];
	}

	//FIXME: check why we need cloning here
	$GLOBALS['we_lv_array'][] = clone($GLOBALS['lv']);
}

//this function is used by all tags adding elements to we_lv_array
function we_post_tag_listview(){
	if(isset($GLOBALS['we_lv_array'])){
		if(isset($GLOBALS['lv'])){
			array_pop($GLOBALS['we_lv_array']);
		}
		if(!empty($GLOBALS['we_lv_array'])){
			$GLOBALS['lv'] = clone(end($GLOBALS['we_lv_array']));
		} else {
			unset($GLOBALS['lv']);
			unset($GLOBALS['we_lv_array']);
		}
	}
}
