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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

if (!isset($GLOBALS['WE_IS_DYN'])) {
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/SEEM/we_SEEM.class.php');
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/global.inc.php');
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/linklist_edit.inc.php');
}

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_html_tools.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/date.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/parser.inc.php');

// Tag and TagBlock Cache
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/cache/weCacheHelper.class.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/cache/weCache.class.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/cache/weTagCache.class.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/cache/weTagBlockCache.class.php');

include_once $_SERVER['DOCUMENT_ROOT'].'/webEdition/lib/we/core/autoload.php';

include_once (WE_USERS_MODULE_DIR . 'we_users_util.php');

function we_tag($name, $attribs=array(), $content = ''){
	
	if ($content) {
		$content = str_replace('we_:_', 'we:', $content);
	}
	$edMerk = isset($GLOBALS['we_editmode']) ? $GLOBALS['we_editmode'] : '';
	if (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) {
		if (isset($attribs['user']) && $attribs['user']) {
			$uAr = makeArrayFromCSV($attribs['user']);
			$userIds = array();
			foreach ($uAr as $u) {
				$i = f("SELECT ID FROM " . USER_TABLE . " WHERE Username='" . mysql_real_escape_string($u) . "'", "ID", $GLOBALS["DB_WE"]);
				if ($i) {
					array_push($userIds, $i);
				}
			}
			if (!isUserInUsers($_SESSION['user']['ID'], $userIds) && (!$_SESSION['perms']['ADMINISTRATOR'])) {
				$GLOBALS['we_editmode'] = false;
			}
		}
	}
	
	// as default: all tag_functions are in this file.
	$fn = "we_tag_$name";
	if (isset($GLOBALS['weNoCache']) && $GLOBALS['weNoCache'] == true) {
		$attribs['cachelifetime'] = 0;
		$CacheType = 'none';
	} else {
		if (isset($GLOBALS['we_doc']->CacheType) ){
			$CacheType = $GLOBALS['we_doc']->CacheType;
		} else {
			$CacheType = 'none';
			$attribs['cachelifetime'] = 0;
		}
	}
	
	if ($name == 'navigation') {
		$configFile = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/navigation/conf/we_conf_navigation.inc.php';
		if (!file_exists($configFile) || !is_file($configFile)) {
			include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/navigation/class/weNavigationSettingControl.class.php');
			weNavigationSettingControl::saveSettings(true);
		}
		include ($configFile);
		$lifeTime = isset($attribs['cachelifetime']) ? $attribs['cachelifetime'] : ($GLOBALS['weDefaultNavigationCacheLifetime'] != '' && $GLOBALS['weDefaultNavigationCacheLifetime'] != '0' ? $GLOBALS['weDefaultNavigationCacheLifetime'] : $GLOBALS['we_doc']->CacheLifeTime);
	} else {
		$lifeTime = isset($attribs['cachelifetime']) ? $attribs['cachelifetime'] : $GLOBALS['we_doc']->CacheLifeTime;
	}
	
	$attribs = removeAttribs($attribs, array(
		'cachelifetime'
	));
	$OtherCacheActive = (isset($GLOBALS['weTagListviewCacheActive']) && $GLOBALS['weTagListviewCacheActive']) || (isset(
			$GLOBALS['weTagBlockCache']) && $GLOBALS['weTagBlockCache'] >= 1);
	$toolinc = '';
	
	if (function_exists($fn)) {
		// do noting
	} else 
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_tags/we_tag_$name.inc.php")) {
			include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_tags/we_tag_$name.inc.php");
		
		} else 
			if ($fn == 'we_tag_noCache') {
			
			} else 
				if (file_exists(
						$_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/custom_tags/' . "we_tag_$name.inc.php")) {
					include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/custom_tags/' . "we_tag_$name.inc.php");
				} else {
					include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/tools/weToolLookup.class.php');
					if (weToolLookup::getToolTag($name, $toolinc,true)) {
						include_once ($toolinc);
					} else {
						if (strpos(trim($name), 'if') === 0) { // this ifTag does not exist
							print parseError(sprintf($GLOBALS['l_parser']['tag_not_known'], trim($name)));
							return false;
						}
						return parseError(sprintf($GLOBALS['l_parser']['tag_not_known'], trim($name)));
					}
				
				}
	
	if ($name == 'block' || $name == 'list' || $name == 'linklist') {
		$weTagCache = new weTagBlockCache($name, $attribs, $content, $lifeTime);
	
	} else {
		$weTagCache = new weTagCache($name, $attribs, $content, $lifeTime);
	}
	
	$foo = '';
	
	if ($fn == 'we_tag_setVar') {
		$fn($attribs, $content);
	}
	
	// Use Document Cache
	if ($CacheType == 'document' && (!isset($GLOBALS['weNoCache']) || !$GLOBALS['weNoCache']) && (!isset(
			$GLOBALS['weCacheOutput']) || !$GLOBALS['weCacheOutput'])) {
		
		// Cache LifeTime > 0
		if ($GLOBALS['we_doc']->CacheLifeTime > 0 && get_class($weTagCache) != 'weTagBlockCache') {
			
			if ($fn == 'we_tag_noCache') {
				echo $content;
				ob_start();
				$GLOBALS['weNoCache'] = true;
				eval('?>' . $content);
				$GLOBALS['weNoCache'] = false;
				ob_end_clean();
				
			// Tag is cacheable
			} else 
				if ($weTagCache->isCacheable()) {
					//echo $fn($attribs, $content);
					$foo = $fn($attribs, $content); // Bug Fix #8250
				

				// Tag is not cacheable
				} else {
					if (eregi('^we_tag_if', $fn)) {
						if (isset($GLOBALS['weTagListviewCacheActive']) && $GLOBALS['weTagListviewCacheActive'] == true) {
							$foo = $fn($attribs, $content);
						
						} else {
							$foo = "<?php if(we_tag('$name', unserialize('" . serialize($attribs) . "'))) {\n$content ?>";
						
						}
					
					} else {
						echo "<?php printElement(we_tag('$name', unserialize('" . serialize($attribs) . "'), '$content')); ?>";
					
					}
				
				}
			
		// normal use
		} else {
			if ($fn == 'we_tag_noCache') {
				$GLOBALS['weNoCache'] = true;
				$foo = eval('?>' . $content);
				$GLOBALS['weNoCache'] = false;
			
			} else {
				$foo = $fn($attribs, $content);
			
			}
		
		}
	
	} else 
		if ($CacheType == 'full' && $weTagCache->lifeTime > 0) {
			$foo = $fn($attribs, $content);
		
		} else {
			
			// Use Tag Cache
			if ($CacheType == 'tag' || $weTagCache->lifeTime > 0) {
				
				// Tag is cacheable
				if ($weTagCache->isCacheable()) { // lifeTime is checked in isCacheable()
					

					// generate the cache
					if ($weTagCache->start()) {
						echo $fn($attribs, $content);
						$weTagCache->end();
					
					}
					$foo = $weTagCache->get();
					
				// Tag is not cacheable
				} else {
					
					if ($fn == 'we_tag_noCache') {
						echo $content;
						ob_start();
						$GLOBALS['weNoCache'] = true;
						eval('?>' . $content);
						$GLOBALS['weNoCache'] = false;
						ob_end_clean();
					
					} else {
						$foo = $fn($attribs, $content);
					
					}
				
				}
				
			// Do not use Cache
			} else {
				if ($fn == 'we_tag_noCache') {
					$GLOBALS['weNoCache'] = true;
					$foo = eval('?>' . $content);
					$GLOBALS['weNoCache'] = false;
				
				} else {
					$foo = $fn($attribs, $content);
				
				}
			
			}
		
		}
	$GLOBALS['we_editmode'] = $edMerk;
	
	return $foo;

}

