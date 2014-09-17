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

$charset = (isset($GLOBALS['we_doc']->Charset) && $GLOBALS['we_doc']->Charset ? //	send charset which might be determined in template
				$GLOBALS['we_doc']->Charset : DEFAULT_CHARSET);


we_html_tools::headerCtCharset('text/html', $charset);

$_editMode = (isset($_previewMode) && $_previewMode == 1 ? 0 : 1);
$parts = $GLOBALS['we_doc']->getFieldsHTML($_editMode);
if(is_array($GLOBALS['we_doc']->DefArray)){
	foreach($GLOBALS['we_doc']->DefArray as $n => $v){
		if(is_array($v)){
			if(isset($v["required"]) && $v["required"] && $_editMode){
				$parts[] = array(
					"headline" => "",
					"html" => '*' . g_l('global', "[required_fields]"),
					"space" => 0,
					"name" => str_replace('.', '', uniqid('', true)),
				);
				break;
			}
		}
	}
}

echo we_html_tools::getHtmlTop('', $charset, 5);
if($GLOBALS['we_doc']->CSS){
	$cssArr = makeArrayFromCSV($GLOBALS['we_doc']->CSS);
	foreach($cssArr as $cs){
		echo we_html_element::cssLink(id_to_path($cs));
	}
}

$we_doc = $GLOBALS['we_doc'];

$jsGUI = new weOrderContainer("_EditorFrame.getContentEditor()", "objectEntry");
echo $jsGUI->getJS(JS_DIR) .
 we_html_multiIconBox::getJs();
?>

<script type="text/javascript"><!--
	function toggleObject(id) {
		var elem = document.getElementById(id);
		if (elem.style.display == "none") {
			elem.style.display = "block";
		} else {
			elem.style.display = "none";
		}
	}
//-->
</script>
<?php
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
echo we_html_element::jsScript(JS_DIR . 'windows.js') .
 STYLESHEET;
?>
</head>

<body class="weEditorBody" onunload="doUnload()">
	<form name="we_form" method="post"><?php
		echo we_class::hiddenTrans();

		if($_editMode){

			echo we_html_multiIconBox::_getBoxStart("100%", g_l('weClass', "[edit]"), md5(uniqid(__FILE__, true)), 30) .
			$jsGUI->getContainer() .
			we_html_multiIconBox::_getBoxEnd("100%");

			foreach($parts as $idx => $part){

				echo '<div id="' . $part['name'] . '">
			<a name="f' . $part['name'] . '"></a>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td class="defaultfont" width="100%">
					<table style="margin-left:30px;" cellpadding="0" cellspacing="0" border="0">
						<tr><td class="defaultfont">' . $part["html"] . '</td></tr>
					</table>
				</td>
			</tr>
			<tr>
				<td><div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;"></div></td>
			</tr>
			</table>
			</div>' .
				we_html_element::jsElement('objectEntry.add(document, \'' . $part['name'] . '\', null);');
			}
		} else {
			if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
				$_msg = '';
			}
			echo we_SEEM::parseDocument(we_html_multiIconBox::getHTML('', '100%', $parts, 30, '', -1, '', '', false));
		}
		?>
		<input type="hidden" name="we_complete_request" value="1"/>
	</form>
</body><?php echo we_html_element::jsElement('setTimeout("doScrollTo();",100);'); ?>
</html>