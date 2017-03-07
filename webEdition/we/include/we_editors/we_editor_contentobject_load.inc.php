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

$cmd = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 0);
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 1);
$id = we_base_request::_(we_base_request::STRING, 'we_cmd', false, 2);

$we_doc = new we_object();

$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
$we_doc->we_initSessDat($we_dt);
//
//	---> Setting the Content-Type
//

$charset = (!empty($we_doc->elements['Charset']['dat']) ? //	send charset which might be determined in template
	$we_doc->elements['Charset']['dat'] : DEFAULT_CHARSET);

//we_html_tools::headerCtCharset('text/html', $charset);
//	---> Loading the Stylesheets
$head = '';
if($we_doc->CSS){
	$cssArr = makeArrayFromCSV($we_doc->CSS);
	foreach($cssArr as $cs){
		$path = id_to_path($cs);
		if($path){
			$head .= we_html_element::cssLink($path);
		}
	}
}

function reloadElement($we_transaction, $we_doc, $id){
	$identifier = array_pop(explode('_', $id, 2));
	$uniqid = 'entry_' . $identifier;
	$wholename = $we_doc->getElement('wholename' . $identifier);

	$content = '<div id="' . $uniqid . '" class="objectFileElement">
	<div id="f' . $uniqid . '" class="default">
					<table cellpadding="6" style="float:left;">' .
		$we_doc->getFieldHTML($wholename, $uniqid) .
		'	</table>
				<span class="defaultfont clearfix" style="width:180px;">' .
		we_html_button::create_button('fa:btn_add_field,fa-plus,fa-lg fa-square-o', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
		we_html_button::create_button(we_html_button::DIRUP, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
		we_html_button::create_button(we_html_button::DIRDOWN, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
		we_html_button::create_button(we_html_button::TRASH, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
		'</span>
</div>';

	$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

	echo we_gui_OrderContainer::getResponse('reload', $uniqid, $content) .
		we_html_element::jsElement('reinitTiny("' . $wholename . 'default]","' . $we_transaction . '",' . intval(we_base_browserDetect::isIE() || we_base_browserDetect::isOpera()) . ');');
}

echo we_html_tools::getHtmlTop('', $charset, 5, $head . we_editor_script::get());
?>
<body><?php
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

		$content = '<div id="' . $uniqid . '" class="objectFileElement">
<div id="f' . $uniqid . '" class="objectFileElement">
				<table cellpadding="6" style="float:left;">' .
			$we_doc->getFieldHTML($we_doc->getElement('wholename' . $identifier), $uniqid) .
			'</table>
				<span class="defaultfont clearfix" style="width:180px;">' .
			we_html_button::create_button('fa:btn_add_field,fa-plus,fa-lg fa-square-o', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
			we_html_button::create_button(we_html_button::DIRUP, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
			we_html_button::create_button(we_html_button::DIRDOWN, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", '', 0, 0, "", "", false, false, "_" . $identifier) .
			we_html_button::create_button(we_html_button::TRASH, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
			'</span>
		</div></div>';

		echo we_gui_OrderContainer::getResponse('add', $uniqid, $content, $afterid);

		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		break;
	case 'object_change_entry_at_class':
		$identifier = array_pop(explode('_', $id, 2));
		$fieldname = $we_doc->getElement('wholename' . $identifier);
		$we_doc->setElement($fieldname . 'default', '');
		reloadElement($we_transaction, $we_doc, $id);
		break;

	case 'object_reload_entry_at_class':
		$identifier = array_pop(explode('_', $id, 2));
		$fieldname = $we_doc->getElement('wholename' . $identifier);
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_up_meta_at_class':
		$we_doc->upMetaAtClass(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_down_meta_at_class':
		$we_doc->downMetaAtClass(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_insert_meta_at_class':
		$we_doc->addMetaToClass(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_delete_meta_class':
		$we_doc->removeMetaFromClass(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_del_all_users':
		$we_doc->del_all_users(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_add_user_to_field':
		$we_doc->add_user_to_field(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', '', 3), we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 4));
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_del_user_from_field':
		$we_doc->del_user_from_field(we_base_request::_(we_base_request::INT, 'we_cmd', '', 3), we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 4));
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_remove_image_at_class';
		$we_doc->remove_image(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_delete_link_at_class':
		$name = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);
		if(isset($we_doc->elements[$name])){
			unset($we_doc->elements[$name]);
		}
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_change_link_at_class':
		$we_doc->changeLink(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
		reloadElement($we_transaction, $we_doc, $id);
		break;
	case 'object_change_multiobject_at_class':
		$name = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);
		while($we_doc->elements[$name . 'count']['dat'] > 0){
			$we_doc->removeMetaFromClass($name, 0);
		}
		$we_doc->removeMetaFromClass($name, 0);
		reloadElement($we_transaction, $we_doc, $id);
		break;

	case 'object_delete_entry_at_class':
		if(isset($id)){
			$identifier = array_pop(explode('_', $id, 2));
			$uniqid = 'entry_' . $identifier;
			$we_doc->removeEntryFromClass($identifier);
			echo we_gui_OrderContainer::getResponse('delete', $uniqid);
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		}
		break;

	case 'object_up_entry_at_class':
		$sort = $we_doc->getElement('we_sort');

		if(isset($id)){
			$identifier = array_pop(explode('_', $id, 2));
			$uniqid = 'entry_' . $identifier;
			$we_doc->upEntryAtClass($identifier);
			$ret = '';
			foreach(array_keys($sort) as $sortId){
				$field = $we_doc->elements['wholename' . $sortId]['dat'];
				$ret .= '
var target = _EditorFrame.getContentEditor(),
	confname = "' . $field . 'default";
if(typeof target.tinyMceRawConfigurations[confname] === \'object\'){
	WE().layout.we_tinyMCE.functions.initEditor(target, target.tinyMceRawConfigurations[confname]);
}';
			}
			echo we_gui_OrderContainer::getResponse('up', $uniqid) .
			($ret ? we_html_element::jsElement($ret) : '');
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		}
		break;

	case 'object_down_entry_at_class':
		$sort = $we_doc->getElement('we_sort');

		if(isset($id)){
			$identifier = array_pop(explode('_', $id, 2));
			$uniqid = 'entry_' . $identifier;
			$we_doc->downEntryAtClass($identifier);
			$ret = '';
			foreach(array_keys($sort) as $sortId){
				$field = $we_doc->elements['wholename' . $sortId]['dat'];
				$ret .= '
var target = _EditorFrame.getContentEditor(),
	confname = "' . $field . 'default";
if(typeof target.tinyMceRawConfigurations[confname] === \'object\'){
		WE().layout.we_tinyMCE.functions.initEditor(target, target.tinyMceRawConfigurations[confname]);
}';
			}
			echo we_gui_OrderContainer::getResponse('down', $uniqid) .
			($ret ? we_html_element::jsElement($ret) : '');
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		}
		break;

	default:
		break;
}
?>
</body>
</html>