### tag utility functions ###

function we_redirect_tagoutput($returnvalue,$nameTo,$to='screen'){
	switch ($to) {
		case 'request' :
			$_REQUEST[$nameTo] = $returnvalue;
			break;
		case 'post' :
			$_POST[$nameTo] = $returnvalue;
			break;
		case 'get' :
			$_GET[$nameTo] = $returnvalue;
			break;
		case 'global' :
			$GLOBALS[$nameTo] = $returnvalue;
			break;
		case 'session' :
			$_SESSION[$nameTo] = $returnvalue;
			break;
		case 'top' :
			$GLOBALS['WE_MAIN_DOC_REF']->setElement($nameTo, $returnvalue);
			break;
		case 'block' :
		case 'self' :
			$GLOBALS['we_doc']->setElement($nameTo, $returnvalue);
			break;		
		case 'sessionfield' :
			if (isset($_SESSION['webuser'][$nameTo])){
				$_SESSION['webuser'][$nameTo] = $returnvalue;
			}
			break;
		case 'screen':
			return $returnvalue;
	}
	return null;

}

function mta($hash, $key){
	return (isset($hash[$key]) && ($hash[$key] != '' || $key == 'alt')) ? (' ' . $key . '="' . $hash[$key] . '"') : '';
}

function printElement($code){
	if (isset($code)) {
		$code = str_replace('<?php', '<?php ', $code);
		$code = str_replace('?>', ' ?>', $code);
		eval('?>' . $code);
	}
}

function makeEmptyTable($in){
	preg_match_all('/<[^>]+>/i', $in, $result, PREG_SET_ORDER);
	
	$out = '';
	for ($i = 0; $i < sizeof($result); $i++) {
		$tag = $result[$i][0];
		
		if (eregi('< ?td', $tag) || eregi('< ?/ ?td', $tag) || eregi('< ?tr', $tag) || eregi('< ?/ ?tr', $tag) || eregi(
				'< ?table',
				$tag) || eregi('< ?/ ?table', $tag) || eregi('< ?tbody', $tag) || eregi('< ?/ ?tbody', $tag)) {
			$out .= $tag;
		}
	
	}
	return $out;
}

function we_cmpText($a, $b){
	$x = strtolower(correctUml($a['properties']['Text']));
	$y = strtolower(correctUml($b['properties']['Text']));
	if ($x == $y)
		return 0;
	return ($x < $y) ? -1 : 1;
}

function we_cmpTextDesc($a, $b){
	$x = strtolower(correctUml($a['properties']['Text']));
	$y = strtolower(correctUml($b['properties']['Text']));
	if ($x == $y)
		return 0;
	return ($x > $y) ? -1 : 1;
}

function we_cmpField($a, $b){
	$x = strtolower(correctUml($a['sort']));
	$y = strtolower(correctUml($b['sort']));
	if ($x == $y)
		return 0;
	return ($x < $y) ? -1 : 1;
}

