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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we.inc.php');



we_html_tools::htmlTop();
if($_SESSION["user"]["ID"]){
	$DB_WE->query("UPDATE ".USER_TABLE." SET Ping=UNIX_TIMESTAMP(NOW()) WHERE ID=".$_SESSION["user"]["ID"]);
	$DB_WE->query('UPDATE '.LOCK_TABLE.' SET lockTime=DATE_ADD( NOW( ) , INTERVAL '.(PING_TIME+PING_TOLERANZ).' SECOND) WHERE UserID='.intval($_SESSION["user"]["ID"]).' AND sessionID="'.session_id().'"');
}

echo we_htmlElement::jsScript('/webEdition/js/libs/yui/yahoo-min.js').
	we_htmlElement::jsScript('/webEdition/js/libs/yui/event-min.js').
	we_htmlElement::jsScript('/webEdition/js/libs/yui/connection-min.js');
?>

<script  type="text/javascript">




var ajaxURL = "/webEdition/rpc/rpc.php";
var ajaxCallback = {
	success: function(o) {
		if(typeof(o.responseText) != 'undefined' && o.responseText != '') {
			eval("var result=" + o.responseText);
			if (result.Success) {
				var num_users = result.DataArray.num_users;
				if (top.weEditorFrameController) {
					var _ref = top.weEditorFrameController.getActiveDocumentReference();
					if (_ref && _ref.setUsersOnline && _ref.setUsersListOnline) {
						_ref.setUsersOnline(num_users);
						var usersHTML = result.DataArray.users;
						if (usersHTML) {
							_ref.setUsersListOnline(usersHTML);
						}
					}
				}
			<?php if (defined("MESSAGING_SYSTEM")) { ?>
				if (top.header_msg.update) {
					var newmsg_count = result.DataArray.newmsg_count;
					var newtodo_count = result.DataArray.newtodo_count;

					top.header_msg.update(newmsg_count, newtodo_count);
				}

			<?php } ?>
				setTimeout("YUIdoAjax()",<?php print PING_TIME; ?>*1000);

			}
		}
	},
	failure: function(o) {
		setTimeout("YUIdoAjax()",<?php print PING_TIME; ?>*1000);
		//silently ignore this - an alert message might stop the js interpreter, if the user doesn't react'
		//alert("<?php echo g_l('global',"[unable_to_call_ping]");?>");
	}
}

function YUIdoAjax() {
	YAHOO.util.Connect.asyncRequest('POST', ajaxURL, ajaxCallback, 'protocol=json&cmd=Ping');
}

//setTimeout("self.location='we_users_ping.php?r=<?php print rand() ?>'",<?php print PING_TIME ?>*1000);
</script>
</head>
<body bgcolor="white" onLoad="YUIdoAjax();">
</body>
</html>
