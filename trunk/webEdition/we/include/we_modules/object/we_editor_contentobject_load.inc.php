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
require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');

we_html_tools::protect();


$cmd = weRequest('raw', 'we_cmd', '', 0);
$we_transaction = weRequest('transaction', 'we_cmd', '', 1);
$id = weRequest('string', 'we_cmd', false, 2);

$jsGUI = new weOrderContainer('_EditorFrame.getContentEditor()', 'classEntry');

$we_doc = new we_object();

$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
$we_doc->we_initSessDat($we_dt);
//
//	---> Setting the Content-Type
//

$charset = (isset($we_doc->elements['Charset']['dat']) && $we_doc->elements['Charset']['dat'] ? //	send charset which might be determined in template
		$we_doc->elements['Charset']['dat'] : DEFAULT_CHARSET);

we_html_tools::headerCtCharset('text/html', $charset);

//
//	---> Output the HTML Header
//

echo we_html_tools::getHtmlTop('', $charset, 5);


//
//	---> Loading the Stylesheets
//

if($we_doc->CSS){
	$cssArr = makeArrayFromCSV($we_doc->CSS);
	foreach($cssArr as $cs){
		$path = id_to_path($cs);
		if($path){
			echo we_html_element::cssLink($path);
		}
	}
}
echo STYLESHEET;

require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
?>
</head>

