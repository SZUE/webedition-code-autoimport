<?php
/**
 * webEdition CMS
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

include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we.inc.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we_html_tools.inc.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/wysiwyg.inc.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/we_wysiwyg.class.inc.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/html/we_button.inc.php");

include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/charsetHandler.class.php");

$defaultCharset = "UTF-8";

@header("Content-Type: text/html; charset=UTF-8");

protect();

$we_button = new we_button();


$fieldName = $_REQUEST["we_cmd"][1];
htmlTop(
		sprintf($GLOBALS["l_wysiwyg"]["window_title"], $fieldName), $defaultCharset);

if (isset($fieldName) && isset($_REQUEST["we_okpressed"]) && $_REQUEST["we_okpressed"]) {


	$newHTML = $_REQUEST[$fieldName];
	$_SESSION['WEAPP_' . $_REQUEST["we_cmd"][0] .'_'.$_REQUEST["we_cmd"][1]] = $newHTML;
	
	$newHTMLoldA=preg_replace(
					'|script|i', 
					'scr"+"ipt', 
					$newHTML);
	$newHTMLoldB=	preg_replace(
					'|script|i', 
					'scr"+"ipt', 
					parseInternalLinks($newHTML,0));			
	
	$newHTMLencA= base64_encode(htmlspecialchars($newHTMLoldA));
	$newHTMLencB= base64_encode($newHTMLoldB);
	?>
<script language="JavaScript" type="text/javascript">
	if (opener.document.getElementById('<?php print $_REQUEST["we_cmd"][1];?>')){
		opener.we_ui_controls_WeWysiwygEditor.setData('<?php print $_REQUEST["we_cmd"][1];?>','<?php print $newHTMLencA;?>');
	}
	if (opener.document.getElementById('<?php print $_REQUEST["we_cmd"][1];?>_View')){
		opener.we_ui_controls_WeWysiwygEditor.setDataView('<?php print $_REQUEST["we_cmd"][1];?>','<?php print $newHTMLencB;?>');
	}
	
	
	window.close();
</script>

</head>
<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
<?php

} else {
	$cancelBut = $we_button->create_button("cancel", "javascript:top.close()");
	$okBut = $we_button->create_button("ok", "javascript:weWysiwygSetHiddenText();document.we_form.submit();");
	
	print STYLESHEET;
	?>
<script src="<?php
	print JS_DIR;
	?>windows.js" language="JavaScript"
	type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">top.focus();</script>
</head>
<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0"
	background="<?php
	print IMAGE_DIR . 'backgrounds/aquaBackground.gif';
	?>">
<form action="<?php
	print $_SERVER['SCRIPT_NAME'];
	?>" name="we_form"
	method="post"><input type="hidden" name="we_okpressed" value="1" />
<?php
	
	foreach ($_REQUEST["we_cmd"] as $i => $v) {
		print '<input type="hidden" name="we_cmd[' . $i . ']" value="' . $_REQUEST["we_cmd"][$i] . '" />' . "\n";
	}
	
	/*  diese Liste ist wohl nicht ganz richtig
1 = name
2 = width
3 = height
4 = transaction
5 = propstring
6 = classname
7 = fontnames
8 = outsidewe
9 = tbwidth (toolbar width)
10 = tbheight
11 = xml
12 = remove first paragraph
13 = bgcolor
14 = baseHref
15 = charset
16 = cssClasses
17 = Language

*/
	
	$e = new we_wysiwyg(
			$_REQUEST["we_cmd"][1], //$name,
			$_REQUEST["we_cmd"][2], //$width,
			$_REQUEST["we_cmd"][3], //$height
			$_SESSION['WEAPP_' . $_REQUEST["we_cmd"][0] .'_'.$_REQUEST["we_cmd"][1]], //value
			$_REQUEST["we_cmd"][5], //$propstring
			$_REQUEST["we_cmd"][13], //$bgcol 
			"", //$fullscreen
			$_REQUEST["we_cmd"][6], //$className
			'arial; helvetica; sans-serif,courier new; courier; mono,geneva; arial; helvetica; sans-serif,georgia; times new roman; times; serif,tahoma,times new roman; times; serif,verdana; arial; helvetica; sans-serif,wingdings',//$_REQUEST["we_cmd"][7], fontnames ,
			$_REQUEST["we_cmd"][8], //$outsideWE=false
			true,//dies ist xml
			false,//$removeFirstParagraph=true
			true,//$inlineedit=true
			'', //$baseHref
			'UTF-8', 
			$_REQUEST["we_cmd"][14], //$cssClasses
			'',// $_REQUEST["we_cmd"][15], $Language=""
			'', //test
			$_REQUEST["we_cmd"][17],//$spell
			false //frontendEdit
			); 
	
	print we_wysiwyg::getHeaderHTML() . $e->getHTML();
	print '<div style="height:8px"></div>' . $we_button->position_yes_no_cancel($okBut, $cancelBut);
	
	?>
</form>
<?php

}

?>
</body>
</html>
