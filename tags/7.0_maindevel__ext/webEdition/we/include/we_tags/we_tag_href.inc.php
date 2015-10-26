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
function we_tag_href($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$type = weTag_getAttribute('type', $attribs, we_base_link::TYPE_ALL, we_base_request::STRING);
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, we_base_request::BOOL);
	$include = weTag_getAttribute('include', $attribs, false, we_base_request::BOOL);
	$reload = weTag_getAttribute('reload', $attribs, false, we_base_request::BOOL);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '/', we_base_request::FILE);
//	$seeMode = weTag_getAttribute((isset($attribs['seem']) ? 'seem' : 'seeMode'), $attribs, true, true);


	$file = weTag_getAttribute('file', $attribs, true, we_base_request::BOOL);
	$directory = weTag_getAttribute('directory', $attribs, false, we_base_request::BOOL);
	$attribs = removeAttribs($attribs, array('rootdir', 'file', 'directory'));

	if($GLOBALS['we_doc'] instanceof we_objectFile){
		$hrefArr = $GLOBALS['we_doc']->getElement($name) ? unserialize($GLOBALS['we_doc']->getElement($name)) : array();
		return (is_array($hrefArr) && $hrefArr ? we_document::getHrefByArray($hrefArr) : '');
	}

	$nint = $name . we_base_link::MAGIC_INT_LINK;
	$nintID = $name . we_base_link::MAGIC_INT_LINK_ID;
	$nintPath = $name . we_base_link::MAGIC_INT_LINK_PATH;
