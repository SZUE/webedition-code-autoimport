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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

we_html_tools::htmlTop(g_l('messageConsole', "[headline]"));
print STYLESHEET;


$deleteAllButton = we_button::create_button("delete", "javascript:messageConsoleWindow.removeMessages();");
$closeButton = we_button::create_button("close", "javascript:window.close();");

$_buttons = we_button::position_yes_no_cancel($deleteAllButton, null, $closeButton);
?>
<style type="text/css">
	#jsMessageUl {
		border-top			: 1px solid black;
		background			: #fff;
		list-style-type		: none;
		margin				: 0;
		padding				: 0;

	}

	#jsMessageUl li {
		border-bottom		: 1px solid black;
		margin				: 0 0 0 0;
		padding				: 8px 0 8px 35px;
		background-repeat	: no-repeat;
		background-position	: 6 50%;
	}

	#headlineDiv {
		height				: 40px;
	}
	#headlineDiv div {
		padding				: 10px 0 0 10px;
	}

	#messageDiv {
		background			: #fff;
		overflow			: auto;
		height				: 420px ! important;
	}

	.dialogButtonDiv {
		left				: 0;
		height				: 40px;
		background-image	: url(/webEdition/images/edit/editfooterback.gif);
		position			: absolute;
		bottom				: 0;
		width				: 100%;
	}

	li.msgNotice {
		background			: url(/webEdition/images/messageConsole/noticeActive.gif);
		color				: black;
	}
	li.msgWarning {
		background			: url(/webEdition/images/messageConsole/warningActive.gif);
		color				: darkgray;
	}
	li.msgError {
		background			: url(/webEdition/images/messageConsole/errorActive.gif);
		color				: red;
	}
</style>
<?php echo we_html_element::jsScript(JS_DIR . 'messageConsoleImages.js') . we_html_element::jsScript(JS_DIR . 'messageConsoleWindow.js'); ?>
</head>

<body onload="messageConsoleWindow.init();" onunload="messageConsoleWindow.remove();" class="weDialogBody" style="overflow:hidden;">



	<div id="headlineDiv">
		<div class="weDialogHeadline">
			<?php print g_l('messageConsole', "[headline]") ?>
		</div>
	</div>
	<div id="messageDiv">
		<ul id="jsMessageUl"></ul>
	</div>
	<div class="dialogButtonDiv">
		<div style="padding: 10px 10px 0 0;">
			<?php print $_buttons; ?>
		</div>
	</div>
</body>
</html>