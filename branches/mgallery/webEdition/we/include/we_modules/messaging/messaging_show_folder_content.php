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

echo we_html_tools::getHtmlTop() .
 STYLESHEET;
we_html_tools::protect();

$browser = we_base_browserDetect::inst();
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
if(!$transaction){
	exit();
}
echo we_html_element::jsScript(JS_DIR . 'windows.js') .
	we_html_element::jsScript(JS_DIR . 'messaging_std.js');
?>
<script type="text/javascript"><!--
	check0_img = new Image();
	check1_img = new Image();
	check0_img.src = "<?php echo TREE_IMAGE_DIR ?>check0.gif";
	check1_img.src = "<?php echo TREE_IMAGE_DIR ?>check1.gif";
	read_img = new Image();
	read_img.src = "<?php echo IMAGE_DIR ?>msg_read.gif";

	sel_color = "#006DB8";
	sel_text_color = "#ffffff";
	default_text_color = "#000000";
	default_color = "#ffffff";

	passed_dls = [];

	function showContent(id) {
		top.content.editor.edbody.msg_mfv.messaging_msg_view.location = "<?php echo WE_MESSAGING_MODULE_DIR; ?>messaging_message_view.php?id=" + id + "&we_transaction=<?php echo $transaction; ?>";
	}

	function check(elem, groupSel) {
		var j;

		var id = parseInt(elem.match(/\d+/));

		if (top.content.multi_select === false) {

			//de-select all selected entries
			for (j = 0; j < parent.parent.entries_selected.length; j++) {
				highlight_TR(parent.parent.entries_selected[j], default_color, default_text_color);
			}

			parent.parent.entries_selected = new Array();
			doSelectMessage(id);
		} else {

			if (array_search(id, parent.parent.entries_selected) != -1) {
				unSelectMessage(id);
			} else {
				doSelectMessage(id);
			}
		}
	}

	function doSelectMessage(id) {
		if (id == -1) {
			return;
		}
		showContent(id);

		if (parent.parent.entries_selected.length > 0) {
			parent.parent.entries_selected.push(String(id));
		} else {
			parent.parent.entries_selected = [String(id)];
		}

		parent.parent.last_entry_selected = id;

		if (document.images["read_" + id] !== undefined) {
			document.images["read_" + id].src = read_img.src;
		}
		highlight_TR(id, sel_color, sel_text_color);
	}

	function highlight_TR(id, color, text_color) {
		var i;

		for (i = 0; i <= 3; i++) {
			switch (i) {
				case 0:
				case 2:
					if (document.getElementById("td_" + id + "_link_" + i)) {
						document.getElementById("td_" + id + "_link_" + i).style.color = text_color;
					}
					if (document.getElementById("td_" + id + "_" + i)) {
						document.getElementById("td_" + id + "_" + i).style.color = text_color;
					}
					break;
				default:
					if (i != 1 || (top.content.viewclass != "todo")) {
						if (document.getElementById("td_" + id + "_" + i)) {
							document.getElementById("td_" + id + "_" + i).style.color = text_color;
						}
					}
			}
			if (document.getElementById("td_" + id + "_" + i)) {
				document.getElementById("td_" + id + "_" + i).style.backgroundColor = color;
			}
		}
	}

	function unSelectMessage(id, unsel_all) {
		highlight_TR(id, default_color, default_text_color);

		parent.parent.entries_selected = array_rm_elem(parent.parent.entries_selected, id, -1);
		//document.images["img_" + id].src = check0_img.src;

		if (parent.parent.entries_selected.length === 0) {
			top.content.editor.edbody.msg_mfv.messaging_msg_view.location = "about:blank";
		} else {
			showContent(parent.parent.entries_selected[parent.parent.entries_selected.length - 1]);
		}
	}

	function newMessage(username) {
		new jsWindow('<?php echo WE_MESSAGING_MODULE_DIR; ?>messaging_newmessage.php?we_transaction=<?php echo $transaction; ?>&mode=u_' + encodeURI(username), 'messaging_new_message', -1, -1, 670, 530, true, false, true, false);
	}
//-->
</script>

</head>
<body leftmargin="7" topmargin="5" marginwidth="7" marginheight="5" bgcolor="#ffffff">
	<?php
	$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
	$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
	$messaging->init($_SESSION['weS']['we_data'][$transaction]);
	?><table width="99%" cellpadding="0" cellspacing="0" border="0"><?php
		$passed_dls = array();
		foreach($messaging->selected_set as $key => $val){
			echo '<tr onclick="check(\'' . $val['ID'] . '\')" style="cursor:pointer">
		<td id="td_' . $val['ID'] . '_cb" width="18" align="left" class="defaultfont"></td>';

			if($val['hdrs']['ClassName'] === 'we_todo'){
				if($val['hdrs']['Deadline'] < time()){
					$dl_passed = 1;
					$passed_dls[] = $val['ID'];
				} else {
					$dl_passed = 0;
				}

				echo '<td id="td_' . $val['ID'] . '_0" width="200" align="left" class="defaultfont">' . oldHtmlspecialchars($val['hdrs']['Subject']) . '</td>
			<td id="td_' . $val['ID'] . '_1" width="170" align="left" class="' . ($dl_passed == 0 ? 'defaultfont' : 'defaultfontred') . '">' . date(g_l('date', '[format][default]'), $val['hdrs']['Deadline']) . '</td>
			<td id="td_' . $val['ID'] . '_2" width="140" align="left" class="defaultfont"><a id="td_' . $val['ID'] . '_link_2" href="javascript:check(\'' . $val['ID'] . '\')">' . $val['hdrs']['Priority'] . '</a></td>
			<td id="td_' . $val['ID'] . '_3" width="40" align="left" class="defaultfont">' . $val['hdrs']['status'] . '%</td>
			</tr>';
			} else {
				echo '
				<td id="td_' . $val['ID'] . '_0" width="200" align="left" class="defaultfont">' . oldHtmlspecialchars($val['hdrs']['Subject']) . '</td>
				<td id="td_' . $val['ID'] . '_1" width="170" align="left" class="defaultfont">' . date(g_l('date', '[format][default]'), $val['hdrs']['Date']) . '</td>
				<td id="td_' . $val['ID'] . '_2" width="140" align="left" class="defaultfont">' . $val['hdrs']['From'] . '</td>
				<td id="td_' . $val['ID'] . '_3" width="40" align="left" class="defaultfont"><img src="' . IMAGE_DIR . 'msg_' . ($val['hdrs']['seenStatus'] & we_messaging_proto::STATUS_READ ? '' : 'un') . 'read.gif" border="0" width="16" height="18" name="read_' . $val['ID'] . '" /></td>
			</tr>';
			}

			echo '<tr><td>' . we_html_tools::getPixel(1, 3) . '</td><td>' . we_html_tools::getPixel(1, 3) . '</td><td>' . we_html_tools::getPixel(1, 3) . '</td><td>' . we_html_tools::getPixel(1, 3) . '</td></tr>';
		}
		?></table><?php
		?>
  <script type="text/javascript"><!--
	var k;

		for (k = 0; k < parent.parent.entries_selected.length; k++) {
			highlight_TR(parent.parent.entries_selected[k], sel_color, sel_text_color);
		}

		if (parent.parent.entries_selected.length > 0)
			showContent(parent.parent.entries_selected[parent.parent.entries_selected.length - 1]);

<?php
echo 'passed_dls = [String(' . implode('), String(', $passed_dls) . ')];';
?>
//-->
	</script>
</body>
</html>
