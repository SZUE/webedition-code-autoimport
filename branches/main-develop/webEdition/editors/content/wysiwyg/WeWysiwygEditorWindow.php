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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

$fieldName = $_REQUEST['we_cmd'][1];
we_html_tools::htmlTop(sprintf(g_l('wysiwyg', "[window_title]"), $fieldName), 'UTF-8');

if(isset($fieldName) && isset($_REQUEST["we_okpressed"]) && $_REQUEST["we_okpressed"]){


	$newHTML = $_REQUEST[$fieldName];
	$_SESSION['weS']['WEAPP_' . $_REQUEST['we_cmd'][0] . '_' . $_REQUEST['we_cmd'][1]] = $newHTML;
	$newHTMLoldA = preg_replace('|(</?)script([^>]*>)|i', '\\1scr"+"ipt\\2', $newHTML);
	$newHTMLoldB = preg_replace('|(</?)script([^>]*>)|i', '\\1scr"+"ipt\\2', parseInternalLinks($newHTML, 0));

	$newHTMLencA = base64_encode(oldHtmlspecialchars($newHTMLoldA));
	$newHTMLencB = base64_encode($newHTMLoldB);

	echo we_html_element::jsElement('
if (opener.document.getElementById("' . $_REQUEST["we_cmd"][1] . '")) {
	opener.we_ui_controls_WeWysiwygEditor.setData("' . $_REQUEST["we_cmd"][1] . '", "' . $newHTMLencA . '");
}
if (opener.document.getElementById("' . $_REQUEST["we_cmd"][1] . '_View")) {
	opener.we_ui_controls_WeWysiwygEditor.setDataView("' . $_REQUEST["we_cmd"][1] . '", "' . $newHTMLencB . '");
}

window.close();');
	?>
	</head>
	<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
		<?php
	} else {

		echo STYLESHEET .
		we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('top.focus();');
		?>
	</head>
	<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" style="background-image:url(<?php echo IMAGE_DIR; ?>backgrounds/aquaBackground.gif);">
		<form action="<?php print $_SERVER['SCRIPT_NAME']; ?>" name="we_form" method="post">
			<input type="hidden" name="we_okpressed" value="1" />
			<?php
			foreach($_REQUEST['we_cmd'] as $i => $v){
				echo '<input type="hidden" name="we_cmd[' . $i . ']" value="' . $_REQUEST['we_cmd'][$i] . '" />';
			}

			/*  diese Liste ist wohl nicht ganz richtig
			 * => is this file still used? in which case?
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

			$e = new we_wysiwyg_editor(
				$_REQUEST['we_cmd'][1], //$name,
				$_REQUEST['we_cmd'][2], //$width,
				$_REQUEST['we_cmd'][3], //$height
				$_SESSION['weS']['WEAPP_' . $_REQUEST['we_cmd'][0] . '_' . $_REQUEST['we_cmd'][1]], //value
				$_REQUEST['we_cmd'][5], //$propstring
				$_REQUEST['we_cmd'][13], //$bgcol
				"", //$fullscreen
				$_REQUEST['we_cmd'][6], //$className
				'arial; helvetica; sans-serif,courier new; courier; mono,geneva; arial; helvetica; sans-serif,georgia; times new roman; times; serif,tahoma,times new roman; times; serif,verdana; arial; helvetica; sans-serif,wingdings', //$_REQUEST['we_cmd'][7], fontnames ,
				$_REQUEST['we_cmd'][8], //$outsideWE=false
				true, //dies ist xml
				false, //$removeFirstParagraph=true
				true, //$inlineedit=true
				'', //$baseHref
				'UTF-8', $_REQUEST["we_cmd"][14], //$cssClasses
				'', // $_REQUEST["we_cmd"][15], $Language=""
				'', //test
				$_REQUEST["we_cmd"][17], //$spell
				false //frontendEdit
			);

			$cancelBut = we_html_button::create_button('cancel', 'javascript:top.close()');
			$okBut = we_html_button::create_button('ok', 'javascript:weWysiwygSetHiddenText();document.we_form.submit();');

			echo we_wysiwyg_editor::getHeaderHTML() . $e->getHTML() .
			'<div style="height:8px"></div>' . we_html_button::position_yes_no_cancel($okBut, $cancelBut);
			?>
		</form>
	<?php } ?>
</body>
</html>