function we_cmpFieldDesc($a, $b){
	$x = strtolower(correctUml($a['sort']));
	$y = strtolower(correctUml($b['sort']));
	if ($x == $y)
		return 0;
	return ($x > $y) ? -1 : 1;
}

function we_tag_path_hasIndex($path, $indexArray){
	foreach ($indexArray as $index) {
		if (file_exists($path . $index)) {
			return true;
		}
	}
	return false;
}

function makeArrayFromAttribs($attr){
	$attribs = '';
	preg_match_all('/([^=]+)= *("[^"]*")/', $attr, $foo, PREG_SET_ORDER);
	for ($i = 0; $i < sizeof($foo); $i++) {
		$attribs .= '"' . trim($foo[$i][1]) . '"=>' . trim($foo[$i][2]) . ',';
	}
	$arrstr = 'array(' . ereg_replace('(.+),$', '\\1', $attribs) . ')';
	eval('$arr = ' . $arrstr . ';');
	return $arr;
}

function correctDateFormat($format, $t = ''){
	global $l_dayShort, $l_monthLong, $l_dayLong, $l_monthShort;
	if (!$t)
		$t = time();
	
	$format = str_replace('\B', '%%%4%%%', $format);
	$format = str_replace('\I', '%%%5%%%', $format);
	$format = str_replace('\L', '%%%6%%%', $format);
	$format = str_replace('\T', '%%%8%%%', $format);
	$format = str_replace('\U', '%%%9%%%', $format);
	$format = str_replace('\Z', '%%%10%%%', $format);
	
	$format = str_replace('B', '\\B', $format);
	$format = str_replace('I', '\\I', $format);
	$format = str_replace('L', '\\L', $format);
	$format = str_replace('T', '\\T', $format);
	$format = str_replace('U', '\\U', $format);
	$format = str_replace('Z', '\\Z', $format);
	
	$format = str_replace('%%%4%%%', '\B', $format);
	$format = str_replace('%%%5%%%', '\I', $format);
	$format = str_replace('%%%6%%%', '\L', $format);
	$format = str_replace('%%%8%%%', '\T', $format);
	$format = str_replace('%%%9%%%', '\U', $format);
	$format = str_replace('%%%10%%%', '\Z', $format);
	
	$format = str_replace('D', '%%%0%%%', $format);
	$format = str_replace('F', '%%%1%%%', $format);
	$format = str_replace('l', '%%%2%%%', $format);
	$format = str_replace('M', '%%%3%%%', $format);
	
	$foo = $l_dayShort[date('w', $t)];
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%0%%%', $foo, $format);
	$foo = $l_monthLong[date('n', $t) - 1];
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%1%%%', $foo, $format);
	$foo = $l_dayLong[date('w', $t)];
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%2%%%', $foo, $format);
	$foo = $l_monthShort[date('n', $t) - 1];
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%3%%%', $foo, $format);
	return $format;
}

function cutText($text, $max = 0){
	if (!$max)
		return $text;
	if (!strlen($text))
		return '';
	if (strlen($text) <= $max)
		return $text;
	
	$text = strip_tags($text, '<b>,<i>,<em>,<strong>,<a>,<u>,<br>,<div>,<span>');
	$htmlfree = strip_tags($text);
	$text = we_html2uml($text);
	$htmlfree = we_html2uml($htmlfree);
	$left = substr($htmlfree, 0, $max);
	$left = ereg_replace('^(.+)[ \.,].*$', '\1', $left);
	$lastword = ereg_replace('^.+[ \.,;\r\n](.+)$', '\1', $left);
	$orgpos = @strpos($text, $lastword);
	if ($orgpos) {
		$foo = substr($text, 0, $orgpos + strlen($lastword));
		$foo = strip_tags($foo);
	} else {
		$foo = $text;
	}
	$cutpos = $max;
	while ($orgpos && (strlen($foo) < $max)) {
		$cutpos = $orgpos + strlen($lastword);
		$orgpos = @strpos($text, $lastword, $orgpos + 1);
		$foo = substr($text, 0, $orgpos + strlen($lastword));
		$foo = strip_tags($foo);
	}
	$text = substr($text, 0, $cutpos);
	if (eregi('^(.+)(<)(a|b|em|strong|b|i|u|div|span)([ >][^<]*)$', $text, $regs)) {
		$text = $regs[1] . $regs[2] . $regs[3] . $regs[4] . '</' . $regs[3] . '>';
	} else 
		if (eregi('^(.+)(<)(a|em|strong|b|i|u|br|div|span)([^>]*)$', $text, $regs)) {
			$text = $regs[1];
		}
	return $text . '...';
}

function arrayKeyExists($key, $search){
	return (in_array($key, array_keys($search)));
}

