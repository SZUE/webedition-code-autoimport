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
$versionsLogView = new we_versions_logView();

echo we_html_tools::getHtmlTop(g_l('versions', '[versions_log]')) .
 STYLESHEET .
 YAHOO_FILES .
 $versionsLogView->getJS() .
 we_html_element::cssLink(CSS_DIR . 'messageConsole.css');
?>
</head>

<body class="weDialogBody">
	<div id="headlineDiv">
		<div class="weDialogHeadline">
			<?php echo g_l('versions', '[versions_log]') ?>
		</div>
	</div>
	<div id="versionsDiv"><?php
		echo $versionsLogView->printContent();
		?></div>
	<div class="dialogButtonDiv">
		<div style="position:absolute;top:10px;right:20px;">
			<?php echo we_html_button::create_button(we_html_button::CLOSE, "javascript:window.close();"); ?>
		</div>
	</div>
</body>
</html>