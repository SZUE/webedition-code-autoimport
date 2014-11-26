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
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', $we_transaction);

echo we_html_tools::getHtmlTop() .
 we_html_element::jsElement('
	function doSort(sortitem) {
		entrstr = "";

		top.content.cmd.location = "' . WE_MESSAGING_MODULE_DIR . 'edit_messaging_frameset.php?pnt=cmd&mcmd=show_folder_content&sort=" + sortitem + entrstr + "&we_transaction=' . $transaction . '";
	}') .
 STYLESHEET .
 we_html_element::cssElement('.defaultfont a {color:black; text-decoration:none}');
$si = we_base_request::_(we_base_request::STRING, "si");
$so = we_base_request::_(we_base_request::STRING, 'so');
?>
</head>
<body  background="<?php echo IMAGE_DIR; ?>backgrounds/header_with_black_line.gif"  marginwidth="7" marginheight="6" topmargin="6" leftmargin="7">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<?php if(we_base_request::_(we_base_request::STRING, "viewclass") != "todo"){ ?>
				<td width="18"><?php we_html_tools::pPixel(18, 1) ?></td>
				<td class="defaultfont" width="200"><a href="javascript:doSort('subject');"><b><?php echo g_l('modules_messaging', '[subject]') ?></b>&nbsp;<?php echo ( $si === 'subject' ? we_messaging_frames::sort_arrow("arrow_sortorder_" . $so, "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="170"><a href="javascript:doSort('date');"><b><?php echo g_l('modules_messaging', '[date]') ?></b>&nbsp;<?php echo (($si === 'date') ? we_messaging_frames::sort_arrow("arrow_sortorder_" . $so, "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="120"><a href="javascript:doSort('sender');"><b><?php echo g_l('modules_messaging', '[from]') ?></b>&nbsp;<?php echo ($si === 'sender' ? we_messaging_frames::sort_arrow("arrow_sortorder_" . $so, "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="70"><a href="javascript:doSort('isread');"><b><?php echo g_l('modules_messaging', '[is_read]') ?></b>&nbsp;<?php echo ($si === 'isread' ? we_messaging_frames::sort_arrow("arrow_sortorder_" . $so, "") : we_html_tools::getPixel(1, 1)) ?></a></td>
			<?php } else { ?>
				<td width="18"><?php we_html_tools::pPixel(18, 1) ?></td>
				<td class="defaultfont" width="200"><a href="javascript:doSort('subject');"><b><?php echo g_l('modules_messaging', '[subject]') ?></b>&nbsp;<?php echo ($si === 'subject' ? we_messaging_frames::sort_arrow("arrow_sortorder_" . $so, "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="170"><a href="javascript:doSort('deadline');"><b><?php echo g_l('modules_messaging', '[deadline]') ?></b>&nbsp;<?php echo ($si === 'deadline' ? we_messaging_frames::sort_arrow("arrow_sortorder_" . $so, "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="120"><a href="javascript:doSort('priority');"><b><?php echo g_l('modules_messaging', '[priority]') ?></b>&nbsp;<?php echo ($si === 'priority' ? we_messaging_frames::sort_arrow("arrow_sortorder_" . $so, "") : we_html_tools::getPixel(1, 1)) ?></a></td>
				<td class="defaultfont" width="70"><a href="javascript:doSort('status');"><b><?php echo g_l('modules_messaging', '[status]') ?></b>&nbsp;<?php echo ($si === 'status' ? we_messaging_frames::sort_arrow("arrow_sortorder_" . $so, "") : we_html_tools::getPixel(1, 1)) ?></a></td>
						<?php } ?>
		</tr>
	</table>
</body>
</html>
