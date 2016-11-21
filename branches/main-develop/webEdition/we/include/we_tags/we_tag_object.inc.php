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
function we_parse_tag_object($attribs, $content, array $arr){
	$name = weTag_getParserAttribute('name', $arr);
	if($name && strpos($name, ' ') !== false){
		return parseError(sprintf(g_l('parser', '[name_with_space]'), 'object'));
	}

	//dont't check if id or name is set, since it is possible to set object ID by request

	return '<?php ' . (strpos($content, '$lv') !== false ? 'global $lv;' : '') .
		'if(' . we_tag_tagParser::printTag('object', $attribs) . '){?>' . $content . '<?php }
		we_post_tag_listview(); ?>';
}

function we_tag_object(array $attribs){
	if(!defined('OBJECT_TABLE')){
		echo modulFehltError('Object/DB', __FUNCTION__);
		return false;
	}

	$condition = weTag_getAttribute('condition', $attribs, 0, we_base_request::RAW);
	$classid = weTag_getAttribute('classid', $attribs, 0, we_base_request::INT);
	$we_oid = weTag_getAttribute('id', $attribs, 0, we_base_request::INT);
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$size = 5 * intval(weTag_getAttribute('size', $attribs, 30, we_base_request::UNIT));
	$triggerid = weTag_getAttribute('triggerid', $attribs, 0, we_base_request::INT);
	$searchable = weTag_getAttribute('searchable', $attribs, false, we_base_request::BOOL);
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, we_base_request::BOOL);
	$objectseourls = weTag_getAttribute('objectseourls', $attribs, TAGLINKS_OBJECTSEOURLS, we_base_request::BOOL);

	if($name){
		if(strpos($name, ' ') !== false){
			echo parseError(sprintf(g_l('parser', '[name_with_space]'), 'object'));
			return false;
		}

		$we_doc = $GLOBALS['we_doc'];
		//handle listview of documents
		$we_oid = (isset($GLOBALS['lv']) && is_object($GLOBALS['lv']) && $GLOBALS['lv']->f($name) ?
				$GLOBALS['lv']->f($name) :
				($we_doc->getElement($name, 'bdid') ?
					$we_doc->getElement($name, 'bdid') :
					($we_doc->getElement($name) ?
						$we_doc->getElement($name) :
						$we_oid)
				));
		$rootDirID = ($classid ? f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsClassFolder=1 AND TableID=' . intval($classid)) : 0);

		$path = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $we_oid);
		$textname = 'we_' . $we_doc->Name . '_vars[' . $name . '_path]';
		$idname = 'we_' . $we_doc->Name . '_object[' . $name . '#bdid]';

		if($GLOBALS['we_editmode']){
			$delbutton = we_html_button::create_button(we_html_button::TRASH, "javascript:document.forms[0].elements['" . $idname . "'].value=0;document.forms[0].elements['" . $textname . "'].value='';_EditorFrame.setEditorIsHot(false);we_cmd('reload_editpage');");
			$open = we_html_button::create_button(we_html_button::VIEW, "javascript:if(document.forms[0].elements['" . $idname . "'].value){WE().layout.weEditorFrameController.openDocument('" . OBJECT_FILES_TABLE . "', document.forms[0].elements['" . $idname . "'].value,'');}");

			$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . OBJECT_FILES_TABLE . "','" . $idname . "','" . $textname . "','reload_hot_editpage','','" . $rootDirID . "','objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")");


			$yuiSuggest = &weSuggest::getInstance();
			$yuiSuggest->setAcId($name . we_base_file::getUniqueId(), f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . $classid));
			$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::OBJECT_FILE);
			$yuiSuggest->setInput($textname, $path);
			$yuiSuggest->setResult($idname, $we_oid);
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(1);
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setTable(OBJECT_FILES_TABLE);
			$yuiSuggest->setWidth($size);
			?>
			<table class="weEditTable padding0 spacing0 border0">
				<tr>
					<td class="weEditmodeStyle" style="padding:0 6px;"><span class="bold"><?= weTag_getAttribute('text', $attribs, weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING), we_base_request::STRING); ?></span></td>
					<td class="weEditmodeStyle" style="width: <?= ($size + 20); ?>px"><?= $yuiSuggest->getHTML(); ?></td>
					<td class="weEditmodeStyle"><?= $button; ?></td>
					<td class="weEditmodeStyle"><?= $open; ?></td>
					<td class="weEditmodeStyle"><?= $delbutton; ?></td>
				</tr>
			</table><?php
		}
	}

	$we_oid = $we_oid? :
//Fix #10526 check if objectID is given by request
		(we_base_request::_(we_base_request::INT, 'we_objectID') ? : we_base_request::_(we_base_request::INT, 'we_oid', 0));

	//Fix #10609 we need classID now!
	$classid = $classid ? : ($we_oid ? f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=0 AND ID=' . intval($we_oid)) : 0);

	if($we_oid && $classid){
		$unique = md5(uniqid(__FUNCTION__, true));
		$GLOBALS['lv'] = new we_listview_object($unique, 1, 0, '', 0, $classid, '', '', 'of.ID=' . intval($we_oid)  . ($condition ? ' AND ' . $condition : ''), $triggerid, '', '', $searchable, '', '', '', '', '', '', '', 0, '', '', '', '', $hidedirindex, $objectseourls);
		$avail = $GLOBALS['lv']->next_record();

		if($avail){
			if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] === we_base_constants::MODE_SEE){
				echo we_SEEM::getSeemAnchors($we_oid, 'object');
			}
		}
	} else {
		$GLOBALS['lv'] = new stdClass();
		$avail = false;
	}

	we_pre_tag_listview();

	return $avail;
}
