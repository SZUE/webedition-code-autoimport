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

echo we_html_tools::getHtmlTop('', '', 'frameset') . STYLESHEET;

if(we_base_request::_(we_base_request::STRING, 'pnt') === 'white'){
	echo we_html_element::jsElement('top.content.cb_incstate();') .
			'</head>' .
			we_html_element::htmlBody(array('style' => 'background-color: #FFFFFF;'));
} else { ?>
	</head>
	<frameset rows="160,*" framespacing="0" border="1" frameborder="1">
		<frame src="?pnt=white" name="messaging_messages_overview" scrolling="auto" noresize style="border-bottom:1px solid black"/>
		<frame src="?pnt=white" name="messaging_msg_view" scrolling="auto"/>
	</frameset>
	<body>
	</body>
<?php } ?>
</html>
