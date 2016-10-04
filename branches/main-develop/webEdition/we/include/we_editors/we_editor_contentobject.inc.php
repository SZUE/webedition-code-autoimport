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

//	---> Setting the Content-Type

$charset = (!empty($we_doc->elements['Charset']['dat']) ? //	send charset which might be determined in template
		$we_doc->elements['Charset']['dat'] : DEFAULT_CHARSET);

echo we_html_tools::getHtmlTop('', $charset, 5);

//	---> initialize some vars
$jsGUI = new we_gui_OrderContainer('_EditorFrame.getContentEditor()', 'classEntry');


//	---> Loading the Stylesheets
if($we_doc->CSS){
	$cssArr = makeArrayFromCSV($we_doc->CSS);
	foreach($cssArr as $cs){
		$path = id_to_path($cs);
		if($path){
			echo we_html_element::cssLink($path);
		}
	}
}

//	---> Loading some Javascript

echo $jsGUI->getJS();
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
?>

</head>

<body onunload="doUnload()" class="weEditorBody" onload="doScrollTo();">
	<form name="we_form" method="post"><?php
		echo we_class::hiddenTrans();

		if($we_doc->ID){
			//$tableInfo = $GLOBALS['DB_WE']->metadata(OBJECT_X_TABLE . $we_doc->ID);
		}

		$sort = $we_doc->getElement("we_sort");

		$uniquename = md5(uniqid(__FILE__, true));
		$width = 800;

		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0);

		echo $we_doc->getEmptyDefaultFields() . we_html_multiIconBox::_getBoxStart($uniquename) .
		$jsGUI->getContainer([]) .
		'<div id="' . $uniquename . '_div">
 <table style="margin-left:30px;margin-bottom:15px;" class="default">
 <tr>
 <td style="vertical-align:top"></td>
 <td class="defaultfont">' .
		we_html_button::create_button('fa:btn_add_field,fa-plus,fa-lg fa-square-o', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_entry_at_class','" . we_base_request::_(we_base_request::TRANSACTION, 'we_transaction') . "');") .
		'</td>
 </tr>
 </table>
 </div>' .
		we_html_multiIconBox::_getBoxEnd();

		$js = '';
		foreach(array_keys($sort) as $identifier){
			$uniqid = "entry_" . $identifier;

			echo '<div id="' . $uniqid . '" class="objectFileElement">
<div id="f' . $uniqid . '" class="default">
<table cellpadding="6" style="float:left;">' .
			$we_doc->getFieldHTML($we_doc->getElement("wholename" . $identifier), $uniqid) .
			'</table>
		<span class="defaultfont clearfix" style="width:180px;">' .
			we_html_button::create_button('fa:btn_add_field,fa-plus,fa-lg fa-square-o', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
			we_html_button::create_button(we_html_button::DIRUP, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", true, 22, 22, "", "", false, false, "_" . $identifier) .
			we_html_button::create_button(we_html_button::DIRDOWN, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", true, 22, 22, "", "", false, false, "_" . $identifier) .
			we_html_button::create_button(we_html_button::TRASH, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_entry_at_class','" . $we_transaction . "','" . $uniqid . "');") .
			'</span>
		</div></div>';
			$js.='classEntry.add(document, \'' . $uniqid . '\', null);';
		}
		echo we_html_element::jsElement($js .
			$jsGUI->getDisableButtonJS()
		) .
		we_html_element::htmlHidden("we_complete_request", 1);
		?>
	</form>
</body>
</html>