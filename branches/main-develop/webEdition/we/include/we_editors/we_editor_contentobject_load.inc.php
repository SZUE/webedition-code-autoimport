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
require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');

we_html_tools::protect();

function reloadElement(we_base_jsCmd $jsCmd, $we_transaction, $we_doc, $id){
	$identifier = array_pop(explode('_', $id, 2));
	$uniqid = 'entry_' . $identifier;
	$wholename = $we_doc->getElement('wholename' . $identifier);

	$content = '<div id="' . $uniqid . '" class="objectFileElement">
	<div id="f' . $uniqid . '" class="default">
					<table cellpadding="6" style="float:left;">' .
		$we_doc->getFieldHTML($jsCmd, $wholename, $uniqid) .
		'	</table>
				<span class="defaultfont clearfix" style="width:180px;">' .
		we_html_button::create_button('fa:btn_add_field,fa-plus,fa-lg fa-square-o', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
		we_html_button::create_button(we_html_button::DIRUP, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
		we_html_button::create_button(we_html_button::DIRDOWN, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
		we_html_button::create_button(we_html_button::TRASH, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
		'</span>
	</div>';

	$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

	return we_gui_OrderContainer::wrapField($jsCmd, 'reload', $uniqid, $content);
}

$cmd = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 0);
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 1);
$id = we_base_request::_(we_base_request::STRING, 'we_cmd', false, 2);

$we_doc = new we_object();
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
$we_doc->we_initSessDat($we_dt);
//
//	---> Setting the Content-Type
//

$jsCmd = new we_base_jsCmd;
$content = '';

switch($cmd){
	case 'object_insert_entry_at_class':
		if($id != false){
			$after = array_pop(explode('_', $id, 2));
			$afterid = $id;
		} else {
			$after = false;
			$afterid = false;
		}
		$identifier = str_replace('.', '', uniqid('', true));
		$uniqid = 'entry_' . $identifier;
		$we_doc->addEntryToClass($identifier, $after);

		$cnt = '<div id="' . $uniqid . '" class="objectFileElement">
<div id="f' . $uniqid . '" class="objectFileElement">
				<table cellpadding="6" style="float:left;">' .
			$we_doc->getFieldHTML($jsCmd, $we_doc->getElement('wholename' . $identifier), $uniqid) .
			'</table>
				<span class="defaultfont clearfix" style="width:180px;">' .
			we_html_button::create_button('fa:btn_add_field,fa-plus,fa-lg fa-square-o', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
			we_html_button::create_button(we_html_button::DIRUP, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
			we_html_button::create_button(we_html_button::DIRDOWN, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
			we_html_button::create_button(we_html_button::TRASH, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
			'</span>
		</div></div>';

		$content .= we_gui_OrderContainer::wrapField($jsCmd, 'add', $uniqid, $cnt, $afterid);

		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		break;
	case 'object_change_entry_at_class':
		$identifier = array_pop(explode('_', $id, 2));
		$fieldname = $we_doc->getElement('wholename' . $identifier);
		$we_doc->setElement($fieldname . 'default', '');
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;

	case 'object_reload_entry_at_class':
		$identifier = array_pop(explode('_', $id, 2));
		$fieldname = $we_doc->getElement('wholename' . $identifier);
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_up_meta_at_class':
		$we_doc->upMetaAtClass(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
		$content .= reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_down_meta_at_class':
		$we_doc->downMetaAtClass(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_insert_meta_at_class':
		$we_doc->addMetaToClass(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_delete_meta_class':
		$we_doc->removeMetaFromClass(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_del_all_users':
		$we_doc->del_all_users(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_add_user_to_field':
		$we_doc->add_user_to_field(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', '', 3), we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 4));
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_del_user_from_field':
		$we_doc->del_user_from_field(we_base_request::_(we_base_request::INT, 'we_cmd', '', 3), we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 4));
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_remove_image_at_class';
		$we_doc->remove_image(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_delete_link_at_class':
		$name = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);
		if(isset($we_doc->elements[$name])){
			unset($we_doc->elements[$name]);
		}
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_change_link_at_class':
		$we_doc->changeLink(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_change_multiobject_at_class':
		$name = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);
		while($we_doc->elements[$name . 'count']['dat'] > 0){
			$we_doc->removeMetaFromClass($name, 0);
		}
		$we_doc->removeMetaFromClass($name, 0);
		$content .= reloadElement($jsCmd, $we_transaction, $we_doc, $id);
		break;
	case 'object_delete_entry_at_class':
		if(isset($id)){
			$identifier = array_pop(explode('_', $id, 2));
			$uniqid = 'entry_' . $identifier;
			$we_doc->removeEntryFromClass($identifier);
			$content .= we_gui_OrderContainer::wrapField($jsCmd, 'delete', $uniqid);
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		}
		break;
	case 'object_up_entry_at_class':
		if(isset($id)){
			$identifier = array_pop(explode('_', $id, 2));
			$uniqid = 'entry_' . $identifier;
			$we_doc->upEntryAtClass($identifier);
			$content .= we_gui_OrderContainer::wrapField($jsCmd, 'up', $uniqid);
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		}
		break;
	case 'object_down_entry_at_class':
		if(isset($id)){
			$identifier = array_pop(explode('_', $id, 2));
			$uniqid = 'entry_' . $identifier;
			$we_doc->downEntryAtClass($identifier);
			$content .= we_gui_OrderContainer::wrapField($jsCmd, 'down', $uniqid);
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		}
		break;
	default:
		break;
}

$charset = (!empty($we_doc->elements['Charset']['dat']) ? //	send charset which might be determined in template
	$we_doc->elements['Charset']['dat'] : DEFAULT_CHARSET);
//we_html_tools::headerCtCharset('text/html', $charset);
//	---> Loading the Stylesheets
$head = '';
if($we_doc->CSS){
	$cssArr = id_to_path(explode(',', $we_doc->CSS), FILE_TABLE, null, true);
	foreach($cssArr as $path){
		$head .= we_html_element::cssLink($path);
	}
}
echo we_html_tools::getHtmlTop('', $charset, 5, $head  . we_editor_script::get() . $jsCmd->getCmds(), we_html_element::htmlBody([], $content));