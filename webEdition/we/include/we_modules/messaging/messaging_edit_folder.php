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
echo we_html_tools::getHtmlTop(g_l('modules_messaging', '[folder_settings]'));
echo we_html_element::jsScript(WE_JS_MODULES_DIR . 'messaging/messaging.js');

$mode = we_base_request::_(we_base_request::STRING, 'mode');
if(we_base_request::_(we_base_request::STRING, 'mcmd') === 'save_folder_settings'){
	$foldername = we_base_request::_(we_base_request::FILE, 'folder_name');
	$parentfolder = we_base_request::_(we_base_request::INT, 'parent_folder');
	$types = we_base_request::_(we_base_request::STRING, 'foldertypes');
	switch($mode){
		case 'new':
			$res = $messaging->create_folder($foldername, $parentfolder, $types);
			break;
		case 'edit':
			$res = $messaging->modify_folder(we_base_request::_(we_base_request::INT, 'fid'), $foldername, $parentfolder);
	}
	$ID = array_shift($res);
	if($ID >= 0){
		$messaging->saveInSession($_SESSION['weS']['we_data'][$transaction]);
		?>
		<script><!--
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=messaging&pnt=cmd&we_transaction=<?= $transaction ?>&mcmd=save_folder_settings&name=<?= $foldername; ?>&id=<?= $ID ?>&mode=<?= $mode; ?>&parent_id=<?= $parentfolder; ?>&type=<?= $types; ?>';
			top.content.we_cmd('messaging_start_view', '', '<?= we_base_request::_(we_base_request::TABLE, 'table', ""); ?>');
			//-->
		</script>
		</head>
		<body></body>
		</html>
		<?php
		exit;
	} else {
		$jsCmd->addMsg($res[0], we_message_reporting::WE_MESSAGE_ERROR);
	}
}

echo $jsCmd->getCmds();
?>
<body class="weDialogBody" style="border-top: 1px solid black;">
	<form name="edit_folder" action="<?= WE_MESSAGING_MODULE_DIR; ?>messaging_edit_folder.php" method="post">
		<?php
		$fid = we_base_request::_(we_base_request::INT, 'fid');
		echo
		we_html_element::htmlHiddens(['we_transaction' => $transaction,
			'mcmd' => 'save_folder_settings',
			'mode' => $mode,
			($fid ? 'fid' : '') => $fid
		]);

		switch($mode){
			case 'new':
				$heading = g_l('modules_messaging', '[new_folder]');
				$acc_html = we_html_tools::htmlSelect('foldertypes', $messaging->get_wesel_folder_types(), 1, '', false, ['onchange' => "top.content.setHot();"]);
				break;
			case 'edit':
				$heading = g_l('modules_messaging', '[change_folder_settings]');
				$finf = $messaging->get_folder_info($fid);
				$acc_html = we_html_tools::htmlSelect('foldertypes', $messaging->get_wesel_folder_types(), 1, $finf['ClassName'], fasle, ['onchange' => "top.content.setHot();"]);
		}

		$n = isset($finf) ? $finf['Name'] : '';
		$orgn = $n;
		$fooArray = [
			"sent" => g_l('modules_messaging', '[folder_sent]'),
			"messages" => g_l('modules_messaging', '[folder_messages]'),
			"done" => g_l('modules_messaging', '[folder_done]'),
			"task" => g_l('modules_messaging', '[folder_todo]'),
			"rejected" => g_l('modules_messaging', '[folder_rejected]'),
			"todo" => g_l('modules_messaging', '[folder_todo]')
		];
		if(isset($fooArray[strtolower($n)])){
			$n = $fooArray[strtolower($n)];
			$specialfolder = true;
		} else {
			$specialfolder = false;
		}

		$input_tbl = '<table cellpadding="5" >
	<tr>
	  <td class="defaultfont">' . g_l('modules_messaging', '[folder_name]') . '</td>
	  <td class="defaultfont">' . ($specialfolder ? ($n . we_html_element::htmlHidden("folder_name", $orgn)) : we_html_tools::htmlTextInput('folder_name', 24, $n, 24, 'onchange="top.content.setHot();"')) . '</td>
	</tr>
	<tr>
	  <td class="defaultfont">' . g_l('modules_messaging', '[parent_folder]') . '</td>
	  <td>' . we_html_tools::htmlSelect('parent_folder', $messaging->get_wesel_available_folders(), 1, isset($finf) ? $finf['ParentID'] : '', false, ['onchange' => "top.content.setHot();"]) . '</td>
	</tr>
	<tr>
	  <td class="defaultfont">' . g_l('modules_messaging', '[type]') . '</td>
	  <td>' . $acc_html . '</td>
	</tr>
      </table>';

		$btn_tbl = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, "javascript:save()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:top.content.we_cmd('messaging_start_view','', '" . we_base_request::_(we_base_request::TABLE, "table", "") . "')")
			)
		;
		echo we_html_tools::htmlDialogLayout($input_tbl, $heading, $btn_tbl, "100%", 30, "", "none");
		?></td>
</form>
</body>
</html>