function we_isVarSet($name, $type, $docAttr, $property = false, $formname = '', $shopname = ''){
	switch ($type) {
		case 'request' :
			return isset($_REQUEST[$name]);
			break;
		case 'post' :
			return isset($_POST[$name]);
			break;
		case 'get' :
			return isset($_GET[$name]);
			break;
		case 'global' :
			return isset($GLOBALS[$name]);
			break;
		case 'session' :
			return isset($_SESSION[$name]);
			break;
		case 'sessionfield' :
			return isset($_SESSION['webuser'][$name]);
			break;
		case 'shopField' :
			if (isset($GLOBALS[$shopname])) {
				return isset($GLOBALS[$shopname]->CartFields[$name]);
			}
			break;
		case 'sum' :
			return (isset($GLOBALS['summe']) && isset($GLOBALS['summe'][$name]));
			break;
		default :
			$doc = false;
			switch ($docAttr) {
				case 'object' :
				case 'document' :
					$doc = isset($GLOBALS['we_' . $docAttr][$formname]) ? $GLOBALS['we_' . $docAttr][$formname] : false;
					break;
				case 'top' :
					$doc = isset($GLOBALS['WE_MAIN_DOC']) ? $GLOBALS['WE_MAIN_DOC'] : false;
					break;
				default :
					$doc = isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc'] : false;
			}
			if ($doc) {
				if ($property) {
					eval('$retval = isset($doc->' . $name . ');');
					return $retval;
				} else {
					if ($type == 'href') {
						if ($doc->elements[$name . '_we_jkhdsf_int']['dat']) {
							return isset($doc->elements[$name . '_we_jkhdsf_intPath']['dat']);
						}
					}
					$fieldType = isset($doc->elements[$name]['type']) ? $doc->elements[$name]['type'] : '';
					$issetElemNameDat = isset($doc->elements[$name]['dat']);
					if ($fieldType == 'checkbox_feld' && $issetElemNameDat && $doc->elements[$name]['dat'] == 0)
						return false;
					return $issetElemNameDat;
				}
			} else {
				return false;
			}
	}
}

function we_isVarNotEmpty($attribs){	
	$docAttr = we_getTagAttribute('doc', $attribs);
	$type = we_getTagAttribute('type', $attribs);
	$match = we_getTagAttribute('match', $attribs);
	$name = we_getTagAttribute('name', $attribs);
	$type = we_getTagAttribute('type', $attribs, 'txt');
	$formname = we_getTagAttribute('formname', $attribs, 'we_global_form');
	$property = we_getTagAttribute('property', $attribs, '', true);
	
	if (!we_isVarSet($match, $type, $docAttr, $property, $formname))
		return false;
	
	switch ($type) {
		case 'request' :
			return (strlen($_REQUEST[$match]) > 0);
			break;
		case 'post' :
			return (strlen($_POST[$match]) > 0);
			break;
		case 'get' :
			return (strlen($_GET[$match]) > 0);
			break;
		case 'global' :
			return (strlen($GLOBALS[$match]) > 0);
			break;
		case 'session' :
			$foo = isset($_SESSION[$match]) ? $_SESSION[$match] : '';
			return (strlen($foo) > 0);
			break;
		case 'sessionfield' :
			return (strlen($_SESSION['webuser'][$match]) > 0);
			break;
		default :
			$doc = false;
			switch ($docAttr) {
				case 'object' :
				case 'document' :
					$doc = isset($GLOBALS['we_' . $docAttr][$formname]) ? $GLOBALS['we_' . $docAttr][$formname] : false;
					break;
				case 'top' :
					$doc = isset($GLOBALS['WE_MAIN_DOC']) ? $GLOBALS['WE_MAIN_DOC'] : false;
					break;
				default :
					$doc = isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc'] : false;
			}
			if ($doc) {
				if ($property) {
					eval('$retVal = isset($doc->' . $match . ') ? $doc->' . $match . ' : "";');
					return $retVal;
				} else {
					$name = $match;
					switch ($type) {
						case 'href' :
							$attribs['name'] = $match;
							$foo = $doc->getField($attribs, $type, true);
							break;
						case 'multiobject' :
							$attribs['name'] = $match;
							$data = unserialize($doc->getField($attribs, $type, true));
							if (!is_array($data['objects'])) {
								$data['objects'] = array();
							}
							include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/object/we_listview_multiobject.class.php');
							$temp = new we_listview_multiobject($match);
							if (sizeof($temp->Record) > 0) {
								return true;
							} else {
								return false;
							}
						default :
							$foo = $doc->getElement($match);
					}
					return (strlen($foo) > 0);
				}
			} else {
				return false;
			}
	}
}

function we_isNotEmpty($attribs){
	$docAttr = we_getTagAttribute('doc', $attribs);
	$type = we_getTagAttribute('type', $attribs);
	$match = we_getTagAttribute('match', $attribs);
	$doc = we_getDocForTag($docAttr, false);
	
	switch ($type) {
		case 'object' :
			return $doc->getElement($match);
		case 'binary' :
		case 'img' :
		case 'flashmovie' :
			return $doc->getElement($match, 'bdid');
		case 'href' :
			if (isset($doc->TableID) && $doc->TableID) {
				$hrefArr = $doc->getElement($match) ? unserialize($doc->getElement($match)) : array();
				if (!is_array($hrefArr))
					$hrefArr = array();
				$hreftmp = trim(we_document::getHrefByArray($hrefArr));
				if (substr($hreftmp, 0, 1) == '/' && (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp))) {
					return false;
				}
				return $hreftmp ? true : false;
			}
			$int = ($doc->getElement($match . '_we_jkhdsf_int') == '') ? 0 : $doc->getElement(
					$match . '_we_jkhdsf_int');
			if ($int) { // for type = href int
				$intID = $doc->getElement($match . '_we_jkhdsf_intID');
				if ($intID > 0) {
					return strlen(id_to_path($intID)) > 0;
				}
				return false;
			} else {
				$hreftmp = $doc->getElement($match);
				if (substr($hreftmp, 0, 1) == '/' && (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp))) {
					return false;
				}
			}
		default :
			
			if (isset($doc)) {
				//   #3938 added this - some php version crashed, when unserialize started with a ?,?,?
				

				if ((substr($doc->getElement($match), 0, 2) == 'a:')) { //  only unserialize, when $match cluld be an array
					// Added @-operator in front of the unserialze function because there
					// were some PHP notices that had no effect on the output of the function
					// remark holeg: when it is a serialized array, the function looks if it is not empty
					if (is_array(
							$arr = @unserialize($doc->getElement($match)))) {
						return sizeof($arr) ? true : false;
					}
				}
				//   end of #3938
			}
	
	}
	return ($doc->getElement($match) != '') || $doc->getElement($match, 'bdid');
}

