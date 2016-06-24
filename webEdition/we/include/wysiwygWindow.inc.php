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
$charsetHandler = new we_base_charsetHandler();
$whiteList = [];
$charsets = $charsetHandler->charsets;

$fields = array(
	'cmd' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
	'name' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1),
	'windowwidth' => we_base_request::_(we_base_request::UNIT, 'we_cmd', 0, 2),
	'windowheight' => we_base_request::_(we_base_request::UNIT, 'we_cmd', 0, 3),
	'empty' => we_base_request::_(we_base_request::CMD, 'we_cmd', 0, 4),
	'propstring' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5),
	'classname' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 6),
	'fontnames' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 7),
	'outsidewe' => we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8),
	'width' => we_base_request::_(we_base_request::UNIT, 'we_cmd', 0, 9),
	'height' => we_base_request::_(we_base_request::UNIT, 'we_cmd', 0, 10),
	'xml' => we_base_request::_(we_base_request::BOOL, 'we_cmd', true, 11),
	'removeFirstParagraph' => we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 12),
	'bgcolor' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 13),
	'baseHref' => we_base_request::_(we_base_request::URL, 'we_cmd', '', 14),
	'charset' => we_base_request::_(we_base_request::STRING, 'we_cmd', DEFAULT_CHARSET, 15)? : DEFAULT_CHARSET,
	'cssClasses' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 16),
	'Language' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 17),
	'documentCss' => we_base_request::_(we_base_request::CMD, 'we_cmd', '', 18),
	'origName' => we_base_request::_(we_base_request::CMD, 'we_cmd', '', 19),
	'tinyParams' => we_base_request::_(we_base_request::CMD, 'we_cmd', '', 20),
	'contextmenu' => we_base_request::_(we_base_request::CMD, 'we_cmd', '', 21),
	'isInPopup' => we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 22),
	'isInFrontend' => we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 23),
	'templates' => we_base_request::_(we_base_request::INTLIST, 'we_cmd', '', 24),
	'formats' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 25),
	'imagestartid' => we_base_request::_(we_base_request::INT, 'we_cmd', '', 26),
	'galleryTemplates' => we_base_request::_(we_base_request::INTLIST, 'we_cmd', '', 27),
	'fontsizes' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 28),
);


if($fields['charset'] != DEFAULT_CHARSET && $charsets && is_array($charsets)){
	$found = false;
	$tmp = strtolower($fields['charset']);
	foreach($charsets as $v){
		if(!empty($v['charset'])){
			if(strtolower($v['charset']) == $tmp){
				$found = true;
				break;
			}
		}
	}
	if(!$found){
		t_e('charset not found for wysiwyg', $fields['charset']);
		exit();
	}
}

we_html_tools::headerCtCharset('text/html', $fields['charset']);

if(!($fields['isInFrontend'] && $fields['empty'] === 'frontend')){
	we_html_tools::protect();
}

if(preg_match('%^.+_te?xt\[.+\]$%i', $fields['name'])){
	$fieldName = preg_replace('/^.+_te?xt\[(.+)\]$/', '${1}', $fields['name']);
} else if(preg_match('|^.+_input\[.+\]$|i', $fields['name'])){
	$fieldName = preg_replace('/^.+_input\[(.+)\]$/', '${1}', $fields['name']);
} else if(preg_match('|^we_ui.+\[.+\]$|i', $fields['name'])){//we_user_input
	$fieldName = preg_replace('/^we_ui.+\[(.+)\]$/', '${1}', $fields['name']);
	$writeToFrontend = true;
}

echo we_html_tools::getHtmlTop($fieldName, $fields['charset']);

if(isset($fieldName) && we_base_request::_(we_base_request::BOOL, 'we_okpressed')){
	if(!isset($writeToFrontend)){
		if(preg_match('%^(.+_te?xt)\[.+\]$%i', $fields['name'])){
			$reqName = preg_replace('/^(.+_te?xt)\[.+\]$/', '${1}', $fields['name']);
		} else if(preg_match('|^(.+_input)\[.+\]$|i', $fields['name'])){
			$reqName = preg_replace('/^(.+_input)\[.+\]$/', '${1}', $fields['name']);
		}
		$openerDocument = 'WE().layout.weEditorFrameController.getVisibleEditorFrame().document';
	} else {
		$reqName = str_replace('[' . $fieldName . ']', '', $fields['name']);
		$openerDocument = 'top.opener.document';
	}

	$value = preg_replace('|(</?)script([^>]*>)|i', '${1}scr"+"ipt${2}', strtr(we_base_request::_(we_base_request::RAW_CHECKED, $reqName, '', $fieldName), array(
		"\r" => '\r',
		"\n" => '\n',
		"'" => '&#039;'
	)));
	$replacements = array(
		'"' => '\"',
		"\xe2\x80\xa8" => '',
		"\xe2\x80\xa9" => '',
	);
	$taValue = strtr($value, $replacements);
	$divValue = isset($writeToFrontend) ? $taValue : strtr(we_document::parseInternalLinks($value, 0), $replacements);

	echo we_html_element::jsElement('
try{
	' . $openerDocument . '.getElementById("' . $fields['name'] . '").value = \'' . $taValue . '\';
} catch(err){}
try{
	' . $openerDocument . '.getElementById("div_wysiwyg_' . $fields['name'] . '").innerHTML=\'' . $divValue . '\';
} catch(err){}
try{
	WE().layout.weEditorFrameController.getVisibleEditorFrame().seeMode_dealWithLinks();
} catch(err){}

top.close();');
	?>

	</head>
	<body class="weDialogBody">
		<?php
	} else {
		echo STYLESHEET;
		?>
	</head>
	<body class="weDialogBody" onload="top.focus();">
		<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" name="we_form" method="post">
			<input type="hidden" name="we_okpressed" value="1" />
			<?php
			$pos = 0;
			foreach($fields as $v){
				echo we_html_element::htmlHidden('we_cmd[' . ($pos++) . ']', $v);
			}

			$e = new we_wysiwyg_editor(
					$fields['name'], '100%', '100%', $fields['empty'], $fields['propstring'], $fields['bgcolor'], '', $fields['classname'], $fields['fontnames'], $fields['outsidewe'], $fields['xml'], $fields['removeFirstParagraph'], true, $fields['baseHref'], $fields['charset'], $fields['cssClasses'], $fields['Language'], '', true, $fields['isInFrontend'], 'top', true, $fields['documentCss'], $fields['origName'], $fields['tinyParams'], $fields['contextmenu'], true, $fields['templates'], $fields['formats'], $fields['imagestartid'], $fields['galleryTemplates'], $fields['fontsizes']
			);
			$cancelBut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close()");
			$okBut = we_html_button::create_button(we_html_button::OK, "javascript:document.we_form.submit();");

			echo we_html_element::htmlDiv(
					array('style' => 'position:absolute;top:0;bottom:42px;left:0px;right:0px;overflow:hidden;margin:0px'), we_wysiwyg_editor::getHeaderHTML(false, $fields['isInFrontend']) . $e->getHTML()
			) .
			we_html_element::htmlDiv(array('style' => 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;'), we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'height:100%;'), we_html_button::position_yes_no_cancel($okBut, $cancelBut)));
			?>
		</form>
		<?php
	}
	?>
</body>
</html>