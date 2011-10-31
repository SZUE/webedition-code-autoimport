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
function we_tag_include($attribs, $content) {
	/* 	$foo = attributFehltError($attribs, 'name', 'textarea');
	  if ($foo)
	  return $foo;
	 */

	$id = we_getTagAttribute('id', $attribs);
	$path = we_getTagAttribute('path', $attribs);
	$name = we_getTagAttribute('name', $attribs, '');
	$rootdir = we_getTagAttribute('rootdir', $attribs, '/');
	$gethttp = we_getTagAttribute('gethttp', $attribs, '', true);
	$seeMode = we_getTagAttribute((isset($attribs['seem']) ? 'seem' : 'seeMode'), $attribs, '', true, true);
	$isDynamic = true;

	if ((!$id) && (!$path) && (!$name)) {
		return '?><!-- we:include - missing id, path or name !!-->';
	}


	if (we_tag('ifEditmode', array())) {
		if ($name && !($id || $path)) {
			$type = we_getTagAttribute('kind', $attribs);
			$_tmpspan = '<span style="color: white;font-size:' .
							(($GLOBALS['SYSTEM'] == 'MAC') ? '11px' : (($GLOBALS['SYSTEM'] == 'X11') ? '13px' : '12px')) . ';font-family:' .
							$GLOBALS['l_css']['font_family'] . ';">';

			$ret = '?><table style="background: #006DB8;" border="0" cellpadding="0" cellspacing="0"><tr><td style="padding: 3px;">' . $_tmpspan . '&nbsp;' . $GLOBALS['l_tags']['include_file'] . '</span></td></tr><tr><td>';
			$ret.= we_tag('href', array('name' => $name, 'rootdir' => $rootdir, 'type' => $type));
			$ret.='</td></tr></table>';
			return $ret;
		}
	} else {//notEditmode
		if ($name && !($id || $path)) {
			$db = new DB_WE();
			$path = we_tag('href', array('name' => $name, 'rootdir' => $rootdir));
			$nint = $name . "_we_jkhdsf_int";
			$int = ($GLOBALS["we_doc"]->getElement($nint) == "") ? 0 : $GLOBALS["we_doc"]->getElement($nint);
			$intID = $GLOBALS["we_doc"]->getElement($nint.'ID');
			if($int && $intID){
				list($isDynamic,$ct) = getHash('SELECT IsDynamic,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($intID).' AND Published>0',$db);
			}
		}
	}

	if ($id || $path) {
		if (!(($id && ($GLOBALS['we_doc']->ContentType != 'text/webedition' || $GLOBALS['WE_MAIN_DOC']->ID != $id )) || $path != '' )) {
			return '';
		}
		$db = new DB_WE();
		if ($id) {
			$__id__ = ($id == '' ? '' : $id);
			$db->query('SELECT Path,IsDynamic,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id).' AND Published>0');
			if($db->next_record()===false){
				return '';
			}
			$realPath = $db->f('Path');
			$isDynamic = $db->f('IsDynamic');
			$ct = $db->f('ContentType');
		} else {
			$realPath = $path;
		}
		if ($realPath == '') {
			return '';
		}

		/*check early if there is a document - if not the rest is never needed*/
		if ($gethttp) {
			$content = getHTTP(SERVER_NAME, $realPath, '', defined('HTTP_USERNAME') ? HTTP_USERNAME : '', defined('HTTP_PASSWORD') ? HTTP_PASSWORD : '');
		} else {
			$realPath = $_SERVER['DOCUMENT_ROOT'] . $realPath;
			//check Customer-Filter on static documents
				$id=($id?$id:$intID);
			if(!$isDynamic && $id){
				include_once($_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/we_modules/customer/weDocumentCustomerFilter.class.php');

				$filter=weDocumentCustomerFilter::getFilterByIdAndTable($id,FILE_TABLE);

				if(is_object($filter)){
				$obj=(object) array('ID'=>$id,'ContentType'=>$ct);
				if($filter->accessForVisitor($obj,array(),true) != WECF_ACCESS){
					return '';
				}
			}
			}
			$content = @file_get_contents($realPath);
			if ($content === false) {
				return '';
			}
		}

		$we_unique = isset($GLOBALS['we_unique']) ? ++$GLOBALS['we_unique'] : ($GLOBALS['we_unique'] = 1);
		$ret='
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_doc\'] = clone($GLOBALS[\'we_doc\']);
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_IS_DYN\'] = isset($GLOBALS[\'WE_IS_DYN\']) ? 1 : 0;
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_ID\'] = $GLOBALS[\'WE_DOC_ID\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_ParentID\'] = $GLOBALS[\'WE_DOC_ParentID\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_Path\'] = $GLOBALS[\'WE_DOC_Path\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_IsDynamic\'] = $GLOBALS[\'WE_DOC_IsDynamic\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_FILENAME\'] = $GLOBALS[\'WE_DOC_FILENAME\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_Category\'] = $GLOBALS[\'WE_DOC_Category\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_EXTENSION\'] = $GLOBALS[\'WE_DOC_EXTENSION\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'TITLE\'] = $GLOBALS[\'TITLE\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'KEYWORDS\'] = $GLOBALS[\'KEYWORDS\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'DESCRIPTION\'] = $GLOBALS[\'DESCRIPTION\'];
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_cmd\'] = isset($_REQUEST[\'we_cmd\']) ? $_REQUEST[\'we_cmd\'] : \'\';
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'FROM_WE_SHOW_DOC\'] = isset($GLOBALS[\'FROM_WE_SHOW_DOC\']) ? $GLOBALS[\'FROM_WE_SHOW_DOC\'] : \'\';
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_transaction\'] = isset($GLOBALS[\'we_transaction\']) ? $GLOBALS[\'we_transaction\'] : \'\';
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_editmode\'] = isset($GLOBALS[\'we_editmode\']) ? $GLOBALS[\'we_editmode\'] : null;
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_ContentType\'] = isset($GLOBALS[\'we_ContentType\']) ? $GLOBALS[\'we_ContentType\'] : \'text/webedition\';
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'pv_id\'] = isset($_REQUEST[\'pv_id\']) ? $_REQUEST[\'pv_id\'] : \'\';
		$GLOBALS[\'we_backVars\']['.$we_unique.'][\'pv_tid\'] = isset($_REQUEST[\'pv_tid\']) ? $_REQUEST[\'pv_tid\'] : \'\';';
		if (isset($GLOBALS['WE_IS_DYN'])) {
			$ret .= 'unset($GLOBALS[\'WE_IS_DYN\']);';
		}
		$ret .= 'unset($_REQUEST[\'pv_id\']);
						unset($_REQUEST[\'pv_tid\']);';

		if (we_tag('ifSeeMode', array())) {
			if ($seeMode) { //	only show link to seeMode, when id is given
				if ($id) {
					$content .= '<a href="' . $id . '" seem="include"></a>';
				}
				if ($path) {
					$_tmpID = path_to_id($path);
					$content .= '<a href="' . $_tmpID . '" seem="include"></a>';
				}
			}

			$content = eregi_replace('< ?form[^>]*>', '', $content);
			$content = eregi_replace('< ?/ ?form[^>]*>', '', $content);
		}

		$ret .= 'eval(\'?>' . str_replace('\'',"\'",$content).'\');';

		$ret .= '
		$GLOBALS[\'we_doc\'] = clone($GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_doc\']);
		$GLOBALS[\'WE_DOC_ID\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_ID\'];
		$GLOBALS[\'WE_DOC_ParentID\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_ParentID\'];
		$GLOBALS[\'WE_DOC_Path\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_Path\'];
		$GLOBALS[\'WE_DOC_IsDynamic\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_IsDynamic\'];
		$GLOBALS[\'WE_DOC_FILENAME\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_FILENAME\'];
		$GLOBALS[\'WE_DOC_Category\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_Category\'];
		$GLOBALS[\'WE_DOC_EXTENSION\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'WE_DOC_EXTENSION\'];
		$GLOBALS[\'TITLE\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'TITLE\'];
		$GLOBALS[\'KEYWORDS\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'KEYWORDS\'];
		$GLOBALS[\'DESCRIPTION\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'DESCRIPTION\'];
		$_REQUEST[\'we_cmd\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_cmd\'];
		$GLOBALS[\'we_cmd\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_cmd\'];
		$GLOBALS[\'FROM_WE_SHOW_DOC\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'FROM_WE_SHOW_DOC\'];
		$GLOBALS[\'we_transaction\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_transaction\'];
		$GLOBALS[\'we_editmode\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_editmode\'];
		$GLOBALS[\'we_ContentType\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'we_ContentType\'];
		$_REQUEST[\'pv_id\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'pv_id\'];
		$_REQUEST[\'pv_tid\'] = $GLOBALS[\'we_backVars\']['.$we_unique.'][\'pv_tid\'];

		if (isset($GLOBALS[\'WE_IS_DYN\'])) {
			unset($GLOBALS[\'WE_IS_DYN\']);
		}';
		if (isset($GLOBALS['WE_IS_DYN'])) {
			$ret .= '$GLOBALS[\'WE_IS_DYN\'] = 1;';
		}
		$ret .= 'unset($GLOBALS[\'we_backVars\']['.$we_unique.']);';
		return $ret;
	}
	return '';
}