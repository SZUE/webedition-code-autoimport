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

$charset = (!empty($GLOBALS['we_doc']->Charset) ? //	send charset which might be determined in template
		$GLOBALS['we_doc']->Charset : DEFAULT_CHARSET);


we_html_tools::headerCtCharset('text/html', $charset);

$editMode = (isset($previewMode) && $previewMode == 1 ? 0 : 1);
$parts = $GLOBALS['we_doc']->getFieldsHTML($editMode);
if(is_array($GLOBALS['we_doc']->DefArray)){
	foreach($GLOBALS['we_doc']->DefArray as $n => $v){
		if(is_array($v)){
			if(!empty($v["required"]) && $editMode){
				$parts[] = array(
					"headline" => "",
					"html" => '*' . g_l('global', '[required_fields]'),
					"name" => str_replace('.', '', uniqid('', true)),
				);
				break;
			}
		}
	}
}
$yuiSuggest = &weSuggest::getInstance();

echo we_html_tools::getHtmlTop('', $charset, 5) .
 weSuggest::getYuiFiles();
if($GLOBALS['we_doc']->CSS){
	$cssArr = makeArrayFromCSV($GLOBALS['we_doc']->CSS);
	foreach($cssArr as $cs){
		echo we_html_element::cssLink(id_to_path($cs));
	}
}

$we_doc = $GLOBALS['we_doc'];

$jsGUI = new we_gui_OrderContainer("_EditorFrame.getContentEditor()", "objectEntry");
echo $jsGUI->getJS() .
 we_html_multiIconBox::getJs();

require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
?>
</head>

<body class="weEditorBody" onload="doScrollTo();" onunload="doUnload()">
	<form name="we_form" method="post"><?php
		echo we_class::hiddenTrans();

		if($editMode){
			echo we_html_multiIconBox::_getBoxStart(g_l('weClass', '[edit]'), md5(uniqid(__FILE__, true)), 30) .
			$jsGUI->getContainer() .
			we_html_multiIconBox::_getBoxEnd();
			$js = '';
			foreach($parts as $idx => $part){

				echo '<div id="' . $part['name'] . '" class="objectFileElement">
	<div id="f' . $part['name'] . '" class="default defaultfont">
' . $part["html"] . '
</div>
</div>';
				$js.='objectEntry.add(document, \'' . $part['name'] . '\', null);';
			}
			echo we_html_element::jsElement($js);
		} else {
			/*if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
				$msg = '';
			}*/
			echo we_SEEM::parseDocument(we_html_multiIconBox::getHTML('', $parts, 30));
		}
		echo we_html_element::htmlHidden("we_complete_request", 1) .
		$yuiSuggest->getYuiJs();
		?>
	</form>
</body>
</html>