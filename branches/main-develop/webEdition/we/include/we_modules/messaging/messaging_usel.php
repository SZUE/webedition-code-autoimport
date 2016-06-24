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
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
if(!$transaction){
	exit();
}

$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$transaction]);


echo we_html_tools::getHtmlTop(g_l('modules_messaging', '[sel_rcpts]')) .
 we_html_element::jsScript(JS_DIR . 'we_modules/messaging/messaging_std.js');
?>
<script><!--
<?php
if(we_base_request::_(we_base_request::STRING, 'mode') === 'save_addrbook'){
	$addrbook = [];
	$t_arr = [];
	$addrbook_arr = we_base_request::_(we_base_request::STRING, 'addrbook_arr');
	if($addrbook_arr != ''){
		$t_arr = explode("\t", $addrbook_arr);
	}
	$i = 0;
	foreach($t_arr as $elem){
		$addrbook[$i] = [];
		$entry = explode(',', $elem);
		foreach($entry as $val){
			$val = urldecode($val);
			$addrbook[$i][] = $val;
		}
		$i++;
	}

	echo 'function doOnLoad() {' .
	($messaging->save_addresses($addrbook) ?
		we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[addr_book_saved]'), we_message_reporting::WE_MESSAGE_NOTICE) :
		we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[error_occured]'), we_message_reporting::WE_MESSAGE_ERROR)
	) . '}';
} else {

	echo ' function doOnLoad() {
// do nothing
}
';
}


$t_arr = $messaging->get_addresses();

$addrbook_str = [];
if($t_arr){
	foreach($t_arr as $elem){
		$addrbook_str[] = '["' . $elem[0] . '","' . $elem[1] . '","' . $elem[2] . '"]';
	}
}

$rcpts_str = [];
$rcpts = explode(',', we_base_request::_(we_base_request::RAW, "rs", ''));
$db = new DB_WE();
foreach($rcpts as $rcpt){
	if(($uid = we_users_user::getUserID($rcpt, $db)) != -1){
		$rcpts_str[] = '["we_messaging","' . $uid . '","' . $rcpt . '"]';
	}
}
?>

delta_sel = [];
addrbook_sel = [<?= implode(',', $addrbook_str); ?>];
current_sel = [<?= implode(',', $rcpts_str); ?>];

function init() {
	var i;

	for (i = 0; i < current_sel.length; i++) {
		opt = new Option(current_sel[i][2], current_sel[i][1], false, false);
		document.usel.usel_currentsel.options[document.usel.usel_currentsel.length] = opt;
	}

	for (i = 0; i < addrbook_sel.length; i++) {
		opt = new Option(addrbook_sel[i][2], addrbook_sel[i][1], false, false);
		document.usel.usel_addrbook.options[document.usel.usel_addrbook.length] = opt;
	}
}

function browse_users_window() {
	new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "messaging_usel_browse_frameset.php?we_transaction=<?= $transaction; ?>", "messaging_usel_browse", -1, -1, 350, 330, true, false, true, false);
}

function save_addrbook() {
	var submit_str = "";
	var i, j;

	if (addrbook_sel.length > 0) {
		for (i = 0; i < addrbook_sel.length; i++) {
			for (j = 0; j < addrbook_sel[i].length; j++) {
				submit_str += encodeURI(addrbook_sel[i][j]) + ',';
			}
			submit_str = submit_str.substr(0, submit_str.length - 1);

			submit_str += "\t";
		}

		submit_str = submit_str.substr(0, submit_str.length - 1);
	}
	document.addrbook_data.addrbook_arr.value = submit_str;
	document.addrbook_data.submit();
}

function dump_entries(u_type) {
	var i;
	var new_arr = current_sel;
	var pos;

	for (i = 0; i < current_sel.length; i++) {
		if (current_sel[i][0] == u_type) {
			pos = array_two_dim_search(current_sel[i][1], new_arr, 1);
			val = document.usel.usel_currentsel.options[pos].value;
			document.usel.usel_currentsel.options[pos] = null;
			new_arr = array_rm_elem(new_arr, val, 1);
		}
	}

	current_sel = new_arr;
}

function delta_sel_add(user_type) {
	var i;
	var opt;
	var tarr;
	var len = delta_sel.length;

	dump_entries(user_type);

	for (i = 0; i < len; i++) {
		tarr = delta_sel[i].split(',');

		if (WE().util.in_array(String(tarr[0]), current_sel) != -1) {
			continue;
		}

		current_sel = current_sel.concat([[user_type, tarr[0].toString(), tarr[1].toString()]]);
		opt = new Option(tarr[1], tarr[0], false, false);
		document.usel.usel_currentsel.options[document.usel.usel_currentsel.length] = opt;
	}
}

