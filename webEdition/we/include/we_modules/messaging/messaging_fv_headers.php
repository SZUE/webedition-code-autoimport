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

		top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&mcmd=show_folder_content&sort=" + sortitem + entrstr + "&we_transaction=' . $transaction . '";
	}') .
 STYLESHEET;
$si = we_base_request::_(we_base_request::STRING, "si");
$so = we_base_request::_(we_base_request::STRING, 'so');
?>
<style>
	.defaultfont a {
		color:black;
		text-decoration:none
	}
	body{
		margin-left: 7px;
		margin-top: 6px;
	}
	table{
		width:100%;
	}
</style>
</head>
<body id="eHeaderBody">
	<table class="default" style="margin-left:18px;">
		<tr>
			<?php if(we_base_request::_(we_base_request::STRING, "viewclass") != "todo"){ ?>
				<td class="defaultfont" style="width:200px"><a href="javascript:doSort('subject');"><b><?= g_l('modules_messaging', '[subject]') ?></b>&nbsp;<?= ( $si === 'subject' ? we_messaging_frames::sort_arrow($so) : '') ?></a></td>
				<td class="defaultfont" style="width:170px;"><a href="javascript:doSort('date');"><b><?= g_l('modules_messaging', '[date]') ?></b>&nbsp;<?= (($si === 'date') ? we_messaging_frames::sort_arrow($so) : '') ?></a></td>
				<td class="defaultfont" style="width:120px"><a href="javascript:doSort('sender');"><b><?= g_l('modules_messaging', '[from]') ?></b>&nbsp;<?= ($si === 'sender' ? we_messaging_frames::sort_arrow($so) : '') ?></a></td>
				<td class="defaultfont" style="width:70px"><a href="javascript:doSort('isread');"><b><?= g_l('modules_messaging', '[is_read]') ?></b>&nbsp;<?= ($si === 'isread' ? we_messaging_frames::sort_arrow($so) : '') ?></a></td>
			<?php } else { ?>
				<td class="defaultfont" style="width:200px"><a href="javascript:doSort('subject');"><b><?= g_l('modules_messaging', '[subject]') ?></b>&nbsp;<?= ($si === 'subject' ? we_messaging_frames::sort_arrow($so) : '') ?></a></td>
				<td class="defaultfont" style="width:170px"><a href="javascript:doSort('deadline');"><b><?= g_l('modules_messaging', '[deadline]') ?></b>&nbsp;<?= ($si === 'deadline' ? we_messaging_frames::sort_arrow($so) : '') ?></a></td>
				<td class="defaultfont" style="width:120px"><a href="javascript:doSort('priority');"><b><?= g_l('modules_messaging', '[priority]') ?></b>&nbsp;<?= ($si === 'priority' ? we_messaging_frames::sort_arrow( $so) : '') ?></a></td>
				<td class="defaultfont" style="width:70px"><a href="javascript:doSort('status');"><b><?= g_l('modules_messaging', '[status]') ?></b>&nbsp;<?= ($si === 'status' ? we_messaging_frames::sort_arrow($so) : '') ?></a></td>
						<?php } ?>
		</tr>
	</table>
</body>
</html>
