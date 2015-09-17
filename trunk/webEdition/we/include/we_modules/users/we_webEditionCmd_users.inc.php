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

		case "browse_users":
<?php if(permissionhandler::hasPerm("NEW_USER") || permissionhandler::hasPerm("NEW_GROUP") || permissionhandler::hasPerm("SAVE_USER") || permissionhandler::hasPerm("SAVE_GROUP") || permissionhandler::hasPerm("DELETE_USER") || permissionhandler::hasPerm("DELETE_GROUP")){ ?>
				new jsWindow(url, "browse_users", -1, -1, 500, 300, true, false, true);
	<?php
} else {
	echo we_message_reporting::getShowMessageCall(g_l('alert', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
}
?>
			break;
		case "users_edit":
		case "users_edit_ifthere":
<?php if(permissionhandler::hasPerm("NEW_USER") || permissionhandler::hasPerm("SAVE_USER") || permissionhandler::hasPerm("NEW_GROUP") || permissionhandler::hasPerm("SAVE_GROUP") || permissionhandler::hasPerm("DELETE_USER") || permissionhandler::hasPerm("DELETE_GROUP")){ ?>
				new jsWindow(url, "edit_module", -1, -1, 970, 760, true, true, true, true);
	<?php
} else {
	echo we_message_reporting::getShowMessageCall(g_l('alert', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
}
?>

			break;
		case "new_user":
		case "save_user":
		case "new_group":
		case "new_alias":
		case "exit_users":
		case "delete_user":
		case "new_organization":
<?php if(permissionhandler::hasPerm("NEW_USER") || permissionhandler::hasPerm("NEW_GROUP") || permissionhandler::hasPerm("SAVE_USER") || permissionhandler::hasPerm("SAVE_GROUP") || permissionhandler::hasPerm("DELETE_USER") || permissionhandler::hasPerm("DELETE_GROUP")){ ?>
				var fo = false;
				if (jsWindow_count) {
					for (var k = jsWindow_count - 1; k > -1; k--) {
						eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + arguments[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
						if (fo)
							break;
					}
					if (wind)
						wind.focus();
				}
	<?php
} else {
	echo we_message_reporting::getShowMessageCall(g_l('alert', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
}
?>
			break;
		case "doctypes":
<?php if(permissionhandler::hasPerm("CAN_SEE_TEMPLATES")){ ?>
				new jsWindow(url, "doctypes", -1, -1, 720, 670, true, true, true);
	<?php
} else {
	echo we_message_reporting::getShowMessageCall(g_l('alert', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
}
?>
			break;
		case "users_unlock":
			top.YAHOO.util.Connect.asyncRequest('GET', url, {success: weDummy, failure: weDummy});
//		we_repl(self.load,url,arguments[0]);
			break;
		case "users_add_owner":
		case "users_del_owner":
		case "users_del_all_owners":
		case "users_del_user":
		case "users_add_user":
			if (arguments[0] == "object_del_all_users" && arguments[3]) {
				url += '#f' + arguments[3];
			}
			if (!we_sbmtFrm(top.weEditorFrameController.getActiveDocumentReference().frames["1"], url)) {
				url += "&we_transaction=" + arguments[2];
				we_repl(top.weEditorFrameController.getActiveDocumentReference().frames["1"], url, arguments[0]);
			}
			break;
		case "chooseAddress":
			new jsWindow(url, "chooseAddress", -1, -1, 400, 590, true, true, true, true);
			break;
		case "users_changeR":
			we_repl(self.load, url, arguments[0]);
			break;
	}//WE_REMOVE
//-->
</script>