function rm_sel_user() {
	var sel_elems = get_sel_elems(document.usel.usel_currentsel);
	var i;
	var pos = -1;
	var val;

	for (i = 0; i < sel_elems.length; i++) {
		pos = array_two_dim_search(sel_elems[i], current_sel, 1);
		val = document.usel.usel_currentsel.options[pos].value;
		document.usel.usel_currentsel.options[pos] = null;
		current_sel = array_rm_elem(current_sel, val, 1);
	}
}

function rm_addrbook_entry() {
	var sel_elems = get_sel_elems(document.usel.usel_addrbook);
	var i;
	var pos = -1;
	var val;
	for (i = 0; i < sel_elems.length; i++) {
		pos = array_two_dim_search(sel_elems[i], addrbook_sel, 1);
		val = document.usel.usel_addrbook.options[pos].value;
		document.usel.usel_addrbook.options[pos] = null;
		addrbook_sel = array_rm_elem(addrbook_sel, val, 1);
	}
}

function add_toaddr() {
	var sel_elems = get_sel_elems(document.usel.usel_currentsel);
	var i;

	for (i = 0; i < sel_elems.length; i++) {
		curr_offset = array_two_dim_search(String(sel_elems[i]), current_sel, 1);
		if (array_two_dim_search(String(sel_elems[i]), addrbook_sel, 1) != -1) {
			continue;
		}

		addrbook_sel = addrbook_sel.concat([current_sel[curr_offset]]);
		opt = new Option(current_sel[curr_offset][2], current_sel[curr_offset][1], false, false);
		document.usel.usel_addrbook.options[document.usel.usel_addrbook.length] = opt;
	}
}

function add_addr2sel() {
	var sel_elems = get_sel_elems(document.usel.usel_addrbook);
	var i;
	var len = sel_elems.length;

	for (i = 0; i < len; i++) {
		addr_offset = array_two_dim_search(String(sel_elems[i]), addrbook_sel, 1);
		if (array_two_dim_search(String(sel_elems[i]), current_sel, 1) != -1) {
			continue;
		}

		current_sel = current_sel.concat([addrbook_sel[addr_offset]]);
		opt = new Option(addrbook_sel[addr_offset][2], addrbook_sel[addr_offset][1], false, false);
		document.usel.usel_currentsel.options[document.usel.usel_currentsel.length] = opt;
	}
}

function ok() {
	opener.rcpt_sel = current_sel;
	opener.update_rcpts();
	window.close();
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}
//-->
</script>
<?= STYLESHEET; ?>
</head>
<body class="weDialogBody" onload="doOnLoad();
		init();" onunload="doUnload();">
	<form name="usel">
		<?php
		$tbl = '  <table cellspacing="6">
      <tr><td class="defaultfont">' . g_l('modules_messaging', '[addr_book]') . '</td><td></td><td class="defaultfont">' . g_l('modules_messaging', '[selected]') . '</td></tr>
      <tr>
        <td rowspan="3"><select name="usel_addrbook" size="7" style="width:210px" multiple="multiple"></select>
        </td>
        <td style="vertical-align:bottom">' . we_html_button::create_button('fa:btn_direction_left,fa-lg fa-caret-left', "javascript:add_toaddr()") . '</td>
        <td rowspan="3"><select name="usel_currentsel" size="7" style="width:210px" multiple="multiple"></select>
        </td>
      </tr>
      <tr>
	<td style="vertical-align:top">' . we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:add_addr2sel()") . '</td>
      </tr>
      <tr>
	<td>' . we_html_button::create_button(we_html_button::DELETE, "javascript:rm_addrbook_entry();") . '</td>
	<td></td>
	<td>' . we_html_button::create_button(we_html_button::DELETE, "javascript:rm_sel_user();") . '</td>
      </tr>
      <tr>
	<td style="padding-top:15px;">' . we_html_button::create_button('save_address', "javascript:save_addrbook();") . '<td>
	<td colspan="2">' . we_html_button::create_button('select_user', "javascript:browse_users_window();") . '<td>
      </tr>
    </table>';
		echo we_html_tools::htmlDialogLayout($tbl, g_l('modules_messaging', '[sel_rcpts]'), we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:ok()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:window.close();")));
		?>
	</form>
	<form action="<?= WE_MESSAGING_MODULE_DIR; ?>messaging_usel.php" method="post" name="addrbook_data">
		<?php
		echo we_html_element::htmlHiddens(array(
			'mode' => 'save_addrbook',
			'we_transaction' => $transaction,
			'addrbook_arr' => ''
		));
		?>
	</form>
</body>
</html>
