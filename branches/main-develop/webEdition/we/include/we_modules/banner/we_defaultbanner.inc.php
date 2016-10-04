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
echo we_html_tools::getHtmlTop(g_l('modules_banner', '[defaultbanner]'));

if(we_base_request::_(we_base_request::BOOL, "ok")){
	$GLOBALS['DB_WE']->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter(['tool' => 'banner',
			'pref_name' => 'DefaultBannerID',
			'pref_value' => we_base_request::_(we_base_request::INT, "DefaultBannerID", 0)
			]));

	echo we_html_element::jsElement('top.close();') . '</head><body></body></html>';
	exit();
}
$yuiSuggest = & weSuggest::getInstance();

function formBannerChooser($width = "", $table = BANNER_TABLE, $idvalue = 0, $idname = ''){
	$yuiSuggest = & weSuggest::getInstance();
	$path = id_to_path($idvalue, $table);
	$textname = md5(uniqid(__FUNCTION__, true));
	$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_banner_selector',document.we_form.elements['" . $idname . "'].value,'" . $idname . "','" . $textname . "')");

	$yuiSuggest->setAcId("Path");
	$yuiSuggest->setContentType("folder");
	$yuiSuggest->setInput($textname, $path);
	$yuiSuggest->setMaxResults(10);
	$yuiSuggest->setMayBeEmpty(false);
	$yuiSuggest->setResult($idname, $idvalue);
	$yuiSuggest->setSelector(weSuggest::DirSelector);
	$yuiSuggest->setTable($table);
	$yuiSuggest->setWidth($width);
	$yuiSuggest->setSelectButton($button);

	return $yuiSuggest->getHTML();
}

echo we_html_element::jsScript(WE_JS_MODULES_DIR . 'banner/we_defaultbanner.js', 'self.focus();') .
 weSuggest::getYuiFiles();
?>
</head>
<body class="weDialogBody" onunload="doUnload()">
	<form name="we_form" action="<?= $_SERVER["SCRIPT_NAME"]; ?>" method="post"><input type="hidden" name="ok" value="1" /><input type="hidden" name="we_cmd[0]" value="<?= we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0); ?>" />
		<?php
		$DefaultBannerID = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="banner" AND pref_name="DefaultBannerID"');
		$content = formBannerChooser(300, BANNER_TABLE, $DefaultBannerID, "DefaultBannerID");
$yes_button = we_html_button::create_button(we_html_button::SAVE, "javascript:we_save();");
		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");
		$buttons = we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button);

		echo we_html_tools::htmlDialogLayout($content, g_l('modules_banner', '[defaultbanner]'), $buttons, "100%", 30, 175);
		?>
	</form>
	<?= $yuiSuggest->getYuiJs(); ?>
</body>
</html>
