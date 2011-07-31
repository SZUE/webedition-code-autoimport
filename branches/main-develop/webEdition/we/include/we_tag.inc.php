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
if (!isset($GLOBALS['WE_IS_DYN'])) {
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/SEEM/we_SEEM.class.php');
}

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_html_tools.inc.php');
include_once $_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/core/autoload.php';
include_once (WE_USERS_MODULE_DIR . 'we_users_util.php');

function we_include_tag_file($name) {
	$fn = 'we_tag_' . $name;

	// as default: all tag_functions are in this file.
	if (function_exists($fn) || $fn == 'we_tag_noCache') {
		// do noting
		return true;
	} else {
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/we_tag_' . $name . '.inc.php')) {
			include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/we_tag_' . $name . '.inc.php');
			return true;
		} else {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/custom_tags/we_tag_' . $name . '.inc.php')) {
				include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/custom_tags/we_tag_' . $name . '.inc.php');
				return true;
			} else {
				include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/tools/weToolLookup.class.php');
				$toolinc = '';
				if (weToolLookup::getToolTag($name, $toolinc, true)) {
					include_once ($toolinc);
					return true;
				} else {
					if (strpos(trim($name), 'if') === 0) { // this ifTag does not exist
						print parseError(sprintf(g_l('parser', '[tag_not_known]'), trim($name)));
						return false;
					}
					return parseError(sprintf(g_l('parser', '[tag_not_known]'), trim($name)));
				}
			}
		}
	}
	return false;
}

function we_tag($name, $attribs=array(), $content = '') {
	$nameTo = we_getTagAttribute("nameto", $attribs);
	$to = we_getTagAttribute("to", $attribs, 'screen');
	//make sure comment attribute is never shown
	if ($name == 'setVar') {//special handling inside this tag
		$attribs = removeAttribs($attribs, array('cachelifetime', 'comment'));
		$nameTo = '';
		$to = 'screen';
	} else {
		$nameTo = we_getTagAttribute("nameto", $attribs);
		$to = we_getTagAttribute("to", $attribs, 'screen');
		$attribs = removeAttribs($attribs, array('cachelifetime', 'comment', 'to', 'nameto'));
	}

	//make a copy of the name - this copy is never touched even not inside blocks/listviews etc.
	if (isset($attribs['name'])) {
		$attribs['_name_orig'] = $attribs['name'];
		if (isset($GLOBALS['postTagName'])) {
			$attribs['name'] = $attribs['name'] . $GLOBALS['postTagName'];
		}
	}
	//FIXME: after changing block etc. this is obsolete
	if ($content) {
		$content = str_replace('we_:_', 'we:', $content);
	}


	$edMerk = isset($GLOBALS['we_editmode']) ? $GLOBALS['we_editmode'] : false;
	if ($edMerk) {
		if (isset($attribs['user']) && $attribs['user']) {
			$uAr = makeArrayFromCSV($attribs['user']);
			$userIds = array();
			foreach ($uAr as $u) {
				$i = f("SELECT ID FROM " . USER_TABLE . " WHERE Username='" . $GLOBALS['DB_WE']->escape($u) . "'", "ID", $GLOBALS["DB_WE"]);
				if ($i) {
					array_push($userIds, $i);
				}
			}
			if (!isUserInUsers($_SESSION['user']['ID'], $userIds) && (!$_SESSION['perms']['ADMINISTRATOR'])) {
				$GLOBALS['we_editmode'] = false;
			}
		}
	}

	if (($foo = we_include_tag_file($name)) !== true) {
		return $foo;
	}

	$fn = 'we_tag_' . $name;
	$foo = '';
	switch ($fn) {
		case 'we_tag_setVar':
			$fn($attribs, $content);
			break;
		default:
			$foo = $fn($attribs, $content);
	}

	$GLOBALS['we_editmode'] = $edMerk;
	return ($edMerk ?
					$foo :
					we_redirect_tagoutput($foo, $nameTo, $to));
}

