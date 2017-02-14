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
$messaging->set_login_data($_SESSION['user']["ID"], $_SESSION['user']["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$transaction]);

$jsCmd = new we_base_jsCmd();

echo we_html_tools::getHtmlTop(g_l('modules_messaging', '[sel_rcpts]')) .
 we_html_element::jsScript(WE_JS_MODULES_DIR . 'messaging/messaging_std.js');

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



	if($messaging->save_addresses($addrbook)){
		$jsCmd->addMsg(g_l('modules_messaging', '[addr_book_saved]'), we_message_reporting::WE_MESSAGE_NOTICE);
	} else {
		$jsCmd->addMsg(g_l('modules_messaging', '[error_occured]'), we_message_reporting::WE_MESSAGE_ERROR);
	}
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
<script><!--

		addrbook_sel = [<?= implode(',', $addrbook_str); ?>];
		current_sel = [<?= implode(',', $rcpts_str); ?>];


		function browse_users_window() {
			new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "messaging_usel_browse_frameset.php?we_transaction=<?= $transaction; ?>", "messaging_usel_browse", WE().consts.size.dialog.smaller, WE().consts.size.dialog.smaller, true, false, true, false);
		}


		//-->
	</script>
	<?= $jsCmd->getCmds(); ?>
</head>
<body class="weDialogBody" onload="init();" onunload="doUnload();">
	<form name="usel">
		<?= we_html_tools::htmlDialogLayout('<table cellspacing="6">
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
    </table>', g_l('modules_messaging', '[sel_rcpts]'), we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:ok()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:window.close();")));
		?>
	</form>
	<form action="<?= WE_MESSAGING_MODULE_DIR; ?>messaging_usel.php" method="post" name="addrbook_data">
		<?=
		we_html_element::htmlHiddens(['mode' => 'save_addrbook',
			'we_transaction' => $transaction,
			'addrbook_arr' => ''
		]);
		?>
	</form>
</body>
</html>
