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
function we_parse_tag_include($attribs, $c, array $attr){
	$type = weTag_getParserAttribute('type', $attr, 'document');
	if($type === 'template'){
		$attr['_parsed'] = 'true';
	}
	return ($type !== 'template' ?
			'<?php eval(' . we_tag_tagParser::printTag('include', $attribs) . ');?>' : //include documents
			//(($path ?
			'<?php if(($we_inc=' . we_tag_tagParser::printTag('include', $attr) . ')){include' . (weTag_getParserAttribute('once', $attr, false, true) ? '_once' : '') . '($we_inc);}; ?>'//include templates of ID's
		);
}

function we_setBackVar($we_unique){
	$GLOBALS['we']['backVars'][$we_unique] = array(
		'we_doc' => clone($GLOBALS['we_doc']),
		'GLOBAL' => array(
			'WE_IS_DYN' => isset($GLOBALS['WE_IS_DYN']) ? 1 : 0,
			'WE_DOC_ID' => $GLOBALS['WE_DOC_ID'],
			'WE_DOC_ParentID' => $GLOBALS['WE_DOC_ParentID'],
			'WE_DOC_Path' => $GLOBALS['WE_DOC_Path'],
			'WE_DOC_IsDynamic' => $GLOBALS['WE_DOC_IsDynamic'],
			'WE_DOC_FILENAME' => $GLOBALS['WE_DOC_FILENAME'],
			'WE_DOC_Category' => $GLOBALS['WE_DOC_Category'],
			'WE_DOC_EXTENSION' => $GLOBALS['WE_DOC_EXTENSION'],
			'TITLE' => $GLOBALS['TITLE'],
			'KEYWORDS' => $GLOBALS['KEYWORDS'],
			'DESCRIPTION' => $GLOBALS['DESCRIPTION'],
			'we_cmd' => we_base_request::_(we_base_request::RAW, 'we_cmd', ''),
			'FROM_WE_SHOW_DOC' => isset($GLOBALS['FROM_WE_SHOW_DOC']) ? $GLOBALS['FROM_WE_SHOW_DOC'] : '',
			'we_transaction' => isset($GLOBALS['we_transaction']) ? $GLOBALS['we_transaction'] : '',
			'we_editmode' => isset($GLOBALS['we_editmode']) ? $GLOBALS['we_editmode'] : null,
			'we_ContentType' => isset($GLOBALS['we_ContentType']) ? $GLOBALS['we_ContentType'] : we_base_ContentTypes::WEDOCUMENT,
			'postTagName' => isset($GLOBALS['postTagName']) ? $GLOBALS['postTagName'] : '',
		),
		'REQUEST' => array(
			'we_cmd' => we_base_request::_(we_base_request::RAW, 'we_cmd', ''),
	));

	if(isset($GLOBALS['WE_IS_DYN'])){
		unset($GLOBALS['WE_IS_DYN']);
	}
	if(isset($GLOBALS['postTagName'])){
		unset($GLOBALS['postTagName']);
	}
}

function we_resetBackVar($we_unique){
	/* 	if(!is_object($GLOBALS['we']['backVars'][$we_unique]['we_doc'])){
	  t_e($we_unique, $GLOBALS['we']['backVars']);
	  return;
	  } */
	$GLOBALS['we_doc'] = clone($GLOBALS['we']['backVars'][$we_unique]['we_doc']);
	foreach($GLOBALS['we']['backVars'][$we_unique]['GLOBAL'] as $key => $val){
		$GLOBALS[$key] = $val;
	}
	foreach($GLOBALS['we']['backVars'][$we_unique]['REQUEST'] as $key => $val){
		$_REQUEST[$key] = $val;
	}

	if($GLOBALS['we']['backVars'][$we_unique]['GLOBAL']['WE_IS_DYN']){
		$GLOBALS['WE_IS_DYN'] = 1;
	} else if(isset($GLOBALS['WE_IS_DYN'])){
		unset($GLOBALS['WE_IS_DYN']);
	}
	unset($GLOBALS['we']['backVars'][$we_unique]);
}

