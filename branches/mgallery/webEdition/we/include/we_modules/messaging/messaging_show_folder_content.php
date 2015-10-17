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

echo we_html_tools::getHtmlTop() .
 STYLESHEET;

$browser = we_base_browserDetect::inst();
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
if(!$transaction){
	exit();
}
echo we_html_element::jsElement('
		var transaction="' . $transaction . '";
') .
 we_html_element::jsScript(JS_DIR . 'we_modules/messaging/messaging_std.js') .
 we_html_element::jsScript(JS_DIR . 'we_modules/messaging/showFolder.js');
?>
</head>
<body leftmargin="7" topmargin="5" marginwidth="7" marginheight="5" bgcolor="#ffffff">
	<?php
	$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
	$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
	$messaging->init($_SESSION['weS']['we_data'][$transaction]);
	?><table width="99%" class="default"><?php
		$passed_dls = array();
		foreach($messaging->selected_set as $key => $val){
			echo '<tr onclick="check(\'' . $val['ID'] . '\')" style="cursor:pointer">
		<td id="td_' . $val['ID'] . '_cb" width="18" class="defaultfont" style="text-align:left;padding-bottom:3px;"></td>';

			if($val['hdrs']['ClassName'] === 'we_todo'){
				if($val['hdrs']['Deadline'] < time()){
					$dl_passed = 1;
					$passed_dls[] = $val['ID'];
				} else {
					$dl_passed = 0;
				}

				echo '<td id="td_' . $val['ID'] . '_0" width="200" style="text-align:left" class="defaultfont">' . oldHtmlspecialchars($val['hdrs']['Subject']) . '</td>
			<td id="td_' . $val['ID'] . '_1" width="170" style="text-align:left" class="' . ($dl_passed == 0 ? 'defaultfont' : 'defaultfontred') . '">' . date(g_l('date', '[format][default]'), $val['hdrs']['Deadline']) . '</td>
			<td id="td_' . $val['ID'] . '_2" width="140" style="text-align:left" class="defaultfont"><a id="td_' . $val['ID'] . '_link_2" href="javascript:check(\'' . $val['ID'] . '\')">' . $val['hdrs']['Priority'] . '</a></td>
			<td id="td_' . $val['ID'] . '_3" width="40" style="text-align:left" class="defaultfont">' . $val['hdrs']['status'] . '%</td>
			</tr>';
			} else {
				echo '
				<td id="td_' . $val['ID'] . '_0" width="200" style="text-align:left" class="defaultfont">' . oldHtmlspecialchars($val['hdrs']['Subject']) . '</td>
				<td id="td_' . $val['ID'] . '_1" width="170" style="text-align:left" class="defaultfont">' . date(g_l('date', '[format][default]'), $val['hdrs']['Date']) . '</td>
				<td id="td_' . $val['ID'] . '_2" width="140" style="text-align:left" class="defaultfont">' . $val['hdrs']['From'] . '</td>
				<td id="td_' . $val['ID'] . '_3" width="40" style="text-align:left" class="defaultfont"><span class="fa fa-circle '.($val['hdrs']['seenStatus'] & we_messaging_proto::STATUS_READ ? 'msgRead' : 'msgUnRead').'" name="read_' . $val['ID'] . '"></span></td>
			</tr>';
			}
		}
		?></table><?php
		?>
  <script><!--
	var k;

		for (k = 0; k < parent.entries_selected.length; k++) {
			highlight_TR(parent.entries_selected[k], sel_color, sel_text_color);
		}

		if (parent.entries_selected.length > 0)
			showContent(parent.entries_selected[parent.entries_selected.length - 1]);

<?php
echo 'passed_dls = [String(' . implode('), String(', $passed_dls) . ')];';
?>
//-->
	</script>
</body>
</html>
