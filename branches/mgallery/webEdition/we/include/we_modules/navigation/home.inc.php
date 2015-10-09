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
echo (!empty($GLOBALS["we_print_not_htmltop"]) ? we_html_tools::getHtmlTop() : '') .
 STYLESHEET;

$_row = 0;
$_starttable = new we_html_table(array("cellpadding" => 7), 3, 1);
$_starttable->setCol($_row++, 0, array("class" => "defaultfont titleline", "colspan" => 3), g_l('navigation', '[navigation]'));
$_starttable->setCol($_row++, 0, array("class" => "defaultfont", "colspan" => 3), "");

$createNavigation = we_html_button::create_button('new_item', "javascript:we_cmd('module_navigation_new');", true, 0, 0, "", "", !permissionhandler::hasPerm('EDIT_NAVIGATION'));
$createNavigationGroup = we_html_button::create_button('new_folder', "javascript:we_cmd('module_navigation_new_group');", true, 0, 0, "", "", !permissionhandler::hasPerm('EDIT_NAVIGATION'));
$content = $createNavigation . '<br/>' . $createNavigationGroup;

$_starttable->setCol($_row++, 0, array("style" => "text-align:center"), $content);

echo we_html_element::cssLink(CSS_DIR . 'tools_home.css') .
 (isset($GLOBALS["we_head_insert"]) ? $GLOBALS["we_head_insert"] : "");
?>

</head>

<body bgcolor="#F0EFF0" onload="loaded = 1;
		var we_is_home = 1;">
	<div id="tabelle"><?php echo $_starttable->getHtml(); ?></div>
	<div id="modimage"><img src="<?php echo IMAGE_DIR . 'startscreen/navigation.gif'; ?>" width="335" height="329" /></div>

<?php echo (isset($GLOBALS["we_body_insert"]) ? $GLOBALS["we_body_insert"] : ""); ?>
</body>

</html>