function we_isFieldNotEmpty($attribs){
	$type = we_getTagAttribute('type', $attribs);
	$match = we_getTagAttribute('match', $attribs);
	switch ($type) {
		case 'calendar' :
			if (isset($GLOBALS['lv']->calendar_struct)) {
				if ($GLOBALS['lv']->calendar_struct['date'] < 0)
					return false;
				if (count($GLOBALS['lv']->calendar_struct['storage']) < 1)
					return false;
				if ($match == 'day') {
					$sd = mktime(
							0, 
							0, 
							0, 
							$GLOBALS['lv']->calendar_struct['month_human'],
							$GLOBALS['lv']->calendar_struct['day_human'],
							$GLOBALS['lv']->calendar_struct['year_human']);
					$ed = mktime(
							23, 
							59, 
							59, 
							$GLOBALS['lv']->calendar_struct['month_human'],
							$GLOBALS['lv']->calendar_struct['day_human'],
							$GLOBALS['lv']->calendar_struct['year_human']);
				} else 
					if ($match == 'month') {
						$sd = mktime(
								0, 
								0, 
								0, 
								$GLOBALS['lv']->calendar_struct['month_human'],
								1, 
								$GLOBALS['lv']->calendar_struct['year_human']);
						$ed = mktime(
								23, 
								59, 
								59, 
								$GLOBALS['lv']->calendar_struct['month_human'],
								$GLOBALS['lv']->calendar_struct['numofentries'],
								$GLOBALS['lv']->calendar_struct['year_human']);
					} else 
						if ($match == 'year') {
							$sd = mktime(0, 0, 0, 1, 1, $GLOBALS['lv']->calendar_struct['year_human']);
							$sd = mktime(23, 59, 59, 12, 31, $GLOBALS['lv']->calendar_struct['year_human']);
						}
				if (isset($sd) && isset($ed)) {
					foreach ($GLOBALS['lv']->calendar_struct['storage'] as $entry) {
						if ($sd < $entry && $ed > $entry)
							return true;
					}
				}
				return false;
			}
			return false;
			break;
		case 'multiobject' :
			if (isset($GLOBALS['lv'])) {
				if (isset($GLOBALS['lv']->object)) {
					$data = unserialize($GLOBALS['lv']->object->DB_WE->Record['we_' . $match]);
				} else {
					if ($GLOBALS['lv']->ClassName == 'we_listview_shoppingCart'){//Bug #4827
						$data = unserialize($GLOBALS['lv']->f($match));
					} else {
						$data = unserialize($GLOBALS['lv']->DB_WE->Record['we_' . $match]);
					}
				}
			} else {
				$data = unserialize($GLOBALS['we_doc']->getElement($match));
			}
			if (isset($data['objects']) && is_array($data['objects']) && sizeof($data['objects']) > 0) {
				$test = array_count_values($data['objects']);
				if (sizeof($test) > 1 || (sizeof($test) == 1 && !isset($test['']))) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		
		case 'object' : //Bug 3837: erstmal die Klasse rausfinden um auf den Eintrag we_we_object_X zu kommen
			$objectdb= new DB_WE();
			$objectid= f('SELECT ID FROM '.OBJECT_TABLE. " WHERE Text='".$match."'", 'ID', $objectdb);
			$objectdb->close();
			return $GLOBALS['lv']->f('we_object_'.$objectid);
		case 'checkbox' :
		case 'binary' :
		case 'img' :
		case 'flashmovie' :
		case 'quicktime' :
			return $GLOBALS['lv']->f($match);
		case 'href' :
			if ($GLOBALS['lv']->ClassName == 'we_listview_object' || $GLOBALS['lv']->ClassName == 'we_objecttag') {
				$hrefArr = $GLOBALS['lv']->f($match) ? unserialize($GLOBALS['lv']->f($match)) : array();
				if (!is_array($hrefArr))
					$hrefArr = array();
				$hreftmp = trim(we_document::getHrefByArray($hrefArr));
				if (substr($hreftmp, 0, 1) == '/' && (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp))) {
					return false;
				}
				return $hreftmp ? true : false;
			}
			$int = ($GLOBALS['lv']->f($match . '_we_jkhdsf_int') == '') ? 0 : $GLOBALS['lv']->f(
					$match . '_we_jkhdsf_int');
			if ($int) { // for type = href int
				$intID = $GLOBALS['lv']->f($match . '_we_jkhdsf_intID');
				if ($intID > 0) {
					return strlen(id_to_path($intID)) > 0;
				}
				return false;
			} else {
				$hreftmp = $GLOBALS['lv']->f($match);
				if (substr($hreftmp, 0, 1) == '/' && (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp))) {
					return false;
				}
			}
		default :
			$_tmp = @unserialize($GLOBALS['lv']->f($match));
			if (is_array($_tmp)) {
				return sizeof($_tmp) > 0;
			}
	}
	return $GLOBALS['lv']->f($match) != '';
}