### tag utility functions ###

function we_redirect_tagoutput($returnvalue, $nameTo, $to='screen') {
	if (isset($GLOBALS['calculate'])) {
		$to = 'calculate';
	}
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
			if (isset($_SESSION['webuser'][$nameTo])) {
				$_SESSION['webuser'][$nameTo] = $returnvalue;
			}
			break;
		case 'calculate':
			return we_util::std_numberformat($returnvalue);
			break;
		case 'screen':
		default:
			return $returnvalue;
	}
	return null;
}

function mta($hash, $key) {
	return (isset($hash[$key]) && ($hash[$key] != '' || $key == 'alt')) ? (' ' . $key . '="' . $hash[$key] . '"') : '';
}

function printElement($code) {
	if (isset($code)) {
		eval('?>' . str_replace('<?php', '<?php ', str_replace('?>', ' ?>', $code)));
	}
}

function makeEmptyTable($in) {
	preg_match_all('/<[^>]+>/i', $in, $result, PREG_SET_ORDER);

	$out = '';
	foreach ($result as $res) {
		$tag = $res[0];

		if (preg_match('-< ?/? ?(td|tr|table|tbody)-i', $tag)) {
			$out .= $tag;
		}
	}
	return $out;
}

function we_cmpText($a, $b) {
	$x = strtolower(correctUml($a['properties']['Text']));
	$y = strtolower(correctUml($b['properties']['Text']));
	if ($x == $y) {
		return 0;
	}
	return ($x < $y) ? -1 : 1;
}

function we_cmpTextDesc($a, $b) {
	$x = strtolower(correctUml($a['properties']['Text']));
	$y = strtolower(correctUml($b['properties']['Text']));
	if ($x == $y) {
		return 0;
	}
	return ($x > $y) ? -1 : 1;
}

function we_cmpField($a, $b) {
	$x = strtolower(correctUml($a['sort']));
	$y = strtolower(correctUml($b['sort']));
	if ($x == $y) {
		return 0;
	}
	return ($x < $y) ? -1 : 1;
}

function we_cmpFieldDesc($a, $b) {
	$x = strtolower(correctUml($a['sort']));
	$y = strtolower(correctUml($b['sort']));
	if ($x == $y) {
		return 0;
	}
	return ($x > $y) ? -1 : 1;
}

function we_tag_path_hasIndex($path, $indexArray) {
	foreach ($indexArray as $index) {
		if (file_exists($path . $index)) {
			return true;
		}
	}
	return false;
}

function makeArrayFromAttribs($attr) {
	$attribs = '';
	preg_match_all('/([^=]+)= *("[^"]*")/', $attr, $foo, PREG_SET_ORDER);
	for ($i = 0; $i < sizeof($foo); $i++) {
		$attribs .= '"' . trim($foo[$i][1]) . '"=>' . trim($foo[$i][2]) . ',';
	}
	eval('$arr = array(' . ereg_replace('(.+),$', '\\1', $attribs) . ');');
	return $arr;
}

function cutText($text, $max = 0) {
	if ((!$max) || (strlen($text) <= $max)) {
		return $text;
	}
	if (!strlen($text)) {
		return '';
	}

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
	if (preg_match('-^(.+)(<)(a|b|em|strong|b|i|u|div|span)([ >][^<]*)$-i', $text, $regs)) {
		$text = $regs[1] . $regs[2] . $regs[3] . $regs[4] . '</' . $regs[3] . '>';
	} else
	if (preg_match('-^(.+)(<)(a|em|strong|b|i|u|br|div|span)([^>]*)$-i', $text, $regs)) {
		$text = $regs[1];
	}
	return $text . '...';
}

function arrayKeyExists($key, $search) {
	return (in_array($key, array_keys($search)));
}

