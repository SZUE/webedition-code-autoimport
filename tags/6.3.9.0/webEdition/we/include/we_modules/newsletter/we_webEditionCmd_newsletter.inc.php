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

		case "newsletter_edit":
		case "newsletter_edit_ifthere":
			new jsWindow(url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			break;
		case "new_user":
		case "save_newsletter":
		case "new_newsletter":
		case "new_newsletter_group":
		case "send_newsletter":
		case "preview_newsletter":
		case "delete_newsletter":
		case "send_test":
		case "domain_check":
		case "test_newsletter":
		case "show_log":
		case "print_lists":
		case "newsletter_settings":
		case "black_list":
		case "search_email":
		case "edit_file":
		case "clear_log":
		case "exit_newsletter":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
<?php
if(we_base_browserDetect::isIE()){
	echo "wind.focus();";
}
?>
			}
			break;
	}//WE_REMOVE
//-->
</script>