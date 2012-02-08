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
function we_parse_tag_include($attribs, $content){
	return '<?php eval(' . we_tag_tagParser::printTag('include', $attribs) . ');?>';
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
				'we_cmd' => isset($_REQUEST['we_cmd']) ? $_REQUEST['we_cmd'] : '',
				'FROM_WE_SHOW_DOC' => isset($GLOBALS['FROM_WE_SHOW_DOC']) ? $GLOBALS['FROM_WE_SHOW_DOC'] : '',
				'we_transaction' => isset($GLOBALS['we_transaction']) ? $GLOBALS['we_transaction'] : '',
				'we_editmode' => isset($GLOBALS['we_editmode']) ? $GLOBALS['we_editmode'] : null,
				'we_ContentType' => isset($GLOBALS['we_ContentType']) ? $GLOBALS['we_ContentType'] : 'text/webedition',
				'postTagName'=>isset($GLOBALS['postTagName'])?$GLOBALS['postTagName']:'',
			),
			'REQUEST' => array(
				'pv_id' => isset($_REQUEST['pv_id']) ? $_REQUEST['pv_id'] : '',
				'pv_tid' => isset($_REQUEST['pv_tid']) ? $_REQUEST['pv_tid'] : '',
				'we_cmd' => isset($_REQUEST['we_cmd']) ? $_REQUEST['we_cmd'] : '',
			));

		if(isset($GLOBALS['WE_IS_DYN'])){
			unset($GLOBALS['WE_IS_DYN']);
		}
		if(isset($GLOBALS['postTagName'])){
			unset($GLOBALS['postTagName']);
		}
		unset($_REQUEST['pv_id']);
		unset($_REQUEST['pv_tid']);
}

function we_resetBackVar($we_unique){
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

function we_tag_include($attribs, $content){
	$id = weTag_getAttribute('id', $attribs);
	$path = weTag_getAttribute('path', $attribs);
	$name = weTag_getAttribute('name', $attribs);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '/');
	$gethttp = weTag_getAttribute('gethttp', $attribs, false, true);
	$seeMode = weTag_getAttribute((isset($attribs['seem']) ? 'seem' : 'seeMode'), $attribs, true, true);
	$isDynamic = true;

	if((!$id) && (!$path) && (!$name)){
		t_e('we:include - missing id, path or name');
		return '?><!-- we:include - missing id, path or name !!-->';
	}

	if(we_tag('ifEditmode', array())){
		if($name && !($id || $path)){
			$type = weTag_getAttribute('kind', $attribs);
			$_tmpspan = '<span style="color: white;font-size:' .
				(($GLOBALS['SYSTEM'] == 'MAC') ? '11px' : (($GLOBALS['SYSTEM'] == 'X11') ? '13px' : '12px')) . ';font-family:' .
				g_l('css', '[font_family]') . ';">';

			return '<table style="background: #006DB8;" border="0" cellpadding="0" cellspacing="0"><tr><td style="padding: 3px;">' . $_tmpspan . '&nbsp;' . g_l('tags', '[include_file]') . '</span></td></tr><tr><td>' .
				we_tag('href', array('name' => $name, 'rootdir' => $rootdir, 'type' => $type)) .
				'</td></tr></table>';
		}
	} else{//notEditmode
		if($name && !($id || $path)){
			$db = new DB_WE();
			$path = we_tag('href', array('name' => $name, 'rootdir' => $rootdir));
			$nint = $name . "_we_jkhdsf_int";
			$int = ($GLOBALS['we_doc']->getElement($nint) == '') ? 0 : $GLOBALS['we_doc']->getElement($nint);
			$intID = $GLOBALS['we_doc']->getElement($nint . 'ID');
			if($int && $intID){
				list($isDynamic, $ct) = getHash('SELECT IsDynamic,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . $intID . ' AND Published>0', $db);
			}
		}
	}

	if($id || $path){
		if(!(($id && ($GLOBALS['we_doc']->ContentType != 'text/webedition' || $GLOBALS['WE_MAIN_DOC']->ID != $id )) || $path != '' )){
			return '';
		}
		$db = new DB_WE();
		$realPath = '';
		if($id){
			$__id__ = ($id == '' ? '' : $id);
			$db->query('SELECT Path,IsDynamic,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id) . ' AND Published>0');
			if($db->next_record() === false){
				return '';
			}
			$realPath = $db->f('Path');
			$isDynamic = $db->f('IsDynamic');
			$ct = $db->f('ContentType');
		} else{
			$realPath = $path;
		}
		if($realPath == ''){
			return '';
		}

		/* check early if there is a document - if not the rest is never needed */
		if($gethttp){
			$content = getHTTP($_SERVER['SERVER_NAME'], $realPath, '', defined('HTTP_USERNAME') ? HTTP_USERNAME : '', defined('HTTP_PASSWORD') ? HTTP_PASSWORD : '');
		} else{
			$realPath = $_SERVER['DOCUMENT_ROOT'] . $realPath;
			//check Customer-Filter on static documents
			$id = ($id ? $id : (isset($intID) ? $intID : 0));
			if(defined('CUSTOMER_TABLE') && !$isDynamic && $id){
				$filter = weDocumentCustomerFilter::getFilterByIdAndTable($id, FILE_TABLE);

				if(is_object($filter)){
					$obj = (object) array('ID' => $id, 'ContentType' => $ct);
					if($filter->accessForVisitor($obj, array(), true) != weDocumentCustomerFilter::ACCESS){
						return '';
					}
				}
			}
			$content = @file_get_contents($realPath);
			if($content === false){
				return '';
			}
		}

		if(isset($GLOBALS['we']['backVars']) && count($GLOBALS['we']['backVars'])){
				end($GLOBALS['we']['backVars']);
				$we_unique = key($GLOBALS['we']['backVars']) + 1;
		} else{
			$we_unique = 1;
			$GLOBALS['we']['backVars'] = array();
		}
		//create empty array
		$GLOBALS['we']['backVars'][$we_unique] = array();


		if(we_tag('ifSeeMode')){
			if($seeMode){ //	only show link to seeMode, when id is given
				if($id){
					$content .= '<a href="' . $id . '" seem="include"></a>';
				}
				if($path){
					$_tmpID = path_to_id($path);
					$content .= '<a href="' . $_tmpID . '" seem="include"></a>';
				}
			}

			$content = preg_replace('|< */? *form[^>]*>|i', '', $content);
		}

		return 'we_setBackVar('.$we_unique.');'.
		'eval(\'?>' . str_replace('\'',"\'",$content).'\');'.
		'we_resetBackVar('.$we_unique.');';
	}
	return '';
}