// we have to use a html_entity_decode first in case a user has set &amp, &uuml; by himself
	$extPath = oldHtmlspecialchars(html_entity_decode($GLOBALS['we_doc']->getElement($name)));

	switch($type){
		default:
		case '':
		case we_base_link::TYPE_INT:
		case we_base_link::TYPE_ALL:
			$int = ($type == we_base_link::TYPE_INT || $GLOBALS['we_doc']->getElement($nint) != '') ? $GLOBALS['we_doc']->getElement($nint) : false;
			$intID = $GLOBALS['we_doc']->getElement($nintID, 'bdid');
			$intPath = $ct = '';
			if(!$intID){
				$intID = intval(weTag_getAttribute('startid', $attribs, 0, we_base_request::INT));
			}
			if($intID){
				if(($foo = getHash('SELECT Path,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($intID)))){
					$intPath = $foo['Path'];
					$ct = $foo['ContentType'];
				}
			}

			if($int){
				$href = $intPath;
				$include_path = $href ? WEBEDITION_PATH . '..' . $href : ''; //(symlink) webEdition always points to the REAL DOC-Root!
				$path_parts = pathinfo($href);
				if($hidedirindex && show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
					$href = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
				}
				break;
			}
//no break;
		case we_base_link::TYPE_EXT:
			$int = false;
			$href = $extPath;
			$include_path = $href ? WEBEDITION_PATH . '..' . $href : ''; //(symlink) webEdition always points to the REAL DOC-Root!
			break;
	}

	if(!$GLOBALS['we_editmode']){
		if($int && defined('CUSTOMER_TABLE') && $intID && weTag_getAttribute('cfilter', $attribs, true, we_base_request::BOOL)){
			$filter = we_customer_documentFilter::getFilterByIdAndTable($intID, FILE_TABLE, $GLOBALS['DB_WE']);

			if(is_object($filter)){
				if($filter->accessForVisitor($intID, $ct, true) != we_customer_documentFilter::ACCESS){
					return '';
				}
			}
		}
		if($int && $intID && !$include && !isset($attribs['isInternal'])){
			$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE'], true);
			if($urlReplace){
				return preg_replace($urlReplace, array_keys($urlReplace), $href);
			}
		}

		return ($include ? ($include_path && file_exists($include_path) ? '<?php include("' . $include_path . '"); ?>' : '') : $href);
	}

	if($rootdir[0] != '/'){
		$rootdirid = $rootdir;
		$rootdir = id_to_path($rootdir, FILE_TABLE);
	} else {
		$rootdir = ($rootdir === '/' ? $rootdir : rtrim($rootdir, '/'));
		$rootdirid = path_to_id($rootdir, FILE_TABLE);
	}
// Bug Fix #7045
	if($rootdir === '/'){
		$rootdir = '';
	}

	$int_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_href[' . $nint . ']';
	$intPath_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_href[' . $nintPath . ']';
	$intID_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_href[' . $nintID . '#bdid]';
	$ext_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';

	$trashbut = we_html_button::create_button('image:btn_function_trash', "javascript:document.we_form.elements['" . $intID_elem_Name . "'].value = ''; document.we_form.elements['" . $intPath_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true);" . (($include || $reload) ? "setScrollTo(); top.we_cmd('reload_editpage');" : ''), true);
	$span = '<span style="color: black;font-size:' . ((we_base_browserDetect::isMAC()) ? "11px" : ((we_base_browserDetect::isUNIX()) ? "13px" : "12px")) . ';font-family:' . g_l('css', '[font_family]') . ';">';

	$size = 5 * intval(weTag_getAttribute('size', $attribs, 20, we_base_request::INT));

	$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $intID_elem_Name . "'].value");
	$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['" . $intPath_elem_Name . "'].value");
	$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);" . ($type == we_base_link::TYPE_ALL ? "opener.document.we_form.elements['" . $int_elem_Name . "'][0].checked = true;" : '') . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd('reload_editpage');" : ""));
	if(($directory && $file) || $file){
		$but = we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('openDocselector', document.forms[0].elements['" . $intID_elem_Name . "'].value, '" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','', '" . $rootdirid . "', '', " . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",''," . ($directory ? 1 : 0) . ");");
		$but2 = permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES') ? we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'" . $ext_elem_Name . "\\'].value', '" . (($directory && $file) ? "filefolder" : '') . "', document.forms[0].elements['" . $ext_elem_Name . "'].value, 'opener._EditorFrame.setEditorIsHot(true);" . ($type == we_base_link::TYPE_ALL ? "opener.document.we_form.elements[\'" . $int_elem_Name . "\'][1].checked = true;" : '') . "','" . $rootdir . "')") : '';
	} else {
		$but = we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('openDirselector', document.forms[0].elements['" . $intID_elem_Name . "'].value, '" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','', '" . $rootdirid . "');");
		$but2 = permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES') ? we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'" . $ext_elem_Name . "\\'].value', '" . we_base_ContentTypes::FOLDER . "', document.forms[0].elements['" . $ext_elem_Name . "'].value, 'opener._EditorFrame.setEditorIsHot(true);" . ($type == we_base_link::TYPE_ALL ? " opener.document.we_form.elements[\'" . $int_elem_Name . "\'][1].checked = true;" : '') . "','" . $rootdir . "')") : '';
	}
	$open = we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', "javascript:if(document.forms[0].elements['" . $intID_elem_Name . "'].value){top.weEditorFrameController.openDocument('" . FILE_TABLE . "', document.forms[0].elements['" . $intID_elem_Name . "'].value,'');}");
	$trashbut2 = we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'btn_function_trash', "javascript:document.we_form.elements['" . $ext_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true);", true);
	if($type == we_base_link::TYPE_ALL || $type == we_base_link::TYPE_INT){
		$yuiSuggest = &weSuggest::getInstance();
		$yuiSuggest->setAcId($name . we_base_file::getUniqueId(), $rootdir);
		$yuiSuggest->setContentType(implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::JS, we_base_ContentTypes::CSS, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::QUICKTIME)));
		$yuiSuggest->setInput($intPath_elem_Name, $intPath);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($intID_elem_Name, $intID);
		$yuiSuggest->setSelector($directory ? weSuggest::DirSelector : weSuggest::DocSelector);
		$yuiSuggest->setTable(FILE_TABLE);
		$yuiSuggest->setWidth($size);
	}

	return
			'<table class="weEditTable padding0 spacing2">' .
			($type == we_base_link::TYPE_ALL || $type == we_base_link::TYPE_INT ? '
<tr>
	<td class="weEditmodeStyle">' . ($type == we_base_link::TYPE_ALL ? we_html_forms::radiobutton(1, $int, $int_elem_Name, $span . g_l('tags', '[int_href]') . ':</span>') : $span . g_l('tags', '[int_href]') . ':</span><input type="hidden" name="' . $int_elem_Name . '" value="1" />' ) . '</td>
	<td class="weEditmodeStyle" style="width:' . ($size + 20) . 'px">' . $yuiSuggest->getHTML() . '</td>
	<td class="weEditmodeStyle">' . $but . '</td>
	<td class="weEditmodeStyle">' . $open . '</td>
	<td class="weEditmodeStyle">' . $trashbut . '</td>
	</tr>' : '') .
			($type == we_base_link::TYPE_ALL || $type == we_base_link::TYPE_EXT ? '
<tr>
	<td class="weEditmodeStyle">' . ($type == we_base_link::TYPE_ALL ? we_html_forms::radiobutton(0, !$int, $int_elem_Name, $span . g_l('tags', '[ext_href]') . ':</span>') : $span . g_l('tags', '[ext_href]') . ':</span><input type="hidden" name="' . $int_elem_Name . '" value="0" />') . '</td>
	<td class="weEditmodeStyle" style="width:' . ($size + 20) . 'px">' .
					getHtmlTag('input', array_merge(removeAttribs($attribs, array('onkeydown', 'onKeyDown')), array(
						'style' => 'width:' . ($size) . 'px;',
						'onchange' => ($type == we_base_link::TYPE_ALL ? 'this.form.elements[\'' . $int_elem_Name . '\'][1].checked=true;' : ''),
						'type' => "text",
						'name' => 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']',
						'placeholder' => "http://example.org",
						'value' => $extPath
					)))
					. '</td>
	<td class="weEditmodeStyle">' . $but2 . '</td>
	<td class="weEditmodeStyle">' . $trashbut2 . '</td>
</tr>' : '') . '
</table>' .
			($include && $include_path && file_exists($include_path) ? '<?php include("' . $include_path . '"); ?>' : '');
}