function we_isUserInputNotEmpty($attribs){
	$formname = we_getTagAttribute('formname', $attribs, 'we_global_form');
	$match = we_getTagAttribute('match', $attribs,'',false,false,true);
	return (isset($_REQUEST['we_ui_' . $formname][$match]) && strlen($_REQUEST['we_ui_' . $formname][$match]));

}

function we_getDocForTag($docAttr, $maindefault = false){
	if ($maindefault) {
		switch ($docAttr) {
			case 'self' :
				return $GLOBALS['we_doc'];
			default :
				return $GLOBALS['WE_MAIN_DOC'];
		}
	} else {
		switch ($docAttr) {
			case 'top' :
				return $GLOBALS['WE_MAIN_DOC'];
			default :
				return $GLOBALS['we_doc'];
		}
	}
}

/*  **************************************************
    *	we:tags										 *
/*  **************************************************/

//
// Tags for the Sidebar
//

function we_tag_ifSidebar($attribs, $content){
	return defined('WE_SIDEBAR');
}

function we_tag_ifNotSidebar($attribs, $content){
	return !we_tag('ifSidebar',$attribs, $content);
}

//
// /End Tags for the Sidebar
//


function we_tag_ifDeleted($attribs, $content){
	$type = we_getTagAttribute('type', $attribs, 'document');
	return isset($GLOBALS['we_' . $type . '_delete_ok']) && ($GLOBALS['we_' . $type . '_delete_ok'] == true);
}

function we_tag_ifDemo($attribs, $content){
	return !defined('UID');
}

function we_tag_ifEditmode($attribs, $content){
	global $we_editmode, $WE_MAIN_EDITMODE, $we_doc, $WE_MAIN_DOC;
	$doc = we_getTagAttribute('doc', $attribs);
	switch ($doc) {
		case 'self' :
			return $WE_MAIN_DOC == $we_doc && $we_editmode;
		default :
			return $we_editmode || $WE_MAIN_EDITMODE/* || (isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem')*/;
	}
}