<body>

	<?php

	function reloadElement($jsGUI, $we_transaction, $we_doc, $id){
		$identifier = array_pop(explode('_', $id));
		$uniqid = 'entry_' . $identifier;

		$content = '<div id="' . $uniqid . '">
				<a name="f' . $uniqid . '"></a>
				<table style="margin-left:30px;" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="defaultfont" width="600">
					<table cellpadding="6" cellspacing="0" border="0">' .
			$we_doc->getFieldHTML($we_doc->getElement("wholename" . $identifier), $uniqid) .
			'	</table>
				</td>
				<td width="150" class = "defaultfont" valign="top">' .
			we_html_button::create_button_table(
				array(
				we_html_button::create_button('image:btn_add_field', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_entry_at_class','" . $we_transaction . "','" . $uniqid . "');"),
				we_html_button::create_button('image:btn_direction_up', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", true, 22, 22, "", "", false, false, "_" . $identifier),
				we_html_button::create_button('image:btn_direction_down', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", true, 22, 22, "", "", false, false, "_" . $identifier),
				we_html_button::create_button('image:btn_function_trash', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_entry_at_class','" . $we_transaction . "','" . $uniqid . "');")
				), 5) .
			'</td>
			</tr>
			</table>
			<div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;"></div>' . we_html_tools::getPixel(2, 10) .
			'</div>
				</div>';

		echo $jsGUI->getResponse('reload', $uniqid, $content) .
			we_html_element::jsElement('if(typeof tinyMceConfObject__' . $we_doc->getElement("wholename" . $identifier) . 'default === \'object\'){_EditorFrame.getContentEditor().tinyMceInitialize(tinyMceConfObject__' . $we_doc->getElement("wholename" . $identifier) . 'default)};');

		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
	}

	switch($cmd){

		case 'object_insert_entry_at_class':
			if($id != false){
				$after = array_pop(explode('_', $id));
				$afterid = $id;
			} else {
				$after = false;
				$afterid = false;
			}
			$identifier = str_replace('.', '', uniqid('', true));
			$uniqid = 'entry_' . $identifier;
			$we_doc->addEntryToClass($identifier, $after);

			$content = '<div id="' . $uniqid . '">
				<a name="f' . $uniqid . '"></a>
				<table style="margin-left:30px;" cellpadding="0" cellspacing="0" border="0">
				<tr><td class="defaultfont" width="600">
				<table cellpadding="6" cellspacing="0" border="0">' .
				$we_doc->getFieldHTML($we_doc->getElement("wholename" . $identifier), $uniqid) .
				'</table>
				</td>
				<td width="150" class = "defaultfont" valign="top">' .
				we_html_button::create_button_table(
					array(
					we_html_button::create_button('image:btn_add_field', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_entry_at_class','" . $we_transaction . "','" . $uniqid . "');"),
					we_html_button::create_button('image:btn_direction_up', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", true, 22, 22, "", "", false, false, "_" . $identifier),
					we_html_button::create_button('image:btn_direction_down', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", true, 22, 22, "", "", false, false, "_" . $identifier),
					we_html_button::create_button('image:btn_function_trash', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_entry_at_class','" . $we_transaction . "','" . $uniqid . "');")
					), 5) .
				'</td>
				</tr>
				</table>
				<div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;"></div>' . we_html_tools::getPixel(2, 10) .
				'</div>
				</div>';

			echo $jsGUI->getResponse('add', $uniqid, $content, $afterid);

			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
			break;

		case 'object_reload_entry_at_class':
			$identifier = array_pop(explode('_', $id));
			$fieldname = $we_doc->getElement("wholename" . $identifier);
			$we_doc->setElement($fieldname . 'default', '');
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_up_meta_at_class':
			$we_doc->upMetaAtClass($_REQUEST['we_cmd'][3], $_REQUEST['we_cmd'][4]);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_down_meta_at_class':
			$we_doc->downMetaAtClass($_REQUEST['we_cmd'][3], $_REQUEST['we_cmd'][4]);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_insert_meta_at_class':
			$we_doc->addMetaToClass($_REQUEST['we_cmd'][3], $_REQUEST['we_cmd'][4]);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_delete_meta_class':
			$we_doc->removeMetaFromClass($_REQUEST['we_cmd'][3], $_REQUEST['we_cmd'][4]);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_del_all_users':
			$we_doc->del_all_users($_REQUEST['we_cmd'][3]);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_add_user_to_field':
			$we_doc->add_user_to_field($_REQUEST['we_cmd'][3], $_REQUEST['we_cmd'][4]);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_del_user_from_field':
			$we_doc->del_user_from_field($_REQUEST['we_cmd'][3], $_REQUEST['we_cmd'][4]);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_remove_image_at_class';
			$we_doc->remove_image($_REQUEST['we_cmd'][3]);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_delete_link_at_class':
			if(isset($we_doc->elements[$_REQUEST['we_cmd'][3]])){
				unset($we_doc->elements[$_REQUEST['we_cmd'][3]]);
			}
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_change_link_at_class':
			$we_doc->changeLink($_REQUEST['we_cmd'][3]);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;
		case 'object_change_multiobject_at_class':
			while($we_doc->elements[$_REQUEST['we_cmd'][3] . 'count']['dat'] > 0){
				$we_doc->removeMetaFromClass($_REQUEST['we_cmd'][3], 0);
			}
			$we_doc->removeMetaFromClass($_REQUEST['we_cmd'][3], 0);
			reloadElement($jsGUI, $we_transaction, $we_doc, $id);
			break;

		case 'object_delete_entry_at_class':
			if(isset($id)){
				$identifier = array_pop(explode('_', $id));
				$uniqid = 'entry_' . $identifier;
				$we_doc->removeEntryFromClass($identifier);
				echo $jsGUI->getResponse('delete', $uniqid);
				$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
			}
			break;

		case 'object_up_entry_at_class':
			$sort = $we_doc->getElement('we_sort');

			if(isset($id)){
				$identifier = array_pop(explode('_', $id));
				$uniqid = 'entry_' . $identifier;
				$we_doc->upEntryAtClass($identifier);
				echo $jsGUI->getResponse('up', $uniqid);
				$ret = '';
				foreach(array_flip($sort) as $sortId){
					$field = $we_doc->elements['wholename' . $sortId]['dat'];
					if(strpos($field, 'text_') === 0){
						$ret .= '_EditorFrame.getContentEditor().tinyMceInitialize(_EditorFrame.getContentEditor().tinyMceConfObject__' . $field . 'default);
';
					}
				}
				echo $ret ? we_html_element::jsElement($ret) : '';
				$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
			}
			break;

		case 'object_down_entry_at_class':
			$sort = $we_doc->getElement('we_sort');

			if(isset($id)){
				$identifier = array_pop(explode('_', $id));
				$uniqid = 'entry_' . $identifier;
				$we_doc->downEntryAtClass($identifier);
				echo $jsGUI->getResponse('down', $uniqid);
				$ret = '';
				foreach(array_flip($sort) as $sortId){
					$field = $we_doc->elements['wholename' . $sortId]['dat'];
					if(strpos($field, 'text_') === 0){
						$ret .= '_EditorFrame.getContentEditor().tinyMceInitialize(_EditorFrame.getContentEditor().tinyMceConfObject__' . $field . 'default);
';
					}
				}
				echo $ret ? we_html_element::jsElement($ret) : '';
				$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
			}
			break;

		default:
			break;
	}
	?>
</body>
</html>