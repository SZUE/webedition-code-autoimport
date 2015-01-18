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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');


we_html_tools::protect();
$browser = we_base_browserDetect::inst();

echo we_html_tools::getHtmlTop() .
 STYLESHEET .
 we_html_element::jsScript(JS_DIR . 'images.js') .
 we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'messaging_hl.js') .
 we_html_element::jsScript(JS_DIR . 'messaging_std.js') .
 we_html_element::jsElement('
var table="' . USER_TABLE . '";
var tree_icon_dir="' . TREE_ICON_DIR . '";
var tree_img_dir="' . TREE_IMAGE_DIR . '";
var we_dir="' . WEBEDITION_DIR . '";'
		. we_modules_frame::getTree_g_l()
) .
 we_html_element::jsScript(JS_DIR . 'messaging_usel_browse.js');

//FIXME: make the js code equal to *_tree.js
?>
<script type="text/javascript"><!--

	function drawEintraege() {//FIXME: we don't have an existing document to write on, change this, as is changed in tree
		fr = messaging_usel_main.window.document;
		fr.open();
		fr.writeln("<html><head>");
		fr.writeln("<?php echo str_replace(array('script', '"'), array('scr"+"ipt', '\''), we_html_tools::getJSErrorHandler());?>");
		fr.writeln("<script type=\"text/javascript\" src=\"<?php echo JS_DIR . 'messaging_std.js'; ?>\"></" + "script>");
		fr.writeln("<script type=\"text/javascript\">");
		fr.writeln("var clickCount=0;");
		fr.writeln("var wasdblclick=0;");
		fr.writeln("var tout=null;");
		fr.writeln("top.loaded=1;");
		fr.writeln("</" + "script>");
		fr.writeln('<?php echo STYLESHEET_SCRIPT; ?>');
		fr.write("</head>");
		fr.write("<body class=\"weEditorBody\" LINK=\"#000000\" ALINK=\"#000000\" VLINK=\"#000000\" leftmargin=\"10\" topmargin=\"0\" marginheight=\"0\" marginwidth=\"10\" onunload=\"doUnload()\">");
		fr.write("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td class=\"tree\">\n<NOBR>\n");
		zeichne(top.startloc, "");
		fr.write("</NOBR>\n</td></tr></table>\n");
		fr.writeln("  <script type=\"text/javascript\">");
		fr.writeln("    var k;");

		fr.writeln("for (k = 0; k < parent.entries_selected.length; k++) {");
		fr.writeln("  parent.highlight_Elem(parent.entries_selected[k], parent.sel_color, parent.messaging_usel_main);");
		fr.writeln("}");
		fr.writeln("</" + "script>");
		fr.write("</body></html>");

		fr.close();
	}

	function loadData() {

		menuDaten.clear();

<?php
$entries = array();
$DB_WE->query('SELECT ID,ParentID,username,Permissions,Type FROM ' . USER_TABLE . ' ORDER BY username ASC');
echo "startloc=0 ;
menuDaten.add(new self.rootEntry('0','root','root'));";
while($DB_WE->next_record()){
	if($DB_WE->f('Type') == 1){
		echo "  menuDaten.add(new dirEntry('folder'," . $DB_WE->f("ID") . "," . $DB_WE->f("ParentID") . ",'" . $DB_WE->f("username") . "',false,'user','" . USER_TABLE . "','" . $p[0] . "'));";
	} else {
		$p = $DB_WE->f("Permissions");

		echo "checked = (user_array_search(\"" . $DB_WE->f('ID') . "\", opener.current_sel, \"1\", 'we_message') != -1) ? 1 : 0;" .
		"menuDaten.add(new urlEntry('user.gif'," . $DB_WE->f("ID") . "," . $DB_WE->f("ParentID") . ",'" . $DB_WE->f("username") . "','user','" . USER_TABLE . "','" . $p[0] . "', checked));";
	}
}
?>
	}

	function init_check() {
		var i;
		for (i = 0; i < opener.current_sel.length; i++) {
			if (opener.current_sel[i][0] != 'we_message') {
				continue;
			}
			check(opener.current_sel[i][1] + '&' + opener.current_sel[i][2]);
		}
	}

	function start() {
		loadData();
		drawEintraege();
	}

	var startloc = 0;


	sel_color = "#697ace";
	default_color = "#000000";

	function showContent(id) {
		top.cmd.location = "<?php echo WE_MESSAGING_MODULE_DIR; ?>edit_messaging_frameset.php?pnt=cmd&we_transaction=<?php echo $we_transaction ?>&mcmd=show_message&id=" + id;
	}

	function array_search(needle, haystack) {
		var i;

		for (i = 0; i < haystack.length; i++) {
			if (needle == haystack[i]) {
				return i;
			}
		}

		return -1;
	}


	self.focus();
//-->
</script>

</head>

<?php
echo we_html_element::htmlBody(array('style' => 'background-color:#bfbfbf;background-repeat:repeat;background-image: url(' . IMAGE_DIR . 'backgrounds/aquaBackground.gif);position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;','onload'=>'start();')
		, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
				, we_html_element::htmlIFrame('messaging_usel_main', 'about:blank', 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: auto;', 'border:0px;width:100%;height:100%;overflow: auto;') .
				we_html_element::htmlDiv(array('style' => 'position:absolute;height:20px;bottom:0px;left:0px;right:0px;overflow: hidden;padding:10px;background-repeat:repeat;background-image: url(' . IMAGE_DIR . 'edit/editfooterback.gif);'), we_html_button::position_yes_no_cancel(we_html_button::create_button("ok", "javascript:do_selupdate();"), "", we_html_button::create_button("cancel", "javascript:close();")
				))
));
?>

</body>
</html>
