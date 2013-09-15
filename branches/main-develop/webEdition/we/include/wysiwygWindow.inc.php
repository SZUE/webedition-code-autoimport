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
$defaultCharset = "ISO-8859-1";

$_charsetHandler = new charsetHandler();
$_charsets = array();
$whiteList = array();
$_charsets = $_charsetHandler->charsets;

if(!empty($_charsets) && is_array($_charsets)){
	foreach($_charsets as $k => $v){
		if(isset($v['charset']) && $v['charset'] != ''){
			$whiteList[] = strtolower($v['charset']);
		}
	}
}

if(isset($_REQUEST['we_cmd'][15])){
	if($_REQUEST['we_cmd'][15] == ''){
		$_REQUEST['we_cmd'][15] = $defaultCharset;
	} else{
		if(!in_array(strtolower($_REQUEST['we_cmd'][15]), $whiteList)){
			exit();
		}
	}
}

@we_html_tools::headerCtCharset('text/html', ($_REQUEST['we_cmd'][15] ? $_REQUEST['we_cmd'][15] : $defaultCharset));

if(!(isset($_REQUEST['we_cmd'][23]) && $_REQUEST['we_cmd'][23] == 1 && we_cmd_dec(4) == 'frontend')){
	we_html_tools::protect();
}

//$we_dt = isset($_SESSION['weS']['we_data'][$_REQUEST['we_cmd'][4]]) ? $_SESSION['weS']['we_data'][$_REQUEST['we_cmd'][4]] : '';
//include (WE_INCLUDES_PATH . "we_editors/we_init_doc.inc.php");

if(preg_match('%^.+_te?xt\[.+\]$%i', $_REQUEST['we_cmd'][1])){
	$fieldName = preg_replace('/^.+_te?xt\[(.+)\]$/', '\1', $_REQUEST['we_cmd'][1]);
} else if(preg_match('|^.+_input\[.+\]$|i', $_REQUEST['we_cmd'][1])){
	$fieldName = preg_replace('/^.+_input\[(.+)\]$/', '\1', $_REQUEST['we_cmd'][1]);
} else if(preg_match('|^we_ui.+\[.+\]$|i', $_REQUEST['we_cmd'][1])){//we_user_input
	$fieldName = preg_replace('/^we_ui.+\[(.+)\]$/', '\1', $_REQUEST['we_cmd'][1]);
	$writeToFrontend = true;
}

we_html_tools::htmlTop(sprintf("", $fieldName), ($_REQUEST['we_cmd'][15] ? $_REQUEST['we_cmd'][15] : $defaultCharset));

if(isset($fieldName) && isset($_REQUEST["we_okpressed"]) && $_REQUEST["we_okpressed"]){
	if(!isset($writeToFrontend)){
		if(preg_match('%^(.+_te?xt)\[.+\]$%i', $_REQUEST['we_cmd'][1])){
			$reqName = preg_replace('/^(.+_te?xt)\[.+\]$/', '\1', $_REQUEST['we_cmd'][1]);
		} else if(preg_match('|^(.+_input)\[.+\]$|i', $_REQUEST['we_cmd'][1])){
			$reqName = preg_replace('/^(.+_input)\[.+\]$/', '\1', $_REQUEST['we_cmd'][1]);
		}
		$openerDocument = 'top.opener.top.weEditorFrameController.getVisibleEditorFrame().document';
	} else{
		$reqName = str_replace('[' . $fieldName . ']', '', $_REQUEST['we_cmd'][1]);
		$openerDocument = 'top.opener.document';
	}

	$value = preg_replace('|script|i', 'scr"+"ipt', str_replace(array("\r", "\n", "'"), array("\\r", "\\n", "&#039;"), $_REQUEST[$reqName][$fieldName]));
	$taValue = str_replace("\"", "\\\"", $value);
	$divValue = isset($writeToFrontend) ? $taValue : str_replace("\"", "\\\"", parseInternalLinks($value, 0));

	echo we_html_element::jsElement('
		try{
			' . $openerDocument . '.getElementById("' . $_REQUEST['we_cmd'][1] . '").value = \'' . $taValue . '\';
		} catch(err){}
		try{
			' . $openerDocument . '.getElementById("div_wysiwyg_' . $_REQUEST['we_cmd'][1] . '").innerHTML = \'' . $divValue . '\';
		} catch(err){}
		try{
			top.opener.top.weEditorFrameController.getVisibleEditorFrame().seeMode_dealWithLinks();
		} catch(err){}

		top.close();
	');
	?>

	</head>
	<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
		<?php
	} else{
		$cancelBut = we_button::create_button('cancel', "javascript:top.close()");
		$okBut = we_button::create_button('ok', "javascript:weWysiwygSetHiddenText();document.we_form.submit();");

		print STYLESHEET;
		echo we_html_element::jsScript(JS_DIR . 'windows.js') .
		we_html_element::jsElement('top.focus();');
		?>
	</head>
	<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" style="background-image:url(<?php echo IMAGE_DIR;?>backgrounds/aquaBackground.gif);">
		<form action="<?php print $_SERVER['SCRIPT_NAME']; ?>" name="we_form" method="post">
			<input type="hidden" name="we_okpressed" value="1" />
			<?php
			foreach($_REQUEST['we_cmd'] as $i => $v){
				print '<input type="hidden" name="we_cmd[' . $i . ']" value="' . $_REQUEST['we_cmd'][$i] . '" />' . "\n";
			}

			/*
			  1 = name
			  2 = width
			  3 = height
			  4 = empty
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
			  18 = documentCss
			  19 = origName
			  20 = tinyParams
			  21 = contextmenu
			  22 = isInPopup
			  23 = isInFrontend
			 *
			 */

			$e = new we_wysiwyg(
					$_REQUEST['we_cmd'][1],
					$_REQUEST['we_cmd'][2],
					$_REQUEST['we_cmd'][3],
					($_REQUEST['we_cmd'][4] ? we_cmd_dec(4) : ''),
					$_REQUEST['we_cmd'][5],
					$_REQUEST['we_cmd'][13],
					'',
					$_REQUEST['we_cmd'][6],
					$_REQUEST['we_cmd'][7],
					$_REQUEST['we_cmd'][8],
					$_REQUEST['we_cmd'][11],
					$_REQUEST['we_cmd'][12],
					true,
					$_REQUEST['we_cmd'][14],
					$_REQUEST['we_cmd'][15],
					$_REQUEST['we_cmd'][16],
					$_REQUEST['we_cmd'][17],
					'',
					true,
					$_REQUEST['we_cmd'][23],
					'top',
					true,
					we_cmd_dec(18),
					we_cmd_dec(19),
					we_cmd_dec(20),
					we_cmd_dec(21),
					true);

			print we_wysiwyg::getHeaderHTML() . $e->getHTML() .
				'<div style="height:8px"></div>' . we_button::position_yes_no_cancel($okBut, $cancelBut);
			?>
		</form>
		<?php
	}
	?>
</body>
</html>
