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
 we_html_element::jsScript(JS_DIR . 'we_modules/messaging/messaging_std.js') .
 we_html_element::jsElement('
var table="' . USER_TABLE . '";'
) .
 we_html_element::jsScript(JS_DIR . 'we_modules/messaging/messaging_usel_browse.js');

//FIXME: make the js code equal to *_tree.js
?>
<script><!--
function loadData() {
		treeData.clear();

<?php
$entries = array();
$DB_WE->query('SELECT ID,ParentID,username,Permissions,Type FROM ' . USER_TABLE . ' ORDER BY username ASC');
echo "startloc=0 ;
treeData.add(self.rootEntry('0','root','root'));";
while($DB_WE->next_record()){
	if($DB_WE->f('Type') == 1){
		echo "  treeData.add({
id: " . $DB_WE->f("ID") . ",
parentid : " . $DB_WE->f("ParentID") . ",
text : '" . $DB_WE->f("username") . "',
typ : 'folder',
open : 0,
contentType : 'user',
table : '" . USER_TABLE . "',
loaded : 0,
checked : false
			});";
	} else {
		$p = $DB_WE->f("Permissions");

		echo 'checked = (user_array_search("' . $DB_WE->f('ID') . '", opener.current_sel, "1", "we_message") != -1) ? 1 : 0;' .
		'treeData.add({
id : ' . $DB_WE->f("ID") . ',
parentid : ' . $DB_WE->f("ParentID") . ',
text : "' . $DB_WE->f("username") . '",
typ : "user",
contentType : "user",
table : "' . USER_TABLE . '",
published : "' . $p[0] . '",
checked : checked
	});';
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
	self.focus();
//-->
</script>
</head>

<?php
echo we_html_element::htmlBody(array('class' => 'weDialogBody', 'onload' => 'start();')
	, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
		, we_html_element::htmlIFrame('messaging_usel_main', HTML_DIR . 'usel.html', 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;', 'border:0px;width:100%;height:100%;') .
		we_html_element::htmlDiv(array('style' => 'height:20px;bottom:0px;left:0px;right:0px;overflow: hidden;padding:10px;', 'class' => 'editfooter'), we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:do_selupdate();"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:close();")
		))
));
?>

</body>
</html>
