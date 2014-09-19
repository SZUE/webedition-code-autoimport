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
?>
<script type="text/javascript"><!--
	switch (WE_REMOVE) {

		case "navigation_edit":
		case "navigation_edit_ifthere":
			new jsWindow(url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			break;
		case "module_navigation_new":
		case "module_navigation_new_group":
		case "module_navigation_exit":
		case "module_navigation_save":
		case "module_navigation_delete":
		case "module_navigation_reset_customer_filter":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				if (wind && arguments[0] != "empty_log"){
					wind.focus();
				}
			}
			break;
		case "module_navigation_rules":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				if (wind)
					wind.focus();
			}
			new jsWindow("<?php echo WE_INCLUDES_DIR; ?>we_modules/navigation/edit_navigation_rules_frameset.php", "tool_navigation_rules", -1, -1, 680, 580, true, true, true, true);
			break;
		case "module_navigation_edit_navi":
			new jsWindow("<?php echo WE_INCLUDES_DIR; ?>we_modules/navigation/weNaviEditor.php?we_cmd[1]=" + arguments[1], "we_navieditor", -1, -1, 600, 350, true, false, true, true);
			break;
		case "module_navigation_do_reset_customer_filter":
			we_repl(self.load, url, arguments[0]);
			break;
	}//WE_REMOVE

//-->
</script>