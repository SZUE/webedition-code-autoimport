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
		case "export_edit":
		case "export_edit_ifthere":
			new jsWindow(url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			break;
		case "new_export":
		case "new_export_group":
		case "save_export":
		case "delete_export":
		case "exit_export":
		case "start_export":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				wind.focus();
			}
			break;
		case "unlock"://FIXME:???
			we_repl(self.load, url, arguments[0]);
			break;
	}//WE_REMOVE
//-->
</script>