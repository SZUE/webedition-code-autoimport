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
we_html_tools::protect();
require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');
//
//	---> Initalize the document
//

$cmd = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', $we_transaction, 1);

$identifier = we_base_request::_(we_base_request::STRING, 'we_cmd', false, 2);

$jsGUI = new we_gui_OrderContainer("_EditorFrame.getContentEditor()", "objectEntry");

$we_doc = new we_objectFile();

$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
$we_doc->we_initSessDat($we_dt);

//
//	---> Setting the Content-Type
//

$charset = (!empty($we_doc->elements["Charset"]["dat"]) ? //	send charset which might be determined in template
		$we_doc->elements["Charset"]["dat"] :
		DEFAULT_CHARSET);

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
		echo we_html_element::cssLink(id_to_path($cs));
	}
}
echo STYLESHEET;


require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
?>
</head>

<body><?php
	switch($cmd){
		case "object_reload_entry_at_object":
		case 'object_up_meta_at_object':
		case 'object_down_meta_at_object':
		case 'object_insert_meta_at_object':
		case 'object_delete_meta_at_object':
		case 'object_change_objectlink':
		case 'object_remove_image_at_object':
		case 'object_delete_link_at_object':
		case 'object_change_link_at_object':
			$temp = explode("_", $identifier);
			$type = array_shift($temp);
			$name = implode("_", $temp);

			$db = new DB_WE();
			$table = OBJECT_FILES_TABLE;
			switch($cmd){
				case 'object_insert_meta_at_object':
					$we_doc->addMetaToObject($name, we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3));
					break;
				case "object_delete_meta_at_object":
					$we_doc->removeMetaFromObject($name, we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3));
					break;
				case "object_down_meta_at_object":
					$we_doc->downMetaAtObject($name, we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3));
					break;
				case "object_up_meta_at_object":
					$we_doc->upMetaAtObject($name, we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3));
					break;
				case "object_change_objectlink":
					$we_doc->i_getLinkedObjects();
					break;
				case "object_remove_image_at_object":
					$we_doc->remove_image($name);
					break;
				case "object_delete_link_at_object":
					if(isset($we_doc->elements[$name])){
						unset($we_doc->elements[$name]);
					}
					break;
				case "object_change_link_at_object":
					$we_doc->changeLink($name);
					break;
			}


			$content = '
<div id="' . $identifier . '" class="objectFileElement">
	<div id="f' . $identifier . '" class="default defaultfont">
	' . $we_doc->getFieldHTML($name, $type, array()) . '
	<tr><td><div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;"></div></td></tr>
	</div>
</div>';
			$yuiSuggest = &weSuggest::getInstance();
			$fildsObj = $yuiSuggest->getyuiAcFields();
			// AC-FIEDS BY ID
			$fildsById = array();
			foreach(array_keys($fildsObj) as $i => $key){
				$fildsById[] = '"' . $key . '":' . $i;
			}

			//FIXME: this will kill all other fields. we nee code to append & delete the following fields
			$js = '';/*
targetF.YAHOO.autocoml.yuiAcFieldsById = {' . implode(',', $fildsById) . '};
targetF.YAHOO.autocoml.yuiAcFields = [' . implode(',', $fildsObj) . '];
targetF.YAHOO.autocoml.init();
';*/

			echo $jsGUI->getResponse('reload', $identifier, $content, false, $js);

			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
			break;

		default:
			break;
	}
	?>

</body>

</html>