function we_getDocForTag($docAttr, $maindefault = false) {
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

/* * *************************************************
 * 	we:tags										 *
  /*  ************************************************* */

function we_tag_ifSidebar($attribs, $content) {
	return defined('WE_SIDEBAR');
}

function we_tag_ifNotSidebar($attribs, $content) {
	return!we_tag('ifSidebar', $attribs, $content);
}

function we_tag_ifDemo($attribs, $content) {
	return!defined('UID');
}

function we_tag_ifSeeMode($attribs, $content) {
	if (we_tag('ifWebEdition', $attribs, $content)) {
		return (isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem');
	} else {
		return false;
	}
}

function we_tag_ifTdEmpty($attribs, $content) {
	return $GLOBALS['lv']->tdEmpty();
}

function we_tag_ifTdNotEmpty($attribs, $content) {
	return!we_tag('ifTdEmpty', $attribs, $content);
}

function we_tag_ifTop($attribs, $content) {
	return ($GLOBALS['WE_MAIN_DOC'] == $GLOBALS['we_doc']);
}

function we_tag_ifNotSeeMode($attribs, $content) {
	if (we_tag('ifWebEdition', $attribs, $content)) {
		return!(we_tag('ifSeeMode', $attribs, $content));
	} else {
		return true;
	}
}

function we_tag_ifFieldNotEmpty($attribs, $content) {
	return!we_tag('ifFieldEmpty', $attribs, $content);
}

function we_tag_ifNotField($attribs, $content) {
	return!we_tag('ifField', $attribs, $content);
}

function we_tag_ifFound($attribs, $content) {
	return isset($GLOBALS['lv'])&&$GLOBALS['lv']->anz;
}

function we_tag_ifIsNotDomain($attribs, $content) {
	return (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) || !we_tag('ifIsDomain', $attribs);
}

function we_tag_ifLastCol($attribs, $content) {
	return (isset($GLOBALS['lv'])) && $GLOBALS['lv']->shouldPrintEndTR();
}

function we_tag_ifNew($attribs, $content) {
	$type = we_getTagAttribute('type', $attribs);
	return!(isset($_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']) && $_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']);
}

function we_tag_ifNotCat($attribs, $content) {
	return!we_tag('ifCat', $attribs, $content);
}

function we_tag_ifNotCaptcha($attribs, $content) {
	return!we_tag('ifCaptcha', $attribs, $content);
}

function we_tag_ifNotDeleted($attribs, $content) {
	return!we_tag('ifDeleted', $attribs, $content);
}

function we_tag_ifNotDoctype($attribs, $content) {
	return!we_tag('ifDoctype', $attribs, $content);
}

function we_tag_ifNotEditmode($attribs, $content) {
	return!we_tag('ifEditmode', $attribs, $content);
}

function we_tag_ifNotEmpty($attribs, $content) {
	return (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) || !we_tag('ifEmpty', $attribs);
}

function we_tag_ifNotEqual($attribs, $content) {
	return!we_tag('ifEqual', $attribs, $content);
}

function we_tag_ifNotFound($attribs, $content) {
	return!we_tag('ifFound', $attribs, $content);
}

function we_tag_ifNotObject($attribs, $content) {
	return!we_tag('ifObject', $attribs, $content);
}

function we_tag_ifNotObjectLanguage($attribs, $content) {
	return!we_tag('ifObjectLanguage', $attribs, $content);
}

function we_tag_ifNotPageLanguage($attribs, $content) {
	return!(we_tag('ifPageLanguage', $attribs, $content));
}

function we_tag_ifNotHasShopVariants($attribs, $content) {
	return!we_tag('ifHasShopVariants', $attribs, $content);
}

function we_tag_ifNotSendMail($attribs, $content) {
	return!(we_tag('ifSendMail', $attribs, $content));
}

function we_tag_ifNotVoteActive($attribs, $content) {
	return!we_tag('ifVoteActive', $attribs, $content);
}

function we_tag_ifNotVoteIsRequired($attribs, $content) {
	return!we_tag('ifVoteIsRequired', $attribs, $content);
}

function we_tag_ifNotHasChildren($attribs = array(), $content = '') {
	return!we_tag('ifHasChildren', $attribs, $content);
}

function we_tag_ifNotHasEntries($attribs = array(), $content = '') {
	return!we_tag('ifHasEntries', $attribs, $content);
}

function we_tag_ifNotHasCurrentEntry($attribs = array(), $content = '') {
	return!we_tag('ifHasCurrentEntry', $attribs, $content);
}

function we_tag_ifNotRegisteredUser($attribs, $content) {
	return!we_tag('ifRegisteredUser', $attribs, $content);
}

function we_tag_ifNotNewsletterSalutation($attribs, $content) {
	return!we_tag('ifNewsletterSalutation', $attribs, "");
}

function we_tag_ifNotNew($attribs, $content) {
	$type = we_getTagAttribute('type', $attribs, $content);
	return (isset($_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']) && $_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']);
}

function we_tag_ifNotReturnPage($attribs, $content) {
	return!we_tag('ifReturnPage', $attribs, $content);
}

function we_tag_ifNotSearch($attribs, $content) {
	return!we_tag('ifSearch', $attribs, $content);
}

function we_tag_ifNotSelf($attribs, $content) {
	return!we_tag('ifSelf', $attribs, $content);
}

function we_tag_ifNotTop($attribs, $content) {
	return!we_tag('ifTop', $attribs, $content);
}

function we_tag_ifNotTemplate($attribs, $content) {
	return!we_tag('ifTemplate', $attribs, $content);
}

function we_tag_ifNotVar($attribs, $content) {
	return!we_tag('ifVar', $attribs, $content);
}

function we_tag_ifNotVarSet($attribs, $content) {
	return!we_tag('ifVarSet', $attribs);
}

function we_tag_ifNotVotingField($attribs, $content) {
	return!we_tag('ifVotingField', $attribs, $content);
}

function we_tag_ifShopFieldNotEmpty($attribs, $content) {
	return!we_tag('ifShopFieldEmpty', $attribs, $content);
}

function we_tag_ifVotingFieldNotEmpty($attribs, $content) {
	return!we_tag('ifVotingFieldEmpty', $attribs, $content);
}

function we_tag_ifNotWebEdition($attribs, $content) {
	return!we_tag('ifWebEdition', $attribs, $content);
}

function we_tag_ifNotWorkspace($attribs, $content) {
	return!we_tag('ifWorkspace', $attribs, $content);
}

function we_tag_ifNotWritten($attribs, $content) {
	$type = we_getTagAttribute('type', $attribs, '');
	$type = $type ? $type : we_getTagAttribute('var', $attribs, '');
	$type = $type ? $type : we_getTagAttribute('doc', $attribs, 'document');
	return isset($GLOBALS['we_' . $type . '_write_ok']) && ($GLOBALS['we_' . $type . '_write_ok'] == false);
}

function we_tag_ifNotPosition($attribs, $content) {
	return!we_tag('ifPosition', $attribs, $content);
}

function we_tag_pagelogger($attribs, $content) {
	return we_tag('tracker', $attribs, $content);
}

function we_tag_ifReturnPage($attribs, $content) {
	return isset($_REQUEST['we_returnpage']) && ($_REQUEST['we_returnpage']);
}

function we_tag_ifUserInputNotEmpty($attribs, $content) {
	return!we_tag('ifUserInputEmpty', $attribs);
}

function we_tag_ifVarNotEmpty($attribs, $content) {
	return!we_tag('ifVarEmpty', $attribs);
}

function we_tag_ifWebEdition($attribs, $content) {
	return $GLOBALS['WE_MAIN_DOC']->InWebEdition;
}

function we_tag_ifWritten($attribs, $content) {
	$type = we_getTagAttribute('type', $attribs, '');
	$type = $type ? $type : we_getTagAttribute('var', $attribs, 'document');
	return isset($GLOBALS['we_' . $type . '_write_ok']) && ($GLOBALS['we_' . $type . '_write_ok'] == true);
}

function we_tag_linkToSEEM($attribs, $content) {
	return we_tag('linkToSeeMode', $attribs, $content);
}

function we_tag_listviewPageNr($attribs, $content) {
	return $GLOBALS['lv']->rows ? (((abs($GLOBALS['lv']->start) - abs($GLOBALS['lv']->offset)) / $GLOBALS['lv']->maxItemsPerPage) + 1) : 1;
}

function we_tag_listviewPages($attribs, $content) {
	$cols = $GLOBALS['lv']->cols ? $GLOBALS['lv']->cols : 1;
	return $GLOBALS['lv']->rows ? ceil(
									((float) $GLOBALS['lv']->anz_all - abs($GLOBALS['lv']->offset)) / ((float) $GLOBALS['lv']->maxItemsPerPage )) : 1;
}

function we_tag_listviewRows($attribs, $content) {
	return $GLOBALS['lv']->anz_all - abs($GLOBALS['lv']->offset);
}

function we_tag_listviewStart($attribs, $content) {
	return $GLOBALS['lv']->start + 1 - abs($GLOBALS['lv']->offset);
}

function we_tag_makeMail($attribs, $content) {
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

//this function is used by all tags adding elements to we_lv_array
function we_post_tag_listview() {
	if (isset($GLOBALS['we_lv_array'])) {
		array_pop($GLOBALS['we_lv_array']);
		if (count($GLOBALS['we_lv_array'])) {
			$GLOBALS['lv'] = clone($GLOBALS['we_lv_array'][count($GLOBALS['we_lv_array']) - 1]);
		} else {
			unset($GLOBALS['lv']);
			unset($GLOBALS['we_lv_array']);
		}
	}
}

//FIXME: remove in next Versions
function include_all_we_tags() {
	if (defined('INCLUDE_ALL_WE_TAGS') && INCLUDE_ALL_WE_TAGS) {
		$taginclude = array('DID', 'a', 'author', 'back', 'block', 'calculate', 'category', 'categorySelect', 'checkForm', 'condition',
				'conditionAdd', 'conditionAnd', 'conditionOr', 'css', 'date', 'dateSelect',
				'delete', 'description', 'docType', 'field', 'flashmovie', 'formfield', 'hidden', 'href', 'icon', 'ifBack', 'ifCaptcha', 'ifCat', 'ifClient',
				'ifCurrentDate', 'ifDoctype', 'ifEqual', 'ifField', 'ifHasChildren', 'ifHasCurrentEntry', 'ifHasEntries', 'ifNotShopField', 'ifPosition',
				'ifSearch', 'ifSelf', 'ifRegisteredUserCanChange', 'ifShopField', 'ifShopFieldEmpty', 'ifTemplate',
				'ifDeleted', 'ifEditmode', 'ifEmpty', 'ifFieldEmpty', 'ifIsDomain', 'ifNext', 'ifNoJavaScript', 'ifUserInputEmpty', 'ifVarEmpty',
				'ifVar', 'ifVarSet', 'ifWorkspace', 'img', 'input', 'js', 'keywords', 'link', 'linkToSeeMode',
				'linklist', 'list', 'listdir', 'listviewEnd', 'navigation', 'navigationEntries', 'navigationEntry',
				'navigationField', 'navigationWrite', 'next', 'options', 'path', 'position', 'printVersion', 'processDateSelect',
				'quicktime', 'registeredUser', 'returnPage', 'search', 'select', 'sendMail', 'sessionStart',
				'setVar', 'sidebar', 'textarea', 'title', 'tracker', 'url', 'userInput', 'var', 'write', 'xmlfeed'
		);
		foreach ($taginclude AS $fn) {
			$file = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/we_tag_' . $fn . '.inc.php';
			if (!function_exists($fn) && is_file($file)) {
				include_once ($file);
			}
		}
	}
}