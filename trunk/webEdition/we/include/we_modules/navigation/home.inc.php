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
$createNavigation = we_html_button::create_button('new_item', "javascript:we_cmd('module_navigation_new');", true, 0, 0, "", "", !permissionhandler::hasPerm('EDIT_NAVIGATION'));
$createNavigationGroup = we_html_button::create_button('new_folder', "javascript:we_cmd('module_navigation_new_group');", true, 0, 0, "", "", !permissionhandler::hasPerm('EDIT_NAVIGATION'));
$content = $createNavigation . we_html_tools::getPixel(2, 14) . $createNavigationGroup;
$title = g_l('navigation', '[navigation]');

echo (!(isset($GLOBALS["we_print_not_htmltop"]) && $GLOBALS["we_print_not_htmltop"]) ? we_html_tools::getHtmlTop() : '') .
 STYLESHEET;

$we_head_insert = isset($GLOBALS["we_head_insert"]) ? $GLOBALS["we_head_insert"] : "";
$we_body_insert = isset($GLOBALS["we_body_insert"]) ? $GLOBALS["we_body_insert"] : "";


$_row = 0;
$_starttable = new we_html_table(array("border" => 0,
	"cellpadding" => 7,
	"cellspacing" => 0,
	"width" => 228), 3, 1);
$_starttable->setCol($_row++, 0, array("class" => "defaultfont",
	"colspan" => 3,
	"align" => "center"), "<strong>" .
		$title . "</strong>");

$_starttable->setCol($_row++, 0, array("class" => "defaultfont",
	"colspan" => 3), "");


$_starttable->setCol($_row++, 0, array("align" => "center"), $content);
?>
<style media="screen" type="text/css">
<?php
$_x_table = 50;
$_y_table = 0;

$_x_table_back = $_x_table - 10;
$_y_table_back = $_y_table + 3;

$_x_we3 = $_x_table_back + 120;
$_y_we3 = $_y_table_back + 116;
?>
	#tabelle     { position: absolute; top: 0px; left: 50px; width: 100px; height: 100px; visibility: visible; z-index: 3 }
	#hintergrund { position: absolute; top: 3px; left: 40px; width: 251px; height: 220px; visibility: visible; z-index: 2 }
	#modimage    { position: absolute; top: 131px; left: 286px; width: 335px; height: 329px; visibility: visible; z-index: 1 }

</style>

<?php echo $we_head_insert; ?>

</head>

<body bgcolor="#F0EFF0" onload="loaded = 1;">
	<div id="tabelle"><?php echo $_starttable->getHtml(); ?></div>
	<div id="hintergrund"><img src="<?php echo IMAGE_DIR . "startscreen/we_startbox_modul.gif" ?>" width="251" height="220" /></div>
	<div id="modimage"><img src="<?php echo WE_INCLUDES_DIR . 'we_modules/navigation/layout/home.gif'; ?>" width="335" height="329" /></div>

	<?php echo $we_body_insert . we_html_element::jsElement('var we_is_home = 1;'); ?>
</body>

</html>