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
echo (!(isset($GLOBALS["we_print_not_htmltop"]) && $GLOBALS["we_print_not_htmltop"]) ? we_html_tools::getHtmlTop() : '') .
 STYLESHEET;

$_row = 0;
$_starttable = new we_html_table(array("border" => 0, "cellpadding" => 7, "cellspacing" => 0), 3, 1);
$_starttable->setCol($_row++, 0, array("class" => "defaultfont titleline", "colspan" => 3), $title);
$_starttable->setCol($_row++, 0, array("class" => "defaultfont", "colspan" => 3), "");
$_starttable->setCol($_row++, 0, array("align" => "center"), $content);

echo we_html_element::cssLink(CSS_DIR . 'tools_home.css') .
 (isset($GLOBALS["we_head_insert"]) ? $GLOBALS["we_head_insert"] : "");
?>

</head>

<?php
$tooldir = ($tool === 'weSearch' ? WE_INCLUDES_DIR . 'we_tools/' : WE_APPS_DIR);
?>

<body bgcolor="#F0EFF0" onload="loaded = 1;var we_is_home = 1;">
	<div id="tabelle"><?php echo $_starttable->getHtml(); ?></div>
	<div id="modimage"><img src="<?php echo $tooldir . $tool . '/layout/home.gif'; ?>" width="335" height="329" /></div>

	<?php echo (isset($GLOBALS["we_body_insert"]) ? $GLOBALS["we_body_insert"] : ""); ?>
</body>

</html>