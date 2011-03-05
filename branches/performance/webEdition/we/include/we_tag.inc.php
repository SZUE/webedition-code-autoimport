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
}

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_html_tools.inc.php');


include_once $_SERVER['DOCUMENT_ROOT'].'/webEdition/lib/we/core/autoload.php';

include_once (WE_USERS_MODULE_DIR . 'we_users_util.php');

function we_tag($name, $attribs=array(), $content = ''){
	$fn = "we_tag_$name";
	$attribs = removeAttribs($attribs, array(
		'cachelifetime'
	));

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
	if (function_exists($fn)||$fn=='we_tag_noCache') {
		// do noting
	} else {
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_tags/we_tag_$name.inc.php")) {
			include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_tags/we_tag_$name.inc.php");
		
		} else{
				if (file_exists(
						$_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/custom_tags/' . "we_tag_$name.inc.php")) {
					include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tags/custom_tags/' . "we_tag_$name.inc.php");
				} else {
					include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/tools/weToolLookup.class.php');
					$toolinc = '';
					if (weToolLookup::getToolTag($name, $toolinc,true)) {
						include_once ($toolinc);
					} else {
						if (strpos(trim($name), 'if') === 0) { // this ifTag does not exist
							print parseError(sprintf(g_l('parser','[tag_not_known]'), trim($name)));
							return false;
						}
						return parseError(sprintf(g_l('parser','[tag_not_known]'), trim($name)));
					}

				}
		}
	}

	$foo = '';

	if ($fn == 'we_tag_setVar') {
		$fn($attribs, $content);
	}else if ($fn == 'we_tag_noCache') {
		$foo = eval('?>' . $content);
	} else {
		$foo = $fn($attribs, $content);
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

	$foo = g_l('date','[day][short]['.date('w', $t).']');
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%0%%%', $foo, $format);
	$foo = g_l('date','[month][long]['.(date('n', $t) - 1).']');
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%1%%%', $foo, $format);
	$foo = g_l('date','[day][long]['.date('w', $t).']');
	$foo = ereg_replace('([a-zA-Z])', '\\\1', $foo);
	$format = str_replace('%%%2%%%', $foo, $format);
	$foo = g_l('date','[month][short]['.(date('n', $t) - 1).']');
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
function we_tag_ifSidebar($attribs, $content){
	return defined('WE_SIDEBAR');
}

function we_tag_ifNotSidebar($attribs, $content){
	return !we_tag('ifSidebar',$attribs, $content);
}

function we_tag_ifDemo($attribs, $content){
	return !defined('UID');
}

function we_tag_ifSeeMode($attribs, $content){
	if (we_tag('ifWebEdition',$attribs, $content)) {
		return (isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem');
	} else {
		return false;
	}
}

function we_tag_ifTdEmpty($attribs, $content){
	return $GLOBALS['lv']->tdEmpty();
}

function we_tag_ifTdNotEmpty($attribs, $content){
	return !we_tag('ifTdEmpty',$attribs, $content);
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

function we_tag_ifFieldNotEmpty($attribs, $content){
	return !we_tag('ifFieldEmpty',$attribs,$content);
}

function we_tag_ifNotField($attribs, $content){
	return !we_tag('ifField',$attribs, $content);
}

function we_tag_ifFound($attribs, $content){
	return $GLOBALS['lv']->anz ? true : false;
}

function we_tag_ifIsNotDomain($attribs, $content){
	return (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) || !we_tag('ifIsDomain',$attribs);
}

function we_tag_ifLastCol($attribs, $content){
	return (isset($GLOBALS['lv'])) && $GLOBALS['lv']->shouldPrintEndTR();
}

function we_tag_ifNew($attribs, $content){
	$type = we_getTagAttribute('type', $attribs);
	return !(isset($_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']) && $_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']);
}

function we_tag_ifNotCat($attribs, $content){
	return !we_tag('ifCat',$attribs, $content);
}

function we_tag_ifNotCaptcha($attribs, $content){
	return !we_tag('ifCaptcha',$attribs, $content);
}

function we_tag_ifNotDeleted($attribs, $content){
	return !we_tag('ifDeleted',$attribs, $content);
}

function we_tag_ifNotDoctype($attribs,$content){
	return !we_tag('ifDoctype',$attribs,$content);
}

function we_tag_ifNotEditmode($attribs, $content) {
	return !we_tag('ifEditmode', $attribs, $content);
}

function we_tag_ifNotEmpty($attribs, $content){
	return (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode'])||!we_tag('ifEmpty',$attribs);
}

function we_tag_ifNotEqual($attribs, $content){
	return !we_tag('ifEqual',$attribs, $content);
}

function we_tag_ifNotFound($attribs, $content){
	return !we_tag('ifFound',$attribs,$content);
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
	return !we_tag('ifReturnPage',$attribs,$content);
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
	return !we_tag('ifVarSet',$attribs);
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

function we_tag_ifUserInputNotEmpty($attribs, $content){
	return !we_tag('ifUserInputEmpty',$attribs);
}

function we_tag_ifVarNotEmpty($attribs, $content){
	return !we_tag('ifVarEmpty',$attribs);
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


//FIXME: remove in next Versions
function include_all_we_tags(){
	if(defined('INCLUDE_ALL_WE_TAGS') && INCLUDE_ALL_WE_TAGS){
		$taginclude= array('DID','a','author','back','block','calculate','category','categorySelect','checkForm','condition','css','date','dateSelect',
			'delete','description','docType','field','flashmovie','formfield','hidden','href','icon','ifBack','ifCaptcha','ifCat','ifClient',
			'ifCurrentDate','ifDoctype','ifEqual','ifField','ifHasChildren','ifHasCurrentEntry','ifHasEntries','ifNotShopField','ifPosition',
			'ifSearch','ifSelf','ifRegisteredUserCanChange','ifShopField','ifShopFieldEmpty','ifTemplate',
			'ifDeleted','ifEditmode','ifEmpty','ifFieldEmpty','ifIsDomain','ifNext','ifNoJavaScript','ifUserInputEmpty','ifVarEmpty',
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
