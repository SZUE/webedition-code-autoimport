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


include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_inc_min.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_browser_check.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_tag.inc.php");

we_html_tools::protect();

if(isset($GLOBALS['we_doc']->Charset)){	//	send charset which might be determined in template
	we_html_tools::headerCtCharset('text/html',$GLOBALS['we_doc']->Charset);
}

$_editMode = (isset($_previewMode) && $_previewMode == 1 ? 0 : 1);
$parts = $GLOBALS['we_doc']->getFieldsHTML($_editMode);

if (is_array($GLOBALS['we_doc']->DefArray)){
	foreach($GLOBALS['we_doc']->DefArray as $n=>$v) {
		if(is_array($v)){
			if(isset($v["required"]) && $v["required"] && $_editMode) {
				array_push($parts,
							array(
								"headline"=>"",
								"html"=>'*'.g_l('global',"[required_fields]"),
								"space"=>0,
								"name"=>uniqid(""),
							)
						);
				break;
			}

		}
	}
}

we_html_tools::htmlTop();
if($GLOBALS['we_doc']->CSS){
	$cssArr = makeArrayFromCSV($GLOBALS['we_doc']->CSS);
	foreach($cssArr as $cs){
		print '<link href="'.id_to_path($cs).'" rel="styleSheet" type="text/css" />'."\n";

	}
}

$we_doc = $GLOBALS['we_doc'];

$jsGUI = new weOrderContainer("_EditorFrame.getContentEditor()", "objectEntry");
echo $jsGUI->getJS(WEBEDITION_DIR."js");

echo we_multiIconBox::getJs();
?>

<script type="text/javascript">
<!--
function toggleObject(id) {
	var elem = document.getElementById(id);
	if(elem.style.display == "none") {
		elem.style.display = "block";
	} else {
		elem.style.display = "none";
	}
}
//-->
</script>
<?php echo we_html_element::jsScript(JS_DIR.'windows.js');
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_editors/we_editor_script.inc.php");
print STYLESHEET; ?>
</head>

<body class="weEditorBody" onUnload="doUnload()">
	<form name="we_form" method="post"><?php $GLOBALS['we_doc']->pHiddenTrans();

if($_editMode){

	echo we_multiIconBox::_getBoxStart("100%", g_l('weClass',"[edit]"), uniqid(""),30);

	echo $jsGUI->getContainer();

	echo we_multiIconBox::_getBoxEnd("100%");

	foreach($parts as $idx => $part) {
		$uniqid = uniqid("");

		$content =		'<div id="'.$part['name'].'">'
					.	'<a name="f'.$part['name'].'"></a>'
					.	'<table cellpadding="0" cellspacing="0" border="0" width="100%">'
					.	'<tr>'
					.	'<td class="defaultfont" width="100%">'
					.	'<table style="margin-left:30px;" cellpadding="0" cellspacing="0" border="0">'
					.	'<tr>'
					.	'<td class="defaultfont">'.$part["html"].'</td>'
					.	'</tr>'
					.	'</table>'
					.	'</td>'
					.	'</tr>'
					.	'<tr>'
					.	'<td><div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;">'.we_html_tools::getPixel(1,1).'</div></td>'
					.	'</tr>'
					.	'</table>'
					.	'</div>'
					.	'<script type="text/javascript">'
					.	'objectEntry.add(document, \''.$part['name'].'\', null);'
					.	'</script>';

		echo $content;

	}

} else {
	if($_SESSION["we_mode"] == "normal"){
		$_msg = "";
	}
	print we_SEEM::parseDocument(we_multiIconBox::getHTML("","100%",$parts, 30, "", -1, "", "", false));
}


?>
	</form>
</body><script  type="text/javascript">setTimeout("doScrollTo();",100);</script>

</html>