function we_tag_include($attribs){//FIXME: include doesn't work in editmode - check funktionen
	$id = intval(weTag_getAttribute('id', $attribs, 0, we_base_request::INT));
	$path = weTag_getAttribute('path', $attribs, '', we_base_request::FILE);

	if(weTag_getAttribute('type', $attribs, '', we_base_request::STRING) === 'template'){
		if(!isset($attribs['_parsed'])){
			echo 'cannot use we:include with type="template" dynamically';
			return '';
		}
		$ret = preg_replace('/.tmpl$/i', '.php', ($id ? id_to_path($id, TEMPLATES_TABLE) : str_replace('..', '', $path))); //filter rel. paths
		return ($ret && $ret != '/' ? TEMPLATES_PATH . $ret : '');
	}

	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$gethttp = weTag_getAttribute('gethttp', $attribs, false, we_base_request::BOOL);
	$seeMode = weTag_getAttribute((isset($attribs['seem']) ? 'seem' : 'seeMode'), $attribs, true, we_base_request::BOOL);
	$once = weTag_getAttribute('once', $attribs, false, we_base_request::BOOL);

	if((!$id) && (!$path) && (!$name)){
		t_e('we:include - missing id, path or name');
		echo '<!-- we:include - missing id, path or name !!-->';
		return '';
	}

	if(we_tag('ifEditmode')){
		if($name && !($id || $path)){
			$type = weTag_getAttribute('kind', $attribs, we_base_link::TYPE_ALL, we_base_request::STRING);
			$_name = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
			$description = weTag_getAttribute('description', $attribs, g_l('tags', '[include_file]'), we_base_request::RAW);

			echo '<table class="weEditTable" style="background: #006DB8;border:0px;padding:0px;"><tr><td style="padding: 3px;color:white;">' . '&nbsp;' . $description . '</td></tr><tr><td>' .
			we_tag('href', array('name' => $_name, 'rootdir' => weTag_getAttribute('rootdir', $attribs, '/', we_base_request::FILE), 'startid' => weTag_getAttribute('startid', $attribs, 0, we_base_request::INT), 'type' => $type, 'size' => weTag_getAttribute('size', $attribs, 50, we_base_request::UNIT))) .
			'</td></tr></table>';
			return '';
		}
	} else //notEditmode
	if($name && !($id || $path)){
		$type = weTag_getAttribute('kind', $attribs, we_base_link::TYPE_ALL, we_base_request::STRING);
		$_name = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
		$path = we_tag('href', array('name' => $_name, 'hidedirindex' => 'false', 'type' => $type, 'isInternal' => 1));
		$nint = $name . we_base_link::MAGIC_INT_LINK;
		$int = ($GLOBALS['we_doc']->getElement($nint) == '') ? 0 : $GLOBALS['we_doc']->getElement($nint);
		$intID = $GLOBALS['we_doc']->getElement($nint . 'ID');
		if($int && $intID){
			$ct = f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id) . ' AND Published>0');
		}
	}

	if(!$id && !$path){
		return '';
	}

	if($id){
		if($GLOBALS['WE_MAIN_DOC']->ID == $id || //don't include same id
			$GLOBALS['we_doc']->ContentType != we_base_ContentTypes::WEDOCUMENT //don't include any unknown document
		){
			return '';
		}
		$tmp = getHash('SELECT Path,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id) . ' AND Published>0', null);
		$realPath = $tmp ? $tmp['Path'] : '';
		$ct = $tmp ? $tmp['ContentType'] : '';
	} else {
		$realPath = $path;
	}
	if(!$realPath){
		return '';
	}

	$isSeemode = (we_tag('ifSeeMode'));
	// check early if there is a document - if not the rest is never needed
	if($gethttp){
		$content = /* ($isSeemode ? getHTTP(getServerUrl(true), $realPath) : */ 'echo getHTTP(getServerUrl(true), \'' . $realPath . '\');'/* )' */;
	} else {
		$realPath = $_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . '..' . $realPath; //(symlink) webEdition always points to the REAL DOC-Root!
		if(!file_exists($realPath) || !is_file($realPath)){
			//t_e('include of', 'id:' . $id . ',path:' . $path . ',name:' . $name, ' doesn\'t exist');
			return '';
		}
		//check Customer-Filter on static documents
		$id = intval($id ? : (isset($intID) ? $intID : 0));
		if(defined('CUSTOMER_TABLE') && $id){
			$filter = we_customer_documentFilter::getFilterByIdAndTable($id, FILE_TABLE, $GLOBALS['DB_WE']);

			if(is_object($filter)){
				if($filter->accessForVisitor($intID, $ct, true) != we_customer_documentFilter::ACCESS){
					return '';
				}
			}
		}
		$content = /* ($isSeemode ? file_get_contents($realPath) : */ 'include' . ($once ? '_once' : '') . '(\'' . $realPath . '\');'/* ) */;
	}

	if(isset($GLOBALS['we']['backVars']) && count($GLOBALS['we']['backVars'])){
		end($GLOBALS['we']['backVars']);
		$we_unique = key($GLOBALS['we']['backVars']) + 1;
		$GLOBALS['we']['backVars'][$we_unique] = array();
	} else {
		$we_unique = 1;
		$GLOBALS['we']['backVars'] = array(
			$we_unique => array()
		);
	}

	return 'we_setBackVar(' . $we_unique . ');' .
		$content .
		($isSeemode && $seeMode && ($id || $path) ? 'echo \'' . we_SEEM::getSeemAnchors(($id ? : path_to_id($path)), 'include') . '\';' : '') .
		'we_resetBackVar(' . $we_unique . ');';
}
