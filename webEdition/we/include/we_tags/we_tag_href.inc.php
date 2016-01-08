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
		$hrefArr = we_unserialize($GLOBALS['we_doc']->getElement($name));
		return we_document::getHrefByArray($hrefArr);
	}

	$nint = $name . we_base_link::MAGIC_INT_LINK;
	$nintID = $name . we_base_link::MAGIC_INT_LINK_ID;
	$nintPath = $name . we_base_link::MAGIC_INT_LINK_PATH;
	$extPath = '';
	$int = ($type == we_base_link::TYPE_EXT ? false : ($type == we_base_link::TYPE_INT || $GLOBALS['we_doc']->getElement($nint)) ? $GLOBALS['we_doc']->getElement($nint) : false);
	$intPath = $ct = '';

	if($int){
		$intID = $GLOBALS['we_doc']->getElement($nintID, 'bdid')? : intval(weTag_getAttribute('startid', $attribs, 0, we_base_request::INT));
		if($intID){
			if(($foo = getHash('SELECT Path,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($intID)))){
				$intPath = $foo['Path'];
				$ct = $foo['ContentType'];
			}
		}
		$href = $intPath;
		$include_path = $href ? WEBEDITION_PATH . '..' . $href : ''; //(symlink) webEdition always points to the REAL DOC-Root!
		$path_parts = pathinfo($href);
		if($hidedirindex && seoIndexHide($path_parts['basename'])){
			$href = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
		}
	} else {
		$intID = 0;
// we have to use a html_entity_decode first in case a user has set &amp, &uuml; by himself
		$href = $extPath = oldHtmlspecialchars(html_entity_decode($GLOBALS['we_doc']->getElement($name)));
		$include_path = $href ? WEBEDITION_PATH . '..' . $href : ''; //(symlink) webEdition always points to the REAL DOC-Root!
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

		switch(weTag_getAttribute('only', $attribs, '', we_base_request::STRING)){
			case 'id':
				return intval($intID);
		}
		if($int && $intID && !$include && !isset($attribs['isInternal'])){
			$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE'], true);
			if($urlReplace){
				return preg_replace($urlReplace, array_keys($urlReplace), $href);
			}
		}
		if($include){
			if($include_path && file_exists($include_path)){
				include($include_path);
			}
			return '';
		}

		return $href;
	}

	if($rootdir[0] != '/'){
		$rootdirid = $rootdir;
		$rootdir = id_to_path($rootdir, FILE_TABLE);
	} else {
		$rootdir = ($rootdir === '/' ? $rootdir : rtrim($rootdir, '/'));
		$rootdirid = path_to_id($rootdir, FILE_TABLE, $GLOBALS['DB_WE']);
	}
// Bug Fix #7045
	if($rootdir === '/'){
		$rootdir = '';
	}

	$int_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_href[' . $nint . '#bdid]';
	$intPath_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_vars[' . $nintPath . ']';
	$intID_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_href[' . $nintID . '#bdid]';
	$ext_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';

	$trashbut = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $intID_elem_Name . "'].value = ''; document.we_form.elements['" . $intPath_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true);" . (($include || $reload) ? "setScrollTo(); top.we_cmd('reload_editpage');" : ''), true);
	$span = '<span class="defaultfont" style="color: black;">';

	$size = 5 * intval(weTag_getAttribute('size', $attribs, 20, we_base_request::INT));
	$cmd1 = "document.we_form.elements['" . $intID_elem_Name . "'].value";
	$wecmdenc1 = we_base_request::encCmd($cmd1);
	$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $intPath_elem_Name . "'].value");
	$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);" . ($type == we_base_link::TYPE_ALL ? "opener.document.we_form.elements['" . $int_elem_Name . "'][0].checked = true;" : '') . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd('reload_editpage');" : ""));
	if(($directory && $file) || $file){
		$but = we_html_button::create_button('fa:btn_edit_link,fa-lg fa-pencil,fa-lg fa-link', "javascript:we_cmd('we_selector_document', " . $cmd1 . ", '" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','', '" . $rootdirid . "', '', " . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",''," . ($directory ? 1 : 0) . ");");
		$but2 = permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES') ? we_html_button::create_button('fa:btn_edit_link,fa-lg fa-pencil,fa-lg fa-link', "javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'" . $ext_elem_Name . "\\'].value', '" . (($directory && $file) ? "filefolder" : '') . "', document.forms[0].elements['" . $ext_elem_Name . "'].value, 'opener._EditorFrame.setEditorIsHot(true);" . ($type == we_base_link::TYPE_ALL ? "opener.document.we_form.elements[\'" . $int_elem_Name . "\'][1].checked = true;" : '') . "','" . $rootdir . "')") : '';
	} else {
		$but = we_html_button::create_button('fa:btn_edit_link,fa-lg fa-pencil,fa-lg fa-link', "javascript:we_cmd('we_selector_directory', " . $cmd1 . ", '" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','', '" . $rootdirid . "');");
		$but2 = permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES') ? we_html_button::create_button('fa:btn_edit_link,fa-lg fa-pencil,fa-lg fa-link', "javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'" . $ext_elem_Name . "\\'].value', '" . we_base_ContentTypes::FOLDER . "', document.forms[0].elements['" . $ext_elem_Name . "'].value, 'opener._EditorFrame.setEditorIsHot(true);" . ($type == we_base_link::TYPE_ALL ? " opener.document.we_form.elements[\'" . $int_elem_Name . "\'][1].checked = true;" : '') . "','" . $rootdir . "')") : '';
	}
	$open = we_html_button::create_button(we_html_button::VIEW, "javascript:if(" . $cmd1 . "){WE().layout.weEditorFrameController.openDocument('" . FILE_TABLE . "', " . $cmd1 . ",'');}");
	$trashbut2 = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $ext_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true);", true);

	switch($type){
		case we_base_link::TYPE_ALL:
		case we_base_link::TYPE_INT:
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

	ob_start();
	if($include && $include_path && file_exists($include_path)){
		include($include_path);
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
</table>' . ob_get_clean();
}