function we_tag_ifSeeMode($attribs, $content){	
	if (we_tag('ifWebEdition',$attribs, $content)) {
		return (isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem');
	} else {
		return false;
	}
}

function we_tag_ifTop($attribs, $content){
	return ($GLOBALS['WE_MAIN_DOC'] == $GLOBALS['we_doc']);
}

function we_tag_ifNotSeeMode($attribs, $content){
	if (we_tag('ifWebEdition',$attribs, $content)) {
		return !(we_tag('ifSeeMode',$attribs, $content));
	} else {
		return true;
	}
}

function we_tag_ifEmpty($attribs, $content){
	$foo = attributFehltError($attribs, 'match', 'ifEmpty');
	if ($foo) {
		print($foo);
		return '';
	}
	if (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) {
		return true;
	}
	return !we_isNotEmpty($attribs);
}

function we_tag_ifFieldEmpty($attribs, $content){
	global $we_editmode;
	$foo = attributFehltError($attribs, 'match', 'ifFieldNotEmpty');
	if ($foo) {
		print($foo);
		return '';
	}
	return !we_isFieldNotEmpty($attribs);

}

function we_tag_ifFieldNotEmpty($attribs, $content){
	global $we_editmode;
	
	$foo = attributFehltError($attribs, 'match', 'ifFieldNotEmpty');
	if ($foo) {
		print($foo);
		return '';
	}
	
	return we_isFieldNotEmpty($attribs);

}

function we_tag_ifNotField($attribs, $content){
	return !we_tag('ifField',$attribs, $content);
}

function we_tag_ifFound($attribs, $content){
	return $GLOBALS['lv']->anz ? true : false;
}

function we_tag_ifIsDomain($attribs, $content){
	global $we_editmode;
	$foo = attributFehltError($attribs, 'domain', 'ifIsDomain');
	if ($foo) {
		print($foo);
		return '';
	}
	$domain = we_getTagAttribute('domain', $attribs);
	return $we_editmode || ($domain == $_SERVER['SERVER_NAME']);
}

function we_tag_ifIsNotDomain($attribs, $content){
	global $we_editmode;
	$foo = attributFehltError($attribs, 'domain', 'ifIsNotDomain');
	if ($foo) {
		print($foo);
		return '';
	}
	$domain = we_getTagAttribute('domain', $attribs);
	return $we_editmode || (!($domain == $_SERVER['SERVER_NAME']));
}

function we_tag_ifLastCol($attribs, $content){
	return $GLOBALS['lv']->shouldPrintEndTR();
}

function we_tag_ifNew($attribs, $content){
	$type = we_getTagAttribute('type', $attribs);
	return !(isset($_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']) && $_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']);
}

function we_tag_ifNext($attribs, $content){
	if (isset($GLOBALS['_we_voting_list']))
		return $GLOBALS['_we_voting_list']->hasNextPage();
	$useparent = we_getTagAttribute('useparent', $attribs, '', true);
	return $GLOBALS['lv']->hasNextPage($useparent);
}

function we_tag_ifNoJavaScript($attribs, $content){
	$foo = attributFehltError($attribs, 'id', 'ifNoJavaScript');
	if ($foo) {
		print($foo);
		return '';
	}
	$id = we_getTagAttribute('id', $attribs);
	$row = getHash('SELECT Path,IsFolder,IsDynamic FROM ' . FILE_TABLE . ' WHERE ID='.abs($id), new DB_WE());
	$url = $row['Path'] . ($row['IsFolder'] ? '/' : '');
	$attr = we_make_attribs($attribs, 'id');
	return '<noscript><meta http-equiv="refresh" content="0;URL=' . $url . '"></noscript>';
}

function we_tag_ifNotCat($attribs, $content){
	return !we_tag('ifCat',$attribs, $content);
}

function we_tag_ifNotCaptcha($attribs, $content){
	return !we_tag('ifCaptcha',$attribs, $content);
}

function we_tag_ifNotDeleted($attribs, $content){
	$type = we_getTagAttribute('type', $attribs, 'document');
	return isset($GLOBALS['we_' . $type . '_delete_ok']) && ($GLOBALS['we_' . $type . '_delete_ok'] == false);
}

function we_tag_ifNotDoctype($attribs,$content){
	return !we_tag('ifDoctype',$attribs,$content);
}

function we_tag_ifNotEditmode($attribs, $content) {
	return !we_tag('ifEditmode', $attribs, $content);
}

function we_tag_ifNotEmpty($attribs, $content){
	$foo = attributFehltError($attribs, 'match', 'ifNotEmpty');
	if ($foo) {
		print($foo);
		return '';
	}
	if (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) {
		return true;
	}
	return we_isNotEmpty($attribs);
}

function we_tag_ifNotEqual($attribs, $content){
	return !we_tag('ifEqual',$attribs, $content);
}

function we_tag_ifNotFound($attribs, $content){
	return $GLOBALS['lv']->anz ? false : true;
}

function we_tag_ifNotObject($attribs,$content) {
	return !we_tag('ifObject',$attribs, $content);
}

function we_tag_ifNotObjectLanguage($attribs, $content){
	return !we_tag('ifObjectLanguage',$attribs, $content);
}

function we_tag_ifNotPageLanguage($attribs, $content){
	return !(we_tag('ifPageLanguage',$attribs, $content));
}

function we_tag_ifNotHasShopVariants($attribs,$content) {
	return !we_tag('ifHasShopVariants',$attribs,$content);
}

function we_tag_ifNotSendMail($attribs, $content){
	return !(we_tag('ifSendMail',$attribs, $content));
}

function we_tag_ifNotVoteActive($attribs,$content) {
	return !we_tag('ifVoteActive',$attribs, $content);
}

function we_tag_ifNotVoteIsRequired($attribs,$content) {
	return !we_tag('ifVoteIsRequired',$attribs, $content);
}


function we_tag_ifNotHasChildren($attribs = array(), $content = ''){
	return !we_tag('ifHasChildren',$attribs,$content);
}

function we_tag_ifNotHasEntries($attribs = array(), $content = ''){
	return !we_tag('ifHasEntries',$attribs,$content);
}

function we_tag_ifNotHasCurrentEntry($attribs = array(), $content = ''){
	return !we_tag('ifHasCurrentEntry',$attribs,$content);
}

function we_tag_ifNotNew($attribs, $content){
	$type = we_getTagAttribute('type', $attribs,$content);
	return (isset($_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']) && $_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']);
}

function we_tag_ifNotReturnPage($attribs, $content){
	return !(isset($_REQUEST['we_returnpage']) && ($_REQUEST['we_returnpage']));
}

function we_tag_ifNotSearch($attribs, $content){
	return !we_tag('ifSearch',$attribs, $content);
}

function we_tag_ifNotSelf($attribs, $content){
	return !we_tag('ifSelf',$attribs, $content);
}

function we_tag_ifNotTop($attribs, $content){
	return !we_tag('ifTop',$attribs, $content);
}

function we_tag_ifNotTemplate($attribs, $content){
	return !we_tag('ifTemplate',$attribs, $content);
}

function we_tag_ifNotVar($attribs, $content){
	return !we_tag('ifVar',$attribs, $content);
}

function we_tag_ifNotVarSet($attribs, $content){
	$foo = attributFehltError($attribs, 'name', 'ifNotVarSet');
	if ($foo) {
		print($foo);
		return '';
	}
	$type = we_getTagAttribute('var', $attribs);
	$type = $type ? $type : we_getTagAttribute('type', $attribs);
	$doc = we_getTagAttribute('doc', $attribs);
	$name = we_getTagAttribute('name', $attribs);
	$formname = we_getTagAttribute('formname', $attribs, 'we_global_form');
	$property = we_getTagAttribute('property', $attribs, '', true);
	$shopname = we_getTagAttribute('shopname', $attribs, '');
	
	return !we_isVarSet($name, $type, $doc, $property, $formname, $shopname);
}

function we_tag_ifNotVotingField($attribs,$content) {
	return !we_tag('ifVotingField',$attribs, $content);
}

function we_tag_ifShopFieldNotEmpty($attribs,$content) {
	return !we_tag('ifShopFieldEmpty',$attribs, $content);
}

function we_tag_ifVotingFieldNotEmpty($attribs,$content) {
	return !we_tag('ifVotingFieldEmpty',$attribs, $content);
}

function we_tag_ifNotWebEdition($attribs, $content){
	return !we_tag('ifWebEdition',$attribs, $content);
}

function we_tag_ifNotWorkspace($attribs, $content){
	return !we_tag('ifWorkspace',$attribs, $content);
}

function we_tag_ifNotWritten($attribs, $content){
	$type = we_getTagAttribute('type', $attribs, '');
	$type = $type ? $type : we_getTagAttribute('var', $attribs, '');
	$type = $type ? $type : we_getTagAttribute('doc', $attribs, 'document');
	return isset($GLOBALS['we_' . $type . '_write_ok']) && ($GLOBALS['we_' . $type . '_write_ok'] == false);
}


function we_tag_ifNotPosition($attribs, $content){
	return !we_tag('ifPosition',$attribs, $content);
}

function we_tag_pagelogger($attribs, $content){
	return we_tag('tracker',$attribs, $content);
}

function we_tag_ifReturnPage($attribs, $content){
	return isset($_REQUEST['we_returnpage']) && ($_REQUEST['we_returnpage']);
}

function we_tag_ifUserInputEmpty($attribs, $content){
	$foo = attributFehltError($attribs, 'match', 'ifUserInputEmpty');
	if ($foo) {
		print($foo);
		return '';
	}
	return !we_isUserInputNotEmpty($attribs);
}

function we_tag_ifUserInputNotEmpty($attribs, $content){
	$foo = attributFehltError($attribs, 'match', 'ifUserInputNotEmpty');
	if ($foo) {
		print($foo);
		return '';
	}
	return we_isUserInputNotEmpty($attribs);
}


function we_tag_ifVarEmpty($attribs, $content){
	$foo = attributFehltError($attribs, 'match', 'ifVarEmpty');
	if ($foo) {
		print($foo);
		return '';
	}
	return !we_isVarNotEmpty($attribs);
}

function we_tag_ifVarNotEmpty($attribs, $content){
	$foo = attributFehltError($attribs, 'match', 'ifVarNotEmpty');
	if ($foo) {
		print($foo);
		return '';
	}
	return we_isVarNotEmpty($attribs);
}

function we_tag_ifWebEdition($attribs, $content){
	return $GLOBALS['WE_MAIN_DOC']->InWebEdition;
}

function we_tag_ifWritten($attribs, $content){
	$type = we_getTagAttribute('type', $attribs, '');
	$type = $type ? $type : we_getTagAttribute('var', $attribs, 'document');
	return isset($GLOBALS['we_' . $type . '_write_ok']) && ($GLOBALS['we_' . $type . '_write_ok'] == true);
}


function we_tag_linkToSEEM($attribs, $content){
	return we_tag('linkToSeeMode',$attribs, $content);
}

function we_tag_listviewPageNr($attribs, $content){
	return $GLOBALS['lv']->rows ? (((abs($GLOBALS['lv']->start) - abs($GLOBALS['lv']->offset)) / $GLOBALS['lv']->rows) + 1) : 1;
}

function we_tag_listviewPages($attribs, $content){
	return $GLOBALS['lv']->rows ? ceil(
			((float)$GLOBALS['lv']->anz_all - abs($GLOBALS['lv']->offset)) / (float)$GLOBALS['lv']->rows) : 1;
}

function we_tag_listviewRows($attribs, $content){
	return $GLOBALS['lv']->anz_all - abs($GLOBALS['lv']->offset);
}

function we_tag_listviewStart($attribs, $content){
	return $GLOBALS['lv']->start + 1 - abs($GLOBALS['lv']->offset);
}

function we_tag_makeMail($attribs, $content){
	return '';
}

function we_tag_ifshopexists($attribs, $content) {
	return defined("SHOP_TABLE");
}

function we_tag_ifobjektexists($attribs, $content) {
	return defined("OBJECT_TABLE");
}

function we_tag_ifnewsletterexists($attribs, $content) {
	return defined("NEWSLETTER_TABLE");
}

function we_tag_ifcustomerexists($attribs, $content) {
	return defined("CUSTOMER_TABLE");
}

function we_tag_ifbannerexists($attribs, $content) {
	return defined("BANNER_TABLE");
}

function we_tag_ifvotingexists($attribs, $content) {
	return defined("VOTING_TABLE");
}

//FIXME: remove in next Versions
function include_all_we_tags(){
	if(defined('INCLUDE_ALL_WE_TAGS') && INCLUDE_ALL_WE_TAGS){
		$taginclude= array('DID','a','author','back','block','calculate','category','categorySelect','checkForm','condition','css','date','dateSelect',
			'delete','description','docType','field','flashmovie','formfield','hidden','href','icon','ifBack','ifCaptcha','ifCat','ifClient',
			'ifCurrentDate','ifDoctype','ifEqual','ifField','ifHasChildren','ifHasCurrentEntry','ifHasEntries','ifNotShopField','ifPosition',
			'ifSearch','ifSelf','ifRegisteredUserCanChange','ifShopField','ifShopFieldEmpty','ifTemplate',
			'ifVar','ifVarSet','ifWorkspace','img','input','js','keywords','link','linkToSeeMode',
			'linklist','list','listdir','listviewEnd','navigation','navigationEntries','navigationEntry',
			'navigationField','navigationWrite','next','options','path','position','printVersion','processDateSelect',
			'quicktime','registeredUser','returnPage','search','select','sendMail','sessionStart',
			'setVar','sidebar','textarea','title','tracker','url','userInput','var','write','xmlfeed'
		);
		foreach($taginclude AS $fn){
			$file=$_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/we_tag_'.$fn.'.inc.php';
			if(!function_exists($fn) && is_file($file)){
				include_once ($file);
			}
		}
	}
}